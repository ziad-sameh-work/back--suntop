<?php
/*
 * Debug script to check back_color column and data
 * Place this in project root and access via browser: http://127.0.0.1:8000/debug_back_color.php
 */

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Modules\Products\Models\Product;

try {
    echo "<h1>🔍 Debug: back_color Column</h1>";
    
    // Check if back_color column exists
    $hasBackColorColumn = Schema::hasColumn('products', 'back_color');
    echo "<h2>1. Column Existence Check:</h2>";
    echo "back_color column exists: " . ($hasBackColorColumn ? "✅ YES" : "❌ NO") . "<br>";
    
    // Show all columns in products table
    echo "<h2>2. All Products Table Columns:</h2>";
    $columns = DB::select("SHOW COLUMNS FROM products");
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Default</th></tr>";
    foreach ($columns as $column) {
        $highlight = $column->Field === 'back_color' ? 'style="background-color: yellow;"' : '';
        echo "<tr $highlight>";
        echo "<td>{$column->Field}</td>";
        echo "<td>{$column->Type}</td>";
        echo "<td>{$column->Null}</td>";
        echo "<td>{$column->Default}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Check sample products with back_color values
    echo "<h2>3. Sample Products with back_color:</h2>";
    $products = DB::table('products')
        ->select('id', 'name', 'back_color')
        ->limit(10)
        ->get();
    
    if ($products->count() > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Product Name</th><th>Back Color</th></tr>";
        foreach ($products as $product) {
            echo "<tr>";
            echo "<td>{$product->id}</td>";
            echo "<td>{$product->name}</td>";
            echo "<td>";
            if ($product->back_color) {
                echo "<span style='background-color: {$product->back_color}; padding: 5px; color: white;'>{$product->back_color}</span>";
            } else {
                echo "<em>NULL</em>";
            }
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No products found in the database.</p>";
    }
    
    // Test update functionality
    echo "<h2>4. Test Update (if products exist):</h2>";
    $firstProduct = Product::first();
    if ($firstProduct) {
        $testColor = '#FF6B35'; // Suntop orange
        
        echo "<p>Testing update on product ID: {$firstProduct->id}</p>";
        echo "<p>Product Name: {$firstProduct->name}</p>";
        echo "<p>Current back_color: " . ($firstProduct->back_color ?? 'NULL') . "</p>";
        echo "<p>Setting new color: $testColor</p>";
        
        if ($hasBackColorColumn) {
            // Test using Eloquent model
            $firstProduct->back_color = $testColor;
            $saved = $firstProduct->save();
            
            echo "<p>Eloquent save result: " . ($saved ? "✅ SUCCESS" : "❌ FAILED") . "</p>";
            
            // Verify the update
            $firstProduct->refresh();
            echo "<p>Verified color (Eloquent): " . ($firstProduct->back_color ?? 'NULL') . "</p>";
            
            // Also test with update method
            $testColor2 = '#00AA00';
            echo "<p>Testing update() method with color: $testColor2</p>";
            $updated = $firstProduct->update(['back_color' => $testColor2]);
            echo "<p>Update method result: " . ($updated ? "✅ SUCCESS" : "❌ FAILED") . "</p>";
            
            $firstProduct->refresh();
            echo "<p>Final verified color: " . ($firstProduct->back_color ?? 'NULL') . "</p>";
            
            // Check fillable
            echo "<h3>Fillable Check:</h3>";
            $fillable = $firstProduct->getFillable();
            $inFillable = in_array('back_color', $fillable);
            echo "<p>back_color in fillable: " . ($inFillable ? "✅ YES" : "❌ NO") . "</p>";
            
            if (!$inFillable) {
                echo "<p style='color: red;'>❌ PROBLEM: back_color is NOT in fillable array!</p>";
                echo "<p>Current fillable fields: " . implode(', ', $fillable) . "</p>";
            }
            
        } else {
            echo "<p>❌ Cannot test update - column doesn't exist</p>";
        }
    } else {
        echo "<p>No products available for testing.</p>";
    }
    
    echo "<h2>✅ Debug Complete</h2>";
    echo "<p><a href='/admin/products'>Go to Products Admin</a></p>";
    echo "<p style='color: red;'>⚠️ Remember to delete this debug file after use!</p>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>❌ Error:</h2>";
    echo "<p>{$e->getMessage()}</p>";
    echo "<pre>{$e->getTraceAsString()}</pre>";
}
?>

