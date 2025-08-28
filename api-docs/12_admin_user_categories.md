# API Documentation - Admin User Categories Management

Base URL: `http://127.0.0.1:8000`

## Admin User Categories Management Endpoints
All endpoints in this section require admin authentication.

### List User Categories
- **URL**: `/api/admin/user-categories`
- **Method**: `GET`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Accept: application/json`
- **Query Parameters**:
  - `sort` (optional): Sort field
  - `order` (optional): Sort order ('asc', 'desc')
- **Response (200 OK)**:
```json
{
  "success": true,
  "data": {
    "categories": [
      {
        "id": "1",
        "name": "بلاتينيوم",
        "display_name": "عميل بلاتينيوم",
        "description": "كبار العملاء",
        "min_purchase_units": 500,
        "carton_discount_percentage": 15,
        "package_discount_percentage": 10,
        "unit_discount_percentage": 5,
        "loyalty_points_multiplier": 2.0,
        "color_code": "#C0C0C0",
        "icon": "crown",
        "user_count": 25,
        "is_active": true,
        "created_at": "2023-10-01T08:30:00Z",
        "updated_at": "2024-01-15T12:00:00Z"
      },
      {
        "id": "2",
        "name": "ذهبي",
        "display_name": "عميل ذهبي",
        "description": "عملاء مميزين",
        "min_purchase_units": 250,
        "carton_discount_percentage": 10,
        "package_discount_percentage": 8,
        "unit_discount_percentage": 3,
        "loyalty_points_multiplier": 1.5,
        "color_code": "#FFD700",
        "icon": "star",
        "user_count": 65,
        "is_active": true,
        "created_at": "2023-10-01T08:30:00Z",
        "updated_at": "2024-01-15T12:00:00Z"
      }
    ]
  }
}
```

### Create User Category
- **URL**: `/api/admin/user-categories`
- **Method**: `POST`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Content-Type: application/json`
  - `Accept: application/json`
- **Request Body**:
```json
{
  "name": "برونزي",
  "display_name": "عميل برونزي",
  "description": "عملاء جدد",
  "min_purchase_units": 100,
  "carton_discount_percentage": 5,
  "package_discount_percentage": 3,
  "unit_discount_percentage": 1,
  "loyalty_points_multiplier": 1.0,
  "color_code": "#CD7F32",
  "icon": "medal",
  "is_active": true
}
```
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "تم إنشاء فئة المستخدم بنجاح",
  "data": {
    "id": "5",
    "name": "برونزي",
    "display_name": "عميل برونزي",
    "description": "عملاء جدد",
    "min_purchase_units": 100,
    "carton_discount_percentage": 5,
    "package_discount_percentage": 3,
    "unit_discount_percentage": 1,
    "loyalty_points_multiplier": 1.0,
    "color_code": "#CD7F32",
    "icon": "medal",
    "is_active": true,
    "created_at": "2024-01-22T10:00:00Z"
  }
}
```

### Get User Category Details
- **URL**: `/api/admin/user-categories/{id}`
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
    "name": "بلاتينيوم",
    "display_name": "عميل بلاتينيوم",
    "description": "كبار العملاء",
    "min_purchase_units": 500,
    "carton_discount_percentage": 15,
    "package_discount_percentage": 10,
    "unit_discount_percentage": 5,
    "loyalty_points_multiplier": 2.0,
    "color_code": "#C0C0C0",
    "icon": "crown",
    "user_count": 25,
    "total_orders": 1250,
    "total_purchase_amount": 125000.50,
    "is_active": true,
    "created_at": "2023-10-01T08:30:00Z",
    "updated_at": "2024-01-15T12:00:00Z"
  }
}
```

### Update User Category
- **URL**: `/api/admin/user-categories/{id}`
- **Method**: `PUT`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Content-Type: application/json`
  - `Accept: application/json`
- **Request Body**:
```json
{
  "display_name": "عميل بلاتينيوم مميز",
  "carton_discount_percentage": 18,
  "package_discount_percentage": 12,
  "unit_discount_percentage": 6,
  "loyalty_points_multiplier": 2.5
}
```
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "تم تحديث فئة المستخدم بنجاح",
  "data": {
    "id": "1",
    "display_name": "عميل بلاتينيوم مميز",
    "carton_discount_percentage": 18,
    "package_discount_percentage": 12,
    "unit_discount_percentage": 6,
    "loyalty_points_multiplier": 2.5,
    "updated_at": "2024-01-22T11:00:00Z"
  }
}
```

### Delete User Category
- **URL**: `/api/admin/user-categories/{id}`
- **Method**: `DELETE`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Accept: application/json`
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "تم حذف فئة المستخدم بنجاح"
}
```

### Toggle User Category Status
- **URL**: `/api/admin/user-categories/{id}/toggle-status`
- **Method**: `POST`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Accept: application/json`
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "تم تحديث حالة فئة المستخدم بنجاح",
  "data": {
    "id": "1",
    "is_active": false
  }
}
```

