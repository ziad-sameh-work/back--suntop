<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\User;
use App\Events\NewChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestChatController extends Controller
{
    /**
     * Send a test message with real-time broadcasting
     * POST /api/test/send-message
     */
    public function sendTestMessage(Request $request)
    {
        $request->validate([
            'chat_id' => 'required|exists:chats,id',
            'message' => 'required|string|max:1000',
            'sender_type' => 'required|in:admin,customer'
        ]);

        // Get or create a test user
        $user = Auth::user();
        if (!$user) {
            // Create a test user if not authenticated
            $user = User::firstOrCreate([
                'email' => 'test@example.com'
            ], [
                'name' => 'Test User',
                'password' => bcrypt('password'),
                'role' => $request->sender_type
            ]);
        }

        $chat = Chat::findOrFail($request->chat_id);

        // Create the message
        $message = ChatMessage::create([
            'chat_id' => $chat->id,
            'sender_id' => $user->id,
            'sender_type' => $request->sender_type,
            'message' => $request->message,
            'message_type' => 'text',
            'metadata' => [
                'sent_from' => 'test_api',
                'test_mode' => true
            ]
        ]);

        // Fire the real-time event
        event(new NewChatMessage($message));

        return response()->json([
            'success' => true,
            'message' => 'Test message sent successfully',
            'data' => [
                'message_id' => $message->id,
                'chat_id' => $chat->id,
                'message' => $message->message,
                'sender_type' => $message->sender_type,
                'created_at' => $message->created_at,
                'channels' => [
                    'chat.' . $chat->id,
                    'private-admin.chats',
                    'admin-chats-public'
                ],
                'event_name' => 'message.new'
            ]
        ]);
    }

    /**
     * Create a test chat for testing
     * POST /api/test/create-chat
     */
    public function createTestChat(Request $request)
    {
        $request->validate([
            'subject' => 'string|max:255',
            'customer_email' => 'email'
        ]);

        // Get or create customer
        $customer = User::firstOrCreate([
            'email' => $request->customer_email ?? 'testcustomer@example.com'
        ], [
            'name' => 'Test Customer',
            'password' => bcrypt('password'),
            'role' => 'customer'
        ]);

        // Create chat
        $chat = Chat::create([
            'customer_id' => $customer->id,
            'subject' => $request->subject ?? 'Test Chat - ' . now()->format('Y-m-d H:i:s'),
            'status' => 'open',
            'priority' => 'medium',
            'customer_unread_count' => 0,
            'admin_unread_count' => 0,
            'last_message_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Test chat created successfully',
            'data' => [
                'chat_id' => $chat->id,
                'subject' => $chat->subject,
                'customer_id' => $customer->id,
                'customer_email' => $customer->email,
                'status' => $chat->status
            ]
        ]);
    }

    /**
     * Get chat messages for testing
     * GET /api/test/chat/{chatId}/messages
     */
    public function getChatMessages($chatId)
    {
        $chat = Chat::with(['customer', 'messages.sender'])->findOrFail($chatId);

        return response()->json([
            'success' => true,
            'data' => [
                'chat' => [
                    'id' => $chat->id,
                    'subject' => $chat->subject,
                    'status' => $chat->status,
                    'customer' => $chat->customer
                ],
                'messages' => $chat->messages->map(function($message) {
                    return [
                        'id' => $message->id,
                        'message' => $message->message,
                        'sender_type' => $message->sender_type,
                        'sender' => $message->sender,
                        'created_at' => $message->created_at,
                        'formatted_time' => $message->created_at->format('H:i')
                    ];
                })
            ]
        ]);
    }

    /**
     * Test Pusher connection and broadcasting
     * POST /api/test/pusher-broadcast
     */
    public function testPusherBroadcast(Request $request)
    {
        $request->validate([
            'channel' => 'required|string',
            'event' => 'required|string',
            'data' => 'array'
        ]);

        try {
            // Test direct Pusher broadcast
            $pusher = app('pusher');
            
            $result = $pusher->trigger(
                $request->channel,
                $request->event,
                $request->data ?? ['test' => true, 'timestamp' => now()]
            );

            return response()->json([
                'success' => true,
                'message' => 'Pusher broadcast sent successfully',
                'data' => [
                    'channel' => $request->channel,
                    'event' => $request->event,
                    'pusher_response' => $result,
                    'timestamp' => now()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Pusher broadcast failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
