<!DOCTYPE html>
<html lang="vi" prefix="og: https://ogp.me/ns#">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', ($storefrontSettings['site_name'] ?? 'VPP & Thiết Bị Máy In') . ' - Tổng Kho Văn Phòng Phẩm & Dịch Vụ Máy In')</title>
    <meta name="description" content="@yield('meta_description', 'Tổng kho hơn 1.000 mặt hàng văn phòng phẩm giá sỉ, giấy in photo Double A, PaperOne, bút Thiên Long, dịch vụ nạp mực & sửa chữa máy in tận nơi tại Bình Hòa, Biên Hòa, Đồng Nai nhanh chóng, uy tín.')">
    <meta name="keywords" content="@yield('meta_keywords', 'văn phòng phẩm, văn phòng phẩm đồng nai, nạp mực máy in, sửa máy in đồng nai, bơm mực canon 2900, giấy in photo a4, giấy double a giá sỉ, bút thiên long, thiết bị máy in bình hòa')">
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
    <meta name="author" content="{{ $storefrontSettings['company_name'] ?? $storefrontSettings['site_name'] }}">
    <link rel="canonical" href="@yield('canonical_url', url()->current())">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- SEO LOCAL & GEOTARGETING (Chuẩn Google Maps & Tìm Kiếm Khu Vực Đồng Nai) -->
    <meta name="geo.region" content="VN-39">
    <meta name="geo.placename" content="Bình Hòa, Đồng Nai, Việt Nam">
    <meta name="geo.position" content="10.9984;106.8458">
    <meta name="ICBM" content="10.9984, 106.8458">

    <!-- OPEN GRAPH (Facebook, Zalo, LinkedIn) -->
    <meta property="og:locale" content="vi_VN">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:title" content="@yield('title', ($storefrontSettings['site_name'] ?? 'VPP & Thiết Bị Máy In') . ' - Tổng Kho Văn Phòng Phẩm & Dịch Vụ Máy In')">
    <meta property="og:description" content="@yield('meta_description', 'Tổng kho văn phòng phẩm và dịch vụ nạp mực máy in tận nơi tại Bình Hòa, Đồng Nai uy tín, nhanh chóng.')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="{{ $storefrontSettings['site_name'] ?? 'VPP & Thiết Bị Máy In' }}">
    <meta property="og:image" content="@yield('og_image', !empty($storefrontSettings['logo_url']) ? $storefrontSettings['logo_url'] : asset('favicon.ico'))">

    <!-- TWITTER CARD -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', ($storefrontSettings['site_name'] ?? 'VPP & Thiết Bị Máy In'))">
    <meta name="twitter:description" content="@yield('meta_description', 'Tổng kho văn phòng phẩm & Dịch vụ kỹ thuật máy in tận nơi')">
    <meta name="twitter:image" content="@yield('og_image', !empty($storefrontSettings['logo_url']) ? $storefrontSettings['logo_url'] : asset('favicon.ico'))">

    <!-- AI SEARCH ENGINE OPTIMIZATION (GEO: Perplexity, ChatGPT, Claude, Gemini) -->
    <link rel="alternate" type="text/plain" href="{{ url('/llms.txt') }}" title="LLMs.txt for AI Search Engines">

    @if(!empty($storefrontSettings['favicon_url']))
        <link rel="icon" href="{{ $storefrontSettings['favicon_url'] }}">
    @else
        <link rel="icon" href="{{ asset('favicon.ico') }}">
    @endif

    <!-- STRUCTURED DATA: SCHEMA.ORG JSON-LD LOCAL BUSINESS & WEBSITE -->
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@graph": [
        {
          "@type": ["LocalBusiness", "Store", "HomeGoodsStore"],
          "@id": "{{ url('/') }}#store",
          "name": "{{ $storefrontSettings['site_name'] ?? 'VPP & Thiết Bị Máy In' }}",
          "alternateName": "Tổng Kho Văn Phòng Phẩm & Dịch Vụ Máy In Đồng Nai",
          "url": "{{ url('/') }}",
          "telephone": "{{ $storefrontSettings['hotline'] ?? '0974.194.305' }}",
          "email": "{{ $storefrontSettings['email'] ?? 'hotro@vpp.vn' }}",
          "priceRange": "10.000đ - 10.000.000đ",
          "currenciesAccepted": "VND",
          "paymentAccepted": "Tiền mặt, Chuyển khoản VietQR, Thẻ ngân hàng",
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
          "openingHoursSpecification": [
            {
              "@type": "OpeningHoursSpecification",
              "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
              "opens": "07:30",
              "closes": "18:30"
            },
            {
              "@type": "OpeningHoursSpecification",
              "dayOfWeek": "Sunday",
              "opens": "08:00",
              "closes": "17:00"
            }
          ],
          "areaServed": [
            {"@type": "AdministrativeArea", "name": "Đồng Nai"},
            {"@type": "AdministrativeArea", "name": "Bình Hòa"},
            {"@type": "AdministrativeArea", "name": "Biên Hòa"},
            {"@type": "AdministrativeArea", "name": "Vĩnh Cửu"},
            {"@type": "AdministrativeArea", "name": "Bình Dương"},
            {"@type": "AdministrativeArea", "name": "TP.HCM"}
          ],
          "hasOfferCatalog": {
            "@type": "OfferCatalog",
            "name": "Danh mục sản phẩm & dịch vụ chính",
            "itemListElement": [
              {
                "@type": "OfferCatalog",
                "name": "Văn phòng phẩm & Giấy in",
                "description": "Giấy photo Double A, PaperOne, IK Plus, Bút viết Thiên Long, Bìa còng file hồ sơ"
              },
              {
                "@type": "OfferCatalog",
                "name": "Dịch vụ kỹ thuật máy in",
                "description": "Nạp mực máy in tận nơi trong 30 phút, thay linh kiện trống gạt trục sạc máy in Canon, HP, Brother"
              }
            ]
          }
        },
        {
          "@type": "WebSite",
          "@id": "{{ url('/') }}#website",
          "url": "{{ url('/') }}",
          "name": "{{ $storefrontSettings['site_name'] ?? 'VPP & Thiết Bị Máy In' }}",
          "inLanguage": "vi-VN",
          "potentialAction": {
            "@type": "SearchAction",
            "target": {
              "@type": "EntryPoint",
              "urlTemplate": "{{ route('storefront.products') }}?q={search_term_string}"
            },
            "query-input": "required name=search_term_string"
          }
        }
      ]
    }
    </script>

    @yield('schema_extra')

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; color: #0f172a; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
    @stack('styles')
