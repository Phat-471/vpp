import test from 'node:test';
import assert from 'node:assert/strict';
import { mergeMessages, messageTime, requestError } from '../../public/js/live-chat.js';

test('Polling and send responses merge in ID order without duplicate bubbles', () => {
    const result = mergeMessages([{ id: 3, message: 'Khách gửi' }], [{ id: 2, message: 'Admin trả lời' }, { id: 3, message: 'Khách gửi' }]);
    assert.deepEqual(result.map(row => row.id), [2, 3]);
});

test('Retry confirmation updates an existing message rather than duplicating it', () => {
    const result = mergeMessages([{ id: 1, is_read: false }], [{ id: 1, is_read: true }]);
    assert.equal(result.length, 1);
    assert.equal(result[0].is_read, true);
});

test('Time is displayed in Vietnam timezone and invalid dates are safe', () => {
    assert.match(messageTime('2026-10-09T02:05:00Z'), /09:05/);
    assert.equal(messageTime('invalid'), '');
});

test('Validation, spam and expired session errors have actionable Vietnamese copy', () => {
    assert.match(requestError(429), /chờ một phút/);
    assert.match(requestError(419), /tải lại trang/);
    assert.equal(requestError(422, { errors: { message: ['Tin quá dài'] } }), 'Tin quá dài');
    assert.match(requestError(500), /thử lại/);
});
