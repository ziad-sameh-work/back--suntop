# 🏷️ Admin Orders Dashboard API

## Overview

Dashboard متقدم لإدارة الطلبات مع تحديثات فورية، إشعارات ذكية، وإحصائيات شاملة للأدمن.

## Base URL

```
http://127.0.0.1:8000/api/admin
```

## Authentication

```
Authorization: Bearer {admin_access_token}
Middleware: auth:sanctum, role:admin
```

---

## 📊 **Dashboard Endpoints**

### 1. Real-time Dashboard Data

**Endpoint:** `GET /admin/orders/dashboard`

**Response:**
```json
{
  "success": true,
  "data": {
    "dashboard": {
      "today": {
        "total_orders": 25,
        "pending_orders": 5,
        "confirmed_orders": 8,
        "shipped_orders": 7,
        "delivered_orders": 5,
        "revenue": 1250.75
      },
      "yesterday": {
        "total_orders": 18,
        "revenue": 890.50
      },
      "this_week": {
        "total_orders": 150,
        "revenue": 7500.25,
        "avg_order_value": 50.00
      },
      "this_month": {
        "total_orders": 600,
        "revenue": 30000.00
      },
      "recent_orders": [
        {
          "id": "20",
          "order_number": "ORD-2025-005",
          "user": {
            "name": "فاطمة أحمد"
          },
          "status": "pending",
          "total_amount": 75.50,
          "created_at": "2025-01-21T20:15:00Z"
        }
      ],
      "overdue_orders": 3,
      "urgent_pending": 2
    }
  }
}
```

---

### 2. Update Order Status (Enhanced)

**Endpoint:** `PUT /admin/orders/{id}/status`

**Request Body:**
```json
{
  "status": "shipped",
  "location": "مركز التوزيع - الجيزة",
  "driver_name": "أحمد محمد",
  "driver_phone": "01098765432", 
  "notes": "الطلب في الطريق إليك",
  "estimated_delivery_minutes": 60,
  "send_notification": true
}
```

**Response:**
```json
{
  "success": true,
  "message": "تم تحديث حالة الطلب بنجاح",
  "data": {
    "order": {
      "id": "15",
      "status": "shipped",
      "status_text": "تم الشحن",
      "latest_tracking": {
        "location": "مركز التوزيع - الجيزة",
        "driver_name": "أحمد محمد",
        "driver_phone": "01098765432",
        "timestamp": "2025-01-21T19:30:00Z"
      }
    },
    "notification_sent": true
  }
}
```

---

### 3. Bulk Status Update

**Endpoint:** `POST /admin/orders/bulk-update-status`

**Request Body:**
```json
{
  "order_ids": [15, 16, 17, 18],
  "status": "confirmed",
  "location": "متجر سن توب الرئيسي",
  "notes": "تم تأكيد جميع الطلبات",
  "send_notifications": true
}
```

**Response:**
```json
{
  "success": true,
  "message": "تم تحديث 4 طلب بنجاح",
  "data": {
    "updated_count": 4,
    "total_count": 4,
    "notifications_sent": 4
  }
}
```

---

## 🔔 **Notifications Integration**

### للأدمن Dashboard:
```javascript
// Auto-refresh every 30 seconds
setInterval(() => {
  fetch('/api/admin/orders/dashboard')
    .then(response => response.json())
    .then(data => {
      updatePendingOrdersBadge(data.data.dashboard.today.pending_orders);
      
      if (data.data.dashboard.urgent_pending > 0) {
        showUrgentOrdersNotification(data.data.dashboard.urgent_pending);
      }
    });
}, 30000);
```

### إشعارات العملاء التلقائية:
عند تحديث حالة الطلب من الأدمن، يتم إرسال إشعار تلقائي للعميل:

```json
{
  "title": "تم شحن طلبك",
  "message": "طلبك رقم ORD-2025-001 تم شحنه وهو في الطريق إليك مع السائق أحمد محمد",
  "type": "order_status",
  "data": {
    "order_id": "15",
    "order_number": "ORD-2025-001",
    "status": "shipped",
    "driver_name": "أحمد محمد",
    "driver_phone": "01098765432",
    "location": "مركز التوزيع"
  }
}
```

---

## 📱 **UI Features**

### ✅ **تم تنفيذها:**

