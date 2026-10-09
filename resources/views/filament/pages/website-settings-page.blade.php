<x-filament-panels::page>
    <div style="background: linear-gradient(135deg, #1e1b4b 0%, #0f172a 100%); color: #ffffff;" class="mb-4 p-4 rounded-2xl shadow-lg border border-indigo-800/40 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <div class="flex items-center space-x-3">
            <div style="background: rgba(99, 102, 241, 0.2); border: 1px solid rgba(129, 140, 248, 0.3);" class="w-10 h-10 rounded-xl flex items-center justify-center text-xl shrink-0">
                ⚙️
            </div>
            <div>
                <h3 style="color: #ffffff;" class="text-sm font-black tracking-tight">Trung Tâm Cấu Hình Hệ Thống Toàn Diện</h3>
                <p style="color: #c7d2fe;" class="text-xs mt-0.5">Mọi thay đổi tại đây sẽ được đồng bộ tức thì lên Cửa Hàng Web (Storefront), Quầy Thu Ngân POS, Hóa Đơn In và Mã VietQR.</p>
            </div>
        </div>
        <div class="flex items-center space-x-2 shrink-0">
            <a href="{{ route('storefront.index') }}" target="_blank" style="background: rgba(255, 255, 255, 0.12); color: #ffffff; border: 1px solid rgba(255, 255, 255, 0.25);" class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition hover:opacity-90">
                <span>🌐 Xem Cửa Hàng</span>
                <span>↗</span>
            </a>
            <a href="{{ route('pos.index') }}" target="_blank" style="background: #059669; color: #ffffff;" class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition shadow-sm hover:opacity-90">
                <span>⚡ Quầy POS</span>
            </a>
        </div>
    </div>

    <form wire:submit="save" class="space-y-6">
        {{ $this->form }}

        <div class="flex items-center justify-end gap-x-3 pt-4 border-t border-slate-200">
            <x-filament::button type="submit" size="lg" icon="heroicon-o-check" color="primary">
                Lưu cài đặt website & cửa hàng
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
