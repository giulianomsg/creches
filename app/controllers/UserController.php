<?php

namespace App\Controllers;

use App\Helpers\CSRFHelper;
use App\Helpers\FlashHelper;
use App\Helpers\SessionHelper;
use App\Helpers\ValidationHelper;
use App\Models\UserModel;
use PDOException;

class UserController extends BaseController
{
    private UserModel $users;

    public function __construct()
    {
        parent::__construct();
        $this->users = new UserModel();
        $this->ensureAdmin();
    }

    public function index(): string
    {
        $users = $this->users->all();
        $this->logActivity('view', 'Listagem de usuários do sistema');

        return $this->render('users/index', [
            'users' => $users,
        ]);
    }

    public function create(): string
    {
        [$errors, $old] = $this->recoverFormState(['role' => 'cadastro']);
        $this->logActivity('view', 'Acesso ao formulário de criação de usuário');

        return $this->render('users/form', [
            'title' => 'Cadastrar usuário',
            'formAction' => '/?route=config/users/store',
            'errors' => $errors,
            'old' => $old,
            'editingUser' => null,
        ]);
    }

    public function edit(): string
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($id <= 0) {
            FlashHelper::add('danger', 'Usuário inválido.');
            $this->redirect('/?route=config/users');
        }

        $user = $this->users->findById($id);
        if (!$user) {
            FlashHelper::add('danger', 'Usuário não encontrado.');
            $this->redirect('/?route=config/users');
        }

        [$errors, $old] = $this->recoverFormState($user);
        $this->logActivity('view', 'Acesso ao formulário de edição do usuário #' . $id);

