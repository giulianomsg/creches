<?php

namespace App\Controllers;

use App\Helpers\CSRFHelper;
use App\Helpers\FlashHelper;
use App\Helpers\SessionHelper;
use App\Helpers\UploadHelper;
use App\Helpers\ValidationHelper;
use App\Models\DocumentModel;
use App\Models\StudentEnrolledSiblingModel;
use App\Models\StudentModel;
use App\Models\StudentUnitModel;
use App\Models\StudentWaitingSiblingModel;
use App\Models\UnitModel;
use App\Services\GeoService;
use App\Services\PriorityService;

class StudentController extends BaseController
{
    private StudentModel $students;
    private DocumentModel $documents;
    private StudentUnitModel $studentUnits;
    private StudentWaitingSiblingModel $waitingSiblings;
    private StudentEnrolledSiblingModel $enrolledSiblings;
    private UnitModel $units;
    private GeoService $geoService;
    private PriorityService $priorityService;

    public function __construct()
    {
        parent::__construct();
        $this->students = new StudentModel();
        $this->documents = new DocumentModel();
        $this->studentUnits = new StudentUnitModel();
        $this->waitingSiblings = new StudentWaitingSiblingModel();
        $this->enrolledSiblings = new StudentEnrolledSiblingModel();
        $this->units = new UnitModel();
        $this->geoService = new GeoService();
        $this->priorityService = new PriorityService();
        $this->ensureAuthenticated();
    }

    private function ensureAuthenticated(): void
    {
        if (!SessionHelper::get('user')) {
            $this->redirect('/login.php');
        }
    }

    private function ensureCanDelete(): void
    {
        $user = SessionHelper::get('user');
        if ($user['role'] !== 'admin') {
            FlashHelper::add('danger', 'Você não tem permissão para excluir cadastros.');
            $this->redirect('/?route=students');
        }
    }

    public function index(): string
    {
        $user = SessionHelper::get('user');
        $filters = [
            'status' => $_GET['status'] ?? null,
            'search' => $_GET['busca'] ?? null,
        ];
        $students = $this->students->all($filters);

        foreach ($students as &$student) {
            $student['pontuacao'] = $this->priorityService->calculateScore($student);
        }

        $this->logActivity('view', 'Listagem de cadastros de alunos');
        return $this->render('students/index', compact('students', 'user'));
    }

    public function create(): string
    {
        $user = SessionHelper::get('user');
        $units = $this->units->all();
        $student = null;
        $waitingSiblings = [];
        $enrolledSiblings = [];
        $preferences = [];

        $this->logActivity('view', 'Acesso ao formulário de cadastro de aluno');
        return $this->render('students/form', compact('student', 'units', 'waitingSiblings', 'enrolledSiblings', 'preferences', 'user'));
    }

    public function store(): void
    {
        $this->validateCsrf();
        $user = SessionHelper::get('user');

        $data = $this->sanitizeStudentData($_POST, $user['id']);
        $errors = $this->validateStudent($data);

        if ($errors) {
            FlashHelper::add('danger', 'Erros de validação encontrados.');
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_old'] = $_POST;
            $this->redirect('/?route=students/create');
        }

        if (!$data['latitude'] || !$data['longitude']) {
            $coordinates = $this->geoService->getCoordinates($data['endereco'] . ', ' . $data['numero'] . ', ' . $data['bairro'] . ', ' . $data['cep']);
            if ($coordinates) {
                $data['latitude'] = $coordinates['lat'];
                $data['longitude'] = $coordinates['lng'];
            }
        }

        $studentId = $this->students->create($data);

        $documents = $this->handleUploads($studentId);
        $this->documents->saveMany($studentId, $documents);

        $this->studentUnits->savePreferences($studentId, $_POST['unidades'] ?? []);
        $this->waitingSiblings->saveList($studentId, $_POST['irmaos_lista'] ?? []);
        $enrolled = $_POST['irmaos_matriculados'] ?? [];
        $formatted = array_map(fn($nome, $escola) => ['nome' => $nome, 'escola' => $escola], $enrolled['nome'] ?? [], $enrolled['escola'] ?? []);
        $this->enrolledSiblings->saveList($studentId, $formatted);

        $this->logActivity('create', 'Cadastro de aluno #' . $studentId, (int) $user['id']);

        FlashHelper::add('success', 'Aluno cadastrado com sucesso.');
        $this->redirect('/?route=students');
    }

