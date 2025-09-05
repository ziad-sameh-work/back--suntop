<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Modules\Offers\Models\Offer;
use App\Modules\Products\Models\Product;
use App\Modules\Orders\Models\Order;
use App\Modules\Users\Models\UserCategory;

class AdminOfferController extends Controller
{
    /**
     * Display a listing of offers
     */
    public function index(Request $request)
    {
        $query = Offer::with('userCategory');

        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('type', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Status filter
        if ($request->filled('status') && $request->status !== 'all') {
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

        // User category filter
        if ($request->filled('user_category_id') && $request->user_category_id !== 'all') {
            $query->where('user_category_id', $request->user_category_id);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->where('valid_from', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('valid_until', '<=', $request->date_to);
        }

        $offers = $query->orderBy('created_at', 'desc')
                       ->paginate($request->get('per_page', 20));

        // Get user categories for filter dropdown
        $userCategories = UserCategory::where('is_active', true)
                                    ->orderBy('sort_order')
                                    ->get();

        // Get statistics
        $stats = $this->getOffersStats($request);

        if ($request->expectsJson()) {
            return response()->json([
                'offers' => $offers,
                'stats' => $stats
            ]);
        }

        return view('admin.offers.index', compact('offers', 'stats', 'userCategories'));
    }

    /**
     * Show the form for creating a new offer
     */
    public function create()
    {
        // Get categories from product_categories table instead
        $categories = \App\Modules\Products\Models\ProductCategory::where('is_active', true)
                                                                 ->orderBy('sort_order')
                                                                 ->pluck('name')
                                                                 ->sort();
        
        $products = Product::select('id', 'name', 'category_id')
                          ->with('category:id,name')
                          ->where('is_available', true)
                          ->get();
        
        $userCategories = UserCategory::where('is_active', true)->orderBy('sort_order')->get();
        
        return view('admin.offers.create', compact('categories', 'products', 'userCategories'));
    }

    /**
     * Store a newly created offer
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'nullable|string|max:255',
            'user_category_id' => 'nullable|exists:user_categories,id',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'minimum_amount' => 'nullable|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after:valid_from',
            'is_active' => 'boolean',
            'first_order_only' => 'boolean',
            'applicable_categories' => 'nullable|array',
            'applicable_products' => 'nullable|array',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('offers', 'public');
            $validated['image_url'] = $path;
        }

        $offer = Offer::create($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء العرض بنجاح',
                'offer' => $offer
            ]);
        }

        return redirect()->route('admin.offers.index')
                        ->with('success', 'تم إنشاء العرض بنجاح');
    }

    /**
     * Display the specified offer
     */
    public function show(Offer $offer)
    {
        $offer->load([]);
        
        // Get offer statistics
        $offerStats = $this->getOfferStatistics($offer);
        
        // Get recent usage
        $recentUsage = Order::where('discount', '>', 0)
                           ->with(['user'])
                           ->orderBy('created_at', 'desc')
                           ->limit(10)
                           ->get();

        return view('admin.offers.show', compact('offer', 'offerStats', 'recentUsage'));
    }

    /**
     * Show the form for editing the specified offer
     */
    public function edit(Offer $offer)
    {
        // Get categories from product_categories table instead
        $categories = \App\Modules\Products\Models\ProductCategory::where('is_active', true)
                                                                 ->orderBy('sort_order')
                                                                 ->pluck('name')
                                                                 ->sort();
        
        $products = Product::select('id', 'name', 'category_id')
                          ->with('category:id,name')
                          ->where('is_available', true)
                          ->get();
        
        $userCategories = UserCategory::where('is_active', true)->orderBy('sort_order')->get();
        
        return view('admin.offers.edit', compact('offer', 'categories', 'products', 'userCategories'));
    }

    /**
     * Update the specified offer
     */
    public function update(Request $request, Offer $offer)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'nullable|string|max:255',
            'user_category_id' => 'nullable|exists:user_categories,id',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'minimum_amount' => 'nullable|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after:valid_from',
            'is_active' => 'boolean',
            'first_order_only' => 'boolean',
            'applicable_categories' => 'nullable|array',
            'applicable_products' => 'nullable|array',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($offer->image_url) {
                Storage::disk('public')->delete($offer->image_url);
            }
            
            $path = $request->file('image')->store('offers', 'public');
            $validated['image_url'] = $path;
        }

        $offer->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم تحديث العرض بنجاح',
                'offer' => $offer
            ]);
        }

        return redirect()->route('admin.offers.index')
                        ->with('success', 'تم تحديث العرض بنجاح');
    }

    /**
     * Remove the specified offer
     */
    public function destroy(Offer $offer)
    {
        // Delete image if exists
        if ($offer->image_url) {
            Storage::disk('public')->delete($offer->image_url);
        }
        
        $offer->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف العرض بنجاح'
        ]);
    }

    /**
     * Toggle offer status
     */
    public function toggleStatus(Offer $offer)
    {
        $offer->update(['is_active' => !$offer->is_active]);

        return response()->json([
            'success' => true,
            'message' => $offer->is_active ? 'تم تفعيل العرض' : 'تم إلغاء تفعيل العرض',
            'is_active' => $offer->is_active
        ]);
    }

    /**
     * Bulk actions on offers
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'offer_ids' => 'required|array',
            'offer_ids.*' => 'exists:offers,id'
        ]);

        $offers = Offer::whereIn('id', $request->offer_ids);
        $count = $offers->count();

        switch ($request->action) {
            case 'activate':
                $offers->update(['is_active' => true]);
                $message = "تم تفعيل {$count} عرض";
                break;
            case 'deactivate':
                $offers->update(['is_active' => false]);
                $message = "تم إلغاء تفعيل {$count} عرض";
                break;
            case 'delete':
                $offers->delete();
                $message = "تم حذف {$count} عرض";
                break;
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    /**
     * Get offers statistics
     */
    private function getOffersStats(Request $request = null)
    {
        $query = Offer::query();
        
        // Apply same filters as index
        if ($request) {
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('title', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('description', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('type', 'LIKE', "%{$searchTerm}%");
                });
            }
        }

        return [
            'total_offers' => $query->count(),
            'active_offers' => (clone $query)->where('is_active', true)->count(),
            'inactive_offers' => (clone $query)->where('is_active', false)->count(),
            'expired_offers' => (clone $query)->where('valid_until', '<', now())->count(),
            'upcoming_offers' => (clone $query)->where('valid_from', '>', now())->count(),
            'category_offers' => (clone $query)->whereNotNull('user_category_id')->count(),
            'general_offers' => (clone $query)->whereNull('user_category_id')->count(),
            'total_usage' => 0, // No longer tracking individual usage
            'avg_discount_percentage' => (clone $query)->whereNotNull('discount_percentage')->avg('discount_percentage'),
        ];
    }

    /**
     * Get offer statistics for individual offer
     */
    private function getOfferStatistics($offer)
    {
        // Calculate total savings from orders that used discounts during this offer period
        $totalSavings = Order::where('discount', '>', 0)
                            ->whereBetween('created_at', [$offer->valid_from, $offer->valid_until])
                            ->sum('discount');

        // Count orders with discounts during offer period as usage approximation
        $totalUsage = Order::where('discount', '>', 0)
                          ->whereBetween('created_at', [$offer->valid_from, $offer->valid_until])
                          ->count();

        return [
            'days_remaining' => now()->diffInDays($offer->valid_until, false),
            'is_expired' => $offer->valid_until < now(),
            'is_upcoming' => $offer->valid_from > now(),
            'user_category' => $offer->userCategory ? $offer->userCategory->display_name : 'جميع الفئات',
            'created_at' => $offer->created_at,
            'offer_type' => $offer->type ?? 'غير محدد',
            'total_usage' => $totalUsage,
            'total_savings' => $totalSavings,
            'remaining_usage' => null, // No longer applicable since no usage limits
        ];
    }

    /**
     * Export offers (placeholder)
     */
    public function export(Request $request)
    {
        return response()->json([
            'success' => false,
            'message' => 'ميزة تصدير العروض قيد التطوير'
        ]);
    }

}
