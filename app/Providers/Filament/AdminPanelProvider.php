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
            ->brandLogo(fn () => view('filament.components.brand-logo'))
            ->brandLogoHeight('2.75rem')
            ->colors([
                'primary' => '#4f46e5', // Modern Indigo
                'success' => '#10b981', // Emerald
                'warning' => '#f59e0b', // Amber
                'danger' => '#ef4444',  // Rose
                'info' => '#0ea5e9',     // Sky Blue
                'gray' => '#64748b',     // Slate
            ])
            ->font('Plus Jakarta Sans')
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
            ->widgets([])
            ->renderHook(
                PanelsRenderHook::STYLES_AFTER,
                fn (): string => '<link rel="stylesheet" href="' . asset('css/admin-panel.css') . '?v=' . time() . '">',
            )
            ->renderHook(
                PanelsRenderHook::USER_MENU_BEFORE,
                fn (): string => view('filament.components.admin-topbar-quick-actions')->render(),
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
