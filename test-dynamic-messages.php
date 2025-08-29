<?php

// Test script for dynamic chat messages
echo "🧪 Testing Dynamic Chat Messages...\n";

$baseUrl = 'http://127.0.0.1:8000';

// Different test messages to see dynamic updates
$testMessages = [
    'مرحبا، أحتاج مساعدة في طلبي 🛒',
    'هل يمكنني إلغاء الطلب رقم #12345؟',
    'أواجه مشكلة في التطبيق، لا يفتح عندي 📱',
    'متى سيصل طلبي؟ أنتظر منذ 3 أيام ⏰',
    'أريد إرجاع المنتج، الجودة سيئة 😞',
    'شكراً لكم، الخدمة ممتازة! 👏',
    'هل هناك عروض جديدة هذا الأسبوع؟ 🔥',
    'لا أستطيع تسجيل الدخول، كلمة المرور خطأ 🔐'
];

echo "📝 Test Messages Available:\n";
foreach ($testMessages as $index => $message) {
    echo "   " . ($index + 1) . ". $message\n";
}

echo "\n🔧 How to test:\n";
echo "1. Open admin panel: $baseUrl/admin/chats\n";
echo "2. Use these cURL commands with your customer token:\n\n";

foreach ($testMessages as $index => $message) {
    echo "# Test " . ($index + 1) . ":\n";
    echo "curl -X POST \"$baseUrl/api/chat/send\" \\\n";
    echo "  -H \"Authorization: Bearer YOUR_CUSTOMER_TOKEN\" \\\n";
    echo "  -H \"Content-Type: application/json\" \\\n";
    echo "  -d '{\"chat_id\": 1, \"message\": \"$message\"}'\n\n";
}

echo "🎯 Expected Results:\n";
echo "✅ Customer name appears dynamically\n";
echo "✅ Message text updates in real-time\n";
echo "✅ Chat moves to top of list\n";
echo "✅ Time shows 'الآن'\n";
echo "✅ Unread count increases\n";
echo "✅ Visual highlight appears\n";
echo "✅ Notification pops up\n\n";

echo "🚀 Try sending different messages to see dynamic updates!\n";
