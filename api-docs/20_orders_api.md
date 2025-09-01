# 📦 Orders API Documentation

## Overview

نظام إدارة الطلبات المتكامل مع تتبع شامل للحالة وإشعارات فورية. يوفر النظام إدارة كاملة للطلبات من الإنشاء حتى التوصيل مع تنبيهات مستمرة للعملاء والإدارة.

## Base URL

```
https://suntop-eg.com/api
```

## Authentication

جميع endpoints تتطلب مصادقة عبر Bearer token:

```
Authorization: Bearer {access_token}
```

---

## 🛍️ Customer Orders API

### 1. Create New Order - إنشاء طلب جديد

**Endpoint:** `POST /orders`

**Headers:**
```json
{
  "Authorization": "Bearer {access_token}",
  "Content-Type": "application/json",
  "Accept": "application/json"
}
```

**Request Body:**
```json
{
  "merchant_id": "1",
  "items": [
    {
      "product_id": "5",
      "quantity": 2,
      "unit_price": 2.99,
      "selling_type": "unit"
    },
    {
      "product_id": "3",
      "quantity": 1,
      "unit_price": 35.88,
      "selling_type": "carton"
    }
  ],
  "delivery_address": {
    "street": "شارع التحرير",
    "building": "123",
    "apartment": "4أ",
    "city": "القاهرة",
    "district": "وسط البلد",
    "postal_code": "11511",
    "phone": "01234567890",
    "notes": "بجوار محطة المترو"
  },
  "payment_method": "cash_on_delivery",
  "offer_code": "SAVE10",
  "use_loyalty_points": 50,
  "notes": "يرجى الاتصال قبل التوصيل"
}
```

**Response:**
```json
{
  "success": true,
  "message": "تم إنشاء الطلب بنجاح",
  "data": {
    "order": {
      "id": "15",
      "order_number": "ORD-2025-001",
      "tracking_number": "TRK64F89A1B2C3D4",
      "status": "pending",
      "status_text": "في انتظار التأكيد",
      "merchant": {
        "id": "1",
        "name": "متجر سن توب الرئيسي",
        "phone": "01111111111"
      },
      "financial": {
        "subtotal": 41.86,
        "delivery_fee": 15.00,
        "discount": 4.19,
        "category_discount": 2.09,
        "loyalty_discount": 2.50,
        "tax": 6.79,
        "total_amount": 54.87,
        "currency": "EGP"
      },
      "items": [
        {
          "product_id": "5",
          "product_name": "سن توب برتقال طازج",
          "quantity": 2,
          "unit_price": 2.99,
          "total_price": 5.98,
          "selling_type": "unit"
        }
      ],
      "delivery_address": {
        "street": "شارع التحرير",
        "building": "123",
        "city": "القاهرة"
      },
      "estimated_delivery_time": "2025-01-21T18:30:00Z",
      "created_at": "2025-01-21T17:45:00Z"
    }
  }
}
```

---

### 2. Get User Orders - جلب طلبات المستخدم

**Endpoint:** `GET /orders`

**Query Parameters:**
- `status` (optional): pending, confirmed, preparing, shipped, delivered, cancelled
- `limit` (optional): عدد العناصر في الصفحة (افتراضي: 20)
- `page` (optional): رقم الصفحة (افتراضي: 1)

**Response:**
```json
{
  "success": true,
  "data": {
    "orders": [
      {
        "id": "15",
        "order_number": "ORD-2025-001",
        "status": "shipped",
        "status_text": "تم الشحن",
        "merchant": {
          "id": "1",
          "name": "متجر سن توب الرئيسي"
        },
        "total_amount": 54.87,
        "currency": "EGP",
        "items_count": 2,
        "estimated_delivery_time": "2025-01-21T18:30:00Z",
        "created_at": "2025-01-21T17:45:00Z",
        "can_cancel": false,
        "can_reorder": true,
        "can_rate": false
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 20,
      "total": 5,
      "total_pages": 1,
      "has_next": false,
      "has_prev": false
    },
    "summary": {
      "pending_count": 1,
      "confirmed_count": 2,
      "shipped_count": 1,
      "delivered_count": 1
    }
  }
}
```

---

### 3. Get Order Details - تفاصيل الطلب

**Endpoint:** `GET /orders/{id}`

