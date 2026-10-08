(() => {
    const normalizePhone = value => {
        const trimmed = value.trim();
        if (!/^\+?[0-9 ().-]+$/.test(trimmed)) return trimmed;
        const digits = trimmed.replace(/[^0-9]/g, '');
        return digits.startsWith('84') ? `0${digits.slice(2)}` : digits;
    };
    const messages = {
        name(value) { return value.length >= 2 && value.length <= 100 && /\p{L}/u.test(value) ? '' : 'Họ tên cần 2–100 ký tự và có chữ cái.'; },
        phone(value) { return /^(?:0[35789][0-9]{8}|02[0-9]{9})$/.test(normalizePhone(value)) ? '' : 'sdt không hợp lệ, ví dụ 0912 345 678 hoặc +84 912 345 678.'; },
        address(value) { return value.length >= 5 && value.length <= 255 && /\p{L}/u.test(value) ? '' : 'Ghi rõ địa chỉ nhận hàng, ít nhất 5 ký tự và có tên đường hoặc địa danh.'; },
    };
    const fieldMap = { 'order-customer-name': 'customer_name', 'chk-name': 'customer_name', 'order-customer-phone': 'customer_phone', 'chk-phone': 'customer_phone', 'order-customer-address': 'customer_address', 'chk-address': 'customer_address' };
    function apply(input, message) {
        input.setCustomValidity(message);
        input.setAttribute('aria-invalid', message ? 'true' : 'false');
        let error = input.parentElement.querySelector(`[data-error-for="${input.id}"]`);
        if (!error) {
            error = document.createElement('p');
            error.dataset.errorFor = input.id;
            error.id = `${input.id}-error`;
            error.className = 'text-rose-700 text-xs mt-1';
            error.setAttribute('role', 'alert');
            input.insertAdjacentElement('afterend', error);
            input.setAttribute('aria-describedby', error.id);
        }
        error.textContent = message;
        error.hidden = !message;
    }
    function validateInput(input) {
        const kind = input.id.endsWith('name') ? 'name' : input.id.endsWith('phone') ? 'phone' : 'address';
        apply(input, messages[kind](input.value.trim()));
    }
    window.CheckoutValidation = {
        normalizePhone,
        message: messages,
        validate(form) {
            form.querySelectorAll('[data-checkout-contact]').forEach(validateInput);
            form.querySelectorAll('[data-invoice-form]').forEach(root => window.InvoiceForm.validate(root.id, false));
            return form.reportValidity();
        },
        serverErrors(form, errors, box) {
            const allMessages = Object.values(errors || {}).flat();
            for (const [id, field] of Object.entries(fieldMap)) {
                const input = form.querySelector(`#${id}`);
                if (input && errors?.[field]?.[0]) apply(input, errors[field][0]);
            }
            if (box && allMessages.length) {
                box.textContent = allMessages.join(' ');
                box.classList.remove('hidden');
            }
            form.reportValidity();
            return allMessages.join(' ');
        },
    };
    document.querySelectorAll('#online-checkout-form, #main-checkout-form').forEach(form => {
        for (const id of Object.keys(fieldMap)) {
            const input = form.querySelector(`#${id}`);
            if (!input) continue;
            if (input.parentElement.classList.contains('grid')) {
                const field = document.createElement('div');
                input.before(field);
                field.appendChild(input);
            }
            input.dataset.checkoutContact = '';
            input.maxLength = id.endsWith('name') ? 100 : id.endsWith('phone') ? 25 : 255;
            input.addEventListener('input', () => validateInput(input));
            input.addEventListener('blur', () => validateInput(input));
        }
        // Include programmatic submits; server validation remains authoritative.
        form.addEventListener('submit', event => {
            if (!window.CheckoutValidation.validate(form)) {
                event.preventDefault();
                event.stopImmediatePropagation();
            }
        }, true);
    });
})();
