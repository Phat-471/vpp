<div class="admin-topbar-actions">
    <!-- Nút Mở Quầy POS Bán Hàng -->
    <a href="{{ route('pos.index') }}" target="_blank" class="admin-topbar-btn admin-topbar-btn--pos" title="Mở giao diện Thu Ngân POS">
        <span class="admin-topbar-btn__icon">⚡</span>
        <span class="admin-topbar-btn__text">Quầy POS</span>
    </a>

    <!-- Nút Xem Cửa Hàng Web -->
    <a href="{{ route('storefront.index') }}" target="_blank" class="admin-topbar-btn admin-topbar-btn--web" title="Xem Cửa hàng Khách">
        <span class="admin-topbar-btn__icon">🌐</span>
        <span class="admin-topbar-btn__text">Xem Web</span>
        <span class="admin-topbar-btn__arrow">↗</span>
    </a>

    <!-- Trạng thái hệ thống -->
    <div class="admin-topbar-status">
        <span class="admin-topbar-status__dot"></span>
        <span class="admin-topbar-status__text">Hệ thống Online</span>
    </div>
</div>
