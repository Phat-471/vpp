<x-filament-panels::page>
    {{-- 1. Top Header Banner --}}
    <div style="background: linear-gradient(135deg, #1e1b4b 0%, #0f172a 100%); color: #ffffff;" class="mb-4 p-4 rounded-2xl shadow-lg border border-indigo-800/40 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
        <div class="flex items-center space-x-3">
            <div style="background: rgba(99, 102, 241, 0.2); border: 1px solid rgba(129, 140, 248, 0.3);" class="w-11 h-11 rounded-xl flex items-center justify-center text-2xl shrink-0 shadow-inner">
                💬
            </div>
            <div>
                <div class="flex items-center space-x-2">
                    <h3 style="color: #ffffff;" class="text-base font-black tracking-tight">Hỗ Trợ Khách Hàng Trực Tuyến</h3>
                    <span style="background: rgba(16, 185, 129, 0.2); color: #6ee7b7; border: 1px solid rgba(52, 211, 153, 0.3);" class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold">
                        ● Trực tuyến 24/7
                    </span>
                </div>
                <p style="color: #c7d2fe;" class="text-xs mt-0.5">Tiếp nhận, tư vấn sản phẩm văn phòng phẩm & hỗ trợ kỹ thuật máy in theo thời gian thực.</p>
            </div>
        </div>

        {{-- Quick Stats Pills --}}
        <div class="flex flex-wrap items-center gap-2">
            <div style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.15);" class="px-3 py-1.5 rounded-xl text-xs flex items-center space-x-1.5">
                <span style="color: #94a3b8;">Tổng hội thoại:</span>
                <b style="color: #ffffff;" class="font-black">{{ $this->totalCount }}</b>
            </div>
            <div style="background: {{ $this->unreadCount > 0 ? 'rgba(244, 63, 94, 0.25)' : 'rgba(255, 255, 255, 0.08)' }}; border: 1px solid {{ $this->unreadCount > 0 ? 'rgba(244, 63, 94, 0.5)' : 'rgba(255, 255, 255, 0.15)' }};" class="px-3 py-1.5 rounded-xl text-xs flex items-center space-x-1.5">
                <span style="color: {{ $this->unreadCount > 0 ? '#fecdd3' : '#94a3b8' }};">Chưa đọc:</span>
                <b style="color: {{ $this->unreadCount > 0 ? '#fda4af' : '#ffffff' }};" class="font-black {{ $this->unreadCount > 0 ? 'animate-pulse' : '' }}">{{ $this->unreadCount }}</b>
            </div>
            <div style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3);" class="px-3 py-1.5 rounded-xl text-xs flex items-center space-x-1.5">
                <span style="color: #6ee7b7;">Đang mở:</span>
                <b style="color: #a7f3d0;" class="font-black">{{ $this->activeCount }}</b>
            </div>
            <a href="{{ route('storefront.index') }}" target="_blank" style="background: rgba(255, 255, 255, 0.12); color: #ffffff; border: 1px solid rgba(255, 255, 255, 0.25);" class="inline-flex items-center space-x-1 px-3 py-1.5 rounded-xl text-xs font-bold transition hover:opacity-90">
                <span>🌐 Xem Cửa Hàng</span>
                <span>↗</span>
            </a>
        </div>
    </div>

    {{-- 2. Chat Desk Split Layout --}}
    <div wire:poll.visible.5s="refreshSelected" class="chat-desk">
        {{-- LEFT COLUMN: Hộp Thư Hội Thoại (Inbox Sidebar) --}}
        <aside class="chat-inbox">
            <div class="chat-inbox-header">
                <div class="chat-inbox-title">
                    <h2>💬 Hội thoại khách hàng</h2>
                    <span class="chat-badge-count">{{ $this->sessions->total() }}</span>
                </div>

                {{-- Search Box --}}
                <div class="chat-search-wrap">
                    <span class="chat-search-icon">🔍</span>
                    <input id="chat-search" type="search" wire:model.live.debounce.400ms="search" maxlength="100" placeholder="Tìm tên, SĐT, nội dung…">
                    @if(trim($search) !== '')
                        <button type="button" wire:click="$set('search', '')" class="chat-search-clear">✕</button>
                    @endif
                </div>

                {{-- Filter Segmented Pills --}}
                <div class="chat-filter-pills">
                    <button type="button" wire:click="$set('filter', 'all')" class="chat-pill-btn {{ $filter === 'all' ? 'active' : '' }}">
                        Tất cả
                    </button>
                    <button type="button" wire:click="$set('filter', 'unread')" class="chat-pill-btn {{ $filter === 'unread' ? 'active active-unread' : '' }}">
                        Chưa đọc
                        @if($this->unreadCount > 0)
                            <span class="chat-dot-unread"></span>
                        @endif
                    </button>
                    <button type="button" wire:click="$set('filter', 'active')" class="chat-pill-btn {{ $filter === 'active' ? 'active active-open' : '' }}">
                        Đang mở
                    </button>
                    <button type="button" wire:click="$set('filter', 'closed')" class="chat-pill-btn {{ $filter === 'closed' ? 'active' : '' }}">
                        Đã đóng
                    </button>
                </div>

                {{-- Accessibility Select --}}
                <select id="chat-filter" wire:model.live="filter" class="chat-hidden-select" aria-label="Bộ lọc trạng thái">
                    <option value="all">Tất cả</option>
                    <option value="unread">Chưa đọc</option>
                    <option value="active">Đang mở</option>
                    <option value="closed">Đã đóng</option>
                </select>
            </div>

            {{-- Session Cards List --}}
            <div class="chat-session-list">
                @forelse($this->sessions as $session)
                    @php
                        $isSelected = ($selectedSessionId === $session->id);
                        $hasUnread = ($session->unread_admin > 0);
                        $initial = mb_strtoupper(mb_substr($session->customer_name ?? 'K', 0, 1));
                    @endphp
                    <button type="button"
                        wire:key="chat-session-{{ $session->id }}"
                        wire:click="selectSession({{ $session->id }})"
                        class="chat-session {{ $isSelected ? 'is-selected' : '' }}"
                        aria-pressed="{{ $isSelected ? 'true' : 'false' }}">
                        
                        <div class="chat-avatar-wrap">
                            <div class="chat-avatar {{ $isSelected ? 'avatar-selected' : '' }}">
                                {{ $initial }}
                            </div>
                            <span class="chat-status-dot {{ $session->status === 'active' ? 'online' : 'offline' }}"></span>
                        </div>

                        <div class="chat-session-body">
                            <div class="chat-session-heading">
                                <strong>{{ $session->customer_name }}</strong>
                                @if($hasUnread)
                                    <span class="chat-unread-badge">{{ $session->unread_admin }} chưa đọc</span>
                                @else
                                    <small>{{ $session->last_message_at?->diffForHumans() ?? '' }}</small>
                                @endif
                            </div>

                            @if($session->customer_phone)
                                <div class="chat-session-phone">
                                    <span>📞</span>
                                    <span>{{ $session->customer_phone }}</span>
                                </div>
                            @endif

                            <div class="chat-preview">
                                {{ $session->last_message ?? 'Chưa có yêu cầu hỗ trợ' }}
                            </div>

                            <div class="chat-session-meta">
                                <span>{{ $session->last_message_at?->format('H:i, d/m/Y') }}</span>
                                <span class="{{ $session->status === 'active' ? 'text-status-open' : 'text-status-closed' }}">
                                    {{ $session->status === 'active' ? '● Đang mở' : '○ Đã đóng' }}
                                </span>
                            </div>
                        </div>
                    </button>
                @empty
                    <div class="chat-inbox-empty">
                        <div class="empty-icon">📭</div>
                        <p>Không tìm thấy hội thoại nào.</p>
                        <small>Hãy thử bộ lọc khác hoặc xóa từ khóa tìm kiếm.</small>
                    </div>
                @endforelse
            </div>

            <div class="chat-pagination">
                {{ $this->sessions->links() }}
            </div>
        </aside>

        {{-- RIGHT COLUMN: Không Gian Trò Chuyện (Conversation Desk) --}}
        <section class="chat-conversation" aria-label="Chi tiết hội thoại">
            @if($this->currentSession)
                {{-- Header --}}
                <header class="chat-desk-header">
                    <div class="chat-header-user">
                        <div class="chat-header-avatar">
                            {{ mb_strtoupper(mb_substr($this->currentSession->customer_name ?? 'K', 0, 1)) }}
                        </div>
                        <div>
                            <div class="chat-header-name-row">
                                <h2>{{ $this->currentSession->customer_name }}</h2>
                                <span class="chat-status-pill {{ $this->currentSession->status === 'active' ? 'open' : 'closed' }}">
                                    {{ $this->currentSession->status === 'active' ? '🟢 Đang mở hỗ trợ' : '⚪ Đã đóng' }}
                                </span>
                            </div>
                            <p class="chat-header-sub">
                                @if($this->currentSession->customer_phone)
                                    <a href="tel:{{ $this->currentSession->customer_phone }}" class="chat-phone-link">
                                        📞 {{ $this->currentSession->customer_phone }}
                                    </a>
                                @else
                                    <span>Khách vãng lai web</span>
                                @endif
                                <span>· Phiên #{{ $this->currentSession->id }}</span>
                            </p>
                        </div>
                    </div>

                    <div class="chat-header-actions">
                        @if($this->currentSession->unread_admin > 0)
                            <button type="button" wire:click="markSelectedRead" wire:loading.attr="disabled" wire:target="markSelectedRead" class="btn-mark-read">
                                ✓ {{ $this->currentSession->unread_admin }} tin chưa đọc · Đánh dấu đã đọc
                            </button>
                        @endif

                        <button type="button" wire:click="toggleSessionStatus" wire:loading.attr="disabled" wire:target="toggleSessionStatus" wire:confirm="Xác nhận thay đổi trạng thái hội thoại?" class="btn-toggle-status {{ $this->currentSession->status === 'active' ? 'btn-close' : 'btn-open' }}">
                            {{ $this->currentSession->status === 'active' ? '🔒 Đóng hội thoại' : '🔓 Mở lại hội thoại' }}
                        </button>
                    </div>
                </header>

                {{-- Customer Insight Strip (Nếu có trong DB) --}}
                @if($customer = $this->customerDetails)
                    <div class="chat-customer-insight">
                        <div class="insight-info">
                            <span>👤 <b>{{ $customer->name }}</b> (Khách quen)</span>
                            <span>📦 <b>{{ $customer->orders()->count() }}</b> đơn hàng</span>
                            <span>🛠️ <b>{{ $customer->repairTickets()->count() }}</b> phiếu sửa máy in</span>
                            @if($customer->debt_balance > 0)
                                <span class="insight-debt">⚠️ Công nợ: {{ number_format($customer->debt_balance) }}đ</span>
                            @endif
                        </div>
                        <a href="{{ route('filament.admin.resources.customers.edit', $customer) }}" target="_blank" class="insight-link">
                            Hồ sơ khách ↗
                        </a>
                    </div>
                @endif

                {{-- Pagination Helper Bar --}}
                <div class="chat-history-bar">
                    <small>Trang 1 là nhóm tin mới nhất.</small>
                    <div class="chat-pagination-inner">
                        {{ $this->messages->links() }}
                    </div>
                </div>

                {{-- Message Stream --}}
                <div id="admin-chat-messages" class="chat-desk-messages" role="log" aria-label="Tin nhắn" aria-live="polite">
                    @forelse($this->messages->getCollection()->reverse() as $msg)
                        @php
                            $isAdmin = ($msg->sender_type === 'admin');
                        @endphp
                        <article wire:key="chat-message-{{ $msg->id }}" class="chat-desk-message {{ $isAdmin ? 'from-admin' : 'from-customer' }}">
                            <div class="msg-avatar">
                                {{ $isAdmin ? 'AD' : mb_strtoupper(mb_substr($msg->sender_name ?? 'K', 0, 1)) }}
                            </div>
                            <div class="msg-bubble">
                                <strong class="msg-sender">{{ $msg->sender_name }}</strong>
                                <p class="msg-text">{{ $msg->message }}</p>
                                <div class="msg-meta">
                                    <span>{{ $msg->created_at->format('H:i, d/m/Y') }}</span>
                                    @if($isAdmin)
                                        <span class="msg-read-status">{{ $msg->is_read ? '· ✓✓ Đã đọc' : '· ✓ Đã gửi' }}</span>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="chat-no-messages">
                            <div class="empty-icon">💬</div>
                            <p>Chưa có tin nhắn trong hội thoại này.</p>
                            <small>Hãy gửi lời chào đầu tiên để hỗ trợ khách hàng.</small>
                        </div>
                    @endforelse
                </div>

                {{-- Quick Reply Section --}}
                <div class="chat-quick-section">
                    <div class="quick-header">
                        <label>💡 Mẫu câu phản hồi nhanh (Bấm 1 chạm để điền):</label>
                        <select id="chat-quick-reply" wire:change="useQuickReply($event.target.value)" class="chat-hidden-select" aria-label="Mẫu câu phản hồi nhanh">
                            <option value="" disabled selected>Chọn mẫu câu…</option>
                            @foreach($this->quickReplies as $index => $reply)
                                <option value="{{ $index }}">{{ Str::limit($reply, 45) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="quick-chips">
                        @foreach($this->quickReplies as $index => $reply)
                            <button type="button" wire:click="useQuickReply({{ $index }})" class="quick-chip-btn">
                                @if($index === 0) 👋 Chào & hỏi nhu cầu
                                @elseif($index === 1) 📦 Báo giá & số lượng
                                @elseif($index === 2) 🖨️ Hỏi máy in & lỗi
                                @elseif($index === 3) 🚚 Kiểm tra đơn hàng
                                @elseif($index === 4) ⏳ Đã ghi nhận xử lý
                                @else 💬 Mẫu {{ $index + 1 }}
                                @endif
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Reply Composer Form --}}
                <form wire:submit="sendReply" class="chat-reply-form">
                    <div class="reply-textarea-wrap">
                        <textarea id="admin-chat-reply" wire:model="replyMessage" rows="3" required maxlength="2000"
                            placeholder="Nhập nội dung trả lời (Nhấn phím Ctrl + Enter để gửi nhanh)..."
                            wire:keydown.ctrl.enter="sendReply"
                            wire:keydown.cmd.enter="sendReply"
                            aria-describedby="chat-reply-error"
                            @disabled($this->currentSession->status === 'closed')></textarea>

                        @error('replyMessage')
                            <p id="chat-reply-error" role="alert" class="chat-form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="reply-footer">
                        <div class="reply-hint">
                            @if($this->currentSession->status === 'closed')
                                <span class="hint-closed">⚠️ Hội thoại đang đóng. Bấm "Mở lại hội thoại" ở trên để tiếp tục trò chuyện.</span>
                            @else
                                <span>⚡ Nhấn <b>Ctrl + Enter</b> để gửi nhanh · Tối đa 2.000 ký tự</span>
                            @endif
                        </div>

                        <button type="submit" wire:loading.attr="disabled" wire:target="sendReply" @disabled($this->currentSession->status === 'closed')" class="btn-send">
                            <span wire:loading.remove wire:target="sendReply">Gửi trả lời ➔</span>
                            <span wire:loading wire:target="sendReply">Đang gửi…</span>
                        </button>
                    </div>
                </form>
            @else
                {{-- Empty State: Khi chưa chọn hội thoại --}}
                <div class="chat-conversation-empty">
                    <div class="empty-box">
                        <div class="empty-icon-large">💬</div>
                        <h2>Chọn hội thoại để hỗ trợ</h2>
                        <p>Hội thoại chưa đọc không bị đánh dấu đã đọc khi chỉ mở trang này.</p>
                        
                        <div class="empty-tips-grid">
                            <div class="empty-tip-card">
                                <strong>⚡ Phản hồi siêu tốc</strong>
                                <small>Tư vấn khách trong 1-3 phút giúp tăng 80% tỷ lệ chốt đơn văn phòng phẩm.</small>
                            </div>
                            <div class="empty-tip-card">
                                <strong>🛠️ Kỹ thuật máy in</strong>
                                <small>Hỗ trợ kiểm tra lỗi máy in, báo giá hộp mực và cử thợ nạp mực tận nơi.</small>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </section>
    </div>

    {{-- 3. Dedicated Modern Styles --}}
    <style>
        .chat-desk {
            display: grid;
            grid-template-columns: 360px minmax(0, 1fr);
            background: #ffffff;
            color: #0f172a;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            overflow: hidden;
            min-height: 700px;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
        }

        /* INBOX SIDEBAR */
        .chat-inbox {
            display: flex;
            flex-direction: column;
            background: #f8fafc;
            border-right: 1px solid #e2e8f0;
            min-width: 0;
            height: 700px;
        }
        .chat-inbox-header {
            padding: 16px;
            border-bottom: 1px solid #e2e8f0;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .chat-inbox-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .chat-inbox-title h2 {
            font-size: 14px;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
        }
        .chat-badge-count {
            background: #e0e7ff;
            color: #4338ca;
            font-size: 11px;
            font-weight: 800;
            padding: 2px 8px;
            border-radius: 10px;
        }
        .chat-search-wrap {
            position: relative;
        }
        .chat-search-icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 12px;
            color: #94a3b8;
            pointer-events: none;
        }
        .chat-search-wrap input {
            width: 100%;
            padding: 8px 30px 8px 32px;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            font-size: 12px;
            background: #f8fafc;
            color: #0f172a;
            outline: none;
            transition: all 0.2s;
        }
        .chat-search-wrap input:focus {
            border-color: #6366f1;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        }
        .chat-search-clear {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #94a3b8;
            font-size: 12px;
            cursor: pointer;
        }
        .chat-filter-pills {
            display: flex;
            gap: 4px;
            background: #f1f5f9;
            padding: 3px;
            border-radius: 10px;
        }
        .chat-pill-btn {
            flex: 1;
            border: none;
            background: none;
            padding: 6px 4px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 600;
            color: #64748b;
            cursor: pointer;
            transition: all 0.15s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
        }
        .chat-pill-btn.active {
            background: #ffffff;
            color: #4338ca;
            font-weight: 800;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }
        .chat-pill-btn.active-unread {
            color: #e11d48;
        }
        .chat-pill-btn.active-open {
            color: #059669;
        }
        .chat-dot-unread {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #e11d48;
        }
        .chat-hidden-select {
            display: none;
        }

        /* SESSION LIST */
        .chat-session-list {
            flex: 1;
            overflow-y: auto;
            padding: 8px;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .chat-session {
            display: flex;
            gap: 10px;
            padding: 12px;
            border-radius: 12px;
            border: 1px solid #f1f5f9;
            background: #ffffff;
            cursor: pointer;
            transition: all 0.15s ease;
            text-align: left;
            width: 100%;
            position: relative;
        }
        .chat-session:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            transform: translateY(-1px);
        }
        .chat-session.is-selected {
            background: #eef2ff;
            border-color: #c7d2fe;
            border-left: 4px solid #4f46e5;
            box-shadow: 0 2px 6px rgba(79, 70, 229, 0.08);
        }
        .chat-avatar-wrap {
            position: relative;
            flex-shrink: 0;
        }
        .chat-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, #64748b 0%, #475569 100%);
            color: #ffffff;
            font-weight: 800;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .chat-avatar.avatar-selected {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        }
        .chat-status-dot {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            border: 2px solid #ffffff;
        }
        .chat-status-dot.online {
            background: #10b981;
        }
        .chat-status-dot.offline {
            background: #94a3b8;
        }
        .chat-session-body {
            flex: 1;
            min-width: 0;
        }
        .chat-session-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 6px;
            margin-bottom: 2px;
        }
        .chat-session-heading strong {
            font-size: 12px;
            font-weight: 700;
            color: #0f172a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .chat-unread-badge {
            background: #e11d48;
            color: #ffffff;
            font-size: 10px;
            font-weight: 800;
            padding: 1px 6px;
            border-radius: 10px;
            flex-shrink: 0;
            animation: pulse 2s infinite;
        }
        .chat-session-heading small {
            font-size: 10px;
            color: #94a3b8;
            flex-shrink: 0;
        }
        .chat-session-phone {
            font-size: 11px;
            color: #64748b;
            margin-bottom: 3px;
        }
        .chat-preview {
            font-size: 11px;
            color: #475569;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.4;
        }
        .chat-session-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 10px;
            color: #94a3b8;
            margin-top: 6px;
            padding-top: 4px;
            border-top: 1px solid #f1f5f9;
        }
        .text-status-open {
            color: #059669;
            font-weight: 600;
        }
        .text-status-closed {
            color: #94a3b8;
        }
        .chat-inbox-empty {
            padding: 40px 16px;
            text-align: center;
            color: #94a3b8;
        }
        .chat-inbox-empty .empty-icon {
            font-size: 32px;
            margin-bottom: 8px;
        }
        .chat-pagination {
            padding: 8px;
            border-top: 1px solid #e2e8f0;
            background: #ffffff;
            font-size: 11px;
        }

        /* CONVERSATION WORKSPACE */
        .chat-conversation {
            display: flex;
            flex-direction: column;
            background: #ffffff;
            min-width: 0;
            height: 700px;
        }
        .chat-desk-header {
            padding: 14px 18px;
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }
        .chat-header-user {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .chat-header-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%);
            color: #ffffff;
            font-weight: 800;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 6px rgba(79, 70, 229, 0.2);
        }
        .chat-header-name-row {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .chat-header-name-row h2 {
            font-size: 15px;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
        }
        .chat-status-pill {
            font-size: 10px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 12px;
        }
        .chat-status-pill.open {
            background: #d1fae5;
            color: #065f46;
        }
        .chat-status-pill.closed {
            background: #f1f5f9;
            color: #475569;
        }
        .chat-header-sub {
            font-size: 11px;
            color: #64748b;
            margin: 2px 0 0;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .chat-phone-link {
            color: #4f46e5;
            font-weight: 700;
            text-decoration: none;
        }
        .chat-phone-link:hover {
            text-decoration: underline;
        }
        .chat-header-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .btn-mark-read {
            background: #eef2ff;
            color: #4338ca;
            border: 1px solid #c7d2fe;
            padding: 6px 12px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.15s;
        }
        .btn-mark-read:hover {
            background: #e0e7ff;
        }
        .btn-toggle-status {
            padding: 6px 12px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.15s;
        }
        .btn-toggle-status.btn-close {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
        }
        .btn-toggle-status.btn-close:hover {
            background: #fee2e2;
            color: #b91c1c;
            border-color: #fca5a5;
        }
        .btn-toggle-status.btn-open {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }
        .btn-toggle-status.btn-open:hover {
            background: #a7f3d0;
        }

        /* CUSTOMER INSIGHT */
        .chat-customer-insight {
            background: #ecfdf5;
            border-bottom: 1px solid #a7f3d0;
            padding: 8px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 11px;
            color: #065f46;
        }
        .insight-info {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        .insight-debt {
            color: #be123c;
            font-weight: 700;
        }
        .insight-link {
            color: #047857;
            font-weight: 700;
            text-decoration: none;
        }
        .insight-link:hover {
            text-decoration: underline;
        }

        .chat-history-bar {
            padding: 6px 18px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 11px;
            color: #64748b;
        }
        .chat-pagination-inner {
            font-size: 11px;
        }

        /* MESSAGE STREAM */
        .chat-desk-messages {
            flex: 1;
            overflow-y: auto;
            padding: 18px;
            background: #f8fafc;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }
        .chat-desk-message {
            display: flex;
            align-items: flex-end;
            gap: 8px;
            max-width: 80%;
        }
        .chat-desk-message.from-customer {
            align-self: flex-start;
        }
        .chat-desk-message.from-admin {
            align-self: flex-end;
            flex-direction: row-reverse;
        }
        .msg-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 800;
            color: #ffffff;
            flex-shrink: 0;
            background: #64748b;
        }
        .from-admin .msg-avatar {
            background: #4f46e5;
        }
        .msg-bubble {
            padding: 10px 14px;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            position: relative;
        }
        .from-customer .msg-bubble {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            color: #0f172a;
            border-bottom-left-radius: 4px;
        }
        .from-admin .msg-bubble {
            background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
            color: #ffffff;
            border-bottom-right-radius: 4px;
            box-shadow: 0 3px 10px rgba(79, 70, 229, 0.2);
        }
        .msg-sender {
            font-size: 11px;
            font-weight: 700;
            display: block;
            margin-bottom: 2px;
        }
        .from-customer .msg-sender {
            color: #64748b;
        }
        .from-admin .msg-sender {
            color: #e0e7ff;
        }
        .msg-text {
            font-size: 13px;
            line-height: 1.5;
            margin: 0;
            white-space: pre-wrap;
            word-break: break-word;
        }
        .msg-meta {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 4px;
            font-size: 10px;
            margin-top: 4px;
        }
        .from-customer .msg-meta {
            color: #94a3b8;
        }
        .from-admin .msg-meta {
            color: #c7d2fe;
        }
        .msg-read-status {
            font-weight: 700;
            color: #6ee7b7;
        }
        .chat-no-messages {
            margin: auto;
            text-align: center;
            color: #94a3b8;
            padding: 30px;
        }
        .chat-no-messages .empty-icon {
            font-size: 32px;
            margin-bottom: 6px;
        }

        /* QUICK REPLIES */
        .chat-quick-section {
            padding: 8px 18px;
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .quick-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .quick-header label {
            font-size: 11px;
            font-weight: 700;
            color: #475569;
            margin: 0;
        }
        .quick-select {
            font-size: 11px;
            padding: 2px 6px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            background: #f8fafc;
            color: #475569;
        }
        .quick-chips {
            display: flex;
            gap: 6px;
            overflow-x: auto;
            padding-bottom: 2px;
        }
        .quick-chips::-webkit-scrollbar {
            height: 3px;
        }
        .quick-chips::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        .quick-chip-btn {
            white-space: nowrap;
            padding: 4px 10px;
            border-radius: 20px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            color: #334155;
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s;
            flex-shrink: 0;
        }
        .quick-chip-btn:hover {
            background: #eef2ff;
            color: #4338ca;
            border-color: #c7d2fe;
            transform: translateY(-1px);
        }

        /* REPLY FORM */
        .chat-reply-form {
            padding: 12px 18px;
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .reply-textarea-wrap textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            font-size: 13px;
            line-height: 1.5;
            color: #0f172a;
            background: #f8fafc;
            outline: none;
            resize: vertical;
            transition: all 0.2s;
        }
        .reply-textarea-wrap textarea:focus {
            border-color: #6366f1;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        }
        .reply-textarea-wrap textarea:disabled {
            background: #f1f5f9;
            color: #94a3b8;
            cursor: not-allowed;
        }
        .chat-form-error {
            color: #e11d48;
            font-size: 11px;
            margin: 4px 0 0;
            font-weight: 700;
        }
        .reply-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }
        .reply-hint {
            font-size: 11px;
            color: #94a3b8;
        }
        .hint-closed {
            color: #d97706;
            font-weight: 700;
        }
        .btn-send {
            background: #059669;
            color: #ffffff;
            border: none;
            padding: 8px 18px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.15s;
            box-shadow: 0 2px 6px rgba(5, 150, 105, 0.2);
            white-space: nowrap;
        }
        .btn-send:hover {
            background: #047857;
            transform: translateY(-1px);
        }
        .btn-send:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        /* CONVERSATION EMPTY */
        .chat-conversation-empty {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            background: #f8fafc;
        }
        .empty-box {
            max-width: 420px;
            text-align: center;
        }
        .empty-icon-large {
            font-size: 48px;
            margin-bottom: 12px;
        }
        .empty-box h2 {
            font-size: 16px;
            font-weight: 800;
            color: #0f172a;
            margin: 0 0 6px;
        }
        .empty-box p {
            font-size: 12px;
            color: #64748b;
            margin: 0 0 20px;
        }
        .empty-tips-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            text-align: left;
        }
        .empty-tip-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        }
        .empty-tip-card strong {
            display: block;
            font-size: 12px;
            color: #4f46e5;
            margin-bottom: 2px;
        }
        .empty-tip-card small {
            font-size: 10px;
            color: #64748b;
            line-height: 1.3;
            display: block;
        }

        /* RESPONSIVE */
        @media (max-width: 900px) {
            .chat-desk {
                grid-template-columns: 1fr;
            }
            .chat-inbox {
                border-right: none;
                border-bottom: 1px solid #e2e8f0;
                height: 380px;
            }
            .chat-conversation {
                height: 520px;
            }
        }

        /* DARK MODE SUPPORT */
        .dark .chat-desk {
            background: #0f172a;
            color: #f1f5f9;
            border-color: #334155;
        }
        .dark .chat-inbox {
            background: #1e293b;
            border-color: #334155;
        }
        .dark .chat-inbox-header,
        .dark .chat-pagination,
        .dark .chat-desk-header,
        .dark .chat-quick-section,
        .dark .chat-reply-form {
            background: #0f172a;
            border-color: #334155;
        }
        .dark .chat-inbox-title h2,
        .dark .chat-header-name-row h2,
        .dark .empty-box h2 {
            color: #f8fafc;
        }
        .dark .chat-search-wrap input,
        .dark .reply-textarea-wrap textarea {
            background: #1e293b;
            border-color: #475569;
            color: #f8fafc;
        }
        .dark .chat-filter-pills {
            background: #1e293b;
        }
        .dark .chat-pill-btn {
            color: #94a3b8;
        }
        .dark .chat-pill-btn.active {
            background: #334155;
            color: #a5b4fc;
        }
        .dark .chat-session {
            background: #1e293b;
            border-color: #334155;
        }
        .dark .chat-session.is-selected {
            background: #1e1b4b;
            border-color: #6366f1;
        }
        .dark .chat-session-heading strong {
            color: #f8fafc;
        }
        .dark .chat-desk-messages,
        .dark .chat-conversation-empty {
            background: #0f172a;
        }
        .dark .from-customer .msg-bubble {
            background: #1e293b;
            border-color: #334155;
            color: #f8fafc;
        }
        .dark .empty-tip-card {
            background: #1e293b;
            border-color: #334155;
        }
    </style>

    @script
    <script>
        const scrollChat = () => {
            const box = document.getElementById('admin-chat-messages');
            if (box) {
                box.scrollTop = box.scrollHeight;
            }
        };
        $wire.on('chat-session-selected', () => requestAnimationFrame(scrollChat));
        $wire.on('chat-reply-sent', () => requestAnimationFrame(scrollChat));
    </script>
    @endscript
</x-filament-panels::page>
