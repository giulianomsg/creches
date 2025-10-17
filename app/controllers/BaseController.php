<?php

namespace App\Controllers;

use App\Helpers\CSRFHelper;
use App\Helpers\SessionHelper;

abstract class BaseController
{
    protected array $config;

    public function __construct()
    {
        $this->config = require __DIR__ . '/../../config/config.php';
        SessionHelper::start($this->config['session_name']);
        CSRFHelper::init($this->config['security']['csrf_token_name']);
    }

    protected function render(string $view, array $data = []): string
    {
        extract($data);
        $config = $this->config;
        $content = __DIR__ . '/../views/' . $view . '.php';
        ob_start();
        include __DIR__ . '/../views/layouts/main.php';
        return ob_get_clean();
    }

    protected function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . $this->config['base_url'] . $path);
        exit;
    }
}
