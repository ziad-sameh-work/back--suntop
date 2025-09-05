<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\User;
use App\Events\NewChatMessage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MobileChatController extends Controller
{
    /**
     * Get Pusher configuration for mobile app
     */
    public function getPusherConfig(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'pusher_key' => config('broadcasting.connections.pusher.key'),
                'pusher_cluster' => config('broadcasting.connections.pusher.options.cluster'),
                'pusher_force_tls' => true,
                'auth_endpoint' => url('/api/broadcasting/auth'),
                'channels' => [
                    'admin_channel' => 'private-admin.chats',
                    'chat_channel_prefix' => 'chat.',
                    'public_admin_channel' => 'admin-chats-public'
                ]
            ],
            'message' => 'إعدادات Pusher للموبايل'
        ]);
    }

    /**
     * بدء أو الحصول على محادثة للعميل
     */
    public function startChat(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if (!$user || $user->role !== 'customer') {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مصرح. مطلوب دخول العميل.'
                ], 401);
            }

            // البحث عن محادثة موجودة أو إنشاء جديدة
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
                        'created_from' => 'mobile_app',
                        'app_version' => $request->header('App-Version'),
                        'device_type' => $request->header('Device-Type')
                    ]
                ]);

                $chat->load(['messages.sender', 'assignedAdmin']);
                
                // إرسال رسالة ترحيب تلقائية
                $welcomeMessage = ChatMessage::create([
                    'chat_id' => $chat->id,
                    'sender_id' => 1, // Admin user ID (يمكن تغييره)
                    'sender_type' => 'admin',
                    'message' => 'مرحباً بك في خدمة الدعم الفني لـ SunTop! كيف يمكننا مساعدتك اليوم؟',
                    'message_type' => 'text',
                    'metadata' => [
                        'auto_message' => true,
                        'welcome_message' => true
                    ]
                ]);
                
                $welcomeMessage->load('sender');
                
                // إضافة الرسالة للمجموعة
                $chat->messages->prepend($welcomeMessage);
            } else {
                // تعليم الرسائل كمقروءة للعميل
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
                    }),
                    'pusher_config' => [
                        'channel_name' => 'chat.' . $chat->id,
                        'event_name' => 'message.new',
                        'auth_required' => false // قناة عامة للسهولة
                    ]
                ],
                'message' => 'تم الحصول على المحادثة بنجاح'
            ]);

        } catch (\Exception $e) {
            \Log::error('Mobile Chat Start Error: ' . $e->getMessage(), [
                'user_id' => $user->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'خطأ في بدء المحادثة: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * إرسال رسالة جديدة مع Real-time Broadcasting
     */
    public function sendMessage(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if (!$user || $user->role !== 'customer') {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مصرح. مطلوب دخول العميل.'
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
                    'message' => 'بيانات غير صحيحة',
                    'errors' => $validator->errors()
                ], 422);
            }

            $chat = Chat::findOrFail($request->chat_id);

            // التحقق من ملكية المحادثة
            if ($chat->customer_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مصرح لك بإرسال رسائل في هذه المحادثة.'
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

            // إنشاء الرسالة
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
                    'sent_from' => 'mobile_app',
                    'app_version' => $request->header('App-Version'),
                    'device_type' => $request->header('Device-Type')
                ]
            ]);

            // تحديث حالة المحادثة إذا كانت مغلقة
            if (in_array($chat->status, ['resolved', 'closed'])) {
                $chat->update(['status' => 'open']);
            }

            // تحديث وقت آخر رسالة
            $chat->update(['last_message_at' => now()]);

            $message->load('sender');

            // 🔥 إرسال الحدث الفوري - هذا الجزء المهم!
            try {
                event(new NewChatMessage($message));
                
                \Log::info('Real-time event fired successfully', [
                    'chat_id' => $chat->id,
                    'message_id' => $message->id,
                    'channels' => [
                        'chat.' . $chat->id,
                        'private-admin.chats',
                        'admin-chats-public'
                    ]
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to fire real-time event: ' . $e->getMessage());
            }

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
                    ],
                    'chat_updated' => [
                        'id' => $chat->id,
                        'status' => $chat->status,
                        'last_message_at' => $chat->last_message_at->toISOString()
                    ]
                ],
                'message' => 'تم إرسال الرسالة بنجاح'
            ]);

        } catch (\Exception $e) {
            \Log::error('Mobile Send Message Error: ' . $e->getMessage(), [
                'user_id' => $user->id ?? null,
                'chat_id' => $request->chat_id ?? null,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'خطأ في إرسال الرسالة: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * الحصول على رسائل المحادثة مع التصفح
     */
    public function getMessages(Request $request, $chatId): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if (!$user || $user->role !== 'customer') {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مصرح. مطلوب دخول العميل.'
                ], 401);
            }

            $chat = Chat::findOrFail($chatId);

            // التحقق من ملكية المحادثة
            if ($chat->customer_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مصرح لك بالوصول إلى هذه المحادثة.'
                ], 403);
            }

            $perPage = $request->get('per_page', 50);
            $page = $request->get('page', 1);

            $messages = $chat->messages()
                ->with('sender')
                ->latest()
                ->paginate($perPage, ['*'], 'page', $page);

            // تعليم الرسائل كمقروءة للعميل
            $chat->markAsRead('customer');

            return response()->json([
                'success' => true,
                'data' => [
                    'messages' => $messages->map(function ($message) {
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
                'message' => 'خطأ في الحصول على الرسائل: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * تاريخ المحادثات للعميل
     */
    public function getChatHistory(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if (!$user || $user->role !== 'customer') {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مصرح. مطلوب دخول العميل.'
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
                'message' => 'خطأ في الحصول على تاريخ المحادثات: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * تعليم رسائل المحادثة كمقروءة
     */
    public function markAsRead(Request $request, $chatId): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if (!$user || $user->role !== 'customer') {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مصرح. مطلوب دخول العميل.'
                ], 401);
            }

            $chat = Chat::findOrFail($chatId);

            // التحقق من ملكية المحادثة
            if ($chat->customer_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مصرح لك بالوصول إلى هذه المحادثة.'
                ], 403);
            }

            $chat->markAsRead('customer');

            return response()->json([
                'success' => true,
                'message' => 'تم تعليم الرسائل كمقروءة بنجاح.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في تعليم الرسائل كمقروءة: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * اختبار البث الفوري - للتأكد من عمل Real-time
     */
    public function testBroadcast(Request $request, $chatId): JsonResponse
    {
        try {
            $user = Auth::user();
            $chat = Chat::findOrFail($chatId);

            // إنشاء رسالة اختبار
            $testMessage = ChatMessage::create([
                'chat_id' => $chat->id,
                'sender_id' => $user->id,
                'sender_type' => 'customer',
                'message' => 'رسالة اختبار Real-time - ' . now()->format('H:i:s'),
                'message_type' => 'text',
                'metadata' => [
                    'test_message' => true,
                    'sent_from' => 'test_endpoint'
                ]
            ]);

            $testMessage->load('sender');

            // إرسال الحدث الفوري
            event(new NewChatMessage($testMessage));

            return response()->json([
                'success' => true,
                'data' => [
                    'message' => [
                        'id' => $testMessage->id,
                        'message' => $testMessage->message,
                        'created_at' => $testMessage->created_at->toISOString()
                    ],
                    'broadcast_info' => [
                        'channels' => [
                            'chat.' . $chat->id,
                            'private-admin.chats',
                            'admin-chats-public'
                        ],
                        'event_name' => 'message.new'
                    ]
                ],
                'message' => 'تم إرسال رسالة الاختبار بنجاح'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في اختبار البث: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * اختبار إرسال رسالة بدون authentication (للاختبار فقط)
     */
    public function testSendMessage(Request $request, $chatId): JsonResponse
    {
        try {
            $chat = Chat::findOrFail($chatId);

            // إنشاء رسالة اختبار بدون مستخدم محدد
            $testMessage = ChatMessage::create([
                'chat_id' => $chat->id,
                'sender_id' => 1, // مستخدم افتراضي
                'sender_type' => 'admin',
                'message' => $request->message ?? 'رسالة اختبار من النظام - ' . now()->format('H:i:s'),
                'message_type' => 'text',
                'metadata' => [
                    'test_message' => true,
                    'sent_from' => 'test_endpoint_no_auth'
                ]
            ]);

            $testMessage->load('sender');

            // إرسال الحدث الفوري
            event(new NewChatMessage($testMessage));

            \Log::info('Test message sent successfully', [
                'chat_id' => $chat->id,
                'message_id' => $testMessage->id
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'message' => [
                        'id' => $testMessage->id,
                        'message' => $testMessage->message,
                        'sender_type' => $testMessage->sender_type,
                        'created_at' => $testMessage->created_at->toISOString()
                    ],
                    'broadcast_info' => [
                        'channels' => [
                            'chat.' . $chat->id,
                            'private-admin.chats', 
                            'admin-chats-public'
                        ],
                        'event_name' => 'message.new'
                    ]
                ],
                'message' => 'تم إرسال رسالة الاختبار بدون authentication'
            ]);

        } catch (\Exception $e) {
            \Log::error('Test send message error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'خطأ في إرسال رسالة الاختبار: ' . $e->getMessage()
            ], 500);
        }
    }
}
