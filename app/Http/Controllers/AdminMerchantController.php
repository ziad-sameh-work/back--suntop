<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use App\Modules\Merchants\Models\Merchant;
use App\Modules\Products\Models\Product;
use App\Modules\Orders\Models\Order;
use App\Modules\Users\Models\User;

class AdminMerchantController extends Controller
{
    /**
     * Display merchants list
     */
    public function index(Request $request)
    {
        $query = Merchant::query();
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('business_name', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('is_active', $request->status === 'active');
        }

        // Open status filter
        if ($request->filled('open_status') && $request->open_status !== 'all') {
            $query->where('is_open', $request->open_status === 'open');
        }

        // City filter
        if ($request->filled('city') && $request->city !== 'all') {
            $query->where('city', $request->city);
        }

        // Commission range filter
        if ($request->filled('commission_from')) {
            $query->where('commission_rate', '>=', $request->commission_from);
        }
        if ($request->filled('commission_to')) {
            $query->where('commission_rate', '<=', $request->commission_to);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        // Pagination
        $perPage = $request->get('per_page', 20);
        $merchants = $query->paginate($perPage)->withQueryString();

        // Statistics
        $stats = $this->getMerchantsStats($request);

        // Get cities for filter
        $cities = Merchant::distinct()->pluck('city')->filter()->sort()->values();

        return view('admin.merchants.index', compact('merchants', 'stats', 'cities'));
    }

    /**
     * Show merchant details
     */
    public function show($id)
    {
        $merchant = Merchant::findOrFail($id);

        // Get merchant statistics
        $merchantStats = $this->getMerchantStatistics($merchant);
        
        // Get merchant products
        $products = Product::where('merchant_id', $merchant->id)
                          ->withCount(['orderItems'])
                          ->orderBy('created_at', 'desc')
                          ->limit(10)
                          ->get();

        // Get recent orders
        $recentOrders = Order::where('merchant_id', $merchant->id)
                          ->with(['user', 'items.product'])
                          ->orderBy('created_at', 'desc')
                          ->limit(10)
                          ->get();

        return view('admin.merchants.show', compact('merchant', 'merchantStats', 'products', 'recentOrders'));
    }

    /**
     * Show create merchant form
     */
    public function create()
    {
        return view('admin.merchants.create');
    }

    /**
     * Store new merchant
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:merchants,email',
            'phone' => 'required|string|max:20',
            'business_name' => 'required|string|max:255',
            'business_type' => 'nullable|string|max:100',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'description' => 'nullable|string',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
            'is_open' => 'boolean',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            DB::beginTransaction();

            $merchantData = $request->except(['logo']);
            $merchantData['is_active'] = $request->has('is_active');
            $merchantData['is_open'] = $request->has('is_open');

            // Handle logo upload
            if ($request->hasFile('logo')) {
                $logoName = time() . '_' . $request->file('logo')->getClientOriginalName();
                $request->file('logo')->move(public_path('uploads/merchants'), $logoName);
                $merchantData['logo'] = 'uploads/merchants/' . $logoName;
            }

            $merchant = Merchant::create($merchantData);

            DB::commit();

            return redirect()->route('admin.merchants.show', $merchant->id)
                           ->with('success', 'تم إضافة التاجر بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء إضافة التاجر: ' . $e->getMessage());
        }
    }

    /**
     * Show edit merchant form
     */
    public function edit($id)
    {
        $merchant = Merchant::findOrFail($id);
        return view('admin.merchants.edit', compact('merchant'));
    }

    /**
     * Update merchant
     */
    public function update(Request $request, $id)
    {
        $merchant = Merchant::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:merchants,email,' . $merchant->id,
            'phone' => 'required|string|max:20',
            'business_name' => 'required|string|max:255',
            'business_type' => 'nullable|string|max:100',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'description' => 'nullable|string',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
            'is_open' => 'boolean',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            DB::beginTransaction();

            $merchantData = $request->except(['logo']);
            $merchantData['is_active'] = $request->has('is_active');
            $merchantData['is_open'] = $request->has('is_open');

            // Handle logo upload
            if ($request->hasFile('logo')) {
                // Delete old logo
                if ($merchant->logo && file_exists(public_path($merchant->logo))) {
                    unlink(public_path($merchant->logo));
                }

                $logoName = time() . '_' . $request->file('logo')->getClientOriginalName();
                $request->file('logo')->move(public_path('uploads/merchants'), $logoName);
                $merchantData['logo'] = 'uploads/merchants/' . $logoName;
            }

            $merchant->update($merchantData);

            DB::commit();

            return redirect()->route('admin.merchants.show', $merchant->id)
                           ->with('success', 'تم تحديث بيانات التاجر بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث التاجر: ' . $e->getMessage());
        }
    }

    /**
     * Toggle merchant status
     */
    public function toggleStatus(Request $request, $id)
    {
        try {
            $merchant = Merchant::findOrFail($id);
            $merchant->update(['is_active' => !$merchant->is_active]);

            return response()->json([
                'success' => true,
                'message' => $merchant->is_active ? 'تم تفعيل التاجر بنجاح' : 'تم إلغاء تفعيل التاجر بنجاح',
                'new_status' => $merchant->is_active
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث حالة التاجر'
            ], 500);
        }
    }

