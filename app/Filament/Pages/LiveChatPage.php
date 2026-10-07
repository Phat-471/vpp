<?php

namespace App\Filament\Pages;

use App\Models\ChatMessage;
use App\Models\ChatSession;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LiveChatPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'CSKH & Tương Tác';

    protected static ?string $navigationLabel = 'Hỗ trợ trực tuyến (Live Chat)';

    protected static ?string $title = 'Trung Tâm Live Chat & CSKH Trực Tuyến';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.live-chat-page';

    public ?int $selectedSessionId = null;
    public string $replyMessage = '';
    public string $filter = 'all'; // all, unread, active
    public string $search = '';

    public array $quickReplies = [
        'Dạ em chào anh/chị ạ! Em là nhân viên tư vấn của VPP Ánh Dương. Em có thể hỗ trợ gì cho anh/chị hôm nay ạ?',
        'Dạ sản phẩm này bên em đang có sẵn số lượng lớn tại kho. Bên em hỗ trợ giao nhanh trong 2 giờ ạ!',
        'Dạ về dịch vụ nạp mực / sửa máy in tận nơi, kỹ thuật viên bên em sẽ đến sau 15-30 phút ạ. Anh/chị cho em xin địa chỉ cụ thể nhé ạ!',
        'Dạ đơn hàng mua sỉ / số lượng lớn bên em có chính sách chiết khấu 15% - 30% và xuất đầy đủ hóa đơn VAT điện tử ạ.',
        'Dạ vâng ạ, bên em đã ghi nhận thông tin và sẽ xử lý ngay cho anh/chị ạ!',
    ];

    public function mount(): void
    {
        $firstSession = ChatSession::orderBy('last_message_at', 'desc')->first();
        if ($firstSession) {
            $this->selectSession($firstSession->id);
        }
    }

    public function selectSession(int $sessionId): void
    {
        $this->selectedSessionId = $sessionId;
        $session = ChatSession::find($sessionId);
        if ($session) {
            $session->update(['unread_admin' => 0]);
            ChatMessage::where('chat_session_id', $sessionId)
                ->where('sender_type', 'customer')
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }
    }

    public function sendReply(): void
    {
        $clean = trim($this->replyMessage);
        if (empty($clean) || !$this->selectedSessionId) {
            return;
        }

        $session = ChatSession::find($this->selectedSessionId);
        if (!$session) {
            Notification::make()->title('Không tìm thấy phiên chat!')->danger()->send();
            return;
        }

        DB::transaction(function () use ($session, $clean) {
            $adminName = Auth::user()?->name ?? 'Quản trị viên';

            ChatMessage::create([
                'chat_session_id' => $session->id,
                'sender_type' => 'admin',
                'sender_name' => $adminName,
                'message' => $clean,
                'is_read' => false,
            ]);

            $session->unread_customer += 1;
            $session->last_message = Str::limit($clean, 80);
            $session->last_message_at = now();
            $session->save();
        });

        $this->replyMessage = '';

        Notification::make()
            ->title('Đã gửi phản hồi cho khách hàng')
            ->success()
            ->send();
    }

    public function useQuickReply(string $template): void
    {
        $this->replyMessage = $template;
    }

    public function toggleSessionStatus(): void
    {
        if (!$this->selectedSessionId) return;

        $session = ChatSession::find($this->selectedSessionId);
        if ($session) {
            $newStatus = $session->status === 'active' ? 'closed' : 'active';
            $session->update(['status' => $newStatus]);

            Notification::make()
                ->title($newStatus === 'active' ? 'Đã mở lại phiên chat' : 'Đã đóng phiên chat')
                ->info()
                ->send();
        }
    }

    public function getSessionsProperty()
    {
        $query = ChatSession::orderBy('last_message_at', 'desc');

        if ($this->filter === 'unread') {
            $query->where('unread_admin', '>', 0);
        } elseif ($this->filter === 'active') {
            $query->where('status', 'active');
        }

        if (!empty($this->search)) {
            $search = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'LIKE', $search)
                  ->orWhere('customer_phone', 'LIKE', $search)
                  ->orWhere('last_message', 'LIKE', $search);
            });
        }

        return $query->take(30)->get();
    }

    public function getCurrentSessionProperty()
    {
        if (!$this->selectedSessionId) {
            return null;
        }

        return ChatSession::with(['messages'])->find($this->selectedSessionId);
    }
}
