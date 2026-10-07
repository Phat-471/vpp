<x-filament-panels::page>
    <form wire:submit="save" class="space-y-6">
        {{ $this->form }}

        <div class="flex items-center justify-end gap-x-3 pt-4 border-t border-slate-200 dark:border-slate-800">
            <x-filament::button type="submit" size="lg" icon="heroicon-o-check" color="primary">
                Lưu cài đặt website
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
