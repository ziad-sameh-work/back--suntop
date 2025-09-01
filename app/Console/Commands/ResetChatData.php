<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\PusherChat;
use App\Models\PusherMessage;
use App\Models\User;

class ResetChatData extends Command
{
    protected $signature = 'chat:reset {--create-sample : Create sample data after reset}';
    protected $description = 'Reset all chat data and optionally create sample data';

    public function handle()
    {
        $this->info('🗑️ Resetting Chat Data...');
        $this->line('');

        // Confirm before proceeding
        if (!$this->confirm('This will delete ALL chat data. Are you sure?')) {
            $this->info('Operation cancelled.');
            return 0;
        }

        // Disable foreign key checks temporarily
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $this->info('🧹 Clearing chat messages...');
        try {
            ChatMessage::truncate();
            $this->info('✅ Chat messages cleared');
        } catch (\Exception $e) {
            $this->warn('⚠️ Using DELETE instead of TRUNCATE for chat messages');
            ChatMessage::query()->delete();
            $this->info('✅ Chat messages deleted');
        }

        $this->info('🧹 Clearing chats...');
        try {
            Chat::truncate();
            $this->info('✅ Chats cleared');
        } catch (\Exception $e) {
            $this->warn('⚠️ Using DELETE instead of TRUNCATE for chats');
            Chat::query()->delete();
            $this->info('✅ Chats deleted');
        }

        $this->info('🧹 Clearing pusher messages...');
        try {
            PusherMessage::truncate();
            $this->info('✅ Pusher messages cleared');
        } catch (\Exception $e) {
            $this->warn('⚠️ Pusher messages table might not exist or using DELETE');
            try {
                PusherMessage::query()->delete();
                $this->info('✅ Pusher messages deleted');
            } catch (\Exception $e2) {
                $this->warn('⚠️ Pusher messages table not available');
            }
        }

        $this->info('🧹 Clearing pusher chats...');
        try {
            PusherChat::truncate();
            $this->info('✅ Pusher chats cleared');
        } catch (\Exception $e) {
            $this->warn('⚠️ Pusher chats table might not exist or using DELETE');
            try {
                PusherChat::query()->delete();
                $this->info('✅ Pusher chats deleted');
            } catch (\Exception $e2) {
                $this->warn('⚠️ Pusher chats table not available');
            }
        }

        // Re-enable foreign key checks
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->line('');
        $this->info('🎉 All chat data cleared successfully!');

        // Create sample data if requested
        if ($this->option('create-sample')) {
            $this->line('');
            $this->info('📱 Creating sample data...');
            $this->createSampleData();
        }

        $this->line('');
        $this->info('🔍 Current data counts:');
        $this->line("   - Chats: " . Chat::count());
        $this->line("   - Chat Messages: " . ChatMessage::count());
        
        try {
            $this->line("   - Pusher Chats: " . PusherChat::count());
            $this->line("   - Pusher Messages: " . PusherMessage::count());
        } catch (\Exception $e) {
            $this->line("   - Pusher tables: Not available");
        }

        $this->line('');
        $this->info('🚀 Ready for fresh testing!');
        $this->line('');
        $this->info('🎯 Next steps:');
        $this->line('1. Open admin panel: https://suntop-eg.com/admin/chats');
        $this->line('2. Test with: https://suntop-eg.com/test-chat-event/1');
        $this->line('3. Or use: php artisan chat:test-events');

        return 0;
    }

    private function createSampleData()
    {
        // Find or create a test customer
        $customer = User::where('email', 'customer@test.com')->first();
        
        if (!$customer) {
            $customer = User::create([
                'name' => 'Test Customer',
                'email' => 'customer@test.com',
                'password' => bcrypt('password'),
                'role' => 'user'
            ]);
            $this->info('✅ Created test customer: ' . $customer->name);
        } else {
            $this->info('✅ Using existing customer: ' . $customer->name);
        }

        // Create a fresh chat
        $chat = Chat::create([
            'customer_id' => $customer->id,
            'subject' => 'Fresh Test Chat - ' . now()->format('H:i'),
            'status' => 'open',
            'priority' => 'medium',
            'admin_unread_count' => 0,
            'customer_unread_count' => 0,
            'last_message_at' => now()
        ]);

        $this->info('✅ Created test chat: ' . $chat->subject);
        $this->line("   - Chat ID: {$chat->id}");
        $this->line("   - Customer: {$customer->name}");
        $this->line("   - Status: {$chat->status}");

        // Create initial message
        $message = ChatMessage::create([
            'chat_id' => $chat->id,
            'sender_id' => $customer->id,
            'sender_type' => 'customer',
            'message' => 'مرحبا، أحتاج مساعدة في الطلب رقم #12345',
            'message_type' => 'text',
            'metadata' => [
                'created_by' => 'sample_command'
            ]
        ]);

        $this->info('✅ Created initial message');
        $this->line("   - Message ID: {$message->id}");
        $this->line("   - Content: " . substr($message->message, 0, 50) . "...");

        $this->line('');
        $this->info('🎯 Sample data created successfully!');
        $this->line("   - Use Chat ID: {$chat->id} for testing");
        $this->line("   - Customer Email: {$customer->email}");
        $this->line("   - Customer Password: password");
    }
}
