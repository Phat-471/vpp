@extends('layouts.storefront')

@section('title', ($storefrontSettings['site_name'] ?? 'VPP & Thiết Bị Máy In') . ' - Tổng Kho Văn Phòng Phẩm & Dịch Vụ Máy In Tận Nơi Đồng Nai')
@section('meta_description', 'Tổng kho hơn 1.000 sản phẩm văn phòng phẩm chính hãng, giấy in photo Double A giá sỉ theo thùng, dịch vụ nạp mực & sửa chữa máy in tận nơi trong 30 phút tại Đồng Nai. Hóa đơn VAT đầy đủ.')

@section('schema_extra')
    <script type="application/ld+json">
        {
          "@@context": "https://schema.org",
          "@type": "FAQPage",
          "mainEntity": [
            {
              "@type": "Question",
              "name": "Bơm mực máy in tận nơi tại Đồng Nai giá bao nhiêu và mất bao lâu?",
              "acceptedAnswer": {
                "@type": "Answer",
                "text": "Dịch vụ nạp mực máy in tận nơi tại Đồng Nai có giá dao động từ 80.000đ đến 150.000đ tùy theo dòng máy in (Canon 2900, HP, Brother, Epson...). Kỹ thuật viên có mặt tận nơi trong vòng 30 - 45 phút, quy trình bao gồm hút sạch mực thải, vệ sinh linh kiện và in bản test sắc nét trước khi bàn giao."
              }
            },
            {
              "@type": "Question",
              "name": "Cửa hàng có giao văn phòng phẩm tận nơi miễn phí không?",
              "acceptedAnswer": {
                "@type": "Answer",
                "text": "Cửa hàng miễn phí giao hàng cho tất cả các đơn hàng từ 500.000đ tại khu vực Đồng Nai. Hỗ trợ giao hỏa tốc trong 2 giờ và giao tận bàn làm việc cho các văn phòng, trường học và khu công nghiệp."
              }
            },
            {
              "@type": "Question",
              "name": "Chính sách bảo hành sau khi sửa máy in và nạp mực như thế nào?",
              "acceptedAnswer": {
                "@type": "Answer",
                "text": "Cửa hàng cam kết bảo hành chất lượng bản in đến khi hết hạt mực cuối cùng. Các linh kiện thay thế như trống in (drum), gạt mực, trục từ, bao lụa được bảo hành 1 đổi 1 từ 1 đến 3 tháng."
              }
            },
            {
              "@type": "Question",
              "name": "Doanh nghiệp, trường học mua sỉ văn phòng phẩm có được chiết khấu và xuất hóa đơn VAT không?",
              "acceptedAnswer": {
                "@type": "Answer",
                "text": "Có, chúng tôi cung cấp mức chiết khấu từ 15% đến 25% cho khách hàng mua số lượng lớn, hỗ trợ công nợ 30 ngày và xuất hóa đơn điện tử VAT hợp lệ 100% theo quy định trong vòng 15 phút."
              }
            }
          ]
        }
    </script>
@endsection

