<?php

namespace App\Support;

use App\Models\Setting;

final class StorefrontSettings
{
    /** Only public store information is passed to storefront views. */
    public function all(): array
    {
        $defaults = [
            'site_name' => 'VPP & MỰC IN VIỆT',
            'site_slogan' => 'Tổng kho văn phòng phẩm và dịch vụ máy in',
            'hotline' => '0901.234.567',
            'zalo' => '0901.234.567',
            'email' => 'hotro@vpp.local',
            'address' => '123 Đường Văn Phòng Phẩm, Q.1, TP.HCM',
            'opening_hours' => '7h30 - 20h00 (Cả Thứ 7 & Chủ Nhật)',
            'notice_bar_text' => '',
            'freeship_threshold' => 500000,
        ];

        $settings = [];
        foreach ($defaults as $key => $default) {
            $settings[$key] = Setting::get($key, $default);
        }

        $settings['hotline_url'] = 'tel:'.preg_replace('/[^0-9+]/', '', (string) $settings['hotline']);
        $settings['zalo_url'] = 'https://zalo.me/'.preg_replace('/[^0-9]/', '', (string) $settings['zalo']);
        $settings['freeship_label'] = number_format((float) $settings['freeship_threshold'], 0, ',', '.').' ₫';

        return $settings;
    }
}
