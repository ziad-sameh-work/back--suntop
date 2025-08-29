<?php
/*
 * Add sample images and categories to products
 * Access via: http://127.0.0.1:8000/add_sample_data.php
 */

echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Add Sample Data</title>";
echo "<style>body{font-family:Arial;padding:20px} .ok{color:green} .error{color:red} .warning{color:orange} .info{background:#e7f3ff;padding:15px;border-radius:5px;margin:15px 0}</style>";
echo "</head><body>";

echo "<h1>📦 إضافة بيانات تجريبية للمنتجات</h1>";

try {
    require_once '../vendor/autoload.php';
    $app = require_once '../bootstrap/app.php';
    
    use Illuminate\Support\Facades\DB;
    use App\Modules\Products\Models\Product;
    use App\Modules\Products\Models\ProductCategory;
    
    echo "<h2>1. إنشاء فئات تجريبية</h2>";
    
    if (isset($_POST['create_categories'])) {
        $categories = [
            ['name' => '250ml', 'display_name' => '250 مل', 'description' => 'منتجات بحجم 250 مل'],
            ['name' => '500ml', 'display_name' => '500 مل', 'description' => 'منتجات بحجم 500 مل'],
            ['name' => '1L', 'display_name' => '1 لتر', 'description' => 'منتجات بحجم 1 لتر'],
            ['name' => '2L', 'display_name' => '2 لتر', 'description' => 'منتجات بحجم 2 لتر'],
        ];
        
        $created = 0;
        foreach ($categories as $categoryData) {
            $existing = ProductCategory::where('name', $categoryData['name'])->first();
            if (!$existing) {
                ProductCategory::create($categoryData);
                $created++;
                echo "<p class='ok'>✅ تم إنشاء فئة: {$categoryData['display_name']}</p>";
            } else {
                echo "<p class='warning'>⚠️ الفئة موجودة بالفعل: {$categoryData['display_name']}</p>";
            }
        }
        
        echo "<p class='ok'>✅ تم إنشاء $created فئة جديدة</p>";
    } else {
        echo "<form method='POST'>";
        echo "<input type='hidden' name='create_categories' value='1'>";
        echo "<button type='submit' style='background:#28a745;color:white;padding:10px 20px;border:none;border-radius:5px'>إنشاء فئات تجريبية</button>";
        echo "</form>";
    }
    
    echo "<h2>2. تحديث منتجات بفئات وصور تجريبية</h2>";
    
    if (isset($_POST['update_products'])) {
        $categories = ProductCategory::all();
        $products = Product::limit(10)->get();
        
        $sampleImages = [
            'uploads/products/sample1.jpg',
            'uploads/products/sample2.jpg',
            'uploads/products/sample3.jpg',
            'https://via.placeholder.com/400x400/FF6B35/FFFFFF?text=Product+1',
            'https://via.placeholder.com/400x400/007BFF/FFFFFF?text=Product+2',
            'https://via.placeholder.com/400x400/28A745/FFFFFF?text=Product+3',
        ];
        
        $sampleColors = ['#FF6B35', '#007BFF', '#28A745', '#FFC107', '#DC3545', '#6C757D'];
        
        $updated = 0;
        foreach ($products as $index => $product) {
            $updateData = [];
            
            // Add category
            if ($categories->count() > 0 && !$product->category_id) {
                $randomCategory = $categories->random();
                $updateData['category_id'] = $randomCategory->id;
            }
            
            // Add images
            if (!$product->images || empty($product->images)) {
                $productImages = [
                    $sampleImages[$index % count($sampleImages)],
                    $sampleImages[($index + 1) % count($sampleImages)]
                ];
                $updateData['images'] = $productImages;
            }
            
            // Add back_color if missing
            if (!$product->back_color) {
                $updateData['back_color'] = $sampleColors[$index % count($sampleColors)];
            }
            
            // Add legacy image_url if missing
            if (!$product->image_url && empty($updateData['images'])) {
                $updateData['image_url'] = $sampleImages[$index % count($sampleImages)];
            }
            
            // Add volume_category if missing
            if (!$product->volume_category && $categories->count() > 0) {
                $randomCategory = $categories->random();
                $updateData['volume_category'] = $randomCategory->name;
            }
            
            if (!empty($updateData)) {
                $product->update($updateData);
                $updated++;
                echo "<p class='ok'>✅ تم تحديث المنتج: {$product->name}</p>";
            }
        }
        
        echo "<p class='ok'>✅ تم تحديث $updated منتج</p>";
    } else {
        echo "<form method='POST'>";
        echo "<input type='hidden' name='update_products' value='1'>";
        echo "<button type='submit' style='background:#007bff;color:white;padding:10px 20px;border:none;border-radius:5px'>تحديث المنتجات ببيانات تجريبية</button>";
        echo "</form>";
    }
    
    echo "<h2>3. إضافة منتجات تجريبية جديدة</h2>";
    
    if (isset($_POST['create_products'])) {
        $categories = ProductCategory::all();
        
        $sampleProducts = [
            [
                'name' => 'عصير برتقال طبيعي',
                'description' => 'عصير برتقال طبيعي 100% بدون إضافات',
                'price' => 25.50,
                'stock_quantity' => 100,
                'images' => ['https://via.placeholder.com/400x400/FF6B35/FFFFFF?text=Orange+Juice'],
                'back_color' => '#FF6B35',
                'volume_category' => '250ml'
            ],
            [
                'name' => 'عصير تفاح طازج',
                'description' => 'عصير تفاح طازج ولذيذ',
                'price' => 22.00,
                'stock_quantity' => 80,
                'images' => ['https://via.placeholder.com/400x400/28A745/FFFFFF?text=Apple+Juice'],
                'back_color' => '#28A745',
                'volume_category' => '500ml'
            ],
            [
                'name' => 'مشروب الليمون المنعش',
                'description' => 'مشروب الليمون الطبيعي المنعش',
                'price' => 18.75,
                'stock_quantity' => 120,
                'images' => ['https://via.placeholder.com/400x400/FFC107/000000?text=Lemon+Drink'],
                'back_color' => '#FFC107',
                'volume_category' => '1L'
            ]
        ];
        
        $created = 0;
        foreach ($sampleProducts as $productData) {
            // Add category_id if categories exist
            if ($categories->count() > 0) {
                $category = $categories->where('name', $productData['volume_category'])->first();
                if ($category) {
                    $productData['category_id'] = $category->id;
                }
            }
            
            $existing = Product::where('name', $productData['name'])->first();
            if (!$existing) {
                Product::create($productData);
                $created++;
                echo "<p class='ok'>✅ تم إنشاء منتج: {$productData['name']}</p>";
            } else {
                echo "<p class='warning'>⚠️ المنتج موجود بالفعل: {$productData['name']}</p>";
            }
        }
        
        echo "<p class='ok'>✅ تم إنشاء $created منتج جديد</p>";
    } else {
        echo "<form method='POST'>";
        echo "<input type='hidden' name='create_products' value='1'>";
        echo "<button type='submit' style='background:#28a745;color:white;padding:10px 20px;border:none;border-radius:5px'>إنشاء منتجات تجريبية</button>";
        echo "</form>";
    }
    
    echo "<h2>4. الحالة الحالية</h2>";
    
    $totalProducts = Product::count();
    $productsWithImages = Product::whereNotNull('images')->count();
    $productsWithCategories = Product::whereNotNull('category_id')->count();
    $totalCategories = ProductCategory::count();
    
    echo "<div class='info'>";
    echo "<p><strong>إجمالي المنتجات:</strong> $totalProducts</p>";
    echo "<p><strong>منتجات بها صور:</strong> $productsWithImages</p>";
    echo "<p><strong>منتجات مربوطة بفئات:</strong> $productsWithCategories</p>";
    echo "<p><strong>إجمالي الفئات:</strong> $totalCategories</p>";
    echo "</div>";
    
    echo "<h2>5. اختبار النتائج</h2>";
    echo "<div class='info'>";
    echo "<p>اختبر النتائج:</p>";
    echo "<ul>";
    echo "<li><a href='/test_images_categories.php' target='_blank'>🔍 اختبار الصور والفئات</a></li>";
    echo "<li><a href='/test_endpoints.html' target='_blank'>🌐 اختبار API Endpoints</a></li>";
    echo "<li><a href='/api/products' target='_blank'>📦 API: جميع المنتجات</a></li>";
    echo "<li><a href='/api/products/featured' target='_blank'>⭐ API: المنتجات المميزة</a></li>";
    echo "</ul>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p class='error'>❌ خطأ: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr>";
echo "<p style='color:red;'><strong>⚠️ احذف هذا الملف بعد الانتهاء!</strong></p>";

echo "</body></html>";
?>
