export function mergeMessages(previous, incoming) {
    const unique = new Map(previous.map(message => [message.id, message]));
    incoming.forEach(message => unique.set(message.id, message));
    return [...unique.values()].sort((a, b) => a.id - b.id);
}

export function messageTime(value) {
    const date = new Date(value);
    return Number.isNaN(date.getTime()) ? '' : new Intl.DateTimeFormat('vi-VN', {
        hour: '2-digit', minute: '2-digit', day: '2-digit', month: '2-digit',
        timeZone: 'Asia/Ho_Chi_Minh',
    }).format(date);
}

export function requestError(status, data = {}) {
    if (status === 429) return 'Bạn đang gửi quá nhanh. Hãy chờ một phút rồi thử lại.';
    if (status === 419 || status === 403) return 'Phiên hỗ trợ đã hết hạn. Hãy tải lại trang rồi thử lại.';
    if (status === 422) return Object.values(data.errors || {}).flat()[0] || 'Nội dung chưa hợp lệ. Hãy kiểm tra lại.';
    return 'Chưa thể kết nối. Hãy thử lại hoặc liên hệ qua hotline/Zalo.';
}

export function mountLiveChat(root) {
    const find = id => root.querySelector(`#${id}`);
    const box = find('live-chat-box');
    const toggle = find('live-chat-toggle-btn');
    const list = find('live-chat-messages');
    const input = find('live-chat-input');
    const badge = find('chat-unread-badge');
    const error = find('chat-error');
    const retry = find('chat-connect-retry');
    const history = find('chat-history');
    const send = find('chat-send-btn');
    const soundBtn = find('chat-sound-toggle');
    const scrollBottomBtn = find('chat-scroll-bottom');
    const charCounter = find('chat-char-counter');
    const fileInput = find('live-chat-file-input');
    const previewBar = find('chat-image-preview-bar');
    const previewImg = find('chat-preview-img');
    const previewFilename = find('chat-preview-filename');
    const removeImageBtn = find('chat-remove-image-btn');
    const lightbox = find('live-chat-lightbox');
    const lightboxImg = find('chat-lightbox-img');
    const lightboxClose = find('chat-lightbox-close');
    const lightboxBackdrop = find('chat-lightbox-backdrop');

    let selectedFile = null;
    let previewObjectUrl = null;

    function clearSelectedFile() {
        selectedFile = null;
        if (fileInput) fileInput.value = '';
        if (previewObjectUrl) {
            try { URL.revokeObjectURL(previewObjectUrl); } catch { /* ignore */ }
            previewObjectUrl = null;
        }
        if (previewBar) previewBar.hidden = true;
        if (previewImg) previewImg.src = '';
        if (previewFilename) previewFilename.textContent = '';
    }

    function setSelectedFile(file) {
        if (!file) return;
        const validTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
        if (!validTypes.includes(file.type)) {
            showError('Định dạng tệp không được hỗ trợ. Hãy chọn ảnh JPG, PNG hoặc WEBP.');
            return;
        }
        if (file.size > 5 * 1024 * 1024) {
            showError('Dung lượng ảnh vượt quá 5MB. Hãy chụp lại hoặc giảm kích thước ảnh.');
            return;
        }
        showError('');
        selectedFile = file;
        if (previewObjectUrl) {
            try { URL.revokeObjectURL(previewObjectUrl); } catch { /* ignore */ }
        }
        try {
            previewObjectUrl = URL.createObjectURL(file);
            if (previewImg) previewImg.src = previewObjectUrl;
        } catch { /* object url fallback */ }
        if (previewFilename) previewFilename.textContent = file.name || 'Ảnh đã chọn';
        if (previewBar) previewBar.hidden = false;
        input.focus();
    }

    if (fileInput) {
        fileInput.addEventListener('change', () => {
            if (fileInput.files && fileInput.files[0]) {
                setSelectedFile(fileInput.files[0]);
            }
        });
    }

    if (removeImageBtn) {
        removeImageBtn.addEventListener('click', () => {
            clearSelectedFile();
        });
    }

    function openLightbox(url) {
        if (!lightbox || !lightboxImg) return;
        lightboxImg.src = url;
        lightbox.hidden = false;
    }

    function closeLightbox() {
        if (!lightbox) return;
        lightbox.hidden = true;
        if (lightboxImg) lightboxImg.src = '';
    }

    if (lightboxClose) lightboxClose.addEventListener('click', closeLightbox);
    if (lightboxBackdrop) lightboxBackdrop.addEventListener('click', closeLightbox);
    if (lightbox) {
        lightbox.addEventListener('keydown', e => {
            if (e.key === 'Escape') closeLightbox();
        });
    }

    let messages = [];
    let cursor = 0;
    let oldest = null;
    let initialized = false;
    let initPromise = null;
    let busyPoll = false;
    let busySend = false;
    let busyHistory = false;
    let pending = null;
    let timer = null;
    let scrollTimer = null;
    let busyRead = false;
    let status = 'active';

    const storage = (key, value) => {
        try {
            if (value === undefined) return sessionStorage.getItem(key);
            sessionStorage.setItem(key, value);
        } catch { /* Chat still works when browser storage is unavailable. */ }
        return null;
    };

    // Sound toggle state
    let soundMuted = storage('vpp_chat_sound_muted') === '1';

    const updateSoundUI = () => {
        if (soundBtn) {
            soundBtn.textContent = soundMuted ? '🔕' : '🔔';
            soundBtn.title = soundMuted ? 'Bật chuông âm thanh' : 'Tắt chuông âm thanh';
            soundBtn.setAttribute('aria-label', soundMuted ? 'Bật chuông âm thanh' : 'Tắt chuông âm thanh');
        }
    };

    const playChime = () => {
        if (soundMuted) return;
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            if (ctx.state === 'suspended') ctx.resume();
            const now = ctx.currentTime;
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.type = 'sine';
            osc.frequency.setValueAtTime(587.33, now); // D5
            osc.frequency.exponentialRampToValueAtTime(880, now + 0.12); // A5
            gain.gain.setValueAtTime(0.2, now);
            gain.gain.exponentialRampToValueAtTime(0.001, now + 0.3);
            osc.start(now);
            osc.stop(now + 0.3);
        } catch (e) { /* Audio context gesture blocked or unsupported */ }
    };

    if (soundBtn) {
        updateSoundUI();
        soundBtn.addEventListener('click', () => {
            soundMuted = !soundMuted;
            storage('vpp_chat_sound_muted', soundMuted ? '1' : '0');
            updateSoundUI();
            if (!soundMuted) playChime();
        });
    }

    // Legacy cleanup
    try { localStorage.removeItem('vpp_chat_token'); } catch { /* optional cleanup */ }

    async function api(url, payload = null) {
        const controller = new AbortController();
        const timeout = setTimeout(() => controller.abort(), 12000);
        try {
            const isFormData = typeof FormData !== 'undefined' && payload instanceof FormData;
            const headers = {
                Accept: 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            };
            if (!isFormData) {
                headers['Content-Type'] = 'application/json';
            }
            const response = await fetch(url, {
                method: payload === null ? 'GET' : 'POST', credentials: 'same-origin', cache: 'no-store',
                headers,
                body: payload === null ? undefined : (isFormData ? payload : JSON.stringify(payload)),
                signal: controller.signal,
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok || !data.success) throw new Error(requestError(response.status, data));
            return data;
        } catch (failure) {
            if (failure.name === 'AbortError' || failure instanceof TypeError) {
                throw new Error('Chưa nhận được xác nhận gửi. Hãy thử lại để kiểm tra và gửi tiếp.');
            }
            throw failure;
        } finally { clearTimeout(timeout); }
    }

    function showError(text = '') {
        error.textContent = text;
        error.hidden = !text;
    }

    function updateUnread(count) {
        badge.textContent = count > 99 ? '99+' : String(count);
        badge.hidden = count === 0;
        toggle.setAttribute('aria-label', count ? `Hỗ trợ trực tuyến, ${count} tin chưa đọc` : 'Hỗ trợ trực tuyến');
    }

    function updateStatus(value) {
        status = value || status;
        find('chat-session-status').textContent = status === 'closed'
            ? 'Hội thoại đã đóng. Gửi tin mới để tiếp tục hỗ trợ.'
            : 'Phản hồi thường trong 1-3 phút';
    }

    function nearBottom() { return list.scrollHeight - list.scrollTop - list.clientHeight < 80; }

    function render(forceBottom = false) {
        const bottom = nearBottom();
        const top = list.scrollTop;
        const fragment = document.createDocumentFragment();
        for (const message of [...messages, ...(pending ? [pending] : [])]) {
            const row = document.createElement('article');
            row.className = `lc-message${message.sender_type === 'customer' ? ' is-customer' : ''}${message.failed ? ' is-failed' : ''}`;
            if (message.id) row.dataset.messageId = String(message.id);
            const author = document.createElement('strong');
            author.textContent = message.sender_type === 'customer' ? 'Bạn' : message.sender_name;
            row.append(author);

            if (message.image_url) {
                const imgWrap = document.createElement('div');
                imgWrap.className = 'lc-message-image-wrap';
                const img = document.createElement('img');
                img.src = message.image_url;
                img.alt = 'Ảnh đính kèm';
                img.className = 'lc-message-image';
                img.loading = 'lazy';
                img.addEventListener('click', () => openLightbox(message.image_url));
                imgWrap.append(img);
                row.append(imgWrap);
            }

            if (message.message && message.message !== '[Hình ảnh đính kèm]') {
                const text = document.createElement('p');
                text.textContent = message.message;
                row.append(text);
            }

            const meta = document.createElement('span');
            meta.className = 'lc-meta';
            meta.textContent = `${messageTime(message.created_at)}${message.request_id ? (message.failed ? ' · Chưa xác nhận gửi' : ' · Đang gửi…') : message.sender_type === 'customer' ? ' · Đã gửi' : ''}`;
            row.append(meta);
            if (message.failed) {
                const again = document.createElement('button');
                again.type = 'button';
                again.textContent = 'Thử lại';
                again.disabled = busySend;
                again.addEventListener('click', () => submitPending());
                row.append(again);
            }
            fragment.append(row);
        }

        // Typing indicator when pending or busy
        if (busySend) {
            const typing = document.createElement('div');
            typing.className = 'lc-typing';
            typing.setAttribute('aria-label', 'Đang gửi tin nhắn…');
            typing.innerHTML = '<span class="lc-typing-dot"></span><span class="lc-typing-dot"></span><span class="lc-typing-dot"></span>';
            fragment.append(typing);
        }

        list.replaceChildren(fragment);
        list.scrollTop = forceBottom || bottom ? list.scrollHeight : top;
    }

    async function acknowledge() {
        if (box.hidden || document.hidden || !messages.length || busyRead) return;
        const bounds = list.getBoundingClientRect();
        const ids = [...list.querySelectorAll('[data-message-id]')].filter(row => {
            const rect = row.getBoundingClientRect();
            return rect.bottom > bounds.top && rect.top < bounds.bottom;
        }).map(row => Number(row.dataset.messageId));
        if (!ids.length) return;
        const first = Math.min(...ids);
        const last = Math.max(...ids);
        if (!messages.some(row => row.id >= first && row.id <= last && row.sender_type === 'admin' && !row.is_read)) return;
        busyRead = true;
        try {
            const data = await api(root.dataset.readUrl, { from_id: first, through_id: last });
            messages.forEach(row => { if (row.sender_type === 'admin' && row.id >= first && row.id <= last) row.is_read = true; });
            updateUnread(data.unread_count);
        } finally { busyRead = false; }
    }

    async function initialize() {
        if (initialized) return;
        if (initPromise) return initPromise;
        initPromise = (async () => {
            const data = await api(root.dataset.initUrl, {});
            messages = mergeMessages(messages, data.messages);
            cursor = messages.at(-1)?.id || 0;
            oldest = messages[0]?.id || null;
            history.hidden = !data.has_more;
            initialized = true;
            updateUnread(data.unread_count);
            updateStatus(data.status);
            find('chat-connection').textContent = 'Đã kết nối hệ thống hỗ trợ 24/7';
            retry.hidden = true;
            render(true);
            await acknowledge();
        })().finally(() => { initPromise = null; });
        return initPromise;
    }

    async function poll() {
        if (busyPoll || document.hidden || !initialized) return;
        busyPoll = true;
        try {
            const data = await api(`${root.dataset.messagesUrl}?after_id=${cursor}`);
            if (data.initialized === false) {
                initialized = false;
                throw new Error('Phiên hỗ trợ đã hết hạn. Hãy tải lại trang để kết nối lại.');
            }

            // Check if there are new admin messages to play audio chime
            if (data.messages && data.messages.length > 0) {
                const hasNewAdminMsg = data.messages.some(m => m.sender_type === 'admin' && !messages.some(old => old.id === m.id));
                if (hasNewAdminMsg) {
                    playChime();
                }
            }

            messages = mergeMessages(messages, data.messages);
            if (data.messages.length) cursor = data.messages.at(-1).id;
            updateStatus(data.status);
            updateUnread(data.unread_count);
            if (data.messages.length) render();
            await acknowledge();
            find('chat-connection').textContent = 'Đã kết nối hệ thống hỗ trợ 24/7';
            retry.hidden = true;
            if (data.has_more) queueMicrotask(() => poll());
        } catch (failure) {
            find('chat-connection').textContent = failure.message;
            retry.hidden = false;
        } finally { busyPoll = false; }
    }

    function schedule() {
        clearTimeout(timer);
        timer = setTimeout(async () => { await poll(); schedule(); }, box.hidden ? 15000 : 5000);
    }

    async function setOpen(open) {
        box.hidden = !open;
        toggle.hidden = open;
        toggle.setAttribute('aria-expanded', String(open));
        storage('vpp_chat_open', open ? '1' : '0');
        if (!open) { toggle.focus(); schedule(); return; }
        input.focus();
        try {
            await initialize();
            await poll();
            render(true);
        } catch (failure) { find('chat-connection').textContent = failure.message; retry.hidden = false; }
        schedule();
    }

    async function submitPending() {
        if (!pending || busySend) return;
        busySend = true;
        send.disabled = true;
        pending.failed = false;
        showError();
        render(true);
        try {
            await initialize();
            let payload;
            if (pending.file_payload) {
                payload = new FormData();
                payload.append('request_id', pending.request_id);
                payload.append('message', pending.message || '');
                payload.append('image', pending.file_payload);
            } else {
                payload = { message: pending.message, request_id: pending.request_id };
            }
            const data = await api(root.dataset.sendUrl, payload);
            messages = mergeMessages(messages, [data.message]);
            pending = null;
            updateStatus('active');
        } catch (failure) { pending.failed = true; showError(failure.message); }
        finally { busySend = false; send.disabled = false; render(true); }
    }

    find('live-chat-form').addEventListener('submit', event => {
        event.preventDefault();
        if (pending) { showError('Tin trước chưa được xác nhận. Hãy bấm Thử lại trước khi gửi tin tiếp theo.'); return; }
        const text = input.value.trim();
        const file = selectedFile;
        if (!text && !file) {
            showError('Nhập câu hỏi hoặc chọn ảnh máy in bị lỗi trước khi gửi.');
            return;
        }
        if (text && ([...text].length > 2000 || /[\u0000-\u0008\u000B\u000C\u000E-\u001F\u007F]/u.test(text))) {
            showError('Nhập tin nhắn hợp lệ, tối đa 2.000 ký tự.');
            return;
        }
        let localImgUrl = null;
        if (file) {
            try { localImgUrl = URL.createObjectURL(file); } catch { /* ignore */ }
        }
        pending = {
            request_id: crypto.randomUUID(),
            message: text || '[Hình ảnh đính kèm]',
            image_url: localImgUrl,
            file_payload: file,
            sender_type: 'customer',
            created_at: new Date().toISOString()
        };
        input.value = '';
        if (charCounter) charCounter.textContent = '0/2000';
        clearSelectedFile();
        submitPending();
    });

    // Paste image from clipboard directly into the chat input
    input.addEventListener('paste', event => {
        const items = event.clipboardData?.items;
        if (!items) return;
        for (const item of items) {
            if (item.type && item.type.startsWith('image/')) {
                const file = item.getAsFile();
                if (file) {
                    setSelectedFile(file);
                    event.preventDefault();
                    break;
                }
            }
        }
    });

    // Enter to submit, Shift+Enter for newline
    input.addEventListener('keydown', event => {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            find('live-chat-form').requestSubmit();
        }
    });

    // Character counter
    input.addEventListener('input', () => {
        if (charCounter) {
            charCounter.textContent = `${input.value.length}/2000`;
        }
    });

    toggle.addEventListener('click', () => setOpen(box.hidden));
    find('chat-close').addEventListener('click', () => setOpen(false));
    box.addEventListener('keydown', event => { if (event.key === 'Escape') { event.preventDefault(); setOpen(false); } });

    // Suggestion chips fill composer and focus
    root.querySelectorAll('[data-chat-suggestion]').forEach(button => button.addEventListener('click', () => {
        input.value = button.dataset.chatSuggestion;
        if (charCounter) charCounter.textContent = `${input.value.length}/2000`;
        input.focus();
    }));

    retry.addEventListener('click', async () => {
        retry.disabled = true;
        try { await initialize(); await poll(); }
        catch (failure) { find('chat-connection').textContent = failure.message; }
        finally { retry.disabled = false; }
    });

    history.addEventListener('click', async () => {
        if (!oldest || busyHistory) return;
        busyHistory = true;
        history.disabled = true;
        const oldHeight = list.scrollHeight;
        const top = list.scrollTop;
        try {
            const data = await api(`${root.dataset.messagesUrl}?before_id=${oldest}`);
            messages = mergeMessages(messages, data.messages);
            oldest = messages[0]?.id || oldest;
            history.hidden = !data.has_more;
            render();
            list.scrollTop = top + list.scrollHeight - oldHeight;
            await acknowledge();
        } catch (failure) { showError(failure.message); }
        finally { busyHistory = false; history.disabled = false; }
    });

    document.addEventListener('visibilitychange', () => { if (!document.hidden) { poll(); schedule(); } });

    list.addEventListener('scroll', () => {
        clearTimeout(scrollTimer);
        scrollTimer = setTimeout(() => acknowledge().catch(() => {}), 500);

        if (scrollBottomBtn) {
            scrollBottomBtn.hidden = nearBottom();
        }
    });

    if (scrollBottomBtn) {
        scrollBottomBtn.addEventListener('click', () => {
            list.scrollTo({ top: list.scrollHeight, behavior: 'smooth' });
            scrollBottomBtn.hidden = true;
        });
    }

    window.addEventListener('pagehide', () => { clearTimeout(timer); clearTimeout(scrollTimer); });
    window.addEventListener('pageshow', () => schedule());

    if (storage('vpp_chat_open') === '1') {
        setOpen(true);
    } else {
        // Read-only restore: no new conversation and no read receipt merely from visiting a page.
        api(root.dataset.messagesUrl).then(data => {
            if (!data.initialized) return;
            initialized = true;
            messages = data.messages;
            cursor = messages.at(-1)?.id || 0;
            oldest = messages[0]?.id || null;
            history.hidden = !data.has_more;
            updateUnread(data.unread_count);
            updateStatus(data.status);
            render();
            schedule();
        }).catch(() => { /* Opening the widget provides a visible retry. */ });
    }
}

if (typeof document !== 'undefined') {
    const widget = document.getElementById('live-chat-widget');
    if (widget) mountLiveChat(widget);
}
