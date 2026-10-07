# HỒ SƠ ĐẶC TẢ KỸ THUẬT & DANH MỤC CÔNG VIỆC DỰ ÁN
## HỆ THỐNG QUẢN TRỊ BÁN LẺ VĂN PHÒNG PHẨM & DỊCH VỤ SỬA CHỮA MÁY IN
*Nền tảng: Laravel (Filament PHP + Blade/Livewire + Tailwind CSS)*

---

# PHẦN 1: BỐI CẢNH DỰ ÁN & MỤC TIÊU VẬN HÀNH

### 1. Hiện trạng & Bài toán thực tế
* **Quy mô hàng hóa:** ~1.000 SKU văn phòng phẩm, đã có danh mục dữ liệu thô (Excel) nhưng chưa có hình ảnh sản phẩm.
* **Mô hình kinh doanh:** Bán lẻ trực tiếp tại cửa hàng, bán online vãng lai, hạn chế tối đa nợ đọng/công nợ dài ngày.
* **Dịch vụ thiết bị:** Nhận sửa chữa, thay thế linh kiện, nạp mực máy in khi khách mang trực tiếp đến cửa hàng; tạo phiếu hẹn lấy máy.
* **Hạ tầng hiện có:** 
  * Máy tính để bàn (PC) và smartphone tại quầy thu ngân.
  * Chưa có máy in hóa đơn nhiệt mini K80; sử dụng máy in văn phòng hiện có (khổ A4/A5).
  * Tài khoản ngân hàng cá nhân tiếp nhận thanh toán (chuẩn bị sẵn cấu trúc pháp lý hóa đơn/thuế để nâng cấp Hộ kinh doanh/Doanh nghiệp trong tương lai).

### 2. Mục tiêu hệ thống
* **Tối ưu di động (Mobile-First):** Giao diện thao tác bằng một tay trên smartphone cho khách mua online và thợ kỹ thuật.
* **Xử lý nhanh tại quầy (Fast Intake POS):** Lên đơn văn phòng phẩm và in phiếu hẹn tiếp nhận máy in trong vòng dưới 60 giây.
* **Tự động hóa thanh toán:** Quét mã VietQR động, tự động nhận diện giao dịch và chuyển trạng thái đơn hàng mà không cần kiểm tra số dư thủ công.
* **An toàn dữ liệu:** Chống triệt để việc lộ thông tin giữa các khách hàng (lỗ hổng IDOR/BOLA).

---

# PHẦN 2: ĐẶC TẢ YÊU CẦU CHỨC NĂNG (FUNCTIONAL REQUIREMENTS)

### 1. Phân hệ Quản lý Sản phẩm (Catalog & Inventory)
* **Import Excel thông minh:** Tải tệp dữ liệu 1.000 SKU gồm: Mã SKU, Tên, Danh mục, Đơn vị tính, Giá nhập, Giá bán lẻ, Tồn kho ban đầu.
* **Giải pháp thiếu ảnh:** 
  * Tự động gán ảnh đại diện mặc định (Placeholder) theo từng danh mục để giao diện không bị vỡ.
  * Bộ lọc trong trang quản trị: "Sản phẩm chưa có ảnh" để nhân viên dùng smartphone chụp và cập nhật dần.
* **Đơn vị tính kép (Unit Conversion):** Hỗ trợ bán lẻ và bán sỉ cùng một sản phẩm (VD: Giấy in A4 bán theo Ram hoặc bán theo Thùng 5 Ram tự trừ tồn kho tương ứng theo hệ số 5).
* **Tra cứu tương thích máy in:** Gắn quan hệ giữa hộp mực/linh kiện với danh sách dòng máy in phổ biến (Canon 2900, HP 107a, Brother 2321D...).

