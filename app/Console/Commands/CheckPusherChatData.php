<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\PusherChat;
use App\Models\PusherMessage;
use App\Models\User;

class CheckPusherChatData extends Command
{
    protected $signature = 'pusher-chat:check';
    protected $description = 'Check pusher chat data vs regular chat data in database';

    public function handle()
    {
        $this->info('🔍 Checking Chat Data in Database (Regular vs Pusher)...');
        $this->line('');

        // Count regular records
        $regularChatCount = Chat::count();
        $regularMessageCount = ChatMessage::count();

        // Count pusher records
        $pusherChatCount = PusherChat::count();
        $pusherMessageCount = PusherMessage::count();

        $customerCount = User::where('role', 'user')->count();
        $adminCount = User::where('role', 'admin')->count();

        $this->info('📊 Database Counts Comparison:');
        $this->line('');
        
        $this->line('   🔵 Regular Chat System:');
        $this->line("      - Chats: {$regularChatCount}");
        $this->line("      - Messages: {$regularMessageCount}");
        
        $this->line('');
        $this->line('   🟢 Pusher Chat System:');
        $this->line("      - Chats: {$pusherChatCount}");
        $this->line("      - Messages: {$pusherMessageCount}");
        
        $this->line('');
        $this->line('   👥 Users:');
        $this->line("      - Customers: {$customerCount}");
        $this->line("      - Admins: {$adminCount}");

        $this->line('');

        // Show recent regular chats
        if ($regularChatCount > 0) {
            $this->info('📋 Recent Regular Chats:');
            $recentRegularChats = Chat::with(['customer', 'messages'])
                ->latest()
                ->take(3)
                ->get();

            foreach ($recentRegularChats as $chat) {
                $this->line("   🔵 Chat #{$chat->id}: {$chat->subject}");
                $this->line("      Customer: {$chat->customer->name}");
                $this->line("      Messages: {$chat->messages->count()}");
                $this->line("      Status: {$chat->status}");
                $this->line('');
            }
        } else {
            $this->warn('⚠️ No regular chats found!');
            $this->line('');
        }

        // Show recent pusher chats
        if ($pusherChatCount > 0) {
            $this->info('📋 Recent Pusher Chats:');
            $recentPusherChats = PusherChat::with(['customer', 'messages'])
                ->latest()
                ->take(3)
                ->get();

            foreach ($recentPusherChats as $chat) {
                $this->line("   Pusher Chat #{$chat->id}: " . ($chat->subject ?: 'محادثة Pusher'));
                $this->line("      Customer: {$chat->customer->name}");
                $this->line("      Messages: {$chat->messages->count()}");
                $this->line("      Status: {$chat->status}");
                $this->line('');
            }
        } else {
            $this->warn('⚠️ No pusher chats found!');
            $this->line('');
        }

        // Show recent messages comparison
        $this->info('💬 Recent Messages Comparison:');
        $this->line('');

        if ($regularMessageCount > 0) {
            $this->line('   🔵 Latest Regular Messages:');
            $recentRegularMessages = ChatMessage::with(['sender', 'chat'])
                ->latest()
                ->take(2)
                ->get();

            foreach ($recentRegularMessages as $message) {
                $this->line("      #{$message->id}: " . substr($message->message, 0, 40) . "...");
                $this->line("         From: {$message->sender->name} ({$message->sender_type})");
                $this->line("         Chat: #{$message->chat_id}");
                $this->line('');
            }
        }

        if ($pusherMessageCount > 0) {
            $this->line('   🟢 Latest Pusher Messages:');
            $recentPusherMessages = PusherMessage::with(['user', 'chat'])
                ->latest()
                ->take(2)
                ->get();

            foreach ($recentPusherMessages as $message) {
                $this->line("      #{$message->id}: " . substr($message->message, 0, 40) . "...");
                $this->line("         From: {$message->user->name}");
                $this->line("         Chat: #{$message->chat_id}");
                $this->line('');
            }
        }

        // Admin panel status
        $this->info('🔗 Admin Panel Status:');
        $this->line("   - Admin Panel: http://127.0.0.1:8000/admin/chats");
        $this->line("   - Should show: Combined chats (Regular + Pusher)");
        $this->line("   - Total visible: " . ($regularChatCount + $pusherChatCount) . " chats");

        $this->line('');

        // Recommendations
        $this->info('💡 Recommendations:');
        
        if ($pusherChatCount === 0 && $regularChatCount === 0) {
            $this->line('   1. Create test data: php artisan pusher-chat:test --reset');
            $this->line('   2. Or create regular data: php artisan chat:force-reset --create-sample');
        } elseif ($pusherChatCount === 0) {
            $this->line('   1. Create pusher test data: php artisan pusher-chat:test --reset');
            $this->line('   2. Test real-time with pusher: http://127.0.0.1:8000/api/pusher-chat/start');
        } elseif ($regularChatCount === 0) {
            $this->line('   1. Create regular test data: php artisan chat:force-reset --create-sample');
            $this->line('   2. Both systems will be visible in admin panel');
        } else {
            $this->line('   ✅ Both systems have data - Admin panel should show combined results');
            $this->line('   1. Test pusher real-time: php artisan pusher-chat:test');
            $this->line('   2. Test regular real-time: php artisan chat:test-real-data');
        }

        $this->line('');

        // Test commands
        $this->info('🧪 Available Test Commands:');
        $this->line('   - php artisan pusher-chat:test --reset    # Test pusher system');
        $this->line('   - php artisan chat:test-real-data --reset # Test regular system');
        $this->line('   - php artisan pusher-chat:check           # Check data status');
        $this->line('   - php artisan chat:check-data             # Check regular data');

        return 0;
    }
}
