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
        // 1. Doanh thu hôm nay và sparkline 7 ngày thực tế
        $todayRevenue = (float) Order::whereDate('created_at', today())
            ->where('payment_status', 'paid')
            ->sum('grand_total');

        $todayOrderCount = Order::whereDate('created_at', today())
            ->where('payment_status', 'paid')
            ->count();

        $last7DaysSparkline = collect(range(6, 0))->map(function ($daysAgo) {
            $date = today()->subDays($daysAgo);
            $amount = (float) Order::whereDate('created_at', $date)
                ->where('payment_status', 'paid')
                ->sum('grand_total');
            return (int) round($amount / 100000);
        })->toArray();

        // 2. Máy in đang sửa chữa & cảnh báo trễ hẹn
        $activeRepairs = RepairTicket::whereIn('status', ['received', 'diagnosing', 'quoted', 'in_progress'])->count();
        $overdueRepairs = RepairTicket::whereIn('status', ['received', 'diagnosing', 'quoted', 'in_progress'])
            ->whereNotNull('promised_at')
            ->where('promised_at', '<', now())
            ->count();

        $repairDesc = $overdueRepairs > 0
            ? "Cảnh báo: Có {$overdueRepairs} máy in trễ hẹn trả"
            : ($activeRepairs > 0 ? "Đang tiếp nhận hoặc xử lý kỹ thuật" : "Không có máy in tồn đọng");
        $repairColor = $overdueRepairs > 0 ? 'danger' : ($activeRepairs > 0 ? 'warning' : 'success');

        // 3. Tồn kho cảnh báo
        $lowStockCount = Product::whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->where('is_active', true)
            ->count();
        $outOfStockCount = Product::where('stock_quantity', '<=', 0)
            ->where('is_active', true)
            ->count();

        $stockDesc = $outOfStockCount > 0
            ? "Có {$outOfStockCount} mặt hàng đã hết sạch kho"
            : ($lowStockCount > 0 ? "Tồn kho chạm ngưỡng tối thiểu" : "Toàn bộ kho hàng an toàn");
        $stockColor = $outOfStockCount > 0 ? 'danger' : ($lowStockCount > 0 ? 'warning' : 'success');

        // 4. Doanh thu tháng này và so sánh tháng trước
        $currentMonthRevenue = (float) Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('payment_status', 'paid')
            ->sum('grand_total');

        $lastMonth = now()->subMonth();
        $lastMonthRevenue = (float) Order::whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->where('payment_status', 'paid')
            ->sum('grand_total');

        if ($lastMonthRevenue > 0) {
            $diffPercent = round((($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1);
            if ($diffPercent >= 0) {
                $monthDesc = "Tăng {$diffPercent}% so với tháng " . $lastMonth->month;
                $monthIcon = 'heroicon-m-arrow-trending-up';
                $monthColor = 'success';
            } else {
                $absDiff = abs($diffPercent);
                $monthDesc = "Giảm {$absDiff}% so với tháng " . $lastMonth->month;
                $monthIcon = 'heroicon-m-arrow-trending-down';
                $monthColor = 'warning';
            }
        } else {
            $monthDesc = 'Doanh thu tháng ' . now()->month;
            $monthIcon = 'heroicon-m-calendar-days';
            $monthColor = 'primary';
        }

        return [
            Stat::make('Doanh thu hôm nay', AppHelper::formatMoney($todayRevenue))
                ->description("{$todayOrderCount} đơn hàng đã hoàn tất thanh toán")
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart($last7DaysSparkline)
                ->color('success')
                ->url(route('filament.admin.resources.orders.index')),

            Stat::make('Máy in đang sửa chữa', "{$activeRepairs} máy")
                ->description($repairDesc)
                ->descriptionIcon($overdueRepairs > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-wrench-screwdriver')
                ->color($repairColor)
                ->url(route('filament.admin.resources.repair-tickets.index')),

            Stat::make('Hàng sắp hết / Hết hàng', "{$lowStockCount} mặt hàng")
                ->description($stockDesc)
                ->descriptionIcon('heroicon-m-archive-box-x-mark')
                ->color($stockColor)
                ->url(route('filament.admin.resources.products.index')),

            Stat::make('Doanh thu tháng ' . now()->month, AppHelper::formatMoney($currentMonthRevenue))
                ->description($monthDesc)
                ->descriptionIcon($monthIcon)
                ->color($monthColor)
                ->url(route('filament.admin.resources.orders.index')),
        ];
    }
}