### 2. Phân hệ Bán lẻ tại quầy (POS) & Mua sắm Online
* **Bán tại quầy (POS Screen):**
  * Hỗ trợ tìm kiếm nhanh theo tên sản phẩm, gõ mã SKU hoặc quét mã vạch qua máy quét/camera.
  * Tự động tính tiền, chiết khấu và in hóa đơn bán lẻ (khổ A5 hoặc A4 chia đôi).
* **Website Storefront (Mobile-First):**
  * Thanh điều hướng đáy (Bottom Navigation): *Trang chủ*, *Danh mục*, *Đặt thợ/Dịch vụ*, *Tra cứu phiếu*, *Giỏ hàng*.
  * Nút gọi Hotline và nhắn Zalo luôn ghim cố định ở góc dưới màn hình.
  * **Guest Checkout:** Khách chỉ cần nhập Tên, Số điện thoại và Địa chỉ; không bắt buộc tạo tài khoản hay ghi nhớ mật khẩu.

### 3. Phân hệ Dịch vụ & Phiếu tiếp nhận máy in (Repair Intake)
Hệ thống xử lý 2 nhánh tiếp nhận linh hoạt:
* **Nhánh 1 (Có thợ tại quầy kiểm tra ngay):**
  * Thợ báo giá trực tiếp $\rightarrow$ Nhập chi phí công và linh kiện thay thế $\rightarrow$ Trạng thái chuyển thẳng sang `IN_PROGRESS` (Đang sửa) $\rightarrow$ In phiếu biên nhận có sẵn giá tiền và thời gian hẹn trả.
* **Nhánh 2 (Tiếp nhận trước - Báo giá sau):**
  * Thu ngân nhập: Tên máy, hiện trạng lỗi, phụ kiện đi kèm (dây nguồn, khay giấy) $\rightarrow$ Trạng thái `RECEIVED` (Đã tiếp nhận) $\rightarrow$ In phiếu hẹn.
  * Thợ tháo máy kiểm tra (`DIAGNOSING`) $\rightarrow$ Nhập bảng giá dự kiến $\rightarrow$ Nhân viên gọi điện thoại cho khách $\rightarrow$ Khách đồng ý thì tích chọn "Khách đã duyệt giá" $\rightarrow$ Chuyển sang `IN_PROGRESS`. Khách từ chối thì chuyển sang `CANCELLED`.
* **In ấn phiếu hẹn:** Xuất bản in gồm 2 phần:
  * *Phần 1:* Cuống phiếu dán lên vỏ máy (chứa mã vạch/mã QR để thợ quét nhận diện máy).
  * *Phần 2:* Phiếu hẹn đưa khách mang về (có in mã QR tra cứu tiến độ).

### 4. Phân hệ Thanh toán VietQR & Chuẩn hóa Thuế
* **VietQR Động:** Khi khách thanh toán (tại quầy hoặc online), hệ thống sinh mã QR kèm chính xác số tiền lẻ và mã đơn/phiếu (VD: `VPP1023` hoặc `SC554`).
* **Webhook tự động (SePay / Casso):** Khi tài khoản ngân hàng nhận tiền có nội dung khớp mã, hệ thống tự động đổi trạng thái đơn sang `PAID` trong vòng 3–5 giây.
* **Chuẩn bị dữ liệu kế toán/thuế:** Bóc tách rõ ràng: *Tiền hàng/dịch vụ trước thuế (subtotal)* và *Tiền thuế VAT (vat_amount)*. Giai đoạn đầu thiết lập thuế suất 0%; khi nâng cấp doanh nghiệp chỉ cần bật cấu hình VAT mà không phải chỉnh sửa cơ sở dữ liệu.

---

# PHẦN 3: KIẾN TRÚC KỸ THUẬT, BẢO MẬT & HIỆU NĂNG

