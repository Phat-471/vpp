# Lịch sử đơn hàng POS

## Phạm vi

Trang riêng `/pos/don-hang`, menu **Đơn hàng** trong POS. Tìm theo mã đơn, tên khách hoặc SĐT; lọc ngày bắt đầu/kết thúc và trạng thái thanh toán. Danh sách phân trang 15 đơn, dùng timezone ứng dụng như thời gian đang lưu trong DB.

Thu ngân chỉ xem đơn POS do chính tài khoản tạo. Admin xem tất cả đơn POS. Đơn online không xuất hiện và không mở được từ màn hình này. UUID không thay thế kiểm tra quyền: service kiểm tra Gate khi mở chi tiết và mỗi lần render; endpoint bill dùng Gate hiện có.

Chi tiết lấy giá, tên hàng, khách hàng và tổng tiền đã lưu trên đơn, không suy diễn từ sản phẩm/khách hàng hiện tại. Hiển thị tiền hàng, giảm giá, thuế, tiền đã thanh toán, còn phải thanh toán, tiền khách đưa và tiền thừa. Đơn cũ chưa lưu tiền khách đưa được ghi rõ, không tự tạo số liệu.

Nút in mở bill hiện có trong tab riêng và tự gọi hộp thoại in; không tạo đơn, trừ kho, ghi nhận thanh toán hoặc cập nhật xác nhận in. Trình duyệt không thể xác minh tờ giấy đã thực sự in. Đơn chưa thanh toán được ghi rõ là phiếu chưa thanh toán.

Chưa triển khai trả hàng, hoàn tiền hoặc ca thu ngân. Các bước này cần thống nhất nghiệp vụ trước khi thay đổi tiền, kho hay schema.

## Các file thay đổi

- `app/Services/PosOrderHistory.php`: query có giới hạn quyền, validation bộ lọc và tính tiền còn lại/tiền thừa dùng chung.
- `app/Livewire/PosOrders.php`, `resources/views/livewire/pos-orders.blade.php`: màn lịch sử và chi tiết.
- `routes/web.php`, `resources/views/layouts/pos.blade.php`, `public/css/pos.css`: route riêng, menu và giao diện đồng bộ.
- `resources/views/layouts/storefront.blade.php`: khôi phục thông báo admin ở cả mobile/desktop, hiển thị ngưỡng miễn phí giao hàng đúng cài đặt; giữ escape HTML.
- `app/Providers/AppServiceProvider.php`, `resources/views/print/order.blade.php`: dùng chung cách tính tiền thừa với chi tiết POS.
- `tests/Feature/PosOrdersTest.php`: quyền, IDOR, lọc/validate/phân trang, snapshot, XSS và in không thay đổi dữ liệu.

## Vận hành và hoàn tác

Không thêm package hay migration. POS dùng CSS trực tiếp có cache-busting; tải lại trang để thấy menu mới. Không cần reset database hoặc chạy seeder.

Nếu cần hoàn tác, chỉ gỡ các file/thay đổi được liệt kê phía trên, không reset worktree và không xóa dữ liệu đơn. Có thể giữ riêng bản sửa thông báo storefront. Chạy `php artisan test` và Pint cho các file PHP liên quan trước khi triển khai.
