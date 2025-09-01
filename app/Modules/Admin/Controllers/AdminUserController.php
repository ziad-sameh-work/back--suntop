<?php

namespace App\Modules\Admin\Controllers;

use App\Modules\Core\BaseController;
use App\Models\User;
use App\Modules\Admin\Requests\CreateUserRequest;
use App\Modules\Admin\Requests\UpdateUserRequest;
use App\Modules\Admin\Resources\AdminUserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:admin');
    }

    /**
     * Get all users with filtering and pagination
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = User::with('userCategory');

            // Apply filters
            if ($request->has('role') && $request->role !== 'all') {
                $query->where('role', $request->role);
            }

            if ($request->has('category') && $request->category !== 'all') {
                $query->where('user_category_id', $request->category);
            }

            if ($request->has('status') && $request->status !== 'all') {
                $isActive = $request->status === 'active';
                $query->where('is_active', $isActive);
            }

            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%")
                      ->orWhere('username', 'LIKE', "%{$search}%")
                      ->orWhere('phone', 'LIKE', "%{$search}%");
                });
            }

            // Sort
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $perPage = $request->get('limit', 20);
            $users = $query->paginate($perPage);

            return $this->successResponse([
                'users' => AdminUserResource::collection($users),
                'pagination' => [
                    'current_page' => $users->currentPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                    'total_pages' => $users->lastPage(),
                    'has_next' => $users->hasMorePages(),
                    'has_prev' => $users->currentPage() > 1,
                ],
                'summary' => [
                    'total_users' => User::count(),
                    'active_users' => User::where('is_active', true)->count(),
                    'inactive_users' => User::where('is_active', false)->count(),
                    'admins' => User::where('role', 'admin')->count(),
                    'customers' => User::where('role', 'customer')->count(),
                ]
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Create new user
     */
    public function store(CreateUserRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['password'] = Hash::make($data['password']);
            $data['email_verified_at'] = now();

            $user = User::create($data);

            return $this->successResponse(
                ['user' => new AdminUserResource($user->load('userCategory'))],
                'تم إنشاء المستخدم بنجاح',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get specific user
     */
    public function show(int $id): JsonResponse
    {
        try {
            $user = User::with(['userCategory', 'orders.merchant'])
                       ->withCount('orders')
                       ->findOrFail($id);

            return $this->successResponse([
                'user' => new AdminUserResource($user),
                'stats' => [
                    'total_orders' => $user->orders_count,
                    'total_spent' => $user->total_purchase_amount,
                    'last_order' => $user->orders()->latest()->first()?->created_at,
                    'category_since' => $user->category_updated_at,
                ]
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), null, 404);
        }
    }

    /**
     * Update user
     */
    public function update(UpdateUserRequest $request, int $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);
            $data = $request->validated();

            // Handle password update
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            $user->update($data);

            return $this->successResponse(
                ['user' => new AdminUserResource($user->load('userCategory'))],
                'تم تحديث المستخدم بنجاح'
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Delete user (soft delete)
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);

            // Prevent deleting the current admin user
            if ($user->id === auth()->id()) {
                return $this->errorResponse('لا يمكنك حذف حسابك الشخصي');
            }

            // Check if user has orders
            if ($user->orders()->count() > 0) {
                return $this->errorResponse('لا يمكن حذف المستخدم لوجود طلبات مرتبطة به');
            }

            $user->delete();

            return $this->successResponse(null, 'تم حذف المستخدم بنجاح');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Toggle user status (activate/deactivate)
     */
    public function toggleStatus(int $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);

            // Prevent deactivating the current admin user
            if ($user->id === auth()->id() && $user->is_active) {
                return $this->errorResponse('لا يمكنك إلغاء تفعيل حسابك الشخصي');
            }

            $user->update(['is_active' => !$user->is_active]);

            $status = $user->is_active ? 'تفعيل' : 'إلغاء تفعيل';
            return $this->successResponse(
                ['user' => new AdminUserResource($user->load('userCategory'))],
                "تم {$status} المستخدم بنجاح"
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Reset user password
     */
    public function resetPassword(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'new_password' => 'required|string|min:8|confirmed',
            ], [
                'new_password.required' => 'كلمة المرور الجديدة مطلوبة',
                'new_password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
                'new_password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
            ]);

            $user = User::findOrFail($id);
            $user->update([
                'password' => Hash::make($request->new_password),
                'password_changed_at' => now(),
            ]);

            return $this->successResponse(null, 'تم إعادة تعيين كلمة المرور بنجاح');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get user activity statistics
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = [
                'overview' => [
                    'total_users' => User::count(),
                    'active_users' => User::where('is_active', true)->count(),
                    'new_users_this_month' => User::whereMonth('created_at', now()->month)->count(),
                    'users_with_orders' => User::has('orders')->count(),
                ],
                'by_role' => [
                    'admins' => User::where('role', 'admin')->count(),
                    'customers' => User::where('role', 'customer')->count(),
                ],
                'by_category' => User::leftJoin('user_categories', 'users.user_category_id', '=', 'user_categories.id')
                                   ->selectRaw('user_categories.name as category, user_categories.display_name, COUNT(users.id) as count')
                                   ->groupBy('user_categories.id', 'user_categories.name', 'user_categories.display_name')
                                   ->get(),
                'registration_trend' => User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                                          ->where('created_at', '>=', now()->subDays(30))
                                          ->groupBy('date')
                                          ->orderBy('date')
                                          ->get(),
            ];

            return $this->successResponse(['statistics' => $stats]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}
