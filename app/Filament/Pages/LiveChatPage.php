<?php

namespace App\Filament\Pages;

use App\Models\ChatSession;
use App\Services\LiveChatService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Locked;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class LiveChatPage extends Page
{
    use WithFileUploads;
    use WithPagination;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Khách hàng';

    protected static ?string $navigationLabel = 'Chat online';

    protected static ?string $title = 'Chat trực tuyến';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.live-chat-page';

    #[Locked]
    public ?int $selectedSessionId = null;

    public string $replyMessage = '';

    public $attachment = null;

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

        $this->validate([
            'replyMessage' => $this->attachment ? ['nullable', 'string', 'max:2000'] : ['required', 'string', 'max:2000'],
            'attachment' => ['nullable', 'image', 'mimes:jpeg,png,webp,jpg', 'max:5120'],
        ], [
            'replyMessage.required' => 'Nhập nội dung trả lời trước khi gửi.',
            'attachment.image' => 'Tệp tải lên phải là hình ảnh.',
            'attachment.mimes' => 'Hình ảnh phải có định dạng JPG, PNG, WEBP.',
            'attachment.max' => 'Dung lượng ảnh tối đa là 5MB.',
        ]);

        $text = !empty($this->replyMessage) ? $this->replyMessage : '[Hình ảnh đính kèm]';
        app(LiveChatService::class)->reply(auth()->user(), $session, $text, $this->attachment);
        $this->replyMessage = '';
        $this->attachment = null;
        $this->resetPage('messagesPage');
        $this->dispatch('chat-reply-sent');
    }

    public function removeAttachment(): void
    {
        $this->attachment = null;
    }

    public function getQuickRepliesProperty(): array
    {
        return [
            'Xin chào anh/chị! Em có thể tư vấn sản phẩm hay hỗ trợ máy in giúp mình ạ?',
            'Anh/chị cho em xin tên sản phẩm và số lượng cần mua để em kiểm tra giá và tồn kho nhé.',
            'Anh/chị cho em xin dòng máy in và mô tả lỗi (kẹt giấy, in mờ, kêu to...) để kỹ thuật tư vấn ạ.',
            'Dạ bên em có dịch vụ nạp mực & sửa máy in tận nơi. Anh/chị cho em xin địa chỉ và SĐT để kỹ thuật viên liên hệ nhé.',
            'Dạ anh/chị có thể cung cấp mã đơn hàng hoặc SĐT đặt hàng để em kiểm tra tình trạng giao hàng ngay ạ.',
            'Dạ công ty mình cần xuất hóa đơn VAT xin gửi giúp em: Tên công ty, Mã số thuế và Địa chỉ đăng ký thuế ạ.',
            'Bên em đã ghi nhận yêu cầu và chuyển bộ phận xử lý. Nhân viên sẽ kiểm tra và phản hồi tại đây ngay ạ.',
            'Dạ cảm ơn anh/chị đã liên hệ. Nếu cần hỗ trợ thêm, anh/chị cứ nhắn lại cho em nhé. Chúc anh/chị một ngày tốt lành!',
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

    public function getCustomerOrdersProperty()
    {
        if (! $this->currentSession) {
            return collect();
        }

        if ($customer = $this->customerDetails) {
            return $customer->orders()->latest('id')->limit(3)->get();
        }

        if (! empty($this->currentSession->customer_phone)) {
            return \App\Models\Order::query()->where('customer_phone', $this->currentSession->customer_phone)->latest('id')->limit(3)->get();
        }

        return collect();
    }

    public function getCustomerRepairTicketsProperty()
    {
        if (! $this->currentSession) {
            return collect();
        }

        if ($customer = $this->customerDetails) {
            return $customer->repairTickets()->latest('id')->limit(3)->get();
        }

        if (! empty($this->currentSession->customer_phone)) {
            return \App\Models\RepairTicket::query()->where('customer_phone', $this->currentSession->customer_phone)->latest('id')->limit(3)->get();
        }

        return collect();
    }
}
