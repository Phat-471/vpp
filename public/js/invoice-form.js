(() => {
    const normalize = value => /^\d{13}$/.test(value.trim()) ? `${value.trim().slice(0, 10)}-${value.trim().slice(10)}` : value.trim();
    window.InvoiceForm = {
        validate(id, report = true) {
            const root = document.getElementById(id);
            if (!root) return true;
            const enabled = root.querySelector('[data-invoice-enabled]').checked;
            const checks = [
                ['[data-tax-code]', value => /^(?:\d{10}(?:-\d{3})?|\d{12})$/.test(normalize(value)), 'MST cần 10 số, 12 số hoặc MST chi nhánh 13 số.'],
                ['[data-company-name]', value => value.length >= 2 && value.length <= 255 && /\p{L}/u.test(value), 'Nhập đầy đủ tên người mua hoặc doanh nghiệp.'],
                ['[data-company-address]', value => value.length >= 5 && value.length <= 255 && /\p{L}/u.test(value), 'Nhập đầy đủ địa chỉ xuất hóa đơn.'],
                ['[data-invoice-email]', value => value.length <= 255 && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value), 'Email nhận hóa đơn chưa đúng định dạng.'],
            ];
            let valid = true;
            for (const [selector, check, message] of checks) {
                const input = root.querySelector(selector);
                const error = enabled && !check(input.value.trim()) ? message : '';
                input.setCustomValidity(error);
                if (error) {
                    valid = false;
                    if (report) { input.reportValidity(); report = false; }
                }
            }
            return valid;
        },
        read(id) {
            const root = document.getElementById(id);
            if (!root || !root.querySelector('[data-invoice-enabled]').checked) return { is_vat_invoice: false };
            return {
                is_vat_invoice: true,
                company_tax_id: normalize(root.querySelector('[data-tax-code]').value),
                company_name: root.querySelector('[data-company-name]').value.trim(),
                company_address: root.querySelector('[data-company-address]').value.trim(),
                invoice_email: root.querySelector('[data-invoice-email]').value.trim(),
            };
        },
    };
    document.querySelectorAll('[data-invoice-form]').forEach(root => {
        const enabled = root.querySelector('[data-invoice-enabled]');
        const fields = root.querySelector('[data-invoice-fields]');
        const tax = root.querySelector('[data-tax-code]');
        const name = root.querySelector('[data-company-name]');
        const address = root.querySelector('[data-company-address]');
        const email = root.querySelector('[data-invoice-email]');
        const status = root.querySelector('[data-tax-status]');
        const retry = root.querySelector('[data-tax-retry]');
        let timer;
        let controller;
        let sequence = 0;
        function invalidate() {
            clearTimeout(timer);
            controller?.abort();
            sequence++;
            name.value = '';
            address.value = '';
            status.textContent = '';
            retry.disabled = false;
        }
        async function lookup() {
            if (!enabled.checked) return;
            const code = normalize(tax.value);
            if (/^\d{12}$/.test(code)) {
                status.textContent = 'Mã 12 số thuộc nhóm cá nhân. Nguồn miễn phí hiện tại chưa tự tra nhóm này; vui lòng nhập tên và địa chỉ thủ công.';
                return;
            }
            if (!/^\d{10}(?:-\d{3})?$/.test(code)) {
                status.textContent = 'Nhập MST doanh nghiệp 10 số hoặc MST chi nhánh 13 số.';
                return;
            }
            controller?.abort();
            controller = new AbortController();
            const current = ++sequence;
            name.value = '';
            address.value = '';
            retry.disabled = true;
            status.textContent = 'Đang tra cứu…';
            try {
                const response = await fetch(root.dataset.lookupUrl, {
                    method: 'POST', credentials: 'same-origin', signal: controller.signal,
                    headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify({ tax_code: code }),
                });
                const data = await response.json();
                if (current !== sequence || !enabled.checked || normalize(tax.value) !== code) return;
                if (response.ok && data.data && data.data.tax_code === code) {
                    tax.value = code;
                    name.value = data.data.name;
                    address.value = data.data.address;
                }
                status.textContent = data.message || 'Không tra cứu được. Có thể nhập thủ công.';
            } catch (error) {
                if (current === sequence && error.name !== 'AbortError') status.textContent = 'Không kết nối được nguồn tra cứu. Có thể nhập thủ công.';
            } finally {
                if (current === sequence) retry.disabled = false;
            }
        }
        enabled.addEventListener('change', () => {
            invalidate();
            fields.hidden = !enabled.checked;
            [tax, name, address, email].forEach(input => { input.required = enabled.checked; input.disabled = !enabled.checked; input.setCustomValidity(''); });
            if (enabled.checked && tax.value) lookup();
        });
        tax.addEventListener('input', () => {
            invalidate();
            tax.setCustomValidity('');
            if (/^\d{12}$/.test(tax.value.trim())) {
                status.textContent = 'Mã 12 số: nhập tên và địa chỉ xuất hóa đơn thủ công; nguồn hiện tại chỉ tự tra doanh nghiệp.';
            } else if (/^\d{10}(?:-?\d{3})?$/.test(tax.value.trim())) timer = setTimeout(lookup, 700);
        });
        [tax, name, address, email].forEach(input => {
            input.disabled = !enabled.checked;
            input.required = enabled.checked;
            input.addEventListener('input', () => input.setCustomValidity(''));
            input.addEventListener('blur', () => window.InvoiceForm.validate(root.id, false));
        });
        fields.hidden = !enabled.checked;
        retry.addEventListener('click', lookup);
    });
})();
