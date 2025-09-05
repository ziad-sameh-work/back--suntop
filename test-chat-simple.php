<?php

/**
 * Simple Chat Test - Uses existing users
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\User;

echo "🧪 SIMPLE CHAT REAL-TIME TEST\n";
echo "=============================\n\n";

// Find existing users
$customer = User::where('role', 'customer')->first();
$admin = User::where('role', 'admin')->first();

if (!$customer) {
    echo "❌ No customer user found. Please create a customer user first.\n";
    exit(1);
}

if (!$admin) {
    echo "❌ No admin user found. Please create an admin user first.\n";
    exit(1);
}

echo "✅ Using existing users:\n";
echo "   Customer: {$customer->name} (ID: {$customer->id})\n";
echo "   Admin: {$admin->name} (ID: {$admin->id})\n\n";

// Find or create a test chat
$chat = Chat::firstOrCreate([
    'customer_id' => $customer->id,
    'status' => 'open'
], [
    'subject' => 'Real-time Test Chat',
    'priority' => 'medium'
]);

echo "✅ Using chat ID: {$chat->id}\n\n";

// Determine sender based on command argument
$senderType = isset($argv[1]) ? $argv[1] : 'customer';
$message = isset($argv[2]) ? $argv[2] : null;

if (!in_array($senderType, ['customer', 'admin'])) {
    echo "❌ Invalid sender type. Use 'customer' or 'admin'\n";
    exit(1);
}

$sender = $senderType === 'admin' ? $admin : $customer;
$defaultMessage = "Test message from {$senderType} at " . date('H:i:s');
$messageText = $message ?: $defaultMessage;

echo "📤 Sending message...\n";
echo "   From: {$sender->name} ({$senderType})\n";
echo "   Message: {$messageText}\n";
echo "   Chat ID: {$chat->id}\n\n";

try {
    // Create the message
    $chatMessage = ChatMessage::create([
        'chat_id' => $chat->id,
        'sender_id' => $sender->id,
        'sender_type' => $senderType,
        'message' => $messageText,
        'message_type' => 'text',
        'metadata' => [
            'sent_from' => 'test_script',
            'test' => true
        ]
    ]);

    echo "✅ Message sent successfully!\n";
    echo "   Message ID: {$chatMessage->id}\n";
    echo "   Expected real-time channels:\n";
    echo "     - chat.{$chat->id}\n";
    echo "     - private-admin.chats\n";
    echo "   Event name: message.new\n\n";

    echo "🔔 Check your admin chat page for real-time updates!\n";
    echo "   URL: " . url("/admin/chats/{$chat->id}") . "\n\n";

} catch (Exception $e) {
    echo "❌ Error sending message: " . $e->getMessage() . "\n";
}

echo "🏁 Test completed at " . date('Y-m-d H:i:s') . "\n";
