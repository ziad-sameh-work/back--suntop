<?php

namespace App\Modules\Admin\Controllers;

use App\Modules\Core\BaseController;
use App\Modules\Loyalty\Models\LoyaltyPoint;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminLoyaltyController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:admin');
    }

    /**
     * Get loyalty points transactions
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = LoyaltyPoint::with('user');

            // Apply filters
            if ($request->has('type') && $request->type !== 'all') {
                $query->where('type', $request->type);
            }

            if ($request->has('user_id') && $request->user_id) {
                $query->where('user_id', $request->user_id);
            }

            if ($request->has('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->has('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%");
                });
            }

            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $perPage = $request->get('limit', 20);
            $transactions = $query->paginate($perPage);

            return $this->successResponse([
                'transactions' => $transactions->items(),
                'pagination' => [
                    'current_page' => $transactions->currentPage(),
                    'per_page' => $transactions->perPage(),
                    'total' => $transactions->total(),
                    'total_pages' => $transactions->lastPage(),
                    'has_next' => $transactions->hasMorePages(),
                    'has_prev' => $transactions->currentPage() > 1,
                ],
                'summary' => [
                    'total_transactions' => LoyaltyPoint::count(),
                    'total_points_awarded' => LoyaltyPoint::where('points', '>', 0)->sum('points'),
                    'total_points_redeemed' => LoyaltyPoint::where('points', '<', 0)->sum('points'),
                    'active_users' => LoyaltyPoint::distinct('user_id')->count(),
                ]
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Award points to user
     */
    public function awardPoints(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'points' => 'required|integer|min:1',
                'description' => 'required|string|max:255',
                'expires_at' => 'nullable|date|after:today',
            ]);

            $user = User::findOrFail($request->user_id);

            LoyaltyPoint::create([
                'user_id' => $user->id,
                'points' => $request->points,
                'type' => 'admin_award',
                'description' => $request->description,
                'expires_at' => $request->expires_at,
            ]);

            return $this->successResponse(null, 'تم منح النقاط بنجاح');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Deduct points from user
     */
    public function deductPoints(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'points' => 'required|integer|min:1',
                'description' => 'required|string|max:255',
            ]);

            $user = User::findOrFail($request->user_id);
            $userBalance = LoyaltyPoint::where('user_id', $user->id)->sum('points');

            if ($userBalance < $request->points) {
                return $this->errorResponse('المستخدم لا يملك نقاط كافية');
            }

            LoyaltyPoint::create([
                'user_id' => $user->id,
                'points' => -$request->points,
                'type' => 'admin_deduction',
                'description' => $request->description,
            ]);

            return $this->successResponse(null, 'تم خصم النقاط بنجاح');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get user loyalty summary
     */
    public function userSummary(int $userId): JsonResponse
    {
        try {
            $user = User::findOrFail($userId);
            
            $balance = LoyaltyPoint::where('user_id', $userId)->sum('points');
            $earned = LoyaltyPoint::where('user_id', $userId)->where('points', '>', 0)->sum('points');
            $redeemed = LoyaltyPoint::where('user_id', $userId)->where('points', '<', 0)->sum('points');
            $expiring = LoyaltyPoint::where('user_id', $userId)
                                  ->where('expires_at', '<=', now()->addDays(30))
                                  ->where('expires_at', '>', now())
                                  ->sum('points');

            $recentTransactions = LoyaltyPoint::where('user_id', $userId)
                                            ->orderBy('created_at', 'desc')
                                            ->take(10)
                                            ->get();

            return $this->successResponse([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'summary' => [
                    'current_balance' => $balance,
                    'total_earned' => $earned,
                    'total_redeemed' => abs($redeemed),
                    'expiring_soon' => $expiring,
                ],
                'recent_transactions' => $recentTransactions,
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get loyalty statistics
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = [
                'overview' => [
                    'total_transactions' => LoyaltyPoint::count(),
                    'total_users' => LoyaltyPoint::distinct('user_id')->count(),
                    'total_points_awarded' => LoyaltyPoint::where('points', '>', 0)->sum('points'),
                    'total_points_redeemed' => abs(LoyaltyPoint::where('points', '<', 0)->sum('points')),
                ],
                'by_type' => LoyaltyPoint::selectRaw('type, COUNT(*) as count, SUM(points) as total_points')
                                       ->groupBy('type')
                                       ->get(),
                'top_users' => LoyaltyPoint::selectRaw('user_id, SUM(points) as balance')
                                         ->with('user:id,name,email')
                                         ->groupBy('user_id')
                                         ->orderBy('balance', 'desc')
                                         ->take(10)
                                         ->get(),
                'expiring_points' => LoyaltyPoint::where('expires_at', '<=', now()->addDays(30))
                                                 ->where('expires_at', '>', now())
                                                 ->sum('points'),
                'monthly_trend' => LoyaltyPoint::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(points) as total')
                                              ->where('created_at', '>=', now()->subMonths(12))
                                              ->groupBy('month')
                                              ->orderBy('month')
                                              ->get(),
            ];

            return $this->successResponse(['statistics' => $stats]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Clean expired points
     */
    public function cleanExpired(): JsonResponse
    {
        try {
            $expiredCount = LoyaltyPoint::where('expires_at', '<', now())
                                      ->where('points', '>', 0)
                                      ->count();

            LoyaltyPoint::where('expires_at', '<', now())
                       ->where('points', '>', 0)
                       ->delete();

            return $this->successResponse(
                ['cleaned_count' => $expiredCount],
                "تم حذف {$expiredCount} نقطة منتهية الصلاحية"
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}
