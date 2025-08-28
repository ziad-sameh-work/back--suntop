# API Documentation - Admin Offers Management

Base URL: `http://127.0.0.1:8000`

## Admin Offers Management Endpoints
All endpoints in this section require admin authentication.

### List Offers
- **URL**: `/api/admin/offers`
- **Method**: `GET`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Accept: application/json`
- **Query Parameters**:
  - `page` (optional): Page number (default: 1)
  - `limit` (optional): Items per page (default: 20)
  - `search` (optional): Search by offer code or name
  - `status` (optional): Filter by status ('active', 'inactive')
  - `type` (optional): Filter by offer type ('percentage', 'fixed')
  - `sort` (optional): Sort field
  - `order` (optional): Sort order ('asc', 'desc')
- **Response (200 OK)**:
```json
{
  "success": true,
  "data": {
    "offers": [
      {
        "id": "1",
        "code": "SUMMER30",
        "name": "خصم الصيف",
        "description": "خصم 30% على جميع منتجات الصيف",
        "type": "percentage",
        "value": 30,
        "min_order_value": 50.00,
        "max_discount": 100.00,
        "start_date": "2024-01-01T00:00:00Z",
        "end_date": "2024-03-31T23:59:59Z",
        "usage_limit": 1000,
        "usage_count": 325,
        "is_active": true,
        "created_at": "2023-12-15T10:30:00Z",
        "updated_at": "2024-01-15T12:00:00Z"
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 20,
      "total": 8,
      "total_pages": 1,
      "has_next": false,
      "has_prev": false
    }
  }
}
```

### Create Offer
- **URL**: `/api/admin/offers`
- **Method**: `POST`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Content-Type: application/json`
  - `Accept: application/json`
- **Request Body**:
```json
{
  "code": "WELCOME20",
  "name": "خصم الترحيب",
  "description": "خصم 20% للعملاء الجدد",
  "type": "percentage",
  "value": 20,
  "min_order_value": 30.00,
  "max_discount": 50.00,
  "start_date": "2024-01-22T00:00:00Z",
  "end_date": "2024-12-31T23:59:59Z",
  "usage_limit": 500,
  "is_active": true,
  "applies_to": {
    "products": ["all"],
    "categories": ["all"],
    "user_categories": ["all"]
  }
}
```
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "تم إنشاء العرض بنجاح",
  "data": {
    "id": "2",
    "code": "WELCOME20",
    "name": "خصم الترحيب",
    "description": "خصم 20% للعملاء الجدد",
    "type": "percentage",
    "value": 20,
    "min_order_value": 30.00,
    "max_discount": 50.00,
    "start_date": "2024-01-22T00:00:00Z",
    "end_date": "2024-12-31T23:59:59Z",
    "usage_limit": 500,
    "is_active": true,
    "created_at": "2024-01-22T10:00:00Z"
  }
}
```

### Get Offer Details
- **URL**: `/api/admin/offers/{id}`
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
    "code": "SUMMER30",
    "name": "خصم الصيف",
    "description": "خصم 30% على جميع منتجات الصيف",
    "type": "percentage",
    "value": 30,
    "min_order_value": 50.00,
    "max_discount": 100.00,
    "start_date": "2024-01-01T00:00:00Z",
    "end_date": "2024-03-31T23:59:59Z",
    "usage_limit": 1000,
    "usage_count": 325,
    "is_active": true,
    "applies_to": {
      "products": ["all"],
      "categories": ["Citrus", "Tropical"],
      "user_categories": [1, 2, 3]
    },
    "stats": {
      "total_discount_amount": 9750.50,
      "average_discount_amount": 30.00,
      "usage_by_date": {
        "2024-01-15": 25,
        "2024-01-16": 32,
        "2024-01-17": 28
      }
    },
    "created_at": "2023-12-15T10:30:00Z",
    "updated_at": "2024-01-15T12:00:00Z"
  }
}
```

### Update Offer
- **URL**: `/api/admin/offers/{id}`
- **Method**: `PUT`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Content-Type: application/json`
  - `Accept: application/json`
- **Request Body**:
```json
{
  "name": "خصم الصيف المميز",
  "value": 35,
  "end_date": "2024-04-30T23:59:59Z",
  "max_discount": 120.00
}
```
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "تم تحديث العرض بنجاح",
  "data": {
    "id": "1",
    "name": "خصم الصيف المميز",
    "value": 35,
    "end_date": "2024-04-30T23:59:59Z",
    "max_discount": 120.00,
    "updated_at": "2024-01-22T11:00:00Z"
  }
}
```

### Delete Offer
- **URL**: `/api/admin/offers/{id}`
- **Method**: `DELETE`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Accept: application/json`
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "تم حذف العرض بنجاح"
}
```

### Toggle Offer Status
- **URL**: `/api/admin/offers/{id}/toggle-status`
- **Method**: `POST`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Accept: application/json`
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "تم تحديث حالة العرض بنجاح",
  "data": {
    "id": "1",
    "is_active": false
  }
}
```

### Get Offer Statistics
- **URL**: `/api/admin/offers/statistics/overview`
- **Method**: `GET`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Accept: application/json`
- **Response (200 OK)**:
```json
{
  "success": true,
  "data": {
    "total_offers": 8,
    "active_offers": 5,
    "inactive_offers": 3,
    "expired_offers": 2,
    "total_usage": 1250,
    "total_discount_amount": 28750.50,
    "top_offers": [
      {
        "id": "1",
        "code": "SUMMER30",
        "name": "خصم الصيف",
        "usage_count": 325,
        "discount_amount": 9750.50
      }
    ],
    "offers_by_type": {
      "percentage": 6,
      "fixed": 2
    },
    "recent_usage": {
      "today": 25,
      "this_week": 145,
      "this_month": 450
    }
  }
}
```

## Error Responses

### Offer Not Found (404)
```json
{
  "success": false,
  "error": {
    "message": "العرض غير موجود",
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
        "code": ["رمز العرض مستخدم بالفعل"],
        "value": ["قيمة العرض يجب أن تكون رقماً موجباً"],
        "end_date": ["تاريخ انتهاء العرض يجب أن يكون بعد تاريخ البدء"]
      }
    },
    "timestamp": "2024-01-20T14:30:00Z"
  }
}
```
