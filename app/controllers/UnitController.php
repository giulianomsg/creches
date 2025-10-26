<?php

namespace App\Controllers;

use App\Helpers\CSRFHelper;
use App\Helpers\FlashHelper;
use App\Helpers\SessionHelper;
use App\Helpers\ValidationHelper;
use App\Models\UnitModel;
use PDOException;

class UnitController extends BaseController
{
    private UnitModel $units;

    public function __construct()
    {
        parent::__construct();
        $this->units = new UnitModel();
        $this->ensureAdmin();
    }

    public function index(): string
    {
        $units = $this->units->all();
        $user = SessionHelper::get('user');
        $this->logActivity('view', 'Listagem de unidades escolares');

        return $this->render('units/index', compact('units', 'user'));
    }

    public function create(): string
    {
        [$errors, $old] = $this->recoverFormState();
        $user = SessionHelper::get('user');
        $this->logActivity('view', 'Acesso ao formulário de criação de unidades escolares');

        return $this->render('units/form', [
            'user' => $user,
            'title' => 'Cadastrar unidade escolar',
            'formAction' => '/?route=config/units/store',
            'errors' => $errors,
            'old' => $old,
            'unit' => null,
        ]);
    }

    public function edit(): string
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($id <= 0) {
            FlashHelper::add('danger', 'Unidade inválida.');
            $this->redirect('/?route=config/units');
        }

        $unit = $this->units->find($id);
        if (!$unit) {
            FlashHelper::add('danger', 'Unidade não encontrada.');
            $this->redirect('/?route=config/units');
        }

        [$errors, $old] = $this->recoverFormState($unit);
        $user = SessionHelper::get('user');
        $this->logActivity('view', 'Acesso ao formulário de edição da unidade escolar #' . $id);

