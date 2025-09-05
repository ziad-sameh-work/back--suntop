<?php
// Simple test script to check API endpoints
echo "=== اختبار API Endpoints ===\n\n";

$baseUrl = 'http://localhost/back-suntop/api';

// Test endpoints
$endpoints = [
    'GET /test/chat/1/messages' => $baseUrl . '/test/chat/1/messages',
    'POST /test/create-chat' => $baseUrl . '/test/create-chat',
    'POST /test/send-message' => $baseUrl . '/test/send-message',
    'POST /test/pusher-broadcast' => $baseUrl . '/test/pusher-broadcast'
];

foreach ($endpoints as $name => $url) {
    echo "🔍 اختبار: $name\n";
    echo "URL: $url\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true); // HEAD request only
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        echo "❌ خطأ: $error\n";
    } else {
        if ($httpCode == 200) {
            echo "✅ متاح (HTTP $httpCode)\n";
        } elseif ($httpCode == 404) {
            echo "❌ غير موجود (HTTP $httpCode)\n";
        } elseif ($httpCode == 405) {
            echo "⚠️ Method غير مسموح (HTTP $httpCode) - لكن الـ route موجود\n";
        } else {
            echo "⚠️ HTTP $httpCode\n";
        }
    }
    echo "---\n";
}

echo "\n📋 ملاحظات:\n";
echo "- إذا كان HTTP 405: الـ route موجود لكن الـ method مختلف\n";
echo "- إذا كان HTTP 404: الـ route غير موجود\n";
echo "- إذا كان HTTP 200: الـ endpoint يعمل بشكل صحيح\n";