    public function edit(): string
    {
        $user = SessionHelper::get('user');
        $id = (int)($_GET['id'] ?? 0);
        $student = $this->students->find($id);
        if (!$student) {
            FlashHelper::add('danger', 'Aluno não encontrado.');
            $this->redirect('/?route=students');
        }

        $this->logActivity('view', 'Acesso ao formulário de edição do aluno #' . $id);
        $units = $this->units->all();
        $waitingSiblings = $this->waitingSiblings->list($id);
        $enrolledSiblings = $this->enrolledSiblings->list($id);
        $preferences = $this->studentUnits->preferences($id);
        $documents = $this->documents->byStudent($id);

        return $this->render('students/form', compact('student', 'units', 'waitingSiblings', 'enrolledSiblings', 'preferences', 'documents', 'user'));
    }

    public function update(): void
    {
        $this->validateCsrf();
        $user = SessionHelper::get('user');
        $id = (int)($_POST['id'] ?? 0);

        $student = $this->students->find($id);
        if (!$student) {
            FlashHelper::add('danger', 'Aluno não encontrado.');
            $this->redirect('/?route=students');
        }

        $data = $this->sanitizeStudentData($_POST, $student['created_by']);
        $errors = $this->validateStudent($data);

        if ($errors) {
            FlashHelper::add('danger', 'Erros de validação encontrados.');
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_old'] = $_POST;
            $this->redirect('/?route=students/edit&id=' . $id);
        }

        $this->students->update($id, $data);

        $documents = $this->handleUploads($id);
        $this->documents->saveMany($id, $documents);

        $this->studentUnits->savePreferences($id, $_POST['unidades'] ?? []);
        $this->waitingSiblings->saveList($id, $_POST['irmaos_lista'] ?? []);
        $enrolled = $_POST['irmaos_matriculados'] ?? [];
        $formatted = array_map(fn($nome, $escola) => ['nome' => $nome, 'escola' => $escola], $enrolled['nome'] ?? [], $enrolled['escola'] ?? []);
        $this->enrolledSiblings->saveList($id, $formatted);

        $this->logActivity('update', 'Atualização do aluno #' . $id, (int) $user['id']);

        FlashHelper::add('success', 'Cadastro atualizado com sucesso.');
        $this->redirect('/?route=students');
    }

    public function delete(): void
    {
        $this->validateCsrf();
        $this->ensureCanDelete();
        $id = (int)($_POST['id'] ?? 0);
        $student = $this->students->find($id);
        if (!$student) {
            FlashHelper::add('danger', 'Aluno não encontrado.');
            $this->redirect('/?route=students');
        }

        $this->documents->deleteByStudent($id);
        $this->studentUnits->deleteByStudent($id);
        $this->waitingSiblings->deleteByStudent($id);
        $this->enrolledSiblings->deleteByStudent($id);
        $this->students->delete($id);

        $user = SessionHelper::get('user');
        $this->logActivity('delete', 'Exclusão do aluno #' . $id, (int) $user['id']);

        FlashHelper::add('success', 'Aluno removido.');
        $this->redirect('/?route=students');
    }

    public function show(): string
    {
        $user = SessionHelper::get('user');
        $id = (int)($_GET['id'] ?? 0);
        $student = $this->students->find($id);
        if (!$student) {
            FlashHelper::add('danger', 'Aluno não encontrado.');
            $this->redirect('/?route=students');
        }

        $this->logActivity('view', 'Visualização do cadastro do aluno #' . $id);
        $documents = $this->documents->byStudent($id);
        $waitingSiblings = $this->waitingSiblings->list($id);
        $enrolledSiblings = $this->enrolledSiblings->list($id);
        $preferences = $this->studentUnits->preferences($id);
        $pontuacao = $this->priorityService->calculateScore($student);

        return $this->render('students/show', compact('student', 'documents', 'waitingSiblings', 'enrolledSiblings', 'preferences', 'pontuacao', 'user'));
    }

    private function validateCsrf(): void
    {
        if (!CSRFHelper::validate($_POST[$this->config['security']['csrf_token_name']] ?? null)) {
            FlashHelper::add('danger', 'Token CSRF inválido.');
            $this->redirect('/?route=students');
        }
    }

