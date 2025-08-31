# 🔐 API Guide - تسجيل دخول المستخدمين العاديين

## 🌐 Base URL
```
https://suntop-eg.com/api
```

---

## 🔑 1. تسجيل الدخول (Login)

### **📍 Endpoint:**
```
POST /auth/login
```

### **📋 Headers:**
```json
{
  "Content-Type": "application/json",
  "Accept": "application/json"
}
```

### **📝 Request Body:**
```json
{
  "username": "testuser",
  "password": "password123"
}
```

### **📊 نموذج Request (مثال كامل):**
```bash
curl -X POST https://suntop-eg.com/api/auth/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "username": "testuser",
    "password": "password123"
  }'
```

### **✅ Response الناجح (200):**
```json
{
  "success": true,
  "message": "تم تسجيل الدخول بنجاح",
  "data": {
    "access_token": "1|eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "refresh_token": "2|eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "token_type": "Bearer",
    "expires_in": 3600,
    "user": {
      "id": "1",
      "username": "testuser",
      "email": "test@suntop.com",
      "full_name": "مستخدم تجريبي",
      "phone": "+20 109 999 9999",
      "role": "customer",
      "is_active": true,
      "profile_image": null,
      "created_at": "2024-01-20T10:30:00Z",
      "last_login_at": "2024-01-20T14:30:00Z",
      "user_category": {
        "id": "2",
        "name": "Regular",
        "display_name": "عميل عادي",
        "discount_percentage": 5.00
      },
      "loyalty_points": {
        "current_balance": 450
      },
      "total_cartons_purchased": 2,
      "total_packages_purchased": 5,
      "total_orders_count": 3
    }
  }
}
```

### **❌ Response الفاشل (401):**
```json
{
  "success": false,
  "message": "اسم المستخدم أو كلمة المرور غير صحيحة",
  "data": null
}
```

### **⚠️ Response - حساب غير مفعل (401):**
```json
{
  "success": false,
  "message": "الحساب غير مفعل",
  "data": null
}
```

### **📋 Validation Errors (422):**
```json
{
  "success": false,
  "message": "خطأ في البيانات المدخلة",
  "data": {
    "username": ["اسم المستخدم مطلوب"],
    "password": ["كلمة المرور يجب أن تحتوي على 6 أحرف على الأقل"]
  }
}
```

---

## 🔄 2. تجديد التوكن (Refresh Token)

### **📍 Endpoint:**
```
POST /auth/refresh-token
```

### **📋 Headers:**
```json
{
  "Content-Type": "application/json",
  "Accept": "application/json"
}
```

### **📝 Request Body:**
```json
{
  "refresh_token": "2|eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
}
```

### **✅ Response الناجح (200):**
```json
{
  "success": true,
  "message": "تم تجديد التوكن بنجاح",
  "data": {
    "access_token": "3|newTokenHere...",
    "refresh_token": "4|newRefreshTokenHere...",
    "token_type": "Bearer",
    "expires_in": 3600
  }
}
```

---

## 👤 3. عرض الملف الشخصي (Profile)

### **📍 Endpoint:**
```
GET /auth/profile
```

### **📋 Headers:**
```json
{
  "Authorization": "Bearer 1|eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
  "Accept": "application/json"
}
```

### **✅ Response الناجح (200):**
```json
{
  "success": true,
  "data": {
    "id": "1",
    "username": "testuser",
    "email": "test@suntop.com",
    "full_name": "مستخدم تجريبي",
    "phone": "+20 109 999 9999",
    "role": "customer",
    "is_active": true,
    "profile_image": null,
    "created_at": "2024-01-20T10:30:00Z",
    "last_login_at": "2024-01-20T14:30:00Z",
    "user_category": {
      "id": "2",
      "name": "Regular",
      "display_name": "عميل عادي",
      "discount_percentage": 5.00
    },
    "loyalty_points": {
      "current_balance": 450
    }
  }
}
```

---

## 🚪 4. تسجيل الخروج (Logout)

### **📍 Endpoint:**
```
POST /auth/logout
```

### **📋 Headers:**
```json
{
  "Authorization": "Bearer 1|eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
  "Accept": "application/json"
}
```

### **✅ Response الناجح (200):**
```json
{
  "success": true,
  "message": "تم تسجيل الخروج بنجاح",
  "data": null
}
```

---

## 🔐 5. تغيير كلمة المرور (Reset Password)

### **📍 Endpoint:**
```
POST /auth/reset-password
```

### **📋 Headers:**
```json
{
  "Authorization": "Bearer 1|eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
  "Content-Type": "application/json",
  "Accept": "application/json"
}
```

### **📝 Request Body:**
```json
{
  "old_password": "password123",
  "new_password": "newpassword456",
  "confirm_password": "newpassword456"
}
```

### **✅ Response الناجح (200):**
```json
{
  "success": true,
  "message": "تم تغيير كلمة المرور بنجاح",
  "data": null
}
```

---

## 🧪 بيانات المستخدمين التجريبيين

### **👤 مستخدم تجريبي 1:**
```json
{
  "username": "testuser",
  "password": "password123",
  "email": "test@suntop.com",
  "full_name": "مستخدم تجريبي"
}
```

### **👤 مستخدم تجريبي 2:**
```json
{
  "username": "mohamed_ali",
  "password": "password123",
  "email": "mohamed@suntop.com",
  "full_name": "محمد علي"
}
```

### **👤 مستخدم تجريبي 3:**
```json
{
  "username": "ahmed_hassan",
  "password": "password123",
  "email": "ahmed@suntop.com",
  "full_name": "أحمد حسن"
}
```

