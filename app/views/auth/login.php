<?php use App\Helpers\CSRFHelper; ?>
<div class="row justify-content-center align-items-stretch" style="min-height: 70vh;">
    <div class="col-lg-5 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-body d-flex flex-column">
                <h2 class="card-title h4">Autenticação institucional (Google OAuth)</h2>
                <p class="text-muted">Utilize a conta @educacao.riopreto.sp.gov.br para acessar com os mesmos dados da rede municipal.</p>
                <a href="<?= htmlspecialchars($authUrl) ?>" class="btn btn-danger btn-lg mb-3">Entrar com Google</a>

                <?php if (!empty($oauthChecklist)): ?>
                    <div class="alert alert-warning" role="alert">
                        <h3 class="h6 fw-bold">Checklist pendente para o OAuth funcionar:</h3>
                        <ul class="mb-0">
                            <?php foreach ($oauthChecklist as $item): ?>
                                <li><?= htmlspecialchars($item) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php else: ?>
                    <p class="small text-success mb-0">Configuração do OAuth concluída. Caso enfrente instabilidades temporárias você pode usar o login alternativo ao lado.</p>
                <?php endif; ?>

                <p class="mt-auto small text-muted">Este sistema respeita a LGPD (Lei 13.709/2018). Ao acessar você concorda com o tratamento dos dados conforme termo de consentimento disponível no menu.</p>
            </div>
        </div>
    </div>
    <div class="col-lg-5 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h2 class="card-title h4">Login alternativo (fallback)</h2>
                <p class="text-muted">Use este acesso apenas quando o login via Google estiver indisponível. As permissões seguem o perfil cadastrado para o usuário.</p>

                <?php if (!$hasLocalUsers): ?>
                    <div class="alert alert-info" role="alert">
                        Nenhuma senha local foi cadastrada ainda. Cadastre ao menos um usuário administrador com senha para liberar este acesso.
                    </div>
                <?php endif; ?>

                <form method="post" novalidate>
                    <input type="hidden" name="<?= htmlspecialchars($config['security']['csrf_token_name']) ?>" value="<?= htmlspecialchars(CSRFHelper::token()) ?>">
                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail institucional</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="nome@educacao.riopreto.sp.gov.br" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100" <?= $hasLocalUsers ? '' : 'disabled' ?>>Entrar com usuário e senha</button>
                </form>

                <p class="mt-3 small text-muted">Para criar ou atualizar uma senha local, gere um hash seguro conforme orientações do README e atualize a coluna <strong>password_hash</strong> da tabela <strong>users</strong>.</p>
            </div>
        </div>
    </div>
</div>
