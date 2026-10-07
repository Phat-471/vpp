<?php

use App\Models\Setting;

if (!function_exists('setting')) {
    /**
     * Get or set settings easily
     */
    function setting(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }
}

if (!function_exists('format_currency_vn')) {
    /**
     * Format currency to VN standard
     */
    function format_currency_vn(float|int|string|null $amount): string
    {
        $num = (float) ($amount ?? 0);
        return number_format($num, 0, ',', '.') . '₫';
    }
}

if (!function_exists('vietnamese_number_to_words')) {
    /**
     * Chuyển số thành chữ bằng tiếng Việt (Dùng cho Hóa đơn VAT)
     */
    function vietnamese_number_to_words(float|int|string $number): string
    {
        $number = round((float) $number);
        if ($number <= 0) return 'Không đồng';

        $digits = ['', 'một', 'hai', 'ba', 'bốn', 'năm', 'sáu', 'bảy', 'tám', 'chín'];
        $units = ['', 'nghìn', 'triệu', 'tỷ', 'nghìn tỷ', 'triệu tỷ'];

        $numStr = (string) $number;
        $len = strlen($numStr);
        $groups = [];

        while ($len > 0) {
            $take = min(3, $len);
            $start = max(0, $len - 3);
            $group = substr($numStr, $start, $take);
            array_unshift($groups, str_pad($group, 3, '0', STR_PAD_LEFT));
            $len -= $take;
        }

        $totalGroups = count($groups);
        $result = [];

        foreach ($groups as $idx => $group) {
            $h = (int) $group[0];
            $t = (int) $group[1];
            $u = (int) $group[2];

            if ($h == 0 && $t == 0 && $u == 0) continue;

            $groupText = [];
            if ($h > 0 || $idx > 0) {
                $groupText[] = $digits[$h] . ' trăm';
            }

            if ($t > 1) {
                $groupText[] = $digits[$t] . ' mươi';
                if ($u == 1) $groupText[] = 'mốt';
                elseif ($u == 5) $groupText[] = 'lăm';
                elseif ($u > 0) $groupText[] = $digits[$u];
            } elseif ($t == 1) {
                $groupText[] = 'mười';
                if ($u == 5) $groupText[] = 'lăm';
                elseif ($u > 0) $groupText[] = $digits[$u];
            } elseif ($t == 0 && $u > 0) {
                if ($h > 0 || $idx > 0) $groupText[] = 'lẻ';
                $groupText[] = $digits[$u];
            }

            $unitIndex = $totalGroups - 1 - $idx;
            if (isset($units[$unitIndex]) && $units[$unitIndex] !== '') {
                $groupText[] = $units[$unitIndex];
            }

            $result[] = implode(' ', $groupText);
        }

        $text = trim(implode(' ', $result)) . ' đồng';
        return mb_strtoupper(mb_substr($text, 0, 1)) . mb_substr($text, 1);
    }
}
