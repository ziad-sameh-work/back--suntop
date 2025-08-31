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
        $availability = $request->get('availability', '');
        $price_range = $request->get('price_range', '');
        $featured = $request->get('featured', '');
        $perPage = $request->get('per_page', 15);

        // Build query  
        $query = Product::query()
            ->withCount(['orderItems']);
            


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



        if ($availability !== '') {
            $query->where('is_available', $availability == 'available');
        }

        // إزالة فلتر is_featured - لم يعد موجوداً في الجدول
        // if ($featured !== '') {
        //     $query->where('is_featured', $featured == 'featured');
        // }



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



        // Get categories for filter
        $categories = [];
        if (Schema::hasTable('product_categories')) {
            $categories = \App\Modules\Products\Models\ProductCategory::all();
        }

        return view('admin.products.index', compact(
            'products',
            'stats',
            'categories',
            'search',
            'availability',
            'price_range',
            'perPage'
        ));
    }

    /**
     * Show product details
     */
    public function show($id)
    {
        $product = Product::findOrFail($id);
        

        
        // Get product statistics
        $productStats = $this->getProductDetailsStats($product);

        return view('admin.products.show', compact('product', 'productStats'));
    }

    /**
     * Show create product form
     */
    public function create()
    {
        $categories = [];
        
        try {
            if (Schema::hasTable('product_categories')) {
                $categories = \App\Modules\Products\Models\ProductCategory::where('is_active', true)
                    ->orderBy('sort_order')
                    ->get();
            }
        } catch (\Exception $e) {
            // Product Categories table might not exist yet
        }
        
        return view('admin.products.create', compact('categories'));
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
            'back_color' => 'required|string|max:20',
            'is_available' => 'boolean',
        ];
        
        // Add conditional validation rules
        if (Schema::hasColumn('products', 'category_id') && Schema::hasTable('product_categories')) {
            $rules['category_id'] = 'required|exists:product_categories,id';
        }
        if (Schema::hasColumn('products', 'images')) {
            $rules['images'] = 'nullable|array|max:5';
            $rules['images.*'] = 'image|mimes:jpeg,png,jpg,gif,webp|max:10240'; // Increased to 10MB
        }
        
        $validated = $request->validate($rules);

        try {
            DB::beginTransaction();

            // Clean validated data - remove non-fillable fields
            $productData = [
                'name' => $validated['name'],
                'description' => $validated['description'],
                'price' => $validated['price'],
                'back_color' => $validated['back_color'],
                'is_available' => $request->has('is_available'),
            ];

            // Add category_id if it exists
            if (isset($validated['category_id'])) {
                $productData['category_id'] = $validated['category_id'];
            }

            // Handle images upload using Storage
            $images = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    try {
                        // Validate image
                        if (!$image->isValid()) {
                            continue;
                        }
                        
                        // Generate unique filename
                        $imageName = time() . '_' . $index . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                        
                        // Store in storage/app/public/products directory
                        $path = $image->storeAs('products', $imageName, 'public');
                        
                        if ($path) {
                            $images[] = $path; // This will be 'products/filename.ext'
                        }
                    } catch (\Exception $imageError) {
                        \Log::error("Image upload failed for image $index: " . $imageError->getMessage());
                    }
                }
            }
            $productData['images'] = $images;

            $product = Product::create($productData);

            DB::commit();

            return redirect()->route('admin.products.show', $product->id)
                ->with('success', 'تم إنشاء المنتج بنجاح');

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Product creation failed: ' . $e->getMessage());
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
        $categories = [];
        
        try {
            if (Schema::hasColumn('products', 'category_id')) {
                $product->load('category');
            }
        } catch (\Exception $e) {
            // Relationships might not exist yet
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
        
        return view('admin.products.edit', compact('product', 'categories'));
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
            'back_color' => 'required|string|max:20',
            'is_available' => 'boolean',
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
            
            // Debug: Log the back_color value
            \Log::info('Product Update - back_color value:', [
                'request_back_color' => $request->get('back_color'),
                'validated_back_color' => $validated['back_color'] ?? 'not set',
                'product_id' => $product->id,
                'has_back_color_column' => Schema::hasColumn('products', 'back_color'),
                'all_request_data' => $request->all()
            ]);
            
            // Ensure back_color is included in update data
            if ($request->has('back_color')) {
                $validated['back_color'] = $request->get('back_color');
            } elseif ($request->has('back_color_backup')) {
                $validated['back_color'] = $request->get('back_color_backup');
            }
            
            // Additional logging for debugging
            \Log::info('Final validated data for update:', [
                'product_id' => $product->id,
                'back_color_in_validated' => isset($validated['back_color']),
                'back_color_value' => $validated['back_color'] ?? 'not set',
                'all_validated_keys' => array_keys($validated)
            ]);

            // Handle images upload (only if images column exists)
            if (Schema::hasColumn('products', 'images')) {
                $currentImages = $product->images ?? [];
                
                // Handle removed images
                if ($request->has('removed_images') && $request->removed_images) {
                    $removedIndexes = explode(',', $request->removed_images);
                    foreach ($removedIndexes as $index) {
                        if (isset($currentImages[$index])) {
                            // Delete physical file from storage
                            if (Storage::disk('public')->exists($currentImages[$index])) {
                                Storage::disk('public')->delete($currentImages[$index]);
                            }
                            // Remove from array
                            unset($currentImages[$index]);
                        }
                    }
                    $currentImages = array_values($currentImages); // Re-index array
                }
                
                // Handle new images using Storage
                if ($request->hasFile('images')) {
                    $newImages = [];
                    foreach ($request->file('images') as $index => $image) {
                        try {
                            // Validate image
                            if (!$image->isValid()) {
                                continue;
                            }
                            
                            // Generate unique filename
                            $imageName = time() . '_' . $index . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                            
                            // Store in storage/app/public/products directory
                            $path = $image->storeAs('products', $imageName, 'public');
                            
                            if ($path) {
                                $newImages[] = $path; // This will be 'products/filename.ext'
                            }
                        } catch (\Exception $imageError) {
                            \Log::error("Image upload failed for image $index: " . $imageError->getMessage());
                        }
                    }
                    
                    // Merge current and new images (max 5 total)
                    $allImages = array_merge($currentImages, $newImages);
                    $validated['images'] = array_slice($allImages, 0, 5);
                } else {
                    // Keep current images (after removal)
                    $validated['images'] = $currentImages;
                }
            }

            // Try to update the product
            $updateResult = $product->update($validated);
            
            // If update failed but we have back_color, try to set it directly
            if (!$updateResult && isset($validated['back_color'])) {
                \Log::warning('Product update failed, trying direct assignment', [
                    'product_id' => $product->id,
                    'back_color' => $validated['back_color']
                ]);
                
                $product->back_color = $validated['back_color'];
                $product->save();
            }
            
            // Verify the update worked
            $product->refresh();
            \Log::info('Product update verification:', [
                'product_id' => $product->id,
                'final_back_color' => $product->back_color,
                'update_successful' => $updateResult
            ]);

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
     * Toggle product featured status - DISABLED (feature removed)
     */
    public function toggleFeatured($id)
    {
        return response()->json([
            'success' => false,
            'message' => 'ميزة المنتجات المميزة لم تعد متاحة'
        ], 404);
    }

    /**
     * Update product stock - DISABLED (stock_quantity column no longer exists)
     */
    public function updateStock(Request $request, $id)
    {
        return response()->json([
            'success' => false,
            'message' => 'ميزة تحديث المخزون لم تعد متاحة - تم تبسيط نظام المنتجات'
        ], 404);
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
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
        $recentProducts = Product::where('created_at', '>=', Carbon::now()->subDays(30))->count();

        // حساب إحصائيات الفئات
        $categoriesStats = [];
        if (Schema::hasTable('product_categories')) {
            $categoriesStats = \App\Modules\Products\Models\ProductCategory::withCount('products')->get();
        }

        return [
            'total_products' => $totalProducts,
            'available_products' => $availableProducts,
            'recent_products' => $recentProducts,
            'categories_stats' => $categoriesStats,
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

            'stock_movements' => [],
            'recent_orders' => []
        ];
    }
}
