<div class="text-center p-4">
    <div class="mb-3">
        <span class="text-xs uppercase tracking-wider font-semibold text-gray-500">Mã phiếu tiếp nhận</span>
        <h3 class="text-xl font-bold text-indigo-600 dark:text-indigo-400">{{ $ticket->ticket_code }}</h3>
        <p class="text-sm text-gray-600 dark:text-gray-300">{{ $ticket->customer_name }} - {{ $ticket->device_name }}</p>
    </div>

    <div class="inline-block bg-white p-3 rounded-xl border border-gray-200 shadow-md my-2">
        <img src="{{ $qrUrl }}" alt="VietQR" class="w-64 h-64 object-contain mx-auto" />
    </div>

    <div class="mt-4 p-3 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 rounded-lg">
        <div class="text-sm text-emerald-800 dark:text-emerald-300">
            Số tiền cần thanh toán: <span class="text-lg font-bold">{{ number_format($amount, 0, ',', '.') }} ₫</span>
        </div>
        <div class="text-xs text-emerald-700 dark:text-emerald-400 mt-1">
            Nội dung chuyển khoản chuẩn: <code class="font-mono font-bold bg-white dark:bg-gray-800 px-2 py-0.5 rounded">{{ $ticket->ticket_code }}</code>
        </div>
    </div>

    <p class="text-xs text-gray-500 mt-3 italic">
        * Webhook SePay/Casso sẽ tự động nhận diện và cập nhật trạng thái phiếu sau 3-5 giây khi tiền vào tài khoản.
    </p>
</div>
