<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Controllers\AuthController;

$controller = new AuthController();

if (isset($_GET['code'])) {
    $controller->callback();
    exit;
}

echo $controller->login();
