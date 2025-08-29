<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Modules\Products\Models\Product;

echo "=== Back Color Column Test ===\n\n";

try {
    // 1. Check if column exists
    $hasColumn = Schema::hasColumn('products', 'back_color');
    echo "1. back_color column exists: " . ($hasColumn ? "YES" : "NO") . "\n";
    
    if (!$hasColumn) {
        echo "❌ Column doesn't exist! Need to run migration.\n";
        exit;
    }
    
    // 2. Show table structure
    echo "\n2. Products table columns:\n";
    $columns = DB::select("SHOW COLUMNS FROM products WHERE Field LIKE '%color%'");
    foreach ($columns as $col) {
        echo "   - {$col->Field} ({$col->Type}) - Null: {$col->Null} - Default: {$col->Default}\n";
    }
    
    // 3. Test a sample product
    $product = Product::first();
    if ($product) {
        echo "\n3. Sample product test:\n";
        echo "   Product ID: {$product->id}\n";
        echo "   Product Name: {$product->name}\n";
        echo "   Current back_color: " . ($product->back_color ?? 'NULL') . "\n";
        
        // 4. Test update
        echo "\n4. Testing update...\n";
        $testColor = '#FF6B35';
        echo "   Setting color to: $testColor\n";
        
        $result = $product->update(['back_color' => $testColor]);
        echo "   Update result: " . ($result ? "SUCCESS" : "FAILED") . "\n";
        
        // 5. Verify update
        $product->refresh();
        echo "   Verified color: " . ($product->back_color ?? 'NULL') . "\n";
        
        // 6. Test with different color
        $testColor2 = '#00FF00';
        echo "\n5. Testing another color: $testColor2\n";
        $result2 = $product->update(['back_color' => $testColor2]);
        echo "   Update result: " . ($result2 ? "SUCCESS" : "FAILED") . "\n";
        
        $product->refresh();
        echo "   Final color: " . ($product->back_color ?? 'NULL') . "\n";
        
        // 7. Check fillable
        echo "\n6. Checking fillable array:\n";
        $fillable = $product->getFillable();
        $hasBackColorInFillable = in_array('back_color', $fillable);
        echo "   back_color in fillable: " . ($hasBackColorInFillable ? "YES" : "NO") . "\n";
        
        if (!$hasBackColorInFillable) {
            echo "   ❌ back_color not in fillable array!\n";
            echo "   Current fillable: " . implode(', ', $fillable) . "\n";
        }
        
    } else {
        echo "\n❌ No products found in database.\n";
    }
    
    echo "\n✅ Test completed.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
?>
