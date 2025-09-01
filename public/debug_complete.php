<?php
/*
 * Complete diagnostic tool for back_color issue
 * Access via: https://suntop-eg.com/debug_complete.php
 */

// HTML header
echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Back Color Debug</title>";
echo "<style>body{font-family:Arial;padding:20px} .ok{color:green} .error{color:red} .warning{color:orange} table{border-collapse:collapse;width:100%} th,td{border:1px solid #ddd;padding:8px;text-align:left} th{background-color:#f2f2f2}</style>";
echo "</head><body>";

echo "<h1>🔍 Complete Back Color Diagnostic</h1>";

// Include Laravel
try {
    require_once '../vendor/autoload.php';
    $app = require_once '../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    echo "<p class='ok'>✅ Laravel loaded successfully</p>";
} catch (Exception $e) {
    echo "<p class='error'>❌ Laravel load failed: " . $e->getMessage() . "</p>";
    exit;
}

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Modules\Products\Models\Product;

// Test 1: Database Connection
echo "<h2>1. Database Connection Test</h2>";
try {
    DB::connection()->getPdo();
    echo "<p class='ok'>✅ Database connected</p>";
} catch (Exception $e) {
    echo "<p class='error'>❌ Database connection failed: " . $e->getMessage() . "</p>";
    exit;
}