### 1. Kiến trúc hệ thống
* **Framework:** Laravel 11.x / 12.x chạy trên PHP 8.2+.
* **Cơ sở dữ liệu:** MySQL 8.0+ hoặc PostgreSQL.
* **Giao diện quản trị (Admin/POS):** Filament PHP (xây dựng trên Livewire, Tailwind CSS, Alpine.js) giúp giao diện tự động co giãn chuẩn mực trên cả PC lẫn điện thoại.
* **Giao diện Storefront:** Laravel Blade Template kết hợp Tailwind CSS.

### 2. Chiến lược Tối ưu hóa Hình ảnh & Hiệu năng
* **Xử lý ảnh bất đồng bộ (Queue Worker):** Toàn bộ ảnh sản phẩm hoặc ảnh máy in hư hỏng được xử lý qua hàng đợi (Redis hoặc Database Queue) bằng thư viện `intervention/image`.
* **Quy chuẩn ảnh:**
  * Giảm kích thước tối đa xuống chiều rộng 1200px.
  * Nén chất lượng 80-85% và chuyển đổi hoàn toàn sang định dạng `.webp` (dung lượng giảm từ 3MB xuống còn ~60-150KB).
  * Tự động sinh 3 phiên bản: Thumbnail (150px), Medium (500px), Large (1200px).
* **Caching:** Cache cây danh mục sản phẩm và bảng tra cứu dòng máy in tương thích vào bộ nhớ đệm (TTL 24 giờ).

### 3. Bảo mật & Chống rò rỉ dữ liệu (Chống lỗi IDOR)
* **Định danh URL:** Không dùng ID số tự tăng tuần tự trên đường dẫn URL công khai. Bắt buộc dùng **UUID** hoặc **Mã ngẫu nhiên có tiền tố** (VD: `tra-cuu/PSC-82934-XYZ`).
* **Kiểm soát quyền (Laravel Policy):** Cấm truy cập chéo đơn hàng. Chỉ tài khoản sở hữu hoặc Admin mới có quyền truy xuất dữ liệu đơn.
* **Tra cứu công khai:** Khách hàng muốn xem tiến độ sửa máy bắt buộc phải nhập đúng cả 2 thông tin: **Mã phiếu tiếp nhận + 4 số cuối Số điện thoại**.

---

# PHẦN 4: THIẾT KẾ CƠ SỞ DỮ LIỆU (DATABASE SCHEMA)

### 1. Bảng `categories` (Danh mục ngành hàng)
* `id` (BIGINT, PK, AUTO_INCREMENT)
* `name` (VARCHAR 100): Tên danh mục (Giấy in, Bút viết, Hộp mực, Linh kiện...)
* `slug` (VARCHAR 120, UNIQUE)
* `icon` (VARCHAR 100, NULL): Icon giao diện (Heroicons)
* `placeholder_image` (VARCHAR 255, NULL): Đường dẫn ảnh đại diện mẫu khi sản phẩm chưa chụp ảnh
* `description` (TEXT, NULL)
* `sort_order` (INT, DEFAULT 0): Thứ tự hiển thị
* `is_active` (BOOLEAN, DEFAULT TRUE)
* `timestamps`

### 2. Bảng `printer_models` (Dòng máy in thị trường)
* `id` (BIGINT, PK, AUTO_INCREMENT)
* `brand` (VARCHAR 60): Hãng máy (Canon, HP, Brother, Epson...)
* `model_name` (VARCHAR 100): Mã model (LBP 2900, 107a, HL-L2321D...)
* `printer_type` (VARCHAR 100): Laser đen trắng, Laser màu, Phun màu đa năng...
* `compatible_cartridges` (VARCHAR 255): Mã hộp mực/drum tương thích (VD: Cartridge 12A/303, TN-2385...)
* `notes` (TEXT, NULL): Ghi chú kỹ thuật tháo lắp & reset
* `timestamps`

