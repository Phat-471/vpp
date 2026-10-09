<x-filament-panels::page>
    @php
        $data = $this->reportData;
    @endphp

    <style>
        .report-wrap {
            font-family: inherit;
        }
        .report-hero {
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 60%, #312e81 100%);
            border-radius: 1.25rem;
            padding: 1.5rem;
            color: #ffffff;
            box-shadow: 0 10px 25px -5px rgba(30, 27, 75, 0.25);
            border: 1px solid rgba(99, 102, 241, 0.2);
        }
        .report-pill-btn {
            padding: 0.4rem 0.85rem;
            border-radius: 0.75rem;
            font-size: 0.75rem;
            font-weight: 700;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            border: 1px solid transparent;
        }
        .report-pill-active {
            background: #4f46e5 !important;
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.4);
            border-color: #818cf8 !important;
        }
        .report-pill-inactive {
            background: rgba(255, 255, 255, 0.08);
            color: #cbd5e1;
            border-color: rgba(255, 255, 255, 0.12);
        }
        .report-pill-inactive:hover {
            background: rgba(255, 255, 255, 0.16);
            color: #ffffff;
        }
        .report-grid-4 {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1rem;
        }
        @media (max-width: 1024px) {
            .report-grid-4 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        @media (max-width: 640px) {
            .report-grid-4 {
                grid-template-columns: 1fr;
            }
        }
        .report-kpi-card {
            background: #ffffff;
            border-radius: 1.25rem;
            padding: 1.25rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 8px -2px rgba(15, 23, 42, 0.06);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .dark .report-kpi-card {
            background: #0f172a;
            border-color: #1e293b;
            box-shadow: none;
        }
        .report-tab-nav {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            border-bottom: 2px solid #e2e8f0;
            margin-bottom: 1.5rem;
        }
        .dark .report-tab-nav {
            border-color: #1e293b;
        }
        .report-tab-item {
            padding: 0.75rem 1.25rem;
            font-size: 0.875rem;
            font-weight: 700;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            margin-bottom: -2px;
            transition: all 0.2s ease;
            color: #64748b;
        }
        .report-tab-item.active {
            color: #4f46e5;
            border-bottom-color: #4f46e5;
        }
        .dark .report-tab-item.active {
            color: #818cf8;
            border-bottom-color: #818cf8;
        }
        .report-bar-col {
            transition: all 0.25s ease;
            border-radius: 6px 6px 0 0;
            cursor: pointer;
        }
        .report-bar-col:hover {
            filter: brightness(1.15);
            transform: scaleY(1.02);
            transform-origin: bottom;
        }
        .report-content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.5rem;
        }
        @media (max-width: 1024px) {
            .report-content-grid {
                grid-template-columns: 1fr;
            }
        }
        @media print {
            .no-print {
                display: none !important;
            }
            .report-hero {
                background: #ffffff !important;
                color: #000000 !important;
                border: 1px solid #000000 !important;
                box-shadow: none !important;
            }
            .report-kpi-card {
                box-shadow: none !important;
                border: 1px solid #999999 !important;
            }
        }
    </style>

    <div class="report-wrap space-y-6">

        {{-- 1. HERO HEADER BANNER --}}
        <div class="report-hero">
            <div class="flex flex-col xl:flex-row items-start xl:items-center justify-between gap-4">
                <div class="flex items-center space-x-3.5">
                    <div style="background: rgba(99, 102, 241, 0.25); border: 1px solid rgba(165, 180, 252, 0.3);" class="w-12 h-12 rounded-2xl flex items-center justify-center text-2xl shrink-0 shadow-inner">
                        📊
                    </div>
                    <div>
                        <div class="flex items-center space-x-2">
                            <h2 style="color: #ffffff;" class="text-xl font-black tracking-tight">Báo Cáo Doanh Thu & Thuế GTGT</h2>
                            <span style="background: rgba(16, 185, 129, 0.25); color: #6ee7b7; border: 1px solid rgba(52, 211, 153, 0.4);" class="px-2.5 py-0.5 rounded-full text-[11px] font-bold">
                                Hợp Nhất Toàn Diện
                            </span>
                        </div>
                        <p style="color: #c7d2fe;" class="text-xs mt-1">
                            Tổng hợp số liệu bán lẻ văn phòng phẩm & dịch vụ kỹ thuật sửa chữa máy in từ 
                            <strong style="color: #ffffff;">{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</strong> đến 
                            <strong style="color: #ffffff;">{{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</strong>
                        </p>
                    </div>
                </div>

                {{-- Quick Actions: Export CSV & Print --}}
                <div class="flex flex-wrap items-center gap-2.5 no-print">
                    <button type="button" wire:click="exportTaxCsv" wire:loading.attr="disabled"
                        style="background: #059669; color: #ffffff;"
                        class="px-3.5 py-2 rounded-xl text-xs font-bold hover:bg-emerald-700 transition shadow-sm flex items-center space-x-1.5 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span>Xuất Excel / CSV (HTKK)</span>
                    </button>

                    <button type="button" onclick="window.print()"
                        style="background: rgba(255, 255, 255, 0.15); color: #ffffff; border: 1px solid rgba(255, 255, 255, 0.25);"
                        class="px-3.5 py-2 rounded-xl text-xs font-bold hover:bg-white/25 transition shadow-sm flex items-center space-x-1.5 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        <span>In báo cáo</span>
                    </button>
                </div>
            </div>

            {{-- Toolbar Lọc Kỳ Thời Gian --}}
            <div style="border-top: 1px solid rgba(255, 255, 255, 0.12);" class="mt-4 pt-4 flex flex-col md:flex-row items-start md:items-center justify-between gap-3 no-print">
                <div class="flex flex-wrap items-center gap-1.5">
                    <span style="color: #94a3b8;" class="text-[11px] font-bold uppercase tracking-wider mr-1">Kỳ:</span>
                    <button type="button" wire:click="setPeriod('today')" class="report-pill-btn {{ $period === 'today' ? 'report-pill-active' : 'report-pill-inactive' }}">Hôm nay</button>
                    <button type="button" wire:click="setPeriod('yesterday')" class="report-pill-btn {{ $period === 'yesterday' ? 'report-pill-active' : 'report-pill-inactive' }}">Hôm qua</button>
                    <button type="button" wire:click="setPeriod('last_7_days')" class="report-pill-btn {{ $period === 'last_7_days' ? 'report-pill-active' : 'report-pill-inactive' }}">7 ngày</button>
                    <button type="button" wire:click="setPeriod('this_month')" class="report-pill-btn {{ $period === 'this_month' ? 'report-pill-active' : 'report-pill-inactive' }}">Tháng này</button>
                    <button type="button" wire:click="setPeriod('last_month')" class="report-pill-btn {{ $period === 'last_month' ? 'report-pill-active' : 'report-pill-inactive' }}">Tháng trước</button>
                    <button type="button" wire:click="setPeriod('this_quarter')" class="report-pill-btn {{ $period === 'this_quarter' ? 'report-pill-active' : 'report-pill-inactive' }}">Quý này</button>
                    <button type="button" wire:click="setPeriod('this_year')" class="report-pill-btn {{ $period === 'this_year' ? 'report-pill-active' : 'report-pill-inactive' }}">Năm nay</button>
                    <button type="button" wire:click="setPeriod('all')" class="report-pill-btn {{ $period === 'all' ? 'report-pill-active' : 'report-pill-inactive' }}">Toàn bộ</button>
                </div>

                {{-- Custom Date Pickers --}}
                <div class="flex items-center space-x-2 text-xs">
                    <span style="color: #cbd5e1;" class="text-[11px] font-semibold">Tùy chọn:</span>
                    <input type="date" wire:model.live="startDate"
                        style="background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); color: #ffffff;"
                        class="rounded-lg px-2.5 py-1 text-xs focus:ring-2 focus:ring-indigo-400 outline-hidden">
                    <span style="color: #94a3b8;">→</span>
                    <input type="date" wire:model.live="endDate"
                        style="background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); color: #ffffff;"
                        class="rounded-lg px-2.5 py-1 text-xs focus:ring-2 focus:ring-indigo-400 outline-hidden">
                </div>
            </div>
        </div>

        {{-- 2. 4 CARDS CHỈ SỐ TÀI CHÍNH QUAN TRỌNG (GRID 4 CỘT) --}}
        <div class="report-grid-4">
            
            {{-- KPI 1: TỔNG DOANH THU HỢP NHẤT --}}
            <div class="report-kpi-card">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Tổng Doanh Thu Hợp Nhất</span>
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold">
                            💰
                        </div>
                    </div>
                    <div class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                        {{ number_format($data['total_revenue'], 0, ',', '.') }} <span class="text-base font-bold text-slate-500">₫</span>
                    </div>

                    {{-- Growth Badge --}}
                    <div class="mt-2 flex items-center space-x-1.5 text-xs font-semibold">
                        @if($data['revenue_growth'] >= 0)
                            <span class="inline-flex items-center text-emerald-600 dark:text-emerald-400">
                                <svg class="w-3.5 h-3.5 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                +{{ $data['revenue_growth'] }}%
                            </span>
                        @else
                            <span class="inline-flex items-center text-rose-600 dark:text-rose-400">
                                <svg class="w-3.5 h-3.5 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6"></path></svg>
                                {{ $data['revenue_growth'] }}%
                            </span>
                        @endif
                        <span class="text-slate-400 font-normal">so với kỳ trước</span>
                    </div>
                </div>

                {{-- Breakdown nhỏ: VPP vs Sửa chữa --}}
                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 text-[11px] space-y-1 text-slate-500 dark:text-slate-400">
                    <div class="flex items-center justify-between">
                        <span>📦 Bán hàng VPP ({{ $data['order_count'] }} đơn):</span>
                        <strong class="text-slate-800 dark:text-slate-200">{{ number_format($data['order_revenue'], 0, ',', '.') }} ₫</strong>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>🔧 Sửa máy in ({{ $data['repair_count'] }} phiếu):</span>
                        <strong class="text-slate-800 dark:text-slate-200">{{ number_format($data['repair_revenue'], 0, ',', '.') }} ₫</strong>
                    </div>
                </div>
            </div>

            {{-- KPI 2: DÒNG TIỀN THỰC THU & CÔNG NỢ --}}
            <div class="report-kpi-card">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Tiền Đã Thực Thu</span>
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold">
                            🏦
                        </div>
                    </div>
                    <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 tracking-tight">
                        {{ number_format($data['total_paid'], 0, ',', '.') }} <span class="text-base font-bold text-slate-500">₫</span>
                    </div>

                    <div class="mt-2 text-xs text-slate-500">
                        Tiền mặt vào két & tài khoản chuyển khoản
                    </div>
                </div>

                {{-- Công nợ cảnh báo --}}
                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 text-[11px]">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">⏳ Còn nợ chưa thu:</span>
                        @if($data['total_receivables'] > 0)
                            <strong class="text-rose-600 font-bold bg-rose-50 dark:bg-rose-950/40 px-2 py-0.5 rounded-md">
                                {{ number_format($data['total_receivables'], 0, ',', '.') }} ₫
                            </strong>
                        @else
                            <span class="text-emerald-600 font-semibold">Đã thu đủ 100%</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- KPI 3: THUẾ VAT ĐẦU RA CẦN KÊ KHAI --}}
            <div class="report-kpi-card">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Thuế GTGT (VAT) Đầu Ra</span>
                        <div class="w-8 h-8 rounded-lg bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center font-bold">
                            🧾
                        </div>
                    </div>
                    <div class="text-2xl font-black text-rose-600 dark:text-rose-400 tracking-tight">
                        {{ number_format($data['total_vat'], 0, ',', '.') }} <span class="text-base font-bold text-slate-500">₫</span>
                    </div>

                    <div class="mt-2 text-xs text-slate-500">
                        Kê khai thuế theo Nghị quyết Quốc Hội
                    </div>
                </div>

                {{-- Số lượng hóa đơn xuất VAT --}}
                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 text-[11px] space-y-1 text-slate-500">
                    <div class="flex items-center justify-between">
                        <span>Hóa đơn VAT đã phát hành:</span>
                        <strong class="text-slate-800 dark:text-slate-200">{{ $data['vat_orders_count'] }} đơn</strong>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Thuế suất phổ biến:</span>
                        <span class="font-bold text-indigo-600">8% & 10%</span>
                    </div>
                </div>
            </div>

            {{-- KPI 4: LỢI NHUẬN GỘP & BIÊN LỢI NHUẬN --}}
            <div class="report-kpi-card">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Lợi Nhuận Gộp Ước Tính</span>
                        <div class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold">
                            📈
                        </div>
                    </div>
                    <div class="text-2xl font-black {{ $data['gross_profit'] >= 0 ? 'text-slate-900 dark:text-white' : 'text-rose-600' }} tracking-tight">
                        {{ number_format($data['gross_profit'], 0, ',', '.') }} <span class="text-base font-bold text-slate-500">₫</span>
                    </div>

                    <div class="mt-2 flex items-center space-x-1.5 text-xs">
                        <span class="px-2 py-0.5 rounded-md font-bold {{ $data['profit_margin'] >= 20 ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-amber-100 text-amber-800' }}">
                            Biên LN: {{ $data['profit_margin'] }}%
                        </span>
                        <span class="text-slate-400">Doanh thu - Giá vốn</span>
                    </div>
                </div>

                {{-- Chi tiết giá vốn --}}
                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 text-[11px] text-slate-500 space-y-1">
                    <div class="flex items-center justify-between">
                        <span>Tổng giá vốn xuất kho:</span>
                        <strong class="text-slate-800 dark:text-slate-200">{{ number_format($data['total_cogs'], 0, ',', '.') }} ₫</strong>
                    </div>
                </div>
            </div>

        </div>

        {{-- 3. TAB NAVIGATION --}}
        <div class="report-tab-nav no-print">
            <button type="button" wire:click="setActiveTab('overview')" class="report-tab-item {{ $activeTab === 'overview' ? 'active' : '' }}">
                📈 Biểu đồ Doanh thu & Dòng tiền
            </button>
            <button type="button" wire:click="setActiveTab('vat_table')" class="report-tab-item {{ $activeTab === 'vat_table' ? 'active' : '' }}">
                📑 Bảng kê Thuế GTGT & Hóa đơn ({{ count($data['vat_orders']) }})
            </button>
            <button type="button" wire:click="setActiveTab('breakdown')" class="report-tab-item {{ $activeTab === 'breakdown' ? 'active' : '' }}">
                ⚖️ Phân tích Bán hàng vs Sửa chữa
            </button>
        </div>

        {{-- TAB 1: OVERVIEW & BIỂU ĐỒ --}}
        @if($activeTab === 'overview')
            <div class="report-content-grid">

                {{-- BIỂU ĐỒ DOANH THU THEO THỜI GIAN --}}
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 mb-6">
                            <div>
                                <h3 class="font-extrabold text-slate-900 dark:text-white text-base">Diễn biến Doanh thu theo kỳ</h3>
                                <p class="text-xs text-slate-500">So sánh doanh thu bán hàng văn phòng phẩm và dịch vụ sửa máy in</p>
                            </div>
                            <div class="flex items-center space-x-3 text-xs">
                                <span class="flex items-center space-x-1.5">
                                    <span class="w-3 h-3 rounded-xs bg-indigo-600 inline-block"></span>
                                    <span class="text-slate-600 dark:text-slate-300 font-medium">Bán hàng VPP</span>
                                </span>
                                <span class="flex items-center space-x-1.5">
                                    <span class="w-3 h-3 rounded-xs bg-emerald-500 inline-block"></span>
                                    <span class="text-slate-600 dark:text-slate-300 font-medium">Sửa máy in</span>
                                </span>
                            </div>
                        </div>

                        {{-- Biểu đồ SVG Bar Chart thuần không cần thư viện bên ngoài --}}
                        @if(count($data['chart_data']) > 0)
                            <div class="relative pt-4 pb-2" style="height: 270px;">
                                <div style="height: 230px;" class="flex items-end justify-between gap-2 sm:gap-4 px-2 border-b border-slate-200 dark:border-slate-800">
                                    @foreach($data['chart_data'] as $c)
                                        @php
                                            $maxV = $data['max_chart_val'] > 0 ? $data['max_chart_val'] : 1;
                                            $heightTotal = min(100, max(8, round(($c['total_revenue'] / $maxV) * 100)));
                                        @endphp
                                        <div class="flex-1 flex flex-col items-center justify-end group relative" style="height: 100%;">
                                            {{-- Tooltip hover --}}
                                            <div class="absolute bottom-full mb-2 hidden group-hover:flex flex-col z-20 bg-slate-900 text-white text-[11px] rounded-lg p-2.5 shadow-xl whitespace-nowrap pointer-events-none border border-slate-700">
                                                <div class="font-bold text-slate-200 border-b border-slate-700 pb-1 mb-1">{{ $c['full_date'] }}</div>
                                                <div class="flex justify-between space-x-3 text-indigo-300">
                                                    <span>Bán hàng VPP:</span>
                                                    <b>{{ number_format($c['order_revenue'], 0, ',', '.') }} ₫</b>
                                                </div>
                                                <div class="flex justify-between space-x-3 text-emerald-300">
                                                    <span>Sửa máy in:</span>
                                                    <b>{{ number_format($c['repair_revenue'], 0, ',', '.') }} ₫</b>
                                                </div>
                                                <div class="flex justify-between space-x-3 text-white font-bold pt-1 border-t border-slate-800 mt-1">
                                                    <span>Tổng thu:</span>
                                                    <span>{{ number_format($c['total_revenue'], 0, ',', '.') }} ₫</span>
                                                </div>
                                            </div>

                                            {{-- Cột chồng 2 màu --}}
                                            <div class="w-full max-w-[48px] flex flex-col justify-end report-bar-col overflow-hidden rounded-t-lg shadow-sm" style="height: {{ $heightTotal }}%; min-height: 8px;">
                                                @if($c['repair_revenue'] > 0)
                                                    <div style="flex: {{ $c['repair_revenue'] }}; background: #10b981; min-height: 4px;"></div>
                                                @endif
                                                @if($c['order_revenue'] > 0)
                                                    <div style="flex: {{ $c['order_revenue'] }}; background: #4f46e5; min-height: 4px;"></div>
                                                @endif
                                                @if($c['total_revenue'] == 0)
                                                    <div style="height: 4px; background: #e2e8f0; width: 100%;"></div>
                                                @endif
                                            </div>

                                            <span class="text-[11px] font-semibold text-slate-500 mt-2 truncate w-full text-center">
                                                {{ $c['label'] }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div style="height: 230px;" class="flex items-center justify-center text-slate-400 text-sm">
                                Không có dữ liệu trong kỳ này
                            </div>
                        @endif
                    </div>
                </div>

                {{-- CƠ CẤU THANH TOÁN & DÒNG TIỀN --}}
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm space-y-5">
                    <div>
                        <h3 class="font-extrabold text-slate-900 dark:text-white text-base">Cơ cấu Phương thức Thanh toán</h3>
                        <p class="text-xs text-slate-500">Phân bổ dòng tiền thu được từ khách</p>
                    </div>

                    <div class="space-y-4">
                        @forelse($data['payment_breakdown'] as $pmKey => $pm)
                            @php
                                $pct = $data['total_revenue'] > 0 ? round(($pm['total'] / $data['total_revenue']) * 100, 1) : 0;
                                $color = match($pmKey) {
                                    'vietqr' => 'bg-indigo-600',
                                    'cash' => 'bg-emerald-600',
                                    'transfer' => 'bg-sky-600',
                                    'cod' => 'bg-amber-500',
                                    default => 'bg-slate-500',
                                };
                            @endphp
                            <div class="bg-slate-50 dark:bg-slate-800/50 p-3 rounded-xl border border-slate-100 dark:border-slate-800">
                                <div class="flex items-center justify-between text-xs mb-1.5">
                                    <span class="font-bold text-slate-800 dark:text-slate-200 flex items-center space-x-1.5">
                                        <span>{{ $pm['icon'] }}</span>
                                        <span>{{ $pm['label'] }}</span>
                                    </span>
                                    <span class="font-black text-slate-900 dark:text-white">
                                        {{ number_format($pm['total'], 0, ',', '.') }} ₫
                                    </span>
                                </div>
                                <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2 overflow-hidden flex">
                                    <div class="{{ $color }} h-2 rounded-full" style="width: {{ $pct }}%"></div>
                                </div>
                                <div class="flex items-center justify-between text-[11px] text-slate-500 mt-1">
                                    <span>{{ $pm['count'] }} giao dịch</span>
                                    <span class="font-bold">{{ $pct }}% tổng thu</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-slate-400 text-xs">
                                Chưa có giao dịch nào được ghi nhận
                            </div>
                        @endforelse
                    </div>

                    {{-- Thông tin kênh bán hàng --}}
                    <div class="pt-4 border-t border-slate-200 dark:border-slate-800">
                        <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Tỷ trọng Kênh Bán Hàng:</h4>
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div class="p-2.5 rounded-xl bg-indigo-50/50 dark:bg-indigo-950/20 border border-indigo-100 dark:border-indigo-900/40">
                                <div class="text-[11px] text-indigo-700 dark:text-indigo-400 font-semibold">⚡ Bán tại quầy (POS)</div>
                                <div class="text-sm font-extrabold text-slate-900 dark:text-white mt-0.5">{{ number_format($data['pos_revenue'], 0, ',', '.') }} ₫</div>
                                <div class="text-[10px] text-slate-500">{{ $data['pos_orders'] }} đơn</div>
                            </div>
                            <div class="p-2.5 rounded-xl bg-sky-50/50 dark:bg-sky-950/20 border border-sky-100 dark:border-sky-900/40">
                                <div class="text-[11px] text-sky-700 dark:text-sky-400 font-semibold">🌐 Online / Website</div>
                                <div class="text-sm font-extrabold text-slate-900 dark:text-white mt-0.5">{{ number_format($data['online_revenue'], 0, ',', '.') }} ₫</div>
                                <div class="text-[10px] text-slate-500">{{ $data['online_orders'] }} đơn</div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        @endif

        {{-- TAB 2: BẢNG KÊ THUẾ GTGT & HÓA ĐƠN --}}
        @if($activeTab === 'vat_table')
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                {{-- Header & Search --}}
                <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h3 class="font-extrabold text-slate-900 dark:text-white text-base">Bảng Kê Hóa Đơn & Thuế GTGT Bán Ra</h3>
                        <p class="text-xs text-slate-500">Mẫu kê khai doanh thu và thuế GTGT đầu ra theo kỳ báo cáo</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        {{-- Search Input --}}
                        <div class="relative">
                            <input type="text" wire:model.live.debounce.350ms="vatSearch"
                                placeholder="Tìm mã đơn, MST, tên khách..."
                                class="pl-8 pr-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500 text-slate-900 dark:text-white">
                            <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>

                        {{-- Filter Selector --}}
                        <select wire:model.live="vatFilterType"
                            class="py-1.5 px-3 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-700 dark:text-slate-200 font-medium">
                            <option value="all">Tất cả hóa đơn</option>
                            <option value="vat_only">Chỉ hóa đơn có VAT</option>
                            <option value="non_vat">Đơn lẻ thông thường (Không VAT)</option>
                        </select>
                    </div>
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800/70 border-b border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300">
                                <th class="p-3 font-bold">Số HĐ / Ngày</th>
                                <th class="p-3 font-bold">Khách hàng / Doanh nghiệp</th>
                                <th class="p-3 font-bold">Mã số thuế (MST)</th>
                                <th class="p-3 font-bold text-right">Doanh thu chưa thuế</th>
                                <th class="p-3 font-bold text-center">Thuế suất</th>
                                <th class="p-3 font-bold text-right">Tiền thuế VAT</th>
                                <th class="p-3 font-bold text-right">Tổng thanh toán</th>
                                <th class="p-3 font-bold text-center">Hình thức</th>
                                <th class="p-3 font-bold text-center no-print">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse($data['vat_orders'] as $ord)
                                @php
                                    $untaxed = (float) ($ord->subtotal - $ord->discount_amount);
                                @endphp
                                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                                    <td class="p-3 whitespace-nowrap">
                                        <div class="font-extrabold text-indigo-600 dark:text-indigo-400 font-mono">{{ $ord->order_code }}</div>
                                        <div class="text-[11px] text-slate-400">{{ $ord->created_at->format('d/m/Y H:i') }}</div>
                                    </td>
                                    <td class="p-3 max-w-[200px]">
                                        <div class="font-semibold text-slate-900 dark:text-white truncate">
                                            {{ $ord->company_name ?: $ord->customer_name }}
                                        </div>
                                        <div class="text-[11px] text-slate-400 truncate">{{ $ord->customer_phone }}</div>
                                    </td>
                                    <td class="p-3 font-mono text-slate-600 dark:text-slate-300">
                                        @if($ord->company_tax_id)
                                            <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 font-bold text-slate-800 dark:text-slate-200">
                                                {{ $ord->company_tax_id }}
                                            </span>
                                        @else
                                            <span class="text-slate-400">Khách lẻ</span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-right font-medium text-slate-700 dark:text-slate-300">
                                        {{ number_format($untaxed, 0, ',', '.') }} ₫
                                    </td>
                                    <td class="p-3 text-center">
                                        @if($ord->tax_rate > 0)
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 dark:bg-rose-950 dark:text-rose-300">
                                                {{ (int) $ord->tax_rate }}%
                                            </span>
                                        @else
                                            <span class="text-slate-400">0%</span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-right font-bold text-rose-600 dark:text-rose-400">
                                        {{ number_format($ord->tax_amount, 0, ',', '.') }} ₫
                                    </td>
                                    <td class="p-3 text-right font-extrabold text-slate-900 dark:text-white">
                                        {{ number_format($ord->grand_total, 0, ',', '.') }} ₫
                                    </td>
                                    <td class="p-3 text-center whitespace-nowrap">
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                            {{ match($ord->payment_method) {
                                                'vietqr' => 'VietQR',
                                                'transfer' => 'Chuyển khoản',
                                                'cod' => 'COD',
                                                default => 'Tiền mặt',
                                            } }}
                                        </span>
                                    </td>
                                    <td class="p-3 text-center whitespace-nowrap no-print">
                                        <div class="flex items-center justify-center space-x-1">
                                            @if($ord->is_vat_invoice)
                                                <a href="{{ url('/print/vat-invoice/' . $ord->id) }}" target="_blank"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-bold text-rose-600 hover:text-rose-800 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:text-rose-300 rounded-lg transition">
                                                    📄 Hóa đơn VAT
                                                </a>
                                            @endif
                                            <a href="{{ url('/print/order/' . $ord->id) }}" target="_blank"
                                                class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-bold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-950/40 dark:text-indigo-300 rounded-lg transition">
                                                🧾 Bill A5
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="p-8 text-center text-slate-400">
                                        Không tìm thấy hóa đơn nào phù hợp với bộ lọc trong kỳ này
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- TAB 3: BREAKDOWN SO SÁNH BÁN HÀNG VPP VS SỬA MÁY IN --}}
        @if($activeTab === 'breakdown')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Khối 1: Bán Hàng Văn Phòng Phẩm --}}
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                        <div class="flex items-center space-x-3">
                            <span class="text-3xl">📦</span>
                            <div>
                                <h3 class="font-extrabold text-slate-900 dark:text-white text-base">Phân Hệ Bán Hàng VPP & Thiết Bị</h3>
                                <p class="text-xs text-slate-500">Giấy in, bút mực, thiết bị văn phòng, máy in mới</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-xl text-xs font-bold bg-indigo-50 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">
                            {{ $data['order_count'] }} đơn hàng
                        </span>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div class="flex justify-between py-1.5 border-b border-slate-100 dark:border-slate-800">
                            <span class="text-slate-500">Tổng doanh thu bán hàng:</span>
                            <strong class="text-slate-900 dark:text-white font-extrabold text-sm">{{ number_format($data['order_revenue'], 0, ',', '.') }} ₫</strong>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-slate-100 dark:border-slate-800">
                            <span class="text-slate-500">Giá vốn xuất kho (COGS):</span>
                            <span class="text-slate-700 dark:text-slate-300 font-semibold">{{ number_format($data['order_cogs'], 0, ',', '.') }} ₫</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-slate-100 dark:border-slate-800">
                            <span class="text-slate-500">Tiền chiết khấu / giảm giá:</span>
                            <span class="text-amber-600 font-semibold">{{ number_format($data['order_discount'], 0, ',', '.') }} ₫</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-slate-100 dark:border-slate-800">
                            <span class="text-slate-500">Thuế VAT phát sinh:</span>
                            <span class="text-rose-600 font-semibold">{{ number_format($data['order_vat'], 0, ',', '.') }} ₫</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-slate-100 dark:border-slate-800">
                            <span class="text-slate-500">Kênh Bán lẻ tại quầy (POS):</span>
                            <span class="text-indigo-600 font-bold">{{ number_format($data['pos_revenue'], 0, ',', '.') }} ₫ ({{ $data['pos_orders'] }} đơn)</span>
                        </div>
                        <div class="flex justify-between py-1.5">
                            <span class="text-slate-500">Kênh Đặt hàng Online:</span>
                            <span class="text-sky-600 font-bold">{{ number_format($data['online_revenue'], 0, ',', '.') }} ₫ ({{ $data['online_orders'] }} đơn)</span>
                        </div>
                    </div>
                </div>

                {{-- Khối 2: Dịch Vụ Kỹ Thuật & Sửa Máy In --}}
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                        <div class="flex items-center space-x-3">
                            <span class="text-3xl">🔧</span>
                            <div>
                                <h3 class="font-extrabold text-slate-900 dark:text-white text-base">Phân Hệ Dịch Vụ Sửa Chữa Máy In</h3>
                                <p class="text-xs text-slate-500">Công kỹ thuật, nạp mực, thay trống, gạt, linh kiện</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                            {{ $data['repair_count'] }} phiếu sửa
                        </span>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div class="flex justify-between py-1.5 border-b border-slate-100 dark:border-slate-800">
                            <span class="text-slate-500">Tổng doanh thu dịch vụ:</span>
                            <strong class="text-emerald-600 dark:text-emerald-400 font-extrabold text-sm">{{ number_format($data['repair_revenue'], 0, ',', '.') }} ₫</strong>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-slate-100 dark:border-slate-800">
                            <span class="text-slate-500">Tiền công dịch vụ kỹ thuật:</span>
                            <span class="text-slate-700 dark:text-slate-300 font-semibold">{{ number_format($data['repair_labor'], 0, ',', '.') }} ₫</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-slate-100 dark:border-slate-800">
                            <span class="text-slate-500">Tiền linh kiện thay thế:</span>
                            <span class="text-slate-700 dark:text-slate-300 font-semibold">{{ number_format($data['repair_parts'], 0, ',', '.') }} ₫</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-slate-100 dark:border-slate-800">
                            <span class="text-slate-500">Giá vốn linh kiện nhập kho:</span>
                            <span class="text-amber-600 font-semibold">{{ number_format($data['repair_cogs'], 0, ',', '.') }} ₫</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-slate-100 dark:border-slate-800">
                            <span class="text-slate-500">Tiền khách đã thanh toán:</span>
                            <span class="text-emerald-600 font-bold">{{ number_format($data['repair_paid'], 0, ',', '.') }} ₫</span>
                        </div>
                        <div class="flex justify-between py-1.5">
                            <span class="text-slate-500">Công nợ dịch vụ còn thu:</span>
                            <span class="text-rose-600 font-bold">{{ number_format($data['repair_receivables'], 0, ',', '.') }} ₫</span>
                        </div>
                    </div>
                </div>

            </div>
        @endif

        {{-- 4. GHI CHÚ CHÍNH SÁCH THUẾ GTGT --}}
        <div class="bg-gradient-to-r from-indigo-50/80 to-slate-50 dark:from-slate-800/80 dark:to-slate-900 p-5 rounded-2xl border border-indigo-100 dark:border-slate-800 text-xs text-slate-600 dark:text-slate-300 flex items-start space-x-3 no-print">
            <span class="text-xl">ℹ️</span>
            <div class="space-y-1">
                <h4 class="font-bold text-slate-800 dark:text-slate-100">Lưu Ý Nghiệp Vụ Kế Toán & Thuế GTGT 2026:</h4>
                <p>• Dữ liệu xuất file Excel / CSV đã được mã hóa UTF-8 chuẩn xác, có thể mở trực tiếp bằng Microsoft Excel mà không bị lỗi font chữ tiếng Việt.</p>
                <p>• Cột thuế suất tự động trích xuất theo từng mặt hàng (8% cho văn phòng phẩm được giảm thuế theo quy định hiện hành, 10% cho các nhóm hàng hóa dịch vụ tiêu chuẩn).</p>
            </div>
        </div>

    </div>
</x-filament-panels::page>
