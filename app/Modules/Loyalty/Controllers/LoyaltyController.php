<?php

namespace App\Modules\Loyalty\Controllers;

use App\Modules\Core\BaseController;
use App\Modules\Loyalty\Services\LoyaltyService;
use App\Modules\Loyalty\Services\RewardService;
use App\Modules\Loyalty\Resources\LoyaltyPointsResource;
use App\Modules\Loyalty\Resources\RewardResource;
use App\Modules\Loyalty\Resources\RewardTierResource;
use App\Modules\Loyalty\Models\LoyaltyPoint;
use App\Modules\Loyalty\Models\Reward;
use App\Modules\Loyalty\Models\RewardTier;
use App\Modules\Loyalty\Models\RewardRedemption;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LoyaltyController extends BaseController
{
    protected $loyaltyService;
    protected $rewardService;

    public function __construct(LoyaltyService $loyaltyService, RewardService $rewardService)
    {
        $this->loyaltyService = $loyaltyService;
        $this->rewardService = $rewardService;
    }

    /**
     * Get user's loyalty points summary
     */
    public function getPoints(Request $request): JsonResponse
    {
        try {
            if (!$request->user()) {
                return $this->errorResponse('يجب تسجيل الدخول', null, 401);
            }

            $userId = $request->user()->id;
            $pointsSummary = $this->loyaltyService->getUserPointsSummary($userId);

            return $this->successResponse($pointsSummary);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get user's points transaction history
     */
    public function getTransactions(Request $request): JsonResponse
    {
        try {
            if (!$request->user()) {
                return $this->errorResponse('يجب تسجيل الدخول', null, 401);
            }

            $userId = $request->user()->id;
            $filters = [
                'type' => $request->get('type'),
                'date_from' => $request->get('date_from'),
                'date_to' => $request->get('date_to'),
            ];

            $perPage = $request->get('per_page', 20);
            $transactions = $this->loyaltyService->getUserTransactions($userId, $filters, $perPage);

            return $this->successResponse([
                'transactions' => LoyaltyPointsResource::collection($transactions),
                'pagination' => [
                    'current_page' => $transactions->currentPage(),
                    'per_page' => $transactions->perPage(),
                    'total' => $transactions->total(),
                    'last_page' => $transactions->lastPage(),
                    'has_next' => $transactions->hasMorePages(),
                    'has_prev' => $transactions->currentPage() > 1,
                ],
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get available rewards
     */
    public function getRewards(Request $request): JsonResponse
    {
        try {
            $filters = [
                'category' => $request->get('category'),
                'type' => $request->get('type'),
                'min_points' => $request->get('min_points'),
                'max_points' => $request->get('max_points'),
                'user_id' => $request->user() ? $request->user()->id : null,
            ];

            $sortBy = $request->get('sort_by', 'points_cost');
            $sortOrder = $request->get('sort_order', 'asc');
            $perPage = $request->get('per_page', 20);

            $rewards = $this->rewardService->getAvailableRewards($filters, $sortBy, $sortOrder, $perPage);

            return $this->successResponse([
                'rewards' => RewardResource::collection($rewards),
                'pagination' => [
                    'current_page' => $rewards->currentPage(),
                    'per_page' => $rewards->perPage(),
                    'total' => $rewards->total(),
                    'last_page' => $rewards->lastPage(),
                    'has_next' => $rewards->hasMorePages(),
                    'has_prev' => $rewards->currentPage() > 1,
                ],
                'filters' => [
                    'categories' => $this->rewardService->getAvailableCategories(),
                    'types' => Reward::TYPES,
                    'points_range' => $this->rewardService->getPointsRange(),
                ],
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get reward tiers/levels
     */
    public function getTiers(Request $request): JsonResponse
    {
        try {
            $tiers = RewardTier::active()->ordered()->get();
            $userTier = null;
            $nextTier = null;
            $progress = 0;

            if ($request->user()) {
                $userTier = RewardTier::getTierForUser($request->user());
                $nextTier = RewardTier::getNextTierForUser($request->user());
                
                if ($userTier && $nextTier) {
                    $progress = $userTier->getProgressPercentage($request->user());
                }
            }

            return $this->successResponse([
                'tiers' => RewardTierResource::collection($tiers),
                'user_tier' => $userTier ? new RewardTierResource($userTier) : null,
                'next_tier' => $nextTier ? new RewardTierResource($nextTier) : null,
                'progress_percentage' => $progress,
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Redeem reward
     */
    public function redeemReward(Request $request, string $rewardId): JsonResponse
    {
        try {
            $request->validate([
                'order_id' => 'sometimes|exists:orders,id',
            ]);

            if (!$request->user()) {
                return $this->errorResponse('يجب تسجيل الدخول', null, 401);
            }

            $redemption = $this->rewardService->redeemReward(
                $rewardId,
                $request->user()->id,
                $request->order_id ?? null
            );

            return $this->successResponse([
                'redemption' => [
                    'id' => $redemption->id,
                    'redemption_code' => $redemption->redemption_code,
                    'points_deducted' => $redemption->points_deducted,
                    'discount_amount' => $redemption->discount_amount,
                    'expires_at' => $redemption->expires_at->toISOString(),
                    'status' => $redemption->status,
                ]
            ], 'تم استبدال المكافأة بنجاح');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get user's reward redemptions
     */
    public function userRedemptions(Request $request): JsonResponse
    {
        try {
            if (!$request->user()) {
                return $this->errorResponse('يجب تسجيل الدخول', null, 401);
            }

            $status = $request->get('status');
            $perPage = $request->get('per_page', 20);

            $query = RewardRedemption::where('user_id', $request->user()->id)
                                   ->with(['reward', 'order']);

            if ($status) {
                $query->where('status', $status);
            }

            $redemptions = $query->orderBy('created_at', 'desc')
                                ->paginate($perPage);

            return $this->successResponse([
                'redemptions' => $redemptions->map(function($redemption) {
                    return [
                        'id' => $redemption->id,
                        'reward' => [
                            'id' => $redemption->reward->id,
                            'title' => $redemption->reward->title,
                            'type' => $redemption->reward->type,
                        ],
                        'redemption_code' => $redemption->redemption_code,
                        'points_deducted' => $redemption->points_deducted,
                        'discount_amount' => $redemption->discount_amount,
                        'status' => $redemption->status,
                        'status_name' => $redemption->status_name,
                        'expires_at' => $redemption->expires_at->toISOString(),
                        'used_at' => $redemption->used_at ? $redemption->used_at->toISOString() : null,
                        'created_at' => $redemption->created_at->toISOString(),
                    ];
                }),
                'pagination' => [
                    'current_page' => $redemptions->currentPage(),
                    'per_page' => $redemptions->perPage(),
                    'total' => $redemptions->total(),
                    'last_page' => $redemptions->lastPage(),
                ],
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Add points (for merchants/admin)
     */
    public function addPoints(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'customer_id' => 'required|exists:users,id',
                'points' => 'required|integer|min:1',
                'transaction_id' => 'sometimes|string',
                'reason' => 'required|string|max:255',
            ]);

            // Check if user is admin or merchant
            if (!$request->user() || !in_array($request->user()->role, ['admin', 'merchant'])) {
                return $this->errorResponse('غير مصرح لك بهذا الإجراء', null, 403);
            }

            $loyaltyPoint = $this->loyaltyService->addPoints(
                $request->customer_id,
                $request->points,
                $request->reason,
                $request->transaction_id ?? null,
                $request->user()->id
            );

            return $this->successResponse([
                'loyalty_point' => new LoyaltyPointsResource($loyaltyPoint)
            ], 'تم إضافة النقاط بنجاح');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get loyalty analytics
     */
    public function getAnalytics(Request $request): JsonResponse
    {
        try {
            if (!$request->user()) {
                return $this->errorResponse('يجب تسجيل الدخول', null, 401);
            }

            $userId = $request->user()->id;
            $analytics = $this->loyaltyService->getUserAnalytics($userId);

            return $this->successResponse($analytics);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get points earning opportunities
     */
    public function getEarningOpportunities(Request $request): JsonResponse
    {
        try {
            $opportunities = $this->loyaltyService->getEarningOpportunities();

            return $this->successResponse([
                'opportunities' => $opportunities
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}
