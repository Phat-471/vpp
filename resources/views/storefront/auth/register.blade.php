@extends('layouts.storefront')

@section('title', 'Đăng Ký Tài Khoản Khách Hàng | VPP')
@section('meta_description', 'Tạo tài khoản khách hàng để lưu địa chỉ nhận hàng, theo dõi lịch sử đơn hàng và tiến độ sửa máy in.')

@section('content')
<div class="max-w-md mx-auto px-4 py-8 sm:py-14 space-y-6">

    <div class="text-center space-y-2">
        <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-indigo-700 to-amber-500 text-white flex items-center justify-center font-black text-xl mx-auto shadow-sm">
            VP
        </div>
        <h1 class="text-xl sm:text-2xl font-black text-slate-900">Đăng Ký Tài Khoản Mới</h1>
        <p class="text-xs text-slate-500">
            Quản lý đơn hàng, lưu địa chỉ giao hàng và tra cứu lịch sử sửa máy in dễ dàng.
        </p>
    </div>

    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-xs space-y-5">
        
        <form action="{{ route('customer.post-register') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Họ và tên của bạn *</label>
                <input type="text" name="name" required value="{{ old('name') }}" placeholder="VD: Nguyễn Văn An" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 bg-slate-50 font-medium" />
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Số điện thoại đăng nhập *</label>
                <input type="tel" name="phone" required value="{{ old('phone') }}" placeholder="VD: 0901 234 567" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 bg-slate-50 font-medium" />
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Email (tùy chọn)</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="VD: vanan@gmail.com" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 bg-slate-50 font-medium" />
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Địa chỉ giao hàng mặc định (tùy chọn)</label>
                <input type="text" name="address" value="{{ old('address') }}" placeholder="VD: 45 Lê Duẩn, Q.1, TP.HCM" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 bg-slate-50 font-medium" />
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Mật khẩu *</label>
                <input type="password" name="password" required minlength="6" placeholder="Tối thiểu 6 ký tự..." class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 bg-slate-50 font-medium" />
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Xác nhận mật khẩu *</label>
                <input type="password" name="password_confirmation" required minlength="6" placeholder="Nhập lại mật khẩu..." class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 bg-slate-50 font-medium" />
            </div>

            <button type="submit" class="w-full py-3.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs rounded-xl shadow-xs transition">
                TẠO TÀI KHOẢN NGAY
            </button>
        </form>

        <div class="pt-2 border-t border-slate-100 text-center text-xs text-slate-500">
            <span>Đã có tài khoản?</span>
            <a href="{{ route('customer.login') }}" class="font-bold text-indigo-600 hover:underline ml-1">Đăng nhập tại đây</a>
        </div>

    </div>

</div>
@endsection
