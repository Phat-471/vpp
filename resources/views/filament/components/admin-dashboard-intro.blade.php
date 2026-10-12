@php
    $hour = now()->hour;
    $greeting = match(true) {
        $hour >= 5 && $hour < 12 => 'Buổi sáng tốt lành',
        $hour >= 12 && $hour < 18 => 'Buổi chiều năng suất',
        default => 'Buổi tối an lành',
    };
    $userName = auth()->user()?->name ?? 'Quản Trị Viên';

    $unreadChats = \App\Models\ChatMessage::where('sender_type', 'customer')->where('is_read', false)->count();
    $activeRepairs = \App\Models\RepairTicket::whereIn('status', ['received', 'diagnosing', 'quoted', 'in_progress'])->count();
    $overdueRepairs = \App\Models\RepairTicket::whereIn('status', ['received', 'diagnosing', 'quoted', 'in_progress'])
        ->whereNotNull('promised_at')
        ->where('promised_at', '<', now())
        ->count();
@endphp

<section class="admin-dashboard-intro" aria-labelledby="admin-dashboard-title">
    <div class="admin-dashboard-intro__copy">
        <p class="admin-dashboard-intro__eyebrow">
            <span class="admin-dashboard-intro__sun" aria-hidden="true">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"/>
                </svg>
            </span>
            <span>QUẢN LÝ CỬA HÀNG</span>
            <span class="admin-dashboard-intro__date">{{ now()->locale('vi')->translatedFormat('l, d/m/Y') }}</span>
        </p>
        
        <h1 id="admin-dashboard-title">
            {{ $greeting }}, <span class="text-transparent bg-clip-text bg-gradient-to-r from-white via-indigo-100 to-amber-200">{{ $userName }}</span>!
        </h1>
        
        <p class="admin-dashboard-intro__description">
            Theo dõi doanh thu, tồn kho và tiến độ sửa chữa.
        </p>

        <!-- Quick Status Badges -->
        <div class="mt-3 flex flex-wrap items-center gap-2 text-xs">
            @if ($unreadChats > 0)
                <a href="{{ route('filament.admin.pages.live-chat-page') }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-200 border border-emerald-400/30 hover:bg-emerald-500/30 transition-colors">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="font-bold">{{ $unreadChats }}</span> tin chưa đọc
                </a>
            @endif

            @if ($overdueRepairs > 0)
                <a href="{{ route('filament.admin.resources.repair-tickets.index') }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-rose-500/20 text-rose-200 border border-rose-400/30 hover:bg-rose-500/30 transition-colors">
                    <span class="w-2 h-2 rounded-full bg-rose-400 animate-ping"></span>
                    <span class="font-bold">{{ $overdueRepairs }}</span> phiếu sửa quá hạn
                </a>
            @elseif ($activeRepairs > 0)
                <a href="{{ route('filament.admin.resources.repair-tickets.index') }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-indigo-500/20 text-indigo-200 border border-indigo-400/30 hover:bg-indigo-500/30 transition-colors">
                    <span class="w-2 h-2 rounded-full bg-indigo-400"></span>
                    <span class="font-bold">{{ $activeRepairs }}</span> phiếu đang xử lý
                </a>
            @endif
        </div>
    </div>

    <nav class="admin-dashboard-intro__actions" aria-label="Thao tác nhanh">
        <!-- Nút bán hàng quầy -->
        <a class="admin-dashboard-action admin-dashboard-action--pos" href="{{ route('pos.index') }}" target="_blank" rel="noopener">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            <span>Bán tại quầy</span>
        </a>

        <!-- Nút Tạo phiếu sửa máy in -->
        <a class="admin-dashboard-action admin-dashboard-action--glass" href="{{ route('filament.admin.resources.repair-tickets.create') }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            <span>Nhận sửa máy</span>
        </a>

        <!-- Nút Thêm sản phẩm nhanh -->
        <a class="admin-dashboard-action admin-dashboard-action--glass" href="{{ route('filament.admin.resources.products.create') }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/></svg>
            <span>Thêm sản phẩm</span>
        </a>

        <!-- Nút Trực chat khách hàng -->
        <a class="admin-dashboard-action admin-dashboard-action--glass" href="{{ route('filament.admin.pages.live-chat-page') }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            <span>Chat hỗ trợ</span>
            @if ($unreadChats > 0)
                <span class="px-1.5 py-0.2 text-[10px] font-black rounded-full bg-rose-500 text-white leading-tight">{{ $unreadChats }}</span>
            @endif
        </a>

        <!-- Nút Xem Cửa Hàng -->
        <a class="admin-dashboard-action admin-dashboard-action--glass hidden xl:inline-flex" href="{{ route('storefront.index') }}" target="_blank" rel="noopener">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
            <span>Xem cửa hàng</span>
        </a>
    </nav>

    <!-- Background Decorative Elements -->
    <span class="admin-dashboard-intro__orb admin-dashboard-intro__orb--one" aria-hidden="true"></span>
    <span class="admin-dashboard-intro__orb admin-dashboard-intro__orb--two" aria-hidden="true"></span>
</section>
