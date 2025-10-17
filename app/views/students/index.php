<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3">Cadastros de Alunos</h1>
    <div>
        <a href="/?route=students/create" class="btn btn-primary">Novo Cadastro</a>
    </div>
</div>
<table class="table table-striped" id="tableAlunos">
    <thead>
    <tr>
        <th>Nome</th>
        <th>Nascimento</th>
        <th>Status</th>
        <th>Pontuação</th>
        <th>Ações</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($students as $student): ?>
        <tr>
            <td><?= htmlspecialchars($student['nome']) ?></td>
            <td><?= htmlspecialchars($student['data_nascimento']) ?></td>
            <td><?= htmlspecialchars($student['status']) ?></td>
            <td><span class="badge text-bg-info"><?= htmlspecialchars((string)$student['pontuacao']) ?></span></td>
            <td>
                <a href="/?route=students/show&id=<?= $student['id'] ?>" class="btn btn-sm btn-secondary">Ver</a>
                <a href="/?route=students/edit&id=<?= $student['id'] ?>" class="btn btn-sm btn-primary">Editar</a>
                <?php if ($user['role'] === 'admin'): ?>
                    <form action="/?route=students/delete" method="post" class="d-inline" onsubmit="return confirm('Confirmar exclusão?');">
                        <input type="hidden" name="<?= $config['security']['csrf_token_name'] ?>" value="<?= App\Helpers\CSRFHelper::token() ?>">
                        <input type="hidden" name="id" value="<?= $student['id'] ?>">
                        <button class="btn btn-sm btn-danger">Excluir</button>
                    </form>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
