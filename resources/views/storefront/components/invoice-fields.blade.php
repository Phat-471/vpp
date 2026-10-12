<section id="{{ $invoiceId }}" data-invoice-form data-lookup-url="{{ route('business.lookup') }}" class="invoice-fields">
    <label class="invoice-fields__toggle"><input type="checkbox" data-invoice-enabled> <span>Xuất hóa đơn</span></label>
    <div data-invoice-fields hidden class="invoice-fields__body">
        <label>Mã số thuế
            <input data-tax-code maxlength="14" inputmode="numeric" placeholder="MST 10 hoặc 13 số">
        </label>
        <button data-tax-retry type="button">Tra cứu MST</button>
        <p data-tax-status role="status" aria-live="polite"></p>
        <label>Tên người mua / doanh nghiệp<input data-company-name minlength="2" maxlength="255"></label>
        <label>Địa chỉ đăng ký<input data-company-address maxlength="255"></label>
        <label>Email nhận hóa đơn<input data-invoice-email type="email" maxlength="255" placeholder="ketoan@doanhnghiep.vn"></label>
        <p class="invoice-fields__hint">Kiểm tra lại thông tin trước khi đặt hàng. Có thể nhập thủ công.</p>
    </div>
</section>
