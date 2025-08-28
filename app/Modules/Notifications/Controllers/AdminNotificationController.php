<?php

namespace App\Modules\Notifications\Controllers;

use App\Modules\Core\BaseController;
use App\Modules\Notifications\Services\NotificationService;
use App\Modules\Notifications\Resources\AdminNotificationResource;
use App\Modules\Notifications\Requests\CreateNotificationRequest;
use App\Modules\Notifications\Requests\BulkNotificationRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminNotificationController extends BaseController
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Get all notifications (Admin)
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = [
                'user_id' => $request->get('user_id'),
                'type' => $request->get('type'),
                'is_read' => $request->get('is_read'),
                'priority' => $request->get('priority'),
                'search' => $request->get('search'),
            ];

            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $perPage = $request->get('per_page', 20);

            $notifications = $this->notificationService->getAllNotifications(
                $filters,
                $sortBy,
                $sortOrder,
                $perPage
            );

            return $this->successResponse([
                'notifications' => AdminNotificationResource::collection($notifications),
                'pagination' => [
                    'current_page' => $notifications->currentPage(),
                    'per_page' => $notifications->perPage(),
                    'total' => $notifications->total(),
                    'last_page' => $notifications->lastPage(),
                    'has_next' => $notifications->hasMorePages(),
                    'has_prev' => $notifications->currentPage() > 1,
                ],
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Create single notification
     */
    public function store(CreateNotificationRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            
            $notification = $this->notificationService->createNotification(
                $data['user_id'],
                $data['title'],
                $data['message'],
                $data['type'],
                $data['data'] ?? [],
                $data['priority'] ?? 'medium',
                $data['action_url'] ?? null,
                isset($data['scheduled_at']) ? \Carbon\Carbon::parse($data['scheduled_at']) : null
            );

            return $this->successResponse(
                new AdminNotificationResource($notification),
                'تم إنشاء الإشعار بنجاح'
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Create bulk notifications
     */
    public function storeBulk(BulkNotificationRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            
            $count = $this->notificationService->createBulkNotification(
                $data['user_ids'],
                $data['title'],
                $data['message'],
                $data['type'],
                $data['data'] ?? [],
                $data['priority'] ?? 'medium',
                $data['action_url'] ?? null,
                isset($data['scheduled_at']) ? \Carbon\Carbon::parse($data['scheduled_at']) : null
            );

            return $this->successResponse([
                'created_count' => $count
            ], "تم إنشاء {$count} إشعار بنجاح");
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Send notification to all users
     */
    public function sendToAll(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'message' => 'required|string',
                'type' => 'required|in:shipment,offer,reward,general,order_status,payment',
                'priority' => 'sometimes|in:low,medium,high',
                'data' => 'sometimes|array',
                'action_url' => 'sometimes|string',
                'scheduled_at' => 'sometimes|date',
            ]);

            $userIds = User::where('is_active', true)->pluck('id')->toArray();
            
            $count = $this->notificationService->createBulkNotification(
                $userIds,
                $request->title,
                $request->message,
                $request->type,
                $request->data ?? [],
                $request->priority ?? 'medium',
                $request->action_url,
                $request->scheduled_at ? \Carbon\Carbon::parse($request->scheduled_at) : null
            );

            return $this->successResponse([
                'created_count' => $count
            ], "تم إرسال الإشعار إلى {$count} مستخدم");
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get notification statistics
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = $this->notificationService->getAdminNotificationStats();
            return $this->successResponse($stats);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Delete notification (Admin)
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $notification = $this->notificationService->model->find($id);
            
            if (!$notification) {
                return $this->errorResponse('الإشعار غير موجود', null, 404);
            }

            $notification->delete();

            return $this->successResponse(null, 'تم حذف الإشعار بنجاح');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Clean old notifications
     */
    public function cleanOld(Request $request): JsonResponse
    {
        try {
            $daysOld = $request->get('days_old', 30);
            $deletedCount = $this->notificationService->cleanOldNotifications($daysOld);

            return $this->successResponse([
                'deleted_count' => $deletedCount
            ], "تم حذف {$deletedCount} إشعار قديم");
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get users for notification targeting
     */
    public function getUsers(Request $request): JsonResponse
    {
        try {
            $query = User::select('id', 'name', 'email', 'role')
                        ->where('is_active', true);

            if ($request->has('role')) {
                $query->where('role', $request->role);
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%");
                });
            }

            $users = $query->paginate($request->get('per_page', 50));

            return $this->successResponse([
                'users' => $users->items(),
                'pagination' => [
                    'current_page' => $users->currentPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                    'last_page' => $users->lastPage(),
                ],
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}
