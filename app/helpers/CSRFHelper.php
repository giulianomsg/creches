<?php

namespace App\Helpers;

class CSRFHelper
{
    private static string $tokenName;

    public static function init(string $tokenName): void
    {
        self::$tokenName = $tokenName;
        if (!isset($_SESSION[self::$tokenName])) {
            $_SESSION[self::$tokenName] = bin2hex(random_bytes(32));
        }
    }

    public static function token(): string
    {
        return $_SESSION[self::$tokenName] ?? '';
    }

    public static function validate(?string $token): bool
    {
        return hash_equals($_SESSION[self::$tokenName] ?? '', $token ?? '');
    }
}
