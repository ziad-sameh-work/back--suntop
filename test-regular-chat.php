<?php

// Test script for regular chat API
echo "🧪 Testing Regular Chat API...\n";

$baseUrl = 'http://127.0.0.1:8000';

// Test data
$testData = [
    'subject' => 'Test Chat من PHP',
    'message' => 'هذه رسالة تجريبية من العميل',
    'priority' => 'medium'
];

echo "📡 Testing regular chat endpoints...\n";

// You need to replace with actual customer token
$customerToken = 'YOUR_CUSTOMER_TOKEN_HERE';

if ($customerToken === 'YOUR_CUSTOMER_TOKEN_HERE') {
    echo "⚠️  Please set a real customer token in the script\n";
    echo "   1. Login as customer via API\n";
    echo "   2. Copy the Bearer token\n";
    echo "   3. Replace YOUR_CUSTOMER_TOKEN_HERE in this script\n\n";
}

echo "🔗 Available endpoints:\n";
echo "   1. GET  /api/chat/start - Start or get chat\n";
echo "   2. POST /api/chat/send - Send message\n";
echo "   3. GET  /api/chat/{chatId}/messages - Get messages\n";
echo "   4. GET  /api/chat/history - Get chat history\n\n";

echo "📝 Example cURL commands:\n\n";

echo "1. Start chat:\n";
echo "curl -X GET '$baseUrl/api/chat/start' \\\n";
echo "  -H 'Authorization: Bearer YOUR_CUSTOMER_TOKEN' \\\n";
echo "  -H 'Content-Type: application/json'\n\n";

echo "2. Send message:\n";
echo "curl -X POST '$baseUrl/api/chat/send' \\\n";
echo "  -H 'Authorization: Bearer YOUR_CUSTOMER_TOKEN' \\\n";
echo "  -H 'Content-Type: application/json' \\\n";
echo "  -d '{\"chat_id\": CHAT_ID, \"message\": \"Test message\"}'\n\n";

echo "💡 This will trigger NewChatMessage event → admin.chats channel\n";
echo "🎯 Open /admin/chats and run the commands to see real-time updates!\n";
