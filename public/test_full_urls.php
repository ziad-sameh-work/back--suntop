<?php
require_once '../vendor/autoload.php';

// Load Laravel app
$app = require_once '../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "<h2>🔗 Full URL Test</h2>";

try {
    // Get first product with images
    $product = \App\Modules\Products\Models\Product::whereNotNull('images')->first();
    
    if ($product) {
        echo "<h3>✅ Product: " . $product->name . " (ID: " . $product->id . ")</h3>";
        
        echo "<div style='background: #f8f9fa; padding: 15px; margin: 10px 0; border-left: 4px solid #007bff;'>";
        echo "<h4>🖼️ Image URLs Test:</h4>";
        
        // Test first_image attribute
        $firstImageUrl = $product->first_image;
        echo "<p><strong>First Image URL:</strong> <br><code>" . $firstImageUrl . "</code></p>";
        
        // Check if it's full URL
        $isFullUrl = filter_var($firstImageUrl, FILTER_VALIDATE_URL);
        echo "<p><strong>Is Full URL:</strong> " . ($isFullUrl ? '✅ YES' : '❌ NO') . "</p>";
        
        // Display the image
        echo "<div style='margin: 20px 0;'>";
        echo "<h5>Image Preview:</h5>";
        echo "<img src='$firstImageUrl' style='max-width: 300px; height: 200px; object-fit: cover; border: 2px solid " . ($isFullUrl ? 'green' : 'red') . ";' onerror='this.style.border=\"2px solid red\"; this.alt=\"❌ FAILED TO LOAD\";'>";
        echo "</div>";
        
        // Test raw images data
        if ($product->images && is_array($product->images)) {
            echo "<h4>📂 Raw Images Data:</h4>";
            foreach ($product->images as $index => $imagePath) {
                echo "<p><strong>Image $index:</strong> <code>$imagePath</code></p>";
                $fullUrl = url($imagePath);
                echo "<p><strong>Full URL:</strong> <code>$fullUrl</code></p>";
                echo "<hr style='margin: 10px 0;'>";
            }
        }
        
        echo "</div>";
        
        // Test API Resource
        echo "<div style='background: #fff3cd; padding: 15px; margin: 10px 0; border-left: 4px solid #ffc107;'>";
        echo "<h4>🔌 API Resource Test:</h4>";
        $productResource = new \App\Modules\Products\Resources\ProductResource($product);
        $resourceArray = $productResource->toArray(null);
        
        echo "<p><strong>API image_url:</strong> <br><code>" . $resourceArray['image_url'] . "</code></p>";
        echo "<p><strong>API images:</strong> <br><code>" . json_encode($resourceArray['images']) . "</code></p>";
        echo "</div>";
        
        // Test expected vs actual
        echo "<div style='background: #d4edda; padding: 15px; margin: 10px 0; border-left: 4px solid #28a745;'>";
        echo "<h4>🎯 Expected vs Actual:</h4>";
        echo "<p><strong>Expected format:</strong> <code>https://suntop-eg.com/uploads/products/filename.png</code></p>";
        echo "<p><strong>Actual result:</strong> <code>$firstImageUrl</code></p>";
        
        $urlParts = parse_url($firstImageUrl);
        echo "<p><strong>Domain:</strong> " . ($urlParts['host'] ?? 'N/A') . "</p>";
        echo "<p><strong>Path:</strong> " . ($urlParts['path'] ?? 'N/A') . "</p>";
        
        if (isset($urlParts['host']) && $urlParts['host'] === 'suntop-eg.com') {
            echo "<p style='color: green;'>✅ Domain is correct!</p>";
        } else {
            echo "<p style='color: red;'>❌ Domain issue detected!</p>";
        }
        echo "</div>";
        
    } else {
        echo "<p style='color: orange;'>⚠️ No products with images found</p>";
    }
    
    // Test Laravel configuration
    echo "<div style='background: #e2e3e5; padding: 15px; margin: 10px 0; border-left: 4px solid #6c757d;'>";
    echo "<h4>⚙️ Laravel Configuration:</h4>";
    echo "<p><strong>APP_URL:</strong> <code>" . config('app.url') . "</code></p>";
    echo "<p><strong>asset() test:</strong> <code>" . asset('test.png') . "</code></p>";
    echo "<p><strong>url() test:</strong> <code>" . url('test.png') . "</code></p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 15px; margin: 10px 0; border-left: 4px solid #dc3545;'>";
    echo "<h4>❌ Error:</h4>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "</div>";
}

echo "<hr>";
echo "<h3>📋 Instructions:</h3>";
echo "<ol>";
echo "<li>The image URLs should now be full URLs with your domain</li>";
echo "<li>They should start with <code>https://suntop-eg.com/</code></li>";
echo "<li>Images should display correctly in both admin panel and API</li>";
echo "<li>Flutter app will now receive complete URLs</li>";
echo "</ol>";
?>