---

## 📱 JavaScript Example

```javascript
class SunTopAPI {
    constructor() {
        this.baseURL = 'https://suntop-eg.com/api';
        this.token = localStorage.getItem('access_token');
    }

    async login(username, password) {
        try {
            const response = await fetch(`${this.baseURL}/auth/login`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    username: username,
                    password: password
                })
            });

            const data = await response.json();
            
            if (data.success) {
                // حفظ التوكن
                localStorage.setItem('access_token', data.data.access_token);
                localStorage.setItem('refresh_token', data.data.refresh_token);
                localStorage.setItem('user', JSON.stringify(data.data.user));
                
                this.token = data.data.access_token;
                return data.data;
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            console.error('Login failed:', error);
            throw error;
        }
    }

    async getProfile() {
        try {
            const response = await fetch(`${this.baseURL}/auth/profile`, {
                headers: {
                    'Authorization': `Bearer ${this.token}`,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            
            if (data.success) {
                return data.data;
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            console.error('Get profile failed:', error);
            throw error;
        }
    }

    async logout() {
        try {
            const response = await fetch(`${this.baseURL}/auth/logout`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${this.token}`,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            
            if (data.success) {
                // مسح البيانات المحلية
                localStorage.removeItem('access_token');
                localStorage.removeItem('refresh_token');
                localStorage.removeItem('user');
                this.token = null;
                
                return true;
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            console.error('Logout failed:', error);
            throw error;
        }
    }
}

// Usage Example
const api = new SunTopAPI();

// تسجيل الدخول
api.login('testuser', 'password123')
    .then(userData => {
        console.log('Login successful:', userData);
        // إعادة توجيه للصفحة الرئيسية
    })
    .catch(error => {
        console.error('Login error:', error.message);
    });
```

---

## 📱 Flutter/Dart Example

```dart
class SunTopAPI {
  static const String baseURL = 'https://suntop-eg.com/api';
  String? accessToken;
  String? refreshToken;

  Future<Map<String, dynamic>> login(String username, String password) async {
    try {
      final response = await http.post(
        Uri.parse('$baseURL/auth/login'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: jsonEncode({
          'username': username,
          'password': password,
        }),
      );

      final data = jsonDecode(response.body);

      if (data['success'] == true) {
        accessToken = data['data']['access_token'];
        refreshToken = data['data']['refresh_token'];
        
        // حفظ التوكن محلياً
        SharedPreferences prefs = await SharedPreferences.getInstance();
        await prefs.setString('access_token', accessToken!);
        await prefs.setString('refresh_token', refreshToken!);
        await prefs.setString('user', jsonEncode(data['data']['user']));
        
        return data['data'];
      } else {
        throw Exception(data['message']);
      }
    } catch (e) {
      throw Exception('فشل تسجيل الدخول: $e');
    }
  }

  Future<Map<String, dynamic>> getProfile() async {
    try {
      final response = await http.get(
        Uri.parse('$baseURL/auth/profile'),
        headers: {
          'Authorization': 'Bearer $accessToken',
          'Accept': 'application/json',
        },
      );

      final data = jsonDecode(response.body);

      if (data['success'] == true) {
        return data['data'];
      } else {
        throw Exception(data['message']);
      }
    } catch (e) {
      throw Exception('فشل جلب الملف الشخصي: $e');
    }
  }
}
```

---

## 🚨 ملاحظات مهمة

### **🔐 الأمان:**
- **Access Token**: صالح لمدة ساعة واحدة فقط
- **Refresh Token**: صالح لمدة 30 يوم
- يتم حذف جميع التوكنات القديمة عند تسجيل دخول جديد

### **👤 تسجيل الدخول:**
- يمكن استخدام **username** أو **email** في حقل username
- كلمة المرور يجب أن تكون على الأقل 6 أحرف
- الحساب يجب أن يكون مفعل (`is_active = true`)

### **📊 بيانات المستخدم:**
- **role**: دائماً `"customer"` للمستخدمين العاديين
- **user_category**: فئة المستخدم (Starter, Regular, Premium, Wholesale)
- **loyalty_points**: نقاط الولاء الحالية
- **total_cartons_purchased**: إجمالي الكراتين المشتراة
- **total_packages_purchased**: إجمالي العلب المشتراة

### **🔄 إدارة التوكن:**
- احفظ التوكن في `localStorage` أو `SharedPreferences`
- استخدم Refresh Token لتجديد Access Token
- تحقق من انتهاء صلاحية التوكن وجدده تلقائياً

---

## 🧪 اختبار API

### **Postman Collection:**
يمكنك استيراد ملف Postman من: `SunTop_API_Collection.postman_collection.json`

### **cURL Commands:**
```bash
# تسجيل الدخول
curl -X POST https://suntop-eg.com/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"username":"testuser","password":"password123"}'

# عرض الملف الشخصي
curl -X GET https://suntop-eg.com/api/auth/profile \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"

# تسجيل الخروج
curl -X POST https://suntop-eg.com/api/auth/logout \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

---

## 📞 الدعم

إذا واجهت أي مشاكل في تسجيل الدخول:

1. **تأكد من البيانات**: username وpassword صحيحين
2. **تحقق من Headers**: Content-Type و Accept
3. **فحص الاستجابة**: اقرأ رسالة الخطأ في response
4. **تحقق من قاعدة البيانات**: تأكد أن المستخدم موجود ومفعل

**🎯 الآن يمكنك تسجيل الدخول بنجاح واستخدام جميع APIs المحمية!**
