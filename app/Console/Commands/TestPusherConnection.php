<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Pusher\Pusher;
use Exception;

class TestPusherConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pusher:test 
                            {--channel=test-channel : The channel to test}
                            {--event=test-event : The event to trigger}
                            {--message=Hello from Laravel! : The test message}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Pusher connection and broadcasting';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🚀 Testing Pusher Connection...');
        $this->info('================================');

        try {
            // Get Pusher configuration
            $config = config('broadcasting.connections.pusher');
            
            $this->table(
                ['Setting', 'Value'],
                [
                    ['App ID', $config['app_id']],
                    ['Key', $config['key']],
                    ['Secret', str_repeat('*', strlen($config['secret']))],
                    ['Cluster', $config['options']['cluster']],
                    ['Use TLS', $config['options']['useTLS'] ? 'Yes' : 'No'],
                ]
            );

            // Initialize Pusher
            $pusher = new Pusher(
                $config['key'],
                $config['secret'],
                $config['app_id'],
                $config['options']
            );

            $this->info('✅ Pusher client initialized successfully');

            // Prepare test data
            $channel = $this->option('channel');
            $event = $this->option('event');
            $message = $this->option('message');

            $data = [
                'message' => $message,
                'timestamp' => now()->toISOString(),
                'test_id' => uniqid(),
                'sent_from' => 'Laravel Artisan Command',
                'user' => [
                    'name' => 'Test User',
                    'id' => 999
                ]
            ];

            $this->info("📤 Sending test message to channel: {$channel}");
            $this->info("🎯 Event: {$event}");

            // Send test message
            $result = $pusher->trigger($channel, $event, $data);

            if ($result) {
                $this->info('✅ Message sent successfully!');
                $this->info('📊 Test Data:');
                $this->line(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

                $this->info('');
                $this->info('🎉 Pusher is working correctly!');
                $this->info('📝 You can now use the real-time chat system.');

                // Test with chat-specific data
                $this->testChatMessage($pusher);

            } else {
                $this->error('❌ Failed to send message');
                return 1;
            }

        } catch (Exception $e) {
            $this->error('❌ Pusher connection failed:');
            $this->error("🔍 Error: {$e->getMessage()}");
            $this->error("📁 File: {$e->getFile()}");
            $this->error("📍 Line: {$e->getLine()}");

            $this->info('');
            $this->warn('🛠️ Troubleshooting steps:');
            $this->line('1. Check your Pusher credentials in .env file');
            $this->line('2. Verify internet connection');
            $this->line('3. Ensure Pusher app is active in dashboard');
            $this->line('4. Install Pusher package: composer require pusher/pusher-php-server');

            return 1;
        }

        return 0;
    }

    /**
     * Test with chat-specific message format
     */
    private function testChatMessage(Pusher $pusher)
    {
        $this->info('');
        $this->info('🧪 Testing with chat message format...');

        try {
            // Simulate a real chat message
            $chatData = [
                'message' => [
                    'id' => 999,
                    'chat_id' => 1,
                    'user_id' => 1,
                    'message' => 'This is a test chat message from Artisan!',
                    'sender_type' => 'customer',
                    'is_read' => false,
                    'created_at' => now()->toISOString(),
                    'formatted_time' => now()->format('H:i'),
                    'formatted_date' => now()->format('d/m/Y'),
                    'user' => [
                        'id' => 1,
                        'name' => 'Test Customer',
                        'email' => 'test@example.com',
                        'role' => 'customer',
                    ],
                    'metadata' => ['test' => true],
                ],
                'chat' => [
                    'id' => 1,
                    'user_id' => 1,
                    'status' => 'active',
                    'title' => 'Test Chat',
                    'last_message_at' => now()->toISOString(),
                    'unread_admin_count' => 1,
                    'unread_customer_count' => 0,
                    'customer' => [
                        'id' => 1,
                        'name' => 'Test Customer',
                        'email' => 'test@example.com',
                    ],
                ],
                'timestamp' => now()->toISOString(),
            ];

            // Send to both channels that our chat system uses
            $channels = ['private-chat.1', 'private-admin.chats'];
            
            foreach ($channels as $channel) {
                $result = $pusher->trigger($channel, 'message.sent', $chatData);
                if ($result) {
                    $this->info("✅ Chat test message sent to: {$channel}");
                } else {
                    $this->warn("⚠️ Failed to send to: {$channel}");
                }
            }

        } catch (Exception $e) {
            $this->warn("⚠️ Chat message test failed: {$e->getMessage()}");
        }
    }
}
