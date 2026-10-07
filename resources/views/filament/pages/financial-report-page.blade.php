<x-filament-panels::page>
    @php
        $data = $this->reportData;
    @endphp

    <div class="space-y-6">
        <!-- TOOLBAR BỘ LỌC THỜI GIAN -->
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider mr-1">Kỳ báo cáo:</span>
                <button type="button" wire:click="$set('period', 'today')" class="px-3 py-1.5 rounded-xl text-xs font-semibold transition {{ $period === 'today' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200' }}">Hôm nay</button>
                <button type="button" wire:click="$set('period', 'last_7_days')" class="px-3 py-1.5 rounded-xl text-xs font-semibold transition {{ $period === 'last_7_days' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200' }}">7 ngày qua</button>
                <button type="button" wire:click="$set('period', 'this_month')" class="px-3 py-1.5 rounded-xl text-xs font-semibold transition {{ $period === 'this_month' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200' }}">Tháng này</button>
                <button type="button" wire:click="$set('period', 'this_quarter')" class="px-3 py-1.5 rounded-xl text-xs font-semibold transition {{ $period === 'this_quarter' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200' }}">Quý này</button>
                <button type="button" wire:click="$set('period', 'all')" class="px-3 py-1.5 rounded-xl text-xs font-semibold transition {{ $period === 'all' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200' }}">Tất cả</button>
            </div>

            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2 text-xs">
                    <input type="date" wire:model.live="startDate" class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-slate-700 dark:text-slate-200">
                    <span class="text-slate-400">đến</span>
                    <input type="date" wire:model.live="endDate" class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-slate-700 dark:text-slate-200">
                </div>

                <button type="button" onclick="window.print()" class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-200 text-xs font-semibold rounded-lg flex items-center gap-1.5 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    <span>In báo cáo</span>
                </button>
            </div>
        </div>

        <!-- 4 CARDS CHỈ SỐ TÀI CHÍNH & THUẾ -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- 1. Doanh thu tổng -->
            <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold text-slate-500 uppercase">Tổng Doanh Thu</span>
                    <span class="p-2 bg-indigo-50 dark:bg-indigo-950 text-indigo-600 rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </span>
                </div>
                <h3 class="text-2xl font-extrabold text-slate-900 dark:text-white">
                    {{ number_format($data['gross_revenue'], 0, ',', '.') }} ₫
                </h3>
                <p class="text-xs text-slate-500 mt-2 flex items-center justify-between">
                    <span>Tổng {{ $data['total_orders'] }} đơn hàng</span>
                    <span class="font-medium text-indigo-600">Bao gồm VAT</span>
                </p>
            </div>

            <!-- 2. Tiền thuế VAT phải nộp -->
            <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold text-slate-500 uppercase">Thuế VAT Đầu Ra (8%-10%)</span>
                    <span class="p-2 bg-rose-50 dark:bg-rose-950 text-rose-600 rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"></path></svg>
                    </span>
                </div>
                <h3 class="text-2xl font-extrabold text-rose-600 dark:text-rose-400">
                    {{ number_format($data['total_vat_tax'], 0, ',', '.') }} ₫
                </h3>
                <p class="text-xs text-slate-500 mt-2 flex items-center justify-between">
                    <span>{{ $data['vat_orders_count'] }} đơn xuất VAT</span>
                    <span class="font-medium text-rose-600">Kê khai thuế</span>
                </p>
            </div>

            <!-- 3. Giá vốn hàng bán -->
            <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold text-slate-500 uppercase">Giá Vốn Hàng Bán (COGS)</span>
                    <span class="p-2 bg-amber-50 dark:bg-amber-950 text-amber-600 rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </span>
                </div>
                <h3 class="text-2xl font-extrabold text-slate-900 dark:text-white">
                    {{ number_format($data['cogs'], 0, ',', '.') }} ₫
                </h3>
                <p class="text-xs text-slate-500 mt-2 flex items-center justify-between">
                    <span>Chi phí nhập kho</span>
                    <span class="font-medium text-amber-600">Giá vốn thực</span>
                </p>
            </div>

            <!-- 4. Lợi nhuận gộp -->
            <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold text-slate-500 uppercase">Lợi Nhuận Gộp</span>
                    <span class="p-2 bg-emerald-50 dark:bg-emerald-950 text-emerald-600 rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </span>
                </div>
                <h3 class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400">
                    {{ number_format($data['gross_profit'], 0, ',', '.') }} ₫
                </h3>
                <p class="text-xs text-slate-500 mt-2 flex items-center justify-between">
                    <span>Biên LN: <strong>{{ $data['profit_margin'] }}%</strong></span>
                    <span class="font-medium text-emerald-600">Doanh thu - Vốn</span>
                </p>
            </div>
        </div>

        <!-- PHẦN CHI TIẾT: BẢNG KÊ THUẾ & PHƯƠNG THỨC THANH TOÁN -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            <!-- BẢNG KÊ HÓA ĐƠN GTGT (8 cols) -->
            <div class="lg:col-span-8 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                    <div>
                        <h4 class="font-bold text-slate-900 dark:text-white text-base">Bảng Kê Hóa Đơn Bán Hàng Kê Khai Thuế GTGT</h4>
                        <p class="text-xs text-slate-500">Mẫu bảng kê tổng hợp doanh thu và thuế GTGT đầu ra bán ra định kỳ</p>
                    </div>
                    <span class="text-xs font-semibold px-2.5 py-1 bg-indigo-50 text-indigo-700 rounded-lg">
                        {{ count($data['vat_orders']) }} hóa đơn
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300">
                                <th class="p-3 font-semibold">Số HĐ / Ngày</th>
                                <th class="p-3 font-semibold">Khách hàng / Công ty</th>
                                <th class="p-3 font-semibold">Mã số thuế</th>
                                <th class="p-3 font-semibold text-right">Doanh thu chưa thuế</th>
                                <th class="p-3 font-semibold text-center">Thuế suất</th>
                                <th class="p-3 font-semibold text-right">Tiền thuế VAT</th>
                                <th class="p-3 font-semibold text-right">Tổng thanh toán</th>
                                <th class="p-3 font-semibold text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse($data['vat_orders'] as $ord)
                                @php
                                    $untaxed = (float) ($ord->subtotal - $ord->discount_amount);
                                @endphp
                                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                                    <td class="p-3 whitespace-nowrap">
                                        <span class="font-bold text-indigo-600 dark:text-indigo-400 font-mono">{{ $ord->order_code }}</span>
                                        <div class="text-[11px] text-slate-400">{{ $ord->created_at->format('d/m/Y H:i') }}</div>
                                    </td>
                                    <td class="p-3 max-w-[180px]">
                                        <div class="font-semibold text-slate-800 dark:text-slate-100 truncate">{{ $ord->company_name ?: $ord->customer_name }}</div>
                                        <div class="text-[11px] text-slate-400 truncate">{{ $ord->customer_phone }}</div>
                                    </td>
                                    <td class="p-3 font-mono text-slate-600 dark:text-slate-400">
                                        {{ $ord->company_tax_id ?: 'Cá nhân' }}
                                    </td>
                                    <td class="p-3 text-right font-medium">
                                        {{ number_format($untaxed, 0, ',', '.') }} ₫
                                    </td>
                                    <td class="p-3 text-center">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                            {{ (int) ($ord->tax_rate ?: 8) }}%
                                        </span>
                                    </td>
                                    <td class="p-3 text-right font-bold text-rose-600">
                                        {{ number_format($ord->tax_amount, 0, ',', '.') }} ₫
                                    </td>
                                    <td class="p-3 text-right font-bold text-slate-900 dark:text-white">
                                        {{ number_format($ord->grand_total, 0, ',', '.') }} ₫
                                    </td>
                                    <td class="p-3 text-center whitespace-nowrap">
                                        <a href="{{ url('/print/vat-invoice/' . $ord->id) }}" target="_blank"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-semibold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                            In VAT
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="p-8 text-center text-slate-400">
                                        Không có đơn hàng nào trong khoảng thời gian này
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- CỘT PHẢI: PHƯƠNG THỨC THANH TOÁN & QUY ĐỊNH THUẾ (4 cols) -->
            <div class="lg:col-span-4 space-y-6">
                <!-- Card phân bổ dòng tiền thanh toán -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-5">
                    <h4 class="font-bold text-slate-900 dark:text-white text-sm mb-4">Cơ Cấu Dòng Tiền Thanh Toán</h4>
                    <div class="space-y-3">
                        @foreach($data['payment_breakdown'] as $pm)
                            @php
                                $pct = $data['gross_revenue'] > 0 ? round(($pm->total / $data['gross_revenue']) * 100, 1) : 0;
                                $pmName = match($pm->payment_method) {
                                    'vietqr' => 'Chuyển khoản VietQR 24/7',
                                    'transfer' => 'Chuyển khoản ngân hàng',
                                    'cod' => 'Giao hàng thu COD',
                                    default => 'Tiền mặt tại quầy',
                                };
                            @endphp
                            <div>
                                <div class="flex items-center justify-between text-xs mb-1">
                                    <span class="font-medium text-slate-700 dark:text-slate-300">{{ $pmName }} ({{ $pm->count }} đơn)</span>
                                    <span class="font-bold text-slate-900 dark:text-white">{{ number_format($pm->total, 0, ',', '.') }} ₫ ({{ $pct }}%)</span>
                                </div>
                                <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2">
                                    <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Card Tóm tắt quy định thuế GTGT -->
                <div class="bg-gradient-to-br from-indigo-50 to-sky-50 dark:from-slate-800 dark:to-slate-800/80 rounded-2xl border border-indigo-100 dark:border-slate-700 p-5">
                    <h4 class="font-bold text-indigo-950 dark:text-indigo-200 text-sm mb-2 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Lưu Ý Pháp Lý Thuế GTGT (2026)
                    </h4>
                    <ul class="text-xs text-indigo-900/80 dark:text-slate-300 space-y-2 leading-relaxed">
                        <li>• Thuế suất VAT mặt hàng văn phòng phẩm, giấy in áp dụng <strong>8%</strong> hoặc <strong>10%</strong> theo Nghị quyết giảm thuế GTGT của Quốc Hội.</li>
                        <li>• Hóa đơn điện tử khởi tạo từ máy tính tiền (POS) có kết nối truyền dữ liệu đến Cơ quan Thuế.</li>
                        <li>• Hạn nộp tờ khai thuế GTGT hàng quý vào ngày cuối cùng của tháng đầu quý sau (Quý I: 30/04, Quý II: 31/07, Quý III: 31/10, Quý IV: 31/01).</li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</x-filament-panels::page>
