<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\User;

class CheckChatData extends Command
{
    protected $signature = 'chat:check-data';
    protected $description = 'Check current chat data in database';

    public function handle()
    {
        $this->info('🔍 Checking Current Chat Data in Database...');
        $this->line('');

        // Count total records
        $chatCount = Chat::count();
        $messageCount = ChatMessage::count();
        $customerCount = User::where('role', 'user')->count();
        $adminCount = User::where('role', 'admin')->count();

        $this->info('📊 Database Counts:');
        $this->line("   - Total Chats: {$chatCount}");
        $this->line("   - Total Messages: {$messageCount}");
        $this->line("   - Customers: {$customerCount}");
        $this->line("   - Admins: {$adminCount}");

        $this->line('');

        if ($chatCount === 0) {
            $this->warn('⚠️ No chats found in database!');
            $this->info('💡 Run: php artisan chat:force-reset --create-sample');
            return 0;
        }

        // Show recent chats
        $this->info('📋 Recent Chats:');
        $recentChats = Chat::with(['customer', 'messages'])
            ->latest()
            ->take(5)
            ->get();

        foreach ($recentChats as $chat) {
            $this->line('');
            $this->line("   Chat #{$chat->id}:");
            $this->line("   ├─ Subject: {$chat->subject}");
            $this->line("   ├─ Customer: {$chat->customer->name} ({$chat->customer->email})");
            $this->line("   ├─ Status: {$chat->status}");
            $this->line("   ├─ Messages: {$chat->messages->count()}");
            $this->line("   └─ Created: {$chat->created_at->format('Y-m-d H:i:s')}");
        }

        $this->line('');

        // Show recent messages
        if ($messageCount > 0) {
            $this->info('💬 Recent Messages:');
            $recentMessages = ChatMessage::with(['sender', 'chat'])
                ->latest()
                ->take(5)
                ->get();

            foreach ($recentMessages as $message) {
                $this->line('');
                $this->line("   Message #{$message->id}:");
                $this->line("   ├─ Chat: #{$message->chat_id} - {$message->chat->subject}");
                $this->line("   ├─ Sender: {$message->sender->name} ({$message->sender_type})");
                $this->line("   ├─ Content: " . substr($message->message, 0, 60) . "...");
                $this->line("   └─ Time: {$message->created_at->format('Y-m-d H:i:s')}");
            }
        } else {
            $this->warn('⚠️ No messages found!');
        }

        $this->line('');

        // Show admin panel links
        $this->info('🔗 Admin Panel Links:');
        if ($chatCount > 0) {
            $firstChat = Chat::first();
            $this->line("   - All Chats: http://127.0.0.1:8000/admin/chats");
            $this->line("   - First Chat: http://127.0.0.1:8000/admin/chats/{$firstChat->id}");
            $this->line("   - Test Event: http://127.0.0.1:8000/test-chat-event/{$firstChat->id}");
        }

        $this->line('');

        // Test suggestions
        $this->info('🧪 Test Suggestions:');
        if ($chatCount === 0) {
            $this->line('   1. Create sample data: php artisan chat:force-reset --create-sample');
            $this->line('   2. Then check again: php artisan chat:check-data');
        } else {
            $firstChat = Chat::first();
            $this->line("   1. Test real-time: php artisan chat:test-real-data --chat_id={$firstChat->id}");
            $this->line("   2. Open admin panel: http://127.0.0.1:8000/admin/chats");
            $this->line("   3. Send test message: curl 'http://127.0.0.1:8000/test-chat-event/{$firstChat->id}'");
            $this->line("   4. Watch for real-time updates!");
        }

        return 0;
    }
}
