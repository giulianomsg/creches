<?php

namespace App\Controllers;

use App\Helpers\FlashHelper;
use App\Helpers\SessionHelper;
use App\Models\LogModel;
use App\Models\UserModel;
use App\Services\GoogleOAuthService;

class AuthController extends BaseController
{
    private GoogleOAuthService $googleOAuthService;
    private UserModel $users;
    private LogModel $logs;

    public function __construct()
    {
        parent::__construct();
        $this->googleOAuthService = new GoogleOAuthService();
        $this->users = new UserModel();
        $this->logs = new LogModel();
    }

    public function login(): string
    {
        $authUrl = $this->googleOAuthService->getAuthUrl();
        return $this->render('auth/login', compact('authUrl'));
    }

    public function callback(): void
    {
        $code = $_GET['code'] ?? null;
        if (!$code) {
            FlashHelper::add('danger', 'Código de autenticação inválido.');
            $this->redirect('/login.php');
        }

        try {
            $userInfo = $this->googleOAuthService->authenticate($code);
            $user = $this->users->findByGoogleId($userInfo['google_id']);

            if (!$user) {
                $userInfo['role'] = 'cadastro';
                $userId = $this->users->create($userInfo);
                $user = $this->users->findByGoogleId($userInfo['google_id']);
            }

            SessionHelper::set('user', $user);
            $this->logs->record([
                'user_id' => $user['id'],
                'acao' => 'login',
                'descricao' => 'Login realizado via OAuth Google',
            ]);

            $this->redirect('/');
        } catch (\Throwable $exception) {
            FlashHelper::add('danger', 'Falha na autenticação: ' . $exception->getMessage());
            $this->redirect('/login.php');
        }
    }

    public function logout(): void
    {
        $user = SessionHelper::get('user');
        SessionHelper::destroy();
        if ($user) {
            $this->logs->record([
                'user_id' => $user['id'],
                'acao' => 'logout',
                'descricao' => 'Logout do usuário',
            ]);
        }
        $this->redirect('/login.php');
    }
}
