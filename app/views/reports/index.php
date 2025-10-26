<?php $baseUrl = rtrim($config['base_url'], '/'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3">Relatórios</h1>
    <div class="btn-group">
        <a href="<?= htmlspecialchars($baseUrl . '/?route=reports/export-csv') ?>" class="btn btn-outline-secondary">Exportar CSV</a>
        <a href="<?= htmlspecialchars($baseUrl . '/?route=reports/export-excel') ?>" class="btn btn-outline-success">Exportar Excel</a>
        <a href="<?= htmlspecialchars($baseUrl . '/?route=reports/export-pdf') ?>" class="btn btn-outline-danger">Exportar PDF</a>
    </div>
</div>
<table class="table table-striped" id="tableRelatorio">
    <thead>
    <tr>
        <th>Nome</th>
        <th>Status</th>
        <th>Pontuação</th>
        <th>Bairro</th>
        <th>Data Cadastro</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($students as $student): ?>
        <tr>
            <td><?= htmlspecialchars($student['nome']) ?></td>
            <td><?= htmlspecialchars($student['status']) ?></td>
            <td><?= htmlspecialchars((string)$student['pontuacao']) ?></td>
            <td><?= htmlspecialchars($student['bairro']) ?></td>
            <td><?= htmlspecialchars($student['created_at'] ?? '') ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
