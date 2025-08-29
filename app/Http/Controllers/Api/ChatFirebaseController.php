<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\User;
use App\Services\FirebaseRealtimeService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ChatFirebaseController extends Controller
{
    private $firebaseService;

    public function __construct(FirebaseRealtimeService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * اختبار الاتصال مع Firebase
     */
    public function testFirebaseConnection(): JsonResponse
    {
        $result = $this->firebaseService->testConnection();
        
        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'data' => $result['data'] ?? null,
            'firebase_url' => env('FIREBASE_DATABASE_URL'),
            'timestamp' => now()->toISOString()
        ], $result['success'] ? 200 : 500);
    }

    /**
     * إنشاء أو الحصول على شات مع دعم Firebase Real-time
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

            // البحث عن شات مفتوح أو إنشاء واحد جديد
            $chat = Chat::where('customer_id', $user->id)
                ->whereIn('status', ['open', 'in_progress'])
                ->with(['messages.sender', 'assignedAdmin'])
                ->first();

            if (!$chat) {
                $chat = DB::transaction(function () use ($user, $request) {
                    $chat = Chat::create([
                        'customer_id' => $user->id,
                        'subject' => $request->subject ?? 'دعم عام',
                        'status' => 'open',
                        'priority' => $request->priority ?? 'medium',
                        'metadata' => [
                            'user_agent' => $request->header('User-Agent'),
                            'ip_address' => $request->ip(),
                            'created_from' => 'mobile_app_firebase'
                        ]
                    ]);

                    // إنشاء الشات في Firebase
                    $this->firebaseService->createOrUpdateChat($chat->id, [
                        'customer_id' => $user->id,
                        'customer_name' => $user->full_name ?? $user->name,
                        'subject' => $chat->subject,
                        'status' => $chat->status,
                        'priority' => $chat->priority,
                        'created_at' => $chat->created_at->toISOString(),
                        'customer_unread_count' => 0,
                        'admin_unread_count' => 0
                    ]);

                    // إشعار الإدارة بشات جديد
                    $this->firebaseService->notifyAdmins($chat->id, [
                        'type' => 'new_chat',
                        'customer_name' => $user->full_name ?? $user->name,
                        'message' => "شات جديد من {$user->full_name}: {$chat->subject}"
                    ]);

                    return $chat;
                });

                $chat->load(['messages.sender', 'assignedAdmin']);
            } else {
                // تحديث Firebase مع آخر البيانات
                $this->firebaseService->createOrUpdateChat($chat->id, [
                    'customer_id' => $user->id,
                    'customer_name' => $user->full_name ?? $user->name,
                    'subject' => $chat->subject,
                    'status' => $chat->status,
                    'priority' => $chat->priority,
                    'assigned_admin_id' => $chat->assigned_admin_id,
                    'assigned_admin_name' => $chat->assignedAdmin?->full_name,
                    'created_at' => $chat->created_at->toISOString(),
                    'customer_unread_count' => $chat->customer_unread_count,
                    'admin_unread_count' => $chat->admin_unread_count
                ]);

                // تمييز الرسائل كمقروءة
                $chat->markAsRead('customer');
                $this->firebaseService->markMessagesAsRead($chat->id, 'customer');
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
                        // معلومات Firebase للتطبيق
                        'firebase' => [
                            'chat_path' => "chats/{$chat->id}",
                            'messages_path' => "chats/{$chat->id}/messages",
                            'info_path' => "chats/{$chat->id}/info",
                            'notifications_path' => "customer_notifications/{$user->id}"
                        ]
                    ],
                    'messages' => $chat->messages->map(function ($message) {
                        return $this->formatMessage($message);
                    }),
                    'firebase_config' => [
                        'database_url' => env('FIREBASE_DATABASE_URL'),
                        'listen_paths' => [
                            "chats/{$chat->id}/messages",
                            "customer_notifications/{$user->id}"
                        ]
                    ]
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
     * إرسال رسالة مع دعم Firebase Real-time
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

            // التحقق من ملكية الشات
            if ($chat->customer_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to send message in this chat.'
                ], 403);
            }

            // معالجة المرفقات
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

            // إنشاء الرسالة في قاعدة البيانات
            $message = DB::transaction(function () use ($chat, $user, $request, $messageType, $attachmentPath, $attachmentName) {
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
                        'sent_from' => 'mobile_app_firebase'
                    ]
                ]);

                // تحديث حالة الشات إذا كان مغلق
                if (in_array($chat->status, ['resolved', 'closed'])) {
                    $chat->update(['status' => 'open']);
                }

                return $message;
            });

            $message->load('sender');

            // إرسال الرسالة إلى Firebase
            $firebaseSuccess = $this->firebaseService->sendMessage($chat->id, [
                'id' => $message->id,
                'sender_id' => $user->id,
                'sender_name' => $user->full_name ?? $user->name,
                'sender_type' => 'customer',
                'message' => $message->message,
                'message_type' => $messageType,
                'attachment_url' => $attachmentPath ? url('storage/' . $attachmentPath) : null,
                'attachment_name' => $attachmentName,
                'metadata' => $message->metadata
            ]);

            // إشعار الإدارة برسالة جديدة
            if ($firebaseSuccess) {
                $this->firebaseService->notifyAdmins($chat->id, [
                    'type' => 'new_message',
                    'customer_name' => $user->full_name ?? $user->name,
                    'message' => "رسالة جديدة من {$user->full_name}: " . substr($message->message, 0, 50) . (strlen($message->message) > 50 ? '...' : '')
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'message' => $this->formatMessage($message),
                    'firebase_sent' => $firebaseSuccess
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
     * الحصول على الرسائل مع معلومات Firebase
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

            // التحقق من ملكية الشات
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

            // تمييز الرسائل كمقروءة
            $chat->markAsRead('customer');
            $this->firebaseService->markMessagesAsRead($chat->id, 'customer');

            return response()->json([
                'success' => true,
                'data' => [
                    'messages' => collect($messages->items())->map(function ($message) {
                        return $this->formatMessage($message);
                    }),
                    'firebase_config' => [
                        'database_url' => env('FIREBASE_DATABASE_URL'),
                        'messages_path' => "chats/{$chat->id}/messages",
                        'chat_info_path' => "chats/{$chat->id}/info",
                        'notifications_path' => "customer_notifications/{$user->id}"
                    ],
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
     * تحديث حالة القراءة
     */
    public function markAsRead(Request $request, $chatId): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized.'
                ], 401);
            }

            $chat = Chat::findOrFail($chatId);

            // التحقق من الصلاحية
            if ($user->role === 'customer' && $chat->customer_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to access this chat.'
                ], 403);
            }

            $userType = $user->role === 'customer' ? 'customer' : 'admin';
            
            // تحديث قاعدة البيانات
            $chat->markAsRead($userType);
            
            // تحديث Firebase
            $this->firebaseService->markMessagesAsRead($chat->id, $userType);

            return response()->json([
                'success' => true,
                'message' => 'Messages marked as read'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark as read: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * إرسال typing indicator من العميل
     */
    public function sendTypingIndicator(Request $request): JsonResponse
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
                'is_typing' => 'required|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $chat = Chat::findOrFail($request->chat_id);

            // التحقق من ملكية الشات
            if ($chat->customer_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to access this chat.'
                ], 403);
            }

            // إرسال typing indicator إلى Firebase
            $this->firebaseService->sendTypingIndicator(
                $request->chat_id,
                'customer',
                $user->full_name ?? $user->name,
                $request->is_typing
            );

            return response()->json([
                'success' => true,
                'message' => 'Typing indicator updated'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update typing indicator: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * تنسيق الرسالة للاستجابة
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
                'full_name' => $message->sender->full_name ?? $message->sender->name,
                'avatar' => $message->sender->profile_image ? url('storage/' . $message->sender->profile_image) : null
            ],
            'attachment_url' => $message->attachment_path ? url('storage/' . $message->attachment_path) : null,
            'attachment_name' => $message->attachment_name,
            'created_at' => $message->created_at->toISOString(),
            'formatted_time' => $message->created_at->format('H:i'),
            'formatted_date' => $message->created_at->format('d/m/Y'),
            'is_read' => $message->is_read,
            'timestamp' => $message->created_at->timestamp
        ];
    }
}
