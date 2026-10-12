@extends('layouts.storefront')

@section('title', 'Dịch Vụ Máy In Tận Nơi & Cung Ứng VPP Doanh Nghiệp B2B Đồng Nai | ' . ($storefrontSettings['site_name'] ?? 'VPP & Thiết Bị Máy In'))
@section('meta_description', 'Chuyên nghiệp: Nạp mực & sửa máy in tận nơi trong 30 phút tại Đồng Nai. Cung ứng văn phòng phẩm trọn gói cho doanh nghiệp, KCN, trường học chiết khấu tới 25%, xuất hóa đơn VAT 100%.')

@section('schema_extra')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@graph": [
    {
      "@type": "LocalBusiness",
      "@id": "{{ url('/') }}#business",
      "name": "{{ $storefrontSettings['site_name'] ?? 'VPP & Dịch Vụ Máy In Đồng Nai' }}",
      "url": "{{ url('/') }}",
      "telephone": "{{ $storefrontSettings['hotline'] ?? '0901234567' }}",
      "priceRange": "$$",
      "image": "{{ asset('images/banners/stationery_store_bright.jpg') }}",
      "description": "Trung tâm dịch vụ nạp mực máy in, sửa chữa máy in tận nơi 30 phút và tổng kho văn phòng phẩm chiết khấu cao cho doanh nghiệp tại Biên Hòa, Đồng Nai.",
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "{{ $storefrontSettings['address'] ?? 'Đồng Nai' }}",
        "addressLocality": "Biên Hòa",
        "addressRegion": "Đồng Nai",
        "addressCountry": "VN"
      },
      "openingHoursSpecification": {
        "@type": "OpeningHoursSpecification",
        "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"],
        "opens": "07:30",
        "closes": "20:00"
      }
    },
    {
      "@type": "FAQPage",
      "@id": "{{ url('/') }}#faq",
      "mainEntity": [
        {
          "@type": "Question",
          "name": "Kỹ thuật viên nạp mực máy in tận nơi có mặt sau bao lâu tại Đồng Nai?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Đội ngũ kỹ thuật viên thường trực tại các trạm Biên Hòa, Amata, Long Thành, Vĩnh Cửu cam kết có mặt tận nơi sau 15 đến 30 phút từ khi tiếp nhận cuộc gọi."
          }
        },
        {
          "@type": "Question",
          "name": "Doanh nghiệp mua văn phòng phẩm và nạp mực có được xuất hóa đơn VAT và công nợ không?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "100% dịch vụ và sản phẩm đều được xuất hóa đơn điện tử GTGT hợp lệ trong ngày. Khách hàng doanh nghiệp ký hợp đồng được áp dụng chính sách công nợ gối đầu 30 ngày linh hoạt."
          }
        },
        {
          "@type": "Question",
          "name": "Chính sách cho thuê máy in doanh nghiệp hoạt động như thế nào?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Khách hàng không mất chi phí đầu tư mua máy (0đ đặt cọc), được miễn phí toàn bộ mực in và bảo trì hàng tháng. Nếu máy gặp sự cố, chúng tôi hỗ trợ đổi máy mới tương đương trong 2 giờ."
          }
        }
      ]
    }
  ]
}
</script>
@endsection

