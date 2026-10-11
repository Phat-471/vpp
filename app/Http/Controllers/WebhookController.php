<?php

namespace App\Http\Controllers;

use App\Models\PaymentTransaction;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Handle incoming webhook from SePay
     */
    public function sepay(Request $request)
    {
        // 1. Xác thực Token SePay nếu được cấu hình
        $configuredToken = Setting::get('sepay_webhook_token') ?: env('SEPAY_WEBHOOK_TOKEN');
        if (!empty($configuredToken)) {
            $authHeader = (string) ($request->header('Authorization') ?: $request->header('X-Api-Key'));
            $token = trim(preg_replace('/^(Apikey|Bearer)\s+/i', '', $authHeader));

            if ($token !== $configuredToken) {
                Log::warning('SePay Webhook Unauthorized attempt:', ['header' => $authHeader]);
                return response()->json([
                    'success' => false,
                    'message' => 'Token xác thực SePay không hợp lệ',
                ], 401);
            }
        }

        $payload = $request->all();
        Log::info('SePay Webhook Received:', $payload);

        $txId = (string) ($payload['id'] ?? '');
        if (empty($txId)) {
            $txId = (string) ($payload['referenceCode'] ?? uniqid('SP_'));
        }

        // 2. Chống Replay / Xử lý trùng lặp (Idempotent)
        $existing = PaymentTransaction::where('gateway', 'sepay')
            ->where('transaction_id', $txId)
            ->first();

        if ($existing) {
            Log::info("SePay Webhook Duplicate Ignored: {$txId}");
            return response()->json([
                'success' => true,
                'message' => 'Giao dịch đã được xử lý trước đó',
                'duplicate' => true,
                'transaction_id' => $txId,
                'matched' => $existing->status === 'processed',
            ]);
        }

        $amount = (float) ($payload['transferAmount'] ?? 0);
        $content = (string) ($payload['content'] ?? '');
        $accountNo = (string) ($payload['accountNumber'] ?? '');
        $gatewayName = (string) ($payload['gateway'] ?? 'SePay');
        $code = $this->extractReferenceCode($content);

        // 3. Xử lý ghi nhận và khớp lệnh trong DB::transaction
        $applied = false;
        DB::transaction(function () use ($txId, $code, $amount, $accountNo, $gatewayName, $content, $payload, &$applied) {
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
                'status' => 'pending',
            ]);

            $applied = $tx->matchAndApply();
        });

        return response()->json([
            'success' => true,
            'matched' => $applied,
            'reference_code' => $code,
            'amount' => $amount,
            'transaction_id' => $txId,
        ]);
    }

    /**
     * Handle incoming webhook from Casso
     */
    public function casso(Request $request)
    {
        // 1. Xác thực Secure Token Casso nếu được cấu hình
        $configuredToken = Setting::get('casso_webhook_token') ?: env('CASSO_WEBHOOK_TOKEN');
        if (!empty($configuredToken)) {
            $incomingToken = (string) ($request->header('secure-token') ?: $request->header('Authorization') ?: $request->header('X-Api-Key'));
            $token = trim(preg_replace('/^(Bearer|Apikey)\s+/i', '', $incomingToken));

            if ($token !== $configuredToken) {
                Log::warning('Casso Webhook Unauthorized attempt:', ['header' => $incomingToken]);
                return response()->json([
                    'error' => 401,
                    'messages' => 'Token xác thực Casso không hợp lệ',
                ], 401);
            }
        }

        $payload = $request->all();
        Log::info('Casso Webhook Received:', $payload);

        $records = $payload['data'] ?? [];
        if (empty($records) && isset($payload['description'])) {
            $records = [$payload];
        }

        $processedCount = 0;
        $duplicateCount = 0;

        foreach ($records as $item) {
            $txId = (string) ($item['tid'] ?? ($item['id'] ?? uniqid('CS_')));

            // 2. Chống Replay / Xử lý trùng lặp (Idempotent)
            $existing = PaymentTransaction::where('gateway', 'casso')
                ->where('transaction_id', $txId)
                ->first();

            if ($existing) {
                $duplicateCount++;
                continue;
            }

            $amount = (float) ($item['amount'] ?? 0);
            $content = (string) ($item['description'] ?? '');
            $accountNo = (string) ($item['bank_sub_acc_id'] ?? ($item['subAccId'] ?? ''));
            $code = $this->extractReferenceCode($content);

            DB::transaction(function () use ($txId, $code, $amount, $accountNo, $content, $item, &$processedCount) {
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
                    'status' => 'pending',
                ]);

                if ($tx->matchAndApply()) {
                    $processedCount++;
                }
            });
        }

        return response()->json([
            'error' => 0,
            'messages' => 'Success',
            'processed_count' => $processedCount,
            'duplicate_count' => $duplicateCount,
        ]);
    }

    /**
     * Regex extract order or ticket code from bank transfer message
     * Matches patterns like: SC260001, SC-260001, HD260001, HD-260001, VPP1001, VPP-1001
     */
    private function extractReferenceCode(string $content): ?string
    {
        $cleaned = strtoupper($content);

        // Pattern 1: SC / HD followed by numbers (e.g. HD260001, SC260001, HD-260001, SC_260001)
        if (preg_match('/(SC|HD)[\s\-_]?(\d{2,10})/i', $cleaned, $matches)) {
            return $matches[1] . $matches[2];
        }

        // Pattern 2: VPP followed by numbers (e.g. VPP1001, VPP-1001)
        if (preg_match('/(VPP)[\s\-_]?(\d{2,10})/i', $cleaned, $matches)) {
            return $matches[1] . $matches[2];
        }

        return null;
    }
}
