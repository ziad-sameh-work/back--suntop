<?php

/**
 * Real-time Chat Test Script
 * Tests Pusher broadcasting and chat functionality from terminal
 */

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Support\Facades\DB;

// Load Laravel environment
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

class ChatTester
{
    private $adminUser;
    private $customerUser;
    private $testChat;

    public function __construct()
    {
        $this->setupTestUsers();
        $this->setupTestChat();
    }

    private function setupTestUsers()
    {
        // Find or create admin user
        $this->adminUser = User::where('role', 'admin')->first();
        if (!$this->adminUser) {
            $this->adminUser = User::create([
                'name' => 'Test Admin',
                'email' => 'admin@test.com',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'phone' => '1234567890'
            ]);
        }

        // Find or create customer user
        $this->customerUser = User::where('role', 'customer')->first();
        if (!$this->customerUser) {
            $this->customerUser = User::create([
                'name' => 'Test Customer',
                'email' => 'customer@test.com',
                'password' => bcrypt('password'),
                'role' => 'customer',
                'phone' => '0987654321'
            ]);
        }

        echo "✅ Test users ready:\n";
        echo "   Admin: {$this->adminUser->name} (ID: {$this->adminUser->id})\n";
        echo "   Customer: {$this->customerUser->name} (ID: {$this->customerUser->id})\n\n";
    }

    private function setupTestChat()
    {
        // Create or find test chat
        $this->testChat = Chat::firstOrCreate([
            'customer_id' => $this->customerUser->id,
        ], [
            'subject' => 'Real-time Test Chat',
            'status' => 'open',
            'priority' => 'medium',
            'assigned_admin_id' => $this->adminUser->id,
        ]);

        echo "✅ Test chat ready: ID {$this->testChat->id}\n";
        echo "   Subject: {$this->testChat->subject}\n";
        echo "   Status: {$this->testChat->status}\n\n";
    }

    public function sendTestMessage($senderType = 'customer', $message = null)
    {
        $sender = $senderType === 'admin' ? $this->adminUser : $this->customerUser;
        $message = $message ?: "Test message from {$senderType} at " . date('H:i:s');

        echo "📤 Sending message from {$senderType}...\n";
        echo "   Sender: {$sender->name}\n";
        echo "   Message: {$message}\n";

        try {
            $chatMessage = ChatMessage::create([
                'chat_id' => $this->testChat->id,
                'sender_id' => $sender->id,
                'sender_type' => $senderType,
                'message' => $message,
                'message_type' => 'text',
                'metadata' => [
                    'sent_from' => 'terminal_test',
                    'test_mode' => true
                ]
            ]);

            echo "✅ Message sent successfully!\n";
            echo "   Message ID: {$chatMessage->id}\n";
            echo "   Created at: {$chatMessage->created_at}\n";
            echo "   🔔 Pusher event should be broadcasted now!\n\n";

            return $chatMessage;

        } catch (Exception $e) {
            echo "❌ Error sending message: {$e->getMessage()}\n\n";
            return false;
        }
    }

    public function testSequence()
    {
        echo "🚀 Starting Real-time Chat Test Sequence...\n\n";

        // Test 1: Customer message
        echo "=== TEST 1: Customer Message ===\n";
        $this->sendTestMessage('customer', 'Hello, I need help with my order!');
        sleep(2);

        // Test 2: Admin reply
        echo "=== TEST 2: Admin Reply ===\n";
        $this->sendTestMessage('admin', 'Hi! I\'m here to help. What\'s your order number?');
        sleep(2);

        // Test 3: Customer response
        echo "=== TEST 3: Customer Response ===\n";
        $this->sendTestMessage('customer', 'My order number is #12345');
        sleep(2);

        // Test 4: Admin final message
        echo "=== TEST 4: Admin Final Message ===\n";
        $this->sendTestMessage('admin', 'I found your order. It will be delivered tomorrow!');

        echo "🎉 Test sequence completed!\n\n";
    }

    public function showChatInfo()
    {
        $this->testChat->refresh();
        $messageCount = $this->testChat->messages()->count();

        echo "📊 Chat Information:\n";
        echo "   Chat ID: {$this->testChat->id}\n";
        echo "   Customer: {$this->customerUser->name}\n";
        echo "   Admin: {$this->adminUser->name}\n";
        echo "   Total Messages: {$messageCount}\n";
        echo "   Last Message: {$this->testChat->last_message_at}\n";
        echo "   Customer Unread: {$this->testChat->customer_unread_count}\n";
        echo "   Admin Unread: {$this->testChat->admin_unread_count}\n\n";
    }

    public function showRecentMessages($limit = 5)
    {
        $messages = $this->testChat->messages()
            ->with('sender')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        echo "💬 Recent Messages:\n";
        foreach ($messages->reverse() as $msg) {
            $time = $msg->created_at->format('H:i:s');
            $sender = $msg->sender->name;
            $type = $msg->sender_type;
            echo "   [{$time}] {$sender} ({$type}): {$msg->message}\n";
        }
        echo "\n";
    }

    public function cleanup()
    {
        echo "🧹 Cleaning up test data...\n";
        
        // Delete test messages
        $deletedMessages = $this->testChat->messages()->delete();
        echo "   Deleted {$deletedMessages} test messages\n";
        
        // Delete test chat
        $this->testChat->delete();
        echo "   Deleted test chat\n";
        
        echo "✅ Cleanup completed!\n\n";
    }
}

// Main execution
echo "🔥 PUSHER REAL-TIME CHAT TESTER 🔥\n";
echo "==================================\n\n";

if ($argc < 2) {
    echo "Usage: php test-realtime-chat.php [command]\n\n";
    echo "Commands:\n";
    echo "  info           - Show chat information\n";
    echo "  customer       - Send customer message\n";
    echo "  admin          - Send admin message\n";
    echo "  sequence       - Run full test sequence\n";
    echo "  messages       - Show recent messages\n";
    echo "  cleanup        - Clean up test data\n\n";
    echo "Examples:\n";
    echo "  php test-realtime-chat.php customer\n";
    echo "  php test-realtime-chat.php admin\n";
    echo "  php test-realtime-chat.php sequence\n\n";
    exit(1);
}

$command = $argv[1];
$tester = new ChatTester();

switch ($command) {
    case 'info':
        $tester->showChatInfo();
        break;
        
    case 'customer':
        $message = isset($argv[2]) ? $argv[2] : null;
        $tester->sendTestMessage('customer', $message);
        break;
        
    case 'admin':
        $message = isset($argv[2]) ? $argv[2] : null;
        $tester->sendTestMessage('admin', $message);
        break;
        
    case 'sequence':
        $tester->testSequence();
        break;
        
    case 'messages':
        $tester->showRecentMessages();
        break;
        
    case 'cleanup':
        $tester->cleanup();
        break;
        
    default:
        echo "❌ Unknown command: {$command}\n";
        echo "Run without arguments to see available commands.\n";
        break;
}

echo "🏁 Test completed at " . date('Y-m-d H:i:s') . "\n";
