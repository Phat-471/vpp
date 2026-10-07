<!-- LIVE CHAT WIDGET (Interactive Mobile & Desktop Support) -->
<div id="live-chat-widget" class="fixed bottom-20 sm:bottom-6 right-4 sm:right-6 z-50">
    
    <!-- Floating Chat Trigger Button -->
    <button
        type="button"
        id="live-chat-toggle-btn"
        onclick="toggleLiveChat()"
        class="group flex items-center space-x-2 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white py-2.5 px-4 sm:py-3 sm:px-5 rounded-full shadow-2xl transition duration-200 transform hover:scale-105 border-2 border-white/80"
        title="Trò chuyện với tư vấn viên"
    >
        <span class="relative flex h-3 w-3">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
        </span>
        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>
        <span class="font-bold text-xs sm:text-sm tracking-wide">Hỗ Trợ 24/7</span>
    </button>

    <!-- Chat Box Window -->
    <div
        id="live-chat-box"
        class="hidden fixed bottom-20 sm:bottom-20 right-4 sm:right-6 w-[92vw] sm:w-96 max-w-sm bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col transition-all duration-200 ease-out z-50 animate-in fade-in slide-in-from-bottom-4"
        style="height: 510px;"
    >
        <!-- Header -->
        <div class="bg-gradient-to-r from-indigo-950 via-indigo-900 to-slate-900 text-white p-4 flex items-center justify-between border-b border-indigo-800">
            <div class="flex items-center space-x-3">
                <div class="relative">
                    <div class="w-10 h-10 rounded-full bg-indigo-800 border-2 border-emerald-400 flex items-center justify-center text-lg">
                        👨‍🔧
                    </div>
                    <span class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-500 border-2 border-indigo-950 rounded-full"></span>
                </div>
                <div>
                    <h4 class="font-black text-sm text-white leading-tight">Tư Vấn Kỹ Thuật & VPP</h4>
                    <span class="text-[11px] text-emerald-400 flex items-center space-x-1 font-semibold">
                        <span>●</span>
                        <span>Đang trực tuyến • Phản hồi ngay</span>
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
                <a href="tel:0901234567" class="text-rose-600 font-bold hover:underline flex items-center space-x-0.5">
                    <span>📞 0901.234.567</span>
                </a>
                <span class="text-slate-300">|</span>
                <a href="https://zalo.me/0901234567" target="_blank" class="text-sky-600 font-bold hover:underline">
                    Zalo
                </a>
            </div>
        </div>

        <!-- Chat Message Area -->
        <div id="live-chat-messages" class="flex-1 overflow-y-auto p-4 space-y-3 bg-slate-50 text-xs">
            
            <!-- Bot Welcome Bubble -->
            <div class="flex items-start space-x-2">
                <div class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs flex-shrink-0 font-bold">
                    VPP
                </div>
                <div class="bg-white p-3 rounded-2xl rounded-tl-none shadow-sm border border-slate-200 text-slate-800 space-y-1.5 max-w-[85%]">
                    <p class="leading-relaxed">
                        Chào bạn! Mình là nhân viên kỹ thuật trực tuyến của cửa hàng VPP & Máy In.
                    </p>
                    <p class="text-slate-500 text-[11px] leading-relaxed">
                        Bạn cần báo giá nạp mực, sửa máy in hay đặt mua văn phòng phẩm số lượng lớn ạ?
                    </p>
                </div>
            </div>

            <!-- Quick Suggestion Topic Chips -->
            <div id="chat-quick-suggestions" class="space-y-1.5 pt-1">
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Gợi ý câu hỏi nhanh:</span>
                <div class="flex flex-wrap gap-1.5">
                    <button
                        type="button"
                        onclick="sendQuickMessage('Báo giá nạp mực và sửa máy in')"
                        class="px-2.5 py-1.5 bg-white hover:bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-xl font-medium text-[11px] shadow-2xs transition"
                    >
                        🖨️ Báo giá nạp mực & sửa máy
                    </button>
                    <button
                        type="button"
                        onclick="sendQuickMessage('Báo giá giấy in giá sỉ cho công ty')"
                        class="px-2.5 py-1.5 bg-white hover:bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-xl font-medium text-[11px] shadow-2xs transition"
                    >
                        📦 Báo giá giấy in theo thùng
                    </button>
                    <button
                        type="button"
                        onclick="sendQuickMessage('Kiểm tra hộp mực cho máy Canon 2900')"
                        class="px-2.5 py-1.5 bg-white hover:bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-xl font-medium text-[11px] shadow-2xs transition"
                    >
                        ⚙️ Hỏi hộp mực Canon 2900
                    </button>
                    <button
                        type="button"
                        onclick="sendQuickMessage('Chính sách giao hàng và xuất hóa đơn VAT')"
                        class="px-2.5 py-1.5 bg-white hover:bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-xl font-medium text-[11px] shadow-2xs transition"
                    >
                        🧾 Xuất hóa đơn VAT & giao hàng
                    </button>
                </div>
            </div>

        </div>

        <!-- Chat Input Form -->
        <form onsubmit="handleUserChatSubmit(event)" class="p-2.5 bg-white border-t border-slate-200 flex items-center space-x-2">
            <input
                type="text"
                id="live-chat-input"
                placeholder="Nhập câu hỏi hoặc SĐT để được gọi lại..."
                class="flex-1 text-xs py-2 px-3.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 bg-slate-50 font-medium text-slate-900"
                autocomplete="off"
            />
            <button
                type="submit"
                class="p-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow-sm transition flex-shrink-0"
                title="Gửi tin nhắn"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
            </button>
        </form>
    </div>
</div>

