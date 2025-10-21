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
<div class="row">
    <div class="col-md-6">
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
    <div class="col-md-6">
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
                                <small><?= htmlspecialchars($unit['bairro']) ?> - Capacidade: <?= htmlspecialchars((string)$unit['capacidade']) ?></small>
                            </span>
                            <span class="badge text-bg-primary">CEP <?= htmlspecialchars($unit['cep']) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>
