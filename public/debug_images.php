<?php
require_once '../vendor/autoload.php';

// Load Laravel app
$app = require_once '../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "<h2>🔍 Image Debug Console</h2>";
echo "<div style='background: #f5f5f5; padding: 10px; font-family: monospace; white-space: pre-line;'>";

try {
    // Get all products
    $products = \App\Modules\Products\Models\Product::take(5)->get();
    
    echo "Found " . $products->count() . " products to test\n\n";
    
    foreach ($products as $product) {
        echo "==== TESTING PRODUCT ID: " . $product->id . " ====\n";
        echo "Name: " . $product->name . "\n";
        
        // Test the first_image attribute - this will trigger our logs
        echo "Calling first_image attribute...\n";
        $firstImage = $product->first_image;
        echo "Result: " . $firstImage . "\n\n";
        
        echo "Raw images data: " . json_encode($product->images) . "\n";
        echo "Images count: " . (is_array($product->images) ? count($product->images) : 'Not array') . "\n\n";
        
        if ($product->images && is_array($product->images) && count($product->images) > 0) {
            foreach ($product->images as $index => $imagePath) {
                $fullPath = public_path($imagePath);
                echo "Image $index: $imagePath\n";
                echo "Full path: $fullPath\n";
                echo "Exists: " . (file_exists($fullPath) ? 'YES' : 'NO') . "\n";
                if (file_exists($fullPath)) {
                    echo "Size: " . filesize($fullPath) . " bytes\n";
                }
                echo "---\n";
            }
        }
        
        echo "\n" . str_repeat("=", 50) . "\n\n";
    }
    
    echo "\n🎯 Check Laravel logs for detailed debugging info!\n";
    echo "Log location: storage/logs/laravel.log\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "</div>";

echo "<h3>📝 How to check logs:</h3>";
echo "<ol>";
echo "<li>Open <code>storage/logs/laravel.log</code></li>";
echo "<li>Look for <code>=== Product first_image DEBUG START ===</code></li>";
echo "<li>Check all the debug information</li>";
echo "</ol>";
?>
