@extends('layouts.storefront')

@section('title', 'Giới Thiệu Về Cửa Hàng & Trung Tâm Kỹ Thuật | ' . ($storefrontSettings['site_name'] ?? 'VPP & Thiết Bị Máy In'))
@section('meta_description', 'Khám phá năng lực cung ứng hơn 1.000 SKU văn phòng phẩm giá sỉ và dịch vụ nạp mực, sửa chữa máy in tận nơi chuyên nghiệp tại Bình Hòa, Đồng Nai.')

@section('schema_extra')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@type": "AboutPage",
  "mainEntity": {
    "@type": "LocalBusiness",
    "name": "{{ $storefrontSettings['site_name'] ?? 'VPP & Thiết Bị Máy In' }}",
    "image": "{{ !empty($storefrontSettings['logo_url']) ? $storefrontSettings['logo_url'] : asset('favicon.ico') }}",
    "telephone": "{{ $storefrontSettings['hotline'] ?? '0974.194.305' }}",
    "address": {
      "@type": "PostalAddress",
      "streetAddress": "{{ $storefrontSettings['address'] ?? '30 Bình Hòa' }}",
      "addressLocality": "Bình Hòa",
      "addressRegion": "Đồng Nai",
      "addressCountry": "VN"
    },
    "geo": {
      "@type": "GeoCoordinates",
      "latitude": "10.9984",
      "longitude": "106.8458"
    },
    "url": "{{ route('storefront.about') }}",
    "openingHours": "Mo-Sa 07:30-18:30"
  }
}
</script>
@endsection

