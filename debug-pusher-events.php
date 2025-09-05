<?php

/**
 * Debug Pusher Events - Test if events are being sent to Pusher
 */

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel environment
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Pusher\Pusher;

echo "🔍 PUSHER DEBUG TOOL\n";
echo "===================\n\n";

// Check Pusher configuration
$pusherConfig = config('broadcasting.connections.pusher');
echo "📋 Pusher Configuration:\n";
echo "   App ID: " . $pusherConfig['app_id'] . "\n";
echo "   Key: " . $pusherConfig['key'] . "\n";
echo "   Secret: " . substr($pusherConfig['secret'], 0, 4) . "...\n";
echo "   Cluster: " . $pusherConfig['options']['cluster'] . "\n";
echo "   Driver: " . config('broadcasting.default') . "\n\n";

// Test direct Pusher connection
try {
    $pusher = new Pusher(
        $pusherConfig['key'],
        $pusherConfig['secret'],
        $pusherConfig['app_id'],
        $pusherConfig['options']
    );
    
    echo "✅ Pusher instance created successfully\n\n";
    
    // Test sending a direct event
    echo "📤 Sending test event...\n";
    
    $testData = [
        'message' => [
            'id' => 999,
            'chat_id' => 1,
            'message' => 'Test message from debug script',
            'sender_type' => 'customer',
            'sender' => [
                'id' => 1,
                'name' => 'Debug Tester'
            ],
            'created_at' => now()->toISOString(),
            'formatted_time' => now()->format('H:i')
        ],
        'chat' => [
            'id' => 1,
            'customer' => [
                'name' => 'Debug Customer'
            ]
        ],
        'timestamp' => now()->toISOString()
    ];
    
    // Send to both channels
    $channels = ['chat.1', 'private-admin.chats'];
    
    foreach ($channels as $channel) {
        echo "   Sending to channel: {$channel}\n";
        
        $result = $pusher->trigger($channel, 'message.new', $testData);
        
        if ($result) {
            echo "   ✅ Event sent successfully to {$channel}\n";
        } else {
            echo "   ❌ Failed to send event to {$channel}\n";
        }
    }
    
    echo "\n🎯 Test completed!\n";
    echo "Check your browser console for incoming events.\n";
    echo "Expected event name: 'message.new'\n";
    echo "Expected channels: 'chat.1' and 'private-admin.chats'\n\n";
    
    // Get channel info
    echo "📊 Channel Information:\n";
    try {
        $channelInfo = $pusher->get('/channels');
        if ($channelInfo && isset($channelInfo->channels)) {
            echo "   Active channels: " . count((array)$channelInfo->channels) . "\n";
            foreach ($channelInfo->channels as $channelName => $info) {
                $userCount = isset($info->user_count) ? $info->user_count : 'unknown';
                echo "   - {$channelName}: {$userCount} users\n";
            }
        } else {
            echo "   No active channels found\n";
        }
    } catch (Exception $e) {
        echo "   Could not retrieve channel info: " . $e->getMessage() . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n🏁 Debug completed at " . date('Y-m-d H:i:s') . "\n";
