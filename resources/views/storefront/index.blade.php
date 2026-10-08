@extends('layouts.storefront')

@section('title', $storefrontSettings['site_name'].' - Tổng Kho Văn Phòng Phẩm & Dịch Vụ Máy In Chuyên Nghiệp')
@section('meta_description', 'Tổng kho hơn 1.000 SKU văn phòng phẩm, giấy in photo Double A, PaperOne, bút Thiên Long giá sỉ & lẻ. Hộp mực, trống drum linh kiện máy in Canon 2900, Brother, HP chính hãng. Giao siêu tốc 2H.')

@section('content')
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-8 w-full">

    <!-- 1. HERO BANNER FULL WIDTH (Hình ảnh siêu thị văn phòng phẩm sắc nét kết hợp gradient sang trọng) -->
    <div class="relative overflow-hidden rounded-3xl text-white p-6 sm:p-10 lg:p-12 shadow-2xl border border-slate-700/60 group">

        <!-- Background Image with Vibrant Store View & Readability Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/banners/stationery_store_bright.jpg') }}" alt="Siêu Thị Văn Phòng Phẩm & Hộp Mực Máy In" class="w-full h-full object-cover object-center transform group-hover:scale-105 transition duration-700 opacity-65" />
            <div class="absolute inset-0 bg-gradient-to-r from-[#060c20]/95 via-[#0a1433]/85 to-[#0b173d]/50"></div>
            <!-- Background Ambient Glow & Shapes -->
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-emerald-500/25 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-blue-500/25 rounded-full blur-3xl pointer-events-none"></div>
        </div>

        <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">

            <!-- Left Info Content (7 cols) -->
            <div class="lg:col-span-7 space-y-5 text-left">
                <!-- Badges Row -->
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black bg-amber-400 text-slate-950 shadow-xs uppercase tracking-wider">
                        ⭐ TỔNG KHO SỈ & LẺ CHÍNH HÃNG
                    </span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                        ⚡ Giao Siêu Tốc 2 Giờ TP.HCM
                    </span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-white/10 text-slate-200 border border-white/10">
                        🏢 Hóa Đơn VAT Điện Tử
                    </span>
                </div>

                <!-- Main Headline -->
                <h1 class="text-2xl sm:text-4xl lg:text-5xl font-black text-white leading-tight tracking-tight">
                    Văn Phòng Phẩm & Hộp Mực Máy In <br class="hidden sm:inline" />
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-teal-200">
                        Giá Sỉ Tận Gốc - Giao Siêu Tốc
                    </span>
                </h1>

                <!-- Subheadline -->
                <p class="text-xs sm:text-sm lg:text-base text-slate-300 leading-relaxed max-w-2xl">
                    Hơn 1.000+ sản phẩm sẵn kho: Giấy in photo Double A, PaperOne, bút Thiên Long, hộp mực Canon 2900, Brother TN-2385, linh kiện thay thế chính hãng. Chiết khấu tới 25% cho doanh nghiệp, cơ quan & trường học.
                </p>

                <!-- Action CTA Buttons (Đã bỏ nút hotline trùng lặp theo yêu cầu) -->
                <div class="pt-2 flex flex-wrap gap-3 items-center">
                    <a href="{{ route('storefront.products') }}" class="px-6 py-3 rounded-2xl bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white font-extrabold text-xs sm:text-sm shadow-lg shadow-emerald-900/30 transition transform hover:-translate-y-0.5 flex items-center space-x-2">
                        <span>Khám Phá Sản Phẩm Ngay</span>
                        <span>→</span>
                    </a>
                    <a href="{{ route('storefront.wholesale') }}" class="px-6 py-3 rounded-2xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs sm:text-sm border border-white/20 backdrop-blur-xs transition flex items-center space-x-2">
                        <span>⭐ Nhận Báo Giá Sỉ Doanh Nghiệp</span>
                    </a>
                </div>

                <!-- Trust Guarantee Micro-tags -->
                <div class="pt-3 flex flex-wrap items-center gap-4 text-[11px] text-slate-300 font-semibold border-t border-slate-700/60">
                    <span class="flex items-center space-x-1 text-emerald-400">
                        <span>✓</span> <span>100% Chính hãng</span>
                    </span>
                    <span class="flex items-center space-x-1 text-emerald-400">
                        <span>✓</span> <span>Đổi trả 1-đổi-1 trong 7 ngày</span>
                    </span>
                    <span class="flex items-center space-x-1 text-emerald-400">
                        <span>✓</span> <span>Hỗ trợ công nợ doanh nghiệp</span>
                    </span>
                    <span class="flex items-center space-x-1 text-emerald-400">
                        <span>✓</span> <span>Kỹ thuật viên nạp mực tận nơi</span>
                    </span>
                </div>
            </div>

            <!-- Right Showcase Visual (5 cols) -->
            <div class="lg:col-span-5 relative flex items-center justify-center">
                <div class="relative w-full max-w-md bg-white/5 backdrop-blur-md rounded-3xl p-5 border border-white/10 shadow-2xl">
                    <!-- Float Tag -->
                    <div class="absolute -top-3 -right-3 bg-rose-500 text-white font-black text-xs px-3 py-1 rounded-full shadow-md uppercase tracking-wider animate-bounce">
                        🔥 Bán chạy nhất
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <!-- Item 1: Giấy Double A -->
                        <div class="bg-white rounded-2xl p-3 shadow-md flex flex-col items-center text-center group hover:scale-105 transition transform">
                            <div class="w-24 h-24 flex items-center justify-center p-1 mb-1">
                                <img src="{{ asset('images/products/giay-double-a-a4.jpg') }}" alt="Giấy Double A" class="max-h-full max-w-full object-contain" />
                            </div>
                            <span class="text-xs font-extrabold text-slate-900 line-clamp-1">Giấy Double A A4 70gsm</span>
                            <span class="text-[11px] font-bold text-rose-600 font-mono mt-0.5">85.000 VNĐ / Ram</span>
                        </div>

                        <!-- Item 2: Hộp Mực 12A -->
                        <div class="bg-white rounded-2xl p-3 shadow-md flex flex-col items-center text-center group hover:scale-105 transition transform">
                            <div class="w-24 h-24 flex items-center justify-center p-1 mb-1">
                                <img src="{{ asset('images/products/muc-cartridge-12a.jpg') }}" alt="Hộp Mực 12A" class="max-h-full max-w-full object-contain" />
                            </div>
                            <span class="text-xs font-extrabold text-slate-900 line-clamp-1">Hộp Mực 12A Canon 2900</span>
                            <span class="text-[11px] font-bold text-rose-600 font-mono mt-0.5">250.000 VNĐ / Hộp</span>
                        </div>

                        <!-- Item 3: Bút Thiên Long -->
                        <div class="bg-white rounded-2xl p-3 shadow-md flex flex-col items-center text-center group hover:scale-105 transition transform">
                            <div class="w-24 h-24 flex items-center justify-center p-1 mb-1">
                                <img src="{{ asset('images/products/but-thien-long.jpg') }}" alt="Bút Thiên Long" class="max-h-full max-w-full object-contain" />
                            </div>
                            <span class="text-xs font-extrabold text-slate-900 line-clamp-1">Bút Bi Thiên Long TL-027</span>
                            <span class="text-[11px] font-bold text-rose-600 font-mono mt-0.5">5.000 VNĐ / Cây</span>
                        </div>

                        <!-- Item 4: Mực Brother TN-2385 -->
                        <div class="bg-white rounded-2xl p-3 shadow-md flex flex-col items-center text-center group hover:scale-105 transition transform">
                            <div class="w-24 h-24 flex items-center justify-center p-1 mb-1">
                                <img src="{{ asset('images/products/muc-brother-tn2385.jpg') }}" alt="Mực Brother TN2385" class="max-h-full max-w-full object-contain" />
                            </div>
                            <span class="text-xs font-extrabold text-slate-900 line-clamp-1">Mực Brother TN-2385</span>
                            <span class="text-[11px] font-bold text-rose-600 font-mono mt-0.5">280.000 VNĐ / Hộp</span>
                        </div>
                    </div>

                    <div class="mt-3 text-center">
                        <a href="{{ route('storefront.products') }}" class="text-[11px] text-amber-300 font-bold hover:underline inline-flex items-center space-x-1">
                            <span>Xem toàn bộ 1.000+ sản phẩm sẵn có tại kho</span>
                            <span>→</span>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- 2. FOUR CORE COMMITMENTS BAR -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/90 flex items-center space-x-3.5 hover:border-emerald-500 hover:shadow-md transition">
            <div class="w-11 h-11 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-xl shrink-0">
                ⚡
            </div>
            <div>
                <h3 class="text-xs sm:text-sm font-black text-slate-900">Giao Siêu Tốc 2H</h3>
                <p class="text-[11px] text-slate-500 leading-tight mt-0.5">Nội thành TP.HCM nhận trong 120 phút</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/90 flex items-center space-x-3.5 hover:border-emerald-500 hover:shadow-md transition">
            <div class="w-11 h-11 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center text-xl shrink-0">
                🛡️
            </div>
            <div>
                <h3 class="text-xs sm:text-sm font-black text-slate-900">100% Chính Hãng</h3>
                <p class="text-[11px] text-slate-500 leading-tight mt-0.5">Đền bù 200% nếu phát hiện hàng giả</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/90 flex items-center space-x-3.5 hover:border-emerald-500 hover:shadow-md transition">
            <div class="w-11 h-11 rounded-2xl bg-amber-100 text-amber-800 flex items-center justify-center text-xl shrink-0">
                💰
            </div>
            <div>
                <h3 class="text-xs sm:text-sm font-black text-slate-900">Giá Sỉ Tận Kho</h3>
                <p class="text-[11px] text-slate-500 leading-tight mt-0.5">Chiết khấu tới 25% cho doanh nghiệp</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/90 flex items-center space-x-3.5 hover:border-emerald-500 hover:shadow-md transition">
            <div class="w-11 h-11 rounded-2xl bg-rose-100 text-rose-700 flex items-center justify-center text-xl shrink-0">
                🔄
            </div>
            <div>
                <h3 class="text-xs sm:text-sm font-black text-slate-900">Bảo Hành 1 Đổi 1</h3>
                <p class="text-[11px] text-slate-500 leading-tight mt-0.5">Đổi mới hộp mực và linh kiện bị lỗi</p>
            </div>
        </div>
    </div>

    <!-- 3. FOUR FEATURED CATEGORIES SHOWCASE -->
    <div>
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight">
                    Danh Mục Sản Phẩm Nổi Bật
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Các mặt hàng thiết yếu cho văn phòng công ty và trường học</p>
            </div>
            <a href="{{ route('storefront.products') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800 hover:underline flex items-center space-x-1">
                <span>Xem tất cả</span>
                <span>→</span>
            </a>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Category 1: Giấy in photo -->
            <a href="{{ route('storefront.products', ['category' => 'giay-in-photo']) }}" class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm border border-slate-200/80 hover:shadow-lg hover:border-emerald-500 transition duration-200 text-center flex flex-col items-center justify-between group">
                <div class="w-24 h-24 sm:w-28 sm:h-28 flex items-center justify-center p-2 mb-2 bg-slate-50 rounded-xl group-hover:bg-emerald-50/50 transition">
                    <img src="{{ asset('images/products/giay-double-a-a4.jpg') }}" alt="Giấy In Photo" class="max-h-full max-w-full object-contain group-hover:scale-105 transition transform duration-200" />
                </div>
                <div class="space-y-0.5">
                    <span class="text-sm sm:text-base font-bold text-slate-900 group-hover:text-emerald-700 transition block">
                        Giấy In & Photocopy
                    </span>
                    <span class="text-[11px] text-slate-500 block">Double A, PaperOne, IK Plus</span>
                </div>
            </a>

            <!-- Category 2: Bút & Dụng cụ văn phòng -->
            <a href="{{ route('storefront.products', ['category' => 'but-viet-muc-viet']) }}" class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm border border-slate-200/80 hover:shadow-lg hover:border-emerald-500 transition duration-200 text-center flex flex-col items-center justify-between group">
                <div class="w-24 h-24 sm:w-28 sm:h-28 flex items-center justify-center p-2 mb-2 bg-slate-50 rounded-xl group-hover:bg-emerald-50/50 transition">
                    <img src="{{ asset('images/products/but-thien-long.jpg') }}" alt="Bút Viết & Bìa File" class="max-h-full max-w-full object-contain group-hover:scale-105 transition transform duration-200" />
                </div>
                <div class="space-y-0.5">
                    <span class="text-sm sm:text-base font-bold text-slate-900 group-hover:text-emerald-700 transition block">
                        Bút Viết & File Hồ Sơ
                    </span>
                    <span class="text-[11px] text-slate-500 block">Thiên Long, Kokuyo, Bìa còng</span>
                </div>
            </a>

            <!-- Category 3: Hộp mực & Phụ kiện -->
            <a href="{{ route('storefront.products', ['category' => 'hop-muc-may-in']) }}" class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm border border-slate-200/80 hover:shadow-lg hover:border-emerald-500 transition duration-200 text-center flex flex-col items-center justify-between group">
                <div class="w-24 h-24 sm:w-28 sm:h-28 flex items-center justify-center p-2 mb-2 bg-slate-50 rounded-xl group-hover:bg-emerald-50/50 transition">
                    <img src="{{ asset('images/products/muc-cartridge-12a.jpg') }}" alt="Hộp Mực Máy In" class="max-h-full max-w-full object-contain group-hover:scale-105 transition transform duration-200" />
                </div>
                <div class="space-y-0.5">
                    <span class="text-sm sm:text-base font-bold text-slate-900 group-hover:text-emerald-700 transition block">
                        Hộp Mực Máy In
                    </span>
                    <span class="text-[11px] text-slate-500 block">Canon 2900, Brother, HP, Epson</span>
                </div>
            </a>

            <!-- Category 4: Linh kiện & Dịch vụ máy in -->
            <a href="{{ route('storefront.products', ['category' => 'linh-kien-may-in']) }}" class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm border border-slate-200/80 hover:shadow-lg hover:border-emerald-500 transition duration-200 text-center flex flex-col items-center justify-between group">
                <div class="w-24 h-24 sm:w-28 sm:h-28 flex items-center justify-center p-2 mb-2 bg-slate-50 rounded-xl group-hover:bg-emerald-50/50 transition">
                    <img src="{{ asset('images/products/muc-brother-tn2385.jpg') }}" alt="Linh Kiện Máy In" class="max-h-full max-w-full object-contain group-hover:scale-105 transition transform duration-200" />
                </div>
                <div class="space-y-0.5">
                    <span class="text-sm sm:text-base font-bold text-slate-900 group-hover:text-emerald-700 transition block">
                        Trống Drum & Linh Kiện
                    </span>
                    <span class="text-[11px] text-slate-500 block">Trống gạt, trục từ, dịch vụ nạp mực</span>
                </div>
            </a>
        </div>
    </div>

    <!-- 4. PRODUCT CATALOG SHOWCASE (ĐÃ SỬA: SẢN PHẨM BẤM XEM ĐƯỢC CHI TIẾT 100%) -->
    <div class="space-y-4">

        <!-- Section Title with Flash Sale Badges -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-200 pb-3">
            <div class="flex items-center space-x-3">
                <span class="text-base sm:text-xl font-black text-slate-900 flex items-center space-x-2">
                    <span class="text-rose-600">⚡ Flash Sale</span>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black bg-rose-500 text-white animate-pulse">Giảm tới 30%</span>
                </span>
                <span class="text-slate-300">|</span>
                <span class="text-xs sm:text-sm font-bold text-slate-600 hidden sm:inline">Sản Phẩm Bán Chạy Nhất Tại Kho</span>
            </div>

            <div class="flex items-center space-x-2">
                <span class="text-xs text-emerald-700 font-bold bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200">
                    🚚 Miễn phí giao hàng từ 300.000đ
                </span>
            </div>
        </div>

        <!-- Product Grid (Responsive: 2 cols on mobile, 4 on desktop) -->
        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @forelse($products as $p)
            <div
                onclick="window.location.href='{{ route('storefront.product-detail', $p->slug) }}'"
                class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/90 flex flex-col justify-between hover:shadow-xl hover:border-emerald-500 transition duration-200 group relative cursor-pointer"
            >

                <div>
                    <!-- Badges Header -->
                    <div class="flex items-center justify-between gap-1 text-[10px] mb-2">
                        <span class="bg-rose-500 text-white font-black px-2 py-0.5 rounded-md">Hot Deal</span>
                        <span class="bg-emerald-50 text-emerald-700 font-bold px-2 py-0.5 rounded-md border border-emerald-200">● Còn hàng</span>
                    </div>

                    <!-- Product Thumbnail Packshot -->
                    <div class="block w-full h-36 sm:h-40 bg-slate-50/70 rounded-xl mb-3 flex items-center justify-center overflow-hidden p-2 group-hover:bg-emerald-50/30 transition">
                        <img src="{{ $p->image_url }}" alt="{{ $p->name }}" class="max-h-full max-w-full object-contain group-hover:scale-105 transition transform duration-200" />
                    </div>

                    <!-- SKU & Category -->
                    <div class="flex items-center justify-between text-[10px] text-slate-400 font-mono mb-1">
                        <span>SKU: {{ $p->sku }}</span>
                        @if($p->category)
                        <span class="text-slate-500 font-sans truncate max-w-[100px]">{{ $p->category->name }}</span>
                        @endif
                    </div>

                    <!-- Product Name -->
                    <h3 class="text-xs sm:text-sm font-bold text-slate-900 leading-snug line-clamp-2 mb-2 group-hover:text-emerald-700 transition">
                        {{ $p->name }}
                    </h3>

                    <!-- Star Rating -->
                    <div class="flex items-center space-x-1 text-[11px] text-amber-400 mb-2">
                        <span>★★★★★</span>
                        <span class="text-[10px] text-slate-400 font-semibold">(5.0)</span>
                    </div>

                    <!-- Price Section (VND Formatted) -->
                    <div class="space-y-1 mb-3">
                        <div class="flex items-baseline space-x-2">
                            <span class="text-sm sm:text-base font-black text-rose-600 font-mono">
                                {{ number_format($p->retail_price, 0, ',', '.') }} VNĐ
                            </span>
                            <span class="text-[11px] text-slate-400 line-through font-mono">
                                {{ number_format($p->retail_price * 1.15, 0, ',', '.') }} VNĐ
                            </span>
                        </div>

                        <!-- Multi-unit pricing pills if exists -->
                        @if($p->units->count() > 0)
                        <div class="pt-1 space-y-0.5">
                            @foreach($p->units as $u)
                            <div class="bg-slate-50 text-slate-600 px-2 py-0.5 rounded text-[10px] flex justify-between">
                                <span>{{ $u->unit_name }}:</span>
                                <span class="font-bold text-slate-900 font-mono">{{ number_format($u->price, 0, ',', '.') }} VNĐ</span>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Two Actions: Xem Chi Tiết + Thêm Vào Giỏ Hàng -->
                <div class="pt-3 border-t border-slate-100 grid grid-cols-2 gap-2">
                    <!-- Nút 1: Xem chi tiết -->
                    <span
                        class="w-full py-2 bg-slate-100 group-hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition flex items-center justify-center text-center"
                    >
                        Chi Tiết →
                    </span>

                    <!-- Nút 2: Thêm giỏ hàng -->
                    <button
                        type="button"
                        onclick="event.stopPropagation(); addToCart({{ $p->id }}, null, '{{ addslashes($p->name) }}', '{{ $p->base_unit }}', {{ (float) $p->retail_price }}, '{{ $p->image_url }}')"
                        class="w-full py-2 bg-[#059669] hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold text-xs rounded-xl transition flex items-center justify-center space-x-1 shadow-sm"
                    >
                        <span>+ Giỏ hàng</span>
                    </button>
                </div>

            </div>
            @empty
            <div class="col-span-full text-center py-12 bg-white rounded-3xl border border-slate-200">
                <span class="text-4xl block mb-2">🔍</span>
                <h4 class="text-base font-bold text-slate-700">Không tìm thấy sản phẩm phù hợp</h4>
                <p class="text-xs text-slate-400 mt-1">Quý khách vui lòng thử tìm với từ khóa khác.</p>
                <a href="{{ route('storefront.products') }}" class="inline-block mt-3 px-5 py-2.5 bg-[#059669] text-white rounded-xl text-xs font-bold shadow">
                    Xem tất cả sản phẩm
                </a>
            </div>
            @endforelse
        </div>

        <!-- View All Products Button -->
        <div class="pt-4 text-center">
            <a href="{{ route('storefront.products') }}" class="inline-flex items-center space-x-2 px-8 py-3 bg-white hover:bg-slate-50 text-slate-900 border-2 border-slate-300 hover:border-emerald-600 rounded-2xl font-black text-xs sm:text-sm shadow-sm transition">
                <span>Xem Thêm Hơn 1.000+ Sản Phẩm Khác</span>
                <span>→</span>
            </a>
        </div>
    </div>

    <!-- 5. CORPORATE WHOLESALE CALL TO ACTION BANNER (B2B SỈ DOANH NGHIỆP) -->
    <div class="relative overflow-hidden rounded-3xl text-white p-6 sm:p-10 border border-slate-700/60 shadow-xl group">
        <!-- Background Image with Dark Gradient Tint Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/banners/banner1.jpg') }}" alt="Báo Giá Sỉ Doanh Nghiệp" class="w-full h-full object-cover object-center transform group-hover:scale-105 transition duration-700 opacity-25" />
            <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-900/95 to-indigo-950/90"></div>
        </div>
        <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
            <div class="lg:col-span-8 space-y-3">
                <span class="inline-block px-3 py-1 bg-amber-400 text-slate-950 font-black text-xs rounded-full uppercase tracking-wider">
                    ⭐ DÀNH RIÊNG CHO DOANH NGHIỆP & TRƯỜNG HỌC
                </span>
                <h2 class="text-xl sm:text-3xl font-black leading-tight text-white">
                    Đăng Ký Báo Giá Sỉ Văn Phòng Phẩm Định Kỳ
                </h2>
                <p class="text-xs sm:text-sm text-slate-300 leading-relaxed max-w-2xl">
                    Chiết khấu từ 15% đến 25% trực tiếp trên đơn hàng. Hỗ trợ công nợ 30 ngày, miễn phí giao định kỳ hàng tháng, xuất hóa đơn VAT điện tử nhanh chóng trong 15 phút.
                </p>
                <div class="pt-2 flex flex-wrap gap-3">
                    <a href="{{ route('storefront.wholesale') }}" class="px-6 py-3 bg-amber-400 hover:bg-amber-500 text-slate-950 font-black text-xs sm:text-sm rounded-xl transition shadow">
                        Đăng Ký Nhận Báo Giá Sỉ Ngay →
                    </a>
                    <a href="{{ $storefrontSettings['zalo_url'] }}" target="_blank" class="px-5 py-3 bg-white/10 hover:bg-white/20 text-white font-bold text-xs sm:text-sm rounded-xl border border-white/20 transition">
                        Tư Vấn Hợp Đồng Qua Zalo
                    </a>
                </div>
            </div>

            <div class="lg:col-span-4 bg-white/5 backdrop-blur-md rounded-2xl p-5 border border-white/10 space-y-3">
                <h4 class="text-xs font-black uppercase text-amber-300 tracking-wider">Quyền Lợi Khách Hàng Doanh Nghiệp</h4>
                <ul class="space-y-2 text-xs text-slate-200">
                    <li class="flex items-start space-x-2">
                        <span class="text-emerald-400 font-bold">✓</span>
                        <span>Bảng báo giá cạnh tranh nhất TP.HCM</span>
                    </li>
                    <li class="flex items-start space-x-2">
                        <span class="text-emerald-400 font-bold">✓</span>
                        <span>Giao tận bàn làm việc các tầng văn phòng</span>
                    </li>
                    <li class="flex items-start space-x-2">
                        <span class="text-emerald-400 font-bold">✓</span>
                        <span>Kiểm tra & vệ sinh máy in định kỳ miễn phí</span>
                    </li>
                    <li class="flex items-start space-x-2">
                        <span class="text-emerald-400 font-bold">✓</span>
                        <span>Ký hợp đồng cung ứng dài hạn linh hoạt</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- 6. LOOKUP REPAIR TICKET SHORTCUT BANNER -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200 flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="flex items-center space-x-4">
            <div class="w-14 h-14 rounded-2xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-2xl font-black shrink-0">
                🔍
            </div>
            <div>
                <h3 class="text-base sm:text-lg font-black text-slate-900">
                    Tra Cứu Tiến Độ Sửa Máy In & Nạp Mực
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">
                    Khách hàng đã gửi máy tại cửa hàng có thể tra cứu tình trạng xử lý và hình ảnh thực tế trực tuyến 24/7.
                </p>
            </div>
        </div>
        <div class="shrink-0 w-full md:w-auto">
            <a href="{{ route('lookup.index') }}" class="w-full md:w-auto inline-flex items-center justify-center space-x-2 px-6 py-3 bg-[#1e3a8a] hover:bg-blue-900 text-white font-extrabold text-xs sm:text-sm rounded-xl shadow transition">
                <span>Tra Cứu Phiếu Tiếp Nhận</span>
                <span>→</span>
            </a>
        </div>
    </div>

</main>
@endsection
