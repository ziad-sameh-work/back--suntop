<?php
/*
 * Simple test to verify product update functionality
 * Run this file from project root
 */

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Modules\Products\Models\Product;

echo "=== Product Update Test ===\n";

try {
    // 1. Check environment
    echo "1. Environment Check:\n";
    echo "   - Laravel loaded: ✓\n";
    echo "   - Database connected: " . (DB::connection()->getPdo() ? "✓" : "✗") . "\n";
    echo "   - back_color column exists: " . (Schema::hasColumn('products', 'back_color') ? "✓" : "✗") . "\n";
    
    // 2. Get first product
    $product = Product::first();
    if (!$product) {
        echo "   - No products found!\n";
        exit;
    }
    
    echo "   - Test product: {$product->name} (ID: {$product->id})\n";
    echo "   - Current back_color: " . ($product->back_color ?? 'NULL') . "\n";
    
    // 3. Test fillable
    $fillable = $product->getFillable();
    $inFillable = in_array('back_color', $fillable);
    echo "   - back_color in fillable: " . ($inFillable ? "✓" : "✗") . "\n";
    
    if (!$inFillable) {
        echo "   ERROR: back_color not in fillable array!\n";
        echo "   Current fillable: " . implode(', ', $fillable) . "\n";
        exit;
    }
    
    // 4. Test update
    echo "\n2. Update Test:\n";
    $originalColor = $product->back_color;
    $testColors = ['#FF6B35', '#00AA00', '#0066CC'];
    
    foreach ($testColors as $i => $testColor) {
        echo "   Test " . ($i + 1) . ": Setting color to $testColor\n";
        
        // Try update method
        $result = $product->update(['back_color' => $testColor]);
        echo "     - Update result: " . ($result ? "SUCCESS" : "FAILED") . "\n";
        
        // Verify
        $product->refresh();
        $currentColor = $product->back_color;
        echo "     - Verified color: " . ($currentColor ?? 'NULL') . "\n";
        
        if ($currentColor === $testColor) {
            echo "     - Status: ✓ PASS\n";
        } else {
            echo "     - Status: ✗ FAIL\n";
        }
        
        sleep(1); // Small delay
    }
    
    // 5. Restore original
    if ($originalColor !== null) {
        $product->update(['back_color' => $originalColor]);
        echo "\n3. Restored original color: $originalColor\n";
    } else {
        $product->update(['back_color' => null]);
        echo "\n3. Restored original color: NULL\n";
    }
    
    echo "\n✓ Test completed successfully!\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>
