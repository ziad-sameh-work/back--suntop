<?php
/**
 * Test Pusher Connection Script
 * تأكد من عمل اتصال Pusher مع البيانات المقدمة
 */

require_once 'vendor/autoload.php';

use Pusher\Pusher;

try {
    echo "🚀 اختبار اتصال Pusher...\n";
    echo "=====================================\n";
    
    // إعداد Pusher بالبيانات المقدمة
    $pusher = new Pusher(
        '44911da009b5537ffae1', // key
        'f3be89a3c36340498803', // secret
        '2043781',               // app_id
        [
            'cluster' => 'eu',
            'useTLS' => true,
            'timeout' => 30,
            'debug' => true
        ]
    );
    
    echo "✅ تم إنشاء Pusher client بنجاح\n";
    echo "📍 Cluster: eu\n";
    echo "🔑 App ID: 2043781\n";
    echo "🗝️ Key: 44911da009b5537ffae1\n\n";
    
    // اختبار إرسال رسالة
    echo "📤 اختبار إرسال رسالة...\n";
    
    $data = [
        'message' => 'مرحبا! تم الاتصال مع Pusher بنجاح 🎉',
        'timestamp' => date('Y-m-d H:i:s'),
        'test_id' => uniqid(),
        'from' => 'Laravel Chat System'
    ];
    
    $result = $pusher->trigger(
        'test-channel',           // channel name
        'test-message',          // event name
        $data,                   // data
        ['socket_id' => null]    // exclude socket
    );
    
    if ($result) {
        echo "✅ تم إرسال الرسالة بنجاح!\n";
        echo "📊 تفاصيل الاستجابة:\n";
        echo "   - Status: Success\n";
        echo "   - Channel: test-channel\n";
        echo "   - Event: test-message\n";
        echo "   - Data sent: " . json_encode($data, JSON_UNESCAPED_UNICODE) . "\n";
    } else {
        echo "❌ فشل في إرسال الرسالة\n";
    }
    
    echo "\n=====================================\n";
    echo "🎯 نتيجة الاختبار: نجح الاتصال مع Pusher!\n";
    echo "🔧 يمكنك الآن استخدام الشات بأمان\n";
    echo "📝 تأكد من إضافة هذه البيانات إلى ملف .env:\n\n";
    
    echo "BROADCAST_DRIVER=pusher\n";
    echo "PUSHER_APP_ID=2043781\n";
    echo "PUSHER_APP_KEY=44911da009b5537ffae1\n";
    echo "PUSHER_APP_SECRET=f3be89a3c36340498803\n";
    echo "PUSHER_APP_CLUSTER=eu\n";
    echo "MIX_PUSHER_APP_KEY=44911da009b5537ffae1\n";
    echo "MIX_PUSHER_APP_CLUSTER=eu\n";
    
} catch (Exception $e) {
    echo "❌ خطأ في الاتصال مع Pusher:\n";
    echo "🔍 رسالة الخطأ: " . $e->getMessage() . "\n";
    echo "📁 الملف: " . $e->getFile() . "\n";
    echo "📍 السطر: " . $e->getLine() . "\n\n";
    
    echo "🛠️ الحلول المقترحة:\n";
    echo "1. تأكد من صحة بيانات Pusher\n";
    echo "2. تأكد من وجود اتصال بالإنترنت\n";
    echo "3. تأكد من تثبيت Pusher package: composer require pusher/pusher-php-server\n";
    echo "4. تأكد من أن Pusher app مفعل في لوحة التحكم\n";
}

echo "\n🔗 روابط مفيدة:\n";
echo "- Pusher Dashboard: https://dashboard.pusher.com/apps/2043781\n";
echo "- Pusher Docs: https://pusher.com/docs\n";
echo "- Laravel Broadcasting: https://laravel.com/docs/broadcasting\n";
?>
