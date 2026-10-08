<section id="{{ $invoiceId }}" data-invoice-form data-lookup-url="{{ route('business.lookup') }}" class="space-y-3 text-xs border border-slate-200 rounded-xl p-3">
    <label class="flex items-center gap-2 font-bold text-slate-700"><input type="checkbox" data-invoice-enabled class="accent-emerald-600"> Xuất hóa đơn</label>
    <div data-invoice-fields hidden class="space-y-3">
        <label class="block font-bold text-slate-700">Mã số thuế
            <input data-tax-code maxlength="14" inputmode="numeric" placeholder="MST 10 số, 12 số hoặc MST chi nhánh" class="block w-full px-3 py-2 rounded-xl border border-slate-300 bg-slate-50 mt-1">
        </label>
        <button data-tax-retry type="button" class="px-3 py-2 rounded-lg bg-emerald-50 text-emerald-800 font-bold">Tra cứu lại</button>
        <p data-tax-status role="status" class="text-slate-600"></p>
        <label class="block font-bold text-slate-700">Tên người mua / doanh nghiệp<input data-company-name minlength="2" maxlength="255" class="block w-full px-3 py-2 rounded-xl border border-slate-300 bg-slate-50 mt-1"></label>
        <label class="block font-bold text-slate-700">Địa chỉ đăng ký<input data-company-address maxlength="255" class="block w-full px-3 py-2 rounded-xl border border-slate-300 bg-slate-50 mt-1"></label>
        <label class="block font-bold text-slate-700">Email nhận hóa đơn<input data-invoice-email type="email" maxlength="255" placeholder="ketoan@doanhnghiep.vn" class="block w-full px-3 py-2 rounded-xl border border-slate-300 bg-slate-50 mt-1"></label>
        <p class="text-slate-500">Chỉ gửi MST tới dịch vụ tra cứu VietQR/Xinvoice. Kiểm tra lại tên và địa chỉ; có thể nhập thủ công khi không tìm thấy.</p>
    </div>
</section>
