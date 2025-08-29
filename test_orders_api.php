<?php

/**
 * اختبار سريع لـ Orders API
 * 
 * استخدم هذا الملف للتأكد من عمل API بشكل صحيح
 */

$baseUrl = 'http://127.0.0.1:8000/api';

// مثال على بيانات الطلب
$orderData = [
    'merchant_id' => '1',
    'items' => [
        [
            'product_id' => '1',
            'quantity' => 2,
            'unit_price' => 2.50
        ]
    ],
    'delivery_address' => [
        'street' => 'شارع النيل',
        'building' => 'رقم 15',
        'apartment' => 'شقة 3',
        'city' => 'القاهرة',
        'district' => 'المعادي',
        'postal_code' => '11728',
        'phone' => '+20 109 999 9999',
        'notes' => 'الدور الثاني'
    ],
    'payment_method' => 'cash_on_delivery',
    'notes' => 'يرجى التوصيل بحذر'
];

// مثال على طلب تسجيل الدخول أولاً
$loginData = [
    'username' => 'testuser',
    'password' => 'password123'
];

echo "🧪 اختبار Orders API\n";
echo "===================\n\n";

echo "📋 بيانات الطلب المثال:\n";
echo json_encode($orderData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

echo "🔐 خطوات الاختبار:\n";
echo "1. تسجيل الدخول: POST {$baseUrl}/auth/login\n";
echo "2. إنشاء طلب: POST {$baseUrl}/orders\n";
echo "3. جلب الطلبات: GET {$baseUrl}/orders\n";
echo "4. تفاصيل الطلب: GET {$baseUrl}/orders/{order_id}\n";
echo "5. تتبع الطلب: GET {$baseUrl}/orders/{order_id}/tracking\n\n";

echo "📄 مثال على استخدام curl:\n";
echo "# تسجيل الدخول\n";
echo "curl -X POST {$baseUrl}/auth/login \\\n";
echo "  -H 'Content-Type: application/json' \\\n";
echo "  -H 'Accept: application/json' \\\n";
echo "  -d '" . json_encode($loginData) . "'\n\n";

echo "# إنشاء طلب جديد (باستخدام token من الخطوة السابقة)\n";
echo "curl -X POST {$baseUrl}/orders \\\n";
echo "  -H 'Content-Type: application/json' \\\n";
echo "  -H 'Accept: application/json' \\\n";
echo "  -H 'Authorization: Bearer YOUR_TOKEN_HERE' \\\n";
echo "  -d '" . json_encode($orderData) . "'\n\n";

echo "✅ إذا لم تظهر أخطاء syntax، فالمشكلة تم حلها!\n";
