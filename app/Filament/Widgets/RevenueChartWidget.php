<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class RevenueChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Phân tích doanh thu đa luồng (Bán hàng & Dịch vụ máy in)';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    public ?string $filter = '7';

    protected function getFilters(): ?array
    {
        return [
            '7' => '7 ngày gần nhất',
            '14' => '14 ngày gần nhất',
            '30' => '30 ngày gần nhất',
        ];
    }

    protected function getData(): array
    {
        $numDays = match ($this->filter) {
            '14' => 14,
            '30' => 30,
            default => 7,
        };

        $days = collect(range($numDays - 1, 0))->map(fn ($d) => Carbon::today()->subDays($d));

        $labels = [];
        $retailData = [];
        $repairData = [];

        foreach ($days as $day) {
            $dateString = $day->format('Y-m-d');
            $labels[] = $day->format('d/m');

            // 1. Doanh thu bán hàng VPP / bán lẻ
            $retailRevenue = (float) Order::whereDate('created_at', $dateString)
                ->where('payment_status', 'paid')
                ->sum('grand_total');

            // 2. Doanh thu dịch vụ sửa chữa máy in
            $repairRevenue = (float) \App\Models\RepairTicket::whereDate('created_at', $dateString)
                ->where('payment_status', 'paid')
                ->sum('grand_total');

            $retailData[] = $retailRevenue;
            $repairData[] = $repairRevenue;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Bán hàng VPP / Bán lẻ (VNĐ)',
                    'data' => $retailData,
                    'borderColor' => '#4f46e5',
                    'backgroundColor' => 'rgba(79, 70, 229, 0.08)',
                    'pointBackgroundColor' => '#4f46e5',
                    'pointBorderColor' => '#ffffff',
                    'pointBorderWidth' => 2,
                    'pointRadius' => 4,
                    'pointHoverRadius' => 6,
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Dịch vụ & Sửa máy in (VNĐ)',
                    'data' => $repairData,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.08)',
                    'pointBackgroundColor' => '#10b981',
                    'pointBorderColor' => '#ffffff',
                    'pointBorderWidth' => 2,
                    'pointRadius' => 4,
                    'pointHoverRadius' => 6,
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
