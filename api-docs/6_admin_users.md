# API Documentation - Admin User Management

Base URL: `http://127.0.0.1:8000`

## Admin User Management Endpoints
All endpoints in this section require admin authentication.

### List Users
- **URL**: `/api/admin/users`
- **Method**: `GET`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Accept: application/json`
- **Query Parameters**:
  - `page` (optional): Page number (default: 1)
  - `limit` (optional): Items per page (default: 20)
  - `search` (optional): Search by name, email, or phone
  - `role` (optional): Filter by user role ('customer', 'admin', 'merchant')
  - `status` (optional): Filter by status ('active', 'inactive')
  - `sort` (optional): Sort field
  - `order` (optional): Sort order ('asc', 'desc')
  - `user_category` (optional): Filter by user category ID
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
        "role": "customer",
        "is_active": true,
        "profile_image": "http://127.0.0.1:8000/storage/users/1.jpg",
        "user_category": {
          "id": "2",
          "name": "ذهبي",
          "display_name": "عميل ذهبي"
        },
        "total_orders_count": 25,
        "total_purchase_amount": 4580.50,
        "created_at": "2023-11-15T08:30:00Z",
        "last_login_at": "2024-01-20T14:30:00Z"
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 20,
      "total": 248,
      "total_pages": 13,
      "has_next": true,
      "has_prev": false
    },
    "filters": {
      "roles": ["customer", "admin", "merchant"],
      "statuses": ["active", "inactive"],
      "user_categories": [
        {
          "id": "1",
          "name": "بلاتينيوم"
        },
        {
          "id": "2",
          "name": "ذهبي"
        },
        {
          "id": "3",
          "name": "فضي"
        },
        {
          "id": "4",
          "name": "عادي"
        }
      ]
    }
  }
}
```

### Create User
- **URL**: `/api/admin/users`
- **Method**: `POST`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Content-Type: multipart/form-data`
  - `Accept: application/json`
- **Request Body**:
```
username: "newuser"
email: "newuser@example.com"
password: "password123"
password_confirmation: "password123"
full_name: "New User"
phone: "+20 109 888 8888"
role: "customer"
is_active: true
user_category_id: 3
profile_image: [FILE] (optional)
```
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "تم إنشاء المستخدم بنجاح",
  "data": {
    "id": "2",
    "username": "newuser",
    "email": "newuser@example.com",
    "full_name": "New User",
    "phone": "+20 109 888 8888",
    "role": "customer",
    "is_active": true,
    "profile_image": null,
    "user_category": {
      "id": "3",
      "name": "فضي",
      "display_name": "عميل فضي"
    },
    "created_at": "2024-01-20T15:30:00Z"
  }
}
```

### Get User Details
- **URL**: `/api/admin/users/{id}`
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
    "username": "testuser",
    "email": "test@example.com",
    "full_name": "Test User",
    "phone": "+20 109 999 9999",
    "role": "customer",
    "is_active": true,
    "profile_image": "http://127.0.0.1:8000/storage/users/1.jpg",
    "user_category": {
      "id": "2",
      "name": "ذهبي",
      "display_name": "عميل ذهبي"
    },
    "total_orders_count": 25,
    "total_purchase_amount": 4580.50,
    "total_cartons_purchased": 120,
    "total_packages_purchased": 85,
    "total_units_purchased": 350,
    "created_at": "2023-11-15T08:30:00Z",
    "last_login_at": "2024-01-20T14:30:00Z",
    "orders": {
      "recent": [
        {
          "id": "150",
          "order_number": "ORD-2024-150",
          "total_amount": 185.50,
          "status": "delivered",
          "created_at": "2024-01-18T09:30:00Z"
        }
      ],
      "count_by_status": {
        "pending": 2,
        "processing": 1,
        "shipped": 0,
        "delivered": 22,
        "cancelled": 0
      }
    },
    "loyalty_points": {
      "current_balance": 450,
      "total_earned": 650,
      "total_spent": 200,
      "points_history": [
        {
          "amount": 50,
          "type": "earned",
          "description": "طلب #ORD-2024-150",
          "created_at": "2024-01-18T09:30:00Z"
        }
      ]
    }
  }
}
```

### Update User
- **URL**: `/api/admin/users/{id}`
- **Method**: `PUT`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Content-Type: application/json`
  - `Accept: application/json`
- **Request Body**:
```json
{
  "email": "updated@example.com",
  "full_name": "Updated User",
  "phone": "+20 109 777 7777",
  "user_category_id": 2,
  "is_active": true
}
```
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "تم تحديث بيانات المستخدم بنجاح",
  "data": {
    "id": "1",
    "username": "testuser",
    "email": "updated@example.com",
    "full_name": "Updated User",
    "phone": "+20 109 777 7777",
    "role": "customer",
    "is_active": true,
    "user_category": {
      "id": "2",
      "name": "ذهبي",
      "display_name": "عميل ذهبي"
    },
    "updated_at": "2024-01-20T16:00:00Z"
  }
}
```

### Delete User
- **URL**: `/api/admin/users/{id}`
- **Method**: `DELETE`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Accept: application/json`
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "تم حذف المستخدم بنجاح"
}
```

### Toggle User Status
- **URL**: `/api/admin/users/{id}/toggle-status`
- **Method**: `POST`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Accept: application/json`
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "تم تحديث حالة المستخدم بنجاح",
  "data": {
    "id": "1",
    "is_active": false
  }
}
```

### Reset User Password
- **URL**: `/api/admin/users/{id}/reset-password`
- **Method**: `POST`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Content-Type: application/json`
  - `Accept: application/json`
- **Request Body**:
```json
{
  "new_password": "newpassword123",
  "new_password_confirmation": "newpassword123"
}
```
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "تم إعادة تعيين كلمة المرور بنجاح"
}
```

### Get User Statistics
- **URL**: `/api/admin/users/statistics/overview`
- **Method**: `GET`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Accept: application/json`
- **Response (200 OK)**:
```json
{
  "success": true,
  "data": {
    "total_users": 248,
    "active_users": 235,
    "inactive_users": 13,
    "users_by_role": {
      "customer": 230,
      "admin": 8,
      "merchant": 10
    },
    "users_by_category": [
      {
        "id": "1",
        "name": "بلاتينيوم",
        "count": 25
      },
      {
        "id": "2",
        "name": "ذهبي",
        "count": 65
      },
      {
        "id": "3",
        "name": "فضي",
        "count": 90
      },
      {
        "id": "4",
        "name": "عادي",
        "count": 50
      }
    ],
    "recent_registrations": {
      "daily": 5,
      "weekly": 18,
      "monthly": 45
    }
  }
}
```

## Error Responses

### Unauthorized (401)
```json
{
  "success": false,
  "error": {
    "message": "Unauthorized access",
    "timestamp": "2024-01-20T14:30:00Z"
  }
}
```

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

### Validation Error (422)
```json
{
  "success": false,
  "error": {
    "message": "خطأ في التحقق من صحة البيانات",
    "details": {
      "validation_errors": {
        "email": ["البريد الإلكتروني مستخدم بالفعل"],
        "password": ["كلمة المرور يجب أن تحتوي على 8 أحرف على الأقل"]
      }
    },
    "timestamp": "2024-01-20T14:30:00Z"
  }
}
```
