<?php

/**
 * Check Pusher Configuration Script
 * Verifies if Pusher is properly configured in Laravel
 */

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel environment
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔥 PUSHER CONFIGURATION CHECK 🔥\n";
echo "================================\n\n";

// Check environment variables
echo "📋 Environment Variables:\n";
echo "BROADCAST_DRIVER: " . env('BROADCAST_DRIVER', 'NOT SET') . "\n";
echo "PUSHER_APP_ID: " . (env('PUSHER_APP_ID') ? '✅ SET' : '❌ NOT SET') . "\n";
echo "PUSHER_APP_KEY: " . (env('PUSHER_APP_KEY') ? '✅ SET (' . substr(env('PUSHER_APP_KEY'), 0, 10) . '...)' : '❌ NOT SET') . "\n";
echo "PUSHER_APP_SECRET: " . (env('PUSHER_APP_SECRET') ? '✅ SET' : '❌ NOT SET') . "\n";
echo "PUSHER_APP_CLUSTER: " . (env('PUSHER_APP_CLUSTER') ? '✅ ' . env('PUSHER_APP_CLUSTER') : '❌ NOT SET') . "\n\n";

// Check config values
echo "⚙️ Laravel Config:\n";
echo "Broadcasting Driver: " . config('broadcasting.default') . "\n";
echo "Pusher Key: " . (config('broadcasting.connections.pusher.key') ? '✅ SET' : '❌ NOT SET') . "\n";
echo "Pusher Secret: " . (config('broadcasting.connections.pusher.secret') ? '✅ SET' : '❌ NOT SET') . "\n";
echo "Pusher App ID: " . (config('broadcasting.connections.pusher.app_id') ? '✅ SET' : '❌ NOT SET') . "\n";
echo "Pusher Cluster: " . (config('broadcasting.connections.pusher.options.cluster') ? '✅ ' . config('broadcasting.connections.pusher.options.cluster') : '❌ NOT SET') . "\n\n";

// Check if Pusher package is installed
echo "📦 Pusher Package:\n";
try {
    $pusher = new \Pusher\Pusher(
        config('broadcasting.connections.pusher.key') ?: 'test',
        config('broadcasting.connections.pusher.secret') ?: 'test', 
        config('broadcasting.connections.pusher.app_id') ?: 'test',
        config('broadcasting.connections.pusher.options') ?: []
    );
    echo "✅ Pusher PHP SDK is installed and working\n";
} catch (Exception $e) {
    echo "❌ Pusher PHP SDK error: " . $e->getMessage() . "\n";
}

echo "\n";

// Check if all required values are set
$allSet = env('BROADCAST_DRIVER') === 'pusher' &&
          env('PUSHER_APP_ID') &&
          env('PUSHER_APP_KEY') &&
          env('PUSHER_APP_SECRET') &&
          env('PUSHER_APP_CLUSTER');

if ($allSet) {
    echo "🎉 All Pusher configuration looks good!\n";
    echo "📝 Your Pusher App Key for testing: " . env('PUSHER_APP_KEY') . "\n";
    echo "📝 Your Pusher Cluster: " . env('PUSHER_APP_CLUSTER') . "\n\n";
    
    echo "🔗 Test URLs:\n";
    echo "1. Open: http://localhost/back-suntop/test-pusher-connection.html\n";
    echo "2. Enter your key and cluster, then click Test Connection\n";
    echo "3. Run: php test-realtime-chat.php customer \"Test message\"\n";
    echo "4. You should see the message in real-time!\n";
} else {
    echo "❌ Pusher configuration is incomplete!\n";
    echo "📝 Please check your .env file and make sure these are set:\n";
    echo "   BROADCAST_DRIVER=pusher\n";
    echo "   PUSHER_APP_ID=your_app_id\n";
    echo "   PUSHER_APP_KEY=your_app_key\n";
    echo "   PUSHER_APP_SECRET=your_app_secret\n";
    echo "   PUSHER_APP_CLUSTER=your_cluster\n";
}

echo "\n🏁 Configuration check completed at " . date('Y-m-d H:i:s') . "\n";
