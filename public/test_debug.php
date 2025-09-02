<?php

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 Debug Order Status Update Issue</h1>";

// Include Laravel bootstrap
try {
    require_once __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    
    echo "<p>✅ Laravel loaded successfully</p>";
    
    // Boot the application
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    echo "<p>✅ Laravel bootstrapped successfully</p>";
    
} catch (Exception $e) {
    echo "<p>❌ Failed to load Laravel: " . $e->getMessage() . "</p>";
    exit;
}

echo "<h2>1. Testing Order Model</h2>";

try {
    $order = \App\Modules\Orders\Models\Order::find(14);
    if ($order) {
        echo "<p>✅ Order 14 found</p>";
        echo "<p>Order Number: " . ($order->order_number ?? 'NULL') . "</p>";
        echo "<p>Status: " . ($order->status ?? 'NULL') . "</p>";
        echo "<p>User ID: " . ($order->user_id ?? 'NULL') . "</p>";
        
        if ($order->user) {
            echo "<p>✅ User found: " . $order->user->name . "</p>";
        } else {
            echo "<p>❌ User not found</p>";
        }
    } else {
        echo "<p>❌ Order 14 not found</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Error loading order: " . $e->getMessage() . "</p>";
}

echo "<h2>2. Testing AdminOrderController Class</h2>";

try {
    $controller = new \App\Http\Controllers\AdminOrderController();
    echo "<p>✅ AdminOrderController instantiated</p>";
    
    $reflection = new ReflectionClass($controller);
    if ($reflection->hasMethod('updateStatusWithNotification')) {
        echo "<p>✅ updateStatusWithNotification method exists</p>";
    } else {
        echo "<p>❌ updateStatusWithNotification method NOT found</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error with AdminOrderController: " . $e->getMessage() . "</p>";
    echo "<p>File: " . $e->getFile() . " Line: " . $e->getLine() . "</p>";
}

echo "<h2>3. Testing Route</h2>";

try {
    $routes = \Illuminate\Support\Facades\Route::getRoutes();
    $found = false;
    
    foreach ($routes as $route) {
        if (strpos($route->uri(), 'admin/orders/{order}/update-status-with-notification') !== false) {
            echo "<p>✅ Route found: " . $route->uri() . "</p>";
            echo "<p>Action: " . $route->getActionName() . "</p>";
            echo "<p>Methods: " . implode(', ', $route->methods()) . "</p>";
            $found = true;
            break;
        }
    }
    
    if (!$found) {
        echo "<p>❌ Route NOT found</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error checking routes: " . $e->getMessage() . "</p>";
}

echo "<h2>4. Testing Request Simulation</h2>";

try {
    // Create a mock request
    $request = new \Illuminate\Http\Request();
    $request->merge([
        'status' => 'preparing',
        'title' => 'بدء التجهيز',
        'message' => 'تم البدء في تجهيز طلبكم، سيتم إشعاركم عند اكتمال التجهيز',
        'notes' => 'Test from debug script'
    ]);
    
    // Test validation
    $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
        'status' => 'required|in:pending,confirmed,preparing,processing,shipping,shipped,delivered,cancelled,refunded',
        'title' => 'required|string|max:100',
        'message' => 'required|string|max:500',
        'notes' => 'nullable|string|max:500'
    ]);
    
    if ($validator->passes()) {
        echo "<p>✅ Validation passed</p>";
    } else {
        echo "<p>❌ Validation failed:</p>";
        foreach ($validator->errors()->all() as $error) {
            echo "<p>- $error</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error in request simulation: " . $e->getMessage() . "</p>";
}

echo "<h2>5. Testing Notification Model</h2>";

try {
    $notification = \App\Models\Notification::createOrderStatusNotification(
        1, // user_id
        'TEST-ORDER',
        'preparing',
        [
            'title' => 'بدء التجهيز',
            'message' => 'تم البدء في تجهيز طلبكم، سيتم إشعاركم عند اكتمال التجهيز',
            'custom_message' => true
        ]
    );
    
    if ($notification) {
        echo "<p>✅ Notification created successfully: ID " . $notification->id . "</p>";
    } else {
        echo "<p>❌ Failed to create notification</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error creating notification: " . $e->getMessage() . "</p>";
    echo "<p>File: " . $e->getFile() . " Line: " . $e->getLine() . "</p>";
}

echo "<h2>6. Direct Method Test</h2>";

try {
    // Test the actual method directly
    $controller = new \App\Http\Controllers\AdminOrderController();
    $request = \Illuminate\Http\Request::create('/test', 'POST', [
        'status' => 'preparing',
        'title' => 'بدء التجهيز',
        'message' => 'تم البدء في تجهيز طلبكم، سيتم إشعاركم عند اكتمال التجهيز',
        'notes' => 'Direct test'
    ]);
    
    echo "<p>🧪 Attempting direct method call...</p>";
    
    // This will likely fail due to authentication, but let's see the error
    ob_start();
    $response = $controller->updateStatusWithNotification($request, 14);
    $output = ob_get_clean();
    
    if ($response) {
        echo "<p>✅ Method executed, Response type: " . get_class($response) . "</p>";
        if (method_exists($response, 'getContent')) {
            echo "<p>Response content: " . $response->getContent() . "</p>";
        }
    }
    
    if ($output) {
        echo "<p>Output: $output</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error in direct method test: " . $e->getMessage() . "</p>";
    echo "<p>File: " . $e->getFile() . " Line: " . $e->getLine() . "</p>";
    echo "<p><strong>This might be the actual issue!</strong></p>";
}

echo "<h2>🎯 Summary</h2>";
echo "<p>Run this script by visiting: <strong>http://127.0.0.1:8000/test_debug.php</strong></p>";
echo "<p>This will help identify exactly where the 500 error is coming from.</p>";

?>