@section('content')
<main class="storefront-home max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 sm:py-6 space-y-6 sm:space-y-10 w-full">

    <!-- 1. SPLIT HERO SECTION TỶ LỆ KÉP: B2B PROCUREMENT (TRÁI) & CỨU HỘ MÁY IN 30 PHÚT (PHẢI) -->
    <section class="grid grid-cols-1 lg:grid-cols-12 gap-5 sm:gap-6 items-stretch" aria-labelledby="b2b-hero-title">
        
        <!-- Cột Trái: Cung Ứng VPP Trọn Gói Doanh Nghiệp (lg:col-span-7) -->
        <div class="lg:col-span-7 bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 sm:p-9 text-white shadow-xl border border-indigo-900/60 flex flex-col justify-between relative overflow-hidden group">
            <!-- Background Decorative Glow -->
            <div class="absolute -top-24 -right-24 w-72 h-72 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -left-24 w-72 h-72 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="space-y-4 relative z-10">
                <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-indigo-500/20 text-indigo-300 border border-indigo-400/30 text-[11px] font-black uppercase tracking-wider">
                    <span>🏢</span>
                    <span>GIẢI PHÁP VPP & VẬT TƯ IN ẤN DOANH NGHIỆP</span>
                </div>

                <h1 id="b2b-hero-title" class="text-2xl sm:text-4xl font-black text-white leading-tight tracking-tight">
                    Cung Ứng Văn Phòng Phẩm & Dịch Vụ Máy In Trọn Gói Cho Doanh Nghiệp
                </h1>

                <p class="text-xs sm:text-sm text-slate-300 leading-relaxed max-w-xl">
                    Tiết kiệm đến <b>25% chi phí vận hành hàng tháng</b>. Đối tác tin cậy của hơn <b>500+ doanh nghiệp, nhà máy KCN và trường học</b> tại Đồng Nai. Hỗ trợ công nợ gối đầu 30 ngày, xuất hóa đơn VAT điện tử hợp lệ 100%.
                </p>

                <!-- 3 Huy hiệu cam kết cốt lõi -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 pt-2">
                    <div class="p-3 bg-white/5 backdrop-blur-md rounded-2xl border border-white/10 flex items-center space-x-2.5">
                        <span class="text-2xl">📦</span>
                        <div>
                            <strong class="text-xs block text-white font-black leading-tight">Chiết Khấu 25%</strong>
                            <span class="text-[10px] text-slate-400">Theo hợp đồng sỉ định kỳ</span>
                        </div>
                    </div>

                    <div class="p-3 bg-white/5 backdrop-blur-md rounded-2xl border border-white/10 flex items-center space-x-2.5">
                        <span class="text-2xl">🧾</span>
                        <div>
                            <strong class="text-xs block text-emerald-400 font-black leading-tight">VAT 100% Trong 15p</strong>
                            <span class="text-[10px] text-slate-400">Hóa đơn điện tử hợp lệ</span>
                        </div>
                    </div>

                    <div class="p-3 bg-white/5 backdrop-blur-md rounded-2xl border border-white/10 flex items-center space-x-2.5">
                        <span class="text-2xl">💳</span>
                        <div>
                            <strong class="text-xs block text-amber-300 font-black leading-tight">Công Nợ 30 Ngày</strong>
                            <span class="text-[10px] text-slate-400">Thanh toán cuối tháng</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nút Hành Động Trái -->
            <div class="pt-6 flex flex-wrap gap-3 relative z-10">
                <a href="#b2b-pricing-catalog" class="py-3 px-5 rounded-2xl bg-emerald-500 hover:bg-emerald-600 active:scale-95 text-slate-950 font-black text-xs sm:text-sm shadow-lg transition flex items-center space-x-2">
                    <span>📋</span>
                    <span>XEM BẢNG BÁO GIÁ SỈ THEO THÙNG</span>
                </a>

                <a href="#b2b-contract-form" class="py-3 px-5 rounded-2xl bg-white/10 hover:bg-white/20 active:scale-95 text-white font-bold text-xs sm:text-sm border border-white/20 transition flex items-center space-x-2">
                    <span>✍️</span>
                    <span>ĐĂNG KÝ HỢP ĐỒNG B2B</span>
                </a>
            </div>
        </div>

        <!-- Cột Phải: Cứu Hộ & Nạp Mực Máy In Tận Nơi 30 Phút (lg:col-span-5) -->
        <div class="lg:col-span-5 bg-gradient-to-br from-amber-500 via-orange-600 to-rose-600 rounded-3xl p-6 sm:p-8 text-white shadow-xl flex flex-col justify-between relative overflow-hidden">
            <!-- Background Shape -->
            <div class="absolute -right-12 -bottom-12 w-48 h-48 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>

            <div class="space-y-3 relative z-10">
                <div class="flex items-center justify-between">
                    <span class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full bg-white/20 text-white text-[10px] font-black uppercase tracking-wider backdrop-blur-md">
                        <span class="w-2 h-2 rounded-full bg-emerald-300 animate-ping"></span>
                        <span>CỨU HỘ MÁY IN HỎA TỐC 30 PHÚT</span>
                    </span>
                    <span class="text-[11px] font-bold text-amber-100 font-mono">ĐỒNG NAI</span>
                </div>

                <h2 class="text-xl sm:text-2xl font-black text-white leading-snug">
                    Nạp Mực & Sửa Chữa Máy In Tận Nơi Trong 30 Phút
                </h2>

                <p class="text-xs text-amber-100 leading-relaxed">
                    Kỹ thuật viên chuyên nghiệp có mặt sau 15–30 phút tại Biên Hòa, KCN Amata, Vĩnh Cửu, Long Thành. Mực siêu mịn, bản in đen đậm chuẩn hãng, bảo hành tận nơi.
                </p>

                <!-- Form Đặt Thợ Nhanh 1 Phút Trực Tiếp Trên Hero -->
                <div class="bg-white/95 backdrop-blur-md rounded-2xl p-4 text-slate-900 shadow-lg space-y-3 mt-2">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                        <h3 class="text-xs font-black text-slate-900 uppercase flex items-center space-x-1.5">
                            <span>⚡</span>
                            <span>Đặt Kỹ Thuật Viên Tận Nơi Nhanh</span>
                        </h3>
                        <span class="text-[10px] text-emerald-700 font-bold">Giá chỉ từ 80k</span>
                    </div>

                    <form action="{{ route('storefront.book') }}" method="POST" class="space-y-2.5">
                        @csrf
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-700 mb-0.5">Họ và tên *</label>
                                <input type="text" name="customer_name" required placeholder="Nguyễn Văn A" class="w-full text-xs px-2.5 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 focus-visible:outline-none bg-slate-50 font-medium" />
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-700 mb-0.5">Số điện thoại *</label>
                                <input type="tel" name="customer_phone" required placeholder="0901 234 567" class="w-full text-xs px-2.5 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 focus-visible:outline-none bg-slate-50 font-medium font-mono" />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-700 mb-0.5">Dòng máy in *</label>
                                <select name="printer_model_id" class="w-full text-xs px-2 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 focus-visible:outline-none bg-slate-50 font-medium">
                                    <option value="">Chọn máy in...</option>
                                    @foreach($printerModels as $pm)
                                    <option value="{{ $pm->id }}">{{ $pm->brand }} {{ $pm->model_name }}</option>
                                    @endforeach
                                    <option value="">Khác / Không rõ mã máy</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-700 mb-0.5">Nhu cầu dịch vụ *</label>
                                <select name="issue_description" class="w-full text-xs px-2 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 focus-visible:outline-none bg-slate-50 font-medium">
                                    <option value="Bơm mực / Nạp mực máy in tận nơi">Bơm mực / Nạp mực (80k - 120k)</option>
                                    <option value="Máy in kẹt giấy / Không kéo giấy">Sửa lỗi kẹt giấy / Không kéo giấy</option>
                                    <option value="Bản in mờ / Sọc đen / Đốm chấm">Bản in mờ / Sọc đen / Lem mực</option>
                                    <option value="Thay linh kiện trống Drum / Gạt / Trục từ">Thay linh kiện trống Drum / Gạt</option>
                                    <option value="Cần tư vấn thuê máy in doanh nghiệp 0đ">Tư vấn thuê máy in văn phòng (0đ)</option>
                                </select>
                            </div>
                        </div>

                        <input type="hidden" name="device_name" value="Máy in văn phòng cần kỹ thuật xử lý" />

                        <button type="submit" class="w-full py-2.5 px-4 bg-gradient-to-r from-orange-600 to-rose-600 hover:from-orange-700 hover:to-rose-700 active:scale-98 text-white font-black text-xs rounded-xl shadow-md transition flex items-center justify-center space-x-1.5 cursor-pointer">
                            <span>🚀</span>
                            <span>ĐẶT THỢ NGAY (CÓ MẶT SAU 30 PHÚT)</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Hotline trực tiếp góc dưới -->
            <div class="pt-3 flex items-center justify-between text-xs text-amber-100 border-t border-white/20 mt-2">
                <span>Cần thợ gấp trong 15 phút? Gọi ngay:</span>
                <a href="{{ $storefrontSettings['hotline_url'] ?? 'tel:0901234567' }}" class="font-black text-white hover:underline text-sm font-mono">
                    📞 {{ $storefrontSettings['hotline'] ?? '0901 234 567' }}
                </a>
            </div>
        </div>

    </section>

    <!-- 2. THANH TRA CỨU ĐA NĂNG 3-TRONG-1 (MULTI-UTILITY LOOKUP BAR) -->
    <section class="bg-white rounded-3xl p-5 sm:p-7 border border-slate-200/90 shadow-sm" x-data="{ activeTab: 'printers' }" aria-label="Công cụ tra cứu tiện ích">
        <!-- Tab Headers -->
        <div class="flex items-center space-x-2 border-b border-slate-200 pb-3 overflow-x-auto no-scrollbar">
            <button type="button" @click="activeTab = 'printers'" :class="activeTab === 'printers' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-4 py-2 rounded-xl font-black text-xs transition flex items-center space-x-1.5 shrink-0 cursor-pointer">
                <span>🖨️</span>
                <span>Tra Mã Máy In Tìm Mực & Linh Kiện</span>
            </button>

            <button type="button" @click="activeTab = 'tickets'" :class="activeTab === 'tickets' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-4 py-2 rounded-xl font-black text-xs transition flex items-center space-x-1.5 shrink-0 cursor-pointer">
                <span>🔍</span>
                <span>Tra Cứu Phiếu Sửa Chữa & Đơn Hàng</span>
            </button>

            <button type="button" @click="activeTab = 'estimate'" :class="activeTab === 'estimate' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-4 py-2 rounded-xl font-black text-xs transition flex items-center space-x-1.5 shrink-0 cursor-pointer">
                <span>💰</span>
                <span>Dự Toán Chi Phí Nạp Mực & Sửa Chữa</span>
            </button>
        </div>

        <!-- Tab 1: Tra mã máy in lấy hộp mực tương thích -->
        <div x-show="activeTab === 'printers'" class="pt-4 space-y-3">
            <p class="text-xs text-slate-600">
                Chọn dòng máy in để xem <b>hộp mực tương thích, trống in, gạt mực</b> và giá nạp mực:
            </p>
            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-2">
                @foreach($printerModels as $pm)
                <a href="{{ route('storefront.products', ['printer' => $pm->id]) }}" class="p-2.5 rounded-xl bg-slate-50 hover:bg-indigo-50 border border-slate-200 hover:border-indigo-400 text-center transition group">
                    <span class="block text-[10px] font-bold text-slate-400 uppercase">{{ $pm->brand }}</span>
                    <strong class="text-xs text-slate-800 group-hover:text-indigo-600 font-bold block truncate">{{ $pm->model_name }}</strong>
                </a>
                @endforeach
            </div>
        </div>

        <!-- Tab 2: Tra cứu tiến độ sửa chữa & đơn hàng (Anti-IDOR) -->
        <div x-show="activeTab === 'tickets'" x-cloak class="pt-4 max-w-xl mx-auto space-y-3 text-center">
            <p class="text-xs text-slate-600">
                Nhập <b>Mã phiếu sửa chữa</b> (hoặc Mã đơn hàng) cùng <b>4 số cuối số điện thoại</b> đã đăng ký để kiểm tra tiến độ kỹ thuật và hóa đơn:
            </p>
            <form action="{{ route('lookup.search') }}" method="POST" class="flex flex-col sm:flex-row gap-2">
                @csrf
                <input type="text" name="code" required placeholder="Mã phiếu (VD: SC-1234 hoặc HD-5678)" class="flex-1 text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 focus-visible:outline-none bg-slate-50 font-bold uppercase" />
                <input type="text" name="phone_last4" required maxlength="4" placeholder="4 số cuối SĐT" class="w-full sm:w-36 text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 focus-visible:outline-none bg-slate-50 font-mono font-bold text-center" />
                <button type="submit" class="py-2.5 px-5 bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs rounded-xl shadow-xs transition shrink-0 cursor-pointer">
                    Tra Cứu Ngay →
                </button>
            </form>
        </div>

        <!-- Tab 3: Dự toán chi phí nạp mực & sửa chữa -->
        <div x-show="activeTab === 'estimate'" x-cloak class="pt-4 space-y-3" x-data="{ brand: 'canon', part: 'ink' }">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200">
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">1. Hãng máy in:</label>
                    <select x-model="brand" class="w-full text-xs p-2 rounded-xl border border-slate-300 bg-white">
                        <option value="canon">Canon (2900, 3300, 6230...)</option>
                        <option value="hp">HP LaserJet (107a, M402, M404...)</option>
                        <option value="brother">Brother (2321D, 2366, B7535...)</option>
                        <option value="epson">Epson (In phun màu L3110, L3210...)</option>
                    </select>
                </div>

                <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200">
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">2. Hạng mục kỹ thuật:</label>
                    <select x-model="part" class="w-full text-xs p-2 rounded-xl border border-slate-300 bg-white">
                        <option value="ink">Nạp mực tận nơi (Bao gồm vệ sinh máy)</option>
                        <option value="drum">Thay trống in (Drum) mới 100%</option>
                        <option value="blade">Thay gạt mực lớn / gạt nhỏ</option>
                        <option value="mag">Thay trục từ / Trục cao su</option>
                        <option value="fuser">Thay bao lụa sấy / Lô ép</option>
                        <option value="roller">Thay quả đào kéo giấy (Sửa lỗi kẹt giấy)</option>
                    </select>
                </div>

                <div class="p-3.5 bg-emerald-50 rounded-2xl border border-emerald-200 flex flex-col justify-between">
                    <div>
                        <span class="text-[10px] text-emerald-800 font-bold block uppercase">Chi phí dự kiến trọn gói:</span>
                        <div class="text-xl font-black text-emerald-700 font-mono mt-0.5">
                            <span x-show="part === 'ink'">80.000₫ - 120.000₫</span>
                            <span x-show="part === 'drum'">130.000₫ - 180.000₫</span>
                            <span x-show="part === 'blade'">60.000₫ - 90.000₫</span>
                            <span x-show="part === 'mag'">90.000₫ - 140.000₫</span>
                            <span x-show="part === 'fuser'">220.000₫ - 350.000₫</span>
                            <span x-show="part === 'roller'">120.000₫ - 180.000₫</span>
                        </div>
                    </div>
                    <span class="text-[10px] text-emerald-600 font-semibold">✓ Đã gồm công thợ tận nơi & Bảo hành đậm đẹp</span>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. KHỐI 4 DỊCH VỤ KỸ THUẬT CỐT LÕI (CORE TECHNICAL SERVICES 4-GRID) -->
    <section class="space-y-4" aria-labelledby="core-services-title">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <span class="w-2.5 h-6 rounded-full bg-[#1e3a8a]"></span>
                <h2 id="core-services-title" class="text-lg sm:text-xl font-black text-slate-900">
                    Hệ Thống Dịch Vụ Kỹ Thuật Máy In & Giải Pháp Doanh Nghiệp
                </h2>
            </div>
            <a href="{{ route('storefront.about') }}" class="text-xs text-[#1e3a8a] font-bold hover:underline flex items-center space-x-1">
                <span>Cam kết dịch vụ</span>
                <span>→</span>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- Dịch vụ 1 -->
            <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-xs hover:shadow-md transition flex flex-col justify-between group">
                <div class="space-y-2.5">
                    <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center text-2xl group-hover:scale-110 transition">
                        🖨️
                    </div>
                    <h3 class="font-black text-slate-900 text-sm sm:text-base leading-snug">
                        Nạp Mực Máy In Tận Nơi (Laser & Phun Màu)
                    </h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Mực in siêu mịn công nghệ Nhật Bản, trang in sắc nét, không lem, không hại đầu phun. Vệ sinh máy in và hút mực thải miễn phí.
                    </p>
                    <ul class="text-[11px] text-slate-600 space-y-1 pt-1">
                        <li class="flex items-center space-x-1.5"><span class="text-emerald-500">✓</span><span>Có mặt sau 15–30 phút</span></li>
                        <li class="flex items-center space-x-1.5"><span class="text-emerald-500">✓</span><span>Bảo hành đến hạt mực cuối cùng</span></li>
                    </ul>
                </div>
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between mt-3">
                    <div>
                        <span class="text-[10px] text-slate-400 block">Đơn giá từ</span>
                        <strong class="font-mono text-emerald-700 font-black text-sm">80.000₫</strong>
                    </div>
                    <a href="#b2b-quick-booking" class="px-3 py-1.5 bg-slate-100 hover:bg-amber-100 text-slate-700 hover:text-amber-900 font-bold text-xs rounded-xl transition">
                        Đặt thợ →
                    </a>
                </div>
            </div>

            <!-- Dịch vụ 2 -->
            <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-xs hover:shadow-md transition flex flex-col justify-between group">
                <div class="space-y-2.5">
                    <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center text-2xl group-hover:scale-110 transition">
                        🛠️
                    </div>
                    <h3 class="font-black text-slate-900 text-sm sm:text-base leading-snug">
                        Sửa Chữa Máy In & Thay Thế Linh Kiện
                    </h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Xử lý triệt để lỗi kẹt giấy, kêu to, bản in có sọc, không nhận lệnh in. Thay thế trống Drum, gạt, trục từ, bao lụa chính hãng 100%.
                    </p>
                    <ul class="text-[11px] text-slate-600 space-y-1 pt-1">
                        <li class="flex items-center space-x-1.5"><span class="text-emerald-500">✓</span><span>Báo giá trước khi sửa</span></li>
                        <li class="flex items-center space-x-1.5"><span class="text-emerald-500">✓</span><span>Bảo hành linh kiện 1-đổi-1</span></li>
                    </ul>
                </div>
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between mt-3">
                    <div>
                        <span class="text-[10px] text-slate-400 block">Đơn giá từ</span>
                        <strong class="font-mono text-blue-700 font-black text-sm">120.000₫</strong>
                    </div>
                    <a href="#b2b-quick-booking" class="px-3 py-1.5 bg-slate-100 hover:bg-blue-100 text-slate-700 hover:text-blue-900 font-bold text-xs rounded-xl transition">
                        Khám máy →
                    </a>
                </div>
            </div>

            <!-- Dịch vụ 3 -->
            <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-xs hover:shadow-md transition flex flex-col justify-between group">
                <div class="space-y-2.5">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-2xl group-hover:scale-110 transition">
                        🏢
                    </div>
                    <h3 class="font-black text-slate-900 text-sm sm:text-base leading-snug">
                        Cho Thuê Máy In Doanh Nghiệp (0đ Đầu Tư)
                    </h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Không cần bỏ vốn mua máy in. Miễn phí 100% mực in, bảo trì hàng tháng. Đổi máy mới tương đương trong 2 giờ nếu máy có sự cố kỹ thuật.
                    </p>
                    <ul class="text-[11px] text-slate-600 space-y-1 pt-1">
                        <li class="flex items-center space-x-1.5"><span class="text-emerald-500">✓</span><span>0đ tiền máy ban đầu</span></li>
                        <li class="flex items-center space-x-1.5"><span class="text-emerald-500">✓</span><span>Chỉ trả tiền theo trang in</span></li>
                    </ul>
                </div>
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between mt-3">
                    <div>
                        <span class="text-[10px] text-slate-400 block">Chỉ từ</span>
                        <strong class="font-mono text-emerald-700 font-black text-sm">490.000₫/tháng</strong>
                    </div>
                    <a href="#b2b-contract-form" class="px-3 py-1.5 bg-slate-100 hover:bg-emerald-100 text-slate-700 hover:text-emerald-900 font-bold text-xs rounded-xl transition">
                        Tư vấn thuê →
                    </a>
                </div>
            </div>

            <!-- Dịch vụ 4 -->
            <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-xs hover:shadow-md transition flex flex-col justify-between group">
                <div class="space-y-2.5">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-2xl group-hover:scale-110 transition">
                        📜
                    </div>
                    <h3 class="font-black text-slate-900 text-sm sm:text-base leading-snug">
                        Gói Thầu Cung Ứng VPP Trọn Gói Định Kỳ
                    </h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Giao hàng định kỳ tận nơi cho nhà xưởng KCN, trường học, bệnh viện. Danh mục giấy in, bút viết, file còng đồng bộ với giá sỉ cấp 1.
                    </p>
                    <ul class="text-[11px] text-slate-600 space-y-1 pt-1">
                        <li class="flex items-center space-x-1.5"><span class="text-emerald-500">✓</span><span>Chiết khấu cao tới 25%</span></li>
                        <li class="flex items-center space-x-1.5"><span class="text-emerald-500">✓</span><span>Công nợ gối đầu 30 ngày</span></li>
                    </ul>
                </div>
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between mt-3">
                    <div>
                        <span class="text-[10px] text-slate-400 block">Chiết khấu tới</span>
                        <strong class="font-mono text-indigo-700 font-black text-sm">25% Hợp Đồng</strong>
                    </div>
                    <a href="{{ route('storefront.wholesale') }}" class="px-3 py-1.5 bg-slate-100 hover:bg-indigo-100 text-slate-700 hover:text-indigo-900 font-bold text-xs rounded-xl transition">
                        Đăng ký sỉ →
                    </a>
                </div>
            </div>

        </div>
    </section>

    <!-- 4. BẢNG BÁO GIÁ DỊCH VỤ KỸ THUẬT MINH BẠCH (TRANSPARENT SERVICE PRICING TABLE) -->
    <section class="bg-white rounded-3xl p-6 sm:p-9 border border-slate-200/90 shadow-sm space-y-6" id="b2b-service-pricing">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
            <div>
                <span class="text-[10px] font-black uppercase text-indigo-600 tracking-wider">BẢNG GIÁ NIÊM YẾT CÔNG KHAI</span>
                <h2 class="text-lg sm:text-2xl font-black text-slate-900">
                    Bảng Báo Giá Nạp Mực & Linh Kiện Máy In Tận Nơi
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Áp dụng cho khách hàng cơ quan, doanh nghiệp, trường học và cá nhân tại khu vực Đồng Nai.
                </p>
            </div>
            <a href="tel:{{ $storefrontSettings['hotline'] ?? '0901234567' }}" class="px-4 py-2.5 bg-amber-50 text-amber-900 border border-amber-300 rounded-xl text-xs font-black self-start sm:self-auto hover:bg-amber-100 transition flex items-center space-x-1.5">
                <span>📞 Hotline Kỹ Thuật: {{ $storefrontSettings['hotline'] ?? '0901 234 567' }}</span>
            </a>
        </div>

        <div class="overflow-x-auto no-scrollbar rounded-2xl border border-slate-200">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-100/80 text-slate-700 uppercase font-bold text-[11px] border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3">Dòng Máy In Phổ Biến</th>
                        <th class="px-4 py-3">Mã Hộp Mực / Linh Kiện</th>
                        <th class="px-4 py-3 text-right">Đơn Giá Nạp Mực</th>
                        <th class="px-4 py-3 text-right">Thay Trống In (Drum)</th>
                        <th class="px-4 py-3">Thời Gian Bảo Hành</th>
                        <th class="px-4 py-3 text-center">Tác Vụ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <!-- Canon 2900 -->
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-4 py-3">
                            <strong class="text-slate-900 font-bold block">Canon LBP 2900 / 3000</strong>
                            <span class="text-[10px] text-slate-400">Máy in laser trắng đen A4</span>
                        </td>
                        <td class="px-4 py-3 font-mono font-bold text-slate-700">Cartridge 12A / 303</td>
                        <td class="px-4 py-3 text-right font-mono font-black text-emerald-700">80.000₫</td>
                        <td class="px-4 py-3 text-right font-mono font-bold text-slate-800">130.000₫</td>
                        <td class="px-4 py-3 text-slate-600">Đậm đẹp đến hạt mực cuối</td>
                        <td class="px-4 py-3 text-center">
                            <a href="#b2b-quick-booking" class="px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-[11px] rounded-lg transition">Gọi thợ</a>
                        </td>
                    </tr>

                    <!-- Canon 3300 -->
                    <tr class="hover:bg-slate-50 transition bg-slate-50/50">
                        <td class="px-4 py-3">
                            <strong class="text-slate-900 font-bold block">Canon LBP 3300 / 6200 / 6230dn</strong>
                            <span class="text-[10px] text-slate-400">Máy in laser 2 mặt tự động</span>
                        </td>
                        <td class="px-4 py-3 font-mono font-bold text-slate-700">Cartridge 49A / 05A / 326</td>
                        <td class="px-4 py-3 text-right font-mono font-black text-emerald-700">90.000₫</td>
                        <td class="px-4 py-3 text-right font-mono font-bold text-slate-800">140.000₫</td>
                        <td class="px-4 py-3 text-slate-600">Đậm đẹp đến hạt mực cuối</td>
                        <td class="px-4 py-3 text-center">
                            <a href="#b2b-quick-booking" class="px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-[11px] rounded-lg transition">Gọi thợ</a>
                        </td>
                    </tr>

                    <!-- HP 107a / M15 -->
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-4 py-3">
                            <strong class="text-slate-900 font-bold block">HP LaserJet 107a / 135a / M15a</strong>
                            <span class="text-[10px] text-slate-400">Dòng máy HP công nghệ mới</span>
                        </td>
                        <td class="px-4 py-3 font-mono font-bold text-slate-700">HP 107A (W1107A) / 48A</td>
                        <td class="px-4 py-3 text-right font-mono font-black text-emerald-700">100.000₫</td>
                        <td class="px-4 py-3 text-right font-mono font-bold text-slate-800">160.000₫</td>
                        <td class="px-4 py-3 text-slate-600">Bảo hành trang in sắc nét</td>
                        <td class="px-4 py-3 text-center">
                            <a href="#b2b-quick-booking" class="px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-[11px] rounded-lg transition">Gọi thợ</a>
                        </td>
                    </tr>

                    <!-- Brother HL-L2321D -->
                    <tr class="hover:bg-slate-50 transition bg-slate-50/50">
                        <td class="px-4 py-3">
                            <strong class="text-slate-900 font-bold block">Brother HL-L2321D / 2366DW / DCP-2520</strong>
                            <span class="text-[10px] text-slate-400">Máy in Brother văn phòng</span>
                        </td>
                        <td class="px-4 py-3 font-mono font-bold text-slate-700">Hộp mực TN-2385 / Cụm DR-2385</td>
                        <td class="px-4 py-3 text-right font-mono font-black text-emerald-700">120.000₫</td>
                        <td class="px-4 py-3 text-right font-mono font-bold text-slate-800">220.000₫</td>
                        <td class="px-4 py-3 text-slate-600">Đã gồm Reset nhông mực miễn phí</td>
                        <td class="px-4 py-3 text-center">
                            <a href="#b2b-quick-booking" class="px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-[11px] rounded-lg transition">Gọi thợ</a>
                        </td>
                    </tr>

                    <!-- Epson L3110 / L3210 -->
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-4 py-3">
                            <strong class="text-slate-900 font-bold block">Epson EcoTank L3110 / L3210 / L805</strong>
                            <span class="text-[10px] text-slate-400">Máy in phun màu đa năng liên tục</span>
                        </td>
                        <td class="px-4 py-3 font-mono font-bold text-slate-700">Mực nước chính hãng Epson 003 / 673</td>
                        <td class="px-4 py-3 text-right font-mono font-black text-emerald-700">70.000₫/màu</td>
                        <td class="px-4 py-3 text-right font-mono font-bold text-slate-800">Hỗ trợ Clear đầu phun</td>
                        <td class="px-4 py-3 text-slate-600">Màu chuẩn, không tắc nghẽn đầu phun</td>
                        <td class="px-4 py-3 text-center">
                            <a href="#b2b-quick-booking" class="px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-[11px] rounded-lg transition">Gọi thợ</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Lưu ý dịch vụ & Cam kết hậu mãi -->
        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 text-xs text-slate-600 space-y-1.5">
            <div class="font-bold text-slate-900 flex items-center space-x-1.5">
                <span>🛡️</span>
                <span>Chính Sách Cam Kết Kỹ Thuật Tận Nơi:</span>
            </div>
            <p>• <b>Không phát sinh chi phí:</b> Đơn giá trên đã bao gồm công thợ đến tận văn phòng/nhà xưởng, hút sạch mực thải và vệ sinh máy in miễn phí.</p>
            <p>• <b>Hóa đơn điện tử VAT:</b> Xuất hóa đơn GTGT điện tử ngay trong ngày cho cơ quan, doanh nghiệp, trường học.</p>
        </div>
    </section>

    <!-- 5. BẢNG BÁO GIÁ SỈ VĂN PHÒNG PHẨM THEO THÙNG (B2B BULK PRICING CATALOG) -->
    <section class="space-y-4" id="b2b-pricing-catalog" aria-labelledby="b2b-catalog-title">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div class="flex items-center space-x-2">
                <span class="w-2.5 h-6 rounded-full bg-emerald-600"></span>
                <h2 id="b2b-catalog-title" class="text-lg sm:text-xl font-black text-slate-900">
                    Vật Tư Giấy In & Thiết Bị Tiêu Hao Doanh Nghiệp Giá Sỉ Cấp 1
                </h2>
            </div>
            <a href="{{ route('storefront.products', ['category' => 'giay-in-photo']) }}" class="text-xs text-emerald-700 font-bold hover:underline flex items-center space-x-1">
                <span>Xem toàn bộ danh mục giấy & mực</span>
                <span>→</span>
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-5">
            @forelse($b2bPaperProducts as $p)
                @include('storefront.components.product-card', ['p' => $p, 'isSlide' => false, 'topBadge' => 'GIÁ SỈ THEO THÙNG', 'topBadgeClass' => 'bg-emerald-600 text-white'])
            @empty
                @foreach($products->take(4) as $p)
                    @include('storefront.components.product-card', ['p' => $p, 'isSlide' => false])
                @endforeach
            @endforelse
        </div>
    </section>

    <!-- 6. HỘP MỰC MÁY IN & LINH KIỆN THAY THẾ BÁN CHẠY (PRINTER SUPPLIES) -->
    <section class="space-y-4" aria-labelledby="printer-supplies-title">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div class="flex items-center space-x-2">
                <span class="w-2.5 h-6 rounded-full bg-indigo-700"></span>
                <h2 id="printer-supplies-title" class="text-lg sm:text-xl font-black text-slate-900">
                    Hộp Mực Máy In & Phụ Kiện Kỹ Thuật Đạt Chuẩn Xuất Xưởng
                </h2>
            </div>
            <a href="{{ route('storefront.products', ['category' => 'hop-muc-may-in']) }}" class="text-xs text-indigo-700 font-bold hover:underline flex items-center space-x-1">
                <span>Xem tất cả hộp mực Canon, HP, Brother</span>
                <span>→</span>
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-5">
            @forelse($popularCartridges as $cp)
                @include('storefront.components.product-card', ['p' => $cp, 'isSlide' => false, 'topBadge' => 'CHUẨN HÃNG', 'topBadgeClass' => 'bg-indigo-700 text-white'])
            @empty
                @foreach($flashSaleProducts->take(4) as $fp)
                    @include('storefront.components.product-card', ['p' => $fp, 'isSlide' => false])
                @endforeach
            @endforelse
        </div>
    </section>

    <!-- 7. QUY TRÌNH 4 BƯỚC PHỤC VỤ KỸ THUẬT SLA 30 PHÚT (4-STEP WORKFLOW) -->
    <section class="bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 sm:p-10 text-white shadow-xl space-y-6">
        <div class="text-center max-w-2xl mx-auto space-y-2">
            <span class="px-3 py-1 bg-indigo-500/20 text-indigo-300 rounded-full text-[10px] font-black uppercase tracking-wider border border-indigo-400/30">
                SLA CHUẨN DOANH NGHIỆP
            </span>
            <h2 class="text-xl sm:text-3xl font-black text-white">
                Quy Trình 4 Bước Phục Vụ Tận Nơi Trong 30 Phút
            </h2>
            <p class="text-xs sm:text-sm text-slate-300">
                Tiết kiệm thời gian và không làm gián đoạn công việc in ấn.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pt-3">
            <!-- Bước 1 -->
            <div class="bg-white/5 border border-white/10 rounded-2xl p-5 relative overflow-hidden flex flex-col justify-between">
                <span class="text-4xl font-mono font-black text-white/10 absolute top-2 right-3">01</span>
                <div class="space-y-2">
                    <span class="w-10 h-10 rounded-xl bg-indigo-500/20 text-indigo-300 flex items-center justify-center text-lg font-bold">📞</span>
                    <strong class="text-sm font-black text-white block">Tiếp Nhận Trong 5 Phút</strong>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        Khách hàng gọi Hotline hoặc điền form. Điều phối viên xác định mã máy, lỗi và điều phối kỹ thuật viên gần nhất.
                    </p>
                </div>
            </div>

            <!-- Bước 2 -->
            <div class="bg-white/5 border border-white/10 rounded-2xl p-5 relative overflow-hidden flex flex-col justify-between">
                <span class="text-4xl font-mono font-black text-white/10 absolute top-2 right-3">02</span>
                <div class="space-y-2">
                    <span class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-300 flex items-center justify-center text-lg font-bold">🛵</span>
                    <strong class="text-sm font-black text-white block">Có Mặt Sau 15–30 Phút</strong>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        Kỹ thuật viên đến văn phòng với dụng cụ, mực và linh kiện cần thiết.
                    </p>
                </div>
            </div>

            <!-- Bước 3 -->
            <div class="bg-white/5 border border-white/10 rounded-2xl p-5 relative overflow-hidden flex flex-col justify-between">
                <span class="text-4xl font-mono font-black text-white/10 absolute top-2 right-3">03</span>
                <div class="space-y-2">
                    <span class="w-10 h-10 rounded-xl bg-blue-500/20 text-blue-300 flex items-center justify-center text-lg font-bold">🔍</span>
                    <strong class="text-sm font-black text-white block">Kiểm Tra & Báo Giá Minh Bạch</strong>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        In bản test hiện trạng lỗi, thông báo phương án xử lý và chi phí chính xác. Khách duyệt mới tiến hành làm.
                    </p>
                </div>
            </div>

            <!-- Bước 4 -->
            <div class="bg-white/5 border border-white/10 rounded-2xl p-5 relative overflow-hidden flex flex-col justify-between">
                <span class="text-4xl font-mono font-black text-white/10 absolute top-2 right-3">04</span>
                <div class="space-y-2">
                    <span class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-300 flex items-center justify-center text-lg font-bold">✅</span>
                    <strong class="text-sm font-black text-white block">Nghiệm Thu & Xuất Hóa Đơn VAT</strong>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        In bản test nghiệm thu đạt chuẩn, dán tem bảo hành, ký nhận biên bản bàn giao và xuất hóa đơn VAT điện tử.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- 8. FORM ĐĂNG KÝ HỢP ĐỒNG B2B & ĐẶT LỊCH KỸ THUẬT (UNIFIED B2B LEAD FORM) -->
    <section class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200/90 shadow-sm grid grid-cols-1 lg:grid-cols-12 gap-8 items-center" id="b2b-contract-form" x-data="{ formType: 'contract' }">
        
        <div class="lg:col-span-5 space-y-4">
            <span class="px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full text-[10px] font-black uppercase tracking-wider">
                ĐỒNG HÀNH CÙNG DOANH NGHIỆP
            </span>
            <h2 class="text-xl sm:text-3xl font-black text-slate-900 leading-tight">
                Gửi Yêu Cầu Báo Giá Hoặc Đặt Lịch Hẹn Kỹ Thuật
            </h2>
            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                Đội ngũ chăm sóc khách hàng doanh nghiệp sẽ liên hệ lại trong vòng <b>15 phút</b> kèm bảng báo giá chiết khấu chi tiết và danh mục sản phẩm mẫu.
            </p>

            <div class="space-y-2 pt-2 text-xs text-slate-700">
                <div class="flex items-center space-x-2">
                    <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-[10px]">✓</span>
                    <span>Miễn phí giao hàng toàn bộ đơn hàng trong tỉnh Đồng Nai.</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-[10px]">✓</span>
                    <span>Áp dụng công nợ cuối tháng (đối soát từ 25 - 30 hàng tháng).</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-[10px]">✓</span>
                    <span>Xuất hóa đơn VAT điện tử theo từng đơn hoặc gộp bảng kê tháng.</span>
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs">
                <span class="text-slate-500 block">Trực tiếp liên hệ Trưởng bộ phận B2B:</span>
                <strong class="text-sm font-black text-slate-900 font-mono block mt-0.5">{{ $storefrontSettings['hotline'] ?? '0901 234 567' }}</strong>
            </div>
        </div>

        <div class="lg:col-span-7 bg-slate-50 rounded-2xl p-6 sm:p-8 border border-slate-200">
            <!-- Form Tabs -->
            <div class="flex rounded-xl bg-slate-200/80 p-1 mb-5">
                <button type="button" @click="formType = 'contract'" :class="formType === 'contract' ? 'bg-white text-indigo-700 shadow-xs' : 'text-slate-600 hover:text-slate-900'" class="flex-1 py-2 rounded-lg font-black text-xs transition cursor-pointer">
                    1. Đăng Ký Hợp Đồng B2B / Nhận Báo Giá Sỉ
                </button>
                <button type="button" @click="formType = 'repair'" :class="formType === 'repair' ? 'bg-white text-orange-600 shadow-xs' : 'text-slate-600 hover:text-slate-900'" class="flex-1 py-2 rounded-lg font-black text-xs transition cursor-pointer" id="b2b-quick-booking">
                    2. Đặt Lịch Nạp Mực / Sửa Máy In 30p
                </button>
            </div>

            <!-- Form 1: Hợp đồng B2B (storefront.post-wholesale) -->
            <form x-show="formType === 'contract'" action="{{ route('storefront.post-wholesale') }}" method="POST" class="space-y-3">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tên công ty / Trường học *</label>
                        <input type="text" name="company_name" required placeholder="Cty TNHH Sản Xuất ABC…" class="w-full text-xs px-3 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 bg-white" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Mã số thuế (nếu có)</label>
                        <input type="text" name="tax_id" placeholder="3600xxxxxx" class="w-full text-xs px-3 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 bg-white font-mono" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Họ tên người phụ trách *</label>
                        <input type="text" name="contact_name" required placeholder="Nguyễn Thị Lan (Phòng Hành chính)" class="w-full text-xs px-3 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 bg-white" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Số điện thoại / Zalo *</label>
                        <input type="tel" name="phone" required placeholder="0901 234 567" class="w-full text-xs px-3 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 bg-white font-mono" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Email nhận báo giá *</label>
                        <input type="email" name="email" required placeholder="hanhchinh@doanhnghiep.vn" class="w-full text-xs px-3 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 bg-white" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Khu vực / KCN tại Đồng Nai *</label>
                        <input type="text" name="address" required placeholder="KCN Amata, Biên Hòa..." class="w-full text-xs px-3 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 bg-white" />
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Mặt hàng doanh nghiệp cần quan tâm:</label>
                    <input type="text" name="interested_categories" placeholder="VD: Giấy in A4 theo thùng, Hộp mực Canon 2900, Bìa còng 7cm..." class="w-full text-xs px-3 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 bg-white" />
                </div>

                <button type="submit" class="w-full py-3.5 px-6 bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs rounded-xl shadow-md transition cursor-pointer">
                    NHẬN BẢNG BÁO GIÁ CHIẾT KHẤU B2B TRONG 15 PHÚT →
                </button>
            </form>

            <!-- Form 2: Sửa máy in / Nạp mực (storefront.book) -->
            <form x-show="formType === 'repair'" x-cloak action="{{ route('storefront.book') }}" method="POST" class="space-y-3">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Họ tên khách hàng *</label>
                        <input type="text" name="customer_name" required placeholder="Nguyễn Văn A" class="w-full text-xs px-3 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 bg-white" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Số điện thoại *</label>
                        <input type="tel" name="customer_phone" required placeholder="0901 234 567" class="w-full text-xs px-3 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 bg-white font-mono" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Dòng máy in cần xử lý *</label>
                        <input type="text" name="device_name" required placeholder="VD: Canon 2900, HP 107a, Brother 2321D..." class="w-full text-xs px-3 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 bg-white" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Địa chỉ có mặt thợ *</label>
                        <input type="text" name="address" placeholder="VD: Số 45 Đường Amata, Biên Hòa..." class="w-full text-xs px-3 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 bg-white" />
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Mô tả tình trạng lỗi máy in *</label>
                    <textarea name="issue_description" rows="2" required placeholder="VD: Máy hết mực in mờ, hoặc giấy bị kẹt giữa máy kêu to cạch cạch..." class="w-full text-xs px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 bg-white"></textarea>
                </div>

                <button type="submit" class="w-full py-3.5 px-6 bg-gradient-to-r from-orange-600 to-rose-600 hover:from-orange-700 hover:to-rose-700 text-white font-black text-xs rounded-xl shadow-md transition cursor-pointer">
                    🚀 ĐIỀU PHỐI KỸ THUẬT VIÊN ĐẾN TẬN NƠI (SAU 30 PHÚT) →
                </button>
            </form>
        </div>

    </section>

    <!-- 9. BẢN ĐỒ KHU VỰC PHỤC VỤ & KHÁCH HÀNG DOANH NGHIỆP TIÊU BIỂU (SOCIAL PROOF) -->
    <section class="space-y-4">
        <div class="text-center max-w-xl mx-auto space-y-1">
            <span class="text-[10px] font-black uppercase text-indigo-600 tracking-wider">ĐỊA BÀN PHỤC VỤ TRỌNG ĐIỂM</span>
            <h2 class="text-lg sm:text-2xl font-black text-slate-900">
                Phục Vụ Nhanh Chóng Toàn Bộ Các Khu Công Nghiệp Đồng Nai
            </h2>
            <p class="text-xs text-slate-500">
                Kỹ thuật viên túc trực liên tục tại các trạm dịch vụ trung tâm.
            </p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-2.5 text-center text-xs">
            <div class="p-3 bg-white rounded-2xl border border-slate-200 shadow-2xs">
                <span class="text-base block mb-0.5">🏭</span>
                <strong class="font-bold text-slate-800 text-[11px] block">KCN Amata</strong>
                <span class="text-[9px] text-emerald-700 font-semibold">15 phút có mặt</span>
            </div>
            <div class="p-3 bg-white rounded-2xl border border-slate-200 shadow-2xs">
                <span class="text-base block mb-0.5">🏭</span>
                <strong class="font-bold text-slate-800 text-[11px] block">KCN Biên Hòa 1</strong>
                <span class="text-[9px] text-emerald-700 font-semibold">15 phút có mặt</span>
            </div>
            <div class="p-3 bg-white rounded-2xl border border-slate-200 shadow-2xs">
                <span class="text-base block mb-0.5">🏭</span>
                <strong class="font-bold text-slate-800 text-[11px] block">KCN Biên Hòa 2</strong>
                <span class="text-[9px] text-emerald-700 font-semibold">20 phút có mặt</span>
            </div>
            <div class="p-3 bg-white rounded-2xl border border-slate-200 shadow-2xs">
                <span class="text-base block mb-0.5">🏭</span>
                <strong class="font-bold text-slate-800 text-[11px] block">KCN Loteco</strong>
                <span class="text-[9px] text-emerald-700 font-semibold">20 phút có mặt</span>
            </div>
            <div class="p-3 bg-white rounded-2xl border border-slate-200 shadow-2xs">
                <span class="text-base block mb-0.5">🏭</span>
                <strong class="font-bold text-slate-800 text-[11px] block">KCN Hố Nai</strong>
                <span class="text-[9px] text-emerald-700 font-semibold">25 phút có mặt</span>
            </div>
            <div class="p-3 bg-white rounded-2xl border border-slate-200 shadow-2xs">
                <span class="text-base block mb-0.5">🏭</span>
                <strong class="font-bold text-slate-800 text-[11px] block">KCN Giang Điền</strong>
                <span class="text-[9px] text-emerald-700 font-semibold">30 phút có mặt</span>
            </div>
            <div class="p-3 bg-white rounded-2xl border border-slate-200 shadow-2xs">
                <span class="text-base block mb-0.5">🏭</span>
                <strong class="font-bold text-slate-800 text-[11px] block">KCN Long Đức</strong>
                <span class="text-[9px] text-emerald-700 font-semibold">30 phút có mặt</span>
            </div>
            <div class="p-3 bg-white rounded-2xl border border-slate-200 shadow-2xs">
                <span class="text-base block mb-0.5">🏭</span>
                <strong class="font-bold text-slate-800 text-[11px] block">KCN Nhơn Trạch</strong>
                <span class="text-[9px] text-emerald-700 font-semibold">30 phút có mặt</span>
            </div>
        </div>
    </section>

</main>
@endsection
