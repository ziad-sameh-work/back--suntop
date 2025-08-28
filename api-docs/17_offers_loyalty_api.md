# 🎁 Offers & Loyalty System API Documentation

## Overview

The Offers & Loyalty System API provides comprehensive management for promotional offers, loyalty points, rewards, and tier-based benefits for the SunTop platform. This system supports multiple offer types (discounts, BOGO, freebies, cashback) and a sophisticated loyalty program with tier-based benefits.

## Base URL

```
http://127.0.0.1:8000/api
```

## Authentication

Most endpoints require authentication via Bearer token:

```
Authorization: Bearer {access_token}
```

---

## 🎯 Offers API Endpoints

### 1. Get All Offers

Retrieve paginated list of active offers with advanced filtering.

**Endpoint:** `GET /offers`

**Headers:**
```json
{
  "Accept": "application/json"
}
```

**Query Parameters:**
- `category` (string, optional): Filter by offer category (`عصائر`, `توصيل`, `نقاط الولاء`, `موسمية`)
- `type` (string, optional): Filter by offer type (`discount`, `bogo`, `freebie`, `cashback`)
- `active_only` (boolean, optional): Show only active offers (default: true)
- `sort_by` (string, optional): Sort field (default: `created_at`)
- `sort_order` (string, optional): Sort order (`asc`, `desc`, default: `desc`)
- `per_page` (integer, optional): Items per page (default: 20, max: 100)
- `page` (integer, optional): Page number (default: 1)

**Response:**
```json
{
  "success": true,
  "data": {
    "offers": [
      {
        "id": "1",
        "title": "عرض الصباح الطازج",
        "description": "خصم 25% على جميع عصائر سن توب الحمضية حتى الساعة 12 ظهراً",
        "code": "MORNING25",
        "type": "discount",
        "type_name": "خصم",
        "discount_percentage": 25,
        "discount_amount": null,
        "minimum_amount": 30,
        "maximum_discount": 50,
        "image_url": "http://127.0.0.1:8000/storage/offers/morning-fresh.jpg",
        "valid_from": "2024-01-20T00:00:00Z",
        "valid_until": "2024-02-19T23:59:59Z",
        "usage_limit": 500,
        "used_count": 87,
        "remaining_uses": 413,
        "is_active": true,
        "is_valid": true,
        "applicable_categories": ["Citrus", "حمضيات"],
        "applicable_products": null,
        "first_order_only": false,
        "created_at": "2024-01-20T08:00:00Z",
        "expires_in_days": 30
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 20,
      "total": 15,
      "last_page": 1,
      "has_next": false,
      "has_prev": false
    },
    "filters": {
      "categories": ["عصائر", "توصيل", "نقاط الولاء", "موسمية"],
      "types": {
        "discount": "خصم",
        "bogo": "اشتري واحصل على مجاني",
        "freebie": "منتج مجاني",
        "cashback": "استرداد نقدي"
      }
    }
  }
}
```

### 2. Get Featured Offers

Retrieve the most attractive current offers.

**Endpoint:** `GET /offers/featured`

**Query Parameters:**
- `limit` (integer, optional): Number of offers to return (default: 10)

**Response:**
```json
{
  "success": true,
  "data": {
    "offers": [
      {
        "id": "6",
        "title": "عرض العملاء الجدد",
        "description": "خصم 40% على طلبك الأول من سن توب",
        "code": "WELCOME40",
        "type": "discount",
        "discount_percentage": 40,
        "maximum_discount": 100,
        "first_order_only": true,
        "expires_in_days": 45
      }
    ]
  }
}
```

### 3. Get Offer Categories

Retrieve available offer categories.

**Endpoint:** `GET /offers/categories`

**Response:**
```json
{
  "success": true,
  "data": {
    "categories": [
      "عصائر",
      "توصيل",
      "نقاط الولاء",
      "موسمية"
    ]
  }
}
```

### 4. Get Offer Types

Retrieve available offer types with Arabic names.

**Endpoint:** `GET /offers/types`

