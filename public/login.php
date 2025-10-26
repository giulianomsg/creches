<?php
require __DIR__ . '/../bootstrap.php';

use App\Controllers\AuthController;

$controller = new AuthController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->localAuthenticate();
    exit;
}

if (isset($_GET['code'])) {
    $controller->callback();
    exit;
}

echo $controller->login();