@section('content')
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6 space-y-6 sm:space-y-8 w-full">

        <!-- 0. TICKER TIN KHUYẾN MÃI NÓNG HỔI (MEGA DEALS ANNOUNCEMENT) -->
        <div class="bg-gradient-to-r from-amber-500 via-rose-500 to-emerald-600 rounded-2xl p-0.5 shadow-sm overflow-hidden">
            <div class="bg-slate-900/95 backdrop-blur-md rounded-[14px] px-3 sm:px-4 py-2 flex items-center justify-between text-white text-xs">
                <div class="flex items-center space-x-2 shrink-0">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-400 text-slate-950 uppercase tracking-wider animate-pulse">
                        🔥 TIN NÓNG
                    </span>
                    <span class="font-bold text-amber-300 hidden sm:inline">Ưu Đãi Hôm Nay:</span>
                </div>
                <div class="overflow-hidden whitespace-nowrap w-full mx-3 text-[11px] sm:text-xs text-slate-200">
                    <div class="inline-flex items-center space-x-6 animate-marquee">
                        <span class="inline-flex items-center space-x-1.5">
                            <span>⚡</span>
                            <span><strong>Flash Sale Giảm Tới 35%:</strong> Hộp mực máy in Canon 2900 & Giấy in Double A đang có giá sốc.</span>
                        </span>
                        <span class="text-amber-400">•</span>
                        <span class="inline-flex items-center space-x-1.5">
                            <span>🚚</span>
                            <span><strong>Giao Hỏa Tốc 2 Giờ:</strong> Miễn phí giao hàng đơn từ 500.000đ tại Biên Hòa & Đồng Nai.</span>
                        </span>
                        <span class="text-amber-400">•</span>
                        <span class="inline-flex items-center space-x-1.5">
                            <span>🧾</span>
                            <span><strong>Hóa Đơn VAT Điện Tử:</strong> Xuất nhanh trong 15 phút, hỗ trợ công nợ 30 ngày cho doanh nghiệp.</span>
                        </span>
                        <span class="text-amber-400">•</span>
                        <span class="inline-flex items-center space-x-1.5">
                            <span>🛠️</span>
                            <span><strong>Thợ Nạp Mực 30 Phút:</strong> Có mặt tận nơi, bảo hành đậm đẹp đến giọt cuối cùng.</span>
                        </span>
                    </div>
                </div>
                <a href="{{ route('storefront.wholesale') }}" class="shrink-0 text-amber-300 hover:text-white font-bold text-[11px] sm:text-xs underline flex items-center space-x-1">
                    <span>Xem Thêm</span>
                    <span>→</span>
                </a>
            </div>
        </div>

        <!-- 1. HERO SECTION TỶ LỆ VÀNG 1 + 2 (1 BANNER LỚN CHÍNH + 2 CARD KHUYẾN MÃI NỔI BẬT) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 sm:gap-6 items-stretch">

            <!-- Main Banner Lớn (lg:col-span-8) -->
            <div class="lg:col-span-8 relative overflow-hidden rounded-3xl text-white p-6 sm:p-9 shadow-xl border border-slate-700/60 group flex flex-col justify-between min-h-[400px]">
                <!-- Background Image & Gradient Overlays -->
                <div class="absolute inset-0 z-0">
                    <img src="{{ asset('images/banners/stationery_store_bright.jpg') }}"
                        alt="Siêu Thị Văn Phòng Phẩm & Hộp Mực Máy In Đồng Nai"
                        width="800"
                        height="450"
                        fetchpriority="high"
                        decoding="async"
                        class="w-full h-full object-cover object-center transform group-hover:scale-105 transition duration-700 opacity-60" />
                    <div class="absolute inset-0 bg-gradient-to-r from-[#060c20]/95 via-[#0b173d]/90 to-[#0e2158]/65"></div>
                    <!-- Ambient Glow -->
                    <div class="absolute -top-20 -right-20 w-80 h-80 bg-emerald-500/20 rounded-full blur-3xl pointer-events-none"></div>
                    <div class="absolute -bottom-20 -left-20 w-80 h-80 bg-blue-500/20 rounded-full blur-3xl pointer-events-none"></div>
                </div>

                <!-- Content -->
                <div class="relative z-10 space-y-4 max-w-2xl">
                    <!-- Badges Row -->
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-black bg-amber-400 text-slate-950 shadow-xs uppercase tracking-wider">
                            ⭐ TỔNG KHO SỈ & LẺ CHÍNH HÃNG
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                            ⚡ Giao Siêu Tốc 2 Giờ
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white/10 text-slate-200 border border-white/10">
                            🧾 Hóa Đơn VAT Điện Tử
                        </span>
                    </div>

                    <!-- Main Headline -->
                    <h1 class="text-2xl sm:text-4xl lg:text-5xl font-black text-white leading-tight tracking-tight">
                        Văn Phòng Phẩm & Hộp Mực Máy In <br />
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 via-teal-200 to-amber-300">
                            Giá Sỉ Tận Gốc - Giao Siêu Tốc 2H
                        </span>
                    </h1>

                    <!-- Subtitle -->
                    <p class="text-xs sm:text-sm text-slate-300 leading-relaxed max-w-xl">
                        Hơn 1.000+ sản phẩm sẵn kho tại Đồng Nai: Giấy Double A, PaperOne, bút Thiên Long, mực Canon 2900, Brother. Chiết khấu tới 25% cho cơ quan, trường học & doanh nghiệp.
                    </p>

                    <!-- Action CTA Buttons -->
                    <div class="pt-2 flex flex-wrap gap-3 items-center">
                        <a href="#flash-sale-section"
                            class="px-6 py-3.5 rounded-2xl bg-gradient-to-r from-rose-500 to-orange-500 hover:from-rose-600 hover:to-orange-600 text-white font-extrabold text-xs sm:text-sm shadow-lg shadow-rose-900/30 transition transform hover:-translate-y-0.5 flex items-center space-x-2">
                            <span>⚡ Săn Flash Sale Giờ Vàng</span>
                            <span>→</span>
                        </a>
                        <a href="{{ route('storefront.wholesale') }}"
                            class="px-5 py-3.5 rounded-2xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs sm:text-sm border border-white/20 backdrop-blur-xs transition flex items-center space-x-1.5">
                            <span>⭐ Bảng Báo Giá Sỉ B2B</span>
                        </a>
                    </div>
                </div>

                <!-- Trust Guarantee & Live Counter -->
                <div class="relative z-10 pt-4 mt-4 border-t border-slate-700/60 flex flex-wrap items-center justify-between gap-4 text-[11px] text-slate-300 font-semibold">
                    <div class="flex flex-wrap items-center gap-4">
                        <span class="flex items-center space-x-1 text-emerald-400">
                            <span>✓</span> <span>100% Chính hãng</span>
                        </span>
                        <span class="flex items-center space-x-1 text-emerald-400">
                            <span>✓</span> <span>Đổi mới 7 ngày</span>
                        </span>
                        <span class="flex items-center space-x-1 text-emerald-400">
                            <span>✓</span> <span>Công nợ 30 ngày</span>
                        </span>
                        <span class="flex items-center space-x-1 text-emerald-400">
                            <span>✓</span> <span>Thợ có mặt 30 phút</span>
                        </span>
                    </div>
                    <div class="text-slate-400 text-[10px]">
                        Hơn <strong class="text-white">{{ number_format($stats['served_tickets'] ?? 350) }}+</strong> thiết bị đã được cứu hộ
                    </div>
                </div>
            </div>

            <!-- 2 Side Promo Cards (lg:col-span-4) -->
            <div class="lg:col-span-4 flex flex-col sm:flex-row lg:flex-col gap-4 justify-between">

                <!-- Side Card 1: Giấy in photo giá sỉ theo thùng -->
                <div class="flex-1 bg-gradient-to-br from-emerald-950/70 via-slate-900 to-slate-900 rounded-3xl p-5 border border-emerald-500/30 shadow-lg relative overflow-hidden group flex flex-col justify-between">
                    <div class="space-y-2 relative z-10">
                        <div class="flex items-center justify-between">
                            <span class="px-2.5 py-0.5 bg-emerald-500 text-white font-black text-[10px] rounded-full uppercase tracking-wider">
                                🔥 Bán Chạy Nhất
                            </span>
                            <span class="text-emerald-300 font-bold text-xs">Tiết kiệm sỉ tới 25%</span>
                        </div>
                        <h3 class="text-base font-black text-white group-hover:text-emerald-300 transition">
                            Giấy In Photo Giá Sỉ Theo Thùng
                        </h3>
                        <p class="text-xs text-slate-300 leading-snug">
                            Double A, PaperOne, IK Plus 70/80gsm trắng mịn không kẹt giấy. Đóng gói 5 Ram/Thùng nguyên đai nguyên kiện.
                        </p>
                    </div>

                    <div class="pt-4 flex items-center justify-between relative z-10 border-t border-slate-800 mt-3">
                        <div>
                            <span class="text-[10px] text-slate-400 block">Giá từ</span>
                            <span class="text-sm font-black text-emerald-400 font-mono">68.000₫ / Ram</span>
                        </div>
                        <a href="{{ route('storefront.products', ['category' => 'giay-in-photo']) }}"
                            class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl text-xs font-bold transition flex items-center space-x-1 shadow-sm">
                            <span>Xem Giá Sỉ</span>
                            <span>→</span>
                        </a>
                    </div>
                </div>

                <!-- Side Card 2: Dịch vụ máy in 30P -->
                <div class="flex-1 bg-gradient-to-br from-blue-950/70 via-slate-900 to-slate-900 rounded-3xl p-5 border border-blue-500/30 shadow-lg relative overflow-hidden group flex flex-col justify-between">
                    <div class="space-y-2 relative z-10">
                        <div class="flex items-center justify-between">
                            <span class="px-2.5 py-0.5 bg-amber-400 text-slate-950 font-black text-[10px] rounded-full uppercase tracking-wider">
                                ⚡ Thợ Tận Nơi 30P
                            </span>
                            <span class="text-amber-300 font-bold text-xs">Cứu hộ khẩn cấp</span>
                        </div>
                        <h3 class="text-base font-black text-white group-hover:text-amber-300 transition">
                            Nạp Mực & Cứu Hộ Máy In 24/7
                        </h3>
                        <p class="text-xs text-slate-300 leading-snug">
                            Canon 2900, HP, Brother tận nơi tại Đồng Nai. Mực siêu mịn, hút sạch mực thải, bảo hành đến giọt cuối cùng.
                        </p>
                    </div>

                    <div class="pt-4 flex items-center justify-between relative z-10 border-t border-slate-800 mt-3">
                        <div>
                            <span class="text-[10px] text-slate-400 block">Trọn gói chỉ từ</span>
                            <span class="text-sm font-black text-amber-400 font-mono">80.000₫ / Lần</span>
                        </div>
                        <a href="#dich-vu-may-in"
                            class="px-4 py-2 bg-[#1e3a8a] hover:bg-blue-800 text-white rounded-xl text-xs font-bold transition flex items-center space-x-1 shadow-sm">
                            <span>Gọi Thợ Ngay</span>
                            <span>→</span>
                        </a>
                    </div>
                </div>

            </div>
        </div>

        <!-- 2. LƯỚI 10 DANH MỤC NGÀNH HÀNG ĐA TẦNG CHUẨN SIÊU THỊ E-COMMERCE -->
        <section aria-label="Danh mục ngành hàng nổi bật" class="bg-white rounded-3xl p-4 sm:p-6 border border-slate-200/90 shadow-xs space-y-4">
            <div class="flex items-center justify-between px-1">
                <div class="flex items-center space-x-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></span>
                    <h2 class="text-xs sm:text-sm font-black text-slate-900 uppercase tracking-wider">
                        Danh Mục Ngành Hàng Đa Tầng
                    </h2>
                    <span class="hidden md:inline text-[11px] text-slate-500 font-medium">• Chọn nhanh ngành hàng cần mua</span>
                </div>
                <a href="{{ route('storefront.products') }}"
                    class="text-[11px] sm:text-xs font-bold text-emerald-700 hover:text-emerald-800 hover:underline flex items-center space-x-1">
                    <span>Xem tất cả danh mục</span>
                    <span>→</span>
                </a>
            </div>

            <!-- Grid 10 Danh Mục (5 Cột trên Desktop, 2 Hàng cuộn chạm nhẹ trên Mobile) -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3 sm:gap-4">

                <!-- 1: Giấy in photo -->
                <a href="{{ route('storefront.products', ['category' => 'giay-in-photo']) }}"
                    class="group relative flex flex-col items-center text-center p-3 sm:p-4 rounded-2xl bg-slate-50 hover:bg-emerald-50/70 border border-slate-200/70 hover:border-emerald-300 transition duration-200 transform hover:-translate-y-1 hover:shadow-md">
                    <span class="absolute top-2 right-2 text-[9px] font-black px-1.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800">SỈ</span>
                    <span class="w-12 h-12 rounded-2xl bg-white shadow-xs border border-slate-100 flex items-center justify-center text-2xl group-hover:scale-110 transition mb-2">📄</span>
                    <span class="text-xs font-black text-slate-900 group-hover:text-emerald-800 line-clamp-1">Giấy In Photo</span>
                    <span class="text-[10px] text-slate-500 line-clamp-1">Double A, PaperOne</span>
                </a>

                <!-- 2: Hộp mực máy in -->
                <a href="{{ route('storefront.products', ['category' => 'hop-muc-may-in']) }}"
                    class="group relative flex flex-col items-center text-center p-3 sm:p-4 rounded-2xl bg-slate-50 hover:bg-blue-50/70 border border-slate-200/70 hover:border-blue-300 transition duration-200 transform hover:-translate-y-1 hover:shadow-md">
                    <span class="absolute top-2 right-2 text-[9px] font-black px-1.5 py-0.5 rounded-full bg-rose-100 text-rose-800">HOT</span>
                    <span class="w-12 h-12 rounded-2xl bg-white shadow-xs border border-slate-100 flex items-center justify-center text-2xl group-hover:scale-110 transition mb-2">🖨️</span>
                    <span class="text-xs font-black text-slate-900 group-hover:text-blue-800 line-clamp-1">Hộp Mực Máy In</span>
                    <span class="text-[10px] text-slate-500 line-clamp-1">Canon, HP, Brother</span>
                </a>

                <!-- 3: Bút viết & bút bi -->
                <a href="{{ route('storefront.products', ['category' => 'but-viet-muc-viet']) }}"
                    class="group relative flex flex-col items-center text-center p-3 sm:p-4 rounded-2xl bg-slate-50 hover:bg-amber-50/70 border border-slate-200/70 hover:border-amber-300 transition duration-200 transform hover:-translate-y-1 hover:shadow-md">
                    <span class="absolute top-2 right-2 text-[9px] font-black px-1.5 py-0.5 rounded-full bg-amber-100 text-amber-800">SỈ</span>
                    <span class="w-12 h-12 rounded-2xl bg-white shadow-xs border border-slate-100 flex items-center justify-center text-2xl group-hover:scale-110 transition mb-2">🖊️</span>
                    <span class="text-xs font-black text-slate-900 group-hover:text-amber-900 line-clamp-1">Bút Viết & Bút Bi</span>
                    <span class="text-[10px] text-slate-500 line-clamp-1">Thiên Long, Bút Ký</span>
                </a>

                <!-- 4: Bìa còng & file hồ sơ -->
                <a href="{{ route('storefront.products', ['category' => 'bia-file-ho-so']) }}"
                    class="group relative flex flex-col items-center text-center p-3 sm:p-4 rounded-2xl bg-slate-50 hover:bg-purple-50/70 border border-slate-200/70 hover:border-purple-300 transition duration-200 transform hover:-translate-y-1 hover:shadow-md">
                    <span class="w-12 h-12 rounded-2xl bg-white shadow-xs border border-slate-100 flex items-center justify-center text-2xl group-hover:scale-110 transition mb-2">📁</span>
                    <span class="text-xs font-black text-slate-900 group-hover:text-purple-900 line-clamp-1">Bìa Còng & File</span>
                    <span class="text-[10px] text-slate-500 line-clamp-1">Kokuyo, Bìa Còng 7F</span>
                </a>

                <!-- 5: Dụng cụ văn phòng -->
                <a href="{{ route('storefront.products', ['category' => 'dung-cu-van-phong']) }}"
                    class="group relative flex flex-col items-center text-center p-3 sm:p-4 rounded-2xl bg-slate-50 hover:bg-rose-50/70 border border-slate-200/70 hover:border-rose-300 transition duration-200 transform hover:-translate-y-1 hover:shadow-md">
                    <span class="w-12 h-12 rounded-2xl bg-white shadow-xs border border-slate-100 flex items-center justify-center text-2xl group-hover:scale-110 transition mb-2">✂️</span>
                    <span class="text-xs font-black text-slate-900 group-hover:text-rose-900 line-clamp-1">Dụng Cụ Văn Phòng</span>
                    <span class="text-[10px] text-slate-500 line-clamp-1">Bấm kim, kéo, dao</span>
                </a>

                <!-- 6: Băng keo & đóng gói -->
                <a href="{{ route('storefront.products', ['category' => 'dung-cu-van-phong']) }}"
                    class="group relative flex flex-col items-center text-center p-3 sm:p-4 rounded-2xl bg-slate-50 hover:bg-orange-50/70 border border-slate-200/70 hover:border-orange-300 transition duration-200 transform hover:-translate-y-1 hover:shadow-md">
                    <span class="w-12 h-12 rounded-2xl bg-white shadow-xs border border-slate-100 flex items-center justify-center text-2xl group-hover:scale-110 transition mb-2">📦</span>
                    <span class="text-xs font-black text-slate-900 group-hover:text-orange-900 line-clamp-1">Băng Keo & Đóng Gói</span>
                    <span class="text-[10px] text-slate-500 line-clamp-1">Băng keo OPP, PE</span>
                </a>

                <!-- 7: Trống drum & linh kiện -->
                <a href="{{ route('storefront.products', ['category' => 'linh-kien-may-in']) }}"
                    class="group relative flex flex-col items-center text-center p-3 sm:p-4 rounded-2xl bg-slate-50 hover:bg-sky-50/70 border border-slate-200/70 hover:border-sky-300 transition duration-200 transform hover:-translate-y-1 hover:shadow-md">
                    <span class="w-12 h-12 rounded-2xl bg-white shadow-xs border border-slate-100 flex items-center justify-center text-2xl group-hover:scale-110 transition mb-2">⚙️</span>
                    <span class="text-xs font-black text-slate-900 group-hover:text-sky-900 line-clamp-1">Trống Drum Linh Kiện</span>
                    <span class="text-[10px] text-slate-500 line-clamp-1">Gạt từ, trục sấy</span>
                </a>

                <!-- 8: Thợ sửa máy in 30P -->
                <a href="#dich-vu-may-in"
                    class="group relative flex flex-col items-center text-center p-3 sm:p-4 rounded-2xl bg-slate-50 hover:bg-teal-50/70 border border-slate-200/70 hover:border-teal-300 transition duration-200 transform hover:-translate-y-1 hover:shadow-md">
                    <span class="absolute top-2 right-2 text-[9px] font-black px-1.5 py-0.5 rounded-full bg-teal-100 text-teal-800">30P</span>
                    <span class="w-12 h-12 rounded-2xl bg-white shadow-xs border border-slate-100 flex items-center justify-center text-2xl group-hover:scale-110 transition mb-2">🛠️</span>
                    <span class="text-xs font-black text-slate-900 group-hover:text-teal-900 line-clamp-1">Sửa Máy In Tận Nơi</span>
                    <span class="text-[10px] text-slate-500 line-clamp-1">Nạp mực, sửa kẹt</span>
                </a>

                <!-- 9: Tra cứu đơn & phiếu sửa -->
                <a href="{{ route('lookup.index') }}"
                    class="group relative flex flex-col items-center text-center p-3 sm:p-4 rounded-2xl bg-slate-50 hover:bg-indigo-50/70 border border-slate-200/70 hover:border-indigo-300 transition duration-200 transform hover:-translate-y-1 hover:shadow-md">
                    <span class="w-12 h-12 rounded-2xl bg-white shadow-xs border border-slate-100 flex items-center justify-center text-2xl group-hover:scale-110 transition mb-2">🔍</span>
                    <span class="text-xs font-black text-slate-900 group-hover:text-indigo-900 line-clamp-1">Tra Cứu Tiến Độ</span>
                    <span class="text-[10px] text-slate-500 line-clamp-1">Nhập mã & 4 số cuối</span>
                </a>

                <!-- 10: Báo giá sỉ B2B -->
                <a href="{{ route('storefront.wholesale') }}"
                    class="group relative flex flex-col items-center text-center p-3 sm:p-4 rounded-2xl bg-gradient-to-br from-amber-400 to-amber-500 text-slate-950 border border-amber-600 shadow-sm transition duration-200 transform hover:-translate-y-1 hover:shadow-lg">
                    <span class="absolute top-2 right-2 text-[9px] font-black px-1.5 py-0.5 rounded-full bg-slate-950 text-amber-300">-25%</span>
                    <span class="w-12 h-12 rounded-2xl bg-white shadow-xs flex items-center justify-center text-2xl group-hover:scale-110 transition mb-2">⭐</span>
                    <span class="text-xs font-black text-slate-950 line-clamp-1">Báo Giá Sỉ Doanh Nghiệp</span>
                    <span class="text-[10px] text-slate-800 line-clamp-1">Chiết khấu tới 25%</span>
                </a>

            </div>
        </section>

        <!-- 3. BỐN CAM KẾT VÀNG CỬA HÀNG -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
            <div class="bg-white rounded-2xl p-4 shadow-2xs border border-slate-200/90 flex items-center space-x-3.5 hover:border-emerald-500 hover:shadow-md transition">
                <div class="w-11 h-11 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-xl shrink-0">
                    ⚡
                </div>
                <div>
                    <h3 class="text-xs sm:text-sm font-black text-slate-900">Giao Siêu Tốc 2H</h3>
                    <p class="text-[11px] text-slate-500 leading-tight mt-0.5">Khu vực Đồng Nai nhận trong 120 phút</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-4 shadow-2xs border border-slate-200/90 flex items-center space-x-3.5 hover:border-emerald-500 hover:shadow-md transition">
                <div class="w-11 h-11 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center text-xl shrink-0">
                    🛡️
                </div>
                <div>
                    <h3 class="text-xs sm:text-sm font-black text-slate-900">100% Chính Hãng</h3>
                    <p class="text-[11px] text-slate-500 leading-tight mt-0.5">Đền bù 200% nếu phát hiện hàng giả</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-4 shadow-2xs border border-slate-200/90 flex items-center space-x-3.5 hover:border-emerald-500 hover:shadow-md transition">
                <div class="w-11 h-11 rounded-2xl bg-amber-100 text-amber-800 flex items-center justify-center text-xl shrink-0">
                    💰
                </div>
                <div>
                    <h3 class="text-xs sm:text-sm font-black text-slate-900">Giá Sỉ Tận Kho</h3>
                    <p class="text-[11px] text-slate-500 leading-tight mt-0.5">Chiết khấu tới 25% cho doanh nghiệp</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-4 shadow-2xs border border-slate-200/90 flex items-center space-x-3.5 hover:border-emerald-500 hover:shadow-md transition">
                <div class="w-11 h-11 rounded-2xl bg-rose-100 text-rose-700 flex items-center justify-center text-xl shrink-0">
                    🔄
                </div>
                <div>
                    <h3 class="text-xs sm:text-sm font-black text-slate-900">Bảo Hành 1 Đổi 1</h3>
                    <p class="text-[11px] text-slate-500 leading-tight mt-0.5">Đổi mới hộp mực và linh kiện bị lỗi</p>
                </div>
            </div>
        </div>

        @if(!empty($isFiltering))
            <!-- KẾT QUẢ TÌM KIẾM / LỌC SẢN PHẨM -->
            <section class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h2 class="text-base sm:text-xl font-black text-slate-900">
                            Kết Quả Tìm Kiếm
                            @if(!empty($searchKeyword)) cho từ khóa: <span class="text-emerald-700">"{{ $searchKeyword }}"</span> @endif
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Tìm thấy {{ $products->count() }} sản phẩm phù hợp</p>
                    </div>
                    <a href="{{ route('storefront.index') }}" class="text-xs font-bold text-rose-600 hover:underline">Xóa bộ lọc ✕</a>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @forelse($products as $p)
                        @include('storefront.components.product-card', ['p' => $p])
                    @empty
                        <div class="col-span-full text-center py-12">
                            <span class="text-4xl block mb-2">🔍</span>
                            <h4 class="text-base font-bold text-slate-700">Không tìm thấy sản phẩm phù hợp</h4>
                            <p class="text-xs text-slate-400 mt-1">Quý khách vui lòng thử tìm với từ khóa khác.</p>
                            <a href="{{ route('storefront.products') }}"
                                class="inline-block mt-3 px-5 py-2.5 bg-emerald-600 text-white rounded-xl text-xs font-bold shadow">
                                Xem tất cả sản phẩm
                            </a>
                        </div>
                    @endforelse
                </div>
            </section>
        @endif

        <!-- 4. ⚡ FLASH SALE GIỜ VÀNG (ĐẾM NGƯỢC THỜI GIAN THỰC & SLIDER CỐ ĐỊNH 16 SẢN PHẨM) -->
        <section id="flash-sale-section" aria-label="Flash Sale Khuyến Mãi" class="space-y-4">
            <!-- Flash Sale Header với Countdown Timer & Nút điều khiển Slider -->
            <div class="bg-gradient-to-r from-rose-600 via-rose-500 to-amber-500 rounded-3xl p-4 sm:p-6 shadow-xl relative overflow-hidden">
                <!-- Ambient glow -->
                <div class="absolute -top-10 -right-10 w-44 h-44 bg-yellow-400/20 rounded-full blur-2xl pointer-events-none"></div>
                <div class="absolute -bottom-10 -left-10 w-44 h-44 bg-rose-700/30 rounded-full blur-2xl pointer-events-none"></div>

                <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <!-- Left: Title + Badges -->
                    <div class="space-y-1.5">
                        <div class="flex items-center space-x-3">
                            <span class="text-3xl animate-bounce">⚡</span>
                            <h2 class="text-xl sm:text-3xl font-black text-white tracking-tight leading-none uppercase">
                                FLASH SALE GIÁ SỐC
                            </h2>
                            <span class="hidden sm:inline-flex items-center px-3 py-1 rounded-full text-[11px] font-black bg-yellow-400 text-slate-950 shadow-sm animate-pulse uppercase tracking-wider">
                                🔥 Giảm tới 35%
                            </span>
                        </div>
                        <p class="text-xs text-rose-100 hidden sm:block">Số lượng có hạn trong ngày • Hết hàng hệ thống tự ngưng giá sốc</p>
                    </div>

                    <!-- Right: Countdown Timer & Nút trượt slider -->
                    <div class="flex items-center justify-between sm:justify-end gap-3 flex-wrap">
                        <!-- Countdown Timer -->
                        <div class="flex items-center space-x-2 bg-black/30 backdrop-blur-md px-3.5 py-2 rounded-2xl border border-white/20">
                            <span class="text-[11px] text-rose-100 font-black uppercase tracking-wider hidden sm:inline">Kết thúc trong:</span>
                            <div id="flashSaleCountdown" class="flex items-center space-x-1">
                                <div class="bg-white/20 rounded-lg px-2 py-1 min-w-[34px] text-center">
                                    <span id="countdown-hours"
                                        class="text-sm sm:text-base font-black text-white font-mono block leading-none">00</span>
                                    <span class="text-[8px] text-rose-100 font-semibold uppercase">Giờ</span>
                                </div>
                                <span class="text-white font-black text-sm">:</span>
                                <div class="bg-white/20 rounded-lg px-2 py-1 min-w-[34px] text-center">
                                    <span id="countdown-minutes"
                                        class="text-sm sm:text-base font-black text-white font-mono block leading-none">00</span>
                                    <span class="text-[8px] text-rose-100 font-semibold uppercase">Phút</span>
                                </div>
                                <span class="text-white font-black text-sm">:</span>
                                <div class="bg-white/20 rounded-lg px-2 py-1 min-w-[34px] text-center">
                                    <span id="countdown-seconds"
                                        class="text-sm sm:text-base font-black text-white font-mono block leading-none">00</span>
                                    <span class="text-[8px] text-rose-100 font-semibold uppercase">Giây</span>
                                </div>
                            </div>
                        </div>

                        <!-- Slider Arrow Controls (Prev / Next) -->
                        <div class="flex items-center space-x-2">
                            <button type="button" onclick="slideFlashSale(-1)" aria-label="Xem sản phẩm trước"
                                class="w-10 h-10 rounded-2xl bg-white/20 hover:bg-white text-white hover:text-slate-900 border border-white/25 flex items-center justify-center text-lg font-black transition duration-200 active:scale-95 shadow-sm"
                                title="Trượt sang trái">
                                ❮
                            </button>
                            <button type="button" onclick="slideFlashSale(1)" aria-label="Xem sản phẩm tiếp theo"
                                class="w-10 h-10 rounded-2xl bg-white/20 hover:bg-white text-white hover:text-slate-900 border border-white/25 flex items-center justify-center text-lg font-black transition duration-200 active:scale-95 shadow-sm"
                                title="Trượt sang phải">
                                ❯
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Flash Sale Slider Track (Cố định 16 sản phẩm dạng slide trượt ngang) -->
            <div class="relative">
                <div id="flashSaleTrack"
                    class="flex gap-4 overflow-x-auto scroll-smooth no-scrollbar pb-3 pt-1 snap-x snap-mandatory">
                    @foreach($flashSaleProducts as $p)
                        @include('storefront.components.product-card', [
                            'p' => $p,
                            'isSlide' => true,
                            'topBadge' => '⚡ Flash Deal',
                            'topBadgeClass' => 'bg-gradient-to-r from-rose-600 to-orange-500 text-white',
                            'showProgress' => true,
                        ])
                    @endforeach
                </div>
            </div>
        </section>

        <!-- 5. 🏷️ BẢNG GIÁ SỈ THEO THÙNG & COMBO DOANH NGHIỆP (B2B BULK PRICING MATRIX) -->
        <section aria-label="Bảng giá sỉ văn phòng phẩm theo thùng" class="bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-5 sm:p-8 text-white border border-slate-800 shadow-xl space-y-6">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 border-b border-slate-800 pb-4">
                <div class="space-y-1.5">
                    <div class="flex items-center space-x-2">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-amber-400 text-slate-950 uppercase tracking-wider">
                            ⭐ TIẾT KIỆM TỐI ĐA CHO DOANH NGHIỆP
                        </span>
                        <span class="text-xs text-emerald-400 font-bold hidden sm:inline">Hỗ trợ công nợ 30 ngày</span>
                    </div>
                    <h2 class="text-xl sm:text-3xl font-black text-white tracking-tight">
                        Bảng Báo Giá Sỉ Theo Thùng & Combo Văn Phòng
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-300 max-w-2xl">
                        Giá áp dụng trực tiếp cho đơn vị hành chính sự nghiệp, trường học và công ty tại Đồng Nai. Xuất hóa đơn VAT điện tử đầy đủ trong 15 phút.
                    </p>
                </div>
                <div class="flex items-center space-x-3 shrink-0">
                    <a href="{{ route('storefront.wholesale') }}"
                        class="px-5 py-2.5 bg-amber-400 hover:bg-amber-300 text-slate-950 font-black text-xs sm:text-sm rounded-xl transition shadow">
                        Tải Bảng Giá Excel/PDF →
                    </a>
                </div>
            </div>

            <!-- Bảng Ma Trận Giá Sỉ Minh Bạch -->
            <div class="overflow-x-auto no-scrollbar">
                <table class="w-full text-left text-xs border-collapse min-w-[640px]">
                    <thead>
                        <tr class="border-b border-slate-800 text-[11px] uppercase tracking-wider text-slate-400">
                            <th class="py-3 px-3">Sản Phẩm Chủ Lực</th>
                            <th class="py-3 px-3">Quy Cách Đóng Gói</th>
                            <th class="py-3 px-3">Giá Mua Lẻ</th>
                            <th class="py-3 px-3 text-emerald-400 font-black">Giá Sỉ Thùng (Từ 5 Thùng)</th>
                            <th class="py-3 px-3 text-amber-300 font-black">Ưu Đãi Kèm Theo</th>
                            <th class="py-3 px-3 text-right">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        <!-- Hàng 1: Giấy Double A -->
                        <tr class="hover:bg-white/5 transition">
                            <td class="py-3.5 px-3">
                                <div class="font-bold text-white text-sm">Giấy In Double A A4 70gsm</div>
                                <span class="text-[10px] text-slate-400">Độ trắng 148 CIE, láng mịn không kẹt</span>
                            </td>
                            <td class="py-3.5 px-3 text-slate-300">5 Ram / Thùng (500 tờ/Ram)</td>
                            <td class="py-3.5 px-3 text-slate-400 font-mono line-through">75.000₫ / Ram</td>
                            <td class="py-3.5 px-3">
                                <span class="text-sm font-black text-emerald-400 font-mono">345.000₫ / Thùng</span>
                                <span class="block text-[10px] text-emerald-300 font-semibold">(Chỉ 69.000₫/Ram)</span>
                            </td>
                            <td class="py-3.5 px-3">
                                <span class="inline-block px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-300 text-[10px] font-bold border border-emerald-500/30">
                                    🚚 Miễn phí giao tận bàn
                                </span>
                            </td>
                            <td class="py-3.5 px-3 text-right">
                                <a href="{{ route('storefront.products', ['category' => 'giay-in-photo']) }}" class="px-3 py-1.5 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-[11px] transition">
                                    Đặt Thùng
                                </a>
                            </td>
                        </tr>

                        <!-- Hàng 2: Giấy PaperOne -->
                        <tr class="hover:bg-white/5 transition">
                            <td class="py-3.5 px-3">
                                <div class="font-bold text-white text-sm">Giấy In PaperOne A4 70gsm / 80gsm</div>
                                <span class="text-[10px] text-slate-400">Công nghệ ProDigi bảo vệ đầu phun máy in</span>
                            </td>
                            <td class="py-3.5 px-3 text-slate-300">5 Ram / Thùng</td>
                            <td class="py-3.5 px-3 text-slate-400 font-mono line-through">72.000₫ / Ram</td>
                            <td class="py-3.5 px-3">
                                <span class="text-sm font-black text-emerald-400 font-mono">330.000₫ / Thùng</span>
                                <span class="block text-[10px] text-emerald-300 font-semibold">(Chỉ 66.000₫/Ram)</span>
                            </td>
                            <td class="py-3.5 px-3">
                                <span class="inline-block px-2 py-0.5 rounded bg-blue-500/20 text-blue-300 text-[10px] font-bold border border-blue-500/30">
                                    🎁 Tặng 1 bút dạ quang
                                </span>
                            </td>
                            <td class="py-3.5 px-3 text-right">
                                <a href="{{ route('storefront.products', ['category' => 'giay-in-photo']) }}" class="px-3 py-1.5 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-[11px] transition">
                                    Đặt Thùng
                                </a>
                            </td>
                        </tr>

                        <!-- Hàng 3: Hộp mực Canon 2900 / HP 12A -->
                        <tr class="hover:bg-white/5 transition">
                            <td class="py-3.5 px-3">
                                <div class="font-bold text-white text-sm">Hộp Mực Máy In Canon 2900 / HP 12A</div>
                                <span class="text-[10px] text-slate-400">Mới 100%, in 2.000 trang sắc nét</span>
                            </td>
                            <td class="py-3.5 px-3 text-slate-300">Hộp nguyên seal có lỗ nạp</td>
                            <td class="py-3.5 px-3 text-slate-400 font-mono line-through">280.000₫ / Hộp</td>
                            <td class="py-3.5 px-3">
                                <span class="text-sm font-black text-emerald-400 font-mono">220.000₫ / Hộp</span>
                                <span class="block text-[10px] text-emerald-300 font-semibold">(Từ 3 hộp: 210.000₫)</span>
                            </td>
                            <td class="py-3.5 px-3">
                                <span class="inline-block px-2 py-0.5 rounded bg-amber-500/20 text-amber-300 text-[10px] font-bold border border-amber-500/30">
                                    🛡️ Bảo hành 1 đổi 1 trong 12T
                                </span>
                            </td>
                            <td class="py-3.5 px-3 text-right">
                                <a href="{{ route('storefront.products', ['category' => 'hop-muc-may-in']) }}" class="px-3 py-1.5 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-[11px] transition">
                                    Đặt Mực
                                </a>
                            </td>
                        </tr>

                        <!-- Hàng 4: Bút bi Thiên Long TL-027 -->
                        <tr class="hover:bg-white/5 transition">
                            <td class="py-3.5 px-3">
                                <div class="font-bold text-white text-sm">Bút Bi Thiên Long TL-027 (Xanh/Đen/Đỏ)</div>
                                <span class="text-[10px] text-slate-400">Đầu bi 0.5mm êm mượt, không chảy mực</span>
                            </td>
                            <td class="py-3.5 px-3 text-slate-300">20 Cây / Hộp • 60 Hộp / Thùng</td>
                            <td class="py-3.5 px-3 text-slate-400 font-mono line-through">5.000₫ / Cây</td>
                            <td class="py-3.5 px-3">
                                <span class="text-sm font-black text-emerald-400 font-mono">75.000₫ / Hộp</span>
                                <span class="block text-[10px] text-emerald-300 font-semibold">(Chỉ 3.750₫/Cây)</span>
                            </td>
                            <td class="py-3.5 px-3">
                                <span class="inline-block px-2 py-0.5 rounded bg-purple-500/20 text-purple-300 text-[10px] font-bold border border-purple-500/30">
                                    ⭐ Chiết khấu thêm 5% từ 3 hộp
                                </span>
                            </td>
                            <td class="py-3.5 px-3 text-right">
                                <a href="{{ route('storefront.products', ['category' => 'but-viet-muc-viet']) }}" class="px-3 py-1.5 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-[11px] transition">
                                    Đặt Hộp
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Micro Info Bar -->
            <div class="pt-2 flex flex-wrap items-center justify-between gap-3 text-xs text-slate-400 border-t border-slate-800/80">
                <span class="flex items-center space-x-1.5 text-emerald-300">
                    <span>✓</span> <span>Giá đã bao gồm xuất hóa đơn điện tử VAT hợp lệ</span>
                </span>
                <span class="flex items-center space-x-1.5 text-slate-300">
                    <span>📞</span> <span>Hotline hỗ trợ báo giá sỉ 24/7: <strong class="text-white">{{ $storefrontSettings['hotline'] ?? '0974.194.305' }}</strong></span>
                </span>
            </div>
        </section>

        <!-- 6. 🏆 SẢN PHẨM BÁN CHẠY NHẤT (TOP BEST SELLERS LEADERBOARD) -->
        <section aria-label="Sản phẩm bán chạy nhất" class="space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3 border-b border-slate-200/90 pb-3">
                <div>
                    <div class="flex items-center space-x-2">
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black bg-amber-100 text-amber-900 uppercase tracking-wider border border-amber-300">
                            🏆 BẢNG XẾP HẠNG
                        </span>
                        <span class="text-xs text-slate-500 font-medium hidden sm:inline">• Hơn 500+ doanh nghiệp & trường học đã tin dùng</span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight mt-1">
                        Sản Phẩm Bán Chạy Nhất Tại Kho
                    </h2>
                </div>
                <a href="{{ route('storefront.products', ['sort' => 'popular']) }}"
                    class="text-xs font-bold text-emerald-700 hover:text-emerald-800 hover:underline flex items-center space-x-1">
                    <span>Xem tất cả sản phẩm bán chạy</span>
                    <span>→</span>
                </a>
            </div>

            <!-- Grid 8 sản phẩm bán chạy nhất với huy chương Top 1, Top 2, Top 3 -->
            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($bestSellerProducts as $p)
                    @php
                        $rank = $loop->iteration;
                        $rankBadge = match ($rank) {
                            1 => '🥇 TOP 1 BÁN CHẠY',
                            2 => '🥈 TOP 2 BÁN CHẠY',
                            3 => '🥉 TOP 3 BÁN CHẠY',
                            default => '🎖️ TOP BÁN CHẠY',
                        };
                        $rankClass = match ($rank) {
                            1 => 'bg-gradient-to-r from-amber-500 to-yellow-400 text-slate-950 font-black',
                            2 => 'bg-gradient-to-r from-slate-400 to-slate-300 text-slate-900 font-black',
                            3 => 'bg-gradient-to-r from-amber-700 to-amber-600 text-white font-black',
                            default => 'bg-slate-800 text-white font-bold',
                        };
                    @endphp
                    @include('storefront.components.product-card', [
                        'p' => $p,
                        'topBadge' => $rankBadge,
                        'topBadgeClass' => $rankClass,
                        'showProgress' => false,
                    ])
                @endforeach
            </div>
        </section>

        <!-- 7. 📦 SẢN PHẨM THEO DANH MỤC TRỌNG ĐIỂM -->
        <section aria-label="Sản phẩm theo từng ngành hàng" class="space-y-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200/90 pb-3">
                <div>
                    <div class="flex items-center space-x-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-xs font-black text-slate-900 uppercase tracking-wider">Danh Mục Ngành Hàng Trọng Điểm</span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight mt-1">
                        Sản Phẩm Theo Danh Mục
                    </h2>
                </div>
                <a href="{{ route('storefront.products') }}"
                    class="text-xs font-bold text-emerald-700 hover:text-emerald-800 hover:underline flex items-center space-x-1">
                    <span>Xem tất cả hơn {{ number_format($stats['total_products'] ?? 1000) }}+ sản phẩm</span>
                    <span>→</span>
                </a>
            </div>

            @foreach($categorySections as $cat)
                @if($cat->products && $cat->products->count() > 0)
                    @php
                        $catIcon = match ($cat->slug) {
                            'giay-in-photo' => '📄',
                            'hop-muc-may-in' => '🖨️',
                            'but-viet-muc-viet' => '🖊️',
                            'bia-ho-so-luu-tru', 'bia-file-ho-so' => '📁',
                            'dung-cu-van-phong' => '✂️',
                            default => '📦',
                        };
                    @endphp
                    <div class="bg-white rounded-3xl p-4 sm:p-6 border border-slate-200/90 shadow-xs space-y-4">
                        <!-- Category Header -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
                            <div class="flex items-center space-x-3">
                                <span class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-800 flex items-center justify-center text-xl shadow-xs border border-emerald-100">
                                    {{ $catIcon }}
                                </span>
                                <div>
                                    <div class="flex items-center space-x-2">
                                        <h3 class="text-base sm:text-xl font-black text-slate-900 tracking-tight">
                                            {{ $cat->name }}
                                        </h3>
                                        <span class="text-[10px] bg-emerald-50 text-emerald-700 font-bold px-2 py-0.5 rounded-full border border-emerald-200">
                                            Sẵn kho giá sỉ
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-slate-500">
                                        {{ $cat->description ?: 'Các sản phẩm thiết yếu cho văn phòng công ty và trường học tại Đồng Nai' }}
                                    </p>
                                </div>
                            </div>

                            <a href="{{ route('storefront.products', ['category' => $cat->slug]) }}"
                                class="text-xs font-bold text-emerald-700 hover:text-emerald-800 hover:underline flex items-center space-x-1 shrink-0 self-start sm:self-auto">
                                <span>Xem toàn bộ danh mục này</span>
                                <span>→</span>
                            </a>
                        </div>

                        <!-- Category Products Grid (8 sản phẩm) -->
                        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            @foreach($cat->products as $p)
                                @include('storefront.components.product-card', [
                                    'p' => $p,
                                    'showProgress' => false,
                                ])
                            @endforeach
                        </div>

                        <!-- View More Button -->
                        <div class="pt-2 text-center">
                            <a href="{{ route('storefront.products', ['category' => $cat->slug]) }}"
                                class="inline-flex items-center space-x-2 px-6 py-2.5 bg-slate-50 hover:bg-emerald-50 text-slate-700 hover:text-emerald-800 border border-slate-200 hover:border-emerald-300 rounded-xl font-bold text-xs transition shadow-2xs group">
                                <span>Xem thêm toàn bộ sản phẩm {{ $cat->name }}</span>
                                <span class="group-hover:translate-x-1 transition-transform">→</span>
                            </a>
                        </div>
                    </div>
                @endif
            @endforeach
        </section>

        <!-- 8. DỊCH VỤ NẠP MỰC & SỬA MÁY IN TẬN NƠI TẠI ĐỒNG NAI (LOCAL SEO & BẢNG GIÁ MINH BẠCH) -->
        <div id="dich-vu-may-in"
            class="bg-gradient-to-br from-slate-900 via-blue-950 to-slate-900 rounded-3xl p-6 sm:p-10 text-white shadow-xl border border-slate-800 space-y-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 border-b border-slate-800 pb-6">
                <div class="space-y-2">
                    <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 font-black text-xs uppercase tracking-wider border border-emerald-500/30">
                        🛠️ DỊCH VỤ KỸ THUẬT TẬN NƠI TRONG 30 PHÚT
                    </span>
                    <h2 class="text-xl sm:text-3xl font-black text-white tracking-tight">
                        Nạp Mực & Sửa Chữa Máy In Tận Nơi Tại Đồng Nai
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-400 max-w-2xl">
                        Chuyên xử lý máy in Canon, HP, Brother, Epson: nạp mực siêu mịn, sửa kẹt giấy, bản in lem mờ, thay trống gạt chính hãng. Bảo hành đến giọt mực cuối cùng!
                    </p>
                </div>
                <div class="shrink-0 flex items-center space-x-3">
                    <a href="{{ $storefrontSettings['technical_hotline_url'] ?? $storefrontSettings['hotline_url'] }}"
                        class="px-5 py-3 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-black text-xs sm:text-sm shadow-lg shadow-emerald-500/25 transition flex items-center space-x-2">
                        <span>📞 Gọi Thợ Ngay: {{ $storefrontSettings['technical_hotline'] ?? $storefrontSettings['hotline'] }}</span>
                    </a>
                </div>
            </div>

            <!-- Bảng Giá Dịch Vụ Minh Bạch Chuẩn SEO -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Gói 1 -->
                <div class="bg-white/5 rounded-2xl p-5 border border-white/10 space-y-3 hover:bg-white/10 transition">
                    <div class="flex justify-between items-start">
                        <div>
                            <span class="text-[11px] font-bold text-emerald-400 uppercase">Phổ Biến Nhất</span>
                            <h3 class="text-base font-bold text-white mt-0.5">Nạp Mực Máy In Laser Canon / HP</h3>
                        </div>
                        <span class="text-base font-mono font-black text-amber-400">80k - 100k</span>
                    </div>
                    <p class="text-[11px] text-slate-400 leading-relaxed">
                        Áp dụng cho Canon LBP 2900, 3000, 3300, HP 1020, 1005, P1102, M12a... Mực siêu mịn, hút sạch mực thải, vệ sinh máy miễn phí.
                    </p>
                    <div class="text-[10px] text-emerald-300 font-semibold flex items-center space-x-1">
                        <span>✓</span> <span>Bảo hành nét chữ đến hết hộp mực</span>
                    </div>
                </div>

                <!-- Gói 2 -->
                <div class="bg-white/5 rounded-2xl p-5 border border-white/10 space-y-3 hover:bg-white/10 transition">
                    <div class="flex justify-between items-start">
                        <div>
                            <span class="text-[11px] font-bold text-sky-400 uppercase">Dòng Máy Brother</span>
                            <h3 class="text-base font-bold text-white mt-0.5">Nạp Mực & Reset Máy In Brother</h3>
                        </div>
                        <span class="text-base font-mono font-black text-amber-400">120k - 150k</span>
                    </div>
                    <p class="text-[11px] text-slate-400 leading-relaxed">
                        Dòng máy Brother HL-L2321D, L2366DW, DCP-B7535DW (mã hộp mực TN-2385, TN-B022). Đã bao gồm reset nhông mực & vệ sinh cụm Drum.
                    </p>
                    <div class="text-[10px] text-emerald-300 font-semibold flex items-center space-x-1">
                        <span>✓</span> <span>Bản in đậm đẹp không xám nền</span>
                    </div>
                </div>

                <!-- Gói 3 -->
                <div class="bg-white/5 rounded-2xl p-5 border border-white/10 space-y-3 hover:bg-white/10 transition">
                    <div class="flex justify-between items-start">
                        <div>
                            <span class="text-[11px] font-bold text-purple-400 uppercase">Linh Kiện Thay Thế</span>
                            <h3 class="text-base font-bold text-white mt-0.5">Thay Trống In, Gạt, Trục Từ, Sấy</h3>
                        </div>
                        <span class="text-base font-mono font-black text-amber-400">130k - 250k</span>
                    </div>
                    <p class="text-[11px] text-slate-400 leading-relaxed">
                        Khắc phục dứt điểm tình trạng bản in bị vệt đen dọc trang giấy, chấm đen lặp lại, mờ mịt hoặc kẹt giấy liên tục. Linh kiện loại 1 nhập khẩu.
                    </p>
                    <div class="text-[10px] text-emerald-300 font-semibold flex items-center space-x-1">
                        <span>✓</span> <span>Bảo hành 1 đổi 1 trong 3 tháng</span>
                    </div>
                </div>
            </div>

            <!-- Cam Kết 4 Điểm Kỹ Thuật -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 pt-2 text-center text-xs">
                <div class="p-3 rounded-xl bg-white/5 border border-white/5">
                    <span class="text-xl block mb-1">⚡</span>
                    <span class="font-bold text-white block">Có mặt trong 30-45P</span>
                    <span class="text-[10px] text-slate-400">Khu vực Đồng Nai</span>
                </div>
                <div class="p-3 rounded-xl bg-white/5 border border-white/5">
                    <span class="text-xl block mb-1">🔍</span>
                    <span class="font-bold text-white block">In test nghiệm thu</span>
                    <span class="text-[10px] text-slate-400">Đạt chuẩn mới thanh toán</span>
                </div>
                <div class="p-3 rounded-xl bg-white/5 border border-white/5">
                    <span class="text-xl block mb-1">🛡️</span>
                    <span class="font-bold text-white block">Bảo hành hạt mực</span>
                    <span class="text-[10px] text-slate-400">Hỗ trợ kỹ thuật 24/7</span>
                </div>
                <div class="p-3 rounded-xl bg-white/5 border border-white/5">
                    <span class="text-xl block mb-1">📑</span>
                    <span class="font-bold text-white block">Hóa đơn VAT đầy đủ</span>
                    <span class="text-[10px] text-slate-400">Cho công ty & cơ quan</span>
                </div>
            </div>
        </div>

        <!-- 9. ⭐ ĐÁNH GIÁ THỰC TẾ TỪ KHÁCH HÀNG & DOANH NGHIỆP (SOCIAL PROOF) -->
        <section aria-label="Đánh giá từ khách hàng" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-2xs space-y-6">
            <div class="text-center space-y-1">
                <span class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 font-black text-xs uppercase tracking-wider border border-emerald-200">
                    ⭐ ĐÁNH GIÁ THỰC TẾ
                </span>
                <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                    Khách Hàng Nói Gì Về Chúng Tôi
                </h2>
                <p class="text-xs text-slate-500">Đồng hành cùng hơn 500+ cơ quan, trường học và doanh nghiệp tại Đồng Nai</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Review 1 -->
                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/70 space-y-3 flex flex-col justify-between">
                    <div class="space-y-2">
                        <div class="flex text-amber-400 text-sm">★★★★★</div>
                        <p class="text-xs text-slate-700 leading-relaxed italic">
                            "Công ty tôi ở KCN Amata đặt giấy Double A theo tháng, giá sỉ tốt hơn nhiều so với các siêu thị lớn, xuất hóa đơn VAT điện tử trong 15 phút. Rất hài lòng!"
                        </p>
                    </div>
                    <div class="pt-2 border-t border-slate-200/60 flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-full bg-emerald-100 text-emerald-800 font-black flex items-center justify-center text-xs">KT</div>
                        <div>
                            <h4 class="text-xs font-bold text-slate-900">Chị Thu Thảo</h4>
                            <p class="text-[10px] text-slate-500">Kế Toán Trưởng • KCN Amata Biên Hòa</p>
                        </div>
                    </div>
                </div>

                <!-- Review 2 -->
                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/70 space-y-3 flex flex-col justify-between">
                    <div class="space-y-2">
                        <div class="flex text-amber-400 text-sm">★★★★★</div>
                        <p class="text-xs text-slate-700 leading-relaxed italic">
                            "Máy in Canon 2900 của phòng bị kẹt giấy và mờ chữ vào lúc 4h chiều chuẩn bị in hợp đồng. Gọi thợ 25 phút đã có mặt, xử lý nhanh chóng và vệ sinh máy sạch sẽ."
                        </p>
                    </div>
                    <div class="pt-2 border-t border-slate-200/60 flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-800 font-black flex items-center justify-center text-xs">HQ</div>
                        <div>
                            <h4 class="text-xs font-bold text-slate-900">Anh Hoàng Quân</h4>
                            <p class="text-[10px] text-slate-500">Giám Đốc VP Công Chứng • Trảng Bom</p>
                        </div>
                    </div>
                </div>

                <!-- Review 3 -->
                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/70 space-y-3 flex flex-col justify-between">
                    <div class="space-y-2">
                        <div class="flex text-amber-400 text-sm">★★★★★</div>
                        <p class="text-xs text-slate-700 leading-relaxed italic">
                            "Trường chúng tôi đặt số lượng lớn bút bi Thiên Long, phấn và bìa còng phục vụ năm học mới. Giao hàng đúng hẹn tận kho trường, chính sách công nợ linh hoạt."
                        </p>
                    </div>
                    <div class="pt-2 border-t border-slate-200/60 flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-full bg-amber-100 text-amber-800 font-black flex items-center justify-center text-xs">LM</div>
                        <div>
                            <h4 class="text-xs font-bold text-slate-900">Thầy Lê Minh</h4>
                            <p class="text-[10px] text-slate-500">Phụ Trách Cơ Sở Vật Chất • Biên Hòa</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 10. KHỐI CÂU HỎI THƯỜNG GẶP (FAQ SECTION - SEO & AI SEARCH OVERVIEW) -->
        <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-2xs border border-slate-200 space-y-6">
            <div class="text-center space-y-2">
                <span class="px-3 py-1 rounded-full bg-blue-50 text-blue-700 font-black text-xs uppercase tracking-wider border border-blue-200">
                    ❓ GIẢI ĐÁP NHANH
                </span>
                <h2 class="text-xl sm:text-3xl font-black text-slate-900 tracking-tight">
                    Câu Hỏi Thường Gặp Của Khách Hàng
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 max-w-xl mx-auto">
                    Những thông tin được tìm kiếm nhiều nhất về chính sách giao hàng, thanh toán và bảo hành tại cửa hàng.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                <!-- FAQ 1 -->
                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2">
                    <h3 class="text-xs sm:text-sm font-bold text-slate-900 flex items-start space-x-2">
                        <span class="text-blue-600 font-black">Q1:</span>
                        <span>Bơm mực máy in tận nơi tại Đồng Nai giá bao nhiêu và mất bao lâu?</span>
                    </h3>
                    <p class="text-xs text-slate-600 leading-relaxed pl-6">
                        Giá dịch vụ nạp mực chỉ từ <strong>80.000đ đến 150.000đ</strong> tùy dòng máy. Kỹ thuật viên có mặt tại địa chỉ của Quý khách trong vòng <strong>30 - 45 phút</strong>, gồm vệ sinh máy và hút sạch mực thải miễn phí.
                    </p>
                </div>

                <!-- FAQ 2 -->
                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2">
                    <h3 class="text-xs sm:text-sm font-bold text-slate-900 flex items-start space-x-2">
                        <span class="text-blue-600 font-black">Q2:</span>
                        <span>Cửa hàng có giao văn phòng phẩm tận nơi miễn phí không?</span>
                    </h3>
                    <p class="text-xs text-slate-600 leading-relaxed pl-6">
                        Chúng tôi <strong>miễn phí giao hàng</strong> cho mọi đơn từ 500.000đ tại Đồng Nai. Hỗ trợ giao hàng hỏa tốc trong 2 giờ và giao tận bàn làm việc cho các văn phòng, trường học, khu công nghiệp.
                    </p>
                </div>

                <!-- FAQ 3 -->
                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2">
                    <h3 class="text-xs sm:text-sm font-bold text-slate-900 flex items-start space-x-2">
                        <span class="text-blue-600 font-black">Q3:</span>
                        <span>Chính sách bảo hành sau khi sửa máy in và nạp mực như thế nào?</span>
                    </h3>
                    <p class="text-xs text-slate-600 leading-relaxed pl-6">
                        Cam kết bảo hành chất lượng bản in đến khi <strong>hết hạt mực cuối cùng</strong>. Các linh kiện thay thế như trống in (drum), gạt mực, trục sạc được bảo hành 1 đổi 1 từ 1 đến 3 tháng.
                    </p>
                </div>

                <!-- FAQ 4 -->
                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2">
                    <h3 class="text-xs sm:text-sm font-bold text-slate-900 flex items-start space-x-2">
                        <span class="text-blue-600 font-black">Q4:</span>
                        <span>Doanh nghiệp, trường học mua sỉ có được chiết khấu và hóa đơn VAT không?</span>
                    </h3>
                    <p class="text-xs text-slate-600 leading-relaxed pl-6">
                        Có! Mức chiết khấu từ <strong>15% đến 25%</strong> cho khách hàng định kỳ, xuất hóa đơn VAT điện tử hợp lệ 100% trong 15 phút và hỗ trợ công nợ thanh toán 30 ngày.
                    </p>
                </div>
            </div>
        </div>

        <!-- 11. TRA CỨU TIẾN ĐỘ SỬA MÁY IN ONLINE (CHỐNG IDOR) -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-2xs border border-slate-200 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center space-x-4">
                <div class="w-14 h-14 rounded-2xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-2xl font-black shrink-0">
                    🔍
                </div>
                <div>
                    <h3 class="text-base sm:text-lg font-black text-slate-900">
                        Tra Cứu Tiến Độ Sửa Máy In & Nạp Mực Online
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Khách hàng đã gửi máy tại cửa hàng có thể tra cứu tình trạng xử lý và hình ảnh thực tế trực tuyến 24/7 chỉ với Mã phiếu & 4 số cuối SĐT.
                    </p>
                </div>
            </div>
            <div class="shrink-0 w-full md:w-auto">
                <a href="{{ route('lookup.index') }}"
                    class="w-full md:w-auto inline-flex items-center justify-center space-x-2 px-6 py-3 bg-[#1e3a8a] hover:bg-blue-900 text-white font-extrabold text-xs sm:text-sm rounded-xl shadow transition">
                    <span>Tra Cứu Phiếu Tiếp Nhận Ngay</span>
                    <span>→</span>
                </a>
            </div>
        </div>

    </main>
