<main id="pos-main" class="pos-workspace" x-data="{ customerOpen: false, noteOpen: false, discountOpen: false }"
    x-on:keydown.f2.window.prevent="if (!$wire.customerManagerOpen) $refs.search.focus()"
    x-on:keydown.f4.window.prevent="if (!$wire.customerManagerOpen) $wire.saveDraft()"
    x-on:keydown.f9.window.prevent="if (!$wire.customerManagerOpen) $wire.checkout()">
    @if($customerManagerOpen)
        <div class="pos-modal-backdrop" x-data x-on:keydown.escape.window="$wire.set('customerManagerOpen', false)">
            <section class="pos-customers-dialog" role="dialog" aria-modal="true" aria-label="Chọn và quản lý khách hàng" x-trap.inert.noscroll="true">
                <button class="pos-button" type="button" wire:click="$set('customerManagerOpen', false)">Đóng danh sách</button>
                <livewire:pos-customers :select-for-sale="true" />
            </section>
        </div>
    @endif
    <section class="pos-order-panel" aria-labelledby="order-title">
        <div class="pos-order-head">
            <div><p class="pos-eyebrow">BÁN TẠI QUẦY</p><h1 id="order-title">Đơn hàng mới</h1></div>
            <button class="pos-button pos-soft" wire:click="newSale" wire:confirm="Tạo đơn mới và bỏ nội dung giỏ hàng hiện tại?" type="button"><x-heroicon-o-plus /> Đơn mới</button>
        </div>
        <button class="pos-customer" type="button" x-on:click="customerOpen = !customerOpen" x-bind:aria-expanded="customerOpen">
            <span class="pos-customer-icon"><x-heroicon-o-user /></span>
            <span><strong>{{ $customerName ?: 'Khách lẻ' }}</strong><small>{{ $customerPhone ?: 'Thêm thông tin khách hàng' }}</small></span>
            <x-heroicon-o-chevron-down />
        </button>
        <div x-show="customerOpen" x-cloak class="pos-customer-fields">
            <button class="pos-button pos-soft" type="button" wire:click="$set('customerManagerOpen', true)" @disabled($completedOrderUuid)>Chọn / thêm / sửa khách hàng</button>
            <label>Tên khách hàng<input wire:model.blur="customerName" maxlength="100" placeholder="Tên khách hàng (không bắt buộc)"></label>
            <label>Số điện thoại<input wire:model.blur="customerPhone" type="tel" maxlength="20" placeholder="Số điện thoại khách hàng"></label>
        </div>
        @if($errors->any())<div class="pos-alert" role="alert">{{ $errors->first() }}</div>@endif
        @if($message)<div class="pos-notice" role="status">{{ $message }}</div>@endif
        <div wire:offline class="pos-alert" role="alert">Mất kết nối. Kiểm tra mạng trước khi thanh toán.</div>
        <div class="pos-cart" aria-label="Giỏ hàng">
            @forelse($cart as $key => $line)
                <article class="pos-cart-line" wire:key="cart-{{ $key }}">
                    <div class="pos-line-heading"><h2 title="{{ $line['name'] }}">{{ $line['name'] }}</h2><button class="pos-remove" wire:click="removeFromCart('{{ $key }}')" type="button" aria-label="Xóa {{ $line['name'] }}"><x-heroicon-o-trash /></button></div>
                    <div class="pos-line-controls">
                        <select wire:change="changeUnit('{{ $key }}', $event.target.value)" aria-label="Đơn vị bán của {{ $line['name'] }}">
                            <option value="0" @selected(!$line['product_unit_id'])>{{ $cartProducts->get($line['product_id'])?->base_unit ?? $line['unit_name'] }}</option>
                            @foreach(($cartProducts->get($line['product_id'])?->units ?? []) as $unit)
                                <option value="{{ $unit->id }}" @selected($line['product_unit_id'] === $unit->id)>{{ $unit->unit_name }} (×{{ $unit->conversion_rate }})</option>
                            @endforeach
                        </select>
                        <div class="pos-quantity"><button wire:click="updateQuantity('{{ $key }}', -1)" type="button" aria-label="Giảm số lượng">−</button><span>{{ $line['quantity'] }}</span><button wire:click="updateQuantity('{{ $key }}', 1)" type="button" aria-label="Tăng số lượng">+</button></div>
                        <strong class="pos-line-total">{{ \App\Helpers\AppHelper::formatMoney($line['subtotal']) }}</strong>
                    </div>
                </article>
            @empty
                <div class="pos-cart-empty"><x-heroicon-o-shopping-bag /><h2>Giỏ hàng đang trống</h2><p>Chọn sản phẩm bên cạnh hoặc quét mã vạch để bắt đầu.</p><kbd>F2</kbd><span> Tìm nhanh sản phẩm</span></div>
            @endforelse
        </div>
        <div class="pos-checkout-area">
            <div class="pos-inline-actions">
                <button class="pos-button" x-on:click="discountOpen = !discountOpen" type="button" x-bind:aria-expanded="discountOpen"><x-heroicon-o-receipt-percent /> Giảm giá</button>
                <button class="pos-button" x-on:click="noteOpen = !noteOpen" type="button" x-bind:aria-expanded="noteOpen"><x-heroicon-o-pencil-square /> Ghi chú</button>
            </div>
            <label x-show="discountOpen" x-cloak class="pos-inline-field">Giảm giá (₫)<input wire:model.live.debounce.300ms="discount" type="number" min="0" max="{{ $subtotal }}" step="1"></label>
            <label x-show="noteOpen" x-cloak class="pos-inline-field">Ghi chú đơn hàng<textarea wire:model.blur="notes" maxlength="1000" rows="2"></textarea></label>
            <dl class="pos-totals"><div><dt>Tiền hàng <small>({{ count($cart) }} mặt hàng)</small></dt><dd>{{ \App\Helpers\AppHelper::formatMoney($subtotal) }}</dd></div><div><dt>Giảm giá</dt><dd>{{ \App\Helpers\AppHelper::formatMoney(max(0, (int) $discount)) }}</dd></div><div><dt>Thuế GTGT (0%)</dt><dd>0 ₫</dd></div></dl>
            <details class="pos-invoice-details"><summary>Thông tin xuất hóa đơn</summary>
                <label class="pos-invoice-toggle"><input type="checkbox" wire:model.live="isVatInvoice"> Khách cần hóa đơn</label>
                @if($isVatInvoice)
                    <label>Mã số thuế<input wire:model.live.debounce.700ms="companyTaxId" maxlength="14" inputmode="numeric" placeholder="10 số, 12 số hoặc MST chi nhánh"></label>
                    <button type="button" class="pos-button pos-soft" wire:click="lookupCompany" wire:loading.attr="disabled" wire:target="lookupCompany,companyTaxId">Tra cứu lại</button>
                    <p class="pos-muted" wire:loading wire:target="lookupCompany,companyTaxId">Đang tra cứu…</p>
                    <p class="pos-muted" role="status">{{ $taxLookupMessage }}</p>
                    <label>Tên người mua / doanh nghiệp<input wire:model="companyName" maxlength="255"></label>
                    <label>Địa chỉ đăng ký<input wire:model="companyAddress" maxlength="255"></label>
                    <label>Email nhận hóa đơn<input wire:model="invoiceEmail" type="email" maxlength="255" placeholder="ketoan@doanhnghiep.vn"></label>
                    <p class="pos-muted">Tra cứu qua VietQR/Xinvoice; chỉ gửi MST. Có thể sửa thông tin trước khi thanh toán.</p>
                @endif
            </details>
            <div class="pos-grand-total"><span>Khách cần thanh toán</span><strong>{{ \App\Helpers\AppHelper::formatMoney($total) }}</strong></div>
            <div class="pos-payment-options" role="group" aria-label="Phương thức thanh toán">
                <button class="pos-button {{ $paymentMethod === 'cash' ? 'is-selected' : '' }}" wire:click="$set('paymentMethod', 'cash')" aria-pressed="{{ $paymentMethod === 'cash' ? 'true' : 'false' }}" type="button"><x-heroicon-o-banknotes /> Tiền mặt</button>
                <button class="pos-button {{ $paymentMethod === 'vietqr' ? 'is-selected' : '' }}" wire:click="$set('paymentMethod', 'vietqr')" aria-pressed="{{ $paymentMethod === 'vietqr' ? 'true' : 'false' }}" type="button"><x-heroicon-o-qr-code /> VietQR</button>
            </div>
            @if($paymentMethod === 'cash')
                <div class="pos-cash"><label>Khách đưa (₫)<input wire:model.live.debounce.300ms="cashGiven" type="number" min="0" step="1" inputmode="numeric"></label><span>Tiền thừa<strong>{{ \App\Helpers\AppHelper::formatMoney($change) }}</strong></span></div>
                @if($total > 0)<button class="pos-exact-cash" type="button" wire:click="$set('cashGiven', '{{ $total }}')">Khách đưa đủ {{ \App\Helpers\AppHelper::formatMoney($total) }}</button>@endif
            @else
                <p class="pos-payment-hint">Tạo đơn để hiển thị mã QR. Đơn được ghi nhận khi hệ thống xác nhận nhận tiền.</p>
            @endif
            <div class="pos-checkout-buttons">
                <button class="pos-button pos-primary" type="button" wire:click="checkout" wire:loading.attr="disabled" wire:target="checkout,addToCart,updateQuantity,changeUnit" @disabled(!$cart || $completedOrderUuid)><span wire:loading.remove wire:target="checkout">{{ $paymentMethod === 'cash' ? 'Thanh toán' : 'Tạo đơn VietQR' }} <small>F9</small></span><span wire:loading wire:target="checkout">Đang xử lý…</span><x-heroicon-o-arrow-right /></button>
                <button class="pos-button" type="button" wire:click="saveDraft" wire:loading.attr="disabled" @disabled(!$cart || $completedOrderUuid) @if($hasDraft) wire:confirm="Thay thế đơn tạm đang lưu bằng giỏ hàng hiện tại?" @endif title="Lưu trong phiên làm việc (F4)"><x-heroicon-o-bookmark /> Lưu tạm</button>
            </div>
            @if($hasDraft)<button class="pos-draft-link" wire:click="restoreDraft" type="button" @disabled((bool)$cart || $completedOrderUuid)>Mở đơn tạm đã lưu</button>@endif
        </div>
    </section>
    <section class="pos-catalog-panel" aria-labelledby="catalog-title">
        <div class="pos-catalog-header"><h2 id="catalog-title">Danh sách sản phẩm</h2><span>{{ number_format($products->total(), 0, ',', '.') }} sản phẩm</span></div>
        <form class="pos-search" wire:submit="scan"><x-heroicon-o-magnifying-glass /><input x-ref="search" wire:model.live.debounce.250ms="search" maxlength="100" placeholder="Tìm tên, mã sản phẩm hoặc quét mã vạch" aria-label="Tìm sản phẩm"><kbd>F2</kbd><button class="pos-icon-button" type="submit" title="Thêm sản phẩm theo mã vạch hoặc mã SKU" aria-label="Thêm theo mã"><x-heroicon-o-qr-code /></button></form>
        <div class="pos-categories" role="group" aria-label="Lọc loại sản phẩm"><button class="{{ !$selectedCategory ? 'is-active' : '' }}" wire:click="$set('selectedCategory', null)" type="button">Tất cả</button>@foreach($categories as $category)<button wire:key="category-{{ $category->id }}" class="{{ $selectedCategory === $category->id ? 'is-active' : '' }}" wire:click="$set('selectedCategory', {{ $category->id }})" type="button">{{ $category->name }}</button>@endforeach</div>
        <div class="pos-product-table-wrap" wire:loading.class="pos-loading" wire:target="search,selectedCategory,nextPage,previousPage">
            <table class="pos-product-table"><thead><tr><th scope="col">Tên sản phẩm</th><th scope="col">Loại</th><th scope="col">Giá bán</th><th scope="col"><span class="pos-sr-only">Thêm vào giỏ</span></th></tr></thead><tbody>
            @forelse($products as $product)
                <tr wire:key="product-{{ $product->id }}">
                    <td><strong title="{{ $product->name }}">{{ $product->name }}</strong><small>{{ $product->sku }}@if($product->stock_quantity <= 0) · <span class="pos-out-of-stock">Hết hàng</span>@endif</small></td>
                    <td class="pos-category-cell">{{ $product->category?->name ?? 'Khác' }}</td>
                    <td class="pos-price-cell"><strong>{{ \App\Helpers\AppHelper::formatMoney((float) $product->retail_price) }}</strong><small>/ {{ $product->base_unit }}</small></td>
                    <td><button class="pos-add-button" type="button" wire:click="addToCart({{ $product->id }})" wire:loading.attr="disabled" wire:target="addToCart({{ $product->id }})" @disabled($product->stock_quantity <= 0 || $completedOrderUuid) aria-label="Thêm {{ $product->name }}" title="Thêm {{ $product->name }}"><x-heroicon-o-plus /></button></td>
                </tr>
            @empty
                <tr><td colspan="4"><div class="pos-products-empty"><x-heroicon-o-magnifying-glass /><h3>Không tìm thấy sản phẩm</h3><p>Thử tên khác, mã SKU hoặc chọn lại danh mục.</p><button class="pos-button pos-soft" type="button" wire:click="$set('search', '')">Xóa từ khóa</button></div></td></tr>
            @endforelse
            </tbody></table>
        </div>
        <div class="pos-pagination"><span>Trang {{ $products->currentPage() }} / {{ max(1, $products->lastPage()) }}</span><div><button class="pos-button" wire:click="previousPage" type="button" @disabled($products->onFirstPage()) aria-label="Trang trước"><x-heroicon-o-chevron-left /></button><button class="pos-button" wire:click="nextPage" type="button" @disabled(!$products->hasMorePages()) aria-label="Trang sau"><x-heroicon-o-chevron-right /></button></div></div>
        <p class="pos-catalog-help">Giá theo đơn vị gốc · Đổi đơn vị bán trong giỏ hàng</p>
    </section>
    @if($completedOrder)
        <div class="pos-modal-backdrop" @if($completedOrder->payment_status !== 'paid') wire:poll.5s="refreshPayment" @endif>
            <section class="pos-result-modal" role="dialog" aria-modal="true" aria-labelledby="result-title" x-data x-init="$nextTick(() => $refs.receiptAction?.focus())">
                <div class="pos-result-icon {{ $completedOrder->payment_status === 'paid' ? 'is-paid' : '' }}">@if($completedOrder->payment_status === 'paid')<x-heroicon-o-check />@else<x-heroicon-o-clock />@endif</div>
                <h2 id="result-title">{{ $completedOrder->payment_status === 'paid' ? 'Đã thanh toán' : 'Chờ thanh toán VietQR' }}</h2>
                <p>Mã đơn <strong>{{ $completedOrder->order_code }}</strong></p><strong class="pos-result-total">{{ \App\Helpers\AppHelper::formatMoney((float) $completedOrder->grand_total) }}</strong>
                @if($completedOrder->payment_status !== 'paid')
                    @if($qrUrl)<img class="pos-vietqr" src="{{ $qrUrl }}" alt="Mã VietQR thanh toán đơn {{ $completedOrder->order_code }}"><p class="pos-muted">Giữ đúng số tiền và nội dung chuyển khoản. Trạng thái sẽ được cập nhật khi có xác nhận thanh toán.</p>
                    @else<div class="pos-alert">Chưa cấu hình đủ tài khoản VietQR. Quản trị viên cần cập nhật thông tin ngân hàng trong cài đặt cửa hàng.</div>@endif
                @endif
                @if($completedOrder->payment_status === 'paid')
                    <p class="pos-muted">Hộp thoại in được mở tự động. Nếu đã hủy in hoặc máy in gặp lỗi, bấm “In lại hóa đơn”.</p>
                    @error('receipt')<div class="pos-alert" role="alert">{{ $message }}</div>@enderror
                    <div class="pos-result-actions">
                        <button class="pos-button" type="button" wire:click="reprintReceipt" wire:loading.attr="disabled"><x-heroicon-o-printer /> In lại hóa đơn</button>
                        @if(!$completedOrder->receipt_confirmed_at)
                            <button class="pos-button pos-primary" x-ref="receiptAction" type="button" wire:click="confirmReceiptPrinted" wire:loading.attr="disabled">Xác nhận đã in</button>
                        @else
                            <button class="pos-button pos-primary" x-ref="receiptAction" type="button" wire:click="newSale" wire:loading.attr="disabled">Đơn tiếp theo <x-heroicon-o-arrow-right /></button>
                        @endif
                    </div>
                @endif
            </section>
        </div>
    @endif
</main>
