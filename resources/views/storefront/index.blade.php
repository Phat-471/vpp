<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cửa Hàng Văn Phòng Phẩm & Dịch Vụ Sửa Chữa Máy In Chuyên Nghiệp</title>
    <meta name="description" content="Cung cấp hơn 1.000 mặt hàng văn phòng phẩm, giấy in, bút viết giá sỉ và lẻ. Dịch vụ nạp mực, sửa chữa máy in Canon, HP, Brother, Epson lấy ngay, tra cứu tiến độ online.">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col justify-between antialiased selection:bg-indigo-600 selection:text-white">

    <!-- 1. Top Announcement Bar (Multi-Device) -->
    <div class="bg-indigo-950 text-slate-300 text-xs py-2 border-b border-indigo-900/60 hidden sm:block">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <span class="flex items-center space-x-1 text-amber-400 font-semibold">
                    <span>⚡ Tiếp nhận máy in dưới 60 giây</span>
                </span>
                <span class="text-slate-500">|</span>
                <span>📍 Số 123 Đường Văn Phòng Phẩm, P. Bến Nghé, Q.1, TP.HCM</span>
                <span class="text-slate-500">|</span>
                <span>⏰ Giờ mở cửa: 7h30 - 20h00 (Cả T7 & CN)</span>
            </div>
            <div class="flex items-center space-x-3 text-xs">
                <span>Hỗ trợ kỹ thuật: <a href="tel:0901234567" class="text-white font-bold hover:text-amber-400">0901.234.567</a></span>
                <span class="text-slate-500">|</span>
                <a href="https://zalo.me/0901234567" target="_blank" class="text-sky-400 hover:underline">Zalo Tư Vấn</a>
            </div>
        </div>
    </div>

    <!-- 2. Main Navigation Bar (Full Responsive Desktop & Mobile) -->
    <header class="bg-white sticky top-0 z-40 border-b border-slate-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-20">

                <!-- Logo & Brand Name -->
                <div class="flex items-center space-x-3">
                    <a href="{{ url('/') }}" class="flex items-center space-x-3 group">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-gradient-to-tr from-indigo-700 via-indigo-600 to-amber-500 flex items-center justify-center font-black text-white text-lg sm:text-xl shadow-md group-hover:scale-105 transition transform">
                            VP
                        </div>
                        <div>
                            <span class="text-base sm:text-xl font-black tracking-tight text-slate-900 block leading-tight">
                                VPP & DỊCH VỤ MÁY IN
                            </span>
                            <span class="text-[11px] text-indigo-600 font-semibold tracking-wider uppercase block">
                                Văn Phòng Phẩm Sỉ & Lẻ • Sửa Máy In Lấy Ngay
                            </span>
                        </div>
                    </a>
                </div>

                <!-- Desktop Search Bar -->
                <div class="hidden md:flex flex-1 max-w-md mx-8">
                    <form action="{{ url('/') }}" method="GET" class="w-full relative">
                        <input type="text" name="q" value="{{ request('q') }}"
                            placeholder="Tìm kiếm giấy in, bút, hộp mực 12A, linh kiện..."
                            class="w-full text-xs sm:text-sm pl-10 pr-24 py-2.5 rounded-full border border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 bg-slate-50/70" />
                        <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <button type="submit" class="absolute right-1.5 top-1.5 px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-full transition">
                            Tìm kiếm
                        </button>
                    </form>
                </div>

                <!-- Action Buttons & Navigation -->
                <div class="flex items-center space-x-2 sm:space-x-3">
                    <button type="button" onclick="openCartDrawer()" class="relative inline-flex items-center space-x-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 px-3.5 py-2 rounded-xl text-xs sm:text-sm font-bold transition shadow-xs">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                        <span>Giỏ Hàng</span>
                        <span id="header-cart-badge" class="px-2 py-0.5 bg-emerald-600 text-white rounded-full text-[10px] font-black hidden">0</span>
                    </button>

                    <!-- Account / Login -->
                    @if(auth('customer')->check())
                    <a href="{{ route('customer.profile') }}" class="inline-flex items-center space-x-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 px-3 py-2 rounded-xl text-xs sm:text-sm font-bold transition">
                        <span>👤</span>
                        <span class="hidden sm:inline">{{ Str::limit(auth('customer')->user()->name, 12) }}</span>
                    </a>
                    @else
                    <a href="{{ route('customer.login') }}" class="inline-flex items-center space-x-1 bg-slate-100 hover:bg-slate-200 text-slate-800 border border-slate-200 px-3 py-2 rounded-xl text-xs sm:text-sm font-bold transition">
                        <span>Đăng Nhập</span>
                    </a>
                    @endif

                    <a href="{{ route('lookup.index') }}" class="hidden sm:inline-flex items-center space-x-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 px-3.5 py-2 rounded-xl text-xs sm:text-sm font-bold transition">
                        <span>Tra Cứu</span>
                    </a>

                    <a href="{{ url('/admin') }}" class="inline-flex items-center space-x-1 bg-slate-900 hover:bg-indigo-900 text-white px-3 py-2 rounded-xl text-xs sm:text-sm font-bold shadow transition">
                        <span class="text-amber-400">⚡</span>
                        <span>POS</span>
                    </a>
                </div>

            </div>

            <!-- Mobile Navigation Quick Pills (Instant 1-tap access to all subpages) -->
            <div class="flex items-center space-x-2 overflow-x-auto no-scrollbar pb-3 pt-1 md:hidden text-[11px] font-bold">
                <a href="{{ url('/') }}" class="px-3 py-1 bg-indigo-50 text-indigo-700 rounded-full whitespace-nowrap border border-indigo-200">🏠 Trang Chủ</a>
                <a href="{{ route('storefront.products') }}" class="px-3 py-1 bg-white text-slate-700 rounded-full whitespace-nowrap border border-slate-200">📦 Sản Phẩm</a>
                <a href="{{ route('storefront.wholesale') }}" class="px-3 py-1 bg-amber-50 text-amber-800 rounded-full whitespace-nowrap border border-amber-200">⭐ Đại Lý Sỉ</a>
                <a href="{{ route('storefront.checkout-page') }}" class="px-3 py-1 bg-emerald-50 text-emerald-800 rounded-full whitespace-nowrap border border-emerald-200">🛒 Thanh Toán</a>
                <a href="{{ route('lookup.index') }}" class="px-3 py-1 bg-white text-slate-700 rounded-full whitespace-nowrap border border-slate-200">🔍 Tra Cứu</a>
                <a href="{{ route('storefront.about') }}" class="px-3 py-1 bg-white text-slate-700 rounded-full whitespace-nowrap border border-slate-200">🏢 Giới Thiệu</a>
                <a href="{{ route('storefront.privacy') }}" class="px-3 py-1 bg-white text-slate-700 rounded-full whitespace-nowrap border border-slate-200">🛡️ Bảo Mật</a>
            </div>
        </div>

        <!-- Desktop Sub-Navigation Bar -->
        <div class="bg-slate-50 border-t border-slate-200 text-xs font-bold text-slate-700 hidden md:block">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-10">
                <div class="flex items-center space-x-5">
                    <a href="{{ url('/') }}" class="text-indigo-700 hover:text-indigo-900 flex items-center space-x-1 transition">
                        <span>🏠</span> <span>Trang Chủ</span>
                    </a>
                    <a href="{{ route('storefront.products') }}" class="hover:text-indigo-700 flex items-center space-x-1 transition">
                        <span>📦</span> <span>Sản Phẩm (1.000+ SKU)</span>
                    </a>
                    <a href="{{ route('storefront.wholesale') }}" class="hover:text-amber-800 flex items-center space-x-1 transition text-amber-700">
                        <span>⭐</span> <span>Đăng Ký Đại Lý Sỉ</span>
                    </a>
                    <a href="{{ route('lookup.index') }}" class="hover:text-indigo-700 flex items-center space-x-1 transition">
                        <span>🔍</span> <span>Tra Cứu Phiếu Sửa</span>
                    </a>
                    <a href="{{ route('storefront.about') }}" class="hover:text-indigo-700 flex items-center space-x-1 transition">
                        <span>🏢</span> <span>Giới Thiệu</span>
                    </a>
                    <a href="{{ route('storefront.terms') }}" class="hover:text-indigo-700 flex items-center space-x-1 transition">
                        <span>📄</span> <span>Chính Sách</span>
                    </a>
                    <a href="{{ route('storefront.privacy') }}" class="hover:text-emerald-700 flex items-center space-x-1 transition text-emerald-800">
                        <span>🛡️</span> <span>Bảo Mật Dữ Liệu</span>
                    </a>
                </div>
                <div class="flex items-center space-x-3 text-[11px] text-slate-500">
                    <span class="flex items-center space-x-1 text-emerald-600 font-semibold">
                        <span>✔</span> <span>Chính hãng 100%</span>
                    </span>
                    <span>•</span>
                    <span class="text-indigo-600 font-bold">Freeship nội thành từ 500k</span>
                </div>
            </div>
        </div>
    </header>

    <!-- 3. Main Content Container (Full Width Responsive on Desktop/Tablet/Mobile) -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex-1 space-y-8 w-full">

        <!-- HERO SECTION (Desktop 2-Column / Mobile Stacked) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">

            <!-- Hero Banner Box (Desktop: 8 cols, Mobile: 12 cols) -->
            <div class="lg:col-span-8 relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-950 via-indigo-900 to-slate-900 text-white p-6 sm:p-10 shadow-xl border border-indigo-800/80 flex flex-col justify-between">
                <div class="relative z-10 space-y-4 max-w-2xl">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-amber-400 text-indigo-950 shadow-sm">
                            ⭐ HỆ THỐNG VẬN HÀNH THÔNG MINH
                        </span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-white/10 text-indigo-200 border border-white/10">
                            Tiếp nhận quầy < 60 giây
                        </span>
                    </div>

                    <h2 class="text-2xl sm:text-4xl lg:text-5xl font-black leading-tight sm:leading-tight text-white tracking-tight">
                        Văn Phòng Phẩm Tận Gốc & Sửa Chữa Máy In <span class="text-amber-400">Lấy Ngay</span>
                    </h2>

                    <p class="text-xs sm:text-base text-indigo-200 leading-relaxed font-normal">
                        Hơn <b>1.000+ SKU</b> văn phòng phẩm sẵn kho (hỗ trợ bán sỉ theo thùng). Đội ngũ thợ giàu kinh nghiệm xử lý nhanh các lỗi kẹt giấy, mờ bản in, nạp mực cho các dòng máy <b>Canon, HP, Brother, Epson</b>. Tra cứu tiến độ máy online minh bạch 24/7.
                    </p>

                    <!-- Trust Badges -->
                    <div class="pt-2 grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                        <div class="bg-white/10 backdrop-blur rounded-xl p-2.5 border border-white/10">
                            <span class="block font-bold text-amber-300">1.000+ SKU</span>
                            <span class="text-[11px] text-slate-300">Văn phòng phẩm sẵn kho</span>
                        </div>
                        <div class="bg-white/10 backdrop-blur rounded-xl p-2.5 border border-white/10">
                            <span class="block font-bold text-emerald-400">Đơn vị kép</span>
                            <span class="text-[11px] text-slate-300">Bán lẻ Ram & Sỉ Thùng</span>
                        </div>
                        <div class="bg-white/10 backdrop-blur rounded-xl p-2.5 border border-white/10">
                            <span class="block font-bold text-sky-300">30 Phút</span>
                            <span class="text-[11px] text-slate-300">Nạp mực chuẩn nét</span>
                        </div>
                        <div class="bg-white/10 backdrop-blur rounded-xl p-2.5 border border-white/10">
                            <span class="block font-bold text-purple-300">VietQR Động</span>
                            <span class="text-[11px] text-slate-300">Khớp lệnh 3s tự động</span>
                        </div>
                    </div>

                    <!-- Call To Action Buttons -->
                    <div class="pt-4 flex flex-wrap gap-3 items-center">
                        <a href="#dat-lich" class="px-5 py-3 rounded-2xl bg-amber-400 hover:bg-amber-300 text-slate-950 font-black text-xs sm:text-sm shadow-lg hover:shadow-xl transition transform hover:-translate-y-0.5">
                            Đặt Thợ Sửa Máy In Ngay ↓
                        </a>
                        <a href="{{ route('lookup.index') }}" class="px-5 py-3 rounded-2xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs sm:text-sm border border-white/20 backdrop-blur transition">
                            Tra Cứu Tiến Độ Phiếu Sửa →
                        </a>
                    </div>
                </div>

                <!-- Decorative blur circle background -->
                <div class="absolute -right-12 -bottom-12 w-64 h-64 bg-indigo-600/30 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute right-1/4 -top-12 w-48 h-48 bg-amber-500/20 rounded-full blur-3xl pointer-events-none"></div>
            </div>

            <!-- Quick Printer Match & Stats Card (Desktop: 4 cols, Mobile: 12 cols) -->
            <div class="lg:col-span-4 bg-white rounded-3xl p-6 sm:p-7 shadow-lg border border-slate-200/90 flex flex-col justify-between">
                <div>
                    <div class="flex items-center space-x-2 text-indigo-700 font-extrabold text-xs uppercase tracking-wider mb-2">
                        <span class="p-1.5 bg-indigo-100 rounded-lg">🖨️</span>
                        <span>Tra Cứu Nhanh Theo Dòng Máy</span>
                    </div>

                    <h3 class="text-lg sm:text-xl font-black text-slate-900 leading-snug mb-2">
                        Tìm Hộp Mực & Linh Kiện Đúng 100% Cho Máy Của Bạn
                    </h3>

                    <p class="text-xs text-slate-500 mb-4 leading-relaxed">
                        Chọn model máy in của bạn để hệ thống lọc ra ngay các loại mực in, trống drum, gạt mực lắp vừa vặn tuyệt đối:
                    </p>

                    <!-- Quick Printer Selector Form -->
                    <form action="{{ url('/') }}" method="GET" class="space-y-3">
                        @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}" />
                        @endif
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Chọn Dòng Máy In:</label>
                            <select name="printer" onchange="this.form.submit()" class="w-full text-xs sm:text-sm py-2.5 px-3.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 bg-slate-50 font-medium text-slate-800">
                                <option value="">-- Tất cả các dòng máy in --</option>
                                @foreach($printerModels as $pm)
                                <option value="{{ $pm->id }}" {{ request('printer') == $pm->id ? 'selected' : '' }}>
                                    {{ $pm->brand }} {{ $pm->model_name }} (Dùng: {{ $pm->compatible_cartridges }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                        @if(request('printer'))
                        <a href="{{ url('/') }}" class="inline-block text-xs text-rose-600 hover:underline font-semibold">
                            ✕ Xóa bộ lọc dòng máy in
                        </a>
                        @endif
                    </form>
                </div>

                <!-- Credibility Stats Box -->
                <div class="mt-6 pt-5 border-t border-slate-100 grid grid-cols-3 gap-2 text-center">
                    <div class="p-2 bg-slate-50 rounded-xl">
                        <span class="block text-lg font-black text-indigo-700">{{ $stats['total_products'] }}</span>
                        <span class="text-[10px] text-slate-500 font-semibold">Sản phẩm VPP</span>
                    </div>
                    <div class="p-2 bg-slate-50 rounded-xl">
                        <span class="block text-lg font-black text-indigo-700">{{ $stats['total_printers'] }}</span>
                        <span class="text-[10px] text-slate-500 font-semibold">Dòng máy hỗ trợ</span>
                    </div>
                    <div class="p-2 bg-slate-50 rounded-xl">
                        <span class="block text-lg font-black text-emerald-600">{{ $stats['served_tickets'] }}</span>
                        <span class="text-[10px] text-slate-500 font-semibold">Phiếu sửa máy</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- 4. VALUE PROPOSITIONS (4 Columns on Desktop, 2 on Tablet, 1 on Mobile) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-4 sm:p-5 rounded-2xl shadow-sm border border-slate-200/80 flex items-start space-x-3.5 hover:shadow-md transition">
                <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-lg flex-shrink-0">
                    📦
                </div>
                <div>
                    <h4 class="font-bold text-sm text-slate-900 mb-0.5">Bán Sỉ & Bán Lẻ Tận Gốc</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">Đơn vị quy đổi kép Ram $\leftrightarrow$ Thùng, chiết khấu tốt cho văn phòng & trường học.</p>
                </div>
            </div>

            <div class="bg-white p-4 sm:p-5 rounded-2xl shadow-sm border border-slate-200/80 flex items-start space-x-3.5 hover:shadow-md transition">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-lg flex-shrink-0">
                    🔧
                </div>
                <div>
                    <h4 class="font-bold text-sm text-slate-900 mb-0.5">Sửa Chữa & Nạp Mực Lấy Ngay</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">Linh kiện sẵn kho trừ tự động, thợ kiểm tra trực tiếp tại quầy, bảo hành 3 tháng.</p>
                </div>
            </div>

            <div class="bg-white p-4 sm:p-5 rounded-2xl shadow-sm border border-slate-200/80 flex items-start space-x-3.5 hover:shadow-md transition">
                <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-lg flex-shrink-0">
                    🔍
                </div>
                <div>
                    <h4 class="font-bold text-sm text-slate-900 mb-0.5">Tra Cứu Online 24/7</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">Bảo mật chống IDOR (Mã phiếu + 4 số cuối SĐT), theo dõi từng bước máy sửa xong chưa.</p>
                </div>
            </div>

            <div class="bg-white p-4 sm:p-5 rounded-2xl shadow-sm border border-slate-200/80 flex items-start space-x-3.5 hover:shadow-md transition">
                <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center text-lg flex-shrink-0">
                    💳
                </div>
                <div>
                    <h4 class="font-bold text-sm text-slate-900 mb-0.5">VietQR Tự Động 3–5s</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">Quét mã chuyển khoản SePay/Casso tự động khớp đơn, không cần chụp màn hình.</p>
                </div>
            </div>
        </div>

        <!-- PROMOTIONAL CAMPAIGN BANNERS (Dual High-Impact Visual Cards) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-stretch">
            
            <!-- Promo Banner 1: Fast Delivery & Wholesale Stationery -->
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-amber-500 via-orange-600 to-red-600 text-white p-6 sm:p-8 shadow-xl flex flex-col justify-between group">
                <div class="relative z-10 space-y-3">
                    <div class="flex items-center space-x-2">
                        <span class="px-3 py-1 bg-white/20 backdrop-blur rounded-full text-[11px] font-black uppercase tracking-wider text-white border border-white/20">
                            ⚡ HỎA TỐC 120 PHÚT
                        </span>
                        <span class="px-2.5 py-0.5 bg-yellow-300 text-slate-900 rounded-full text-[11px] font-extrabold shadow-sm">
                            GIÁ SỈ THEO THÙNG
                        </span>
                    </div>

                    <h3 class="text-xl sm:text-2xl font-black text-white leading-tight">
                        Đại Tiệc Giấy In & Văn Phòng Phẩm Sỉ Cho Doanh Nghiệp
                    </h3>

                    <p class="text-xs sm:text-sm text-amber-100 leading-relaxed max-w-md">
                        Double A, PaperOne, IK Plus chính hãng. Đơn vị kép bán lẻ Ram & chiết khấu cực sâu theo Thùng. Miễn phí vận chuyển nội thành từ 500k hoặc từ 5 thùng giấy.
                    </p>

                    <div class="pt-2 flex flex-wrap gap-2 text-xs font-bold">
                        <span class="bg-black/20 backdrop-blur px-2.5 py-1 rounded-xl">📄 Giấy A4 từ 58k/ram</span>
                        <span class="bg-black/20 backdrop-blur px-2.5 py-1 rounded-xl">🚚 Ship trong 2 giờ</span>
                        <span class="bg-black/20 backdrop-blur px-2.5 py-1 rounded-xl">🧾 Hóa đơn VAT điện tử</span>
                    </div>
                </div>

                <div class="relative z-10 pt-6 flex items-center justify-between">
                    <a href="{{ url('/?category=giay-in-photo') }}" class="px-5 py-2.5 bg-white text-orange-700 hover:bg-amber-50 font-black text-xs sm:text-sm rounded-2xl shadow-lg transition transform group-hover:translate-x-1 flex items-center space-x-1.5">
                        <span>Xem Báo Giá Giấy In Sỉ</span>
                        <span>→</span>
                    </a>
                    <span class="text-3xl sm:text-4xl filter drop-shadow">📦</span>
                </div>

                <!-- Abstract Decorative Shapes -->
                <div class="absolute -right-8 -bottom-8 w-48 h-48 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
                <div class="absolute right-12 top-0 w-32 h-32 bg-yellow-400/20 rounded-full blur-xl pointer-events-none"></div>
            </div>

            <!-- Promo Banner 2: Printer Service & Supreme Data Privacy Guarantee -->
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-950 via-slate-900 to-indigo-900 text-white p-6 sm:p-8 shadow-xl border border-indigo-800 flex flex-col justify-between group">
                <div class="relative z-10 space-y-3">
                    <div class="flex items-center space-x-2">
                        <span class="px-3 py-1 bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 rounded-full text-[11px] font-black uppercase tracking-wider">
                            🛡️ TIÊU CHUẨN BẢO MẬT TỐI CAO
                        </span>
                        <span class="px-2.5 py-0.5 bg-indigo-500/40 text-indigo-200 rounded-full text-[11px] font-bold">
                            BẢO HÀNH 90 NGÀY
                        </span>
                    </div>

                    <h3 class="text-xl sm:text-2xl font-black text-white leading-tight">
                        Dịch Vụ Sửa Máy In & Cam Kết Bảo Mật Tuyệt Đối Dữ Liệu
                    </h3>

                    <p class="text-xs sm:text-sm text-slate-300 leading-relaxed max-w-md">
                        Kỹ thuật viên 10+ năm kinh nghiệm nạp mực, thay drum, gạt mực, bao lụa Canon/HP/Brother/Epson trong 30 phút. Cam kết 100% không đọc, không sao chép dữ liệu in ấn.
                    </p>

                    <div class="pt-2 flex flex-wrap gap-2 text-xs font-bold">
                        <span class="bg-white/10 px-2.5 py-1 rounded-xl text-emerald-300">✔ Không lưu trữ dữ liệu test</span>
                        <span class="bg-white/10 px-2.5 py-1 rounded-xl text-sky-300">✔ Tra cứu tiến độ 2 lớp</span>
                        <span class="bg-white/10 px-2.5 py-1 rounded-xl text-amber-300">✔ Đổi mới 1-đổi-1</span>
                    </div>
                </div>

                <div class="relative z-10 pt-6 flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <a href="#dat-lich" class="px-5 py-2.5 bg-amber-400 hover:bg-amber-300 text-slate-950 font-black text-xs sm:text-sm rounded-2xl shadow-lg transition transform group-hover:scale-105">
                            Đặt Thợ Nạp Mực
                        </a>
                        <a href="{{ route('storefront.privacy') }}" class="px-3.5 py-2.5 bg-white/10 hover:bg-white/20 text-white font-bold text-xs rounded-2xl border border-white/20 transition">
                            Chính Sách Bảo Mật →
                        </a>
                    </div>
                    <span class="text-3xl sm:text-4xl filter drop-shadow">🖨️</span>
                </div>

                <!-- Abstract Decorative Shapes -->
                <div class="absolute -right-8 -bottom-8 w-48 h-48 bg-indigo-500/20 rounded-full blur-2xl pointer-events-none"></div>
                <div class="absolute left-1/2 -top-10 w-40 h-40 bg-emerald-500/10 rounded-full blur-2xl pointer-events-none"></div>
            </div>

        </div>

        <!-- 5. CATEGORY NAVIGATION (Horizontal & Grid on Desktop) -->
        <div class="bg-white p-5 rounded-3xl shadow-sm border border-slate-200/80">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base sm:text-lg font-black text-slate-900">Danh Mục Ngành Hàng</h3>
                    <p class="text-xs text-slate-500">Khám phá các dòng sản phẩm văn phòng phẩm & vật tư máy in</p>
                </div>
                @if(request('category'))
                <a href="{{ url('/') }}" class="text-xs text-indigo-600 font-bold hover:underline">
                    Xem tất cả ngành hàng
                </a>
                @endif
            </div>

            <!-- Categories Grid (Responsive 2 to 7 cols) -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-7 gap-3">
                <a href="{{ url('/') }}" class="p-3 rounded-2xl border text-center transition flex flex-col items-center justify-center group {{ !request('category') ? 'bg-indigo-600 text-white border-indigo-600 shadow-md' : 'bg-slate-50 hover:bg-indigo-50 border-slate-200 text-slate-700' }}">
                    <span class="text-2xl mb-1 group-hover:scale-110 transition">📋</span>
                    <span class="text-xs font-bold leading-tight">Tất Cả Hàng</span>
                    <span class="text-[10px] opacity-75 mt-0.5">{{ $products->count() }} mặt hàng</span>
                </a>

                @foreach($categories as $cat)
                <a href="{{ url('/?category=' . $cat->slug . (request('printer') ? '&printer=' . request('printer') : '')) }}" 
                   class="p-3 rounded-2xl border text-center transition flex flex-col items-center justify-center group {{ request('category') == $cat->slug ? 'bg-indigo-600 text-white border-indigo-600 shadow-md' : 'bg-slate-50 hover:bg-indigo-50 border-slate-200 text-slate-700' }}">
                    <span class="text-2xl mb-1 group-hover:scale-110 transition">
                        @if(str_contains($cat->slug, 'giay')) 📄
                        @elseif(str_contains($cat->slug, 'but')) ✏️
                        @elseif(str_contains($cat->slug, 'bia')) 📁
                        @elseif(str_contains($cat->slug, 'so')) 📓
                        @elseif(str_contains($cat->slug, 'dung-cu')) ✂️
                        @elseif(str_contains($cat->slug, 'hop-muc')) 🖨️
                        @elseif(str_contains($cat->slug, 'linh-kien')) ⚙️
                        @else 📦
                        @endif
                    </span>
                    <span class="text-xs font-bold leading-tight line-clamp-1">{{ $cat->name }}</span>
                    <span class="text-[10px] opacity-75 mt-0.5">{{ $cat->products_count ?: $cat->products()->count() }} sản phẩm</span>
                </a>
                @endforeach
            </div>
        </div>

        <!-- 6. PRODUCT CATALOG SHOWCASE (Responsive Grid 2 to 5 columns on Desktop) -->
        <div class="space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-slate-200 pb-3">
                <div>
                    <h3 class="text-lg sm:text-xl font-black text-slate-900 flex items-center space-x-2">
                        <span>Danh Sách Sản Phẩm & Vật Tư Sẵn Kho</span>
                        @if(request('q'))
                        <span class="text-xs bg-amber-100 text-amber-800 font-bold px-2 py-0.5 rounded-md">Từ khóa: "{{ request('q') }}"</span>
                        @endif
                    </h3>
                    <p class="text-xs text-slate-500">Giá bán lẻ niêm yết rõ ràng, hỗ trợ quy đổi đơn vị theo thùng/lốc</p>
                </div>
                <div class="flex items-center space-x-2 text-xs">
                    <span class="text-slate-500">Hiển thị: <b>{{ $products->count() }}</b> sản phẩm</span>
                </div>
            </div>

            <!-- Product Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                @forelse($products as $p)
                <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200 flex flex-col justify-between hover:shadow-lg hover:border-indigo-300 transition duration-150 group">
                    <div>
                        <!-- Category Tag & Status Badges -->
                        <div class="flex items-center justify-between text-[11px] mb-2">
                            <span class="text-slate-400 font-medium truncate max-w-[100px]">{{ $p->category?->name }}</span>
                            @if($p->is_service_part)
                            <span class="bg-amber-100 text-amber-800 font-extrabold px-1.5 py-0.5 rounded text-[10px]">Máy in</span>
                            @else
                            <span class="bg-slate-100 text-slate-600 font-medium px-1.5 py-0.5 rounded text-[10px]">VPP</span>
                            @endif
                        </div>

                        <!-- Product Thumbnail Image / Placeholder -->
                        <div class="w-full h-32 sm:h-36 bg-slate-50 rounded-xl mb-3 flex items-center justify-center overflow-hidden border border-slate-100 p-2">
                            <img src="{{ $p->image_url }}" alt="{{ $p->name }}" class="max-h-full max-w-full object-contain group-hover:scale-105 transition transform duration-200" />
                        </div>

                        <!-- Product SKU & Barcode -->
                        <span class="text-[10px] font-mono text-slate-400 block mb-1">SKU: {{ $p->sku }}</span>

                        <!-- Product Name -->
                        <h4 class="text-xs sm:text-sm font-bold text-slate-900 leading-snug line-clamp-2 mb-2 group-hover:text-indigo-600 transition">
                            {{ $p->name }}
                        </h4>

                        <!-- Price Section (VND Formatted) -->
                        <div class="space-y-1">
                            <div class="text-indigo-700 font-black text-sm sm:text-base font-mono">
                                {{ number_format($p->retail_price, 0, ',', '.') }} ₫
                                <span class="text-[11px] font-normal text-slate-500 font-sans">/ {{ $p->base_unit }}</span>
                            </div>

                            <!-- Dual-Unit Packaging Badges (Ram <-> Thùng) -->
                            @if($p->units->count() > 0)
                            <div class="space-y-1 pt-1">
                                @foreach($p->units as $u)
                                <div class="bg-emerald-50 text-emerald-800 px-2 py-0.5 rounded-md text-[10px] font-bold border border-emerald-200 flex justify-between items-center">
                                    <span>{{ $u->unit_name }}:</span>
                                    <span class="font-mono text-emerald-950 font-black">{{ number_format($u->price, 0, ',', '.') }} ₫</span>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Footer Details & Add to Cart Actions -->
                    <div class="mt-4 pt-3 border-t border-slate-100 space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-[11px] text-slate-500">
                                Kho: <b class="{{ $p->stock_quantity <= 5 ? 'text-red-500 font-black' : 'text-emerald-700 font-bold' }}">{{ $p->stock_quantity }}</b> {{ $p->base_unit }}
                            </span>
                            @if($p->stock_quantity <= 0)
                            <span class="text-[10px] text-rose-600 font-bold bg-rose-50 px-1.5 py-0.5 rounded">Tạm hết</span>
                            @endif
                        </div>

                        <div class="space-y-1.5">
                            <button
                                type="button"
                                onclick="addToCart({{ $p->id }}, null, '{{ addslashes($p->name) }}', '{{ $p->base_unit }}', {{ (float) $p->retail_price }}, '{{ $p->image_url }}')"
                                class="w-full py-1.5 px-2.5 bg-indigo-50 hover:bg-indigo-600 hover:text-white text-indigo-700 rounded-xl text-xs font-bold transition flex items-center justify-between border border-indigo-200"
                            >
                                <span>+ Thêm {{ $p->base_unit }}</span>
                                <span class="font-mono">{{ number_format($p->retail_price, 0, ',', '.') }}₫</span>
                            </button>

                            @if($p->units->count() > 0)
                                @foreach($p->units as $u)
                                <button
                                    type="button"
                                    onclick="addToCart({{ $p->id }}, {{ $u->id }}, '{{ addslashes($p->name) }}', '{{ $u->unit_name }}', {{ (float) $u->price }}, '{{ $p->image_url }}')"
                                    class="w-full py-1 px-2.5 bg-emerald-50 hover:bg-emerald-600 hover:text-white text-emerald-800 rounded-xl text-[11px] font-bold transition flex items-center justify-between border border-emerald-200"
                                >
                                    <span>+ Thêm {{ $u->unit_name }} (x{{ $u->conversion_rate }})</span>
                                    <span class="font-mono">{{ number_format($u->price, 0, ',', '.') }}₫</span>
                                </button>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full text-center py-12 bg-white rounded-3xl border border-slate-200">
                    <span class="text-4xl block mb-2">🔍</span>
                    <h4 class="text-base font-bold text-slate-700">Không tìm thấy sản phẩm phù hợp</h4>
                    <p class="text-xs text-slate-400 mt-1">Quý khách vui lòng thử tìm với từ khóa khác hoặc xóa bộ lọc.</p>
                    <a href="{{ url('/') }}" class="inline-block mt-3 px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-bold">
                        Xem tất cả sản phẩm
                    </a>
                </div>
                @endforelse
            </div>
        </div>

        <!-- MID-PAGE FULL-WIDTH CAMPAIGN STRIP BANNER -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white p-6 sm:p-10 shadow-2xl border border-indigo-900">
            <div class="relative z-10 flex flex-col lg:flex-row items-center justify-between gap-6">
                <div class="space-y-3 text-center lg:text-left max-w-2xl">
                    <div class="inline-flex items-center space-x-2 bg-amber-400/20 text-amber-300 px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider border border-amber-400/30">
                        <span>💬 TƯ VẤN VIÊN KỸ THUẬT & BÁO GIÁ ĐANG TRỰC TUYẾN</span>
                    </div>
                    <h3 class="text-xl sm:text-3xl font-black text-white leading-tight">
                        Doanh Nghiệp Cần Cung Ứng VPP Trọn Gói Hoặc Hỗ Trợ Kỹ Thuật?
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                        Hơn 1.000 SKU văn phòng phẩm sẵn sàng xuất kho trong 2 giờ. Kỹ thuật viên sẵn sàng tư vấn chọn hộp mực tương thích, xử lý kẹt máy hoặc gửi báo giá chiết khấu đại lý.
                    </p>
                </div>

                <div class="relative z-10 flex flex-wrap gap-3 justify-center lg:justify-end flex-shrink-0">
                    <button
                        type="button"
                        onclick="toggleLiveChat()"
                        class="px-5 py-3.5 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white font-black text-xs sm:text-sm rounded-2xl shadow-xl transition transform hover:scale-105 flex items-center space-x-2"
                    >
                        <span>💬</span>
                        <span>Chat Với Kỹ Thuật Viên 24/7</span>
                    </button>
                    <a
                        href="tel:0901234567"
                        class="px-5 py-3.5 bg-white text-slate-950 hover:bg-slate-100 font-bold text-xs sm:text-sm rounded-2xl shadow-lg transition flex items-center space-x-2"
                    >
                        <span>📞 0901.234.567</span>
                    </a>
                    <a
                        href="{{ route('storefront.terms') }}"
                        class="px-4 py-3.5 bg-white/10 hover:bg-white/20 text-white font-bold text-xs sm:text-sm rounded-2xl border border-white/20 transition flex items-center space-x-1.5"
                    >
                        <span>Chính Sách Mua Hàng →</span>
                    </a>
                </div>
            </div>

            <!-- Background Glowing Effect -->
            <div class="absolute -left-12 -bottom-12 w-64 h-64 bg-indigo-600/30 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -right-12 -top-12 w-64 h-64 bg-emerald-500/20 rounded-full blur-3xl pointer-events-none"></div>
        </div>

        <!-- 7. REPAIR SERVICE & BOOKING INTAKE (Responsive 2 Columns on Desktop) -->
        <div id="dat-lich" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch scroll-mt-24">

            <!-- Left: Technical Process & Guarantees (lg:col-span-6) -->
            <div class="lg:col-span-6 bg-gradient-to-br from-slate-900 to-indigo-950 text-white p-6 sm:p-8 rounded-3xl shadow-lg border border-slate-800 flex flex-col justify-between">
                <div>
                    <span class="text-xs font-black uppercase tracking-wider text-amber-400 block mb-2">
                        QUY TRÌNH KỸ THUẬT MINH BẠCH
                    </span>
                    <h3 class="text-xl sm:text-3xl font-black leading-tight text-white mb-3">
                        Dịch Vụ Sửa Máy In & Nạp Mực Tiêu Chuẩn 2 Nhánh
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-300 leading-relaxed mb-6">
                        Cửa hàng áp dụng quy trình kiểm tra rõ ràng, khách hàng luôn nắm rõ chi phí trước khi thợ tiến hành sửa chữa:
                    </p>

                    <!-- 2 Flows Explanation -->
                    <div class="space-y-4 text-xs">
                        <div class="bg-white/10 p-4 rounded-2xl border border-white/10">
                            <span class="font-extrabold text-amber-300 text-sm block mb-1">
                                Nhánh 1: Thợ có mặt kiểm tra & Báo giá trực tiếp
                            </span>
                            <p class="text-slate-300">
                                Kiểm tra lỗi ngay tại quầy $\rightarrow$ Báo giá tiền công và linh kiện $\rightarrow$ Chuyển sang sửa chữa $\rightarrow$ In phiếu biên nhận có sẵn chi phí và giờ hẹn trả.
                            </p>
                        </div>

                        <div class="bg-white/10 p-4 rounded-2xl border border-white/10">
                            <span class="font-extrabold text-sky-300 text-sm block mb-1">
                                Nhánh 2: Tiếp nhận trước - Tháo máy chẩn đoán sau
                            </span>
                            <p class="text-slate-300">
                                Thu ngân nhập máy, ghi nhận phụ kiện $\rightarrow$ In phiếu hẹn có cuống dán máy $\rightarrow$ Thợ kiểm tra chuyên sâu $\rightarrow$ Gọi điện thoại báo giá, khách đồng ý mới tiến hành sửa.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Guarantee List -->
                <div class="mt-6 pt-5 border-t border-white/10 grid grid-cols-2 gap-3 text-xs text-slate-300">
                    <div class="flex items-center space-x-2">
                        <span class="text-emerald-400 font-bold">✓</span>
                        <span>Linh kiện chính hãng 100%</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="text-emerald-400 font-bold">✓</span>
                        <span>Bảo hành chu đáo 3 tháng</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="text-emerald-400 font-bold">✓</span>
                        <span>Không đổi tráo linh kiện</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="text-emerald-400 font-bold">✓</span>
                        <span>Tra cứu tiến độ online 24/7</span>
                    </div>
                </div>
            </div>

            <!-- Right: Quick Booking Form (lg:col-span-6) -->
            <div class="lg:col-span-6 bg-white p-6 sm:p-8 rounded-3xl shadow-lg border border-slate-200 flex flex-col justify-between">
                <div>
                    <div class="flex items-center space-x-2 text-indigo-700 font-extrabold text-xs uppercase tracking-wider mb-2">
                        <span class="p-1.5 bg-indigo-100 rounded-lg">📋</span>
                        <span>ĐẶT HẸN TRỰC TUYẾN</span>
                    </div>

                    <h3 class="text-xl sm:text-2xl font-black text-slate-900 leading-tight mb-2">
                        Gửi Yêu Cầu Sửa Chữa / Nạp Mực
                    </h3>
                    <p class="text-xs text-slate-500 mb-5">
                        Điền nhanh thông tin dưới đây, kỹ thuật viên sẽ chuẩn bị trước linh kiện và gọi hỗ trợ bạn ngay:
                    </p>

                    @if(session('success'))
                    <div class="mb-4 p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs flex items-center space-x-2">
                        <span class="text-base">✓</span>
                        <span>{{ session('success') }}</span>
                    </div>
                    @endif

                    @if($errors->any())
                    <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-2xl text-xs">
                        {{ $errors->first() }}
                    </div>
                    @endif

                    <form action="{{ route('storefront.book') }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Họ tên của bạn *</label>
                                <input type="text" name="customer_name" value="{{ old('customer_name') }}" required 
                                    placeholder="VD: Anh Minh" 
                                    class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 bg-slate-50" />
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Số điện thoại liên hệ *</label>
                                <input type="tel" name="customer_phone" value="{{ old('customer_phone') }}" required 
                                    placeholder="VD: 0912 345 678" 
                                    class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 bg-slate-50" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Tên dòng máy in *</label>
                                <input type="text" name="device_name" value="{{ old('device_name') }}" required 
                                    placeholder="VD: Canon LBP 2900, Brother 2321D..." 
                                    class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 bg-slate-50" />
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Model máy (nếu biết)</label>
                                <select name="printer_model_id" class="w-full text-xs sm:text-sm py-2.5 px-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 bg-slate-50 font-medium">
                                    <option value="">-- Chọn trong danh mục --</option>
                                    @foreach($printerModels as $pm)
                                    <option value="{{ $pm->id }}">{{ $pm->brand }} {{ $pm->model_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Hiện trạng lỗi máy in *</label>
                            <textarea name="issue_description" rows="3" required 
                                placeholder="VD: Máy bị kẹt giấy liên tục, bản in sọc đen, nạp mực mới, máy kêu to..." 
                                class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 bg-slate-50"></textarea>
                        </div>

                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-bold py-3.5 px-6 rounded-2xl shadow-md text-xs sm:text-sm flex items-center justify-center space-x-2 transition transform hover:-translate-y-0.5">
                            <span>GỬI YÊU CẦU TIẾP NHẬN MÁY IN</span>
                            <span>→</span>
                        </button>
                    </form>
                </div>

                <p class="text-[11px] text-slate-400 mt-4 text-center">
                    * Sau khi gửi, hệ thống sẽ cấp ngay một <b>Mã phiếu sửa</b> để bạn có thể tra cứu tiến độ online bất cứ lúc nào.
                </p>
            </div>

        </div>

    </main>

    <!-- 8. ENTERPRISE FOOTER (Responsive Multi-Column Desktop) -->
    <footer class="bg-slate-900 text-slate-400 text-xs mt-12 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">

                <!-- Col 1: Shop Info -->
                <div class="space-y-3">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-8 h-8 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-black">VP</div>
                        <span class="text-base font-black text-white">VPP & DỊCH VỤ MÁY IN</span>
                    </div>
                    <p class="text-slate-400 leading-relaxed text-xs">
                        Hệ thống bán lẻ văn phòng phẩm chính hãng kết hợp trung tâm bảo dưỡng, sửa chữa, thay thế linh kiện máy in văn phòng nhanh chóng, chuẩn xác.
                    </p>
                    <div class="pt-2 text-slate-300 text-xs space-y-1">
                        <p>📍 Địa chỉ: 123 Đường Văn Phòng Phẩm, P. Bến Nghé, Q.1, TP.HCM</p>
                        <p>📞 Hotline: <b class="text-amber-400">0901.234.567</b></p>
                        <p>💬 Zalo: <b class="text-sky-400">0901.234.567</b></p>
                    </div>
                    <div class="pt-1">
                        <a href="{{ route('storefront.about') }}" class="text-xs text-amber-400 hover:underline font-bold inline-flex items-center space-x-1">
                            <span>Xem giới thiệu & cơ sở vật chất</span>
                            <span>→</span>
                        </a>
                    </div>
                </div>

                <!-- Col 2: Categories -->
                <div>
                    <h4 class="text-white font-bold uppercase text-xs tracking-wider mb-3">Ngành Hàng Nổi Bật</h4>
                    <ul class="space-y-2 text-xs">
                        <li><a href="{{ url('/?category=giay-in-photo') }}" class="hover:text-white transition">Giấy In & Photo (Double A, PaperOne)</a></li>
                        <li><a href="{{ url('/?category=but-viet-muc-viet') }}" class="hover:text-white transition">Bút Bi, Bút Gel, Bút Lông Bảng Thiên Long</a></li>
                        <li><a href="{{ url('/?category=bia-file-ho-so') }}" class="hover:text-white transition">Bìa Còng Kokuyo, Bìa Lá, File Hồ Sơ</a></li>
                        <li><a href="{{ url('/?category=dung-cu-van-phong') }}" class="hover:text-white transition">Dụng Cụ Văn Phòng, Băng Keo, Bấm Kim Plus</a></li>
                        <li><a href="{{ url('/?category=hop-muc-may-in') }}" class="hover:text-white transition">Hộp Mực Máy In Cartridge 12A, TN-2385...</a></li>
                    </ul>
                </div>

                <!-- Col 3: Services & Printing -->
                <div>
                    <h4 class="text-white font-bold uppercase text-xs tracking-wider mb-3">Dịch Vụ Máy In</h4>
                    <ul class="space-y-2 text-xs">
                        <li><a href="#dat-lich" class="hover:text-white transition">Nạp Mực Máy In Laser Đen Trắng / Màu</a></li>
                        <li><a href="#dat-lich" class="hover:text-white transition">Thay Trống In (Drum), Gạt Mực, Trục Từ</a></li>
                        <li><a href="#dat-lich" class="hover:text-white transition">Xử Lý Lỗi Kẹt Giấy, Rách Bao Lụa, Hỏng Sấy</a></li>
                        <li><a href="{{ route('lookup.index') }}" class="hover:text-white transition font-bold text-amber-400">Tra Cứu Tiến Độ Phiếu Sửa Chữa Online</a></li>
                        <li><a href="{{ url('/admin') }}" class="hover:text-white transition">Cổng Quản Trị & Thu Ngân POS</a></li>
                    </ul>
                </div>

                <!-- Col 4: Policies & Legal -->
                <div>
                    <h4 class="text-white font-bold uppercase text-xs tracking-wider mb-3">Chính Sách & Khách Hàng</h4>
                    <ul class="space-y-2 text-xs">
                        <li>
                            <a href="{{ route('storefront.about') }}" class="hover:text-amber-300 transition flex items-center space-x-1.5">
                                <span>🏢</span>
                                <span>Giới Thiệu Về Cửa Hàng & Showroom</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('storefront.privacy') }}" class="hover:text-emerald-300 transition flex items-center space-x-1.5 font-bold text-emerald-400">
                                <span>🛡️</span>
                                <span>Chính Sách Bảo Mật Dữ Liệu Máy In</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('storefront.terms') }}" class="hover:text-amber-300 transition flex items-center space-x-1.5">
                                <span>📦</span>
                                <span>Chính Sách Mua Hàng & Giao Hỏa Tốc 2H</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('storefront.terms') }}" class="hover:text-amber-300 transition flex items-center space-x-1.5">
                                <span>🔄</span>
                                <span>Chính Sách Đồng Kiểm & Đổi Trả 7 Ngày</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('storefront.terms') }}" class="hover:text-amber-300 transition flex items-center space-x-1.5">
                                <span>🧾</span>
                                <span>Quy Định Thanh Toán & Hóa Đơn VAT</span>
                            </a>
                        </li>
                    </ul>
                    <div class="mt-3 p-3 bg-slate-800/80 rounded-2xl border border-slate-700 text-[11px] space-y-1">
                        <p class="text-slate-300 font-semibold">Tài khoản VietQR Napas 24/7:</p>
                        <p class="text-amber-300 font-mono font-bold">Techcombank: 190333888999</p>
                        <p class="text-slate-400">Chủ TK: NGUYEN VAN A</p>
                    </div>
                </div>

            </div>

            <!-- Bottom Copyright & Security note -->
            <div class="mt-10 pt-6 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between text-[11px] text-slate-500 gap-3">
                <p>© 2026 Cửa Hàng Văn Phòng Phẩm & Dịch Vụ Máy In. Nền tảng Laravel 13 & Filament PHP.</p>
                <div class="flex flex-wrap items-center gap-3 text-slate-400">
                    <a href="{{ route('storefront.about') }}" class="hover:text-white transition">Giới Thiệu</a>
                    <span>•</span>
                    <a href="{{ route('storefront.terms') }}" class="hover:text-white transition">Chính Sách Mua Hàng</a>
                    <span>•</span>
                    <a href="{{ route('storefront.privacy') }}" class="hover:text-white transition font-bold text-emerald-400">Bảo Mật Dữ Liệu</a>
                    <span>•</span>
                    <a href="{{ route('lookup.index') }}" class="hover:text-white transition text-amber-400">Tra Cứu Phiếu Sửa</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- 9. Mobile Bottom Navigation Bar (5 Items for Mobile Screen - md:hidden) -->
    <nav class="bg-white/95 backdrop-blur-md border-t border-slate-200 sticky bottom-0 z-40 md:hidden w-full shadow-lg">
        <div class="grid grid-cols-5 text-center text-[10px] py-2 font-medium">
            <a href="{{ route('storefront.index') }}" class="text-indigo-600 font-bold flex flex-col items-center">
                <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                <span>Trang chủ</span>
            </a>
            <a href="{{ route('storefront.products') }}" class="text-slate-500 hover:text-indigo-600 flex flex-col items-center">
                <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                <span>Sản phẩm</span>
            </a>
            <button type="button" onclick="openCartDrawer()" class="text-slate-500 hover:text-indigo-600 flex flex-col items-center relative">
                <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                <span>Giỏ hàng</span>
                <span id="mobile-cart-badge" class="absolute -top-1 right-2 px-1.5 py-0.2 bg-emerald-600 text-white rounded-full text-[9px] font-black hidden">0</span>
            </button>
            <a href="{{ route('storefront.wholesale') }}" class="text-slate-500 hover:text-indigo-600 flex flex-col items-center">
                <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                <span>Đại lý</span>
            </a>
            @if(auth('customer')->check())
            <a href="{{ route('customer.profile') }}" class="text-slate-500 hover:text-indigo-600 flex flex-col items-center">
                <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                <span>Tài khoản</span>
            </a>
            @else
            <a href="{{ route('customer.login') }}" class="text-slate-500 hover:text-indigo-600 flex flex-col items-center">
                <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                <span>Đăng nhập</span>
            </a>
            @endif
        </div>
    </nav>

    <!-- 10. Floating Pinned Hotline & Zalo (All Devices - Positioned on bottom-left) -->
    <div class="fixed bottom-20 sm:bottom-6 left-4 sm:left-6 z-40 flex flex-col space-y-2.5">
        <a href="https://zalo.me/0901234567" target="_blank" 
           class="w-12 h-12 sm:w-14 sm:h-14 bg-blue-600 hover:bg-blue-500 text-white rounded-full flex items-center justify-center shadow-xl font-black text-xs sm:text-sm transform hover:scale-110 transition duration-150 border-2 border-white">
            Zalo
        </a>
        <a href="tel:0901234567" 
           class="w-12 h-12 sm:w-14 sm:h-14 bg-red-600 hover:bg-red-500 text-white rounded-full flex items-center justify-center shadow-xl transform hover:scale-110 transition duration-150 animate-pulse border-2 border-white"
           title="Gọi ngay hotline 0901.234.567">
            <svg class="w-6 h-6 sm:w-7 sm:h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M6.62 10.79a15.053 15.053 0 006.59 6.59l2.2-2.2a1 1 0 011.01-.24c1.12.37 2.33.57 3.58.57a1 1 0 011 1V20a1 1 0 01-1 1A17 17 0 013 4a1 1 0 011-1h3.5a1 1 0 011 1c0 1.25.2 2.45.57 3.57a1 1 0 01-.25 1.02l-2.2 2.2z"></path></svg>
        </a>
    </div>

    <!-- 11. SLIDE-OVER CART DRAWER & ONLINE CHECKOUT -->
    <div id="cart-drawer-overlay" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden transition-opacity duration-300">
        <div class="fixed inset-y-0 right-0 max-w-md w-full bg-white shadow-2xl flex flex-col justify-between transform transition duration-300 ease-in-out">
            <!-- Drawer Header -->
            <div class="p-4 sm:p-5 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                <div class="flex items-center space-x-2">
                    <span class="text-xl">🛒</span>
                    <div>
                        <h3 class="font-black text-slate-900 text-base leading-tight">Giỏ Hàng Văn Phòng Phẩm</h3>
                        <span id="drawer-item-count" class="text-xs text-slate-500">0 sản phẩm</span>
                    </div>
                </div>
                <button type="button" onclick="closeCartDrawer()" class="p-2 text-slate-400 hover:text-slate-700 rounded-xl hover:bg-slate-200 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Drawer Items Container -->
            <div id="cart-items-container" class="flex-1 overflow-y-auto p-4 sm:p-5 space-y-3 divide-y divide-slate-100">
                <!-- Dynamically populated via JS -->
            </div>

            <!-- Drawer Footer: Subtotal & Guest Checkout Form -->
            <div id="cart-checkout-section" class="p-4 sm:p-5 border-t border-slate-200 bg-slate-50 space-y-4">
                <div class="space-y-1.5 text-xs">
                    <div class="flex justify-between text-slate-500">
                        <span>Tạm tính tiền hàng:</span>
                        <span id="drawer-subtotal" class="font-mono font-bold text-slate-800">0 ₫</span>
                    </div>
                    <div class="flex justify-between text-slate-500">
                        <span>Phí giao hàng:</span>
                        <span class="font-bold text-emerald-600">Miễn phí nội thành</span>
                    </div>
                    <div class="flex justify-between text-sm font-black text-slate-900 pt-2 border-t border-slate-200">
                        <span>Tổng Thanh Toán:</span>
                        <span id="drawer-grand-total" class="font-mono text-base text-indigo-700">0 ₫</span>
                    </div>
                </div>

                <!-- Checkout Form -->
                <form id="online-checkout-form" onsubmit="submitOnlineOrder(event)" class="space-y-2.5 text-xs">
                    <div class="text-[11px] font-bold text-slate-700 uppercase tracking-wide">
                        Thông Tin Nhận Hàng (Không Cần Đăng Nhập):
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="text" id="order-customer-name" required placeholder="Họ và tên..." class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 bg-white text-xs font-medium" />
                        <input type="tel" id="order-customer-phone" required placeholder="Số điện thoại..." class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 bg-white text-xs font-medium" />
                    </div>
                    <input type="text" id="order-customer-address" required placeholder="Địa chỉ giao hàng (Số nhà, đường, phường, quận)..." class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 bg-white text-xs font-medium" />
                    <input type="text" id="order-notes" placeholder="Ghi chú giao hàng (ví dụ: giao giờ hành chính)..." class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 bg-white text-xs font-medium" />

                    <!-- Payment Method Toggle -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Phương thức thanh toán:</label>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="flex items-center space-x-1.5 p-2 rounded-xl border border-slate-300 bg-white cursor-pointer hover:border-indigo-500">
                                <input type="radio" name="order_payment_method" value="cod" checked class="text-indigo-600 focus:ring-indigo-500" />
                                <span class="font-bold text-slate-800">💵 Trả khi nhận (COD)</span>
                            </label>
                            <label class="flex items-center space-x-1.5 p-2 rounded-xl border border-slate-300 bg-white cursor-pointer hover:border-indigo-500">
                                <input type="radio" name="order_payment_method" value="vietqr" class="text-indigo-600 focus:ring-indigo-500" />
                                <span class="font-bold text-indigo-700">📱 VietQR Tự động</span>
                            </label>
                        </div>
                    </div>

                    <div id="checkout-error-msg" class="hidden text-rose-600 font-bold text-[11px] p-2 bg-rose-50 rounded-lg border border-rose-200"></div>

                    <button type="submit" id="btn-submit-order" class="w-full py-3 px-4 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white rounded-xl font-black text-sm shadow-md transition flex items-center justify-center space-x-2">
                        <span>XÁC NHẬN ĐẶT HÀNG NGAY</span>
                        <span>→</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- 12. ORDER SUCCESS POP-UP MODAL (Dynamic VietQR) -->
    <div id="order-success-modal" class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 text-center space-y-4 shadow-2xl border border-slate-100">
            <div class="w-16 h-16 mx-auto rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-3xl font-black">
                ✓
            </div>
            <div>
                <h3 class="text-xl font-black text-slate-900">Đặt Hàng Thành Công!</h3>
                <p class="text-xs text-slate-500 mt-1">Cảm ơn quý khách. Đơn hàng của bạn đã được tiếp nhận vào hệ thống.</p>
            </div>

            <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200 text-left space-y-2 text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-500">Mã đơn hàng:</span>
                    <span id="success-order-code" class="font-mono font-bold text-slate-900 text-sm"></span>
                </div>
                <div class="flex justify-between border-t border-slate-200 pt-2 text-sm">
                    <span class="font-bold text-slate-700">Tổng thanh toán:</span>
                    <span id="success-grand-total" class="font-black font-mono text-emerald-600"></span>
                </div>
            </div>

            <!-- VietQR Container (Show if VietQR chosen) -->
            <div id="success-vietqr-container" class="hidden bg-slate-50 p-3 rounded-2xl border border-slate-200">
                <p class="text-xs font-bold text-slate-700 mb-2">Mở app Ngân hàng quét mã VietQR để thanh toán:</p>
                <img id="success-vietqr-img" src="" alt="Mã VietQR" class="w-44 h-44 mx-auto rounded-xl object-contain border border-slate-200 bg-white p-1" />
                <p class="text-[10px] text-slate-400 mt-1.5">Hệ thống sẽ tự động cập nhật trạng thái đã thanh toán sau 3-5 giây</p>
            </div>

            <div class="pt-2">
                <button type="button" onclick="closeSuccessModal()" class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition">
                    Tiếp Tục Mua Sắm
                </button>
            </div>
        </div>
    </div>

    <!-- 13. TOAST NOTIFICATION -->
    <div id="cart-toast" class="fixed bottom-20 left-1/2 transform -translate-x-1/2 bg-slate-900 text-white px-4 py-2.5 rounded-full shadow-2xl text-xs font-bold z-50 hidden flex items-center space-x-2 border border-slate-700 animate-bounce">
        <span>✓</span>
        <span id="cart-toast-msg">Đã thêm vào giỏ hàng!</span>
    </div>

    <!-- 14. CLIENT CART MANAGEMENT JAVASCRIPT (Zero-Dependency) -->
    <script>
        const CART_STORAGE_KEY = 'vpp_cart_items';

        function getCart() {
            try {
                return JSON.parse(localStorage.getItem(CART_STORAGE_KEY)) || [];
            } catch (e) {
                return [];
            }
        }

        function saveCart(cart) {
            localStorage.setItem(CART_STORAGE_KEY, JSON.stringify(cart));
            updateCartBadges();
            renderCart();
        }

        function updateCartBadges() {
            const cart = getCart();
            const totalQty = cart.reduce((sum, item) => sum + item.quantity, 0);

            const hBadge = document.getElementById('header-cart-badge');
            const mBadge = document.getElementById('mobile-cart-badge');
            const countLabel = document.getElementById('drawer-item-count');

            if (hBadge) {
                hBadge.textContent = totalQty;
                hBadge.classList.toggle('hidden', totalQty === 0);
            }
            if (mBadge) {
                mBadge.textContent = totalQty;
                mBadge.classList.toggle('hidden', totalQty === 0);
            }
            if (countLabel) {
                countLabel.textContent = totalQty + ' sản phẩm';
            }
        }

        function showToast(msg) {
            const toast = document.getElementById('cart-toast');
            const label = document.getElementById('cart-toast-msg');
            if (!toast || !label) return;
            label.textContent = msg;
            toast.classList.remove('hidden');
            setTimeout(() => {
                toast.classList.add('hidden');
            }, 2000);
        }

        function addToCart(productId, unitId, name, unitName, price, imageUrl) {
            const cart = getCart();
            const cartKey = productId + '_' + (unitId || 'base');
            const existingIndex = cart.findIndex(i => i.key === cartKey);

            if (existingIndex > -1) {
                cart[existingIndex].quantity += 1;
            } else {
                cart.push({
                    key: cartKey,
                    product_id: productId,
                    unit_id: unitId,
                    name: name,
                    unit_name: unitName,
                    price: price,
                    image_url: imageUrl,
                    quantity: 1,
                });
            }

            saveCart(cart);
            showToast('Đã thêm 1 ' + unitName + ' ' + name);
        }

        function updateCartQuantity(cartKey, delta) {
            let cart = getCart();
            const idx = cart.findIndex(i => i.key === cartKey);
            if (idx > -1) {
                cart[idx].quantity += delta;
                if (cart[idx].quantity <= 0) {
                    cart.splice(idx, 1);
                }
            }
            saveCart(cart);
        }

        function removeCartItem(cartKey) {
            let cart = getCart();
            cart = cart.filter(i => i.key !== cartKey);
            saveCart(cart);
        }

        function formatMoney(num) {
            return new Intl.NumberFormat('vi-VN').format(num) + ' ₫';
        }

        function renderCart() {
            const cart = getCart();
            const container = document.getElementById('cart-items-container');
            const checkoutSection = document.getElementById('cart-checkout-section');
            if (!container) return;

            if (cart.length === 0) {
                container.innerHTML = `
                    <div class="py-12 text-center text-slate-400">
                        <span class="text-4xl block mb-2">🛒</span>
                        <p class="font-bold text-slate-700 text-sm">Giỏ hàng của bạn đang trống</p>
                        <p class="text-xs text-slate-400 mt-1">Hãy nhấp vào "+ Thêm" ở danh sách sản phẩm để chọn mua.</p>
                    </div>
                `;
                if (checkoutSection) checkoutSection.classList.add('hidden');
                return;
            }

            if (checkoutSection) checkoutSection.classList.remove('hidden');

            let subtotal = 0;
            let html = '';

            cart.forEach(item => {
                const itemTotal = item.price * item.quantity;
                subtotal += itemTotal;

                html += `
                    <div class="pt-3 first:pt-0 flex items-start justify-between gap-3 text-xs">
                        <div class="w-12 h-12 rounded-xl bg-slate-50 border border-slate-100 flex-shrink-0 flex items-center justify-center p-1 overflow-hidden">
                            <img src="${item.image_url}" alt="${item.name}" class="max-h-full max-w-full object-contain" />
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold text-slate-900 leading-snug line-clamp-1">${item.name}</h4>
                            <div class="flex items-center space-x-2 text-[11px] text-slate-500 mt-0.5">
                                <span class="bg-indigo-50 text-indigo-700 font-bold px-1.5 py-0.2 rounded text-[10px]">${item.unit_name}</span>
                                <span class="font-mono">${formatMoney(item.price)}</span>
                            </div>
                        </div>
                        <div class="flex flex-col items-end space-y-1">
                            <span class="font-mono font-bold text-slate-900">${formatMoney(itemTotal)}</span>
                            <div class="flex items-center space-x-1.5 bg-slate-100 rounded-lg p-0.5">
                                <button type="button" onclick="updateCartQuantity('${item.key}', -1)" class="w-5 h-5 flex items-center justify-center bg-white rounded font-bold hover:bg-slate-200">-</button>
                                <span class="w-5 text-center font-bold font-mono">${item.quantity}</span>
                                <button type="button" onclick="updateCartQuantity('${item.key}', 1)" class="w-5 h-5 flex items-center justify-center bg-white rounded font-bold hover:bg-slate-200">+</button>
                            </div>
                            <button type="button" onclick="removeCartItem('${item.key}')" class="text-[10px] text-rose-500 hover:underline">Xóa</button>
                        </div>
                    </div>
                `;
            });

            container.innerHTML = html;
            const subtotalLabel = document.getElementById('drawer-subtotal');
            const grandTotalLabel = document.getElementById('drawer-grand-total');
            if (subtotalLabel) subtotalLabel.textContent = formatMoney(subtotal);
            if (grandTotalLabel) grandTotalLabel.textContent = formatMoney(subtotal);
        }

        function openCartDrawer() {
            renderCart();
            const overlay = document.getElementById('cart-drawer-overlay');
            if (overlay) overlay.classList.remove('hidden');
        }

        function closeCartDrawer() {
            const overlay = document.getElementById('cart-drawer-overlay');
            if (overlay) overlay.classList.add('hidden');
        }

        function closeSuccessModal() {
            const modal = document.getElementById('order-success-modal');
            if (modal) modal.classList.add('hidden');
        }

        async function submitOnlineOrder(e) {
            e.preventDefault();
            const cart = getCart();
            if (cart.length === 0) {
                alert('Giỏ hàng đang trống!');
                return;
            }

            const errorContainer = document.getElementById('checkout-error-msg');
            const submitBtn = document.getElementById('btn-submit-order');
            if (errorContainer) errorContainer.classList.add('hidden');

            const name = document.getElementById('order-customer-name').value.trim();
            const phone = document.getElementById('order-customer-phone').value.trim();
            const address = document.getElementById('order-customer-address').value.trim();
            const notes = document.getElementById('order-notes').value.trim();
            const paymentMethod = document.querySelector('input[name="order_payment_method"]:checked')?.value || 'cod';

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            submitBtn.disabled = true;
            submitBtn.innerHTML = `<span>Đang gửi đơn hàng...</span>`;

            try {
                const response = await fetch("{{ route('storefront.checkout') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({
                        customer_name: name,
                        customer_phone: phone,
                        customer_address: address,
                        payment_method: paymentMethod,
                        notes: notes,
                        items: cart.map(i => ({
                            product_id: i.product_id,
                            unit_id: i.unit_id,
                            quantity: i.quantity,
                        })),
                    }),
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Không thể tạo đơn hàng, vui lòng kiểm tra lại thông tin.');
                }

                // Order placed successfully!
                localStorage.removeItem(CART_STORAGE_KEY);
                updateCartBadges();
                closeCartDrawer();

                // Show Success Modal
                document.getElementById('success-order-code').textContent = data.order_code;
                document.getElementById('success-grand-total').textContent = data.grand_total_formatted;

                const qrBox = document.getElementById('success-vietqr-container');
                const qrImg = document.getElementById('success-vietqr-img');
                if (data.viet_qr_url && paymentMethod === 'vietqr') {
                    qrImg.src = data.viet_qr_url;
                    qrBox.classList.remove('hidden');
                } else {
                    qrBox.classList.add('hidden');
                }

                document.getElementById('order-success-modal').classList.remove('hidden');

            } catch (err) {
                if (errorContainer) {
                    errorContainer.textContent = err.message;
                    errorContainer.classList.remove('hidden');
                } else {
                    alert(err.message);
                }
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = `<span>XÁC NHẬN ĐẶT HÀNG NGAY</span><span>→</span>`;
            }
        }

        // Initialize cart badges on page load
        document.addEventListener('DOMContentLoaded', () => {
            updateCartBadges();
        });
    </script>

    <!-- 12. LIVE CHAT INTERACTIVE 24/7 SUPPORT WIDGET -->
    @include('storefront.components.livechat')

</body>
</html>
