<?php

namespace App\Http\Controllers;

use App\Http\Requests\LiveChatRequest;
use App\Services\LiveChatService;
use Illuminate\Http\JsonResponse;

class LiveChatController extends Controller
{
    public function init(LiveChatRequest $request, LiveChatService $chat): JsonResponse
    {
        $session = $chat->initialize($request, $request->validated());

        return $this->respond(['success' => true, ...$chat->messages($session)]);
    }

    public function send(LiveChatRequest $request, LiveChatService $chat): JsonResponse
    {
        $session = $chat->browserSession($request);
        abort_unless($session, 403, 'Phiên hỗ trợ đã hết hạn. Hãy tải lại trang để kết nối lại.');
        $data = $request->validated();
        $message = $chat->sendCustomer($session, $data['message'], $data['request_id']);

        return $this->respond(['success' => true, 'message' => $chat->publicMessage($message)]);
    }

    public function getMessages(LiveChatRequest $request, LiveChatService $chat): JsonResponse
    {
        $session = $chat->browserSession($request);
        if (! $session) {
            return $this->respond(['success' => true, 'initialized' => false, 'messages' => [], 'unread_count' => 0]);
        }
        $data = $request->validated();

        return $this->respond(['success' => true, 'initialized' => true,
            ...$chat->messages($session, isset($data['after_id']) ? (int) $data['after_id'] : null, isset($data['before_id']) ? (int) $data['before_id'] : null),
        ]);
    }

    public function read(LiveChatRequest $request, LiveChatService $chat): JsonResponse
    {
        $session = $chat->browserSession($request);
        abort_unless($session, 403);
        $data = $request->validated();
        $chat->markRead($session, 'admin', (int) $data['from_id'], (int) $data['through_id']);

        return $this->respond(['success' => true, 'unread_count' => $session->messages()->where('sender_type', 'admin')->where('is_read', false)->count()]);
    }

    private function respond(array $data): JsonResponse
    {
        return response()->json($data)->header('Cache-Control', 'no-store, private');
    }
}
