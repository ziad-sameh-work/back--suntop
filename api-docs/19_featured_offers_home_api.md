# 🌟 Featured Offers & Home Page API Documentation

## Overview

This API provides specialized endpoints for the home page special offers section, quick statistics, and trending offers functionality. These endpoints are specifically designed to support the Flutter mobile app's home page requirements with optimized data structures and performance.

## Base URL

```
https://suntop-eg.com/api
```

## Authentication

Some endpoints are public, others require authentication via Bearer token:

```
Authorization: Bearer {access_token}
```

---

## 🎯 Featured Offers Endpoints

### 1. Get Featured Offers for Home Page

Retrieve featured offers specifically formatted for the home page special offers section.

**Endpoint:** `GET /api/offers/featured`

**Headers:**
```json
{
  "Accept": "application/json"
}
```

**Query Parameters:**
- `limit` (integer, optional): Number of featured offers to return (default: 5, max: 20)
- `category_id` (integer, optional): Filter by specific product category

**Example Request:**
```bash
curl -X GET "https://suntop-eg.com/api/offers/featured?limit=5&category_id=1" \
  -H "Accept: application/json"
```

**Response:**
```json
{
  "success": true,
  "data": {
    "featured_offers": [
      {
        "id": "1",
        "title": "خصم 30% على عصائر الحمضيات",
        "description": "خصم 30% على جميع العصائر الحمضية",
        "discount_percentage": 30,
        "discount_amount": null,
        "image_url": "https://suntop-eg.com/storage/offers/citrus-offer.jpg",
        "background_color": "#FF6B35",
        "text_color": "#FFFFFF",
        "offer_type": {
          "id": "1",
          "name": "discount",
          "display_name": "خصم"
        },
        "category": {
          "id": "1",
          "name": "عصائر حمضية"
        },
        "merchant": {
          "id": "1",
          "name": "سن توب",
          "logo_url": "https://suntop-eg.com/storage/merchants/suntop-logo.jpg"
        },
        "valid_from": "2024-01-21T00:00:00Z",
        "valid_until": "2024-02-20T23:59:59Z",
        "is_active": true,
        "is_featured": true,
        "usage_count": 67,
        "max_usage": 1000,
        "min_purchase_amount": 50.00,
        "offer_tag": "رائج",
        "applicable_products": [
          {
            "id": "1",
            "name": "سن توب برتقال",
            "price": 2.99,
            "discounted_price": 2.09
          },
          {
            "id": "2", 
            "name": "سن توب ليمون",
            "price": 3.25,
            "discounted_price": 2.28
          }
        ]
      },
      {
        "id": "2",
        "title": "عرض المانجو الاستوائي",
        "description": "خصم 25% على عصائر المانجو الاستوائية",
        "discount_percentage": 25,
        "discount_amount": null,
        "image_url": "https://suntop-eg.com/storage/offers/mango-offer.jpg",
        "background_color": "#FFA500",
        "text_color": "#FFFFFF",
        "offer_type": {
          "id": "2",
          "name": "discount",
          "display_name": "خصم"
        },
        "category": {
          "id": "2",
          "name": "عصائر استوائية"
        },
        "merchant": {
          "id": "1",
          "name": "سن توب",
          "logo_url": "https://suntop-eg.com/storage/merchants/suntop-logo.jpg"
        },
        "valid_from": "2024-01-21T00:00:00Z",
        "valid_until": "2024-03-06T23:59:59Z",
        "is_active": true,
        "is_featured": true,
        "usage_count": 42,
        "max_usage": 500,
        "min_purchase_amount": 30.00,
        "offer_tag": "جديد",
        "applicable_products": [
          {
            "id": "3",
            "name": "سن توب مانجو",
            "price": 3.50,
            "discounted_price": 2.63
          }
        ]
      },
      {
        "id": "3",
        "title": "شحن مجاني للطلبات +100 جنيه",
        "description": "شحن مجاني للطلبات أكثر من 100 جنيه",
        "discount_percentage": null,
        "discount_amount": null,
        "image_url": "https://suntop-eg.com/storage/offers/free-shipping.jpg",
        "background_color": "#28A745",
        "text_color": "#FFFFFF",
        "offer_type": {
          "id": "3",
          "name": "free_shipping",
          "display_name": "شحن مجاني"
        },
        "category": {
          "id": "1",
          "name": "عام"
        },
        "merchant": {
          "id": "1",
          "name": "سن توب",
          "logo_url": "https://suntop-eg.com/storage/merchants/suntop-logo.jpg"
        },
        "valid_from": "2024-01-21T00:00:00Z",
        "valid_until": "2024-03-21T23:59:59Z",
        "is_active": true,
        "is_featured": true,
        "usage_count": 156,
        "max_usage": null,
        "min_purchase_amount": 100.00,
        "offer_tag": "حصري",
        "applicable_products": []
      }
    ],
    "total_featured": 3
  }
}
```

