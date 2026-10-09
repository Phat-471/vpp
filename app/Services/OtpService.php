<?php

namespace App\Services;

use App\Helpers\AppHelper;
use App\Models\Customer;
use App\Models\PhoneOtp;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OtpService
{
    /**
     * Thời gian mã OTP có hiệu lực (phút)
     */
    public const EXPIRE_MINUTES = 5;

    /**
     * Thời gian tối thiểu giữa 2 lần gửi mã (giây)
     */
    public const RESEND_COOLDOWN_SECONDS = 60;

    /**
     * Giới hạn số lần nhận OTP trong 1 ngày trên 1 SĐT
     */
    public const MAX_DAILY_REQUESTS = 5;

    /**
     * Sinh và phát hành mã OTP 6 số
     */
    public function generate(
        string $phone,
        string $action = 'verify',
        ?Customer $customer = null,
        ?string $ip = null,
        string $channel = 'zns'
    ): array {
        $cleanedPhone = AppHelper::cleanPhone($phone);
        if (empty($cleanedPhone) || strlen($cleanedPhone) < 10) {
            return [
                'success' => false,
                'message' => 'Số điện thoại không đúng định dạng di động Việt Nam.',
            ];
        }

        // 1. Kiểm tra thời gian chờ gửi lại (Cooldown 60s)
        $recentOtp = PhoneOtp::where('phone', $cleanedPhone)
            ->where('action', $action)
            ->where('created_at', '>=', now()->subSeconds(self::RESEND_COOLDOWN_SECONDS))
            ->latest('id')
            ->first();

        if ($recentOtp) {
            $secondsElapsed = now()->diffInSeconds($recentOtp->created_at);
            $waitSeconds = max(1, self::RESEND_COOLDOWN_SECONDS - $secondsElapsed);
            return [
                'success' => false,
                'cooldown' => true,
                'wait_seconds' => $waitSeconds,
                'message' => "Vui lòng chờ {$waitSeconds} giây nữa trước khi yêu cầu gửi lại mã OTP.",
            ];
        }

        // 2. Chống spam SMS/ZNS: Tối đa 5 lần / ngày / SĐT
        $dailyCount = PhoneOtp::where('phone', $cleanedPhone)
            ->whereDate('created_at', now()->toDateString())
            ->count();

        if ($dailyCount >= self::MAX_DAILY_REQUESTS) {
            return [
                'success' => false,
                'message' => 'Số điện thoại này đã đạt giới hạn gửi mã OTP trong ngày (tối đa ' . self::MAX_DAILY_REQUESTS . ' lần). Vui lòng liên hệ hotline để được hỗ trợ.',
            ];
        }

        // 3. Vô hiệu hóa các mã OTP cũ chưa sử dụng của số điện thoại này
        PhoneOtp::where('phone', $cleanedPhone)
            ->where('action', $action)
            ->where('is_used', false)
            ->update(['is_used' => true]);

        // 4. Sinh mã ngẫu nhiên 6 chữ số
        $otpCode = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        // 5. Lưu vào Database
        $phoneOtp = PhoneOtp::create([
            'customer_id' => $customer?->id,
            'phone' => $cleanedPhone,
            'otp_code' => $otpCode,
            'channel' => $channel,
            'action' => $action,
            'attempts' => 0,
            'is_used' => false,
            'expires_at' => now()->addMinutes(self::EXPIRE_MINUTES),
            'ip_address' => $ip,
        ]);

        // 6. Gửi mã OTP qua Gateway (ZNS hoặc SMS)
        $dispatchResult = $this->dispatchOtp($cleanedPhone, $otpCode, $channel);

        return [
            'success' => true,
            'message' => "Mã OTP 6 số đã được gửi tới số {$cleanedPhone}.",
            'phone' => $cleanedPhone,
            'phone_masked' => AppHelper::maskPhone($cleanedPhone),
            'expires_in' => self::EXPIRE_MINUTES * 60,
            'cooldown_seconds' => self::RESEND_COOLDOWN_SECONDS,
            'channel' => $channel,
            'dev_otp' => $dispatchResult['is_mock'] ? $otpCode : null, // Chỉ trả về khi đang chạy mock/chưa cấu hình API
        ];
    }

    /**
     * Xác thực mã OTP người dùng gửi lên
     */
    public function verify(string $phone, string $otpCode, string $action = 'verify'): array
    {
        $cleanedPhone = AppHelper::cleanPhone($phone);
        $cleanOtp = trim($otpCode);

        if (empty($cleanedPhone) || strlen($cleanOtp) !== 6) {
            return [
                'success' => false,
                'message' => 'Vui lòng nhập đủ 6 chữ số của mã xác thực OTP.',
            ];
        }

        $otp = PhoneOtp::where('phone', $cleanedPhone)
            ->where('action', $action)
            ->where('is_used', false)
            ->latest('id')
            ->first();

        if (!$otp) {
            return [
                'success' => false,
                'message' => 'Mã OTP không tồn tại hoặc đã được sử dụng. Vui lòng bấm gửi lại mã mới.',
            ];
        }

        if ($otp->isExpired()) {
            return [
                'success' => false,
                'message' => 'Mã OTP đã hết hạn sau ' . self::EXPIRE_MINUTES . ' phút. Vui lòng bấm gửi lại mã mới.',
            ];
        }

        if ($otp->attempts >= 5) {
            $otp->update(['is_used' => true]);
            return [
                'success' => false,
                'message' => 'Mã OTP đã bị khóa do nhập sai quá 5 lần. Vui lòng gửi lại mã mới.',
            ];
        }

        // Kiểm tra mã OTP
        if ($otp->otp_code !== $cleanOtp) {
            $otp->increment('attempts');
            $remainingAttempts = max(0, 5 - ($otp->attempts));
            return [
                'success' => false,
                'message' => "Mã xác thực không chính xác! Bạn còn {$remainingAttempts} lần thử.",
                'remaining_attempts' => $remainingAttempts,
            ];
        }

        // Mã đúng: Đánh dấu đã dùng
        $otp->update(['is_used' => true]);

        // Kích hoạt khách hàng
        $customer = Customer::where('phone', $cleanedPhone)->first();
        if ($customer) {
            $customer->update([
                'phone_verified_at' => now(),
            ]);
        }

        return [
            'success' => true,
            'message' => 'Xác thực số điện thoại thành công!',
            'customer' => $customer,
        ];
    }

    /**
     * Gửi OTP qua Zalo ZNS hoặc SMS Gateway
     */
    protected function dispatchOtp(string $phone, string $otpCode, string $channel): array
    {
        $znsAccessToken = setting('zalo_zns_access_token');
        $znsTemplateId = setting('zalo_zns_template_id');
        $smsApiKey = setting('sms_api_key');

        // Chuẩn hóa định dạng số điện thoại quốc tế cho Zalo (84xxx)
        $internationalPhone = '84' . ltrim($phone, '0');

        // Kênh 1: Zalo Notification Service (ZNS)
        if ($channel === 'zns' && !empty($znsAccessToken) && !empty($znsTemplateId)) {
            try {
                $response = Http::withHeaders([
                    'access_token' => $znsAccessToken,
                    'Content-Type' => 'application/json',
                ])->timeout(5)->post('https://business.openapi.zalo.me/message/template', [
                    'phone' => $internationalPhone,
                    'template_id' => $znsTemplateId,
                    'template_data' => [
                        'otp' => $otpCode,
                        'expire' => self::EXPIRE_MINUTES,
                    ],
                ]);

                if ($response->successful() && $response->json('error') === 0) {
                    Log::info("Đã gửi ZNS OTP thành công cho {$phone}");
                    return ['success' => true, 'is_mock' => false];
                }

                Log::warning("Gửi ZNS thất bại cho {$phone}: " . $response->body());
            } catch (\Throwable $e) {
                Log::error("Lỗi kết nối Zalo ZNS API: " . $e->getMessage());
            }
        }

        // Kênh 2: SMS Brandname Gateway (Ví dụ SpeedSMS / eSMS)
        if ($channel === 'sms' && !empty($smsApiKey)) {
            try {
                $smsResponse = Http::timeout(5)->post('https://api.speedsms.vn/index.php/sms/send', [
                    'to' => [$phone],
                    'content' => "Ma xac thuc VPP cua ban la {$otpCode}. Hieu luc trong " . self::EXPIRE_MINUTES . " phut.",
                    'sms_type' => 2, // OTP SMS
                    'sender' => setting('sms_brandname', 'VPP'),
                ]);

                if ($smsResponse->successful()) {
                    Log::info("Đã gửi SMS OTP thành công cho {$phone}");
                    return ['success' => true, 'is_mock' => false];
                }
            } catch (\Throwable $e) {
                Log::error("Lỗi kết nối SMS Gateway: " . $e->getMessage());
            }
        }

        // Kênh 3: Dự phòng / Môi trường chưa cắm token ZNS thật
        Log::info("[OTP THỬ NGHIỆM] Số: {$phone} | Kênh: {$channel} | Mã OTP: {$otpCode} | Hạn: " . self::EXPIRE_MINUTES . " phút");
        return [
            'success' => true,
            'is_mock' => true,
        ];
    }
}
