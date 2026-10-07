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

---

## 5. QUY CHUẨN GIAO DIỆN UI/UX (KẾ THỪA SKILL UI-UX-PRO-MAX)
Mọi trang giao diện (Storefront, Tra cứu, Hóa đơn in ấn, Admin POS) phải áp dụng chuẩn thiết kế:

1. **100% TIẾNG VIỆT CHUẨN MỰC:**
   * Toàn bộ giao diện, nhãn trường, nút bấm, thông báo toast, modal xác nhận, thông báo lỗi phải là tiếng Việt rõ ràng, chuẩn ngữ pháp, thân thiện với người dùng Việt Nam.
   * Không dùng từ ngữ nửa Anh nửa Việt (VD: dùng "Đã thanh toán" thay vì "Status: Paid", "Hủy bỏ" thay vì "Cancel", "Lưu thay đổi" thay vì "Save changes").
2. **Thiết kế Mobile-First:**
   * Giao diện phải thao tác mượt mà bằng một tay trên điện thoại di động (Thumb-friendly). Nút bấm có chiều cao tối thiểu 44px.
   * Thanh Bottom Navigation luôn cố định ở đáy màn hình trên thiết bị di động.
   * Nút gọi Hotline và Zalo luôn ghim cố định góc phải màn hình.
3. **Phối màu & Typography hiện đại (Không dùng màu cơ bản chói gắt):**
   * Font chữ: `Plus Jakarta Sans` hoặc `Inter`.
   * Bảng màu chủ đạo: Nền Slate (`#f8fafc`), Màu chính Indigo/Navy (`#312e81`, `#4f46e5`), Màu thành công Emerald (`#059669`), Màu cảnh báo Amber (`#d97706`), Màu nguy hiểm Rose/Red (`#e11d48`).
   * Sử dụng card có bo góc lớn (`rounded-2xl`), đổ bóng nhẹ (`shadow-sm`), viền mảnh tinh tế (`border border-slate-200`).
4. **Trạng thái Trực quan (States):**
   * Phải luôn có trạng thái: Hover, Focus, Active, Disabled, Loading (Spinner / Skeleton), và Empty State (khi danh sách trống phải có icon + thông báo lịch sự + nút hành động).
5. **In ấn Chuẩn mực:**
   * Sử dụng `@media print` căn chỉnh chính xác cho máy in văn phòng (khổ A4 dọc, A5). Có đường nét đứt (cut line) rõ ràng cho các phiếu cần chia đôi.

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
