<main id="pos-main" class="pos-customers pos-orders">
    <header class="pos-customers-head">
        <div><p class="pos-eyebrow">LỊCH SỬ BÁN TẠI QUẦY</p><h1>Đơn hàng POS</h1><p class="pos-muted">{{ auth('web')->user()->isAdmin() ? 'Tất cả đơn POS của cửa hàng' : 'Các đơn POS do tài khoản này tạo' }}</p></div>
        <a class="pos-button pos-primary" href="{{ route('pos.index') }}">+ Bán hàng mới</a>
    </header>
    <form class="pos-order-filters" wire:submit="applyFilters">
        <label>Tìm đơn hàng<input wire:model="search" type="search" maxlength="100" placeholder="Mã đơn, tên khách hoặc SĐT"></label>
        <label>Từ ngày<input wire:model="from" type="date" max="9999-12-31"></label>
        <label>Đến ngày<input wire:model="to" type="date" min="{{ $from }}" max="9999-12-31"></label>
        <label>Thanh toán<select wire:model="payment"><option value="">Tất cả trạng thái</option>@foreach($paymentLabels as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach</select></label>
        <label>Ca bán hàng<select wire:model="shift"><option value="">Tất cả đơn</option><option value="in_shift">Trong ca</option><option value="outside">Ngoài ca</option><option value="legacy">Đơn cũ chưa gắn ca</option></select></label>
        <div class="pos-customer-actions"><button class="pos-button pos-primary" type="submit" wire:loading.attr="disabled">Lọc đơn</button><button class="pos-button" type="button" wire:click="clearFilters">Xóa bộ lọc</button></div>
    </form>
    @if($errors->any())<p class="pos-alert" role="alert">{{ $errors->first() }}</p>@endif
    <p class="pos-muted" role="status" wire:loading wire:target="applyFilters,clearFilters,showOrder,nextPage,previousPage">Đang tải dữ liệu…</p>
    <div class="pos-customer-table-wrap">
        <table class="pos-customer-table"><thead><tr><th>Mã đơn / Thời gian</th><th>Khách hàng</th><th>Thanh toán</th><th>Tổng tiền</th><th>Thu ngân</th><th>Thao tác</th></tr></thead><tbody>
        @forelse($orders as $order)
            <tr wire:key="order-{{ $order->uuid }}">
                <td><strong>{{ $order->order_code }}</strong><small>{{ $order->created_at->format('d/m/Y H:i') }}</small><small>{{ $order->pos_shift_id ? 'Trong ca' : ($order->pos_outside_shift ? 'Ngoài ca' : 'Đơn cũ chưa gắn ca') }}</small></td>
                <td>{{ $order->customer_name ?: 'Khách lẻ' }}<small>{{ $order->customer_phone }}</small></td>
                <td><span class="pos-order-badge {{ $order->payment_status === 'paid' ? 'is-paid' : '' }}">{{ $paymentLabels[$order->payment_status] ?? 'Chưa xác định' }}</span><small>{{ $methodLabels[$order->payment_method] ?? 'Khác' }}</small></td>
                <td class="pos-order-money">{{ \App\Helpers\AppHelper::formatMoney($order->grand_total) }}</td>
                <td>{{ $order->creator?->name ?? 'Không còn tài khoản' }}</td>
                <td><button class="pos-button" type="button" wire:click="showOrder('{{ $order->uuid }}')" wire:loading.attr="disabled">Chi tiết</button></td>
            </tr>
        @empty<tr><td colspan="6"><div class="pos-products-empty"><x-heroicon-o-document-text /><h2>Chưa có đơn hàng phù hợp</h2><p>Thử đổi bộ lọc hoặc bắt đầu bán hàng tại quầy.</p><button class="pos-button" type="button" wire:click="clearFilters">Xóa bộ lọc</button></div></td></tr>@endforelse
        </tbody></table>
    </div>
    <div class="pos-pagination"><span>{{ $orders->total() }} đơn · Trang {{ $orders->currentPage() }} / {{ max(1, $orders->lastPage()) }}</span><div><button class="pos-button" type="button" wire:click="previousPage" @disabled($orders->onFirstPage())>Trước</button><button class="pos-button" type="button" wire:click="nextPage" @disabled(!$orders->hasMorePages())>Sau</button></div></div>
    @if($selectedOrder)
        <div class="pos-modal-backdrop" x-data x-on:keydown.escape.window="$wire.closeOrder()">
            <section class="pos-customers-dialog pos-order-detail" role="dialog" aria-modal="true" aria-labelledby="order-detail-title" x-trap.inert.noscroll="true">
                <header class="pos-customers-head"><div><p class="pos-eyebrow">CHI TIẾT ĐƠN HÀNG</p><h2 id="order-detail-title">{{ $selectedOrder->order_code }}</h2><p>{{ $selectedOrder->created_at->format('d/m/Y H:i') }} · {{ $statusLabels[$selectedOrder->status] ?? 'Chưa xác định' }}</p></div><button type="button" class="pos-button" wire:click="closeOrder">Đóng</button></header>
                <div class="pos-order-info"><div><h3>Khách hàng</h3><p>{{ $selectedOrder->customer_name ?: 'Khách lẻ' }}</p><p>{{ $selectedOrder->customer_phone ?: 'Không có SĐT' }}</p>@if($selectedOrder->customer_address)<p>{{ $selectedOrder->customer_address }}</p>@endif</div><div><h3>Thông tin thanh toán</h3><p>{{ $paymentLabels[$selectedOrder->payment_status] ?? 'Chưa xác định' }} · {{ $methodLabels[$selectedOrder->payment_method] ?? 'Khác' }}</p><p>Thu ngân: {{ $selectedOrder->creator?->name ?? 'Không còn tài khoản' }}</p><p>{{ $selectedOrder->receipt_confirmed_at ? 'Đã xác nhận in bill: '.$selectedOrder->receipt_confirmed_at->format('d/m/Y H:i') : 'Chưa ghi nhận xác nhận in bill' }}</p></div></div>
                <div class="pos-customer-table-wrap"><table class="pos-customer-table"><thead><tr><th>Hàng đã mua</th><th>Đơn vị</th><th>Số lượng</th><th>Đơn giá</th><th>Thành tiền</th></tr></thead><tbody>@foreach($selectedOrder->orderItems as $item)<tr><td>{{ $item->product_name }}</td><td>{{ $item->unit_name }}</td><td>{{ $item->quantity }}</td><td>{{ \App\Helpers\AppHelper::formatMoney($item->unit_price) }}</td><td>{{ \App\Helpers\AppHelper::formatMoney($item->subtotal) }}</td></tr>@endforeach</tbody></table></div>
                <dl class="pos-totals pos-history-totals"><div><dt>Tiền hàng</dt><dd>{{ \App\Helpers\AppHelper::formatMoney($selectedOrder->subtotal) }}</dd></div><div><dt>Giảm giá</dt><dd>{{ \App\Helpers\AppHelper::formatMoney($selectedOrder->discount_amount) }}</dd></div><div><dt>Thuế GTGT ({{ $selectedOrder->tax_rate }}%)</dt><dd>{{ \App\Helpers\AppHelper::formatMoney($selectedOrder->tax_amount) }}</dd></div><div><dt>Tổng thanh toán</dt><dd>{{ \App\Helpers\AppHelper::formatMoney($selectedOrder->grand_total) }}</dd></div><div><dt>Đã thanh toán</dt><dd>{{ \App\Helpers\AppHelper::formatMoney($selectedOrder->paid_amount) }}</dd></div><div><dt>Còn phải thanh toán</dt><dd>{{ \App\Helpers\AppHelper::formatMoney($summary['remaining']) }}</dd></div>
                    @if($selectedOrder->payment_method === 'cash' && $selectedOrder->cash_received !== null)<div><dt>Tiền khách đưa</dt><dd>{{ \App\Helpers\AppHelper::formatMoney($selectedOrder->cash_received) }}</dd></div><div><dt>Tiền thừa trả khách</dt><dd>{{ \App\Helpers\AppHelper::formatMoney($summary['change']) }}</dd></div>@elseif($selectedOrder->payment_method === 'cash')<p class="pos-muted">Đơn cũ chưa lưu tiền khách đưa và tiền thừa.</p>@endif
                </dl>
                @if($selectedOrder->is_vat_invoice)<section class="pos-order-invoice"><h3>Thông tin yêu cầu hóa đơn</h3><p>{{ $selectedOrder->company_name }} · MST: {{ $selectedOrder->company_tax_id }}</p><p>{{ $selectedOrder->company_address }}</p><p>Email nhận hóa đơn: {{ $selectedOrder->invoice_email }}</p></section>@endif
                @if($selectedOrder->notes)<p>Ghi chú: {{ $selectedOrder->notes }}</p>@endif
                <footer class="pos-customer-actions"><a class="pos-button pos-primary" href="{{ route('pos.receipt', $selectedOrder->uuid) }}?autoprint=1" target="_blank" rel="noopener">{{ $selectedOrder->payment_status === 'paid' ? 'In lại bill' : 'In phiếu đơn chưa thanh toán' }}</a><p class="pos-muted">In không thay đổi thanh toán, tồn kho hoặc xác nhận in của đơn.</p></footer>
            </section>
        </div>
    @endif
</main>
