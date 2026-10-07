<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class RevenueChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Biểu Đồ Doanh Thu 7 Ngày Gần Nhất';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $days = collect(range(6, 0))->map(fn ($d) => Carbon::today()->subDays($d));

        $labels = [];
        $data = [];

        foreach ($days as $day) {
            $dateString = $day->format('Y-m-d');
            $labels[] = $day->format('d/m');

            $revenue = (float) Order::whereDate('created_at', $dateString)
                ->where('payment_status', 'paid')
                ->sum('grand_total');

            $data[] = $revenue;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Doanh thu (VNĐ)',
                    'data' => $data,
                    'borderColor' => '#4f46e5',
                    'backgroundColor' => 'rgba(79, 70, 229, 0.15)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
