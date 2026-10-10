<?php

namespace App\Filament\Pages;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\RepairItem;
use App\Models\RepairTicket;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FinancialReportPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static ?string $navigationGroup = 'Tài chính';

    protected static ?string $navigationLabel = 'Báo cáo';

    protected static ?string $title = 'Báo cáo tài chính & thuế';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.financial-report-page';

    public string $period = 'this_month'; // today, yesterday, last_7_days, this_month, last_month, this_quarter, this_year, all, custom
    public ?string $startDate = null;
    public ?string $endDate = null;
    public string $activeTab = 'overview'; // overview, breakdown, vat_table
    public string $vatSearch = '';
    public string $vatFilterType = 'all'; // all, vat_only, non_vat

    public function mount(): void
    {
        $this->setDateRangeForPeriod();
    }

    public function updatedPeriod(): void
    {
        $this->setDateRangeForPeriod();
    }

    public function updatedStartDate(): void
    {
        $this->period = 'custom';
    }

    public function updatedEndDate(): void
    {
        $this->period = 'custom';
    }

    public function setPeriod(string $newPeriod): void
    {
        $this->period = $newPeriod;
        $this->setDateRangeForPeriod();
    }

    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    protected function setDateRangeForPeriod(): void
    {
        $now = Carbon::now();
        switch ($this->period) {
            case 'today':
                $this->startDate = $now->copy()->startOfDay()->format('Y-m-d');
                $this->endDate = $now->copy()->endOfDay()->format('Y-m-d');
                break;
            case 'yesterday':
                $this->startDate = $now->copy()->subDay()->startOfDay()->format('Y-m-d');
                $this->endDate = $now->copy()->subDay()->endOfDay()->format('Y-m-d');
                break;
            case 'last_7_days':
                $this->startDate = $now->copy()->subDays(6)->startOfDay()->format('Y-m-d');
                $this->endDate = $now->copy()->endOfDay()->format('Y-m-d');
                break;
            case 'this_month':
                $this->startDate = $now->copy()->startOfMonth()->format('Y-m-d');
                $this->endDate = $now->copy()->endOfMonth()->format('Y-m-d');
                break;
            case 'last_month':
                $this->startDate = $now->copy()->subMonth()->startOfMonth()->format('Y-m-d');
                $this->endDate = $now->copy()->subMonth()->endOfMonth()->format('Y-m-d');
                break;
            case 'this_quarter':
                $this->startDate = $now->copy()->firstOfQuarter()->format('Y-m-d');
                $this->endDate = $now->copy()->lastOfQuarter()->format('Y-m-d');
                break;
            case 'this_year':
                $this->startDate = $now->copy()->startOfYear()->format('Y-m-d');
                $this->endDate = $now->copy()->endOfYear()->format('Y-m-d');
                break;
            case 'all':
                $this->startDate = '2025-01-01';
                $this->endDate = $now->copy()->endOfDay()->format('Y-m-d');
                break;
            case 'custom':
                // keep current dates
                break;
        }
    }

    protected function getPreviousPeriodDates(): array
    {
        $start = Carbon::parse($this->startDate ?? now()->startOfMonth())->startOfDay();
        $end = Carbon::parse($this->endDate ?? now()->endOfMonth())->endOfDay();
        $diffDays = $start->diffInDays($end) + 1;

        switch ($this->period) {
            case 'today':
                $prevStart = $start->copy()->subDay();
                $prevEnd = $end->copy()->subDay();
                break;
            case 'yesterday':
                $prevStart = $start->copy()->subDays(2);
                $prevEnd = $end->copy()->subDays(2);
                break;
            case 'last_7_days':
                $prevStart = $start->copy()->subDays(7);
                $prevEnd = $start->copy()->subSecond();
                break;
            case 'this_month':
                $prevStart = $start->copy()->subMonth()->startOfMonth();
                $prevEnd = $start->copy()->subMonth()->endOfMonth();
                break;
            case 'last_month':
                $prevStart = $start->copy()->subMonths(2)->startOfMonth();
                $prevEnd = $start->copy()->subMonths(2)->endOfMonth();
                break;
            case 'this_quarter':
                $prevStart = $start->copy()->subQuarter()->firstOfQuarter();
                $prevEnd = $start->copy()->subQuarter()->lastOfQuarter();
                break;
            case 'this_year':
                $prevStart = $start->copy()->subYear()->startOfYear();
                $prevEnd = $start->copy()->subYear()->endOfYear();
                break;
            default:
                $prevStart = $start->copy()->subDays($diffDays);
                $prevEnd = $start->copy()->subSecond();
                break;
        }

        return [$prevStart, $prevEnd];
    }

    public function getReportDataProperty(): array
    {
        $start = Carbon::parse($this->startDate ?? now()->startOfMonth())->startOfDay();
        $end = Carbon::parse($this->endDate ?? now()->endOfMonth())->endOfDay();

        // 1. DỮ LIỆU ĐƠN HÀNG (BÁN HÀNG VPP & THIẾT BỊ)
        $ordersQuery = Order::whereBetween('created_at', [$start, $end])
            ->where('status', '!=', 'cancelled');

        $orderCount = (clone $ordersQuery)->count();
        $orderRevenue = (float) (clone $ordersQuery)->sum('grand_total');
        $orderSubtotal = (float) (clone $ordersQuery)->sum('subtotal');
        $orderDiscount = (float) (clone $ordersQuery)->sum('discount_amount');
        $orderVat = (float) (clone $ordersQuery)->sum('tax_amount');
        $orderShipping = (float) (clone $ordersQuery)->sum('shipping_fee');
        $orderPaid = (float) (clone $ordersQuery)->sum('paid_amount');
        $orderReceivables = (float) (clone $ordersQuery)->selectRaw('SUM(CASE WHEN grand_total > COALESCE(paid_amount, 0) THEN grand_total - COALESCE(paid_amount, 0) ELSE 0 END) as rec')->value('rec') ?? 0;
        $vatOrdersCount = (clone $ordersQuery)->where('is_vat_invoice', true)->count();

        // COGS đơn hàng
        $orderIds = (clone $ordersQuery)->pluck('id');
        $orderCogs = 0;
        if ($orderIds->isNotEmpty()) {
            $orderCogs = (float) OrderItem::whereIn('order_id', $orderIds)
                ->selectRaw('SUM(COALESCE(cost_price, 0) * quantity * COALESCE(conversion_rate, 1)) as total_cogs')
                ->value('total_cogs') ?? 0;
        }

        // Kênh bán POS vs Online
        $posOrders = (clone $ordersQuery)->where('channel', 'pos')->count();
        $posRevenue = (float) (clone $ordersQuery)->where('channel', 'pos')->sum('grand_total');
        $onlineOrders = (clone $ordersQuery)->where('channel', '!=', 'pos')->count();
        $onlineRevenue = (float) (clone $ordersQuery)->where('channel', '!=', 'pos')->sum('grand_total');

        // 2. DỮ LIỆU PHIẾU SỬA CHỮA (DỊCH VỤ KỸ THUẬT & SỬA MÁY IN)
        $repairsQuery = RepairTicket::whereBetween('created_at', [$start, $end])
            ->where('status', '!=', 'cancelled');

        $repairCount = (clone $repairsQuery)->count();
        $repairRevenue = (float) (clone $repairsQuery)->sum('grand_total');
        $repairLabor = (float) (clone $repairsQuery)->sum('labor_fee');
        $repairParts = (float) (clone $repairsQuery)->sum('parts_total');
        $repairDiscount = (float) (clone $repairsQuery)->sum('discount_amount');
        $repairVat = (float) (clone $repairsQuery)->sum('tax_amount');
        $repairPaid = (float) (clone $repairsQuery)->sum('paid_amount');
        $repairReceivables = (float) (clone $repairsQuery)->selectRaw('SUM(CASE WHEN grand_total > COALESCE(paid_amount, 0) THEN grand_total - COALESCE(paid_amount, 0) ELSE 0 END) as rec')->value('rec') ?? 0;

        // COGS linh kiện sửa chữa
        $repairTicketIds = (clone $repairsQuery)->pluck('id');
        $repairCogs = 0;
        if ($repairTicketIds->isNotEmpty()) {
            $repairCogs = (float) RepairItem::whereIn('repair_ticket_id', $repairTicketIds)
                ->selectRaw('SUM(COALESCE(cost_price, 0) * quantity) as total_cogs')
                ->value('total_cogs') ?? 0;
        }

        // 3. TỔNG HỢP HỢP NHẤT TOÀN CỬA HÀNG (CONSOLIDATED TOTALS)
        $totalRevenue = $orderRevenue + $repairRevenue;
        $totalCogs = $orderCogs + $repairCogs;
        $totalVat = $orderVat + $repairVat;
        $totalPaid = $orderPaid + $repairPaid;
        $totalReceivables = $orderReceivables + $repairReceivables;

        // Lợi nhuận gộp = (Doanh thu thuần chưa thuế) - (Giá vốn)
        $netUntaxedSales = ($orderSubtotal - $orderDiscount) + ($repairLabor + $repairParts - $repairDiscount);
        $grossProfit = $netUntaxedSales - $totalCogs;
        $profitMargin = $totalRevenue > 0 ? round(($grossProfit / $totalRevenue) * 100, 1) : 0;

        // 4. SO SÁNH VỚI KỲ TRƯỚC (GROWTH METRICS)
        [$prevStart, $prevEnd] = $this->getPreviousPeriodDates();
        $prevOrdersQuery = Order::whereBetween('created_at', [$prevStart, $prevEnd])->where('status', '!=', 'cancelled');
        $prevRepairsQuery = RepairTicket::whereBetween('created_at', [$prevStart, $prevEnd])->where('status', '!=', 'cancelled');

        $prevOrderRevenue = (float) (clone $prevOrdersQuery)->sum('grand_total');
        $prevRepairRevenue = (float) (clone $prevRepairsQuery)->sum('grand_total');
        $prevTotalRevenue = $prevOrderRevenue + $prevRepairRevenue;
        $prevOrderCount = (clone $prevOrdersQuery)->count();
        $prevRepairCount = (clone $prevRepairsQuery)->count();
        $prevTotalTransactions = $prevOrderCount + $prevRepairCount;

        $revenueGrowth = 0;
        if ($prevTotalRevenue > 0) {
            $revenueGrowth = round((($totalRevenue - $prevTotalRevenue) / $prevTotalRevenue) * 100, 1);
        } elseif ($totalRevenue > 0) {
            $revenueGrowth = 100;
        }

        $transactionsGrowth = 0;
        $currentTransactions = $orderCount + $repairCount;
        if ($prevTotalTransactions > 0) {
            $transactionsGrowth = round((($currentTransactions - $prevTotalTransactions) / $prevTotalTransactions) * 100, 1);
        } elseif ($currentTransactions > 0) {
            $transactionsGrowth = 100;
        }

        // 5. CHUỖI THỜI GIAN CHO BIỂU ĐỒ (SMART TIMELINE GROUPING)
        $chartData = [];
        $diffDays = $start->diffInDays($end) + 1;

        if ($diffDays <= 14) {
            // Dưới 14 ngày: Hiển thị từng ngày chi tiết
            $periodDays = CarbonPeriod::create($start, '1 day', $end);
            foreach ($periodDays as $day) {
                $dayStart = $day->copy()->startOfDay();
                $dayEnd = $day->copy()->endOfDay();

                $dayOrderRev = (float) Order::whereBetween('created_at', [$dayStart, $dayEnd])
                    ->where('status', '!=', 'cancelled')
                    ->sum('grand_total');

                $dayRepairRev = (float) RepairTicket::whereBetween('created_at', [$dayStart, $dayEnd])
                    ->where('status', '!=', 'cancelled')
                    ->sum('grand_total');

                $chartData[] = [
                    'label' => $day->format('d/m'),
                    'full_date' => $day->format('d/m/Y'),
                    'order_revenue' => $dayOrderRev,
                    'repair_revenue' => $dayRepairRev,
                    'total_revenue' => $dayOrderRev + $dayRepairRev,
                ];
            }
        } else {
            // Trên 14 ngày: Nhóm theo khoảng tuần / chặng 5-7 ngày để cột rộng và trực quan
            $curr = $start->copy();
            $chunkIndex = 1;
            while ($curr <= $end) {
                $intervalEnd = $curr->copy()->addDays(6)->endOfDay();
                if ($intervalEnd > $end) {
                    $intervalEnd = $end->copy();
                }

                $intervalOrderRev = (float) Order::whereBetween('created_at', [$curr, $intervalEnd])
                    ->where('status', '!=', 'cancelled')
                    ->sum('grand_total');

                $intervalRepairRev = (float) RepairTicket::whereBetween('created_at', [$curr, $intervalEnd])
                    ->where('status', '!=', 'cancelled')
                    ->sum('grand_total');

                $chartData[] = [
                    'label' => 'T' . $chunkIndex . ' (' . $curr->format('d/m') . ')',
                    'full_date' => 'Tuần ' . $chunkIndex . ': ' . $curr->format('d/m') . ' - ' . $intervalEnd->format('d/m/Y'),
                    'order_revenue' => $intervalOrderRev,
                    'repair_revenue' => $intervalRepairRev,
                    'total_revenue' => $intervalOrderRev + $intervalRepairRev,
                ];

                $curr = $curr->copy()->addDays(7)->startOfDay();
                $chunkIndex++;
            }
        }

        $maxChartVal = 1;
        foreach ($chartData as $item) {
            if ($item['total_revenue'] > $maxChartVal) {
                $maxChartVal = $item['total_revenue'];
            }
        }

        // 6. CƠ CẤU THANH TOÁN HỢP NHẤT
        $pmMap = [
            'cash' => ['label' => 'Tiền mặt tại quầy', 'total' => 0, 'count' => 0, 'icon' => '💵'],
            'vietqr' => ['label' => 'Chuyển khoản VietQR', 'total' => 0, 'count' => 0, 'icon' => '⚡'],
            'transfer' => ['label' => 'Chuyển khoản ngân hàng', 'total' => 0, 'count' => 0, 'icon' => '🏦'],
            'cod' => ['label' => 'Giao hàng thu tiền (COD)', 'total' => 0, 'count' => 0, 'icon' => '📦'],
            'other' => ['label' => 'Khác / Chưa ghi nhận', 'total' => 0, 'count' => 0, 'icon' => '💳'],
        ];

        $orderPayments = (clone $ordersQuery)
            ->select('payment_method', DB::raw('SUM(grand_total) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('payment_method')
            ->get();

        foreach ($orderPayments as $op) {
            $key = in_array($op->payment_method, ['cash', 'vietqr', 'transfer', 'cod']) ? $op->payment_method : 'other';
            $pmMap[$key]['total'] += (float) $op->total;
            $pmMap[$key]['count'] += (int) $op->count;
        }

        $repairPayments = (clone $repairsQuery)
            ->select('payment_method', DB::raw('SUM(grand_total) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('payment_method')
            ->get();

        foreach ($repairPayments as $rp) {
            $key = in_array($rp->payment_method, ['cash', 'vietqr', 'transfer', 'cod']) ? $rp->payment_method : 'other';
            $pmMap[$key]['total'] += (float) $rp->total;
            $pmMap[$key]['count'] += (int) $rp->count;
        }

        // Lọc bỏ phương thức không có số liệu
        $activePaymentBreakdown = array_filter($pmMap, fn($item) => $item['total'] > 0 || $item['count'] > 0);

        // 7. BẢNG KÊ HÓA ĐƠN & THUẾ GTGT (HỖ TRỢ TÌM KIẾM & BỘ LỌC)
        $vatQuery = Order::whereBetween('created_at', [$start, $end])
            ->where('status', '!=', 'cancelled');

        if ($this->vatFilterType === 'vat_only') {
            $vatQuery->where(function ($q) {
                $q->where('is_vat_invoice', true)->orWhere('tax_amount', '>', 0);
            });
        } elseif ($this->vatFilterType === 'non_vat') {
            $vatQuery->where('is_vat_invoice', false)->where('tax_amount', '<=', 0);
        }

        if (!empty(trim($this->vatSearch))) {
            $keyword = '%' . trim($this->vatSearch) . '%';
            $vatQuery->where(function ($q) use ($keyword) {
                $q->where('order_code', 'LIKE', $keyword)
                    ->orWhere('company_name', 'LIKE', $keyword)
                    ->orWhere('company_tax_id', 'LIKE', $keyword)
                    ->orWhere('customer_name', 'LIKE', $keyword)
                    ->orWhere('customer_phone', 'LIKE', $keyword);
            });
        }

        $vatOrders = $vatQuery->with(['orderItems'])
            ->orderBy('created_at', 'desc')
            ->take(100)
            ->get();

        return [
            // Tổng hợp
            'total_revenue' => $totalRevenue,
            'total_cogs' => $totalCogs,
            'gross_profit' => $grossProfit,
            'profit_margin' => $profitMargin,
            'total_vat' => $totalVat,
            'total_paid' => $totalPaid,
            'total_receivables' => $totalReceivables,
            'current_transactions' => $currentTransactions,

            // Tăng trưởng
            'revenue_growth' => $revenueGrowth,
            'transactions_growth' => $transactionsGrowth,
            'prev_total_revenue' => $prevTotalRevenue,
            'prev_start_label' => $prevStart->format('d/m/Y'),
            'prev_end_label' => $prevEnd->format('d/m/Y'),

            // Chi tiết Bán hàng VPP
            'order_count' => $orderCount,
            'order_revenue' => $orderRevenue,
            'order_subtotal' => $orderSubtotal,
            'order_discount' => $orderDiscount,
            'order_vat' => $orderVat,
            'order_shipping' => $orderShipping,
            'order_cogs' => $orderCogs,
            'order_paid' => $orderPaid,
            'order_receivables' => $orderReceivables,
            'vat_orders_count' => $vatOrdersCount,
            'pos_orders' => $posOrders,
            'pos_revenue' => $posRevenue,
            'online_orders' => $onlineOrders,
            'online_revenue' => $onlineRevenue,

            // Chi tiết Dịch vụ Sửa chữa
            'repair_count' => $repairCount,
            'repair_revenue' => $repairRevenue,
            'repair_labor' => $repairLabor,
            'repair_parts' => $repairParts,
            'repair_discount' => $repairDiscount,
            'repair_vat' => $repairVat,
            'repair_cogs' => $repairCogs,
            'repair_paid' => $repairPaid,
            'repair_receivables' => $repairReceivables,

            // Biểu đồ & Cơ cấu
            'chart_data' => $chartData,
            'max_chart_val' => $maxChartVal,
            'payment_breakdown' => $activePaymentBreakdown,

            // Bảng kê thuế
            'vat_orders' => $vatOrders,
        ];
    }

    /**
     * Xuất file CSV Bảng kê hóa đơn và thuế GTGT (Chuẩn UTF-8 with BOM mở trực tiếp bằng Microsoft Excel)
     */
    public function exportTaxCsv(): StreamedResponse
    {
        $start = Carbon::parse($this->startDate ?? now()->startOfMonth())->startOfDay();
        $end = Carbon::parse($this->endDate ?? now()->endOfMonth())->endOfDay();

        $filename = 'Bang_Ke_Hoa_Don_Thue_GTGT_' . $start->format('Ymd') . '_' . $end->format('Ymd') . '.csv';

        $orders = Order::whereBetween('created_at', [$start, $end])
            ->where('status', '!=', 'cancelled')
            ->orderBy('created_at', 'asc')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->streamDownload(function () use ($orders, $start, $end) {
            $output = fopen('php://output', 'w');

            // Ghi BOM UTF-8 để Microsoft Excel nhận diện đúng tiếng Việt có dấu
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Tiêu đề báo cáo
            fputcsv($output, ['BẢNG KÊ HÓA ĐƠN & THUẾ GIÁ TRỊ GIA TĂNG (GTGT) - ÁNH DƯƠNG ERP']);
            fputcsv($output, ['Kỳ báo cáo:', 'Từ ' . $start->format('d/m/Y') . ' đến ' . $end->format('d/m/Y')]);
            fputcsv($output, ['Ngày xuất file:', now()->format('d/m/Y H:i:s')]);
            fputcsv($output, []); // Dòng trống

            // Dòng tiêu đề cột
            fputcsv($output, [
                'STT',
                'Mã Hóa Đơn',
                'Ngày Lập',
                'Tên Khách Hàng / Đơn Vị Mua',
                'Mã Số Thuế (MST)',
                'Địa Chỉ Doanh Nghiệp',
                'Doanh Thu Chưa Thuế (VNĐ)',
                'Thuế Suất (%)',
                'Tiền Thuế GTGT (VNĐ)',
                'Tổng Tiền Thanh Toán (VNĐ)',
                'Hình Thức Thanh Toán',
                'Kênh Bán',
                'Hóa Đơn VAT',
                'Ghi Chú',
            ]);

            $stt = 1;
            $sumUntaxed = 0;
            $sumVat = 0;
            $sumGrand = 0;

            foreach ($orders as $ord) {
                $untaxed = (float) ($ord->subtotal - $ord->discount_amount);
                $vat = (float) $ord->tax_amount;
                $grand = (float) $ord->grand_total;

                $sumUntaxed += $untaxed;
                $sumVat += $vat;
                $sumGrand += $grand;

                fputcsv($output, [
                    $stt++,
                    $ord->order_code,
                    $ord->created_at->format('d/m/Y H:i'),
                    $ord->company_name ?: $ord->customer_name,
                    $ord->company_tax_id ?: 'Cá nhân',
                    $ord->company_address ?: $ord->customer_address,
                    number_format($untaxed, 0, '', ''),
                    (int) ($ord->tax_rate ?: 0),
                    number_format($vat, 0, '', ''),
                    number_format($grand, 0, '', ''),
                    match ($ord->payment_method) {
                        'vietqr' => 'Chuyển khoản VietQR',
                        'transfer' => 'Chuyển khoản NH',
                        'cod' => 'COD',
                        default => 'Tiền mặt',
                    },
                    $ord->channel === 'pos' ? 'Bán tại quầy (POS)' : 'Online / Storefront',
                    $ord->is_vat_invoice ? 'Có (Xuất VAT)' : 'Không',
                    $ord->notes ?: '',
                ]);
            }

            // Dòng tổng cộng
            fputcsv($output, []);
            fputcsv($output, [
                'TỔNG CỘNG',
                '',
                '',
                count($orders) . ' đơn hàng',
                '',
                '',
                number_format($sumUntaxed, 0, '', ''),
                '',
                number_format($sumVat, 0, '', ''),
                number_format($sumGrand, 0, '', ''),
                '',
                '',
                '',
                '',
            ]);

            fclose($output);
        }, $filename, $headers);
    }
}
