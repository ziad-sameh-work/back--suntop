<?php
/*
 * Add back_color column to products table if missing
 * Access via: http://127.0.0.1:8000/add_back_color_column.php
 */

echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Add Back Color Column</title>";
echo "<style>body{font-family:Arial;padding:20px} .ok{color:green} .error{color:red} .warning{color:orange} .info{background:#e7f3ff;padding:10px;border-radius:5px;margin:10px 0}</style>";
echo "</head><body>";

echo "<h1>🔧 Add back_color Column to Products Table</h1>";

try {
    require_once '../vendor/autoload.php';
    $app = require_once '../bootstrap/app.php';
    
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Schema;
    use App\Modules\Products\Models\Product;
    
    echo "<h2>1. Current Status Check</h2>";
    
    // Check if column exists
    $hasColumn = Schema::hasColumn('products', 'back_color');
    echo "<p class='" . ($hasColumn ? 'ok' : 'error') . "'>";
    echo ($hasColumn ? '✅' : '❌') . " back_color column exists: " . ($hasColumn ? 'YES' : 'NO');
    echo "</p>";
    
    if (!$hasColumn) {
        echo "<h2>2. Adding Column</h2>";
        
        try {
            // Add the column
            DB::statement("ALTER TABLE products ADD COLUMN back_color VARCHAR(255) NULL AFTER is_featured COMMENT 'Background color for product display'");
            echo "<p class='ok'>✅ Column added successfully!</p>";
            
            // Verify
            $hasColumnAfter = Schema::hasColumn('products', 'back_color');
            echo "<p class='" . ($hasColumnAfter ? 'ok' : 'error') . "'>";
            echo ($hasColumnAfter ? '✅' : '❌') . " Verification: " . ($hasColumnAfter ? 'SUCCESS' : 'FAILED');
            echo "</p>";
            
        } catch (Exception $e) {
            echo "<p class='error'>❌ Failed to add column: " . $e->getMessage() . "</p>";
        }
    }
    
    // Check products data
    echo "<h2>3. Products Data Check</h2>";
    $totalProducts = Product::count();
    $productsWithColor = Product::whereNotNull('back_color')->count();
    $productsWithoutColor = $totalProducts - $productsWithColor;
    
    echo "<div class='info'>";
    echo "<p><strong>Total Products:</strong> $totalProducts</p>";
    echo "<p><strong>Products with back_color:</strong> $productsWithColor</p>";
    echo "<p><strong>Products without back_color:</strong> $productsWithoutColor</p>";
    echo "</div>";
    
    if ($productsWithoutColor > 0) {
        echo "<h2>4. Setting Default Colors</h2>";
        
        if (isset($_POST['set_default_colors'])) {
            $defaultColor = $_POST['default_color'] ?? '#FFFFFF';
            
            $updated = Product::whereNull('back_color')->update(['back_color' => $defaultColor]);
            echo "<p class='ok'>✅ Updated $updated products with default color: $defaultColor</p>";
            
            // Refresh counts
            $productsWithColor = Product::whereNotNull('back_color')->count();
            $productsWithoutColor = $totalProducts - $productsWithColor;
            echo "<p class='ok'>✅ Now $productsWithColor products have colors, $productsWithoutColor without color</p>";
            
        } else {
            echo "<form method='POST'>";
            echo "<p>Set default color for products without back_color:</p>";
            echo "<input type='color' name='default_color' value='#FFFFFF'>";
            echo "<input type='hidden' name='set_default_colors' value='1'>";
            echo "<button type='submit' style='background:#007bff;color:white;padding:8px 15px;border:none;border-radius:3px;margin-left:10px'>Set Default Colors</button>";
            echo "</form>";
        }
    }
    
    // Test update functionality
    echo "<h2>5. Test Update Functionality</h2>";
    
    if (isset($_POST['test_update'])) {
        $productId = $_POST['product_id'];
        $testColor = $_POST['test_color'];
        
        $product = Product::find($productId);
        if ($product) {
            $originalColor = $product->back_color;
            
            echo "<p><strong>Testing Product:</strong> {$product->name} (ID: {$product->id})</p>";
            echo "<p><strong>Original Color:</strong> " . ($originalColor ?? 'NULL') . "</p>";
            echo "<p><strong>Test Color:</strong> $testColor</p>";
            
            // Try update
            $result = $product->update(['back_color' => $testColor]);
            echo "<p class='" . ($result ? 'ok' : 'error') . "'>";
            echo ($result ? '✅' : '❌') . " Update result: " . ($result ? 'SUCCESS' : 'FAILED');
            echo "</p>";
            
            // Verify
            $product->refresh();
            $newColor = $product->back_color;
            echo "<p><strong>Verified Color:</strong> " . ($newColor ?? 'NULL') . "</p>";
            
            if ($newColor === $testColor) {
                echo "<p class='ok'>✅ Update test PASSED!</p>";
            } else {
                echo "<p class='error'>❌ Update test FAILED!</p>";
            }
            
            // Restore original
            if ($originalColor !== $testColor) {
                $product->update(['back_color' => $originalColor]);
                echo "<p class='ok'>✅ Original color restored</p>";
            }
            
        } else {
            echo "<p class='error'>❌ Product not found!</p>";
        }
        
    } else {
        $firstProduct = Product::first();
        if ($firstProduct) {
            echo "<form method='POST'>";
            echo "<p>Test update functionality on product: <strong>{$firstProduct->name}</strong></p>";
            echo "<input type='hidden' name='product_id' value='{$firstProduct->id}'>";
            echo "<input type='color' name='test_color' value='#FF6B35'>";
            echo "<input type='hidden' name='test_update' value='1'>";
            echo "<button type='submit' style='background:#28a745;color:white;padding:8px 15px;border:none;border-radius:3px;margin-left:10px'>Test Update</button>";
            echo "</form>";
        }
    }
    
    // Sample products display
    echo "<h2>6. Sample Products</h2>";
    $sampleProducts = Product::limit(5)->get(['id', 'name', 'back_color']);
    
    if ($sampleProducts->count() > 0) {
        echo "<table style='width:100%;border-collapse:collapse'>";
        echo "<tr style='background:#f0f0f0'><th style='border:1px solid #ddd;padding:8px'>ID</th><th style='border:1px solid #ddd;padding:8px'>Name</th><th style='border:1px solid #ddd;padding:8px'>Back Color</th><th style='border:1px solid #ddd;padding:8px'>Visual</th></tr>";
        
        foreach ($sampleProducts as $product) {
            echo "<tr>";
            echo "<td style='border:1px solid #ddd;padding:8px'>{$product->id}</td>";
            echo "<td style='border:1px solid #ddd;padding:8px'>" . substr($product->name, 0, 40) . "...</td>";
            echo "<td style='border:1px solid #ddd;padding:8px'>" . ($product->back_color ?? 'NULL') . "</td>";
            echo "<td style='border:1px solid #ddd;padding:8px'>";
            if ($product->back_color) {
                echo "<span style='background-color:{$product->back_color};padding:8px 15px;color:white;border-radius:3px;display:inline-block'>{$product->back_color}</span>";
            } else {
                echo "<em style='color:#999'>No color set</em>";
            }
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<h2>✅ Setup Complete!</h2>";
    echo "<div class='info'>";
    echo "<p><strong>Next Steps:</strong></p>";
    echo "<ul>";
    echo "<li>Test API endpoints: <a href='/test_api.php'>Test API</a></li>";
    echo "<li>Go to admin panel: <a href='/admin/products'>Products Admin</a></li>";
    echo "<li>Test product editing with back_color field</li>";
    echo "</ul>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr>";
echo "<p style='color:red;'><strong>⚠️ Delete this file after setup is complete!</strong></p>";

echo "</body></html>";
?>
