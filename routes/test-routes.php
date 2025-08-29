<?php

use Illuminate\Support\Facades\Route;
use App\Events\NewChatMessage;
use App\Models\ChatMessage;
use App\Models\Chat;

// Test route to manually trigger chat events
Route::get('/test-chat-event/{chat_id}', function ($chatId) {
    try {
        $chat = Chat::with('customer')->findOrFail($chatId);
        
        // Create a test message
        $message = ChatMessage::create([
            'chat_id' => $chat->id,
            'sender_id' => $chat->customer->id,
            'sender_type' => 'customer',
            'message' => '🧪 Test message from route - ' . now()->format('H:i:s'),
            'message_type' => 'text',
            'metadata' => [
                'sent_from' => 'test_route',
                'test' => true
            ]
        ]);
        
        // Load relationships
        $message->load(['sender', 'chat.customer']);
        
        // Trigger event
        event(new NewChatMessage($message));
        
        return response()->json([
            'success' => true,
            'message' => 'Event triggered successfully',
            'data' => [
                'message_id' => $message->id,
                'chat_id' => $chat->id,
                'sender' => $message->sender->name,
                'message_text' => $message->message,
                'channels' => [
                    "chat.{$chat->id}",
                    "private-admin.chats"
                ],
                'event' => 'message.new'
            ]
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
});

// Test Pusher configuration
Route::get('/test-pusher-config', function () {
    return response()->json([
        'broadcasting_driver' => config('broadcasting.default'),
        'pusher_config' => [
            'key' => config('broadcasting.connections.pusher.key'),
            'cluster' => config('broadcasting.connections.pusher.options.cluster'),
            'app_id' => config('broadcasting.connections.pusher.app_id'),
            'secret' => substr(config('broadcasting.connections.pusher.secret'), 0, 4) . '...',
        ],
        'env_vars' => [
            'BROADCAST_DRIVER' => env('BROADCAST_DRIVER'),
            'PUSHER_APP_KEY' => env('PUSHER_APP_KEY'),
            'PUSHER_APP_CLUSTER' => env('PUSHER_APP_CLUSTER'),
        ]
    ]);
});
