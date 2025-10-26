<?php

namespace App\Controllers;

use App\Helpers\CSRFHelper;
use App\Helpers\FlashHelper;
use App\Helpers\SessionHelper;
use App\Models\UserModel;
use App\Services\GoogleOAuthService;

class AuthController extends BaseController
{
    private GoogleOAuthService $googleOAuthService;
    private UserModel $users;

    public function __construct()
    {
        parent::__construct();
        $this->googleOAuthService = new GoogleOAuthService();
        $this->users = new UserModel();
    }

    public function login(): string
    {
        $authUrl = $this->googleOAuthService->getAuthUrl();
        $oauthChecklist = $this->googleOAuthService->getConfigurationChecklist();
        $hasLocalUsers = $this->users->hasLocalUsers();

        return $this->render('auth/login', compact('authUrl', 'oauthChecklist', 'hasLocalUsers'));
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
                $user = $this->users->findByEmail($userInfo['email']);

                if ($user) {
                    $this->users->attachGoogleAccount((int) $user['id'], $userInfo['google_id']);
                    $user = $this->users->findById((int) $user['id']);
                } else {
                    $userInfo['role'] = 'cadastro';
                    $userId = $this->users->create($userInfo);
                    $user = $this->users->findById($userId);
                }
            }

            $this->finalizeLogin($user, 'OAuth Google');
        } catch (\Throwable $exception) {
            FlashHelper::add('danger', 'Falha na autenticação: ' . $exception->getMessage());
            $this->redirect('/login.php');
        }
    }

    public function localAuthenticate(): void
    {
        $token = $_POST[$this->config['security']['csrf_token_name']] ?? '';
        if (!CSRFHelper::validate($token)) {
            FlashHelper::add('danger', 'Token CSRF inválido. Recarregue a página e tente novamente.');
            $this->redirect('/login.php');
        }

        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            FlashHelper::add('danger', 'Informe um e-mail institucional válido.');
            $this->redirect('/login.php');
        }

        if ($password === '') {
            FlashHelper::add('danger', 'Informe a senha de acesso.');
            $this->redirect('/login.php');
        }

        $domain = substr(strrchr($email, '@') ?: '', 1);
        if ($domain !== strtolower($this->config['google']['hosted_domain'])) {
            FlashHelper::add('danger', 'Este login alternativo está limitado ao domínio institucional autorizado.');
            $this->redirect('/login.php');
        }

        $user = $this->users->findByEmail($email);

        if (!$user || empty($user['password_hash'])) {
            FlashHelper::add('danger', 'Credenciais inválidas ou usuário sem senha cadastrada.');
            $this->redirect('/login.php');
        }

        $peppered = $password . $this->config['security']['password_pepper'];

        if (!password_verify($peppered, $user['password_hash'])) {
            FlashHelper::add('danger', 'Credenciais inválidas.');
            $this->redirect('/login.php');
        }

        $this->finalizeLogin($user, 'autenticação local');
    }

    public function logout(): void
    {
        $user = SessionHelper::get('user');
        if ($user) {
            $this->logActivity('logout', 'Logout do usuário', (int) $user['id']);
        }
        SessionHelper::destroy();
        $this->redirect('/login.php');
    }

    private function finalizeLogin(array $user, string $method): void
    {
        $userId = (int) ($user['id'] ?? 0);
        unset($user['password_hash']);

        SessionHelper::set('user', $user);

        $this->logActivity('login', 'Login realizado via ' . $method, $userId > 0 ? $userId : null);

        FlashHelper::add('success', 'Autenticação realizada com sucesso.');
        $this->redirect('/');
    }
}
