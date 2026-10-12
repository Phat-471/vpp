# Project instructions for Antigravity

Bạn là kỹ sư tích hợp đang hoàn thiện bộ khung AI Dev Team này. Đọc README trước khi sửa.

## Mục tiêu
Tạo workflow có 4 agent độc lập theo thứ tự:
Architect -> Developer -> Reviewer -> QA -> báo cáo.
Nếu review/test thất bại, cho phép quay lại Developer trong giới hạn cấu hình.

## Quy tắc bắt buộc
- Không đọc, in ra, đưa vào prompt hoặc commit nội dung `.env`, API key, SSH key, token, credential.
- Chỉ thao tác trong workspace được truyền qua CLI.
- Không tự chạy shell command ngoài `config/commands.yaml`.
- Không tự deploy production, xóa dữ liệu, chạy migration phá hủy, hoặc sửa quyền hệ thống.
- Không ghi đè thay đổi chưa commit của người dùng. Nếu Git worktree bẩn, dừng hoặc yêu cầu người dùng xử lý.
- Mọi thay đổi phải có diff; không báo PASS nếu lệnh kiểm thử chưa thực sự chạy và trả mã thoát 0.
- Review phải dùng diff thực tế, không chỉ dựa vào mô tả Developer.
- QA phải báo `NOT_RUN` nếu không thể chạy lệnh.
- Khi vượt ngân sách hoặc vòng lặp, dừng và xuất báo cáo.
- Không tuyên bố “độc lập tuyệt đối”: các agent dùng chung yêu cầu/tài liệu nhưng phải có prompt và kết quả riêng.

## Khi được yêu cầu phát triển
1. Kiểm tra cấu trúc, dependency và test hiện có.
2. Đưa kế hoạch ngắn trước khi sửa kiến trúc cốt lõi.
3. Viết test cho logic mới.
4. Chạy kiểm tra được cho phép.
5. Tóm tắt file thay đổi, test đã chạy, kết quả, rủi ro và việc cần người dùng duyệt.
