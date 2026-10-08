<?php

namespace App\Services;

use App\Exceptions\TaxLookupUnavailable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Throwable;

final class BusinessTaxLookup
{
    public static function normalize(string $value): string
    {
        $value = trim($value);

        return preg_match('/^[0-9]{13}$/D', $value) ? substr($value, 0, 10).'-'.substr($value, 10) : $value;
    }

    public function find(string $taxCode): ?array
    {
        $taxCode = self::normalize($taxCode);
        if (preg_match('/^[0-9]{12}$/D', $taxCode)) {
            throw new TaxLookupUnavailable('Mã 12 số thuộc nhóm cá nhân. Nguồn miễn phí đang dùng không hỗ trợ tự điền; vui lòng nhập tên và địa chỉ xuất hóa đơn thủ công.');
        }
        if (! preg_match('/^[0-9]{10}(?:-[0-9]{3})?$/D', $taxCode)) {
            throw ValidationException::withMessages(['company_tax_id' => 'Nhập MST doanh nghiệp gồm 10 số hoặc MST chi nhánh gồm 13 số.']);
        }
        $provider = config('services.tax_lookup.provider', 'vietqr');
        $key = 'tax-lookup:'.$provider.':'.$taxCode;
        if (Cache::has($key)) {
            return Cache::get($key) ?: null;
        }
        $rateKey = 'tax-lookup:'.hash('sha256', (string) request()->ip());
        if (RateLimiter::tooManyAttempts($rateKey, 10)) {
            throw new TaxLookupUnavailable('Đã tra cứu nhiều lần. Vui lòng đợi một phút hoặc nhập thông tin thủ công.');
        }
        RateLimiter::hit($rateKey, 60);
        try {
            $http = Http::acceptJson()->connectTimeout(3)->timeout(6)->withOptions([
                'allow_redirects' => false,
                'verify' => config('services.tax_lookup.ca_bundle') ?: true,
            ]);
            if ($provider === 'xinvoice') {
                $client = config('services.tax_lookup.client_id');
                $apiKey = config('services.tax_lookup.api_key');
                if (! $client || ! $apiKey) {
                    throw new TaxLookupUnavailable('Chưa cấu hình khóa API Xinvoice. Có thể nhập thông tin thủ công.');
                }
                $response = $http->withHeaders(['client-id' => $client, 'api-key' => $apiKey])
                    ->get('https://api.xinvoice.vn/gdt-api/tax-payer/'.$taxCode);
                $data = $response->json();
                $returnedCode = $data['taxID'] ?? '';
            } elseif ($provider === 'vietqr') {
                if (now()->gte('2027-03-01')) {
                    throw new TaxLookupUnavailable('Nguồn VietQR đã đến ngày ngừng hỗ trợ. Cần cấu hình Xinvoice hoặc nhập thủ công.');
                }
                $response = $http->get('https://api.vietqr.io/v2/business/'.$taxCode);
                $payload = $response->json();
                $data = ($payload['code'] ?? null) === '00' ? ($payload['data'] ?? null) : null;
                $returnedCode = $data['id'] ?? '';
            } else {
                throw new TaxLookupUnavailable('Nguồn tra cứu chưa được cấu hình đúng. Có thể nhập thủ công.');
            }
            if (! $response->successful()) {
                throw new TaxLookupUnavailable('Nguồn tra cứu đang bận. Vui lòng thử lại hoặc nhập thông tin thủ công.');
            }
            if (! $data) {
                Cache::put($key, [], now()->addMinutes(5));

                return null;
            }
            if (! is_string($returnedCode) || self::normalize($returnedCode) !== $taxCode
                || ! is_string($data['name'] ?? null) || ! is_string($data['address'] ?? null)
                || trim($data['name']) === '' || trim($data['address']) === ''
                || mb_strlen($data['name']) > 255 || mb_strlen($data['address']) > 255) {
                throw new TaxLookupUnavailable('Thông tin tra cứu chưa đầy đủ hoặc không khớp MST. Vui lòng nhập thủ công.');
            }
            $result = ['tax_code' => $taxCode, 'name' => trim($data['name']), 'address' => trim($data['address']), 'source' => $provider];
            Cache::put($key, $result, now()->addHours(24));

            return $result;
        } catch (TaxLookupUnavailable $e) {
            throw $e;
        } catch (Throwable) {
            // Never expose provider payloads, credentials or customer data in responses/logs.
            throw new TaxLookupUnavailable('Không kết nối được nguồn tra cứu. Vui lòng thử lại hoặc nhập thủ công.');
        }
    }
}
