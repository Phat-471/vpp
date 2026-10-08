@extends('layouts.pos')

@section('content')
<main id="pos-main" class="pos-login">
    <section class="pos-login-card" aria-labelledby="login-title">
        <div class="pos-login-mark"><x-heroicon-o-shopping-bag /></div>
        <p class="pos-eyebrow">QUẦY BÁN HÀNG</p>
        <h1 id="login-title">Bắt đầu phiên bán hàng</h1>
        <p class="pos-muted">Đăng nhập bằng tài khoản quản trị hoặc thu ngân của cửa hàng.</p>
        @if($errors->any())<div class="pos-alert" role="alert">{{ $errors->first() }}</div>@endif
        <form action="{{ route('pos.authenticate') }}" method="POST" class="pos-login-form">
            @csrf
            <label>Email hoặc số điện thoại<input type="text" name="identifier" value="{{ old('identifier') }}" autocomplete="username" placeholder="Email hoặc số điện thoại nhân viên" required autofocus maxlength="255"></label>
            <label>Mật khẩu<input type="password" name="password" autocomplete="current-password" required maxlength="255"></label>
            <button class="pos-button pos-primary" type="submit">Đăng nhập quầy bán hàng <x-heroicon-o-arrow-right /></button>
        </form>
        <a class="pos-back-link" href="{{ route('storefront.index') }}">← Về trang web</a>
    </section>
</main>
@endsection
