<link rel="stylesheet" href="{{ asset('css/live-chat.css') }}">
<div id="live-chat-widget" class="lc-widget"
    data-init-url="{{ route('chat.init') }}" data-send-url="{{ route('chat.send') }}"
    data-messages-url="{{ route('chat.messages') }}" data-read-url="{{ route('chat.read') }}">
    <button type="button" id="live-chat-toggle-btn" class="lc-trigger" aria-expanded="false" aria-controls="live-chat-box">
        <span aria-hidden="true">✉</span> Hỗ trợ trực tuyến
        <span id="chat-unread-badge" class="lc-badge" hidden aria-label="Tin nhắn chưa đọc"></span>
    </button>
    <section id="live-chat-box" class="lc-box" hidden aria-label="Hỗ trợ khách hàng">
        <header class="lc-header">
            <div><strong>{{ $storefrontSettings['site_name'] }}</strong><p id="chat-session-status">Để lại câu hỏi, nhân viên sẽ phản hồi tại đây.</p></div>
            <button type="button" id="chat-close" aria-label="Thu gọn trò chuyện">×</button>
        </header>
        <div class="lc-contact"><span>Cần hỗ trợ gấp?</span><a href="{{ $storefrontSettings['hotline_url'] }}">{{ $storefrontSettings['hotline'] }}</a><a href="{{ $storefrontSettings['zalo_url'] }}" target="_blank" rel="noopener noreferrer">Zalo</a></div>
        <div id="chat-connection" role="status" class="lc-connection">Đang kết nối…</div>
        <button type="button" id="chat-connect-retry" class="lc-history" hidden>Thử kết nối lại</button>
        <button type="button" id="chat-history" class="lc-history" hidden>Xem tin nhắn trước</button>
        <div id="live-chat-messages" class="lc-messages" role="log" aria-label="Nội dung trò chuyện" aria-live="polite" aria-relevant="additions"></div>
        <div class="lc-suggestions" aria-label="Câu hỏi gợi ý">
            <button type="button" data-chat-suggestion="Tôi cần tư vấn hộp mực phù hợp với máy in.">Tư vấn hộp mực</button>
            <button type="button" data-chat-suggestion="Tôi muốn hỏi giá giấy in mua theo thùng.">Giấy in mua sỉ</button>
            <button type="button" data-chat-suggestion="Tôi cần hỗ trợ về hóa đơn mua hàng.">Hỗ trợ hóa đơn</button>
        </div>
        <form id="live-chat-form" class="lc-form">
            <label class="lc-sr" for="live-chat-input">Nội dung tin nhắn</label>
            <textarea id="live-chat-input" rows="2" required maxlength="2000" placeholder="Nhập câu hỏi của bạn…" aria-describedby="chat-input-help chat-error"></textarea>
            <button type="submit" id="chat-send-btn">Gửi</button>
        </form>
        <p id="chat-error" class="lc-error" role="alert" hidden></p>
        <p id="chat-input-help" class="lc-help">Tối đa 2.000 ký tự. Không gửi mật khẩu hoặc mã OTP.</p>
    </section>
</div>
<script type="module" src="{{ asset('js/live-chat.js') }}"></script>
