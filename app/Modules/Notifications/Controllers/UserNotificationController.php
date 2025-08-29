<?php

namespace App\Modules\Notifications\Controllers;

use App\Modules\Core\BaseController;
use App\Modules\Notifications\Services\NotificationService;
use App\Modules\Notifications\Resources\NotificationResource;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserNotificationController extends BaseController
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
        $this->middleware('auth:sanctum');
    }

    /**
     * Get user's notifications
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $filters = [
                'type' => $request->get('type'),
                'is_read' => $request->get('is_read'),
                'priority' => $request->get('priority'),
                'alert_type' => $request->get('alert_type'),
                'date_from' => $request->get('date_from'),
                'date_to' => $request->get('date_to'),
            ];

            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $perPage = $request->get('per_page', 20);

            $notifications = $this->notificationService->getUserNotifications(
                $userId,
                $filters,
                $sortBy,
                $sortOrder,
                $perPage
            );

            $unreadCount = $this->notificationService->getUnreadCount($userId);
            $stats = $this->notificationService->getUserNotificationStats($userId);

            return $this->successResponse([
                'notifications' => NotificationResource::collection($notifications),
                'unread_count' => $unreadCount,
                'pagination' => [
                    'current_page' => $notifications->currentPage(),
                    'last_page' => $notifications->lastPage(),
                    'per_page' => $notifications->perPage(),
                    'total' => $notifications->total(),
                    'has_more_pages' => $notifications->hasMorePages(),
                ],
                'statistics' => $stats,
            ], 'تم جلب الإشعارات بنجاح');

        } catch (\Exception $e) {
            return $this->errorResponse('حدث خطأ أثناء جلب الإشعارات', $e->getMessage());
        }
    }

    /**
     * Get unread notifications count
     */
    public function unreadCount(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $count = $this->notificationService->getUnreadCount($userId);

            return $this->successResponse([
                'unread_count' => $count
            ], 'تم جلب عدد الإشعارات غير المقروءة بنجاح');

        } catch (\Exception $e) {
            return $this->errorResponse('حدث خطأ أثناء جلب عدد الإشعارات');
        }
    }

    /**
     * Get notification statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $stats = $this->notificationService->getUserNotificationStats($userId);

            return $this->successResponse($stats, 'تم جلب إحصائيات الإشعارات بنجاح');

        } catch (\Exception $e) {
            return $this->errorResponse('حدث خطأ أثناء جلب الإحصائيات');
        }
    }

    /**
     * Get notification types
     */
    public function types(): JsonResponse
    {
        try {
            return $this->successResponse([
                'notification_types' => Notification::TYPES,
                'alert_types' => Notification::ALERT_TYPES,
                'priority_levels' => Notification::PRIORITIES,
            ], 'تم جلب أنواع الإشعارات بنجاح');

        } catch (\Exception $e) {
            return $this->errorResponse('حدث خطأ أثناء جلب الأنواع');
        }
    }

    /**
     * Show specific notification
     */
    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $notification = Notification::where('id', $id)
                                      ->where('user_id', $userId)
                                      ->first();

            if (!$notification) {
                return $this->errorResponse('الإشعار غير موجود', null, 404);
            }

            // Mark as read if not already
            if (!$notification->is_read) {
                $notification->markAsRead();
            }

            return $this->successResponse([
                'notification' => new NotificationResource($notification)
            ], 'تم جلب الإشعار بنجاح');

        } catch (\Exception $e) {
            return $this->errorResponse('حدث خطأ أثناء جلب الإشعار');
        }
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request, int $id): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $success = $this->notificationService->markAsRead($id, $userId);

            if (!$success) {
                return $this->errorResponse('الإشعار غير موجود', null, 404);
            }

            return $this->successResponse(null, 'تم تحديد الإشعار كمقروء بنجاح');

        } catch (\Exception $e) {
            return $this->errorResponse('حدث خطأ أثناء تحديث الإشعار');
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $count = $this->notificationService->markAllAsRead($userId);

            return $this->successResponse([
                'marked_count' => $count
            ], "تم تحديد {$count} إشعار كمقروء");

        } catch (\Exception $e) {
            return $this->errorResponse('حدث خطأ أثناء تحديث الإشعارات');
        }
    }

    /**
     * Delete notification
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $success = $this->notificationService->deleteNotification($id, $userId);

            if (!$success) {
                return $this->errorResponse('الإشعار غير موجود', null, 404);
            }

            return $this->successResponse(null, 'تم حذف الإشعار بنجاح');

        } catch (\Exception $e) {
            return $this->errorResponse('حدث خطأ أثناء حذف الإشعار');
        }
    }

    /**
     * Delete all notifications for user
     */
    public function destroyAll(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $count = $this->notificationService->deleteAllForUser($userId);

            return $this->successResponse([
                'deleted_count' => $count
            ], "تم حذف {$count} إشعار");

        } catch (\Exception $e) {
            return $this->errorResponse('حدث خطأ أثناء حذف الإشعارات');
        }
    }
}
