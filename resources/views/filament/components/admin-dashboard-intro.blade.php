<section class="admin-dashboard-intro" aria-labelledby="admin-dashboard-title">
    <div class="admin-dashboard-intro__copy">
        <p class="admin-dashboard-intro__eyebrow">
            <span class="admin-dashboard-intro__sun" aria-hidden="true">✦</span>
            TRUNG TÂM VẬN HÀNH
            <span class="admin-dashboard-intro__date">{{ now()->locale('vi')->translatedFormat('l, d/m/Y') }}</span>
        </p>
        <h1 id="admin-dashboard-title">Chào mừng, {{ auth()->user()?->name ?? 'bạn' }}</h1>
        <p class="admin-dashboard-intro__description">Theo dõi doanh thu, hàng hóa và tiến độ sửa chữa tại một nơi.</p>
    </div>

    <nav class="admin-dashboard-intro__actions" aria-label="Thao tác nhanh">
        @can('use-pos')
        <a class="admin-dashboard-action admin-dashboard-action--light" href="{{ route('pos.index') }}" target="_blank" rel="noopener">
            <span class="admin-dashboard-action__icon" aria-hidden="true">＋</span>
            Mở quầy bán hàng
        </a>
        @endcan
        <a class="admin-dashboard-action admin-dashboard-action--glass" href="{{ route('filament.admin.resources.products.index') }}">
            Quản lý sản phẩm
            <span aria-hidden="true">↗</span>
        </a>
    </nav>

    <span class="admin-dashboard-intro__orb admin-dashboard-intro__orb--one" aria-hidden="true"></span>
    <span class="admin-dashboard-intro__orb admin-dashboard-intro__orb--two" aria-hidden="true"></span>
</section>
