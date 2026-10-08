<?php

namespace App\Support;

final class ContactFormat
{
    public const PHONE_PATTERN = '/^(?:0[35789][0-9]{8}|02[0-9]{9})$/D';

    public static function phone(string $value): string
    {
        $value = trim($value);
        if (! preg_match('/^\+?[0-9 ().-]+$/D', $value)) {
            return $value;
        }
        $digits = preg_replace('/[^0-9]/', '', $value);
        if (str_starts_with($digits, '84')) {
            $digits = '0'.substr($digits, 2);
        }

        return $digits;
    }
}