**Response Fields Explanation:**
- `id`: Unique offer identifier
- `title`: Main offer title for display
- `description`: Short description for home page
- `discount_percentage`: Percentage discount (if applicable)
- `discount_amount`: Fixed discount amount (if applicable)
- `image_url`: Offer banner image URL (can be null)
- `background_color`: Hex color for offer background
- `text_color`: Hex color for offer text
- `offer_type`: Object containing offer type information
- `category`: Product category this offer applies to
- `merchant`: Merchant/brand information
- `valid_from/valid_until`: Offer validity period
- `is_active`: Whether offer is currently active
- `is_featured`: Whether offer is marked as featured
- `usage_count`: Number of times offer has been used
- `max_usage`: Maximum allowed usage (null = unlimited)
- `min_purchase_amount`: Minimum order amount required
- `offer_tag`: Tag label (جديد، حصري، محدود، رائج، etc.)
- `applicable_products`: Array of products with discounted prices

---

## 📊 Quick Stats Endpoint

### 2. Get Quick Statistics for Home Page

Retrieve quick statistics to display in the home page header or stats section.

**Endpoint:** `GET /api/offers/stats`

**Headers:**
```json
{
  "Accept": "application/json"
}
```

**Example Request:**
```bash
curl -X GET "https://suntop-eg.com/api/offers/stats" \
  -H "Accept: application/json"
```

**Response:**
```json
{
  "success": true,
  "data": {
    "active_offers_count": 8,
    "total_savings_today": 1250.50,
    "most_popular_offer": {
      "id": "3",
      "title": "شحن مجاني للطلبات +100 جنيه",
      "usage_count": 156
    }
  }
}
```

**Response Fields:**
- `active_offers_count`: Number of currently active offers
- `total_savings_today`: Total amount saved by users today (in EGP)
- `most_popular_offer`: Object containing the most used offer information
  - `id`: Offer ID
  - `title`: Offer title
  - `usage_count`: Number of times it has been used

**Use Cases:**
- Display offer count in home header
- Show total savings achieved by users
- Highlight most popular offer
- Create engagement metrics

---

## 🔥 Trending Offers Endpoint

### 3. Get Trending Offers

Retrieve currently trending offers based on usage patterns and trend scores.

**Endpoint:** `GET /api/offers/trending`

**Headers:**
```json
{
  "Accept": "application/json"
}
```

**Query Parameters:**
- `limit` (integer, optional): Number of trending offers to return (default: 3, max: 10)

**Example Request:**
```bash
curl -X GET "https://suntop-eg.com/api/offers/trending?limit=3" \
  -H "Accept: application/json"
```

**Response:**
```json
{
  "success": true,
  "data": {
    "trending_offers": [
      {
        "id": "3",
        "title": "شحن مجاني للطلبات +100 جنيه",
        "offer_type": {
          "name": "free_shipping",
          "display_name": "شحن مجاني"
        },
        "usage_count": 156,
        "trend_percentage": 92.5,
        "trend_score": 342
      },
      {
        "id": "1",
        "title": "خصم 30% على عصائر الحمضيات",
        "offer_type": {
          "name": "percentage_discount",
          "display_name": "خصم نسبي"
        },
        "usage_count": 67,
        "trend_percentage": 67.8,
        "trend_score": 198
      },
      {
        "id": "5",
        "title": "استرداد نقدي 15%",
        "offer_type": {
          "name": "cashback",
          "display_name": "استرداد نقدي"
        },
        "usage_count": 43,
        "trend_percentage": 45.2,
        "trend_score": 142
      }
    ]
  }
}
```

**Response Fields:**
- `id`: Offer identifier
- `title`: Offer title
- `offer_type`: Object with offer type information
- `usage_count`: Number of times offer has been used
- `trend_percentage`: Calculated trending percentage (0-100)
- `trend_score`: Internal trending score used for ranking

