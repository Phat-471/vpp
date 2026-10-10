<x-filament-panels::page>
    <div class="settings-hero-banner mb-3 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2.5">
        <div class="flex items-center space-x-2.5 min-w-0">
            <div style="background: rgba(255, 255, 255, 0.18); border: 1px solid rgba(255, 255, 255, 0.25);" class="w-8 h-8 rounded-lg flex items-center justify-center text-sm shrink-0">
                ⚙️
            </div>
            <div class="min-w-0">
                <h3 style="color: #ffffff !important;" class="text-xs font-bold tracking-tight">Cài đặt thông tin cửa hàng</h3>
                <p style="color: #e0e7ff !important;" class="text-[11px] truncate">Áp dụng trực tiếp cho website, hóa đơn in và thanh toán chuyển khoản.</p>
            </div>
        </div>
        <div class="flex items-center space-x-2 shrink-0">
            <a href="{{ route('storefront.index') }}" target="_blank" style="background: rgba(255, 255, 255, 0.18); color: #ffffff !important; border: 1px solid rgba(255, 255, 255, 0.25);" class="inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-lg text-xs font-bold transition hover:opacity-90">
                <span>🌐 Xem Web</span>
                <span>↗</span>
            </a>
            <a href="{{ route('pos.index') }}" target="_blank" style="background: #059669; color: #ffffff !important;" class="inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-lg text-xs font-bold transition shadow-xs hover:opacity-90">
                <span>⚡ Bán tại quầy</span>
            </a>
        </div>
    </div>

    <form wire:submit="save" class="space-y-6">
        {{ $this->form }}

        <div class="flex items-center justify-end gap-x-3 pt-4 border-t border-slate-200">
            <x-filament::button type="submit" size="lg" icon="heroicon-o-check" color="primary">
                Lưu thay đổi
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
