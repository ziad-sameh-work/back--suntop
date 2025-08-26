<?php

namespace App\Modules\Admin\Controllers;

use App\Modules\Core\BaseController;
use App\Modules\Users\Services\UserCategoryService;
use App\Modules\Admin\Requests\CreateUserCategoryRequest;
use App\Modules\Admin\Requests\UpdateUserCategoryRequest;
use App\Modules\Admin\Resources\UserCategoryResource;
use App\Modules\Admin\Resources\CategoryStatisticsResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserCategoryController extends BaseController
{
    protected $categoryService;

    public function __construct(UserCategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
        $this->middleware('auth:sanctum');
        $this->middleware('role:admin'); // Custom middleware to check admin role
    }

    /**
     * Get all user categories
     */
    public function index(): JsonResponse
    {
        try {
            $categories = $this->categoryService->getAllActive();
            
            return $this->successResponse([
                'categories' => UserCategoryResource::collection($categories)
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Create new user category
     */
    public function store(CreateUserCategoryRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $category = $this->categoryService->createCategory($data);
            
            return $this->successResponse(
                ['category' => new UserCategoryResource($category)],
                'تم إنشاء الفئة بنجاح',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get specific category
     */
    public function show(int $id): JsonResponse
    {
        try {
            $category = $this->categoryService->findByIdOrFail($id);
            
            return $this->successResponse([
                'category' => new UserCategoryResource($category)
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), null, 404);
        }
    }

    /**
     * Update user category
     */
    public function update(UpdateUserCategoryRequest $request, int $id): JsonResponse
    {
        try {
            $data = $request->validated();
            $category = $this->categoryService->updateCategory($id, $data);
            
            return $this->successResponse(
                ['category' => new UserCategoryResource($category)],
                'تم تحديث الفئة بنجاح'
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Delete user category
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $result = $this->categoryService->deleteCategory($id);
            
            if ($result) {
                return $this->successResponse(null, 'تم حذف الفئة بنجاح');
            }
            
            return $this->errorResponse('فشل في حذف الفئة');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get category statistics
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = $this->categoryService->getCategoryStatistics();
            
            return $this->successResponse([
                'statistics' => CategoryStatisticsResource::collection($stats)
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get users by category
     */
    public function users(Request $request, int $categoryId): JsonResponse
    {
        try {
            $perPage = $request->get('limit', 20);
            $users = $this->categoryService->getUsersByCategory($categoryId, $perPage);
            
            return $this->successResponse([
                'users' => $users->items(),
                'pagination' => [
                    'current_page' => $users->currentPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                    'total_pages' => $users->lastPage(),
                    'has_next' => $users->hasMorePages(),
                    'has_prev' => $users->currentPage() > 1,
                ]
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Recalculate all user categories
     */
    public function recalculateCategories(): JsonResponse
    {
        try {
            $this->categoryService->recalculateAllUserCategories();
            
            return $this->successResponse(null, 'تم إعادة حساب فئات جميع المستخدمين بنجاح');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get category for specific amount (for testing)
     */
    public function getCategoryForAmount(Request $request): JsonResponse
    {
        try {
            $amount = $request->get('amount', 0);
            $category = $this->categoryService->getCategoryForAmount($amount);
            
            if ($category) {
                return $this->successResponse([
                    'category' => new UserCategoryResource($category),
                    'amount' => $amount
                ]);
            }
            
            return $this->successResponse([
                'category' => null,
                'amount' => $amount,
                'message' => 'لا توجد فئة مناسبة لهذا المبلغ'
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}
