# API Documentation - Loyalty Points (Customer)

Base URL: `http://127.0.0.1:8000`

## Loyalty Points Endpoints

### Get Loyalty Points Summary
- **URL**: `/api/loyalty/points`
- **Method**: `GET`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Accept: application/json`
- **Response (200 OK)**:
```json
{
  "success": true,
  "data": {
    "current_balance": 450,
    "lifetime_points": 650,
    "points_to_expire": {
      "next_month": 100,
      "next_three_months": 250
    }
  }
}
```

### Get Loyalty Transactions History
- **URL**: `/api/loyalty/transactions`
- **Method**: `GET`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Accept: application/json`
- **Query Parameters**:
  - `per_page` (optional): Number of transactions per page (default: 10)
  - `page` (optional): Page number (default: 1)
- **Response (200 OK)**:
```json
{
  "success": true,
  "data": {
    "transactions": [
      {
        "id": "1",
        "user_id": 1,
        "points": 50,
        "type": "earned",
        "description": "طلب #ORD-2024-001",
        "order_id": "1",
        "expires_at": "2025-01-20T00:00:00Z",
        "created_at": "2024-01-20T10:30:00Z",
        "updated_at": "2024-01-20T10:30:00Z"
      },
      {
        "id": "2",
        "user_id": 1,
        "points": -20,
        "type": "redeemed",
        "description": "خصم على الطلب",
        "order_id": "2",
        "expires_at": null,
        "created_at": "2024-01-22T14:30:00Z",
        "updated_at": "2024-01-22T14:30:00Z"
      }
    ],
    "summary": {
      "total_earned": 650,
      "total_redeemed": 200
    },
    "pagination": {
      "current_page": 1,
      "total_pages": 1,
      "per_page": 10,
      "total": 2
    }
  }
}
```

## User Authentication Endpoints with Loyalty Points

### Login (Now includes User Category and Loyalty Points)
- **URL**: `/api/auth/login`
- **Method**: `POST`
- **Headers**:
  - `Content-Type: application/json`
  - `Accept: application/json`
- **Request Body**:
```json
{
  "username": "testuser",
  "password": "password123"
}
```
- **Response (200 OK)**:
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
      "email": "test@example.com",
      "full_name": "Test User",
      "phone": "+20 109 999 9999",
      "role": "user",
      "is_active": true,
      "profile_image": null,
      "created_at": "2024-01-20T10:30:00Z",
      "last_login_at": "2024-01-20T14:30:00Z",
      "user_category": {
        "id": "2",
        "name": "ذهبي",
        "display_name": "عميل ذهبي",
        "discount_percentage": 10.00
      },
      "loyalty_points": {
        "current_balance": 450
      }
    }
  }
}
```

### Get User Profile (Now includes User Category and Loyalty Points)
- **URL**: `/api/auth/profile`
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
    "role": "user",
    "is_active": true,
    "profile_image": null,
    "created_at": "2024-01-20T10:30:00Z",
    "last_login_at": "2024-01-20T14:30:00Z",
    "user_category": {
      "id": "2",
      "name": "ذهبي",
      "display_name": "عميل ذهبي",
      "discount_percentage": 10.00
    },
    "loyalty_points": {
      "current_balance": 450
    }
  }
}
```

### Get Current User (With User Category)
- **URL**: `/api/user`
- **Method**: `GET`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Accept: application/json`
- **Response (200 OK)**:
```json
{
  "id": "1",
  "username": "testuser",
  "email": "test@example.com",
  "full_name": "Test User",
  "phone": "+20 109 999 9999",
  "role": "customer",
  "is_active": true,
  "total_cartons_purchased": 10,
  "total_packages_purchased": 5,
  "total_units_purchased": 20,
  "total_orders_count": 8,
  "category_updated_at": "2023-12-10T09:15:00Z",
  "total_purchase_amount": 1250.50,
  "created_at": "2023-11-15T08:30:00Z",
  "userCategory": {
    "id": "2",
    "name": "ذهبي",
    "display_name": "عميل ذهبي",
    "description": "عملاء مميزين",
    "min_cartons": 5,
    "max_cartons": 20,
    "carton_loyalty_points": 10,
    "bonus_points_per_carton": 1,
    "points_multiplier": 1.5,
    "discount_percentage": 10.00,
    "is_active": true
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

### Server Error (500)
```json
{
  "success": false,
  "message": "حدث خطأ أثناء جلب نقاط الولاء: [تفاصيل الخطأ]",
  "timestamp": "2024-01-20T14:30:00Z"
}
```
