<?php

namespace App\Services;

use App\Enums\ChatStatus;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\User;
use App\Support\StorefrontSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class LiveChatService
{
    public function browserSession(Request $request): ?ChatSession
    {
        $owner = $request->user('customer')?->getAuthIdentifier();
        if ($request->session()->get('live_chat_owner') !== $owner) {
            $request->session()->forget(['live_chat_token', 'live_chat_owner']);

            return null;
        }
        $token = $request->session()->get('live_chat_token');

        return is_string($token) ? ChatSession::where('session_token', $token)->first() : null;
    }

    public function initialize(Request $request, array $contact): ChatSession
    {
        if ($session = $this->browserSession($request)) {
            return $session;
        }
        $customer = $request->user('customer');
        $session = DB::transaction(function () use ($customer, $contact) {
            $session = ChatSession::create([
                'session_token' => Str::random(64), 'customer_id' => $customer?->id,
                'customer_name' => $customer?->name ?: ($contact['customer_name'] ?? 'Khách vãng lai'),
                'customer_phone' => $customer?->phone ?: ($contact['customer_phone'] ?? null),
                'status' => ChatStatus::Active->value, 'last_message_at' => now(),
            ]);
            $brand = app(StorefrontSettings::class)->all()['site_name'];
            $session->messages()->create([
                'sender_type' => 'admin', 'sender_name' => Str::limit($brand, 100, ''),
                'message' => 'Xin chào! Cảm ơn bạn đã liên hệ '.$brand.'. Hãy để lại câu hỏi, nhân viên sẽ phản hồi tại đây.',
                'is_read' => true,
            ]);

            return $session;
        });
        $request->session()->put('live_chat_token', $session->session_token);
        $request->session()->put('live_chat_owner', $customer?->getAuthIdentifier());

        return $session;
    }

    public function messages(ChatSession $session, ?int $after = null, ?int $before = null): array
    {
        $query = $session->messages()->reorder();
        if ($before) {
            $query->where('id', '<', $before);
        } elseif ($after !== null) {
            $query->where('id', '>', $after);
        }
        $ascending = $after !== null && ! $before;
        $rows = $query->orderBy('id', $ascending ? 'asc' : 'desc')->limit(51)->get();
        $more = $rows->count() > 50;
        $rows = $rows->take(50);
        if (! $ascending) {
            $rows = $rows->reverse();
        }

        return [
            'messages' => $rows->values()->map(fn (ChatMessage $message) => $this->publicMessage($message))->all(),
            'has_more' => $more,
            'unread_count' => $session->messages()->where('sender_type', 'admin')->where('is_read', false)->count(),
            'status' => $session->fresh()->status,
        ];
    }

    public function publicMessage(ChatMessage $message): array
    {
        return $message->only(['id', 'sender_type', 'sender_name', 'message', 'is_read', 'created_at']);
    }

    public function sendCustomer(ChatSession $session, string $text, string $requestId): ChatMessage
    {
        // A retry reuses its UUID. Cache contains only an ID, never message content.
        $key = 'live-chat-send:'.$session->id.':'.hash('sha256', $requestId);

        return Cache::lock($key.':lock', 10)->block(3, function () use ($session, $text, $key) {
            if ($id = Cache::get($key)) {
                if ($message = $session->messages()->find($id)) {
                    return $message;
                }
            }
            $message = $this->write($session, 'customer', $session->customer_name, $text);
            Cache::put($key, $message->id, now()->addDay());

            return $message;
        });
    }

    public function reply(User $user, ChatSession $session, string $text): ChatMessage
    {
        Gate::forUser($user)->authorize('manage-live-chat');
        $this->limitStaff($user);

        return $this->write($session, 'admin', Str::limit($user->name, 100, ''), $text);
    }

    private function write(ChatSession $session, string $sender, string $name, string $text): ChatMessage
    {
        $text = trim($text);
        Validator::make(['replyMessage' => $text], [
            'replyMessage' => ['required', 'string', 'max:2000', 'not_regex:/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/'],
        ], [
            'replyMessage.required' => 'Nhập nội dung tin nhắn trước khi gửi.',
            'replyMessage.max' => 'Tin nhắn tối đa 2.000 ký tự.',
            'replyMessage.not_regex' => 'Tin nhắn có ký tự không hợp lệ. Hãy kiểm tra lại.',
        ])->validate();

        return DB::transaction(function () use ($session, $sender, $name, $text) {
            $locked = ChatSession::whereKey($session->id)->lockForUpdate()->firstOrFail();
            if ($sender === 'admin' && $locked->status === ChatStatus::Closed->value) {
                throw ValidationException::withMessages(['replyMessage' => 'Hội thoại đã đóng. Mở lại hội thoại trước khi trả lời.']);
            }
            $message = $locked->messages()->create([
                'sender_type' => $sender, 'sender_name' => $name, 'message' => $text, 'is_read' => false,
            ]);
            $locked->status = ChatStatus::Active->value;
            $locked->last_message = Str::limit($text, 80);
            $locked->last_message_at = now();
            $this->syncUnread($locked);

            return $message;
        }, 3);
    }

    public function markRead(ChatSession $session, string $sender, int $from, int $through): void
    {
        DB::transaction(function () use ($session, $sender, $from, $through) {
            $locked = ChatSession::whereKey($session->id)->lockForUpdate()->firstOrFail();
            $locked->messages()->where('sender_type', $sender)->whereBetween('id', [$from, $through])->where('is_read', false)->update(['is_read' => true]);
            $this->syncUnread($locked);
        }, 3);
    }

    public function markReadByAdmin(User $user, ChatSession $session, int $from, int $through): void
    {
        Gate::forUser($user)->authorize('manage-live-chat');
        $this->markRead($session, 'customer', $from, $through);
    }

    private function syncUnread(ChatSession $session): void
    {
        $session->unread_admin = $session->messages()->where('sender_type', 'customer')->where('is_read', false)->count();
        $session->unread_customer = $session->messages()->where('sender_type', 'admin')->where('is_read', false)->count();
        $session->save();
    }

    public function toggle(User $user, ChatSession $session): void
    {
        Gate::forUser($user)->authorize('manage-live-chat');
        DB::transaction(function () use ($session) {
            $locked = ChatSession::whereKey($session->id)->lockForUpdate()->firstOrFail();
            $locked->update(['status' => $locked->status === ChatStatus::Active->value ? ChatStatus::Closed->value : ChatStatus::Active->value]);
        }, 3);
    }

    private function limitStaff(User $user): void
    {
        $key = 'live-chat-admin:'.$user->id;
        if (RateLimiter::tooManyAttempts($key, 30)) {
            throw ValidationException::withMessages(['replyMessage' => 'Bạn đang gửi quá nhanh. Hãy chờ một phút rồi thử lại.']);
        }
        RateLimiter::hit($key, 60);
    }
}
