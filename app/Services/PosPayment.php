<?php

namespace App\Services;

use App\Helpers\AppHelper;
use App\Models\Order;
use App\Models\Setting;

final class PosPayment
{
    public function qrUrl(Order $order): ?string
    {
        $bank = trim((string) Setting::get('vietqr_bank_code', ''));
        $account = trim((string) Setting::get('vietqr_account_number', ''));
        $name = trim((string) Setting::get('vietqr_account_name', ''));
        if (! preg_match('/^[a-zA-Z0-9]+$/', $bank) || ! preg_match('/^[0-9]+$/', $account) || $name === '') {
            return null;
        }

        return AppHelper::generateVietQrUrl((float) $order->grand_total, $order->order_code, $bank, $account, $name);
    }
}
