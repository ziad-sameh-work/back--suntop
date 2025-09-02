<?php

// Quick order 14 check
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $order = \App\Modules\Orders\Models\Order::with('user')->find(14);
    
    if ($order) {
        echo "Order 14 Status: " . $order->status . "\n";
        echo "Order Number: " . ($order->order_number ?? 'NULL') . "\n";
        echo "User ID: " . ($order->user_id ?? 'NULL') . "\n";
        echo "User Name: " . ($order->user?->name ?? 'NULL') . "\n";
        echo "User Role: " . ($order->user?->role ?? 'NULL') . "\n";
        echo "\nNext Available Action:\n";
        
        switch($order->status) {
            case 'pending':
                echo "- تأكيد الطلب (confirmed)\n";
                break;
            case 'confirmed':
                echo "- بدء التجهيز (preparing)\n";
                break;
            case 'preparing':
                echo "- تم الشحن (shipped)\n";
                break;
            case 'shipped':
                echo "- تم التسليم (delivered)\n";
                break;
            default:
                echo "- No further actions available\n";
        }
    } else {
        echo "Order 14 not found\n";
    }
    
    // Test the status update directly
    echo "\n=== Testing Status Update to 'preparing' ===\n";
    
    if ($order && $order->status === 'confirmed') {
        $order->update(['status' => 'preparing', 'status_notes' => 'Test update']);
        echo "✅ Successfully updated order to 'preparing'\n";
        
        // Test notification
        if ($order->user) {
            $notification = \App\Models\Notification::createOrderStatusNotification(
                $order->user->id,
                $order->order_number,
                'preparing',
                [
                    'title' => 'بدء التجهيز',
                    'message' => 'تم البدء في تجهيز طلبكم، سيتم إشعاركم عند اكتمال التجهيز',
                    'custom_message' => true
                ]
            );
            echo "✅ Notification created: " . $notification->id . "\n";
        }
        
        // Reset status back
        $order->update(['status' => 'confirmed']);
        echo "✅ Order status reset to 'confirmed'\n";
    } else {
        echo "❌ Order is not in 'confirmed' status, current: " . ($order?->status ?? 'NULL') . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

?>
