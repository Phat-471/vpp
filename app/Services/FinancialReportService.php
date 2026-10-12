<?php

namespace App\Services;

use App\Models\CashTransaction;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\RepairItem;
use App\Models\RepairTicket;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinancialReportService
{
    /**
     * Lấy báo cáo doanh thu, giá vốn và lợi nhuận gộp/ròng theo khoảng thời gian.
     */
    public function getProfitReport(?string $startDate = null, ?string $endDate = null): array
    {
        $start = $startDate ? Carbon::parse($startDate)->startOfDay() : Carbon::now()->startOfMonth();
        $end = $endDate ? Carbon::parse($endDate)->endOfDay() : Carbon::now()->endOfDay();

        // 1. DOANH THU & GIÁ VỐN BÁN LẺ VPP
        $ordersQuery = Order::where('status', 'completed')
            ->whereBetween('created_at', [$start, $end]);

        $orderRevenue = (float) (clone $ordersQuery)->sum('grand_total');
        $orderCount = (int) (clone $ordersQuery)->count();

        $orderIds = (clone $ordersQuery)->pluck('id');
        $cogsOrders = (float) OrderItem::whereIn('order_id', $orderIds)
            ->selectRaw('SUM(quantity * cost_price) as total_cogs')
            ->value('total_cogs') ?? 0;

        $grossProfitOrders = $orderRevenue - $cogsOrders;

        // 2. DOANH THU & GIÁ VỐN DỊCH VỤ SỬA MÁY IN
        $ticketsQuery = RepairTicket::where('status', 'delivered')
            ->whereBetween('created_at', [$start, $end]);

        $repairRevenue = (float) (clone $ticketsQuery)->sum('grand_total');
        $repairCount = (int) (clone $ticketsQuery)->count();

        $ticketIds = (clone $ticketsQuery)->pluck('id');
        $cogsRepairs = (float) RepairItem::whereIn('repair_ticket_id', $ticketIds)
            ->selectRaw('SUM(quantity * cost_price) as total_cogs')
            ->value('total_cogs') ?? 0;

        $grossProfitRepairs = $repairRevenue - $cogsRepairs;

        // 3. TỔNG HỢP TOÀN CỬA HÀNG
        $totalRevenue = $orderRevenue + $repairRevenue;
        $totalCogs = $cogsOrders + $cogsRepairs;
        $totalGrossProfit = $totalRevenue - $totalCogs;
        $grossMarginPercent = $totalRevenue > 0 ? round(($totalGrossProfit / $totalRevenue) * 100, 2) : 0;

        // 4. CHI PHÍ VẬN HÀNH (Operating Expenses từ Sổ Quỹ - ngoại trừ trả nợ NCC)
        $operatingExpenses = (float) CashTransaction::where('type', 'payment')
            ->where('status', 'completed')
            ->where('category', '!=', 'supplier_debt')
            ->whereBetween('performed_at', [$start, $end])
            ->sum('amount');

        // 5. LỢI NHUẬN THUẦN (Net Profit)
        $netProfit = $totalGrossProfit - $operatingExpenses;
        $netMarginPercent = $totalRevenue > 0 ? round(($netProfit / $totalRevenue) * 100, 2) : 0;

        // 6. TOP 10 SẢN PHẨM SINH LỜI CAO NHẤT
        $topProfitableProducts = OrderItem::whereIn('order_id', $orderIds)
            ->select(
                'product_name',
                DB::raw('SUM(quantity) as total_sold_qty'),
                DB::raw('SUM(subtotal) as total_sales'),
                DB::raw('SUM(quantity * cost_price) as total_cost'),
                DB::raw('SUM(subtotal - (quantity * cost_price)) as gross_profit')
            )
            ->groupBy('product_name')
            ->orderByDesc('gross_profit')
            ->limit(10)
            ->get()
            ->toArray();

        return [
            'period' => [
                'start' => $start->format('d/m/Y'),
                'end' => $end->format('d/m/Y'),
            ],
            'retail' => [
                'order_count' => $orderCount,
                'revenue' => $orderRevenue,
                'cogs' => $cogsOrders,
                'gross_profit' => $grossProfitOrders,
            ],
            'repair' => [
                'ticket_count' => $repairCount,
                'revenue' => $repairRevenue,
                'cogs' => $cogsRepairs,
                'gross_profit' => $grossProfitRepairs,
            ],
            'summary' => [
                'total_revenue' => $totalRevenue,
                'total_cogs' => $totalCogs,
                'gross_profit' => $totalGrossProfit,
                'gross_margin_percent' => $grossMarginPercent,
                'operating_expenses' => $operatingExpenses,
                'net_profit' => $netProfit,
                'net_margin_percent' => $netMarginPercent,
            ],
            'top_products' => $topProfitableProducts,
        ];
    }
}
