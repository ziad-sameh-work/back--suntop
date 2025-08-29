# 🔥 دليل اختبار Firebase Real-Time Chat - شامل

## 📋 **جدول المحتويات**
1. [إعداد النظام](#setup)
2. [اختبار APIs](#api-testing)
3. [اختبار لوحة الإدارة](#admin-testing)
4. [اختبار Flutter](#flutter-testing)
5. [اختبار الميزات المتقدمة](#advanced-testing)

---

## ⚙️ **إعداد النظام** {#setup}

### 1. إضافة Firebase URL إلى .env
```env
FIREBASE_DATABASE_URL=https://suntop-609f9-default-rtdb.europe-west1.firebasedatabase.app
```

### 2. تشغيل الخادم
```bash
php artisan serve
```

### 3. الحصول على Auth Token
```bash
# تسجيل دخول عميل
POST http://127.0.0.1:8000/api/auth/login
Content-Type: application/json

{
    "phone": "01000000001",
    "password": "password"
}

# نسخ الـ token من الاستجابة
```

---

## 🧪 **اختبار APIs** {#api-testing}

### **1. اختبار الاتصال مع Firebase**
```bash
GET http://127.0.0.1:8000/api/firebase-chat/test-connection
Authorization: Bearer YOUR_TOKEN
```

**الاستجابة المتوقعة:**
```json
{
    "success": true,
    "message": "Firebase connection successful",
    "firebase_url": "https://suntop-609f9-default-rtdb.europe-west1.firebasedatabase.app",
    "timestamp": "2025-08-28T17:30:00.000000Z"
}
```

### **2. بدء شات جديد**
```bash
POST http://127.0.0.1:8000/api/firebase-chat/start
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
    "subject": "اختبار النظام المطور",
    "priority": "high"
}
```

**الاستجابة المتوقعة:**
```json
{
    "success": true,
    "data": {
        "chat": {
            "id": 1,
            "subject": "اختبار النظام المطور",
            "status": "open",
            "priority": "high",
            "firebase": {
                "chat_path": "chats/1",
                "messages_path": "chats/1/messages",
                "info_path": "chats/1/info"
            }
        },
        "firebase_config": {
            "database_url": "https://suntop-609f9-default-rtdb.europe-west1.firebasedatabase.app"
        }
    }
}
```

### **3. إرسال رسالة**
```bash
POST http://127.0.0.1:8000/api/firebase-chat/send
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
    "chat_id": "1",
    "message": "مرحباً! هذا اختبار للنظام الجديد 🔥"
}
```

### **4. إرسال Typing Indicator**
```bash
POST http://127.0.0.1:8000/api/firebase-chat/typing-indicator
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
    "chat_id": "1",
    "is_typing": true
}
```

### **5. الحصول على الرسائل**
```bash
GET http://127.0.0.1:8000/api/firebase-chat/1/messages?per_page=20&page=1
Authorization: Bearer YOUR_TOKEN
```

### **6. تمييز كمقروء**
```bash
POST http://127.0.0.1:8000/api/firebase-chat/1/read
Authorization: Bearer YOUR_TOKEN
```

---

## 👨‍💼 **اختبار لوحة الإدارة** {#admin-testing}

### **1. فتح لوحة الإدارة Real-Time**
```
http://127.0.0.1:8000/admin/chats/realtime-dashboard
```

### **2. اختبار APIs الإدارية**

#### إرسال رسالة من الإدارة:
```bash
POST http://127.0.0.1:8000/admin/api/firebase-chat/send-message
Content-Type: application/json
X-CSRF-TOKEN: YOUR_CSRF_TOKEN

{
    "chat_id": "1",
    "message": "مرحباً من فريق الدعم! كيف يمكننا مساعدتك؟"
}
```

#### تحديث حالة المحادثة:
```bash
POST http://127.0.0.1:8000/admin/api/firebase-chat/1/status
Content-Type: application/json
X-CSRF-TOKEN: YOUR_CSRF_TOKEN

{
    "status": "in_progress"
}
```

#### إرسال Typing Indicator من الإدارة:
```bash
POST http://127.0.0.1:8000/admin/api/firebase-chat/typing-indicator
Content-Type: application/json
X-CSRF-TOKEN: YOUR_CSRF_TOKEN

{
    "chat_id": "1",
    "is_typing": true
}
```

#### الحصول على الإحصائيات:
```bash
GET http://127.0.0.1:8000/admin/api/firebase-chat/stats
X-Requested-With: XMLHttpRequest
```

### **3. تحقق من Firebase**
افتح متصفح ثاني وتوجه إلى:
```
https://suntop-609f9-default-rtdb.europe-west1.firebasedatabase.app/chats.json
```

يجب أن ترى البيانات المحدثة مباشرة!

---

## 📱 **اختبار Flutter** {#flutter-testing}

### **1. إعداد Flutter Project**
```yaml
# pubspec.yaml
dependencies:
  flutter:
    sdk: flutter
  firebase_core: ^2.24.2
  firebase_database: ^10.4.0
  http: ^0.13.6
```

### **2. إعداد Firebase في Flutter**
```dart
// main.dart
import 'package:firebase_core/firebase_core.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await Firebase.initializeApp(
    options: FirebaseOptions(
      apiKey: "your-api-key", // اختياري
      appId: "your-app-id",   // اختياري
      messagingSenderId: "your-sender-id", // اختياري
      projectId: "your-project-id", // اختياري
      databaseURL: "https://suntop-609f9-default-rtdb.europe-west1.firebasedatabase.app",
    ),
  );
  runApp(MyApp());
}
```

### **3. اختبار Firebase Connection**
```dart
// Test Firebase connection
FirebaseDatabase.instanceFor(
  databaseURL: 'https://suntop-609f9-default-rtdb.europe-west1.firebasedatabase.app',
).ref('test').set({
  'timestamp': DateTime.now().millisecondsSinceEpoch,
  'message': 'Flutter connection test'
});
```

---

## 🚀 **اختبار الميزات المتقدمة** {#advanced-testing}

### **1. اختبار Real-Time Updates**

#### الطريقة:
1. افتح لوحة الإدارة في تبويب
2. افتح تطبيق العميل في تبويب آخر
3. أرسل رسالة من العميل
4. تأكد من ظهورها فوراً في لوحة الإدارة

### **2. اختبار Typing Indicators**

#### من العميل:
```javascript
// في تطبيق العميل
document.getElementById('message-input').addEventListener('input', function() {
    fetch('/api/firebase-chat/typing-indicator', {
        method: 'POST',
        headers: {
            'Authorization': 'Bearer ' + authToken,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            chat_id: chatId,
            is_typing: true
        })
    });
});
```

#### من الإدارة:
سيظهر "العميل يكتب..." في لوحة الإدارة

### **3. اختبار Online Status**

#### تحقق من Admin Presence:
```
https://suntop-609f9-default-rtdb.europe-west1.firebasedatabase.app/admin_presence.json
```

### **4. اختبار الإشعارات**

#### إشعارات الإدارة:
```
https://suntop-609f9-default-rtdb.europe-west1.firebasedatabase.app/admin_notifications.json
```

#### إشعارات العملاء:
```
https://suntop-609f9-default-rtdb.europe-west1.firebasedatabase.app/customer_notifications/1.json
```

---

## 📊 **مراقبة Firebase Database**

### **1. هيكل البيانات المتوقع**
```json
{
  "chats": {
    "1": {
      "info": {
        "customer_id": 1,
        "customer_name": "أحمد محمد",
        "subject": "اختبار النظام",
        "status": "open",
        "priority": "high",
        "admin_unread_count": 1,
        "customer_unread_count": 0,
        "created_at": "2025-08-28T17:30:00.000Z",
        "updated_at": "2025-08-28T17:35:00.000Z"
      },
      "messages": {
        "1724861400_abc123": {
          "id": 1,
          "sender_name": "أحمد محمد",
          "sender_type": "customer",
          "message": "مرحباً!",
          "timestamp": 1724861400000,
          "created_at": "2025-08-28T17:30:00.000Z"
        }
      },
      "typing": {
        "user_type": "customer",
        "user_name": "أحمد محمد",
        "timestamp": 1724861500000
      }
    }
  },
  "admin_presence": {
    "1": {
      "name": "مدير النظام",
      "status": "online",
      "last_seen": "2025-08-28T17:35:00.000Z",
      "timestamp": 1724861500000
    }
  },
  "admin_notifications": {
    "1724861400_notification1": {
      "type": "new_message",
      "chat_id": 1,
      "customer_name": "أحمد محمد",
      "message": "رسالة جديدة من أحمد محمد",
      "timestamp": 1724861400000,
      "created_at": "2025-08-28T17:30:00.000Z"
    }
  },
  "customer_notifications": {
    "1": {
      "1724861600_reply1": {
        "type": "admin_reply",
        "chat_id": 1,
        "admin_name": "مدير النظام",
        "message": "رد جديد من مدير النظام",
        "timestamp": 1724861600000,
        "created_at": "2025-08-28T17:40:00.000Z"
      }
    }
  }
}
```

---

## 🔍 **استكشاف الأخطاء**

### **مشاكل شائعة وحلولها:**

#### 1. Firebase Connection Failed
```bash
# تحقق من الـ URL
curl "https://suntop-609f9-default-rtdb.europe-west1.firebasedatabase.app/test.json"
```

#### 2. Auth Token غير صحيح
```bash
# تحقق من صحة التوكن
POST http://127.0.0.1:8000/api/user/profile
Authorization: Bearer YOUR_TOKEN
```

#### 3. CSRF Token خطأ
```javascript
// تأكد من إرسال CSRF token
'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
```

#### 4. Real-time لا يعمل
- تحقق من JavaScript Console
- تأكد من تحميل Firebase SDK
- تحقق من الـ listeners

---

## ✅ **Checklist النهائي**

### **APIs:**
- [ ] اختبار الاتصال مع Firebase
- [ ] بدء شات جديد
- [ ] إرسال رسالة
- [ ] Typing indicators
- [ ] قراءة الرسائل
- [ ] تمييز كمقروء

### **Admin Panel:**
- [ ] عرض المحادثات Real-time
- [ ] إرسال رسائل فورية
- [ ] تحديث الحالة والأولوية
- [ ] Typing indicators
- [ ] الإحصائيات المباشرة
- [ ] Online admins list

### **Firebase:**
- [ ] تحديث البيانات فورياً
- [ ] Admin presence
- [ ] Customer notifications
- [ ] Admin notifications
- [ ] Chat stats

### **Flutter (اختياري):**
- [ ] الاتصال مع Firebase
- [ ] Real-time messages
- [ ] Typing indicators
- [ ] UI responsive

---

## 🎯 **نصائح للاختبار الأمثل**

1. **استخدم متصفحات متعددة** لمحاكاة users مختلفين
2. **افتح Developer Tools** لمراقبة الـ Network و Console
3. **راقب Firebase Database** مباشرة
4. **اختبر انقطاع الإنترنت** للتأكد من إعادة الاتصال
5. **جرب السيناريوهات الحقيقية** (رسائل متعددة، محادثات متعددة)

---

## 🔧 **إعدادات متقدمة (اختيارية)**

### إضافة Firebase Rules للأمان:
```json
{
  "rules": {
    "chats": {
      "$chatId": {
        ".read": true,
        ".write": true
      }
    },
    "admin_presence": {
      ".read": true,
      ".write": true
    },
    "admin_notifications": {
      ".read": true,
      ".write": true
    },
    "customer_notifications": {
      "$customerId": {
        ".read": true,
        ".write": true
      }
    }
  }
}
```

**🎉 الآن نظام الشات المباشر جاهز بالكامل مع Firebase!**
