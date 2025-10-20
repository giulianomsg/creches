<?php

require __DIR__ . '/../bootstrap.php';

use App\Controllers\AuthController;
use App\Controllers\ConfigController;
use App\Controllers\DashboardController;
use App\Controllers\LogController;
use App\Controllers\ReportController;
use App\Controllers\StudentController;
use App\Core\Router;
use App\Helpers\SessionHelper;

$config = require __DIR__ . '/../config/config.php';
SessionHelper::start($config['session_name']);

$route = $_GET['route'] ?? '';
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
    $controller = new ConfigController();
    return $controller->units();
});
$router->post('/config/save-unit', function () {
    $controller = new ConfigController();
    $controller->saveUnit();
});
$router->post('/config/delete-unit', function () {
    $controller = new ConfigController();
    $controller->deleteUnit();
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
