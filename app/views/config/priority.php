<h1 class="h3 mb-3">Configuração de Pontuação</h1>
<form action="/?route=config/save-rules" method="post" class="card p-4">
    <input type="hidden" name="<?= $config['security']['csrf_token_name'] ?>" value="<?= App\Helpers\CSRFHelper::token() ?>">
    <div class="row g-3">
        <?php foreach ($rules as $rule): ?>
            <div class="col-md-4">
                <label class="form-label"><?= htmlspecialchars($rule['descricao']) ?></label>
                <input type="number" name="peso[<?= $rule['chave'] ?>]" class="form-control" value="<?= htmlspecialchars((string)$rule['peso']) ?>" min="0">
            </div>
        <?php endforeach; ?>
    </div>
    <div class="text-end mt-3">
        <button class="btn btn-primary">Salvar</button>
    </div>
</form>
