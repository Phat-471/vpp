<?php

namespace App\Filament\Pages;

use App\Models\ChatSession;
use App\Services\LiveChatService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Locked;
use Livewire\WithPagination;

class LiveChatPage extends Page
{
    use WithPagination;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Chăm sóc khách hàng';

    protected static ?string $navigationLabel = 'Hỗ trợ trực tuyến';

    protected static ?string $title = 'Hỗ trợ khách hàng trực tuyến';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.live-chat-page';

    #[Locked]
    public ?int $selectedSessionId = null;

    public string $replyMessage = '';

    public string $filter = 'all';

    public string $search = '';

    public static function canAccess(): bool
    {
        return Gate::allows('manage-live-chat');
    }

    public function boot(): void
    {
        Gate::authorize('manage-live-chat');
    }

    public function mount(): void
    {
        // Selecting a conversation is explicit; opening the page never clears unread messages.
    }

    public function selectSession(int $sessionId): void
    {
        Gate::authorize('manage-live-chat');
        ChatSession::findOrFail($sessionId);
        $this->selectedSessionId = $sessionId;
        $this->replyMessage = '';
        $this->resetValidation();
        $this->resetPage('messagesPage'); // The pagination hook acknowledges the displayed group.
        $this->dispatch('chat-session-selected');
    }

    public function refreshSelected(): void
    {
        Gate::authorize('manage-live-chat');
        unset($this->messages, $this->currentSession, $this->sessions);
    }

    public function markSelectedRead(): void
    {
        $this->refreshSelected();
        if ($session = $this->currentSession) {
            $ids = $this->messages->getCollection()->pluck('id');
            if ($ids->isNotEmpty()) {
                app(LiveChatService::class)->markReadByAdmin(auth()->user(), $session, $ids->min(), $ids->max());
                unset($this->messages, $this->currentSession, $this->sessions);
            }
        }
    }

    public function updatedSearch(): void
    {
        $this->validateOnly('search', ['search' => ['string', 'max:100']]);
        $this->resetPage('sessionsPage');
    }

    public function updatedFilter(): void
    {
        $this->validateOnly('filter', ['filter' => ['in:all,unread,active,closed']]);
        $this->resetPage('sessionsPage');
    }

    public function updatedPaginators($page, $pageName): void
    {
        if ($pageName === 'messagesPage') {
            $this->markSelectedRead();
        }
    }

    public function sendReply(): void
    {
        Gate::authorize('manage-live-chat');
        if (! $session = $this->currentSession) {
            $this->addError('replyMessage', 'Chọn một hội thoại trước khi gửi tin nhắn.');

            return;
        }
        app(LiveChatService::class)->reply(auth()->user(), $session, $this->replyMessage);
        $this->replyMessage = '';
        $this->resetPage('messagesPage');
        $this->dispatch('chat-reply-sent');
    }

    public function getQuickRepliesProperty(): array
    {
        return [
            'Xin chào anh/chị! Anh/chị cần tư vấn sản phẩm hay hỗ trợ máy in ạ?',
            'Anh/chị cho em xin tên sản phẩm và số lượng cần mua để em kiểm tra giá và tồn kho nhé.',
            'Anh/chị cho em xin dòng máy in và mô tả lỗi để em chuyển thông tin cho kỹ thuật viên nhé.',
            'Anh/chị có thể cung cấp mã đơn hàng để em kiểm tra tình trạng giao hàng.',
            'Bên em đã ghi nhận yêu cầu. Nhân viên sẽ kiểm tra và phản hồi tại đây.',
        ];
    }

    public function useQuickReply(int $index): void
    {
        Gate::authorize('manage-live-chat');
        abort_unless(array_key_exists($index, $this->quickReplies), 422);
        $this->replyMessage = $this->quickReplies[$index];
    }

    public function toggleSessionStatus(): void
    {
        Gate::authorize('manage-live-chat');
        if ($session = $this->currentSession) {
            app(LiveChatService::class)->toggle(auth()->user(), $session);
            unset($this->currentSession, $this->sessions);
            Notification::make()->title($this->currentSession->status === 'active' ? 'Đã mở lại hội thoại' : 'Đã đóng hội thoại')->success()->send();
        }
    }

    public function getSessionsProperty()
    {
        Gate::authorize('manage-live-chat');
        $this->validate(['filter' => ['in:all,unread,active,closed'], 'search' => ['string', 'max:100']]);

        return ChatSession::query()->select(['id', 'customer_name', 'customer_phone', 'status', 'unread_admin', 'last_message', 'last_message_at'])
            ->when($this->filter === 'unread', fn ($query) => $query->where('unread_admin', '>', 0))
            ->when(in_array($this->filter, ['active', 'closed']), fn ($query) => $query->where('status', $this->filter))
            ->when(trim($this->search) !== '', function ($query) {
                $search = '%'.trim($this->search).'%';
                $query->where(fn ($q) => $q->where('customer_name', 'like', $search)->orWhere('customer_phone', 'like', $search)->orWhere('last_message', 'like', $search));
            })->orderByDesc('last_message_at')->orderByDesc('id')->paginate(20, pageName: 'sessionsPage');
    }

    public function getCurrentSessionProperty(): ?ChatSession
    {
        Gate::authorize('manage-live-chat');

        return $this->selectedSessionId ? ChatSession::findOrFail($this->selectedSessionId) : null;
    }

    public function getMessagesProperty()
    {
        Gate::authorize('manage-live-chat');

        return $this->currentSession?->messages()->reorder()->orderByDesc('id')->paginate(50, pageName: 'messagesPage');
    }

    public function getUnreadCountProperty(): int
    {
        return ChatSession::query()->where('unread_admin', '>', 0)->count();
    }

    public function getActiveCountProperty(): int
    {
        return ChatSession::query()->where('status', 'active')->count();
    }

    public function getClosedCountProperty(): int
    {
        return ChatSession::query()->where('status', 'closed')->count();
    }

    public function getTotalCountProperty(): int
    {
        return ChatSession::query()->count();
    }

    public function getCustomerDetailsProperty(): ?\App\Models\Customer
    {
        if (! $this->currentSession) {
            return null;
        }

        if ($this->currentSession->customer) {
            return $this->currentSession->customer;
        }

        if (! empty($this->currentSession->customer_phone)) {
            return \App\Models\Customer::query()->where('phone', $this->currentSession->customer_phone)->first();
        }

        return null;
    }
}
