<h1 class="h3 mb-3">Logs de Auditoria</h1>
<table class="table table-striped" id="tableLogs">
    <thead>
    <tr>
        <th>Data/Hora</th>
        <th>Usuário</th>
        <th>Ação</th>
        <th>Descrição</th>
        <th>IP</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($logs as $log): ?>
        <tr>
            <td><?= htmlspecialchars($log['criado_em']) ?></td>
            <td><?= htmlspecialchars($log['usuario'] ?? 'Sistema') ?></td>
            <td><?= htmlspecialchars($log['acao']) ?></td>
            <td><?= htmlspecialchars($log['descricao']) ?></td>
            <td><?= htmlspecialchars($log['ip']) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
