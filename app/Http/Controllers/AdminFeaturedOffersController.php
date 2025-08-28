<?php

namespace App\Http\Controllers;

use App\Modules\Offers\Models\Offer;
use App\Modules\Products\Models\Product;
use App\Modules\Products\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminFeaturedOffersController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Display featured offers management page
     */
    public function index()
    {
        $featuredOffers = Offer::featured()
                              ->orderBy('display_order', 'asc')
                              ->orderBy('created_at', 'desc')
                              ->paginate(10);

        $stats = [
            'total_featured' => Offer::featured()->count(),
            'active_featured' => Offer::activeFeatured()->count(),
            'total_offers' => Offer::count(),
        ];

        return view('admin.featured-offers.index', compact('featuredOffers', 'stats'));
    }

    /**
     * Show form to create featured offer
     */
    public function create()
    {
        $offers = Offer::where('is_active', true)->get();
        $products = Product::where('is_available', true)->get();
        $categories = ProductCategory::all();
        $offerTags = Offer::getOfferTags();

        return view('admin.featured-offers.create', compact('offers', 'products', 'categories', 'offerTags'));
    }

    /**
     * Store new featured offer
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:255',
            'code' => 'required|string|unique:offers,code',
            'type' => 'required|in:discount,bogo,freebie,cashback',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'minimum_amount' => 'nullable|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'min_purchase_amount' => 'nullable|numeric|min:0',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after:valid_from',
            'usage_limit' => 'nullable|integer|min:1',
            'background_color' => 'required|string',
            'text_color' => 'required|string',
            'display_order' => 'nullable|integer|min:0',
            'offer_tag' => 'nullable|string',
            'applicable_categories' => 'nullable|array',
            'applicable_products' => 'nullable|array',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'first_order_only' => 'boolean',
        ]);

        $data = $request->except(['image', 'applicable_categories', 'applicable_products']);
        
        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('offers', 'public');
            $data['image_url'] = $imagePath;
        }

        // Handle arrays
        $data['applicable_categories'] = $request->applicable_categories ?: [];
        $data['applicable_products'] = $request->applicable_products ?: [];

        // Set defaults
        $data['used_count'] = 0;
        $data['trend_score'] = 0;
        $data['is_featured'] = $request->has('is_featured');
        $data['is_active'] = $request->has('is_active');
        $data['first_order_only'] = $request->has('first_order_only');

        $offer = Offer::create($data);

        return redirect()->route('admin.featured-offers.index')
                        ->with('success', 'تم إنشاء العرض المميز بنجاح');
    }

    /**
     * Show form to edit featured offer
     */
    public function edit(Offer $offer)
    {
        $products = Product::where('is_available', true)->get();
        $categories = ProductCategory::all();
        $offerTags = Offer::getOfferTags();

        return view('admin.featured-offers.edit', compact('offer', 'products', 'categories', 'offerTags'));
    }

    /**
     * Update featured offer
     */
    public function update(Request $request, Offer $offer)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:255',
            'code' => 'required|string|unique:offers,code,' . $offer->id,
            'type' => 'required|in:discount,bogo,freebie,cashback',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'minimum_amount' => 'nullable|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'min_purchase_amount' => 'nullable|numeric|min:0',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after:valid_from',
            'usage_limit' => 'nullable|integer|min:1',
            'background_color' => 'required|string',
            'text_color' => 'required|string',
            'display_order' => 'nullable|integer|min:0',
            'offer_tag' => 'nullable|string',
            'applicable_categories' => 'nullable|array',
            'applicable_products' => 'nullable|array',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'first_order_only' => 'boolean',
        ]);

        $data = $request->except(['image', 'applicable_categories', 'applicable_products']);
        
        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($offer->image_url) {
                Storage::disk('public')->delete($offer->image_url);
            }
            
            $imagePath = $request->file('image')->store('offers', 'public');
            $data['image_url'] = $imagePath;
        }

        // Handle arrays
        $data['applicable_categories'] = $request->applicable_categories ?: [];
        $data['applicable_products'] = $request->applicable_products ?: [];

        // Handle checkboxes
        $data['is_featured'] = $request->has('is_featured');
        $data['is_active'] = $request->has('is_active');
        $data['first_order_only'] = $request->has('first_order_only');

        $offer->update($data);

        return redirect()->route('admin.featured-offers.index')
                        ->with('success', 'تم تحديث العرض المميز بنجاح');
    }

    /**
     * Delete featured offer
     */
    public function destroy(Offer $offer)
    {
        // Delete image if exists
        if ($offer->image_url) {
            Storage::disk('public')->delete($offer->image_url);
        }

        $offer->delete();

        return redirect()->route('admin.featured-offers.index')
                        ->with('success', 'تم حذف العرض المميز بنجاح');
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(Offer $offer)
    {
        $offer->is_featured = !$offer->is_featured;
        $offer->save();

        $status = $offer->is_featured ? 'مميز' : 'غير مميز';
        
        return response()->json([
            'success' => true,
            'message' => "تم تغيير حالة العرض إلى {$status}",
            'is_featured' => $offer->is_featured,
        ]);
    }

    /**
     * Update display order
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'offers' => 'required|array',
            'offers.*.id' => 'required|exists:offers,id',
            'offers.*.order' => 'required|integer|min:0',
        ]);

        foreach ($request->offers as $offerData) {
            Offer::where('id', $offerData['id'])
                 ->update(['display_order' => $offerData['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث ترتيب العروض بنجاح',
        ]);
    }

    /**
     * Update trend scores for all offers
     */
    public function updateTrendScores()
    {
        $offers = Offer::where('is_active', true)->get();
        
        foreach ($offers as $offer) {
            $offer->updateTrendScore();
        }

        return redirect()->route('admin.featured-offers.index')
                        ->with('success', 'تم تحديث نقاط الرواج لجميع العروض');
    }

    /**
     * Get offer stats for dashboard
     */
    public function getStats()
    {
        $stats = [
            'total_offers' => Offer::count(),
            'active_offers' => Offer::where('is_active', true)->count(),
            'featured_offers' => Offer::featured()->count(),
            'trending_offers' => Offer::trending()->count(),
            'total_redemptions' => \App\Modules\Offers\Models\OfferRedemption::count(),
            'today_redemptions' => \App\Modules\Offers\Models\OfferRedemption::whereDate('created_at', today())->count(),
        ];

        return response()->json(['success' => true, 'data' => $stats]);
    }
}

