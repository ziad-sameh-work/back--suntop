<?php

echo "🔍 CHECKING ENVIRONMENT CONFIGURATION\n";
echo "====================================\n\n";

// Check if .env file exists
if (!file_exists('.env')) {
    echo "❌ .env file not found!\n";
    echo "📋 Please copy .env.example to .env and configure it.\n";
    exit(1);
}

// Load .env file
$envContent = file_get_contents('.env');
$envLines = explode("\n", $envContent);

echo "📊 Broadcasting Configuration:\n";
echo "-----------------------------\n";

$broadcastDriver = null;
$pusherAppId = null;
$pusherKey = null;
$pusherSecret = null;
$pusherCluster = null;

foreach ($envLines as $line) {
    $line = trim($line);
    if (empty($line) || strpos($line, '#') === 0) continue;
    
    if (strpos($line, 'BROADCAST_DRIVER=') === 0) {
        $broadcastDriver = substr($line, 17);
        echo "   BROADCAST_DRIVER: {$broadcastDriver}\n";
    }
    
    if (strpos($line, 'PUSHER_APP_ID=') === 0) {
        $pusherAppId = substr($line, 14);
        echo "   PUSHER_APP_ID: " . ($pusherAppId ? $pusherAppId : 'NOT SET') . "\n";
    }
    
    if (strpos($line, 'PUSHER_APP_KEY=') === 0) {
        $pusherKey = substr($line, 15);
        echo "   PUSHER_APP_KEY: " . ($pusherKey ? substr($pusherKey, 0, 8) . '...' : 'NOT SET') . "\n";
    }
    
    if (strpos($line, 'PUSHER_APP_SECRET=') === 0) {
        $pusherSecret = substr($line, 18);
        echo "   PUSHER_APP_SECRET: " . ($pusherSecret ? substr($pusherSecret, 0, 8) . '...' : 'NOT SET') . "\n";
    }
    
    if (strpos($line, 'PUSHER_APP_CLUSTER=') === 0) {
        $pusherCluster = substr($line, 19);
        echo "   PUSHER_APP_CLUSTER: " . ($pusherCluster ? $pusherCluster : 'NOT SET') . "\n";
    }
}

echo "\n🔍 Analysis:\n";
echo "-----------\n";

if ($broadcastDriver !== 'pusher') {
    echo "❌ BROADCAST_DRIVER is '{$broadcastDriver}' but should be 'pusher'\n";
    echo "   Fix: Change BROADCAST_DRIVER=pusher in .env file\n";
} else {
    echo "✅ BROADCAST_DRIVER is correctly set to 'pusher'\n";
}

if (!$pusherAppId || !$pusherKey || !$pusherSecret || !$pusherCluster) {
    echo "❌ Missing Pusher credentials in .env file\n";
    echo "   Required: PUSHER_APP_ID, PUSHER_APP_KEY, PUSHER_APP_SECRET, PUSHER_APP_CLUSTER\n";
} else {
    echo "✅ All Pusher credentials are set\n";
}

// Check Laravel config
echo "\n📋 Laravel Broadcasting Config:\n";
echo "------------------------------\n";

try {
    // Simulate Laravel config loading
    $configBroadcasting = [
        'default' => $broadcastDriver,
        'connections' => [
            'pusher' => [
                'driver' => 'pusher',
                'key' => $pusherKey,
                'secret' => $pusherSecret,
                'app_id' => $pusherAppId,
                'options' => [
                    'cluster' => $pusherCluster,
                    'encrypted' => true,
                ],
            ],
        ],
    ];
    
    echo "   Default driver: " . $configBroadcasting['default'] . "\n";
    echo "   Pusher config loaded: " . (isset($configBroadcasting['connections']['pusher']) ? 'YES' : 'NO') . "\n";
    
} catch (Exception $e) {
    echo "❌ Error loading config: " . $e->getMessage() . "\n";
}

echo "\n🎯 Recommendations:\n";
echo "------------------\n";

if ($broadcastDriver !== 'pusher') {
    echo "1. Set BROADCAST_DRIVER=pusher in .env file\n";
    echo "2. Run: php artisan config:cache\n";
    echo "3. Restart your web server\n";
} else {
    echo "1. Configuration looks correct\n";
    echo "2. Check if queue worker is running (if QUEUE_CONNECTION != sync)\n";
    echo "3. Check Laravel logs for broadcasting errors\n";
    echo "4. Test Pusher connection directly\n";
}

echo "\n";
