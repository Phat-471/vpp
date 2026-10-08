const { test } = require('node:test');
const assert = require('node:assert/strict');
const vm = require('node:vm');
const fs = require('node:fs');
const path = require('node:path');
const source = fs.readFileSync(path.join(__dirname, '../../public/js/invoice-form.js'), 'utf8');

function setup() {
    const elements = {};
    for (const key of ['invoice-enabled', 'invoice-fields', 'tax-code', 'company-name', 'company-address', 'invoice-email', 'tax-status', 'tax-retry']) {
        elements[key] = { value: '', checked: false, hidden: true, handlers: {}, setCustomValidity(message) { this.error = message; }, reportValidity() { return !this.error; }, addEventListener(type, handler) { const previous = this.handlers[type]; this.handlers[type] = previous ? () => { previous(); return handler(); } : handler; } };
    }
    const root = { dataset: { lookupUrl: '/lookup' }, querySelector(selector) { return elements[selector.slice(6, -1)]; } };
    const pending = [];
    const context = {
        window: {}, AbortController, setTimeout: () => 1, clearTimeout() {},
        document: { querySelectorAll: () => [root], getElementById: () => root, querySelector: () => ({ content: 'test-token' }) },
        fetch: () => new Promise(resolve => pending.push(resolve)),
    };
    vm.runInNewContext(source, context);
    return { elements, context, pending };
}

test('invoice flag controls required fields and disabled payload', () => {
    const { elements: e, context } = setup();
    assert.equal(context.window.InvoiceForm.read('form').is_vat_invoice, false);
    e['invoice-enabled'].checked = true;
    e['invoice-enabled'].handlers.change();
    assert.equal(e['invoice-fields'].hidden, false);
    assert.equal(e['invoice-email'].required, true);
    e['tax-code'].value = '0123456789001';
    assert.equal(context.window.InvoiceForm.read('form').company_tax_id, '0123456789-001');
    e['invoice-enabled'].checked = false;
    e['invoice-enabled'].handlers.change();
    assert.equal(e['invoice-email'].required, false);
    assert.equal(context.window.InvoiceForm.read('form').company_name, undefined);
});

test('late lookup response cannot fill the newly entered tax code', async () => {
    const { elements: e, pending } = setup();
    e['invoice-enabled'].checked = true;
    e['tax-code'].value = '0123456789';
    const first = e['tax-retry'].handlers.click();
    e['tax-code'].value = '1234567890';
    e['tax-code'].handlers.input();
    const second = e['tax-retry'].handlers.click();
    pending[1]({ ok: true, json: async () => ({ data: { tax_code: '1234567890', name: 'New company', address: 'New address' } }) });
    await second;
    pending[0]({ ok: true, json: async () => ({ data: { tax_code: '0123456789', name: 'Old company', address: 'Old address' } }) });
    await first;
    assert.equal(e['company-name'].value, 'New company');
    assert.equal(e['company-address'].value, 'New address');
});

test('lookup failure clears old data and allows manual entry', async () => {
    const { elements: e, pending } = setup();
    e['invoice-enabled'].checked = true;
    e['tax-code'].value = '0123456789';
    e['company-name'].value = 'Old company';
    const lookup = e['tax-retry'].handlers.click();
    assert.equal(e['company-name'].value, '');
    pending[0]({ ok: false, json: async () => ({ message: 'Nhập thủ công' }) });
    await lookup;
    assert.equal(e['tax-status'].textContent, 'Nhập thủ công');
    assert.equal(e['tax-retry'].disabled, false);
});

test('invoice validation blocks malformed data and permits manual personal details', () => {
    const { elements: e, context, pending } = setup();
    e['invoice-enabled'].checked = true;
    e['invoice-enabled'].handlers.change();
    e['tax-code'].value = '123';
    e['company-name'].value = 'a';
    e['company-address'].value = 'b';
    e['invoice-email'].value = 'wrong';
    assert.equal(context.window.InvoiceForm.validate('form', false), false);
    assert.ok(e['tax-code'].error);
    e['tax-code'].value = '123456789012';
    e['tax-code'].handlers.input();
    assert.equal(pending.length, 0);
    assert.match(e['tax-status'].textContent, /12/);
    e['company-name'].value = 'Người mua kiểm thử';
    e['company-address'].value = 'Địa chỉ kiểm thử';
    e['invoice-email'].value = 'invoice@example.test';
    assert.equal(context.window.InvoiceForm.validate('form', false), true);
});
