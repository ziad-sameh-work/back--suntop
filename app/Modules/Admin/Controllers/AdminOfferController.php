<?php

namespace App\Modules\Admin\Controllers;

use App\Modules\Core\BaseController;
use App\Modules\Offers\Models\Offer;
use App\Modules\Admin\Requests\CreateOfferRequest;
use App\Modules\Admin\Requests\UpdateOfferRequest;
use App\Modules\Admin\Resources\AdminOfferResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminOfferController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:admin');
    }

    /**
     * Get all offers
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Offer::withTrashed();

            // Apply filters
            if ($request->has('status') && $request->status !== 'all') {
                if ($request->status === 'active') {
                    $query->where('is_active', true);
                } elseif ($request->status === 'inactive') {
                    $query->where('is_active', false);
                } elseif ($request->status === 'expired') {
                    $query->where('valid_until', '<', now());
                } elseif ($request->status === 'upcoming') {
                    $query->where('valid_from', '>', now());
                }
            }

            if ($request->has('type') && $request->type !== 'all') {
                $query->where('type', $request->type);
            }

            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'LIKE', "%{$search}%")
                      ->orWhere('code', 'LIKE', "%{$search}%")
                      ->orWhere('description', 'LIKE', "%{$search}%");
                });
            }

            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $perPage = $request->get('limit', 20);
            $offers = $query->paginate($perPage);

            return $this->successResponse([
                'offers' => AdminOfferResource::collection($offers),
                'pagination' => [
                    'current_page' => $offers->currentPage(),
                    'per_page' => $offers->perPage(),
                    'total' => $offers->total(),
                    'total_pages' => $offers->lastPage(),
                    'has_next' => $offers->hasMorePages(),
                    'has_prev' => $offers->currentPage() > 1,
                ],
                'summary' => [
                    'total_offers' => Offer::count(),
                    'active_offers' => Offer::where('is_active', true)->count(),
                    'expired_offers' => Offer::where('valid_until', '<', now())->count(),
                    'used_offers' => Offer::where('used_count', '>', 0)->count(),
                ]
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Create new offer
     */
    public function store(CreateOfferRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $offer = Offer::create($data);

            return $this->successResponse(
                ['offer' => new AdminOfferResource($offer)],
                'تم إنشاء العرض بنجاح',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get specific offer
     */
    public function show(int $id): JsonResponse
    {
        try {
            $offer = Offer::withTrashed()->findOrFail($id);

            return $this->successResponse([
                'offer' => new AdminOfferResource($offer)
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), null, 404);
        }
    }

    /**
     * Update offer
     */
    public function update(UpdateOfferRequest $request, int $id): JsonResponse
    {
        try {
            $offer = Offer::withTrashed()->findOrFail($id);
            $data = $request->validated();
            
            $offer->update($data);

            return $this->successResponse(
                ['offer' => new AdminOfferResource($offer)],
                'تم تحديث العرض بنجاح'
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Delete offer
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $offer = Offer::findOrFail($id);
            $offer->delete();

            return $this->successResponse(null, 'تم حذف العرض بنجاح');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Toggle offer status
     */
    public function toggleStatus(int $id): JsonResponse
    {
        try {
            $offer = Offer::findOrFail($id);
            $offer->update(['is_active' => !$offer->is_active]);

            $status = $offer->is_active ? 'مفعل' : 'غير مفعل';
            return $this->successResponse(
                ['offer' => new AdminOfferResource($offer)],
                "تم تحديث حالة العرض إلى {$status}"
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get offer usage statistics
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = [
                'overview' => [
                    'total_offers' => Offer::count(),
                    'active_offers' => Offer::where('is_active', true)->count(),
                    'expired_offers' => Offer::where('valid_until', '<', now())->count(),
                    'upcoming_offers' => Offer::where('valid_from', '>', now())->count(),
                ],
                'by_type' => Offer::selectRaw('type, COUNT(*) as count')
                                 ->groupBy('type')
                                 ->get(),
                'most_used' => Offer::where('used_count', '>', 0)
                                   ->orderBy('used_count', 'desc')
                                   ->take(10)
                                   ->get(['id', 'title', 'code', 'used_count', 'usage_limit']),
                'expiring_soon' => Offer::where('is_active', true)
                                       ->where('valid_until', '>', now())
                                       ->where('valid_until', '<=', now()->addDays(7))
                                       ->get(['id', 'title', 'code', 'valid_until']),
            ];

            return $this->successResponse(['statistics' => $stats]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}
