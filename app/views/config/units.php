<?php
$baseUrl = rtrim($config['base_url'], '/');
$formErrors = $formErrors ?? [];
$formOld = $formOld ?? [];
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3">Unidades Escolares</h1>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalUnidade">Nova Unidade</button>
</div>
<?php if (!empty($formErrors)): ?>
    <div class="alert alert-danger">
        <p class="mb-1">Existem erros no formulário. Verifique os campos abaixo:</p>
        <ul class="mb-0">
            <?php foreach ($formErrors as $field => $message): ?>
                <?php if (is_string($message) && !in_array($field, ['name', 'endereco', 'bairro', 'cep', 'latitude', 'longitude', 'capacidade'], true)): ?>
                    <li><?= htmlspecialchars($message) ?></li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
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
            <form action="<?= htmlspecialchars($baseUrl . '/?route=config/save-unit') ?>" method="post" novalidate>
                <input type="hidden" name="<?= $config['security']['csrf_token_name'] ?>" value="<?= App\Helpers\CSRFHelper::token() ?>">
                <input type="hidden" name="id" id="unit_id">
                <div class="modal-header">
                    <h5 class="modal-title">Unidade Escolar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nome</label>
                        <input type="text" class="form-control<?= isset($formErrors['name']) ? ' is-invalid' : '' ?>" name="name" id="unit_name" value="<?= htmlspecialchars($formOld['name'] ?? '') ?>" required>
                        <?php if (isset($formErrors['name'])): ?>
                            <div class="invalid-feedback d-block"><?= htmlspecialchars($formErrors['name']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Endereço</label>
                        <input type="text" class="form-control<?= isset($formErrors['endereco']) ? ' is-invalid' : '' ?>" name="endereco" id="unit_endereco" value="<?= htmlspecialchars($formOld['endereco'] ?? '') ?>" required>
                        <?php if (isset($formErrors['endereco'])): ?>
                            <div class="invalid-feedback d-block"><?= htmlspecialchars($formErrors['endereco']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bairro</label>
                        <input type="text" class="form-control<?= isset($formErrors['bairro']) ? ' is-invalid' : '' ?>" name="bairro" id="unit_bairro" value="<?= htmlspecialchars($formOld['bairro'] ?? '') ?>" required>
                        <?php if (isset($formErrors['bairro'])): ?>
                            <div class="invalid-feedback d-block"><?= htmlspecialchars($formErrors['bairro']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">CEP</label>
                        <input type="text" class="form-control<?= isset($formErrors['cep']) ? ' is-invalid' : '' ?>" name="cep" id="unit_cep" value="<?= htmlspecialchars($formOld['cep'] ?? '') ?>" required>
                        <?php if (isset($formErrors['cep'])): ?>
                            <div class="invalid-feedback d-block"><?= htmlspecialchars($formErrors['cep']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="row g-2">
                        <div class="col">
                            <label class="form-label">Latitude</label>
                            <input type="text" class="form-control<?= isset($formErrors['latitude']) ? ' is-invalid' : '' ?>" name="latitude" id="unit_latitude" value="<?= htmlspecialchars($formOld['latitude'] ?? '') ?>">
                            <?php if (isset($formErrors['latitude'])): ?>
                                <div class="invalid-feedback d-block"><?= htmlspecialchars($formErrors['latitude']) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col">
                            <label class="form-label">Longitude</label>
                            <input type="text" class="form-control<?= isset($formErrors['longitude']) ? ' is-invalid' : '' ?>" name="longitude" id="unit_longitude" value="<?= htmlspecialchars($formOld['longitude'] ?? '') ?>">
                            <?php if (isset($formErrors['longitude'])): ?>
                                <div class="invalid-feedback d-block"><?= htmlspecialchars($formErrors['longitude']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="mb-3 mt-2">
                        <label class="form-label">Capacidade de Vagas</label>
                        <input type="number" class="form-control<?= isset($formErrors['capacidade']) ? ' is-invalid' : '' ?>" name="capacidade" id="unit_capacidade" min="0" value="<?= htmlspecialchars($formOld['capacidade'] ?? '') ?>" required>
                        <?php if (isset($formErrors['capacidade'])): ?>
                            <div class="invalid-feedback d-block"><?= htmlspecialchars($formErrors['capacidade']) ?></div>
                        <?php endif; ?>
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
<div id="unitModalErrors" data-show="<?= !empty($formErrors) ? '1' : '0' ?>" data-old='<?= htmlspecialchars(json_encode($formOld, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE)) ?>'></div>
