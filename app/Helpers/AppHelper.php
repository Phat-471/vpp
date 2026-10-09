<?php

namespace App\Helpers;

class AppHelper
{
    /**
     * Format VND currency string cleanly (VD: 175.000 ₫)
     */
    public static function formatMoney(float|int|null $amount): string
    {
        return number_format((float) ($amount ?? 0), 0, ',', '.') . ' ₫';
    }

    /**
     * Clean phone number by removing non-digits
     */
    public static function cleanPhone(?string $phone): string
    {
        if (!$phone) {
            return '';
        }
        return preg_replace('/\D/', '', $phone);
    }

    /**
     * Extract the last 4 digits of a phone number
     */
    public static function extractPhoneLast4(?string $phone): string
    {
        $cleaned = self::cleanPhone($phone);
        return strlen($cleaned) >= 4 ? substr($cleaned, -4) : str_pad($cleaned, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Mask phone number for privacy display (VD: 0912****78)
     */
    public static function maskPhone(?string $phone): string
    {
        $cleaned = self::cleanPhone($phone);
        if (strlen($cleaned) < 7) {
            return $cleaned;
        }
        return substr($cleaned, 0, 4) . '****' . substr($cleaned, -2);
    }

    /**
     * Generate dynamic VietQR image URL directly (Zero external heavy library required)
     */
    public static function generateVietQrUrl(float|int $amount, string $referenceCode, ?string $bankBin = null, ?string $accountNumber = null, ?string $accountName = null): string
    {
        $bank = $bankBin ?: \App\Models\Setting::get('vietqr_bank_code', 'MB');
        $acc = $accountNumber ?: \App\Models\Setting::get('vietqr_account_number', '190333888999');
        $name = $accountName ?: \App\Models\Setting::get('vietqr_account_name', 'NGUYEN VAN A');

        $safeAmount = max(0, (int) round($amount));
        $encodedRef = urlencode(strtoupper(trim($referenceCode)));
        $encodedName = urlencode($name);

        return "https://img.vietqr.io/image/{$bank}-{$acc}-compact2.png?amount={$safeAmount}&addInfo={$encodedRef}&accountName={$encodedName}";
    }

    /**
     * Generate lookup URL with QR code (Safe anti-IDOR link)
     */
    public static function generateLookupUrl(string $ticketCode, string $phoneLast4): string
    {
        return route('lookup.view', [
            'code' => $ticketCode,
            'phone4' => $phoneLast4,
        ]);
    }
}
