<?php
$user = $user ?? null;
$baseUrl = rtrim($config['base_url'] ?? '', '/');
?>
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-bg-primary">
            <div class="card-body">
                <h6>Total de Cadastros</h6>
                <h3><?= $total ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-warning">
            <div class="card-body">
                <h6>Em Análise</h6>
                <h3><?= $emAnalise ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-info">
            <div class="card-body">
                <h6>Lista de Espera</h6>
                <h3><?= $emEspera ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-success">
            <div class="card-body">
                <h6>Vagas Concedidas</h6>
                <h3><?= $comVaga ?></h3>
            </div>
        </div>
    </div>
</div>
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">Distribuição de Vulnerabilidade</div>
            <div class="card-body">
                <canvas id="chartVulnerabilidade"
                        data-alta="<?= $vulnerabilidadeAlta ?>"
                        data-media="<?= $vulnerabilidadeMedia ?>"
                        data-especiais="<?= $necessidadesEspeciais ?>"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">Demanda por macrorregião (preferência 1)</div>
            <div class="card-body">
                <?php $macroLabels = $macroLabels ?? []; $macroValues = $macroValues ?? []; ?>
                <?php if (!empty($macroLabels) && !empty($macroValues)): ?>
                    <canvas id="chartMacroRegiao"
                            data-labels='<?= htmlspecialchars(json_encode($macroLabels, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8') ?>'
                            data-values='<?= htmlspecialchars(json_encode($macroValues, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8') ?>'></canvas>
                <?php else: ?>
                    <p class="text-muted mb-0">Cadastre unidades e preferências de alunos para visualizar a distribuição por macrorregião.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-3">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Unidades Cadastradas</span>
                <?php if (!empty($user) && ($user['role'] ?? '') === 'admin'): ?>
                    <a class="btn btn-sm btn-outline-primary" href="<?= htmlspecialchars($baseUrl . '/?route=config/units') ?>">
                        Gerenciar unidades
                    </a>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    <?php foreach ($units as $unit): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>
                                <strong><?= htmlspecialchars($unit['name']) ?></strong><br>
                                <small><?= htmlspecialchars($unit['endereco'] ?? '') ?><?= !empty($unit['numero']) ? ', ' . htmlspecialchars($unit['numero']) : '' ?> - <?= htmlspecialchars($unit['bairro']) ?></small><br>
                                <small class="text-muted">Macrorregião: <?= htmlspecialchars($unit['macrorregiao'] ?? '-') ?> • Capacidade: <?= htmlspecialchars((string)$unit['capacidade']) ?></small>
                            </span>
                            <span class="badge text-bg-primary">CEP <?= htmlspecialchars($unit['cep']) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <?php if (empty($units)): ?>
                    <p class="text-muted mb-0">Nenhuma unidade cadastrada até o momento.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">Mapa de intensidade de procura</div>
            <div class="card-body">
                <?php $heatmapPoints = $heatmapPoints ?? []; ?>
                <div id="dashboardHeatmap" class="heatmap-map border rounded"
                     data-points='<?= htmlspecialchars(json_encode($heatmapPoints, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8') ?>'
                     data-default-center="-20.811307,-49.375781"></div>
                <p class="text-muted small mt-2 mb-0">As cores mais quentes representam maior concentração de cadastros com status em análise ou aguardando vaga.</p>
            </div>
        </div>
    </div>
</div>
