<?php
require_once '../vendor/autoload.php';

// Load Laravel app
$app = require_once '../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "<h2>🔥 Final Image Display Test</h2>";

try {
    // Get all products with images
    $products = \App\Modules\Products\Models\Product::whereNotNull('images')->get();
    
    echo "<h3>Found " . $products->count() . " products with images</h3>";
    
    foreach ($products as $product) {
        echo "<div style='border: 1px solid #ddd; margin: 20px 0; padding: 20px; border-radius: 8px;'>";
        echo "<h4>🛍️ Product: " . $product->name . " (ID: " . $product->id . ")</h4>";
        
        echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;'>";
        
        // Test the first_image attribute
        echo "<div>";
        echo "<h5>✅ Using \$product->first_image:</h5>";
        echo "<img src='" . $product->first_image . "' style='max-width: 200px; height: 150px; object-fit: cover; border: 2px solid green;' onerror='this.style.border=\"2px solid red\"; this.alt=\"❌ FAILED\";'>";
        echo "<br><small style='word-break: break-all;'>" . $product->first_image . "</small>";
        echo "</div>";
        
        // Test direct access to images array
        if ($product->images && count($product->images) > 0) {
            echo "<div>";
            echo "<h5>🔍 Raw DB data:</h5>";
            echo "<code style='background: #f5f5f5; padding: 5px; display: block; word-break: break-all;'>";
            echo "images[0]: " . $product->images[0];
            echo "</code>";
            echo "<img src='" . asset($product->images[0]) . "' style='max-width: 200px; height: 150px; object-fit: cover; border: 2px solid blue;' onerror='this.style.border=\"2px solid red\"; this.alt=\"❌ FAILED\";'>";
            echo "<br><small style='word-break: break-all;'>" . asset($product->images[0]) . "</small>";
            echo "</div>";
        }
        
        // Test file existence
        if ($product->images && count($product->images) > 0) {
            $filePath = public_path($product->images[0]);
            echo "<div>";
            echo "<h5>📁 File Check:</h5>";
            echo "<p><strong>File path:</strong> " . $filePath . "</p>";
            echo "<p><strong>Exists:</strong> " . (file_exists($filePath) ? '<span style="color:green">✅ YES</span>' : '<span style="color:red">❌ NO</span>') . "</p>";
            if (file_exists($filePath)) {
                echo "<p><strong>Size:</strong> " . filesize($filePath) . " bytes</p>";
            }
            echo "</div>";
        }
        
        echo "</div>";
        echo "</div>";
    }
    
    // Test default image
    echo "<div style='border: 1px solid #ddd; margin: 20px 0; padding: 20px; border-radius: 8px; background: #f9f9f9;'>";
    echo "<h4>🖼️ Default Image Test</h4>";
    echo "<img src='" . asset('images/no-product.png') . "' style='max-width: 200px; height: 150px; object-fit: cover; border: 2px solid gray;' onerror='this.style.border=\"2px solid red\"; this.alt=\"❌ DEFAULT IMAGE MISSING\";'>";
    echo "<br><small>" . asset('images/no-product.png') . "</small>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h3>🎯 Expected Results:</h3>";
echo "<ul>";
echo "<li>✅ All images should display correctly with green borders</li>";
echo "<li>✅ URLs should point to https://suntop-eg.com/uploads/products/filename.png</li>";
echo "<li>✅ File existence check should show YES</li>";
echo "<li>✅ Default image should also display correctly</li>";
echo "</ul>";
?>
