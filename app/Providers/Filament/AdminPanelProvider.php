<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('VPP & MÁY IN ÁNH DƯƠNG')
            ->colors([
                'primary' => '#e66a3c',
                'success' => '#159b83',
                'warning' => '#e5a52e',
                'danger' => '#d94f64',
                'info' => '#4386c5',
                'gray' => '#65748b',
            ])
            ->font('Inter')
            ->darkMode(false)
            ->spa()
            ->sidebarCollapsibleOnDesktop()
            ->navigationGroups([
                'Bán hàng & thu ngân',
                'Kho & sản phẩm',
                'Dịch vụ kỹ thuật',
                'Chăm sóc khách hàng',
                'Tài chính & thuế',
                'Nội dung & trang web',
                'Hệ thống',
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->renderHook(
                PanelsRenderHook::STYLES_AFTER,
                fn (): string => '<link rel="stylesheet" href="' . asset('css/admin-panel.css') . '">',
            )
            ->renderHook(
                PanelsRenderHook::PAGE_START,
                fn (): string => view('filament.components.admin-dashboard-intro')->render(),
                scopes: Pages\Dashboard::class,
            )
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
