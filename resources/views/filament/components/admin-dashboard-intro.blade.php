@php
    $hour = now()->hour;
    $greeting = match(true) {
        $hour >= 5 && $hour < 12 => 'Buổi sáng tốt lành',
        $hour >= 12 && $hour < 18 => 'Buổi chiều năng suất',
        default => 'Buổi tối an lành',
    };
    $userName = auth()->user()?->name ?? 'Quản Trị Viên';
@endphp

<section class="admin-dashboard-intro" aria-labelledby="admin-dashboard-title">
    <div class="admin-dashboard-intro__copy">
        <p class="admin-dashboard-intro__eyebrow">
            <span class="admin-dashboard-intro__sun" aria-hidden="true">✦</span>
            <span>BẢNG ĐIỀU KHIỂN CỬA HÀNG</span>
            <span class="admin-dashboard-intro__date">{{ now()->locale('vi')->translatedFormat('l, d/m/Y') }}</span>
        </p>
        
        <h1 id="admin-dashboard-title">
            {{ $greeting }}, <span class="text-transparent bg-clip-text bg-gradient-to-r from-white via-indigo-100 to-amber-200">{{ $userName }}</span>!
        </h1>
        
        <p class="admin-dashboard-intro__description">
            Tổng quan doanh thu bán hàng, tồn kho và tiến độ sửa chữa máy in trong ngày.
        </p>
    </div>

    <nav class="admin-dashboard-intro__actions" aria-label="Thao tác nhanh">
        <!-- Nút bán hàng quầy -->
        <a class="admin-dashboard-action admin-dashboard-action--pos" href="{{ route('pos.index') }}" target="_blank" rel="noopener">
            <span class="admin-dashboard-action__icon" aria-hidden="true">⚡</span>
            <span>Bán Tại Quầy</span>
        </a>

        <!-- Nút Thêm sản phẩm nhanh -->
        <a class="admin-dashboard-action admin-dashboard-action--glass" href="{{ route('filament.admin.resources.products.create') }}">
            <span aria-hidden="true">＋</span>
            <span>Thêm Sản Phẩm</span>
        </a>

        <!-- Nút Tạo phiếu sửa máy in -->
        <a class="admin-dashboard-action admin-dashboard-action--glass" href="{{ route('filament.admin.resources.repair-tickets.create') }}">
            <span aria-hidden="true">🔧</span>
            <span>Nhận Sửa Máy</span>
        </a>

        <!-- Nút Xem Cửa Hàng -->
        <a class="admin-dashboard-action admin-dashboard-action--glass hidden xl:inline-flex" href="{{ route('storefront.index') }}" target="_blank">
            <span aria-hidden="true">🌐</span>
            <span>Xem Cửa Hàng</span>
            <span class="text-[10px] opacity-70">↗</span>
        </a>
    </nav>

    <!-- Background Decorative Elements -->
    <span class="admin-dashboard-intro__orb admin-dashboard-intro__orb--one" aria-hidden="true"></span>
    <span class="admin-dashboard-intro__orb admin-dashboard-intro__orb--two" aria-hidden="true"></span>
</section>
