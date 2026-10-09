<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>Quầy bán hàng · {{ $store['site_name'] }}</title>
    <link rel="stylesheet" href="{{ asset('css/pos.css') }}?v={{ filemtime(public_path('css/pos.css')) }}">
    @livewireStyles
</head>
<body class="pos-body">
    <a class="pos-skip" href="#pos-main">Đến quầy bán hàng</a>
    <header class="pos-header">
        <a class="pos-brand" href="{{ route('pos.index') }}" title="{{ $store['site_name'] }}">
            <span class="pos-brand-mark" aria-hidden="true">V</span>
            <span>{{ $store['site_name'] }}</span>
        </a>
        @auth('web')
            <nav class="pos-nav" aria-label="Điều hướng quầy bán hàng">
                <a class="{{ request()->routeIs('pos.index') ? 'is-active' : '' }}" href="{{ route('pos.index') }}"><x-heroicon-o-shopping-bag /> Bán hàng</a>
                <a class="{{ request()->routeIs('pos.customers') ? 'is-active' : '' }}" href="{{ route('pos.customers') }}"><x-heroicon-o-users /> Khách hàng</a>
                <a class="{{ request()->routeIs('pos.orders') ? 'is-active' : '' }}" href="{{ route('pos.orders') }}"><x-heroicon-o-document-text /> Đơn hàng</a>
                <a class="{{ request()->routeIs('pos.shifts') ? 'is-active' : '' }}" href="{{ route('pos.shifts') }}"><x-heroicon-o-clock /> Ca bán hàng</a>
                <a href="{{ route('storefront.index') }}" target="_blank" rel="noopener"><x-heroicon-o-globe-alt /> Trang web</a>
                @if(auth('web')->user()->isAdmin())
                    <a href="{{ url('/admin') }}" target="_blank" rel="noopener"><x-heroicon-o-squares-2x2 /> Quản trị</a>
                @endif
            </nav>
            <div class="pos-staff">
                <span><x-heroicon-o-user-circle /> {{ auth('web')->user()->name }}</span>
                <button type="button" data-pos-fullscreen class="pos-icon-button" aria-label="Bật hoặc tắt toàn màn hình" title="Toàn màn hình"><x-heroicon-o-arrows-pointing-out /></button>
                <form method="POST" action="{{ route('pos.logout') }}">@csrf<button class="pos-icon-button" type="submit" aria-label="Đăng xuất" title="Đăng xuất"><x-heroicon-o-arrow-right-on-rectangle /></button></form>
            </div>
        @endauth
    </header>
    {{ $slot ?? '' }}
    @yield('content')
    @livewireScripts
    <script src="{{ asset('js/pos.js') }}?v={{ filemtime(public_path('js/pos.js')) }}" defer></script>
</body>
</html>
