# API Documentation - Admin Order Management

Base URL: `https://suntop-eg.com`

## Admin Order Management Endpoints
All endpoints in this section require admin authentication.

### List Orders
- **URL**: `/api/admin/orders`
- **Method**: `GET`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Accept: application/json`
- **Query Parameters**:
  - `page` (optional): Page number (default: 1)
  - `limit` (optional): Items per page (default: 20)
  - `search` (optional): Search by order number, customer name, or phone
  - `status` (optional): Filter by order status ('pending', 'processing', 'shipped', 'delivered', 'cancelled')
  - `payment_status` (optional): Filter by payment status ('pending', 'paid', 'failed', 'refunded')
  - `start_date` (optional): Filter by start date (format: YYYY-MM-DD)
  - `end_date` (optional): Filter by end date (format: YYYY-MM-DD)
  - `merchant_id` (optional): Filter by merchant ID
  - `sort` (optional): Sort field
  - `order` (optional): Sort order ('asc', 'desc')
- **Response (200 OK)**:
```json
{
  "success": true,
  "data": {
    "orders": [
      {
        "id": "1",
        "order_number": "ORD-2024-001",
        "status": "shipped",
        "status_text": "تم الشحن",
        "customer": {
          "id": "1",
          "name": "محمد أحمد",
          "phone": "+20 109 999 9999"
        },
        "merchant": {
          "id": "1",
          "name": "Fresh Juice Corner"
        },
        "total_amount": 20.79,
        "currency": "EGP",
        "payment_method": "cash_on_delivery",
        "payment_status": "pending",
        "items_count": 3,
        "tracking_number": "TRK123456",
        "created_at": "2024-01-20T10:30:00Z",
        "estimated_delivery_time": "2024-01-22T15:00:00Z"
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 20,
      "total": 532,
      "total_pages": 27,
      "has_next": true,
      "has_prev": false
    },
    "filters": {
      "statuses": ["pending", "processing", "shipped", "delivered", "cancelled"],
      "payment_statuses": ["pending", "paid", "failed", "refunded"],
      "merchants": [
        {
          "id": "1",
          "name": "Fresh Juice Corner"
        },
        {
          "id": "2",
          "name": "Juice Palace"
        }
      ]
    }
  }
}
```

### Get Order Details
- **URL**: `/api/admin/orders/{id}`
- **Method**: `GET`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Accept: application/json`
- **Response (200 OK)**:
```json
{
  "success": true,
  "data": {
    "id": "1",
    "order_number": "ORD-2024-001",
    "status": "shipped",
    "status_text": "تم الشحن",
    "customer": {
      "id": "1",
      "name": "محمد أحمد",
      "email": "customer@example.com",
      "phone": "+20 109 999 9999",
      "user_category": {
        "id": "2",
        "name": "ذهبي",
        "display_name": "عميل ذهبي"
      }
    },
    "merchant": {
      "id": "1",
      "name": "Fresh Juice Corner",
      "phone": "+20 109 999 9999"
    },
    "items": [
      {
        "id": "1",
        "product_id": "1",
        "product_name": "سن توب برتقال طازج",
        "product_image": "https://suntop-eg.com/storage/products/j1.jpg",
        "quantity": 2,
        "unit_price": 2.50,
        "total_price": 5.00
      },
      {
        "id": "2",
        "product_id": "2",
        "product_name": "سن توب ليمون طازج",
        "product_image": "https://suntop-eg.com/storage/products/j2.jpg",
        "quantity": 1,
        "unit_price": 2.99,
        "total_price": 2.99
      }
    ],
    "subtotal": 7.99,
    "delivery_fee": 15.0,
    "discount": 2.40,
    "loyalty_discount": 1.00,
    "tax": 1.20,
    "total_amount": 20.79,
    "currency": "EGP",
    "payment_method": "cash_on_delivery",
    "payment_status": "pending",
    "delivery_address": {
      "street": "شارع النيل",
      "building": "رقم 15",
      "apartment": "شقة 3",
      "city": "القاهرة",
      "district": "المعادي",
      "postal_code": "11728",
      "phone": "+20 109 999 9999",
      "notes": "الدور الثاني"
    },
    "tracking_history": [
      {
        "status": "pending",
        "status_text": "في انتظار التأكيد",
        "timestamp": "2024-01-20T10:30:00Z",
        "notes": null
      },
      {
        "status": "confirmed",
        "status_text": "تم تأكيد الطلب",
        "timestamp": "2024-01-20T11:00:00Z",
        "notes": "تم تأكيد الطلب من قبل المتجر"
      },
      {
        "status": "shipped",
        "status_text": "تم الشحن",
        "timestamp": "2024-01-21T09:00:00Z",
        "notes": "تم تسليم الطلب للشحن"
      }
    ],
    "tracking_number": "TRK123456",
    "estimated_delivery_time": "2024-01-22T15:00:00Z",
    "delivered_at": null,
    "notes": "يرجى التوصيل بحذر",
    "offer_used": {
      "code": "SUMMER30",
      "discount_amount": 2.40
    },
    "loyalty_points": {
      "earned": 10,
      "used": 100,
      "discount_amount": 1.00
    },
    "created_at": "2024-01-20T10:30:00Z",
    "updated_at": "2024-01-21T09:00:00Z"
  }
}
```

### Update Order Status
- **URL**: `/api/admin/orders/{id}/status`
- **Method**: `PUT`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Content-Type: application/json`
  - `Accept: application/json`
- **Request Body**:
```json
{
  "status": "delivered",
  "notes": "تم تسليم الطلب للعميل"
}
```
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "تم تحديث حالة الطلب بنجاح",
  "data": {
    "id": "1",
    "order_number": "ORD-2024-001",
    "status": "delivered",
    "status_text": "تم التسليم",
    "tracking_history": [
      {
        "status": "delivered",
        "status_text": "تم التسليم",
        "timestamp": "2024-01-22T10:00:00Z",
        "notes": "تم تسليم الطلب للعميل"
      }
    ],
    "updated_at": "2024-01-22T10:00:00Z"
  }
}
```

### Cancel Order
- **URL**: `/api/admin/orders/{id}/cancel`
- **Method**: `POST`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Content-Type: application/json`
  - `Accept: application/json`
- **Request Body**:
```json
{
  "reason": "الطلب غير متوفر",
  "notify_customer": true,
  "refund_type": "loyalty_points"
}
```
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "تم إلغاء الطلب بنجاح",
  "data": {
    "id": "1",
    "order_number": "ORD-2024-001",
    "status": "cancelled",
    "status_text": "ملغي",
    "updated_at": "2024-01-22T11:00:00Z",
    "refund": {
      "type": "loyalty_points",
      "amount": 100,
      "status": "processed"
    }
  }
}
```

### Add Tracking Information
- **URL**: `/api/admin/orders/{id}/tracking`
- **Method**: `POST`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Content-Type: application/json`
  - `Accept: application/json`
- **Request Body**:
```json
{
  "tracking_number": "TRK789012",
  "shipping_company": "Speed Delivery",
  "driver_name": "محمد أحمد",
  "driver_phone": "+20 100 123 4567",
  "estimated_delivery_time": "2024-01-23T15:00:00Z",
  "notes": "سيتم التوصيل خلال ساعات النهار"
}
```
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "تم تحديث معلومات التتبع بنجاح",
  "data": {
    "id": "1",
    "order_number": "ORD-2024-001",
    "tracking_number": "TRK789012",
    "shipping_company": "Speed Delivery",
    "driver_name": "محمد أحمد",
    "driver_phone": "+20 100 123 4567",
    "estimated_delivery_time": "2024-01-23T15:00:00Z",
    "updated_at": "2024-01-22T12:00:00Z"
  }
}
```

### Get Orders Statistics
- **URL**: `/api/admin/orders/statistics/overview`
- **Method**: `GET`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Accept: application/json`
- **Query Parameters**:
  - `period` (optional): Time period - 'today', 'week', 'month', 'year' (default: 'week')
- **Response (200 OK)**:
```json
{
  "success": true,
  "data": {
    "total_orders": 532,
    "total_sales": 12450.75,
    "average_order_value": 23.4,
    "orders_by_status": {
      "pending": 45,
      "processing": 32,
      "shipped": 28,
      "delivered": 423,
      "cancelled": 4
    },
    "payment_methods": {
      "cash_on_delivery": {
        "count": 338,
        "percentage": 63.5,
        "amount": 7856.25
      },
      "credit_card": {
        "count": 147,
        "percentage": 27.6,
        "amount": 3445.50
      },
      "wallet": {
        "count": 47,
        "percentage": 8.9,
        "amount": 1149.00
      }
    },
    "top_merchants": [
      {
        "id": "1",
        "name": "Fresh Juice Corner",
        "orders_count": 287,
        "revenue": 6750.50
      }
    ],
    "recent_activity": {
      "new_orders_today": 18,
      "delivered_today": 15,
      "cancelled_today": 1
    },
    "charts": {
      "orders_by_day": {
        "labels": ["الأحد", "الاثنين", "الثلاثاء", "الأربعاء", "الخميس", "الجمعة", "السبت"],
        "values": [85, 63, 92, 78, 65, 95, 54]
      }
    }
  }
}
```

### Export Orders
- **URL**: `/api/admin/orders/export/csv`
- **Method**: `GET`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Accept: application/json`
- **Query Parameters**:
  - `status` (optional): Filter by order status
  - `payment_status` (optional): Filter by payment status
  - `start_date` (optional): Filter by start date (format: YYYY-MM-DD)
  - `end_date` (optional): Filter by end date (format: YYYY-MM-DD)
  - `merchant_id` (optional): Filter by merchant ID
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "تم إنشاء ملف التصدير بنجاح",
  "data": {
    "file_url": "https://suntop-eg.com/storage/exports/orders_2024_01_22_120000.csv",
    "file_name": "orders_2024_01_22_120000.csv",
    "expires_at": "2024-01-29T12:00:00Z"
  }
}
```

## Error Responses

### Order Not Found (404)
```json
{
  "success": false,
  "error": {
    "message": "الطلب غير موجود",
    "timestamp": "2024-01-20T14:30:00Z"
  }
}
```

### Validation Error (422)
```json
{
  "success": false,
  "error": {
    "message": "خطأ في التحقق من صحة البيانات",
    "details": {
      "validation_errors": {
        "status": ["حالة الطلب غير صالحة"],
        "tracking_number": ["رقم التتبع مطلوب"]
      }
    },
    "timestamp": "2024-01-20T14:30:00Z"
  }
}
```

### Invalid Operation (400)
```json
{
  "success": false,
  "error": {
    "message": "لا يمكن إلغاء طلب تم شحنه أو تسليمه بالفعل",
    "timestamp": "2024-01-20T14:30:00Z"
  }
}
```
