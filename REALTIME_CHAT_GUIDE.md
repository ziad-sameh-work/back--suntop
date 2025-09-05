# دليل نظام الشات الفوري (Real-time Chat System)

## نظرة عامة
تم إعادة بناء نظام الشات الفوري بالكامل باستخدام Pusher لضمان التحديثات الفورية للرسائل والمحادثات.

## المتطلبات

### 1. إعدادات Pusher في `.env`
```env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=mt1
```

### 2. تثبيت Pusher PHP SDK
```bash
composer require pusher/pusher-php-server
```

## هيكل النظام

### 1. الأحداث (Events)
- **Event Class**: `App\Events\NewChatMessage`
- **Event Name**: `message.new` (من `broadcastAs()`)
- **Channels**: 
  - `private-admin.chats` (للإدارة)
  - `chat.{id}` (للمحادثات الفردية)

### 2. القنوات (Channels)
- **Admin Channel**: `private-admin.chats` - لتحديثات قائمة المحادثات
- **Individual Chat**: `chat.{id}` - للمحادثات الفردية

### 3. JavaScript Components

#### أ. صفحة قائمة المحادثات (`index.blade.php`)
```javascript
// تهيئة Pusher
initializePusher()

// الاستماع للرسائل الجديدة
adminChannel.bind('message.new', handleNewMessage)

// تحديث قائمة المحادثات فورياً
updateChatInList(data)
```

#### ب. صفحة المحادثة الفردية (`show.blade.php`)
```javascript
// الاستماع للرسائل في المحادثة
chatChannel.bind('message.new', function(data) {
    Livewire.emit('refreshMessages');
})
```

#### ج. مكون Livewire (`chat-interface.blade.php`)
```javascript
// ربط مع Pusher الخاص بالصفحة الأب
window.adminChannel.bind('message.new', function(data) {
    @this.call('refreshMessages');
})
```

## الميزات الرئيسية

### 1. التحديثات الفورية
- ✅ تحديث قائمة المحادثات فورياً
- ✅ تحديث الرسائل في المحادثة الفردية
- ✅ تحديث عدد الرسائل غير المقروءة
- ✅ نقل المحادثة لأعلى القائمة

### 2. التنبيهات
- 🔔 تنبيهات المتصفح (Browser Notifications)
- 📱 تنبيهات داخل الصفحة
- 🎨 تأثيرات بصرية (تمييز المحادثات المحدثة)

### 3. معالجة الأخطاء
- 🔄 إعادة الاتصال التلقائي
- ⚠️ مؤشرات حالة الاتصال
- 🔄 تحديث الصفحة كحل احتياطي

### 4. مؤشرات الحالة
- 🟢 متصل
- 🔴 منقطع
- 🟡 جاري الاتصال
- ✅ مشترك
- ❌ خطأ

## كيفية الاستخدام

### 1. تشغيل النظام
1. تأكد من إعدادات Pusher في `.env`
2. قم بتشغيل الخادم: `php artisan serve`
3. افتح صفحة المحادثات: `/admin/chats`

### 2. اختبار النظام
```bash
# استخدام سكريبت الاختبار
php test-realtime-chat.php customer "رسالة تجريبية"
php test-realtime-chat.php admin "رد من الإدارة"
```

### 3. مراقبة الأحداث
افتح Developer Tools في المتصفح وراقب Console للرسائل التالية:
- `🚀 Initializing Pusher Real-time System...`
- `✅ Pusher connected successfully`
- `✅ Successfully subscribed to admin chats channel`
- `🔥 New message received:`

## استكشاف الأخطاء

### 1. عدم وصول الأحداث
- تحقق من إعدادات Pusher في `.env`
- تأكد من صحة `PUSHER_APP_KEY` و `PUSHER_APP_SECRET`
- راجع Console للأخطاء

### 2. عدم تحديث الواجهة
- تحقق من وجود عنصر `[data-chat-id]` في DOM
- راجع JavaScript Console للأخطاء
- تأكد من تحميل Livewire بشكل صحيح

### 3. مشاكل الاتصال
- تحقق من الإنترنت
- راجع إعدادات Firewall
- تأكد من Pusher Cluster الصحيح

## الملفات المهمة

### Backend
- `app/Events/NewChatMessage.php` - حدث الرسالة الجديدة
- `app/Http/Livewire/ChatInterface.php` - مكون واجهة الشات
- `config/broadcasting.php` - إعدادات البث

### Frontend
- `resources/views/admin/chats/index.blade.php` - قائمة المحادثات
- `resources/views/admin/chats/show.blade.php` - المحادثة الفردية
- `resources/views/livewire/chat-interface.blade.php` - مكون الشات

### Testing
- `test-realtime-chat.php` - سكريبت اختبار النظام

## الأمان

### 1. القنوات الخاصة
- `private-admin.chats` محمية بـ Authentication
- التحقق من صلاحيات المستخدم قبل الاشتراك

### 2. CSRF Protection
- جميع الطلبات محمية بـ CSRF Token
- استخدام Headers آمنة

## الأداء

### 1. التحسينات
- استخدام Pusher بدلاً من Polling
- تحديث DOM مباشرة بدلاً من إعادة تحميل الصفحة
- تجميع الأحداث لتقليل الطلبات

### 2. المراقبة
- مراقبة حالة الاتصال
- إحصائيات الأحداث في Console
- مؤشرات الأداء

## الدعم والصيانة

### 1. السجلات (Logs)
- Laravel Logs: `storage/logs/laravel.log`
- Browser Console: Developer Tools
- Pusher Dashboard: مراقبة الأحداث

### 2. التحديثات
- مراجعة Pusher SDK بانتظام
- تحديث JavaScript Libraries
- اختبار النظام بعد التحديثات

---

## ملاحظات مهمة

⚠️ **تأكد من إعداد Pusher Credentials بشكل صحيح**
⚠️ **اختبر النظام في بيئة التطوير قبل النشر**
⚠️ **راقب استهلاك Pusher API Limits**

🎉 **النظام جاهز للاستخدام والتحديثات الفورية تعمل بكفاءة!**