**Response:**
```json
{
  "success": true,
  "data": {
    "id": "15",
    "order_number": "ORD-2025-001",
    "tracking_number": "TRK64F89A1B2C3D4",
    "status": "shipped",
    "status_text": "تم الشحن",
    "merchant": {
      "id": "1",
      "name": "متجر سن توب الرئيسي",
      "phone": "01111111111",
      "address": "شارع الجامعة، الجيزة"
    },
    "financial": {
      "subtotal": 41.86,
      "delivery_fee": 15.00,
      "discount": 4.19,
      "tax": 6.79,
      "total_amount": 54.87,
      "currency": "EGP"
    },
    "items": [
      {
        "id": "25",
        "product": {
          "id": "5",
          "name": "سن توب برتقال طازج",
          "image_url": "https://suntop-eg.com/storage/products/orange.jpg",
          "price": 2.99
        },
        "quantity": 2,
        "unit_price": 2.99,
        "total_price": 5.98,
        "selling_type": "unit"
      }
    ],
    "delivery_address": {
      "street": "شارع التحرير",
      "building": "123",
      "apartment": "4أ",
      "city": "القاهرة",
      "district": "وسط البلد",
      "phone": "01234567890"
    },
    "tracking": [
      {
        "status": "pending",
        "status_text": "في انتظار التأكيد",
        "location": "متجر سن توب الرئيسي",
        "timestamp": "2025-01-21T17:45:00Z",
        "time_ago": "منذ ساعتين"
      },
      {
        "status": "confirmed",
        "status_text": "تم تأكيد الطلب",
        "location": "متجر سن توب الرئيسي",
        "timestamp": "2025-01-21T18:00:00Z",
        "time_ago": "منذ ساعة و 45 دقيقة"
      },
      {
        "status": "shipped",
        "status_text": "تم الشحن",
        "location": "مركز التوزيع",
        "driver_name": "أحمد محمد",
        "driver_phone": "01098765432",
        "timestamp": "2025-01-21T19:30:00Z",
        "time_ago": "منذ 15 دقيقة"
      }
    ],
    "estimated_delivery_time": "2025-01-21T20:30:00Z",
    "created_at": "2025-01-21T17:45:00Z"
  }
}
```

---

### 4. Track Order - تتبع الطلب

**Endpoint:** `GET /orders/{id}/tracking`

**Response:**
```json
{
  "success": true,
  "data": {
    "order_number": "ORD-2025-001",
    "status": "shipped",
    "status_text": "تم الشحن",
    "progress_percentage": 75,
    "tracking_steps": [
      {
        "step": 1,
        "status": "pending",
        "status_text": "في انتظار التأكيد",
        "completed": true,
        "timestamp": "2025-01-21T17:45:00Z"
      },
      {
        "step": 2,
        "status": "confirmed",
        "status_text": "تم تأكيد الطلب",
        "completed": true,
        "timestamp": "2025-01-21T18:00:00Z"
      },
      {
        "step": 3,
        "status": "preparing",
        "status_text": "جاري التحضير",
        "completed": true,
        "timestamp": "2025-01-21T18:45:00Z"
      },
      {
        "step": 4,
        "status": "shipped",
        "status_text": "تم الشحن",
        "completed": true,
        "current": true,
        "timestamp": "2025-01-21T19:30:00Z",
        "location": "مركز التوزيع",
        "driver_name": "أحمد محمد",
        "driver_phone": "01098765432"
      },
      {
        "step": 5,
        "status": "delivered",
        "status_text": "تم التوصيل",
        "completed": false,
        "estimated_time": "2025-01-21T20:30:00Z"
      }
    ]
  }
}
```

---

### 5. Get Real-time Order Status - حالة الطلب الفورية

**Endpoint:** `GET /orders/{id}/status`

**Response:**
```json
{
  "success": true,
  "data": {
    "order_id": "15",
    "order_number": "ORD-2025-001",
    "status": "shipped",
    "status_text": "تم الشحن",
    "latest_tracking": {
      "location": "مركز التوزيع - الجيزة",
      "driver_name": "أحمد محمد",
      "driver_phone": "01098765432",
      "notes": "الطلب في الطريق إليك",
      "timestamp": "2025-01-21T19:30:00Z",
      "time_ago": "منذ 15 دقيقة"
    },
    "estimated_delivery_time": "2025-01-21T20:30:00Z",
    "delivered_at": null
  }
}
```

---

### 6. Cancel Order - إلغاء الطلب

**Endpoint:** `POST /orders/{id}/cancel`

**Response:**
```json
{
  "success": true,
  "message": "تم إلغاء الطلب بنجاح",
  "data": {
    "order_id": "15",
    "status": "cancelled",
    "refund_info": {
      "refund_amount": 54.87,
      "refund_method": "نقاط ولاء",
      "processing_time": "فوري"
    }
  }
}
```

---

### 7. Reorder - إعادة الطلب