**Trend Score Calculation:**
```
trend_score = (usage_count * 2) + (usage_rate * 50) + recency_bonus
```
- `usage_count * 2`: Base score from total usage
- `usage_rate * 50`: Percentage of usage limit reached
- `recency_bonus`: Bonus points for newer offers (max 30 days)

---

## 📱 Flutter Integration Examples

### FeaturedOffersService Class

```dart
class FeaturedOffersService {
  static const String baseUrl = 'https://suntop-eg.com/api';
  
  // Get featured offers for home page
  Future<FeaturedOffersResponse> getFeaturedOffers({
    int limit = 5,
    int? categoryId,
  }) async {
    final queryParams = {
      'limit': limit.toString(),
      if (categoryId != null) 'category_id': categoryId.toString(),
    };
    
    final response = await http.get(
      Uri.parse('$baseUrl/offers/featured').replace(queryParameters: queryParams),
      headers: {'Accept': 'application/json'},
    );
    
    if (response.statusCode == 200) {
      return FeaturedOffersResponse.fromJson(json.decode(response.body));
    } else {
      throw Exception('Failed to load featured offers');
    }
  }
  
  // Get quick stats
  Future<QuickStatsResponse> getQuickStats() async {
    final response = await http.get(
      Uri.parse('$baseUrl/offers/stats'),
      headers: {'Accept': 'application/json'},
    );
    
    if (response.statusCode == 200) {
      return QuickStatsResponse.fromJson(json.decode(response.body));
    } else {
      throw Exception('Failed to load stats');
    }
  }
  
  // Get trending offers
  Future<TrendingOffersResponse> getTrendingOffers({int limit = 3}) async {
    final response = await http.get(
      Uri.parse('$baseUrl/offers/trending?limit=$limit'),
      headers: {'Accept': 'application/json'},
    );
    
    if (response.statusCode == 200) {
      return TrendingOffersResponse.fromJson(json.decode(response.body));
    } else {
      throw Exception('Failed to load trending offers');
    }
  }
}
```

### Data Models

