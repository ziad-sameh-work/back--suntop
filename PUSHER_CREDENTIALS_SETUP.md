# 🔧 إعداد بيانات Pusher للشات

## بيانات Pusher الخاصة بك

```env
PUSHER_APP_ID=2043781
PUSHER_APP_KEY=44911da009b5537ffae1
PUSHER_APP_SECRET=f3be89a3c36340498803
PUSHER_APP_CLUSTER=eu
```

## 🛠️ خطوات الإعداد

### 1. تحديث ملف .env

أضف هذه البيانات إلى ملف `.env` في مشروعك:

```env
# Broadcasting Driver
BROADCAST_DRIVER=pusher

# Pusher Configuration
PUSHER_APP_ID=2043781
PUSHER_APP_KEY=44911da009b5537ffae1
PUSHER_APP_SECRET=f3be89a3c36340498803
PUSHER_APP_CLUSTER=eu

# Mix Variables for Frontend (Laravel Mix)
MIX_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
MIX_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

### 2. تحديث ملف config/broadcasting.php

تأكد من أن إعدادات Pusher صحيحة:

```php
'pusher' => [
    'driver' => 'pusher',
    'key' => env('PUSHER_APP_KEY'),
    'secret' => env('PUSHER_APP_SECRET'),
    'app_id' => env('PUSHER_APP_ID'),
    'options' => [
        'cluster' => env('PUSHER_APP_CLUSTER'),
        'useTLS' => true,
    ],
],
```

### 3. تنفيذ الأوامر المطلوبة

```bash
# تحديث التكوين
php artisan config:clear
php artisan config:cache

# تشغيل الـ migrations
php artisan migrate

# تفعيل BroadcastServiceProvider
# تأكد من إلغاء التعليق عن هذا السطر في config/app.php:
# App\Providers\BroadcastServiceProvider::class,
```

### 4. اختبار الاتصال مع Pusher

قم بإنشاء ملف اختبار:

```php
// test-pusher.php
<?php
require_once 'vendor/autoload.php';

use Pusher\Pusher;

$pusher = new Pusher(
    '44911da009b5537ffae1', // key
    'f3be89a3c36340498803', // secret
    '2043781',               // app_id
    [
        'cluster' => 'eu',
        'useTLS' => true
    ]
);

// اختبار إرسال رسالة
$result = $pusher->trigger('test-channel', 'test-event', [
    'message' => 'تم الاتصال بنجاح مع Pusher!'
]);

if ($result) {
    echo "✅ تم الاتصال مع Pusher بنجاح!\n";
} else {
    echo "❌ فشل في الاتصال مع Pusher\n";
}
```

### 5. تحديث Admin Views للاستخدام الجديد

سأقوم بتحديث ملفات الـ Admin Views لتستخدم بيانات Pusher الصحيحة:
