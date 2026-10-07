<!-- LIVE CHAT WIDGET (Interactive 2-Way Real-Time Support) -->
<div id="live-chat-widget" class="fixed bottom-20 sm:bottom-6 right-4 sm:right-6 z-50">
    
    <!-- Floating Chat Trigger Button -->
    <button
        type="button"
        id="live-chat-toggle-btn"
        onclick="toggleLiveChat()"
        class="group flex items-center space-x-2 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white py-2.5 px-4 sm:py-3 sm:px-5 rounded-full shadow-2xl transition duration-200 transform hover:scale-105 border-2 border-white/80"
        title="Trò chuyện trực tuyến với CSKH"
    >
        <span class="relative flex h-3 w-3">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
        </span>
        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>
        <span class="font-bold text-xs sm:text-sm tracking-wide">Live Chat CSKH</span>
        <span id="chat-unread-badge" class="hidden bg-rose-500 text-white text-[10px] font-bold px-1.5 py-0.2 rounded-full ml-1">1</span>
    </button>

    <!-- Chat Box Window -->
    <div
        id="live-chat-box"
        class="hidden fixed bottom-20 sm:bottom-20 right-4 sm:right-6 w-[92vw] sm:w-96 max-w-sm bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col transition-all duration-200 ease-out z-50 animate-in fade-in slide-in-from-bottom-4"
        style="height: 520px;"
    >
        <!-- Header -->
        <div class="bg-gradient-to-r from-indigo-950 via-indigo-900 to-slate-900 text-white p-4 flex items-center justify-between border-b border-indigo-800">
            <div class="flex items-center space-x-3">
                <div class="relative">
                    <div class="w-10 h-10 rounded-full bg-indigo-800 border-2 border-emerald-400 flex items-center justify-center text-lg">
                        👨‍💼
                    </div>
                    <span class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-500 border-2 border-indigo-950 rounded-full"></span>
                </div>
                <div>
                    <h4 class="font-black text-sm text-white leading-tight">CSKH VPP Ánh Dương</h4>
                    <span class="text-[11px] text-emerald-400 flex items-center space-x-1 font-semibold">
                        <span>●</span>
                        <span>Nhân viên đang trực tuyến phản hồi</span>
                    </span>
                </div>
            </div>
            <button
                type="button"
                onclick="toggleLiveChat()"
                class="text-slate-300 hover:text-white p-1 rounded-lg hover:bg-white/10 transition"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Direct Connect Quick Bar -->
        <div class="bg-indigo-50 px-3.5 py-2 border-b border-indigo-100 flex items-center justify-between text-xs">
            <span class="text-indigo-900 font-semibold text-[11px]">Cần gấp? Liên hệ ngay:</span>
            <div class="flex items-center space-x-2">
                <a href="tel:{{ setting('hotline', '1900 6868') }}" class="text-rose-600 font-bold hover:underline flex items-center space-x-0.5">
                    <span>📞 {{ setting('hotline', '1900 6868') }}</span>
                </a>
                <span class="text-slate-300">|</span>
                <a href="https://zalo.me/{{ preg_replace('/\D/', '', setting('zalo', '0988123456')) }}" target="_blank" class="text-sky-600 font-bold hover:underline">
                    Zalo
                </a>
            </div>
        </div>

        <!-- Chat Message Area -->
        <div id="live-chat-messages" class="flex-1 overflow-y-auto p-4 space-y-3 bg-slate-50 text-xs">
            <!-- Loading indicator -->
            <div id="chat-loading" class="text-center text-slate-400 py-4 text-[11px]">
                Đang kết nối hệ thống hỗ trợ...
            </div>
        </div>

        <!-- Quick Suggestions -->
        <div id="chat-quick-suggestions" class="p-2 bg-slate-100/70 border-t border-slate-200 flex gap-1.5 overflow-x-auto text-[11px] whitespace-nowrap">
            <button type="button" onclick="sendQuickMessage('Báo giá nạp mực máy in tận nơi')" class="px-2 py-1 bg-white hover:bg-indigo-50 text-indigo-700 rounded-lg border border-slate-200">🖨️ Nạp mực tận nơi</button>
            <button type="button" onclick="sendQuickMessage('Báo giá giấy in mua theo thùng')" class="px-2 py-1 bg-white hover:bg-indigo-50 text-indigo-700 rounded-lg border border-slate-200">📦 Giấy in sỉ</button>
            <button type="button" onclick="sendQuickMessage('Chính sách xuất hóa đơn GTGT (VAT)')" class="px-2 py-1 bg-white hover:bg-indigo-50 text-indigo-700 rounded-lg border border-slate-200">🧾 Xuất hóa đơn VAT</button>
        </div>

        <!-- Chat Input Form -->
        <form onsubmit="handleUserChatSubmit(event)" class="p-2.5 bg-white border-t border-slate-200 flex items-center space-x-2">
            <input
                type="text"
                id="live-chat-input"
                placeholder="Nhập câu hỏi để nhân viên trả lời..."
                class="flex-1 text-xs py-2.5 px-3.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 bg-slate-50 font-medium text-slate-900"
                autocomplete="off"
            />
            <button
                type="submit"
                id="chat-send-btn"
                class="p-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow-sm transition flex-shrink-0"
                title="Gửi tin nhắn"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
            </button>
        </form>
    </div>
</div>