@section('content')
<main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 space-y-10 flex-1">
    
    <!-- Breadcrumbs -->
    <nav class="flex items-center space-x-2 text-xs text-slate-500 font-medium">
        <a href="{{ route('storefront.index') }}" class="hover:text-indigo-600">Trang chủ</a>
        <span>/</span>
        <span class="text-slate-900 font-bold">Giới thiệu cửa hàng</span>
    </nav>

    <!-- Hero Visual Banner Box -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-950 via-slate-900 to-blue-950 text-white p-6 sm:p-12 shadow-2xl border border-indigo-800/80">
        <div class="relative z-10 max-w-3xl space-y-4">
            <div class="inline-flex items-center space-x-2 bg-amber-400 text-slate-950 px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider shadow">
                <span>🏢 ĐỐI TÁC VĂN PHÒNG & THIẾT BỊ IN ẤN TIN CẬY</span>
            </div>
            <h1 class="text-2xl sm:text-4xl lg:text-5xl font-black text-white leading-tight">
                Giải Pháp Toàn Diện Cho Văn Phòng & Dịch Vụ Máy In Doanh Nghiệp
            </h1>
            <p class="text-xs sm:text-base text-slate-300 leading-relaxed font-normal">
                Đồng hành cùng hàng nghìn doanh nghiệp, cơ quan nhà nước, trường học và hộ kinh doanh tại <b>Đồng Nai & khu vực lân cận</b>. Chúng tôi kết hợp hoàn hảo giữa <b>kho văn phòng phẩm hơn 1.000 SKU</b> giá sỉ và <b>trung tâm kỹ thuật sửa chữa máy in tận nơi</b> trong 30 phút.
            </p>
            <div class="pt-2 flex flex-wrap gap-4 text-xs font-bold">
                <span class="flex items-center space-x-1.5 text-emerald-400">
                    <span>✔</span> <span>100% Sản phẩm chính hãng</span>
                </span>
                <span class="flex items-center space-x-1.5 text-amber-300">
                    <span>✔</span> <span>Kỹ thuật viên lành nghề</span>
                </span>
                <span class="flex items-center space-x-1.5 text-sky-300">
                    <span>✔</span> <span>Giao hàng hỏa tốc trong ngày</span>
                </span>
            </div>
        </div>

        <div class="absolute -right-10 -bottom-10 w-72 h-72 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute right-1/3 -top-10 w-60 h-60 bg-emerald-500/20 rounded-full blur-3xl pointer-events-none"></div>
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
            <span class="block text-2xl sm:text-3xl font-black text-amber-500 font-mono">30 Phút</span>
            <span class="text-xs text-slate-500 font-semibold mt-1 block">Có Mặt Nạp Mực Tận Nơi</span>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 text-center">
            <span class="block text-2xl sm:text-3xl font-black text-rose-600 font-mono">100%</span>
            <span class="text-xs text-slate-500 font-semibold mt-1 block">Hóa Đơn VAT Điện Tử</span>
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
                    Phân phối chính hãng trực tiếp từ các thương hiệu hàng đầu: <b>Double A, PaperOne, IK Plus, Thiên Long, Kokuyo, Plus, Kingjim</b>. Hệ thống kho quy mô lớn sẵn sàng cung ứng trọn gói:
                </p>
                <ul class="text-xs text-slate-600 space-y-2 pl-4 list-disc">
                    <li><b>Giấy in photo văn phòng:</b> Khổ A4, A3, A5 định lượng 70gsm, 80gsm bán lẻ theo Ram và chiết khấu cực sâu theo Thùng.</li>
                    <li><b>Bút viết & Dụng cụ:</b> Bút bi, bút gel, bút lông bảng, bấm kim, kẹp giấy, băng keo dán thùng.</li>
                    <li><b>Quản lý hồ sơ:</b> Bìa còng, bìa lá, bìa nút My Clear, file lưu trữ chứng từ chuẩn văn phòng.</li>
                    <li><b>Hóa đơn & Hợp đồng:</b> Hỗ trợ xuất hóa đơn VAT điện tử và công nợ 30 ngày cho khách hàng doanh nghiệp định kỳ.</li>
                </ul>
            </div>
            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                <span class="text-xs text-emerald-600 font-bold">✔ Hỗ trợ công nợ doanh nghiệp</span>
                <a href="{{ route('storefront.products') }}" class="text-xs text-indigo-600 font-bold hover:underline">Xem danh mục →</a>
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
                    <li><b>Nạp mực tận nơi trong 30 phút:</b> Mực in siêu mịn, bản in đậm nét, không đổ mực thải bừa bãi.</li>
                    <li><b>Thay thế linh kiện chính hãng:</b> Trống drum, gạt mực, trục sạc, bao lụa sấy với tem bảo hành minh bạch.</li>
                    <li><b>Tiếp nhận minh bạch:</b> Quy trình nhận máy lập phiếu kiểm tra tình trạng trong 60 giây, không tráo đổi linh kiện.</li>
                    <li><b>Tra cứu tiến độ online:</b> Khách hàng dễ dàng kiểm tra tình trạng sửa chữa, linh kiện thay và chi phí trực tiếp trên website.</li>
                </ul>
            </div>
            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                <span class="text-xs text-amber-600 font-bold">✔ Bảo hành đến hết hạt mực</span>
                <a href="{{ route('lookup.index') }}" class="text-xs text-indigo-600 font-bold hover:underline">Tra cứu phiếu →</a>
            </div>
        </div>

    </div>

    <!-- Showroom & Google Maps Local Section -->
    <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-sm border border-slate-200 space-y-6">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 border-b border-slate-100 pb-5">
            <div>
                <span class="inline-block px-3 py-1 bg-indigo-100 text-indigo-700 font-bold rounded-full text-xs">
                    📍 VỊ TRÍ CỬA HÀNG & LIÊN HỆ
                </span>
                <h3 class="text-xl sm:text-2xl font-black text-slate-900 mt-2">
                    Trung Tâm Cung Ứng & Kỹ Thuật Tại Đồng Nai
                </h3>
            </div>
            <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($storefrontSettings['address'] ?? '30 Bình Hòa, Đồng Nai') }}" target="_blank" rel="noopener noreferrer"
               class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs shadow transition flex items-center space-x-2">
                <span>🗺️ Mở Bản Đồ Chỉ Đường</span>
                <span>→</span>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <!-- Thông tin liên hệ chi tiết (5 cols) -->
            <div class="lg:col-span-5 space-y-4 text-xs sm:text-sm">
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                    <p class="flex items-start space-x-2">
                        <span class="text-indigo-600 font-bold shrink-0">🏢 Địa chỉ:</span>
                        <span class="text-slate-800 font-medium">{{ $storefrontSettings['address'] }}</span>
                    </p>
                    <p class="flex items-start space-x-2">
                        <span class="text-indigo-600 font-bold shrink-0">⏰ Giờ mở cửa:</span>
                        <span class="text-slate-800 font-medium">{{ $storefrontSettings['opening_hours'] }}</span>
                    </p>
                    <p class="flex items-start space-x-2">
                        <span class="text-indigo-600 font-bold shrink-0">📞 Hotline bán hàng:</span>
                        <a href="{{ $storefrontSettings['hotline_url'] }}" class="text-rose-600 font-bold hover:underline">{{ $storefrontSettings['hotline'] }}</a>
                    </p>
                    @if(!empty($storefrontSettings['technical_hotline']))
                    <p class="flex items-start space-x-2">
                        <span class="text-indigo-600 font-bold shrink-0">🛠️ Hotline kỹ thuật:</span>
                        <a href="{{ $storefrontSettings['technical_hotline_url'] }}" class="text-emerald-600 font-bold hover:underline">{{ $storefrontSettings['technical_hotline'] }}</a>
                    </p>
                    @endif
                    <p class="flex items-start space-x-2">
                        <span class="text-indigo-600 font-bold shrink-0">💬 Zalo đặt hàng:</span>
                        <a href="{{ $storefrontSettings['zalo_url'] }}" target="_blank" class="text-sky-600 font-bold hover:underline">{{ $storefrontSettings['zalo'] }}</a>
                    </p>
                    <p class="flex items-start space-x-2">
                        <span class="text-indigo-600 font-bold shrink-0">✉️ Email:</span>
                        <span class="text-slate-800 font-medium">{{ $storefrontSettings['email'] }}</span>
                    </p>
                </div>

                <div class="p-4 rounded-2xl bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-200 text-xs text-emerald-900 space-y-1">
                    <p class="font-bold flex items-center space-x-1 text-emerald-800">
                        <span>🚚</span>
                        <span>Phục vụ giao hàng & Kỹ thuật tận nơi tại:</span>
                    </p>
                    <p class="text-slate-600 leading-relaxed text-[11px]">
                        Bình Hòa, TP. Biên Hòa, Vĩnh Cửu, Trảng Bom, Long Thành, Nhơn Trạch và các KCN lân cận trong bán kính 20km.
                    </p>
                </div>
            </div>

            <!-- Google Maps Embed (7 cols) -->
            <div class="lg:col-span-7 rounded-2xl overflow-hidden border border-slate-200 shadow-sm bg-slate-100 min-h-[300px]">
                <iframe src="https://maps.google.com/maps?q={{ urlencode($storefrontSettings['address'] ?? '30 Bình Hòa, Đồng Nai') }}&t=&z=14&ie=UTF8&iwloc=&output=embed"
                    class="w-full h-80 sm:h-96 border-0" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>
    </div>

</main>
@endsection
