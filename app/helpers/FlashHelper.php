<?php

namespace App\Helpers;

class FlashHelper
{
    public static function add(string $type, string $message): void
    {
        $_SESSION['flash'][] = compact('type', 'message');
    }

    public static function get(): array
    {
        $messages = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return $messages;
    }
}
