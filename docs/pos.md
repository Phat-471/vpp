# Quầy bán hàng riêng

- Đường dẫn bán hàng: `/pos`.
- Đăng nhập nhân viên: `/pos/login`, dùng email hoặc số điện thoại và mật khẩu hiện có. Chỉ quản trị viên và thu ngân được vào POS.
- Số điện thoại được đọc từ `users.phone`; chấp nhận dấu chấm, dấu cách và tiền tố `+84`. Nếu hai nhân viên trùng số điện thoại, dùng email để đăng nhập và chỉnh lại số trong hồ sơ nhân viên.
- POS dùng Laravel 13 / Livewire 3 đang cài, CSS và JavaScript riêng; không cài thêm thư viện.
- Danh sách hàng không có ảnh; tìm theo tên, SKU, mã vạch, lọc danh mục và phân trang. Nhập hoặc quét đúng mã rồi nhấn Enter để thêm hàng.
- Đổi đơn vị bán trong giỏ. Khi thanh toán, giá và hệ số quy đổi được đọc lại từ database; kiểm tra tồn kho tổng theo đơn vị gốc, lưu đơn và trừ kho trong cùng transaction.
- Tiền mặt yêu cầu nhập số tiền khách đưa. VietQR tạo đơn chờ thanh toán, không tự ghi nhận đã trả tiền từ thao tác trình duyệt. Mã QR lấy tài khoản ngân hàng trong cài đặt cửa hàng; trạng thái cập nhật theo thanh toán phía server hiện có.
- Thuế POS giữ quy tắc hiện tại là 0%; không thay đổi schema hay dữ liệu cửa hàng.
- “Lưu tạm” lưu một giỏ hàng theo nhân viên trong phiên làm việc, chưa tạo đơn hoặc trừ kho. Không lưu bền vững sau khi đăng xuất/hết phiên.
- In hóa đơn sau khi tạo đơn qua `/pos/hoa-don/{uuid}`; thu ngân chỉ xem hóa đơn POS của mình, quản trị viên xem được mọi hóa đơn POS.
- Phím tắt: F2 tìm sản phẩm; F4 lưu tạm; F9 thanh toán.

## Hóa đơn và quy trình in

- Hóa đơn dùng tên cửa hàng, địa chỉ, hotline, Zalo và email từ cài đặt website.
- Đơn tiền mặt mới lưu `cash_received`; hóa đơn hiển thị tiền hàng, giảm giá/thuế, tổng thanh toán, tiền khách đưa và tiền thừa. `paid_amount` vẫn là số tiền thanh toán cho đơn, không bao gồm tiền thừa.
- Đơn cũ chưa có dữ liệu tiền khách đưa sẽ hiển thị thông báo, không suy đoán lại số tiền.
- Sau khi thanh toán, POS tự mở hộp thoại in trong frame cùng origin. Với VietQR, chỉ in tự động khi server xác nhận đã thanh toán.
- Thu ngân phải bấm “Xác nhận đã in” trước khi chuyển sang đơn tiếp theo. Xác nhận được lưu ở `receipt_confirmed_at`; đây là xác nhận của nhân viên, không phải tín hiệu từ máy in. Trình duyệt không thể xác minh giấy đã in hoặc ép in im lặng. Nếu hủy hộp thoại hay máy in gặp lỗi, dùng “In lại hóa đơn”.
- Triển khai cần chạy migration `2026_10_08_100000_add_cash_received_and_receipt_confirmation_to_orders`. Migration chỉ thêm hai cột nullable; không backfill hoặc xóa dữ liệu. Rollback chỉ sau khi ngừng sử dụng phiên bản mới; bỏ hai cột sẽ mất thông tin tiền khách đưa và xác nhận in.

Kiểm tra: `php artisan test --filter=Pos`, `php vendor/bin/pint --test`, `node --check public/js/pos.js`, `node --check public/js/pos-receipt.js`.
