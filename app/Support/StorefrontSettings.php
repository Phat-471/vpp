<?php

namespace App\Support;

use App\Models\Setting;

final class StorefrontSettings
{
    /** Only public store information is passed to storefront views and print templates. */
    public function all(): array
    {
        $defaults = [
            // 1. Nhận diện thương hiệu & Thông tin
            'site_name' => 'VPP & Thiết Bị Máy In Ánh Dương',
            'site_slogan' => 'Tổng kho văn phòng phẩm & Dịch vụ kỹ thuật máy in',
            'site_logo' => null,
            'site_favicon' => null,
            'opening_hours' => '7h30 - 20h00 (Cả Thứ 7 & Chủ Nhật)',
            'notice_bar_enabled' => '1',
            'notice_bar_text' => '🎉 Khuyến mãi đầu tháng: Miễn phí vận chuyển cho đơn từ 500.000đ nội thành!',

            // 2. Liên hệ & Hỗ trợ
            'hotline' => '0901.234.567',
            'technical_hotline' => '0912.345.678',
            'zalo' => '0901.234.567',
            'email' => 'hotro@vppanhduong.vn',
            'address' => 'Số 123 Đường Văn Phòng Phẩm, Q.1, TP.HCM',
            'facebook_url' => 'https://facebook.com',
            'google_maps_iframe' => '',

            // 3. Thanh toán VietQR
            'vietqr_bank_code' => 'MB',
            'vietqr_bank_name' => 'Ngân hàng TMCP Quân Đội (MB Bank)',
            'vietqr_account_number' => '190333888999',
            'vietqr_account_name' => 'NGUYEN VAN A',

            // 4. Bán hàng & Vận chuyển & Thuế
            'shipping_fee_default' => 25000,
            'freeship_threshold' => 500000,
            'default_vat_rate' => 8,
            'default_low_stock_threshold' => 5,

            // 5. Pháp lý & Xuất hóa đơn VAT
            'company_name' => 'CÔNG TY TNHH THƯƠNG MẠI VÀ DỊCH VỤ ÁNH DƯƠNG',
            'company_tax_id' => '0109887766',
            'company_address' => 'Số 123 Đường Văn Phòng Phẩm, Phường Bến Nghé, Quận 1, TP.HCM',
            'invoice_email' => 'ketoan@vppanhduong.vn',

            // 6. Quy trình vận hành & Dịch vụ máy in
            'pos_shift_required' => '1',
            'warranty_period_days' => 30,
            'repair_turnaround_hours' => 24,
        ];

        $settings = [];
        foreach ($defaults as $key => $default) {
            $settings[$key] = Setting::get($key, $default);
        }

        $settings['hotline_url'] = 'tel:' . preg_replace('/[^0-9+]/', '', (string) $settings['hotline']);
        $settings['technical_hotline_url'] = 'tel:' . preg_replace('/[^0-9+]/', '', (string) ($settings['technical_hotline'] ?? $settings['hotline']));
        $settings['zalo_url'] = 'https://zalo.me/' . preg_replace('/[^0-9]/', '', (string) $settings['zalo']);
        $settings['freeship_label'] = number_format((float) $settings['freeship_threshold'], 0, ',', '.') . ' ₫';
        $settings['shipping_fee_label'] = number_format((float) $settings['shipping_fee_default'], 0, ',', '.') . ' ₫';

        $settings['logo_url'] = !empty($settings['site_logo'])
            ? asset('storage/' . $settings['site_logo'])
            : null;

        $settings['favicon_url'] = !empty($settings['site_favicon'])
            ? asset('storage/' . $settings['site_favicon'])
            : null;

        return $settings;
    }
}