### 3. Bảng `products` (Kho sản phẩm & Linh kiện thống nhất)
* `id` (BIGINT, PK, AUTO_INCREMENT)
* `category_id` (BIGINT, FK -> categories.id, NULL ON DELETE)
* `sku` (VARCHAR 60, UNIQUE): Mã SKU quản lý
* `barcode` (VARCHAR 60, INDEX, NULL): Mã vạch quét máy đọc
* `name` (VARCHAR 255): Tên hàng hóa / linh kiện
* `slug` (VARCHAR 255, UNIQUE)
* `cost_price` (DECIMAL 14,2, DEFAULT 0): Giá nhập (chỉ Admin xem)
* `retail_price` (DECIMAL 14,2, DEFAULT 0): Giá bán lẻ
* `stock_quantity` (INT, DEFAULT 0): Số lượng tồn kho theo đơn vị cơ bản (Kho duy nhất chung cho bán lẻ & sửa máy)
* `low_stock_threshold` (INT, DEFAULT 5): Ngưỡng cảnh báo hết hàng
* `base_unit` (VARCHAR 30, DEFAULT 'Cái'): Đơn vị tính cơ bản (Ram, Cây, Cuộn, Hộp...)
* `image_path` (VARCHAR 255, NULL): Ảnh chụp thực tế sản phẩm
* `has_custom_image` (BOOLEAN, DEFAULT FALSE): Đánh dấu đã có ảnh thật để lọc nhanh sản phẩm thiếu ảnh
* `is_service_part` (BOOLEAN, DEFAULT FALSE): Đánh dấu linh kiện/mực dùng cho sửa chữa máy in
* `is_active` (BOOLEAN, DEFAULT TRUE)
* `description` (TEXT, NULL)
* `timestamps`

### 4. Bảng `product_units` (Đơn vị quy đổi kép: Bán lẻ & Bán sỉ/thùng)
* `id` (BIGINT, PK, AUTO_INCREMENT)
* `product_id` (BIGINT, FK -> products.id, CASCADE)
* `unit_name` (VARCHAR 50): Tên đơn vị quy đổi (VD: Thùng 5 Ram, Lốc 10 cây...)
* `conversion_rate` (INT, DEFAULT 1): Hệ số quy đổi (VD: 5 -> tự trừ 5 Ram vào kho)
* `price` (DECIMAL 14,2): Giá bán theo đơn vị quy đổi
* `barcode` (VARCHAR 60, NULL)
* `timestamps`

### 5. Bảng `product_printer` (Bảng trung gian tương thích máy in)
* `product_id` (BIGINT, FK -> products.id, CASCADE)
* `printer_model_id` (BIGINT, FK -> printer_models.id, CASCADE)
* `notes` (VARCHAR 255, NULL)
* `timestamps`
* `PRIMARY KEY(product_id, printer_model_id)`

### 6. Bảng `customers` (Khách hàng & Công nợ)
* `id` (BIGINT, PK, AUTO_INCREMENT)
* `name` (VARCHAR 100): Họ tên khách hàng / Đơn vị
* `phone` (VARCHAR 20, INDEX)
* `phone_last4` (CHAR 4, INDEX): 4 số cuối SĐT dùng để bảo mật tra cứu IDOR
* `address` (VARCHAR 255, NULL)
* `debt_balance` (DECIMAL 14,2, DEFAULT 0): Công nợ
* `notes` (TEXT, NULL)
* `timestamps`