**Response:**
```json
{
  "success": true,
  "data": {
    "types": [
      {
        "key": "discount",
        "name": "خصم"
      },
      {
        "key": "bogo",
        "name": "اشتري واحصل على مجاني"
      },
      {
        "key": "freebie",
        "name": "منتج مجاني"
      },
      {
        "key": "cashback",
        "name": "استرداد نقدي"
      }
    ]
  }
}
```

### 5. Get Offer Details

Retrieve detailed information about a specific offer.

**Endpoint:** `GET /offers/{id}`

**Response:**
```json
{
  "success": true,
  "data": {
    "offer": {
      "id": "1",
      "title": "عرض الصباح الطازج",
      "description": "خصم 25% على جميع عصائر سن توب الحمضية حتى الساعة 12 ظهراً",
      "code": "MORNING25",
      "type": "discount",
      "type_name": "خصم",
      "discount_percentage": 25,
      "minimum_amount": 30,
      "maximum_discount": 50,
      "valid_from": "2024-01-20T00:00:00Z",
      "valid_until": "2024-02-19T23:59:59Z",
      "usage_limit": 500,
      "used_count": 87,
      "remaining_uses": 413,
      "is_active": true,
      "is_valid": true,
      "applicable_categories": ["Citrus", "حمضيات"],
      "first_order_only": false,
      "created_at": "2024-01-20T08:00:00Z",
      "updated_at": "2024-01-20T08:00:00Z",
      "expires_in_days": 30,
      "expires_in_hours": 720,
      "terms_conditions": "لا يمكن دمجه مع عروض أخرى",
      "usage_statistics": {
        "usage_rate": 17.4,
        "popularity_score": 87
      }
    }
  }
}
```

### 6. Validate Offer (Protected)

Check if an offer is valid for a specific order amount and items.

**Endpoint:** `POST /offers/{id}/validate`

**Headers:**
```json
{
  "Authorization": "Bearer {access_token}",
  "Content-Type": "application/json",
  "Accept": "application/json"
}
```

**Request Body:**
```json
{
  "order_amount": 75.50,
  "items": [
    {
      "product_id": "1",
      "quantity": 2,
      "category": "Citrus"
    }
  ]
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "valid": true,
    "message": "العرض صالح للاستخدام",
    "discount_amount": 18.88,
    "offer": {
      "id": "1",
      "title": "عرض الصباح الطازج",
      "type": "discount",
      "description": "خصم 25% على جميع عصائر سن توب الحمضية"
    }
  }
}
```

### 7. Redeem Offer (Protected)

Activate an offer for use in an order.

**Endpoint:** `POST /offers/{id}/redeem`

**Headers:**
```json
{
  "Authorization": "Bearer {access_token}",
  "Content-Type": "application/json",
  "Accept": "application/json"
}
```

**Request Body:**
```json
{
  "order_amount": 75.50,
  "order_id": "123",
  "items": [
    {
      "product_id": "1",
      "quantity": 2
    }
  ]
}
```

**Response:**
```json
{
  "success": true,
  "message": "تم تفعيل العرض بنجاح",
  "data": {
    "redemption": {
      "id": "456",
      "redemption_code": "OFF-ABC12345",
      "discount_amount": 18.88,
      "expires_at": "2024-01-27T12:00:00Z",
      "status": "pending"
    }
  }
}
```

### 8. Get User Offer Redemptions (Protected)

Retrieve user's offer redemption history.

**Endpoint:** `GET /offers/user/redemptions`

**Headers:**
```json
{
  "Authorization": "Bearer {access_token}",
  "Accept": "application/json"
}
```

**Query Parameters:**
- `status` (string, optional): Filter by status (`pending`, `used`, `expired`, `cancelled`)
- `per_page` (integer, optional): Items per page (default: 20)

