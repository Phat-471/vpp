# QUY CHUẨN KỸ THUẬT & QUY TẮC PHÁT TRIỂN DỰ ÁN (AI CODING DIRECTIVES)

> **LƯU Ý BẮT BUỘC DÀNH CHO TẤT CẢ CÁC AI ASSISTANT / AGENT:**
> Tài liệu này là **LUẬT BẤT THÀNH VĂN (ABSOLUTE RULES)**. Mọi AI tham gia đọc, viết mã, sửa lỗi hoặc tái cấu trúc trong repository này PHẢI TUÂN THỦ 100%. Không có ngoại lệ.

---

## 1. NGUYÊN TẮC "KHÔNG RÕ PHẢI HỎI - CẤM CODE BỪA" (ZERO GUESSWORK)
* **Không đoán mò nghiệp vụ:** Nếu một yêu cầu chưa rõ ràng, mơ hồ hoặc có nhiều phương án xử lý (đặc biệt liên quan đến luồng tiền, trừ tồn kho, quy trình tiếp nhận, thuế), AI **BẮT BUỘC PHẢI DỪNG LẠI VÀ ĐẶT CÂU HỎI CHO NGƯỜI DÙNG** trước khi sửa hoặc viết code.
* **Không tự ý "sáng tạo" tính năng ngoài phạm vi:** Chỉ làm đúng và trúng bài toán được yêu cầu. Không tự cài thêm package, không tự đổi cấu trúc DB khi chưa thảo luận và được người dùng đồng ý.

---

## 2. NGUYÊN TẮC DRY (DON'T REPEAT YOURSELF) & TÁI SỬ DỤNG CODE
* **Tuyệt đối cấm lặp code (Copy-Paste Logic):** Nếu một đoạn logic hoặc thuật toán được sử dụng từ **2 lần trở lên**, BẮT BUỘC phải tách thành hàm dùng chung:
  * **Xử lý nghiệp vụ / Luồng tính toán / Webhook:** Viết trong `app/Services/` (VD: `InventoryService`, `PaymentWebhookService`, `TicketService`).
  * **Xử lý dữ liệu Model / Quan hệ / Scope:** Viết Trait trong `app/Traits/` hoặc Model method.
  * **Hàm tiện ích nhỏ (Format tiền tệ, làm sạch SĐT, sinh mã ngẫu nhiên):** Tập trung vào `app/Helpers/Helper.php` hoặc Service dùng chung.
* **Không viết lại logic đã có sẵn trong Laravel/Filament:** Tận dụng tối đa các helper và feature có sẵn của Laravel (`Str::...`, `Number::currency`, `Collection`, Eloquent casting).

---

## 3. TỐI ƯU HÓA THƯ VIỆN & NGUYÊN TẮC "DEPENDENCY LEAN"
* **Ưu tiên thư viện siêu nhẹ hoặc tự code:**
  * **CẤM** cài đặt các package lớn, cồng kềnh nếu chỉ cần dùng 1–2 tính năng nhỏ (VD: Không cài cả thư viện QR khổng lồ chỉ để render 1 mã QR tĩnh khi có thể gọi API VietQR/SVG trực tiếp; không cài thư viện export Excel nặng nếu CSV hoặc Spout có sẵn đáp ứng đủ).
  * Trước khi đề xuất `composer require` hoặc `npm install`, AI **BẮT BUỘC PHẢI ĐÁNH GIÁ**:
    1. Tính năng này có thể tự viết bằng PHP/JS thuần trong dưới 50 dòng code không?
    2. Nếu tự viết đơn giản và nhẹ hơn $\rightarrow$ **BẮT BUỘC TỰ CODE**.
* **Đảm bảo tương thích môi trường:** Hệ thống đang chạy trên **PHP 8.3**, **MySQL 9.1 (InnoDB)**, **Laravel 13**, **Filament v3**. Bất kỳ thư viện nào thêm vào phải tương thích 100% với phiên bản này.

---

## 4. KIỂM SOÁT & XÁC THỰC DỮ LIỆU ĐẦU VÀO TUYỆT ĐỐI (STRICT VALIDATION)
* **Bảo vệ 2 lớp (Defense in Depth):**
  * **Lớp 1 (Client-side / UI):** Kiểm tra tức thì trên form (required, min/max, định dạng SĐT, định dạng số, regex) để người dùng không phải bấm submit mới thấy lỗi.
  * **Lớp 2 (Server-side / Backend):** Mọi request gửi lên Controller / Livewire / API / Webhook **BẮT BUỘC PHẢI QUA VALIDATION** (sử dụng `$request->validate(...)`, `FormRequest`, hoặc Filament Form Rules).
