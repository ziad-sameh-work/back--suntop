<?php

namespace App\Http\Controllers;

use App\Modules\Notifications\Services\NotificationService;
use App\Models\Notification;
use App\Models\User;
use App\Modules\Users\Models\UserCategory;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class AdminNotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Display notifications list
     */
    public function index(Request $request): View
    {
        $filters = [
            'user_id' => $request->get('user_id'),
            'type' => $request->get('type'),
            'is_read' => $request->get('is_read'),
            'priority' => $request->get('priority'),
            'search' => $request->get('search'),
            'target_type' => $request->get('target_type'),
        ];

        $notifications = $this->notificationService->getAllNotifications(
            $filters,
            $request->get('sort_by', 'created_at'),
            $request->get('sort_order', 'desc'),
            $request->get('per_page', 20)
        );

        $stats = $this->notificationService->getAdminNotificationStats();
        $users = User::select('id', 'name', 'email')->where('is_active', true)->get();
        $categories = UserCategory::active()->ordered()->get();

        return view('admin.notifications.index', compact('notifications', 'stats', 'users', 'categories', 'filters'));
    }

    /**
     * Show create notification form
     */
    public function create(): View
    {
        $users = User::select('id', 'name', 'email', 'role')
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->get();

        $categories = UserCategory::active()->ordered()->get();

        return view('admin.notifications.create', compact('users', 'categories'));
    }

    /**
     * Store new notification
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:500',
            'body' => 'nullable|string|max:2000',
            'type' => 'required|in:' . implode(',', array_keys(Notification::TYPES)),
            'alert_type' => 'required|in:' . implode(',', array_keys(Notification::ALERT_TYPES)),
            'priority' => 'required|in:' . implode(',', array_keys(Notification::PRIORITIES)),
            'action_url' => 'nullable|url',
            'target_type' => 'required|in:user,category,all',
            'user_ids' => 'required_if:target_type,user|array',
            'user_ids.*' => 'exists:users,id',
            'category_id' => 'required_if:target_type,category|exists:user_categories,id',
            'role_filter' => 'nullable|in:customer,merchant,admin',
        ]);

        try {
            $count = 0;
            
            switch ($validated['target_type']) {
                case 'user':
                    $count = $this->notificationService->createBulkNotification(
                        $validated['user_ids'],
                        $validated['title'],
                        $validated['message'],
                        $validated['type'],
                        $validated['body'] ?? null,
                        $validated['alert_type'],
                        [],
                        $validated['priority'],
                        $validated['action_url'] ?? null
                    );
                    break;

                case 'category':
                    $count = $this->notificationService->createNotificationForCategory(
                        $validated['category_id'],
                        $validated['title'],
                        $validated['message'],
                        $validated['type'],
                        $validated['body'] ?? null,
                        $validated['alert_type'],
                        [],
                        $validated['priority'],
                        $validated['action_url'] ?? null
                    );
                    break;

                case 'all':
                    $count = $this->notificationService->createNotificationForAll(
                        $validated['title'],
                        $validated['message'],
                        $validated['type'],
                        $validated['body'] ?? null,
                        $validated['alert_type'],
                        [],
                        $validated['priority'],
                        $validated['action_url'] ?? null,
                        null,
                        $validated['role_filter'] ?? null
                    );
                    break;
            }

            return redirect()->route('admin.notifications.index')
                           ->with('success', "تم إرسال الإشعار بنجاح إلى {$count} مستخدم");

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withErrors(['error' => 'حدث خطأ أثناء إرسال الإشعار: ' . $e->getMessage()])
                           ->withInput();
        }
    }

    /**
     * Show notification details
     */
    public function show(Notification $notification): View
    {
        $notification->load(['user', 'userCategory']);
        return view('admin.notifications.show', compact('notification'));
    }

    /**
     * Delete notification
     */
    public function destroy(Notification $notification): RedirectResponse
    {
        try {
            $notification->delete();
            return redirect()->route('admin.notifications.index')
                           ->with('success', 'تم حذف الإشعار بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withErrors(['error' => 'حدث خطأ أثناء حذف الإشعار']);
        }
    }

    /**
     * Send notification to all users
     */
    public function sendToAll(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:500',
            'body' => 'nullable|string|max:2000',
            'type' => 'required|in:' . implode(',', array_keys(Notification::TYPES)),
            'alert_type' => 'required|in:' . implode(',', array_keys(Notification::ALERT_TYPES)),
            'priority' => 'required|in:' . implode(',', array_keys(Notification::PRIORITIES)),
            'action_url' => 'nullable|url',
            'role_filter' => 'nullable|in:customer,merchant,admin',
        ]);

        try {
            $count = $this->notificationService->createNotificationForAll(
                $validated['title'],
                $validated['message'],
                $validated['type'],
                $validated['body'] ?? null,
                $validated['alert_type'],
                [],
                $validated['priority'],
                $validated['action_url'] ?? null,
                null,
                $validated['role_filter'] ?? null
            );

            return redirect()->route('admin.notifications.index')
                           ->with('success', "تم إرسال الإشعار إلى {$count} مستخدم");

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withErrors(['error' => 'حدث خطأ أثناء إرسال الإشعار: ' . $e->getMessage()]);
        }
    }

    /**
     * Clean old notifications
     */
    public function cleanOld(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'days_old' => 'required|integer|min:1|max:365',
        ]);

        try {
            $count = $this->notificationService->cleanOldNotifications($validated['days_old']);
            return redirect()->route('admin.notifications.index')
                           ->with('success', "تم حذف {$count} إشعار قديم");

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withErrors(['error' => 'حدث خطأ أثناء تنظيف الإشعارات']);
        }
    }

    /**
     * Get notification statistics (AJAX)
     */
    public function getStats(): JsonResponse
    {
        try {
            $stats = $this->notificationService->getAdminNotificationStats();
            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب الإحصائيات'
            ], 500);
        }
    }

    /**
     * Get users for specific category (AJAX)
     */
    public function getCategoryUsers(UserCategory $category): JsonResponse
    {
        try {
            $users = User::where('user_category_id', $category->id)
                        ->where('is_active', true)
                        ->select('id', 'name', 'email')
                        ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'category' => $category->display_name,
                    'users' => $users,
                    'count' => $users->count()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب المستخدمين'
            ], 500);
        }
    }
}