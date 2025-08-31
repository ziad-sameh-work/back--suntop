<?php
require_once '../vendor/autoload.php';

// Load Laravel app
$app = require_once '../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "<h2>Product Creation Test</h2>";

// Test 1: Check if uploads directory exists and is writable
echo "<h3>1. Upload Directory Check</h3>";
$uploadDir = __DIR__ . '/uploads/products';
echo "Upload directory: " . $uploadDir . "<br>";
echo "Exists: " . (is_dir($uploadDir) ? '<span style="color:green">YES</span>' : '<span style="color:red">NO</span>') . "<br>";
echo "Writable: " . (is_writable($uploadDir) ? '<span style="color:green">YES</span>' : '<span style="color:red">NO</span>') . "<br>";

// Test 2: Check Product model fillable fields
echo "<h3>2. Product Model Check</h3>";
try {
    $product = new \App\Modules\Products\Models\Product();
    $fillable = $product->getFillable();
    echo "Fillable fields: " . implode(', ', $fillable) . "<br>";
    
    // Check if required fields are present
    $requiredFields = ['name', 'description', 'price', 'back_color', 'images', 'is_available'];
    $missingFields = array_diff($requiredFields, $fillable);
    
    if (empty($missingFields)) {
        echo "<span style='color:green'>All required fields are fillable</span><br>";
    } else {
        echo "<span style='color:red'>Missing fillable fields: " . implode(', ', $missingFields) . "</span><br>";
    }
} catch (Exception $e) {
    echo "<span style='color:red'>Error loading Product model: " . $e->getMessage() . "</span><br>";
}

// Test 3: Check database connection and products table
echo "<h3>3. Database Check</h3>";
try {
    $app->make('db');
    echo "<span style='color:green'>Database connection: OK</span><br>";
    
    // Check if products table exists
    $tableExists = \Illuminate\Support\Facades\Schema::hasTable('products');
    echo "Products table exists: " . ($tableExists ? '<span style="color:green">YES</span>' : '<span style="color:red">NO</span>') . "<br>";
    
    if ($tableExists) {
        // Check products table columns
        $columns = \Illuminate\Support\Facades\Schema::getColumnListing('products');
        echo "Products table columns: " . implode(', ', $columns) . "<br>";
        
        // Check specific columns
        $expectedColumns = ['name', 'description', 'price', 'back_color', 'images', 'is_available', 'category_id'];
        $missingColumns = array_diff($expectedColumns, $columns);
        
        if (empty($missingColumns)) {
            echo "<span style='color:green'>All required columns exist</span><br>";
        } else {
            echo "<span style='color:red'>Missing columns: " . implode(', ', $missingColumns) . "</span><br>";
        }
    }
} catch (Exception $e) {
    echo "<span style='color:red'>Database error: " . $e->getMessage() . "</span><br>";
}

// Test 4: Check product categories
echo "<h3>4. Product Categories Check</h3>";
try {
    $categoriesExist = \Illuminate\Support\Facades\Schema::hasTable('product_categories');
    echo "Product categories table exists: " . ($categoriesExist ? '<span style="color:green">YES</span>' : '<span style="color:red">NO</span>') . "<br>";
    
    if ($categoriesExist) {
        $categories = \App\Modules\Products\Models\ProductCategory::where('is_active', true)->get();
        echo "Active categories count: " . $categories->count() . "<br>";
        
        if ($categories->count() > 0) {
            echo "Categories:<br>";
            foreach ($categories as $category) {
                echo "- " . $category->display_name . " (ID: " . $category->id . ")<br>";
            }
        } else {
            echo "<span style='color:orange'>No active categories found</span><br>";
        }
    }
} catch (Exception $e) {
    echo "<span style='color:red'>Categories error: " . $e->getMessage() . "</span><br>";
}

// Test 5: Create a test file
echo "<h3>5. File Creation Test</h3>";
$testFile = $uploadDir . '/test_' . time() . '.txt';
if (file_put_contents($testFile, 'Test file content')) {
    echo "<span style='color:green'>File creation test: SUCCESS</span><br>";
    unlink($testFile);
} else {
    echo "<span style='color:red'>File creation test: FAILED</span><br>";
}

echo "<hr>";
echo "<p>If all tests pass, product creation should work. Check the Laravel logs in storage/logs/ for detailed error messages.</p>";
?>
