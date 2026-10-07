<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Cửa Hàng Văn Phòng Phẩm & Dịch Vụ Máy In')</title>
    <meta name="description" content="@yield('meta_description', 'Văn phòng phẩm giá sỉ, giấy in Double A, hộp mực máy in Canon, HP, Brother, Epson. Sửa chữa máy in lấy ngay trong ngày.')">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col justify-between antialiased selection:bg-indigo-600 selection:text-white">

    <!-- 1. Top Announcement Bar (Gọn gàng, súc tích) -->
    <div class="bg-indigo-950 text-slate-300 text-xs py-1.5 border-b border-indigo-900/60 hidden sm:block">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center text-[11px]">
            <div class="flex items-center space-x-3">
                <span class="text-amber-400 font-bold">⚡ Nạp mực & Sửa máy in 30 phút</span>
                <span class="text-slate-600">|</span>
                <span>📍 123 Đường Văn Phòng Phẩm, P. Bến Nghé, Q.1, TP.HCM</span>
                <span class="text-slate-600">|</span>
                <span>⏰ 7h30 - 20h00 (T2 - CN)</span>
            </div>
            <div class="flex items-center space-x-3">
                <span>Hotline: <a href="tel:0901234567" class="text-white font-bold hover:text-amber-300">0901.234.567</a></span>
                <span class="text-slate-600">|</span>
                <a href="https://zalo.me/0901234567" target="_blank" class="text-sky-400 font-semibold hover:underline">Zalo Tư Vấn</a>
            </div>
        </div>
    </div>

    <!-- 2. Sticky Header Navigation (Cố định, hiện đại, từ ngữ ngắn gọn) -->
    <header class="bg-white/95 backdrop-blur-md sticky top-0 z-40 border-b border-slate-200 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-18">

                <!-- Logo & Brand Name -->
                <a href="{{ route('storefront.index') }}" class="flex items-center space-x-2.5 group flex-shrink-0">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-indigo-700 via-indigo-600 to-amber-500 flex items-center justify-center font-black text-white text-base shadow group-hover:scale-105 transition transform">
                        VP
                    </div>
                    <div>
                        <span class="text-base sm:text-lg font-black tracking-tight text-slate-900 block leading-tight">
                            VPP & MÁY IN
                        </span>
                        <span class="text-[10px] text-indigo-600 font-bold uppercase tracking-wider block">
                            Văn Phòng Phẩm Sỉ • Sửa Máy In
                        </span>
                    </div>
                </a>

                <!-- Desktop Search Bar -->
                <div class="hidden md:flex flex-1 max-w-sm mx-6">
                    <form action="{{ route('storefront.products') }}" method="GET" class="w-full relative">
                        <input type="text" name="q" value="{{ request('q') }}"
                            placeholder="Tìm giấy in, bút, hộp mực 12A..."
                            class="w-full text-xs pl-9 pr-20 py-2 rounded-full border border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-600 bg-slate-50/70" />
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <button type="submit" class="absolute right-1 top-1 px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-white text-[11px] font-bold rounded-full transition">
                            Tìm
                        </button>
                    </form>
                </div>

                <!-- Desktop Nav Links -->
                <nav class="hidden lg:flex items-center space-x-5 text-xs font-bold text-slate-700">
                    <a href="{{ route('storefront.index') }}" class="hover:text-indigo-600 transition {{ request()->routeIs('storefront.index') ? 'text-indigo-600' : '' }}">Trang Chủ</a>
                    <a href="{{ route('storefront.products') }}" class="hover:text-indigo-600 transition {{ request()->routeIs('storefront.products*') ? 'text-indigo-600' : '' }}">Sản Phẩm</a>
                    <a href="{{ route('storefront.wholesale') }}" class="hover:text-indigo-600 transition {{ request()->routeIs('storefront.wholesale') ? 'text-indigo-600' : '' }} text-amber-700">Đại Lý Sỉ</a>
                    <a href="{{ route('lookup.index') }}" class="hover:text-indigo-600 transition {{ request()->routeIs('lookup*') ? 'text-indigo-600' : '' }}">Tra Cứu</a>
                    <a href="{{ route('storefront.about') }}" class="hover:text-indigo-600 transition {{ request()->routeIs('storefront.about') ? 'text-indigo-600' : '' }}">Giới Thiệu</a>
                    <a href="{{ route('storefront.terms') }}" class="hover:text-indigo-600 transition {{ request()->routeIs('storefront.terms') ? 'text-indigo-600' : '' }}">Chính Sách</a>
                </nav>

                <!-- Action Buttons: Cart + Account + Admin POS -->
                <div class="flex items-center space-x-2 sm:space-x-3">
                    
                    <!-- Cart Button -->
                    <a href="{{ route('storefront.checkout-page') }}" class="relative inline-flex items-center space-x-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 px-3 py-2 rounded-xl text-xs font-bold transition shadow-2xs">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                        <span class="hidden sm:inline">Giỏ Hàng</span>
                        <span id="global-cart-badge" class="px-1.5 py-0.2 bg-emerald-600 text-white rounded-full text-[10px] font-black hidden">0</span>
                    </a>

                    <!-- Account / Login Button -->
                    @if(auth('customer')->check())
                    <a href="{{ route('customer.profile') }}" class="inline-flex items-center space-x-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 px-3 py-2 rounded-xl text-xs font-bold transition">
                        <span>👤</span>
                        <span class="hidden sm:inline">{{ Str::limit(auth('customer')->user()->name, 12) }}</span>
                    </a>
                    @else
                    <a href="{{ route('customer.login') }}" class="inline-flex items-center space-x-1 bg-slate-100 hover:bg-slate-200 text-slate-800 border border-slate-200 px-3 py-2 rounded-xl text-xs font-bold transition">
                        <span>Đăng Nhập</span>
                    </a>
                    @endif

                    <!-- POS Admin Shortcut -->
                    <a href="{{ url('/admin') }}" class="hidden sm:inline-flex items-center space-x-1 bg-slate-900 hover:bg-indigo-900 text-white px-2.5 py-2 rounded-xl text-xs font-bold transition shadow-xs">
                        <span class="text-amber-400">⚡</span>
                        <span>POS</span>
                    </a>

                </div>

            </div>

            <!-- Mobile Search & Sub-nav Pills -->
            <div class="pb-2.5 md:hidden space-y-2">
                <form action="{{ route('storefront.products') }}" method="GET" class="relative">
                    <input type="text" name="q" value="{{ request('q') }}"
                        placeholder="Tìm bút, giấy Double A, mực in..."
                        class="w-full text-xs pl-8 pr-16 py-1.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-600 bg-slate-50" />
                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <button type="submit" class="absolute right-1 top-1 px-2.5 py-1 bg-indigo-600 text-white text-[10px] font-bold rounded-lg">Tìm</button>
                </form>

                <div class="flex items-center space-x-1.5 overflow-x-auto no-scrollbar text-[11px] font-bold pt-0.5">
                    <a href="{{ route('storefront.index') }}" class="px-2.5 py-1 rounded-full whitespace-nowrap {{ request()->routeIs('storefront.index') ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-700' }}">Trang Chủ</a>
                    <a href="{{ route('storefront.products') }}" class="px-2.5 py-1 rounded-full whitespace-nowrap {{ request()->routeIs('storefront.products*') ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-700' }}">Sản Phẩm</a>
                    <a href="{{ route('storefront.wholesale') }}" class="px-2.5 py-1 rounded-full whitespace-nowrap {{ request()->routeIs('storefront.wholesale') ? 'bg-indigo-600 text-white' : 'bg-amber-50 text-amber-800 border border-amber-200' }}">Đại Lý Sỉ</a>
                    <a href="{{ route('lookup.index') }}" class="px-2.5 py-1 rounded-full whitespace-nowrap bg-slate-100 text-slate-700">Tra Cứu</a>
                    <a href="{{ route('storefront.terms') }}" class="px-2.5 py-1 rounded-full whitespace-nowrap bg-slate-100 text-slate-700">Chính Sách</a>
                </div>
            </div>

        </div>
    </header>

    <!-- Flash Notifications -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 w-full">
        @if(session('success'))
        <div class="p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs sm:text-sm flex items-center space-x-2 shadow-xs mb-3">
            <span class="text-base font-bold">✓</span>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        @if(session('error'))
        <div class="p-3.5 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs sm:text-sm flex items-center space-x-2 shadow-xs mb-3">
            <span class="text-base font-bold">⚠️</span>
            <span>{{ session('error') }}</span>
        </div>
        @endif

        @if($errors->any())
        <div class="p-3.5 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs sm:text-sm space-y-1 shadow-xs mb-3">
            @foreach($errors->all() as $err)
            <p>• {{ $err }}</p>
            @endforeach
        </div>
        @endif
    </div>

    <!-- Main Content Body -->
    <main class="flex-1 w-full">
        @yield('content')
    </main>

    <!-- 3. Enterprise Footer (4 Cột súc tích, chuyên nghiệp) -->
    <footer class="bg-slate-900 text-slate-400 text-xs mt-12 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">

                <!-- Col 1: Showroom Info -->
                <div class="space-y-3">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-8 h-8 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-black">VP</div>
                        <span class="text-base font-black text-white">VPP & DỊCH VỤ MÁY IN</span>
                    </div>
                    <p class="text-slate-400 text-xs leading-relaxed">
                        Cung cấp 1.000+ mặt hàng văn phòng phẩm giá sỉ và dịch vụ nạp mực, sửa chữa máy in lấy ngay trong 30 phút.
                    </p>
                    <div class="text-slate-300 space-y-1 text-xs">
                        <p>📍 123 Đường Văn Phòng Phẩm, P. Bến Nghé, Q.1, TP.HCM</p>
                        <p>📞 Hotline: <b class="text-amber-400">0901.234.567</b></p>
                        <p>💬 Zalo: <b class="text-sky-400">0901.234.567</b></p>
                        <p>⏰ 7h30 - 20h00 (Tất cả các ngày trong tuần)</p>
                    </div>
                </div>

                <!-- Col 2: Danh Mục Nhanh -->
                <div>
                    <h4 class="text-white font-bold uppercase text-xs tracking-wider mb-3">Ngành Hàng Nổi Bật</h4>
                    <ul class="space-y-2 text-xs">
                        <li><a href="{{ route('storefront.products', ['category' => 'giay-in-photo']) }}" class="hover:text-white transition">Giấy In Double A, PaperOne (Ram / Thùng)</a></li>
                        <li><a href="{{ route('storefront.products', ['category' => 'but-viet-muc-viet']) }}" class="hover:text-white transition">Bút Bi, Bút Gel, Bút Lông Thiên Long</a></li>
                        <li><a href="{{ route('storefront.products', ['category' => 'bia-file-ho-so']) }}" class="hover:text-white transition">Bìa Còng Kokuyo, Bìa Lá, File Hồ Sơ</a></li>
                        <li><a href="{{ route('storefront.products', ['category' => 'dung-cu-van-phong']) }}" class="hover:text-white transition">Dụng Cụ Văn Phòng, Bấm Kim, Băng Keo</a></li>
                        <li><a href="{{ route('storefront.products', ['category' => 'hop-muc-may-in']) }}" class="hover:text-white transition">Hộp Mực Cartridge 12A, TN-2385...</a></li>
                    </ul>
                </div>

                <!-- Col 3: Dịch Vụ & Đại Lý -->
                <div>
                    <h4 class="text-white font-bold uppercase text-xs tracking-wider mb-3">Khách Hàng & Đại Lý</h4>
                    <ul class="space-y-2 text-xs">
                        <li><a href="{{ route('storefront.wholesale') }}" class="hover:text-amber-300 font-bold text-amber-400 transition">⭐ Đăng Ký Đại Lý Mua Sỉ (Chiết Khấu 20%)</a></li>
                        <li><a href="{{ route('storefront.index') }}#dat-lich" class="hover:text-white transition">Đặt Thợ Nạp Mực & Sửa Máy In Tận Nơi</a></li>
                        <li><a href="{{ route('lookup.index') }}" class="hover:text-white transition">Tra Cứu Tiến Độ Phiếu Sửa Chữa Online</a></li>
                        <li><a href="{{ route('customer.login') }}" class="hover:text-white transition">Tài Khoản & Lịch Sử Đơn Hàng</a></li>
                        <li><a href="{{ url('/admin') }}" class="hover:text-white transition">Cổng Quản Trị & Thu Ngân POS</a></li>
                    </ul>
                </div>

                <!-- Col 4: Chính Sách & Cam Kết -->
                <div>
                    <h4 class="text-white font-bold uppercase text-xs tracking-wider mb-3">Chính Sách & Bảo Mật</h4>
                    <ul class="space-y-2 text-xs">
                        <li><a href="{{ route('storefront.privacy') }}" class="hover:text-emerald-300 font-bold text-emerald-400 transition">🛡️ Bảo Mật Dữ Liệu Máy In & Bản In (100%)</a></li>
                        <li><a href="{{ route('storefront.terms') }}" class="hover:text-white transition">Chính Sách Mua Hàng & Giao Hỏa Tốc 2H</a></li>
                        <li><a href="{{ route('storefront.terms') }}" class="hover:text-white transition">Chính Sách Đồng Kiểm & Đổi Trả 7 Ngày</a></li>
                        <li><a href="{{ route('storefront.terms') }}" class="hover:text-white transition">Thanh Toán VietQR & Hóa Đơn VAT Điện Tử</a></li>
                        <li><a href="{{ route('storefront.about') }}" class="hover:text-white transition">Giới Thiệu Về Cửa Hàng & Showroom</a></li>
                    </ul>
                </div>

            </div>

            <!-- Bottom Copyright -->
            <div class="mt-10 pt-6 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between text-[11px] text-slate-500 gap-3">
                <p>© 2026 Cửa Hàng Văn Phòng Phẩm & Dịch Vụ Máy In. Nền tảng Laravel 13 & Filament PHP.</p>
                <div class="flex flex-wrap items-center gap-3 text-slate-400">
                    <a href="{{ route('storefront.about') }}" class="hover:text-white transition">Giới Thiệu</a>
                    <span>•</span>
                    <a href="{{ route('storefront.terms') }}" class="hover:text-white transition">Mua Hàng</a>
                    <span>•</span>
                    <a href="{{ route('storefront.privacy') }}" class="hover:text-white transition text-emerald-400 font-semibold">Bảo Mật Dữ Liệu</a>
                    <span>•</span>
                    <a href="{{ route('lookup.index') }}" class="hover:text-white transition text-amber-400">Tra Cứu Phiếu</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- 4. Mobile Bottom Navigation Bar (5 Items cố định màn hình điện thoại) -->
    <nav class="bg-white/95 backdrop-blur-md border-t border-slate-200 sticky bottom-0 z-40 md:hidden w-full shadow-lg">
        <div class="grid grid-cols-5 text-center text-[10px] py-2 font-medium">
            <a href="{{ route('storefront.index') }}" class="{{ request()->routeIs('storefront.index') ? 'text-indigo-600 font-bold' : 'text-slate-500' }} flex flex-col items-center">
                <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                <span>Trang chủ</span>
            </a>
            <a href="{{ route('storefront.products') }}" class="{{ request()->routeIs('storefront.products*') ? 'text-indigo-600 font-bold' : 'text-slate-500' }} flex flex-col items-center">
                <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                <span>Sản phẩm</span>
            </a>
            <a href="{{ route('storefront.checkout-page') }}" class="{{ request()->routeIs('storefront.checkout-page') ? 'text-indigo-600 font-bold' : 'text-slate-500' }} flex flex-col items-center relative">
                <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                <span>Giỏ hàng</span>
                <span id="mobile-bottom-cart-badge" class="absolute -top-1 right-2 px-1.5 py-0.2 bg-emerald-600 text-white rounded-full text-[9px] font-black hidden">0</span>
            </a>
            <a href="{{ route('storefront.wholesale') }}" class="{{ request()->routeIs('storefront.wholesale') ? 'text-indigo-600 font-bold' : 'text-slate-500' }} flex flex-col items-center">
                <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                <span>Đại lý sỉ</span>
            </a>
            @if(auth('customer')->check())
            <a href="{{ route('customer.profile') }}" class="{{ request()->routeIs('customer.profile') ? 'text-indigo-600 font-bold' : 'text-slate-500' }} flex flex-col items-center">
                <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                <span>Tài khoản</span>
            </a>
            @else
            <a href="{{ route('customer.login') }}" class="text-slate-500 flex flex-col items-center">
                <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                <span>Đăng nhập</span>
            </a>
            @endif
        </div>
    </nav>

    <!-- 5. Floating Pinned Hotline & Zalo (Góc dưới bên trái, tránh Live Chat) -->
    <div class="fixed bottom-20 sm:bottom-6 left-4 sm:left-6 z-40 flex flex-col space-y-2">
        <a href="https://zalo.me/0901234567" target="_blank" 
           class="w-12 h-12 bg-blue-600 hover:bg-blue-500 text-white rounded-full flex items-center justify-center shadow-lg font-black text-xs transform hover:scale-110 transition border-2 border-white">
            Zalo
        </a>
        <a href="tel:0901234567" 
           class="w-12 h-12 bg-red-600 hover:bg-red-500 text-white rounded-full flex items-center justify-center shadow-lg transform hover:scale-110 transition animate-pulse border-2 border-white"
           title="Gọi ngay hotline 0901.234.567">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M6.62 10.79a15.053 15.053 0 006.59 6.59l2.2-2.2a1 1 0 011.01-.24c1.12.37 2.33.57 3.58.57a1 1 0 011 1V20a1 1 0 01-1 1A17 17 0 013 4a1 1 0 011-1h3.5a1 1 0 011 1c0 1.25.2 2.45.57 3.57a1 1 0 01-.25 1.02l-2.2 2.2z"></path></svg>
        </a>
    </div>

    <!-- 6. Live Chat 24/7 (Góc dưới bên phải) -->
    @include('storefront.components.livechat')

    <!-- Unified Cart Logic Script (Zero dependencies) -->
    <script>
        const CART_KEY = 'vpp_cart_items';

        function getCart() {
            try {
                return JSON.parse(localStorage.getItem(CART_KEY)) || [];
            } catch {
                return [];
            }
        }

        function saveCart(cart) {
            localStorage.setItem(CART_KEY, JSON.stringify(cart));
            updateCartBadges();
        }

        function updateCartBadges() {
            const cart = getCart();
            const totalQty = cart.reduce((sum, i) => sum + (i.quantity || 1), 0);
            
            const badges = [
                document.getElementById('global-cart-badge'),
                document.getElementById('mobile-bottom-cart-badge'),
                document.getElementById('header-cart-badge'),
                document.getElementById('mobile-cart-badge')
            ];

            badges.forEach(b => {
                if (b) {
                    if (totalQty > 0) {
                        b.textContent = totalQty;
                        b.classList.remove('hidden');
                    } else {
                        b.classList.add('hidden');
                    }
                }
            });
        }

        function addToCart(productId, unitId, productName, unitName, price, imageUrl) {
            let cart = getCart();
            const key = productId + '_' + (unitId || 'base');
            const existing = cart.find(i => i.key === key);

            if (existing) {
                existing.quantity += 1;
            } else {
                cart.push({
                    key: key,
                    product_id: productId,
                    unit_id: unitId,
                    name: productName,
                    unit_name: unitName,
                    price: price,
                    image_url: imageUrl,
                    quantity: 1
                });
            }

            saveCart(cart);
            showToast('Đã thêm "' + productName + ' (' + unitName + ')" vào giỏ hàng!');
        }

        function showToast(msg) {
            let toast = document.getElementById('cart-toast');
            if (!toast) {
                toast = document.createElement('div');
                toast.id = 'cart-toast';
                toast.className = 'fixed top-20 right-4 sm:right-6 bg-slate-900 text-white text-xs px-4 py-2.5 rounded-2xl shadow-2xl z-50 transition transform duration-200 border border-slate-700 flex items-center space-x-2';
                document.body.appendChild(toast);
            }
            toast.innerHTML = '<span>🛒</span><span>' + msg + '</span>';
            toast.classList.remove('opacity-0', 'pointer-events-none');
            setTimeout(() => {
                toast.classList.add('opacity-0', 'pointer-events-none');
            }, 2500);
        }

        document.addEventListener('DOMContentLoaded', () => {
            updateCartBadges();
        });
    </script>

    @stack('scripts')
</body>
</html>
