<?php
/*
 * Test images and categories in API responses
 * Access via: http://127.0.0.1:8000/test_images_categories.php
 */

echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Images & Categories Test</title>";
echo "<style>
    body{font-family:Arial;padding:20px;background:#f5f5f5} 
    .container{max-width:1200px;margin:0 auto;background:white;padding:20px;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,0.1)} 
    .ok{color:green;font-weight:bold} 
    .error{color:red;font-weight:bold} 
    .warning{color:orange;font-weight:bold} 
    .info{background:#e7f3ff;padding:15px;border-radius:5px;margin:15px 0;border:1px solid #b3d9ff}
    .product-card{border:1px solid #ddd;padding:15px;margin:10px 0;border-radius:8px;background:#f9f9f9}
    .image-preview{max-width:100px;max-height:100px;border:1px solid #ccc;margin:5px}
    .json-output{background:#f8f8f8;padding:10px;border-radius:5px;font-family:monospace;font-size:12px;max-height:300px;overflow-y:auto;white-space:pre-wrap}
    table{border-collapse:collapse;width:100%;margin:15px 0} 
    th,td{border:1px solid #ddd;padding:8px;text-align:right} 
    th{background:#f0f0f0}
    .test-btn{background:#007bff;color:white;padding:8px 15px;border:none;border-radius:3px;cursor:pointer;margin:5px}
</style>";
echo "</head><body>";

echo "<div class='container'>";
echo "<h1>🖼️ اختبار الصور والفئات في API</h1>";

try {
    require_once '../vendor/autoload.php';
    $app = require_once '../bootstrap/app.php';
    
    use Illuminate\Support\Facades\DB;
    use App\Modules\Products\Models\Product;
    use App\Modules\Products\Models\ProductCategory;
    use App\Modules\Products\Resources\ProductResource;
    use App\Modules\Products\Resources\ProductDetailResource;
    
    // 1. Database Analysis
    echo "<h2>📊 تحليل قاعدة البيانات</h2>";
    
    $totalProducts = Product::count();
    $productsWithImages = Product::whereNotNull('images')->count();
    $productsWithImageUrl = Product::whereNotNull('image_url')->count();
    $productsWithGallery = Product::whereNotNull('gallery')->count();
    $productsWithCategory = Product::whereNotNull('category_id')->count();
    $productsWithLegacyCategory = Product::whereNotNull('category')->count();
    $productsWithVolumeCategory = Product::whereNotNull('volume_category')->count();
    
    echo "<div class='info'>";
    echo "<h3>📈 إحصائيات المنتجات:</h3>";
    echo "<ul>";
    echo "<li><strong>إجمالي المنتجات:</strong> $totalProducts</li>";
    echo "<li><strong>منتجات بها images (جديد):</strong> $productsWithImages</li>";
    echo "<li><strong>منتجات بها image_url (قديم):</strong> $productsWithImageUrl</li>";
    echo "<li><strong>منتجات بها gallery (قديم):</strong> $productsWithGallery</li>";
    echo "<li><strong>منتجات بها category_id:</strong> $productsWithCategory</li>";
    echo "<li><strong>منتجات بها category (قديم):</strong> $productsWithLegacyCategory</li>";
    echo "<li><strong>منتجات بها volume_category:</strong> $productsWithVolumeCategory</li>";
    echo "</ul>";
    echo "</div>";
    
    // 2. Sample Products Analysis
    echo "<h2>🔍 تحليل عينة من المنتجات</h2>";
    
    $sampleProducts = Product::limit(5)->get();
    
    if ($sampleProducts->count() > 0) {
        echo "<table>";
        echo "<tr><th>ID</th><th>اسم المنتج</th><th>images</th><th>image_url</th><th>gallery</th><th>category_id</th><th>category</th><th>volume_category</th></tr>";
        
        foreach ($sampleProducts as $product) {
            echo "<tr>";
            echo "<td>{$product->id}</td>";
            echo "<td>" . substr($product->name, 0, 20) . "...</td>";
            echo "<td>" . ($product->images ? (is_array($product->images) ? count($product->images) . ' صور' : 'JSON') : 'NULL') . "</td>";
            echo "<td>" . ($product->image_url ? 'موجود' : 'NULL') . "</td>";
            echo "<td>" . ($product->gallery ? (is_array($product->gallery) ? count($product->gallery) . ' صور' : 'JSON') : 'NULL') . "</td>";
            echo "<td>" . ($product->category_id ?? 'NULL') . "</td>";
            echo "<td>" . ($product->category ?? 'NULL') . "</td>";
            echo "<td>" . ($product->volume_category ?? 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // 3. Categories Analysis
    echo "<h2>📂 تحليل الفئات</h2>";
    
    try {
        $categories = ProductCategory::all();
        if ($categories->count() > 0) {
            echo "<p class='ok'>✅ يوجد {$categories->count()} فئة في جدول product_categories</p>";
            echo "<table>";
            echo "<tr><th>ID</th><th>Name</th><th>Display Name</th><th>Active</th><th>عدد المنتجات</th></tr>";
            
            foreach ($categories as $category) {
                $productCount = $category->products()->count();
                echo "<tr>";
                echo "<td>{$category->id}</td>";
                echo "<td>{$category->name}</td>";
                echo "<td>{$category->display_name}</td>";
                echo "<td>" . ($category->is_active ? 'نشط' : 'غير نشط') . "</td>";
                echo "<td>$productCount</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p class='warning'>⚠️ لا توجد فئات في جدول product_categories</p>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>❌ خطأ في الوصول لجدول product_categories: " . $e->getMessage() . "</p>";
    }
    
    // 4. API Response Test
    echo "<h2>🚀 اختبار استجابة API</h2>";
    
    $firstProduct = Product::first();
    if ($firstProduct) {
        echo "<div class='product-card'>";
        echo "<h3>🔬 منتج تجريبي: {$firstProduct->name}</h3>";
        
        // Test ProductResource
        echo "<h4>📦 ProductResource Response:</h4>";
        $productResource = new ProductResource($firstProduct);
        $resourceArray = $productResource->toArray(request());
        echo "<div class='json-output'>" . json_encode($resourceArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</div>";
        
        // Test ProductDetailResource
        echo "<h4>📋 ProductDetailResource Response:</h4>";
        $detailResource = new ProductDetailResource($firstProduct);
        $detailArray = $detailResource->toArray(request());
        echo "<div class='json-output'>" . json_encode($detailArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</div>";
        
        // Image analysis
        echo "<h4>🖼️ تحليل الصور:</h4>";
        echo "<ul>";
        echo "<li><strong>images:</strong> " . ($firstProduct->images ? json_encode($firstProduct->images) : 'NULL') . "</li>";
        echo "<li><strong>image_url:</strong> " . ($firstProduct->image_url ?? 'NULL') . "</li>";
        echo "<li><strong>gallery:</strong> " . ($firstProduct->gallery ? json_encode($firstProduct->gallery) : 'NULL') . "</li>";
        echo "<li><strong>Main Image URL:</strong> " . ($resourceArray['image_url'] ?? 'NULL') . "</li>";
        echo "<li><strong>All Images:</strong> " . json_encode($resourceArray['images'] ?? []) . "</li>";
        echo "</ul>";
        
        // Category analysis
        echo "<h4>📂 تحليل الفئة:</h4>";
        echo "<ul>";
        echo "<li><strong>category_id:</strong> " . ($firstProduct->category_id ?? 'NULL') . "</li>";
        echo "<li><strong>category (legacy):</strong> " . ($firstProduct->category ?? 'NULL') . "</li>";
        echo "<li><strong>volume_category:</strong> " . ($firstProduct->volume_category ?? 'NULL') . "</li>";
        echo "<li><strong>API Category:</strong> " . ($resourceArray['category'] ?? 'NULL') . "</li>";
        echo "<li><strong>API Category ID:</strong> " . ($resourceArray['category_id'] ?? 'NULL') . "</li>";
        echo "</ul>";
        
        echo "</div>";
    }
    
    // 5. Live API Test
    echo "<h2>🌐 اختبار API مباشر</h2>";
    echo "<div class='info'>";
    echo "<p>اختبر الـ endpoints مباشرة:</p>";
    echo "<button class='test-btn' onclick=\"window.open('/api/products', '_blank')\">📦 All Products</button>";
    echo "<button class='test-btn' onclick=\"window.open('/api/products/featured', '_blank')\">⭐ Featured Products</button>";
    echo "<button class='test-btn' onclick=\"window.open('/api/products/1', '_blank')\">🔍 Single Product</button>";
    echo "</div>";
    
    // 6. Recommendations
    echo "<h2>💡 التوصيات</h2>";
    echo "<div class='info'>";
    echo "<h3>لحل مشاكل الصور:</h3>";
    echo "<ul>";
    if ($productsWithImages == 0 && $productsWithImageUrl == 0 && $productsWithGallery == 0) {
        echo "<li class='error'>❌ لا توجد صور في أي منتج - أضف صور للمنتجات</li>";
    } else {
        echo "<li class='ok'>✅ يمكن للـ API العثور على الصور من المصادر المتاحة</li>";
    }
    echo "<li>استخدم حقل <code>images</code> الجديد لأفضل أداء</li>";
    echo "<li>تأكد من رفع الصور في مجلد <code>public/uploads/products/</code></li>";
    echo "</ul>";
    
    echo "<h3>لحل مشاكل الفئات:</h3>";
    echo "<ul>";
    if ($productsWithCategory == 0) {
        echo "<li class='warning'>⚠️ لا توجد منتجات مربوطة بفئات حديثة - ربط المنتجات بـ product_categories</li>";
    } else {
        echo "<li class='ok'>✅ يوجد منتجات مربوطة بالفئات الحديثة</li>";
    }
    echo "<li>إنشاء فئات في جدول <code>product_categories</code></li>";
    echo "<li>ربط المنتجات بـ <code>category_id</code></li>";
    echo "</ul>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p class='error'>❌ خطأ: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "</div>";
echo "</body></html>";
?>
