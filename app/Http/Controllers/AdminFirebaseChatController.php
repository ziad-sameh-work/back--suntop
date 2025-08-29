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

class AdminFirebaseChatController extends Controller
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
     * عرض جميع المحادثات للإدارة مع Firebase
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

        return view('admin.chats.firebase-index', compact('chats', 'status', 'priority', 'search'));
    }

    /**
     * عرض تفاصيل المحادثة مع Firebase Real-time
     */
    public function show(Chat $chat)
    {
        $chat->load(['customer', 'assignedAdmin', 'messages.sender']);
        
        // تمييز الرسائل كمقروءة للإدارة
        $chat->markAsRead('admin');
        $this->firebaseService->markMessagesAsRead($chat->id, 'admin');
        
        // تحديث Firebase بحضور الأدمن
        $this->firebaseService->updateChatStatus($chat->id, $chat->status, Auth::id(), Auth::user()->full_name);
        
        $admins = User::where('role', 'admin')->select('id', 'name', 'full_name')->get();

        return view('admin.chats.firebase-show', compact('chat', 'admins'));
    }

    /**
     * إرسال رسالة من الإدارة عبر Firebase API
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

            // إنشاء الرسالة في قاعدة البيانات و Firebase
            $message = DB::transaction(function () use ($admin, $chat, $request) {
                $message = ChatMessage::create([
                    'chat_id' => $chat->id,
                    'sender_id' => $admin->id,
                    'sender_type' => 'admin',
                    'message' => $request->message,
                    'message_type' => 'text',
                    'metadata' => [
                        'sent_from' => 'admin_firebase_api',
                        'admin_name' => $admin->full_name ?? $admin->name
                    ]
                ]);

                // إرسال إلى Firebase
                $firebaseSuccess = $this->firebaseService->sendMessage($chat->id, [
                    'id' => $message->id,
                    'sender_id' => $admin->id,
                    'sender_name' => $admin->full_name ?? $admin->name,
                    'sender_type' => 'admin',
                    'message' => $request->message,
                    'message_type' => 'text',
                    'attachment_url' => null,
                    'attachment_name' => null,
                    'metadata' => [
                        'sent_from' => 'admin_firebase_api',
                        'admin_id' => $admin->id,
                        'admin_name' => $admin->full_name ?? $admin->name
                    ]
                ]);

                // إشعار العميل
                if ($firebaseSuccess) {
                    $this->firebaseService->notifyCustomer($chat->customer_id, [
                        'type' => 'admin_reply',
                        'chat_id' => $chat->id,
                        'admin_name' => $admin->full_name ?? $admin->name,
                        'message' => "رد جديد من فريق الدعم: " . substr($request->message, 0, 50) . (strlen($request->message) > 50 ? '...' : '')
                    ]);
                }

                // تحديث حالة الشات
                if (in_array($chat->status, ['resolved', 'closed'])) {
                    $chat->update(['status' => 'in_progress']);
                    $this->firebaseService->updateChatStatus($chat->id, 'in_progress', $admin->id, $admin->full_name);
                }

                return $message;
            });

            $message->load('sender');

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
                        'created_at' => $message->created_at->toISOString(),
                        'formatted_time' => $message->created_at->format('H:i'),
                        'firebase_sent' => true
                    ]
                ],
                'message' => 'تم إرسال الرسالة بنجاح'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في إرسال الرسالة: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * تحديث حالة المحادثة مع Firebase
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

            $admin = Auth::user();
            
            // تحديث في قاعدة البيانات
            $chat->update([
                'status' => $request->status,
                'assigned_admin_id' => $admin->id
            ]);

            // تحديث في Firebase
            $this->firebaseService->updateChatStatus(
                $chat->id, 
                $request->status, 
                $admin->id, 
                $admin->full_name ?? $admin->name
            );

            // إشعار العميل بتغيير الحالة
            $statusMessages = [
                'open' => 'تم فتح المحادثة',
                'in_progress' => 'المحادثة قيد المعالجة',
                'resolved' => 'تم حل المشكلة',
                'closed' => 'تم إغلاق المحادثة'
            ];

            $this->firebaseService->notifyCustomer($chat->customer_id, [
                'type' => 'status_change',
                'chat_id' => $chat->id,
                'admin_name' => $admin->full_name ?? $admin->name,
                'message' => $statusMessages[$request->status]
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'status' => $request->status,
                    'updated_at' => $chat->updated_at->toISOString()
                ],
                'message' => 'تم تحديث حالة المحادثة'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في تحديث الحالة: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * تعيين مدير للمحادثة مع Firebase
     */
    public function assignAdmin(Request $request, Chat $chat): JsonResponse
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
                    'message' => 'المستخدم المحدد ليس مديراً'
                ], 400);
            }

            // تحديث في قاعدة البيانات
            $chat->update([
                'assigned_admin_id' => $assignedAdmin->id
            ]);

            // تحديث في Firebase
            $this->firebaseService->updateChatStatus(
                $chat->id, 
                $chat->status, 
                $assignedAdmin->id, 
                $assignedAdmin->full_name ?? $assignedAdmin->name
            );

            // إشعار العميل بتعيين مدير جديد
            $this->firebaseService->notifyCustomer($chat->customer_id, [
                'type' => 'chat_assigned',
                'chat_id' => $chat->id,
                'admin_name' => $assignedAdmin->full_name ?? $assignedAdmin->name,
                'message' => "تم تعيين {$assignedAdmin->full_name} للمحادثة"
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'assigned_admin' => [
                        'id' => $assignedAdmin->id,
                        'name' => $assignedAdmin->name,
                        'full_name' => $assignedAdmin->full_name
                    ]
                ],
                'message' => 'تم تعيين المدير بنجاح'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في تعيين المدير: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * إرسال مؤشر الكتابة
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
            $chatId = $request->chat_id;

            // إرسال مؤشر الكتابة إلى Firebase
            $success = $this->firebaseService->sendAdminTypingIndicator(
                $chatId,
                $admin->id,
                $admin->full_name ?? $admin->name,
                $request->is_typing
            );

            return response()->json([
                'success' => $success,
                'data' => [
                    'typing_status_sent' => $success,
                    'is_typing' => $request->is_typing
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في إرسال مؤشر الكتابة: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * الحصول على إحصائيات المحادثات
     */
    public function getStats(): JsonResponse
    {
        try {
            $stats = [
                'total_chats' => Chat::count(),
                'open_chats' => Chat::where('status', 'open')->count(),
                'in_progress_chats' => Chat::where('status', 'in_progress')->count(),
                'resolved_chats' => Chat::where('status', 'resolved')->count(),
                'closed_chats' => Chat::where('status', 'closed')->count(),
                'my_assigned_chats' => Chat::where('assigned_admin_id', Auth::id())->count(),
                'unassigned_chats' => Chat::whereNull('assigned_admin_id')->where('status', '!=', 'closed')->count(),
                'todays_chats' => Chat::whereDate('created_at', today())->count(),
                'average_response_time' => '2.5 دقيقة', // يمكن حسابه من البيانات الفعلية
                'customer_satisfaction' => '4.8/5.0' // يمكن حسابه من التقييمات
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في جلب الإحصائيات: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * اختبار اتصال Firebase للأدمن
     */
    public function testFirebaseConnection(): JsonResponse
    {
        try {
            $result = $this->firebaseService->testConnection();
            
            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'],
                'data' => array_merge($result['data'] ?? [], [
                    'admin_id' => Auth::id(),
                    'admin_name' => Auth::user()->full_name ?? Auth::user()->name,
                    'test_timestamp' => now()->toISOString()
                ]),
                'firebase_url' => env('FIREBASE_DATABASE_URL'),
                'admin_info' => [
                    'id' => Auth::id(),
                    'name' => Auth::user()->full_name ?? Auth::user()->name,
                    'role' => Auth::user()->role
                ]
            ], $result['success'] ? 200 : 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في اختبار Firebase: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Real-time Dashboard للشات
     */
    public function realtimeDashboard()
    {
        $stats = [
            'total_chats' => Chat::count(),
            'active_chats' => Chat::whereIn('status', ['open', 'in_progress'])->count(),
            'pending_chats' => Chat::where('status', 'open')->whereNull('assigned_admin_id')->count(),
            'my_chats' => Chat::where('assigned_admin_id', Auth::id())->whereIn('status', ['open', 'in_progress'])->count(),
        ];

        $recentChats = Chat::with(['customer', 'assignedAdmin', 'latestMessage'])
            ->whereIn('status', ['open', 'in_progress'])
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.chats.firebase-dashboard', compact('stats', 'recentChats'));
    }

    /**
     * الحصول على قائمة المديرين
     */
    public function getAdmins(): JsonResponse
    {
        try {
            $admins = User::where('role', 'admin')
                ->select('id', 'name', 'full_name', 'email')
                ->get()
                ->map(function ($admin) {
                    return [
                        'id' => $admin->id,
                        'name' => $admin->name,
                        'full_name' => $admin->full_name ?? $admin->name,
                        'email' => $admin->email,
                        'is_online' => $this->firebaseService->isAdminOnline($admin->id) // يمكن تنفيذها لاحقاً
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => ['admins' => $admins]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في جلب قائمة المديرين: ' . $e->getMessage()
            ], 500);
        }
    }
}