@endsection

@push('scripts')
<script>
    (function () {
        // Countdown Timer Flash Sale
        function updateCountdown() {
            const now = new Date();
            const endOfDay = new Date(now);
            endOfDay.setHours(23, 59, 59, 999);
            const diff = endOfDay - now;
            if (diff <= 0) { return; }

            const hours = Math.floor(diff / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((diff % (1000 * 60)) / 1000);

            const hEl = document.getElementById('countdown-hours');
            const mEl = document.getElementById('countdown-minutes');
            const sEl = document.getElementById('countdown-seconds');
            if (hEl) hEl.textContent = String(hours).padStart(2, '0');
            if (mEl) mEl.textContent = String(minutes).padStart(2, '0');
            if (sEl) sEl.textContent = String(seconds).padStart(2, '0');
        }
        updateCountdown();
        setInterval(updateCountdown, 1000);

        // Flash Sale Slider Controller
        window.slideFlashSale = function (direction) {
            const track = document.getElementById('flashSaleTrack');
            if (track) {
                const card = track.querySelector('div');
                const scrollStep = card ? (card.offsetWidth + 16) * 2 : 540;
                track.scrollBy({
                    left: direction * scrollStep,
                    behavior: 'smooth'
                });
            }
        };
    })();
</script>
@endpush