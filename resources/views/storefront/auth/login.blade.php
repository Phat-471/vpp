@extends('layouts.storefront')

@section('title', 'Đăng Nhập Khách Hàng | VPP')
@section('meta_description', 'Đăng nhập để theo dõi đơn hàng và tra cứu lịch sử sửa máy in của bạn tại Cửa hàng VPP & Dịch Vụ Máy In.')

@section('content')
<div class="max-w-md mx-auto px-4 py-8 sm:py-16 space-y-6">

    <div class="text-center space-y-2">
        <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-indigo-700 to-amber-500 text-white flex items-center justify-center font-black text-xl mx-auto shadow-sm">
            VP
        </div>
        <h1 class="text-xl sm:text-2xl font-black text-slate-900">Đăng Nhập Khách Hàng</h1>
        <p class="text-xs text-slate-500">
            Nhập số điện thoại và mật khẩu để quản lý tài khoản của bạn.
        </p>
    </div>

    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-xs space-y-5">
        
        <form action="{{ route('customer.post-login') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Số điện thoại đăng ký *</label>
                <input type="tel" name="phone" required value="{{ old('phone') }}" placeholder="VD: 0901 234 567" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 bg-slate-50 font-medium" />
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Mật khẩu *</label>
                <input type="password" name="password" required placeholder="Nhập mật khẩu..." class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 bg-slate-50 font-medium" />
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center space-x-2 text-slate-600 cursor-pointer">
                    <input type="checkbox" name="remember" class="w-4 h-4 accent-indigo-600 rounded" />
                    <span>Ghi nhớ đăng nhập</span>
                </label>
                <a href="{{ route('lookup.index') }}" class="text-indigo-600 hover:underline">
                    Quên mật khẩu?
                </a>
            </div>

            <button type="submit" class="w-full py-3.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs rounded-xl shadow-xs transition">
                ĐĂNG NHẬP
            </button>
        </form>

        <div class="pt-2 border-t border-slate-100 text-center text-xs text-slate-500">
            <span>Chưa có tài khoản?</span>
            <a href="{{ route('customer.register') }}" class="font-bold text-indigo-600 hover:underline ml-1">Đăng ký tài khoản mới</a>
        </div>

    </div>

</div>
@endsection
