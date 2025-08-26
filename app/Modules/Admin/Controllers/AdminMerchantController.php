<?php

namespace App\Modules\Admin\Controllers;

use App\Modules\Core\BaseController;
use App\Modules\Merchants\Models\Merchant;
use App\Modules\Admin\Requests\CreateMerchantRequest;
use App\Modules\Admin\Requests\UpdateMerchantRequest;
use App\Modules\Admin\Resources\AdminMerchantResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminMerchantController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:admin');
    }

    /**
     * Get all merchants
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Merchant::withTrashed()->withCount(['orders']);

            // Apply filters
            if ($request->has('status') && $request->status !== 'all') {
                if ($request->status === 'active') {
                    $query->where('is_active', true);
                } elseif ($request->status === 'inactive') {
                    $query->where('is_active', false);
                }
            }

            if ($request->has('category') && $request->category !== 'all') {
                $query->where('category', $request->category);
            }

            if ($request->has('delivery') && $request->delivery !== 'all') {
                $query->where('delivery_available', $request->delivery === 'true');
            }

            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%")
                      ->orWhere('phone', 'LIKE', "%{$search}%")
                      ->orWhere('city', 'LIKE', "%{$search}%");
                });
            }

            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $perPage = $request->get('limit', 20);
            $merchants = $query->paginate($perPage);

            return $this->successResponse([
                'merchants' => AdminMerchantResource::collection($merchants),
                'pagination' => [
                    'current_page' => $merchants->currentPage(),
                    'per_page' => $merchants->perPage(),
                    'total' => $merchants->total(),
                    'total_pages' => $merchants->lastPage(),
                    'has_next' => $merchants->hasMorePages(),
                    'has_prev' => $merchants->currentPage() > 1,
                ],
                'summary' => [
                    'total_merchants' => Merchant::count(),
                    'active_merchants' => Merchant::where('is_active', true)->count(),
                    'open_merchants' => Merchant::where('is_open', true)->count(),
                    'delivery_available' => Merchant::where('delivery_available', true)->count(),
                ]
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Create new merchant
     */
    public function store(CreateMerchantRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $merchant = Merchant::create($data);

            return $this->successResponse(
                ['merchant' => new AdminMerchantResource($merchant)],
                'تم إنشاء التاجر بنجاح',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get specific merchant
     */
    public function show(int $id): JsonResponse
    {
        try {
            $merchant = Merchant::withTrashed()
                              ->withCount(['orders'])
                              ->findOrFail($id);

            $stats = [
                'total_orders' => $merchant->orders()->count(),
                'completed_orders' => $merchant->orders()->where('status', 'delivered')->count(),
                'pending_orders' => $merchant->orders()->where('status', 'pending')->count(),
                'total_revenue' => $merchant->orders()->where('status', 'delivered')->sum('total_amount'),
                'average_delivery_time' => $merchant->estimated_delivery_time,
            ];

            return $this->successResponse([
                'merchant' => new AdminMerchantResource($merchant),
                'statistics' => $stats,
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), null, 404);
        }
    }

    /**
     * Update merchant
     */
    public function update(UpdateMerchantRequest $request, int $id): JsonResponse
    {
        try {
            $merchant = Merchant::withTrashed()->findOrFail($id);
            $data = $request->validated();
            
            $merchant->update($data);

            return $this->successResponse(
                ['merchant' => new AdminMerchantResource($merchant)],
                'تم تحديث التاجر بنجاح'
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Delete merchant
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $merchant = Merchant::findOrFail($id);
            
            // Check if merchant has orders
            if ($merchant->orders()->count() > 0) {
                return $this->errorResponse('لا يمكن حذف التاجر لوجود طلبات مرتبطة به');
            }

            $merchant->delete();

            return $this->successResponse(null, 'تم حذف التاجر بنجاح');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Toggle merchant active status
     */
    public function toggleStatus(int $id): JsonResponse
    {
        try {
            $merchant = Merchant::findOrFail($id);
            $merchant->update(['is_active' => !$merchant->is_active]);

            $status = $merchant->is_active ? 'مفعل' : 'غير مفعل';
            return $this->successResponse(
                ['merchant' => new AdminMerchantResource($merchant)],
                "تم تحديث حالة التاجر إلى {$status}"
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Toggle merchant open status
     */
    public function toggleOpen(int $id): JsonResponse
    {
        try {
            $merchant = Merchant::findOrFail($id);
            $merchant->update(['is_open' => !$merchant->is_open]);

            $status = $merchant->is_open ? 'مفتوح' : 'مغلق';
            return $this->successResponse(
                ['merchant' => new AdminMerchantResource($merchant)],
                "تم تحديث حالة التاجر إلى {$status}"
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get merchant statistics
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = [
                'overview' => [
                    'total_merchants' => Merchant::count(),
                    'active_merchants' => Merchant::where('is_active', true)->count(),
                    'open_merchants' => Merchant::where('is_open', true)->count(),
                    'delivery_available' => Merchant::where('delivery_available', true)->count(),
                ],
                'by_category' => Merchant::selectRaw('category, COUNT(*) as count')
                                      ->whereNotNull('category')
                                      ->groupBy('category')
                                      ->orderBy('count', 'desc')
                                      ->get(),
                'by_city' => Merchant::selectRaw('city, COUNT(*) as count')
                                   ->whereNotNull('city')
                                   ->groupBy('city')
                                   ->orderBy('count', 'desc')
                                   ->get(),
                'top_rated' => Merchant::where('rating', '>', 0)
                                     ->orderBy('rating', 'desc')
                                     ->take(10)
                                     ->get(['id', 'name', 'rating', 'review_count']),
                'performance' => Merchant::withCount(['orders'])
                                       ->orderBy('orders_count', 'desc')
                                       ->take(10)
                                       ->get(),
            ];

            return $this->successResponse(['statistics' => $stats]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}
