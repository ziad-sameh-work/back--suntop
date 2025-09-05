<?php

/**
 * Test Broadcasting Authentication
 * Tests if the user session is properly authenticated for Pusher
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;

echo "🧪 BROADCASTING AUTHENTICATION TEST\n";
echo "===================================\n\n";

// Test 1: Check if we have admin users
echo "1. 📋 CHECKING ADMIN USERS\n";
echo "-------------------------\n";

$adminUsers = User::where('role', 'admin')->get(['id', 'name', 'email']);

if ($adminUsers->isEmpty()) {
    echo "❌ No admin users found!\n";
    echo "Please create an admin user first.\n\n";
    
    echo "Creating a test admin user...\n";
    $testAdmin = User::create([
        'name' => 'Test Admin',
        'email' => 'admin@test.com',
        'username' => 'test-admin',
        'password' => bcrypt('password'),
        'role' => 'admin',
        'phone' => '123456789',
        'is_active' => true
    ]);
    echo "✅ Test admin user created: {$testAdmin->email}\n\n";
} else {
    echo "✅ Found " . $adminUsers->count() . " admin user(s):\n";
    foreach ($adminUsers as $admin) {
        echo "   - {$admin->name} ({$admin->email})\n";
    }
    echo "\n";
}

// Test 2: Check Pusher configuration
echo "2. 🔧 CHECKING PUSHER CONFIGURATION\n";
echo "----------------------------------\n";

$pusherConfig = [
    'BROADCAST_DRIVER' => env('BROADCAST_DRIVER'),
    'PUSHER_APP_ID' => env('PUSHER_APP_ID'),
    'PUSHER_APP_KEY' => env('PUSHER_APP_KEY'),
    'PUSHER_APP_SECRET' => env('PUSHER_APP_SECRET') ? '***' . substr(env('PUSHER_APP_SECRET'), -4) : null,
    'PUSHER_APP_CLUSTER' => env('PUSHER_APP_CLUSTER'),
];

foreach ($pusherConfig as $key => $value) {
    $status = $value ? '✅' : '❌';
    echo "{$status} {$key}: " . ($value ?: 'NOT SET') . "\n";
}

echo "\n";

// Test 3: Test manual authentication
echo "3. 🔐 TESTING MANUAL AUTHENTICATION\n";
echo "-----------------------------------\n";

$testAdmin = User::where('role', 'admin')->first();

if ($testAdmin) {
    // Simulate login
    Auth::login($testAdmin);
    
    if (Auth::check()) {
        $user = Auth::user();
        echo "✅ Authentication successful!\n";
        echo "   User ID: {$user->id}\n";
        echo "   Name: {$user->name}\n";
        echo "   Role: {$user->role}\n";
        echo "   Can access admin.chats: " . ($user->role === 'admin' ? 'YES' : 'NO') . "\n";
        
        // Test channel authorization logic
        $channelName = 'private-admin.chats';
        echo "\n🧪 Testing channel authorization for: {$channelName}\n";
        
        if (strpos($channelName, 'private-admin.chats') !== false) {
            if ($user->role === 'admin') {
                echo "✅ Channel access GRANTED\n";
            } else {
                echo "❌ Channel access DENIED (not admin)\n";
            }
        }
        
    } else {
        echo "❌ Authentication failed!\n";
    }
} else {
    echo "❌ No admin user found for testing\n";
}

echo "\n";

// Test 4: Check route registration
echo "4. 🛣️  CHECKING ROUTE REGISTRATION\n";
echo "---------------------------------\n";

try {
    $authUrl = route('broadcasting.auth');
    echo "✅ Broadcasting auth route registered: {$authUrl}\n";
} catch (Exception $e) {
    echo "❌ Broadcasting auth route NOT registered: {$e->getMessage()}\n";
}

echo "\n";

// Test 5: Check middleware
echo "5. 🛡️  CHECKING MIDDLEWARE CONFIGURATION\n";
echo "---------------------------------------\n";

$middlewareGroups = config('app.middleware_groups', []);
echo "Web middleware group: " . (isset($middlewareGroups['web']) ? 'REGISTERED' : 'NOT FOUND') . "\n";

echo "\n✅ Test completed!\n";
echo "\nNext steps:\n";
echo "1. Make sure you're logged in as an admin in your browser\n";
echo "2. Check browser console for any JavaScript errors\n";
echo "3. Check Laravel logs for authentication errors\n";
echo "4. Test the /test-broadcasting-auth route in your browser\n";
