<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tra Cứu Tiến Độ Sửa Máy In - VPP & Dịch Vụ Máy In</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col justify-between antialiased">

    <!-- Header Navigation Bar -->
    <header class="bg-indigo-950 text-white shadow-md sticky top-0 z-40 border-b border-indigo-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5 flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center space-x-3 group">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-600 to-amber-500 flex items-center justify-center font-black text-white text-base shadow">
                    VP
                </div>
                <div>
                    <span class="font-extrabold text-sm sm:text-base tracking-wide block leading-tight">VPP & DỊCH VỤ MÁY IN</span>
                    <span class="text-[10px] text-amber-400 font-semibold uppercase block">Hệ thống tra cứu tiến độ online</span>
                </div>
            </a>
            <div class="flex items-center space-x-2">
                <a href="{{ url('/') }}" class="text-xs bg-indigo-900 hover:bg-indigo-800 text-indigo-200 px-3 py-1.5 rounded-xl border border-indigo-800">
                    ← Trang chủ
                </a>
                <a href="tel:0901234567" class="text-xs bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold px-3 py-1.5 rounded-xl shadow">
                    📞 Hotline: 0901.234.567
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content Container (Responsive max-w-4xl for PC & Tablets) -->
    <main class="max-w-4xl mx-auto w-full px-4 sm:px-6 py-8 sm:py-12 flex-1">
        
        <div class="text-center mb-8">
            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-indigo-100 text-indigo-700 rounded-3xl flex items-center justify-center mx-auto mb-3 shadow-sm border border-indigo-200">
                <svg class="w-8 h-8 sm:w-10 sm:h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">TRA CỨU TIẾN ĐỘ SỬA CHỮA MÁY IN</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-md mx-auto">
                Hệ thống bảo mật 2 lớp chống rò rỉ thông tin. Vui lòng nhập Mã phiếu và 4 số cuối số điện thoại gửi máy.
            </p>
        </div>

        @if($errors->has('lookup_error'))
        <div class="max-w-xl mx-auto mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl text-xs sm:text-sm flex items-start space-x-2.5 shadow-sm">
            <span class="text-lg leading-none">⚠️</span>
            <span>{{ $errors->first('lookup_error') }}</span>
        </div>
        @endif

        <!-- Form Card (Responsive Layout) -->
        <div class="max-w-xl mx-auto bg-white p-6 sm:p-8 rounded-3xl shadow-lg border border-slate-200">
            <form action="{{ route('lookup.search') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-xs font-black text-slate-700 uppercase tracking-wider mb-1.5">
                        1. Mã phiếu tiếp nhận *
                    </label>
                    <input type="text" name="ticket_code" value="{{ old('ticket_code', request('code')) }}" 
                        placeholder="VD: SC260001" 
                        required 
                        class="w-full px-4 py-3 rounded-2xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 font-mono text-base sm:text-lg font-bold uppercase placeholder:font-sans placeholder:normal-case placeholder:text-slate-400 bg-slate-50" />
                    <p class="text-[11px] text-slate-400 mt-1">In ở góc trên bên phải phiếu hẹn hoặc cuống dán máy in</p>
                </div>

                <div>
                    <label class="block text-xs font-black text-slate-700 uppercase tracking-wider mb-1.5">
                        2. 4 số cuối số điện thoại gửi máy *
                    </label>
                    <input type="text" name="phone_last4" maxlength="4" value="{{ old('phone_last4') }}" 
                        placeholder="VD: 5678" 
                        required 
                        class="w-full px-4 py-3 rounded-2xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 font-mono text-xl sm:text-2xl font-black tracking-widest text-center placeholder:font-sans placeholder:tracking-normal placeholder:text-slate-400 bg-slate-50" />
                    <p class="text-[11px] text-slate-400 mt-1">Cơ chế chống IDOR bảo mật tuyệt đối, chỉ khách hàng sở hữu mới tra cứu được</p>
                </div>

                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-extrabold py-3.5 px-6 rounded-2xl shadow-md flex items-center justify-center space-x-2 transition transform hover:-translate-y-0.5 text-sm sm:text-base">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>KIỂM TRA TIẾN ĐỘ NGAY</span>
                </button>
            </form>
        </div>

        <!-- Sample Quick Tickets Box -->
        <div class="max-w-xl mx-auto mt-6 p-4 sm:p-5 bg-indigo-50/80 border border-indigo-100 rounded-2xl text-xs text-slate-600">
            <span class="font-bold text-indigo-950 block mb-2 text-sm">💡 Các mã phiếu mẫu để thử nghiệm nhanh:</span>
            <ul class="space-y-1.5">
                <li class="flex items-center justify-between bg-white p-2 rounded-xl border border-indigo-100">
                    <span>Canon 2900 (Đang sửa):</span>
                    <span class="font-mono">Mã: <b class="text-indigo-700">SC260001</b> | 4 số cuối SĐT: <b class="text-indigo-700">5678</b></span>
                </li>
                <li class="flex items-center justify-between bg-white p-2 rounded-xl border border-indigo-100">
                    <span>Brother L2321D (Đã xong):</span>
                    <span class="font-mono">Mã: <b class="text-indigo-700">SC260002</b> | 4 số cuối SĐT: <b class="text-indigo-700">5432</b></span>
                </li>
                <li class="flex items-center justify-between bg-white p-2 rounded-xl border border-indigo-100">
                    <span>HP LaserJet 107a (Đang kiểm tra):</span>
                    <span class="font-mono">Mã: <b class="text-indigo-700">SC260003</b> | 4 số cuối SĐT: <b class="text-indigo-700">2233</b></span>
                </li>
            </ul>
        </div>

    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 text-xs py-8 border-t border-slate-800 text-center space-y-2">
        <div class="max-w-7xl mx-auto px-4">
            <p>© 2026 Cửa Hàng Văn Phòng Phẩm & Dịch Vụ Máy In. Bảo mật chống IDOR 100%.</p>
            <div class="flex justify-center space-x-3 pt-2 text-[11px] text-slate-400">
                <a href="{{ url('/') }}" class="hover:text-white transition">Trang Chủ</a>
                <span>•</span>
                <a href="{{ route('storefront.about') }}" class="hover:text-white transition">Giới Thiệu</a>
                <span>•</span>
                <a href="{{ route('storefront.terms') }}" class="hover:text-white transition">Chính Sách Mua Hàng</a>
                <span>•</span>
                <a href="{{ route('storefront.privacy') }}" class="hover:text-white transition font-bold text-emerald-400">Bảo Mật Dữ Liệu</a>
            </div>
        </div>
    </footer>

    <!-- Floating Hotline & Zalo (Bottom-Left) -->
    <div class="fixed bottom-6 left-6 z-40 flex flex-col space-y-2">
        <a href="https://zalo.me/0901234567" target="_blank" class="w-12 h-12 bg-blue-600 hover:bg-blue-500 text-white rounded-full flex items-center justify-center shadow-lg font-bold text-xs transform hover:scale-110 transition border-2 border-white">
            Zalo
        </a>
        <a href="tel:0901234567" class="w-12 h-12 bg-red-600 hover:bg-red-500 text-white rounded-full flex items-center justify-center shadow-lg transform hover:scale-110 transition animate-pulse border-2 border-white">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M6.62 10.79a15.053 15.053 0 006.59 6.59l2.2-2.2a1 1 0 011.01-.24c1.12.37 2.33.57 3.58.57a1 1 0 011 1V20a1 1 0 01-1 1A17 17 0 013 4a1 1 0 011-1h3.5a1 1 0 011 1c0 1.25.2 2.45.57 3.57a1 1 0 01-.25 1.02l-2.2 2.2z"></path></svg>
        </a>
    </div>

    <!-- Live Chat Widget -->
    @include('storefront.components.livechat')

</body>
</html>
