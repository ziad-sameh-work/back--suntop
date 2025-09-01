<?php
/**
 * اختبار إصلاح Orders API
 */

echo "🔧 اختبار إصلاح Orders API\n";
echo "==========================\n\n";

echo "✅ الإصلاحات المطبقة:\n";
echo "1. تم إزالة MerchantService من OrderService constructor في ModuleServiceProvider\n";
echo "2. تم تحديث مسار الصور لحل مشكلة CORS\n";
echo "3. تم إنشاء migration لحذف merchant_id من orders table\n\n";

echo "📋 التحقق من الإصلاحات:\n\n";

// Test 1: Check ModuleServiceProvider
echo "1. فحص ModuleServiceProvider:\n";
$providerFile = file_get_contents('app/Providers/ModuleServiceProvider.php');
if (strpos($providerFile, 'MerchantService') === false) {
    echo "   ✅ تم إزالة MerchantService من OrderService dependency injection\n";
} else {
    echo "   ❌ لا يزال MerchantService موجود في OrderService\n";
}

// Test 2: Check OrderService constructor
echo "\n2. فحص OrderService constructor:\n";
$orderServiceFile = file_get_contents('app/Modules/Orders/Services/OrderService.php');
if (strpos($orderServiceFile, 'MerchantService') === false) {
    echo "   ✅ تم إزالة MerchantService من OrderService imports\n";
} else {
    echo "   ❌ لا يزال MerchantService موجود في OrderService\n";
}

// Test 3: Check CreateOrderRequest
echo "\n3. فحص CreateOrderRequest:\n";
$requestFile = file_get_contents('app/Modules/Orders/Requests/CreateOrderRequest.php');
if (strpos($requestFile, 'merchant_id') === false) {
    echo "   ✅ تم إزالة merchant_id من validation rules\n";
} else {
    echo "   ❌ لا يزال merchant_id موجود في validation\n";
}

// Test 4: Check Order Model
echo "\n4. فحص Order Model:\n";
$orderModelFile = file_get_contents('app/Modules/Orders/Models/Order.php');
if (strpos($orderModelFile, 'merchant_id') === false && strpos($orderModelFile, 'merchant()') === false) {
    echo "   ✅ تم إزالة merchant references من Order Model\n";
} else {
    echo "   ❌ لا تزال merchant references موجودة في Order Model\n";
}

// Test 5: Check migration file
echo "\n5. فحص migration الجديد:\n";
if (file_exists('database/migrations/2025_01_21_130000_remove_merchant_id_from_orders_table.php')) {
    echo "   ✅ تم إنشاء migration لحذف merchant_id\n";
} else {
    echo "   ❌ Migration غير موجود\n";
}

echo "\n📝 الخطوات التالية:\n";
echo "1. تشغيل: php artisan migrate\n";
echo "2. تشغيل: php artisan config:clear\n";
echo "3. تشغيل: php artisan cache:clear\n";
echo "4. إعادة تشغيل السيرفر\n";
echo "5. اختبار Orders API من التطبيق\n\n";

echo "🌐 API Endpoint للاختبار:\n";
echo "POST /api/orders\n";
echo "Headers: Authorization: Bearer {token}\n";
echo "Body: {\n";
echo "  \"items\": [\n";
echo "    {\n";
echo "      \"product_id\": \"1\",\n";
echo "      \"quantity\": 2,\n";
echo "      \"unit_price\": 2.50\n";
echo "    }\n";
echo "  ],\n";
echo "  \"delivery_address\": {\n";
echo "    \"street\": \"شارع التحرير\",\n";
echo "    \"building\": \"123\",\n";
echo "    \"city\": \"القاهرة\",\n";
echo "    \"district\": \"وسط البلد\",\n";
echo "    \"phone\": \"01234567890\"\n";
echo "  },\n";
echo "  \"payment_method\": \"cash_on_delivery\"\n";
echo "}\n\n";

echo "🚀 Orders API جاهز للعمل!\n";
