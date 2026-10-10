<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            [
                'title' => 'Giới thiệu về VPP & Thiết Bị Máy In Ánh Dương',
                'slug' => 'gioi-thieu',
                'summary' => 'Hơn 10 năm đồng hành cùng hàng nghìn doanh nghiệp, trường học và hộ kinh doanh tại Hà Nội và toàn quốc.',
                'content' => '<h2>Chào mừng Quý khách đến với VPP Ánh Dương!</h2>
<p>Được thành lập từ năm 2015, <strong>VPP Ánh Dương</strong> tự hào là đối tác tin cậy chuyên cung cấp giải pháp văn phòng phẩm toàn diện và dịch vụ kỹ thuật máy in hàng đầu.</p>
<h3>1. Hệ sinh thái sản phẩm phong phú</h3>
<p>Chúng tôi phân phối hơn <strong>1.000+ sản phẩm chính hãng</strong> với đầy đủ hóa đơn GTGT (VAT):</p>
<ul>
    <li>Giấy in văn phòng cao cấp: Double A, IK Plus, PaperOne, Supreme, bãi bằng các định lượng 70gsm, 80gsm.</li>
    <li>Bút ký, bút viết, sổ da, bìa còng, dụng cụ lưu trữ hồ sơ thương hiệu Thiên Long, Deli, Plus.</li>
    <li>Mực in chính hãng & mực tương thích chất lượng cao: Canon, HP, Brother, Epson.</li>
    <li>Linh kiện máy in, máy scan, thiết bị văn phòng chuyên nghiệp.</li>
</ul>
<h3>2. Dịch vụ kỹ thuật & Sửa máy in thần tốc</h3>
<p>Đội ngũ kỹ thuật viên lành nghề luôn sẵn sàng hỗ trợ tại chỗ trong vòng <strong>15 - 30 phút</strong> từ khi tiếp nhận cuộc gọi:</p>
<ul>
    <li>Nạp mực máy in tận nơi giá chỉ từ 80.000đ, bảo hành đến hạt mực cuối cùng.</li>
    <li>Khắc phục triệt để các sự cố kẹt giấy, sọc đen, mờ bản in, hỏng hộp quang, cháy nguồn.</li>
    <li>Cho mượn máy in thay thế trong suốt quá trình xử lý thiết bị.</li>
</ul>
<h3>3. Cam kết dịch vụ</h3>
<p>✅ 100% hàng chính hãng, có xuất hóa đơn VAT điện tử minh bạch.<br>
✅ Chiết khấu tới 30% cho khách hàng mua số lượng lớn & hợp đồng doanh nghiệp.<br>
✅ Miễn phí giao hàng cho đơn từ 500.000đ.</p>',
                'category' => 'about',
                'position' => 'footer_col1',
                'is_active' => true,
                'sort_order' => 1,
                'meta_title' => 'Giới thiệu về VPP Ánh Dương - Văn phòng phẩm & Dịch vụ máy in',
                'meta_description' => 'Tìm hiểu về VPP Ánh Dương, đơn vị phân phối văn phòng phẩm chính hãng và dịch vụ sửa chữa máy in chuyên nghiệp tại Hà Nội.',
            ],
            [
                'title' => 'Chính sách bảo mật thông tin khách hàng',
                'slug' => 'chinh-sach-bao-mat',
                'summary' => 'Cam kết bảo vệ 100% dữ liệu cá nhân, thông tin đơn hàng và tài liệu nội bộ trong máy in sửa chữa.',
                'content' => '<h2>Chính Sách Bảo Mật Thông Tin Tại VPP Ánh Dương</h2>
<p>Chúng tôi cam kết tôn trọng và bảo vệ tối đa quyền riêng tư của Quý khách theo Nghị định 13/2023/NĐ-CP về bảo vệ dữ liệu cá nhân.</p>
<h3>1. Mục đích thu thập thông tin</h3>
<p>Thông tin thu thập bao gồm: Họ tên, Số điện thoại, Địa chỉ nhận hàng, Tên công ty, Mã số thuế và Địa chỉ Email. Dữ liệu này chỉ được sử dụng cho các mục đích:</p>
<ul>
    <li>Xử lý và giao hàng đơn mua văn phòng phẩm.</li>
    <li>Xuất hóa đơn điện tử giá trị gia tăng (VAT) hợp lệ.</li>
    <li>Liên hệ điều phối kỹ thuật viên nạp mực, sửa máy in tận nơi.</li>
    <li>Tra cứu tiến độ sửa chữa trực tuyến bằng Mã phiếu và 4 số cuối số điện thoại.</li>
</ul>
<h3>2. Cam kết bảo mật tài liệu máy in sửa chữa</h3>
<p><strong>Đặc biệt đối với dịch vụ kỹ thuật máy in:</strong> Kỹ thuật viên của chúng tôi ký cam kết tuyệt đối không sao chép, không đọc trộm, không lưu trữ bất kỳ văn bản, tài liệu nội bộ hoặc hình ảnh nào còn lưu trong bộ nhớ máy in hoặc khay giấy của khách hàng.</p>
<h3>3. Không chia sẻ với bên thứ ba</h3>
<p>Chúng tôi tuyệt đối không bán, cho thuê hay trao đổi dữ liệu khách hàng cho bất kỳ bên thứ ba nào vì mục đích quảng cáo khi chưa có sự đồng ý của Quý khách.</p>',
                'category' => 'policy',
                'position' => 'footer_col2',
                'is_active' => true,
                'sort_order' => 2,
                'meta_title' => 'Chính sách bảo mật thông tin | VPP Ánh Dương',
                'meta_description' => 'Cam kết bảo mật dữ liệu khách hàng và tài liệu máy in tại VPP Ánh Dương.',
            ],
            [
                'title' => 'Chính sách mua hàng, đổi trả & bảo hành',
                'slug' => 'chinh-sach-mua-hang',
                'summary' => 'Quy định mua hàng, thanh toán, đổi trả miễn phí trong 7 ngày và bảo hành linh kiện máy in.',
                'content' => '<h2>Chính Sách Mua Hàng, Đổi Trả & Bảo Hành</h2>
<h3>1. Quy trình mua hàng & Đặt hàng</h3>
<p>Quý khách có thể đặt hàng trực tiếp qua website, qua Zalo kinh doanh hoặc gọi Hotline 1900 6868. Sau khi nhận đơn, nhân viên sẽ liên hệ xác nhận trong vòng 10 phút.</p>
<h3>2. Phương thức thanh toán</h3>
<ul>
    <li><strong>Thanh toán tiền mặt (COD):</strong> Khách hàng kiểm tra hàng đúng quy cách mới thanh toán cho nhân viên giao hàng.</li>
    <li><strong>Chuyển khoản VietQR 24/7:</strong> Quét mã QR tự động điền số tiền và mã hóa đơn, hệ thống kích hoạt đơn ngay lập tức.</li>
    <li><strong>Công nợ doanh nghiệp 30 ngày:</strong> Áp dụng cho các cơ quan, trường học, công ty đã ký hợp đồng cung ứng định kỳ.</li>
</ul>
<h3>3. Chính sách đổi trả linh hoạt trong 7 ngày</h3>
<p>Áp dụng đổi trả 100% miễn phí khi:</p>
<ul>
    <li>Hàng giao sai quy cách, sai chủng loại hoặc không đúng số lượng đã đặt.</li>
    <li>Sản phẩm bị rách, ẩm ướt, móp méo do quá trình vận chuyển.</li>
    <li>Hộp mực in bị lỗi sọc, lem nhem từ phía nhà sản xuất.</li>
</ul>
<h3>4. Chính sách bảo hành dịch vụ máy in</h3>
<p>Linh kiện thay thế (trống drum, gạt mực, trục sạc, bao lụa) được bảo hành từ <strong>3 đến 6 tháng</strong> hoặc theo số lượng bản in tiêu chuẩn.</p>',
                'category' => 'policy',
                'position' => 'footer_col2',
                'is_active' => true,
                'sort_order' => 3,
                'meta_title' => 'Chính sách mua hàng & đổi trả | VPP Ánh Dương',
                'meta_description' => 'Quy định mua hàng, thanh toán và bảo hành dịch vụ tại VPP Ánh Dương.',
            ],
            [
                'title' => 'Bảng giá dịch vụ nạp mực & Sửa chữa máy in tận nơi',
                'slug' => 'bang-gia-sua-may-in',
                'summary' => 'Bảng báo giá niêm yết công khai dịch vụ nạp mực, thay linh kiện máy in Canon, HP, Brother tận nơi.',
                'content' => '<h2>Bảng Giá Dịch Vụ Mực In & Sửa Máy In Tận Nơi (Cập nhật 2026)</h2>
<p>Áp dụng cho khách hàng tại khu vực Hà Nội. Cam kết không phát sinh chi phí, kỹ thuật có mặt sau 15-30 phút.</p>
<table style="width:100%; border-collapse: collapse; margin-top: 15px;">
    <thead>
        <tr style="background:#f1f5f9; text-align:left;">
            <th style="padding:10px; border:1px solid #cbd5e1;">Hạng mục dịch vụ</th>
            <th style="padding:10px; border:1px solid #cbd5e1;">Dòng máy áp dụng</th>
            <th style="padding:10px; border:1px solid #cbd5e1;">Đơn giá tham khảo</th>
            <th style="padding:10px; border:1px solid #cbd5e1;">Bảo hành</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="padding:10px; border:1px solid #cbd5e1;">Nạp mực máy in laser đơn sắc</td>
            <td style="padding:10px; border:1px solid #cbd5e1;">Canon 2900, 3300, HP 1020, 1102...</td>
            <td style="padding:10px; border:1px solid #cbd5e1; font-weight:bold; color:#4f46e5;">80.000đ - 100.000đ</td>
            <td style="padding:10px; border:1px solid #cbd5e1;">Hết hộp mực</td>
        </tr>
        <tr>
            <td style="padding:10px; border:1px solid #cbd5e1;">Nạp mực Brother laser</td>
            <td style="padding:10px; border:1px solid #cbd5e1;">Brother HL-L2321D, L2366DW...</td>
            <td style="padding:10px; border:1px solid #cbd5e1; font-weight:bold; color:#4f46e5;">120.000đ - 150.000đ</td>
            <td style="padding:10px; border:1px solid #cbd5e1;">Reset & hết mực</td>
        </tr>
        <tr>
            <td style="padding:10px; border:1px solid #cbd5e1;">Thay trống drum in</td>
            <td style="padding:10px; border:1px solid #cbd5e1;">Canon 2900 / HP 12A</td>
            <td style="padding:10px; border:1px solid #cbd5e1; font-weight:bold; color:#4f46e5;">130.000đ - 160.000đ</td>
            <td style="padding:10px; border:1px solid #cbd5e1;">03 tháng</td>
        </tr>
        <tr>
            <td style="padding:10px; border:1px solid #cbd5e1;">Thay gạt từ / gạt mực</td>
            <td style="padding:10px; border:1px solid #cbd5e1;">Canon, HP, Brother</td>
            <td style="padding:10px; border:1px solid #cbd5e1; font-weight:bold; color:#4f46e5;">70.000đ - 90.000đ</td>
            <td style="padding:10px; border:1px solid #cbd5e1;">03 tháng</td>
        </tr>
        <tr>
            <td style="padding:10px; border:1px solid #cbd5e1;">Thay bao lụa sấy + ép</td>
            <td style="padding:10px; border:1px solid #cbd5e1;">Xử lý kẹt giấy cụm sấy</td>
            <td style="padding:10px; border:1px solid #cbd5e1; font-weight:bold; color:#4f46e5;">250.000đ - 350.000đ</td>
            <td style="padding:10px; border:1px solid #cbd5e1;">06 tháng</td>
        </tr>
    </tbody>
</table>
<p style="margin-top:15px;"><em>* Giá trên đã bao gồm công thợ tận nơi. Doanh nghiệp có nhu cầu xuất hóa đơn VAT vui lòng thông báo cho kỹ thuật viên.</em></p>',
                'category' => 'guide',
                'position' => 'footer_col1',
                'is_active' => true,
                'sort_order' => 4,
                'meta_title' => 'Báo giá nạp mực & Sửa chữa máy in tận nơi Hà Nội',
                'meta_description' => 'Bảng giá nạp mực và thay linh kiện máy in Canon, HP, Brother tại nhà hoặc văn phòng.',
            ],
        ];

        foreach ($pages as $item) {
            Page::updateOrCreate(
                ['slug' => $item['slug']],
                $item
            );
        }
    }
}
