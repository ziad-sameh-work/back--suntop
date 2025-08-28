# API Documentation - Admin Loyalty Points Management

Base URL: `http://127.0.0.1:8000`

## Admin Loyalty Points Management Endpoints
All endpoints in this section require admin authentication.

### List Loyalty Transactions
- **URL**: `/api/admin/loyalty`
- **Method**: `GET`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Accept: application/json`
- **Query Parameters**:
  - `page` (optional): Page number (default: 1)
  - `limit` (optional): Items per page (default: 20)
  - `search` (optional): Search by user name or email
  - `user_id` (optional): Filter by user ID
  - `type` (optional): Filter by transaction type ('earned', 'spent', 'expired', 'adjusted')
  - `start_date` (optional): Filter by start date (format: YYYY-MM-DD)
  - `end_date` (optional): Filter by end date (format: YYYY-MM-DD)
  - `sort` (optional): Sort field
  - `order` (optional): Sort order ('asc', 'desc')
- **Response (200 OK)**:
```json
{
  "success": true,
  "data": {
    "transactions": [
      {
        "id": "1",
        "user": {
          "id": "1",
          "name": "محمد أحمد",
          "email": "customer@example.com"
        },
        "amount": 50,
        "balance_after": 450,
        "type": "earned",
        "description": "طلب #ORD-2024-001",
        "reference_id": "order_1",
        "reference_type": "order",
        "expiry_date": "2025-01-20T00:00:00Z",
        "created_at": "2024-01-20T10:30:00Z",
        "created_by": {
          "id": "2",
          "name": "مدير النظام"
        }
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 20,
      "total": 1250,
      "total_pages": 63,
      "has_next": true,
      "has_prev": false
    },
    "summary": {
      "total_active_points": 45820,
      "total_earned": 58750,
      "total_spent": 12480,
      "total_expired": 450
    }
  }
}
```

### Award Points
- **URL**: `/api/admin/loyalty/award-points`
- **Method**: `POST`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Content-Type: application/json`
  - `Accept: application/json`
- **Request Body**:
```json
{
  "user_id": 1,
  "amount": 100,
  "description": "مكافأة عميل مميز",
  "expiry_days": 365,
  "notify_user": true
}
```
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "تم منح النقاط بنجاح",
  "data": {
    "transaction_id": "2",
    "user_id": "1",
    "user_name": "محمد أحمد",
    "amount": 100,
    "balance_after": 550,
    "expiry_date": "2025-01-22T00:00:00Z",
    "created_at": "2024-01-22T10:00:00Z"
  }
}
```

### Deduct Points
- **URL**: `/api/admin/loyalty/deduct-points`
- **Method**: `POST`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Content-Type: application/json`
  - `Accept: application/json`
- **Request Body**:
```json
{
  "user_id": 1,
  "amount": 50,
  "description": "تعديل إداري",
  "notify_user": true
}
```
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "تم خصم النقاط بنجاح",
  "data": {
    "transaction_id": "3",
    "user_id": "1",
    "user_name": "محمد أحمد",
    "amount": -50,
    "balance_after": 500,
    "created_at": "2024-01-22T11:00:00Z"
  }
}
```

### Get User Loyalty Summary
- **URL**: `/api/admin/loyalty/user/{userId}/summary`
- **Method**: `GET`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Accept: application/json`
- **Response (200 OK)**:
```json
{
  "success": true,
  "data": {
    "user": {
      "id": "1",
      "name": "محمد أحمد",
      "email": "customer@example.com",
      "user_category": {
        "id": "2",
        "name": "ذهبي"
      }
    },
    "points": {
      "current_balance": 500,
      "total_earned": 650,
      "total_spent": 150,
      "total_expired": 0,
      "points_to_expire": [
        {
          "amount": 350,
          "expiry_date": "2025-01-20T00:00:00Z"
        },
        {
          "amount": 150,
          "expiry_date": "2025-01-22T00:00:00Z"
        }
      ]
    },
    "transactions": {
      "recent": [
        {
          "id": "3",
          "amount": -50,
          "type": "adjusted",
          "description": "تعديل إداري",
          "created_at": "2024-01-22T11:00:00Z"
        },
        {
          "id": "2",
          "amount": 100,
          "type": "earned",
          "description": "مكافأة عميل مميز",
          "created_at": "2024-01-22T10:00:00Z"
        }
      ],
      "by_type": {
        "earned": 650,
        "spent": 150,
        "expired": 0,
        "adjusted": -50
      }
    },
    "usage_history": {
      "orders_with_points": 3,
      "average_points_per_order": 50,
      "total_discount_amount": 15.00
    }
  }
}
```

### Get Loyalty Statistics
- **URL**: `/api/admin/loyalty/statistics/overview`
- **Method**: `GET`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Accept: application/json`
- **Query Parameters**:
  - `period` (optional): Time period - 'month', 'quarter', 'year' (default: 'month')
- **Response (200 OK)**:
```json
{
  "success": true,
  "data": {
    "points": {
      "total_active": 45820,
      "total_earned": 58750,
      "total_spent": 12480,
      "total_expired": 450,
      "total_adjusted": 250
    },
    "users": {
      "with_points": 235,
      "without_points": 13,
      "percentage_with_points": 94.8,
      "average_balance": 195
    },
    "activity": {
      "points_earned_today": 850,
      "points_spent_today": 350,
      "transactions_today": 45,
      "points_expiring_next_30_days": 2580
    },
    "monetary": {
      "total_points_value": 4582.00,
      "discounts_redeemed": 1248.00
    },
    "charts": {
      "points_activity_by_day": {
        "labels": ["2024-01-15", "2024-01-16", "2024-01-17", "2024-01-18", "2024-01-19", "2024-01-20", "2024-01-21"],
        "earned": [450, 520, 480, 580, 490, 550, 600],
        "spent": [180, 220, 150, 280, 200, 190, 250]
      },
      "points_distribution_by_category": {
        "labels": ["بلاتينيوم", "ذهبي", "فضي", "عادي"],
        "values": [12500, 18750, 10250, 4320]
      }
    }
  }
}
```

### Clean Expired Points
- **URL**: `/api/admin/loyalty/clean-expired`
- **Method**: `POST`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Accept: application/json`
- **Request Body**:
```json
{
  "confirm": true,
  "notify_users": true
}
```
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "تم تنظيف النقاط منتهية الصلاحية بنجاح",
  "data": {
    "points_expired": 1250,
    "users_affected": 85,
    "processing_time": "2.5 seconds"
  }
}
```

## Error Responses

### User Not Found (404)
```json
{
  "success": false,
  "error": {
    "message": "المستخدم غير موجود",
    "timestamp": "2024-01-20T14:30:00Z"
  }
}
```

### Insufficient Balance (400)
```json
{
  "success": false,
  "error": {
    "message": "رصيد النقاط غير كاف",
    "details": {
      "available_balance": 30,
      "requested_amount": 50
    },
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
        "user_id": ["معرف المستخدم مطلوب"],
        "amount": ["كمية النقاط يجب أن تكون رقماً موجباً"]
      }
    },
    "timestamp": "2024-01-20T14:30:00Z"
  }
}
```
