<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PusherChat;
use App\Models\User;

class TestPusherRelationships extends Command
{
    protected $signature = 'pusher:test-relationships';
    protected $description = 'Test PusherChat relationships and fix any issues';

    public function handle()
    {
        $this->info('🔍 Testing PusherChat Relationships...');
        $this->line('');

        // Check if pusher_chats table exists
        try {
            $chatCount = PusherChat::count();
            $this->info("✅ Pusher chats table exists with {$chatCount} records");
        } catch (\Exception $e) {
            $this->error("❌ Error accessing pusher_chats table: " . $e->getMessage());
            return 1;
        }

        if ($chatCount === 0) {
            $this->info('📱 Creating test pusher chat for relationship testing...');
            
            // Find or create a test user
            $user = User::firstOrCreate(
                ['email' => 'relationship-test@example.com'],
                [
                    'name' => 'Test User for Relationships',
                    'password' => bcrypt('password'),
                    'role' => 'user'
                ]
            );

            // Create test pusher chat
            try {
                $chat = PusherChat::create([
                    'user_id' => $user->id,
                    'status' => 'active',
                    'title' => 'Test Relationship Chat',
                    'subject' => 'Test Subject for Relationships'
                ]);
                
                $this->info("✅ Created test chat with ID: {$chat->id}");
            } catch (\Exception $e) {
                $this->error("❌ Error creating test chat: " . $e->getMessage());
                return 1;
            }
        }

        // Test relationships
        $this->info('🔗 Testing Relationships...');
        
        $chat = PusherChat::first();
        
        if (!$chat) {
            $this->error("❌ No pusher chats found");
            return 1;
        }

        $this->line("Testing chat ID: {$chat->id}");

        // Test user relationship
        try {
            $user = $chat->user;
            $this->info("✅ User relationship works: {$user->name} (ID: {$user->id})");
        } catch (\Exception $e) {
            $this->error("❌ User relationship failed: " . $e->getMessage());
        }

        // Test customer relationship (alias)
        try {
            $customer = $chat->customer;
            $this->info("✅ Customer relationship works: {$customer->name} (ID: {$customer->id})");
        } catch (\Exception $e) {
            $this->error("❌ Customer relationship failed: " . $e->getMessage());
        }

        // Test messages relationship
        try {
            $messages = $chat->messages;
            $this->info("✅ Messages relationship works: {$messages->count()} messages");
        } catch (\Exception $e) {
            $this->error("❌ Messages relationship failed: " . $e->getMessage());
        }

        // Test subject attribute
        try {
            $subject = $chat->subject;
            $this->info("✅ Subject attribute works: '{$subject}'");
        } catch (\Exception $e) {
            $this->error("❌ Subject attribute failed: " . $e->getMessage());
        }

        // Test loading with relationships
        $this->line('');
        $this->info('🔄 Testing Eager Loading...');
        
        try {
            $chatsWithRelations = PusherChat::with(['customer', 'messages.user'])->get();
            $this->info("✅ Eager loading works: {$chatsWithRelations->count()} chats loaded");
            
            foreach ($chatsWithRelations as $chat) {
                $this->line("   Chat #{$chat->id}: {$chat->customer->name} ({$chat->messages->count()} messages)");
            }
        } catch (\Exception $e) {
            $this->error("❌ Eager loading failed: " . $e->getMessage());
        }

        $this->line('');
        $this->info('🎯 All relationship tests completed!');
        
        return 0;
    }
}
