<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\User;
use App\Events\NewChatMessage;

class TestChatEvents extends Command
{
    protected $signature = 'chat:test-events {--chat_id=1} {--message=Test message from command}';
    protected $description = 'Test chat events broadcasting';

    public function handle()
    {
        $chatId = $this->option('chat_id');
        $messageText = $this->option('message');

        $this->info('🧪 Testing Chat Events Broadcasting...');
        $this->line('');

        // Find chat
        $chat = Chat::with(['customer'])->find($chatId);
        if (!$chat) {
            $this->error("❌ Chat with ID {$chatId} not found");
            return 1;
        }

        $this->info("✅ Chat found: {$chat->subject}");
        $this->info("👤 Customer: {$chat->customer->name}");
        $this->line('');

        // Find a customer user for testing
        $customer = $chat->customer;
        
        $this->info('📝 Creating test message...');
        
        try {
            // Create test message
            $message = ChatMessage::create([
                'chat_id' => $chat->id,
                'sender_id' => $customer->id,
                'sender_type' => 'customer',
                'message' => $messageText . ' - ' . now()->format('H:i:s'),
                'message_type' => 'text',
                'metadata' => [
                    'sent_from' => 'artisan_command',
                    'test' => true
                ]
            ]);

            $this->info("✅ Message created with ID: {$message->id}");
            $this->line('');

            // Load relationships
            $message->load(['sender', 'chat.customer']);
            
            $this->info('📡 Broadcasting event...');
            
            // Manually trigger event
            event(new NewChatMessage($message));
            
            $this->info('✅ Event broadcasted successfully!');
            $this->line('');
            
            $this->info('📊 Event details:');
            $this->line("   - Chat ID: {$message->chat_id}");
            $this->line("   - Sender: {$message->sender->name} ({$message->sender_type})");
            $this->line("   - Message: {$message->message}");
            $this->line("   - Channels: chat.{$message->chat_id}, private-admin.chats");
            $this->line("   - Event: message.new");
            $this->line('');
            
            $this->info('🎯 Expected results:');
            $this->line('   - Check Pusher dashboard for activity');
            $this->line('   - Check Laravel logs for event dispatch');
            $this->line('   - Check admin panel for real-time updates');
            $this->line('   - Open browser console to see received events');
            $this->line('');
            
            $this->info('🔗 Test URLs:');
            $this->line("   - Admin Chat List: " . url('/admin/chats'));
            $this->line("   - Individual Chat: " . url("/admin/chats/{$chat->id}"));
            $this->line("   - Debug Page: " . url('/test-pusher-debug.html'));

        } catch (\Exception $e) {
            $this->error("❌ Error: {$e->getMessage()}");
            return 1;
        }

        return 0;
    }
}