**Endpoint:** `POST /orders/{id}/reorder`

**Response:**
```json
{
  "success": true,
  "message": "تم إعادة الطلب بنجاح",
  "data": {
    "order": {
      "id": "16",
      "order_number": "ORD-2025-002",
      "status": "pending",
      "total_amount": 54.87,
      "items_count": 2,
      "created_at": "2025-01-21T20:00:00Z"
    }
  }
}
```

---

### 8. Rate Order - تقييم الطلب

**Endpoint:** `POST /orders/{id}/rate`

**Request Body:**
```json
{
  "rating": 5,
  "review": "خدمة ممتازة والتوصيل سريع"
}
```

**Response:**
```json
{
  "success": true,
  "message": "تم تقييم الطلب بنجاح",
  "data": {
    "rating": 5,
    "review": "خدمة ممتازة والتوصيل سريع",
    "loyalty_points_earned": 10
  }
}
```

---

### 9. Get Order History - تاريخ الطلبات

**Endpoint:** `GET /orders/history`

**Response:**
```json
{
  "success": true,
  "data": {
    "recent_orders": [
      {
        "id": "15",
        "order_number": "ORD-2025-001",
        "status": "delivered",
        "total_amount": 54.87,
        "created_at": "2025-01-21T17:45:00Z"
      }
    ],
    "favorite_items": [
      {
        "product": {
          "id": "5",
          "name": "سن توب برتقال طازج",
          "image_url": "url",
          "price": 2.99
        },
        "total_quantity": 12,
        "order_count": 6
      }
    ],
    "statistics": {
      "total_orders": 25,
      "total_spent": 1247.50,
      "avg_order_value": 49.90,
      "favorite_merchant": "متجر سن توب الرئيسي"
    }
  }
}
```

---

## 🔧 Admin Orders Management API

### 1. Get All Orders - جلب جميع الطلبات (للأدمن)

**Endpoint:** `GET /admin/orders`

**Query Parameters:**
- `status` (optional): حالة الطلب
- `payment_status` (optional): حالة الدفع
- `merchant_id` (optional): معرف التاجر
- `date_from` (optional): تاريخ البداية
- `date_to` (optional): تاريخ النهاية
- `search` (optional): البحث في رقم الطلب أو بيانات العميل
- `sort_by` (optional): ترتيب حسب (افتراضي: created_at)
- `sort_order` (optional): اتجاه الترتيب (افتراضي: desc)
- `limit` (optional): عدد العناصر في الصفحة

**Response:**
```json
{
  "success": true,
  "data": {
    "orders": [
      {
        "id": "15",
        "order_number": "ORD-2025-001",
        "tracking_number": "TRK64F89A1B2C3D4",
        "user": {
          "id": "123",
          "name": "أحمد محمد",
          "email": "ahmed@example.com",
          "phone": "01234567890",
          "category": {
            "name": "عميل ذهبي",
            "discount_percentage": 10
          }
        },
        "merchant": {
          "id": "1",
          "name": "متجر سن توب الرئيسي",
          "is_open": true
        },
        "status": "shipped",
        "status_text": "تم الشحن",
        "payment_method": "cash_on_delivery",
        "payment_method_text": "الدفع عند الاستلام",
        "payment_status": "pending",
        "financial": {
          "subtotal": 41.86,
          "delivery_fee": 15.00,
          "total_amount": 54.87,
          "currency": "EGP"
        },
        "items_count": 2,
        "created_at": "2025-01-21T17:45:00Z",
        "order_age_hours": 3,
        "is_overdue": false
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 20,
      "total": 150,
      "total_pages": 8,
      "has_next": true,
      "has_prev": false
    },
    "summary": {
      "total_orders": 150,
      "pending_orders": 25,
      "confirmed_orders": 30,
      "shipped_orders": 20,
      "delivered_orders": 70,
      "cancelled_orders": 5,
      "total_revenue": 12500.50
    }
  }
}
```

---

### 2. Get Order Details (Admin) - تفاصيل الطلب للأدمن

**Endpoint:** `GET /admin/orders/{id}`

