<?php

namespace App\Controllers;

use App\Helpers\CSRFHelper;
use App\Helpers\FlashHelper;
use App\Helpers\SessionHelper;
use App\Helpers\ValidationHelper;
use App\Models\LogModel;
use App\Models\UnitModel;
use PDOException;

class UnitController extends BaseController
{
    private UnitModel $units;
    private LogModel $logs;

    public function __construct()
    {
        parent::__construct();
        $this->units = new UnitModel();
        $this->logs = new LogModel();
        $this->ensureAdmin();
    }

    public function index(): string
    {
        $units = $this->units->all();
        $user = SessionHelper::get('user');

        return $this->render('units/index', compact('units', 'user'));
    }

    public function create(): string
    {
        [$errors, $old] = $this->recoverFormState();
        $user = SessionHelper::get('user');

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

        $user = SessionHelper::get('user');

        try {
            $newId = $this->units->create($data);
            $this->logs->record([
                'user_id' => $user['id'] ?? null,
                'acao' => 'create',
                'descricao' => 'Cadastro de unidade escolar #' . $newId,
            ]);
            FlashHelper::add('success', 'Unidade criada com sucesso.');
        } catch (PDOException $exception) {
            $this->persistFormState(['general' => 'Erro ao salvar a unidade escolar. Tente novamente.'], $old);
            $this->logs->record([
                'user_id' => $user['id'] ?? null,
                'acao' => 'error',
                'descricao' => 'Falha ao criar unidade escolar: ' . $exception->getMessage(),
            ]);
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

        $user = SessionHelper::get('user');

        try {
            $this->units->update($id, $data);
            $this->logs->record([
                'user_id' => $user['id'] ?? null,
                'acao' => 'update',
                'descricao' => 'Atualização da unidade escolar #' . $id,
            ]);
            FlashHelper::add('success', 'Unidade atualizada com sucesso.');
        } catch (PDOException $exception) {
            $this->persistFormState(['general' => 'Erro ao atualizar a unidade escolar.'], $old);
            $this->logs->record([
                'user_id' => $user['id'] ?? null,
                'acao' => 'error',
                'descricao' => 'Falha ao atualizar unidade escolar #' . $id . ': ' . $exception->getMessage(),
            ]);
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

        $user = SessionHelper::get('user');

        try {
            $this->units->delete($id);
            $this->logs->record([
                'user_id' => $user['id'] ?? null,
                'acao' => 'delete',
                'descricao' => 'Exclusão da unidade escolar #' . $id,
            ]);
            FlashHelper::add('success', 'Unidade removida com sucesso.');
        } catch (PDOException $exception) {
            $this->logs->record([
                'user_id' => $user['id'] ?? null,
                'acao' => 'error',
                'descricao' => 'Falha ao excluir unidade escolar #' . $id . ': ' . $exception->getMessage(),
            ]);
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
            'bairro' => trim((string) ($_POST['bairro'] ?? '')),
            'cep' => trim((string) ($_POST['cep'] ?? '')),
            'latitude' => trim((string) ($_POST['latitude'] ?? '')),
            'longitude' => trim((string) ($_POST['longitude'] ?? '')),
            'capacidade' => trim((string) ($_POST['capacidade'] ?? '')),
        ];
    }

    private function validateUnit(array $input, ?int $id): array
    {
        $errors = ValidationHelper::required($input, [
            'name' => 'Informe o nome da unidade escolar.',
            'endereco' => 'Informe o endereço completo.',
            'bairro' => 'Informe o bairro.',
            'cep' => 'Informe o CEP da unidade.',
            'capacidade' => 'Informe a capacidade de vagas.',
        ]);

        $old = $input;

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

        $data = [
            'name' => $input['name'],
            'endereco' => $input['endereco'],
            'bairro' => $input['bairro'],
            'cep' => $input['cep'],
            'latitude' => $input['latitude'] !== '' ? $input['latitude'] : null,
            'longitude' => $input['longitude'] !== '' ? $input['longitude'] : null,
            'capacidade' => $capacidade !== false ? (int) $capacidade : null,
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
