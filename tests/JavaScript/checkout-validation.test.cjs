const { test } = require('node:test');
const assert = require('node:assert/strict');
const vm = require('node:vm');
const fs = require('node:fs');
const path = require('node:path');
const context = { window: {}, document: { querySelectorAll: () => [] } };
vm.runInNewContext(fs.readFileSync(path.join(__dirname, '../../public/js/checkout-validation.js'), 'utf8'), context);
const validation = context.window.CheckoutValidation;

test('malformed receiver information is rejected', () => {
    assert.ok(validation.message.name('á'));
    assert.ok(validation.message.name('1234'));
    assert.ok(validation.message.phone('dás'));
    assert.ok(validation.message.phone('09abc12345678'));
    assert.ok(validation.message.phone('1234567890'));
    assert.ok(validation.message.address('ádas'));
    assert.ok(validation.message.address('123456'));
});

test('Vietnamese receiver formats and international phone prefix are accepted', () => {
    assert.equal(validation.message.name('Nguyễn Văn An'), '');
    assert.equal(validation.message.address('Ấp Đông, xã Bình Minh'), '');
    for (const phone of ['0912345678', '0912 345 678', '+84 912 345 678', '02812345678']) {
        assert.equal(validation.message.phone(phone), '');
    }
    assert.equal(validation.normalizePhone('+84 912 345 678'), '0912345678');
});
