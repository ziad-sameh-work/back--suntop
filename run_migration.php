<?php
/*
 * Simple script to run migration and ensure back_color column exists
 * Run from project root: php run_migration.php
 */

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

echo "=== Migration Runner ===\n";

try {
    // Check current state
    echo "1. Checking current state...\n";
    $hasColumn = Schema::hasColumn('products', 'back_color');
    echo "   back_color column exists: " . ($hasColumn ? "YES" : "NO") . "\n";
    
    if (!$hasColumn) {
        echo "\n2. Column missing - need to add it...\n";
        
        // Add the column directly via raw SQL
        echo "   Adding back_color column...\n";
        DB::statement("ALTER TABLE products ADD COLUMN back_color VARCHAR(255) NULL AFTER is_featured");
        
        // Verify
        $hasColumn = Schema::hasColumn('products', 'back_color');
        echo "   Column added: " . ($hasColumn ? "SUCCESS" : "FAILED") . "\n";
        
        if ($hasColumn) {
            echo "   Setting default values for existing products...\n";
            DB::table('products')->whereNull('back_color')->update(['back_color' => '#FFFFFF']);
            echo "   Default values set.\n";
        }
    } else {
        echo "\n2. Column already exists - checking data...\n";
        $nullCount = DB::table('products')->whereNull('back_color')->count();
        $totalCount = DB::table('products')->count();
        echo "   Total products: $totalCount\n";
        echo "   Products with NULL back_color: $nullCount\n";
        
        if ($nullCount > 0) {
            echo "   Setting default values for NULL entries...\n";
            DB::table('products')->whereNull('back_color')->update(['back_color' => '#FFFFFF']);
            echo "   Default values set.\n";
        }
    }
    
    echo "\n3. Final verification...\n";
    $finalCheck = Schema::hasColumn('products', 'back_color');
    echo "   back_color column exists: " . ($finalCheck ? "YES" : "NO") . "\n";
    
    if ($finalCheck) {
        // Show sample data
        $samples = DB::table('products')->select('id', 'name', 'back_color')->limit(3)->get();
        echo "   Sample products:\n";
        foreach ($samples as $product) {
            echo "     - ID {$product->id}: " . substr($product->name, 0, 30) . " -> " . ($product->back_color ?? 'NULL') . "\n";
        }
    }
    
    echo "\n✓ Migration process completed!\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>
