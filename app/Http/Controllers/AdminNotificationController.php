<?php

namespace App\Http\Controllers;

use App\Modules\Notifications\Services\NotificationService;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

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
        ];

        $notifications = $this->notificationService->getAllNotifications(
            $filters,
            $request->get('sort_by', 'created_at'),
            $request->get('sort_order', 'desc'),
            $request->get('per_page', 20)
        );

        $stats = $this->notificationService->getAdminNotificationStats();
        $users = User::select('id', 'name', 'email')->where('is_active', true)->get();

        return view('admin.notifications.index', compact('notifications', 'stats', 'users', 'filters'));
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

        return view('admin.notifications.create', compact('users'));
    }

    /**
     * Store new notification
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'type' => 'required|in:shipment,offer,reward,general,order_status,payment',
            'priority' => 'required|in:low,medium,high',
            'action_url' => 'nullable|string|max:500',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        try {
            $count = $this->notificationService->createBulkNotification(
                $request->user_ids,
                $request->title,
                $request->message,
                $request->type,
                $request->data ?? [],
                $request->priority,
                $request->action_url,
                $request->scheduled_at ? \Carbon\Carbon::parse($request->scheduled_at) : null
            );

            return redirect()
                ->route('admin.notifications.index')
                ->with('success', "تم إنشاء {$count} إشعار بنجاح");
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * Show notification details
     */
    public function show(int $id): View
    {
        $notification = Notification::with('user')->findOrFail($id);
        return view('admin.notifications.show', compact('notification'));
    }

    /**
     * Delete notification
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $notification = Notification::findOrFail($id);
            $notification->delete();

            return redirect()
                ->route('admin.notifications.index')
                ->with('success', 'تم حذف الإشعار بنجاح');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * Send notification to all users
     */
    public function sendToAll(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'type' => 'required|in:shipment,offer,reward,general,order_status,payment',
            'priority' => 'required|in:low,medium,high',
            'action_url' => 'nullable|string|max:500',
            'role_filter' => 'nullable|in:customer,merchant,admin',
        ]);

        try {
            $query = User::where('is_active', true);
            
            if ($request->role_filter) {
                $query->where('role', $request->role_filter);
            }

            $userIds = $query->pluck('id')->toArray();

            $count = $this->notificationService->createBulkNotification(
                $userIds,
                $request->title,
                $request->message,
                $request->type,
                [],
                $request->priority,
                $request->action_url
            );

            return redirect()
                ->route('admin.notifications.index')
                ->with('success', "تم إرسال الإشعار إلى {$count} مستخدم");
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * Clean old notifications
     */
    public function cleanOld(Request $request): RedirectResponse
    {
        $request->validate([
            'days_old' => 'required|integer|min:1|max:365',
        ]);

        try {
            $deletedCount = $this->notificationService->cleanOldNotifications($request->days_old);

            return redirect()
                ->route('admin.notifications.index')
                ->with('success', "تم حذف {$deletedCount} إشعار قديم");
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * Get notifications statistics (AJAX)
     */
    public function getStats(): \Illuminate\Http\JsonResponse
    {
        try {
            $stats = $this->notificationService->getAdminNotificationStats();
            return response()->json($stats);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
