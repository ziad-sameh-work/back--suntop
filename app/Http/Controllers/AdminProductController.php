<?php

namespace App\Http\Controllers;

use App\Modules\Products\Models\Product;
use App\Modules\Merchants\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Str;

class AdminProductController extends Controller
{
    /**
     * Display products list page
     */
    public function index(Request $request)
    {
        // Get filters from request
        $search = $request->get('search', '');
        $merchant_id = $request->get('merchant_id', '');
        $availability = $request->get('availability', '');
        $stock_status = $request->get('stock_status', '');
        $price_range = $request->get('price_range', '');
        $featured = $request->get('featured', '');
        $perPage = $request->get('per_page', 15);

        // Build query  
        $query = Product::query()
            ->withCount(['orderItems']);
            
        // Only load merchant relationship if it exists
        try {
            $query->with(['merchant']);
        } catch (\Exception $e) {
            // Merchant relationship might not exist yet
        }

        // Apply filters
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                  ->orWhere('description', 'LIKE', '%' . $search . '%');
                // Only search SKU if column exists
                if (Schema::hasColumn('products', 'sku')) {
                    $q->orWhere('sku', 'LIKE', '%' . $search . '%');
                }
            });
        }

        if ($merchant_id && Schema::hasColumn('products', 'merchant_id')) {
            $query->where('merchant_id', $merchant_id);
        }

        if ($availability !== '') {
            $query->where('is_available', $availability == 'available');
        }

        if ($featured !== '') {
            $query->where('is_featured', $featured == 'featured');
        }

        if ($stock_status) {
            switch ($stock_status) {
                case 'in_stock':
                    $query->where('stock_quantity', '>', 10);
                    break;
                case 'low_stock':
                    $query->whereBetween('stock_quantity', [1, 10]);
                    break;
                case 'out_of_stock':
                    $query->where('stock_quantity', 0);
                    break;
            }
        }

        if ($price_range && Schema::hasColumn('products', 'price')) {
            // Use discount_price if available, otherwise use price
            $priceColumn = Schema::hasColumn('products', 'discount_price') ? 
                'COALESCE(discount_price, price)' : 'price';
                
            switch ($price_range) {
                case 'under_100':
                    $query->whereRaw("$priceColumn < 100");
                    break;
                case '100_500':
                    $query->whereRaw("$priceColumn BETWEEN 100 AND 500");
                    break;
                case '500_1000':
                    $query->whereRaw("$priceColumn BETWEEN 500 AND 1000");
                    break;
                case 'over_1000':
                    $query->whereRaw("$priceColumn > 1000");
                    break;
            }
        }

        // Get paginated results
        $products = $query->orderBy('created_at', 'desc')->paginate($perPage);

        // Get statistics
        $stats = $this->getProductsStats();

        // Get filter options
        $merchants = [];
        try {
            if (Schema::hasTable('merchants')) {
                $merchants = Merchant::where('is_active', true)->get();
            }
        } catch (\Exception $e) {
            // Merchants table might not exist yet
        }

        return view('admin.products.index', compact(
            'products',
            'stats',
            'merchants',
            'search',
            'merchant_id',
            'availability',
            'stock_status',
            'price_range',
            'featured',
            'perPage'
        ));
    }

    /**
     * Show product details
     */
    public function show($id)
    {
        $product = Product::findOrFail($id);
        
        // Load merchant relationship if it exists
        try {
            if (Schema::hasColumn('products', 'merchant_id')) {
                $product->load('merchant');
            }
        } catch (\Exception $e) {
            // Merchant relationship might not exist yet
        }
        
        // Get product statistics
        $productStats = $this->getProductDetailsStats($product);

        return view('admin.products.show', compact('product', 'productStats'));
    }

    /**
     * Show create product form
     */
    public function create()
    {
        $merchants = [];
        $categories = [];
        
        try {
            if (Schema::hasTable('merchants')) {
                $merchants = Merchant::where('is_active', true)->get();
            }
        } catch (\Exception $e) {
            // Merchants table might not exist yet
        }
        
        try {
            if (Schema::hasTable('product_categories')) {
                $categories = \App\Modules\Products\Models\ProductCategory::where('is_active', true)
                    ->orderBy('sort_order')
                    ->get();
            }
        } catch (\Exception $e) {
            // Product Categories table might not exist yet
        }
        
        return view('admin.products.create', compact('merchants', 'categories'));
    }

    /**
     * Store new product
     */
    public function store(Request $request)
    {
        // Base validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'is_available' => 'boolean',
            'is_featured' => 'boolean',
            'back_color' => 'nullable|string|max:20',
        ];
        
        // Add conditional validation rules
        if (Schema::hasColumn('products', 'short_description')) {
            $rules['short_description'] = 'nullable|string|max:500';
        }
        if (Schema::hasColumn('products', 'sku')) {
            $rules['sku'] = 'required|string|max:100|unique:products';
        }
        if (Schema::hasColumn('products', 'discount_price')) {
            $rules['discount_price'] = 'nullable|numeric|min:0|lt:price';
        }
        if (Schema::hasColumn('products', 'min_quantity')) {
            $rules['min_quantity'] = 'nullable|integer|min:1';
        }
        if (Schema::hasColumn('products', 'weight')) {
            $rules['weight'] = 'nullable|numeric|min:0';
        }
        if (Schema::hasColumn('products', 'dimensions')) {
            $rules['dimensions'] = 'nullable|string|max:100';
        }
        if (Schema::hasColumn('products', 'merchant_id') && Schema::hasTable('merchants')) {
            $rules['merchant_id'] = 'required|exists:merchants,id';
        }
        if (Schema::hasColumn('products', 'category_id') && Schema::hasTable('product_categories')) {
            $rules['category_id'] = 'nullable|exists:product_categories,id';
        }
        if (Schema::hasColumn('products', 'images')) {
            $rules['images'] = 'nullable|array|max:5';
            $rules['images.*'] = 'image|mimes:jpeg,png,jpg,gif|max:2048';
        }
        if (Schema::hasColumn('products', 'meta_title')) {
            $rules['meta_title'] = 'nullable|string|max:255';
        }
        if (Schema::hasColumn('products', 'meta_description')) {
            $rules['meta_description'] = 'nullable|string|max:500';
        }
        
        $validated = $request->validate($rules);

        try {
            DB::beginTransaction();

            // Generate slug
            $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(6);
            $validated['is_available'] = $request->has('is_available');
            $validated['is_featured'] = $request->has('is_featured');

            // Handle images upload
            $images = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $imageName = time() . '_' . $index . '_' . $image->getClientOriginalName();
                    $image->move(public_path('uploads/products'), $imageName);
                    $images[] = 'uploads/products/' . $imageName;
                }
            }
            $validated['images'] = $images;

            $product = Product::create($validated);

            DB::commit();

            return redirect()->route('admin.products.show', $product->id)
                ->with('success', 'تم إنشاء المنتج بنجاح');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء المنتج: ' . $e->getMessage());
        }
    }

    /**
     * Show edit product form
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        
        // Load merchant and category relationships if they exist
        try {
            if (Schema::hasColumn('products', 'merchant_id')) {
                $product->load('merchant');
            }
            
            if (Schema::hasColumn('products', 'category_id')) {
                $product->load('category');
            }
        } catch (\Exception $e) {
            // Relationships might not exist yet
        }
        
        $merchants = [];
        $categories = [];
        
        try {
            if (Schema::hasTable('merchants')) {
                $merchants = Merchant::where('is_active', true)->get();
            }
        } catch (\Exception $e) {
            // Merchants table might not exist yet
        }
        
        try {
            if (Schema::hasTable('product_categories')) {
                $categories = \App\Modules\Products\Models\ProductCategory::where('is_active', true)
                    ->orderBy('sort_order')
                    ->get();
            }
        } catch (\Exception $e) {
            // Product Categories table might not exist yet
        }
        
        return view('admin.products.edit', compact('product', 'merchants', 'categories'));
    }

    /**
     * Update product
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        // Base validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'is_available' => 'boolean',
            'is_featured' => 'boolean',
            'back_color' => 'nullable|string|max:20',
        ];
        
        // Add conditional validation rules
        if (Schema::hasColumn('products', 'short_description')) {
            $rules['short_description'] = 'nullable|string|max:500';
        }
        if (Schema::hasColumn('products', 'sku')) {
            $rules['sku'] = ['required', 'string', 'max:100', Rule::unique('products')->ignore($product->id)];
        }
        if (Schema::hasColumn('products', 'discount_price')) {
            $rules['discount_price'] = 'nullable|numeric|min:0|lt:price';
        }
        if (Schema::hasColumn('products', 'min_quantity')) {
            $rules['min_quantity'] = 'nullable|integer|min:1';
        }
        if (Schema::hasColumn('products', 'weight')) {
            $rules['weight'] = 'nullable|numeric|min:0';
        }
        if (Schema::hasColumn('products', 'dimensions')) {
            $rules['dimensions'] = 'nullable|string|max:100';
        }
        if (Schema::hasColumn('products', 'merchant_id') && Schema::hasTable('merchants')) {
            $rules['merchant_id'] = 'required|exists:merchants,id';
        }
        if (Schema::hasColumn('products', 'images')) {
            $rules['images'] = 'nullable|array|max:5';
            $rules['images.*'] = 'image|mimes:jpeg,png,jpg,gif|max:2048';
            $rules['removed_images'] = 'nullable|string';
        }
        if (Schema::hasColumn('products', 'meta_title')) {
            $rules['meta_title'] = 'nullable|string|max:255';
        }
        if (Schema::hasColumn('products', 'meta_description')) {
            $rules['meta_description'] = 'nullable|string|max:500';
        }
        
        $validated = $request->validate($rules);

        try {
            DB::beginTransaction();

            $validated['is_available'] = $request->has('is_available');
            $validated['is_featured'] = $request->has('is_featured');

            // Handle images upload (only if images column exists)
            if (Schema::hasColumn('products', 'images')) {
                $currentImages = $product->images ?? [];
                
                // Handle removed images
                if ($request->has('removed_images') && $request->removed_images) {
                    $removedIndexes = explode(',', $request->removed_images);
                    foreach ($removedIndexes as $index) {
                        if (isset($currentImages[$index])) {
                            // Delete physical file
                            if (file_exists(public_path($currentImages[$index]))) {
                                unlink(public_path($currentImages[$index]));
                            }
                            // Remove from array
                            unset($currentImages[$index]);
                        }
                    }
                    $currentImages = array_values($currentImages); // Re-index array
                }
                
                // Handle new images
                if ($request->hasFile('images')) {
                    $newImages = [];
                    foreach ($request->file('images') as $index => $image) {
                        $imageName = time() . '_' . $index . '_' . $image->getClientOriginalName();
                        $image->move(public_path('uploads/products'), $imageName);
                        $newImages[] = 'uploads/products/' . $imageName;
                    }
                    
                    // Merge current and new images (max 5 total)
                    $allImages = array_merge($currentImages, $newImages);
                    $validated['images'] = array_slice($allImages, 0, 5);
                } else {
                    // Keep current images (after removal)
                    $validated['images'] = $currentImages;
                }
            }

            $product->update($validated);

            DB::commit();

            return redirect()->route('admin.products.show', $product->id)
                ->with('success', 'تم تحديث بيانات المنتج بنجاح');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث المنتج: ' . $e->getMessage());
        }
    }

    /**
     * Delete product
     */
    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            
            // Delete images
            if ($product->images) {
                foreach ($product->images as $image) {
                    if (file_exists(public_path($image))) {
                        unlink(public_path($image));
                    }
                }
            }

            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف المنتج بنجاح'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف المنتج: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle product availability
     */
    public function toggleAvailability($id)
    {
        try {
            $product = Product::findOrFail($id);
            
            $product->is_available = !$product->is_available;
            $product->save();

            return response()->json([
                'success' => true,
                'message' => $product->is_available ? 'تم تفعيل المنتج' : 'تم إخفاء المنتج',
                'is_available' => $product->is_available
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث الحالة: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle product featured status
     */
    public function toggleFeatured($id)
    {
        try {
            $product = Product::findOrFail($id);
            
            $product->is_featured = !$product->is_featured;
            $product->save();

            return response()->json([
                'success' => true,
                'message' => $product->is_featured ? 'تم إضافة المنتج للمميزة' : 'تم إزالة المنتج من المميزة',
                'is_featured' => $product->is_featured
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث الحالة: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update product stock
     */
    public function updateStock(Request $request, $id)
    {
        $validated = $request->validate([
            'stock_quantity' => 'required|integer|min:0',
            'action' => 'required|in:set,add,subtract'
        ]);

        try {
            $product = Product::findOrFail($id);
            
            switch ($validated['action']) {
                case 'set':
                    $product->stock_quantity = $validated['stock_quantity'];
                    break;
                case 'add':
                    $product->stock_quantity += $validated['stock_quantity'];
                    break;
                case 'subtract':
                    $product->stock_quantity = max(0, $product->stock_quantity - $validated['stock_quantity']);
                    break;
            }
            
            $product->save();

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث المخزون بنجاح',
                'new_stock' => $product->stock_quantity
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث المخزون: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:activate,deactivate,feature,unfeature,delete',
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id'
        ]);

        try {
            $products = Product::whereIn('id', $validated['product_ids'])->get();
            $count = 0;

            foreach ($products as $product) {
                switch ($validated['action']) {
                    case 'activate':
                        $product->update(['is_available' => true]);
                        $count++;
                        break;
                    case 'deactivate':
                        $product->update(['is_available' => false]);
                        $count++;
                        break;
                    case 'feature':
                        $product->update(['is_featured' => true]);
                        $count++;
                        break;
                    case 'unfeature':
                        $product->update(['is_featured' => false]);
                        $count++;
                        break;
                    case 'delete':
                        // Delete images
                        if ($product->images) {
                            foreach ($product->images as $image) {
                                if (file_exists(public_path($image))) {
                                    unlink(public_path($image));
                                }
                            }
                        }
                        $product->delete();
                        $count++;
                        break;
                }
            }

            $actionText = [
                'activate' => 'تفعيل',
                'deactivate' => 'إخفاء',
                'feature' => 'إضافة للمميزة',
                'unfeature' => 'إزالة من المميزة',
                'delete' => 'حذف'
            ];

            return response()->json([
                'success' => true,
                'message' => "تم {$actionText[$validated['action']]} {$count} منتج بنجاح"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تنفيذ العملية: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get products statistics
     */
    private function getProductsStats()
    {
        $totalProducts = Product::count();
        $availableProducts = Product::where('is_available', true)->count();
        $featuredProducts = Product::where('is_featured', true)->count();
        $outOfStock = Product::where('stock_quantity', 0)->count();
        $lowStock = Product::whereBetween('stock_quantity', [1, 10])->count();
        $recentProducts = Product::where('created_at', '>=', Carbon::now()->subDays(30))->count();

        return [
            'total_products' => $totalProducts,
            'available_products' => $availableProducts,
            'featured_products' => $featuredProducts,
            'out_of_stock' => $outOfStock,
            'low_stock' => $lowStock,
            'recent_products' => $recentProducts,
            'availability_percentage' => $totalProducts > 0 ? round(($availableProducts / $totalProducts) * 100, 1) : 0
        ];
    }

    /**
     * Get product details statistics
     */
    private function getProductDetailsStats($product)
    {
        // Calculate sales data (will be implemented when orders are connected)
        return [
            'total_sales' => 0, // OrderItem::where('product_id', $product->id)->sum('quantity'),
            'total_revenue' => 0, // OrderItem::where('product_id', $product->id)->sum('total_price'),
            'avg_rating' => 0, // Product reviews average
            'total_views' => rand(50, 500), // Product page views
            'stock_movements' => [],
            'recent_orders' => []
        ];
    }
}
