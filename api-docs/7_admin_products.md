# API Documentation - Admin Product Management

Base URL: `http://127.0.0.1:8000`

## Admin Product Management Endpoints
All endpoints in this section require admin authentication.

### List Products
- **URL**: `/api/admin/products`
- **Method**: `GET`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Accept: application/json`
- **Query Parameters**:
  - `page` (optional): Page number (default: 1)
  - `limit` (optional): Items per page (default: 20)
  - `search` (optional): Search by product name or description
  - `category` (optional): Filter by category
  - `availability` (optional): Filter by availability ('available', 'unavailable')
  - `sort` (optional): Sort field
  - `order` (optional): Sort order ('asc', 'desc')
  - `featured` (optional): Filter by featured status ('true', 'false')
- **Response (200 OK)**:
```json
{
  "success": true,
  "data": {
    "products": [
      {
        "id": "1",
        "name": "سن توب برتقال طازج",
        "description": "عصير برتقال طازج فاخر - زجاجة 500 مل",
        "image_url": "http://127.0.0.1:8000/storage/products/j1.jpg",
        "price": 2.50,
        "original_price": 3.00,
        "currency": "EGP",
        "category": "Citrus",
        "size": "500ml",
        "volume_category": "250ml",
        "is_available": true,
        "is_featured": true,
        "stock_quantity": 50,
        "sku": "JCE-ORA-500",
        "barcode": "1234567890123",
        "sales_count": 2580,
        "rating": 4.9,
        "created_at": "2023-11-15T08:30:00Z",
        "updated_at": "2024-01-20T12:00:00Z"
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 20,
      "total": 58,
      "total_pages": 3,
      "has_next": true,
      "has_prev": false
    },
    "filters": {
      "categories": ["1L", "250ml", "Citrus", "Tropical", "Berry"],
      "price_range": {
        "min": 2.25,
        "max": 3.49
      }
    }
  }
}
```

### Create Product
- **URL**: `/api/admin/products`
- **Method**: `POST`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Content-Type: multipart/form-data`
  - `Accept: application/json`
- **Request Body**:
```
name: "سن توب ليمون طازج"
description: "عصير ليمون طازج فاخر - زجاجة 500 مل"
price: 2.25
original_price: 2.75
category: "Citrus"
size: "500ml"
volume_category: "250ml"
is_available: true
is_featured: false
stock_quantity: 75
sku: "JCE-LEM-500"
barcode: "1234567890124"
ingredients: "ليمون طبيعي, ماء, سكر, فيتامين سي"
nutrition_facts: { "calories": 110, "sugar": "22g", "vitamin_c": "80%" }
storage_instructions: "يُحفظ في الثلاجة بعد الفتح"
expiry_info: "صالح لمدة 12 شهر"
image: [FILE]
gallery[0]: [FILE] (optional)
gallery[1]: [FILE] (optional)
tags[0]: "Fresh"
tags[1]: "Citrus"
```
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "تم إنشاء المنتج بنجاح",
  "data": {
    "id": "2",
    "name": "سن توب ليمون طازج",
    "description": "عصير ليمون طازج فاخر - زجاجة 500 مل",
    "image_url": "http://127.0.0.1:8000/storage/products/j2.jpg",
    "price": 2.25,
    "original_price": 2.75,
    "currency": "EGP",
    "category": "Citrus",
    "size": "500ml",
    "volume_category": "250ml",
    "is_available": true,
    "is_featured": false,
    "stock_quantity": 75,
    "sku": "JCE-LEM-500",
    "barcode": "1234567890124",
    "created_at": "2024-01-20T16:30:00Z"
  }
}
```

### Get Product Details
- **URL**: `/api/admin/products/{id}`
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
    "name": "سن توب برتقال طازج",
    "description": "عصير برتقال طازج فاخر - زجاجة 500 مل",
    "image_url": "http://127.0.0.1:8000/storage/products/j1.jpg",
    "gallery": [
      "http://127.0.0.1:8000/storage/products/j1_1.jpg",
      "http://127.0.0.1:8000/storage/products/j1_2.jpg"
    ],
    "price": 2.50,
    "original_price": 3.00,
    "currency": "EGP",
    "category": "Citrus",
    "size": "500ml",
    "volume_category": "250ml",
    "is_available": true,
    "is_featured": true,
    "stock_quantity": 50,
    "sku": "JCE-ORA-500",
    "barcode": "1234567890123",
    "sales_count": 2580,
    "rating": 4.9,
    "ingredients": ["برتقال طبيعي", "ماء", "فيتامين سي"],
    "nutrition_facts": {
      "calories": 120,
      "sugar": "25g",
      "vitamin_c": "100%",
      "sodium": "10mg"
    },
    "tags": ["Popular", "Fresh", "Vitamin C"],
    "storage_instructions": "يُحفظ في الثلاجة بعد الفتح",
    "expiry_info": "صالح لمدة 12 شهر",
    "created_at": "2023-11-15T08:30:00Z",
    "updated_at": "2024-01-20T12:00:00Z",
    "sales_data": {
      "total_units_sold": 2580,
      "total_revenue": 6450.00,
      "last_month_units": 245,
      "last_month_revenue": 612.50
    }
  }
}
```

