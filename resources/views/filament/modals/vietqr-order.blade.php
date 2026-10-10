<div class="text-center p-4">
    <div class="mb-3">
        <span class="text-xs uppercase tracking-wider font-semibold text-gray-500">Hóa đơn bán lẻ</span>
        <h3 class="text-xl font-bold text-indigo-600 dark:text-indigo-400">{{ $order->order_code }}</h3>
        <p class="text-sm text-gray-600 dark:text-gray-300">{{ $order->customer_name ?: 'Khách mua tại quầy' }}</p>
    </div>

    <div class="inline-block bg-white p-3 rounded-xl border border-gray-200 shadow-md my-2">
        <img src="{{ $qrUrl }}" alt="VietQR" class="w-64 h-64 object-contain mx-auto" />
    </div>

    <div class="mt-4 p-3 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 rounded-lg">
        <div class="text-sm text-emerald-800 dark:text-emerald-300">
            Số tiền cần thanh toán: <span class="text-lg font-bold">{{ number_format($amount, 0, ',', '.') }} ₫</span>
        </div>
        <div class="text-xs text-emerald-700 dark:text-emerald-400 mt-1">
            Nội dung chuyển khoản: <code class="font-mono font-bold bg-white dark:bg-gray-800 px-2 py-0.5 rounded">{{ $order->order_code }}</code>
        </div>
    </div>

    <p class="text-xs text-gray-500 mt-3 italic">
        Hệ thống sẽ tự động cập nhật trạng thái đơn hàng sau khi nhận được tiền chuyển khoản.
    </p>
</div>
