<link rel="stylesheet" href="{{ asset('css/live-chat.css') }}">
<div id="live-chat-widget" class="lc-widget"
    data-init-url="{{ route('chat.init') }}" data-send-url="{{ route('chat.send') }}"
    data-messages-url="{{ route('chat.messages') }}" data-read-url="{{ route('chat.read') }}">
    <button type="button" id="live-chat-toggle-btn" class="lc-trigger" aria-expanded="false" aria-controls="live-chat-box">
        <span class="lc-trigger-icon" aria-hidden="true">💬</span>
        <span class="lc-trigger-text">Hỗ trợ trực tuyến</span>
        <span id="chat-unread-badge" class="lc-badge" hidden aria-label="Tin nhắn chưa đọc"></span>
    </button>
    <section id="live-chat-box" class="lc-box" hidden aria-label="Hỗ trợ khách hàng trực tuyến">
        <header class="lc-header">
            <div class="lc-header-brand">
                <div class="lc-avatar-wrap">
                    <span class="lc-avatar" aria-hidden="true">👨‍💼</span>
                    <span class="lc-online-pulse" title="Trực tuyến 24/7"></span>
                </div>
                <div class="lc-header-titles">
                    <strong>{{ $storefrontSettings['site_name'] }}</strong>
                    <p id="chat-session-status">Phản hồi thường trong 1-3 phút</p>
                </div>
            </div>
            <div class="lc-header-actions">
                <button type="button" id="chat-sound-toggle" class="lc-btn-icon" aria-label="Bật/Tắt chuông âm thanh" title="Bật/Tắt chuông âm thanh">🔔</button>
                <button type="button" id="chat-close" class="lc-btn-icon" aria-label="Thu gọn trò chuyện" title="Thu gọn">✕</button>
            </div>
        </header>
        <div class="lc-contact">
            <span>Cần hỗ trợ gấp?</span>
            <a href="{{ $storefrontSettings['hotline_url'] }}">📞 {{ $storefrontSettings['hotline'] }}</a>
            <a href="{{ $storefrontSettings['zalo_url'] }}" target="_blank" rel="noopener noreferrer">💬 Zalo</a>
        </div>
        <div id="chat-connection" role="status" class="lc-connection">Đang kết nối…</div>
        <button type="button" id="chat-connect-retry" class="lc-history" hidden>Thử kết nối lại</button>
        <button type="button" id="chat-history" class="lc-history" hidden>Xem tin nhắn trước</button>
        
        <div id="live-chat-messages" class="lc-messages" role="log" aria-label="Nội dung trò chuyện" aria-live="polite" aria-relevant="additions"></div>
        <button type="button" id="chat-scroll-bottom" class="lc-scroll-bottom" hidden aria-label="Cuộn xuống tin mới nhất">↓ Tin mới</button>

        <div class="lc-suggestions" aria-label="Câu hỏi gợi ý nhanh">
            <button type="button" data-chat-suggestion="Tôi cần tư vấn hộp mực phù hợp với máy in: ">🖨️ Tư vấn mực máy in</button>
            <button type="button" data-chat-suggestion="Máy in của tôi đang bị lỗi cần sửa chữa: ">🛠️ Báo lỗi máy in</button>
            <button type="button" data-chat-suggestion="Tôi muốn tra cứu tiến độ đơn hàng: ">📦 Tra cứu đơn hàng</button>
            <button type="button" data-chat-suggestion="Tôi muốn hỏi giá giấy in mua theo thùng: ">🚚 Giấy in mua sỉ</button>
            <button type="button" data-chat-suggestion="Tôi cần hỗ trợ xuất hóa đơn VAT cho đơn hàng: ">🧾 Hỗ trợ hóa đơn VAT</button>
        </div>

        <form id="live-chat-form" class="lc-form">
            <label class="lc-sr" for="live-chat-input">Nội dung tin nhắn</label>
            <div class="lc-input-wrapper">
                <textarea id="live-chat-input" rows="2" required maxlength="2000" placeholder="Nhập câu hỏi (Enter để gửi, Shift+Enter xuống dòng)…" aria-describedby="chat-input-help chat-error"></textarea>
                <span id="chat-char-counter" class="lc-char-count">0/2000</span>
            </div>
            <button type="submit" id="chat-send-btn" aria-label="Gửi tin nhắn">
                <span class="lc-send-icon" aria-hidden="true">➤</span>
                <span class="lc-send-text">Gửi</span>
            </button>
        </form>
        <p id="chat-error" class="lc-error" role="alert" hidden></p>
        <p id="chat-input-help" class="lc-help">Tối đa 2.000 ký tự. Không gửi mật khẩu hoặc mã OTP.</p>
    </section>
</div>
<script type="module" src="{{ asset('js/live-chat.js') }}"></script>
