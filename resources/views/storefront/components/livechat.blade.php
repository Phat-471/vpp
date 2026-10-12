<link rel="stylesheet" href="{{ asset('css/live-chat.css') }}?v={{ filemtime(public_path('css/live-chat.css')) }}">
<div id="live-chat-widget" class="lc-widget"
    data-init-url="{{ route('chat.init') }}" data-send-url="{{ route('chat.send') }}"
    data-messages-url="{{ route('chat.messages') }}" data-read-url="{{ route('chat.read') }}">
    <button type="button" id="live-chat-toggle-btn" class="lc-trigger" aria-expanded="false" aria-controls="live-chat-box" aria-label="Hỗ trợ trực tuyến">
        <span class="lc-pulse" aria-hidden="true"></span>
        <span class="lc-trigger-icon" aria-hidden="true">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
        </span>
        <span class="lc-trigger-text">Hỗ trợ trực tuyến</span>
        <span id="chat-unread-badge" class="lc-badge" hidden aria-label="Tin nhắn chưa đọc"></span>
    </button>
    <section id="live-chat-box" class="lc-box" hidden aria-label="Hỗ trợ khách hàng trực tuyến">
        <header class="lc-header">
            <div class="lc-header-brand">
                <div class="lc-avatar-wrap">
                    <span class="lc-avatar" aria-hidden="true">👨‍💼</span>
                    <span class="lc-online-pulse" title="Kênh hỗ trợ"></span>
                </div>
                <div class="lc-header-titles">
                    <strong>{{ $storefrontSettings['site_name'] }}</strong>
                    <p id="chat-session-status">Nhắn tin cho cửa hàng</p>
                </div>
            </div>
            <div class="lc-header-actions">
                <button type="button" id="chat-sound-toggle" class="lc-btn-icon" aria-label="Bật/Tắt chuông âm thanh" title="Bật/Tắt chuông âm thanh">🔔</button>
                <button type="button" id="chat-close" class="lc-btn-icon" aria-label="Thu gọn trò chuyện" title="Thu gọn">✕</button>
            </div>
        </header>
        <div class="lc-contact">
            <span>Liên hệ nhanh</span>
            <a href="{{ $storefrontSettings['hotline_url'] }}">📞 {{ $storefrontSettings['hotline'] }}</a>
            <a href="{{ $storefrontSettings['zalo_url'] }}" target="_blank" rel="noopener noreferrer">💬 Zalo</a>
        </div>
        <div id="chat-connection" role="status" class="lc-connection">Đang kết nối…</div>
        <button type="button" id="chat-connect-retry" class="lc-history" hidden>Thử kết nối lại</button>
        <button type="button" id="chat-history" class="lc-history" hidden>Xem tin cũ</button>
        
        <div id="live-chat-messages" class="lc-messages" role="log" aria-label="Nội dung trò chuyện" aria-live="polite" aria-relevant="additions"></div>
        <button type="button" id="chat-scroll-bottom" class="lc-scroll-bottom" hidden aria-label="Cuộn xuống tin mới nhất">↓ Tin mới</button>

        <div class="lc-suggestions" aria-label="Câu hỏi gợi ý nhanh">
            <button type="button" data-chat-suggestion="Tôi cần tư vấn hộp mực cho máy in: ">🖨️ Chọn hộp mực</button>
            <button type="button" data-chat-suggestion="Máy in của tôi đang bị lỗi: ">🛠️ Báo lỗi máy in</button>
            <button type="button" data-chat-suggestion="Tôi muốn tra cứu đơn hàng: ">📦 Tra cứu đơn</button>
            <button type="button" data-chat-suggestion="Tôi muốn hỏi giá giấy in mua theo thùng: ">🚚 Giá giấy mua sỉ</button>
            <button type="button" data-chat-suggestion="Tôi cần hỗ trợ hóa đơn VAT: ">🧾 Hỏi về hóa đơn</button>
        </div>

        <!-- Thanh xem trước ảnh đính kèm trước khi gửi -->
        <div id="chat-image-preview-bar" class="lc-image-preview-bar" hidden>
            <div class="lc-preview-thumb-wrap">
                <img id="chat-preview-img" src="" alt="Ảnh xem trước" class="lc-preview-img">
                <button type="button" id="chat-remove-image-btn" class="lc-preview-remove" aria-label="Hủy ảnh đã chọn" title="Xóa ảnh">✕</button>
            </div>
            <div class="lc-preview-info">
                <span id="chat-preview-filename" class="lc-preview-filename"></span>
                <span class="lc-preview-hint">Đã chọn ảnh máy in / bản in</span>
            </div>
        </div>

        <form id="live-chat-form" class="lc-form">
            <label class="lc-sr" for="live-chat-input">Nội dung tin nhắn</label>
            <label for="live-chat-file-input" class="lc-attach-btn" title="Đính kèm ảnh bản in / lỗi máy in (Tối đa 5MB)" aria-label="Đính kèm ảnh máy in lỗi">
                <input type="file" id="live-chat-file-input" accept="image/jpeg,image/png,image/webp,image/jpg" class="lc-sr">
                <span class="lc-attach-icon" aria-hidden="true">📷</span>
            </label>
            <div class="lc-input-wrapper">
                <textarea id="live-chat-input" rows="2" maxlength="2000" placeholder="Nhập câu hỏi hoặc gửi ảnh máy in bị lỗi…" aria-describedby="chat-input-help chat-error"></textarea>
                <span id="chat-char-counter" class="lc-char-count">0/2000</span>
            </div>
            <button type="submit" id="chat-send-btn" aria-label="Gửi tin nhắn">
                <span class="lc-send-icon" aria-hidden="true">➤</span>
                <span class="lc-send-text">Gửi</span>
            </button>
        </form>
        <p id="chat-error" class="lc-error" role="alert" hidden></p>
        <p id="chat-input-help" class="lc-help">Hỗ trợ gửi ảnh chụp bản in lỗi, máy in kẹt giấy (Tối đa 5MB). Không gửi mật khẩu hoặc OTP.</p>
    </section>

    <!-- Modal phóng to ảnh trong chat (Lightbox) -->
    <div id="live-chat-lightbox" class="lc-lightbox" hidden aria-label="Xem ảnh phóng to" role="dialog" aria-modal="true">
        <div class="lc-lightbox-backdrop" id="chat-lightbox-backdrop"></div>
        <div class="lc-lightbox-box">
            <button type="button" id="chat-lightbox-close" class="lc-lightbox-close" aria-label="Đóng xem ảnh" title="Đóng">✕</button>
            <img id="chat-lightbox-img" src="" alt="Ảnh chi tiết máy in" class="lc-lightbox-img">
        </div>
    </div>
</div>
<script type="module" src="{{ asset('js/live-chat.js') }}"></script>
