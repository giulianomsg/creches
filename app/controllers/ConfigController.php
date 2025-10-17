<?php

namespace App\Controllers;

use App\Helpers\CSRFHelper;
use App\Helpers\FlashHelper;
use App\Helpers\SessionHelper;
use App\Models\PriorityRuleModel;
use App\Models\UnitModel;

class ConfigController extends BaseController
{
    private UnitModel $units;
    private PriorityRuleModel $rules;

    public function __construct()
    {
        parent::__construct();
        $this->units = new UnitModel();
        $this->rules = new PriorityRuleModel();
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
        return $this->render('config/units', compact('units', 'user'));
    }

    public function saveUnit(): void
    {
        $this->validateCsrf();
        $data = [
            'name' => $_POST['name'] ?? '',
            'endereco' => $_POST['endereco'] ?? '',
            'bairro' => $_POST['bairro'] ?? '',
            'cep' => $_POST['cep'] ?? '',
            'latitude' => $_POST['latitude'] ?? null,
            'longitude' => $_POST['longitude'] ?? null,
            'capacidade' => (int)($_POST['capacidade'] ?? 0),
        ];

        if (!empty($_POST['id'])) {
            $this->units->update((int)$_POST['id'], $data);
            FlashHelper::add('success', 'Unidade atualizada.');
        } else {
            $this->units->create($data);
            FlashHelper::add('success', 'Unidade criada.');
        }

        $this->redirect('/?route=config/units');
    }

    public function deleteUnit(): void
    {
        $this->validateCsrf();
        $this->units->delete((int)($_POST['id'] ?? 0));
        FlashHelper::add('success', 'Unidade removida.');
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
