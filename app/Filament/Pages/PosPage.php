<?php

namespace App\Filament\Pages;

use Filament\Navigation\NavigationItem;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Gate;

class PosPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calculator';

    protected static ?string $navigationGroup = 'Bán hàng & thu ngân';

    protected static ?string $navigationLabel = 'Quầy bán hàng (POS)';

    protected static ?string $title = 'Quầy bán hàng';

    protected static ?int $navigationSort = 0;

    protected static bool $shouldRegisterNavigation = false;

    public static function canAccess(): bool
    {
        return Gate::allows('use-pos');
    }

    public static function getNavigationItems(): array
    {
        return [];
    }

    public function mount(): void
    {
        Gate::authorize('use-pos');
        $this->redirectRoute('pos.index');
        $this->skipRender();
    }
}