</head>
<body class="bg-[#f8fafc] text-slate-900 min-h-screen flex flex-col justify-between antialiased selection:bg-emerald-600 selection:text-white">

    <!-- 1. TOP ANNOUNCEMENT BAR (Gọn gàng: Giờ mở cửa bên trái, Tra cứu phiếu bên phải) -->
    @if(($storefrontSettings['notice_bar_enabled'] ?? '1') !== '0' && trim((string) ($storefrontSettings['notice_bar_text'] ?? '')) !== '')
        <div class="bg-emerald-50 text-emerald-900 text-xs text-center px-4 py-2 border-b border-emerald-100">
            {{ $storefrontSettings['notice_bar_text'] }}
        </div>
    @endif
    <div class="bg-slate-950 text-slate-300 text-xs py-2 border-b border-slate-800 hidden sm:block">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center text-[11px]">
            <div class="flex items-center space-x-2 text-slate-300">
                <span>⏰ Giờ làm việc: <b>{{ $storefrontSettings['opening_hours'] }}</b></span>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('lookup.index') }}" class="text-sky-300 hover:text-white font-bold transition flex items-center space-x-1 hover:underline">
                    <span>🔍</span>
                    <span>Tra cứu phiếu sửa</span>
                </a>
            </div>
        </div>
    </div>

    <!-- 2. UNIFIED MAIN HEADER (Executive White & Royal Navy Style - Đồng bộ toàn hệ thống) -->
    <header class="bg-white/95 backdrop-blur-md text-slate-900 sticky top-0 z-40 shadow-xs border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-20 gap-3 lg:gap-8">

                <!-- Logo & Brand Name -->
                <a href="{{ route('storefront.index') }}" class="flex items-center space-x-2.5 group shrink-0">
                    @if(!empty($storefrontSettings['logo_url']))
                        <img src="{{ $storefrontSettings['logo_url'] }}" alt="{{ $storefrontSettings['site_name'] }}" class="h-9 sm:h-11 w-auto max-w-[140px] sm:max-w-[180px] object-contain rounded-lg">
                    @else
                        <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center font-black text-white text-base sm:text-xl shadow-md group-hover:scale-105 transition transform">
                            <span>{{ mb_substr($storefrontSettings['site_name'] ?? 'V', 0, 1) }}</span>
                        </div>
                    @endif
                    <div>
                        <span class="text-base sm:text-xl font-black tracking-tight text-slate-900 group-hover:text-indigo-900 transition block leading-tight">
                            {{ $storefrontSettings['site_name'] }}
                        </span>
                        <span class="text-[10px] text-emerald-700 font-bold uppercase tracking-wider block">
                            {{ $storefrontSettings['site_slogan'] }}
                        </span>
                    </div>
                </a>

                <!-- Desktop Smart Search Bar (Rộng rãi, viền tinh tế, nút Navy) -->
                <div class="hidden md:flex flex-1 max-w-2xl lg:max-w-3xl mx-2 lg:mx-4">
                    <form action="{{ route('storefront.products') }}" method="GET" class="relative flex items-center w-full">
                        <input type="text" name="q" value="{{ request('q') }}"
                            placeholder="Tìm giấy Double A, bút Thiên Long, mực Canon 2900..."
                            class="w-full text-xs sm:text-sm pl-4 pr-12 py-2.5 rounded-2xl border border-slate-300 bg-slate-50/70 text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 focus:bg-white shadow-2xs transition" />
                        <button type="submit" class="absolute right-1 px-4 py-2 bg-[#1e3a8a] hover:bg-blue-900 active:bg-blue-950 text-white rounded-xl transition flex items-center justify-center font-bold text-xs shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </button>
                    </form>
                </div>

                <!-- Right Action Cluster (Hotline Gọi Nhanh + Giỏ Hàng + Tài Khoản) -->
                <div class="flex items-center space-x-2.5 sm:space-x-4 shrink-0">
                    <!-- Hotline Box -->
                    <a href="{{ $storefrontSettings['hotline_url'] }}" class="hidden xl:flex items-center space-x-2.5 px-3 py-1.5 rounded-2xl hover:bg-slate-50 transition border border-transparent hover:border-slate-200">
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-base">
                            📞
                        </div>
                        <div class="text-left text-xs leading-tight">
                            <span class="text-[10px] text-slate-400 font-semibold block uppercase">Hotline tư vấn</span>
                            <span class="font-mono font-black text-slate-900">{{ $storefrontSettings['hotline'] }}</span>
                        </div>
                    </a>

                    <!-- Nút Giỏ Hàng Nổi Bật -->
                    <a href="{{ route('storefront.cart') }}" class="relative inline-flex items-center space-x-2 bg-[#1e3a8a] hover:bg-blue-900 text-white px-4 py-2.5 rounded-2xl text-xs font-extrabold transition shadow-sm">
                        <svg class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                        <span class="hidden sm:inline">Giỏ Hàng</span>
                        <span id="global-cart-badge" class="px-2 py-0.5 bg-rose-500 text-white rounded-full text-[10px] font-black shadow-xs">0</span>
                    </a>

                    <!-- User Account / Login -->
                    @if(auth('customer')->check())
                    <a href="{{ route('customer.profile') }}" class="inline-flex items-center space-x-1.5 bg-slate-100 hover:bg-slate-200 text-slate-800 px-3.5 py-2.5 rounded-2xl text-xs font-bold transition border border-slate-200">
                        <span>👤</span>
                        <span class="hidden sm:inline">{{ Str::limit(auth('customer')->user()->name, 10) }}</span>
                    </a>
                    @else
                    <a href="{{ route('customer.login') }}" class="inline-flex items-center space-x-1.5 bg-slate-100 hover:bg-slate-200 text-slate-800 px-3.5 py-2.5 rounded-2xl text-xs font-bold transition border border-slate-200">
                        <span>👤</span>
                        <span class="hidden sm:inline">Đăng Nhập</span>
                    </a>
                    @endif
                </div>

            </div>

            <!-- Mobile Search Bar (Gọn gàng trên điện thoại) -->
            <div class="pb-3 md:hidden">
                <form action="{{ route('storefront.products') }}" method="GET" class="relative flex items-center">
                    <input type="text" name="q" value="{{ request('q') }}"
                        placeholder="Tìm giấy Double A, bút, mực in..."
                        class="w-full text-xs pl-3.5 pr-10 py-2.5 rounded-xl border border-slate-300 bg-slate-50 text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:bg-white" />
                    <button type="submit" class="absolute right-1 px-3 py-1.5 bg-[#1e3a8a] text-white rounded-lg text-xs font-bold">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </button>
                </form>
            </div>

        </div>

        <!-- Secondary Navigation & Category Bar (Desktop Deep Navy Phẳng Sang Trọng) -->
        <div class="hidden lg:block bg-[#1e3a8a] text-xs text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <!-- Left Categories -->
                <div class="flex items-center space-x-1">
                    <a href="{{ route('storefront.products') }}" class="flex items-center space-x-2 py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 font-extrabold text-white transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        <span>TẤT CẢ SẢN PHẨM</span>
                    </a>
                    <a href="{{ route('storefront.products', ['category' => 'giay-in-photo']) }}" class="py-2.5 px-3 hover:text-amber-300 font-semibold transition flex items-center space-x-1">
                        <span>📄 Giấy In Photo</span>
                    </a>
                    <a href="{{ route('storefront.products', ['category' => 'but-viet-muc-viet']) }}" class="py-2.5 px-3 hover:text-amber-300 font-semibold transition flex items-center space-x-1">
                        <span>🖊️ Bút Viết & Dụng Cụ</span>
                    </a>
                    <a href="{{ route('storefront.products', ['category' => 'hop-muc-may-in']) }}" class="py-2.5 px-3 hover:text-amber-300 font-semibold transition flex items-center space-x-1">
                        <span>🖨️ Hộp Mực Máy In</span>
                    </a>
                    <a href="{{ route('storefront.products', ['category' => 'linh-kien-may-in']) }}" class="py-2.5 px-3 hover:text-amber-300 font-semibold transition flex items-center space-x-1">
                        <span>⚙️ Trống Drum & Linh Kiện</span>
                    </a>
                    <a href="{{ route('storefront.index') }}#dich-vu-may-in" class="py-2.5 px-3 hover:text-amber-300 font-semibold transition flex items-center space-x-1">
                        <span>🛠️ Sửa Máy In 30P</span>
                    </a>
                    <a href="{{ route('storefront.wholesale') }}" class="py-2.5 px-3.5 text-amber-300 font-black hover:text-amber-200 transition flex items-center space-x-1">
                        <span class="animate-pulse">🔥</span>
                        <span>BÁO GIÁ SỈ DOANH NGHIỆP</span>
                    </a>
                </div>
                <!-- Right Quick Links -->
                <div class="flex items-center space-x-4 text-[11px] text-blue-200">
                    <a href="{{ route('lookup.index') }}" class="hover:text-amber-300 transition flex items-center space-x-1 font-semibold">
                        <span>🔍 Tra cứu phiếu sửa</span>
                    </a>
                    <a href="{{ route('storefront.about') }}" class="hover:text-amber-300 transition flex items-center space-x-1 font-semibold">
                        <span>🏢 Về chúng tôi</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- MODERN TOAST NOTIFICATION CONTAINER (Ghim góc trên bên phải, hiển thị thông báo đẹp mắt, tự động ẩn) -->
    <div id="toastContainer" class="fixed top-5 right-5 z-[99999] flex flex-col space-y-3 pointer-events-none max-w-sm sm:max-w-md w-full px-4 sm:px-0"></div>

    <!-- 3. PAGE BODY CONTENT -->
    <div class="flex-1 w-full">
        @yield('content')
    </div>

    <!-- 4. FLOATING ACTION WIDGETS (Zalo & Hotline - Ghim cố định góc phải) -->
    <div id="storefront-contact-shortcuts" class="fixed bottom-20 sm:bottom-6 right-4 sm:right-6 z-40 {{ View::hasSection('custom_bottom_bar') ? 'hidden sm:flex' : 'flex' }} flex-col space-y-2.5">
        <a href="{{ $storefrontSettings['zalo_url'] }}" target="_blank" class="flex items-center space-x-2 bg-[#0068ff] hover:bg-blue-600 text-white px-3.5 py-2.5 rounded-full shadow-xl transition transform hover:scale-105 group border-2 border-white">
            <span class="w-6 h-6 rounded-full bg-white text-[#0068ff] font-black text-xs flex items-center justify-center">Z</span>
            <span class="text-xs font-bold hidden sm:inline">Zalo Chat</span>
        </a>

        <a href="{{ $storefrontSettings['hotline_url'] }}" class="flex items-center space-x-2 bg-[#059669] hover:bg-emerald-700 text-white px-3.5 py-2.5 rounded-full shadow-xl transition transform hover:scale-105 group border-2 border-white">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
            <span class="text-xs font-bold hidden sm:inline">Hotline: {{ $storefrontSettings['hotline'] }}</span>
        </a>
    </div>

    <!-- 5. UNIFIED FOOTER SECTION (Đồng bộ mọi trang) -->
    <footer class="bg-slate-900 text-slate-400 py-10 mt-12 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="space-y-3">
                <span class="text-white font-black text-lg block">{{ $storefrontSettings['site_name'] }}</span>
                <p class="text-xs leading-relaxed text-slate-400">
                    Tổng kho phân phối sỉ & lẻ văn phòng phẩm, giấy in photo Double A, PaperOne, hộp mực và linh kiện máy in Canon, Brother, HP chính hãng tại Đồng Nai. Hỗ trợ nạp mực & sửa chữa máy in tận nơi trong 30 phút.
                </p>
                <div class="text-xs text-slate-300 space-y-1">
                    <p>📍 Địa chỉ: {{ $storefrontSettings['address'] }}</p>
                    <p>📞 Hotline: {{ $storefrontSettings['hotline'] }} | Zalo: {{ $storefrontSettings['zalo'] }}</p>
                    @if(!empty($storefrontSettings['technical_hotline']))
                        <p>🛠️ Kỹ thuật 24/7: <a href="{{ $storefrontSettings['technical_hotline_url'] }}" class="text-emerald-400 hover:underline">{{ $storefrontSettings['technical_hotline'] }}</a></p>
                    @endif
                    <p>Email: {{ $storefrontSettings['email'] }}</p>
                    <p>⏰ Mở cửa: {{ $storefrontSettings['opening_hours'] }}</p>
                    @if(!empty($storefrontSettings['facebook_url']))
                        <p>🌐 Fanpage: <a href="{{ $storefrontSettings['facebook_url'] }}" target="_blank" rel="noopener noreferrer" class="text-sky-400 hover:underline">Facebook Cửa Hàng</a></p>
                    @endif
                    <p>🚀 Miễn phí giao hàng cho đơn từ {{ $storefrontSettings['freeship_label'] }}</p>
                </div>
            </div>

            <div>
                <h4 class="text-white font-bold text-xs uppercase tracking-wider mb-3">Ngành Hàng Chính</h4>
                <ul class="space-y-2 text-xs">
                    <li><a href="{{ route('storefront.products', ['category' => 'giay-in-photo']) }}" class="hover:text-white transition">Giấy In Double A / PaperOne / IK Plus</a></li>
                    <li><a href="{{ route('storefront.products', ['category' => 'but-viet-muc-viet']) }}" class="hover:text-white transition">Bút Bi & Bút Gel Thiên Long</a></li>
                    <li><a href="{{ route('storefront.products', ['category' => 'hop-muc-may-in']) }}" class="hover:text-white transition">Hộp Mực Máy In Canon 2900 & Brother</a></li>
                    <li><a href="{{ route('storefront.products', ['category' => 'bia-file-ho-so']) }}" class="hover:text-white transition">Bìa Còng Kokuyo & Bìa Nút My Clear</a></li>
                    <li><a href="{{ route('storefront.products', ['category' => 'dung-cu-van-phong']) }}" class="hover:text-white transition">Bấm Kim Plus & Băng Keo Dán Thùng</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-white font-bold text-xs uppercase tracking-wider mb-3">Dịch Vụ & Đại Lý</h4>
                <ul class="space-y-2 text-xs">
                    <li><a href="{{ route('storefront.wholesale') }}" class="text-amber-400 font-bold hover:underline">⭐ Báo Giá Sỉ Cho Doanh Nghiệp & Đại Lý</a></li>
                    <li><a href="{{ route('lookup.index') }}" class="hover:text-white transition">Tra Cứu Phiếu Sửa Chữa Máy In Online</a></li>
                    <li><a href="{{ $storefrontSettings['zalo_url'] }}" target="_blank" class="hover:text-white transition">Tư Vấn Nạp Mực Tận Nơi Qua Zalo</a></li>
                    <li><a href="{{ route('customer.login') }}" class="hover:text-white transition">Tài Khoản & Lịch Sử Đơn Hàng</a></li>
                    <li><a href="{{ route('pos.index') }}" class="hover:text-white transition">Quầy bán hàng POS</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-white font-bold text-xs uppercase tracking-wider mb-3">Chính Sách Khách Hàng</h4>
                <ul class="space-y-2 text-xs">
                    <li><a href="{{ route('storefront.terms') }}" class="hover:text-white transition">Chính Sách Giao Hàng Siêu Tốc 2 Giờ</a></li>
                    <li><a href="{{ route('storefront.terms') }}" class="hover:text-white transition">Chính Sách Đổi Trả & Bảo Hành 1-Đổi-1</a></li>
                    <li><a href="{{ route('storefront.privacy') }}" class="hover:text-white transition text-emerald-400 font-bold">🛡️ Bảo Mật Dữ Liệu Khách Hàng (100%)</a></li>
                    <li><a href="{{ route('storefront.about') }}" class="hover:text-white transition">Giới Thiệu Về Chúng Tôi</a></li>
                </ul>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 pt-6 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-2">
            <p>© 2026 {{ $storefrontSettings['site_name'] }}. Hệ thống thương mại văn phòng phẩm & dịch vụ máy in chuyên nghiệp.</p>
            <div class="flex items-center space-x-3 text-slate-400 text-[11px]">
                <a href="{{ route('storefront.about') }}" class="hover:text-white">Giới Thiệu</a>
                <span>•</span>
                <a href="{{ route('storefront.terms') }}" class="hover:text-white">Chính Sách</a>
                <span>•</span>
                <a href="{{ route('storefront.privacy') }}" class="hover:text-white">Bảo Mật</a>
            </div>
        </div>
    </footer>

    <!-- 6. MOBILE BOTTOM NAVIGATION (5 Cố định màn hình điện thoại) HOẶC CUSTOM ACTION BAR -->
    @hasSection('custom_bottom_bar')
        @yield('custom_bottom_bar')
    @else
        <nav class="bg-white/95 backdrop-blur-md border-t border-slate-200 sticky bottom-0 z-40 md:hidden w-full shadow-lg">
            <div class="grid grid-cols-5 text-center text-[10px] py-1.5 font-medium">
                <a href="{{ route('storefront.index') }}" class="{{ request()->routeIs('storefront.index') ? 'text-[#1e3a8a] font-bold' : 'text-slate-500' }} flex flex-col items-center py-1">
                    <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span>Trang chủ</span>
                </a>
                <a href="{{ route('storefront.products') }}" class="{{ request()->routeIs('storefront.products*') ? 'text-[#1e3a8a] font-bold' : 'text-slate-500' }} flex flex-col items-center py-1">
                    <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                    <span>Sản phẩm</span>
                </a>
                <a href="{{ route('lookup.index') }}" class="{{ request()->routeIs('lookup*') ? 'text-[#1e3a8a] font-bold' : 'text-slate-500' }} flex flex-col items-center py-1">
                    <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <span>Tra cứu</span>
                </a>
                <a href="{{ route('storefront.cart') }}" class="{{ request()->routeIs('storefront.cart*') || request()->routeIs('storefront.checkout*') ? 'text-[#1e3a8a] font-bold' : 'text-slate-500' }} flex flex-col items-center py-1 relative">
                    <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                    <span>Giỏ hàng</span>
                    <span id="mobile-bottom-cart-badge" class="absolute top-0 right-2 px-1.5 py-0.2 bg-rose-500 text-white rounded-full text-[9px] font-black hidden">0</span>
                </a>
                <a href="{{ auth('customer')->check() ? route('customer.profile') : route('customer.login') }}" class="{{ request()->routeIs('customer*') ? 'text-[#1e3a8a] font-bold' : 'text-slate-500' }} flex flex-col items-center py-1">
                    <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    <span>{{ auth('customer')->check() ? 'Tài khoản' : 'Đăng nhập' }}</span>
                </a>
            </div>
        </nav>
    @endif

    <!-- ĐÃ CHUYỂN TOÀN BỘ GIỎ HÀNG SANG TRANG RIÊNG BIỆT (/gio-hang) THEO YÊU CẦU NGƯỜI DÙNG -->

    <!-- 8. ORDER SUCCESS POP-UP MODAL (Dynamic VietQR) -->
    <div id="order-success-modal" class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 text-center space-y-4 shadow-2xl border border-slate-100">
            <div class="w-16 h-16 mx-auto rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-3xl font-black">
                ✓
            </div>
            <div>
                <h3 class="text-xl font-black text-slate-900">Đặt Hàng Thành Công!</h3>
                <p class="text-xs text-slate-500 mt-1">Cảm ơn quý khách. Đơn hàng đã được tiếp nhận vào hệ thống.</p>
            </div>

            <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200 text-left space-y-2 text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-500">Mã đơn hàng:</span>
                    <span id="success-order-code" class="font-mono font-bold text-slate-900 text-sm"></span>
                </div>
                <div class="flex justify-between border-t border-slate-200 pt-2 text-sm">
                    <span class="font-bold text-slate-700">Tổng thanh toán:</span>
                    <span id="success-grand-total" class="font-black font-mono text-rose-600"></span>
                </div>
            </div>

            <div id="success-vietqr-container" class="hidden bg-slate-50 p-3 rounded-2xl border border-slate-200">
                <p class="text-xs font-bold text-slate-700 mb-2">Mở app Ngân hàng quét mã VietQR để thanh toán:</p>
                <img id="success-vietqr-img" src="" alt="Mã VietQR" class="w-44 h-44 mx-auto rounded-xl object-contain border border-slate-200 bg-white p-1" />
                <p class="text-[10px] text-slate-400 mt-1.5">Hệ thống sẽ tự động cập nhật trạng thái đã thanh toán sau 3 giây</p>
            </div>

            <div class="pt-2">
                <button type="button" onclick="closeSuccessModal()" class="w-full py-2.5 px-4 bg-[#059669] hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition">
                    Tiếp Tục Mua Sắm
                </button>
            </div>
        </div>
    </div>

    <!-- 9. LIVE CHAT 24/7 SUPPORT WIDGET -->
    @include('storefront.components.livechat')

    <!-- 10. GLOBAL JAVASCRIPT LOGIC (Shared by all pages) -->
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
            const totalCount = cart.reduce((sum, item) => sum + (item.quantity || 1), 0);

            const globalBadge = document.getElementById('global-cart-badge');
            if (globalBadge) {
                globalBadge.textContent = totalCount;
                if (totalCount > 0) {
                    globalBadge.classList.remove('hidden');
                } else {
                    globalBadge.classList.add('hidden');
                }
            }

            const mobileBadge = document.getElementById('mobile-bottom-cart-badge');
            if (mobileBadge) {
                mobileBadge.textContent = totalCount;
                if (totalCount > 0) {
                    mobileBadge.classList.remove('hidden');
                } else {
                    mobileBadge.classList.add('hidden');
                }
            }

            const mobileStickyBadge = document.getElementById('mobile-sticky-cart-badge');
            if (mobileStickyBadge) {
                mobileStickyBadge.textContent = totalCount;
                if (totalCount > 0) {
                    mobileStickyBadge.classList.remove('hidden');
                } else {
                    mobileStickyBadge.classList.add('hidden');
                }
            }

            const drawerCount = document.getElementById('drawer-item-count');
            if (drawerCount) {
                drawerCount.textContent = `${totalCount} sản phẩm`;
            }
        }

        function addToCart(productId, unitId, name, unitName, price, imageUrl, openDrawer = false) {
            const cart = getCart();
            const existingIndex = cart.findIndex(i => i.product_id === productId && i.unit_id === unitId);

            if (existingIndex > -1) {
                cart[existingIndex].quantity += 1;
            } else {
                cart.push({
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

            // Bật Toast thông báo nhẹ nhàng, không bung cả khung giỏ hàng gây khó chịu
            if (typeof window.showToast === 'function') {
                window.showToast(`Đã thêm "${name}" vào giỏ hàng!`, 'success', 2500);
            }

            // Hiệu ứng nảy nhẹ nút giỏ hàng trên thanh header để người dùng nhận biết
            const cartBadges = document.querySelectorAll('#global-header-cart-badge, #global-cart-badge, #mobile-bottom-cart-badge, #cart-badge-count');
            cartBadges.forEach(badge => {
                badge.classList.add('scale-150', 'bg-amber-400');
                setTimeout(() => {
                    badge.classList.remove('scale-150', 'bg-amber-400');
                }, 400);
            });

            if (openDrawer) {
                openCartDrawer();
            }
        }

        function removeFromCart(index) {
            const cart = getCart();
            cart.splice(index, 1);
            saveCart(cart);
        }

        function updateCartQuantity(index, delta) {
            const cart = getCart();
            if (cart[index]) {
                cart[index].quantity += delta;
                if (cart[index].quantity <= 0) {
                    cart.splice(index, 1);
                }
                saveCart(cart);
            }
        }

        function formatCurrency(num) {
            return new Intl.NumberFormat('vi-VN').format(num || 0) + '₫';
        }
        window.formatCurrency = formatCurrency;
        window.formatMoney = formatCurrency;

        function renderCart() {
            const cart = getCart();
            const container = document.getElementById('cart-items-container');
            if (!container) return;

            if (cart.length === 0) {
                container.innerHTML = `
                    <div class="py-12 text-center text-slate-400 space-y-2">
                        <span class="text-4xl block">🛒</span>
                        <p class="text-sm font-semibold">Giỏ hàng của bạn đang trống</p>
                        <p class="text-xs">Hãy chọn mua các mặt hàng văn phòng phẩm & mực in để thêm vào giỏ</p>
                    </div>
                `;
                document.getElementById('drawer-subtotal').textContent = '0 VNĐ';
                document.getElementById('drawer-grand-total').textContent = '0 VNĐ';
                return;
            }

            let subtotal = 0;
            let html = '';

            cart.forEach((item, index) => {
                const itemTotal = item.price * item.quantity;
                subtotal += itemTotal;
                html += `
                    <div class="pt-3 pb-3 flex items-center justify-between gap-3 text-xs">
                        <img src="${item.image_url}" alt="${item.name}" class="w-12 h-12 object-contain rounded-lg bg-slate-50 border p-1 shrink-0" />
                        <div class="flex-1 min-w-0">
                            <h5 class="font-bold text-slate-800 line-clamp-1">${item.name}</h5>
                            <div class="text-slate-500 text-[11px] font-mono mt-0.5">
                                ${formatCurrency(item.price)} / ${item.unit_name}
                            </div>
                        </div>
                        <div class="flex items-center space-x-1.5 shrink-0">
                            <button type="button" onclick="updateCartQuantity(${index}, -1)" class="w-6 h-6 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold">-</button>
                            <span class="font-bold text-slate-800 text-xs px-1">${item.quantity}</span>
                            <button type="button" onclick="updateCartQuantity(${index}, 1)" class="w-6 h-6 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold">+</button>
                            <button type="button" onclick="removeFromCart(${index})" class="text-rose-500 hover:text-rose-700 p-1 ml-1">✕</button>
                        </div>
                    </div>
                `;
            });

            container.innerHTML = html;
            const subtotalEl = document.getElementById('drawer-subtotal');
            if (subtotalEl) subtotalEl.textContent = formatCurrency(subtotal);
            const grandTotalEl = document.getElementById('drawer-grand-total');
            if (grandTotalEl) grandTotalEl.textContent = formatCurrency(subtotal);
        }

        function openCartDrawer() {
            window.location.href = "{{ route('storefront.cart') }}";
        }

        function closeCartDrawer() {
            // Không còn dùng drawer nửa màn hình
        }

        function closeSuccessModal() {
            const modal = document.getElementById('order-success-modal');
            if (modal) modal.classList.add('hidden');
        }

        async function submitOnlineOrder(e) {
            e.preventDefault();
            const checkoutForm = document.getElementById('online-checkout-form');
            if (!window.CheckoutValidation.validate(checkoutForm)) return;
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
                        ...window.InvoiceForm.read('drawer-invoice'),
                        items: cart.map(i => ({
                            product_id: i.product_id,
                            unit_id: i.unit_id,
                            quantity: i.quantity,
                        })),
                    }),
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    const validationMessage = window.CheckoutValidation.serverErrors(checkoutForm, data.errors, errorContainer);
                    throw new Error(validationMessage || data.message || 'Không thể tạo đơn hàng, vui lòng kiểm tra lại thông tin.');
                }

                localStorage.removeItem(CART_STORAGE_KEY);
                updateCartBadges();
                closeCartDrawer();

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

        document.addEventListener('DOMContentLoaded', () => {
            updateCartBadges();
        });

        // ==========================================
        // HỆ THỐNG TOAST THÔNG BÁO HIỆN ĐẠI TOÀN TRANG
        // ==========================================
        function showToast(message, type = 'success', duration = 4500) {
            const container = document.getElementById('toastContainer');
            if (!container || !message) return;

            const toast = document.createElement('div');
            toast.className = 'pointer-events-auto flex items-start p-4 rounded-2xl shadow-xl border backdrop-blur-md transition-all duration-300 transform translate-y-[-10px] opacity-0 bg-white/95';

            let icon = '✓';
            let title = 'Thành công';
            let borderClass = 'border-emerald-300 shadow-emerald-500/10';
            let iconBg = 'bg-emerald-500 text-white shadow-emerald-500/20';

            if (type === 'success') {
                icon = '✓';
                title = 'Thành công';
                borderClass = 'border-emerald-300 shadow-emerald-500/10';
                iconBg = 'bg-emerald-500 text-white';
            } else if (type === 'warning') {
                icon = '⚠️';
                title = 'Cảnh báo';
                borderClass = 'border-amber-300 shadow-amber-500/10';
                iconBg = 'bg-amber-500 text-white';
            } else if (type === 'error') {
                icon = '✕';
                title = 'Lỗi / Không thể thực hiện';
                borderClass = 'border-rose-300 shadow-rose-500/10';
                iconBg = 'bg-rose-500 text-white';
            } else if (type === 'info') {
                icon = 'ℹ';
                title = 'Thông báo';
                borderClass = 'border-blue-300 shadow-blue-500/10';
                iconBg = 'bg-blue-500 text-white';
            }

            toast.classList.add(...borderClass.split(' '));

            toast.innerHTML = `
                <div class="w-8 h-8 rounded-xl flex items-center justify-center font-black text-xs shrink-0 mr-3 shadow-sm ${iconBg}">
                    ${icon}
                </div>
                <div class="flex-1 mr-2 text-xs">
                    <div class="font-extrabold text-slate-900">${title}</div>
                    <div class="text-slate-600 mt-0.5 leading-relaxed font-medium">${message}</div>
                </div>
                <button type="button" class="text-slate-400 hover:text-slate-700 font-bold text-base p-1 leading-none shrink-0 transition" onclick="this.parentElement.remove()">
                    &times;
                </button>
            `;

            container.appendChild(toast);

            // Hiệu ứng trượt xuống mượt mà
            requestAnimationFrame(() => {
                toast.classList.remove('translate-y-[-10px]', 'opacity-0');
                toast.classList.add('translate-y-0', 'opacity-100');
            });

            // Tự động ẩn sau thời gian quy định
            setTimeout(() => {
                toast.classList.remove('opacity-100', 'translate-y-0');
                toast.classList.add('opacity-0', 'translate-y-[-10px]');
                setTimeout(() => toast.remove(), 300);
            }, duration);
        }
        window.showToast = showToast;

        // Tự động quét và hiển thị Toast từ session Backend Laravel
        document.addEventListener('DOMContentLoaded', () => {
            @if(session('success'))
                showToast(@json(session('success')), 'success');
            @endif
            @if(session('warning'))
                showToast(@json(session('warning')), 'warning', 6000);
            @endif
            @if(session('error'))
                showToast(@json(session('error')), 'error', 6000);
            @endif
            @if(session('info'))
                showToast(@json(session('info')), 'info');
            @endif
            @if($errors->any())
                @foreach($errors->all() as $err)
                    showToast(@json($err), 'error', 6000);
                @endforeach
            @endif
        });
    </script>

    <script src="{{ asset('js/invoice-form.js') }}?v={{ filemtime(public_path('js/invoice-form.js')) }}" defer></script>
    <script src="{{ asset('js/checkout-validation.js') }}?v={{ filemtime(public_path('js/checkout-validation.js')) }}" defer></script>
    @stack('scripts')
</body>
</html>