### Update Product
- **URL**: `/api/admin/products/{id}`
- **Method**: `PUT`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Content-Type: application/json`
  - `Accept: application/json`
- **Request Body**:
```json
{
  "name": "سن توب برتقال طازج - عرض",
  "price": 2.25,
  "original_price": 3.00,
  "is_featured": true,
  "stock_quantity": 60,
  "nutrition_facts": {
    "calories": 120,
    "sugar": "25g",
    "vitamin_c": "120%",
    "sodium": "10mg"
  }
}
```
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "تم تحديث بيانات المنتج بنجاح",
  "data": {
    "id": "1",
    "name": "سن توب برتقال طازج - عرض",
    "price": 2.25,
    "original_price": 3.00,
    "is_featured": true,
    "stock_quantity": 60,
    "updated_at": "2024-01-20T17:00:00Z"
  }
}
```

### Delete Product
- **URL**: `/api/admin/products/{id}`
- **Method**: `DELETE`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Accept: application/json`
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "تم حذف المنتج بنجاح"
}
```

### Restore Deleted Product
- **URL**: `/api/admin/products/{id}/restore`
- **Method**: `POST`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Accept: application/json`
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "تم استعادة المنتج بنجاح",
  "data": {
    "id": "1",
    "name": "سن توب برتقال طازج",
    "restored_at": "2024-01-20T17:30:00Z"
  }
}
```

### Force Delete Product
- **URL**: `/api/admin/products/{id}/force-delete`
- **Method**: `DELETE`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Accept: application/json`
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "تم حذف المنتج نهائياً بنجاح"
}
```

### Toggle Product Availability
- **URL**: `/api/admin/products/{id}/toggle-availability`
- **Method**: `POST`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Accept: application/json`
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "تم تحديث حالة توفر المنتج بنجاح",
  "data": {
    "id": "1",
    "is_available": false
  }
}
```

### Toggle Featured Status
- **URL**: `/api/admin/products/{id}/toggle-featured`
- **Method**: `POST`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Accept: application/json`
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "تم تحديث حالة تمييز المنتج بنجاح",
  "data": {
    "id": "1",
    "is_featured": true
  }
}
```

### Update Stock
- **URL**: `/api/admin/products/{id}/update-stock`
- **Method**: `POST`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Content-Type: application/json`
  - `Accept: application/json`
- **Request Body**:
```json
{
  "stock_quantity": 75,
  "operation_type": "set"
}
```
OR
```json
{
  "stock_quantity": 15,
  "operation_type": "add"
}
```
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "تم تحديث المخزون بنجاح",
  "data": {
    "id": "1",
    "stock_quantity": 75,
    "updated_at": "2024-01-20T18:00:00Z"
  }
}
```

### Bulk Action
- **URL**: `/api/admin/products/bulk-action`
- **Method**: `POST`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Content-Type: application/json`
  - `Accept: application/json`
- **Request Body**:
```json
{
  "ids": [1, 2, 3],
  "action": "toggle_availability",
  "value": true
}
```
OR
```json
{
  "ids": [1, 2, 3],
  "action": "delete"
}
```
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "تم تنفيذ الإجراء الشامل بنجاح",
  "data": {
    "affected_products": 3
  }
}
```

### List Product Categories
- **URL**: `/api/admin/products/categories/list`
- **Method**: `GET`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Accept: application/json`
- **Response (200 OK)**:
```json
{
  "success": true,
  "data": {
    "categories": [
      {
        "name": "Citrus",
        "product_count": 12
      },
      {
        "name": "Berry",
        "product_count": 8
      },
      {
        "name": "Tropical",
        "product_count": 10
      },
      {
        "name": "Classic",
        "product_count": 6
      }
    ]
  }
}
```

### Product Analytics Overview
- **URL**: `/api/admin/products/analytics/overview`
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
    "total_products": 58,
    "active_products": 50,
    "out_of_stock": 8,
    "top_selling_products": [
      {
        "id": "1",
        "name": "سن توب برتقال طازج",
        "units_sold": 2580,
        "revenue": 6450.00
      }
    ],
    "sales_by_category": [
      {
        "category": "Citrus",
        "units_sold": 5890,
        "revenue": 14725.00,
        "percentage": 32.2
      }
    ],
    "inventory_value": 28750.50,
    "recent_stock_updates": [
      {
        "product_id": "1",
        "product_name": "سن توب برتقال طازج",
        "old_quantity": 40,
        "new_quantity": 75,
        "updated_at": "2024-01-19T14:00:00Z"
      }
    ]
  }
}
```

## Error Responses

### Product Not Found (404)
```json
{
  "success": false,
  "error": {
    "message": "المنتج غير موجود",
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
        "name": ["اسم المنتج مطلوب"],
        "price": ["السعر يجب أن يكون رقماً موجباً"],
        "stock_quantity": ["الكمية يجب أن تكون رقماً صحيحاً"]
      }
    },
    "timestamp": "2024-01-20T14:30:00Z"
  }
}
```
