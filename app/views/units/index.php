<?php
$baseUrl = rtrim($config['base_url'], '/');
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Unidades escolares</h1>
        <p class="text-muted mb-0">Gerencie os dados oficiais das unidades para cálculo de distância e controle de vagas.</p>
    </div>
    <a class="btn btn-primary" href="<?= htmlspecialchars($baseUrl . '/?route=config/units/create') ?>">Cadastrar unidade</a>
</div>

<div class="table-responsive">
    <table class="table table-striped" id="tableUnits">
        <thead>
        <tr>
            <th>Nome</th>
            <th>Bairro</th>
            <th>CEP</th>
            <th>Capacidade</th>
            <th class="text-end">Ações</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($units as $unit): ?>
            <tr>
                <td><?= htmlspecialchars($unit['name']) ?></td>
                <td><?= htmlspecialchars($unit['bairro']) ?></td>
                <td><?= htmlspecialchars($unit['cep']) ?></td>
                <td><?= htmlspecialchars((string) $unit['capacidade']) ?></td>
                <td class="text-end">
                    <a class="btn btn-sm btn-outline-secondary" href="<?= htmlspecialchars($baseUrl . '/?route=config/units/edit&id=' . $unit['id']) ?>">Editar</a>
                    <form action="<?= htmlspecialchars($baseUrl . '/?route=config/units/destroy') ?>" method="post" class="d-inline" onsubmit="return confirm('Deseja excluir esta unidade?');">
                        <input type="hidden" name="<?= $config['security']['csrf_token_name'] ?>" value="<?= App\Helpers\CSRFHelper::token() ?>">
                        <input type="hidden" name="id" value="<?= (int) $unit['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger">Excluir</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php if (empty($units)): ?>
    <div class="alert alert-info mt-3">
        Nenhuma unidade cadastrada até o momento.
    </div>
<?php endif; ?>
