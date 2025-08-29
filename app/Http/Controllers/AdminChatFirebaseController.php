<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\User;
use App\Services\FirebaseRealtimeService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AdminChatFirebaseController extends Controller
{
    private $firebaseService;

    public function __construct(FirebaseRealtimeService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
        $this->middleware(['auth', 'role:admin']);
        
        // تسجيل الإداري كمتصل عند تهيئة Controller
        $this->middleware(function ($request, $next) {
            if (Auth::check() && Auth::user()->role === 'admin') {
                $this->firebaseService->registerAdminPresence(
                    Auth::id(),
                    Auth::user()->full_name ?? Auth::user()->name
                );
            }
            return $next($request);
        });
    }

    /**
     * عرض جميع المحادثات للإدارة
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $priority = $request->get('priority', 'all');
        $search = $request->get('search');

        $query = Chat::with(['customer', 'assignedAdmin', 'latestMessage']);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($priority !== 'all') {
            $query->where('priority', $priority);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($customerQuery) use ($search) {
                      $customerQuery->where('name', 'like', "%{$search}%")
                                  ->orWhere('full_name', 'like', "%{$search}%");
                  });
            });
        }

        $chats = $query->orderBy('updated_at', 'desc')->paginate(20);

        return view('admin.chats.index', compact('chats', 'status', 'priority', 'search'));
    }

    /**
     * عرض تفاصيل المحادثة
     */
    public function show(Chat $chat)
    {
        $chat->load(['customer', 'assignedAdmin', 'messages.sender']);
        
        // تمييز الرسائل كمقروءة للإدارة
        $chat->markAsRead('admin');
        $this->firebaseService->markMessagesAsRead($chat->id, 'admin');
        
        $admins = User::where('role', 'admin')->select('id', 'name', 'full_name')->get();

        return view('admin.chats.show', compact('chat', 'admins'));
    }

    /**
     * إرسال رسالة من الإدارة عبر API
     */
    public function sendMessage(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'chat_id' => 'required|exists:chats,id',
                'message' => 'required|string|max:2000'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $admin = Auth::user();
            $chat = Chat::findOrFail($request->chat_id);

            // إنشاء الرسالة
            $message = DB::transaction(function () use ($chat, $admin, $request) {
                $message = ChatMessage::create([
                    'chat_id' => $chat->id,
                    'sender_id' => $admin->id,
                    'sender_type' => 'admin',
                    'message' => $request->message,
                    'message_type' => 'text',
                    'metadata' => [
                        'sent_from' => 'admin_panel',
                        'admin_name' => $admin->full_name ?? $admin->name
                    ]
                ]);

                // تحديث حالة الشات إذا كان مغلق
                if ($chat->status === 'open') {
                    $chat->update(['status' => 'in_progress']);
                }

                return $message;
            });

            $message->load('sender');

            // إرسال الرسالة إلى Firebase
            $firebaseSuccess = $this->firebaseService->sendMessage($chat->id, [
                'id' => $message->id,
                'sender_id' => $admin->id,
                'sender_name' => $admin->full_name ?? $admin->name,
                'sender_type' => 'admin',
                'message' => $message->message,
                'message_type' => 'text',
                'attachment_url' => null,
                'attachment_name' => null,
                'metadata' => $message->metadata
            ]);

            // إشعار العميل برد الإدارة
            if ($firebaseSuccess) {
                $this->firebaseService->notifyCustomer($chat->customer_id, [
                    'type' => 'admin_reply',
                    'chat_id' => $chat->id,
                    'admin_name' => $admin->full_name ?? $admin->name,
                    'message' => "رد جديد من {$admin->full_name}: " . substr($message->message, 0, 50) . (strlen($message->message) > 50 ? '...' : '')
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'message' => [
                        'id' => $message->id,
                        'message' => $message->message,
                        'sender_name' => $admin->full_name ?? $admin->name,
                        'created_at' => $message->created_at->format('H:i'),
                        'formatted_time' => $message->created_at->diffForHumans()
                    ],
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
     * تعيين الشات لإداري
     */
    public function assign(Request $request, Chat $chat): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'admin_id' => 'required|exists:users,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $assignedAdmin = User::findOrFail($request->admin_id);
            
            if ($assignedAdmin->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'المستخدم المحدد ليس إداري'
                ], 422);
            }

            $chat->update([
                'assigned_admin_id' => $assignedAdmin->id,
                'status' => 'in_progress'
            ]);

            // تحديث Firebase
            $this->firebaseService->updateChatStatus(
                $chat->id, 
                'in_progress', 
                $assignedAdmin->id, 
                $assignedAdmin->full_name ?? $assignedAdmin->name
            );

            // إشعار العميل بتعيين الشات
            $this->firebaseService->notifyCustomer($chat->customer_id, [
                'type' => 'chat_assigned',
                'chat_id' => $chat->id,
                'admin_name' => $assignedAdmin->full_name ?? $assignedAdmin->name,
                'message' => "تم تعيين {$assignedAdmin->full_name} للرد على استفساراتك"
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم تعيين الشات بنجاح',
                'assigned_admin' => [
                    'id' => $assignedAdmin->id,
                    'name' => $assignedAdmin->full_name ?? $assignedAdmin->name
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في تعيين الشات: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * تحديث حالة الشات
     */
    public function updateStatus(Request $request, Chat $chat): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:open,in_progress,resolved,closed'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $oldStatus = $chat->status;
            $newStatus = $request->status;

            $chat->update(['status' => $newStatus]);

            // تحديث Firebase
            $this->firebaseService->updateChatStatus($chat->id, $newStatus);

            // إشعار العميل بتغيير الحالة
            $statusMessages = [
                'open' => 'تم فتح الشات',
                'in_progress' => 'جاري معالجة استفسارك',
                'resolved' => 'تم حل استفسارك',
                'closed' => 'تم إغلاق الشات'
            ];

            $this->firebaseService->notifyCustomer($chat->customer_id, [
                'type' => 'status_change',
                'chat_id' => $chat->id,
                'admin_name' => 'فريق الدعم',
                'message' => $statusMessages[$newStatus]
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث حالة الشات بنجاح',
                'old_status' => $oldStatus,
                'new_status' => $newStatus
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في تحديث حالة الشات: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * تحديث أولوية الشات
     */
    public function updatePriority(Request $request, Chat $chat): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'priority' => 'required|in:low,medium,high,urgent'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $oldPriority = $chat->priority;
            $newPriority = $request->priority;

            $chat->update(['priority' => $newPriority]);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث أولوية الشات بنجاح',
                'old_priority' => $oldPriority,
                'new_priority' => $newPriority
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في تحديث أولوية الشات: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * الحصول على الإداريين المتاحين
     */
    public function getAdmins(): JsonResponse
    {
        $admins = User::where('role', 'admin')
                     ->select('id', 'name', 'full_name')
                     ->get();

        return response()->json([
            'success' => true,
            'data' => $admins
        ]);
    }

    /**
     * إحصائيات المحادثات للإدارة
     */
    public function getStats(): JsonResponse
    {
        $stats = [
            'total_chats' => Chat::count(),
            'open_chats' => Chat::where('status', 'open')->count(),
            'in_progress_chats' => Chat::where('status', 'in_progress')->count(),
            'resolved_chats' => Chat::where('status', 'resolved')->count(),
            'closed_chats' => Chat::where('status', 'closed')->count(),
            'unassigned_chats' => Chat::whereNull('assigned_admin_id')
                                    ->whereIn('status', ['open', 'in_progress'])
                                    ->count(),
            'high_priority_chats' => Chat::whereIn('priority', ['high', 'urgent'])
                                        ->whereIn('status', ['open', 'in_progress'])
                                        ->count(),
            'total_unread_messages' => ChatMessage::whereHas('chat', function ($query) {
                                                      $query->whereIn('status', ['open', 'in_progress']);
                                                  })
                                                  ->where('sender_type', 'customer')
                                                  ->where('is_read', false)
                                                  ->count()
        ];

        // تحديث الإحصائيات في Firebase
        $this->firebaseService->updateChatStats($stats);

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * عرض لوحة الإدارة Real-Time
     */
    public function realtimeDashboard()
    {
        return view('admin.chats.realtime-dashboard');
    }

    /**
     * إرسال typing indicator
     */
    public function sendTypingIndicator(Request $request): JsonResponse
    {
        try {
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

            $admin = Auth::user();
            
            $this->firebaseService->sendTypingIndicator(
                $request->chat_id,
                'admin',
                $admin->full_name ?? $admin->name,
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
     * تحديث نشاط الإداري
     */
    public function updateActivity(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'activity' => 'required|string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $admin = Auth::user();
            
            $this->firebaseService->updateAdminActivity(
                $admin->id,
                $request->activity
            );

            return response()->json([
                'success' => true,
                'message' => 'Activity updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update activity: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * الحصول على جميع المحادثات للواجهة Real-Time
     */
    public function getAllChatsForRealtime(): JsonResponse
    {
        try {
            $chats = Chat::with(['customer', 'assignedAdmin', 'latestMessage'])
                        ->orderBy('updated_at', 'desc')
                        ->get();

            $formattedChats = $chats->map(function ($chat) {
                return [
                    'id' => $chat->id,
                    'customer_id' => $chat->customer_id,
                    'customer_name' => $chat->customer->full_name ?? $chat->customer->name,
                    'subject' => $chat->subject,
                    'status' => $chat->status,
                    'priority' => $chat->priority,
                    'assigned_admin_id' => $chat->assigned_admin_id,
                    'assigned_admin_name' => $chat->assignedAdmin ? ($chat->assignedAdmin->full_name ?? $chat->assignedAdmin->name) : null,
                    'customer_unread_count' => $chat->customer_unread_count,
                    'admin_unread_count' => $chat->admin_unread_count,
                    'created_at' => $chat->created_at->toISOString(),
                    'updated_at' => $chat->updated_at->toISOString(),
                    'last_message' => $chat->latestMessage ? [
                        'message' => $chat->latestMessage->message,
                        'sender_type' => $chat->latestMessage->sender_type,
                        'created_at' => $chat->latestMessage->created_at->toISOString()
                    ] : null
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedChats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get chats: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * إزالة الإداري من قائمة المتصلين (عند تسجيل الخروج)
     */
    public function removePresence(): JsonResponse
    {
        try {
            $admin = Auth::user();
            
            $this->firebaseService->removeAdminPresence($admin->id);

            return response()->json([
                'success' => true,
                'message' => 'Admin presence removed successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove presence: ' . $e->getMessage()
            ], 500);
        }
    }
}