#### 1. السايدبار بـ Badge الطلبات:
```html
<div class="nav-item">
    <a href="/admin/orders" class="nav-link">
        <i class="fas fa-shopping-cart"></i>
        <span class="nav-text">الطلبات</span>
        <span class="nav-badge">5</span> <!-- عدد الطلبات المعلقة -->
    </a>
</div>
```

#### 2. التحديث التلقائي:
- تحديث كل 30 ثانية
- مؤشر التحديث التلقائي في أسفل الصفحة
- أيقونة دوارة أثناء التحديث

#### 3. الإشعارات الفورية:
- إشعار منبثق للطلبات العاجلة
- صوت تنبيه للطلبات الجديدة (اختياري)
- تحديث Badge السايدبار فورياً

#### 4. إحصائيات متقدمة:
- طلبات اليوم/الأسبوع/الشهر
- طلبات متأخرة وعاجلة
- إيرادات فورية
- كروت إحصائيات قابلة للنقر

#### 5. فلترة وبحث محسن:
- بحث فوري مع debounce
- فلترة بالحالة والتاريخ
- زر تحديث يدوي

---

## 🎨 **Visual Enhancements**

### Animations:
```css
@keyframes newOrderPulse {
    0%, 100% { background: rgba(16, 185, 129, 0.1); }
    50% { background: rgba(16, 185, 129, 0.2); }
}

.new-order-highlight {
    animation: newOrderPulse 2s ease-in-out;
}

.urgent-order {
    border-right: 4px solid #EF4444 !important;
    background: rgba(239, 68, 68, 0.05) !important;
}
```

### Status Badges:
- ألوان مختلفة لكل حالة
- animation عند التحديث
- cursor pointer للتفاعل

---

## 🔄 **Workflow الكامل**

### 1. العميل يضع طلب:
```javascript
// من التطبيق
POST /api/orders {
  merchant_id: "1",
  items: [...]
}
```

### 2. الطلب يظهر للأدمن فوراً:
- Badge في السايدبار يتحدث
- إشعار منبثق إذا كان عاجل
- يظهر في قائمة الطلبات

### 3. الأدمن يحدث الحالة:
```javascript
PUT /api/admin/orders/15/status {
  status: "confirmed",
  send_notification: true
}
```

### 4. العميل يستلم إشعار:
- إشعار فوري في التطبيق
- تحديث حالة الطلب

---

## 📊 **المميزات المتقدمة**

### Real-time Updates:
- تحديث تلقائي كل 30 ثانية
- إشعارات فورية للطلبات الجديدة
- تحديث Badge السايدبار بدون refresh

### Smart Notifications:
- إشعارات للطلبات العاجلة فقط
- إشعارات للعملاء عند تحديث الحالة
- صوت تنبيه للطلبات المهمة

### Enhanced UX:
- Loading animations
- Hover effects
- Color-coded statuses
- Auto-refresh indicator

---

## 🛠️ **التشغيل والإعداد**

### 1. Requirements:
- Laravel 10+
- MySQL/PostgreSQL
- Redis (للإشعارات)
- Pusher/Socket.io (للتحديثات الفورية)

### 2. Installation:
```bash
# Run migrations
php artisan migrate

# Start queue worker
php artisan queue:work

# Start websocket server (optional)
php artisan websockets:serve
```

### 3. Frontend Integration:
```javascript
// في الأدمن Dashboard
const ordersAPI = {
  getDashboard: () => fetch('/api/admin/orders/dashboard'),
  updateStatus: (id, data) => fetch(`/api/admin/orders/${id}/status`, {
    method: 'PUT',
    body: JSON.stringify(data)
  }),
  bulkUpdate: (data) => fetch('/api/admin/orders/bulk-update-status', {
    method: 'POST', 
    body: JSON.stringify(data)
  })
};
```

---

## 🎯 **النتيجة النهائية**

### للأدمن:
- ✅ Dashboard متكامل بالستايل المطلوب
- ✅ تحديثات فورية للطلبات الجديدة  
- ✅ إشعارات ذكية للطلبات العاجلة
- ✅ إدارة سهلة للطلبات المجمعة
- ✅ إحصائيات شاملة ومفيدة

### للعملاء:
- ✅ إشعارات فورية لتحديث الطلب
- ✅ تتبع دقيق لحالة الطلب
- ✅ معلومات السائق والتوصيل
- ✅ تجربة مستخدم محسنة

**النظام جاهز تماماً للاستخدام!** 🚀

---

**Last Updated:** January 21, 2025  
**Status:** Production Ready  
**Dashboard:** Fully Functional with Real-time Updates