* **Làm sạch dữ liệu (Sanitization):**
  * Số điện thoại: Tự động loại bỏ ký tự rác, dấu chấm, dấu cách (`preg_replace('/\D/', '', $phone)`), trích xuất chuẩn `phone_last4`.
  * Tiền tệ: Đảm bảo số tiền >= 0, ép kiểu chuẩn `float` / `decimal`.
  * Chuỗi văn bản: `trim()`, chống XSS, chống Null Byte.
* **Bảo mật cơ sở dữ liệu:**
  * Không ghép chuỗi câu lệnh SQL (`raw SQL concat`). Luôn sử dụng Eloquent ORM hoặc PDO Prepared Statements để triệt tiêu SQL Injection.
  * Các route công khai (như tra cứu phiếu sửa chữa) **BẮT BUỘC CHỐNG IDOR**: Dùng UUID hoặc kết hợp `Mã phiếu + 4 số cuối SĐT`, tuyệt đối không dùng ID số tuần tự.
* **Quy chuẩn Tải Tệp & Nén Ảnh Client-Side (Client-Side Compression Invariant):**
  * **Tự động nén ảnh trước khi tải lên (Client-side Canvas Resize):** Đối với mọi tính năng cho phép người dùng/khách hàng đính kèm hình ảnh (Live Chat, Đặt lịch sửa máy in, Biên nhận), client-side JavaScript BẮT BUỘC phải tự động nén và thu nhỏ kích thước (tối đa chiều rộng/cao 1600px, chất lượng JPEG 80-85%) bằng HTML5 Canvas trước khi gửi lên API. Điều này đảm bảo dung lượng file luôn dưới 1MB, tải lên tức thì, triệt tiêu 100% lỗi timeout và lỗi tràn bộ nhớ Nginx/PHP.
  * **100% Tiếng Việt cho lỗi tải tệp:** Tất cả các thông báo lỗi upload (kể cả lỗi từ Livewire như `validation.uploaded`) BẮT BUỘC phải được cấu hình tiếng Việt: *"Tệp tải lên không thành công (dung lượng vượt quá giới hạn máy chủ hoặc kết nối gián đoạn). Hãy thử lại hoặc chọn ảnh khác."*, tuyệt đối cấm để lọt câu tiếng Anh *"The attachment failed to upload."*.
  * **Xử lý kẹt trạng thái gửi (Pending Recovery):** Khi một lượt gửi có ảnh gặp lỗi, giao diện phải cung cấp nút "Hủy tệp / Xóa tin lỗi" bên cạnh nút "Thử lại" để người dùng không bị kẹt form khi ảnh không thể gửi được.

---

## 5. QUY CHUẨN GIAO DIỆN UI/UX (KẾ THỪA SKILL UI-UX-PRO-MAX)
Mọi trang giao diện (Storefront, Tra cứu, Hóa đơn in ấn, Admin POS) phải áp dụng chuẩn thiết kế:

1. **100% TIẾNG VIỆT CHUẨN MỰC:**
   * Toàn bộ giao diện, nhãn trường, nút bấm, thông báo toast, modal xác nhận, thông báo lỗi phải là tiếng Việt rõ ràng, chuẩn ngữ pháp, thân thiện với người dùng Việt Nam.
   * Không dùng từ ngữ nửa Anh nửa Việt (VD: dùng "Đã thanh toán" thay vì "Status: Paid", "Hủy bỏ" thay vì "Cancel", "Lưu thay đổi" thay vì "Save changes").
2. **Thiết kế Mobile-First & Bố cục Nút nổi (Floating Action Layout):**
   * Giao diện phải thao tác mượt mà bằng một tay trên điện thoại di động (Thumb-friendly). Nút bấm có chiều cao tối thiểu 44px.
   * **Nguyên tắc Single Bottom Bar trên Mobile:** Tại mọi thời điểm trên viewport mobile, chỉ được hiển thị **DUY NHẤT 1 thanh cố định ở đáy**. Nếu trang có thanh mua hàng riêng biệt (`custom_bottom_bar`), layout chính BẮT BUỘC phải ẩn thanh Bottom Navigation mặc định để tránh lỗi lặp 2 menu.
   * **Bố cục các nút liên hệ nổi (Tách 2 bên đối xứng, chống chồng đè):**
     * **Bên TRÁI (Bottom-Left):** Nút liên hệ **Zalo** (màu xanh Zalo đặc trưng, icon SVG chuẩn, bo tròn).
     * **Bên PHẢI (Bottom-Right):** Nút **Hotline** nằm dưới cùng; nút **Live Chat (Hỗ trợ trực tuyến)** nằm xếp chồng ngay phía trên Hotline với khoảng cách an toàn (tối thiểu 12px), tuyệt đối không để Live Chat che mất nút gọi điện thoại.
     * Khi xuất hiện thanh `custom_bottom_bar` trên mobile, các nút nổi phải tự động ẩn hoặc nâng cao độ đáy tương ứng để không che khuất nút thao tác mua hàng.
   * **Quy chuẩn nền đặc & Icon Vector (Anti-Transparency & SVG Only):**
     * Mọi nút nổi (Floating buttons, badge) BẮT BUỘC sử dụng màu nền đặc (`solid background` với gradient/màu thương hiệu), có viền trắng tinh tế (`border-2 border-white`) và đổ bóng rõ ràng (`shadow-lg`). Tuyệt đối không dùng nền trong suốt gây hiện tượng chữ nổi đè lem lên thẻ sản phẩm hay văn bản bên dưới.
     * 100% sử dụng icon vector SVG tinh gọn. CẤM dùng emoji hệ điều hành (như 💬, 📞, 📦) làm icon chính vì gây sai lệch giao diện giữa Android/iOS/Windows.
