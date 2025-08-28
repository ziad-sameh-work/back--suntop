# API Documentation - Admin Merchant Management

Base URL: `http://127.0.0.1:8000`

## Admin Merchant Management Endpoints
All endpoints in this section require admin authentication.

### List Merchants
- **URL**: `/api/admin/merchants`
- **Method**: `GET`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Accept: application/json`
- **Query Parameters**:
  - `page` (optional): Page number (default: 1)
  - `limit` (optional): Items per page (default: 20)
  - `search` (optional): Search by merchant name or contact info
  - `status` (optional): Filter by status ('active', 'inactive')
  - `open_status` (optional): Filter by open status ('open', 'closed')
  - `sort` (optional): Sort field
  - `order` (optional): Sort order ('asc', 'desc')
- **Response (200 OK)**:
```json
{
  "success": true,
  "data": {
    "merchants": [
      {
        "id": "1",
        "name": "Fresh Juice Corner",
        "contact_name": "أحمد محمد",
        "phone": "+20 109 999 9999",
        "email": "fresh@example.com",
        "address": "شارع النيل، القاهرة",
        "logo_url": "http://127.0.0.1:8000/storage/merchants/logo1.jpg",
        "is_active": true,
        "is_open": true,
        "rating": 4.8,
        "total_orders": 287,
        "total_revenue": 6750.50,
        "commission_rate": 10,
        "created_at": "2023-10-15T08:30:00Z",
        "updated_at": "2024-01-15T12:00:00Z"
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 20,
      "total": 10,
      "total_pages": 1,
      "has_next": false,
      "has_prev": false
    }
  }
}
```

### Create Merchant
- **URL**: `/api/admin/merchants`
- **Method**: `POST`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Content-Type: multipart/form-data`
  - `Accept: application/json`
- **Request Body**:
```
name: "Juice Paradise"
contact_name: "محمد علي"
phone: "+20 109 888 8888"
email: "paradise@example.com"
address: "شارع الهرم، الجيزة"
description: "متجر متخصص في العصائر الطبيعية"
commission_rate: 12
is_active: true
is_open: true
opening_hours: "من 10 صباحاً حتى 10 مساءً"
logo: [FILE]
cover_image: [FILE] (optional)
```
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "تم إنشاء المتجر بنجاح",
  "data": {
    "id": "2",
    "name": "Juice Paradise",
    "contact_name": "محمد علي",
    "phone": "+20 109 888 8888",
    "email": "paradise@example.com",
    "address": "شارع الهرم، الجيزة",
    "logo_url": "http://127.0.0.1:8000/storage/merchants/logo2.jpg",
    "is_active": true,
    "is_open": true,
    "commission_rate": 12,
    "created_at": "2024-01-22T10:00:00Z"
  }
}
```

### Get Merchant Details
- **URL**: `/api/admin/merchants/{id}`
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
    "name": "Fresh Juice Corner",
    "contact_name": "أحمد محمد",
    "phone": "+20 109 999 9999",
    "email": "fresh@example.com",
    "address": "شارع النيل، القاهرة",
    "description": "متجر متخصص في العصائر الطبيعية الطازجة",
    "logo_url": "http://127.0.0.1:8000/storage/merchants/logo1.jpg",
    "cover_image_url": "http://127.0.0.1:8000/storage/merchants/cover1.jpg",
    "is_active": true,
    "is_open": true,
    "opening_hours": "من 9 صباحاً حتى 11 مساءً",
    "rating": 4.8,
    "commission_rate": 10,
    "bank_info": {
      "bank_name": "بنك مصر",
      "account_number": "1234567890",
      "account_name": "Fresh Juice Corner"
    },
    "stats": {
      "total_orders": 287,
      "total_revenue": 6750.50,
      "total_commission": 675.05,
      "orders_this_month": 45,
      "revenue_this_month": 950.25
    },
    "created_at": "2023-10-15T08:30:00Z",
    "updated_at": "2024-01-15T12:00:00Z",
    "top_products": [
      {
        "id": "1",
        "name": "سن توب برتقال طازج",
        "orders_count": 145,
        "revenue": 362.50
      }
    ]
  }
}
```

### Update Merchant
- **URL**: `/api/admin/merchants/{id}`
- **Method**: `PUT`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Content-Type: application/json`
  - `Accept: application/json`
- **Request Body**:
```json
{
  "name": "Fresh Juice Corner Premium",
  "contact_name": "أحمد محمد علي",
  "phone": "+20 109 999 9999",
  "email": "fresh-premium@example.com",
  "commission_rate": 8,
  "opening_hours": "من 8 صباحاً حتى 12 مساءً"
}
```
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "تم تحديث بيانات المتجر بنجاح",
  "data": {
    "id": "1",
    "name": "Fresh Juice Corner Premium",
    "contact_name": "أحمد محمد علي",
    "email": "fresh-premium@example.com",
    "commission_rate": 8,
    "opening_hours": "من 8 صباحاً حتى 12 مساءً",
    "updated_at": "2024-01-22T11:00:00Z"
  }
}
```

### Delete Merchant
- **URL**: `/api/admin/merchants/{id}`
- **Method**: `DELETE`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Accept: application/json`
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "تم حذف المتجر بنجاح"
}
```

### Toggle Merchant Status
- **URL**: `/api/admin/merchants/{id}/toggle-status`
- **Method**: `POST`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Accept: application/json`
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "تم تحديث حالة المتجر بنجاح",
  "data": {
    "id": "1",
    "is_active": false
  }
}
```

### Toggle Merchant Open Status
- **URL**: `/api/admin/merchants/{id}/toggle-open`
- **Method**: `POST`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Accept: application/json`
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "تم تحديث حالة فتح المتجر بنجاح",
  "data": {
    "id": "1",
    "is_open": false
  }
}
```

### Get Merchant Statistics
- **URL**: `/api/admin/merchants/statistics/overview`
- **Method**: `GET`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Accept: application/json`
- **Response (200 OK)**:
```json
{
  "success": true,
  "data": {
    "total_merchants": 10,
    "active_merchants": 8,
    "inactive_merchants": 2,
    "open_merchants": 7,
    "closed_merchants": 3,
    "total_revenue": 45780.50,
    "total_commission": 4578.05,
    "top_merchants": [
      {
        "id": "1",
        "name": "Fresh Juice Corner",
        "orders_count": 287,
        "revenue": 6750.50,
        "commission": 675.05
      }
    ],
    "recent_activity": {
      "new_merchants_this_month": 2,
      "orders_today": 25,
      "revenue_today": 585.50
    }
  }
}
```

## Error Responses

### Merchant Not Found (404)
```json
{
  "success": false,
  "error": {
    "message": "المتجر غير موجود",
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
        "name": ["اسم المتجر مطلوب"],
        "phone": ["رقم الهاتف مطلوب"],
        "commission_rate": ["نسبة العمولة يجب أن تكون بين 0 و 100"]
      }
    },
    "timestamp": "2024-01-20T14:30:00Z"
  }
}
```
