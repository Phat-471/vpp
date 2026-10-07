<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giới Thiệu Về Cửa Hàng Văn Phòng Phẩm & Dịch Vụ Máy In Chuyên Nghiệp</title>
    <meta name="description" content="Khám phá hành trình hơn 10 năm phát triển của Cửa hàng Văn Phòng Phẩm & Dịch Vụ Sửa Chữa Máy In. Cung ứng hơn 1.000 SKU văn phòng phẩm chính hãng và dịch vụ kỹ thuật máy in uy tín số 1.">
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
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-gradient-to-tr from-indigo-700 via-indigo-600 to-amber-500 flex items-center justify-center font-black text-white text-lg sm:text-xl shadow-md group-hover:scale-105 transition transform">
                        VP
                    </div>
                    <div>
                        <span class="text-base sm:text-xl font-black tracking-tight text-slate-900 block leading-tight">
                            VPP & DỊCH VỤ MÁY IN
                        </span>
                        <span class="text-[11px] text-indigo-600 font-semibold tracking-wider uppercase block">
                            Giới Thiệu Về Cửa Hàng
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
                    <a href="{{ route('storefront.about') }}" class="text-indigo-600 border-b-2 border-indigo-600 pb-0.5">Giới Thiệu</a>
                    <span class="text-slate-300">|</span>
                    <a href="{{ route('storefront.terms') }}" class="text-slate-600 hover:text-indigo-600 transition">Mua Hàng</a>
                    <span class="text-slate-300">|</span>
                    <a href="{{ route('storefront.privacy') }}" class="text-slate-600 hover:text-indigo-600 transition">Bảo Mật</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 space-y-10 flex-1">
        
        <!-- Breadcrumbs -->
        <nav class="flex items-center space-x-2 text-xs text-slate-500 font-medium">
            <a href="{{ url('/') }}" class="hover:text-indigo-600">Trang chủ</a>
            <span>/</span>
            <span class="text-slate-900 font-bold">Giới thiệu cửa hàng</span>
        </nav>

        <!-- Hero Visual Banner Box -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-950 via-slate-900 to-indigo-900 text-white p-6 sm:p-12 shadow-2xl border border-indigo-800">
            <div class="relative z-10 max-w-3xl space-y-4">
                <div class="inline-flex items-center space-x-2 bg-amber-400 text-indigo-950 px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider shadow">
                    <span>🏢 ĐỐI TÁC VĂN PHÒNG & THIẾT BỊ IN ẤN TIN CẬY</span>
                </div>
                <h1 class="text-2xl sm:text-4xl lg:text-5xl font-black text-white leading-tight">
                    Giải Pháp Toàn Diện Cho Văn Phòng & Thiết Bị In Doanh Nghiệp
                </h1>
                <p class="text-xs sm:text-base text-slate-300 leading-relaxed font-normal">
                    Thành lập với sứ mệnh đồng hành cùng các doanh nghiệp, cơ quan và hộ kinh doanh, chúng tôi kết hợp hoàn hảo giữa <b>kho văn phòng phẩm hơn 1.000 SKU</b> giá sỉ và <b>trung tâm dịch vụ kỹ thuật sửa máy in chuyên sâu</b> lấy ngay trong ngày.
                </p>
                <div class="pt-2 flex flex-wrap gap-4 text-xs font-bold">
                    <span class="flex items-center space-x-1.5 text-emerald-400">
                        <span>✔</span> <span>100% Sản phẩm chính hãng</span>
                    </span>
                    <span class="flex items-center space-x-1.5 text-amber-300">
                        <span>✔</span> <span>Kỹ thuật viên 10+ năm nghề</span>
                    </span>
                    <span class="flex items-center space-x-1.5 text-sky-300">
                        <span>✔</span> <span>Giao hàng hỏa tốc 2 giờ</span>
                    </span>
                </div>
            </div>

            <!-- Background Glowing Effect -->
            <div class="absolute -right-10 -bottom-10 w-72 h-72 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute right-1/3 -top-10 w-60 h-60 bg-amber-500/20 rounded-full blur-3xl pointer-events-none"></div>
        </div>

        <!-- 4 Key Value Stats -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 text-center">
                <span class="block text-2xl sm:text-3xl font-black text-indigo-700 font-mono">1.000+</span>
                <span class="text-xs text-slate-500 font-semibold mt-1 block">SKU Văn Phòng Phẩm</span>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 text-center">
                <span class="block text-2xl sm:text-3xl font-black text-emerald-600 font-mono">10.000+</span>
                <span class="text-xs text-slate-500 font-semibold mt-1 block">Khách Hàng Doanh Nghiệp</span>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 text-center">
                <span class="block text-2xl sm:text-3xl font-black text-amber-500 font-mono">15-30p</span>
                <span class="text-xs text-slate-500 font-semibold mt-1 block">Nạp Mực Siêu Tốc</span>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 text-center">
                <span class="block text-2xl sm:text-3xl font-black text-rose-600 font-mono">90 Ngày</span>
                <span class="text-xs text-slate-500 font-semibold mt-1 block">Bảo Hành Sửa Máy</span>
            </div>
        </div>

        <!-- Story & Brand Pillars -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-stretch">
            
            <!-- Pillar 1: Stationery Supply -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200 space-y-4 flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-2xl">
                        📚
                    </div>
                    <h3 class="text-lg sm:text-xl font-black text-slate-900">
                        1. Nhà Cung Cấp Văn Phòng Phẩm Tận Gốc
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Chúng tôi phân phối trực tiếp từ các thương hiệu hàng đầu như <b>Double A, PaperOne, Thiên Long, Kokuyo, Plus, Bến Nghé</b>. Hệ thống kho quy mô lớn sẵn sàng cung ứng trọn gói:
                    </p>
                    <ul class="text-xs text-slate-600 space-y-2 pl-4 list-disc">
                        <li><b>Giấy in văn phòng:</b> Khổ A4, A3, A5 định lượng 70gsm, 80gsm bán lẻ theo Ram và chiết khấu cực sâu theo Thùng.</li>
                        <li><b>Bút viết & Dụng cụ:</b> Bút bi, bút gel, bút lông bảng, bấm kim, kẹp giấy, băng keo văn phòng.</li>
                        <li><b>Quản lý hồ sơ:</b> Bìa còng, bìa lá, file lưu trữ chứng từ chuẩn văn phòng.</li>
                        <li><b>Hóa đơn & Hợp đồng:</b> Hỗ trợ xuất hóa đơn VAT điện tử và công nợ linh hoạt cho khách hàng doanh nghiệp định kỳ.</li>
                    </ul>
                </div>
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs text-emerald-600 font-bold">✔ Hỗ trợ công nợ doanh nghiệp</span>
                    <a href="{{ url('/') }}" class="text-xs text-indigo-600 font-bold hover:underline">Xem danh mục →</a>
                </div>
            </div>

            <!-- Pillar 2: Printer Repair Services -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200 space-y-4 flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center text-2xl">
                        🖨️
                    </div>
                    <h3 class="text-lg sm:text-xl font-black text-slate-900">
                        2. Dịch Vụ Kỹ Thuật Máy In Chuyên Nghiệp
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Đội ngũ kỹ thuật viên lành nghề chuyên xử lý các dòng máy in laser, in màu, in phun đa năng của <b>Canon, HP, Brother, Epson</b>:
                    </p>
                    <ul class="text-xs text-slate-600 space-y-2 pl-4 list-disc">
                        <li><b>Nạp mực tận nơi & tại quầy:</b> Sử dụng mực in siêu mịn, bản in đen đậm sắc nét, không đổ mực thải bừa bãi.</li>
                        <li><b>Thay thế linh kiện chính hãng:</b> Trống drum, gạt mực, trục sạc, bao lụa, quả đào cuốn giấy với tem bảo hành 90 ngày.</li>
                        <li><b>Tiếp nhận minh bạch:</b> Quy trình nhận máy lập phiếu kiểm tra tình trạng trong 60 giây, không tráo đồ hay tăng giá vô lý.</li>
                        <li><b>Tra cứu tiến độ online:</b> Khách hàng dễ dàng kiểm tra tình trạng sửa chữa, linh kiện thay và chi phí trực tiếp trên website.</li>
                    </ul>
                </div>
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs text-amber-600 font-bold">✔ Bảo hành 90 ngày 1-đổi-1</span>
                    <a href="{{ route('lookup.index') }}" class="text-xs text-indigo-600 font-bold hover:underline">Tra cứu phiếu →</a>
                </div>
            </div>

        </div>

        <!-- Core Commitments (Cam Kết Vàng) -->
        <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white rounded-3xl p-6 sm:p-10 shadow-lg border border-slate-800 space-y-6">
            <div class="text-center space-y-2 max-w-xl mx-auto">
                <span class="text-amber-400 font-bold text-xs uppercase tracking-wider">GIÁ TRỊ CỐT LÕI</span>
                <h3 class="text-xl sm:text-3xl font-black text-white">5 Cam Kết Vàng Với Khách Hàng</h3>
                <p class="text-xs text-slate-400">Chúng tôi đặt chữ TÍN và quyền lợi của người tiêu dùng lên hàng đầu</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-xs">
                <div class="bg-white/5 p-4 rounded-2xl border border-white/10 space-y-2">
                    <span class="text-lg">🛡️</span>
                    <h4 class="font-bold text-white text-sm">Bảo Mật Dữ Liệu Tuyệt Đối</h4>
                    <p class="text-slate-400 leading-relaxed">Cam kết 100% không truy cập, không sao chép và không lưu trữ bất kỳ tệp dữ liệu in ấn nào của khách hàng khi sửa máy in.</p>
                </div>

                <div class="bg-white/5 p-4 rounded-2xl border border-white/10 space-y-2">
                    <span class="text-lg">🏷️</span>
                    <h4 class="font-bold text-white text-sm">Giá Cả Minh Bạch & Báo Trước</h4>
                    <p class="text-slate-400 leading-relaxed">Luôn kiểm tra tình trạng máy in và báo giá cụ thể trước khi thực hiện. Khách đồng ý mới sửa, không phát sinh chi phí ẩn.</p>
                </div>

                <div class="bg-white/5 p-4 rounded-2xl border border-white/10 space-y-2">
                    <span class="text-lg">⚡</span>
                    <h4 class="font-bold text-white text-sm">Giao Hàng Siêu Tốc 2 Giờ</h4>
                    <p class="text-slate-400 leading-relaxed">Đơn hàng văn phòng phẩm nội thành được xử lý và đóng gói giao trong 2 giờ làm việc, không làm gián đoạn công việc của công ty.</p>
                </div>

                <div class="bg-white/5 p-4 rounded-2xl border border-white/10 space-y-2">
                    <span class="text-lg">🔄</span>
                    <h4 class="font-bold text-white text-sm">Đổi Mới 1-Đổi-1 Trong 7 Ngày</h4>
                    <p class="text-slate-400 leading-relaxed">Bất kỳ sản phẩm văn phòng phẩm hoặc hộp mực nào có lỗi kỹ thuật từ nhà sản xuất đều được đổi mới miễn phí ngay lập tức.</p>
                </div>

                <div class="bg-white/5 p-4 rounded-2xl border border-white/10 space-y-2">
                    <span class="text-lg">🧾</span>
                    <h4 class="font-bold text-white text-sm">Hóa Đơn VAT Điện Tử Chuẩn</h4>
                    <p class="text-slate-400 leading-relaxed">Xuất hóa đơn điện tử theo quy định của Tổng Cục Thuế ngay trong ngày để doanh nghiệp dễ dàng thanh quyết toán chi phí.</p>
                </div>

                <div class="bg-white/5 p-4 rounded-2xl border border-white/10 space-y-2">
                    <span class="text-lg">💳</span>
                    <h4 class="font-bold text-white text-sm">Thanh Toán VietQR Tiện Lợi</h4>
                    <p class="text-slate-400 leading-relaxed">Tích hợp thanh toán quét mã QR động, tự động nhận diện tiền về chỉ sau 3 giây mà không cần chụp biên lai xác nhận.</p>
                </div>
            </div>
        </div>

        <!-- Showroom & Contact Card -->
        <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-sm border border-slate-200 grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
            <div class="space-y-4 text-xs sm:text-sm">
                <span class="inline-block px-3 py-1 bg-indigo-100 text-indigo-700 font-bold rounded-full text-xs">
                    📍 SHOWROOM & TRUNG TÂM BẢO HÀNH
                </span>
                <h3 class="text-xl sm:text-2xl font-black text-slate-900">
                    Ghé Thăm Cửa Hàng Hoặc Liên Hệ Trực Tiếp
                </h3>
                <div class="space-y-2.5 text-slate-600">
                    <p class="flex items-start space-x-2">
                        <span class="text-indigo-600 font-bold">🏢 Địa chỉ:</span>
                        <span>123 Đường Văn Phòng Phẩm, Phường Bến Nghé, Quận 1, TP. Hồ Chí Minh</span>
                    </p>
                    <p class="flex items-start space-x-2">
                        <span class="text-indigo-600 font-bold">⏰ Giờ hoạt động:</span>
                        <span>7h30 - 20h00 (Tất cả các ngày trong tuần, kể cả Thứ 7 và Chủ Nhật)</span>
                    </p>
                    <p class="flex items-start space-x-2">
                        <span class="text-indigo-600 font-bold">📞 Hotline bán hàng:</span>
                        <a href="tel:0901234567" class="text-rose-600 font-bold hover:underline">0901.234.567</a>
                    </p>
                    <p class="flex items-start space-x-2">
                        <span class="text-indigo-600 font-bold">💬 Zalo hỗ trợ kỹ thuật:</span>
                        <a href="https://zalo.me/0901234567" target="_blank" class="text-sky-600 font-bold hover:underline">0901.234.567</a>
                    </p>
                    <p class="flex items-start space-x-2">
                        <span class="text-indigo-600 font-bold">✉️ Email liên hệ:</span>
                        <span>hotro@vpp.local</span>
                    </p>
                </div>
            </div>

            <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200/80 space-y-4 text-center">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-indigo-700 to-amber-500 text-white flex items-center justify-center text-3xl mx-auto shadow-md">
                    🏪
                </div>
                <h4 class="font-black text-slate-900 text-base">Cần Báo Giá Đơn Hàng Lớn?</h4>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Liên hệ ngay với bộ phận kinh doanh để nhận bảng báo giá chiết khấu đặc biệt dành riêng cho công ty, trường học và đại lý.
                </p>
                <div class="pt-2 flex flex-col sm:flex-row gap-2 justify-center">
                    <a href="https://zalo.me/0901234567" target="_blank" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs shadow transition">
                        Nhắn Zalo Nhận Báo Giá
                    </a>
                    <a href="{{ url('/') }}" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-xs shadow transition">
                        Xem Danh Mục Hàng Hoá
                    </a>
                </div>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 text-xs py-8 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-2">
            <p>© 2026 Cửa Hàng Văn Phòng Phẩm & Dịch Vụ Máy In. Mọi quyền được bảo lưu.</p>
            <p class="text-slate-500">Địa chỉ: 123 Đường Văn Phòng Phẩm, P. Bến Nghé, Q.1, TP.HCM • Hotline: 0901.234.567</p>
            <div class="flex justify-center space-x-4 pt-2 text-[11px]">
                <a href="{{ url('/') }}" class="hover:text-white transition">Trang Chủ</a>
                <span>•</span>
                <a href="{{ route('storefront.about') }}" class="text-white font-bold">Giới Thiệu</a>
                <span>•</span>
                <a href="{{ route('storefront.terms') }}" class="hover:text-white transition">Chính Sách Mua Hàng</a>
                <span>•</span>
                <a href="{{ route('storefront.privacy') }}" class="hover:text-white transition">Chính Sách Bảo Mật</a>
                <span>•</span>
                <a href="{{ route('lookup.index') }}" class="hover:text-white transition">Tra Cứu Phiếu Sửa</a>
            </div>
        </div>
    </footer>

    <!-- Include Live Chat -->
    @include('storefront.components.livechat')

</body>
</html>
