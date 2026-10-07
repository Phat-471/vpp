<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác Thực Bảo Mật - Phiếu {{ $ticket->ticket_code }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-sm w-full bg-white p-6 rounded-2xl shadow-md border border-slate-200 text-center">
        <div class="w-14 h-14 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto mb-3">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
        </div>

        <h1 class="text-lg font-extrabold text-slate-900">XÁC THỰC BẢO MẬT</h1>
        <p class="text-xs text-slate-500 mt-1 mb-4">
            Để bảo vệ thông tin khách hàng, vui lòng nhập <b class="text-slate-800">4 số cuối số điện thoại</b> đã đăng ký cho phiếu <span class="font-mono font-bold text-indigo-600">{{ $ticket->ticket_code }}</span>.
        </p>

        @if($errors->any())
        <div class="mb-4 p-2.5 bg-red-50 text-red-700 text-xs rounded-lg border border-red-200">
            {{ $errors->first() }}
        </div>
        @endif

        <form action="{{ route('lookup.search') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="ticket_code" value="{{ $ticket->ticket_code }}" />

            <div>
                <input type="text" name="phone_last4" maxlength="4" autofocus required
                    placeholder="VD: 5678" 
                    class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 font-mono text-2xl font-bold tracking-widest text-center" />
            </div>

            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 rounded-xl shadow">
                XÁC NHẬN ĐỂ XEM TIẾN ĐỘ
            </button>
        </form>

        <a href="{{ route('lookup.index') }}" class="block mt-4 text-xs text-slate-400 hover:text-indigo-600">
            ← Quay lại trang tìm kiếm
        </a>
    </div>

</body>
</html>
