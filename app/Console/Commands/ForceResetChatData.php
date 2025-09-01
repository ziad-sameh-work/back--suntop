<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\User;

class ForceResetChatData extends Command
{
    protected $signature = 'chat:force-reset {--create-sample : Create sample data after reset}';
    protected $description = 'Force reset all chat data using raw SQL (bypasses foreign key constraints)';

    public function handle()
    {
        $this->info('🗑️ Force Resetting Chat Data with Raw SQL...');
        $this->line('');

        // Confirm before proceeding
        if (!$this->confirm('This will FORCE DELETE ALL chat data using raw SQL. Are you sure?')) {
            $this->info('Operation cancelled.');
            return 0;
        }

        try {
            // Disable foreign key checks
            $this->info('🔓 Disabling foreign key checks...');
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // Clear tables in correct order (child tables first)
            $this->info('🧹 Clearing chat_messages table...');
            DB::statement('TRUNCATE TABLE chat_messages;');
            $this->info('✅ Chat messages cleared');

            $this->info('🧹 Clearing chats table...');
            DB::statement('TRUNCATE TABLE chats;');
            $this->info('✅ Chats cleared');

            // Try to clear pusher tables if they exist
            $this->info('🧹 Clearing pusher tables...');
            try {
                DB::statement('TRUNCATE TABLE pusher_messages;');
                $this->info('✅ Pusher messages cleared');
            } catch (\Exception $e) {
                $this->warn('⚠️ Pusher messages table might not exist');
            }

            try {
                DB::statement('TRUNCATE TABLE pusher_chats;');
                $this->info('✅ Pusher chats cleared');
            } catch (\Exception $e) {
                $this->warn('⚠️ Pusher chats table might not exist');
            }

            // Re-enable foreign key checks
            $this->info('🔒 Re-enabling foreign key checks...');
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            $this->line('');
            $this->info('🎉 All chat data force cleared successfully!');

            // Reset auto increment counters
            $this->info('🔄 Resetting auto increment counters...');
            DB::statement('ALTER TABLE chats AUTO_INCREMENT = 1;');
            DB::statement('ALTER TABLE chat_messages AUTO_INCREMENT = 1;');
            $this->info('✅ Auto increment counters reset');

        } catch (\Exception $e) {
            $this->error('❌ Error during force reset: ' . $e->getMessage());
            
            // Make sure to re-enable foreign key checks even if there's an error
            try {
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            } catch (\Exception $e2) {
                $this->error('❌ Could not re-enable foreign key checks');
            }
            
            return 1;
        }

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
                'name' => 'احمد محمد',
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
            'subject' => 'استفسار عن الطلب رقم #12345',
            'status' => 'open',
            'priority' => 'medium',
            'admin_unread_count' => 1,
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
            'message' => 'السلام عليكم، أريد الاستفسار عن حالة الطلب رقم #12345. لم يصلني أي تحديث منذ 3 أيام.',
            'message_type' => 'text',
            'metadata' => [
                'created_by' => 'force_reset_command',
                'timestamp' => now()->toISOString()
            ]
        ]);

        $this->info('✅ Created initial message');
        $this->line("   - Message ID: {$message->id}");
        $this->line("   - Content: " . substr($message->message, 0, 50) . "...");

        // Create second chat for testing multiple chats
        $chat2 = Chat::create([
            'customer_id' => $customer->id,
            'subject' => 'مشكلة في المنتج',
            'status' => 'open',
            'priority' => 'high',
            'admin_unread_count' => 2,
            'customer_unread_count' => 0,
            'last_message_at' => now()->subMinutes(30)
        ]);

        $message2 = ChatMessage::create([
            'chat_id' => $chat2->id,
            'sender_id' => $customer->id,
            'sender_type' => 'customer',
            'message' => 'المنتج وصل معطوب، أريد إرجاعه واستبداله بمنتج جديد.',
            'message_type' => 'text',
            'metadata' => [
                'created_by' => 'force_reset_command',
                'timestamp' => now()->subMinutes(30)->toISOString()
            ]
        ]);

        $this->line('');
        $this->info('🎯 Sample data created successfully!');
        $this->line("   - Chat 1 ID: {$chat->id} (New inquiry)");
        $this->line("   - Chat 2 ID: {$chat2->id} (Product issue)");
        $this->line("   - Customer Email: {$customer->email}");
        $this->line("   - Customer Password: password");
        $this->line("   - Total Messages: 2");
    }
}