```dart
// Featured Offer Model
class FeaturedOffer {
  final String id;
  final String title;
  final String description;
  final double? discountPercentage;
  final double? discountAmount;
  final String? imageUrl;
  final String backgroundColor;
  final String textColor;
  final OfferType offerType;
  final Category category;
  final Merchant merchant;
  final DateTime validFrom;
  final DateTime validUntil;
  final bool isActive;
  final bool isFeatured;
  final int usageCount;
  final int? maxUsage;
  final double? minPurchaseAmount;
  final String? offerTag;
  final List<ApplicableProduct> applicableProducts;

  FeaturedOffer({
    required this.id,
    required this.title,
    required this.description,
    this.discountPercentage,
    this.discountAmount,
    this.imageUrl,
    required this.backgroundColor,
    required this.textColor,
    required this.offerType,
    required this.category,
    required this.merchant,
    required this.validFrom,
    required this.validUntil,
    required this.isActive,
    required this.isFeatured,
    required this.usageCount,
    this.maxUsage,
    this.minPurchaseAmount,
    this.offerTag,
    required this.applicableProducts,
  });

  factory FeaturedOffer.fromJson(Map<String, dynamic> json) {
    return FeaturedOffer(
      id: json['id'].toString(),
      title: json['title'],
      description: json['description'],
      discountPercentage: json['discount_percentage']?.toDouble(),
      discountAmount: json['discount_amount']?.toDouble(),
      imageUrl: json['image_url'],
      backgroundColor: json['background_color'],
      textColor: json['text_color'],
      offerType: OfferType.fromJson(json['offer_type']),
      category: Category.fromJson(json['category']),
      merchant: Merchant.fromJson(json['merchant']),
      validFrom: DateTime.parse(json['valid_from']),
      validUntil: DateTime.parse(json['valid_until']),
      isActive: json['is_active'],
      isFeatured: json['is_featured'],
      usageCount: json['usage_count'],
      maxUsage: json['max_usage'],
      minPurchaseAmount: json['min_purchase_amount']?.toDouble(),
      offerTag: json['offer_tag'],
      applicableProducts: (json['applicable_products'] as List)
          .map((item) => ApplicableProduct.fromJson(item))
          .toList(),
    );
  }
}

// Quick Stats Model
class QuickStats {
  final int activeOffersCount;
  final double totalSavingsToday;
  final MostPopularOffer? mostPopularOffer;

  QuickStats({
    required this.activeOffersCount,
    required this.totalSavingsToday,
    this.mostPopularOffer,
  });

  factory QuickStats.fromJson(Map<String, dynamic> json) {
    return QuickStats(
      activeOffersCount: json['active_offers_count'],
      totalSavingsToday: json['total_savings_today'].toDouble(),
      mostPopularOffer: json['most_popular_offer'] != null
          ? MostPopularOffer.fromJson(json['most_popular_offer'])
          : null,
    );
  }
}

// Trending Offer Model
class TrendingOffer {
  final String id;
  final String title;
  final OfferType offerType;
  final int usageCount;
  final double trendPercentage;
  final int trendScore;

  TrendingOffer({
    required this.id,
    required this.title,
    required this.offerType,
    required this.usageCount,
    required this.trendPercentage,
    required this.trendScore,
  });

  factory TrendingOffer.fromJson(Map<String, dynamic> json) {
    return TrendingOffer(
      id: json['id'].toString(),
      title: json['title'],
      offerType: OfferType.fromJson(json['offer_type']),
      usageCount: json['usage_count'],
      trendPercentage: json['trend_percentage'].toDouble(),
      trendScore: json['trend_score'],
    );
  }
}

// Supporting Models
class OfferType {
  final String name;
  final String displayName;

  OfferType({required this.name, required this.displayName});

  factory OfferType.fromJson(Map<String, dynamic> json) {
    return OfferType(
      name: json['name'],
      displayName: json['display_name'],
    );
  }
}

class Category {
  final String id;
  final String name;

  Category({required this.id, required this.name});

  factory Category.fromJson(Map<String, dynamic> json) {
    return Category(
      id: json['id'].toString(),
      name: json['name'],
    );
  }
}

class Merchant {
  final String id;
  final String name;
  final String logoUrl;

  Merchant({required this.id, required this.name, required this.logoUrl});

  factory Merchant.fromJson(Map<String, dynamic> json) {
    return Merchant(
      id: json['id'].toString(),
      name: json['name'],
      logoUrl: json['logo_url'],
    );
  }
}

class ApplicableProduct {
  final String id;
  final String name;
  final double price;
  final double discountedPrice;

  ApplicableProduct({
    required this.id,
    required this.name,
    required this.price,
    required this.discountedPrice,
  });

  factory ApplicableProduct.fromJson(Map<String, dynamic> json) {
    return ApplicableProduct(
      id: json['id'].toString(),
      name: json['name'],
      price: json['price'].toDouble(),
      discountedPrice: json['discounted_price'].toDouble(),
    );
  }
}

class MostPopularOffer {
  final String id;
  final String title;
  final int usageCount;

  MostPopularOffer({
    required this.id,
    required this.title,
    required this.usageCount,
  });

  factory MostPopularOffer.fromJson(Map<String, dynamic> json) {
    return MostPopularOffer(
      id: json['id'].toString(),
      title: json['title'],
      usageCount: json['usage_count'],
    );
  }
}
```

### Home Page Widget Example

