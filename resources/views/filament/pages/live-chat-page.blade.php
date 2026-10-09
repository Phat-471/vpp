<x-filament-panels::page>
    <div wire:poll.visible.5s="refreshSelected" class="chat-desk">
        <aside class="chat-inbox">
            <h2>Hội thoại khách hàng <span>{{ $this->sessions->total() }}</span></h2>
            <label for="chat-search">Tìm hội thoại</label>
            <input id="chat-search" type="search" wire:model.live.debounce.400ms="search" maxlength="100" placeholder="Tên, số điện thoại, nội dung…">
            <label for="chat-filter">Trạng thái</label>
            <select id="chat-filter" wire:model.live="filter">
                <option value="all">Tất cả</option><option value="unread">Chưa đọc</option>
                <option value="active">Đang mở</option><option value="closed">Đã đóng</option>
            </select>
            <div class="chat-session-list">
                @forelse($this->sessions as $session)
                    <button type="button" wire:key="chat-session-{{ $session->id }}" wire:click="selectSession({{ $session->id }})" class="chat-session {{ $selectedSessionId === $session->id ? 'is-selected' : '' }}" aria-pressed="{{ $selectedSessionId === $session->id ? 'true' : 'false' }}">
                        <span class="chat-session-heading"><strong>{{ $session->customer_name }}</strong><span>{{ $session->unread_admin > 0 ? $session->unread_admin.' chưa đọc' : '' }}</span></span>
                        @if($session->customer_phone)<span>{{ $session->customer_phone }}</span>@endif
                        <span class="chat-preview">{{ $session->last_message ?? 'Chưa có yêu cầu hỗ trợ' }}</span>
                        <small>{{ $session->last_message_at?->format('H:i, d/m/Y') }} · {{ $session->status === 'active' ? 'Đang mở' : 'Đã đóng' }}</small>
                    </button>
                @empty
                    <p class="chat-empty">Không tìm thấy hội thoại. Hãy thử bộ lọc khác.</p>
                @endforelse
            </div>
            <div class="chat-pagination">{{ $this->sessions->links() }}</div>
        </aside>
        <section class="chat-conversation" aria-label="Chi tiết hội thoại">
            @if($this->currentSession)
                <header class="chat-desk-header">
                    <div><h2>{{ $this->currentSession->customer_name }}</h2><p>{{ $this->currentSession->customer_phone ?? 'Chưa cung cấp số điện thoại' }} · {{ $this->currentSession->status === 'active' ? 'Đang mở' : 'Đã đóng' }}</p></div>
                    <div>
                        @if($this->currentSession->unread_admin > 0)
                            <button type="button" wire:click="markSelectedRead" wire:loading.attr="disabled" wire:target="markSelectedRead">{{ $this->currentSession->unread_admin }} tin chưa đọc · Đánh dấu đã đọc</button>
                        @endif
                        <button type="button" wire:click="toggleSessionStatus" wire:loading.attr="disabled" wire:target="toggleSessionStatus" wire:confirm="Xác nhận thay đổi trạng thái hội thoại?">{{ $this->currentSession->status === 'active' ? 'Đóng hội thoại' : 'Mở lại hội thoại' }}</button>
                    </div>
                </header>
                <div class="chat-pagination">{{ $this->messages->links() }}<small>Trang 1 là nhóm tin mới nhất.</small></div>
                <div id="admin-chat-messages" class="chat-desk-messages" role="log" aria-label="Tin nhắn" aria-live="polite">
                    @forelse($this->messages->getCollection()->reverse() as $msg)
                        <article wire:key="chat-message-{{ $msg->id }}" class="chat-desk-message {{ $msg->sender_type === 'admin' ? 'from-admin' : '' }}">
                            <strong>{{ $msg->sender_name }}</strong><p>{{ $msg->message }}</p>
                            <small>{{ $msg->created_at->format('H:i, d/m/Y') }} @if($msg->sender_type === 'admin') · {{ $msg->is_read ? 'Đã đọc' : 'Đã gửi' }} @endif</small>
                        </article>
                    @empty
                        <p class="chat-empty">Chưa có tin nhắn trong hội thoại này.</p>
                    @endforelse
                </div>
                <div class="chat-quick-replies">
                    <label for="chat-quick-reply">Câu trả lời mẫu</label>
                    <select id="chat-quick-reply" wire:change="useQuickReply($event.target.value)">
                        <option value="" disabled selected>Chọn mẫu để điền, kiểm tra rồi gửi</option>
                        @foreach($this->quickReplies as $index => $reply)<option value="{{ $index }}">{{ $reply }}</option>@endforeach
                    </select>
                </div>
                <form wire:submit="sendReply" class="chat-reply-form">
                    <label for="admin-chat-reply">Nội dung trả lời</label>
                    <textarea id="admin-chat-reply" wire:model="replyMessage" rows="3" required maxlength="2000" placeholder="Nhập nội dung trả lời…" aria-describedby="chat-reply-error" @disabled($this->currentSession->status === 'closed')></textarea>
                    @error('replyMessage')<p id="chat-reply-error" role="alert" class="chat-form-error">{{ $message }}</p>@enderror
                    <div><small>{{ $this->currentSession->status === 'closed' ? 'Mở lại hội thoại để trả lời. Khách gửi tin mới sẽ tự mở lại hội thoại.' : 'Tối đa 2.000 ký tự. Nội dung được giữ lại nếu gửi thất bại.' }}</small><button type="submit" wire:loading.attr="disabled" wire:target="sendReply" @disabled($this->currentSession->status === 'closed')><span wire:loading.remove wire:target="sendReply">Gửi trả lời</span><span wire:loading wire:target="sendReply">Đang gửi…</span></button></div>
                </form>
            @else
                <div class="chat-empty"><h2>Chọn hội thoại để hỗ trợ</h2><p>Hội thoại chưa đọc không bị đánh dấu đã đọc khi chỉ mở trang này.</p></div>
            @endif
        </section>
    </div>
    <style>
        .chat-desk{display:grid;grid-template-columns:minmax(240px,32%) minmax(0,1fr);background:var(--chat-bg,#fff);color:var(--chat-text,#0f172a);border:1px solid #cbd5e1;border-radius:16px;overflow:hidden;min-height:640px}
        .chat-desk button,.chat-desk input,.chat-desk select{min-height:44px}.chat-desk :is(button,input,textarea,select):focus-visible{outline:3px solid #38bdf8;outline-offset:2px}.chat-desk button{cursor:pointer}.chat-desk button:disabled{opacity:.5;cursor:not-allowed}.chat-desk h2{font-size:16px;font-weight:700}.chat-desk label{display:block;font-size:12px;font-weight:600;margin:10px 0 5px}.chat-desk input,.chat-desk select,.chat-desk textarea{width:100%;border:1px solid #94a3b8;border-radius:10px;padding:10px;background:var(--chat-bg,#fff);color:inherit;font-size:14px}.chat-inbox{padding:16px;background:var(--chat-muted,#f8fafc);border-right:1px solid #cbd5e1;min-width:0}.chat-inbox h2{display:flex;justify-content:space-between}.chat-session-list{margin-top:12px;max-height:500px;overflow-y:auto}.chat-session{display:flex;flex-direction:column;gap:4px;width:100%;text-align:left;padding:12px;border-bottom:1px solid #cbd5e1;font-size:12px}.chat-session:hover,.chat-session.is-selected{background:var(--chat-selected,#ecfdf5)}.chat-session.is-selected{box-shadow:inset 3px 0 #059669}.chat-session-heading{display:flex;justify-content:space-between;gap:8px}.chat-session-heading>span{color:var(--chat-accent,#047857);font-weight:700}.chat-preview{overflow:hidden;text-overflow:ellipsis;white-space:nowrap;width:100%}.chat-desk small{color:var(--chat-secondary,#475569);font-size:11px}.chat-conversation{display:flex;flex-direction:column;min-width:0}.chat-desk-header{display:flex;justify-content:space-between;align-items:center;gap:12px;padding:16px;background:#1e3a8a;color:white}.chat-desk-header p{font-size:12px;margin-top:4px}.chat-desk-header button{border:1px solid #bfdbfe;padding:8px 12px;border-radius:10px;font-size:12px}.chat-desk-messages{height:360px;overflow-y:auto;overscroll-behavior:contain;padding:16px;display:flex;flex-direction:column;gap:12px;background:var(--chat-muted,#f8fafc)}.chat-desk-message{max-width:85%;align-self:flex-start;background:var(--chat-bg,#fff);border:1px solid #cbd5e1;border-radius:12px;padding:10px 12px;overflow-wrap:anywhere}.chat-desk-message.from-admin{align-self:flex-end;background:var(--chat-selected,#ecfdf5)}.chat-desk-message strong{font-size:12px}.chat-desk-message p{white-space:pre-wrap;font-size:14px;margin:5px 0}.chat-quick-replies,.chat-reply-form{padding:0 16px 12px}.chat-reply-form>div{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-top:8px}.chat-reply-form button{background:#059669;color:white;border-radius:10px;padding:10px 16px;white-space:nowrap;font-weight:600}.chat-reply-form button:hover{background:#047857}.chat-empty{padding:32px;text-align:center;margin:auto;color:var(--chat-secondary,#475569)}.chat-empty p{margin-top:8px;font-size:14px}.chat-form-error{color:#be123c;font-size:13px;margin-top:8px}.chat-pagination{padding:8px;font-size:12px}.dark .chat-desk{--chat-bg:#0f172a;--chat-text:#f1f5f9;--chat-muted:#1e293b;--chat-selected:#064e3b;--chat-secondary:#cbd5e1;--chat-accent:#6ee7b7}
        @media(max-width:900px){.chat-desk{grid-template-columns:1fr}.chat-inbox{border-right:0;border-bottom:1px solid #cbd5e1}.chat-session-list{max-height:220px}.chat-desk-messages{height:320px}.chat-reply-form textarea{font-size:16px}}
    </style>
    @script
    <script>
        const scroll = () => { const box = document.getElementById('admin-chat-messages'); if (box) box.scrollTop = box.scrollHeight; };
        $wire.on('chat-session-selected', () => requestAnimationFrame(scroll));
        $wire.on('chat-reply-sent', () => requestAnimationFrame(scroll));
    </script>
    @endscript
</x-filament-panels::page>
