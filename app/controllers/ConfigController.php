<?php

namespace App\Controllers;

use App\Helpers\CSRFHelper;
use App\Helpers\FlashHelper;
use App\Helpers\SessionHelper;
use App\Models\PriorityRuleModel;

class ConfigController extends BaseController
{
    private PriorityRuleModel $rules;

    public function __construct()
    {
        parent::__construct();
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

    public function rules(): string
    {
        $user = SessionHelper::get('user');
        $rules = $this->rules->all();
        $this->logActivity('view', 'Acesso à configuração de prioridades');
        return $this->render('config/priority', compact('rules', 'user'));
    }

    public function saveRules(): void
    {
        $this->validateCsrf('/?route=config/priority');
        $this->rules->updateWeights($_POST['peso'] ?? []);
        $this->logActivity('update', 'Atualização dos pesos de prioridade');
        FlashHelper::add('success', 'Pesos atualizados.');
        $this->redirect('/?route=config/priority');
    }

    private function validateCsrf(string $redirectRoute): void
    {
        if (!CSRFHelper::validate($_POST[$this->config['security']['csrf_token_name']] ?? null)) {
            FlashHelper::add('danger', 'Token CSRF inválido.');
            $this->redirect($redirectRoute);
        }
    }
}