```dart
class HomeSpecialOffersSection extends StatefulWidget {
  @override
  _HomeSpecialOffersSectionState createState() => _HomeSpecialOffersSectionState();
}

class _HomeSpecialOffersSectionState extends State<HomeSpecialOffersSection> {
  final FeaturedOffersService _offersService = FeaturedOffersService();
  List<FeaturedOffer> _featuredOffers = [];
  QuickStats? _quickStats;
  List<TrendingOffer> _trendingOffers = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadHomeData();
  }

  Future<void> _loadHomeData() async {
    try {
      final results = await Future.wait([
        _offersService.getFeaturedOffers(limit: 5),
        _offersService.getQuickStats(),
        _offersService.getTrendingOffers(limit: 3),
      ]);

      setState(() {
        _featuredOffers = (results[0] as FeaturedOffersResponse).featuredOffers;
        _quickStats = (results[1] as QuickStatsResponse).stats;
        _trendingOffers = (results[2] as TrendingOffersResponse).trendingOffers;
        _isLoading = false;
      });
    } catch (e) {
      setState(() {
        _isLoading = false;
      });
      // Handle error
    }
  }

  @override
  Widget build(BuildContext context) {
    if (_isLoading) {
      return CircularProgressIndicator();
    }

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        // Quick Stats Header
        _buildQuickStatsHeader(),
        
        SizedBox(height: 16),
        
        // Featured Offers Section
        Text(
          'العروض الخاصة',
          style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
        ),
        
        SizedBox(height: 12),
        
        // Featured Offers Carousel
        Container(
          height: 180,
          child: ListView.builder(
            scrollDirection: Axis.horizontal,
            itemCount: _featuredOffers.length,
            itemBuilder: (context, index) {
              return _buildFeaturedOfferCard(_featuredOffers[index]);
            },
          ),
        ),
        
        SizedBox(height: 20),
        
        // Trending Offers
        Text(
          'العروض الرائجة',
          style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
        ),
        
        SizedBox(height: 8),
        
        ..._trendingOffers.map((offer) => _buildTrendingOfferItem(offer)),
      ],
    );
  }

  Widget _buildQuickStatsHeader() {
    if (_quickStats == null) return SizedBox();
    
    return Container(
      padding: EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.blue.shade50,
        borderRadius: BorderRadius.circular(12),
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceAround,
        children: [
          _buildStatItem(
            'العروض النشطة',
            _quickStats!.activeOffersCount.toString(),
            Icons.local_offer,
          ),
          _buildStatItem(
            'الوفورات اليوم',
            '${_quickStats!.totalSavingsToday.toStringAsFixed(1)} ج.م',
            Icons.savings,
          ),
        ],
      ),
    );
  }

  Widget _buildStatItem(String label, String value, IconData icon) {
    return Column(
      children: [
        Icon(icon, color: Colors.blue, size: 24),
        SizedBox(height: 4),
        Text(
          value,
          style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
        ),
        Text(
          label,
          style: TextStyle(fontSize: 12, color: Colors.grey),
        ),
      ],
    );
  }

  Widget _buildFeaturedOfferCard(FeaturedOffer offer) {
    return Container(
      width: 280,
      margin: EdgeInsets.only(right: 12),
      decoration: BoxDecoration(
        color: Color(int.parse(offer.backgroundColor.replaceAll('#', '0xFF'))),
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.1),
            blurRadius: 8,
            offset: Offset(0, 4),
          ),
        ],
      ),
      child: Padding(
        padding: EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Offer Tag
            if (offer.offerTag != null)
              Container(
                padding: EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                decoration: BoxDecoration(
                  color: Colors.white.withOpacity(0.2),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Text(
                  offer.offerTag!,
                  style: TextStyle(
                    color: Color(int.parse(offer.textColor.replaceAll('#', '0xFF'))),
                    fontSize: 10,
                    fontWeight: FontWeight.bold,
                  ),
                ),
              ),
            
            SizedBox(height: 8),
            
            // Title
            Text(
              offer.title,
              style: TextStyle(
                color: Color(int.parse(offer.textColor.replaceAll('#', '0xFF'))),
                fontSize: 16,
                fontWeight: FontWeight.bold,
              ),
              maxLines: 2,
              overflow: TextOverflow.ellipsis,
            ),
            
            SizedBox(height: 4),
            
            // Description
            Text(
              offer.description,
              style: TextStyle(
                color: Color(int.parse(offer.textColor.replaceAll('#', '0xFF'))).withOpacity(0.9),
                fontSize: 12,
              ),
              maxLines: 2,
              overflow: TextOverflow.ellipsis,
            ),
            
            Spacer(),
            
            // Discount Display
            if (offer.discountPercentage != null)
              Text(
                '${offer.discountPercentage!.toInt()}% خصم',
                style: TextStyle(
                  color: Color(int.parse(offer.textColor.replaceAll('#', '0xFF'))),
                  fontSize: 24,
                  fontWeight: FontWeight.bold,
                ),
              ),
              
            // Usage Progress
            if (offer.maxUsage != null)
              Container(
                margin: EdgeInsets.only(top: 8),
                child: LinearProgressIndicator(
                  value: offer.usageCount / offer.maxUsage!,
                  backgroundColor: Colors.white.withOpacity(0.3),
                  valueColor: AlwaysStoppedAnimation<Color>(
                    Color(int.parse(offer.textColor.replaceAll('#', '0xFF'))),
                  ),
                ),
              ),
          ],
        ),
      ),
    );
  }

  Widget _buildTrendingOfferItem(TrendingOffer offer) {
    return Container(
      margin: EdgeInsets.only(bottom: 8),
      padding: EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: Colors.grey.shade50,
        borderRadius: BorderRadius.circular(8),
        border: Border.all(color: Colors.grey.shade200),
      ),
      child: Row(
        children: [
          Icon(Icons.trending_up, color: Colors.orange, size: 20),
          SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  offer.title,
                  style: TextStyle(fontWeight: FontWeight.w500),
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
                Text(
                  offer.offerType.displayName,
                  style: TextStyle(fontSize: 12, color: Colors.grey),
                ),
              ],
            ),
          ),
          Column(
            crossAxisAlignment: CrossAxisAlignment.end,
            children: [
              Text(
                '${offer.trendPercentage.toStringAsFixed(1)}%',
                style: TextStyle(
                  fontWeight: FontWeight.bold,
                  color: Colors.orange,
                ),
              ),
              Text(
                '${offer.usageCount} استخدام',
                style: TextStyle(fontSize: 10, color: Colors.grey),
              ),
            ],
          ),
        ],
      ),
    );
  }
}
```

