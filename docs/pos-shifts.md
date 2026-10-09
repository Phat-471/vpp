# Ca thu ngân

## Sử dụng

Mở `/pos/ca-ban-hang` hoặc menu **Ca bán hàng**. Có thể nhập tiền đầu ca ngay trong POS mà không rời giỏ hàng. Nhập số nguyên VND, kể cả 0; không tự chuyển tiền cuối ca trước sang ca mới.

Mặc định bắt buộc mở ca trước khi thanh toán. Admin có thể bỏ chọn **Bắt buộc mở ca mới được bán hàng** và lưu trong cài đặt chế độ của màn ca. Khi không có ca mở, đơn mới được đánh dấu **Ngoài ca**. Nếu đang có ca mở thì đơn vẫn thuộc ca đó, dù chế độ bắt buộc đã tắt.

Thu ngân xem/mở/đóng ca của mình. Admin xem lịch sử tất cả ca nhưng chỉ đóng ca của chính tài khoản; chưa có chức năng đóng thay người khác. Một thu ngân chỉ có một ca đang mở, được bảo vệ bằng unique index và lock trong transaction. Luồng bán và đóng ca dùng cùng lock tài khoản; yêu cầu checkout lặp vẫn trả về đơn cũ, không gắn sang ca mới.

## Đối soát

- Tiền mặt cần có = tiền mặt đầu ca + tiền mặt bán hàng đã thu. Tiền khách đưa lớn hơn giá trị đơn không làm tăng tiền bán hàng vì phải trả tiền thừa.
- Chuyển khoản đã ghi nhận được tách khỏi tiền mặt. Tổng giá trị đơn có thể gồm cả đơn chưa thanh toán; không được hiểu là toàn bộ tiền đã nhận.
- Đóng ca nhập tiền thực đếm và chốt chênh lệch = thực đếm − cần có. Khi lệch tiền, phải nhập lý do ít nhất 5 ký tự. Ca đã đóng không sửa/mở lại; request đóng lặp không ghi đè kết quả.
- Tại lúc đóng, lưu tổng tiền và số đơn chờ chuyển khoản, đồng thời lưu số tiền đã thanh toán trên từng đơn. Chuyển khoản tăng thêm sau đó hiển thị riêng, không thay đổi số đã chốt. Không chuyển đơn chờ sang ca mới.
- Đơn bị hủy không được tính vào tổng hợp hiện tại. Chưa xử lý thu/chi quỹ, trả hàng hoặc hoàn tiền; không dùng màn ca để thay thế các nghiệp vụ này.
- Tiền được tính bằng số nguyên theo 1/100 đồng để bảo toàn dữ liệu decimal hiện có, không tính bằng float. Giao diện dùng quy ước hiển thị VND hiện tại.

Đơn trước khi triển khai không được tự gắn vào ca. Lịch sử đơn có bộ lọc **Trong ca**, **Ngoài ca**, **Đơn cũ chưa gắn ca**.

## Migration và an toàn

Migration `2026_10_08_150000_create_pos_shifts.php` thêm bảng `pos_shifts` và ba cột vào `orders`: `pos_shift_id`, `pos_outside_shift`, `pos_shift_paid_at_close`. Không xóa, seed hoặc sửa snapshot tiền của đơn cũ. Trước khi triển khai production cần backup và chạy migration có kiểm soát; không chạy migrate:fresh.

Chạy `php artisan migrate --path=database/migrations/2026_10_08_150000_create_pos_shifts.php` trên môi trường đã xác nhận. Tải lại các tab POS sau migration. Không cần dependency mới. Nếu hoàn tác, ưu tiên hoàn tác code có kiểm soát và giữ bảng/cột để bảo toàn lịch sử; rollback migration sẽ mất dữ liệu ca nên không thực hiện khi đã có giao dịch.

**Lưu ý trước production:** tổng chuyển khoản lấy từ `orders.paid_amount`, phụ thuộc luồng xác nhận thanh toán hiện có. Webhook SePay/Casso hiện chưa thấy kiểm tra chữ ký/idempotency và còn log payload đầy đủ; phần này chưa được sửa trong tính năng ca. Cần xử lý bảo mật webhook trước khi tin cậy đối soát chuyển khoản thật. Số tăng sau chốt không thay thế thời gian nhận tiền chính xác trong sao kê ngân hàng.

## File và kiểm tra

Thêm `PosShiftStatus`, `PosShift`, `PosShiftPolicy`, `PosShiftService`, `PosShifts`, Blade màn ca và migration. Tích hợp `CreatePosOrder`, `Order`, POS/menu/route/CSS và bộ lọc `PosOrderHistory`/`PosOrders`. Test mới ở `PosShiftsTest`; các fixture bán hàng/hóa đơn cũ đã được cập nhật để kiểm tra theo chế độ phù hợp.

Kiểm tra bằng `php artisan test`, Pint các file liên quan, `composer validate`, `npm run build` và validator tiếng Việt. Test dùng SQLite in-memory; kiểm tra migration local riêng. MySQL concurrency chưa được kiểm thử bằng nhiều tiến trình song song, dù đã có transaction/row lock/unique constraint.