**Response:**
```json
{
  "success": true,
  "data": {
    "redemptions": [
      {
        "id": "456",
        "offer": {
          "id": "1",
          "title": "عرض الصباح الطازج",
          "type": "discount"
        },
        "redemption_code": "OFF-ABC12345",
        "discount_amount": 18.88,
        "status": "used",
        "status_name": "مستخدم",
        "expires_at": "2024-01-27T12:00:00Z",
        "used_at": "2024-01-21T10:30:00Z",
        "created_at": "2024-01-20T14:00:00Z"
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 20,
      "total": 5,
      "last_page": 1
    }
  }
}
```

### 9. Get Offer Performance

Retrieve analytics and performance data for a specific offer.

**Endpoint:** `GET /offers/{id}/performance`

**Response:**
```json
{
  "success": true,
  "data": {
    "offer": {
      "id": "1",
      "title": "عرض الصباح الطازج",
      "type": "discount"
    },
    "analytics": {
      "total_redemptions": 120,
      "used_redemptions": 87,
      "pending_redemptions": 15,
      "usage_count": 87,
      "usage_limit": 500,
      "usage_rate_percentage": 17.4,
      "conversion_rate_percentage": 72.5,
      "total_discount_given": 1642.56,
      "average_discount_per_use": 18.88
    }
  }
}
```

---

## 🏆 Loyalty Points API Endpoints

### 1. Get User Loyalty Points Summary (Protected)

Retrieve comprehensive loyalty points information including tier status.

**Endpoint:** `GET /loyalty/points`

**Headers:**
```json
{
  "Authorization": "Bearer {access_token}",
  "Accept": "application/json"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "current_points": 850,
    "lifetime_points": 2340,
    "current_tier": {
      "id": "2",
      "name": "silver",
      "display_name": "الفضي",
      "points_required": 500,
      "color": "#C0C0C0",
      "icon_url": "http://127.0.0.1:8000/storage/tiers/silver.png",
      "benefits": [
        "خصم 5% على جميع المنتجات",
        "نقاط إضافية 20% مع كل شراء",
        "عروض حصرية للأعضاء الفضيين"
      ],
      "discount_percentage": 5,
      "bonus_multiplier": 1.2
    },
    "next_tier": {
      "id": "3",
      "name": "gold",
      "display_name": "الذهبي",
      "points_required": 1000,
      "points_needed": 150,
      "color": "#FFD700",
      "icon_url": "http://127.0.0.1:8000/storage/tiers/gold.png"
    },
    "progress_percentage": 85.0
  }
}
```

### 2. Get Loyalty Transactions History (Protected)

Retrieve paginated history of loyalty point transactions.

**Endpoint:** `GET /loyalty/transactions`

**Headers:**
```json
{
  "Authorization": "Bearer {access_token}",
  "Accept": "application/json"
}
```

**Query Parameters:**
- `type` (string, optional): Filter by transaction type (`earned`, `redeemed`, `bonus`, `admin_award`)
- `date_from` (date, optional): Filter from date (YYYY-MM-DD)
- `date_to` (date, optional): Filter to date (YYYY-MM-DD)
- `per_page` (integer, optional): Items per page (default: 20)

**Response:**
```json
{
  "success": true,
  "data": {
    "transactions": [
      {
        "id": "789",
        "points": 25,
        "formatted_points": "+25",
        "type": "earned",
        "type_name": "مكتسبة",
        "description": "شراء - 2 كرتون",
        "order": {
          "id": "123",
          "order_number": "ORD-2024-001",
          "total_amount": 125.50
        },
        "expires_at": "2025-01-20T10:30:00Z",
        "is_expired": false,
        "metadata": {
          "cartons_count": 2,
          "transaction_type": "purchase"
        },
        "created_at": "2024-01-20T10:30:00Z",
        "days_until_expiry": 365
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 20,
      "total": 45,
      "last_page": 3,
      "has_next": true,
      "has_prev": false
    }
  }
}
```

### 3. Get User Analytics (Protected)

Retrieve detailed analytics about user's loyalty activity.

**Endpoint:** `GET /loyalty/analytics`

