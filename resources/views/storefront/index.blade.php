@extends('layouts.storefront')

@section('title', ($storefrontSettings['site_name'] ?? 'VPP & Thiết Bị Máy In') . ' - Tổng Kho Văn Phòng Phẩm & Dịch Vụ Máy In Tận Nơi Đồng Nai')
@section('meta_description', 'Tổng kho hơn 1.000 sản phẩm văn phòng phẩm chính hãng, giấy in photo Double A giá sỉ, dịch vụ nạp mực & sửa chữa máy in tận nơi trong 30 phút tại Đồng Nai. Hóa đơn VAT đầy đủ.')

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
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 sm:py-6 space-y-6 sm:space-y-8 w-full">

    <!-- 1. HERO SECTION TỶ LỆ VÀNG 1 + 2 (1 BANNER LỚN CHÍNH + 2 CARD KHUYẾN MÃI NỔI BẬT) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 sm:gap-6 items-stretch">

        <!-- Main Banner Lớn (lg:col-span-8) -->
        <div class="lg:col-span-8 relative overflow-hidden rounded-3xl text-white p-6 sm:p-9 shadow-xl border border-slate-700/60 group flex flex-col justify-between min-h-[380px]">
            <!-- Background Image & Gradient Overlays -->
            <div class="absolute inset-0 z-0">
                <img src="{{ asset('images/banners/stationery_store_bright.jpg') }}" alt="Siêu Thị Văn Phòng Phẩm & Hộp Mực Máy In" class="w-full h-full object-cover object-center transform group-hover:scale-105 transition duration-700 opacity-60" />
                <div class="absolute inset-0 bg-gradient-to-r from-[#060c20]/95 via-[#0b173d]/90 to-[#0e2158]/60"></div>
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
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-teal-200">
                        Giá Sỉ Tận Gốc - Giao Siêu Tốc
                    </span>
                </h1>

                <!-- Subtitle -->
                <p class="text-xs sm:text-sm text-slate-300 leading-relaxed max-w-xl">
                    Hơn 1.000+ sản phẩm sẵn kho: Giấy Double A, PaperOne, bút Thiên Long, mực Canon 2900, Brother. Chiết khấu tới 25% cho cơ quan, trường học & doanh nghiệp tại Đồng Nai.
                </p>

                <!-- Action CTA Buttons -->
                <div class="pt-2 flex flex-wrap gap-3 items-center">
                    <a href="{{ route('storefront.products') }}" class="px-6 py-3 rounded-2xl bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white font-extrabold text-xs sm:text-sm shadow-lg shadow-emerald-900/30 transition transform hover:-translate-y-0.5 flex items-center space-x-2">
                        <span>Khám Phá Sản Phẩm Ngay</span>
                        <span>→</span>
                    </a>
                    <a href="{{ route('storefront.wholesale') }}" class="px-5 py-3 rounded-2xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs sm:text-sm border border-white/20 backdrop-blur-xs transition flex items-center space-x-1.5">
                        <span>⭐ Nhận Báo Giá Sỉ B2B</span>
                    </a>
                </div>
            </div>

            <!-- Trust Guarantee Micro-tags -->
            <div class="relative z-10 pt-4 mt-4 border-t border-slate-700/60 flex flex-wrap items-center gap-4 text-[11px] text-slate-300 font-semibold">
                <span class="flex items-center space-x-1 text-emerald-400">
                    <span>✓</span> <span>100% Hàng chính hãng</span>
                </span>
                <span class="flex items-center space-x-1 text-emerald-400">
                    <span>✓</span> <span>Đổi mới trong 7 ngày</span>
                </span>
                <span class="flex items-center space-x-1 text-emerald-400">
                    <span>✓</span> <span>Hỗ trợ công nợ doanh nghiệp</span>
                </span>
                <span class="flex items-center space-x-1 text-emerald-400">
                    <span>✓</span> <span>Thợ nạp mực 30 phút</span>
                </span>
            </div>
        </div>

        <!-- 2 Side Promo Cards (lg:col-span-4) -->
        <div class="lg:col-span-4 flex flex-col sm:flex-row lg:flex-col gap-4 justify-between">
            
            <!-- Side Card 1: Giấy in photo giá sỉ theo thùng -->
            <div class="flex-1 bg-gradient-to-br from-emerald-900/40 via-slate-900 to-slate-900 rounded-3xl p-5 border border-emerald-500/30 shadow-lg relative overflow-hidden group flex flex-col justify-between">
                <div class="space-y-2 relative z-10">
                    <div class="flex items-center justify-between">
                        <span class="px-2.5 py-0.5 bg-emerald-500 text-white font-black text-[10px] rounded-full uppercase tracking-wider">
                            🔥 Bán Chạy Nhất
                        </span>
                        <span class="text-emerald-300 font-bold text-xs">Tiết kiệm sỉ</span>
                    </div>
                    <h3 class="text-base font-black text-white group-hover:text-emerald-300 transition">
                        Giấy In Photo Văn Phòng
                    </h3>
                    <p class="text-xs text-slate-300 leading-snug">
                        Double A, PaperOne, IK Plus 70/80gsm trắng mịn, không kẹt giấy. Mua theo thùng 5 Ram chiết khấu cao.
                    </p>
                </div>

                <div class="pt-4 flex items-center justify-between relative z-10 border-t border-slate-800 mt-3">
                    <div>
                        <span class="text-[10px] text-slate-400 block">Giá từ</span>
                        <span class="text-sm font-black text-emerald-400 font-mono">68.000₫ / Ram</span>
                    </div>
                    <a href="{{ route('storefront.products', ['category' => 'giay-in-photo']) }}" class="px-3.5 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl text-xs font-bold transition flex items-center space-x-1 shadow-sm">
                        <span>Xem Ngay</span>
                        <span>→</span>
                    </a>
                </div>
            </div>

            <!-- Side Card 2: Dịch vụ máy in 30P -->
            <div class="flex-1 bg-gradient-to-br from-blue-950/50 via-slate-900 to-slate-900 rounded-3xl p-5 border border-blue-500/30 shadow-lg relative overflow-hidden group flex flex-col justify-between">
                <div class="space-y-2 relative z-10">
                    <div class="flex items-center justify-between">
                        <span class="px-2.5 py-0.5 bg-amber-400 text-slate-950 font-black text-[10px] rounded-full uppercase tracking-wider">
                            ⚡ Kỹ Thuật Tận Nơi
                        </span>
                        <span class="text-amber-300 font-bold text-xs">Có mặt 30P</span>
                    </div>
                    <h3 class="text-base font-black text-white group-hover:text-amber-300 transition">
                        Nạp Mực & Sửa Máy In 24/7
                    </h3>
                    <p class="text-xs text-slate-300 leading-snug">
                        Canon 2900, HP, Brother tận nơi tại Đồng Nai. Mực siêu mịn, hút mực thải, bảo hành đến giọt cuối cùng.
                    </p>
                </div>

                <div class="pt-4 flex items-center justify-between relative z-10 border-t border-slate-800 mt-3">
                    <div>
                        <span class="text-[10px] text-slate-400 block">Trọn gói chỉ</span>
                        <span class="text-sm font-black text-amber-400 font-mono">80.000₫ / Lần</span>
                    </div>
                    <a href="{{ route('storefront.index') }}#dich-vu-may-in" class="px-3.5 py-2 bg-[#1e3a8a] hover:bg-blue-800 text-white rounded-xl text-xs font-bold transition flex items-center space-x-1 shadow-sm">
                        <span>Báo Giá Sửa</span>
                        <span>→</span>
                    </a>
                </div>
            </div>

        </div>
    </div>

    <!-- 2. CATEGORY ICON CHIPS (DẢI PHÂN LOẠI SIÊU TỐC - 1 CHẠM TRUY CẬP NGAY) -->
    <section aria-label="Danh mục ngành hàng nổi bật" class="bg-white rounded-2xl sm:rounded-3xl p-3 sm:p-5 border border-slate-200/90 shadow-xs">
        <div class="flex items-center justify-between mb-3 px-1">
            <div class="flex items-center space-x-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-xs sm:text-sm font-black text-slate-900 uppercase tracking-wider">Danh Mục Ngành Hàng Nhanh</span>
            </div>
            <a href="{{ route('storefront.products') }}" class="text-[11px] sm:text-xs font-bold text-[#1e3a8a] hover:underline flex items-center space-x-1">
                <span>Tất cả danh mục</span>
                <span>→</span>
            </a>
        </div>

        <!-- Chips Carousel / Flex Grid -->
        <div class="flex items-center gap-2.5 sm:gap-4 overflow-x-auto no-scrollbar scroll-smooth pb-1 pt-0.5">
            <!-- Chip 1: Giấy in photo -->
            <a href="{{ route('storefront.products', ['category' => 'giay-in-photo']) }}" class="flex-shrink-0 flex items-center space-x-2 px-3.5 py-2.5 rounded-2xl bg-emerald-50/80 hover:bg-emerald-100/90 border border-emerald-200/80 text-emerald-950 transition duration-200 group transform hover:-translate-y-0.5">
                <span class="w-8 h-8 rounded-xl bg-white shadow-xs flex items-center justify-center text-base group-hover:scale-110 transition">📄</span>
                <div class="text-left">
                    <span class="block text-xs font-black text-emerald-900 whitespace-nowrap">Giấy In Photo</span>
                    <span class="block text-[10px] text-emerald-700 whitespace-nowrap">Double A, PaperOne</span>
                </div>
            </a>

            <!-- Chip 2: Hộp mực máy in -->
            <a href="{{ route('storefront.products', ['category' => 'hop-muc-may-in']) }}" class="flex-shrink-0 flex items-center space-x-2 px-3.5 py-2.5 rounded-2xl bg-blue-50/80 hover:bg-blue-100/90 border border-blue-200/80 text-blue-950 transition duration-200 group transform hover:-translate-y-0.5">
                <span class="w-8 h-8 rounded-xl bg-white shadow-xs flex items-center justify-center text-base group-hover:scale-110 transition">🖨️</span>
                <div class="text-left">
                    <span class="block text-xs font-black text-blue-900 whitespace-nowrap">Hộp Mực Máy In</span>
                    <span class="block text-[10px] text-blue-700 whitespace-nowrap">Canon, Brother, HP</span>
                </div>
            </a>

            <!-- Chip 3: Bút viết & dụng cụ -->
            <a href="{{ route('storefront.products', ['category' => 'but-viet-muc-viet']) }}" class="flex-shrink-0 flex items-center space-x-2 px-3.5 py-2.5 rounded-2xl bg-amber-50/80 hover:bg-amber-100/90 border border-amber-200/80 text-amber-950 transition duration-200 group transform hover:-translate-y-0.5">
                <span class="w-8 h-8 rounded-xl bg-white shadow-xs flex items-center justify-center text-base group-hover:scale-110 transition">🖊️</span>
                <div class="text-left">
                    <span class="block text-xs font-black text-amber-950 whitespace-nowrap">Bút Viết & Bút Bi</span>
                    <span class="block text-[10px] text-amber-800 whitespace-nowrap">Thiên Long, Bút Gel</span>
                </div>
            </a>

            <!-- Chip 4: Bìa còng & file hồ sơ -->
            <a href="{{ route('storefront.products', ['category' => 'bia-file-ho-so']) }}" class="flex-shrink-0 flex items-center space-x-2 px-3.5 py-2.5 rounded-2xl bg-purple-50/80 hover:bg-purple-100/90 border border-purple-200/80 text-purple-950 transition duration-200 group transform hover:-translate-y-0.5">
                <span class="w-8 h-8 rounded-xl bg-white shadow-xs flex items-center justify-center text-base group-hover:scale-110 transition">📁</span>
                <div class="text-left">
                    <span class="block text-xs font-black text-purple-950 whitespace-nowrap">Bìa Còng & File</span>
                    <span class="block text-[10px] text-purple-700 whitespace-nowrap">Kokuyo, Bìa Lỗ, Nút</span>
                </div>
            </a>

            <!-- Chip 5: Linh kiện máy in -->
            <a href="{{ route('storefront.products', ['category' => 'linh-kien-may-in']) }}" class="flex-shrink-0 flex items-center space-x-2 px-3.5 py-2.5 rounded-2xl bg-sky-50/80 hover:bg-sky-100/90 border border-sky-200/80 text-sky-950 transition duration-200 group transform hover:-translate-y-0.5">
                <span class="w-8 h-8 rounded-xl bg-white shadow-xs flex items-center justify-center text-base group-hover:scale-110 transition">⚙️</span>
                <div class="text-left">
                    <span class="block text-xs font-black text-sky-950 whitespace-nowrap">Trống Drum Linh Kiện</span>
                    <span class="block text-[10px] text-sky-700 whitespace-nowrap">Gạt từ, trục sấy</span>
                </div>
            </a>

            <!-- Chip 6: Thợ sửa máy in 30P -->
            <a href="{{ route('storefront.index') }}#dich-vu-may-in" class="flex-shrink-0 flex items-center space-x-2 px-3.5 py-2.5 rounded-2xl bg-rose-50/80 hover:bg-rose-100/90 border border-rose-200/80 text-rose-950 transition duration-200 group transform hover:-translate-y-0.5">
                <span class="w-8 h-8 rounded-xl bg-white shadow-xs flex items-center justify-center text-base group-hover:scale-110 transition">🛠️</span>
                <div class="text-left">
                    <span class="block text-xs font-black text-rose-950 whitespace-nowrap">Sửa Máy In 30P</span>
                    <span class="block text-[10px] text-rose-700 whitespace-nowrap">Nạp mực tận nơi ĐN</span>
                </div>
            </a>

            <!-- Chip 7: Dụng cụ văn phòng & Băng keo -->
            <a href="{{ route('storefront.products', ['category' => 'dung-cu-van-phong']) }}" class="flex-shrink-0 flex items-center space-x-2 px-3.5 py-2.5 rounded-2xl bg-orange-50/80 hover:bg-orange-100/90 border border-orange-200/80 text-orange-950 transition duration-200 group transform hover:-translate-y-0.5">
                <span class="w-8 h-8 rounded-xl bg-white shadow-xs flex items-center justify-center text-base group-hover:scale-110 transition">📦</span>
                <div class="text-left">
                    <span class="block text-xs font-black text-orange-950 whitespace-nowrap">Băng Keo - Dụng Cụ</span>
                    <span class="block text-[10px] text-orange-700 whitespace-nowrap">Bấm kim, kéo, dao</span>
                </div>
            </a>

            <!-- Chip 8: Báo giá sỉ doanh nghiệp -->
            <a href="{{ route('storefront.wholesale') }}" class="flex-shrink-0 flex items-center space-x-2 px-3.5 py-2.5 rounded-2xl bg-amber-400 hover:bg-amber-300 border border-amber-500 text-slate-950 transition duration-200 group transform hover:-translate-y-0.5 shadow-xs font-black">
                <span class="w-8 h-8 rounded-xl bg-white shadow-xs flex items-center justify-center text-base group-hover:scale-110 transition">⭐</span>
                <div class="text-left">
                    <span class="block text-xs font-black text-slate-950 whitespace-nowrap">Báo Giá Sỉ B2B</span>
                    <span class="block text-[10px] text-slate-800 whitespace-nowrap">Chiết khấu tới 25%</span>
                </div>
            </a>
        </div>
    </section>

    <!-- 3. FOUR CORE COMMITMENTS BAR -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/90 flex items-center space-x-3.5 hover:border-emerald-500 hover:shadow-md transition">
            <div class="w-11 h-11 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-xl shrink-0">
                ⚡
            </div>
            <div>
                <h3 class="text-xs sm:text-sm font-black text-slate-900">Giao Siêu Tốc 2H</h3>
                <p class="text-[11px] text-slate-500 leading-tight mt-0.5">Khu vực Đồng Nai nhận trong 120 phút</p>
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
                    <a href="{{ route('storefront.products') }}" class="inline-block mt-3 px-5 py-2.5 bg-emerald-600 text-white rounded-xl text-xs font-bold shadow">
                        Xem tất cả sản phẩm
                    </a>
                </div>
            @endforelse
        </div>
    </section>
    @endif

    <!-- 4. ⚡ FLASH SALE (SLIDER CỐ ĐỊNH 16 SẢN PHẨM - ĐẾM NGƯỢC THỜI GIAN) -->
    <section aria-label="Flash Sale Khuyến Mãi" class="space-y-4">
        <!-- Flash Sale Header với Countdown Timer & Nút điều khiển Slider -->
        <div class="bg-gradient-to-r from-rose-600 via-rose-500 to-orange-500 rounded-3xl p-4 sm:p-6 shadow-xl relative overflow-hidden">
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
                    <p class="text-xs sm:text-sm text-rose-100 font-semibold pl-1">
                        Cố định 16 sản phẩm giá ưu đãi sốc hôm nay – Giao siêu tốc 2H tại Đồng Nai
                    </p>
                </div>

                <!-- Right: Countdown Timer & Nút trượt slider -->
                <div class="flex items-center justify-between sm:justify-end gap-3 flex-wrap">
                    <!-- Countdown Timer -->
                    <div class="flex items-center space-x-2 bg-black/25 backdrop-blur-md px-3.5 py-2 rounded-2xl border border-white/20">
                        <span class="text-[11px] text-rose-100 font-black uppercase tracking-wider hidden sm:inline">Kết thúc trong:</span>
                        <div id="flashSaleCountdown" class="flex items-center space-x-1">
                            <div class="bg-white/20 rounded-lg px-2 py-1 min-w-[34px] text-center">
                                <span id="countdown-hours" class="text-sm sm:text-base font-black text-white font-mono block leading-none">00</span>
                                <span class="text-[8px] text-rose-100 font-semibold uppercase">Giờ</span>
                            </div>
                            <span class="text-white font-black text-sm">:</span>
                            <div class="bg-white/20 rounded-lg px-2 py-1 min-w-[34px] text-center">
                                <span id="countdown-minutes" class="text-sm sm:text-base font-black text-white font-mono block leading-none">00</span>
                                <span class="text-[8px] text-rose-100 font-semibold uppercase">Phút</span>
                            </div>
                            <span class="text-white font-black text-sm">:</span>
                            <div class="bg-white/20 rounded-lg px-2 py-1 min-w-[34px] text-center">
                                <span id="countdown-seconds" class="text-sm sm:text-base font-black text-white font-mono block leading-none">00</span>
                                <span class="text-[8px] text-rose-100 font-semibold uppercase">Giây</span>
                            </div>
                        </div>
                    </div>

                    <!-- Slider Arrow Controls (Prev / Next) -->
                    <div class="flex items-center space-x-2">
                        <button
                            type="button"
                            onclick="slideFlashSale(-1)"
                            aria-label="Xem sản phẩm trước"
                            class="w-10 h-10 rounded-2xl bg-white/20 hover:bg-white text-white hover:text-slate-900 border border-white/25 flex items-center justify-center text-lg font-black transition duration-200 active:scale-95 shadow-sm"
                            title="Trượt sang trái"
                        >
                            ❮
                        </button>
                        <button
                            type="button"
                            onclick="slideFlashSale(1)"
                            aria-label="Xem sản phẩm tiếp theo"
                            class="w-10 h-10 rounded-2xl bg-white/20 hover:bg-white text-white hover:text-slate-900 border border-white/25 flex items-center justify-center text-lg font-black transition duration-200 active:scale-95 shadow-sm"
                            title="Trượt sang phải"
                        >
                            ❯
                        </button>
                    </div>
                </div>
            </div>

            <!-- Freeship notice -->
            <div class="relative z-10 mt-3 pt-3 border-t border-white/15 flex items-center space-x-2">
                <span class="inline-flex items-center text-[11px] text-white/95 font-bold bg-white/15 px-3 py-1 rounded-full border border-white/20 backdrop-blur-sm">
                    🚚 Miễn phí giao hàng từ 500.000đ
                </span>
                <span class="inline-flex items-center text-[11px] text-white/95 font-bold bg-white/15 px-3 py-1 rounded-full border border-white/20 backdrop-blur-sm hidden sm:inline-flex">
                    🛡️ 100% chính hãng – Đổi trả 7 ngày
                </span>
                <span class="text-rose-100 text-xs font-semibold ml-auto hidden md:inline">
                    Vuốt hoặc bấm nút mũi tên để xem đủ 16 sản phẩm →
                </span>
            </div>
        </div>

        <!-- Flash Sale Slider Track (Cố định 16 sản phẩm dạng slide trượt ngang) -->
        <div class="relative">
            <div
                id="flashSaleTrack"
                class="flex gap-4 overflow-x-auto scroll-smooth no-scrollbar pb-3 pt-1 snap-x snap-mandatory"
            >
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

    <!-- 5. 🏆 SẢN PHẨM BÁN CHẠY NHẤT (TOP BEST SELLERS) -->
    <section aria-label="Sản phẩm bán chạy nhất" class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3 border-b border-slate-200/90 pb-3">
            <div>
                <div class="flex items-center space-x-2">
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black bg-amber-100 text-amber-900 uppercase tracking-wider border border-amber-300">
                        🏆 TOP BÁN CHẠY
                    </span>
                    <span class="text-xs text-slate-500 font-medium hidden sm:inline">• Hơn 500+ doanh nghiệp & trường học tin dùng</span>
                </div>
                <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight mt-1">
                    Sản Phẩm Bán Chạy Nhất Tại Kho
                </h2>
            </div>
            <a href="{{ route('storefront.products', ['sort' => 'popular']) }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800 hover:underline flex items-center space-x-1">
                <span>Xem tất cả sản phẩm bán chạy</span>
                <span>→</span>
            </a>
        </div>

        <!-- Grid 8 sản phẩm bán chạy nhất -->
        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($bestSellerProducts as $p)
                @php
                    $rank = $loop->iteration;
                    $rankBadge = match($rank) {
                        1 => '🥇 TOP 1 BÁN CHẠY',
                        2 => '🥈 TOP 2 BÁN CHẠY',
                        3 => '🥉 TOP 3 BÁN CHẠY',
                        default => '🎖️ TOP BÁN CHẠY',
                    };
                    $rankClass = match($rank) {
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

    <!-- 6. 📦 SẢN PHẨM THEO DANH MỤC CHỦ LỰC -->
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
            <a href="{{ route('storefront.products') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800 hover:underline flex items-center space-x-1">
                <span>Xem tất cả hơn {{ number_format($stats['total_products'] ?? 1000) }}+ sản phẩm</span>
                <span>→</span>
            </a>
        </div>

        @foreach($categorySections as $cat)
        @if($cat->products && $cat->products->count() > 0)
        @php
            $catIcon = match($cat->slug) {
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

                <a
                    href="{{ route('storefront.products', ['category' => $cat->slug]) }}"
                    class="text-xs font-bold text-emerald-700 hover:text-emerald-800 hover:underline flex items-center space-x-1 shrink-0 self-start sm:self-auto"
                >
                    <span>Xem tất cả danh mục này</span>
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
                <a
                    href="{{ route('storefront.products', ['category' => $cat->slug]) }}"
                    class="inline-flex items-center space-x-2 px-6 py-2.5 bg-slate-50 hover:bg-emerald-50 text-slate-700 hover:text-emerald-800 border border-slate-200 hover:border-emerald-300 rounded-xl font-bold text-xs transition shadow-2xs group"
                >
                    <span>Xem thêm toàn bộ sản phẩm {{ $cat->name }}</span>
                    <span class="group-hover:translate-x-1 transition-transform">→</span>
                </a>
            </div>
        </div>
        @endif
        @endforeach
    </section>

    <!-- Countdown Timer & Slider JavaScript -->
    @push('scripts')
    <script>
    (function() {
        // Countdown Timer
        function updateCountdown() {
            const now = new Date();
            const endOfDay = new Date(now);
            endOfDay.setHours(23, 59, 59, 999);
            const diff = endOfDay - now;
            if (diff <= 0) { location.reload(); return; }

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
        window.slideFlashSale = function(direction) {
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

    <!-- 7. CORPORATE WHOLESALE CALL TO ACTION BANNER (B2B SỈ DOANH NGHIỆP) -->
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
                        <span>Bảng báo giá sỉ cạnh tranh nhất Đồng Nai</span>
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

    <!-- 8. DỊCH VỤ NẠP MỰC & SỬA MÁY IN TẬN NƠI TẠI ĐỒNG NAI (LOCAL SEO & BẢNG GIÁ MINH BẠCH) -->
    <div class="bg-gradient-to-br from-slate-900 via-blue-950 to-slate-900 rounded-3xl p-6 sm:p-10 text-white shadow-xl border border-slate-800 space-y-8">
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
                <a href="{{ $storefrontSettings['technical_hotline_url'] ?? $storefrontSettings['hotline_url'] }}" class="px-5 py-3 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-black text-xs sm:text-sm shadow-lg shadow-emerald-500/25 transition flex items-center space-x-2">
                    <span>📞 Gọi Thợ Ngay: {{ $storefrontSettings['technical_hotline'] ?? $storefrontSettings['hotline'] }}</span>
                </a>
            </div>
        </div>

        <!-- Bảng Giá Dịch Vụ Minh Bạch (Rất Tốt Cho AI Search Trích Dẫn) -->
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

        <!-- Cam Kết 4 Điểm Vàng -->
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

    <!-- 7. KHỐI CÂU HỎI THƯỜNG GẶP (FAQ SECTION - SEO & AI SEARCH OVERVIEW) -->
    <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-sm border border-slate-200 space-y-6">
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

    <!-- 8. LOOKUP REPAIR TICKET SHORTCUT BANNER -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200 flex flex-col md:flex-row items-center justify-between gap-6">
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
            <a href="{{ route('lookup.index') }}" class="w-full md:w-auto inline-flex items-center justify-center space-x-2 px-6 py-3 bg-[#1e3a8a] hover:bg-blue-900 text-white font-extrabold text-xs sm:text-sm rounded-xl shadow transition">
                <span>Tra Cứu Phiếu Tiếp Nhận Ngay</span>
                <span>→</span>
            </a>
        </div>
    </div>

</main>
@endsection
