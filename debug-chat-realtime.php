<?php

/**
 * Real-time Chat Debug Script
 * Tests Pusher configuration and event broadcasting
 */

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel environment
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\User;
use App\Events\NewChatMessage;
use Pusher\Pusher;

class ChatRealtimeDebugger
{
    private $pusher;
    private $testUser;
    private $testChat;

    public function __construct()
    {
        $this->initializePusher();
        $this->setupTestData();
    }

    private function initializePusher()
    {
        try {
            $this->pusher = new Pusher(
                config('broadcasting.connections.pusher.key'),
                config('broadcasting.connections.pusher.secret'),
                config('broadcasting.connections.pusher.app_id'),
                config('broadcasting.connections.pusher.options')
            );
            echo "✅ Pusher initialized successfully\n";
        } catch (Exception $e) {
            echo "❌ Pusher initialization failed: " . $e->getMessage() . "\n";
            exit(1);
        }
    }

    private function setupTestData()
    {
        // Find or create test user
        $this->testUser = User::firstOrCreate([
            'email' => 'test-customer@example.com'
        ], [
            'name' => 'Test Customer',
            'username' => 'test-customer-' . time(), // Add timestamp to avoid conflicts
            'password' => bcrypt('password'),
            'role' => 'customer',
            'phone' => '123456789',
            'is_active' => true
        ]);

        // Find or create test chat
        $this->testChat = Chat::firstOrCreate([
            'customer_id' => $this->testUser->id,
            'status' => 'open'
        ], [
            'subject' => 'Debug Test Chat',
            'priority' => 'medium'
        ]);

        echo "✅ Test data ready:\n";
        echo "   User ID: {$this->testUser->id}\n";
        echo "   Chat ID: {$this->testChat->id}\n\n";
    }

    public function testPusherConfig()
    {
        echo "🔧 PUSHER CONFIGURATION TEST\n";
        echo "============================\n";
        
        echo "Broadcasting Driver: " . config('broadcasting.default') . "\n";
        echo "Pusher App ID: " . config('broadcasting.connections.pusher.app_id') . "\n";
        echo "Pusher Key: " . config('broadcasting.connections.pusher.key') . "\n";
        echo "Pusher Secret: " . substr(config('broadcasting.connections.pusher.secret'), 0, 4) . "...\n";
        echo "Pusher Cluster: " . config('broadcasting.connections.pusher.options.cluster') . "\n";
        echo "Auth Endpoint: " . url('/broadcasting/auth') . "\n\n";
    }

    public function testChannelInfo()
    {
        echo "📡 CHANNEL INFORMATION TEST\n";
        echo "===========================\n";
        
        try {
            $channels = $this->pusher->get_channels();
            echo "Active Channels: " . json_encode($channels, JSON_PRETTY_PRINT) . "\n";
        } catch (Exception $e) {
            echo "❌ Failed to get channel info: " . $e->getMessage() . "\n";
        }
        echo "\n";
    }

    public function testEventBroadcast()
    {
        echo "🚀 EVENT BROADCAST TEST\n";
        echo "=======================\n";
        
        try {
            // Create a test message
            $message = ChatMessage::create([
                'chat_id' => $this->testChat->id,
                'sender_id' => $this->testUser->id,
                'sender_type' => 'customer',
                'message' => '🧪 Debug test message at ' . now()->format('H:i:s'),
                'message_type' => 'text',
                'metadata' => [
                    'test' => true,
                    'sent_from' => 'debug_script'
                ]
            ]);

            echo "✅ Test message created (ID: {$message->id})\n";
            echo "   Chat ID: {$message->chat_id}\n";
            echo "   Sender: {$message->sender->name} ({$message->sender_type})\n";
            echo "   Message: {$message->message}\n";
            echo "   Expected Channels:\n";
            echo "     - chat.{$message->chat_id}\n";
            echo "     - private-admin.chats\n";
            echo "   Event Name: message.new\n\n";

            // Wait a moment for the event to be processed
            sleep(2);

            return $message;

        } catch (Exception $e) {
            echo "❌ Event broadcast test failed: " . $e->getMessage() . "\n";
            return null;
        }
    }