**Headers:**
```json
{
  "Authorization": "Bearer {access_token}",
  "Accept": "application/json"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "total_earned": 2340,
    "total_redeemed": 1490,
    "current_balance": 850,
    "monthly_earned": 120,
    "average_per_transaction": 18.5,
    "total_transactions": 126,
    "points_by_type": {
      "earned": 2340,
      "redeemed": -1490,
      "bonus": 180,
      "admin_award": 50
    },
    "redemption_rate": 63.67
  }
}
```

### 4. Get Earning Opportunities

Retrieve information about ways to earn loyalty points.

**Endpoint:** `GET /loyalty/earning-opportunities`

**Response:**
```json
{
  "success": true,
  "data": {
    "opportunities": [
      {
        "type": "purchase",
        "title": "اكسب نقاط مع كل عملية شراء",
        "description": "احصل على نقاط ولاء مع كل منتج تشتريه",
        "icon": "🛒",
        "points_info": "نقاط متغيرة حسب نوع المنتج"
      },
      {
        "type": "carton_bonus",
        "title": "مكافأة الكراتين",
        "description": "نقاط إضافية عند شراء 5 كراتين أو أكثر",
        "icon": "📦",
        "points_info": "5 نقاط إضافية لكل 5 كراتين"
      },
      {
        "type": "referral",
        "title": "ادع صديق",
        "description": "احصل على نقاط عند دعوة أصدقائك",
        "icon": "👥",
        "points_info": "50 نقطة لكل صديق جديد"
      }
    ]
  }
}
```

---

## 🎁 Rewards API Endpoints

### 1. Get Available Rewards

Retrieve paginated list of rewards that can be redeemed with loyalty points.

**Endpoint:** `GET /loyalty/rewards`

**Headers:**
```json
{
  "Authorization": "Bearer {access_token}",
  "Accept": "application/json"
}
```

**Query Parameters:**
- `category` (string, optional): Filter by reward category
- `type` (string, optional): Filter by reward type (`discount`, `free_product`, `cashback`, `bonus_points`)
- `min_points` (integer, optional): Minimum points cost
- `max_points` (integer, optional): Maximum points cost
- `sort_by` (string, optional): Sort field (default: `points_cost`)
- `sort_order` (string, optional): Sort order (default: `asc`)
- `per_page` (integer, optional): Items per page (default: 20)

**Response:**
```json
{
  "success": true,
  "data": {
    "rewards": [
      {
        "id": "1",
        "title": "خصم 10% على الطلب القادم",
        "description": "احصل على خصم 10% على طلبك القادم من عصائر سن توب",
        "type": "discount",
        "type_name": "خصم",
        "points_cost": 100,
        "formatted_points_cost": "100 نقطة",
        "discount_percentage": 10,
        "discount_amount": null,
        "category": "خصومات",
        "expiry_days": 30,
        "usage_limit": null,
        "used_count": 45,
        "remaining_uses": null,
        "is_active": true,
        "is_available": true,
        "terms_conditions": "صالح لمرة واحدة فقط، لا يمكن دمجه مع عروض أخرى",
        "created_at": "2024-01-20T08:00:00Z",
        "can_afford": true,
        "popularity_score": 45,
        "value_score": 10.0
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 20,
      "total": 10,
      "last_page": 1,
      "has_next": false,
      "has_prev": false
    },
    "filters": {
      "categories": ["خصومات", "منتجات مجانية", "استرداد نقدي", "نقاط إضافية"],
      "types": {
        "discount": "خصم",
        "free_product": "منتج مجاني",
        "cashback": "استرداد نقدي",
        "bonus_points": "نقاط إضافية"
      },
      "points_range": {
        "min": 100,
        "max": 800
      }
    }
  }
}
```

### 2. Redeem Reward (Protected)

Exchange loyalty points for a specific reward.

**Endpoint:** `POST /loyalty/rewards/{id}/redeem`

**Headers:**
```json
{
  "Authorization": "Bearer {access_token}",
  "Content-Type": "application/json",
  "Accept": "application/json"
}
```

**Request Body:**
```json
{
  "order_id": "123"
}
```

