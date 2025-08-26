<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Modules\Offers\Models\Offer;
use App\Modules\Products\Models\Product;
use App\Modules\Orders\Models\Order;

class AdminOfferController extends Controller
{
    /**
     * Display a listing of offers
     */
    public function index(Request $request)
    {
        $query = Offer::query();

        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('code', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'LIKE', "%{$searchTerm}%");
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

        // Type filter
        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
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

        // Get statistics
        $stats = $this->getOffersStats($request);

        if ($request->expectsJson()) {
            return response()->json([
                'offers' => $offers,
                'stats' => $stats
            ]);
        }

        return view('admin.offers.index', compact('offers', 'stats'));
    }

    /**
     * Show the form for creating a new offer
     */
    public function create()
    {
        $categories = Product::distinct()->pluck('category')->filter()->sort();
        $products = Product::select('id', 'name', 'category')->where('is_available', true)->get();
        
        return view('admin.offers.create', compact('categories', 'products'));
    }

    /**
     * Store a newly created offer
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:percentage,fixed_amount',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'minimum_amount' => 'nullable|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after:valid_from',
            'usage_limit' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'first_order_only' => 'boolean',
            'applicable_categories' => 'nullable|array',
            'applicable_products' => 'nullable|array',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Generate unique code
        $validated['code'] = Offer::generateCode();

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
        $categories = Product::distinct()->pluck('category')->filter()->sort();
        $products = Product::select('id', 'name', 'category')->where('is_available', true)->get();
        
        return view('admin.offers.edit', compact('offer', 'categories', 'products'));
    }

    /**
     * Update the specified offer
     */
    public function update(Request $request, Offer $offer)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:percentage,fixed_amount',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'minimum_amount' => 'nullable|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after:valid_from',
            'usage_limit' => 'nullable|integer|min:1',
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
                      ->orWhere('code', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('description', 'LIKE', "%{$searchTerm}%");
                });
            }
        }

        return [
            'total_offers' => $query->count(),
            'active_offers' => (clone $query)->where('is_active', true)->count(),
            'inactive_offers' => (clone $query)->where('is_active', false)->count(),
            'expired_offers' => (clone $query)->where('valid_until', '<', now())->count(),
            'upcoming_offers' => (clone $query)->where('valid_from', '>', now())->count(),
            'percentage_offers' => (clone $query)->where('type', 'percentage')->count(),
            'fixed_amount_offers' => (clone $query)->where('type', 'fixed_amount')->count(),
            'total_usage' => Offer::sum('used_count'),
            'avg_discount_percentage' => (clone $query)->where('type', 'percentage')->avg('discount_percentage'),
        ];
    }

    /**
     * Get offer statistics for individual offer
     */
    private function getOfferStatistics($offer)
    {
        return [
            'total_usage' => $offer->used_count,
            'remaining_usage' => $offer->usage_limit ? max(0, $offer->usage_limit - $offer->used_count) : null,
            'usage_percentage' => $offer->usage_limit ? min(100, ($offer->used_count / $offer->usage_limit) * 100) : 0,
            'days_remaining' => now()->diffInDays($offer->valid_until, false),
            'is_expired' => $offer->valid_until < now(),
            'is_upcoming' => $offer->valid_from > now(),
            'total_savings' => 0, // This would be calculated from actual order usage
            'created_at' => $offer->created_at,
            'last_used_at' => null, // This would come from order usage tracking
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

    /**
     * Offer analytics
     */
    public function analytics($id)
    {
        $offer = Offer::findOrFail($id);
        
        // Get offer statistics
        $offerStats = $this->getOfferStatistics($offer);
        
        return view('admin.offers.analytics', compact('offer', 'offerStats'));
    }
}