    public function testDirectPusherBroadcast()
    {
        echo "📤 DIRECT PUSHER BROADCAST TEST\n";
        echo "===============================\n";
        
        try {
            $testData = [
                'test' => true,
                'message' => 'Direct Pusher test at ' . now()->format('H:i:s'),
                'timestamp' => now()->toISOString()
            ];

            // Test public channel
            $result1 = $this->pusher->trigger(
                'chat.' . $this->testChat->id,
                'test.message',
                $testData
            );

            // Test private channel
            $result2 = $this->pusher->trigger(
                'private-admin.chats',
                'test.message',
                $testData
            );

            echo "✅ Direct broadcast successful:\n";
            echo "   Public Channel (chat.{$this->testChat->id}): " . ($result1 ? 'SUCCESS' : 'FAILED') . "\n";
            echo "   Private Channel (private-admin.chats): " . ($result2 ? 'SUCCESS' : 'FAILED') . "\n";
            echo "   Event: test.message\n";
            echo "   Data: " . json_encode($testData, JSON_PRETTY_PRINT) . "\n\n";

        } catch (Exception $e) {
            echo "❌ Direct broadcast failed: " . $e->getMessage() . "\n";
        }
    }

    public function testWebhookEndpoint()
    {
        echo "🌐 WEBHOOK ENDPOINT TEST\n";
        echo "========================\n";
        
        $authEndpoint = url('/broadcasting/auth');
        echo "Auth Endpoint: {$authEndpoint}\n";
        
        try {
            $response = file_get_contents($authEndpoint, false, stream_context_create([
                'http' => [
                    'method' => 'GET',
                    'header' => 'Accept: application/json'
                ]
            ]));
            echo "✅ Endpoint accessible\n";
        } catch (Exception $e) {
            echo "❌ Endpoint test failed: " . $e->getMessage() . "\n";
        }
        echo "\n";
    }

    public function runFullTest()
    {
        echo "🔥 FULL REAL-TIME CHAT DEBUG TEST\n";
        echo "==================================\n\n";

        $this->testPusherConfig();
        $this->testChannelInfo();
        $this->testWebhookEndpoint();
        $this->testDirectPusherBroadcast();
        $this->testEventBroadcast();

        echo "🏁 DEBUG TEST COMPLETED\n";
        echo "=======================\n";
        echo "Check your browser console and admin chat page for real-time updates!\n";
        echo "Expected to see events on channels:\n";
        echo "- chat.{$this->testChat->id}\n";
        echo "- private-admin.chats\n\n";
    }

    public function cleanup()
    {
        echo "🧹 Cleaning up test data...\n";
        
        // Delete test messages
        $deletedMessages = ChatMessage::where('chat_id', $this->testChat->id)
            ->where('metadata->test', true)
            ->delete();
        
        echo "   Deleted {$deletedMessages} test messages\n";
        echo "✅ Cleanup completed\n";
    }
}

// Main execution
if ($argc < 2) {
    echo "Usage: php debug-chat-realtime.php [command]\n\n";
    echo "Commands:\n";
    echo "  config     - Test Pusher configuration\n";
    echo "  channels   - Test channel information\n";
    echo "  broadcast  - Test event broadcasting\n";
    echo "  direct     - Test direct Pusher broadcast\n";
    echo "  webhook    - Test webhook endpoint\n";
    echo "  full       - Run all tests\n";
    echo "  cleanup    - Clean up test data\n\n";
    exit(1);
}

$command = $argv[1];
$debugger = new ChatRealtimeDebugger();

switch ($command) {
    case 'config':
        $debugger->testPusherConfig();
        break;
        
    case 'channels':
        $debugger->testChannelInfo();
        break;
        
    case 'broadcast':
        $debugger->testEventBroadcast();
        break;
        
    case 'direct':
        $debugger->testDirectPusherBroadcast();
        break;
        
    case 'webhook':
        $debugger->testWebhookEndpoint();
        break;
        
    case 'full':
        $debugger->runFullTest();
        break;
        
    case 'cleanup':
        $debugger->cleanup();
        break;
        
    default:
        echo "❌ Unknown command: {$command}\n";
        echo "Run without arguments to see available commands.\n";
        break;
}

echo "\n🏁 Debug script completed at " . date('Y-m-d H:i:s') . "\n";