**Response:**
```json
{
  "success": true,
  "message": "تم استبدال المكافأة بنجاح",
  "data": {
    "redemption": {
      "id": "789",
      "redemption_code": "RWD-DEF67890",
      "points_deducted": 100,
      "discount_amount": null,
      "expires_at": "2024-02-19T14:30:00Z",
      "status": "pending"
    }
  }
}
```

### 3. Get User Reward Redemptions (Protected)

Retrieve user's reward redemption history.

**Endpoint:** `GET /loyalty/rewards/redemptions`

**Headers:**
```json
{
  "Authorization": "Bearer {access_token}",
  "Accept": "application/json"
}
```

**Query Parameters:**
- `status` (string, optional): Filter by status (`pending`, `used`, `expired`, `cancelled`)
- `per_page` (integer, optional): Items per page (default: 20)

**Response:**
```json
{
  "success": true,
  "data": {
    "redemptions": [
      {
        "id": "789",
        "reward": {
          "id": "1",
          "title": "خصم 10% على الطلب القادم",
          "type": "discount"
        },
        "redemption_code": "RWD-DEF67890",
        "points_deducted": 100,
        "discount_amount": null,
        "status": "used",
        "status_name": "مستخدم",
        "expires_at": "2024-02-19T14:30:00Z",
        "used_at": "2024-01-22T10:15:00Z",
        "created_at": "2024-01-21T14:30:00Z"
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 20,
      "total": 8,
      "last_page": 1
    }
  }
}
```

---

## 🏅 Reward Tiers API Endpoints

### 1. Get Reward Tiers

Retrieve information about loyalty tier system and user's current status.

**Endpoint:** `GET /loyalty/tiers`

**Headers:**
```json
{
  "Authorization": "Bearer {access_token}",
  "Accept": "application/json"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "tiers": [
      {
        "id": "1",
        "name": "bronze",
        "display_name": "البرونزي",
        "description": "مستوى البداية مع مكافآت أساسية",
        "points_required": 0,
        "icon_url": "http://127.0.0.1:8000/storage/tiers/bronze.png",
        "color": "#CD7F32",
        "discount_percentage": 0,
        "bonus_multiplier": 1,
        "benefits": [
          "نقاط ولاء مع كل عملية شراء",
          "إشعارات العروض الخاصة",
          "دعم العملاء المتميز"
        ],
        "is_active": true,
        "sort_order": 1
      },
      {
        "id": "2",
        "name": "silver",
        "display_name": "الفضي",
        "description": "مستوى متقدم مع مكافآت محسّنة",
        "points_required": 500,
        "icon_url": "http://127.0.0.1:8000/storage/tiers/silver.png",
        "color": "#C0C0C0",
        "discount_percentage": 5,
        "bonus_multiplier": 1.2,
        "benefits": [
          "خصم 5% على جميع المنتجات",
          "نقاط إضافية 20% مع كل شراء",
          "عروض حصرية للأعضاء الفضيين"
        ],
        "is_active": true,
        "sort_order": 2
      }
    ],
    "user_tier": {
      "id": "2",
      "name": "silver",
      "display_name": "الفضي",
      "points_required": 500,
      "color": "#C0C0C0"
    },
    "next_tier": {
      "id": "3",
      "name": "gold",
      "display_name": "الذهبي",
      "points_required": 1000,
      "color": "#FFD700"
    },
    "progress_percentage": 85.0
  }
}
```

---

## 🔧 Merchant/Admin Endpoints

### 1. Add Loyalty Points (Protected)

Manually add loyalty points to a customer account.

**Endpoint:** `POST /loyalty/points/add`

**Headers:**
```json
{
  "Authorization": "Bearer {admin_or_merchant_access_token}",
  "Content-Type": "application/json",
  "Accept": "application/json"
}
```

**Request Body:**
```json
{
  "customer_id": 123,
  "points": 50,
  "transaction_id": "TXN_789",
  "reason": "مكافأة خدمة العملاء الممتازة"
}
```

