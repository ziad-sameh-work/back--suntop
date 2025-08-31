<?php

namespace App\Modules\Admin\Controllers;

use App\Modules\Core\BaseController;
use App\Modules\Orders\Models\Order;
use App\Modules\Products\Models\Product;
use App\Models\User;
use App\Modules\Merchants\Models\Merchant;
use App\Modules\Users\Models\UserCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminAnalyticsController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:admin');
    }

    /**
     * Get comprehensive dashboard analytics
     */
    public function dashboard(Request $request): JsonResponse
    {
        try {
            $dateFrom = $request->get('date_from', now()->startOfMonth());
            $dateTo = $request->get('date_to', now()->endOfMonth());

            $analytics = [
                'overview' => $this->getOverviewStats($dateFrom, $dateTo),
                'revenue' => $this->getRevenueStats($dateFrom, $dateTo),
                'orders' => $this->getOrderStats($dateFrom, $dateTo),
                'customers' => $this->getCustomerStats($dateFrom, $dateTo),
                'products' => $this->getProductStats($dateFrom, $dateTo),
                'merchants' => $this->getMerchantStats($dateFrom, $dateTo),
                'trends' => $this->getTrends($dateFrom, $dateTo),
            ];

            return $this->successResponse(['analytics' => $analytics]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get sales analytics
     */
    public function sales(Request $request): JsonResponse
    {
        try {
            $period = $request->get('period', 'month'); // day, week, month, year
            $dateFrom = $this->getDateFromPeriod($period);
            $dateTo = now();

            $sales = [
                'summary' => [
                    'total_revenue' => Order::where('status', 'delivered')
                                           ->whereBetween('created_at', [$dateFrom, $dateTo])
                                           ->sum('total_amount'),
                    'total_orders' => Order::whereBetween('created_at', [$dateFrom, $dateTo])->count(),
                    'average_order_value' => Order::whereBetween('created_at', [$dateFrom, $dateTo])
                                                  ->avg('total_amount'),
                    'completion_rate' => $this->getCompletionRate($dateFrom, $dateTo),
                ],
                'by_day' => $this->getDailySales($dateFrom, $dateTo),
                'by_category' => $this->getSalesByCategory($dateFrom, $dateTo),
                'by_merchant' => $this->getSalesByMerchant($dateFrom, $dateTo),
                'by_payment_method' => $this->getSalesByPaymentMethod($dateFrom, $dateTo),
                'hourly_distribution' => $this->getHourlySales($dateFrom, $dateTo),
            ];

            return $this->successResponse(['sales' => $sales]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get customer analytics
     */
    public function customers(Request $request): JsonResponse
    {
        try {
            $customers = [
                'overview' => [
                    'total_customers' => User::where('role', 'user')->count(),
                    'active_customers' => User::where('role', 'user')->where('is_active', true)->count(),
                    'new_customers_this_month' => User::where('role', 'user')
                                                    ->whereMonth('created_at', now()->month)
                                                    ->count(),
                    'customers_with_orders' => User::where('role', 'user')->has('orders')->count(),
                ],
                'by_category' => UserCategory::withCount('users')->get(),
                'acquisition' => $this->getCustomerAcquisition(),
                'retention' => $this->getCustomerRetention(),
                'top_spenders' => $this->getTopSpenders(),
                'lifecycle' => $this->getCustomerLifecycle(),
            ];

            return $this->successResponse(['customers' => $customers]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get product analytics
     */
    public function products(Request $request): JsonResponse
    {
        try {
            $products = [
                'overview' => [
                    'total_products' => Product::count(),
                    'active_products' => Product::where('is_available', true)->count(),
                    'out_of_stock' => Product::where('stock_quantity', '<=', 0)->count(),
                    'low_stock' => Product::where('stock_quantity', '<=', 10)->where('stock_quantity', '>', 0)->count(),
                ],
                'best_sellers' => $this->getBestSellingProducts(),
                'worst_performers' => $this->getWorstPerformingProducts(),
                'by_category' => $this->getProductsByCategory(),
                'inventory_value' => $this->getInventoryValue(),
                'price_analysis' => $this->getPriceAnalysis(),
            ];

            return $this->successResponse(['products' => $products]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get financial reports
     */
    public function financial(Request $request): JsonResponse
    {
        try {
            $period = $request->get('period', 'month');
            $dateFrom = $this->getDateFromPeriod($period);
            $dateTo = now();

            $financial = [
                'revenue_breakdown' => $this->getRevenueBreakdown($dateFrom, $dateTo),
                'profit_analysis' => $this->getProfitAnalysis($dateFrom, $dateTo),
                'expense_analysis' => $this->getExpenseAnalysis($dateFrom, $dateTo),
                'tax_summary' => $this->getTaxSummary($dateFrom, $dateTo),
                'discount_impact' => $this->getDiscountImpact($dateFrom, $dateTo),
                'payment_methods' => $this->getPaymentMethodStats($dateFrom, $dateTo),
            ];

            return $this->successResponse(['financial' => $financial]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    // Private helper methods
    private function getOverviewStats($dateFrom, $dateTo)
    {
        return [
            'total_revenue' => Order::where('status', 'delivered')
                                   ->whereBetween('created_at', [$dateFrom, $dateTo])
                                   ->sum('total_amount'),
            'total_orders' => Order::whereBetween('created_at', [$dateFrom, $dateTo])->count(),
            'total_customers' => User::where('role', 'user')->count(),
            'total_products' => Product::count(),
            'active_merchants' => Merchant::where('is_active', true)->count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
        ];
    }

    private function getRevenueStats($dateFrom, $dateTo)
    {
        $totalRevenue = Order::where('status', 'delivered')
                            ->whereBetween('created_at', [$dateFrom, $dateTo])
                            ->sum('total_amount');
        
        $previousPeriod = now()->subDays($dateTo->diffInDays($dateFrom));
        $previousRevenue = Order::where('status', 'delivered')
                               ->whereBetween('created_at', [$previousPeriod, $dateFrom])
                               ->sum('total_amount');

        return [
            'current_period' => $totalRevenue,
            'previous_period' => $previousRevenue,
            'growth_rate' => $previousRevenue > 0 ? (($totalRevenue - $previousRevenue) / $previousRevenue) * 100 : 0,
            'average_daily' => $totalRevenue / max(1, $dateTo->diffInDays($dateFrom)),
        ];
    }

    private function getOrderStats($dateFrom, $dateTo)
    {
        return Order::selectRaw('status, COUNT(*) as count')
                   ->whereBetween('created_at', [$dateFrom, $dateTo])
                   ->groupBy('status')
                   ->get()
                   ->mapWithKeys(function ($item) {
                       return [$item->status => $item->count];
                   });
    }

    private function getCustomerStats($dateFrom, $dateTo)
    {
        return [
            'new_customers' => User::where('role', 'user')
                                  ->whereBetween('created_at', [$dateFrom, $dateTo])
                                  ->count(),
            'returning_customers' => Order::whereBetween('created_at', [$dateFrom, $dateTo])
                                         ->whereHas('user', function ($q) use ($dateFrom) {
                                             $q->where('created_at', '<', $dateFrom);
                                         })
                                         ->distinct('user_id')
                                         ->count(),
        ];
    }

    private function getProductStats($dateFrom, $dateTo)
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('product_categories')) {
            return collect([]);
        }

        return DB::table('order_items')
                 ->join('orders', 'order_items.order_id', '=', 'orders.id')
                 ->join('products', 'order_items.product_id', '=', 'products.id')
                 ->leftJoin('product_categories', 'products.category_id', '=', 'product_categories.id')
                 ->whereBetween('orders.created_at', [$dateFrom, $dateTo])
                 ->where('orders.status', 'delivered')
                 ->selectRaw('product_categories.display_name as category, SUM(order_items.quantity) as total_sold, SUM(order_items.total_price) as revenue')
                 ->groupBy('product_categories.display_name')
                 ->get();
    }

    private function getMerchantStats($dateFrom, $dateTo)
    {
        return DB::table('orders')
                 ->join('merchants', 'orders.merchant_id', '=', 'merchants.id')
                 ->whereBetween('orders.created_at', [$dateFrom, $dateTo])
                 ->selectRaw('merchants.name, COUNT(orders.id) as order_count, SUM(orders.total_amount) as revenue')
                 ->groupBy('merchants.id', 'merchants.name')
                 ->orderBy('revenue', 'desc')
                 ->get();
    }

    private function getTrends($dateFrom, $dateTo)
    {
        return Order::selectRaw('DATE(created_at) as date, COUNT(*) as orders, SUM(total_amount) as revenue')
                   ->whereBetween('created_at', [$dateFrom, $dateTo])
                   ->groupBy('date')
                   ->orderBy('date')
                   ->get();
    }

    private function getDateFromPeriod($period)
    {
        return match($period) {
            'day' => now()->startOfDay(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'year' => now()->startOfYear(),
            default => now()->startOfMonth(),
        };
    }

    private function getCompletionRate($dateFrom, $dateTo)
    {
        $totalOrders = Order::whereBetween('created_at', [$dateFrom, $dateTo])->count();
        $deliveredOrders = Order::where('status', 'delivered')
                               ->whereBetween('created_at', [$dateFrom, $dateTo])
                               ->count();
        
        return $totalOrders > 0 ? ($deliveredOrders / $totalOrders) * 100 : 0;
    }

    private function getDailySales($dateFrom, $dateTo)
    {
        return Order::selectRaw('DATE(created_at) as date, COUNT(*) as orders, SUM(total_amount) as revenue')
                   ->where('status', 'delivered')
                   ->whereBetween('created_at', [$dateFrom, $dateTo])
                   ->groupBy('date')
                   ->orderBy('date')
                   ->get();
    }

    private function getBestSellingProducts()
    {
        return DB::table('order_items')
                 ->join('products', 'order_items.product_id', '=', 'products.id')
                 ->join('orders', 'order_items.order_id', '=', 'orders.id')
                 ->where('orders.status', 'delivered')
                 ->selectRaw('products.id, products.name, SUM(order_items.quantity) as total_sold, SUM(order_items.total_price) as revenue')
                 ->groupBy('products.id', 'products.name')
                 ->orderBy('total_sold', 'desc')
                 ->take(10)
                 ->get();
    }

    // Add more helper methods as needed...
}