<script>
    function toggleLiveChat() {
        const box = document.getElementById('live-chat-box');
        if (box) {
            box.classList.toggle('hidden');
            if (!box.classList.contains('hidden')) {
                const input = document.getElementById('live-chat-input');
                if (input) input.focus();
                scrollChatToBottom();
            }
        }
    }

    function scrollChatToBottom() {
        const messages = document.getElementById('live-chat-messages');
        if (messages) {
            messages.scrollTop = messages.scrollHeight;
        }
    }

    function appendMessage(sender, text) {
        const messages = document.getElementById('live-chat-messages');
        if (!messages) return;

        const isUser = sender === 'user';
        const msgDiv = document.createElement('div');
        msgDiv.className = isUser ? 'flex items-start justify-end space-x-2' : 'flex items-start space-x-2';

        if (isUser) {
            msgDiv.innerHTML = `
                <div class="bg-indigo-600 text-white p-3 rounded-2xl rounded-tr-none shadow-sm text-xs max-w-[85%] leading-relaxed">
                    ${escapeHtml(text)}
                </div>
                <div class="w-7 h-7 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center text-xs flex-shrink-0 font-bold">
                    Bạn
                </div>
            `;
        } else {
            msgDiv.innerHTML = `
                <div class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs flex-shrink-0 font-bold">
                    👨‍🔧
                </div>
                <div class="bg-white p-3 rounded-2xl rounded-tl-none shadow-sm border border-slate-200 text-slate-800 text-xs max-w-[85%] leading-relaxed space-y-1">
                    ${text}
                </div>
            `;
        }

        messages.appendChild(msgDiv);
        scrollChatToBottom();
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function sendQuickMessage(text) {
        appendMessage('user', text);
        const suggestions = document.getElementById('chat-quick-suggestions');
        if (suggestions) suggestions.style.display = 'none';
        respondToUser(text);
    }

    function handleUserChatSubmit(e) {
        e.preventDefault();
        const input = document.getElementById('live-chat-input');
        if (!input) return;
        const text = input.value.trim();
        if (!text) return;

        appendMessage('user', text);
        input.value = '';

        const suggestions = document.getElementById('chat-quick-suggestions');
        if (suggestions) suggestions.style.display = 'none';

        respondToUser(text);
    }

    function respondToUser(text) {
        const lower = text.toLowerCase();
        let reply = '';

        if (lower.includes('sua') || lower.includes('sửa') || lower.includes('in') || lower.includes('ket') || lower.includes('kẹt') || lower.includes('nap') || lower.includes('nạp')) {
            reply = `Dạ hiện tại cửa hàng có thợ kỹ thuật kiểm tra máy in trực tiếp tại quầy <b>miễn phí tiền công khám</b>.
                     <br><br>• Nạp mực Laser tận nơi/tại quầy: từ <b>80.000₫</b>
                     <br>• Thay Drum/Trống in: từ <b>120.000₫</b> (Bảo hành 3 tháng)
                     <br><br>Quý khách có thể mang máy đến trực tiếp số 123 Đường VPP hoặc <a href="#dat-lich" class="text-indigo-600 underline font-bold" onclick="toggleLiveChat()">bấm vào đây để đặt thợ trước</a>!`;
        } else if (lower.includes('giay') || lower.includes('giấy') || lower.includes('thung') || lower.includes('thùng') || lower.includes('si') || lower.includes('sỉ')) {
            reply = `Dạ bên mình có đầy đủ giấy in Double A, PaperOne, IK Plus A4/A3 chính hãng chiết khấu cực tốt khi mua theo <b>Thùng</b> (1 Thùng = 5 Ram).
                     <br><br>Đơn từ 2 thùng trở lên cửa hàng miễn phí giao hỏa tốc nội thành và hỗ trợ <b>xuất hóa đơn VAT đầy đủ</b>. Bạn có thể thêm trực tiếp vào giỏ hàng trên web ạ!`;
        } else if (lower.includes('canon') || lower.includes('2900') || lower.includes('hp') || lower.includes('brother') || lower.includes('muc') || lower.includes('mực')) {
            reply = `Dạ hộp mực tương thích cho <b>Canon LBP 2900 / HP 1020</b> mã Cartridge 303/12A có sẵn kho giá chỉ <b>240.000₫/hộp</b> mới 100%, bảo hành in đến hết hạt mực cuối cùng. Hỗ trợ giao tận nơi và lắp đặt miễn phí ạ!`;
        } else if (lower.includes('vat') || lower.includes('hoa don') || lower.includes('hóa đơn') || lower.includes('giao hang') || lower.includes('giao hàng')) {
            reply = `Dạ cửa hàng có đầy đủ hợp đồng cung ứng VPP định kỳ và xuất hóa đơn điện tử VAT chuẩn cho doanh nghiệp, cơ quan trường học.
                     <br><br>Giao hàng hỏa tốc trong 2 giờ nội thành. Bạn vui lòng để lại SĐT hoặc gọi <a href="tel:0901234567" class="text-rose-600 font-bold">0901.234.567</a> để bên mình kết nối gửi báo giá chi tiết nhé!`;
        } else {
            reply = `Cảm ơn quý khách đã nhắn tin! Kỹ thuật viên đã nhận được câu hỏi: <i>"${escapeHtml(text)}"</i>.
                     <br><br>Để được tư vấn ngay lập tức, bạn có thể gọi trực tiếp Hotline/Zalo: <a href="tel:0901234567" class="text-rose-600 font-bold text-sm">0901.234.567</a> (24/7). Rất hân hạnh được phục vụ bạn!`;
        }

        setTimeout(() => {
            appendMessage('bot', reply);
        }, 600);
    }
</script>