3. **Phối màu & Typography hiện đại (Không dùng màu cơ bản chói gắt):**
   * Font chữ: `Plus Jakarta Sans` hoặc `Inter`.
   * Bảng màu chủ đạo: Nền Slate (`#f8fafc`), Màu chính Indigo/Navy (`#312e81`, `#4f46e5`), Màu thành công Emerald (`#059669`), Màu cảnh báo Amber (`#d97706`), Màu nguy hiểm Rose/Red (`#e11d48`).
   * Sử dụng card có bo góc lớn (`rounded-2xl`), đổ bóng nhẹ (`shadow-sm`), viền mảnh tinh tế (`border border-slate-200`).
4. **Trạng thái Trực quan (States):**
   * Phải luôn có trạng thái: Hover, Focus, Active, Disabled, Loading (Spinner / Skeleton), và Empty State (khi danh sách trống phải có icon + thông báo lịch sự + nút hành động).
5. **In ấn Chuẩn mực:**
   * Sử dụng `@media print` căn chỉnh chính xác cho máy in văn phòng (khổ A4 dọc, A5). Có đường nét đứt (cut line) rõ ràng cho các phiếu cần chia đôi.
6. **Quy chuẩn Phân hệ Quản trị Admin Dashboard & Widgets (Filament v3):**
   * **100% SVG Vector - Tuyệt đối không dùng Emoji:** Toàn bộ component Blade quản trị, tiêu đề widget và nút thao tác phải dùng icon SVG vector tinh gọn (Heroicons hoặc SVG đồng bộ phong cách). Tuyệt đối cấm dùng emoji hệ điều hành (như ⚡, 🔧, 📦, ⚠️, 🖨️).
   * **Huy hiệu Cảnh báo Thời gian thực trên Banner:** Banner giới thiệu (`admin-dashboard-intro`) phải tích hợp badge đếm tin nhắn Live Chat chưa đọc (hiệu ứng pulse) và số lượng máy in quá hạn hẹn trả khách (hiệu ứng ping màu đỏ) để chủ cửa hàng điều phối tức thì.
   * **Thẻ Thống kê có Liên kết Điều hướng (Stat Drill-down Linking):** Mọi thẻ Stat trong `StatsOverviewWidget` bắt buộc có `->url(...)` dẫn trực tiếp đến trang tài nguyên tương ứng kèm filter (VD: Bấm vào "Máy in đang sửa" mở ngay danh sách phiếu sửa chữa đang xử lý; bấm vào "Cảnh báo tồn kho" mở ngay danh mục sản phẩm lọc tồn kho thấp).
   * **Phân tách Đa luồng Doanh thu (Revenue Chart):** Biểu đồ doanh thu phải tách biệt 2 luồng: Doanh thu Bán lẻ VPP (Indigo `#4f46e5`) và Doanh thu Dịch vụ Máy in (Emerald `#10b981`), hỗ trợ bộ lọc khoảng thời gian (7 ngày, 14 ngày, 30 ngày).
   * **Thao tác Nhanh tại chỗ (In-place Modal Actions & DB Transaction):** Các widget cảnh báo (như LowStockAlertWidget) phải cung cấp nút thao tác trực tiếp dạng Modal (như "Nhập kho nhanh") để xử lý ngay trên Dashboard mà không cần chuyển trang. Mọi thay đổi tồn kho/tiền bạc trong Action này BẮT BUỘC bọc trong `DB::transaction`.

---

## 6. QUY TẮC CƠ SỞ DỮ LIỆU & TRANSACTION (TỒN KHO & TIỀN BẠC)
* **Toàn vẹn Tồn kho & Tài chính (Database Transaction):**
  * Bất kỳ thao tác nào đồng thời thay đổi tiền hoặc thay đổi tồn kho (như: Bán hàng trừ kho, Thêm linh kiện vào phiếu sửa, Webhook cập nhật tiền) **BẮT BUỘC PHẢI BỌC TRONG `DB::transaction(function() { ... })`**.
  * Nếu một bước gặp sự cố, hệ thống phải tự rollback 100%, không bao giờ được để xảy ra tình trạng "trừ kho xong nhưng tạo đơn bị lỗi".
