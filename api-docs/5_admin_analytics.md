# API Documentation - Admin Analytics

Base URL: `https://suntop-eg.com`

## Admin Analytics Endpoints
All endpoints in this section require admin authentication.

### Dashboard Analytics
- **URL**: `/api/admin/analytics/dashboard`
- **Method**: `GET`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Accept: application/json`
- **Query Parameters**:
  - `period` (optional): Time period for analytics - 'today', 'week', 'month', 'year' (default: 'week')
- **Response (200 OK)**:
```json
{
  "success": true,
  "data": {
    "summary": {
      "total_sales": 12450.75,
      "sales_growth": 15.5,
      "total_orders": 532,
      "orders_growth": 8.3,
      "total_customers": 248,
      "customers_growth": 12.1,
      "avg_order_value": 23.4,
      "currency": "EGP"
    },
    "charts": {
      "sales_by_day": {
        "labels": ["الأحد", "الاثنين", "الثلاثاء", "الأربعاء", "الخميس", "الجمعة", "السبت"],
        "values": [2450.25, 1980.50, 3200.00, 2800.75, 1125.25, 3450.00, 2789.50]
      },
      "orders_by_status": {
        "labels": ["قيد الانتظار", "قيد التجهيز", "تم الشحن", "تم التسليم", "ملغي"],
        "values": [45, 32, 28, 423, 4]
      },
      "top_products": [
        {
          "id": "1",
          "name": "سن توب برتقال طازج",
          "sales_count": 345,
          "sales_amount": 862.50
        }
      ]
    }
  }
}
```

### Sales Analytics
- **URL**: `/api/admin/analytics/sales`
- **Method**: `GET`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Accept: application/json`
- **Query Parameters**:
  - `start_date` (optional): Start date for sales data (format: YYYY-MM-DD)
  - `end_date` (optional): End date for sales data (format: YYYY-MM-DD)
  - `group_by` (optional): Group sales by - 'day', 'week', 'month' (default: 'day')
- **Response (200 OK)**:
```json
{
  "success": true,
  "data": {
    "summary": {
      "total_sales": 45780.50,
      "average_daily_sales": 1526.02,
      "highest_sales_day": "2024-01-15",
      "highest_sales_amount": 3450.00,
      "lowest_sales_day": "2024-01-05",
      "lowest_sales_amount": 850.25,
      "currency": "EGP"
    },
    "trends": {
      "growth_rate": 12.5,
      "growth_trend": "positive",
      "forecast_next_month": 51250.75
    },
    "charts": {
      "sales_over_time": {
        "labels": ["2024-01-01", "2024-01-02", "2024-01-03"],
        "values": [1250.75, 1480.50, 1325.00]
      },
      "sales_by_product_category": {
        "labels": ["Citrus", "Berry", "Tropical", "Classic"],
        "values": [18450.25, 12580.50, 9850.75, 4899.00]
      },
      "sales_by_merchant": {
        "labels": ["Fresh Juice Corner", "Juice Palace", "Healthy Drinks"],
        "values": [25450.75, 12580.50, 7749.25]
      }
    },
    "top_selling_products": [
      {
        "id": "1",
        "name": "سن توب برتقال طازج",
        "quantity_sold": 2580,
        "revenue": 6450.00,
        "growth": 15.5
      }
    ]
  }
}
```

### Customer Analytics
- **URL**: `/api/admin/analytics/customers`
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
    "summary": {
      "total_customers": 1250,
      "new_customers": 125,
      "active_customers": 850,
      "inactive_customers": 400,
      "customer_growth": 10.5
    },
    "demographics": {
      "top_cities": [
        {
          "city": "القاهرة",
          "customer_count": 450,
          "percentage": 36.0
        },
        {
          "city": "الإسكندرية",
          "customer_count": 280,
          "percentage": 22.4
        }
      ],
      "customer_categories": [
        {
          "category": "بلاتينيوم",
          "customer_count": 125,
          "percentage": 10.0
        },
        {
          "category": "ذهبي",
          "customer_count": 325,
          "percentage": 26.0
        },
        {
          "category": "فضي",
          "customer_count": 450,
          "percentage": 36.0
        },
        {
          "category": "عادي",
          "customer_count": 350,
          "percentage": 28.0
        }
      ]
    },
    "activity": {
      "repeat_purchase_rate": 68.5,
      "average_order_frequency": 2.4,
      "customer_retention_rate": 85.2,
      "customer_churn_rate": 14.8
    },
    "charts": {
      "new_customers_over_time": {
        "labels": ["يناير", "فبراير", "مارس", "أبريل"],
        "values": [45, 58, 72, 125]
      },
      "customer_lifetime_value": {
        "labels": ["بلاتينيوم", "ذهبي", "فضي", "عادي"],
        "values": [3500, 1800, 750, 250]
      }
    }
  }
}
```

### Product Analytics
- **URL**: `/api/admin/analytics/products`
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
    "summary": {
      "total_products": 58,
      "active_products": 50,
      "out_of_stock": 8,
      "top_selling_category": "Citrus"
    },
    "performance": {
      "top_selling_products": [
        {
          "id": "1",
          "name": "سن توب برتقال طازج",
          "units_sold": 2580,
          "revenue": 6450.00,
          "percentage": 14.5
        }
      ],
      "low_performing_products": [
        {
          "id": "15",
          "name": "سن توب فراولة",
          "units_sold": 120,
          "revenue": 299.80,
          "percentage": 0.7
        }
      ]
    },
    "inventory": {
      "total_stock_value": 28750.50,
      "average_stock_level": 85.2,
      "restock_needed": [
        {
          "id": "3",
          "name": "سن توب مانجو",
          "current_stock": 5,
          "reorder_level": 10
        }
      ]
    },
    "charts": {
      "sales_by_category": {
        "labels": ["Citrus", "Berry", "Tropical", "Classic"],
        "values": [18450.25, 12580.50, 9850.75, 4899.00]
      },
      "stock_levels": {
        "labels": ["Citrus", "Berry", "Tropical", "Classic"],
        "values": [450, 280, 320, 150]
      }
    }
  }
}
```

### Financial Analytics
- **URL**: `/api/admin/analytics/financial`
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
    "summary": {
      "total_revenue": 45780.50,
      "gross_profit": 32850.75,
      "net_profit": 18450.25,
      "cost_of_goods": 12929.75,
      "operating_expenses": 14400.50,
      "currency": "EGP"
    },
    "metrics": {
      "gross_margin": 71.8,
      "net_margin": 40.3,
      "average_order_value": 85.2,
      "revenue_growth": 15.5
    },
    "payment_methods": [
      {
        "method": "cash_on_delivery",
        "amount": 28750.50,
        "percentage": 62.8,
        "transaction_count": 338
      },
      {
        "method": "credit_card",
        "amount": 12580.75,
        "percentage": 27.5,
        "transaction_count": 147
      },
      {
        "method": "wallet",
        "amount": 4449.25,
        "percentage": 9.7,
        "transaction_count": 47
      }
    ],
    "charts": {
      "revenue_over_time": {
        "labels": ["يناير", "فبراير", "مارس", "أبريل"],
        "values": [38750.25, 40120.50, 42580.75, 45780.50]
      },
      "profit_over_time": {
        "labels": ["يناير", "فبراير", "مارس", "أبريل"],
        "values": [15480.25, 16250.50, 17580.75, 18450.25]
      }
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

### Forbidden (403)
```json
{
  "success": false,
  "error": {
    "message": "Insufficient permissions",
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