<script>
    let chatToken = localStorage.getItem('vpp_chat_token') || '';
    let lastMsgId = 0;
    let chatPollInterval = null;
    let isChatInitialized = false;

    const currentCustomerName = "{{ auth('customer')->check() ? auth('customer')->user()->name : '' }}";
    const currentCustomerPhone = "{{ auth('customer')->check() ? auth('customer')->user()->phone : '' }}";

    function toggleLiveChat() {
        const box = document.getElementById('live-chat-box');
        if (!box) return;

        box.classList.toggle('hidden');
        if (!box.classList.contains('hidden')) {
            const input = document.getElementById('live-chat-input');
            if (input) input.focus();
            
            if (!isChatInitialized) {
                initChatSession();
            }
            scrollChatToBottom();
            startPolling();

            // Hide unread badge
            const badge = document.getElementById('chat-unread-badge');
            if (badge) badge.classList.add('hidden');
        } else {
            stopPolling();
        }
    }

    async function initChatSession() {
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const res = await fetch("{{ route('chat.init') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || ''
                },
                body: JSON.stringify({
                    session_token: chatToken,
                    customer_name: currentCustomerName,
                    customer_phone: currentCustomerPhone
                })
            });

            const data = await res.json();
            if (data.success) {
                chatToken = data.session_token;
                localStorage.setItem('vpp_chat_token', chatToken);
                isChatInitialized = true;

                const msgContainer = document.getElementById('live-chat-messages');
                const loading = document.getElementById('chat-loading');
                if (loading) loading.remove();

                msgContainer.innerHTML = '';
                (data.messages || []).forEach(m => {
                    renderMessageBubble(m.sender_type, m.message, m.sender_name, m.created_at);
                    if (m.id > lastMsgId) lastMsgId = m.id;
                });
                scrollChatToBottom();
            }
        } catch (e) {
            console.error('Chat init error:', e);
            const loading = document.getElementById('chat-loading');
            if (loading) loading.innerText = 'Chưa thể kết nối máy chủ chat. Vui lòng gọi Hotline!';
        }
    }

    async function handleUserChatSubmit(e) {
        e.preventDefault();
        const input = document.getElementById('live-chat-input');
        if (!input) return;
        const text = input.value.trim();
        if (!text) return;

        if (!chatToken) {
            await initChatSession();
        }

        renderMessageBubble('customer', text, 'Bạn');
        input.value = '';
        scrollChatToBottom();

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const res = await fetch("{{ route('chat.send') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || ''
                },
                body: JSON.stringify({
                    session_token: chatToken,
                    message: text,
                    customer_name: currentCustomerName,
                    customer_phone: currentCustomerPhone
                })
            });
            const data = await res.json();
            if (data.success && data.message) {
                if (data.message.id > lastMsgId) lastMsgId = data.message.id;
            }
        } catch (err) {
            console.error('Error sending message:', err);
        }
    }

    function sendQuickMessage(text) {
        const input = document.getElementById('live-chat-input');
        if (input) {
            input.value = text;
            const form = input.closest('form');
            if (form) form.dispatchEvent(new Event('submit'));
        }
    }

    async function checkNewMessages() {
        if (!chatToken) return;

        try {
            const res = await fetch(`{{ route('chat.messages') }}?session_token=${encodeURIComponent(chatToken)}&after_id=${lastMsgId}`);
            const data = await res.json();
            if (data.success && data.messages && data.messages.length > 0) {
                let hasAdminMsg = false;
                data.messages.forEach(m => {
                    if (m.id > lastMsgId) {
                        lastMsgId = m.id;
                        renderMessageBubble(m.sender_type, m.message, m.sender_name, m.created_at);
                        if (m.sender_type === 'admin') hasAdminMsg = true;
                    }
                });
                scrollChatToBottom();

                if (hasAdminMsg) {
                    const box = document.getElementById('live-chat-box');
                    if (box && box.classList.contains('hidden')) {
                        const badge = document.getElementById('chat-unread-badge');
                        if (badge) badge.classList.remove('hidden');
                    }
                }
            }
        } catch (err) {
            // Silently retry next poll
        }
    }

    function renderMessageBubble(senderType, text, senderName, time) {
        const messages = document.getElementById('live-chat-messages');
        if (!messages) return;

        const isUser = senderType === 'customer' || senderType === 'user';
        const msgDiv = document.createElement('div');
        msgDiv.className = isUser ? 'flex items-start justify-end space-x-2' : 'flex items-start space-x-2';

        const safeText = escapeHtml(text).replace(/\n/g, '<br>');

        if (isUser) {
            msgDiv.innerHTML = `
                <div class="bg-indigo-600 text-white p-3 rounded-2xl rounded-tr-none shadow-xs text-xs max-w-[85%] leading-relaxed">
                    ${safeText}
                </div>
                <div class="w-7 h-7 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center text-xs flex-shrink-0 font-bold">
                    Bạn
                </div>
            `;
        } else {
            msgDiv.innerHTML = `
                <div class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs flex-shrink-0 font-bold">
                    👨‍💼
                </div>
                <div class="bg-white p-3 rounded-2xl rounded-tl-none shadow-xs border border-slate-200 text-slate-800 text-xs max-w-[85%] leading-relaxed space-y-1">
                    <p class="font-bold text-[10px] text-indigo-600">${escapeHtml(senderName || 'CSKH Ánh Dương')}</p>
                    <p>${safeText}</p>
                </div>
            `;
        }

        messages.appendChild(msgDiv);
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function scrollChatToBottom() {
        const messages = document.getElementById('live-chat-messages');
        if (messages) {
            messages.scrollTop = messages.scrollHeight;
        }
    }

    function startPolling() {
        if (!chatPollInterval) {
            chatPollInterval = setInterval(checkNewMessages, 3500);
        }
    }

    function stopPolling() {
        if (chatPollInterval) {
            clearInterval(chatPollInterval);
            chatPollInterval = null;
        }
    }

    // Auto-init on page load if user already chatted before
    document.addEventListener('DOMContentLoaded', () => {
        if (chatToken) {
            // Check for unread messages quietly in background
            checkNewMessages();
        }
    });
</script>
