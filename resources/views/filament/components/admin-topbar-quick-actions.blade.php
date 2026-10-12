<div class="admin-topbar-actions">
    <!-- Nút Bán Hàng Tại Quầy -->
    <a href="{{ route('pos.index') }}" target="_blank" class="admin-topbar-btn admin-topbar-btn--pos" title="Bán hàng tại quầy">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        <span class="admin-topbar-btn__text">Bán tại quầy</span>
    </a>

    <!-- Nút Xem Cửa Hàng Web -->
    <a href="{{ route('storefront.index') }}" target="_blank" class="admin-topbar-btn admin-topbar-btn--web" title="Xem Cửa hàng Khách">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
        <span class="admin-topbar-btn__text">Xem Web</span>
    </a>

    <!-- Trạng thái hệ thống -->
    <div class="admin-topbar-status">
        <span class="admin-topbar-status__dot"></span>
        <span class="admin-topbar-status__text">Hệ thống Online</span>
    </div>
</div>
