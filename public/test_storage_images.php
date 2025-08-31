<?php
require_once '../vendor/autoload.php';

// Load Laravel app
$app = require_once '../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "<h2>📦 Storage Images Test</h2>";

try {
    // Test storage configuration
    echo "<div style='background: #e2e3e5; padding: 15px; margin: 10px 0; border-left: 4px solid #6c757d;'>";
    echo "<h3>⚙️ Storage Configuration:</h3>";
    echo "<p><strong>APP_URL:</strong> <code>" . config('app.url') . "</code></p>";
    echo "<p><strong>Storage URL:</strong> <code>" . \Storage::disk('public')->url('test.png') . "</code></p>";
    echo "<p><strong>Storage Path:</strong> <code>" . \Storage::disk('public')->path('') . "</code></p>";
    
    // Check if storage link exists
    $storageLinkPath = public_path('storage');
    echo "<p><strong>Storage Link Exists:</strong> " . (is_link($storageLinkPath) || is_dir($storageLinkPath) ? '✅ YES' : '❌ NO') . "</p>";
    if (!is_link($storageLinkPath) && !is_dir($storageLinkPath)) {
        echo "<p style='color: red;'>⚠️ You need to run: <code>php artisan storage:link</code></p>";
    }
    echo "</div>";

    // Get products with images
    $products = \App\Modules\Products\Models\Product::whereNotNull('images')->take(3)->get();
    
    if ($products->count() > 0) {
        echo "<h3>🛍️ Found " . $products->count() . " products with images</h3>";
        
        foreach ($products as $product) {
            echo "<div style='border: 1px solid #ddd; margin: 20px 0; padding: 20px; border-radius: 8px;'>";
            echo "<h4>Product: " . $product->name . " (ID: " . $product->id . ")</h4>";
            
            if ($product->images && is_array($product->images)) {
                echo "<h5>📂 Raw Images Data:</h5>";
                foreach ($product->images as $index => $imagePath) {
                    echo "<div style='margin: 10px 0; padding: 10px; background: #f8f9fa;'>";
                    echo "<p><strong>Image $index:</strong> <code>$imagePath</code></p>";
                    
                    // Test Storage URL
                    $storageUrl = \Storage::disk('public')->url($imagePath);
                    echo "<p><strong>Storage URL:</strong> <code>$storageUrl</code></p>";
                    
                    // Check if file exists in storage
                    $existsInStorage = \Storage::disk('public')->exists($imagePath);
                    echo "<p><strong>Exists in Storage:</strong> " . ($existsInStorage ? '✅ YES' : '❌ NO') . "</p>";
                    
                    if ($existsInStorage) {
                        $fileSize = \Storage::disk('public')->size($imagePath);
                        echo "<p><strong>File Size:</strong> " . $fileSize . " bytes</p>";
                    }
                    
                    echo "<div style='margin-top: 10px;'>";
                    echo "<img src='$storageUrl' style='max-width: 200px; height: 150px; object-fit: cover; border: 2px solid " . ($existsInStorage ? 'green' : 'red') . ";' onerror='this.style.border=\"2px solid red\"; this.alt=\"❌ FAILED TO LOAD\";'>";
                    echo "</div>";
                    echo "</div>";
                }
                
                // Test first_image attribute
                echo "<h5>🖼️ First Image Attribute Test:</h5>";
                $firstImageUrl = $product->first_image;
                echo "<p><strong>first_image URL:</strong> <code>$firstImageUrl</code></p>";
                echo "<div style='margin: 10px 0;'>";
                echo "<img src='$firstImageUrl' style='max-width: 200px; height: 150px; object-fit: cover; border: 2px solid blue;' onerror='this.style.border=\"2px solid red\"; this.alt=\"❌ FAILED TO LOAD\";'>";
                echo "</div>";
            }
            
            echo "</div>";
        }
        
        // Test API Resource
        echo "<div style='background: #fff3cd; padding: 15px; margin: 10px 0; border-left: 4px solid #ffc107;'>";
        echo "<h4>🔌 API Resource Test:</h4>";
        $firstProduct = $products->first();
        $productResource = new \App\Modules\Products\Resources\ProductResource($firstProduct);
        $resourceArray = $productResource->toArray(null);
        
        echo "<p><strong>API image_url:</strong> <br><code>" . $resourceArray['image_url'] . "</code></p>";
        echo "<p><strong>API images:</strong> <br><pre>" . json_encode($resourceArray['images'], JSON_PRETTY_PRINT) . "</pre></p>";
        echo "</div>";
        
    } else {
        echo "<p style='color: orange;'>⚠️ No products with images found. Try adding a product first.</p>";
    }
    
    // Expected vs Actual
    echo "<div style='background: #d4edda; padding: 15px; margin: 10px 0; border-left: 4px solid #28a745;'>";
    echo "<h4>🎯 Expected Results:</h4>";
    echo "<ul>";
    echo "<li>✅ Images should be saved as: <code>products/filename.ext</code></li>";
    echo "<li>✅ URLs should be: <code>https://suntop-eg.com/storage/products/filename.ext</code></li>";
    echo "<li>✅ Files should exist in: <code>storage/app/public/products/</code></li>";
    echo "<li>✅ Accessible via: <code>public/storage/products/</code> (symbolic link)</li>";
    echo "</ul>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 15px; margin: 10px 0; border-left: 4px solid #dc3545;'>";
    echo "<h4>❌ Error:</h4>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    echo "</div>";
}

echo "<hr>";
echo "<h3>📋 Next Steps:</h3>";
echo "<ol>";
echo "<li>Make sure <code>php artisan storage:link</code> is run</li>";
echo "<li>Add a new product with images to test</li>";
echo "<li>Check that images show up correctly</li>";
echo "<li>Verify API returns proper storage URLs</li>";
echo "</ol>";
?>
