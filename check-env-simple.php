<?php

echo "🔍 CHECKING .ENV FILE\n";
echo "====================\n\n";

// Check if .env file exists
if (!file_exists('.env')) {
    echo "❌ .env file not found!\n";
    echo "📋 Please copy .env.example to .env\n";
    exit(1);
}

// Read .env file
$envContent = file_get_contents('.env');
$envLines = explode("\n", $envContent);

echo "📊 Current .env settings:\n";
echo "------------------------\n";

$foundBroadcast = false;
$foundPusherSettings = false;

foreach ($envLines as $lineNum => $line) {
    $line = trim($line);
    if (empty($line) || strpos($line, '#') === 0) continue;
    
    if (strpos($line, 'BROADCAST_DRIVER=') === 0) {
        $value = substr($line, 17);
        echo "   Line " . ($lineNum + 1) . ": BROADCAST_DRIVER = '{$value}'\n";
        $foundBroadcast = true;
        
        if ($value !== 'pusher') {
            echo "   ❌ Should be 'pusher' for real-time chat!\n";
        } else {
            echo "   ✅ Correctly set to 'pusher'\n";
        }
    }
    
    if (strpos($line, 'PUSHER_APP_') === 0) {
        $parts = explode('=', $line, 2);
        $key = $parts[0];
        $value = isset($parts[1]) ? $parts[1] : '';
        
        if ($key === 'PUSHER_APP_SECRET' && !empty($value)) {
            echo "   Line " . ($lineNum + 1) . ": {$key} = " . substr($value, 0, 8) . "...\n";
        } else {
            echo "   Line " . ($lineNum + 1) . ": {$key} = '{$value}'\n";
        }
        
        if (!empty($value)) {
            $foundPusherSettings = true;
        }
    }
}

echo "\n🎯 Analysis:\n";
echo "-----------\n";

if (!$foundBroadcast) {
    echo "❌ BROADCAST_DRIVER not found in .env file\n";
    echo "   Add: BROADCAST_DRIVER=pusher\n";
} 

if (!$foundPusherSettings) {
    echo "❌ Pusher settings not found or empty\n";
    echo "   Required settings:\n";
    echo "   PUSHER_APP_ID=2046066\n";
    echo "   PUSHER_APP_KEY=f546bf192457a6d47ed5\n";
    echo "   PUSHER_APP_SECRET=d1a687b90b02f69ea917\n";
    echo "   PUSHER_APP_CLUSTER=eu\n";
}

echo "\n📋 Next steps:\n";
echo "-------------\n";
echo "1. Fix .env file settings\n";
echo "2. Run: php artisan config:clear\n";
echo "3. Test again with: php test-realtime-chat.php\n";

echo "\n";
