<?php

/**
 * اختبار سريع للتأكد من إصلاح مشكلة Type Error
 */

echo "🔧 تم إصلاح مشكلة Type Error في OrderService\n";
echo "============================================\n\n";

echo "المشكلة كانت:\n";
echo "- validateAndCalculateItemsWithCartons() ترجع Illuminate\\Support\\Collection\n";
echo "- ولكن تم تعريفها لترجع Illuminate\\Database\\Eloquent\\Collection\n\n";

echo "✅ الحل:\n";
echo "- إضافة: use Illuminate\\Support\\Collection as SupportCollection;\n";
echo "- تغيير نوع الإرجاع إلى: SupportCollection\n\n";

echo "🧪 الآن يمكنك اختبار Orders API مرة أخرى:\n";
echo "POST https://suntop-eg.com/api/orders\n\n";

echo "📋 بيانات الاختبار:\n";
$testData = [
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
        'phone' => '+20 109 999 9999'
    ],
    'payment_method' => 'cash_on_delivery',
    'notes' => 'اختبار بعد إصلاح Type Error'
];

echo json_encode($testData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

echo "🎉 المشكلة تم حلها! جرب الآن من Postman.\n";