**Response:**
```json
{
  "success": true,
  "message": "تم إضافة النقاط بنجاح",
  "data": {
    "loyalty_point": {
      "id": "456",
      "points": 50,
      "formatted_points": "+50",
      "type": "admin_award",
      "type_name": "مكافأة إدارية",
      "description": "مكافأة خدمة العملاء الممتازة",
      "expires_at": "2025-01-21T10:30:00Z",
      "created_at": "2024-01-21T10:30:00Z"
    }
  }
}
```

---

## 📊 Offer Types Detailed

### 1. Discount Offers (`discount`)
- **Percentage discounts**: e.g., 25% off
- **Fixed amount discounts**: e.g., 50 EGP off
- **Category-specific discounts**: Apply to specific product categories
- **Minimum order requirements**: Require minimum purchase amount
- **Maximum discount caps**: Limit maximum discount amount

### 2. Buy One Get One (`bogo`)
- **BOGO Free**: Buy 2, get 1 free
- **BOGO Discount**: Buy 3, pay for 2
- **Quantity-based offers**: Volume discounts

### 3. Freebies (`freebie`)
- **Free products**: Get specific products free with purchase
- **Free shipping**: No delivery charges
- **Free services**: Additional services at no cost

### 4. Cashback (`cashback`)
- **Percentage cashback**: e.g., 15% of order value returned
- **Fixed cashback**: e.g., 25 EGP returned to wallet
- **Wallet credit**: Credits added to user's digital wallet

---

## 🏆 Reward Types Detailed

### 1. Discount Rewards (`discount`)
- **Percentage discounts**: Redeem points for % off next order
- **Category-specific**: Discounts on specific product categories
- **Tiered discounts**: Different discount levels based on points spent

### 2. Free Products (`free_product`)
- **Individual products**: Specific SunTop juice bottles
- **Product bundles**: Collections of products
- **Service rewards**: Free shipping, priority support

### 3. Cashback Rewards (`cashback`)
- **Wallet credits**: Direct money added to user wallet
- **Refund credits**: Credits for future purchases
- **Bill payment credits**: Credits for utility payments

### 4. Bonus Points (`bonus_points`)
- **Point multipliers**: Extra points added to account
- **Future earning bonuses**: Increased earning rates
- **Special event points**: Extra points for limited time

---

## 🎯 Loyalty Tier Benefits

### Bronze Tier (0+ points)
- Basic loyalty point earning
- Special offer notifications
- Standard customer support

### Silver Tier (500+ points)
- 5% discount on all products
- 20% bonus points on purchases
- Exclusive silver member offers
- Free shipping on orders over 100 EGP

### Gold Tier (1000+ points)
- 10% discount on all products
- 50% bonus points on purchases
- Exclusive gold member offers
- Free shipping on all orders
- Priority customer support
- Free gifts with large orders

### Platinum Tier (2000+ points)
- 15% discount on all products
- Double points on all purchases
- Early access to new products
- Exclusive platinum offers
- Free express shipping
- Personal account manager
- Monthly free gifts
- Special event invitations

---

## 📱 Flutter/Mobile Integration Examples

### Getting Offers
```dart
class OffersService {
  static const String baseUrl = 'http://127.0.0.1:8000/api';
  
  Future<OffersResponse> getOffers({
    String? category,
    String? type,
    bool activeOnly = true,
    int page = 1,
    int perPage = 20,
  }) async {
    final queryParams = {
      'page': page.toString(),
      'per_page': perPage.toString(),
      'active_only': activeOnly.toString(),
      if (category != null) 'category': category,
      if (type != null) 'type': type,
    };
    
    final response = await http.get(
      Uri.parse('$baseUrl/offers').replace(queryParameters: queryParams),
      headers: {'Accept': 'application/json'},
    );
    
    return OffersResponse.fromJson(json.decode(response.body));
  }
  
  Future<ValidationResponse> validateOffer(
    String offerId,
    double orderAmount,
    List<OrderItem> items,
  ) async {
    final response = await http.post(
      Uri.parse('$baseUrl/offers/$offerId/validate'),
      headers: {
        'Authorization': 'Bearer $accessToken',
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: json.encode({
        'order_amount': orderAmount,
        'items': items.map((item) => item.toJson()).toList(),
      }),
    );
    
    return ValidationResponse.fromJson(json.decode(response.body));
  }
}
```