        return $this->render('units/form', [
            'user' => $user,
            'title' => 'Editar unidade escolar',
            'formAction' => '/?route=config/units/update',
            'errors' => $errors,
            'old' => $old,
            'unit' => $unit,
        ]);
    }

    public function store(): void
    {
        $this->validateCsrf('/?route=config/units/create');

        $input = $this->collectInput();
        [$errors, $data, $old] = $this->validateUnit($input, null);

        if (!empty($errors)) {
            $this->persistFormState($errors, $old);
            FlashHelper::add('danger', 'Não foi possível salvar a unidade. Verifique os campos destacados.');
            $this->redirect('/?route=config/units/create');
        }

        try {
            $newId = $this->units->create($data);
            $this->logActivity('create', 'Cadastro de unidade escolar #' . $newId);
            FlashHelper::add('success', 'Unidade criada com sucesso.');
        } catch (PDOException $exception) {
            $this->persistFormState(['general' => 'Erro ao salvar a unidade escolar. Tente novamente.'], $old);
            $this->logActivity('error', 'Falha ao criar unidade escolar: ' . $exception->getMessage());
            FlashHelper::add('danger', 'Erro ao salvar a unidade escolar.');
            $this->redirect('/?route=config/units/create');
        }

        $this->redirect('/?route=config/units');
    }

    public function update(): void
    {
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        if ($id <= 0) {
            FlashHelper::add('danger', 'Unidade inválida.');
            $this->redirect('/?route=config/units');
        }

        $this->validateCsrf('/?route=config/units/edit&id=' . $id);

        if (!$this->units->find($id)) {
            FlashHelper::add('danger', 'Unidade não encontrada.');
            $this->redirect('/?route=config/units');
        }

        $input = $this->collectInput();
        $input['id'] = $id;
        [$errors, $data, $old] = $this->validateUnit($input, $id);

        if (!empty($errors)) {
            $this->persistFormState($errors, $old);
            FlashHelper::add('danger', 'Não foi possível atualizar a unidade. Verifique os campos destacados.');
            $this->redirect('/?route=config/units/edit&id=' . $id);
        }

        try {
            $this->units->update($id, $data);
            $this->logActivity('update', 'Atualização da unidade escolar #' . $id);
            FlashHelper::add('success', 'Unidade atualizada com sucesso.');
        } catch (PDOException $exception) {
            $this->persistFormState(['general' => 'Erro ao atualizar a unidade escolar.'], $old);
            $this->logActivity('error', 'Falha ao atualizar unidade escolar #' . $id . ': ' . $exception->getMessage());
            FlashHelper::add('danger', 'Erro ao atualizar a unidade escolar.');
            $this->redirect('/?route=config/units/edit&id=' . $id);
        }

        $this->redirect('/?route=config/units');
    }

    public function destroy(): void
    {
        $this->validateCsrf('/?route=config/units');
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

        if ($id <= 0) {
            FlashHelper::add('danger', 'Unidade inválida.');
            $this->redirect('/?route=config/units');
        }

        $unit = $this->units->find($id);
        if (!$unit) {
            FlashHelper::add('danger', 'Unidade não encontrada.');
            $this->redirect('/?route=config/units');
        }

        try {
            $this->units->delete($id);
            $this->logActivity('delete', 'Exclusão da unidade escolar #' . $id);
            FlashHelper::add('success', 'Unidade removida com sucesso.');
        } catch (PDOException $exception) {
            $this->logActivity('error', 'Falha ao excluir unidade escolar #' . $id . ': ' . $exception->getMessage());
            FlashHelper::add('danger', 'Não foi possível excluir a unidade. Verifique se existem cadastros vinculados.');
        }

        $this->redirect('/?route=config/units');
    }

    private function ensureAdmin(): void
    {
        $user = SessionHelper::get('user');
        if (!$user || $user['role'] !== 'admin') {
            FlashHelper::add('danger', 'Acesso restrito aos administradores.');
            $this->redirect('/');
        }
    }

    private function validateCsrf(string $redirectRoute): void
    {
        if (!CSRFHelper::validate($_POST[$this->config['security']['csrf_token_name']] ?? null)) {
            FlashHelper::add('danger', 'Token CSRF inválido.');
            $this->redirect($redirectRoute);
        }
    }

    private function collectInput(): array
    {
        return [
            'name' => trim((string) ($_POST['name'] ?? '')),
            'endereco' => trim((string) ($_POST['endereco'] ?? '')),
            'numero' => trim((string) ($_POST['numero'] ?? '')),
            'bairro' => trim((string) ($_POST['bairro'] ?? '')),
            'macrorregiao' => trim((string) ($_POST['macrorregiao'] ?? '')),
            'cep' => trim((string) ($_POST['cep'] ?? '')),
            'latitude' => trim((string) ($_POST['latitude'] ?? '')),
            'longitude' => trim((string) ($_POST['longitude'] ?? '')),
            'capacidade' => trim((string) ($_POST['capacidade'] ?? '')),
            'telefone_fixo' => trim((string) ($_POST['telefone_fixo'] ?? '')),
            'telefone_celular' => trim((string) ($_POST['telefone_celular'] ?? '')),
            'whatsapp' => trim((string) ($_POST['whatsapp'] ?? '')),
            'email' => trim((string) ($_POST['email'] ?? '')),
        ];
    }

    private function validateUnit(array $input, ?int $id): array
    {
        $errors = ValidationHelper::required($input, [
            'name' => 'Informe o nome da unidade escolar.',
            'endereco' => 'Informe o endereço completo.',
            'numero' => 'Informe o número do endereço.',
            'bairro' => 'Informe o bairro.',
            'cep' => 'Informe o CEP da unidade.',
            'macrorregiao' => 'Informe a macrorregião de atendimento.',
            'capacidade' => 'Informe a capacidade de vagas.',
        ]);

        $old = $input;

        foreach (['telefone_fixo', 'telefone_celular', 'whatsapp', 'email'] as $optionalField) {
            if (!array_key_exists($optionalField, $old)) {
                $old[$optionalField] = '';
            }
        }

        $capacidade = filter_var($input['capacidade'], FILTER_VALIDATE_INT);
        if ($capacidade === false || $capacidade < 0) {
            $errors['capacidade'] = 'A capacidade deve ser um número inteiro maior ou igual a zero.';
        }

        $cepDigits = preg_replace('/\D/', '', $input['cep']);
        if (strlen($cepDigits) !== 8) {
            $errors['cep'] = 'Informe um CEP válido com 8 dígitos.';
        } else {
            $formattedCep = substr($cepDigits, 0, 5) . '-' . substr($cepDigits, 5);
            $input['cep'] = $formattedCep;
            $old['cep'] = $formattedCep;
        }

        foreach (['latitude', 'longitude'] as $coord) {
            if ($input[$coord] === '') {
                $input[$coord] = null;
                $old[$coord] = '';
                continue;
            }

            if (!is_numeric($input[$coord])) {
                $errors[$coord] = 'Informe um valor numérico válido.';
            }
        }

        if (empty($errors) && $this->units->existsByName($input['name'], $id)) {
            $errors['name'] = 'Já existe uma unidade cadastrada com este nome.';
        }

        $macrorregiao = $input['macrorregiao'];
        if ($macrorregiao !== '') {
            $normalizedMacro = mb_convert_case($macrorregiao, MB_CASE_TITLE, 'UTF-8');
            $input['macrorregiao'] = $normalizedMacro;
            $old['macrorregiao'] = $normalizedMacro;
        }

        if ($input['numero'] !== '') {
            $normalizedNumero = mb_strtoupper($input['numero']);
            $input['numero'] = $normalizedNumero;
            $old['numero'] = $normalizedNumero;
        }

        $telefoneLabels = [
            'telefone_fixo' => 'Telefone fixo',
            'telefone_celular' => 'Telefone celular',
            'whatsapp' => 'WhatsApp',
        ];

        foreach ($telefoneLabels as $field => $label) {
            $value = trim((string) ($input[$field] ?? ''));
            if ($value === '') {
                $input[$field] = null;
                $old[$field] = '';
                continue;
            }

            $digits = preg_replace('/\D/', '', $value);
            if (strncmp($digits, '55', 2) === 0 && strlen($digits) > 11) {
                $digits = substr($digits, 2);
            }

            if (strlen($digits) < 10 || strlen($digits) > 11) {
                $errors[$field] = $label . ' deve conter DDD e número válido.';
                $old[$field] = $value;
                continue;
            }

            $input[$field] = $digits;
            $old[$field] = $digits;
        }

        $email = trim((string) ($input['email'] ?? ''));
        if ($email === '') {
            $input['email'] = null;
            $old['email'] = '';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Informe um e-mail válido.';
        } else {
            $input['email'] = $email;
            $old['email'] = $email;
        }

        $data = [
            'name' => $input['name'],
            'endereco' => $input['endereco'],
            'numero' => $input['numero'],
            'bairro' => $input['bairro'],
            'macrorregiao' => $input['macrorregiao'],
            'cep' => $input['cep'],
            'latitude' => $input['latitude'] !== '' ? $input['latitude'] : null,
            'longitude' => $input['longitude'] !== '' ? $input['longitude'] : null,
            'capacidade' => $capacidade !== false ? (int) $capacidade : null,
            'telefone_fixo' => $input['telefone_fixo'] ?? null,
            'telefone_celular' => $input['telefone_celular'] ?? null,
            'whatsapp' => $input['whatsapp'] ?? null,
            'email' => $input['email'] ?? null,
        ];

        return [$errors, $data, $old];
    }

    private function persistFormState(array $errors, array $old): void
    {
        SessionHelper::set('unit_form_errors', $errors);
        SessionHelper::set('unit_form_old', $old);
    }

    private function recoverFormState(?array $defaults = null): array
    {
        $errors = SessionHelper::get('unit_form_errors', []);
        $old = SessionHelper::get('unit_form_old', []);
        SessionHelper::forget('unit_form_errors');
        SessionHelper::forget('unit_form_old');

        if (empty($old) && $defaults !== null) {
            $old = $defaults;
        }

        return [$errors, $old];
    }
}
