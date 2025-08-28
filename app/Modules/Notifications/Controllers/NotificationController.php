<?php

namespace App\Modules\Notifications\Controllers;

use App\Modules\Core\BaseController;
use App\Modules\Notifications\Services\NotificationService;
use App\Modules\Notifications\Resources\NotificationResource;
use App\Modules\Notifications\Requests\CreateNotificationRequest;
use App\Modules\Notifications\Requests\BulkNotificationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends BaseController
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
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

            return $this->successResponse([
                'notifications' => NotificationResource::collection($notifications),
                'pagination' => [
                    'current_page' => $notifications->currentPage(),
                    'per_page' => $notifications->perPage(),
                    'total' => $notifications->total(),
                    'last_page' => $notifications->lastPage(),
                    'has_next' => $notifications->hasMorePages(),
                    'has_prev' => $notifications->currentPage() > 1,
                ],
                'unread_count' => $unreadCount,
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get specific notification
     */
    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $notification = $this->notificationService->model
                                ->where('id', $id)
                                ->where('user_id', $userId)
                                ->first();

            if (!$notification) {
                return $this->errorResponse('الإشعار غير موجود', null, 404);
            }

            // Auto-mark as read when viewed
            if (!$notification->is_read) {
                $notification->markAsRead();
            }

            return $this->successResponse(new NotificationResource($notification));
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
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

            return $this->successResponse(null, 'تم وضع علامة قراءة على الإشعار');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $markedCount = $this->notificationService->markAllAsRead($userId);

            return $this->successResponse([
                'marked_count' => $markedCount
            ], 'تم وضع علامة قراءة على جميع الإشعارات');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
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
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Delete all notifications for user
     */
    public function destroyAll(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $deletedCount = $this->notificationService->deleteAllForUser($userId);

            return $this->successResponse([
                'deleted_count' => $deletedCount
            ], 'تم حذف جميع الإشعارات بنجاح');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get unread notifications count
     */
    public function unreadCount(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $unreadCount = $this->notificationService->getUnreadCount($userId);

            return $this->successResponse([
                'unread_count' => $unreadCount
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get user notification statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $stats = $this->notificationService->getUserNotificationStats($userId);

            return $this->successResponse($stats);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get notification types
     */
    public function types(): JsonResponse
    {
        try {
            $types = [];
            foreach (\App\Models\Notification::TYPES as $key => $value) {
                $types[] = [
                    'key' => $key,
                    'name' => $value,
                ];
            }

            return $this->successResponse([
                'types' => $types
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}
