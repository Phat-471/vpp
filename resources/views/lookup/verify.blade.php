@extends('layouts.storefront')

@section('title', 'Xác Thực Bảo Mật Tra Cứu Phiếu ' . $ticket->ticket_code . ' | ' . ($storefrontSettings['site_name'] ?? 'VPP & Dịch Vụ Máy In'))
@section('meta_description', 'Xác thực bảo mật chống lộ thông tin cá nhân khách hàng khi tra cứu phiếu sửa chữa.')

@section('content')
<div class="max-w-md mx-auto px-4 py-10 sm:py-16 space-y-6">

    <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-slate-200 text-center space-y-4">
        <div class="w-14 h-14 bg-amber-100 text-amber-700 rounded-2xl flex items-center justify-center mx-auto text-2xl font-black shadow-sm">
            🔐
        </div>

        <div>
            <h1 class="text-lg sm:text-xl font-black text-slate-900">Xác Thực Bảo Mật Phiếu Tiếp Nhận</h1>
            <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                Để bảo vệ quyền riêng tư và thông tin thiết bị, vui lòng nhập <b class="text-slate-800">4 số cuối của số điện thoại</b> đã đăng ký cho phiếu <span class="font-mono font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-lg">{{ $ticket->ticket_code }}</span>.
            </p>
        </div>

        @if($errors->any())
        <div class="p-3 bg-rose-50 text-rose-700 text-xs rounded-xl border border-rose-200 font-bold">
            {{ $errors->first() }}
        </div>
        @endif

        <form action="{{ route('lookup.search') }}" method="POST" class="space-y-4 pt-1">
            @csrf
            <input type="hidden" name="ticket_code" value="{{ $ticket->ticket_code }}" />

            <div>
                <input type="text" name="phone_last4" maxlength="4" autofocus required
                    placeholder="VD: 5678" 
                    class="w-full px-4 py-3 rounded-2xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 bg-slate-50 font-mono text-2xl font-black tracking-widest text-center focus:bg-white transition" />
            </div>

            <button type="submit" class="w-full bg-[#1e3a8a] hover:bg-blue-900 text-white font-extrabold text-xs sm:text-sm py-3.5 rounded-xl shadow-md transition transform active:scale-98">
                XÁC NHẬN ĐỂ XEM TIẾN ĐỘ NGAY →
            </button>
        </form>

        <div class="pt-2">
            <a href="{{ route('lookup.index') }}" class="text-xs text-slate-400 hover:text-indigo-600 font-semibold transition">
                ← Quay lại trang tìm kiếm phiếu
            </a>
        </div>
    </div>

</div>
@endsection
