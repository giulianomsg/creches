<?php

namespace App\Helpers;

class ValidationHelper
{
    public static function required(array $data, array $fields): array
    {
        $errors = [];
        foreach ($fields as $field => $message) {
            $value = trim((string)($data[$field] ?? ''));
            if ($value === '') {
                $errors[$field] = $message;
            }
        }

        return $errors;
    }

    public static function date(string $date, string $format = 'Y-m-d'): bool
    {
        $dt = \DateTime::createFromFormat($format, $date);
        return $dt && $dt->format($format) === $date;
    }

    public static function in(string $value, array $options): bool
    {
        return in_array($value, $options, true);
    }

    public static function email(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function cpf(?string $cpf): bool
    {
        if ($cpf === null) {
            return false;
        }

        $digits = preg_replace('/[^0-9]/', '', $cpf);
        if (strlen($digits) !== 11) {
            return false;
        }

        if (preg_match('/^(\d)\1{10}$/', $digits)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            $sum = 0;
            for ($c = 0; $c < $t; $c++) {
                $sum += (int)$digits[$c] * (($t + 1) - $c);
            }
            $digit = ((10 * $sum) % 11) % 10;
            if ((int)$digits[$t] !== $digit) {
                return false;
            }
        }

        return true;
    }
}
