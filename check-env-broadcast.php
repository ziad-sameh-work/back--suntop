<?php
// Check current .env configuration for broadcasting
echo "=== فحص إعدادات Broadcasting في .env ===\n\n";

// Load .env file
$envFile = __DIR__ . '/.env';
if (!file_exists($envFile)) {
    echo "❌ ملف .env غير موجود!\n";
    exit(1);
}

$envContent = file_get_contents($envFile);
$envLines = explode("\n", $envContent);

echo "📋 إعدادات Broadcasting الحالية:\n";
echo "================================\n";

$broadcastSettings = [
    'BROADCAST_DRIVER',
    'PUSHER_APP_ID',
    'PUSHER_APP_KEY', 
    'PUSHER_APP_SECRET',
    'PUSHER_APP_CLUSTER'
];

$foundSettings = [];

foreach ($envLines as $line) {
    $line = trim($line);
    if (empty($line) || strpos($line, '#') === 0) continue;
    
    foreach ($broadcastSettings as $setting) {
        if (strpos($line, $setting . '=') === 0) {
            $foundSettings[$setting] = $line;
            break;
        }
    }
}

foreach ($broadcastSettings as $setting) {
    if (isset($foundSettings[$setting])) {
        $value = substr($foundSettings[$setting], strlen($setting) + 1);
        if ($setting === 'PUSHER_APP_SECRET') {
            $value = str_repeat('*', strlen($value)); // Hide secret
        }
        echo "✅ $setting = $value\n";
    } else {
        echo "❌ $setting = غير محدد\n";
    }
}

echo "\n🔍 التحقق من الإعدادات:\n";
echo "========================\n";

// Check BROADCAST_DRIVER
if (isset($foundSettings['BROADCAST_DRIVER'])) {
    $driver = substr($foundSettings['BROADCAST_DRIVER'], strlen('BROADCAST_DRIVER') + 1);
    if ($driver === 'pusher') {
        echo "✅ BROADCAST_DRIVER مضبوط على pusher\n";
    } else {
        echo "❌ BROADCAST_DRIVER مضبوط على '$driver' - يجب أن يكون 'pusher'\n";
        echo "💡 لإصلاح المشكلة: غير BROADCAST_DRIVER=log إلى BROADCAST_DRIVER=pusher\n";
    }
} else {
    echo "❌ BROADCAST_DRIVER غير محدد في .env\n";
}

// Check Pusher credentials
$pusherSettings = ['PUSHER_APP_ID', 'PUSHER_APP_KEY', 'PUSHER_APP_SECRET', 'PUSHER_APP_CLUSTER'];
$missingPusher = [];

foreach ($pusherSettings as $setting) {
    if (!isset($foundSettings[$setting]) || empty(trim(substr($foundSettings[$setting], strlen($setting) + 1)))) {
        $missingPusher[] = $setting;
    }
}

if (empty($missingPusher)) {
    echo "✅ جميع إعدادات Pusher محددة\n";
} else {
    echo "❌ إعدادات Pusher المفقودة: " . implode(', ', $missingPusher) . "\n";
}

echo "\n🚀 الخطوات التالية:\n";
echo "==================\n";

if (isset($foundSettings['BROADCAST_DRIVER'])) {
    $driver = substr($foundSettings['BROADCAST_DRIVER'], strlen('BROADCAST_DRIVER') + 1);
    if ($driver !== 'pusher') {
        echo "1. غير BROADCAST_DRIVER من '$driver' إلى 'pusher' في ملف .env\n";
        echo "2. شغل الأمر: php artisan config:clear\n";
        echo "3. أعد تشغيل الخادم\n";
    } else {
        echo "✅ الإعدادات صحيحة - Real-time chat يجب أن يعمل الآن!\n";
    }
} else {
    echo "1. أضف BROADCAST_DRIVER=pusher إلى ملف .env\n";
    echo "2. شغل الأمر: php artisan config:clear\n";
    echo "3. أعد تشغيل الخادم\n";
}

if (!empty($missingPusher)) {
    echo "4. أضف إعدادات Pusher المفقودة إلى .env\n";
}

echo "\n📝 ملاحظة: تأكد من إعادة تشغيل الخادم بعد تعديل .env\n";