**Response:**
```json
{
  "success": true,
  "data": {
    "order": {
      "id": "15",
      "order_number": "ORD-2025-001",
      "tracking_number": "TRK64F89A1B2C3D4",
      "user": {
        "id": "123",
        "name": "أحمد محمد",
        "email": "ahmed@example.com",
        "phone": "01234567890",
        "avatar_url": "url",
        "total_orders": 25,
        "total_spent": 1247.50,
        "category": {
          "name": "عميل ذهبي",
          "discount_percentage": 10
        }
      },
      "merchant": {
        "id": "1",
        "name": "متجر سن توب الرئيسي",
        "phone": "01111111111",
        "address": "شارع الجامعة، الجيزة",
        "delivery_fee": 15.00
      },
      "status": "shipped",
      "status_text": "تم الشحن",
      "financial": {
        "subtotal": 41.86,
        "delivery_fee": 15.00,
        "discount": 4.19,
        "tax": 6.79,
        "total_amount": 54.87,
        "currency": "EGP"
      },
      "items": [
        {
          "id": "25",
          "product_id": "5",
          "product_name": "سن توب برتقال طازج",
          "product": {
            "id": "5",
            "name": "سن توب برتقال طازج",
            "image_url": "url",
            "price": 2.99,
            "stock_quantity": 48
          },
          "quantity": 2,
          "unit_price": 2.99,
          "total_price": 5.98,
          "selling_type": "unit"
        }
      ],
      "delivery_address": {
        "street": "شارع التحرير",
        "building": "123",
        "city": "القاهرة",
        "phone": "01234567890"
      },
      "tracking": [
        {
          "id": "45",
          "status": "shipped",
          "status_text": "تم الشحن",
          "location": "مركز التوزيع",
          "driver_name": "أحمد محمد",
          "driver_phone": "01098765432",
          "notes": "الطلب في الطريق",
          "timestamp": "2025-01-21T19:30:00Z",
          "time_ago": "منذ 15 دقيقة"
        }
      ],
      "can_cancel": false,
      "can_update_status": true,
      "next_possible_statuses": [
        {
          "value": "delivered",
          "text": "تم التوصيل"
        }
      ],
      "created_at": "2025-01-21T17:45:00Z"
    }
  }
}
```

---

### 3. Update Order Status - تحديث حالة الطلب

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

### 4. Add Tracking Update - إضافة تحديث التتبع

**Endpoint:** `POST /admin/orders/{id}/tracking`

**Request Body:**
```json
{
  "status": "shipped",
  "location": "مركز التوزيع - القاهرة",
  "driver_name": "أحمد محمد",
  "driver_phone": "01098765432",
  "notes": "تم تحميل الطلب في سيارة التوصيل"
}
```

**Response:**
```json
{
  "success": true,
  "message": "تم إضافة تحديث التتبع بنجاح",
  "data": {
    "tracking": {
      "id": "46",
      "status": "shipped",
      "status_text": "تم الشحن",
      "location": "مركز التوزيع - القاهرة",
      "driver_name": "أحمد محمد",
      "driver_phone": "01098765432",
      "notes": "تم تحميل الطلب في سيارة التوصيل",
      "timestamp": "2025-01-21T19:45:00Z"
    }
  }
}
```

---

### 5. Cancel Order (Admin) - إلغاء الطلب (أدمن)

**Endpoint:** `POST /admin/orders/{id}/cancel`

**Request Body:**
```json
{
  "reason": "نفاد المخزون"
}
```

**Response:**
```json
{
  "success": true,
  "message": "تم إلغاء الطلب بنجاح",
  "data": {
    "order": {
      "id": "15",
      "status": "cancelled",
      "cancellation_reason": "نفاد المخزون",
      "cancelled_at": "2025-01-21T20:00:00Z"
    },
    "stock_restored": true,
    "refund_processed": false
  }
}
```

---

### 6. Bulk Update Orders Status - تحديث مجمع لحالة الطلبات

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

### 7. Orders Dashboard - لوحة تحكم الطلبات

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

### 8. Orders Statistics - إحصائيات الطلبات

**Endpoint:** `GET /admin/orders/statistics/overview`

**Query Parameters:**
- `date_from` (optional): تاريخ البداية
- `date_to` (optional): تاريخ النهاية

**Response:**
```json
{
  "success": true,
  "data": {
    "statistics": {
      "overview": {
        "total_orders": 600,
        "total_revenue": 30000.00,
        "average_order_value": 50.00,
        "orders_today": 25
      },
      "by_status": [
        {
          "status": "delivered",
          "status_text": "تم التوصيل",
          "count": 450,
          "revenue": 22500.00
        },
        {
          "status": "pending",
          "status_text": "في انتظار التأكيد",
          "count": 25,
          "revenue": 1250.00
        }
      ],
      "by_merchant": [
        {
          "merchant": {
            "id": "1",
            "name": "متجر سن توب الرئيسي"
          },
          "count": 400,
          "revenue": 20000.00
        }
      ],
      "daily_orders": [
        {
          "date": "2025-01-21",
          "count": 25,
          "revenue": 1250.75
        }
      ],
      "top_customers": [
        {
          "user": {
            "id": "123",
            "name": "أحمد محمد",
            "email": "ahmed@example.com"
          },
          "order_count": 15,
          "total_spent": 750.50
        }
      ]
    }
  }
}
```

