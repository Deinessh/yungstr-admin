<?php

namespace App\Support;

class AuthIdentifier
{
    public static function normalizePhone(string $value): string
    {
        $digits = preg_replace('/\D/', '', $value);

        if (strlen($digits) === 12 && str_starts_with($digits, '91')) {
            $digits = substr($digits, 2);
        }

        return $digits;
    }

    /**
     * @return array{type: 'email'|'phone', value: string}
     */
    public static function parse(string $login): array
    {
        $login = trim($login);

        if (str_contains($login, '@')) {
            return ['type' => 'email', 'value' => strtolower($login)];
        }

        return ['type' => 'phone', 'value' => self::normalizePhone($login)];
    }

    public static function isEmail(string $login): bool
    {
        return str_contains(trim($login), '@');
    }
}