* **Chống N+1 Query:**
  * Mọi truy vấn danh sách có lấy dữ liệu quan hệ cha-con (VD: Sản phẩm kèm Danh mục, Phiếu sửa kèm Linh kiện, Đơn hàng kèm Chi tiết) BẮT BUỘC phải dùng Eager Loading `->with([...])`.

---

## 7. CẤU TRÚC MÃ NGUỒN DỰ ÁN
```
app/
├── Helpers/              # Các hàm tiện ích dùng chung toàn hệ thống
├── Services/             # Xử lý nghiệp vụ phức tạp (Kho, Webhook, Thanh toán)
├── Traits/               # Các trait tái sử dụng cho Model
├── Models/               # Eloquent Models (Đã có Casts, Relationships, Helpers)
├── Filament/Resources/   # Giao diện Quản trị & Thu ngân POS
├── Http/Controllers/     # Storefront, Lookup, Print, Webhook Controllers
resources/views/
├── storefront/           # Giao diện khách hàng Mobile-First
├── lookup/               # Trang tra cứu tiến độ (Chống IDOR)
├── print/                # Template in ấn A4/A5 (@media print)
└── filament/modals/      # Modal giao diện Filament (VietQR động)
```

---

## 8. QUY TRÌNH TRIỂN KHAI VPS & QUẢN LÝ VITE BUILD ASSETS
* **Không bỏ sót Vite Assets:**
  * Thư mục `/public/build` (chứa `manifest.json`, CSS, JS và font đã bundle) **BẮT BUỘC PHẢI ĐƯỢC THEO DÕI TRONG GIT** (không được đưa vào `.gitignore`), trừ khi server đã thiết lập CI/CD runner riêng.
  * Trước khi tạo commit và push lên remote repo, nếu có thay đổi CSS/JS/Giao diện, AI **BẮT BUỘC chạy `npm run build`** để đảm bảo file `public/build/manifest.json` luôn mới nhất, triệt tiêu lỗi `ViteManifestNotFoundException` trên môi trường Production.
* **Lệnh cập nhật chuẩn trên VPS (aaPanel / Terminal):**
  * Mỗi khi deploy phiên bản mới lên VPS, quy trình chuẩn gồm các bước:
    ```bash
    cd /www/wwwroot/vpp
    git pull origin master
    php artisan migrate --force
    php artisan storage:link
    chown -R www:www storage bootstrap/cache
    chmod -R 775 storage bootstrap/cache
    php artisan optimize:clear
    ```
* **Cấu hình Upload & Phân Quyền Thư Mục Storage trên VPS (aaPanel / Nginx / PHP-FPM):**
  * **Cấu hình Nginx:** Bổ sung `client_max_body_size 20M;` trong block `server` của Virtual Host trên aaPanel để không bị chặn lỗi 413.
  * **Cấu hình PHP (`php.ini`):** Thiết lập `upload_max_filesize = 20M` và `post_max_size = 20M` để máy chủ tiếp nhận ảnh từ điện thoại.

---

## 9. QUY CHUẨN TÀI KHOẢN QUẢN TRỊ & HỒ SƠ ADMIN (ADMIN PROFILE & AUTH INVARIANT)
* **Điều hướng kép trực quan (Dual-Entry Navigation):**
  * Trang hồ sơ admin (`EditProfile`) BẮT BUỘC phải truy cập được từ 2 vị trí:
    1. **User Dropdown góc phải**: MenuItem `profile` phải gắn `->url(fn () => EditProfile::getUrl())` tường minh kèm icon vector `heroicon-m-user-circle` và nhãn *"Đổi thông tin & Mật khẩu"*.
    2. **Sidebar Menu bên trái**: Đăng ký `NavigationItem` trong nhóm `'Cấu hình'` với nhãn *"Tài khoản quản trị"* để người dùng thao tác trực tiếp mà không cần bấm qua menu avatar.
* **100% Tiếng Việt & Đầy đủ trường cơ bản:**
  * Form hồ sơ admin phải hỗ trợ đầy đủ: Họ và tên (`name`), Email đăng nhập (`email`), Số điện thoại (`phone`), Mật khẩu mới (`password`) kèm Xác nhận mật khẩu (`passwordConfirmation`) và nhãn hiển thị vai trò hệ thống.
  * Thông báo lưu thành công phải là tiếng Việt: *"Cập nhật thông tin tài khoản thành công!"*.
* **Hỗ trợ quản trị CLI khẩn cấp:**
  * Hệ thống luôn duy trì lệnh `php artisan admin:update` để đổi mật khẩu/email trực tiếp từ SSH/terminal VPS khi quên mật khẩu hoặc không vào được web.


