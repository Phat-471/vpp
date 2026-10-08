<?php

namespace App\Services;

use App\Helpers\AppHelper;
use App\Models\Customer;
use App\Models\ZaloVerification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ZaloAuthService
{
    /**
     * Tạo phiên xác thực QR mới với mã token 40 ký tự
     */
    public function createSession(?string $ip = null, ?string $userAgent = null): ZaloVerification
    {
        // Tự động dọn dẹp các phiên chờ quá 1 giờ
        ZaloVerification::where('status', 'pending')
            ->where('created_at', '<', now()->subHours(1))
            ->delete();

        $token = Str::random(40);

        return ZaloVerification::create([
            'token' => $token,
            'status' => 'pending',
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'expires_at' => now()->addMinutes(5),
        ]);
    }

    /**
     * Tạo phiên xác thực QR Zalo gắn liền với Customer vừa đăng ký form
     */
    public function createSessionForCustomer(Customer $customer, ?string $ip = null, ?string $userAgent = null): ZaloVerification
    {
        ZaloVerification::where('customer_id', $customer->id)
            ->where('status', 'pending')
            ->delete();

        $token = Str::random(40);

        return ZaloVerification::create([
            'token' => $token,
            'status' => 'pending',
            'customer_id' => $customer->id,
            'phone' => $customer->phone,
            'name' => $customer->name,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'expires_at' => now()->addMinutes(5),
        ]);
    }

    /**
     * Tạo dữ liệu quét mã QR (Link xác thực và ảnh QR Code)
     */
    public function getSessionQrData(ZaloVerification $verification): array
    {
        $verifyUrl = route('zalo.verify.prompt', ['token' => $verification->token]);
        $qrImageUrl = "https://api.qrserver.com/v1/create-qr-code/?size=260x260&data=" . urlencode($verifyUrl);

        return [
            'token' => $verification->token,
            'verify_url' => $verifyUrl,
            'qr_image_url' => $qrImageUrl,
            'expires_at' => $verification->expires_at->toIso8601String(),
            'seconds_remaining' => max(0, now()->diffInSeconds($verification->expires_at, false)),
        ];
    }

    /**
     * Xác nhận chia sẻ thông tin từ Zalo -> Lưu vào Database Customer
     */
    public function confirmVerification(string $token, string $name, string $phone, ?string $zaloId = null, ?string $avatar = null): array
    {
        return DB::transaction(function () use ($token, $name, $phone, $zaloId, $avatar) {
            $verification = ZaloVerification::where('token', $token)->lockForUpdate()->first();

            if (!$verification) {
                return ['success' => false, 'message' => 'Phiên xác thực không tồn tại hoặc đã hết hạn.'];
            }

            if ($verification->isExpired()) {
                $verification->update(['status' => 'expired']);
                return ['success' => false, 'message' => 'Mã QR đã hết hạn, vui lòng bấm tải lại mã mới.'];
            }

            if ($verification->isVerified()) {
                return [
                    'success' => true,
                    'already_verified' => true,
                    'customer' => $verification->customer,
                ];
            }

            $cleanedPhone = AppHelper::cleanPhone($phone);
            if (empty($cleanedPhone) || strlen($cleanedPhone) < 9) {
                return ['success' => false, 'message' => 'Số điện thoại không hợp lệ.'];
            }

            // Tìm khách hàng có sẵn theo SĐT hoặc zalo_id, nếu chưa có thì tạo mới
            $customer = Customer::where('phone', $cleanedPhone)
                ->orWhere(function ($q) use ($zaloId) {
                    if (!empty($zaloId)) {
                        $q->where('zalo_id', $zaloId);
                    }
                })
                ->first();

            $displayName = trim($name) ?: ('Khách Zalo ' . substr($cleanedPhone, -4));

            if ($customer) {
                $customer->update([
                    'name' => $customer->name ?: $displayName,
                    'phone' => $cleanedPhone,
                    'phone_last4' => substr($cleanedPhone, -4),
                    'zalo_id' => $zaloId ?: $customer->zalo_id,
                    'zalo_name' => $displayName ?: $customer->zalo_name,
                    'zalo_avatar' => $avatar ?: $customer->zalo_avatar,
                    'phone_verified_at' => now(),
                ]);
            } else {
                $customer = Customer::create([
                    'name' => $displayName,
                    'phone' => $cleanedPhone,
                    'phone_last4' => substr($cleanedPhone, -4),
                    'zalo_id' => $zaloId,
                    'zalo_name' => $displayName,
                    'zalo_avatar' => $avatar,
                    'phone_verified_at' => now(),
                    'password' => bcrypt(Str::random(16)),
                ]);
            }

            // Cập nhật trạng thái phiên xác thực
            $verification->update([
                'status' => 'verified',
                'customer_id' => $customer->id,
                'name' => $customer->name,
                'phone' => $cleanedPhone,
                'zalo_id' => $zaloId,
                'avatar' => $avatar,
                'verified_at' => now(),
            ]);

            return [
                'success' => true,
                'message' => 'Xác thực tài khoản qua Zalo thành công!',
                'customer' => $customer,
            ];
        });
    }

    /**
     * Kiểm tra trạng thái phiên xác thực (để web tự động nhận biết)
     */
    public function checkStatus(string $token): array
    {
        $verification = ZaloVerification::with('customer')->where('token', $token)->first();

        if (!$verification) {
            return ['status' => 'not_found', 'verified' => false];
        }

        if ($verification->isExpired() && $verification->status === 'pending') {
            $verification->update(['status' => 'expired']);
            return ['status' => 'expired', 'verified' => false];
        }

        if ($verification->isVerified() && $verification->customer) {
            return [
                'status' => 'verified',
                'verified' => true,
                'customer_id' => $verification->customer->id,
                'customer_name' => $verification->customer->name,
                'customer_phone' => AppHelper::maskPhone($verification->customer->phone),
            ];
        }

        return [
            'status' => 'pending',
            'verified' => false,
            'seconds_remaining' => max(0, now()->diffInSeconds($verification->expires_at, false)),
        ];
    }
}
