<?php
return [
    'app_name' => 'Lista de Espera Creches',
    'base_url' => getenv('APP_BASE_URL') ?: 'http://localhost',
    'session_name' => 'creches_session',
    'google' => [
        'client_id' => getenv('GOOGLE_CLIENT_ID') ?: 'SEU_CLIENT_ID',
        'client_secret' => getenv('GOOGLE_CLIENT_SECRET') ?: 'SEU_CLIENT_SECRET',
        'redirect_uri' => getenv('GOOGLE_REDIRECT_URI') ?: 'http://localhost/login.php',
        'hosted_domain' => 'educacao.riopreto.sp.gov.br',
    ],
    'upload' => [
        'max_size' => 10 * 1024 * 1024,
        'allowed_mime' => [
            'application/pdf',
            'image/jpeg',
            'image/png',
        ],
    ],
    'security' => [
        'csrf_token_name' => '_token',
        'password_pepper' => getenv('PASSWORD_PEPPER') ?: 'changeme',
    ],
    'logs_path' => __DIR__ . '/../app/logs/',
    'uploads_path' => __DIR__ . '/../app/uploads/',
];