        return $this->render('users/form', [
            'title' => 'Editar usuário',
            'formAction' => '/?route=config/users/update',
            'errors' => $errors,
            'old' => $old,
            'editingUser' => $user,
        ]);
    }

    public function store(): void
    {
        $this->validateCsrf('/?route=config/users/create');

        $input = $this->collectInput();
        [$errors, $data, $old] = $this->validateUser($input, null);

        if (!empty($errors)) {
            $this->persistFormState($errors, $old);
            FlashHelper::add('danger', 'Não foi possível cadastrar o usuário. Verifique os campos destacados.');
            $this->redirect('/?route=config/users/create');
        }

        try {
            $userId = $this->users->create($data);
            $this->logActivity('create', 'Cadastro de usuário #' . $userId . ' (' . $data['email'] . ')');
            FlashHelper::add('success', 'Usuário cadastrado com sucesso.');
        } catch (PDOException $exception) {
            $this->persistFormState(['general' => 'Erro ao salvar o usuário. Tente novamente.'], $old);
            $this->logActivity('error', 'Falha ao cadastrar usuário: ' . $exception->getMessage());
            FlashHelper::add('danger', 'Erro ao salvar o usuário.');
            $this->redirect('/?route=config/users/create');
        }

        $this->redirect('/?route=config/users');
    }

    public function update(): void
    {
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        if ($id <= 0) {
            FlashHelper::add('danger', 'Usuário inválido.');
            $this->redirect('/?route=config/users');
        }

        $this->validateCsrf('/?route=config/users/edit&id=' . $id);

        $existing = $this->users->findById($id);
        if (!$existing) {
            FlashHelper::add('danger', 'Usuário não encontrado.');
            $this->redirect('/?route=config/users');
        }

        $input = $this->collectInput();
        $input['id'] = $id;
        [$errors, $data, $old] = $this->validateUser($input, $existing);

        if (!empty($errors)) {
            $this->persistFormState($errors, $old);
            FlashHelper::add('danger', 'Não foi possível atualizar o usuário. Verifique os campos destacados.');
            $this->redirect('/?route=config/users/edit&id=' . $id);
        }

        if ($existing['role'] === 'admin' && $data['role'] !== 'admin' && $this->users->countAdmins($id) === 0) {
            $this->persistFormState(['role' => 'Não é possível alterar o perfil do último administrador.'], $old);
            FlashHelper::add('danger', 'Não é possível alterar o perfil do último administrador.');
            $this->redirect('/?route=config/users/edit&id=' . $id);
        }

        try {
            $this->users->update($id, $data);
            $this->logActivity('update', 'Atualização do usuário #' . $id . ' (' . $data['email'] . ')');
            FlashHelper::add('success', 'Usuário atualizado com sucesso.');
        } catch (PDOException $exception) {
            $this->persistFormState(['general' => 'Erro ao atualizar o usuário.'], $old);
            $this->logActivity('error', 'Falha ao atualizar usuário #' . $id . ': ' . $exception->getMessage());
            FlashHelper::add('danger', 'Erro ao atualizar o usuário.');
            $this->redirect('/?route=config/users/edit&id=' . $id);
        }

        $this->redirect('/?route=config/users');
    }

    public function destroy(): void
    {
        $this->validateCsrf('/?route=config/users');

        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        if ($id <= 0) {
            FlashHelper::add('danger', 'Usuário inválido.');
            $this->redirect('/?route=config/users');
        }

        $user = $this->users->findById($id);
        if (!$user) {
            FlashHelper::add('danger', 'Usuário não encontrado.');
            $this->redirect('/?route=config/users');
        }

        $currentUser = SessionHelper::get('user');
        if ($currentUser && (int) $currentUser['id'] === $id) {
            FlashHelper::add('danger', 'Não é possível excluir o próprio usuário durante a sessão.');
            $this->redirect('/?route=config/users');
        }

        if ($user['role'] === 'admin' && $this->users->countAdmins($id) === 0) {
            FlashHelper::add('danger', 'Não é possível remover o último administrador do sistema.');
            $this->redirect('/?route=config/users');
        }

        try {
            $this->users->delete($id);
            $this->logActivity('delete', 'Exclusão do usuário #' . $id . ' (' . $user['email'] . ')');
            FlashHelper::add('success', 'Usuário removido com sucesso.');
        } catch (PDOException $exception) {
            $this->logActivity('error', 'Falha ao excluir usuário #' . $id . ': ' . $exception->getMessage());
            FlashHelper::add('danger', 'Não foi possível excluir o usuário.');
        }

        $this->redirect('/?route=config/users');
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
        $token = $_POST[$this->config['security']['csrf_token_name']] ?? '';
        if (!CSRFHelper::validate($token)) {
            FlashHelper::add('danger', 'Token CSRF inválido.');
            $this->redirect($redirectRoute);
        }
    }

    private function collectInput(): array
    {
        return [
            'name' => trim((string) ($_POST['name'] ?? '')),
            'email' => trim((string) ($_POST['email'] ?? '')),
            'role' => trim((string) ($_POST['role'] ?? '')),
            'password' => (string) ($_POST['password'] ?? ''),
            'password_confirmation' => (string) ($_POST['password_confirmation'] ?? ''),
            'reset_google' => isset($_POST['reset_google']),
        ];
    }

    private function validateUser(array $input, ?array $current): array
    {
        $errors = ValidationHelper::required($input, [
            'name' => 'Informe o nome completo do usuário.',
            'email' => 'Informe o e-mail institucional.',
            'role' => 'Selecione o perfil de acesso.',
        ]);

        $old = $input;
        unset($old['password'], $old['password_confirmation']);

        $name = preg_replace('/\s+/', ' ', $input['name']);
        $name = trim((string) $name);

        if ($name === '') {
            $errors['name'] = 'Informe o nome completo do usuário.';
        }

        $email = strtolower($input['email']);
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Informe um e-mail institucional válido.';
        } else {
            $domain = substr(strrchr($email, '@') ?: '', 1);
            if (strtolower($domain) !== strtolower($this->config['google']['hosted_domain'])) {
                $errors['email'] = 'Utilize apenas e-mails do domínio institucional autorizado.';
            }
        }

        $role = $input['role'];
        $allowedRoles = ['admin', 'cadastro'];
        if (!in_array($role, $allowedRoles, true)) {
            $errors['role'] = 'Selecione um perfil válido.';
        }

        if (empty($errors['email'])) {
            $ignoreId = $current['id'] ?? null;
            if ($this->users->existsByEmail($email, $ignoreId ? (int) $ignoreId : null)) {
                $errors['email'] = 'Já existe um usuário cadastrado com este e-mail.';
            }
        }

        $passwordHash = $current['password_hash'] ?? null;
        $password = $input['password'];
        $passwordConfirmation = $input['password_confirmation'];

        if ($password !== '' || $passwordConfirmation !== '') {
            if ($password !== $passwordConfirmation) {
                $errors['password'] = 'A confirmação da senha não confere.';
            } elseif (strlen($password) < 8) {
                $errors['password'] = 'A senha deve ter ao menos 8 caracteres.';
            } else {
                $peppered = $password . $this->config['security']['password_pepper'];
                $passwordHash = password_hash($peppered, PASSWORD_DEFAULT);
            }
        }

        $googleId = $current['google_id'] ?? null;
        if (!empty($input['reset_google']) && $googleId !== null) {
            $googleId = null;
        }

        $old['name'] = $name;
        $old['email'] = $email;
        $old['role'] = $role;
        $old['reset_google'] = !empty($input['reset_google']);

        $data = [
            'name' => $name,
            'email' => $email,
            'role' => $role,
            'password_hash' => $passwordHash,
            'google_id' => $googleId,
        ];

        return [$errors, $data, $old];
    }

    private function persistFormState(array $errors, array $old): void
    {
        SessionHelper::set('user_form_errors', $errors);
        SessionHelper::set('user_form_old', $old);
    }

    private function recoverFormState(?array $defaults = null): array
    {
        $errors = SessionHelper::get('user_form_errors', []);
        $old = SessionHelper::get('user_form_old', []);
        SessionHelper::forget('user_form_errors');
        SessionHelper::forget('user_form_old');

        if (empty($old) && $defaults !== null) {
            $old = $defaults;
        }

        if (!isset($old['role'])) {
            $old['role'] = $defaults['role'] ?? 'cadastro';
        }

        if (!isset($old['reset_google'])) {
            $old['reset_google'] = false;
        }

        unset($old['password_hash'], $old['google_id']);

        return [$errors, $old];
    }
}