### Getting Loyalty Points
```dart
class LoyaltyService {
  Future<LoyaltyPointsResponse> getUserPoints() async {
    final response = await http.get(
      Uri.parse('$baseUrl/loyalty/points'),
      headers: {
        'Authorization': 'Bearer $accessToken',
        'Accept': 'application/json',
      },
    );
    
    return LoyaltyPointsResponse.fromJson(json.decode(response.body));
  }
  
  Future<RewardsResponse> getRewards({
    String? category,
    String? type,
    int? minPoints,
    int? maxPoints,
  }) async {
    final queryParams = <String, String>{};
    if (category != null) queryParams['category'] = category;
    if (type != null) queryParams['type'] = type;
    if (minPoints != null) queryParams['min_points'] = minPoints.toString();
    if (maxPoints != null) queryParams['max_points'] = maxPoints.toString();
    
    final response = await http.get(
      Uri.parse('$baseUrl/loyalty/rewards').replace(queryParameters: queryParams),
      headers: {
        'Authorization': 'Bearer $accessToken',
        'Accept': 'application/json',
      },
    );
    
    return RewardsResponse.fromJson(json.decode(response.body));
  }
}
```

---

## 🛠️ Error Handling

### Common Error Responses

**401 Unauthorized:**
```json
{
  "success": false,
  "error": {
    "message": "غير مصرح لك بالوصول",
    "timestamp": "2024-01-21T14:30:00Z"
  }
}
```

**404 Not Found:**
```json
{
  "success": false,
  "error": {
    "message": "العرض غير موجود",
    "timestamp": "2024-01-21T14:30:00Z"
  }
}
```

**422 Validation Error:**
```json
{
  "success": false,
  "error": {
    "message": "خطأ في التحقق من صحة البيانات",
    "details": {
      "validation_errors": {
        "order_amount": ["مبلغ الطلب مطلوب"],
        "customer_id": ["معرف العميل غير صحيح"]
      }
    },
    "timestamp": "2024-01-21T14:30:00Z"
  }
}
```

**400 Business Logic Error:**
```json
{
  "success": false,
  "error": {
    "message": "نقاطك غير كافية لاستبدال هذه المكافأة",
    "timestamp": "2024-01-21T14:30:00Z"
  }
}
```

---

## 📈 Best Practices

1. **Caching**: Cache offers and rewards data locally, refresh periodically
2. **Validation**: Always validate offers before applying to orders
3. **User Experience**: Show clear expiry dates and terms
4. **Error Handling**: Provide helpful error messages in Arabic
5. **Performance**: Use pagination for large lists
6. **Real-time Updates**: Poll for points balance changes after transactions
7. **Offline Support**: Cache user's tier and points for offline viewing

---

## 🔄 Business Logic Flow

### Offer Redemption Flow
1. User browses available offers
2. User validates offer against their cart
3. System checks offer validity, usage limits, and user eligibility
4. User redeems offer, creating a redemption record
5. Redemption code is generated with expiry date
6. User applies redemption code during checkout
7. System verifies and applies discount
8. Redemption is marked as used

### Loyalty Points Flow
1. User makes purchase
2. System calculates points based on products and tier
3. Points are awarded and added to user's balance
4. User browses available rewards
5. User redeems reward, points are deducted
6. Reward redemption code is generated
7. User uses reward code for discount/benefit
8. System tracks usage and analytics

### Tier Progression Flow
1. System tracks user's lifetime points
2. User crosses tier threshold
3. System upgrades user to next tier
4. User receives notification about upgrade
5. New tier benefits are immediately available
6. User sees updated benefits in app

---

**Last Updated:** January 21, 2025  
**API Version:** 1.0  
**Status:** Production Ready

This comprehensive API supports all the Flutter requirements mentioned in your specification, including multiple offer types, sophisticated loyalty tiers, reward redemption, and complete analytics. The system is designed to handle the complex business logic while providing a smooth user experience.
