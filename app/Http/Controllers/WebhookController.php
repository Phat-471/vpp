<?php

namespace App\Http\Controllers;

use App\Models\PaymentTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Handle incoming webhook from SePay
     */
    public function sepay(Request $request)
    {
        $payload = $request->all();
        Log::info('SePay Webhook Received:', $payload);

        $amount = (float) ($payload['transferAmount'] ?? 0);
        $content = (string) ($payload['content'] ?? '');
        $txId = (string) ($payload['id'] ?? uniqid());
        $accountNo = (string) ($payload['accountNumber'] ?? '');
        $gatewayName = (string) ($payload['gateway'] ?? 'SePay');

        $code = $this->extractReferenceCode($content);

        $tx = PaymentTransaction::create([
            'gateway' => 'sepay',
            'transaction_id' => $txId,
            'reference_code' => $code ?: 'UNKNOWN',
            'amount' => $amount,
            'account_number' => $accountNo,
            'bank_brand_name' => $gatewayName,
            'description' => $content,
            'transaction_time' => now(),
            'raw_payload' => $payload,
            'status' => 'processed',
        ]);

        $applied = $tx->matchAndApply();

        return response()->json([
            'success' => true,
            'matched' => $applied,
            'reference_code' => $code,
            'amount' => $amount,
        ]);
    }

    /**
     * Handle incoming webhook from Casso
     */
    public function casso(Request $request)
    {
        $payload = $request->all();
        Log::info('Casso Webhook Received:', $payload);

        $records = $payload['data'] ?? [];
        if (empty($records) && isset($payload['description'])) {
            $records = [$payload];
        }

        $processedCount = 0;

        foreach ($records as $item) {
            $amount = (float) ($item['amount'] ?? 0);
            $content = (string) ($item['description'] ?? '');
            $txId = (string) ($item['tid'] ?? ($item['id'] ?? uniqid()));
            $accountNo = (string) ($item['bank_sub_acc_id'] ?? '');

            $code = $this->extractReferenceCode($content);

            $tx = PaymentTransaction::create([
                'gateway' => 'casso',
                'transaction_id' => $txId,
                'reference_code' => $code ?: 'UNKNOWN',
                'amount' => $amount,
                'account_number' => $accountNo,
                'bank_brand_name' => 'Casso Bank',
                'description' => $content,
                'transaction_time' => now(),
                'raw_payload' => $item,
                'status' => 'processed',
            ]);

            if ($tx->matchAndApply()) {
                $processedCount++;
            }
        }

        return response()->json([
            'error' => 0,
            'messages' => 'Success',
            'processed_count' => $processedCount,
        ]);
    }

    /**
     * Regex extract order or ticket code from bank transfer message
     * Matches patterns like: SC260001, SC-260001, HD260001, HD-260001, VPP1001
     */
    private function extractReferenceCode(string $content): ?string
    {
        $cleaned = strtoupper($content);

        // Pattern 1: SC26XXXX or HD26XXXX
        if (preg_match('/(SC|HD)[\s\-_]?(\d{6,8})/i', $cleaned, $matches)) {
            return $matches[1] . $matches[2];
        }

        // Pattern 2: SCXXXX or HDXXXX
        if (preg_match('/(SC|HD)[\s\-_]?(\d{4,5})/i', $cleaned, $matches)) {
            return $matches[1] . $matches[2];
        }

        // Pattern 3: General prefix
        if (preg_match('/(VPP)[\s\-_]?(\d+)/i', $cleaned, $matches)) {
            return $matches[1] . $matches[2];
        }

        return null;
    }
}
