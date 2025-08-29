<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\User;
use App\Events\NewChatMessage;
use Illuminate\Support\Facades\Http;

class TestRealChatWithData extends Command
{
    protected $signature = 'chat:test-real-data {--reset : Reset data first} {--chat_id=1 : Chat ID to test with}';
    protected $description = 'Test real chat with actual database saving and real-time events';

    public function handle()
    {
        $this->info('🧪 Testing Real Chat with Database Saving...');
        $this->line('');

        // Reset data if requested
        if ($this->option('reset')) {
            $this->info('🗑️ Resetting data first...');
            $this->call('chat:force-reset', ['--create-sample' => true]);
            $this->line('');
        }

        $chatId = $this->option('chat_id');

        // Find or create chat and customer
        $chat = Chat::with('customer')->find($chatId);
        
        if (!$chat) {
            $this->info('📱 Creating fresh chat and customer...');
            
            // Create customer
            $customer = User::firstOrCreate(
                ['email' => 'test-customer@example.com'],
                [
                    'name' => 'احمد الاختبار',
                    'password' => bcrypt('password'),
                    'role' => 'user'
                ]
            );

            // Create chat
            $chat = Chat::create([
                'customer_id' => $customer->id,
                'subject' => 'اختبار المحادثة مع حفظ البيانات',
                'status' => 'open',
                'priority' => 'medium',
                'admin_unread_count' => 0,
                'customer_unread_count' => 0,
                'last_message_at' => now()
            ]);

            $chat->load('customer');
            $this->info("✅ Created Chat ID: {$chat->id}");
            $this->info("✅ Customer: {$chat->customer->name}");
        } else {
            $this->info("✅ Using existing Chat ID: {$chat->id}");
            $this->info("✅ Customer: {$chat->customer->name}");
        }

        $this->line('');

        // Test 1: Direct database insert with event
        $this->info('📝 Test 1: Direct Database Insert + Event...');
        
        $message1 = ChatMessage::create([
            'chat_id' => $chat->id,
            'sender_id' => $chat->customer->id,
            'sender_type' => 'customer',
            'message' => '🧪 رسالة اختبار مباشرة من الداتابيس - ' . now()->format('H:i:s'),
            'message_type' => 'text',
            'metadata' => [
                'test_method' => 'direct_db_insert',
                'command' => 'chat:test-real-data'
            ]
        ]);

        $this->info("✅ Message saved to DB with ID: {$message1->id}");
        $this->line("   - Chat ID: {$message1->chat_id}");
        $this->line("   - Sender: {$message1->sender->name}");
        $this->line("   - Message: {$message1->message}");

        $this->line('');

        // Test 2: Using ChatController API internally
        $this->info('📝 Test 2: Using Chat API Endpoint...');
        
        try {
            // Create a token for the customer
            $token = $chat->customer->createToken('chat-test')->plainTextToken;
            $this->info("✅ Created API token for customer");

            // Make API request
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ])->post('http://127.0.0.1:8000/api/chat/send', [
                'chat_id' => $chat->id,
                'message' => '🚀 رسالة اختبار من API endpoint - ' . now()->format('H:i:s')
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->info("✅ API request successful");
                $this->line("   - Response: " . ($data['success'] ? 'Success' : 'Failed'));
                if (isset($data['data']['message']['id'])) {
                    $this->line("   - Message ID: " . $data['data']['message']['id']);
                }
            } else {
                $this->error("❌ API request failed: " . $response->status());
                $this->line("   - Response: " . $response->body());
            }

        } catch (\Exception $e) {
            $this->error("❌ API test failed: " . $e->getMessage());
        }

        $this->line('');

        // Test 3: Admin reply via Livewire
        $this->info('📝 Test 3: Admin Reply (Simulated)...');
        
        // Find admin user
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $admin = User::create([
                'name' => 'مدير الاختبار',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
                'role' => 'admin'
            ]);
            $this->info("✅ Created admin user");
        }

        $adminMessage = ChatMessage::create([
            'chat_id' => $chat->id,
            'sender_id' => $admin->id,
            'sender_type' => 'admin',
            'message' => '👋 مرحبا، كيف يمكنني مساعدتك؟ - ' . now()->format('H:i:s'),
            'message_type' => 'text',
            'metadata' => [
                'test_method' => 'admin_reply_simulation',
                'sent_from' => 'admin_panel_test'
            ]
        ]);

        $this->info("✅ Admin reply saved with ID: {$adminMessage->id}");

        $this->line('');

        // Verify data is in database
        $this->info('🔍 Verifying Database Data...');
        
        $chatCheck = Chat::with(['messages.sender'])->find($chat->id);
        $this->line("   - Chat ID: {$chatCheck->id}");
        $this->line("   - Total Messages: " . $chatCheck->messages->count());
        $this->line("   - Last Message: " . $chatCheck->messages->last()->message);
        $this->line("   - Last Message Time: " . $chatCheck->messages->last()->created_at->format('H:i:s'));

        $this->line('');

        // Test URLs and expected results
        $this->info('🎯 Test URLs:');
        $this->line("   - Admin Chat List: http://127.0.0.1:8000/admin/chats");
        $this->line("   - Individual Chat: http://127.0.0.1:8000/admin/chats/{$chat->id}");
        $this->line("   - Test Event: http://127.0.0.1:8000/test-chat-event/{$chat->id}");

        $this->line('');
        
        $this->info('✅ Expected Results in Admin Panel:');
        $this->line("   - Chat appears in list with real messages");
        $this->line("   - Shows customer name: {$chat->customer->name}");
        $this->line("   - Shows latest message content");
        $this->line("   - Shows actual timestamps");
        $this->line("   - Real-time updates work when new messages arrive");

        $this->line('');
        
        $this->info('🧪 Manual Test Commands:');
        $this->line("   # Send another test message:");
        $this->line("   php artisan chat:test-real-data --chat_id={$chat->id}");
        $this->line('');
        $this->line("   # Reset and start fresh:");
        $this->line("   php artisan chat:test-real-data --reset");
        $this->line('');
        $this->line("   # Test via route:");
        $this->line("   curl 'http://127.0.0.1:8000/test-chat-event/{$chat->id}'");

        return 0;
    }
}
