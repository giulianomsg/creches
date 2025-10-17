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
}