---

## 🛠️ Error Handling

### Common Error Responses

**400 Bad Request:**
```json
{
  "success": false,
  "error": {
    "message": "معاملات الطلب غير صحيحة",
    "details": {
      "limit": "يجب أن يكون الحد الأقصى بين 1 و 20"
    },
    "timestamp": "2024-01-21T16:45:00Z"
  }
}
```

**404 Not Found:**
```json
{
  "success": false,
  "error": {
    "message": "لا توجد عروض مميزة متاحة حالياً",
    "timestamp": "2024-01-21T16:45:00Z"
  }
}
```

**500 Internal Server Error:**
```json
{
  "success": false,
  "error": {
    "message": "خطأ في الخادم، يرجى المحاولة لاحقاً",
    "timestamp": "2024-01-21T16:45:00Z"
  }
}
```

---

## 🎨 Design Guidelines

### Color Usage
- **Background Color**: Use the provided hex color for offer card background
- **Text Color**: Use the provided hex color for text to ensure readability
- **Default Colors**: If no colors provided, use fallback colors:
  - Background: `#FF6B35` (SunTop Orange)
  - Text: `#FFFFFF` (White)

### Offer Tags Styling
- **جديد (New)**: Green badge
- **حصري (Exclusive)**: Purple badge  
- **محدود (Limited)**: Red badge
- **رائج (Hot)**: Orange badge
- **نهاية الأسبوع (Weekend)**: Blue badge
- **موسمي (Seasonal)**: Yellow badge

### Card Layout Recommendations
- **Card Width**: 280px for horizontal scroll
- **Card Height**: 180px
- **Border Radius**: 16px
- **Shadow**: Light shadow for depth
- **Spacing**: 12px between cards

---

## 📊 Performance Tips

1. **Caching**: Cache featured offers for 5-10 minutes
2. **Image Loading**: Use lazy loading for offer images
3. **Pagination**: Limit featured offers to 5-10 per request
4. **Background Refresh**: Refresh data every time user comes to home
5. **Error Fallback**: Always show cached data if network fails

---

## 🔄 Usage Flow

1. **App Launch**: Load featured offers immediately
2. **Home Display**: Show offers in horizontal carousel
3. **Stats Display**: Show quick stats in header section
4. **Trending Section**: Display trending offers below featured
5. **Refresh**: Pull-to-refresh to get latest offers
6. **Tap Action**: Navigate to offer details or redemption

---

## 📝 API Testing

### Using curl

```bash
# Test featured offers
curl -X GET "https://suntop-eg.com/api/offers/featured?limit=3" \
  -H "Accept: application/json"

# Test quick stats
curl -X GET "https://suntop-eg.com/api/offers/stats" \
  -H "Accept: application/json"

# Test trending offers
curl -X GET "https://suntop-eg.com/api/offers/trending?limit=2" \
  -H "Accept: application/json"
```

### Using Postman

**Collection:** Featured Offers Home API

**Requests:**
1. **Get Featured Offers**
   - Method: GET
   - URL: `{{base_url}}/offers/featured`
   - Query Params: `limit=5`, `category_id=1`

2. **Get Quick Stats**
   - Method: GET
   - URL: `{{base_url}}/offers/stats`

3. **Get Trending Offers**
   - Method: GET
   - URL: `{{base_url}}/offers/trending`
   - Query Params: `limit=3`

---

**Last Updated:** January 21, 2025  
**API Version:** 1.0  
**Status:** Production Ready

This API is specifically designed for the home page special offers section and provides all the necessary data in the exact format required by the Flutter mobile application.
