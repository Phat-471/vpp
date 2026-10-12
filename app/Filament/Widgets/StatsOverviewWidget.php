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
        $businessNow = now('Asia/Ho_Chi_Minh');
        $todayStartUtc = $businessNow->copy()->startOfDay()->utc();
        $tomorrowStartUtc = $todayStartUtc->copy()->addDay();
        $sevenDayStartUtc = $businessNow->copy()->subDays(6)->startOfDay()->utc();

        $paidOrders = Order::query()
            ->where('payment_status', 'paid')
            ->where('created_at', '>=', $sevenDayStartUtc)
            ->where('created_at', '<', $tomorrowStartUtc)
            ->get(['grand_total', 'created_at']);

        $todayOrders = $paidOrders->filter(fn (Order $order) => $order->created_at->timezone('Asia/Ho_Chi_Minh')->isSameDay($businessNow)
        );
        $todayRevenue = (float) $todayOrders->sum('grand_total');
        $todayOrderCount = $todayOrders->count();
        $dailyRevenue = $paidOrders
            ->groupBy(fn (Order $order) => $order->created_at->timezone('Asia/Ho_Chi_Minh')->toDateString())
            ->map(fn ($orders) => (float) $orders->sum('grand_total'));

        $last7DaysSparkline = collect(range(6, 0))->map(function ($daysAgo) use ($businessNow, $dailyRevenue) {
            $date = $businessNow->copy()->subDays($daysAgo)->toDateString();

            return (int) round(($dailyRevenue->get($date, 0)) / 100000);
        })->toArray();

        // 2. Máy in đang sửa chữa & cảnh báo trễ hẹn
        $activeRepairs = RepairTicket::whereIn('status', ['received', 'diagnosing', 'waiting_approval', 'in_progress'])->count();
        $overdueRepairs = RepairTicket::whereIn('status', ['received', 'diagnosing', 'waiting_approval', 'in_progress'])
            ->whereNotNull('promised_at')
            ->where('promised_at', '<', now())
            ->count();

        $repairDesc = $overdueRepairs > 0
            ? "Cảnh báo: Có {$overdueRepairs} máy in trễ hẹn trả"
            : ($activeRepairs > 0 ? 'Đang tiếp nhận hoặc xử lý kỹ thuật' : 'Không có máy in tồn đọng');
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
            : ($lowStockCount > 0 ? 'Tồn kho chạm ngưỡng tối thiểu' : 'Toàn bộ kho hàng an toàn');
        $stockColor = $outOfStockCount > 0 ? 'danger' : ($lowStockCount > 0 ? 'warning' : 'success');

        $monthStartLocal = $businessNow->copy()->startOfMonth();
        $nextMonthStartLocal = $monthStartLocal->copy()->addMonth();
        $previousMonthStartLocal = $monthStartLocal->copy()->subMonth();
        $monthStartUtc = $monthStartLocal->copy()->utc();
        $nextMonthStartUtc = $nextMonthStartLocal->utc();
        $previousMonthStartUtc = $previousMonthStartLocal->utc();

        $currentMonthRevenue = (float) Order::query()
            ->where('payment_status', 'paid')
            ->where('created_at', '>=', $monthStartUtc)
            ->where('created_at', '<', $nextMonthStartUtc)
            ->sum('grand_total');

        $lastMonthRevenue = (float) Order::query()
            ->where('payment_status', 'paid')
            ->where('created_at', '>=', $previousMonthStartUtc)
            ->where('created_at', '<', $monthStartUtc)
            ->sum('grand_total');

        $lastMonth = $businessNow->copy()->subMonth();

        if ($lastMonthRevenue > 0) {
            $diffPercent = round((($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1);
            if ($diffPercent >= 0) {
                $monthDesc = "Tăng {$diffPercent}% so với tháng ".$lastMonth->month;
                $monthIcon = 'heroicon-m-arrow-trending-up';
                $monthColor = 'success';
            } else {
                $absDiff = abs($diffPercent);
                $monthDesc = "Giảm {$absDiff}% so với tháng ".$lastMonth->month;
                $monthIcon = 'heroicon-m-arrow-trending-down';
                $monthColor = 'warning';
            }
        } else {
            $monthDesc = 'Doanh thu tháng '.$businessNow->month;
            $monthIcon = 'heroicon-m-calendar-days';
            $monthColor = 'primary';
        }

        return [
            Stat::make('Doanh thu hôm nay', AppHelper::formatMoney($todayRevenue))
                ->description("{$todayOrderCount} đơn hàng đã hoàn tất thanh toán")
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart($last7DaysSparkline)
                ->color('success')
                ->url(route('filament.admin.resources.orders.index', ['activeTab' => 'paid_today'])),

            Stat::make('Máy in đang sửa chữa', "{$activeRepairs} máy")
                ->description($repairDesc)
                ->descriptionIcon($overdueRepairs > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-wrench-screwdriver')
                ->color($repairColor)
                ->url(route('filament.admin.resources.repair-tickets.index', ['activeTab' => 'processing'])),

            Stat::make('Hàng sắp hết / Hết hàng', "{$lowStockCount} mặt hàng")
                ->description($stockDesc)
                ->descriptionIcon('heroicon-m-archive-box-x-mark')
                ->color($stockColor)
                ->url(route('filament.admin.resources.products.index', ['activeTab' => 'low_stock'])),

            Stat::make('Doanh thu tháng '.$businessNow->month, AppHelper::formatMoney($currentMonthRevenue))
                ->description($monthDesc)
                ->descriptionIcon($monthIcon)
                ->color($monthColor)
                ->url(route('filament.admin.resources.orders.index', ['activeTab' => 'paid_this_month'])),
        ];
    }
}
