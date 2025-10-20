<?php
$autoloadPath = __DIR__ . '/vendor/autoload.php';

if (!file_exists($autoloadPath)) {
    http_response_code(500);
    header('Content-Type: text/html; charset=utf-8');
    echo <<<HTML
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Dependências não instaladas</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Roboto', sans-serif; background: #f8fafc; color: #0f172a; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .card { background: #fff; border-radius: 12px; box-shadow: 0 20px 45px rgba(15, 23, 42, 0.1); padding: 32px; max-width: 520px; }
        h1 { font-size: 1.5rem; margin-bottom: 16px; }
        p { line-height: 1.6; margin: 0 0 12px; }
        code { background: #e2e8f0; padding: 4px 8px; border-radius: 6px; font-size: 0.95rem; }
        ul { margin: 12px 0 0 18px; }
        li { margin-bottom: 8px; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Dependências do Composer não encontradas</h1>
        <p>O sistema precisa que as dependências PHP sejam instaladas antes do primeiro acesso.</p>
        <p>Execute os comandos abaixo no servidor:</p>
        <ul>
            <li><code>cd /var/www/html/creches</code></li>
            <li><code>composer install --no-dev --optimize-autoloader</code></li>
        </ul>
        <p>Após a instalação, atualize esta página.</p>
        <p style="margin-top:16px; font-size:0.9rem; color:#475569;">Se os comandos acima já foram executados, verifique as permissões da pasta <code>vendor/</code> e confirme se o arquivo <code>vendor/autoload.php</code> está acessível pelo servidor web.</p>
    </div>
</body>
</html>
HTML;
    exit;
}

require $autoloadPath;

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';

    if (strpos($class, $prefix) !== 0) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $parts = explode('\\', $relativeClass);
    $fileName = array_pop($parts);

    $directories = array_map(static function (string $segment): string {
        return strtolower($segment);
    }, $parts);

    $path = __DIR__ . '/app/';
    if (!empty($directories)) {
        $path .= implode('/', $directories) . '/';
    }

    $file = $path . $fileName . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});
