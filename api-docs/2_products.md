# API Documentation - Products

Base URL: `http://127.0.0.1:8000`

## Products Endpoints

### Get All Products
- **URL**: `/api/products`
- **Method**: `GET`
- **Headers**:
  - `Accept: application/json`
- **Query Parameters**:
  - `category` (optional): Filter by category name (e.g., "1L", "250ml")
  - `category_id` (optional): Filter by category ID
  - `page` (optional): Page number for pagination (default: 1)
  - `limit` (optional): Number of products per page (default: 20)
  - `search` (optional): Search term
  - `sort_by` (optional): Field to sort by (e.g., "rating", "price")
  - `sort_order` (optional): Sort direction ("asc" or "desc")
- **Response (200 OK)**:
```json
{
  "success": true,
  "data": {
    "products": [
      {
        "id": "1",
        "name": "سن توب برتقال طازج",
        "description": "عصير برتقال طازج فاخر - زجاجة 500 مل مع فيتامين سي",
        "image_url": "http://127.0.0.1:8000/storage/products/j1.jpg",
        "price": 2.50,
        "original_price": 3.00,
            "currency": "EGP",
    "category": "Citrus",
    "category_id": 2,
    "category_info": {
      "id": 2,
      "name": "250ml",
      "display_name": "250 مل"
    },
    "size": "500ml",
        "volume_category": "250ml",
        "is_available": true,
        "stock_quantity": 50,
        "rating": 4.9,
        "review_count": 89,
        "tags": ["Popular", "Fresh", "Vitamin C"],
        "back_color": "#FFF8E1",
        "nutrition_facts": {
          "calories": 120,
          "sugar": "25g",
          "vitamin_c": "100%"
        },
        "created_at": "2024-01-10T08:00:00Z",
        "updated_at": "2024-01-20T12:00:00Z"
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 20,
      "total": 8,
      "total_pages": 1,
      "has_next": false,
      "has_prev": false
    },
          "filters": {
      "categories": [
        {
          "id": 1,
          "name": "1L",
          "display_name": "1 لتر"
        },
        {
          "id": 2,
          "name": "250ml",
          "display_name": "250 مل"
        }
      ],
      "price_range": {
        "min": 2.25,
        "max": 3.49
      }
    }
  }
}
```

### Get Featured Products
- **URL**: `/api/products/featured`
- **Method**: `GET`
- **Headers**:
  - `Accept: application/json`
- **Query Parameters**:
  - `limit` (optional): Maximum number of featured products to return
- **Response (200 OK)**:
```json
{
  "success": true,
  "data": {
    "products": [
      {
        "id": "1",
        "name": "سن توب برتقال طازج",
        "description": "عصير برتقال طازج فاخر",
        "image_url": "http://127.0.0.1:8000/storage/products/j1.jpg",
        "price": 2.50,
        "original_price": 3.00,
            "currency": "EGP",
    "category": "Citrus",
    "category_id": 2,
    "category_info": {
      "id": 2,
      "name": "250ml",
      "display_name": "250 مل"
    },
    "size": "500ml",
        "is_available": true,
        "stock_quantity": 50,
        "rating": 4.9,
        "review_count": 89,
        "tags": ["Popular", "Fresh"],
        "back_color": "#FFF8E1",
        "created_at": "2024-01-10T08:00:00Z",
        "updated_at": "2024-01-20T12:00:00Z"
      }
    ]
  }
}
```

### Get Product Categories
- **URL**: `/api/products/categories`
- **Method**: `GET`
- **Headers**:
  - `Accept: application/json`
- **Response (200 OK)**:
```json
{
  "success": true,
  "data": {
    "categories": [
      {
        "id": 1,
        "name": "1L",
        "display_name": "1 لتر",
        "description": "منتجات سن توب بحجم 1 لتر",
        "icon": "fa-bottle-water"
      },
      {
        "id": 2,
        "name": "250ml",
        "display_name": "250 مل",
        "description": "منتجات سن توب بحجم 250 مل",
        "icon": "fa-glass"
      }
    ]
  }
}
```

### Search Products
- **URL**: `/api/products/search`
- **Method**: `GET`
- **Headers**:
  - `Accept: application/json`
- **Query Parameters**:
  - `q`: Search query
  - `limit` (optional): Number of products per page
  - `page` (optional): Page number
- **Response (200 OK)**:
```json
{
  "success": true,
  "data": {
    "products": [
      {
        "id": "1",
        "name": "سن توب برتقال طازج",
        "description": "عصير برتقال طازج فاخر",
        "image_url": "http://127.0.0.1:8000/storage/products/j1.jpg",
        "price": 2.50,
        "currency": "EGP",
        "category": "Citrus",
        "is_available": true,
        "rating": 4.9,
        "review_count": 89,
        "back_color": "#FFF8E1"
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 20,
      "total": 5,
      "total_pages": 1,
      "has_next": false,
      "has_prev": false
    },
    "query": "سن توب"
  }
}
```

### Get Product Details
- **URL**: `/api/products/{id}`
- **Method**: `GET`
- **Headers**:
  - `Accept: application/json`
- **Response (200 OK)**:
```json
{
  "success": true,
  "data": {
    "id": "1",
    "name": "سن توب برتقال طازج",
    "description": "عصير برتقال طازج فاخر - زجاجة 500 مل مع فيتامين سي",
    "image_url": "http://127.0.0.1:8000/storage/products/j1.jpg",
    "gallery": [
      "http://127.0.0.1:8000/storage/products/j1_1.jpg",
      "http://127.0.0.1:8000/storage/products/j1_2.jpg"
    ],
    "price": 2.50,
    "original_price": 3.00,
    "currency": "EGP",
    "category": "Citrus",
    "category_id": 2,
    "category_info": {
      "id": 2,
      "name": "250ml",
      "display_name": "250 مل"
    },
    "size": "500ml",
    "volume_category": "250ml",
    "is_available": true,
    "stock_quantity": 50,
    "rating": 4.9,
    "review_count": 89,
    "tags": ["Popular", "Fresh", "Vitamin C"],
    "back_color": "#FFF8E1",
    "ingredients": ["برتقال طبيعي", "ماء", "فيتامين سي"],
    "nutrition_facts": {
      "calories": 120,
      "sugar": "25g",
      "vitamin_c": "100%",
      "sodium": "10mg"
    },
    "storage_instructions": "يُحفظ في الثلاجة بعد الفتح",
    "expiry_info": "صالح لمدة 12 شهر",
    "barcode": "1234567890123",
    "reviews": [
      {
        "id": "1",
        "user_name": "أحمد محمد",
        "rating": 5,
        "comment": "طعم رائع ومنعش",
        "created_at": "2024-01-18T15:30:00Z"
      }
    ],
    "created_at": "2024-01-10T08:00:00Z",
    "updated_at": "2024-01-20T12:00:00Z"
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

### Server Error (500)
```json
{
  "success": false,
  "error": {
    "message": "خطأ في الخادم الداخلي",
    "timestamp": "2024-01-20T14:30:00Z"
  }
}
```
