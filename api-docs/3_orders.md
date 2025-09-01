# API Documentation - Orders

Base URL: `https://suntop-eg.com`

## Orders Endpoints

### Get All Orders (Protected)
- **URL**: `/api/orders`
- **Method**: `GET`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Accept: application/json`
- **Query Parameters**:
  - `status` (optional): Filter by order status (e.g., "pending", "shipped", "delivered")
  - `page` (optional): Page number (default: 1)
  - `limit` (optional): Items per page (default: 20)
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
        "merchant_name": "Fresh Juice Corner",
        "total_amount": 20.79,
        "currency": "EGP",
        "items_count": 3,
        "tracking_number": "TRK123456",
        "estimated_delivery": "2024-01-22T15:00:00Z",
        "created_at": "2024-01-20T10:30:00Z"
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 20,
      "total": 15,
      "total_pages": 1,
      "has_next": false,
      "has_prev": false
    }
  }
}
```

### Create New Order (Protected)
- **URL**: `/api/orders`
- **Method**: `POST`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Content-Type: application/json`
  - `Accept: application/json`
- **Request Body**:
```json
{
  "merchant_id": "1",
  "items": [
    {
      "product_id": "1",
      "quantity": 2,
      "unit_price": 2.50
    },
    {
      "product_id": "2",
      "quantity": 1,
      "unit_price": 2.99
    }
  ],
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
  "payment_method": "cash_on_delivery",
  "offer_code": "SUMMER30",
  "use_loyalty_points": 100,
  "notes": "يرجى التوصيل بحذر"
}
```
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "تم إنشاء الطلب بنجاح",
  "data": {
    "order": {
      "id": "1",
      "order_number": "ORD-2024-001",
      "status": "pending",
      "status_text": "في انتظار التأكيد",
      "merchant": {
        "id": "1",
        "name": "Fresh Juice Corner",
        "phone": "+20 109 999 9999"
      },
      "items": [
        {
          "product_id": "1",
          "product_name": "سن توب برتقال طازج",
          "product_image": "https://suntop-eg.com/storage/products/j1.jpg",
          "quantity": 2,
          "unit_price": 2.50,
          "total_price": 5.00
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
        "district": "المعادي"
      },
      "estimated_delivery_time": "2024-01-22T15:00:00Z",
      "tracking_number": "TRK123456",
      "notes": "يرجى التوصيل بحذر",
      "created_at": "2024-01-20T10:30:00Z"
    }
  }
}
```

### Get Order Details (Protected)
- **URL**: `/api/orders/{id}`
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
    "merchant": {
      "id": "1",
      "name": "Fresh Juice Corner",
      "phone": "+20 109 999 9999"
    },
    "items": [
      {
        "product_id": "1",
        "product_name": "سن توب برتقال طازج",
        "product_image": "https://suntop-eg.com/storage/products/j1.jpg",
        "quantity": 2,
        "unit_price": 2.50,
        "total_price": 5.00
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
      "district": "المعادي"
    },
    "estimated_delivery_time": "2024-01-22T15:00:00Z",
    "tracking_number": "TRK123456",
    "delivered_at": null,
    "notes": "يرجى التوصيل بحذر",
    "created_at": "2024-01-20T10:30:00Z"
  }
}
```

### Get Order Tracking (Protected)
- **URL**: `/api/orders/{id}/tracking`
- **Method**: `GET`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Accept: application/json`
- **Response (200 OK)**:
```json
{
  "success": true,
  "data": {
    "order_id": "1",
    "tracking_number": "TRK123456",
    "current_status": "shipped",
    "estimated_delivery": "2024-01-22T15:00:00Z",
    "tracking_history": [
      {
        "status": "pending",
        "status_text": "في انتظار التأكيد",
        "timestamp": "2024-01-20T10:30:00Z",
        "location": "Fresh Juice Corner",
        "driver_name": null,
        "driver_phone": null,
        "notes": null
      },
      {
        "status": "confirmed",
        "status_text": "تم تأكيد الطلب",
        "timestamp": "2024-01-20T11:00:00Z",
        "location": "Fresh Juice Corner",
        "driver_name": null,
        "driver_phone": null,
        "notes": null
      },
      {
        "status": "shipped",
        "status_text": "تم الشحن",
        "timestamp": "2024-01-21T09:00:00Z",
        "location": "مركز التوزيع - القاهرة",
        "driver_name": "محمد أحمد",
        "driver_phone": "+20 100 123 4567",
        "notes": null
      }
    ]
  }
}
```

### Cancel Order (Protected)
- **URL**: `/api/orders/{id}/cancel`
- **Method**: `POST`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Accept: application/json`
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "تم إلغاء الطلب بنجاح"
}
```

### Reorder (Protected)
- **URL**: `/api/orders/{id}/reorder`
- **Method**: `POST`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Accept: application/json`
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "تم إعادة الطلب بنجاح",
  "data": {
    "order": {
      "id": "2",
      "order_number": "ORD-2024-002",
      "status": "pending",
      "merchant": {
        "id": "1",
        "name": "Fresh Juice Corner"
      },
      "total_amount": 20.79,
      "tracking_number": "TRK789012",
      "created_at": "2024-01-20T15:30:00Z"
    }
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
        "merchant_id": ["معرف التاجر مطلوب"],
        "items": ["عناصر الطلب مطلوبة"]
      }
    },
    "timestamp": "2024-01-20T14:30:00Z"
  }
}
```

### Unauthorized (401)
```json
{
  "success": false,
  "error": {
    "message": "غير مصرح لك",
    "timestamp": "2024-01-20T14:30:00Z"
  }
}
```
