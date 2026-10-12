<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\RepairTicket;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class RevenueChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Phân tích doanh thu đa luồng (Bán hàng & Dịch vụ máy in)';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

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

        $businessToday = Carbon::now('Asia/Ho_Chi_Minh')->startOfDay();
        $firstDay = $businessToday->copy()->subDays($numDays - 1);
        $startUtc = $firstDay->copy()->utc();
        $endUtc = $businessToday->copy()->addDay()->utc();

        $ordersByDay = Order::query()
            ->where('payment_status', 'paid')
            ->where('created_at', '>=', $startUtc)
            ->where('created_at', '<', $endUtc)
            ->get(['grand_total', 'created_at'])
            ->groupBy(fn (Order $order) => $order->created_at->timezone('Asia/Ho_Chi_Minh')->toDateString())
            ->map(fn ($orders) => (float) $orders->sum('grand_total'));

        $repairsByDay = RepairTicket::query()
            ->where('payment_status', 'paid')
            ->where('created_at', '>=', $startUtc)
            ->where('created_at', '<', $endUtc)
            ->get(['grand_total', 'created_at'])
            ->groupBy(fn (RepairTicket $ticket) => $ticket->created_at->timezone('Asia/Ho_Chi_Minh')->toDateString())
            ->map(fn ($tickets) => (float) $tickets->sum('grand_total'));

        $labels = [];
        $retailData = [];
        $repairData = [];

        foreach (range($numDays - 1, 0) as $daysAgo) {
            $day = $businessToday->copy()->subDays($daysAgo);
            $date = $day->toDateString();
            $labels[] = $day->format('d/m');
            $retailData[] = $ordersByDay->get($date, 0);
            $repairData[] = $repairsByDay->get($date, 0);
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
