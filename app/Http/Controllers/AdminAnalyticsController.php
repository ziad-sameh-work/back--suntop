<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Modules\Orders\Models\Order;
use App\Modules\Products\Models\Product;
use App\Modules\Merchants\Models\Merchant;
use App\Modules\Users\Models\UserCategory;
use App\Modules\Loyalty\Models\LoyaltyPoint;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminAnalyticsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index(Request $request)
    {
        $timeRange = $request->get('time_range', '30');
        $startDate = now()->subDays($timeRange);
        $endDate = now();

        $data = [
            'overviewStats' => $this->getOverviewStats($startDate, $endDate),
            'salesStats' => $this->getSalesStats($startDate, $endDate),
            'userStats' => $this->getUserStats($startDate, $endDate),
            'productStats' => $this->getProductStats($startDate, $endDate),
            'loyaltyStats' => $this->getLoyaltyStats($startDate, $endDate),
            'chartData' => [
                'salesChart' => $this->getSalesChartData($startDate, $endDate),
                'ordersChart' => $this->getOrdersChartData($startDate, $endDate),
                'usersChart' => $this->getUsersChartData($startDate, $endDate),
                'revenueChart' => $this->getRevenueChartData($startDate, $endDate),
            ],
            'topPerformers' => [
                'products' => $this->getTopProducts($startDate, $endDate),
                'categories' => $this->getTopCategories($startDate, $endDate),
                'merchants' => $this->getTopMerchants($startDate, $endDate),
                'users' => $this->getTopUsers($startDate, $endDate),
            ],
            'timeRange' => $timeRange,
        ];

        return view('admin.analytics.index', $data);
    }

    private function getOverviewStats($startDate, $endDate)
    {
        $orders = Order::whereBetween('created_at', [$startDate, $endDate]);
        $prevStartDate = $startDate->copy()->subDays($endDate->diffInDays($startDate));
        $prevOrders = Order::whereBetween('created_at', [$prevStartDate, $startDate]);

        $currentRevenue = (clone $orders)->where('payment_status', 'paid')->sum('total_amount');
        $prevRevenue = (clone $prevOrders)->where('payment_status', 'paid')->sum('total_amount');
        $revenueChange = $prevRevenue > 0 ? (($currentRevenue - $prevRevenue) / $prevRevenue) * 100 : 0;

        $currentOrders = (clone $orders)->count();
        $prevOrdersCount = (clone $prevOrders)->count();
        $ordersChange = $prevOrdersCount > 0 ? (($currentOrders - $prevOrdersCount) / $prevOrdersCount) * 100 : 0;

        $currentUsers = User::whereBetween('created_at', [$startDate, $endDate])->where('role', 'customer')->count();
        $prevUsers = User::whereBetween('created_at', [$prevStartDate, $startDate])->where('role', 'customer')->count();
        $usersChange = $prevUsers > 0 ? (($currentUsers - $prevUsers) / $prevUsers) * 100 : 0;

        return [
            'total_revenue' => $currentRevenue ?: 0,
            'revenue_change' => $revenueChange,
            'total_orders' => $currentOrders,
            'orders_change' => $ordersChange,
            'new_users' => $currentUsers,
            'users_change' => $usersChange,
            'avg_order_value' => $currentOrders > 0 ? $currentRevenue / $currentOrders : 0,
            'conversion_rate' => $this->getConversionRate($startDate, $endDate),
        ];
    }

    private function getSalesStats($startDate, $endDate)
    {
        $orders = Order::whereBetween('created_at', [$startDate, $endDate]);

        // Get payment methods with counts
        $paymentMethods = (clone $orders)
            ->select('payment_method', DB::raw('count(*) as count'))
            ->whereNotNull('payment_method')
            ->groupBy('payment_method')
            ->pluck('count', 'payment_method')
            ->toArray();

        return [
            'total_sales' => (clone $orders)->where('payment_status', 'paid')->sum('total_amount') ?: 0,
            'pending_sales' => (clone $orders)->where('payment_status', 'pending')->sum('total_amount') ?: 0,
            'failed_sales' => (clone $orders)->where('payment_status', 'failed')->sum('total_amount') ?: 0,
            'refunded_sales' => (clone $orders)->where('payment_status', 'refunded')->sum('total_amount') ?: 0,
            'orders_by_status' => [
                'pending' => (clone $orders)->where('status', 'pending')->count(),
                'confirmed' => (clone $orders)->where('status', 'confirmed')->count(),
                'processing' => (clone $orders)->where('status', 'processing')->count(),
                'shipped' => (clone $orders)->where('status', 'shipped')->count(),
                'delivered' => (clone $orders)->where('status', 'delivered')->count(),
                'cancelled' => (clone $orders)->where('status', 'cancelled')->count(),
            ],
            'payment_methods' => $paymentMethods,
        ];
    }

    private function getUserStats($startDate, $endDate)
    {
        $users = User::where('role', 'customer');

        return [
            'total_users' => (clone $users)->count(),
            'new_users' => (clone $users)->whereBetween('created_at', [$startDate, $endDate])->count(),
            'active_users' => (clone $users)->whereBetween('last_login_at', [$startDate, $endDate])->count(),
            'users_with_orders' => (clone $users)->whereHas('orders', function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            })->count(),
        ];
    }

    private function getProductStats($startDate, $endDate)
    {
        return [
            'total_products' => Product::count(),
            'active_products' => Product::where('is_available', true)->count(),
            'out_of_stock' => Product::where('stock_quantity', '<=', 0)->count(),
            'low_stock' => Product::where('stock_quantity', '>', 0)->where('stock_quantity', '<=', 10)->count(),
        ];
    }

    private function getLoyaltyStats($startDate, $endDate)
    {
        // Check if loyalty_points table exists
        if (!\Illuminate\Support\Facades\Schema::hasTable('loyalty_points')) {
            return [
                'total_points_awarded' => 0,
                'total_points_redeemed' => 0,
                'active_users_with_points' => 0,
                'avg_points_per_user' => 0,
                'points_transactions' => 0,
            ];
        }

        $loyaltyPoints = LoyaltyPoint::whereBetween('created_at', [$startDate, $endDate]);

        return [
            'total_points_awarded' => (clone $loyaltyPoints)->where('points', '>', 0)->sum('points') ?: 0,
            'total_points_redeemed' => abs((clone $loyaltyPoints)->where('points', '<', 0)->sum('points')) ?: 0,
            'active_users_with_points' => User::whereHas('loyaltyPoints', function($q) {
                $q->where('points', '>', 0);
            })->count(),
            'avg_points_per_user' => LoyaltyPoint::where('points', '>', 0)->avg('points') ?: 0,
            'points_transactions' => (clone $loyaltyPoints)->count(),
        ];
    }

    private function getConversionRate($startDate, $endDate)
    {
        $totalUsers = User::where('role', 'customer')
            ->whereBetween('last_login_at', [$startDate, $endDate])
            ->count();
        
        $usersWithOrders = User::where('role', 'customer')
            ->whereHas('orders', function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->count();

        return $totalUsers > 0 ? ($usersWithOrders / $totalUsers) * 100 : 0;
    }

    private function getSalesChartData($startDate, $endDate)
    {
        $data = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('payment_status', 'paid')
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'labels' => $data->pluck('date')->map(function($date) {
                return Carbon::parse($date)->format('M d');
            })->toArray(),
            'data' => $data->pluck('total')->toArray(),
        ];
    }

    private function getOrdersChartData($startDate, $endDate)
    {
        $data = Order::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'labels' => $data->pluck('date')->map(function($date) {
                return Carbon::parse($date)->format('M d');
            })->toArray(),
            'data' => $data->pluck('count')->toArray(),
        ];
    }

    private function getUsersChartData($startDate, $endDate)
    {
        $data = User::where('role', 'customer')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'labels' => $data->pluck('date')->map(function($date) {
                return Carbon::parse($date)->format('M d');
            })->toArray(),
            'data' => $data->pluck('count')->toArray(),
        ];
    }

    private function getRevenueChartData($startDate, $endDate)
    {
        $data = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('payment_status', 'paid')
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'labels' => $data->pluck('date')->map(function($date) {
                return Carbon::parse($date)->format('M d');
            })->toArray(),
            'data' => $data->pluck('total')->toArray(),
        ];
    }

    private function getTopProducts($startDate, $endDate)
    {
        return Product::whereHas('orderItems', function($q) use ($startDate, $endDate) {
                $q->whereHas('order', function($qq) use ($startDate, $endDate) {
                    $qq->whereBetween('created_at', [$startDate, $endDate])
                       ->where('payment_status', 'paid');
                });
            })
            ->withSum(['orderItems' => function($q) use ($startDate, $endDate) {
                $q->whereHas('order', function($qq) use ($startDate, $endDate) {
                    $qq->whereBetween('created_at', [$startDate, $endDate])
                       ->where('payment_status', 'paid');
                });
            }], 'quantity')
            ->withSum(['orderItems' => function($q) use ($startDate, $endDate) {
                $q->whereHas('order', function($qq) use ($startDate, $endDate) {
                    $qq->whereBetween('created_at', [$startDate, $endDate])
                       ->where('payment_status', 'paid');
                });
            }], 'total_price')
            ->orderBy('order_items_sum_quantity', 'desc')
            ->limit(5)
            ->get();
    }

    private function getTopCategories($startDate, $endDate)
    {
        $categories = UserCategory::withCount(['users' => function($q) use ($startDate, $endDate) {
                $q->whereHas('orders', function($qq) use ($startDate, $endDate) {
                    $qq->whereBetween('created_at', [$startDate, $endDate])
                       ->where('payment_status', 'paid');
                });
            }])
            ->orderBy('users_count', 'desc')
            ->limit(5)
            ->get();

        // Calculate revenue for each category manually
        foreach ($categories as $category) {
            $revenue = DB::table('orders')
                ->join('users', 'orders.user_id', '=', 'users.id')
                ->where('users.user_category_id', $category->id)
                ->whereBetween('orders.created_at', [$startDate, $endDate])
                ->where('orders.payment_status', 'paid')
                ->whereNull('orders.deleted_at')
                ->sum('orders.total_amount');
            
            $category->total_revenue = $revenue;
        }

        return $categories;
    }

    private function getTopMerchants($startDate, $endDate)
    {
        return Merchant::whereHas('orders', function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate])
                  ->where('payment_status', 'paid');
            })
            ->withSum(['orders' => function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate])
                  ->where('payment_status', 'paid');
            }], 'total_amount')
            ->withCount(['orders' => function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate])
                  ->where('payment_status', 'paid');
            }])
            ->orderBy('orders_sum_total_amount', 'desc')
            ->limit(5)
            ->get();
    }

    private function getTopUsers($startDate, $endDate)
    {
        return User::where('role', 'customer')
            ->whereHas('orders', function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate])
                  ->where('payment_status', 'paid');
            })
            ->withSum(['orders' => function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate])
                  ->where('payment_status', 'paid');
            }], 'total_amount')
            ->withCount(['orders' => function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate])
                  ->where('payment_status', 'paid');
            }])
            ->orderBy('orders_sum_total_amount', 'desc')
            ->limit(10)
            ->get();
    }

    public function export(Request $request)
    {
        $timeRange = $request->get('time_range', '30');
        $startDate = now()->subDays($timeRange);
        $endDate = now();

        $data = [
            'overview' => $this->getOverviewStats($startDate, $endDate),
            'sales' => $this->getSalesStats($startDate, $endDate),
            'users' => $this->getUserStats($startDate, $endDate),
            'products' => $this->getProductStats($startDate, $endDate),
            'loyalty' => $this->getLoyaltyStats($startDate, $endDate),
        ];

        return response()->json($data);
    }
}