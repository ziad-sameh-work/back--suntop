<?php

echo "🔍 SIMPLE BROADCAST TEST\n";
echo "========================\n\n";

// Test 1: Check if Laravel can load
echo "1. Testing Laravel bootstrap...\n";
try {
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    echo "   ✅ Laravel loaded successfully\n";
} catch (Exception $e) {
    echo "   ❌ Laravel failed to load: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 2: Check broadcasting config
echo "\n2. Testing broadcasting config...\n";
$driver = config('broadcasting.default');
echo "   BROADCAST_DRIVER: {$driver}\n";

if ($driver !== 'pusher') {
    echo "   ❌ Driver should be 'pusher', not '{$driver}'\n";
    echo "   Fix: Set BROADCAST_DRIVER=pusher in .env\n";
    exit(1);
} else {
    echo "   ✅ Broadcasting driver is pusher\n";
}

// Test 3: Check Pusher config
echo "\n3. Testing Pusher config...\n";
$pusherConfig = config('broadcasting.connections.pusher');
if (!$pusherConfig) {
    echo "   ❌ Pusher config not found\n";
    exit(1);
}

echo "   App ID: " . ($pusherConfig['app_id'] ?? 'NOT SET') . "\n";
echo "   Key: " . (isset($pusherConfig['key']) ? substr($pusherConfig['key'], 0, 8) . '...' : 'NOT SET') . "\n";
echo "   Cluster: " . ($pusherConfig['options']['cluster'] ?? 'NOT SET') . "\n";

// Test 4: Test direct Pusher connection
echo "\n4. Testing direct Pusher connection...\n";
try {
    $pusher = new Pusher\Pusher(
        $pusherConfig['key'],
        $pusherConfig['secret'],
        $pusherConfig['app_id'],
        $pusherConfig['options']
    );
    
    $result = $pusher->trigger('test-channel', 'test-event', ['message' => 'Test from PHP']);
    echo "   ✅ Pusher connection successful\n";
    echo "   Event sent to test-channel\n";
    
} catch (Exception $e) {
    echo "   ❌ Pusher connection failed: " . $e->getMessage() . "\n";
}

// Test 5: Create a real message and check if event fires
echo "\n5. Testing Laravel event broadcasting...\n";
try {
    $chat = \App\Models\Chat::first();
    if (!$chat) {
        echo "   ❌ No chat found\n";
        exit(1);
    }
    
    echo "   Creating message in chat {$chat->id}...\n";
    
    $message = \App\Models\ChatMessage::create([
        'chat_id' => $chat->id,
        'sender_type' => 'customer',
        'sender_id' => $chat->customer_id,
        'message' => 'Test message at ' . now()->format('H:i:s'),
        'message_type' => 'text'
    ]);
    
    echo "   ✅ Message created with ID: {$message->id}\n";
    echo "   Check browser console for events on channels:\n";
    echo "   - chat.{$chat->id}\n";
    echo "   - private-admin.chats\n";
    
} catch (Exception $e) {
    echo "   ❌ Message creation failed: " . $e->getMessage() . "\n";
}

echo "\n🎯 NEXT STEPS:\n";
echo "1. Open browser to: http://127.0.0.1:8000/test-browser-events.html\n";
echo "2. Watch for events in browser console\n";
echo "3. If no events appear, check Laravel logs\n";
echo "4. Run this script again to send more test messages\n";

echo "\n";
