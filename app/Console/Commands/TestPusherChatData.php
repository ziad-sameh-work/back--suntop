<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PusherChat;
use App\Models\PusherMessage;
use App\Models\User;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Http;

class TestPusherChatData extends Command
{
    protected $signature = 'pusher-chat:test {--reset : Reset pusher data first} {--chat_id=1 : Chat ID to test with}';
    protected $description = 'Test Pusher chat with actual database saving and real-time events';

    public function handle()
    {
        $this->info('🧪 Testing Pusher Chat with Database Saving...');
        $this->line('');

        // Reset data if requested
        if ($this->option('reset')) {
            $this->info('🗑️ Resetting pusher data first...');
            
            try {
                PusherMessage::truncate();
                $this->info('✅ Pusher messages cleared');
            } catch (\Exception $e) {
                $this->warn('⚠️ Could not clear pusher messages: ' . $e->getMessage());
            }

            try {
                PusherChat::truncate();
                $this->info('✅ Pusher chats cleared');
            } catch (\Exception $e) {
                $this->warn('⚠️ Could not clear pusher chats: ' . $e->getMessage());
            }

            $this->line('');
        }

        $chatId = $this->option('chat_id');

        // Find or create pusher chat and customer
        $pusherChat = PusherChat::with('customer')->find($chatId);
        
        if (!$pusherChat) {
            $this->info('📱 Creating fresh pusher chat and customer...');
            
            // Create customer
            $customer = User::firstOrCreate(
                ['email' => 'pusher-test@example.com'],
                [
                    'name' => 'عميل Pusher',
                    'password' => bcrypt('password'),
                    'role' => 'user'
                ]
            );

            // Create pusher chat
            $pusherChat = PusherChat::create([
                'user_id' => $customer->id,
                'subject' => 'اختبار محادثة Pusher',
                'status' => 'open',
                'metadata' => [
                    'created_by' => 'test_command',
                    'test' => true
                ]
            ]);

            $pusherChat->load('customer');
            $this->info("✅ Created Pusher Chat ID: {$pusherChat->id}");
            $this->info("✅ Customer: {$pusherChat->customer->name}");
        } else {
            $this->info("✅ Using existing Pusher Chat ID: {$pusherChat->id}");
            $this->info("✅ Customer: {$pusherChat->customer->name}");
        }

        $this->line('');

        // Test 1: Direct database insert with Pusher event
        $this->info('📝 Test 1: Direct Pusher Message Insert + Event...');
        
        $message1 = PusherMessage::create([
            'chat_id' => $pusherChat->id,
            'user_id' => $pusherChat->customer->id,
            'message' => '🧪 رسالة Pusher اختبار مباشرة - ' . now()->format('H:i:s'),
            'metadata' => [
                'test_method' => 'direct_pusher_insert',
                'command' => 'pusher-chat:test'
            ]
        ]);

        $this->info("✅ Pusher Message saved to DB with ID: {$message1->id}");
        $this->line("   - Chat ID: {$message1->chat_id}");
        $this->line("   - User: {$message1->user->name}");
        $this->line("   - Message: {$message1->message}");

        // Trigger Pusher event manually
        try {
            event(new MessageSent($message1));
            $this->info("✅ MessageSent event triggered for Pusher");
        } catch (\Exception $e) {
            $this->error("❌ Failed to trigger MessageSent event: " . $e->getMessage());
        }

        $this->line('');

        // Test 2: Using PusherChatController API
        $this->info('📝 Test 2: Using Pusher Chat API Endpoint...');
        
        try {
            // Create a token for the customer
            $token = $pusherChat->customer->createToken('pusher-chat-test')->plainTextToken;
            $this->info("✅ Created API token for customer");

            // Make API request to pusher chat endpoint
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ])->post('https://suntop-eg.com/api/pusher-chat/messages', [
                'chat_id' => $pusherChat->id,
                'message' => '🚀 رسالة Pusher من API endpoint - ' . now()->format('H:i:s')
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->info("✅ Pusher API request successful");
                $this->line("   - Response: " . ($data['success'] ? 'Success' : 'Failed'));
                if (isset($data['data']['message']['id'])) {
                    $this->line("   - Message ID: " . $data['data']['message']['id']);
                }
            } else {
                $this->error("❌ Pusher API request failed: " . $response->status());
                $this->line("   - Response: " . $response->body());
            }

        } catch (\Exception $e) {
            $this->error("❌ Pusher API test failed: " . $e->getMessage());
        }

        $this->line('');

        // Test 3: Create another message for list testing
        $this->info('📝 Test 3: Additional Messages for List Testing...');
        
        $message3 = PusherMessage::create([
            'chat_id' => $pusherChat->id,
            'user_id' => $pusherChat->customer->id,
            'message' => '💬 رسالة أخرى للاختبار - ' . now()->format('H:i:s'),
            'metadata' => [
                'test_method' => 'additional_message',
                'for' => 'list_testing'
            ]
        ]);

        $this->info("✅ Additional message created with ID: {$message3->id}");

        $this->line('');

        // Verify data is in database
        $this->info('🔍 Verifying Pusher Database Data...');
        
        $chatCheck = PusherChat::with(['messages.user'])->find($pusherChat->id);
        $this->line("   - Pusher Chat ID: {$chatCheck->id}");
        $this->line("   - Total Messages: " . $chatCheck->messages->count());
        if ($chatCheck->messages->count() > 0) {
            $lastMessage = $chatCheck->messages->sortByDesc('created_at')->first();
            $this->line("   - Last Message: " . substr($lastMessage->message, 0, 50) . "...");
            $this->line("   - Last Message Time: " . $lastMessage->created_at->format('H:i:s'));
        }

        $this->line('');

        // Test URLs and expected results
        $this->info('🎯 Test URLs:');
        $this->line("   - Admin Chat List: https://suntop-eg.com/admin/chats");
        $this->line("   - Pusher Chat API: https://suntop-eg.com/api/pusher-chat/start");
        $this->line("   - Check Data: php artisan pusher-chat:check");

        $this->line('');
        
        $this->info('✅ Expected Results in Admin Panel:');
        $this->line("   - Pusher chat appears in combined list");
        $this->line("   - Shows customer name: {$pusherChat->customer->name}");
        $this->line("   - Shows latest message content");
        $this->line("   - Shows 'محادثة Pusher' or actual subject");
        $this->line("   - Real-time updates work when new Pusher messages arrive");

        $this->line('');
        
        $this->info('🧪 Manual Test Commands:');
        $this->line("   # Test pusher chat again:");
        $this->line("   php artisan pusher-chat:test --chat_id={$pusherChat->id}");
        $this->line('');
        $this->line("   # Reset and start fresh:");
        $this->line("   php artisan pusher-chat:test --reset");
        $this->line('');
        $this->line("   # Check pusher data:");
        $this->line("   php artisan pusher-chat:check");

        return 0;
    }
}
