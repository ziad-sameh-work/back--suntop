<?php

/**
 * 🔥 إصلاح مشاكل الـ Real-time في الشات
 * هذا الملف يحل المشاكل الشائعة
 */

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel environment
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔥 إصلاح مشاكل الـ Real-time Chat\n";
echo "==================================\n\n";

// 1. فحص إعدادات Broadcasting
echo "1️⃣ فحص إعدادات البث (Broadcasting):\n";
echo "-----------------------------------\n";

$broadcastDriver = config('broadcasting.default');
echo "BROADCAST_DRIVER: " . $broadcastDriver . "\n";

if ($broadcastDriver !== 'pusher') {
    echo "❌ خطأ: BROADCAST_DRIVER يجب أن يكون 'pusher'\n";
    echo "💡 الحل: غير BROADCAST_DRIVER=pusher في ملف .env\n\n";
} else {
    echo "✅ BROADCAST_DRIVER مضبوط صحيح\n\n";
}

// 2. فحص إعدادات Pusher
echo "2️⃣ فحص إعدادات Pusher:\n";
echo "------------------------\n";

$pusherConfig = config('broadcasting.connections.pusher');
$requiredKeys = ['key', 'secret', 'app_id'];
$allKeysPresent = true;

foreach ($requiredKeys as $key) {
    if (empty($pusherConfig[$key])) {
        echo "❌ خطأ: PUSHER_{$key} مفقود\n";
        $allKeysPresent = false;
    } else {
        echo "✅ PUSHER_{$key}: موجود\n";
    }
}

if (!$allKeysPresent) {
    echo "\n💡 الحل: أضف هذه السطور في ملف .env:\n";
    echo "PUSHER_APP_ID=2046066\n";
    echo "PUSHER_APP_KEY=f546bf192457a6d47ed5\n";
    echo "PUSHER_APP_SECRET=d1a687b90b02f69ea917\n";
    echo "PUSHER_APP_CLUSTER=eu\n\n";
} else {
    echo "✅ جميع إعدادات Pusher موجودة\n\n";
}

// 3. فحص Queue Connection
echo "3️⃣ فحص إعدادات Queue:\n";
echo "----------------------\n";

$queueConnection = config('queue.default');
echo "QUEUE_CONNECTION: " . $queueConnection . "\n";

if ($queueConnection === 'sync') {
    echo "✅ Queue مضبوط على 'sync' - الأحداث ستعمل فوراً\n\n";
} else {
    echo "⚠️ تحذير: Queue مضبوط على '{$queueConnection}'\n";
    echo "💡 للاختبار السريع، غير QUEUE_CONNECTION=sync في .env\n";
    echo "💡 أو تأكد من تشغيل: php artisan queue:work\n\n";
}

// 4. اختبار Pusher Connection
echo "4️⃣ اختبار اتصال Pusher:\n";
echo "------------------------\n";

try {
    $pusher = new \Pusher\Pusher(
        $pusherConfig['key'],
        $pusherConfig['secret'],
        $pusherConfig['app_id'],
        $pusherConfig['options'] ?? []
    );
    
    // إرسال حدث اختبار
    $result = $pusher->trigger('test-channel', 'test-event', [
        'message' => 'اختبار الاتصال',
        'timestamp' => now()->toISOString()
    ]);
    
    if ($result) {
        echo "✅ اتصال Pusher يعمل بنجاح!\n";
        echo "📡 تم إرسال حدث اختبار إلى قناة 'test-channel'\n\n";
    } else {
        echo "❌ فشل في إرسال حدث اختبار\n\n";
    }
    
} catch (Exception $e) {
    echo "❌ خطأ في اتصال Pusher: " . $e->getMessage() . "\n\n";
}

// 5. فحص Service Providers
echo "5️⃣ فحص Service Providers:\n";
echo "-------------------------\n";

$providers = config('app.providers');
$broadcastProviderFound = false;

foreach ($providers as $provider) {
    if (strpos($provider, 'BroadcastServiceProvider') !== false) {
        $broadcastProviderFound = true;
        break;
    }
}

if ($broadcastProviderFound) {
    echo "✅ BroadcastServiceProvider مسجل\n\n";
} else {
    echo "❌ BroadcastServiceProvider غير مسجل\n";
    echo "💡 تأكد من وجوده في config/app.php\n\n";
}

// 6. اختبار إرسال رسالة حقيقية
echo "6️⃣ اختبار إرسال رسالة حقيقية:\n";
echo "-------------------------------\n";

try {
    // البحث عن أول محادثة
    $chat = \App\Models\Chat::first();
    
    if ($chat) {
        echo "✅ تم العثور على محادثة ID: {$chat->id}\n";
        
        // إنشاء رسالة اختبار
        $message = \App\Models\ChatMessage::create([
            'chat_id' => $chat->id,
            'sender_id' => 1, // افتراضي
            'sender_type' => 'admin',
            'message' => '🔥 رسالة اختبار Real-time - ' . now()->format('H:i:s'),
            'message_type' => 'text',
            'metadata' => [
                'test_message' => true,
                'sent_from' => 'fix_script'
            ]
        ]);
        
        $message->load('sender');
        
        // إرسال الحدث
        event(new \App\Events\NewChatMessage($message));
        
        echo "✅ تم إرسال رسالة اختبار بنجاح!\n";
        echo "📨 Message ID: {$message->id}\n";
        echo "📡 تم بث الحدث إلى القنوات:\n";
        echo "   - chat.{$chat->id}\n";
        echo "   - mobile-chat.{$chat->id}\n";
        echo "   - private-admin.chats\n";
        echo "   - admin-chats-public\n\n";
        
    } else {
        echo "❌ لا توجد محادثات في قاعدة البيانات\n";
        echo "💡 قم بإنشاء محادثة أولاً من خلال API\n\n";
    }
    
} catch (Exception $e) {
    echo "❌ خطأ في إرسال الرسالة: " . $e->getMessage() . "\n\n";
}

// 7. إرشادات الإصلاح النهائية
echo "7️⃣ إرشادات الإصلاح النهائية:\n";
echo "------------------------------\n";

echo "📋 للتأكد من عمل Real-time:\n\n";

echo "أ) افتح صفحة الاختبار:\n";
echo "   https://suntop-eg.com/test-mobile-chat.html\n\n";

echo "ب) في Flutter، استخدم هذه الإعدادات:\n";
echo "   - Pusher Key: f546bf192457a6d47ed5\n";
echo "   - Cluster: eu\n";
echo "   - Channel: mobile-chat.{chatId}\n";
echo "   - Event: message.new\n\n";

echo "ج) تأكد من تشغيل هذه الأوامر:\n";
echo "   php artisan config:clear\n";
echo "   php artisan config:cache\n";
echo "   php artisan queue:restart\n\n";

echo "د) للاختبار السريع:\n";
echo "   curl -X POST https://suntop-eg.com/api/test-mobile-chat/test-send/1 \\\n";
echo "     -H \"Content-Type: application/json\" \\\n";
echo "     -d '{\"message\": \"اختبار\"}'n\n";

echo "🎉 انتهى الفحص!\n";
echo "📱 إذا كانت جميع النقاط ✅ فالنظام يجب أن يعمل\n";
echo "❌ إذا كان هناك مشاكل، اتبع الحلول المقترحة أعلاه\n";
