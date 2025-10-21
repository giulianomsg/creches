<?php use App\Helpers\CSRFHelper; use App\Helpers\FlashHelper; ?>
<?php $baseUrl = rtrim($config['base_url'], '/'); ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($config['app_name']) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha512-m0RvrR0mRdB0O85xAvn38DSHLVQQP4SRrped1IovnHgwlHGawEq+yLkOlWd0x2h8ailqXF/w3vY9Vx0QWii0Rg==" crossorigin="" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl . '/assets/css/main.css') ?>">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= htmlspecialchars($baseUrl . '/') ?>"><?= htmlspecialchars($config['app_name']) ?></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Alternar navegação">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="<?= htmlspecialchars($baseUrl . '/?route=students') ?>">Cadastros</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= htmlspecialchars($baseUrl . '/?route=reports') ?>">Relatórios</a></li>
                <?php if (!empty($user) && $user['role'] === 'admin'): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Configurações</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= htmlspecialchars($baseUrl . '/?route=config/units') ?>">Unidades</a></li>
                            <li><a class="dropdown-item" href="<?= htmlspecialchars($baseUrl . '/?route=config/priority') ?>">Pontuação</a></li>
                            <li><a class="dropdown-item" href="<?= htmlspecialchars($baseUrl . '/?route=logs') ?>">Logs</a></li>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
            <div class="d-flex align-items-center text-white">
                <?php if (!empty($user)): ?>
                    <span class="me-3">Olá, <?= htmlspecialchars($user['name']) ?></span>
                    <a href="<?= htmlspecialchars($baseUrl . '/?route=logout') ?>" class="btn btn-outline-light btn-sm">Sair</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
<main class="container my-4">
    <?php foreach (FlashHelper::get() as $flash): ?>
        <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></div>
    <?php endforeach; ?>
    <?php include $content; ?>
</main>
<footer class="bg-light text-center py-3">
    <small>Secretaria Municipal de Educação de São José do Rio Preto - Sistema desenvolvido conforme LDB, ECA e legislação municipal.</small>
</footer>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/2.0.7/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.0.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha512-Vxe3G8Q7S+0wz4k0t9UcRgxN6E466+vWXTjhFRu9GaLxgPLZld0dfT8hSdPNgm6kKiC9QcTxyO51h0QzC2BXdQ==" crossorigin="" referrerpolicy="no-referrer"></script>
<script src="<?= htmlspecialchars($baseUrl . '/assets/js/app.js') ?>"></script>
</body>
</html>
