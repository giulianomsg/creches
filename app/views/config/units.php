<?php $baseUrl = rtrim($config['base_url'], '/'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3">Unidades Escolares</h1>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalUnidade">Nova Unidade</button>
</div>
<table class="table table-bordered" id="tableUnidades">
    <thead>
    <tr>
        <th>Nome</th>
        <th>Bairro</th>
        <th>CEP</th>
        <th>Capacidade</th>
        <th>Ações</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($units as $unit): ?>
        <tr data-unit='<?= json_encode($unit, JSON_HEX_APOS | JSON_UNESCAPED_UNICODE) ?>'>
            <td><?= htmlspecialchars($unit['name']) ?></td>
            <td><?= htmlspecialchars($unit['bairro']) ?></td>
            <td><?= htmlspecialchars($unit['cep']) ?></td>
            <td><?= htmlspecialchars((string)$unit['capacidade']) ?></td>
            <td>
                <button class="btn btn-sm btn-secondary btn-edit" data-bs-toggle="modal" data-bs-target="#modalUnidade">Editar</button>
                <form action="<?= htmlspecialchars($baseUrl . '/?route=config/delete-unit') ?>" method="post" class="d-inline" onsubmit="return confirm('Excluir unidade?');">
                    <input type="hidden" name="<?= $config['security']['csrf_token_name'] ?>" value="<?= App\Helpers\CSRFHelper::token() ?>">
                    <input type="hidden" name="id" value="<?= $unit['id'] ?>">
                    <button class="btn btn-sm btn-danger">Excluir</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<div class="modal fade" id="modalUnidade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= htmlspecialchars($baseUrl . '/?route=config/save-unit') ?>" method="post">
                <input type="hidden" name="<?= $config['security']['csrf_token_name'] ?>" value="<?= App\Helpers\CSRFHelper::token() ?>">
                <input type="hidden" name="id" id="unit_id">
                <div class="modal-header">
                    <h5 class="modal-title">Unidade Escolar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nome</label>
                        <input type="text" class="form-control" name="name" id="unit_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Endereço</label>
                        <input type="text" class="form-control" name="endereco" id="unit_endereco" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bairro</label>
                        <input type="text" class="form-control" name="bairro" id="unit_bairro" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">CEP</label>
                        <input type="text" class="form-control" name="cep" id="unit_cep" required>
                    </div>
                    <div class="row g-2">
                        <div class="col">
                            <label class="form-label">Latitude</label>
                            <input type="text" class="form-control" name="latitude" id="unit_latitude">
                        </div>
                        <div class="col">
                            <label class="form-label">Longitude</label>
                            <input type="text" class="form-control" name="longitude" id="unit_longitude">
                        </div>
                    </div>
                    <div class="mb-3 mt-2">
                        <label class="form-label">Capacidade de Vagas</label>
                        <input type="number" class="form-control" name="capacidade" id="unit_capacidade" min="0" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