    /**
     * Toggle merchant open status
     */
    public function toggleOpenStatus(Request $request, $id)
    {
        try {
            $merchant = Merchant::findOrFail($id);
            $merchant->update(['is_open' => !$merchant->is_open]);

            return response()->json([
                'success' => true,
                'message' => $merchant->is_open ? 'تم فتح المتجر بنجاح' : 'تم إغلاق المتجر بنجاح',
                'new_status' => $merchant->is_open
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث حالة المتجر'
            ], 500);
        }
    }

    /**
     * Delete merchant
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $merchant = Merchant::findOrFail($id);

            // Check if merchant has products
            $productsCount = Product::where('merchant_id', $merchant->id)->count();
            if ($productsCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "لا يمكن حذف التاجر لأنه يملك {$productsCount} منتج. يجب حذف المنتجات أولاً."
                ], 422);
            }

            // Delete logo file
            if ($merchant->logo && file_exists(public_path($merchant->logo))) {
                unlink(public_path($merchant->logo));
            }

            $merchant->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف التاجر بنجاح'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف التاجر'
            ], 500);
        }
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,open,close,delete',
            'merchant_ids' => 'required|array',
            'merchant_ids.*' => 'exists:merchants,id'
        ]);

        try {
            DB::beginTransaction();

            $merchants = Merchant::whereIn('id', $request->merchant_ids)->get();
            $processedCount = 0;

            foreach ($merchants as $merchant) {
                switch ($request->action) {
                    case 'activate':
                        $merchant->update(['is_active' => true]);
                        $processedCount++;
                        break;

                    case 'deactivate':
                        $merchant->update(['is_active' => false]);
                        $processedCount++;
                        break;

                    case 'open':
                        $merchant->update(['is_open' => true]);
                        $processedCount++;
                        break;

                    case 'close':
                        $merchant->update(['is_open' => false]);
                        $processedCount++;
                        break;

                    case 'delete':
                        $productsCount = Product::where('merchant_id', $merchant->id)->count();
                        if ($productsCount === 0) {
                            if ($merchant->logo && file_exists(public_path($merchant->logo))) {
                                unlink(public_path($merchant->logo));
                            }
                            $merchant->delete();
                            $processedCount++;
                        }
                        break;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "تم تنفيذ العملية على {$processedCount} تاجر بنجاح"
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تنفيذ العملية الجماعية'
            ], 500);
        }
    }

    /**
     * Get merchants statistics
     */
    private function getMerchantsStats($request = null)
    {
        $query = Merchant::query();
        
        // Apply same filters as main query for consistent stats
        if ($request) {
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('business_name', 'like', "%{$search}%");
                });
            }

            if ($request->filled('city') && $request->city !== 'all') {
                $query->where('city', $request->city);
            }
        }

        return [
            'total_merchants' => $query->count(),
            'active_merchants' => (clone $query)->where('is_active', true)->count(),
            'inactive_merchants' => (clone $query)->where('is_active', false)->count(),
            'open_merchants' => (clone $query)->where('is_open', true)->count(),
            'closed_merchants' => (clone $query)->where('is_open', false)->count(),
            'total_products' => Product::whereIn('merchant_id', (clone $query)->pluck('id'))->count(),
            'total_orders' => Order::whereIn('merchant_id', (clone $query)->pluck('id'))->count(),
            'total_revenue' => Order::whereIn('merchant_id', (clone $query)->pluck('id'))
                                   ->where('payment_status', 'paid')->sum('total_amount'),
            'avg_commission' => (clone $query)->avg('commission_rate'),
            'new_this_month' => (clone $query)->whereMonth('created_at', now()->month)->count(),
        ];
    }

    /**
     * Get merchant statistics for individual merchant
     */
    private function getMerchantStatistics($merchant)
    {
        return [
            'total_products' => Product::where('merchant_id', $merchant->id)->count(),
            'active_products' => Product::where('merchant_id', $merchant->id)->where('is_available', true)->count(),
            'total_orders' => Order::where('merchant_id', $merchant->id)->count(),
            'completed_orders' => Order::where('merchant_id', $merchant->id)->where('status', 'delivered')->count(),
            'total_revenue' => Order::where('merchant_id', $merchant->id)->where('payment_status', 'paid')->sum('total_amount'),
            'this_month_revenue' => Order::where('merchant_id', $merchant->id)
                                         ->where('payment_status', 'paid')
                                         ->whereMonth('created_at', now()->month)
                                         ->sum('total_amount'),
            'avg_order_value' => Order::where('merchant_id', $merchant->id)->where('payment_status', 'paid')->avg('total_amount'),
            'commission_earned' => Order::where('merchant_id', $merchant->id)->where('payment_status', 'paid')->sum('total_amount') * ($merchant->commission_rate / 100),
            'join_date' => $merchant->created_at,
            'last_order_date' => Order::where('merchant_id', $merchant->id)->latest()->first()?->created_at,
        ];
    }

    /**
     * Export merchants (placeholder)
     */
    public function export(Request $request)
    {
        return response()->json([
            'success' => false,
            'message' => 'ميزة تصدير التجار قيد التطوير'
        ]);
    }

    /**
     * Merchant analytics
     */
    public function analytics($id)
    {
        $merchant = Merchant::findOrFail($id);
        
        // Get merchant statistics
        $merchantStats = $this->getMerchantStatistics($merchant);
        
        // Get top products for this merchant
        $topProducts = Product::where('merchant_id', $merchant->id)
            ->withCount(['orderItems'])
            ->orderBy('order_items_count', 'desc')
            ->limit(5)
            ->get();
        
        return view('admin.merchants.analytics', compact('merchant', 'merchantStats', 'topProducts'));
    }
}
