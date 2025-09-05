<?php

echo "🔍 CHECKING .ENV CONFIGURATION\n";
echo "==============================\n\n";

if (!file_exists('.env')) {
    echo "❌ .env file not found!\n";
    echo "📋 Copy .env.example to .env first\n";
    exit(1);
}

$envContent = file_get_contents('.env');
$lines = explode("\n", $envContent);

$broadcastDriver = null;
$pusherAppId = null;
$pusherKey = null;
$pusherSecret = null;
$pusherCluster = null;

echo "📊 Broadcasting Settings:\n";
echo "------------------------\n";

foreach ($lines as $line) {
    $line = trim($line);
    if (empty($line) || $line[0] === '#') continue;
    
    if (strpos($line, 'BROADCAST_DRIVER=') === 0) {
        $broadcastDriver = trim(substr($line, 17));
        echo "BROADCAST_DRIVER = '{$broadcastDriver}'\n";
    }
    
    if (strpos($line, 'PUSHER_APP_ID=') === 0) {
        $pusherAppId = trim(substr($line, 14));
        echo "PUSHER_APP_ID = '{$pusherAppId}'\n";
    }
    
    if (strpos($line, 'PUSHER_APP_KEY=') === 0) {
        $pusherKey = trim(substr($line, 15));
        echo "PUSHER_APP_KEY = '{$pusherKey}'\n";
    }
    
    if (strpos($line, 'PUSHER_APP_SECRET=') === 0) {
        $pusherSecret = trim(substr($line, 18));
        echo "PUSHER_APP_SECRET = '" . substr($pusherSecret, 0, 8) . "...'\n";
    }
    
    if (strpos($line, 'PUSHER_APP_CLUSTER=') === 0) {
        $pusherCluster = trim(substr($line, 19));
        echo "PUSHER_APP_CLUSTER = '{$pusherCluster}'\n";
    }
}

echo "\n🎯 DIAGNOSIS:\n";
echo "-------------\n";

$hasIssues = false;

if ($broadcastDriver !== 'pusher') {
    echo "❌ BROADCAST_DRIVER is '{$broadcastDriver}' but should be 'pusher'\n";
    $hasIssues = true;
} else {
    echo "✅ BROADCAST_DRIVER is correctly set to 'pusher'\n";
}

if (empty($pusherAppId) || empty($pusherKey) || empty($pusherSecret) || empty($pusherCluster)) {
    echo "❌ Missing Pusher credentials\n";
    $hasIssues = true;
} else {
    echo "✅ All Pusher credentials are present\n";
}

if ($hasIssues) {
    echo "\n🔧 REQUIRED FIXES:\n";
    echo "-----------------\n";
    
    if ($broadcastDriver !== 'pusher') {
        echo "1. Change BROADCAST_DRIVER=pusher in .env file\n";
    }
    
    if (empty($pusherAppId) || empty($pusherKey) || empty($pusherSecret) || empty($pusherCluster)) {
        echo "2. Add these lines to .env file:\n";
        echo "   PUSHER_APP_ID=2046066\n";
        echo "   PUSHER_APP_KEY=f546bf192457a6d47ed5\n";
        echo "   PUSHER_APP_SECRET=d1a687b90b02f69ea917\n";
        echo "   PUSHER_APP_CLUSTER=eu\n";
    }
    
    echo "\n3. After fixing, run:\n";
    echo "   php artisan config:clear\n";
    echo "   php artisan config:cache\n";
    
} else {
    echo "\n✅ Configuration looks correct!\n";
    echo "   If events still don't work, check:\n";
    echo "   1. Queue worker (if QUEUE_CONNECTION != sync)\n";
    echo "   2. Laravel logs for errors\n";
    echo "   3. Browser console for connection issues\n";
}

echo "\n";
