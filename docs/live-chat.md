# Hỗ trợ trực tuyến

## Sử dụng

- Khách mở **Hỗ trợ trực tuyến** trên website để gửi câu hỏi; không bắt buộc đăng nhập.
- Tin nhắn hiển thị giờ Việt Nam, trạng thái đang gửi/đã gửi/chưa xác nhận gửi. Khi chưa nhận được xác nhận, bấm **Thử lại** trên tin đó trước khi gửi tin tiếp theo.
- Chuyển trang hoặc tải lại trong cùng phiên trình duyệt sẽ khôi phục hội thoại từ database. Việc mở/thu gọn khung chat được nhớ trong tab, không lưu nội dung tin nhắn ở localStorage.
- Khi khung chat thu gọn, bộ đếm vẫn nhận tin mới. Khi tab bị ẩn, tạm ngừng kiểm tra tin mới. Chỉ các tin hỗ trợ nằm trong vùng đang nhìn thấy mới được gửi xác nhận đã đọc.
- Admin vào `/admin/live-chat-page` hoặc **Chăm sóc khách hàng → Hỗ trợ trực tuyến**. Thu ngân và kỹ thuật viên không có quyền đọc, trả lời hoặc đổi trạng thái ở trang quản trị này.
- Admin tìm theo tên, số điện thoại hoặc nội dung gần nhất; lọc tất cả/chưa đọc/đang mở/đã đóng. Danh sách phân trang 20 hội thoại, nội dung 50 tin mỗi trang, trang 1 là nhóm mới nhất.
- Chọn hội thoại, chuyển trang tin hoặc gửi trả lời sẽ đánh dấu nhóm tin đang xem đã đọc. Tin mới nhận qua polling vẫn giữ chưa đọc cho tới khi admin chọn **Đánh dấu đã đọc** hoặc trả lời. Không tự cuộn khi admin đang đọc tin cũ.
- Mẫu trả lời chỉ điền vào ô soạn, không tự gửi và không hứa thời gian giao hàng/chiết khấu chưa được xác nhận.
- Admin phải mở lại hội thoại đã đóng trước khi trả lời. Khách gửi tin mới tự mở lại hội thoại cũ; không tạo hội thoại mới.

## Quyền truy cập và validation

- Hội thoại được xác định bằng `live_chat_token` và chủ tài khoản trong session Laravel phía server, không theo token/ID khách gửi lên. Thay đổi tài khoản khách sẽ tách khỏi hội thoại của chủ cũ.
- Token không trả về JSON, không nằm trong URL và không dùng localStorage. Token của phiên bản chat cũ không được nhập lại: hội thoại cũ vẫn còn trong admin nhưng khách bắt đầu phiên an toàn mới sau nâng cấp.
- Nội dung được trim, giới hạn 2.000 ký tự, chặn ký tự điều khiển và escape khi hiển thị. Validation dùng chung trong `ChatContent` cho khách và admin. Thông tin liên hệ nếu có phải đúng định dạng; tài khoản đã đăng nhập lấy danh tính từ server.
- API ghi có CSRF và khóa session; thao tác admin có Gate kiểm tra lại ở component và service. Truy vấn tin nhắn luôn giới hạn trong hội thoại hiện tại.
- Rate limit theo IP và từng endpoint riêng: khởi tạo 10/phút, gửi 20/phút, lấy tin 90/phút, đã đọc 90/phút. Admin gửi tối đa 30/phút theo tài khoản. Mạng nhiều khách chung IP có thể gặp giới hạn gửi.
- Gửi/đọc/đổi trạng thái sử dụng transaction và row lock. Bộ đếm được tính lại từ tin thực tế trong khóa, không cộng/trừ trên dữ liệu cũ.
- Thử lại phía khách giữ cùng UUID, cache ghi ID tin trong 24 giờ và dùng atomic lock để tránh lặp khi gửi lại. Đây không phải đảm bảo exactly-once nếu cache bị xóa/hết hạn hoặc tiến trình dừng giữa commit database và ghi cache. Không dùng cơ chế này cho thanh toán.
- GET lấy tin không đánh dấu đã đọc. POST xác nhận đã đọc chỉ áp dụng trong khoảng ID đã hiển thị, không nuốt tin vừa đến sau khoảng này.
- Không ghi nội dung, số điện thoại hoặc token vào log. JSON có `Cache-Control: no-store, private`.

## Vận hành và giới hạn

