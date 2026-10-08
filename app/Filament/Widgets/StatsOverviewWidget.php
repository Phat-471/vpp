<?php

namespace App\Filament\Widgets;

use App\Helpers\AppHelper;
use App\Models\Order;
use App\Models\Product;
use App\Models\RepairTicket;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $todayOrders = Order::whereDate('created_at', today())->where('payment_status', 'paid');
        $todayRevenue = (float) $todayOrders->sum('grand_total');
        $todayCount = $todayOrders->count();

        $activeRepairs = RepairTicket::whereIn('status', ['received', 'diagnosing', 'quoted', 'in_progress'])->count();

        $lowStockCount = Product::whereColumn('stock_quantity', '<=', 'low_stock_threshold')->count();

        $monthRevenue = (float) Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('payment_status', 'paid')
            ->sum('grand_total');

        return [
            Stat::make('Doanh thu hôm nay', AppHelper::formatMoney($todayRevenue))
                ->description("{$todayCount} đơn hàng đã hoàn tất")
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([30, 45, 60, 40, 75, 90, max(10, (int)($todayRevenue / 100000))])
                ->color('success'),

            Stat::make('Máy in đang sửa chữa', "{$activeRepairs} máy")
                ->description('Đang tiếp nhận hoặc xử lý')
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->color($activeRepairs > 0 ? 'warning' : 'gray'),

            Stat::make('Sản phẩm sắp hết hàng', "{$lowStockCount} sản phẩm")
                ->description('Tồn kho dưới ngưỡng an toàn')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($lowStockCount > 0 ? 'danger' : 'success'),

            Stat::make('Doanh thu tháng ' . now()->month, AppHelper::formatMoney($monthRevenue))
                ->description('Tổng thu bán lẻ & dịch vụ')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary'),
        ];
    }
}
