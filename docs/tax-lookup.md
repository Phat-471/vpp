# Tra cứu MST và thông tin nhận hóa đơn

## Nguồn dữ liệu

- Mặc định: VietQR `GET https://api.vietqr.io/v2/business/{taxCode}`, không cần khóa API. Tài liệu: https://vietqr.io/danh-sach-api/tax-id-lookup/ . Nhà cung cấp công bố dừng API vào 01/03/2027; không cam kết uptime hay miễn phí vĩnh viễn.
- Nguồn chuyển đổi: Xinvoice `GET https://api.xinvoice.vn/gdt-api/tax-payer/{taxCode}`. Tài liệu: https://xinvoice.vn/docs/api-tich-hop/api-tra-mst/ . Xinvoice giới thiệu tra cứu miễn phí nhưng API cần đăng ký `client-id` và `api-key`.
- Không scrape HTML của các trang tra cứu hoặc sử dụng endpoint không có tài liệu công khai.

## Cấu hình

Không cần cài thư viện hay migration. Laravel HTTP client gọi provider ở server; không truyền khóa cho trình duyệt. Cấu hình trong secret manager hoặc `.env` (không commit):

Nếu PHP báo lỗi CA (`cURL error 60`), cấu hình `TAX_LOOKUP_CA_BUNDLE` tới bundle CA tin cậy có sẵn của môi trường; không dùng `verify=false`. Máy Wamp hiện tại dùng bundle đi kèm phpMyAdmin, không sửa `php.ini` toàn hệ thống. Nếu server được khởi chạy trong sandbox chặn mạng, cần khởi chạy server từ terminal có quyền kết nối Internet.

```dotenv
TAX_LOOKUP_PROVIDER=vietqr
```

Để chuyển sang Xinvoice, đặt `TAX_LOOKUP_PROVIDER=xinvoice`, cấu hình `XINVOICE_CLIENT_ID` và `XINVOICE_API_KEY` qua môi trường server, sau đó làm mới config cache. Không điền khóa vào tài liệu, code hay chat.

## Luồng sử dụng

- POS: mở “Thông tin xuất hóa đơn”, bật yêu cầu hóa đơn, nhập MST. MST doanh nghiệp hỗ trợ tra cứu sẽ tự điền tên/địa chỉ; nhập thêm email và kiểm tra lại thông tin. Đơn tạm lưu cả thông tin hóa đơn.
- Website: cùng component ở `/thanh-toan` và giỏ hàng trên các trang dùng `layouts.storefront`. Các trang thông tin có layout độc lập và không có checkout không tạo form mới.
- Tự tra cứu doanh nghiệp 10 số và chi nhánh 13 số (có/không dấu gạch); giữ số 0 đầu. Mã cá nhân 12 số được nhận để lưu thông tin hóa đơn nhập thủ công; không gọi nguồn doanh nghiệp hoặc scrape hồ sơ cá nhân. Muốn tự tra nhóm cá nhân cần nguồn API phù hợp và quyền truy cập, không suy đoán tên/địa chỉ.
- Đổi MST sẽ xóa tên/địa chỉ trước; website hủy request cũ và bỏ phản hồi đến trễ. Tra cứu lỗi/không tìm thấy cho phép nhập thủ công.
- Form lưu đúng `is_vat_invoice`, `company_tax_id`, `company_name`, `company_address`, `invoice_email`; server yêu cầu đủ thông tin và email hợp lệ khi bật yêu cầu. Tắt yêu cầu sẽ không lưu dữ liệu cũ.
- Validation đặt hàng dùng `CheckoutRequest` trước khi ghi dữ liệu: họ tên 2–100 ký tự có chữ, địa chỉ 5–255 ký tự có chữ, SĐT di động Việt Nam 10 số hoặc cố định 11 số. Nhận dấu cách/chấm/gạch và `+84`, chuẩn hóa về số bắt đầu 0; không âm thầm bỏ chữ trong SĐT sai. Đây là kiểm tra định dạng, không xác minh người nhận/địa chỉ có thật.
- Website dùng chung `checkout-validation.js` cho giỏ hàng và trang thanh toán: chặn gửi form lỗi, thông báo tại trường; server vẫn trả 422 nếu bỏ qua kiểm tra trình duyệt. Thông tin hóa đơn yêu cầu tên/địa chỉ đủ dài, MST đúng độ dài và email có tên miền đầy đủ. POS dùng cùng validation tiền khách/đơn và kiểm tra SĐT ở server.
- API chỉ nhận MST và trả tên/địa chỉ/nguồn; không gửi email hay hồ sơ khách hàng ra ngoài. Rate limit endpoint 30 lần/phút/IP và provider 10 lần cache miss/phút/IP; timeout 6 giây, không theo redirect. Cache thành công 24 giờ, không tìm thấy 5 phút, lỗi mạng không cache.
- Tên/địa chỉ là dữ liệu tham khảo, không chứng nhận hoạt động thuế. Khách/thu ngân cần kiểm tra lại; không tự phát hành hoặc gửi hóa đơn điện tử. POS vẫn giữ thuế 0%; website giữ cách tính thuế hiện có theo cài đặt khi bật yêu cầu hóa đơn.

## Kiểm thử và rollback

`php artisan test --filter=BusinessTaxLookup`, `php artisan test --filter=Pos`, `node --check public/js/invoice-form.js`, Pint và Blade compile.

Kiểm thử JavaScript: `node tests/JavaScript/invoice-form.test.cjs` (chạy trực tiếp khi môi trường không cho test runner tạo tiến trình con).

Kiểm thử validation: `php artisan test --filter=CheckoutValidation`, `node tests/JavaScript/checkout-validation.test.cjs`.

Test dùng HTTP fake và database sqlite riêng, không gọi provider thật hoặc ghi database cửa hàng. Đã kiểm tra kết nối VietQR với MST doanh nghiệp công khai trong tài liệu; không gửi dữ liệu khách hàng thật.

Rollback code giao diện, routes và service về phiên bản trước; không có schema mới. Thông tin hóa đơn trên các đơn đã lưu vẫn nằm ở các cột hiện có. Để ngừng gọi provider ngay, cấu hình provider không hợp lệ; hệ thống báo nhập thủ công.
