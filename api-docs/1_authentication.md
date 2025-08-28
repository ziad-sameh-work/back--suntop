# API Documentation - Authentication

Base URL: `http://127.0.0.1:8000`

## Authentication Endpoints

### Login
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

### Refresh Token
- **URL**: `/api/auth/refresh-token`
- **Method**: `POST`
- **Headers**:
  - `Content-Type: application/json`
  - `Accept: application/json`
- **Request Body**:
```json
{
  "refresh_token": "2|eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
}
```
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "تم تحديث الرمز المميز بنجاح",
  "data": {
    "access_token": "3|eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
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
      "created_at": "2024-01-20T10:30:00Z"
    }
  }
}
```

### Logout (Protected)
- **URL**: `/api/auth/logout`
- **Method**: `POST`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Accept: application/json`
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "تم تسجيل الخروج بنجاح"
}
```

### Reset Password (Protected)
- **URL**: `/api/auth/reset-password`
- **Method**: `POST`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Content-Type: application/json`
  - `Accept: application/json`
- **Request Body**:
```json
{
  "old_password": "password123",
  "new_password": "newpassword123",
  "new_password_confirmation": "newpassword123"
}
```
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "تم تغيير كلمة المرور بنجاح",
  "data": {
    "password_changed_at": "2024-01-20T14:30:00Z"
  }
}
```

### Get User Profile (Protected)
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

## Error Responses

### Unauthorized (401)
```json
{
  "success": false,
  "error": {
    "message": "اسم المستخدم أو كلمة المرور غير صحيحة",
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
        "new_password": ["كلمة المرور الجديدة مطلوبة"],
        "old_password": ["كلمة المرور القديمة غير صحيحة"]
      }
    },
    "timestamp": "2024-01-20T14:30:00Z"
  }
}
```
