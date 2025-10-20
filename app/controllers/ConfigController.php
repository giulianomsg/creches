<?php

namespace App\Controllers;

use App\Helpers\CSRFHelper;
use App\Helpers\FlashHelper;
use App\Helpers\SessionHelper;
use App\Helpers\ValidationHelper;
use App\Models\PriorityRuleModel;
use App\Models\UnitModel;
use App\Models\LogModel;
use PDOException;

class ConfigController extends BaseController
{
    private UnitModel $units;
    private PriorityRuleModel $rules;
    private LogModel $logs;

    public function __construct()
    {
        parent::__construct();
        $this->units = new UnitModel();
        $this->rules = new PriorityRuleModel();
        $this->logs = new LogModel();
        $this->ensureAdmin();
    }

    private function ensureAdmin(): void
    {
        $user = SessionHelper::get('user');
        if (!$user || $user['role'] !== 'admin') {
            FlashHelper::add('danger', 'Acesso restrito aos administradores.');
            $this->redirect('/');
        }
    }

    public function units(): string
    {
        $user = SessionHelper::get('user');
        $units = $this->units->all();
        $formErrors = SessionHelper::get('units_errors', []);
        $formOld = SessionHelper::get('units_old', []);
        SessionHelper::forget('units_errors');
        SessionHelper::forget('units_old');

        return $this->render('config/units', compact('units', 'user', 'formErrors', 'formOld'));
    }

    public function saveUnit(): void
    {
        $this->validateCsrf();
        $id = isset($_POST['id']) && $_POST['id'] !== '' ? (int)$_POST['id'] : null;
        $input = [
            'id' => $id,
            'name' => trim((string)($_POST['name'] ?? '')),
            'endereco' => trim((string)($_POST['endereco'] ?? '')),
            'bairro' => trim((string)($_POST['bairro'] ?? '')),
            'cep' => trim((string)($_POST['cep'] ?? '')),
            'latitude' => trim((string)($_POST['latitude'] ?? '')),
            'longitude' => trim((string)($_POST['longitude'] ?? '')),
            'capacidade' => $_POST['capacidade'] ?? '',
        ];

        $errors = ValidationHelper::required($input, [
            'name' => 'Informe o nome da unidade escolar.',
            'endereco' => 'Informe o endereço completo.',
            'bairro' => 'Informe o bairro.',
            'cep' => 'Informe o CEP da unidade.',
            'capacidade' => 'Informe a capacidade de vagas.',
        ]);

        $capacidade = filter_var($input['capacidade'], FILTER_VALIDATE_INT);
        if ($capacidade === false || $capacidade < 0) {
            $errors['capacidade'] = 'A capacidade deve ser um número inteiro maior ou igual a zero.';
        }

        $cepDigits = preg_replace('/\D/', '', $input['cep']);
        if (strlen($cepDigits) !== 8) {
            $errors['cep'] = 'Informe um CEP válido com 8 dígitos.';
        } else {
            $input['cep'] = substr($cepDigits, 0, 5) . '-' . substr($cepDigits, 5);
        }

        foreach (['latitude', 'longitude'] as $coord) {
            if ($input[$coord] === '') {
                $input[$coord] = null;
                continue;
            }

            if (!is_numeric($input[$coord])) {
                $errors[$coord] = 'Informe um valor numérico válido.';
            }
        }

        if ($id !== null && !$this->units->find($id)) {
            FlashHelper::add('danger', 'Unidade não encontrada.');
            $this->redirect('/?route=config/units');
        }

        if (empty($errors) && $this->units->existsByName($input['name'], $id)) {
            $errors['name'] = 'Já existe uma unidade cadastrada com este nome.';
        }

        if ($errors) {
            FlashHelper::add('danger', 'Não foi possível salvar a unidade. Verifique os campos destacados.');
            SessionHelper::set('units_errors', $errors);
            SessionHelper::set('units_old', $input);
            $this->redirect('/?route=config/units');
        }

        $data = [
            'name' => $input['name'],
            'endereco' => $input['endereco'],
            'bairro' => $input['bairro'],
            'cep' => $input['cep'],
            'latitude' => $input['latitude'],
            'longitude' => $input['longitude'],
            'capacidade' => $capacidade,
        ];

        $user = SessionHelper::get('user');

        try {
            if ($id !== null) {
                $this->units->update($id, $data);
                $this->logs->record([
                    'user_id' => $user['id'] ?? null,
                    'acao' => 'update',
                    'descricao' => 'Atualização da unidade escolar #' . $id,
                ]);
                FlashHelper::add('success', 'Unidade atualizada com sucesso.');
            } else {
                $newId = $this->units->create($data);
                $this->logs->record([
                    'user_id' => $user['id'] ?? null,
                    'acao' => 'create',
                    'descricao' => 'Cadastro de unidade escolar #' . $newId,
                ]);
                FlashHelper::add('success', 'Unidade criada com sucesso.');
            }
        } catch (PDOException $exception) {
            FlashHelper::add('danger', 'Erro ao salvar a unidade escolar. Tente novamente.');
            $this->logs->record([
                'user_id' => $user['id'] ?? null,
                'acao' => 'error',
                'descricao' => 'Falha ao salvar unidade escolar: ' . $exception->getMessage(),
            ]);
            SessionHelper::set('units_errors', ['general' => 'Ocorreu um erro interno ao salvar a unidade.']);
            SessionHelper::set('units_old', $input);
        }

        $this->redirect('/?route=config/units');
    }

    public function deleteUnit(): void
    {
        $this->validateCsrf();
        $id = (int)($_POST['id'] ?? 0);

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
            $user = SessionHelper::get('user');
            $this->logs->record([
                'user_id' => $user['id'] ?? null,
                'acao' => 'delete',
                'descricao' => 'Exclusão da unidade escolar #' . $id,
            ]);
            FlashHelper::add('success', 'Unidade removida com sucesso.');
        } catch (PDOException $exception) {
            FlashHelper::add('danger', 'Não foi possível excluir a unidade. Verifique se existem cadastros vinculados.');
            $user = SessionHelper::get('user');
            $this->logs->record([
                'user_id' => $user['id'] ?? null,
                'acao' => 'error',
                'descricao' => 'Falha ao excluir unidade escolar #' . $id . ': ' . $exception->getMessage(),
            ]);
        }

        $this->redirect('/?route=config/units');
    }

    public function rules(): string
    {
        $user = SessionHelper::get('user');
        $rules = $this->rules->all();
        return $this->render('config/priority', compact('rules', 'user'));
    }

    public function saveRules(): void
    {
        $this->validateCsrf();
        $this->rules->updateWeights($_POST['peso'] ?? []);
        FlashHelper::add('success', 'Pesos atualizados.');
        $this->redirect('/?route=config/priority');
    }

    private function validateCsrf(): void
    {
        if (!CSRFHelper::validate($_POST[$this->config['security']['csrf_token_name']] ?? null)) {
            FlashHelper::add('danger', 'Token CSRF inválido.');
            $this->redirect('/?route=config/units');
        }
    }
}
