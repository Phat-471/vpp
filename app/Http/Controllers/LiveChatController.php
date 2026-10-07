<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\ChatSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LiveChatController extends Controller
{
    /**
     * Khởi tạo hoặc khôi phục phiên chat của khách hàng
     */
    public function init(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'session_token' => 'nullable|string|max:64',
            'customer_name' => 'nullable|string|max:100',
            'customer_phone' => 'nullable|string|max:20',
        ]);

        $token = $validated['session_token'] ?? null;
        $session = null;

        if ($token) {
            $session = ChatSession::where('session_token', $token)->first();
        }

        if (!$session) {
            $token = Str::random(40);
            $session = ChatSession::create([
                'session_token' => $token,
                'customer_name' => !empty($validated['customer_name']) ? trim($validated['customer_name']) : 'Khách vãng lai',
                'customer_phone' => !empty($validated['customer_phone']) ? preg_replace('/\D/', '', $validated['customer_phone']) : null,
                'status' => 'active',
                'last_message_at' => now(),
            ]);

            // Gửi tin nhắn chào mừng tự động đầu tiên từ hệ thống
            ChatMessage::create([
                'chat_session_id' => $session->id,
                'sender_type' => 'admin',
                'sender_name' => 'Hệ thống VPP Ánh Dương',
                'message' => 'Xin chào Quý khách! VPP & Thiết Bị Máy In Ánh Dương có thể hỗ trợ gì cho Quý khách hôm nay ạ?',
                'is_read' => true,
            ]);
        }

        $messages = $session->messages()->orderBy('id', 'asc')->get();

        return response()->json([
            'success' => true,
            'session_token' => $session->session_token,
            'session_id' => $session->id,
            'customer_name' => $session->customer_name,
            'messages' => $messages,
        ]);
    }

    /**
     * Khách hàng gửi tin nhắn lên hệ thống
     */
    public function send(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'session_token' => 'required|string|max:64',
            'message' => 'required|string|min:1|max:2000',
            'customer_name' => 'nullable|string|max:100',
            'customer_phone' => 'nullable|string|max:20',
        ]);

        $session = ChatSession::where('session_token', $validated['session_token'])->first();

        if (!$session) {
            return response()->json(['success' => false, 'message' => 'Phiên chat không tồn tại'], 404);
        }

        $cleanMsg = trim(strip_tags($validated['message']));
        if (empty($cleanMsg)) {
            return response()->json(['success' => false, 'message' => 'Tin nhắn không được để trống'], 422);
        }

        $messageRecord = DB::transaction(function () use ($session, $validated, $cleanMsg) {
            if (!empty($validated['customer_name']) && $session->customer_name === 'Khách vãng lai') {
                $session->customer_name = trim($validated['customer_name']);
            }
            if (!empty($validated['customer_phone']) && empty($session->customer_phone)) {
                $session->customer_phone = preg_replace('/\D/', '', $validated['customer_phone']);
            }

            $msg = ChatMessage::create([
                'chat_session_id' => $session->id,
                'sender_type' => 'customer',
                'sender_name' => $session->customer_name,
                'message' => $cleanMsg,
                'is_read' => false,
            ]);

            $session->unread_admin += 1;
            $session->last_message = Str::limit($cleanMsg, 80);
            $session->last_message_at = now();
            $session->status = 'active';
            $session->save();

            return $msg;
        });

        return response()->json([
            'success' => true,
            'message' => $messageRecord,
        ]);
    }

    /**
     * Khách hàng lấy tin nhắn mới nhất
     */
    public function getMessages(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'session_token' => 'required|string|max:64',
            'after_id' => 'nullable|integer',
        ]);

        $session = ChatSession::where('session_token', $validated['session_token'])->first();

        if (!$session) {
            return response()->json(['success' => false, 'messages' => []]);
        }

        $query = $session->messages()->orderBy('id', 'asc');
        if (!empty($validated['after_id'])) {
            $query->where('id', '>', $validated['after_id']);
        }

        $messages = $query->get();

        // Đánh dấu khách đã đọc tin nhắn từ admin
        if ($session->unread_customer > 0) {
            $session->update(['unread_customer' => 0]);
        }

        return response()->json([
            'success' => true,
            'messages' => $messages,
        ]);
    }
}