    private function sanitizeStudentData(array $data, int $userId): array
    {
        return [
            'nome' => trim($data['nome'] ?? ''),
            'data_nascimento' => $data['data_nascimento'] ?? null,
            'sexo' => $data['sexo'] ?? null,
            'nome_mae' => trim($data['nome_mae'] ?? ''),
            'nome_pai' => trim($data['nome_pai'] ?? ''),
            'telefone' => trim($data['telefone'] ?? ''),
            'requerente' => $data['requerente'] ?? null,
            'possui_gemeo' => isset($data['possui_gemeo']) ? 1 : 0,
            'nome_gemeo' => trim($data['nome_gemeo'] ?? ''),
            'possui_irmao_lista' => isset($data['possui_irmao_lista']) ? 1 : 0,
            'necessidades_especiais' => isset($data['necessidades_especiais']) ? 1 : 0,
            'mae_trabalha' => isset($data['mae_trabalha']) ? 1 : 0,
            'mae_adolescente' => isset($data['mae_adolescente']) ? 1 : 0,
            'sob_guarda_avo' => isset($data['sob_guarda_avo']) ? 1 : 0,
            'pais_deficientes' => isset($data['pais_deficientes']) ? 1 : 0,
            'filho_servidor' => isset($data['filho_servidor']) ? 1 : 0,
            'servidor_municipal' => isset($data['servidor_municipal']) ? 1 : 0,
            'bolsa_familia' => isset($data['bolsa_familia']) ? 1 : 0,
            'nis' => trim($data['nis'] ?? ''),
            'alta_vulnerabilidade' => isset($data['alta_vulnerabilidade']) ? 1 : 0,
            'media_vulnerabilidade' => isset($data['media_vulnerabilidade']) ? 1 : 0,
            'endereco' => trim($data['endereco'] ?? ''),
            'numero' => trim($data['numero'] ?? ''),
            'complemento' => trim($data['complemento'] ?? ''),
            'bairro' => trim($data['bairro'] ?? ''),
            'cep' => trim($data['cep'] ?? ''),
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'status' => $data['status'] ?? 'analise',
            'created_by' => $userId,
        ];
    }

    private function validateStudent(array $data): array
    {
        $errors = ValidationHelper::required($data, [
            'nome' => 'Nome é obrigatório.',
            'data_nascimento' => 'Data de nascimento é obrigatória.',
            'nome_mae' => 'Nome da mãe é obrigatório.',
            'telefone' => 'Telefone é obrigatório.',
            'endereco' => 'Endereço é obrigatório.',
            'numero' => 'Número é obrigatório.',
            'bairro' => 'Bairro é obrigatório.',
            'cep' => 'CEP é obrigatório.',
        ]);

        if (!ValidationHelper::date($data['data_nascimento'])) {
            $errors['data_nascimento'] = 'Data de nascimento inválida.';
        }

        if (!ValidationHelper::in($data['sexo'], ['Masculino', 'Feminino', 'Outro'])) {
            $errors['sexo'] = 'Sexo inválido.';
        }

        if (!ValidationHelper::in($data['requerente'], ['PAI', 'MÃE', 'RESPONSÁVEL LEGAL', 'AVÓS'])) {
            $errors['requerente'] = 'Requerente inválido.';
        }

        return $errors;
    }

    private function handleUploads(int $studentId): array
    {
        $uploadsFolder = $this->config['uploads_path'] . $studentId;
        if (!is_dir($uploadsFolder)) {
            mkdir($uploadsFolder, 0775, true);
        }

        $documents = [
            'certidao_nascimento' => $_FILES['certidao_nascimento'] ?? null,
            'rg' => $_FILES['rg'] ?? null,
            'cpf' => $_FILES['cpf'] ?? null,
            'comprovante_residencia' => $_FILES['comprovante_residencia'] ?? null,
            'necessidades_especiais' => $_FILES['doc_necessidades'] ?? null,
            'trabalho_mae' => $_FILES['doc_trabalho_mae'] ?? null,
            'mae_menor' => $_FILES['doc_mae_menor'] ?? null,
            'guarda_avo' => $_FILES['doc_guarda_avo'] ?? null,
            'pais_deficientes' => $_FILES['doc_pais_deficientes'] ?? null,
            'holerite_servidor' => $_FILES['doc_holerite'] ?? null,
            'vulnerabilidade' => $_FILES['doc_vulnerabilidade'] ?? null,
        ];

        $saved = [];
        foreach ($documents as $key => $file) {
            if (!$file || $file['error'] === UPLOAD_ERR_NO_FILE) {
                $saved[$key] = null;
                continue;
            }
            $saved[$key] = UploadHelper::handle($file, $this->config['upload'], $uploadsFolder);
        }

        return $saved;
    }
}
