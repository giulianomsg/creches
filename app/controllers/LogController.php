<?php

namespace App\Controllers;

use App\Helpers\SessionHelper;
use App\Models\LogModel;

class LogController extends BaseController
{
    private LogModel $logs;

    public function __construct()
    {
        parent::__construct();
        $this->logs = new LogModel();
        $this->ensureAdmin();
    }

    private function ensureAdmin(): void
    {
        $user = SessionHelper::get('user');
        if (!$user || $user['role'] !== 'admin') {
            $this->redirect('/');
        }
    }

    public function index(): string
    {
        $user = SessionHelper::get('user');
        $logs = $this->logs->all();
        $this->logActivity('view', 'Visualização dos registros de auditoria');
        return $this->render('logs/index', compact('logs', 'user'));
    }
}
