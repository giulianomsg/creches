<?php
$baseUrl = rtrim($config['base_url'], '/');
$errors = $errors ?? [];
$old = $old ?? [];
$formatPhone = static function ($value) {
    if ($value === null || $value === '') {
        return '';
    }

    $digits = preg_replace('/\D/', '', (string) $value);
    $length = strlen($digits);

    if ($length === 11) {
        return sprintf('(%s) %s-%s', substr($digits, 0, 2), substr($digits, 2, 5), substr($digits, 7));
    }

    if ($length === 10) {
        return sprintf('(%s) %s-%s', substr($digits, 0, 2), substr($digits, 2, 4), substr($digits, 6));
    }

    return $value;
};
$isEdit = !empty($unit);
$actionUrl = $baseUrl . $formAction;
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1"><?= htmlspecialchars($title) ?></h1>
        <p class="text-muted mb-0">Informe os dados completos da unidade para cálculo de distância e pontuação.</p>
    </div>
    <a class="btn btn-outline-secondary" href="<?= htmlspecialchars($baseUrl . '/?route=config/units') ?>">Voltar para a lista</a>
</div>

<?php if (!empty($errors['general'])): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($errors['general']) ?></div>
<?php endif; ?>

<form action="<?= htmlspecialchars($actionUrl) ?>" method="post" class="row g-3">
    <input type="hidden" name="<?= $config['security']['csrf_token_name'] ?>" value="<?= App\Helpers\CSRFHelper::token() ?>">
    <?php if ($isEdit): ?>
        <input type="hidden" name="id" value="<?= (int) ($old['id'] ?? $unit['id']) ?>">
    <?php endif; ?>
    <div class="col-12">
        <label for="unit_name" class="form-label">Nome da unidade</label>
        <input type="text" class="form-control<?= isset($errors['name']) ? ' is-invalid' : '' ?>" id="unit_name" name="name" value="<?= htmlspecialchars($old['name'] ?? ($unit['name'] ?? '')) ?>" required>
        <?php if (isset($errors['name'])): ?><div class="invalid-feedback d-block"><?= htmlspecialchars($errors['name']) ?></div><?php endif; ?>
    </div>
    <div class="col-md-3">
        <label for="unit_cep" class="form-label">CEP</label>
        <input type="text" class="form-control<?= isset($errors['cep']) ? ' is-invalid' : '' ?>" id="unit_cep" name="cep" value="<?= htmlspecialchars($old['cep'] ?? ($unit['cep'] ?? '')) ?>" data-cep-autocomplete data-cep-logradouro="#unit_endereco" data-cep-bairro="#unit_bairro" required>
        <?php if (isset($errors['cep'])): ?><div class="invalid-feedback d-block"><?= htmlspecialchars($errors['cep']) ?></div><?php endif; ?>
    </div>
    <div class="col-md-5">
        <label for="unit_endereco" class="form-label">Endereço</label>
        <input type="text" class="form-control<?= isset($errors['endereco']) ? ' is-invalid' : '' ?>" id="unit_endereco" name="endereco" value="<?= htmlspecialchars($old['endereco'] ?? ($unit['endereco'] ?? '')) ?>" required>
        <?php if (isset($errors['endereco'])): ?><div class="invalid-feedback d-block"><?= htmlspecialchars($errors['endereco']) ?></div><?php endif; ?>
    </div>
    <div class="col-md-2">
        <label for="unit_numero" class="form-label">Número</label>
        <input type="text" class="form-control<?= isset($errors['numero']) ? ' is-invalid' : '' ?>" id="unit_numero" name="numero" value="<?= htmlspecialchars($old['numero'] ?? ($unit['numero'] ?? '')) ?>" required>
        <?php if (isset($errors['numero'])): ?><div class="invalid-feedback d-block"><?= htmlspecialchars($errors['numero']) ?></div><?php endif; ?>
    </div>
    <div class="col-md-2">
        <label for="unit_bairro" class="form-label">Bairro</label>
        <input type="text" class="form-control<?= isset($errors['bairro']) ? ' is-invalid' : '' ?>" id="unit_bairro" name="bairro" value="<?= htmlspecialchars($old['bairro'] ?? ($unit['bairro'] ?? '')) ?>" required>
        <?php if (isset($errors['bairro'])): ?><div class="invalid-feedback d-block"><?= htmlspecialchars($errors['bairro']) ?></div><?php endif; ?>
    </div>
    <div class="col-md-3">
        <label for="unit_macrorregiao" class="form-label">Macrorregião</label>
        <input type="text" class="form-control<?= isset($errors['macrorregiao']) ? ' is-invalid' : '' ?>" id="unit_macrorregiao" name="macrorregiao" list="macroRegionOptions" value="<?= htmlspecialchars($old['macrorregiao'] ?? ($unit['macrorregiao'] ?? '')) ?>" required>
        <?php if (isset($errors['macrorregiao'])): ?><div class="invalid-feedback d-block"><?= htmlspecialchars($errors['macrorregiao']) ?></div><?php endif; ?>
        <datalist id="macroRegionOptions">
            <?php foreach ($config['macrorregioes'] ?? [] as $macroOption): ?>
                <option value="<?= htmlspecialchars($macroOption) ?>">
            <?php endforeach; ?>
        </datalist>
    </div>
    <div class="col-md-3">
        <label for="unit_capacidade" class="form-label">Capacidade de vagas</label>
        <input type="number" min="0" class="form-control<?= isset($errors['capacidade']) ? ' is-invalid' : '' ?>" id="unit_capacidade" name="capacidade" value="<?= htmlspecialchars($old['capacidade'] ?? ($unit['capacidade'] ?? '')) ?>" required>
        <?php if (isset($errors['capacidade'])): ?><div class="invalid-feedback d-block"><?= htmlspecialchars($errors['capacidade']) ?></div><?php endif; ?>
    </div>
    <div class="col-md-3">
        <label for="unit_telefone_fixo" class="form-label">Telefone fixo</label>
        <input type="tel" class="form-control<?= isset($errors['telefone_fixo']) ? ' is-invalid' : '' ?>" id="unit_telefone_fixo" name="telefone_fixo" value="<?= htmlspecialchars($formatPhone($old['telefone_fixo'] ?? ($unit['telefone_fixo'] ?? ''))) ?>" placeholder="(17) 3210-0000">
        <?php if (isset($errors['telefone_fixo'])): ?><div class="invalid-feedback d-block"><?= htmlspecialchars($errors['telefone_fixo']) ?></div><?php endif; ?>
        <div class="form-text">Informe o DDD seguido do número.</div>
    </div>
    <div class="col-md-3">
        <label for="unit_telefone_celular" class="form-label">Telefone celular</label>
        <input type="tel" class="form-control<?= isset($errors['telefone_celular']) ? ' is-invalid' : '' ?>" id="unit_telefone_celular" name="telefone_celular" value="<?= htmlspecialchars($formatPhone($old['telefone_celular'] ?? ($unit['telefone_celular'] ?? ''))) ?>" placeholder="(17) 99100-0000">
        <?php if (isset($errors['telefone_celular'])): ?><div class="invalid-feedback d-block"><?= htmlspecialchars($errors['telefone_celular']) ?></div><?php endif; ?>
        <div class="form-text">Inclua o DDD e utilize apenas números se preferir.</div>
    </div>
    <div class="col-md-3">
        <label for="unit_whatsapp" class="form-label">WhatsApp</label>
        <div class="input-group">
            <input type="tel" class="form-control<?= isset($errors['whatsapp']) ? ' is-invalid' : '' ?>" id="unit_whatsapp" name="whatsapp" value="<?= htmlspecialchars($formatPhone($old['whatsapp'] ?? ($unit['whatsapp'] ?? ''))) ?>" placeholder="(17) 99100-0000" data-whatsapp-input data-whatsapp-target="#unitWhatsappLink">
            <a id="unitWhatsappLink" class="btn btn-outline-success disabled" href="#" target="_blank" rel="noopener" data-whatsapp-launcher aria-disabled="true">
                Abrir conversa
            </a>
        </div>
        <?php if (isset($errors['whatsapp'])): ?><div class="invalid-feedback d-block"><?= htmlspecialchars($errors['whatsapp']) ?></div><?php endif; ?>
        <div class="form-text">Clique em "Abrir conversa" para acessar o WhatsApp Web com este número.</div>
    </div>
    <div class="col-md-3">
        <label for="unit_email" class="form-label">E-mail institucional</label>
        <input type="email" class="form-control<?= isset($errors['email']) ? ' is-invalid' : '' ?>" id="unit_email" name="email" value="<?= htmlspecialchars($old['email'] ?? ($unit['email'] ?? '')) ?>" placeholder="unidade@educacao.riopreto.br">
        <?php if (isset($errors['email'])): ?><div class="invalid-feedback d-block"><?= htmlspecialchars($errors['email']) ?></div><?php endif; ?>
    </div>
    <div class="col-md-3">
        <label for="unit_latitude" class="form-label">Latitude</label>
        <input type="text" class="form-control<?= isset($errors['latitude']) ? ' is-invalid' : '' ?>" id="unit_latitude" name="latitude" value="<?= htmlspecialchars($old['latitude'] ?? ($unit['latitude'] ?? '')) ?>">
        <?php if (isset($errors['latitude'])): ?><div class="invalid-feedback d-block"><?= htmlspecialchars($errors['latitude']) ?></div><?php endif; ?>
    </div>
    <div class="col-md-3">
        <label for="unit_longitude" class="form-label">Longitude</label>
        <input type="text" class="form-control<?= isset($errors['longitude']) ? ' is-invalid' : '' ?>" id="unit_longitude" name="longitude" value="<?= htmlspecialchars($old['longitude'] ?? ($unit['longitude'] ?? '')) ?>">
        <?php if (isset($errors['longitude'])): ?><div class="invalid-feedback d-block"><?= htmlspecialchars($errors['longitude']) ?></div><?php endif; ?>
    </div>
    <div class="col-12">
        <label class="form-label">Localização no mapa</label>
        <div id="unitLocationMap" class="coordinate-map border rounded" data-lat-input="#unit_latitude" data-lng-input="#unit_longitude" data-address-fields="#unit_endereco,#unit_numero,#unit_bairro,#unit_cep" data-geocode-context="São José do Rio Preto - SP"></div>
        <div class="form-text">Arraste o marcador para ajustar a referência geográfica da unidade.</div>
    </div>
    <div class="col-12 d-flex justify-content-end gap-2">
        <a class="btn btn-outline-secondary" href="<?= htmlspecialchars($baseUrl . '/?route=config/units') ?>">Cancelar</a>
        <button type="submit" class="btn btn-primary">Salvar unidade</button>
    </div>
</form>
