<?php

namespace App\Support;

class AddressParser
{
    public static function extractPincode(?string $address): ?string
    {
        if (! $address) {
            return null;
        }

        if (preg_match('/\b(\d{6})\b/', $address, $matches)) {
            return $matches[1];
        }

        return null;
    }

    public static function splitName(string $fullName): array
    {
        $parts = preg_split('/\s+/', trim($fullName)) ?: [];
        $first = array_shift($parts) ?: 'Customer';
        $last = implode(' ', $parts);

        return [$first, $last ?: null];
    }
}
