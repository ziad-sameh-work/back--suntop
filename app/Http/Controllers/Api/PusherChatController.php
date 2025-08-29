<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PusherChat;
use App\Models\PusherMessage;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PusherChatController extends Controller
{
    /**
     * Get or create a chat for the authenticated customer
     */
    public function getOrCreateChat(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if (!$user || $user->role === 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Customer access required.'
                ], 401);
            }

            // Find existing active chat or create new one
            $chat = PusherChat::where('user_id', $user->id)
                ->where('status', PusherChat::STATUS_ACTIVE)
                ->with(['messages.user', 'user'])
                ->first();

            if (!$chat) {
                $chat = PusherChat::create([
                    'user_id' => $user->id,
                    'status' => PusherChat::STATUS_ACTIVE,
                    'title' => 'Chat with ' . $user->name,
                    'metadata' => [
                        'customer_ip' => $request->ip(),
                        'user_agent' => $request->header('User-Agent'),
                        'created_from' => 'api'
                    ]
                ]);
                
                $chat->load(['messages.user', 'user']);
            }

            // Mark admin messages as read for customer
            $chat->markMessagesAsRead('customer');

            return response()->json([
                'success' => true,
                'data' => [
                    'chat' => $this->formatChatResponse($chat),
                    'messages' => $chat->messages->map(function ($message) {
                        return $this->formatMessageResponse($message);
                    })
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get/create chat: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send a message
     */
    public function sendMessage(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'chat_id' => 'required|exists:pusher_chats,id',
                'message' => 'required|string|max:2000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();
            $chat = PusherChat::findOrFail($request->chat_id);

            // Check authorization
            if ($user->role !== 'admin' && $chat->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to send message in this chat.'
                ], 403);
            }

            // Determine sender type
            $senderType = $user->role === 'admin' ? PusherMessage::SENDER_ADMIN : PusherMessage::SENDER_CUSTOMER;

            // Create message in transaction
            $message = DB::transaction(function () use ($chat, $user, $request, $senderType) {
                $message = PusherMessage::create([
                    'chat_id' => $chat->id,
                    'user_id' => $user->id,
                    'message' => $request->message,
                    'sender_type' => $senderType,
                    'metadata' => [
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->header('User-Agent'),
                    ]
                ]);

                // Update chat's last message time
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
                'data' => [
                    'message' => $this->formatMessageResponse($message)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send message: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get messages for a specific chat
     */
    public function getMessages(Request $request, $chatId): JsonResponse
    {
        try {
            $user = Auth::user();
            $chat = PusherChat::with(['user'])->findOrFail($chatId);

            // Check authorization
            if ($user->role !== 'admin' && $chat->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to access this chat.'
                ], 403);
            }

            $perPage = $request->get('per_page', 50);
            $page = $request->get('page', 1);

            $messages = $chat->messages()
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            // Mark messages as read based on user type
            $userType = $user->role === 'admin' ? 'admin' : 'customer';
            $chat->markMessagesAsRead($userType);

            return response()->json([
                'success' => true,
                'data' => [
                    'chat' => $this->formatChatResponse($chat),
                    'messages' => collect($messages->items())->map(function ($message) {
                        return $this->formatMessageResponse($message);
                    }),
                    'pagination' => [
                        'current_page' => $messages->currentPage(),
                        'last_page' => $messages->lastPage(),
                        'per_page' => $messages->perPage(),
                        'total' => $messages->total(),
                        'has_more' => $messages->hasMorePages()
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get messages: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all chats (for admins)
     */
    public function getAllChats(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if ($user->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.'
                ], 401);
            }

            $status = $request->get('status', PusherChat::STATUS_ACTIVE);
            $perPage = $request->get('per_page', 20);

            $chats = PusherChat::with(['user', 'latestMessage.user'])
                ->where('status', $status)
                ->withRecentActivity()
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => [
                    'chats' => collect($chats->items())->map(function ($chat) {
                        return $this->formatChatResponse($chat, true);
                    }),
                    'pagination' => [
                        'current_page' => $chats->currentPage(),
                        'last_page' => $chats->lastPage(),
                        'per_page' => $chats->perPage(),
                        'total' => $chats->total(),
                        'has_more' => $chats->hasMorePages()
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get chats: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Admin reply to a chat
     */
    public function adminReply(Request $request, $chatId): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if ($user->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.'
                ], 401);
            }

            $validator = Validator::make($request->all(), [
                'message' => 'required|string|max:2000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $chat = PusherChat::findOrFail($chatId);

            // Create admin message
            $message = DB::transaction(function () use ($chat, $user, $request) {
                $message = PusherMessage::create([
                    'chat_id' => $chat->id,
                    'user_id' => $user->id,
                    'message' => $request->message,
                    'sender_type' => PusherMessage::SENDER_ADMIN,
                    'metadata' => [
                        'admin_id' => $user->id,
                        'admin_name' => $user->name,
                        'ip_address' => $request->ip(),
                    ]
                ]);

                // Update chat
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
                'data' => [
                    'message' => $this->formatMessageResponse($message)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send admin reply: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark chat as closed
     */
    public function closeChat(Request $request, $chatId): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if ($user->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.'
                ], 401);
            }

            $chat = PusherChat::findOrFail($chatId);
            $chat->update(['status' => PusherChat::STATUS_CLOSED]);

            return response()->json([
                'success' => true,
                'message' => 'Chat closed successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to close chat: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Format chat response
     */
    private function formatChatResponse(PusherChat $chat, bool $includeLatestMessage = false): array
    {
        $response = [
            'id' => $chat->id,
            'user_id' => $chat->user_id,
            'status' => $chat->status,
            'title' => $chat->title,
            'last_message_at' => $chat->last_message_at?->toISOString(),
            'unread_admin_count' => $chat->unread_admin_count,
            'unread_customer_count' => $chat->unread_customer_count,
            'created_at' => $chat->created_at->toISOString(),
            'updated_at' => $chat->updated_at->toISOString(),
            'customer' => [
                'id' => $chat->user->id,
                'name' => $chat->user->name,
                'email' => $chat->user->email,
            ],
        ];

        if ($includeLatestMessage && $chat->latestMessage) {
            $response['latest_message'] = $this->formatMessageResponse($chat->latestMessage);
        }

        return $response;
    }

    /**
     * Format message response
     */
    private function formatMessageResponse(PusherMessage $message): array
    {
        return [
            'id' => $message->id,
            'chat_id' => $message->chat_id,
            'user_id' => $message->user_id,
            'message' => $message->message,
            'sender_type' => $message->sender_type,
            'is_read' => $message->is_read,
            'created_at' => $message->created_at->toISOString(),
            'formatted_time' => $message->formatted_time,
            'formatted_date' => $message->formatted_date,
            'user' => [
                'id' => $message->user->id,
                'name' => $message->user->name,
                'email' => $message->user->email,
                'role' => $message->user->role ?? 'customer',
            ],
            'metadata' => $message->metadata,
        ];
    }
}
