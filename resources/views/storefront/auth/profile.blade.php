@extends('layouts.storefront')

@section('title', 'Tài Khoản Của Tôi | ' . ($storefrontSettings['site_name'] ?? 'VPP & Dịch Vụ Máy In'))
@section('meta_description', 'Quản lý đơn hàng, theo dõi tiến độ sửa chữa máy in và thông tin tài khoản cá nhân tại ' . ($storefrontSettings['site_name'] ?? 'VPP & Dịch Vụ Máy In'))

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10 space-y-6">

        <!-- Top Account Welcome Banner -->
        <div
            class="bg-gradient-to-r from-slate-900 via-blue-950 to-slate-900 text-white rounded-3xl p-6 sm:p-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 border border-blue-900/60 shadow-xl relative overflow-hidden">
            <!-- Ambient background glows -->
            <div
                class="absolute -right-20 -bottom-20 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none">
            </div>
            <div class="absolute -left-20 -top-20 w-64 h-64 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 flex items-center space-x-4">
                <div
                    class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-emerald-500 via-teal-500 to-indigo-600 flex items-center justify-center text-2xl font-black text-white shadow-lg border-2 border-white/20 shrink-0">
                    {{ mb_substr($customer->name, 0, 1) }}
                </div>
                <div class="space-y-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-xs text-amber-300 font-extrabold uppercase tracking-wider">KHÁCH HÀNG THÂN
                            THIẾT</span>
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40">
                            <svg class="w-3 h-3 mr-1 text-emerald-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            Tài khoản chính thức
                        </span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight">
                        {{ $customer->name }}
                    </h1>
                    <p class="text-xs text-slate-300 font-mono">
                        SĐT: <b>{{ $customer->phone }}</b> @if($customer->email)• Email: {{ $customer->email }}@endif
                    </p>
                </div>
            </div>

            <!-- KPI Quick Counters & Logout -->
            <div class="relative z-10 flex flex-wrap items-center gap-3 w-full md:w-auto justify-between md:justify-end">
                <!-- Counter: Orders -->
                <div
                    class="bg-white/10 backdrop-blur-md px-4 py-2.5 rounded-2xl border border-white/10 text-center min-w-[90px]">
                    <span class="text-[10px] text-slate-300 uppercase font-bold block">Đơn Hàng</span>
                    <span class="text-lg font-black font-mono text-emerald-300">{{ $orders->count() }}</span>
                </div>

                <!-- Counter: Repair Tickets -->
                <div
                    class="bg-white/10 backdrop-blur-md px-4 py-2.5 rounded-2xl border border-white/10 text-center min-w-[90px]">
                    <span class="text-[10px] text-slate-300 uppercase font-bold block">Phiếu Máy In</span>
                    <span class="text-lg font-black font-mono text-amber-300">{{ $repairTickets->count() }}</span>
                </div>

                <!-- Logout Button -->
                <form action="{{ route('customer.logout') }}" method="POST" class="shrink-0">
                    @csrf
                    <button type="submit"
                        class="px-4 py-3 bg-white/10 hover:bg-rose-600 text-white font-bold text-xs rounded-2xl border border-white/20 transition flex items-center space-x-1.5 shadow-sm active:scale-95">
                        <span>🚪</span>
                        <span>Đăng Xuất</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- MAIN DASHBOARD CONTENT: TAB NAVIGATION LAYOUT (MẪU A: CHUẨN SHOPEE/LAZADA) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- LEFT SIDEBAR: TAB NAVIGATION (Desktop 3.5 cols, Mobile Horizontal Scrollable) -->
            <div class="lg:col-span-4 space-y-4">

                <!-- Mobile Navigation Pills (Trượt ngang mượt mà trên điện thoại) -->
                <div class="lg:hidden flex items-center space-x-2 overflow-x-auto no-scrollbar pb-1 text-xs font-bold">
                    <button type="button" onclick="switchTab('orders')" id="m-tab-orders"
                        class="tab-btn px-4 py-2.5 rounded-2xl whitespace-nowrap transition flex items-center space-x-2 bg-indigo-600 text-white shadow-sm">
                        <span>📦</span>
                        <span>Đơn Hàng ({{ $orders->count() }})</span>
                    </button>
                    <button type="button" onclick="switchTab('repairs')" id="m-tab-repairs"
                        class="tab-btn px-4 py-2.5 rounded-2xl whitespace-nowrap transition flex items-center space-x-2 bg-white text-slate-700 border border-slate-200">
                        <span>🖨️</span>
                        <span>Phiếu Sửa ({{ $repairTickets->count() }})</span>
                    </button>
                    <button type="button" onclick="switchTab('profile')" id="m-tab-profile"
                        class="tab-btn px-4 py-2.5 rounded-2xl whitespace-nowrap transition flex items-center space-x-2 bg-white text-slate-700 border border-slate-200">
                        <span>👤</span>
                        <span>Hồ Sơ</span>
                    </button>
                    <button type="button" onclick="switchTab('password')" id="m-tab-password"
                        class="tab-btn px-4 py-2.5 rounded-2xl whitespace-nowrap transition flex items-center space-x-2 bg-white text-slate-700 border border-slate-200">
                        <span>🔒</span>
                        <span>Đổi Mật Khẩu</span>
                    </button>
                </div>

                <!-- Desktop Sidebar Navigation Card -->
                <div class="hidden lg:block bg-white rounded-3xl border border-slate-200 shadow-sm p-3 space-y-1">
                    <div class="px-4 py-3 text-[11px] font-black uppercase text-slate-400 tracking-wider">
                        QUẢN LÝ TÀI KHOẢN
                    </div>

                    <!-- Tab 1: Đơn Hàng -->
                    <button type="button" onclick="switchTab('orders')" id="d-tab-orders"
                        class="tab-btn w-full flex items-center justify-between px-4 py-3.5 rounded-2xl font-bold text-xs transition text-left bg-indigo-50 text-indigo-700">
                        <div class="flex items-center space-x-3">
                            <span class="text-base">📦</span>
                            <span>Đơn Hàng Của Tôi</span>
                        </div>
                        <span
                            class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-indigo-200 text-indigo-900">
                            {{ $orders->count() }}
                        </span>
                    </button>

                    <!-- Tab 2: Phiếu Máy In -->
                    <button type="button" onclick="switchTab('repairs')" id="d-tab-repairs"
                        class="tab-btn w-full flex items-center justify-between px-4 py-3.5 rounded-2xl font-bold text-xs transition text-left text-slate-600 hover:bg-slate-50 hover:text-slate-900">
                        <div class="flex items-center space-x-3">
                            <span class="text-base">🖨️</span>
                            <span>Sưa máy</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-slate-100 text-slate-700">
                            {{ $repairTickets->count() }}
                        </span>
                    </button>

                    <!-- Tab 3: Thông Tin Cá Nhân -->
                    <button type="button" onclick="switchTab('profile')" id="d-tab-profile"
                        class="tab-btn w-full flex items-center justify-between px-4 py-3.5 rounded-2xl font-bold text-xs transition text-left text-slate-600 hover:bg-slate-50 hover:text-slate-900">
                        <div class="flex items-center space-x-3">
                            <span class="text-base">👤</span>
                            <span>Hồ Sơ & Địa Chỉ Nhận Hàng</span>
                        </div>
                        <span>→</span>
                    </button>

                    <!-- Tab 4: Đổi Mật Khẩu -->
                    <button type="button" onclick="switchTab('password')" id="d-tab-password"
                        class="tab-btn w-full flex items-center justify-between px-4 py-3.5 rounded-2xl font-bold text-xs transition text-left text-slate-600 hover:bg-slate-50 hover:text-slate-900">
                        <div class="flex items-center space-x-3">
                            <span class="text-base">🔒</span>
                            <span>Đổi Mật Khẩu Bảo Mật</span>
                        </div>
                        <span>→</span>
                    </button>

                    <!-- Hotline & Support Widget -->
                    <div
                        class="pt-3 mt-3 border-t border-slate-100 p-3 bg-slate-50 rounded-2xl text-[11px] text-slate-500 space-y-1">
                        <span class="font-bold text-slate-700 block">📞 Hỗ trợ khách hàng:</span>
                        <p>Hotline: <a href="{{ $storefrontSettings['hotline_url'] ?? 'tel:0974194305' }}"
                                class="text-indigo-600 font-bold hover:underline">{{ $storefrontSettings['hotline'] ?? '0974.194.305' }}</a>
                        </p>
                        <p>Zalo: <a href="{{ $storefrontSettings['zalo_url'] ?? 'https://zalo.me/0974194305' }}"
                                target="_blank" class="text-sky-600 font-bold hover:underline">Chat trực tiếp 24/7</a></p>
                    </div>
                </div>

            </div>

            <!-- RIGHT CONTENT AREA: TAB PANES (Desktop 8.5 cols) -->
            <div class="lg:col-span-8 space-y-6">

                <!-- TAB PANE 1: ĐƠN HÀNG CỦA TÔI -->
                <div id="pane-orders" class="tab-pane space-y-4">

                    <!-- Header card with filter pills -->
                    <div
                        class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                        <div>
                            <h2 class="text-base font-black text-slate-900 flex items-center space-x-2">
                                <span>📦</span>
                                <span>Danh Sách Đơn Hàng</span>
                            </h2>
                            <p class="text-xs text-slate-500 mt-0.5">Theo dõi chi tiết các đơn văn phòng phẩm và mực in đã
                                đặt mua</p>
                        </div>

                        <!-- Quick Status Filters -->
                        <div class="flex items-center space-x-1.5 text-[11px] font-bold">
                            <button type="button" onclick="filterOrders('all')"
                                class="order-filter-btn px-3 py-1.5 rounded-xl bg-indigo-600 text-white" data-filter="all">
                                Tất cả ({{ $orders->count() }})
                            </button>
                            <button type="button" onclick="filterOrders('paid')"
                                class="order-filter-btn px-3 py-1.5 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200"
                                data-filter="paid">
                                Đã thanh toán ({{ $orders->where('payment_status', 'paid')->count() }})
                            </button>
                            <button type="button" onclick="filterOrders('unpaid')"
                                class="order-filter-btn px-3 py-1.5 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200"
                                data-filter="unpaid">
                                Chờ thanh toán ({{ $orders->where('payment_status', '!=', 'paid')->count() }})
                            </button>
                        </div>
                    </div>

                    <!-- Orders List -->
                    <div class="space-y-4">
                        @forelse($orders as $order)
                            <div class="order-card bg-white p-5 sm:p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4 hover:border-indigo-300 transition"
                                data-payment="{{ $order->payment_status }}">

                                <!-- Order Card Header -->
                                <div
                                    class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 pb-3 text-xs">
                                    <div class="flex items-center space-x-2">
                                        <span
                                            class="font-mono font-black text-indigo-700 text-sm">#{{ $order->order_code }}</span>
                                        <span class="text-slate-300">•</span>
                                        <span class="text-slate-500">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                                    </div>

                                    <div class="flex items-center space-x-2">
                                        <span
                                            class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider {{ $order->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                            {{ $order->payment_status === 'paid' ? '✓ Đã Thanh Toán' : '⏳ Chờ Thanh Toán' }}
                                        </span>
                                        <span
                                            class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-800 border border-slate-200">
                                            {{ $order->status_label }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Order Items List -->
                                <div class="space-y-2.5 divide-y divide-slate-100 text-xs">
                                    @foreach($order->items as $item)
                                        <div class="pt-2 flex items-center justify-between gap-3">
                                            <div class="flex items-center space-x-3 min-w-0">
                                                <div
                                                    class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center p-1 shrink-0">
                                                    @if($item->product && $item->product->image_url)
                                                        <img src="{{ $item->product->image_url }}" alt="{{ $item->product_name }}"
                                                            class="max-h-full max-w-full object-contain" />
                                                    @else
                                                        <span>📦</span>
                                                    @endif
                                                </div>
                                                <div class="min-w-0">
                                                    <h4 class="font-bold text-slate-900 truncate">{{ $item->product_name }}</h4>
                                                    <p class="text-[11px] text-slate-500 font-mono">
                                                        {{ number_format($item->unit_price, 0, ',', '.') }}₫ x {{ $item->quantity }}
                                                        {{ $item->unit_name }}
                                                    </p>
                                                </div>
                                            </div>
                                            <span class="font-mono font-bold text-slate-900 shrink-0">
                                                {{ number_format($item->subtotal, 0, ',', '.') }}₫
                                            </span>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Order Card Footer -->
                                <div
                                    class="pt-3 border-t border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-xs">
                                    <div class="text-slate-500">
                                        <span>Phương thức:
                                            <b>{{ strtoupper($order->payment_method) === 'VIETQR' ? 'Chuyển khoản VietQR' : 'Tiền mặt khi nhận (COD)' }}</b></span>
                                    </div>

                                    <div class="flex items-center space-x-4 w-full sm:w-auto justify-between sm:justify-end">
                                        <div class="text-right">
                                            <span class="text-[11px] text-slate-400 block">Tổng thanh toán:</span>
                                            <span class="text-base sm:text-lg font-black font-mono text-rose-600">
                                                {{ number_format($order->grand_total, 0, ',', '.') }}₫
                                            </span>
                                        </div>

                                        <!-- Action: Buy Again / Reorder -->
                                        @php
                                            $reorderPayload = $order->items->map(function ($i) {
                                                return [
                                                    'id' => $i->product_id,
                                                    'name' => $i->product_name,
                                                    'unit' => $i->unit_name,
                                                    'price' => (float) $i->unit_price,
                                                    'qty' => $i->quantity,
                                                    'image' => $i->product?->image_url ?? '',
                                                ];
                                            })->values();
                                        @endphp
                                        <button type="button" data-reorder='@json($reorderPayload)'
                                            onclick="reorderItems(JSON.parse(this.dataset.reorder || '[]'))"
                                            class="px-3.5 py-2 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-xs transition flex items-center space-x-1 shadow-xs">
                                            <span>🛒</span>
                                            <span>Mua Lại</span>
                                        </button>
                                    </div>
                                </div>

                            </div>
                        @empty
                            <div class="bg-white p-12 text-center rounded-3xl border border-slate-200 shadow-sm space-y-3">
                                <span class="text-5xl block">🛒</span>
                                <h3 class="text-base font-bold text-slate-800">Bạn chưa có đơn hàng nào</h3>
                                <p class="text-xs text-slate-500 max-w-sm mx-auto">
                                    Khám phá ngay hơn 1.000 sản phẩm giấy in, bút viết và hộp mực máy in chính hãng tại kho.
                                </p>
                                <a href="{{ route('storefront.products') }}"
                                    class="inline-block mt-2 px-6 py-2.5 bg-[#059669] hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow transition">
                                    Khám phá sản phẩm ngay →
                                </a>
                            </div>
                        @endforelse
                    </div>

                </div>

                <!-- TAB PANE 2: PHIẾU SỬA CHỮA MÁY IN -->
                <div id="pane-repairs" class="tab-pane hidden space-y-4">

                    <div
                        class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm flex items-center justify-between">
                        <div>
                            <h2 class="text-base font-black text-slate-900 flex items-center space-x-2">
                                <span>🖨️</span>
                                <span>Tiến Độ Sửa Chữa & Nạp Mực</span>
                            </h2>
                            <p class="text-xs text-slate-500 mt-0.5">Theo dõi quy trình kiểm tra, thay linh kiện và sửa chữa
                                thiết bị của bạn</p>
                        </div>
                        <a href="{{ route('lookup.index') }}"
                            class="text-xs font-bold text-indigo-700 hover:underline hidden sm:inline">
                            🔍 Tra cứu bằng mã phiếu khác
                        </a>
                    </div>

                    <div class="space-y-4">
                        @forelse($repairTickets as $ticket)
                            <div
                                class="bg-white p-5 sm:p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4 hover:border-indigo-300 transition">

                                <!-- Ticket Header -->
                                <div
                                    class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 pb-3 text-xs">
                                    <div class="flex items-center space-x-2">
                                        <span
                                            class="font-mono font-black text-amber-700 text-sm">#{{ $ticket->ticket_code }}</span>
                                        <span class="text-slate-300">•</span>
                                        <span class="font-black text-slate-900 text-sm">{{ $ticket->device_name }}</span>
                                    </div>
                                    <span
                                        class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-100 text-amber-900">
                                        {{ $ticket->status_label }}
                                    </span>
                                </div>

                                <!-- Ticket Details -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                                    <div class="bg-slate-50 p-3 rounded-2xl border border-slate-100 space-y-1">
                                        <span class="text-slate-400 font-bold text-[10px] uppercase block">Hiện trạng lỗi
                                            báo:</span>
                                        <p class="text-slate-800 italic">"{{ $ticket->issue_description }}"</p>
                                    </div>
                                    <div class="bg-slate-50 p-3 rounded-2xl border border-slate-100 space-y-1">
                                        <span class="text-slate-400 font-bold text-[10px] uppercase block">Chẩn đoán kỹ
                                            thuật:</span>
                                        <p class="text-indigo-950 font-medium">
                                            {{ $ticket->technician_diagnosis ?: 'Đang tiến hành tháo máy kiểm tra…' }}</p>
                                    </div>
                                </div>

                                <!-- Cost summary & link -->
                                <div
                                    class="pt-2 border-t border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-xs">
                                    <div class="space-x-3 text-slate-500">
                                        <span>Ngày tiếp nhận: <b>{{ $ticket->created_at->format('d/m/Y') }}</b></span>
                                        @if($ticket->remaining_amount > 0)
                                            <span class="text-rose-600 font-bold">Còn cần trả:
                                                {{ number_format($ticket->remaining_amount, 0, ',', '.') }}₫</span>
                                        @else
                                            <span class="text-emerald-600 font-bold">✓ Đã thanh toán</span>
                                        @endif
                                    </div>

                                    <a href="{{ route('lookup.view', ['code' => $ticket->ticket_code, 'phone4' => $ticket->phone_last4]) }}"
                                        class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center space-x-1.5">
                                        <span>Xem Tiến Độ Trực Tuyến</span>
                                        <span>→</span>
                                    </a>
                                </div>

                            </div>
                        @empty
                            <div class="bg-white p-12 text-center rounded-3xl border border-slate-200 shadow-sm space-y-3">
                                <span class="text-5xl block">🖨️</span>
                                <h3 class="text-base font-bold text-slate-800">Chưa có phiếu sửa chữa nào</h3>
                                <p class="text-xs text-slate-500 max-w-sm mx-auto">
                                    Khi gửi máy in hoặc gọi nạp mực tại cửa hàng, phiếu bảo dưỡng sẽ tự động hiển thị ở đây.
                                </p>
                                <a href="{{ $storefrontSettings['zalo_url'] ?? 'https://zalo.me/0974194305' }}" target="_blank"
                                    class="inline-block mt-2 px-6 py-2.5 bg-[#0068ff] hover:bg-blue-600 text-white font-bold text-xs rounded-xl shadow transition">
                                    Gọi thợ nạp mực tận nơi qua Zalo →
                                </a>
                            </div>
                        @endforelse
                    </div>

                </div>

                <!-- TAB PANE 3: HỒ SƠ THÔNG TIN CÁ NHÂN -->
                <div id="pane-profile" class="tab-pane hidden space-y-4">

                    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
                        <div class="border-b border-slate-100 pb-4">
                            <h2 class="text-base font-black text-slate-900 flex items-center space-x-2">
                                <span>👤</span>
                                <span>Hồ Sơ & Sổ Địa Chỉ Nhận Hàng</span>
                            </h2>
                            <p class="text-xs text-slate-500 mt-0.5">Cập nhật thông tin để hệ thống tự động điền khi bạn đặt
                                hàng</p>
                        </div>

                        <!-- Flash status message -->
                        @if(session('success') && session('active_tab') === 'profile')
                            <div
                                class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center space-x-2">
                                <span>✓</span>
                                <span>{{ session('success') }}</span>
                            </div>
                        @endif

                        <form action="{{ route('customer.update-profile') }}" method="POST" class="space-y-4">
                            @csrf

                            <!-- Họ và tên -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">
                                    Họ và tên khách hàng <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="name" required value="{{ old('name', $customer->name) }}"
                                    class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-600 focus:outline-none font-medium transition" />
                            </div>

                            <!-- Số điện thoại (Chỉ đọc để bảo vệ tài khoản) -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">
                                    Số điện thoại đăng nhập (Tài khoản)
                                </label>
                                <div class="relative">
                                    <input type="text" readonly disabled value="{{ $customer->phone }}"
                                        class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-100 font-mono font-bold text-slate-500 cursor-not-allowed" />
                                    <span
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-xs text-emerald-600 font-bold">
                                        ✓ Đã kích hoạt
                                    </span>
                                </div>
                                <span class="text-[10px] text-slate-400 mt-1 block">Để đổi số điện thoại đăng nhập, vui lòng
                                    liên hệ trực tiếp hotline cửa hàng.</span>
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">
                                    Địa chỉ Email nhận thông báo / hóa đơn
                                </label>
                                <input type="email" name="email" value="{{ old('email', $customer->email) }}"
                                    placeholder="VD: khachhang@gmail.com"
                                    class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-600 focus:outline-none font-medium transition" />
                            </div>

                            <!-- Địa chỉ nhận hàng mặc định -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">
                                    Địa chỉ giao hàng mặc định (Tự điền khi mua hàng)
                                </label>
                                <textarea name="address" rows="3"
                                    placeholder="VD: Số 30 Đường Bình Hòa, TP. Biên Hòa, Đồng Nai"
                                    class="w-full text-xs sm:text-sm p-3.5 rounded-xl border border-slate-300 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-600 focus:outline-none font-medium transition">{{ old('address', $customer->address) }}</textarea>
                            </div>

                            <div class="pt-2">
                                <button type="submit"
                                    class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs sm:text-sm rounded-xl shadow-md transition transform active:scale-98">
                                    LƯU THAY ĐỔI HỒ SƠ
                                </button>
                            </div>
                        </form>
                    </div>

                </div>

                <!-- TAB PANE 4: ĐỔI MẬT KHẨU BẢO MẬT -->
                <div id="pane-password" class="tab-pane hidden space-y-4">

                    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
                        <div class="border-b border-slate-100 pb-4">
                            <h2 class="text-base font-black text-slate-900 flex items-center space-x-2">
                                <span>🔒</span>
                                <span>Đổi Mật Khẩu Bảo Mật</span>
                            </h2>
                            <p class="text-xs text-slate-500 mt-0.5">Đặt mật khẩu mạnh để bảo vệ tài khoản và lịch sử giao
                                dịch của bạn</p>
                        </div>

                        <!-- Flash status message -->
                        @if(session('success') && session('active_tab') === 'password')
                            <div
                                class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center space-x-2">
                                <span>✓</span>
                                <span>{{ session('success') }}</span>
                            </div>
                        @endif

                        @if(session('error') && session('active_tab') === 'password')
                            <div
                                class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold flex items-center space-x-2">
                                <span>⚠️</span>
                                <span>{{ session('error') }}</span>
                            </div>
                        @endif

                        <form action="{{ route('customer.change-password') }}" method="POST" class="space-y-4 max-w-md">
                            @csrf

                            <!-- Mật khẩu hiện tại -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">
                                    Mật khẩu hiện tại <span class="text-rose-500">*</span>
                                </label>
                                <input type="password" name="current_password" required
                                    placeholder="Nhập mật khẩu đang dùng..."
                                    class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-600 focus:outline-none font-medium transition" />
                            </div>

                            <!-- Mật khẩu mới -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">
                                    Mật khẩu mới <span class="text-rose-500">*</span>
                                </label>
                                <input type="password" name="new_password" required minlength="6"
                                    placeholder="Tối thiểu 6 ký tự..."
                                    class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-600 focus:outline-none font-medium transition" />
                            </div>

                            <!-- Xác nhận mật khẩu mới -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">
                                    Xác nhận lại mật khẩu mới <span class="text-rose-500">*</span>
                                </label>
                                <input type="password" name="new_password_confirmation" required minlength="6"
                                    placeholder="Nhập lại mật khẩu mới..."
                                    class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-xl border border-slate-300 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-600 focus:outline-none font-medium transition" />
                            </div>

                            <div class="pt-2">
                                <button type="submit"
                                    class="px-6 py-3 bg-gradient-to-r from-blue-700 to-indigo-700 hover:from-blue-800 hover:to-indigo-800 text-white font-black text-xs sm:text-sm rounded-xl shadow-md transition transform active:scale-98">
                                    CẬP NHẬT MẬT KHẨU MỚI
                                </button>
                            </div>
                        </form>
                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- JAVASCRIPT TAB SWITCHER VÀ XỬ LÝ LỌC / MUA LẠI ĐƠN HÀNG -->
    <script>
        function switchTab(tabId) {
            // Ẩn tất cả panes
            const panes = document.querySelectorAll('.tab-pane');
            panes.forEach(p => p.classList.add('hidden'));

            // Hiện pane tương ứng
            const activePane = document.getElementById('pane-' + tabId);
            if (activePane) activePane.classList.remove('hidden');

            // Cập nhật style Desktop sidebar
            const desktopBtns = document.querySelectorAll('.lg\\:block .tab-btn');
            desktopBtns.forEach(b => {
                b.classList.remove('bg-indigo-50', 'text-indigo-700');
                b.classList.add('text-slate-600', 'hover:bg-slate-50');
            });
            const activeDesktopBtn = document.getElementById('d-tab-' + tabId);
            if (activeDesktopBtn) {
                activeDesktopBtn.classList.remove('text-slate-600', 'hover:bg-slate-50');
                activeDesktopBtn.classList.add('bg-indigo-50', 'text-indigo-700');
            }

            // Cập nhật style Mobile buttons
            const mobileBtns = document.querySelectorAll('.lg\\:hidden .tab-btn');
            mobileBtns.forEach(b => {
                b.classList.remove('bg-indigo-600', 'text-white', 'shadow-sm');
                b.classList.add('bg-white', 'text-slate-700', 'border', 'border-slate-200');
            });
            const activeMobileBtn = document.getElementById('m-tab-' + tabId);
            if (activeMobileBtn) {
                activeMobileBtn.classList.remove('bg-white', 'text-slate-700', 'border', 'border-slate-200');
                activeMobileBtn.classList.add('bg-indigo-600', 'text-white', 'shadow-sm');
            }

            // Cập nhật URL hash
            if (history.replaceState) {
                history.replaceState(null, null, '#' + tabId);
            }
        }

        function filterOrders(status) {
            const cards = document.querySelectorAll('.order-card');
            const filterBtns = document.querySelectorAll('.order-filter-btn');

            filterBtns.forEach(b => {
                if (b.dataset.filter === status) {
                    b.className = 'order-filter-btn px-3 py-1.5 rounded-xl bg-indigo-600 text-white';
                } else {
                    b.className = 'order-filter-btn px-3 py-1.5 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200';
                }
            });

            cards.forEach(card => {
                const payment = card.dataset.payment;
                if (status === 'all') {
                    card.style.display = 'block';
                } else if (status === 'paid') {
                    card.style.display = (payment === 'paid') ? 'block' : 'none';
                } else if (status === 'unpaid') {
                    card.style.display = (payment !== 'paid') ? 'block' : 'none';
                }
            });
        }

        function reorderItems(items) {
            if (!items || !items.length) return;
            let count = 0;
            items.forEach(item => {
                if (item.id && typeof addToCart === 'function') {
                    addToCart(item.id, null, item.name, item.unit || 'Cái', item.price, item.image || '');
                    count++;
                }
            });
            if (typeof showToast === 'function') {
                showToast(`Đã thêm ${count} món từ đơn cũ vào giỏ hàng!`, 'success');
            }
            setTimeout(() => {
                window.location.href = "{{ route('storefront.cart') }}";
            }, 800);
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Khôi phục tab từ URL hash hoặc session
            const hash = window.location.hash.replace('#', '');
            const serverActiveTab = @json(session('active_tab'));

            if (serverActiveTab) {
                switchTab(serverActiveTab);
            } else if (hash && ['orders', 'repairs', 'profile', 'password'].includes(hash)) {
                switchTab(hash);
            } else {
                switchTab('orders');
            }
        });
    </script>
@endsection