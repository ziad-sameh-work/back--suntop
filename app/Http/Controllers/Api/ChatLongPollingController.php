<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;

class ChatLongPollingController extends Controller
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
                        'created_from' => 'mobile_app'
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
                        'unread_count' => $chat->customer_unread_count,
                        'last_message_timestamp' => $chat->last_message_at ? $chat->last_message_at->timestamp : 0
                    ],
                    'messages' => $chat->messages->map(function ($message) {
                        return $this->formatMessage($message);
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
     * Poll for new messages
     */
    public function pollMessages(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'chat_id' => 'required|exists:chats,id',
                'last_timestamp' => 'required|numeric'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();
            
            if (!$user || $user->role !== 'customer') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Customer access required.'
                ], 401);
            }

            $chat = Chat::findOrFail($request->chat_id);

            // Verify chat belongs to the customer
            if ($chat->customer_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to access this chat.'
                ], 403);
            }

            $lastTimestamp = Carbon::createFromTimestamp($request->last_timestamp);

            // Wait up to 20 seconds for new messages
            $startTime = time();
            $timeout = 20; // 20 seconds timeout
            $checkInterval = 2; // Check every 2 seconds

            while (time() - $startTime < $timeout) {
                // Check for new messages
                $newMessages = $chat->messages()
                    ->where('created_at', '>', $lastTimestamp)
                    ->with('sender')
                    ->get();

                if ($newMessages->count() > 0) {
                    // New messages found
                    $chat->markAsRead('customer');

                    return response()->json([
                        'success' => true,
                        'data' => [
                            'messages' => $newMessages->map(function ($message) {
                                return $this->formatMessage($message);
                            }),
                            'last_message_timestamp' => now()->timestamp
                        ]
                    ]);
                }

                // Check status updates
                if ($chat->updated_at > $lastTimestamp) {
                    return response()->json([
                        'success' => true,
                        'data' => [
                            'messages' => [],
                            'chat_status' => $chat->status,
                            'last_message_timestamp' => now()->timestamp
                        ]
                    ]);
                }

                // No new messages yet, wait before checking again
                sleep($checkInterval);
            }

            // No new messages after timeout
            return response()->json([
                'success' => true,
                'data' => [
                    'messages' => [],
                    'last_message_timestamp' => $request->last_timestamp
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to poll messages: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send a chat message
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
                    'ip_address' => $request->ip(),
                    'sent_from' => 'mobile_app'
                ]
            ]);

            // Update chat status if it was closed/resolved
            if (in_array($chat->status, ['resolved', 'closed'])) {
                $chat->update(['status' => 'open']);
            }

            $message->load('sender');

            return response()->json([
                'success' => true,
                'data' => [
                    'message' => $this->formatMessage($message),
                    'last_message_timestamp' => now()->timestamp
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
                    'messages' => collect($messages->items())->map(function ($message) {
                        return $this->formatMessage($message);
                    }),
                    'last_message_timestamp' => now()->timestamp,
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
     * Format message for API response
     */
    private function formatMessage($message)
    {
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
            'attachment_url' => $message->attachment_path ? url('storage/' . $message->attachment_path) : null,
            'attachment_name' => $message->attachment_name,
            'created_at' => $message->created_at->toISOString(),
            'timestamp' => $message->created_at->timestamp,
            'formatted_time' => $message->created_at->format('H:i'),
            'is_read' => $message->is_read
        ];
    }
}
