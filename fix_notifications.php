<?php
/*
 * Quick fix script for existing notifications
 * Run this script once to fix existing notification data
 * 
 * Usage: Place this file in your Laravel project root and run it via browser
 * URL: http://127.0.0.1:8000/fix_notifications.php
 */

// Include Laravel bootstrap
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use Illuminate\Support\Facades\DB;

try {
    echo "<h1>Fixing Notifications Data</h1>";
    
    // Check if the new columns exist
    $columns = DB::select("SHOW COLUMNS FROM notifications");
    $columnNames = array_column($columns, 'Field');
    
    echo "<h2>Available Columns:</h2>";
    echo "<pre>" . implode(", ", $columnNames) . "</pre>";
    
    $hasAlertType = in_array('alert_type', $columnNames);
    $hasTargetType = in_array('target_type', $columnNames);
    $hasBody = in_array('body', $columnNames);
    $hasUserCategoryId = in_array('user_category_id', $columnNames);
    
    echo "<h2>Migration Status:</h2>";
    echo "alert_type: " . ($hasAlertType ? "✅ EXISTS" : "❌ MISSING") . "<br>";
    echo "target_type: " . ($hasTargetType ? "✅ EXISTS" : "❌ MISSING") . "<br>";
    echo "body: " . ($hasBody ? "✅ EXISTS" : "❌ MISSING") . "<br>";
    echo "user_category_id: " . ($hasUserCategoryId ? "✅ EXISTS" : "❌ MISSING") . "<br>";
    
    if (!$hasAlertType || !$hasTargetType) {
        echo "<h2 style='color: red;'>❌ Migration Required</h2>";
        echo "<p>Please run the migration first:</p>";
        echo "<code>php artisan migrate</code>";
        exit;
    }
    
    // Count existing notifications
    $totalNotifications = DB::table('notifications')->count();
    echo "<h2>Total Notifications: {$totalNotifications}</h2>";
    
    if ($totalNotifications == 0) {
        echo "<p>No notifications found. Creating sample notification...</p>";
        
        // Create a sample notification
        DB::table('notifications')->insert([
            'title' => 'إشعار تجريبي',
            'message' => 'هذا إشعار للتجربة من النظام الجديد',
            'body' => 'تفاصيل إضافية للإشعار التجريبي. يمكن استخدام هذا لاختبار النظام.',
            'type' => 'general',
            'alert_type' => 'info',
            'user_id' => 1, // Assuming user ID 1 exists
            'target_type' => 'user',
            'priority' => 'medium',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        echo "✅ Sample notification created!<br>";
    }
    
    // Fix existing notifications with NULL values
    $updated = DB::table('notifications')
        ->whereNull('alert_type')
        ->orWhereNull('target_type')
        ->update([
            'alert_type' => DB::raw("COALESCE(alert_type, 'info')"),
            'target_type' => DB::raw("COALESCE(target_type, 'user')"),
        ]);
    
    echo "<h2>✅ Fixed {$updated} notifications with missing data</h2>";
    
    // Show current notifications
    $notifications = DB::table('notifications')
        ->select('id', 'title', 'type', 'alert_type', 'target_type', 'user_id')
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();
    
    echo "<h2>Recent Notifications:</h2>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Title</th><th>Type</th><th>Alert Type</th><th>Target Type</th><th>User ID</th></tr>";
    
    foreach ($notifications as $notification) {
        echo "<tr>";
        echo "<td>{$notification->id}</td>";
        echo "<td>{$notification->title}</td>";
        echo "<td>{$notification->type}</td>";
        echo "<td>{$notification->alert_type}</td>";
        echo "<td>{$notification->target_type}</td>";
        echo "<td>{$notification->user_id}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h2>🎉 All Done!</h2>";
    echo "<p>You can now test the API: <a href='/api/notifications' target='_blank'>GET /api/notifications</a></p>";
    echo "<p style='color: red;'>⚠️ Don't forget to delete this file after use for security!</p>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>❌ Error:</h2>";
    echo "<p>{$e->getMessage()}</p>";
    echo "<pre>{$e->getTraceAsString()}</pre>";
}
?>

