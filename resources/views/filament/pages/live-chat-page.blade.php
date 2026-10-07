<x-filament-panels::page>
    <div wire:poll.4s class="grid grid-cols-1 lg:grid-cols-12 gap-6 h-[720px] bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
        
        <!-- CỘT TRÁI: DANH SÁCH CUỘC HỘI THOẠI (4 cols) -->
        <div class="lg:col-span-4 border-r border-slate-200 dark:border-slate-800 flex flex-col h-full bg-slate-50/50 dark:bg-slate-900/50">
            <!-- Header tìm kiếm & Lọc -->
            <div class="p-4 border-b border-slate-200 dark:border-slate-800 space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2 text-base">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Khách đang trực tuyến
                    </h3>
                    <span class="text-xs bg-indigo-50 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 px-2 py-0.5 rounded-full font-semibold">
                        {{ $this->sessions->count() }} hội thoại
                    </span>
                </div>

                <div class="relative">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Tìm tên, SĐT khách..." 
                        class="w-full text-xs pl-8 pr-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <svg class="w-4 h-4 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>

                <div class="flex gap-1.5 text-xs">
                    <button wire:click="$set('filter', 'all')" class="px-2.5 py-1 rounded-lg font-medium transition {{ $filter === 'all' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100' }}">Tất cả</button>
                    <button wire:click="$set('filter', 'unread')" class="px-2.5 py-1 rounded-lg font-medium transition {{ $filter === 'unread' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100' }}">Chưa đọc</button>
                    <button wire:click="$set('filter', 'active')" class="px-2.5 py-1 rounded-lg font-medium transition {{ $filter === 'active' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100' }}">Đang mở</button>
                </div>
            </div>

            <!-- Danh sách phiên chat cuộn dọc -->
            <div class="flex-1 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($this->sessions as $session)
                    <div wire:click="selectSession({{ $session->id }})" 
                        class="p-3.5 cursor-pointer transition flex items-start gap-3 hover:bg-white dark:hover:bg-slate-800/80 {{ $selectedSessionId === $session->id ? 'bg-white dark:bg-slate-800 border-l-4 border-indigo-600 shadow-xs' : '' }}">
                        <div class="relative flex-shrink-0">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-indigo-500 to-sky-400 flex items-center justify-center text-white font-bold text-sm shadow-xs">
                                {{ mb_substr($session->customer_name, 0, 1) }}
                            </div>
                            @if($session->unread_admin > 0)
                                <span class="absolute -top-1 -right-1 w-5 h-5 bg-rose-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center animate-bounce">
                                    {{ $session->unread_admin }}
                                </span>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-1 mb-0.5">
                                <h4 class="text-sm font-semibold text-slate-900 dark:text-white truncate">
                                    {{ $session->customer_name }}
                                </h4>
                                <span class="text-[11px] text-slate-400 whitespace-nowrap">
                                    {{ $session->last_message_at ? $session->last_message_at->diffForHumans() : '' }}
                                </span>
                            </div>

                            @if($session->customer_phone)
                                <p class="text-[11px] text-indigo-600 dark:text-indigo-400 font-mono mb-1">
                                    📞 {{ $session->customer_phone }}
                                </p>
                            @endif

                            <p class="text-xs text-slate-500 dark:text-slate-400 truncate">
                                {{ $session->last_message ?? 'Đã bắt đầu hội thoại' }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400">
                        <svg class="w-12 h-12 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                        <p class="text-xs">Chưa có cuộc trò chuyện nào</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- CỘT PHẢI: KHUNG NỘI DUNG CHAT (8 cols) -->
        <div class="lg:col-span-8 flex flex-col h-full bg-white dark:bg-slate-900">
            @if($this->currentSession)
                <!-- Chat Header -->
                <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-slate-50/70 dark:bg-slate-900/70">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-base shadow-xs">
                            {{ mb_substr($this->currentSession->customer_name, 0, 1) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-bold text-slate-900 dark:text-white text-base">
                                    {{ $this->currentSession->customer_name }}
                                </h3>
                                <span class="px-2 py-0.5 text-[11px] rounded-full font-medium {{ $this->currentSession->status === 'active' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300' }}">
                                    {{ $this->currentSession->status === 'active' ? 'Đang mở' : 'Đã kết thúc' }}
                                </span>
                            </div>
                            <div class="flex items-center gap-3 text-xs text-slate-500">
                                @if($this->currentSession->customer_phone)
                                    <a href="tel:{{ $this->currentSession->customer_phone }}" class="text-indigo-600 hover:underline flex items-center gap-1 font-medium">
                                        📞 {{ $this->currentSession->customer_phone }}
                                    </a>
                                @endif
                                <span>Phiên: {{ $this->currentSession->session_token }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button wire:click="toggleSessionStatus" class="px-3 py-1.5 text-xs font-medium border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                            {{ $this->currentSession->status === 'active' ? 'Đóng phiên chat' : 'Mở lại phiên chat' }}
                        </button>
                    </div>
                </div>

                <!-- Chat Messages Scroll Area -->
                <div id="admin-chat-messages" class="flex-1 overflow-y-auto p-4 space-y-4 bg-slate-50/30 dark:bg-slate-950/20">
                    @forelse($this->currentSession->messages as $msg)
                        <div class="flex flex-col {{ $msg->sender_type === 'admin' ? 'items-end' : 'items-start' }}">
                            <div class="flex items-baseline gap-2 mb-1">
                                <span class="text-[11px] font-semibold {{ $msg->sender_type === 'admin' ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-700 dark:text-slate-300' }}">
                                    {{ $msg->sender_name }}
                                </span>
                                <span class="text-[10px] text-slate-400">
                                    {{ $msg->created_at->format('H:i, d/m') }}
                                </span>
                            </div>

                            <div class="max-w-[75%] rounded-2xl px-4 py-2.5 text-sm shadow-xs {{ $msg->sender_type === 'admin' ? 'bg-indigo-600 text-white rounded-br-xs' : 'bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 border border-slate-200 dark:border-slate-700 rounded-bl-xs' }}">
                                {!! nl2br(e($msg->message)) !!}
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12 text-slate-400 text-xs">
                            Chưa có tin nhắn trong phiên chat này
                        </div>
                    @endforelse
                </div>

                <!-- Quick Replies Selector -->
                <div class="px-4 py-2 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 flex items-center gap-2 overflow-x-auto text-xs whitespace-nowrap">
                    <span class="text-slate-400 font-medium text-[11px] flex-shrink-0">Câu trả lời mẫu:</span>
                    @foreach($quickReplies as $reply)
                        <button type="button" wire:click="useQuickReply('{{ addslashes($reply) }}')" 
                            class="px-2.5 py-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 rounded-lg hover:border-indigo-400 hover:text-indigo-600 transition truncate max-w-[220px]">
                            {{ $reply }}
                        </button>
                    @endforeach
                </div>

                <!-- Reply Input Area -->
                <div class="p-3 border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900">
                    <form wire:submit="sendReply" class="flex gap-2 items-end">
                        <div class="flex-1">
                            <textarea wire:model="replyMessage" wire:keydown.enter.prevent="sendReply" rows="2" placeholder="Nhập tin nhắn trả lời khách hàng (Enter để gửi)..."
                                class="w-full text-sm p-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none resize-none"></textarea>
                        </div>
                        <button type="submit" class="px-5 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl text-sm transition flex items-center gap-1.5 shadow-sm">
                            <span>Gửi</span>
                            <svg class="w-4 h-4 rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                        </button>
                    </form>
                </div>
            @else
                <div class="flex-1 flex flex-col items-center justify-center text-slate-400 p-8 text-center">
                    <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-3">
                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    </div>
                    <h4 class="text-base font-bold text-slate-700 dark:text-slate-300 mb-1">Chưa chọn cuộc trò chuyện</h4>
                    <p class="text-xs text-slate-400 max-w-sm">Chọn một khách hàng bên cột trái để xem lịch sử trò chuyện và tư vấn trực tuyến.</p>
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            const scrollChat = () => {
                const box = document.getElementById('admin-chat-messages');
                if (box) {
                    box.scrollTop = box.scrollHeight;
                }
            };
            scrollChat();
            Livewire.hook('morph.updated', () => {
                scrollChat();
            });
        });
    </script>
</x-filament-panels::page>
