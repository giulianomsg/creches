<?php
$baseUrl = rtrim($config['base_url'], '/');

$formatRole = static function (string $role): string {
    return $role === 'admin' ? 'Administrador' : 'Somente cadastro';
};

$hasUsers = !empty($users);
$currentUserId = isset($user['id']) ? (int) $user['id'] : 0;
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Usuários do sistema</h1>
        <p class="text-muted mb-0">Gerencie as credenciais locais e perfis autorizados a acessar o painel.</p>
    </div>
    <a class="btn btn-primary" href="<?= htmlspecialchars($baseUrl . '/?route=config/users/create') ?>">Cadastrar usuário</a>
</div>

<div class="table-responsive">
    <table class="table table-striped" id="tableUsers">
        <thead>
        <tr>
            <th>Nome</th>
            <th>E-mail</th>
            <th>Perfil</th>
            <th>Acesso local</th>
            <th>Google OAuth</th>
            <th class="text-end">Ações</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td><?= htmlspecialchars($item['email']) ?></td>
                <td>
                    <span class="badge bg-<?= $item['role'] === 'admin' ? 'primary' : 'secondary' ?>">
                        <?= htmlspecialchars($formatRole($item['role'])) ?>
                    </span>
                </td>
                <td>
                    <?php if (!empty($item['password_hash'])): ?>
                        <span class="badge bg-success">Habilitado</span>
                    <?php else: ?>
                        <span class="badge bg-light text-dark">Desabilitado</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (!empty($item['google_id'])): ?>
                        <span class="badge bg-success">Vinculado</span>
                    <?php else: ?>
                        <span class="badge bg-light text-dark">Sem vínculo</span>
                    <?php endif; ?>
                </td>
                <td class="text-end">
                    <a class="btn btn-sm btn-outline-secondary" href="<?= htmlspecialchars($baseUrl . '/?route=config/users/edit&id=' . $item['id']) ?>">Editar</a>
                    <?php if ($currentUserId === (int) $item['id']): ?>
                        <button class="btn btn-sm btn-outline-danger" type="button" disabled title="Não é possível excluir o próprio usuário.">Excluir</button>
                    <?php else: ?>
                        <form action="<?= htmlspecialchars($baseUrl . '/?route=config/users/destroy') ?>" method="post" class="d-inline" onsubmit="return confirm('Confirma a exclusão deste usuário? Esta ação não pode ser desfeita.');">
                            <input type="hidden" name="<?= $config['security']['csrf_token_name'] ?>" value="<?= App\Helpers\CSRFHelper::token() ?>">
                            <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger">Excluir</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php if (!$hasUsers): ?>
    <div class="alert alert-info mt-3">Nenhum usuário cadastrado até o momento.</div>
<?php endif; ?>
