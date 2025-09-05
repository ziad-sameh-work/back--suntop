<?php

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔍 BROADCASTING DEBUG\n";
echo "====================\n\n";

// Check broadcasting configuration
$broadcastConfig = config('broadcasting');
echo "📊 Broadcasting Configuration:\n";
echo "   Default driver: " . $broadcastConfig['default'] . "\n";
echo "   Pusher config exists: " . (isset($broadcastConfig['connections']['pusher']) ? 'YES' : 'NO') . "\n";

if (isset($broadcastConfig['connections']['pusher'])) {
    $pusherConfig = $broadcastConfig['connections']['pusher'];
    echo "   Pusher App ID: " . ($pusherConfig['app_id'] ?? 'NOT SET') . "\n";
    echo "   Pusher Key: " . (isset($pusherConfig['key']) ? substr($pusherConfig['key'], 0, 8) . '...' : 'NOT SET') . "\n";
    echo "   Pusher Cluster: " . ($pusherConfig['options']['cluster'] ?? 'NOT SET') . "\n";
}

echo "\n🎯 Testing Event Broadcasting:\n";
echo "-----------------------------\n";

try {
    // Create a test chat message
    $chat = \App\Models\Chat::first();
    if (!$chat) {
        echo "❌ No chat found for testing\n";
        exit(1);
    }
    
    echo "   Using chat ID: {$chat->id}\n";
    
    // Create test message
    $message = new \App\Models\ChatMessage([
        'chat_id' => $chat->id,
        'sender_type' => 'customer',
        'sender_id' => $chat->customer_id,
        'message' => 'Test broadcast message at ' . now()->format('H:i:s'),
        'message_type' => 'text'
    ]);
    
    echo "   Creating test message...\n";
    $message->save();
    
    echo "✅ Message created with ID: {$message->id}\n";
    echo "   Event should be broadcasted automatically via model observer\n";
    
    // Check if event was dispatched
    echo "\n📡 Event Broadcasting Status:\n";
    echo "   Check Laravel logs for broadcasting events\n";
    echo "   Check browser console for incoming events\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n🔍 Next Steps:\n";
echo "1. Check Laravel logs: tail -f storage/logs/laravel.log\n";
echo "2. Open browser console on chat page\n";
echo "3. Look for 'Broadcasting NewChatMessage event' in logs\n";
echo "4. Look for Pusher events in browser console\n";

echo "\n";
