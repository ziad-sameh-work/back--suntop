<?php

/**
 * Pusher Real-time Configuration Checker
 * Validates all Pusher settings and broadcasting configuration
 */

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel environment
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔍 PUSHER REAL-TIME CONFIGURATION CHECKER\n";
echo "==========================================\n\n";

// 1. Check Environment Variables
echo "1. 📋 ENVIRONMENT VARIABLES\n";
echo "----------------------------\n";

$envVars = [
    'BROADCAST_DRIVER',
    'PUSHER_APP_ID',
    'PUSHER_APP_KEY',
    'PUSHER_APP_SECRET',
    'PUSHER_APP_CLUSTER'
];

$envStatus = true;
foreach ($envVars as $var) {
    $value = env($var);
    if ($value) {
        $displayValue = in_array($var, ['PUSHER_APP_SECRET']) ? 
            substr($value, 0, 4) . '...' : $value;
        echo "   ✅ {$var}: {$displayValue}\n";
    } else {
        echo "   ❌ {$var}: NOT SET\n";
        $envStatus = false;
    }
}

if (!$envStatus) {
    echo "\n❌ Environment variables are missing! Please set them in your .env file.\n\n";
} else {
    echo "\n✅ All environment variables are set.\n\n";
}

// 2. Check Broadcasting Configuration
echo "2. ⚙️ BROADCASTING CONFIGURATION\n";
echo "--------------------------------\n";

$broadcastConfig = config('broadcasting');
echo "   Default Driver: " . $broadcastConfig['default'] . "\n";
echo "   Pusher Config:\n";
echo "     - App ID: " . $broadcastConfig['connections']['pusher']['app_id'] . "\n";
echo "     - Key: " . $broadcastConfig['connections']['pusher']['key'] . "\n";
echo "     - Secret: " . substr($broadcastConfig['connections']['pusher']['secret'], 0, 4) . "...\n";
echo "     - Cluster: " . $broadcastConfig['connections']['pusher']['options']['cluster'] . "\n";
echo "     - Use TLS: " . ($broadcastConfig['connections']['pusher']['options']['useTLS'] ? 'Yes' : 'No') . "\n\n";

// 3. Test Pusher Connection
echo "3. 🔌 PUSHER CONNECTION TEST\n";
echo "----------------------------\n";

try {
    $pusher = new \Pusher\Pusher(
        config('broadcasting.connections.pusher.key'),
        config('broadcasting.connections.pusher.secret'),
        config('broadcasting.connections.pusher.app_id'),
        config('broadcasting.connections.pusher.options')
    );
    
    echo "   ✅ Pusher instance created successfully\n";
    
    // Test a simple trigger
    $testResult = $pusher->trigger('test-channel', 'test-event', [
        'message' => 'Configuration test',
        'timestamp' => now()->toISOString()
    ]);
    
    if ($testResult) {
        echo "   ✅ Test broadcast successful\n";
    } else {
        echo "   ❌ Test broadcast failed\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Pusher connection failed: " . $e->getMessage() . "\n";
}

echo "\n";

// 4. Check Routes
echo "4. 🛤️ ROUTES CHECK\n";
echo "------------------\n";

$routes = [
    'broadcasting.auth' => '/broadcasting/auth',
    'api.broadcasting.auth' => '/api/broadcasting/auth'
];

foreach ($routes as $name => $path) {
    try {
        $routeExists = \Illuminate\Support\Facades\Route::has($name);
        if ($routeExists) {
            echo "   ✅ Route '{$name}' exists: {$path}\n";
        } else {
            // Check if route exists by URL
            $allRoutes = \Illuminate\Support\Facades\Route::getRoutes();
            $found = false;
            foreach ($allRoutes as $route) {
                if ($route->uri() === ltrim($path, '/')) {
                    echo "   ✅ Route found: {$path}\n";
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                echo "   ❌ Route not found: {$path}\n";
            }
        }
    } catch (Exception $e) {
        echo "   ❌ Route check failed for {$path}: " . $e->getMessage() . "\n";
    }
}

echo "\n";

// 5. Check Models and Events
echo "5. 🏗️ MODELS AND EVENTS CHECK\n";
echo "-----------------------------\n";

$classes = [
    'App\\Models\\Chat' => 'Chat Model',
    'App\\Models\\ChatMessage' => 'ChatMessage Model',
    'App\\Events\\NewChatMessage' => 'NewChatMessage Event',
    'App\\Http\\Controllers\\BroadcastingAuthController' => 'Broadcasting Auth Controller'
];

foreach ($classes as $class => $description) {
    if (class_exists($class)) {
        echo "   ✅ {$description}: {$class}\n";
    } else {
        echo "   ❌ {$description} not found: {$class}\n";
    }
}

echo "\n";

// 6. Check Database Tables
echo "6. 🗄️ DATABASE TABLES CHECK\n";
echo "---------------------------\n";

$tables = ['chats', 'chat_messages', 'users'];

foreach ($tables as $table) {
    try {
        if (\Illuminate\Support\Facades\Schema::hasTable($table)) {
            $count = \Illuminate\Support\Facades\DB::table($table)->count();
            echo "   ✅ Table '{$table}' exists with {$count} records\n";
        } else {
            echo "   ❌ Table '{$table}' not found\n";
        }
    } catch (Exception $e) {
        echo "   ❌ Error checking table '{$table}': " . $e->getMessage() . "\n";
    }
}

echo "\n";

// 7. Generate Frontend JavaScript Config
echo "7. 🌐 FRONTEND CONFIGURATION\n";
echo "----------------------------\n";

$jsConfig = [
    'pusher_key' => config('broadcasting.connections.pusher.key'),
    'pusher_cluster' => config('broadcasting.connections.pusher.options.cluster'),
    'auth_endpoint' => url('/broadcasting/auth'),
    'csrf_token' => csrf_token(),
    'channels' => [
        'admin_chats' => 'private-admin.chats',
        'individual_chat' => 'chat.{id}'
    ]
];

echo "   Frontend Config (copy to your JavaScript):\n";
echo "   ```javascript\n";
echo "   const pusherConfig = " . json_encode($jsConfig, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . ";\n";
echo "   ```\n\n";

// 8. Recommendations
echo "8. 💡 RECOMMENDATIONS\n";
echo "--------------------\n";

$recommendations = [];

if (env('BROADCAST_DRIVER') !== 'pusher') {
    $recommendations[] = "Set BROADCAST_DRIVER=pusher in your .env file";
}

if (!env('PUSHER_APP_KEY')) {
    $recommendations[] = "Set your Pusher credentials in the .env file";
}

if (config('app.debug')) {
    $recommendations[] = "Consider disabling debug mode in production (APP_DEBUG=false)";
}

if (empty($recommendations)) {
    echo "   ✅ Configuration looks good!\n";
} else {
    foreach ($recommendations as $rec) {
        echo "   💡 {$rec}\n";
    }
}

echo "\n";

// 9. Test URLs
echo "9. 🔗 TEST URLS\n";
echo "--------------\n";
echo "   Test Pusher Config: " . url('/test-pusher-config') . "\n";
echo "   Test Chat Event: " . url('/test-chat-event/{chat_id}') . "\n";
echo "   Broadcasting Auth: " . url('/broadcasting/auth') . "\n";
echo "   Admin Chats: " . url('/admin/chats') . "\n";

echo "\n🏁 Configuration check completed at " . now()->format('Y-m-d H:i:s') . "\n";
