import test from 'node:test';
import assert from 'node:assert/strict';
import { mergeMessages, messageTime, requestError, mountLiveChat } from '../../public/js/live-chat.js';

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

// A small DOM test double exercises the real client controller without browser/dependency installs.
class Element {
    constructor(tag = 'div') {
        this.tag = tag;
        this.children = [];
        this.events = {};
        this.dataset = {};
        this.hidden = false;
        this.value = '';
        this.textContent = '';
        this.scrollHeight = 200;
        this.clientHeight = 200;
        this.scrollTop = 0;
    }
    addEventListener(name, handler) { this.events[name] = handler; }
    append(...children) { this.children.push(...children); }
    replaceChildren(fragment) { this.children = fragment.children; }
    setAttribute(name, value) { this[name] = value; }
    focus() { this.focused = true; }
    getBoundingClientRect() { return { top: 0, bottom: 200 }; }
    querySelectorAll() { return this.children.filter(node => node.dataset.messageId); }
    async emit(name) { return this.events[name]?.({ preventDefault() {} }); }
}

const flush = async () => { for (let i = 0; i < 8; i++) await new Promise(resolve => setImmediate(resolve)); };

function fixture(t, responses, savedOpen = '0') {
    const originals = new Map();
    const replace = (key, value) => { originals.set(key, globalThis[key]); globalThis[key] = value; };
    const ids = ['live-chat-box', 'live-chat-toggle-btn', 'live-chat-messages', 'live-chat-input', 'chat-unread-badge', 'chat-error', 'chat-connect-retry', 'chat-history', 'chat-send-btn', 'chat-session-status', 'chat-connection', 'live-chat-form', 'chat-close'];
    const nodes = Object.fromEntries(ids.map(id => [id, new Element()]));
    nodes['live-chat-box'].hidden = true;
    const root = { dataset: { initUrl: '/init', sendUrl: '/send', messagesUrl: '/messages', readUrl: '/read' }, querySelector: key => nodes[key.slice(1)], querySelectorAll: () => [] };
    const calls = [];
    const timers = [];
    const stored = new Map([['vpp_chat_open', savedOpen]]);
    replace('sessionStorage', { getItem: key => stored.get(key), setItem: (key, value) => stored.set(key, value) });
    replace('localStorage', { removeItem() {} });
    replace('document', { hidden: false, querySelector: () => ({ content: 'csrf-test' }), createElement: tag => new Element(tag), createDocumentFragment: () => new Element(), addEventListener() {} });
    replace('window', { addEventListener() {} });
    replace('setTimeout', (callback, duration) => { timers.push({ callback, duration }); return timers.length; });
    replace('clearTimeout', () => {});
    replace('fetch', async (url, options) => {
        calls.push({ url, payload: options.body ? JSON.parse(options.body) : null });
        const next = responses.shift();
        if (!next) throw new Error(`Unexpected request ${url}`);
        return { ok: next.status === undefined || next.status < 400, status: next.status || 200, json: async () => next.data };
    });
    t.after(() => { originals.forEach((value, key) => { if (value === undefined) delete globalThis[key]; else globalThis[key] = value; }); });
    mountLiveChat(root);
    return { nodes, calls, timers, stored };
}

const welcome = { id: 1, sender_type: 'admin', sender_name: 'Hỗ trợ', message: 'Xin chào', is_read: true, created_at: '2026-10-09T02:05:00Z' };
const packet = messages => ({ data: { success: true, initialized: true, messages, unread_count: 0, status: 'active', has_more: false } });

test('A failed send stays unconfirmed; retry uses the same UUID and shows one confirmed bubble', async t => {
    const responses = [
        { data: { success: true, initialized: false } },
        packet([welcome]), packet([]),
        { status: 500, data: {} },
        { data: { success: true, message: { id: 3, sender_type: 'customer', message: 'Hỏi giá', created_at: welcome.created_at } } },
    ];
    const { nodes, calls } = fixture(t, responses);
    await flush();
    await nodes['live-chat-toggle-btn'].emit('click');
    nodes['live-chat-input'].value = 'Hỏi giá';
    await nodes['live-chat-form'].emit('submit');
    await flush();
    const pending = nodes['live-chat-messages'].children.at(-1);
    assert.match(pending.className, /is-failed/);
    assert.match(pending.children[2].textContent, /Chưa xác nhận gửi/);
    assert.equal(nodes['chat-error'].hidden, false);
    await pending.children.at(-1).emit('click');
    await flush();
    const attempts = calls.filter(call => call.url === '/send');
    assert.equal(attempts.length, 2);
    assert.equal(attempts[0].payload.request_id, attempts[1].payload.request_id);
    assert.equal(nodes['live-chat-messages'].children.length, 2);
    assert.match(nodes['live-chat-messages'].children.at(-1).children[2].textContent, /Đã gửi/);
});

test('Collapsed restoration retains unread replies and does not send a read acknowledgement', async t => {
    const unread = { ...welcome, id: 2, message: 'Tin chưa đọc', is_read: false };
    const responses = [{ data: { ...packet([welcome, unread]).data, unread_count: 1 } }];
    const { nodes, calls } = fixture(t, responses);
    await flush();
    assert.equal(nodes['chat-unread-badge'].textContent, '1');
    assert.equal(nodes['chat-unread-badge'].hidden, false);
    assert.equal(nodes['live-chat-messages'].children.length, 2);
    assert.equal(calls.length, 1);
    assert.equal(calls[0].url, '/messages');
    assert.equal(nodes['live-chat-box'].hidden, true);
});

test('Sending does not advance the poll cursor past an intervening admin reply', async t => {
    const responses = [{ data: { success: true, initialized: false } }, packet([welcome]), packet([]),
        { data: { success: true, message: { id: 3, sender_type: 'customer', message: 'Khách gửi', created_at: welcome.created_at } } },
        packet([{ ...welcome, id: 2, message: 'Admin trả lời trước' }, { id: 3, sender_type: 'customer', message: 'Khách gửi', created_at: welcome.created_at }]),
    ];
    const { nodes, calls, timers } = fixture(t, responses);
    await flush();
    await nodes['live-chat-toggle-btn'].emit('click');
    nodes['live-chat-input'].value = 'Khách gửi';
    await nodes['live-chat-form'].emit('submit');
    await flush();
    await timers.find(timer => timer.duration === 5000).callback();
    await flush();
    const polls = calls.filter(call => call.url.startsWith('/messages?'));
    assert.equal(polls.at(-1).url, '/messages?after_id=1');
    assert.deepEqual(nodes['live-chat-messages'].children.map(row => row.dataset.messageId), ['1', '2', '3']);
});
