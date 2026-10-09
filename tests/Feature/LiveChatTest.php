<?php

namespace Tests\Feature;

use App\Filament\Pages\LiveChatPage;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\Customer;
use App\Models\Setting;
use App\Models\User;
use App\Services\LiveChatService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class LiveChatTest extends TestCase
{
    use RefreshDatabase;

    private function conversation(string $name = 'Khách thử nghiệm'): ChatSession
    {
        return ChatSession::create(['session_token' => Str::random(64), 'customer_name' => $name, 'status' => 'active', 'last_message_at' => now()]);
    }

    private function browser(ChatSession $session): self
    {
        return $this->withSession(['live_chat_token' => $session->session_token, 'live_chat_owner' => null]);
    }

    public function test_init_restores_server_session_without_exposing_token_and_uses_store_brand(): void
    {
        Setting::set('site_name', 'Cửa hàng kiểm thử');
        $response = $this->postJson(route('chat.init'), ['customer_name' => 'Khách kiểm thử', 'customer_phone' => '+84 901 234 567']);
        $response->assertOk()->assertJsonPath('success', true)->assertJsonMissingPath('session_token')->assertJsonMissingPath('session_id');
        $this->assertStringContainsString('Cửa hàng kiểm thử', $response->json('messages.0.message'));
        $session = ChatSession::firstOrFail();
        $this->assertSame('0901234567', $session->customer_phone);
        $this->postJson(route('chat.init'), ['customer_name' => 'Không đổi chủ'])->assertOk();
        $this->getJson(route('chat.messages'))->assertOk()->assertJsonPath('initialized', true);
        $this->assertSame(1, ChatSession::count());
        $this->assertSame(1, ChatMessage::count());
        $this->assertArrayNotHasKey('session_token', $session->toArray());
    }

    public function test_bearer_token_and_session_ids_cannot_access_another_browser_conversation(): void
    {
        $victim = $this->conversation();
        $victim->messages()->create(['sender_type' => 'admin', 'sender_name' => 'Hỗ trợ', 'message' => 'Nội dung riêng tư']);
        $this->getJson(route('chat.messages', ['session_token' => $victim->session_token]))->assertOk()->assertJsonPath('initialized', false)->assertJsonCount(0, 'messages');
        $this->postJson(route('chat.send'), ['session_token' => $victim->session_token, 'message' => 'Giả mạo', 'request_id' => (string) Str::uuid()])->assertForbidden();
        $this->postJson(route('chat.init'), ['session_token' => $victim->session_token])->assertOk();
        $this->assertSame(2, ChatSession::count());
        $this->getJson(route('chat.messages'))->assertJsonMissing(['message' => 'Nội dung riêng tư']);
        $this->postJson(route('chat.read'), ['session_id' => $victim->id, 'from_id' => 1, 'through_id' => 999])->assertOk();
        $this->assertFalse($victim->messages()->first()->is_read);
    }

    public function test_send_validates_content_contacts_and_cursor(): void
    {
        $this->postJson(route('chat.init'), ['customer_phone' => 'abc0901234567'])->assertUnprocessable()->assertJsonValidationErrors('customer_phone');
        $session = $this->conversation();
        foreach (['   ', str_repeat('a', 2001), "x\0y"] as $value) {
            $this->browser($session)->postJson(route('chat.send'), ['message' => $value, 'request_id' => (string) Str::uuid()])->assertUnprocessable()->assertJsonValidationErrors('message');
        }
        $this->browser($session)->postJson(route('chat.send'), ['message' => 'Nội dung', 'request_id' => 'bad'])->assertUnprocessable()->assertJsonValidationErrors('request_id');
        $this->browser($session)->getJson(route('chat.messages', ['after_id' => -1]))->assertUnprocessable();
        $this->assertSame(0, ChatMessage::count());
    }

    public function test_retry_is_deduplicated_and_client_cannot_spoof_sender(): void
    {
        $session = $this->conversation();
        $payload = ['request_id' => (string) Str::uuid(), 'message' => 'Hỏi giá', 'sender_type' => 'admin', 'is_read' => true, 'customer_id' => 999];
        $first = $this->browser($session)->postJson(route('chat.send'), $payload)->assertOk();
        $second = $this->browser($session)->postJson(route('chat.send'), $payload)->assertOk();
        $this->assertSame($first->json('message.id'), $second->json('message.id'));
        $this->assertSame(1, ChatMessage::count());
        $first->assertJsonPath('message.sender_type', 'customer')->assertJsonPath('message.is_read', false)->assertJsonMissingPath('message.chat_session_id');
        $this->assertSame(1, $session->fresh()->unread_admin);
    }

    public function test_polling_is_read_only_and_acknowledgement_preserves_new_and_unfetched_messages(): void
    {
        $session = $this->conversation();
        $service = app(LiveChatService::class);
        $admin = User::factory()->create(['role' => 'admin']);
        $older = $service->reply($admin, $session, 'Tin cũ');
        $shown = $service->reply($admin, $session, 'Tin đang hiển thị');
        $newer = $service->reply($admin, $session, 'Tin đến sau');
        $this->browser($session)->getJson(route('chat.messages'))->assertOk()->assertJsonPath('unread_count', 3);
        $this->assertSame(3, $session->fresh()->unread_customer);
        $this->browser($session)->postJson(route('chat.read'), ['from_id' => $shown->id, 'through_id' => $shown->id])->assertOk()->assertJsonPath('unread_count', 2);
        $this->assertTrue($shown->fresh()->is_read);
        $this->assertFalse($older->fresh()->is_read);
        $this->assertFalse($newer->fresh()->is_read);
    }

    public function test_public_history_is_bounded_and_after_cursor_never_skips_replies(): void
    {
        $session = $this->conversation();
        for ($i = 0; $i < 65; $i++) {
            $session->messages()->create(['sender_type' => 'customer', 'sender_name' => 'Khách', 'message' => 'Tin '.$i]);
        }
        $last = $this->browser($session)->getJson(route('chat.messages'))->assertOk()->assertJsonCount(50, 'messages')->assertJsonPath('has_more', true);
        $this->browser($session)->getJson(route('chat.messages', ['before_id' => $last->json('messages.0.id')]))->assertOk()->assertJsonCount(15, 'messages')->assertJsonPath('has_more', false);
        $this->browser($session)->getJson(route('chat.messages', ['after_id' => 0]))->assertJsonPath('messages.0.message', 'Tin 0')->assertJsonPath('messages.49.message', 'Tin 49')->assertJsonPath('has_more', true);
    }

    public function test_closed_chat_requires_admin_reopen_but_new_customer_message_reopens(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);
        $session = $this->conversation();
        Livewire::test(LiveChatPage::class)->call('selectSession', $session->id)->call('toggleSessionStatus')->set('replyMessage', 'Trả lời')->call('sendReply')->assertHasErrors('replyMessage');
        $this->assertSame('closed', $session->fresh()->status);
        Livewire::test(LiveChatPage::class)->call('selectSession', $session->id)->call('toggleSessionStatus')->set('replyMessage', 'Trả lời')->call('sendReply')->assertHasNoErrors();
        app(LiveChatService::class)->toggle($admin, $session);
        $this->browser($session)->postJson(route('chat.send'), ['message' => 'Cần hỗ trợ tiếp', 'request_id' => (string) Str::uuid()])->assertOk();
        $this->assertSame('active', $session->fresh()->status);
    }

    public function test_admin_filters_searches_uses_trusted_templates_and_paginated_messages(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $session = $this->conversation('Khách tìm kiếm');
        $service = app(LiveChatService::class);
        $service->sendCustomer($session, 'Tư vấn', (string) Str::uuid());
        $closed = $this->conversation('Khách đã đóng');
        $closed->update(['status' => 'closed']);
        $component = Livewire::test(LiveChatPage::class)->assertSee('Khách tìm kiếm')->set('filter', 'closed')->assertSee('Khách đã đóng')->assertDontSee('Khách tìm kiếm');
        $component->set('filter', 'all')->set('search', 'tìm kiếm')->assertSee('Khách tìm kiếm')->assertDontSee('Khách đã đóng')->call('selectSession', $session->id)->call('useQuickReply', 1);
        $this->assertStringContainsString('số lượng', $component->get('replyMessage'));
        $component->set('replyMessage', str_repeat('x', 2001))->call('sendReply')->assertHasErrors('replyMessage');
        $component->set('replyMessage', 'Đã nhận yêu cầu')->call('sendReply')->assertHasNoErrors();
        $this->assertSame(0, $session->fresh()->unread_admin);
        $this->assertSame(1, $session->fresh()->unread_customer);
    }

    public function test_non_admin_cannot_read_or_reply_even_through_livewire_or_service(): void
    {
        $session = $this->conversation();
        foreach (['cashier', 'technician'] as $role) {
            $user = User::factory()->create(['role' => $role]);
            $this->actingAs($user);
            $this->assertFalse(Gate::allows('manage-live-chat'));
            Livewire::test(LiveChatPage::class)->assertForbidden();
            $this->get('/admin/live-chat-page')->assertForbidden();
        }
        $this->assertSame(0, ChatMessage::count());
    }

    public function test_customer_account_change_does_not_restore_previous_guest_chat(): void
    {
        $guest = $this->conversation('Khách vãng lai');
        $customer = Customer::create(['name' => 'Tài khoản thử', 'phone' => '0901234567']);
        $this->actingAs($customer, 'customer');
        $this->browser($guest)->getJson(route('chat.messages'))->assertJsonPath('initialized', false);
        $this->postJson(route('chat.init'), ['customer_name' => 'Giả danh'])->assertOk();
        $this->assertDatabaseHas('chat_sessions', ['customer_id' => $customer->id, 'customer_name' => $customer->name]);
    }

    public function test_spam_is_limited_without_interfering_with_polling(): void
    {
        $session = $this->conversation();
        for ($i = 0; $i < 20; $i++) {
            $this->browser($session)->postJson(route('chat.send'), ['message' => 'Tin '.$i, 'request_id' => (string) Str::uuid()])->assertOk();
        }
        $this->browser($session)->postJson(route('chat.send'), ['message' => 'Tin bị chặn', 'request_id' => (string) Str::uuid()])->assertTooManyRequests();
        $this->browser($session)->getJson(route('chat.messages'))->assertOk();
        $this->assertSame(20, ChatMessage::count());
    }
}
