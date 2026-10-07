<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chính Sách Bảo Mật Thông Tin & Dữ Liệu Khách Hàng | VPP & Dịch Vụ Máy In</title>
    <meta name="description" content="Chính sách bảo mật thông tin cá nhân và bảo mật dữ liệu tài liệu trên máy in của khách hàng tại Cửa hàng Văn Phòng Phẩm & Dịch Vụ Máy In. Cam kết chống rò rỉ IDOR 100%.">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col justify-between antialiased selection:bg-indigo-600 selection:text-white">

    <!-- Header Navigation -->
    <header class="bg-white sticky top-0 z-40 border-b border-slate-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-20">
                <a href="{{ url('/') }}" class="flex items-center space-x-3 group">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-gradient-to-br from-indigo-700 to-indigo-900 flex items-center justify-center text-white shadow-md text-xl">
                        🖨️
                    </div>
                    <div>
                        <span class="text-base sm:text-xl font-black tracking-tight text-slate-900 block leading-tight">
                            VPP & DỊCH VỤ MÁY IN
                        </span>
                        <span class="text-[11px] text-indigo-600 font-semibold tracking-wider uppercase block">
                            Chính Sách Bảo Mật Dữ Liệu
                        </span>
                    </div>
                </a>

                <div class="flex items-center space-x-3 text-xs sm:text-sm font-bold">
                    <a href="{{ route('storefront.index') }}" class="text-slate-600 hover:text-indigo-600 transition">Trang Chủ</a>
                    <span class="text-slate-300">|</span>
                    <a href="{{ route('storefront.products') }}" class="text-slate-600 hover:text-indigo-600 transition">Sản Phẩm</a>
                    <span class="text-slate-300">|</span>
                    <a href="{{ route('storefront.wholesale') }}" class="text-amber-700 hover:text-amber-800 transition">Đại Lý Sỉ</a>
                    <span class="text-slate-300">|</span>
                    <a href="{{ route('storefront.about') }}" class="text-slate-600 hover:text-indigo-600 transition">Giới Thiệu</a>
                    <span class="text-slate-300">|</span>
                    <a href="{{ route('storefront.terms') }}" class="text-slate-600 hover:text-indigo-600 transition">Mua Hàng</a>
                    <span class="text-slate-300">|</span>
                    <a href="{{ route('storefront.privacy') }}" class="text-indigo-600 border-b-2 border-indigo-600 pb-0.5">Bảo Mật</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-16 space-y-8 flex-1">
        
        <!-- Breadcrumbs -->
        <nav class="flex items-center space-x-2 text-xs text-slate-500 font-medium">
            <a href="{{ url('/') }}" class="hover:text-indigo-600">Trang chủ</a>
            <span>/</span>
            <span class="text-slate-900 font-bold">Chính sách bảo mật thông tin & dữ liệu</span>
        </nav>

        <!-- Title Banner Box -->
        <div class="bg-gradient-to-br from-indigo-950 via-slate-900 to-indigo-900 text-white p-6 sm:p-10 rounded-3xl shadow-xl border border-indigo-800 space-y-3">
            <div class="inline-flex items-center space-x-2 bg-emerald-500/20 text-emerald-300 px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider border border-emerald-400/30">
                <span>🛡️ TIÊU CHUẨN AN TOÀN DỮ LIỆU TỐI CAO</span>
            </div>
            <h1 class="text-2xl sm:text-4xl font-black text-white leading-tight">
                Chính Sách Bảo Mật Thông Tin & Dữ Liệu Máy In Doanh Nghiệp
            </h1>
            <p class="text-xs sm:text-sm text-slate-300 leading-relaxed max-w-2xl">
                Chúng tôi hiểu rằng máy in và văn phòng phẩm là nơi lưu giữ và in ấn các tài liệu hợp đồng, số liệu tài chính quan trọng của quý khách. Cửa hàng cam kết áp dụng các biện pháp an ninh nghiêm ngặt nhất.
            </p>
        </div>

        <!-- Privacy Content Sections -->
        <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-sm border border-slate-200/80 space-y-8 text-sm text-slate-700 leading-relaxed">

            <!-- Section 1: Machine Data Confidentiality -->
            <section class="space-y-3">
                <h2 class="text-lg sm:text-xl font-black text-slate-900 flex items-center space-x-2.5">
                    <span class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-sm font-black flex-shrink-0">1</span>
                    <span>Cam Kết Bảo Mật Dữ Liệu Thiết Bị & Bản In Thử (Rất Quan Trọng)</span>
                </h2>
                <div class="pl-10 space-y-2 text-xs sm:text-sm">
                    <p>
                        Khi khách hàng gửi máy in, máy scan hoặc máy photocopy đến cửa hàng để bảo trì, sửa chữa hoặc nạp mực:
                    </p>
                    <ul class="list-disc pl-5 space-y-1.5 text-slate-600">
                        <li><b>Tuyệt đối không sao chép hoặc trích xuất dữ liệu:</b> Kỹ thuật viên bị nghiêm cấm truy cập vào bộ nhớ đệm, ổ cứng (đối với dòng máy in đa chức năng) hoặc sao lưu các tệp tài liệu của khách hàng dưới bất kỳ hình thức nào.</li>
                        <li><b>Quy trình xử lý giấy test kỹ thuật:</b> Các trang in test trong quá trình kiểm tra màu mực và trống in chỉ sử dụng tài liệu kiểm tra tiêu chuẩn (Test Page của hệ điều hành). Nếu có tài liệu còn sót lại trên khay nạp giấy, nhân viên sẽ gửi trả nguyên vẹn cho khách hoặc tiêu hủy bằng máy cắt giấy tại quầy nếu có yêu cầu.</li>
                        <li><b>Trách nhiệm của thợ kỹ thuật:</b> 100% nhân viên kỹ thuật đều ký cam kết bảo mật thông tin nội bộ (NDA). Mọi hành vi vi phạm sẽ bị xử lý nghiêm minh theo quy định pháp luật.</li>
                    </ul>
                </div>
            </section>

            <!-- Section 2: IDOR Prevention -->
            <section class="space-y-3 border-t border-slate-100 pt-6">
                <h2 class="text-lg sm:text-xl font-black text-slate-900 flex items-center space-x-2.5">
                    <span class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-sm font-black flex-shrink-0">2</span>
                    <span>Cơ Chế Kỹ Thuật Chống Rò Rỉ Dữ Liệu Online (Anti-IDOR)</span>
                </h2>
                <div class="pl-10 space-y-2 text-xs sm:text-sm">
                    <p>
                        Hệ thống phần mềm được thiết kế với tiêu chuẩn an ninh bảo vệ 2 lớp nhằm loại bỏ hoàn toàn lỗ hổng IDOR (Insecure Direct Object Reference):
                    </p>
                    <ul class="list-disc pl-5 space-y-1.5 text-slate-600">
                        <li><b>Xác thực kép khi tra cứu:</b> Khách hàng muốn xem tiến độ sửa chữa máy in bắt buộc phải cung cấp đúng cả 2 thông tin: <b>Mã phiếu sửa chữa + 4 số cuối số điện thoại</b> đăng ký nhận máy. Người lạ không thể dò tìm hoặc đoán số điện thoại của người khác.</li>
                        <li><b>Định danh an toàn:</b> Đường dẫn URL công khai không bao giờ để lộ ID cơ sở dữ liệu số tự tăng (auto-increment ID), bảo vệ thông tin lịch sử mua hàng và chi phí sửa chữa của khách hàng.</li>
                    </ul>
                </div>
            </section>

            <!-- Section 3: Customer Personal Data -->
            <section class="space-y-3 border-t border-slate-100 pt-6">
                <h2 class="text-lg sm:text-xl font-black text-slate-900 flex items-center space-x-2.5">
                    <span class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-sm font-black flex-shrink-0">3</span>
                    <span>Mục Đích Thu Thập & Sử Dụng Thông Tin Cá Nhân</span>
                </h2>
                <div class="pl-10 space-y-2 text-xs sm:text-sm">
                    <p>
                        Chúng tôi chỉ thu thập các thông tin tối thiểu cần thiết phục vụ cho việc vận hành dịch vụ:
                    </p>
                    <ul class="list-disc pl-5 space-y-1.5 text-slate-600">
                        <li><b>Họ tên & Số điện thoại:</b> Dùng để liên hệ báo giá sửa chữa, thông báo máy đã sửa xong và gọi điện giao hàng văn phòng phẩm.</li>
                        <li><b>Địa chỉ giao nhận:</b> Dùng để giao hàng văn phòng phẩm và trả máy in tận nơi theo yêu cầu của khách hàng.</li>
                        <li><b>Cam kết không chia sẻ bên thứ ba:</b> Chúng tôi cam kết không bán, không cho thuê hoặc chuyển nhượng thông tin khách hàng cho bất kỳ công ty quảng cáo hoặc bên thứ ba nào.</li>
                    </ul>
                </div>
            </section>

            <!-- Section 4: Payment Security -->
            <section class="space-y-3 border-t border-slate-100 pt-6">
                <h2 class="text-lg sm:text-xl font-black text-slate-900 flex items-center space-x-2.5">
                    <span class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-sm font-black flex-shrink-0">4</span>
                    <span>An Toàn Giao Dịch Thanh Toán & VietQR</span>
                </h2>
                <div class="pl-10 space-y-2 text-xs sm:text-sm">
                    <p>
                        Khi quý khách thanh toán qua mã VietQR động hoặc tiền mặt:
                    </p>
                    <ul class="list-disc pl-5 space-y-1.5 text-slate-600">
                        <li>Hệ thống tạo mã VietQR theo chuẩn Napas 24/7 trực tiếp từ ngân hàng, không thu thập hay lưu trữ thông tin thẻ tín dụng/mật khẩu tài khoản ngân hàng của khách.</li>
                        <li>Mọi giao dịch ngân hàng được đồng bộ tự động mã hóa qua cổng Webhook SePay/Casso với chứng chỉ SSL 256-bit.</li>
                    </ul>
                </div>
            </section>

            <!-- Section 5: Data Rights -->
            <section class="space-y-3 border-t border-slate-100 pt-6">
                <h2 class="text-lg sm:text-xl font-black text-slate-900 flex items-center space-x-2.5">
                    <span class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-sm font-black flex-shrink-0">5</span>
                    <span>Quyền Của Khách Hàng Đối Với Dữ Liệu</span>
                </h2>
                <div class="pl-10 space-y-2 text-xs sm:text-sm">
                    <p>
                        Khách hàng có toàn quyền yêu cầu cửa hàng:
                    </p>
                    <ul class="list-disc pl-5 space-y-1.5 text-slate-600">
                        <li>Kiểm tra, cập nhật hoặc điều chỉnh thông tin cá nhân của mình.</li>
                        <li>Yêu cầu xóa toàn bộ lịch sử đơn hàng và số điện thoại khỏi hệ thống sau khi đã hoàn tất hợp đồng và hết thời hạn bảo hành.</li>
                        <li>Mọi yêu cầu xin liên hệ trực tiếp hotline quản lý: <b>0901.234.567</b> (Zalo) hoặc email hỗ trợ: <b>hotro@vpp.local</b>.</li>
                    </ul>
                </div>
            </section>

        </div>

    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 text-xs py-8 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-2">
            <p>© 2026 Cửa Hàng Văn Phòng Phẩm & Dịch Vụ Máy In. Mọi quyền được bảo lưu.</p>
            <p class="text-slate-500">Địa chỉ: 123 Đường Văn Phòng Phẩm, P. Bến Nghé, Q.1, TP.HCM • Hotline: 0901.234.567</p>
        </div>
    </footer>

    <!-- Include Live Chat -->
    @include('storefront.components.livechat')

</body>
</html>