- Không cài dependency, không thay schema, không chạy migration hoặc xóa dữ liệu.
- Giữ Laravel 13.35.0, Filament 3.3.56 và Livewire 3.8.10 theo lock file.
- Cần session phía server (database/file/Redis), cache hỗ trợ atomic lock và cache dùng chung nếu chạy nhiều worker/server. Không dùng session driver `cookie` cho route session blocking; cache `array` chỉ phù hợp kiểm thử.
- Production dùng HTTPS, cookie bảo mật, `APP_DEBUG=false`; không cache các route `/api/chat/*` ở proxy/CDN.
- Cập nhật bằng polling: 5 giây khi mở khung chat, 15 giây khi thu gọn, 5 giây ở admin. Chưa có WebSocket, thông báo ngoài trình duyệt, chatbot, file/ảnh hoặc kiểm tra nhân viên online. Giao diện không tuyên bố nhân viên đang online.
- Phiên hết hạn, xóa cookie hoặc đổi trình duyệt sẽ không khôi phục hội thoại khách. Chưa cung cấp đồng bộ lịch sử giữa nhiều thiết bị.
- Giờ hiển thị dùng `Asia/Ho_Chi_Minh` ở khách; admin dùng timezone ứng dụng hiện tại. Tin cũ vẫn giữ tên người gửi và nội dung lịch sử.
- Khi deploy, thay cả PHP, Blade và asset `public/css/live-chat.css`, `public/js/live-chat.js`; nếu có view/route cache cũ cần làm mới theo quy trình deploy. Có thể revert nhóm file để rollback code, nhưng rollback phiên bản cũ sẽ phục hồi cơ chế bearer token cũ nên không khuyến nghị.

## File thay đổi

- `app/Services/LiveChatService.php`, `app/Support/ChatContent.php`, `app/Enums/ChatStatus.php`: nghiệp vụ và validation dùng chung.
- `app/Http/Requests/LiveChatRequest.php`, `app/Http/Controllers/LiveChatController.php`, `routes/web.php`: API session-bound, phân nhóm tin và xác nhận đã đọc.
- `app/Providers/AppServiceProvider.php`, `app/Models/ChatSession.php`: Gate admin, giới hạn spam và ẩn token.
- `app/Filament/Pages/LiveChatPage.php`, `resources/views/filament/pages/live-chat-page.blade.php`: hộp thư admin, phân trang và mẫu trả lời.
- `resources/views/storefront/components/livechat.blade.php`, `public/js/live-chat.js`, `public/css/live-chat.css`: giờ gửi, retry, unread và restore phía khách; dùng `ui-styling` cho nút tối thiểu 44px, focus, responsive và trạng thái. Nút liên hệ trong layout storefront chuyển sang bên trái để không đè nút chat.
- `tests/Feature/LiveChatTest.php`, `tests/js/live-chat.test.mjs`: quyền, validation, retry, bộ đếm, trạng thái và luồng client. Nội dung/nhãn được rà soát với `vietnamese-tech-writing`.

## Kiểm tra

```powershell
php artisan test --compact
node tests/js/live-chat.test.mjs
php vendor/bin/pint --test app/Enums/ChatStatus.php app/Support/ChatContent.php app/Http/Requests/LiveChatRequest.php app/Services/LiveChatService.php app/Http/Controllers/LiveChatController.php app/Filament/Pages/LiveChatPage.php app/Providers/AppServiceProvider.php app/Models/ChatSession.php routes/web.php tests/Feature/LiveChatTest.php
composer validate
git diff --check
```

PHP test dùng SQLite in-memory, không ghi vào database cửa hàng. Chưa có stress test concurrency trên MySQL hoặc kiểm tra nhiều server thật. Browser automation không có trình duyệt được kết nối trong phiên làm việc; cần kiểm tra trực quan cuối cùng trên desktop và điện thoại.

Kết quả kiểm tra ngày 09/10/2026: `php artisan test --compact` đạt 79/79 test (13 test chat), `node tests/js/live-chat.test.mjs` đạt 7/7 test, scoped Pint đạt, `composer validate` hợp lệ và `git diff --check` sạch. Validator tiếng Việt đạt với tài liệu, Blade, JavaScript và các lớp xử lý/validation chat mới. Website local trả HTTP 200, HTML có widget và đường dẫn script mới.

`npm run build` bị sandbox chặn spawn/native module; chạy lại ngoài sandbox đã thành công. Môi trường vẫn cảnh báo Node 20.18.0 thấp hơn yêu cầu Vite (20.19+ hoặc 22.12+) và thiếu package tùy chọn `fontaine`; không thay dependency trong đợt này. Xdebug cảnh báo đường dẫn log cũ không mở được, không làm test thất bại.
