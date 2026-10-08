<?php

namespace App\Filament\Pages;

use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class FinancialReportPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static ?string $navigationGroup = 'Tài chính & thuế';

    protected static ?string $navigationLabel = 'Báo cáo doanh thu và thuế VAT';

    protected static ?string $title = 'Báo cáo tài chính và thuế GTGT';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.financial-report-page';

    public string $period = 'this_month'; // today, last_7_days, this_month, this_quarter, all, custom
    public ?string $startDate = null;
    public ?string $endDate = null;

    public function mount(): void
    {
        $this->setDateRangeForPeriod();
    }

    public function updatedPeriod(): void
    {
        $this->setDateRangeForPeriod();
    }

    protected function setDateRangeForPeriod(): void
    {
        $now = Carbon::now();
        switch ($this->period) {
            case 'today':
                $this->startDate = $now->copy()->startOfDay()->format('Y-m-d');
                $this->endDate = $now->copy()->endOfDay()->format('Y-m-d');
                break;
            case 'last_7_days':
                $this->startDate = $now->copy()->subDays(6)->startOfDay()->format('Y-m-d');
                $this->endDate = $now->copy()->endOfDay()->format('Y-m-d');
                break;
            case 'this_month':
                $this->startDate = $now->copy()->startOfMonth()->format('Y-m-d');
                $this->endDate = $now->copy()->endOfMonth()->format('Y-m-d');
                break;
            case 'this_quarter':
                $this->startDate = $now->copy()->firstOfQuarter()->format('Y-m-d');
                $this->endDate = $now->copy()->lastOfQuarter()->format('Y-m-d');
                break;
            case 'all':
                $this->startDate = '2025-01-01';
                $this->endDate = $now->copy()->endOfDay()->format('Y-m-d');
                break;
        }
    }

    public function getReportDataProperty(): array
    {
        $start = Carbon::parse($this->startDate ?? now()->startOfMonth())->startOfDay();
        $end = Carbon::parse($this->endDate ?? now()->endOfMonth())->endOfDay();

        $ordersQuery = Order::whereBetween('created_at', [$start, $end])
            ->where('status', '!=', 'cancelled');

        $totalOrdersCount = (clone $ordersQuery)->count();
        $grossRevenue = (float) (clone $ordersQuery)->sum('grand_total');
        $netSubtotal = (float) (clone $ordersQuery)->sum('subtotal');
        $totalDiscount = (float) (clone $ordersQuery)->sum('discount_amount');
        $totalVatTax = (float) (clone $ordersQuery)->sum('tax_amount');
        $vatOrdersCount = (clone $ordersQuery)->where('is_vat_invoice', true)->count();

        // Calculate Cost of Goods Sold (COGS) to find Gross Profit
        $orderIds = (clone $ordersQuery)->pluck('id');
        $cogs = (float) OrderItem::whereIn('order_id', $orderIds)
            ->selectRaw('SUM(cost_price * quantity * conversion_rate) as total_cogs')
            ->value('total_cogs') ?? 0;

        $grossProfit = ($netSubtotal - $totalDiscount) - $cogs;

        // Payment method breakdown
        $paymentBreakdown = (clone $ordersQuery)
            ->select('payment_method', DB::raw('SUM(grand_total) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('payment_method')
            ->get();

        // VAT invoice detailed list for tax declaration
        $vatOrders = (clone $ordersQuery)
            ->where(function ($q) {
                $q->where('is_vat_invoice', true)
                  ->orWhere('tax_amount', '>', 0);
            })
            ->with(['orderItems'])
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get();

        return [
            'total_orders' => $totalOrdersCount,
            'gross_revenue' => $grossRevenue,
            'net_subtotal' => $netSubtotal,
            'total_discount' => $totalDiscount,
            'total_vat_tax' => $totalVatTax,
            'vat_orders_count' => $vatOrdersCount,
            'cogs' => $cogs,
            'gross_profit' => $grossProfit,
            'profit_margin' => $grossRevenue > 0 ? round(($grossProfit / $grossRevenue) * 100, 1) : 0,
            'payment_breakdown' => $paymentBreakdown,
            'vat_orders' => $vatOrders,
        ];
    }
}
