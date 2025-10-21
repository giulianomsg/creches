<?php $baseUrl = rtrim($config['base_url'], '/'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3">Ficha do Aluno</h1>
    <div>
        <a href="<?= htmlspecialchars($baseUrl . '/?route=students/edit&id=' . $student['id']) ?>" class="btn btn-primary">Editar</a>
        <a href="<?= htmlspecialchars($baseUrl . '/?route=students') ?>" class="btn btn-secondary">Voltar</a>
    </div>
</div>
<div class="card mb-3">
    <div class="card-header">Dados Gerais</div>
    <div class="card-body row g-3">
        <div class="col-md-4"><strong>Nome:</strong> <?= htmlspecialchars($student['nome']) ?></div>
        <div class="col-md-4"><strong>Data de Nascimento:</strong> <?= htmlspecialchars($student['data_nascimento']) ?></div>
        <div class="col-md-4"><strong>Sexo:</strong> <?= htmlspecialchars($student['sexo']) ?></div>
        <div class="col-md-4"><strong>Mãe:</strong> <?= htmlspecialchars($student['nome_mae']) ?></div>
        <div class="col-md-4"><strong>Pai:</strong> <?= htmlspecialchars($student['nome_pai']) ?></div>
        <div class="col-md-4"><strong>Telefone:</strong> <?= htmlspecialchars($student['telefone']) ?></div>
        <div class="col-md-4"><strong>Requerente:</strong> <?= htmlspecialchars($student['requerente']) ?></div>
        <div class="col-md-4"><strong>Status:</strong> <?= htmlspecialchars($student['status']) ?></div>
        <div class="col-md-4"><strong>Pontuação:</strong> <span class="badge text-bg-info"><?= htmlspecialchars((string)$pontuacao) ?></span></div>
    </div>
</div>
<div class="card mb-3">
    <div class="card-header">Endereço</div>
    <div class="card-body row g-3">
        <div class="col-md-6"><strong>Endereço:</strong> <?= htmlspecialchars($student['endereco']) ?>, <?= htmlspecialchars($student['numero']) ?></div>
        <div class="col-md-6"><strong>Complemento:</strong> <?= htmlspecialchars($student['complemento']) ?></div>
        <div class="col-md-4"><strong>Bairro:</strong> <?= htmlspecialchars($student['bairro']) ?></div>
        <div class="col-md-4"><strong>CEP:</strong> <?= htmlspecialchars($student['cep']) ?></div>
        <div class="col-md-2"><strong>Latitude:</strong> <?= htmlspecialchars($student['latitude']) ?></div>
        <div class="col-md-2"><strong>Longitude:</strong> <?= htmlspecialchars($student['longitude']) ?></div>
        <?php if (!empty($student['latitude']) && !empty($student['longitude'])): ?>
            <div class="col-12">
                <div id="studentLocationMapView" class="coordinate-map border rounded" data-lat-value="<?= htmlspecialchars($student['latitude']) ?>" data-lng-value="<?= htmlspecialchars($student['longitude']) ?>" data-zoom="15"></div>
                <small class="text-muted d-block mt-2">Posição aproximada informada para o endereço residencial.</small>
            </div>
        <?php endif; ?>
    </div>
</div>
<div class="card mb-3">
    <div class="card-header">Documentos</div>
    <div class="card-body">
        <ul class="list-group">
            <?php foreach ($documents as $doc): ?>
                <?php $fileUrl = $baseUrl . '/uploads/' . $student['id'] . '/' . rawurlencode($doc['arquivo']); ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span><?= htmlspecialchars(strtoupper(str_replace('_',' ', $doc['tipo']))) ?></span>
                    <a href="<?= htmlspecialchars($fileUrl) ?>" class="btn btn-sm btn-outline-primary" target="_blank">Visualizar</a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
<div class="card mb-3">
    <div class="card-header">Irmãos e Preferências</div>
    <div class="card-body row g-3">
        <div class="col-md-6">
            <h5>Irmãos aguardando vaga</h5>
            <ul>
                <?php foreach ($waitingSiblings as $sibling): ?>
                    <li><?= htmlspecialchars($sibling['nome']) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="col-md-6">
            <h5>Irmãos matriculados</h5>
            <ul>
                <?php foreach ($enrolledSiblings as $sibling): ?>
                    <li><?= htmlspecialchars($sibling['nome']) ?> - <?= htmlspecialchars($sibling['escola']) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="col-12">
            <h5>Unidades escolhidas</h5>
            <ol>
                <?php foreach ($preferences as $pref): ?>
                    <li><?= htmlspecialchars($pref['name']) ?> (Preferência <?= $pref['priority'] ?>)</li>
                <?php endforeach; ?>
            </ol>
        </div>
    </div>
</div>
