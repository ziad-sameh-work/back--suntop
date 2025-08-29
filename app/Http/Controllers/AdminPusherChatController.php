<?php

namespace App\Http\Controllers;

use App\Models\PusherChat;
use App\Models\PusherMessage;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminPusherChatController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Display the chat dashboard
     */
    public function index()
    {
        $stats = [
            'active_chats' => PusherChat::where('status', PusherChat::STATUS_ACTIVE)->count(),
            'total_chats' => PusherChat::count(),
            'total_messages' => PusherMessage::count(),
            'unread_messages' => PusherMessage::where('sender_type', PusherMessage::SENDER_CUSTOMER)
                ->where('is_read', false)->count(),
        ];

        $recentChats = PusherChat::with(['user', 'latestMessage.user'])
            ->active()
            ->withRecentActivity()
            ->take(10)
            ->get();

        return view('admin.pusher-chat.index', compact('stats', 'recentChats'));
    }

    /**
     * Display a specific chat
     */
    public function show(PusherChat $chat)
    {
        $chat->load(['user', 'messages.user']);
        
        // Mark admin messages as read
        $chat->markMessagesAsRead('admin');

        return view('admin.pusher-chat.show', compact('chat'));
    }

    /**
     * Send admin reply via AJAX
     */
    public function sendReply(Request $request, PusherChat $chat)
    {
        $request->validate([
            'message' => 'required|string|max:2000'
        ]);

        $user = Auth::user();

        $message = DB::transaction(function () use ($chat, $user, $request) {
            $message = PusherMessage::create([
                'chat_id' => $chat->id,
                'user_id' => $user->id,
                'message' => $request->message,
                'sender_type' => PusherMessage::SENDER_ADMIN,
                'metadata' => [
                    'admin_id' => $user->id,
                    'admin_name' => $user->name,
                    'sent_from' => 'admin_panel'
                ]
            ]);

            $chat->update([
                'last_message_at' => now(),
                'status' => PusherChat::STATUS_ACTIVE
            ]);

            return $message;
        });

        $message->load('user', 'chat.user');

        // Message will be broadcasted automatically from model boot method

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'message' => $message->message,
                'sender_type' => $message->sender_type,
                'user_name' => $message->user->name,
                'created_at' => $message->created_at->toISOString(),
                'formatted_time' => $message->formatted_time,
            ]
        ]);
    }

    /**
     * Get chat messages via AJAX
     */
    public function getMessages(PusherChat $chat)
    {
        $messages = $chat->messages()
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'message' => $message->message,
                    'sender_type' => $message->sender_type,
                    'user_name' => $message->user->name,
                    'created_at' => $message->created_at->toISOString(),
                    'formatted_time' => $message->formatted_time,
                    'formatted_date' => $message->formatted_date,
                ];
            });

        return response()->json([
            'success' => true,
            'messages' => $messages
        ]);
    }

    /**
     * Close a chat
     */
    public function closeChat(PusherChat $chat)
    {
        $chat->update(['status' => PusherChat::STATUS_CLOSED]);

        return response()->json([
            'success' => true,
            'message' => 'Chat closed successfully'
        ]);
    }

    /**
     * Get live stats for dashboard
     */
    public function getLiveStats()
    {
        $stats = [
            'active_chats' => PusherChat::where('status', PusherChat::STATUS_ACTIVE)->count(),
            'total_chats' => PusherChat::count(),
            'total_messages' => PusherMessage::count(),
            'unread_messages' => PusherMessage::where('sender_type', PusherMessage::SENDER_CUSTOMER)
                ->where('is_read', false)->count(),
            'timestamp' => now()->toISOString(),
        ];

        return response()->json($stats);
    }
}
