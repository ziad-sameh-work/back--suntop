<?php
/*
 * Test API endpoints for products with back_color
 * Access via: https://suntop-eg.com/test_api.php
 */

echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>API Test</title>";
echo "<style>body{font-family:Arial;padding:20px} .endpoint{background:#f5f5f5;padding:15px;margin:10px 0;border-radius:5px} .method{background:#007bff;color:white;padding:5px 10px;border-radius:3px;font-size:12px} .url{font-family:monospace;background:white;padding:5px;border:1px solid #ddd;margin:5px 0} .test-btn{background:#28a745;color:white;padding:8px 15px;border:none;border-radius:3px;cursor:pointer;margin:5px 0} .result{background:#fff;border:1px solid #ddd;padding:10px;max-height:400px;overflow-y:auto;white-space:pre-wrap;font-family:monospace;font-size:12px}</style>";
echo "<script>
function testAPI(url, resultId) {
    const resultDiv = document.getElementById(resultId);
    resultDiv.innerHTML = 'Loading...';
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            resultDiv.innerHTML = JSON.stringify(data, null, 2);
        })
        .catch(error => {
            resultDiv.innerHTML = 'Error: ' + error.message;
        });
}
</script>";
echo "</head><body>";

echo "<h1>🧪 API Endpoints Test</h1>";
echo "<p>اختبار جميع endpoints المنتجات للتأكد من وجود back_color</p>";

// Test endpoints
$endpoints = [
    [
        'name' => 'All Products',
        'method' => 'GET',
        'url' => '/api/products',
        'description' => 'جميع المنتجات مع back_color'
    ],
    [
        'name' => 'Featured Products', 
        'method' => 'GET',
        'url' => '/api/products/featured',
        'description' => 'المنتجات المميزة مع back_color'
    ],
    [
        'name' => 'Single Product',
        'method' => 'GET', 
        'url' => '/api/products/1',
        'description' => 'منتج واحد مع تفاصيل back_color'
    ],
    [
        'name' => 'Admin Products (API)',
        'method' => 'GET',
        'url' => '/api/admin/products',
        'description' => 'منتجات الإدارة (يحتاج authentication)'
    ]
];

foreach ($endpoints as $index => $endpoint) {
    echo "<div class='endpoint'>";
    echo "<h3>{$endpoint['name']}</h3>";
    echo "<p>{$endpoint['description']}</p>";
    echo "<span class='method'>{$endpoint['method']}</span>";
    echo "<div class='url'>{$endpoint['url']}</div>";
    echo "<button class='test-btn' onclick=\"testAPI('{$endpoint['url']}', 'result{$index}')\">Test API</button>";
    echo "<div id='result{$index}' class='result'>Click 'Test API' to see results</div>";
    echo "</div>";
}

// Direct database check
echo "<div class='endpoint'>";
echo "<h3>🔍 Database Direct Check</h3>";
echo "<p>فحص مباشر لقاعدة البيانات</p>";

try {
    require_once '../vendor/autoload.php';
    $app = require_once '../bootstrap/app.php';
    
    use Illuminate\Support\Facades\DB;
    use App\Modules\Products\Models\Product;
    
    echo "<h4>Sample Products from Database:</h4>";
    $products = Product::limit(5)->get(['id', 'name', 'back_color']);
    
    if ($products->count() > 0) {
        echo "<table style='width:100%;border-collapse:collapse'>";
        echo "<tr style='background:#f0f0f0'><th style='border:1px solid #ddd;padding:8px'>ID</th><th style='border:1px solid #ddd;padding:8px'>Name</th><th style='border:1px solid #ddd;padding:8px'>Back Color</th><th style='border:1px solid #ddd;padding:8px'>Visual</th></tr>";
        
        foreach ($products as $product) {
            echo "<tr>";
            echo "<td style='border:1px solid #ddd;padding:8px'>{$product->id}</td>";
            echo "<td style='border:1px solid #ddd;padding:8px'>" . substr($product->name, 0, 30) . "...</td>";
            echo "<td style='border:1px solid #ddd;padding:8px'>" . ($product->back_color ?? 'NULL') . "</td>";
            echo "<td style='border:1px solid #ddd;padding:8px'>";
            if ($product->back_color) {
                echo "<span style='background-color:{$product->back_color};padding:5px 15px;color:white;border-radius:3px'>{$product->back_color}</span>";
            } else {
                echo "<em>No color</em>";
            }
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color:red'>No products found in database!</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color:red'>Database Error: " . $e->getMessage() . "</p>";
}

echo "</div>";

// Postman collection
echo "<div class='endpoint'>";
echo "<h3>📮 Postman Collection</h3>";
echo "<p>لاستخدام مع Postman:</p>";

$postmanCollection = [
    'info' => [
        'name' => 'SunTop Products API',
        'description' => 'Test endpoints for products with back_color'
    ],
    'item' => []
];

$baseUrl = 'https://suntop-eg.com';

foreach ($endpoints as $endpoint) {
    $postmanCollection['item'][] = [
        'name' => $endpoint['name'],
        'request' => [
            'method' => $endpoint['method'],
            'header' => [],
            'url' => [
                'raw' => $baseUrl . $endpoint['url'],
                'host' => ['127.0.0.1'],
                'port' => '8000',
                'path' => explode('/', trim($endpoint['url'], '/'))
            ]
        ]
    ];
}

echo "<textarea style='width:100%;height:200px;font-family:monospace;font-size:11px'>";
echo json_encode($postmanCollection, JSON_PRETTY_PRINT);
echo "</textarea>";
echo "<p><small>Copy this JSON and import it into Postman</small></p>";
echo "</div>";

echo "<hr>";
echo "<p><strong>Admin Panel:</strong> <a href='/admin/products'>Go to Products Admin</a></p>";
echo "<p style='color:red;'><strong>⚠️ Delete this file after testing!</strong></p>";

echo "</body></html>";
?>
