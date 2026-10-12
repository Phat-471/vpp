<?php

namespace App\Http\Controllers;

use App\Services\PaymentWebhookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function sepay(Request $request, PaymentWebhookService $webhooks)
    {
        if (! $webhooks->authenticate($request, 'sepay')) {
            $configured = setting('sepay_webhook_token') ?: env('SEPAY_WEBHOOK_TOKEN');
            $status = empty($configured) ? 503 : 401;
            Log::warning('SePay webhook authentication failed');

            return response()->json(['success' => false, 'message' => 'Webhook authentication failed.'], $status);
        }

        $payload = $request->all();
        $id = trim((string) ($payload['id'] ?? ''));
        $amount = $payload['transferAmount'] ?? null;
        if ($id === '' || strlen($id) > 100 || ! is_numeric($amount) || (float) $amount <= 0 || (float) $amount > 999999999999.99) {
            return response()->json(['success' => false, 'message' => 'Invalid transaction.'], 422);
        }
        if (isset($payload['transferType']) && strtoupper((string) $payload['transferType']) !== 'IN') {
            return response()->json(['success' => false, 'message' => 'Only incoming transfers are accepted.'], 422);
        }

        $description = mb_substr((string) ($payload['content'] ?? ''), 0, 2000);
        $reference = $this->extractReferenceCode($description);
        $result = $webhooks->record('sepay', $id, [
            'reference_code' => $reference ?: 'UNKNOWN',
            'amount' => $amount,
            'account_number' => mb_substr((string) ($payload['accountNumber'] ?? ''), 0, 50),
            'bank_brand_name' => mb_substr((string) ($payload['gateway'] ?? 'SePay'), 0, 50),
            'description' => $description,
            'transaction_time' => now(),
            'raw_payload' => array_intersect_key($payload, array_flip(['id', 'referenceCode', 'transferAmount', 'content', 'accountNumber', 'gateway', 'transferType'])),
        ]);

        if (! $result['duplicate']) {
            Log::info('SePay webhook recorded', ['transaction_id' => $id, 'reference_code' => $reference]);
        }

        return response()->json([
            'success' => true,
            'matched' => $result['matched'],
            'reference_code' => $reference,
            'amount' => $amount,
            'transaction_id' => $id,
            'duplicate' => $result['duplicate'],
        ]);
    }

    public function casso(Request $request, PaymentWebhookService $webhooks)
    {
        if (! $webhooks->authenticate($request, 'casso')) {
            $configured = setting('casso_webhook_token') ?: env('CASSO_WEBHOOK_TOKEN');
            $status = empty($configured) ? 503 : 401;
            Log::warning('Casso webhook authentication failed');

            return response()->json(['error' => $status, 'messages' => 'Webhook authentication failed.'], $status);
        }

        $payload = $request->all();
        $records = $payload['data'] ?? [];
        if (empty($records) && isset($payload['description'])) {
            $records = [$payload];
        }

        $processed = $duplicates = $rejected = 0;
        foreach ($records as $item) {
            if (! is_array($item)) {
                $rejected++;

                continue;
            }

            $id = trim((string) ($item['tid'] ?? ($item['id'] ?? '')));
            $amount = $item['amount'] ?? null;
            if ($id === '' || strlen($id) > 100 || ! is_numeric($amount) || (float) $amount <= 0 || (float) $amount > 999999999999.99) {
                $rejected++;

                continue;
            }

            $description = mb_substr((string) ($item['description'] ?? ''), 0, 2000);
            $reference = $this->extractReferenceCode($description);
            $result = $webhooks->record('casso', $id, [
                'reference_code' => $reference ?: 'UNKNOWN',
                'amount' => $amount,
                'account_number' => mb_substr((string) ($item['bank_sub_acc_id'] ?? ($item['subAccId'] ?? '')), 0, 50),
                'bank_brand_name' => 'Casso Bank',
                'description' => $description,
                'transaction_time' => now(),
                'raw_payload' => array_intersect_key($item, array_flip(['tid', 'id', 'amount', 'description', 'bank_sub_acc_id', 'subAccId'])),
            ]);
            if ($result['duplicate']) {
                $duplicates++;
            } elseif ($result['matched']) {
                $processed++;
            }
        }

        return response()->json([
            'error' => 0,
            'messages' => 'Success',
            'processed_count' => $processed,
            'duplicate_count' => $duplicates,
            'rejected_count' => $rejected,
        ]);
    }

    private function extractReferenceCode(string $content): ?string
    {
        if (preg_match('/(SC|HD)[\s\-_]?(\d{2,10})/i', $content, $matches)
            || preg_match('/(VPP)[\s\-_]?(\d{2,10})/i', $content, $matches)) {
            return strtoupper($matches[1].$matches[2]);
        }

        return null;
    }
}
