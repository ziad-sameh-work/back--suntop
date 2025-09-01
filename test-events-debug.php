<?php

// Debug script to test chat events
echo "🔧 Testing Chat Events and Data Structure...\n\n";

$baseUrl = 'https://suntop-eg.com';

echo "📋 Debug Steps:\n\n";

echo "1. 🌐 Open Admin Panel:\n";
echo "   $baseUrl/admin/chats\n";
echo "   Open Browser Console (F12)\n\n";

echo "2. 🔍 Check Pusher Connection:\n";
echo "   Look for:\n";
echo "   ✅ '✅ Pusher connected successfully'\n";
echo "   ✅ '✅ Successfully subscribed to admin chats channel'\n";
echo "   ✅ '🟢 Real-time نشط'\n\n";

echo "3. 📨 Send Test Message:\n";
echo "curl -X POST \"$baseUrl/api/chat/send\" \\\n";
echo "  -H \"Authorization: Bearer YOUR_CUSTOMER_TOKEN\" \\\n";
echo "  -H \"Content-Type: application/json\" \\\n";
echo "  -d '{\"chat_id\": 1, \"message\": \"🧪 Testing events debug message!\"}'\n\n";

echo "4. 🔍 Expected Console Logs:\n";
echo "   📡 'NewChatMessage event created'\n";
echo "   📺 'Broadcasting on channels: [chat.1, private-admin.chats]'\n";
echo "   🔔 '🔔 New regular chat message received: {data}'\n";
echo "   📊 '📨 Processing regular chat message: {data}'\n\n";

echo "5. 🧪 Debug Data Structure:\n";
echo "   Check if data has:\n";
echo "   ✅ data.message.id\n";
echo "   ✅ data.message.sender.name\n";
echo "   ✅ data.message.sender_type\n";
echo "   ✅ data.message.message\n";
echo "   ✅ data.chat.id\n";
echo "   ✅ data.chat.customer.name\n\n";

echo "6. 🔧 Common Issues to Check:\n";
echo "   ❌ 'Messages container not found' → HTML structure problem\n";
echo "   ❌ 'Chat not found in current list' → Wrong chat ID\n";
echo "   ❌ 'Undefined property' → Data structure issue\n";
echo "   ❌ No console logs → Events not broadcasting\n\n";

echo "7. 📱 Manual Test Commands:\n";
echo "   # Test chat start:\n";
echo "   curl -X GET \"$baseUrl/api/chat/start\" \\\n";
echo "     -H \"Authorization: Bearer YOUR_CUSTOMER_TOKEN\"\n\n";

echo "   # Test different messages:\n";
$messages = [
    '🚀 First test message',
    '📱 Second test message', 
    '🎯 Third test message',
    '✨ Fourth test message'
];

foreach ($messages as $index => $message) {
    echo "   # Message " . ($index + 1) . ":\n";
    echo "   curl -X POST \"$baseUrl/api/chat/send\" \\\n";
    echo "     -H \"Authorization: Bearer YOUR_CUSTOMER_TOKEN\" \\\n";
    echo "     -H \"Content-Type: application/json\" \\\n";
    echo "     -d '{\"chat_id\": 1, \"message\": \"$message\"}'\n\n";
}

echo "8. 🎯 Success Indicators:\n";
echo "   ✅ Console shows proper data structure\n";
echo "   ✅ Message appears in chat list preview\n";
echo "   ✅ Chat moves to top of list\n";
echo "   ✅ Unread count updates\n";
echo "   ✅ Time shows 'الآن'\n";
echo "   ✅ Visual highlight appears\n";
echo "   ✅ Notification pops up\n\n";

echo "🚨 If Still Not Working:\n";
echo "1. Check Laravel logs: storage/logs/laravel.log\n";
echo "2. Check Pusher connection status\n";
echo "3. Verify Bearer token is correct\n";
echo "4. Make sure chat_id exists in database\n";
echo "5. Check browser network tab for errors\n\n";

echo "🔍 Debug JavaScript in Console:\n";
echo "// Check if functions exist:\n";
echo "typeof handleNewRegularChatMessage\n";
echo "typeof updateChatItemRealtime\n";
echo "typeof showNotification\n\n";

echo "// Check if elements exist:\n";
echo "document.querySelector('.chats-list')\n";
echo "document.querySelector('[data-chat-id=\"1\"]')\n";
echo "document.querySelector('.chat-preview')\n\n";

echo "🎉 When working correctly, you'll see instant updates!\n";
