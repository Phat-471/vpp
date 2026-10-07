<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaults = [
            // Thông tin chung
            [
                'key' => 'site_name',
                'value' => 'VPP & Thiết Bị Máy In Ánh Dương',
                'group' => 'general',
                'type' => 'text',
                'description' => 'Tên thương hiệu cửa hàng / doanh nghiệp',
            ],
            [
                'key' => 'site_slogan',
                'value' => 'Tổng Kho Văn Phòng Phẩm & Dịch Vụ Mực In - Máy In Toàn Diện',
                'group' => 'general',
                'type' => 'text',
                'description' => 'Khẩu hiệu kinh doanh',
            ],
            [
                'key' => 'hotline',
                'value' => '1900 6868',
                'group' => 'contact',
                'type' => 'text',
                'description' => 'Hotline tư vấn và tiếp nhận sự cố kỹ thuật 24/7',
            ],
            [
                'key' => 'zalo',
                'value' => '0988.123.456',
                'group' => 'contact',
                'type' => 'text',
                'description' => 'Số Zalo chăm sóc khách hàng & báo giá sỉ',
            ],
            [
                'key' => 'email',
                'value' => 'hotro@vppanhduong.vn',
                'group' => 'contact',
                'type' => 'text',
                'description' => 'Hòm thư điện tử tiếp nhận đơn hàng & hóa đơn',
            ],
            [
                'key' => 'address',
                'value' => 'Số 123 Đường Cầu Giấy, Phường Dịch Vọng, Quận Cầu Giấy, Hà Nội',
                'group' => 'contact',
                'type' => 'text',
                'description' => 'Địa chỉ showroom & kho tổng',
            ],
            [
                'key' => 'opening_hours',
                'value' => '08:00 - 18:30 (Thứ 2 - Thứ 7, CN trực kỹ thuật)',
                'group' => 'contact',
                'type' => 'text',
                'description' => 'Giờ làm việc cửa hàng',
            ],
            [
                'key' => 'notice_bar_text',
                'value' => '🔥 Miễn phí vận chuyển cho đơn từ 500.000đ | Hỗ trợ nạp mực & sửa máy in tận nơi trong 30 phút!',
                'group' => 'general',
                'type' => 'text',
                'description' => 'Thông báo chạy thanh thông tin trên cùng (Notice bar)',
            ],

            // Cấu hình thanh toán VietQR Napas 247
            [
                'key' => 'vietqr_bank_code',
                'value' => 'MB',
                'group' => 'payment',
                'type' => 'text',
                'description' => 'Mã ngân hàng (MB, VCB, TCB, ACB, ICB...)',
            ],
            [
                'key' => 'vietqr_bank_name',
                'value' => 'Ngân hàng TMCP Quân Đội (MB Bank)',
                'group' => 'payment',
                'type' => 'text',
                'description' => 'Tên đầy đủ ngân hàng thụ hưởng',
            ],
            [
                'key' => 'vietqr_account_number',
                'value' => '190333888999',
                'group' => 'payment',
                'type' => 'text',
                'description' => 'Số tài khoản ngân hàng thụ hưởng',
            ],
            [
                'key' => 'vietqr_account_name',
                'value' => 'CONG TY TNHH VPP ANH DUONG',
                'group' => 'payment',
                'type' => 'text',
                'description' => 'Tên chủ tài khoản (In hoa không dấu)',
            ],

            // Cấu hình Thuế & Vận chuyển
            [
                'key' => 'default_vat_rate',
                'value' => '8',
                'group' => 'tax_shipping',
                'type' => 'number',
                'description' => 'Thuế suất VAT (%) áp dụng mặc định khi xuất hóa đơn GTGT (8 hoặc 10)',
            ],
            [
                'key' => 'shipping_fee_default',
                'value' => '30000',
                'group' => 'tax_shipping',
                'type' => 'number',
                'description' => 'Phí vận chuyển mặc định (VNĐ)',
            ],
            [
                'key' => 'freeship_threshold',
                'value' => '500000',
                'group' => 'tax_shipping',
                'type' => 'number',
                'description' => 'Ngưỡng giá trị đơn hàng được miễn phí ship (VNĐ)',
            ],

            // Thông tin pháp nhân xuất hóa đơn điện tử
            [
                'key' => 'company_name',
                'value' => 'CÔNG TY TNHH THƯƠNG MẠI VÀ DỊCH VỤ VPP ÁNH DƯƠNG',
                'group' => 'invoice',
                'type' => 'text',
                'description' => 'Tên doanh nghiệp xuất hóa đơn VAT',
            ],
            [
                'key' => 'company_tax_id',
                'value' => '0109887766',
                'group' => 'invoice',
                'type' => 'text',
                'description' => 'Mã số thuế doanh nghiệp',
            ],
            [
                'key' => 'company_address',
                'value' => 'Số 123 Đường Cầu Giấy, Phường Dịch Vọng, Quận Cầu Giấy, TP. Hà Nội',
                'group' => 'invoice',
                'type' => 'text',
                'description' => 'Địa chỉ đăng ký kinh doanh xuất hóa đơn',
            ],
        ];

        foreach ($defaults as $item) {
            Setting::updateOrCreate(
                ['key' => $item['key']],
                $item
            );
        }
    }
}
