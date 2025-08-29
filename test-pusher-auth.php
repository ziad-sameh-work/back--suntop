<?php

// Simple test script for Pusher authentication
echo "🔧 Testing Pusher Authentication...\n";

$baseUrl = 'http://127.0.0.1:8000';

// Test data
$testData = [
    'channel_name' => 'private-admin.chats',
    'socket_id' => 'test-socket-123'
];

echo "📡 Testing /api/broadcasting/auth endpoint...\n";

// Test without auth (should fail)
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/api/broadcasting/auth');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($testData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded',
    'Accept: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status: $httpCode\n";
echo "Response: $response\n";

if ($httpCode === 401) {
    echo "✅ Authentication endpoint working correctly (requires auth)\n";
} else {
    echo "❌ Unexpected response from auth endpoint\n";
}

echo "\n📝 To test with real authentication:\n";
echo "1. Login to admin panel\n";
echo "2. Get API token from Laravel Sanctum\n";
echo "3. Use token in Authorization header\n";
echo "\n🎯 Ready for Pusher real-time testing!\n";