### Get Users in Category
- **URL**: `/api/admin/user-categories/{id}/users`
- **Method**: `GET`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Accept: application/json`
- **Query Parameters**:
  - `page` (optional): Page number (default: 1)
  - `limit` (optional): Items per page (default: 20)
  - `search` (optional): Search by user name or email
- **Response (200 OK)**:
```json
{
  "success": true,
  "data": {
    "users": [
      {
        "id": "1",
        "username": "testuser",
        "email": "test@example.com",
        "full_name": "Test User",
        "phone": "+20 109 999 9999",
        "total_cartons_purchased": 350,
        "total_packages_purchased": 150,
        "total_units_purchased": 200,
        "total_orders_count": 25,
        "total_purchase_amount": 4580.50,
        "created_at": "2023-11-15T08:30:00Z",
        "category_updated_at": "2023-12-10T09:15:00Z"
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 20,
      "total": 25,
      "total_pages": 2,
      "has_next": true,
      "has_prev": false
    }
  }
}
```

### Get User Category Statistics
- **URL**: `/api/admin/user-categories/statistics/overview`
- **Method**: `GET`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Accept: application/json`
- **Response (200 OK)**:
```json
{
  "success": true,
  "data": {
    "categories_count": 4,
    "user_distribution": [
      {
        "id": "1",
        "name": "بلاتينيوم",
        "user_count": 25,
        "percentage": 10.1
      },
      {
        "id": "2",
        "name": "ذهبي",
        "user_count": 65,
        "percentage": 26.2
      },
      {
        "id": "3",
        "name": "فضي",
        "user_count": 90,
        "percentage": 36.3
      },
      {
        "id": "4",
        "name": "عادي",
        "user_count": 68,
        "percentage": 27.4
      }
    ],
    "purchasing_stats": {
      "average_purchase_by_category": [
        {
          "id": "1",
          "name": "بلاتينيوم",
          "avg_purchase_amount": 5250.50,
          "avg_order_count": 32
        },
        {
          "id": "2",
          "name": "ذهبي",
          "avg_purchase_amount": 2750.25,
          "avg_order_count": 18
        }
      ],
      "total_discounts_by_category": [
        {
          "id": "1",
          "name": "بلاتينيوم",
          "total_discount_amount": 9850.25
        },
        {
          "id": "2",
          "name": "ذهبي",
          "total_discount_amount": 7250.50
        }
      ]
    },
    "recent_category_changes": {
      "promotions": 25,
      "demotions": 18,
      "unchanged": 205
    }
  }
}
```

### Recalculate User Categories
- **URL**: `/api/admin/user-categories/recalculate`
- **Method**: `POST`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Content-Type: application/json`
  - `Accept: application/json`
- **Request Body**:
```json
{
  "confirm": true,
  "user_ids": [1, 2, 3],  // Optional, if not provided will recalculate for all users
  "notify_users": true
}
```
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "تم إعادة حساب فئات المستخدمين بنجاح",
  "data": {
    "total_users_processed": 248,
    "users_promoted": 25,
    "users_demoted": 18,
    "users_unchanged": 205,
    "processing_time": "5.8 seconds",
    "changes": [
      {
        "user_id": 1,
        "user_name": "محمد أحمد",
        "old_category": "ذهبي",
        "new_category": "بلاتينيوم",
        "change_type": "promotion"
      }
    ]
  }
}
```

### Test Amount for Category
- **URL**: `/api/admin/user-categories/test/amount`
- **Method**: `GET`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Accept: application/json`
- **Query Parameters**:
  - `units` (required): Total purchase units to test
- **Response (200 OK)**:
```json
{
  "success": true,
  "data": {
    "units": 350,
    "matching_category": {
      "id": "2",
      "name": "ذهبي",
      "display_name": "عميل ذهبي",
      "carton_discount_percentage": 10,
      "package_discount_percentage": 8,
      "unit_discount_percentage": 3
    },
    "next_category": {
      "id": "1",
      "name": "بلاتينيوم",
      "display_name": "عميل بلاتينيوم",
      "min_purchase_units": 500,
      "units_needed": 150
    }
  }
}
```

## Error Responses

### Category Not Found (404)
```json
{
  "success": false,
  "error": {
    "message": "فئة المستخدم غير موجودة",
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
        "name": ["اسم الفئة مستخدم بالفعل"],
        "min_purchase_units": ["الحد الأدنى للوحدات يجب أن يكون رقماً موجباً"],
        "carton_discount_percentage": ["نسبة الخصم يجب أن تكون بين 0 و 100"]
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
    "message": "لا يمكن حذف فئة المستخدم لأنها مرتبطة بمستخدمين",
    "timestamp": "2024-01-20T14:30:00Z"
  }
}
```
