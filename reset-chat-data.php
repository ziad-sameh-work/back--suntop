<?php

// Reset chat data script
echo "🗑️ Reset Chat Data Script\n\n";

echo "📋 Commands to Reset Chat Data:\n\n";

echo "1. 🧹 Clear Chat Messages:\n";
echo "   php artisan tinker\n";
echo "   >>> \\App\\Models\\ChatMessage::truncate();\n";
echo "   >>> exit\n\n";

echo "2. 🧹 Clear Chats:\n";
echo "   php artisan tinker\n";
echo "   >>> \\App\\Models\\Chat::truncate();\n";
echo "   >>> exit\n\n";

echo "3. 🧹 Clear Pusher Messages:\n";
echo "   php artisan tinker\n";
echo "   >>> \\App\\Models\\PusherMessage::truncate();\n";
echo "   >>> exit\n\n";

echo "4. 🧹 Clear Pusher Chats:\n";
echo "   php artisan tinker\n";
echo "   >>> \\App\\Models\\PusherChat::truncate();\n";
echo "   >>> exit\n\n";

echo "🚀 OR Use Single Command (All at once):\n";
echo "php artisan tinker\n";
echo ">>> \\App\\Models\\ChatMessage::truncate();\n";
echo ">>> \\App\\Models\\Chat::truncate();\n";
echo ">>> \\App\\Models\\PusherMessage::truncate();\n";
echo ">>> \\App\\Models\\PusherChat::truncate();\n";
echo ">>> echo 'All chat data cleared!';\n";
echo ">>> exit\n\n";

echo "✨ Alternative SQL Commands (if tinker fails):\n";
echo "mysql -u root -p\n";
echo "use your_database_name;\n";
echo "TRUNCATE TABLE chat_messages;\n";
echo "TRUNCATE TABLE chats;\n";
echo "TRUNCATE TABLE pusher_messages;\n";
echo "TRUNCATE TABLE pusher_chats;\n";
echo "exit\n\n";

echo "🧪 After clearing, test fresh data:\n\n";

echo "📱 Create Fresh Customer:\n";
echo "php artisan tinker\n";
echo ">>> \$user = \\App\\Models\\User::create([\n";
echo ">>>     'name' => 'Test Customer',\n";
echo ">>>     'email' => 'customer@test.com',\n";
echo ">>>     'password' => bcrypt('password'),\n";
echo ">>>     'role' => 'user'\n";
echo ">>> ]);\n";
echo ">>> echo 'Customer created with ID: ' . \$user->id;\n";
echo ">>> exit\n\n";

echo "💬 Create Fresh Chat:\n";
echo "php artisan tinker\n";
echo ">>> \$chat = \\App\\Models\\Chat::create([\n";
echo ">>>     'customer_id' => 1, // Use the customer ID from above\n";
echo ">>>     'subject' => 'Fresh Test Chat',\n";
echo ">>>     'status' => 'open',\n";
echo ">>>     'priority' => 'medium'\n";
echo ">>> ]);\n";
echo ">>> echo 'Chat created with ID: ' . \$chat->id;\n";
echo ">>> exit\n\n";

echo "📨 Send Test Message:\n";
echo "curl -X POST \"http://127.0.0.1:8000/test-chat-event/1\" \\\n";
echo "  -H \"Content-Type: application/json\"\n\n";

echo "🎯 Expected Fresh Test Flow:\n";
echo "1. Clear all data ✅\n";
echo "2. Create fresh customer ✅\n";
echo "3. Create fresh chat ✅\n";
echo "4. Open admin panel: http://127.0.0.1:8000/admin/chats\n";
echo "5. Send test message and see real-time updates ✅\n\n";

echo "🔍 Verify Data is Clean:\n";
echo "php artisan tinker\n";
echo ">>> \\App\\Models\\ChatMessage::count(); // Should be 0\n";
echo ">>> \\App\\Models\\Chat::count(); // Should be 0\n";
echo ">>> \\App\\Models\\PusherMessage::count(); // Should be 0\n";
echo ">>> \\App\\Models\\PusherChat::count(); // Should be 0\n";
echo ">>> exit\n";
