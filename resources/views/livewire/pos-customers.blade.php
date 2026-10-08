<section id="{{ $selectForSale ? 'pos-customer-picker' : 'pos-main' }}" class="pos-customers" aria-labelledby="customers-title">
    <header class="pos-customers-head">
        <div><p class="pos-eyebrow">QUẢN LÝ TẠI QUẦY</p><h1 id="customers-title">Khách hàng</h1><p class="pos-muted">Dùng chung dữ liệu với admin · Giữ nguyên lịch sử hóa đơn</p></div>
        <button class="pos-button pos-primary" type="button" wire:click="newCustomer">+ Thêm khách hàng</button>
    </header>
    @if($message)<p class="pos-notice" role="status">{{ $message }}</p>@endif
    @if($errors->any())<div class="pos-alert" role="alert">{{ $errors->first() }}</div>@endif
    @if($formOpen)
        <form class="pos-customer-editor" wire:submit="saveCustomer" wire:key="customer-form-{{ $editingId ?? 'new' }}" x-data x-init="$nextTick(() => $refs.name.focus())">
            <h2>{{ $editingId ? 'Sửa thông tin khách hàng' : 'Thêm khách hàng mới' }}</h2>
            <div class="pos-customer-grid">
                <label>Tên khách hàng *<input x-ref="name" wire:model="form.name" required minlength="2" maxlength="100" autocomplete="name"></label>
                <label>Số điện thoại *<input wire:model="form.phone" type="tel" required maxlength="20" autocomplete="tel" placeholder="VD: 0901 234 567"></label>
                <label>Email<input wire:model="form.email" type="email" maxlength="150" autocomplete="email"></label>
                <label>Địa chỉ<input wire:model="form.address" minlength="5" maxlength="255" autocomplete="street-address"></label>
            </div>
            <label>Ghi chú<textarea wire:model="form.notes" rows="2" maxlength="1000"></textarea></label>
            <p class="pos-muted">Không thể đổi SĐT của khách có tài khoản đăng nhập. Không sửa công nợ tại màn hình này.</p>
            <div class="pos-customer-actions"><button class="pos-button pos-primary" type="submit" wire:loading.attr="disabled" wire:target="saveCustomer">{{ $selectForSale ? 'Lưu và chọn khách' : 'Lưu thông tin' }}</button><button class="pos-button" type="button" wire:click="$set('formOpen', false)">Hủy</button></div>
        </form>
    @endif
    <label class="pos-customer-search">Tìm khách hàng<input wire:model.live.debounce.300ms="search" type="search" maxlength="100" placeholder="Tên, số điện thoại hoặc email"></label>
    <div class="pos-customer-table-wrap">
        <table class="pos-customer-table"><thead><tr><th>Khách hàng</th><th>Liên hệ</th><th>Lịch sử</th><th>Thao tác</th></tr></thead><tbody>
            @forelse($customers as $customer)
                <tr wire:key="customer-{{ $customer->id }}">
                    <td><strong>{{ $customer->name }}</strong><small>{{ $customer->address ?: 'Chưa có địa chỉ' }}</small></td>
                    <td>{{ $customer->phone }}<small>{{ $customer->email }}</small></td>
                    <td>{{ $customer->orders_count }} đơn hàng<small>{{ $customer->repair_tickets_count }} phiếu sửa</small></td>
                    <td><div class="pos-customer-actions">
                        @if($selectForSale)<button class="pos-button pos-soft" type="button" wire:click="selectCustomer({{ $customer->id }})">Chọn</button>@endif
                        <button class="pos-button" type="button" wire:click="editCustomer({{ $customer->id }})">Sửa</button>
                        @can('delete', $customer)<button class="pos-button pos-customer-delete" type="button" wire:click="deleteCustomer({{ $customer->id }})" wire:loading.attr="disabled" wire:confirm="Xóa khách hàng này? Chỉ khách chưa có giao dịch, công nợ hoặc tài khoản mới được xóa. Không thể khôi phục sau khi xóa.">Xóa</button>@endcan
                    </div></td>
                </tr>
            @empty<tr><td colspan="4">Chưa có khách hàng phù hợp. Thử từ khóa khác hoặc thêm khách mới.</td></tr>@endforelse
        </tbody></table>
    </div>
    <div class="pos-pagination"><span>{{ $customers->total() }} khách · Trang {{ $customers->currentPage() }} / {{ max(1, $customers->lastPage()) }}</span><div><button class="pos-button" type="button" wire:click="previousPage('customersPage')" @disabled($customers->onFirstPage())>Trước</button><button class="pos-button" type="button" wire:click="nextPage('customersPage')" @disabled(!$customers->hasMorePages())>Sau</button></div></div>
</section>
