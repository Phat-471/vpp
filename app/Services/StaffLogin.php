<?php

namespace App\Services;

use App\Helpers\AppHelper;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class StaffLogin
{
    public function findUser(string $identifier): ?User
    {
        $identifier = trim($identifier);
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            return User::whereRaw('LOWER(email) = ?', [mb_strtolower($identifier)])->first();
        }
        if (! preg_match('/^\+?[0-9 ().-]+$/', $identifier)) {
            return null;
        }
        $phone = AppHelper::cleanPhone($identifier);
        if (str_starts_with($phone, '84') && in_array(strlen($phone), [11, 12], true)) {
            $phone = '0'.substr($phone, 2);
        }
        if (! preg_match('/^0[0-9]{8,10}$/', $phone)) {
            return null;
        }
        // Normalize legacy display formatting without rewriting existing employee records.
        $normalized = "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(phone, ' ', ''), '.', ''), '-', ''), '(', ''), ')', ''), '+', '')";
        $users = User::whereIn(DB::raw($normalized), [$phone, '84'.substr($phone, 1)])->limit(2)->get();

        // A phone shared by two accounts must not select an arbitrary employee.
        return $users->count() === 1 ? $users->first() : null;
    }

    public function throttleKey(string $identifier): string
    {
        $identifier = mb_strtolower(trim($identifier));
        if (! str_contains($identifier, '@')) {
            $identifier = AppHelper::cleanPhone($identifier);
            if (str_starts_with($identifier, '84')) {
                $identifier = '0'.substr($identifier, 2);
            }
        }

        return hash('sha256', $identifier);
    }
}
