<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\User;
use App\Services\PosOrderHistory;
use App\Services\StaffLogin;
use App\Support\StorefrontSettings;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (file_exists(app_path('Helpers/Helper.php'))) {
            require_once app_path('Helpers/Helper.php');
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        Gate::define('use-pos', fn (User $user) => $user->isCashier());
        Gate::define('manage-live-chat', fn (User $user) => $user->isAdmin());
        Gate::define('view-pos-order', fn (User $user, Order $order) => $user->isCashier()
            && $order->channel === 'pos' && ($user->isAdmin() || $order->created_by === $user->id));
        RateLimiter::for('pos-login', fn (Request $request) => [
            Limit::perMinute(10)->by($request->ip()),
            Limit::perMinute(5)->by(app(StaffLogin::class)->throttleKey((string) $request->input('identifier')).'|'.$request->ip()),
        ]);
        foreach (['init' => 10, 'send' => 20, 'messages' => 90, 'read' => 90] as $operation => $limit) {
            RateLimiter::for('chat-'.$operation, fn (Request $request) => Limit::perMinute($limit)
                ->by('chat-'.$operation.'|'.$request->ip())
                ->response(fn (Request $request, array $headers) => response()->json(['success' => false, 'message' => 'Bạn đang thao tác quá nhanh. Hãy chờ một phút rồi thử lại.'], 429, $headers)));
        }

        View::composer(['layouts.storefront', 'storefront.*', 'lookup.*', 'print.order'], function ($view) {
            $view->with('storefrontSettings', app(StorefrontSettings::class)->all());
            if ($view->name() === 'print.order') {
                $view->with('paymentSummary', app(PosOrderHistory::class)->paymentSummary($view->getData()['order']));
            }
        });
    }
}
