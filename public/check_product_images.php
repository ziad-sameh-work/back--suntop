<?php
require_once '../vendor/autoload.php';

// Load Laravel app
$app = require_once '../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "<h2>Product Images Debug</h2>";

try {
    // Get first product
    $product = \App\Modules\Products\Models\Product::first();
    
    if ($product) {
        echo "<h3>Product: " . $product->name . "</h3>";
        echo "<p><strong>ID:</strong> " . $product->id . "</p>";
        
        echo "<h4>Images Data (from database):</h4>";
        echo "<pre>";
        var_dump($product->images);
        echo "</pre>";
        
        echo "<h4>First Image Tests:</h4>";
        
        if ($product->images && count($product->images) > 0) {
            $firstImage = $product->images[0];
            echo "<p><strong>Raw path from DB:</strong> " . $firstImage . "</p>";
            echo "<p><strong>Using asset():</strong> " . asset($firstImage) . "</p>";
            echo "<p><strong>Using first_image attribute:</strong> " . $product->first_image . "</p>";
            
            $fullPath = public_path($firstImage);
            echo "<p><strong>Full file path:</strong> " . $fullPath . "</p>";
            echo "<p><strong>File exists:</strong> " . (file_exists($fullPath) ? 'YES' : 'NO') . "</p>";
            
            if (file_exists($fullPath)) {
                echo "<p><strong>File size:</strong> " . filesize($fullPath) . " bytes</p>";
            }
            
            echo "<h4>Test Image Display:</h4>";
            echo "<div style='display: flex; gap: 20px; flex-wrap: wrap;'>";
            
            // Test different ways to display the image
            echo "<div>";
            echo "<h5>Using asset(\$product->images[0]):</h5>";
            echo "<img src='" . asset($firstImage) . "' style='max-width: 200px; border: 1px solid #ccc;' onerror='this.style.border=\"2px solid red\"; this.alt=\"FAILED TO LOAD\";'>";
            echo "<br><small>" . asset($firstImage) . "</small>";
            echo "</div>";
            
            echo "<div>";
            echo "<h5>Using \$product->first_image:</h5>";
            echo "<img src='" . $product->first_image . "' style='max-width: 200px; border: 1px solid #ccc;' onerror='this.style.border=\"2px solid red\"; this.alt=\"FAILED TO LOAD\";'>";
            echo "<br><small>" . $product->first_image . "</small>";
            echo "</div>";
            
            echo "<div>";
            echo "<h5>Direct URL test:</h5>";
            $directUrl = url($firstImage);
            echo "<img src='" . $directUrl . "' style='max-width: 200px; border: 1px solid #ccc;' onerror='this.style.border=\"2px solid red\"; this.alt=\"FAILED TO LOAD\";'>";
            echo "<br><small>" . $directUrl . "</small>";
            echo "</div>";
            
            echo "</div>";
            
        } else {
            echo "<p>No images found for this product</p>";
        }
        
    } else {
        echo "<p>No products found in database</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
