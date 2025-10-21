<?php
$baseUrl = rtrim($config['base_url'], '/');

$formatPhone = static function ($value) {
    if ($value === null || $value === '') {
        return null;
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

$buildWhatsappLink = static function ($value) {
    if ($value === null || $value === '') {
        return null;
    }

    $digits = preg_replace('/\D/', '', (string) $value);
    if ($digits === '') {
        return null;
    }

    if (strncmp($digits, '55', 2) === 0 && strlen($digits) > 11) {
        $digits = substr($digits, 2);
    }

    if (strlen($digits) < 10 || strlen($digits) > 11) {
        return null;
    }

    return 'https://wa.me/55' . $digits;
};
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
            <th>Macrorregião</th>
            <th>Bairro</th>
            <th>CEP</th>
            <th>Capacidade</th>
            <th>Telefone fixo</th>
            <th>Telefone celular</th>
            <th>WhatsApp</th>
            <th>E-mail</th>
            <th class="text-end">Ações</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($units as $unit): ?>
            <tr>
                <td><?= htmlspecialchars($unit['name']) ?></td>
                <td><?= htmlspecialchars($unit['macrorregiao'] ?? 'Não informada') ?></td>
                <td><?= htmlspecialchars($unit['bairro']) ?></td>
                <td><?= htmlspecialchars($unit['cep']) ?></td>
                <td><?= htmlspecialchars((string) $unit['capacidade']) ?></td>
                <td>
                    <?php $telefoneFixo = $formatPhone($unit['telefone_fixo'] ?? null); ?>
                    <?php if ($telefoneFixo): ?>
                        <?= htmlspecialchars($telefoneFixo) ?>
                    <?php else: ?>
                        <span class="text-muted">Não informado</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php $telefoneCelular = $formatPhone($unit['telefone_celular'] ?? null); ?>
                    <?php if ($telefoneCelular): ?>
                        <?= htmlspecialchars($telefoneCelular) ?>
                    <?php else: ?>
                        <span class="text-muted">Não informado</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php $whatsLink = $buildWhatsappLink($unit['whatsapp'] ?? null); ?>
                    <?php if ($whatsLink): ?>
                        <a class="btn btn-sm btn-success" href="<?= htmlspecialchars($whatsLink) ?>" target="_blank" rel="noopener">
                            Conversar
                        </a>
                    <?php else: ?>
                        <span class="text-muted">Não informado</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (!empty($unit['email'])): ?>
                        <a href="mailto:<?= htmlspecialchars($unit['email']) ?>"><?= htmlspecialchars($unit['email']) ?></a>
                    <?php else: ?>
                        <span class="text-muted">Não informado</span>
                    <?php endif; ?>
                </td>
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
