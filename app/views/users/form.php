<?php
use App\Helpers\CSRFHelper;

$baseUrl = rtrim($config['base_url'], '/');
$errors = $errors ?? [];
$old = $old ?? [];
$isEdit = !empty($editingUser);
$actionUrl = $baseUrl . $formAction;
$selectedRole = $old['role'] ?? ($editingUser['role'] ?? 'cadastro');
$roleOptions = [
    'admin' => 'Administrador',
    'cadastro' => 'Somente cadastro',
];

$googleLinked = $isEdit && !empty($editingUser['google_id']);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1"><?= htmlspecialchars($title) ?></h1>
        <p class="text-muted mb-0">Defina os dados de acesso institucional e permissões do usuário.</p>
    </div>
    <a class="btn btn-outline-secondary" href="<?= htmlspecialchars($baseUrl . '/?route=config/users') ?>">Voltar para a lista</a>
</div>

<?php if (!empty($errors['general'])): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($errors['general']) ?></div>
<?php endif; ?>

<form action="<?= htmlspecialchars($actionUrl) ?>" method="post" class="row g-3" novalidate>
    <input type="hidden" name="<?= htmlspecialchars($config['security']['csrf_token_name']) ?>" value="<?= htmlspecialchars(CSRFHelper::token()) ?>">
    <?php if ($isEdit): ?>
        <input type="hidden" name="id" value="<?= (int) $editingUser['id'] ?>">
    <?php endif; ?>

    <div class="col-12">
        <label for="user_name" class="form-label">Nome completo</label>
        <input type="text" class="form-control<?= isset($errors['name']) ? ' is-invalid' : '' ?>" id="user_name" name="name" value="<?= htmlspecialchars($old['name'] ?? ($editingUser['name'] ?? '')) ?>" required>
        <?php if (isset($errors['name'])): ?><div class="invalid-feedback d-block"><?= htmlspecialchars($errors['name']) ?></div><?php endif; ?>
    </div>

    <div class="col-md-6">
        <label for="user_email" class="form-label">E-mail institucional</label>
        <input type="email" class="form-control<?= isset($errors['email']) ? ' is-invalid' : '' ?>" id="user_email" name="email" value="<?= htmlspecialchars($old['email'] ?? ($editingUser['email'] ?? '')) ?>" placeholder="nome@<?= htmlspecialchars($config['google']['hosted_domain']) ?>" required>
        <?php if (isset($errors['email'])): ?><div class="invalid-feedback d-block"><?= htmlspecialchars($errors['email']) ?></div><?php endif; ?>
    </div>

    <div class="col-md-6">
        <label for="user_role" class="form-label">Perfil de acesso</label>
        <select class="form-select<?= isset($errors['role']) ? ' is-invalid' : '' ?>" id="user_role" name="role" required>
            <?php foreach ($roleOptions as $value => $label): ?>
                <option value="<?= htmlspecialchars($value) ?>" <?= $selectedRole === $value ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($errors['role'])): ?><div class="invalid-feedback d-block"><?= htmlspecialchars($errors['role']) ?></div><?php endif; ?>
    </div>

    <div class="col-md-6">
        <label for="user_password" class="form-label">Senha local</label>
        <input type="password" class="form-control<?= isset($errors['password']) ? ' is-invalid' : '' ?>" id="user_password" name="password" autocomplete="new-password" <?= $isEdit ? '' : 'placeholder="Defina uma senha inicial"' ?>>
        <?php if (isset($errors['password'])): ?><div class="invalid-feedback d-block"><?= htmlspecialchars($errors['password']) ?></div><?php endif; ?>
        <div class="form-text">Informe ao menos 8 caracteres para habilitar o acesso alternativo com usuário e senha.</div>
    </div>

    <div class="col-md-6">
        <label for="user_password_confirmation" class="form-label">Confirmação da senha</label>
        <input type="password" class="form-control<?= isset($errors['password']) ? ' is-invalid' : '' ?>" id="user_password_confirmation" name="password_confirmation" autocomplete="new-password">
    </div>

    <?php if ($googleLinked): ?>
        <div class="col-12">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="user_reset_google" name="reset_google" <?= !empty($old['reset_google']) ? 'checked' : '' ?>>
                <label class="form-check-label" for="user_reset_google">
                    Revogar vínculo com o Google OAuth para exigir nova autorização no próximo login.
                </label>
            </div>
            <div class="form-text">Usuário atualmente vinculado ao Google ID: <strong><?= htmlspecialchars($editingUser['google_id']) ?></strong>.</div>
        </div>
    <?php endif; ?>

    <div class="col-12">
        <div class="alert alert-secondary small mb-0">
            Perfis <strong>Administrador</strong> têm acesso total ao sistema. Perfis <strong>Somente cadastro</strong> não podem excluir registros nem alterar configurações.
        </div>
    </div>

    <div class="col-12 d-flex justify-content-end gap-2">
        <a class="btn btn-outline-secondary" href="<?= htmlspecialchars($baseUrl . '/?route=config/users') ?>">Cancelar</a>
        <button type="submit" class="btn btn-primary">Salvar usuário</button>
    </div>
</form>
