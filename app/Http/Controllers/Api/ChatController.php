<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    /**
     * Get or create a chat for the authenticated customer
     */
    public function getOrCreateChat(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if (!$user || $user->role !== 'customer') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Customer access required.'
                ], 401);
            }

            // Get existing open/in_progress chat or create new one
            $chat = Chat::where('customer_id', $user->id)
                ->whereIn('status', ['open', 'in_progress'])
                ->with(['messages.sender', 'assignedAdmin'])
                ->first();

            if (!$chat) {
                $chat = Chat::create([
                    'customer_id' => $user->id,
                    'subject' => $request->subject ?? 'دعم عام',
                    'status' => 'open',
                    'priority' => $request->priority ?? 'medium',
                    'metadata' => [
                        'user_agent' => $request->header('User-Agent'),
                        'ip_address' => $request->ip(),
                        'created_from' => 'api'
                    ]
                ]);

                $chat->load(['messages.sender', 'assignedAdmin']);
            } else {
                // Mark messages as read for customer
                $chat->markAsRead('customer');
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'chat' => [
                        'id' => $chat->id,
                        'subject' => $chat->subject,
                        'status' => $chat->status,
                        'priority' => $chat->priority,
                        'created_at' => $chat->created_at->toISOString(),
                        'assigned_admin' => $chat->assignedAdmin ? [
                            'id' => $chat->assignedAdmin->id,
                            'name' => $chat->assignedAdmin->name,
                            'full_name' => $chat->assignedAdmin->full_name
                        ] : null,
                        'unread_count' => $chat->customer_unread_count
                    ],
                    'messages' => $chat->messages->map(function ($message) {
                        return [
                            'id' => $message->id,
                            'message' => $message->message,
                            'message_type' => $message->message_type,
                            'sender_type' => $message->sender_type,
                            'sender' => [
                                'id' => $message->sender->id,
                                'name' => $message->sender->name,
                                'full_name' => $message->sender->full_name ?? $message->sender->name
                            ],
                            'attachment_url' => $message->attachment_url,
                            'attachment_name' => $message->attachment_name,
                            'created_at' => $message->created_at->toISOString(),
                            'formatted_time' => $message->formatted_time,
                            'is_read' => $message->is_read
                        ];
                    })
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get chat: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send a message in the chat
     */
    public function sendMessage(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if (!$user || $user->role !== 'customer') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Customer access required.'
                ], 401);
            }

            $validator = Validator::make($request->all(), [
                'chat_id' => 'required|exists:chats,id',
                'message' => 'required_without:attachment|string|max:2000',
                'attachment' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,txt'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $chat = Chat::findOrFail($request->chat_id);

            // Verify chat belongs to the customer
            if ($chat->customer_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to send message in this chat.'
                ], 403);
            }

            // Handle file attachment if present
            $attachmentPath = null;
            $attachmentName = null;
            $messageType = 'text';

            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $attachmentName = $file->getClientOriginalName();
                $attachmentPath = $file->store('chat-attachments/' . $chat->id, 'public');
                
                if (in_array($file->getClientOriginalExtension(), ['jpg', 'jpeg', 'png', 'gif'])) {
                    $messageType = 'image';
                } else {
                    $messageType = 'file';
                }
            }

            $message = ChatMessage::create([
                'chat_id' => $chat->id,
                'sender_id' => $user->id,
                'sender_type' => 'customer',
                'message' => $request->message ?? '',
                'message_type' => $messageType,
                'attachment_path' => $attachmentPath,
                'attachment_name' => $attachmentName,
                'metadata' => [
                    'user_agent' => $request->header('User-Agent'),
                    'ip_address' => $request->ip()
                ]
            ]);

            // Update chat status if it was closed/resolved
            if (in_array($chat->status, ['resolved', 'closed'])) {
                $chat->update(['status' => 'open']);
            }

            $message->load('sender');

            // Fire the real-time event for Flutter
            event(new \App\Events\NewChatMessage($message));

            return response()->json([
                'success' => true,
                'data' => [
                    'message' => [
                        'id' => $message->id,
                        'message' => $message->message,
                        'message_type' => $message->message_type,
                        'sender_type' => $message->sender_type,
                        'sender' => [
                            'id' => $message->sender->id,
                            'name' => $message->sender->name,
                            'full_name' => $message->sender->full_name ?? $message->sender->name
                        ],
                        'attachment_url' => $message->attachment_url,
                        'attachment_name' => $message->attachment_name,
                        'created_at' => $message->created_at->toISOString(),
                        'formatted_time' => $message->formatted_time
                    ]
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
     * Get chat messages with pagination
     */
    public function getMessages(Request $request, $chatId): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if (!$user || $user->role !== 'customer') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Customer access required.'
                ], 401);
            }

            $chat = Chat::findOrFail($chatId);

            // Verify chat belongs to the customer
            if ($chat->customer_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to access this chat.'
                ], 403);
            }

            $perPage = $request->get('per_page', 50);
            $page = $request->get('page', 1);

            $messages = $chat->messages()
                ->with('sender')
                ->latest()
                ->paginate($perPage, ['*'], 'page', $page);

            // Mark messages as read for customer
            $chat->markAsRead('customer');

            return response()->json([
                'success' => true,
                'data' => [
                    'messages' => $messages->items(),
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
     * Get customer's chat history
     */
    public function getChatHistory(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if (!$user || $user->role !== 'customer') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Customer access required.'
                ], 401);
            }

            $perPage = $request->get('per_page', 20);
            
            $chats = Chat::where('customer_id', $user->id)
                ->with(['latestMessage.sender', 'assignedAdmin'])
                ->orderBy('last_message_at', 'desc')
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => [
                    'chats' => $chats->map(function ($chat) {
                        return [
                            'id' => $chat->id,
                            'subject' => $chat->subject,
                            'status' => $chat->status,
                            'priority' => $chat->priority,
                            'created_at' => $chat->created_at->toISOString(),
                            'last_message_at' => $chat->last_message_at?->toISOString(),
                            'unread_count' => $chat->customer_unread_count,
                            'assigned_admin' => $chat->assignedAdmin ? [
                                'id' => $chat->assignedAdmin->id,
                                'name' => $chat->assignedAdmin->name,
                                'full_name' => $chat->assignedAdmin->full_name
                            ] : null,
                            'latest_message' => $chat->latestMessage->first() ? [
                                'message' => Str::limit($chat->latestMessage->first()->message, 100),
                                'sender_type' => $chat->latestMessage->first()->sender_type,
                                'created_at' => $chat->latestMessage->first()->created_at->toISOString()
                            ] : null
                        ];
                    }),
                    'pagination' => [
                        'current_page' => $chats->currentPage(),
                        'last_page' => $chats->lastPage(),
                        'per_page' => $chats->perPage(),
                        'total' => $chats->total()
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get chat history: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark chat messages as read
     */
    public function markAsRead(Request $request, $chatId): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if (!$user || $user->role !== 'customer') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Customer access required.'
                ], 401);
            }

            $chat = Chat::findOrFail($chatId);

            // Verify chat belongs to the customer
            if ($chat->customer_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to access this chat.'
                ], 403);
            }

            $chat->markAsRead('customer');

            return response()->json([
                'success' => true,
                'message' => 'Messages marked as read successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark messages as read: ' . $e->getMessage()
            ], 500);
        }
    }
}
