<div class="admin-topbar-actions">
    <!-- Nút Bán Hàng Tại Quầy -->
    <a href="{{ route('pos.index') }}" target="_blank" class="admin-topbar-btn admin-topbar-btn--pos" title="Bán hàng tại quầy">
        <span class="admin-topbar-btn__icon">⚡</span>
        <span class="admin-topbar-btn__text">Bán tại quầy</span>
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
