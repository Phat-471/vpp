# Khách hàng trong POS

- Trang riêng: `/pos/khach-hang`; trong đơn bán mở thông tin khách rồi chọn **Chọn / thêm / sửa khách hàng** để không rời giỏ hàng.
- Thu ngân và quản trị viên được tìm, thêm, sửa, chọn khách. Chỉ quản trị viên được xóa.
- Cùng bảng `customers` với admin; không có kho dữ liệu riêng. Hóa đơn giữ snapshot tên/SĐT tại thời điểm bán.
- `CustomerManagement` kiểm tra tên, SĐT Việt Nam, email, địa chỉ và ghi chú ở server; chuẩn hóa SĐT, chặn trùng cả bản ghi cũ có dấu cách/chấm/mã quốc gia. POS dùng cùng cách tìm SĐT khi tạo đơn.
- POS chỉ sửa thông tin liên hệ, không sửa mật khẩu, trạng thái xác thực hoặc công nợ. Không đổi SĐT gắn với tài khoản đăng nhập/xác thực/Zalo.
- Không xóa khách có đơn hàng, phiếu sửa, công nợ, tài khoản hay dữ liệu xác thực Zalo. Admin DeleteAction cũng gọi cùng service. Xóa khách chưa có giao dịch là xóa thật, có xác nhận và không khôi phục được qua UI.
- Không thêm dependency, không đổi schema, không cần chạy migration. Cache store hiện tại cần hỗ trợ atomic lock (database/Redis); không thay sang store không có lock.

## Kiểm tra

`php artisan test --filter=PosCustomersTest` dùng SQLite in-memory, không sửa khách thật. Test bao gồm quyền truy cập, tạo/sửa, validate, SĐT trùng, chặn xóa dữ liệu lịch sử, phân trang, chống lộ credentials qua key form giả và chọn khách không mất giỏ hàng.

## Hoàn tác

Gỡ route/menu/component khách hàng POS và các thay đổi tích hợp tương ứng, giữ nguyên dữ liệu `customers`. Không reset worktree hay rollback các thay đổi khác trong dự án. Giữ biện pháp bảo vệ xóa lịch sử nếu vẫn sử dụng admin.
