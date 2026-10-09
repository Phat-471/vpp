@extends('layouts.storefront')

@section('title', 'Tài Khoản Của Tôi | VPP & Dịch Vụ Máy In')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10 space-y-8">

    <!-- Top Account Welcome Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white rounded-3xl p-6 sm:p-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border border-indigo-900 shadow-sm">
        <div class="flex items-center space-x-4">
            <div class="w-14 h-14 rounded-2xl bg-indigo-700 border-2 border-indigo-400 flex items-center justify-center text-2xl font-black text-white shadow-sm">
                👤
            </div>
            <div>
                <span class="text-[10px] text-amber-400 font-bold uppercase tracking-wider block">KHÁCH HÀNG THÂN THIẾT</span>
                <h1 class="text-xl sm:text-2xl font-black text-white flex items-center gap-2">
                    <span>{{ $customer->name }}</span>
                    @if($customer->phone_verified_at)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40" title="Tài khoản chính thức đã kích hoạt">
                            <svg class="w-3 h-3 mr-1 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            Tài khoản chính thức
                        </span>
                    @endif
                </h1>
                <p class="text-xs text-slate-300 font-mono">SĐT: {{ $customer->phone }} @if($customer->email)• Email: {{ $customer->email }}@endif</p>
            </div>
        </div>

        <form action="{{ route('customer.logout') }}" method="POST">
            @csrf
            <button type="submit" class="px-4 py-2 bg-white/10 hover:bg-rose-600 text-white font-bold text-xs rounded-xl border border-white/20 transition flex items-center space-x-1.5">
                <span>🚪</span>
                <span>Đăng Xuất</span>
            </button>
        </form>
    </div>

    <!-- Account Details & Activities Grid (3 Tabs / Sections) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        <!-- Left: Update Profile Form (lg:col-span-4) -->
        <div class="lg:col-span-4 bg-white p-6 rounded-3xl border border-slate-200 shadow-xs space-y-5">
            <h2 class="font-black text-slate-900 text-base border-b border-slate-100 pb-3 flex items-center space-x-2">
                <span>⚙️</span>
                <span>Thông Tin Cá Nhân</span>
            </h2>

            <form action="{{ route('customer.update-profile') }}" method="POST" class="space-y-4 text-xs">
                @csrf

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Họ và tên *</label>
                    <input type="text" name="name" required value="{{ old('name', $customer->name) }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-slate-50 font-medium" />
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Số điện thoại *</label>
                    <input type="tel" name="phone" required value="{{ old('phone', $customer->phone) }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-slate-50 font-medium" />
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $customer->email) }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-slate-50 font-medium" />
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Địa chỉ nhận hàng mặc định</label>
                    <textarea name="address" rows="2" class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-slate-50 font-medium">{{ old('address', $customer->address) }}</textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Đổi mật khẩu mới (bỏ trống nếu không đổi)</label>
                    <input type="password" name="password" minlength="6" placeholder="Mật khẩu mới..." class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-slate-50 font-medium" />
                </div>

                <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-xs transition">
                    Cập Nhật Thông Tin
                </button>
            </form>
        </div>

        <!-- Right: Orders History & Repair Tickets (lg:col-span-8) -->
        <div class="lg:col-span-8 space-y-6">

            <!-- Section 1: Lịch Sử Đơn Hàng -->
            <div class="bg-white p-6 sm:p-7 rounded-3xl border border-slate-200 shadow-xs space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-black text-slate-900 text-base flex items-center space-x-2">
                        <span>📦</span>
                        <span>Lịch Sử Đơn Hàng Của Tôi</span>
                    </h3>
                    <span class="text-xs text-slate-500 font-semibold">{{ $orders->count() }} đơn</span>
                </div>

                <div class="space-y-3">
                    @forelse($orders as $order)
                    <div class="p-4 rounded-2xl border border-slate-200 hover:border-indigo-300 bg-slate-50/50 transition space-y-3">
                        <div class="flex flex-wrap items-center justify-between gap-2 text-xs">
                            <div class="flex items-center space-x-2">
                                <span class="font-mono font-black text-indigo-700 text-sm">#{{ $order->order_code }}</span>
                                <span class="text-slate-400">•</span>
                                <span class="text-slate-500">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                            </div>

                            <div class="flex items-center space-x-2">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $order->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $order->payment_status === 'paid' ? 'Đã Thanh Toán' : 'Chưa Thanh Toán' }}
                                </span>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-200 text-slate-800">
                                    {{ $order->status_label }}
                                </span>
                            </div>
                        </div>

                        <!-- Items list -->
                        <div class="text-xs divide-y divide-slate-100 pt-1">
                            @foreach($order->items as $item)
                            <div class="py-1 flex justify-between text-slate-700">
                                <span>{{ $item->product_name }} ({{ $item->unit_name }} x{{ $item->quantity }})</span>
                                <span class="font-mono font-bold">{{ number_format($item->subtotal, 0, ',', '.') }}₫</span>
                            </div>
                            @endforeach
                        </div>

                        <div class="pt-2 border-t border-slate-200/80 flex items-center justify-between text-xs">
                            <span class="text-slate-500">PTTT: <b>{{ strtoupper($order->payment_method) }}</b></span>
                            <div class="text-right">
                                <span class="text-slate-500 text-[11px]">Tổng đơn: </span>
                                <span class="font-mono font-black text-sm text-indigo-700">{{ number_format($order->grand_total, 0, ',', '.') }}₫</span>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="py-8 text-center text-slate-400 text-xs">
                        <span>Chưa có đơn hàng nào được đặt.</span>
                        <a href="{{ route('storefront.products') }}" class="text-indigo-600 font-bold hover:underline block mt-1">Khám phá sản phẩm ngay →</a>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Section 2: Lịch Sử Phiếu Sửa Chữa Máy In -->
            <div class="bg-white p-6 sm:p-7 rounded-3xl border border-slate-200 shadow-xs space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-black text-slate-900 text-base flex items-center space-x-2">
                        <span>🖨️</span>
                        <span>Phiếu Sửa Chữa Máy In Của Tôi</span>
                    </h3>
                    <span class="text-xs text-slate-500 font-semibold">{{ $repairTickets->count() }} phiếu</span>
                </div>

                <div class="space-y-3">
                    @forelse($repairTickets as $ticket)
                    <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50 transition space-y-2.5">
                        <div class="flex items-center justify-between text-xs">
                            <div class="flex items-center space-x-2">
                                <span class="font-mono font-black text-amber-700 text-sm">#{{ $ticket->ticket_code }}</span>
                                <span class="text-slate-400">•</span>
                                <span class="font-bold text-slate-900">{{ $ticket->device_name }}</span>
                            </div>
                            <a href="{{ route('lookup.view', ['code' => $ticket->ticket_code, 'phone4' => $ticket->phone_last4]) }}" class="px-2.5 py-1 bg-indigo-50 hover:bg-indigo-600 hover:text-white text-indigo-700 rounded-lg font-bold text-[11px] transition">
                                Xem Tiến Độ →
                            </a>
                        </div>

                        <p class="text-xs text-slate-600 leading-relaxed">
                            Hiện trạng: <i>{{ $ticket->issue_description }}</i>
                        </p>

                        <div class="flex items-center justify-between text-xs pt-2 border-t border-slate-200/80">
                            <span class="text-slate-500">Ngày gửi: {{ $ticket->created_at->format('d/m/Y H:i') }}</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                {{ $ticket->status_label }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="py-8 text-center text-slate-400 text-xs">
                        <span>Chưa có phiếu sửa chữa nào.</span>
                        <a href="{{ $storefrontSettings['zalo_url'] }}" target="_blank" class="text-indigo-600 font-bold hover:underline block mt-1">Liên hệ thợ máy in qua Zalo →</a>
                    </div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