### 7. Bảng `repair_tickets` (Phiếu tiếp nhận & Dịch vụ máy in)
* `id` (BIGINT, PK, AUTO_INCREMENT)
* `uuid` (CHAR 36, UNIQUE): Định danh an toàn trên URL
* `ticket_code` (VARCHAR 30, UNIQUE): Mã phiếu in thực tế (VD: `SC260001`)
* `customer_id` (BIGINT, FK -> customers.id, NULL ON DELETE)
* `customer_name` (VARCHAR 100)
* `customer_phone` (VARCHAR 20, INDEX)
* `phone_last4` (CHAR 4, INDEX)
* `printer_model_id` (BIGINT, FK -> printer_models.id, NULL ON DELETE)
* `device_name` (VARCHAR 150): Tên máy (Canon 2900...)
* `serial_number` (VARCHAR 100, NULL)
* `accessories` (VARCHAR 255, NULL): Phụ kiện gửi lại (Dây nguồn, khay giấy...)
* `issue_description` (TEXT): Hiện trạng lỗi khách báo
* `technician_diagnosis` (TEXT, NULL): Chẩn đoán kỹ thuật của thợ
* `intake_flow` (VARCHAR 30): `quote_immediate` (Nhánh 1) hoặc `quote_later` (Nhánh 2)
* `status` (VARCHAR 30): `received` | `diagnosing` | `waiting_approval` | `in_progress` | `completed` | `delivered` | `cancelled`
* `labor_fee` (DECIMAL 14,2, DEFAULT 0): Tiền công thợ
* `parts_total` (DECIMAL 14,2, DEFAULT 0): Tiền linh kiện
* `discount_amount` (DECIMAL 14,2, DEFAULT 0)
* `tax_amount` (DECIMAL 14,2, DEFAULT 0)
* `grand_total` (DECIMAL 14,2, DEFAULT 0): Tổng thanh toán
* `paid_amount` (DECIMAL 14,2, DEFAULT 0): Tiền đã trả
* `payment_status` (VARCHAR 30): `unpaid` | `partially_paid` | `paid`
* `payment_method` (VARCHAR 30): `cash` | `vietqr` | `transfer`
* `promised_at` (DATETIME, NULL): Hẹn trả lúc
* `completed_at` (DATETIME, NULL)
* `delivered_at` (DATETIME, NULL)
* `technician_id` (BIGINT, FK -> users.id, NULL ON DELETE)
* `created_by` (BIGINT, FK -> users.id, NULL ON DELETE)
* `internal_notes` (TEXT, NULL)
* `timestamps`

### 8. Bảng `repair_items` (Linh kiện thay thế trên phiếu sửa máy in)
* `id` (BIGINT, PK, AUTO_INCREMENT)
* `repair_ticket_id` (BIGINT, FK -> repair_tickets.id, CASCADE)
* `product_id` (BIGINT, FK -> products.id, NULL ON DELETE): Liên kết trực tiếp kho VPP (tự động trừ kho)
* `item_name` (VARCHAR 255)
* `unit` (VARCHAR 30, DEFAULT 'Cái')
* `quantity` (INT, DEFAULT 1)
* `cost_price` (DECIMAL 14,2, DEFAULT 0)
* `unit_price` (DECIMAL 14,2, DEFAULT 0)
* `subtotal` (DECIMAL 14,2, DEFAULT 0)
* `timestamps`

### 9. Bảng `orders` & `order_items` (Bán lẻ tại quầy POS & Đặt Online)
* Bóc tách chuẩn thuế: `subtotal`, `discount_amount`, `tax_rate`, `tax_amount`, `grand_total`.
* Hỗ trợ bán theo đơn vị quy đổi (`conversion_rate` tự nhân để trừ đúng số lượng đơn vị cơ bản trong kho).

### 10. Bảng `payment_transactions` (Lịch sử Webhook SePay & Casso)
* `id` (BIGINT, PK, AUTO_INCREMENT)
* `gateway` (VARCHAR 30): `sepay` | `casso` | `manual`
* `transaction_id` (VARCHAR 100, INDEX, NULL): Mã giao dịch ngân hàng
* `reference_code` (VARCHAR 50, INDEX): Mã đơn hoặc mã phiếu sửa (`SC...`, `HD...`)
* `order_id` (BIGINT, NULL), `repair_ticket_id` (BIGINT, NULL)
* `amount` (DECIMAL 14,2): Số tiền nhận
* `account_number`, `bank_brand_name`, `description`, `transaction_time`, `raw_payload`, `status`
* `timestamps`