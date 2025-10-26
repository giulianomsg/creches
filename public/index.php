<?php

require __DIR__ . '/../bootstrap.php';

use App\Controllers\AuthController;
use App\Controllers\ConfigController;
use App\Controllers\DashboardController;
use App\Controllers\LogController;
use App\Controllers\ReportController;
use App\Controllers\StudentController;
use App\Controllers\UnitController;
use App\Controllers\UserController;
use App\Core\Router;
use App\Helpers\SessionHelper;

$config = require __DIR__ . '/../config/config.php';
SessionHelper::start($config['session_name']);

$route = $_GET['route'] ?? '';

if ($route === '' && isset($_SERVER['REQUEST_URI'])) {
    $requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
    $basePath = parse_url($config['base_url'], PHP_URL_PATH) ?: '';
    $basePath = rtrim($basePath, '/');

    if ($basePath !== '' && str_starts_with($requestPath, $basePath)) {
        $requestPath = substr($requestPath, strlen($basePath));
    }

    $route = trim($requestPath, '/');
}
$router = new Router();

$router->get('/', function () {
    $controller = new DashboardController();
    return $controller->index();
});

$router->get('/students', function () {
    $controller = new StudentController();
    return $controller->index();
});
$router->get('/students/create', function () {
    $controller = new StudentController();
    return $controller->create();
});
$router->post('/students/store', function () {
    $controller = new StudentController();
    $controller->store();
});
$router->get('/students/edit', function () {
    $controller = new StudentController();
    return $controller->edit();
});
$router->post('/students/update', function () {
    $controller = new StudentController();
    $controller->update();
});
$router->post('/students/delete', function () {
    $controller = new StudentController();
    $controller->delete();
});
$router->get('/students/show', function () {
    $controller = new StudentController();
    return $controller->show();
});

$router->get('/reports', function () {
    $controller = new ReportController();
    return $controller->index();
});
$router->get('/reports/export-csv', function () {
    $controller = new ReportController();
    $controller->exportCsv();
});
$router->get('/reports/export-excel', function () {
    $controller = new ReportController();
    $controller->exportExcel();
});
$router->get('/reports/export-pdf', function () {
    $controller = new ReportController();
    $controller->exportPdf();
});

$router->get('/config/units', function () {
    $controller = new UnitController();
    return $controller->index();
});
$router->get('/config/units/create', function () {
    $controller = new UnitController();
    return $controller->create();
});
$router->get('/config/units/edit', function () {
    $controller = new UnitController();
    return $controller->edit();
});
$router->post('/config/units/store', function () {
    $controller = new UnitController();
    $controller->store();
});
$router->post('/config/units/update', function () {
    $controller = new UnitController();
    $controller->update();
});
$router->post('/config/units/destroy', function () {
    $controller = new UnitController();
    $controller->destroy();
});
$router->get('/config/users', function () {
    $controller = new UserController();
    return $controller->index();
});
$router->get('/config/users/create', function () {
    $controller = new UserController();
    return $controller->create();
});
$router->get('/config/users/edit', function () {
    $controller = new UserController();
    return $controller->edit();
});
$router->post('/config/users/store', function () {
    $controller = new UserController();
    $controller->store();
});
$router->post('/config/users/update', function () {
    $controller = new UserController();
    $controller->update();
});
$router->post('/config/users/destroy', function () {
    $controller = new UserController();
    $controller->destroy();
});
$router->get('/config/priority', function () {
    $controller = new ConfigController();
    return $controller->rules();
});
$router->post('/config/save-rules', function () {
    $controller = new ConfigController();
    $controller->saveRules();
});

$router->get('/logs', function () {
    $controller = new LogController();
    return $controller->index();
});

$router->get('/logout', function () {
    $controller = new AuthController();
    $controller->logout();
});

$router->dispatch($_SERVER['REQUEST_METHOD'], $route ? '/' . $route : '/');