// Test 2: Products Table Structure
echo "<h2>2. Products Table Structure</h2>";
try {
    $columns = DB::select("SHOW COLUMNS FROM products");
    $hasBackColor = false;
    
    echo "<table><tr><th>Field</th><th>Type</th><th>Null</th><th>Default</th><th>Extra</th></tr>";
    foreach ($columns as $col) {
        if ($col->Field === 'back_color') {
            $hasBackColor = true;
            echo "<tr style='background-color: #ffffcc;'>";
        } else {
            echo "<tr>";
        }
        echo "<td>{$col->Field}</td>";
        echo "<td>{$col->Type}</td>";
        echo "<td>{$col->Null}</td>";
        echo "<td>" . ($col->Default ?? 'NULL') . "</td>";
        echo "<td>{$col->Extra}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    if ($hasBackColor) {
        echo "<p class='ok'>✅ back_color column EXISTS in database</p>";
    } else {
        echo "<p class='error'>❌ back_color column MISSING from database</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Error checking table structure: " . $e->getMessage() . "</p>";
}

// Test 3: Laravel Schema Check
echo "<h2>3. Laravel Schema Check</h2>";
$hasColumnLaravel = Schema::hasColumn('products', 'back_color');
echo "<p class='" . ($hasColumnLaravel ? 'ok' : 'error') . "'>";
echo ($hasColumnLaravel ? '✅' : '❌') . " Schema::hasColumn('products', 'back_color'): " . ($hasColumnLaravel ? 'TRUE' : 'FALSE');
echo "</p>";

// Test 4: Product Model Check
echo "<h2>4. Product Model Analysis</h2>";
try {
    $product = new Product();
    $fillable = $product->getFillable();
    $inFillable = in_array('back_color', $fillable);
    
    echo "<p class='" . ($inFillable ? 'ok' : 'error') . "'>";
    echo ($inFillable ? '✅' : '❌') . " back_color in fillable: " . ($inFillable ? 'YES' : 'NO');
    echo "</p>";
    
    echo "<h3>All Fillable Fields:</h3>";
    echo "<p>" . implode(', ', $fillable) . "</p>";
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Error checking Product model: " . $e->getMessage() . "</p>";
}

// Test 5: Sample Products Data
echo "<h2>5. Sample Products Data</h2>";
try {
    $products = Product::limit(5)->get(['id', 'name', 'back_color']);
    
    if ($products->count() > 0) {
        echo "<table><tr><th>ID</th><th>Name</th><th>Back Color</th><th>Visual</th></tr>";
        foreach ($products as $product) {
            echo "<tr>";
            echo "<td>{$product->id}</td>";
            echo "<td>" . substr($product->name, 0, 30) . "...</td>";
            echo "<td>" . ($product->back_color ?? 'NULL') . "</td>";
            echo "<td>";
            if ($product->back_color) {
                echo "<span style='background-color: {$product->back_color}; padding: 5px 15px; color: white; border-radius: 3px;'>{$product->back_color}</span>";
            } else {
                echo "<em>No color</em>";
            }
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='warning'>⚠️ No products found in database</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Error fetching products: " . $e->getMessage() . "</p>";
}

// Test 6: Update Test
echo "<h2>6. Update Functionality Test</h2>";
try {
    $testProduct = Product::first();
    if ($testProduct) {
        $originalColor = $testProduct->back_color;
        $testColor = '#FF6B35';
        
        echo "<p><strong>Test Product:</strong> {$testProduct->name} (ID: {$testProduct->id})</p>";
        echo "<p><strong>Original Color:</strong> " . ($originalColor ?? 'NULL') . "</p>";
        echo "<p><strong>Test Color:</strong> $testColor</p>";
        
        // Test direct assignment
        echo "<h3>Direct Assignment Test:</h3>";
        $testProduct->back_color = $testColor;
        $saved = $testProduct->save();
        echo "<p class='" . ($saved ? 'ok' : 'error') . "'>";
        echo ($saved ? '✅' : '❌') . " Direct save: " . ($saved ? 'SUCCESS' : 'FAILED');
        echo "</p>";
        
        // Verify
        $testProduct->refresh();
        $currentColor = $testProduct->back_color;
        echo "<p><strong>Verified Color:</strong> " . ($currentColor ?? 'NULL') . "</p>";
        
        if ($currentColor === $testColor) {
            echo "<p class='ok'>✅ Direct assignment works correctly!</p>";
        } else {
            echo "<p class='error'>❌ Direct assignment failed - color not saved</p>";
        }
        
        // Test update method
        echo "<h3>Update Method Test:</h3>";
        $testColor2 = '#00AA00';
        $updated = $testProduct->update(['back_color' => $testColor2]);
        echo "<p class='" . ($updated ? 'ok' : 'error') . "'>";
        echo ($updated ? '✅' : '❌') . " Update method: " . ($updated ? 'SUCCESS' : 'FAILED');
        echo "</p>";
        
        $testProduct->refresh();
        $finalColor = $testProduct->back_color;
        echo "<p><strong>Final Color:</strong> " . ($finalColor ?? 'NULL') . "</p>";
        
        if ($finalColor === $testColor2) {
            echo "<p class='ok'>✅ Update method works correctly!</p>";
        } else {
            echo "<p class='error'>❌ Update method failed - color not saved</p>";
        }
        
        // Restore original color
        if ($originalColor !== null) {
            $testProduct->update(['back_color' => $originalColor]);
            echo "<p class='ok'>✅ Original color restored</p>";
        }
        
    } else {
        echo "<p class='warning'>⚠️ No products available for testing</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Error during update test: " . $e->getMessage() . "</p>";
}

// Test 7: Form Simulation
echo "<h2>7. Form Submission Simulation</h2>";
if (isset($_POST['test_form_submit']) && isset($_POST['back_color'])) {
    echo "<h3>Form Submitted!</h3>";
    $submittedColor = $_POST['back_color'];
    echo "<p><strong>Submitted Color:</strong> $submittedColor</p>";
    
    try {
        $testProduct = Product::first();
        if ($testProduct) {
            $result = $testProduct->update(['back_color' => $submittedColor]);
            echo "<p class='" . ($result ? 'ok' : 'error') . "'>";
            echo ($result ? '✅' : '❌') . " Form update: " . ($result ? 'SUCCESS' : 'FAILED');
            echo "</p>";
            
            $testProduct->refresh();
            echo "<p><strong>Saved Color:</strong> " . ($testProduct->back_color ?? 'NULL') . "</p>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>❌ Form update error: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<form method='POST'>";
    echo "<input type='hidden' name='test_form_submit' value='1'>";
    echo "<label>Test Color: <input type='color' name='back_color' value='#FF6B35'></label>";
    echo "<button type='submit'>Test Form Submit</button>";
    echo "</form>";
}

// Test 8: Laravel Log Check
echo "<h2>8. Recent Laravel Logs</h2>";
try {
    $logFile = storage_path('logs/laravel.log');
    if (file_exists($logFile)) {
        $logContent = file_get_contents($logFile);
        $productUpdateLogs = [];
        
        // Find product update logs
        if (preg_match_all('/.*Product Update.*back_color.*/', $logContent, $matches)) {
            $productUpdateLogs = array_slice($matches[0], -5); // Last 5 entries
        }
        
        if (!empty($productUpdateLogs)) {
            echo "<h3>Recent Product Update Logs:</h3>";
            echo "<pre style='background-color: #f5f5f5; padding: 10px; border-radius: 5px;'>";
            foreach ($productUpdateLogs as $log) {
                echo htmlspecialchars($log) . "\n";
            }
            echo "</pre>";
        } else {
            echo "<p class='warning'>⚠️ No recent product update logs found</p>";
        }
    } else {
        echo "<p class='warning'>⚠️ Laravel log file not found</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Error reading logs: " . $e->getMessage() . "</p>";
}

// Summary
echo "<h2>🎯 Summary & Recommendations</h2>";

if ($hasBackColor && $hasColumnLaravel && $inFillable) {
    echo "<div style='background-color: #d4edda; padding: 15px; border-radius: 5px; border: 1px solid #c3e6cb;'>";
    echo "<h3 style='color: #155724;'>✅ All Checks Passed</h3>";
    echo "<p>The back_color functionality should work correctly. If you're still experiencing issues:</p>";
    echo "<ul>";
    echo "<li>Clear Laravel cache: <code>php artisan cache:clear</code></li>";
    echo "<li>Clear config cache: <code>php artisan config:clear</code></li>";
    echo "<li>Check browser console for JavaScript errors</li>";
    echo "<li>Ensure you're submitting the form with the correct field name</li>";
    echo "</ul>";
    echo "</div>";
} else {
    echo "<div style='background-color: #f8d7da; padding: 15px; border-radius: 5px; border: 1px solid #f5c6cb;'>";
    echo "<h3 style='color: #721c24;'>❌ Issues Found</h3>";
    echo "<ul>";
    if (!$hasBackColor) echo "<li>back_color column missing from database - run migration</li>";
    if (!$hasColumnLaravel) echo "<li>Laravel Schema check failed - cache issue?</li>";
    if (!$inFillable) echo "<li>back_color not in fillable array - check Product model</li>";
    echo "</ul>";
    echo "</div>";
}

echo "<hr>";
echo "<p><strong>Admin Panel:</strong> <a href='/admin/products'>Go to Products</a></p>";
echo "<p><strong>Debug Script:</strong> <a href='/debug_back_color.php'>Alternative Debug</a></p>";
echo "<p style='color: red;'><strong>⚠️ Delete this file after debugging!</strong></p>";

echo "</body></html>";
?>