---

## 📊 Notification System Integration

النظام يرسل إشعارات فورية للعملاء عند:

### إشعارات العملاء:
1. **إنشاء الطلب** - تأكيد إنشاء الطلب
2. **تأكيد الطلب** - تأكيد الطلب من التاجر
3. **بدء التحضير** - بدء تحضير الطلب
4. **الشحن** - شحن الطلب مع بيانات السائق
5. **في الطريق** - الطلب في الطريق للعميل
6. **التوصيل** - تم توصيل الطلب
7. **الإلغاء** - إلغاء الطلب مع السبب

### إشعارات الإدارة:
1. **طلب جديد** - وصول طلب جديد للنظام
2. **طلبات متأخرة** - طلبات تجاوزت الوقت المتوقع
3. **طلبات عاجلة** - طلبات في انتظار التأكيد لفترة طويلة

---

## 🔄 Order Status Flow

```
pending → confirmed → preparing → shipped → delivered
   ↓         ↓           ↓
cancelled  cancelled  cancelled
```

**القواعد:**
- يمكن إلغاء الطلب في حالات: pending, confirmed, preparing
- لا يمكن تغيير حالة الطلبات المكتملة (delivered) أو الملغاة (cancelled)
- التحديثات التلقائية للمخزون عند الإلغاء
- حساب نقاط الولاء عند التوصيل

---

## 📱 Mobile App Integration Examples

### Flutter Service Example:
```dart
class OrderService {
  static const String baseUrl = 'https://suntop-eg.com/api';
  
  // Create order
  Future<OrderResponse> createOrder(CreateOrderRequest request) async {
    final response = await http.post(
      Uri.parse('$baseUrl/orders'),
      headers: {
        'Authorization': 'Bearer $accessToken',
        'Content-Type': 'application/json',
      },
      body: json.encode(request.toJson()),
    );
    
    return OrderResponse.fromJson(json.decode(response.body));
  }
  
  // Track order real-time
  Future<OrderStatusResponse> trackOrder(String orderId) async {
    final response = await http.get(
      Uri.parse('$baseUrl/orders/$orderId/status'),
      headers: {
        'Authorization': 'Bearer $accessToken',
        'Accept': 'application/json',
      },
    );
    
    return OrderStatusResponse.fromJson(json.decode(response.body));
  }
  
  // Get order history
  Future<OrderHistoryResponse> getOrderHistory() async {
    final response = await http.get(
      Uri.parse('$baseUrl/orders/history'),
      headers: {
        'Authorization': 'Bearer $accessToken',
        'Accept': 'application/json',
      },
    );
    
    return OrderHistoryResponse.fromJson(json.decode(response.body));
  }
}
```

---

## 🛡️ Error Handling

### Common Error Responses:

**400 Validation Error:**
```json
{
  "success": false,
  "error": {
    "message": "خطأ في التحقق من صحة البيانات",
    "details": {
      "validation_errors": {
        "items": ["عناصر الطلب مطلوبة"],
        "delivery_address.phone": ["رقم الهاتف مطلوب"]
      }
    }
  }
}
```

**404 Order Not Found:**
```json
{
  "success": false,
  "error": {
    "message": "الطلب غير موجود",
    "timestamp": "2025-01-21T20:00:00Z"
  }
}
```

**422 Business Logic Error:**
```json
{
  "success": false,
  "error": {
    "message": "لا يمكن إلغاء هذا الطلب في هذه المرحلة",
    "current_status": "shipped",
    "allowed_actions": ["track", "rate"]
  }
}
```

---

## 🔧 Best Practices

1. **Real-time Updates**: استخدم `/orders/{id}/status` للتحديثات الفورية
2. **Caching**: احفظ بيانات الطلبات محلياً واستخدم التحديثات التزايدية
3. **Notifications**: اربط النظام بإشعارات Firebase/OneSignal
4. **Error Handling**: تعامل مع انقطاع الاتصال بحفظ الطلبات محلياً
5. **Performance**: استخدم pagination للطلبات الكثيرة
6. **Security**: تحقق من صحة المستخدم قبل عرض بيانات الطلب

---

**Last Updated:** January 21, 2025  
**API Version:** 1.0  
**Status:** Production Ready

هذا النظام المتكامل للطلبات يوفر تجربة شاملة للعملاء والإدارة مع تتبع فوري وإشعارات ذكية لضمان أفضل خدمة ممكنة.
