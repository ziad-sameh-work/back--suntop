# ❤️ Favorites API Documentation

## Overview

The Favorites API provides comprehensive wishlist/favorites functionality for the SunTop platform, allowing users to save their favorite products, get personalized recommendations, and manage their favorite items with advanced filtering and statistics.

## Base URL

```
http://127.0.0.1:8000/api
```

## Authentication

All favorites endpoints require authentication via Bearer token:

```
Authorization: Bearer {access_token}
```

---

## 📱 Favorites API Endpoints

### 1. Get User Favorites

Retrieve paginated list of user's favorite products with advanced filtering.

**Endpoint:** `GET /favorites`

**Headers:**
```json
{
  "Authorization": "Bearer {access_token}",
  "Accept": "application/json"
}
```

**Query Parameters:**
- `category_id` (integer, optional): Filter by product category
- `available_only` (boolean, optional): Show only available products (default: true)
- `search` (string, optional): Search in product names and descriptions
- `price_min` (decimal, optional): Minimum price filter
- `price_max` (decimal, optional): Maximum price filter
- `sort_by` (string, optional): Sort field (default: `created_at`)
- `sort_order` (string, optional): Sort order (`asc`, `desc`, default: `desc`)
- `per_page` (integer, optional): Items per page (default: 20, max: 100)
- `page` (integer, optional): Page number (default: 1)

**Response:**
```json
{
  "success": true,
  "data": {
    "favorites": [
      {
        "id": "1",
        "user_id": "123",
        "product_id": "5",
        "product": {
          "id": "5",
          "name": "سن توب برتقال طازج",
          "description": "عصير برتقال طبيعي 100% بدون إضافات",
          "price": 2.99,
          "image_url": "http://127.0.0.1:8000/storage/products/orange-juice.jpg",
          "category": {
            "id": "1",
            "name": "عصائر حمضية"
          },
          "is_available": true,
          "carton_price": 35.88,
          "package_price": 11.96,
          "unit_price": 2.99
        },
        "added_at": "2024-01-15T14:30:00Z",
        "created_at": "2024-01-15T14:30:00Z",
        "days_since_added": 6,
        "is_product_available": true
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 20,
      "total": 8,
      "last_page": 1,
      "has_next": false,
      "has_prev": false
    },
    "total_favorites": 8
  }
}
```

### 2. Add Product to Favorites

Add a specific product to user's favorites list.

**Endpoint:** `POST /favorites`

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
  "product_id": 5
}
```

**Response:**
```json
{
  "success": true,
  "message": "تم إضافة المنتج إلى المفضلة بنجاح",
  "data": {
    "is_favorited": true,
    "favorite_id": "15",
    "added_at": "2024-01-21T16:45:00Z"
  }
}
```

**Error Response (Already Favorited):**
```json
{
  "success": false,
  "error": {
    "message": "المنتج موجود بالفعل في المفضلة",
    "timestamp": "2024-01-21T16:45:00Z"
  }
}
```

### 3. Remove Product from Favorites

Remove a specific product from user's favorites list.

**Endpoint:** `DELETE /favorites/{product_id}`

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
  "message": "تم إزالة المنتج من المفضلة بنجاح",
  "data": {
    "is_favorited": false
  }
}
```

### 4. Toggle Favorite Status

Toggle the favorite status of a product (add if not favorited, remove if favorited).

**Endpoint:** `POST /favorites/toggle`

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
  "product_id": 5
}
```

**Response (Added):**
```json
{
  "success": true,
  "message": "تم إضافة المنتج إلى المفضلة",
  "data": {
    "action": "added",
    "is_favorited": true,
    "product": {
      "id": "5",
      "name": "سن توب برتقال طازج",
      "price": 2.99,
      "image_url": "http://127.0.0.1:8000/storage/products/orange-juice.jpg"
    }
  }
}
```

**Response (Removed):**
```json
{
  "success": true,
  "message": "تم إزالة المنتج من المفضلة",
  "data": {
    "action": "removed",
    "is_favorited": false,
    "product": {
      "id": "5",
      "name": "سن توب برتقال طازج",
      "price": 2.99,
      "image_url": "http://127.0.0.1:8000/storage/products/orange-juice.jpg"
    }
  }
}
```

### 5. Check if Product is Favorited

Check if a specific product is in user's favorites.

**Endpoint:** `GET /favorites/check/{product_id}`

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
    "is_favorited": true,
    "product_id": "5"
  }
}
```

### 6. Check Multiple Products

Check favorite status for multiple products at once (useful for product listing pages).

**Endpoint:** `POST /favorites/check-multiple`

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
  "product_ids": [1, 2, 3, 4, 5]
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "favorites_status": [
      {
        "product_id": 1,
        "is_favorited": false
      },
      {
        "product_id": 2,
        "is_favorited": true
      },
      {
        "product_id": 3,
        "is_favorited": false
      },
      {
        "product_id": 4,
        "is_favorited": true
      },
      {
        "product_id": 5,
        "is_favorited": true
      }
    ]
  }
}
```

### 7. Get Favorites Count

Get the total count of user's favorite products.

**Endpoint:** `GET /favorites/count`

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
    "favorites_count": 8
  }
}
```

### 8. Clear All Favorites

Remove all products from user's favorites list.

**Endpoint:** `DELETE /favorites/clear`

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
  "message": "تم حذف 8 منتج من المفضلة",
  "data": {
    "deleted_count": 8
  }
}
```

### 9. Get User Favorites Statistics

Get detailed statistics about user's favorites.

**Endpoint:** `GET /favorites/statistics`

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
    "total_favorites": 8,
    "categories_breakdown": {
      "عصائر حمضية": 3,
      "عصائر استوائية": 2,
      "عصائر مختلطة": 3
    },
    "average_price": 3.24,
    "recently_added": 2
  }
}
```

### 10. Get Popular Products

Get the most favorited products across all users.

**Endpoint:** `GET /favorites/popular`

**Headers:**
```json
{
  "Authorization": "Bearer {access_token}",
  "Accept": "application/json"
}
```

**Query Parameters:**
- `limit` (integer, optional): Number of products to return (default: 10)

**Response:**
```json
{
  "success": true,
  "data": {
    "popular_products": [
      {
        "product": {
          "id": "2",
          "name": "سن توب مانجو استوائي",
          "description": "عصير مانجو طبيعي بنكهة استوائية",
          "price": 3.50,
          "image_url": "http://127.0.0.1:8000/storage/products/mango-juice.jpg",
          "category": {
            "id": "2",
            "name": "عصائر استوائية"
          },
          "is_available": true
        },
        "favorites_count": 25
      },
      {
        "product": {
          "id": "1",
          "name": "سن توب برتقال طازج",
          "description": "عصير برتقال طبيعي 100%",
          "price": 2.99,
          "image_url": "http://127.0.0.1:8000/storage/products/orange-juice.jpg",
          "category": {
            "id": "1",
            "name": "عصائر حمضية"
          },
          "is_available": true
        },
        "favorites_count": 22
      }
    ]
  }
}
```

### 11. Get Personalized Recommendations

Get product recommendations based on user's favorite categories and preferences.

**Endpoint:** `GET /favorites/recommendations`

**Headers:**
```json
{
  "Authorization": "Bearer {access_token}",
  "Accept": "application/json"
}
```

**Query Parameters:**
- `limit` (integer, optional): Number of recommendations to return (default: 10)

**Response:**
```json
{
  "success": true,
  "data": {
    "recommendations": [
      {
        "id": "7",
        "name": "سن توب ليمون نعناع",
        "description": "مزيج منعش من الليمون والنعناع الطبيعي",
        "price": 3.25,
        "image_url": "http://127.0.0.1:8000/storage/products/lemon-mint.jpg",
        "category": {
          "id": "1",
          "name": "عصائر حمضية"
        },
        "is_available": true,
        "carton_price": 39.00,
        "package_price": 13.00,
        "unit_price": 3.25
      }
    ]
  }
}
```

### 12. Bulk Add to Favorites

Add multiple products to favorites at once.

**Endpoint:** `POST /favorites/bulk-add`

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
  "product_ids": [1, 2, 3, 4, 5]
}
```

**Response:**
```json
{
  "success": true,
  "message": "تم إضافة 3 منتج بنجاح، فشل في 2",
  "data": {
    "success_count": 3,
    "fail_count": 2,
    "results": [
      {
        "product_id": 1,
        "success": true,
        "message": "تم إضافة المنتج إلى المفضلة بنجاح"
      },
      {
        "product_id": 2,
        "success": false,
        "message": "المنتج موجود بالفعل في المفضلة"
      },
      {
        "product_id": 3,
        "success": true,
        "message": "تم إضافة المنتج إلى المفضلة بنجاح"
      },
      {
        "product_id": 4,
        "success": true,
        "message": "تم إضافة المنتج إلى المفضلة بنجاح"
      },
      {
        "product_id": 5,
        "success": false,
        "message": "المنتج غير موجود"
      }
    ]
  }
}
```

---

## 📊 Integration with Product APIs

### Enhanced Product Listing

When fetching products, you can now include favorite status:

**Example Request:** `GET /products`
```json
{
  "Authorization": "Bearer {access_token}",
  "Accept": "application/json"
}
```

**Enhanced Product Response:**
```json
{
  "success": true,
  "data": {
    "products": [
      {
        "id": "1",
        "name": "سن توب برتقال طازج",
        "price": 2.99,
        "image_url": "http://127.0.0.1:8000/storage/products/orange-juice.jpg",
        "is_available": true,
        "is_favorited": true,
        "favorites_count": 22
      }
    ]
  }
}
```

---

## 📱 Flutter/Mobile Integration Examples

### FavoritesService Class
```dart
class FavoritesService {
  static const String baseUrl = 'http://127.0.0.1:8000/api';
  
  // Get user favorites
  Future<FavoritesResponse> getFavorites({
    int? categoryId,
    bool availableOnly = true,
    String? search,
    double? priceMin,
    double? priceMax,
    int page = 1,
    int perPage = 20,
  }) async {
    final queryParams = {
      'page': page.toString(),
      'per_page': perPage.toString(),
      'available_only': availableOnly.toString(),
      if (categoryId != null) 'category_id': categoryId.toString(),
      if (search != null) 'search': search,
      if (priceMin != null) 'price_min': priceMin.toString(),
      if (priceMax != null) 'price_max': priceMax.toString(),
    };
    
    final response = await http.get(
      Uri.parse('$baseUrl/favorites').replace(queryParameters: queryParams),
      headers: {
        'Authorization': 'Bearer $accessToken',
        'Accept': 'application/json',
      },
    );
    
    return FavoritesResponse.fromJson(json.decode(response.body));
  }
  
  // Toggle favorite status
  Future<ToggleResponse> toggleFavorite(int productId) async {
    final response = await http.post(
      Uri.parse('$baseUrl/favorites/toggle'),
      headers: {
        'Authorization': 'Bearer $accessToken',
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: json.encode({
        'product_id': productId,
      }),
    );
    
    return ToggleResponse.fromJson(json.decode(response.body));
  }
  
  // Check multiple products favorite status
  Future<MultipleCheckResponse> checkMultipleFavorites(List<int> productIds) async {
    final response = await http.post(
      Uri.parse('$baseUrl/favorites/check-multiple'),
      headers: {
        'Authorization': 'Bearer $accessToken',
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: json.encode({
        'product_ids': productIds,
      }),
    );
    
    return MultipleCheckResponse.fromJson(json.decode(response.body));
  }
  
  // Get favorites count
  Future<int> getFavoritesCount() async {
    final response = await http.get(
      Uri.parse('$baseUrl/favorites/count'),
      headers: {
        'Authorization': 'Bearer $accessToken',
        'Accept': 'application/json',
      },
    );
    
    final data = json.decode(response.body);
    return data['data']['favorites_count'];
  }
  
  // Get recommendations
  Future<List<Product>> getRecommendations({int limit = 10}) async {
    final response = await http.get(
      Uri.parse('$baseUrl/favorites/recommendations?limit=$limit'),
      headers: {
        'Authorization': 'Bearer $accessToken',
        'Accept': 'application/json',
      },
    );
    
    final data = json.decode(response.body);
    return (data['data']['recommendations'] as List)
        .map((item) => Product.fromJson(item))
        .toList();
  }
}
```

### Flutter Widget Example
```dart
class FavoriteButton extends StatefulWidget {
  final Product product;
  final VoidCallback? onChanged;

  const FavoriteButton({
    Key? key,
    required this.product,
    this.onChanged,
  }) : super(key: key);

  @override
  _FavoriteButtonState createState() => _FavoriteButtonState();
}

class _FavoriteButtonState extends State<FavoriteButton> {
  bool _isLoading = false;
  bool _isFavorited = false;

  @override
  void initState() {
    super.initState();
    _isFavorited = widget.product.isFavorited ?? false;
  }

  Future<void> _toggleFavorite() async {
    setState(() {
      _isLoading = true;
    });

    try {
      final response = await FavoritesService().toggleFavorite(widget.product.id);
      
      setState(() {
        _isFavorited = response.data.isFavorited;
        _isLoading = false;
      });

      // Show success message
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(response.message),
          backgroundColor: _isFavorited ? Colors.red : Colors.green,
        ),
      );

      // Notify parent widget
      widget.onChanged?.call();
    } catch (e) {
      setState(() {
        _isLoading = false;
      });

      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('حدث خطأ: $e'),
          backgroundColor: Colors.red,
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: _isLoading ? null : _toggleFavorite,
      child: Container(
        padding: EdgeInsets.all(8),
        decoration: BoxDecoration(
          shape: BoxShape.circle,
          color: Colors.white,
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.1),
              blurRadius: 4,
              offset: Offset(0, 2),
            ),
          ],
        ),
        child: _isLoading
            ? SizedBox(
                width: 20,
                height: 20,
                child: CircularProgressIndicator(
                  strokeWidth: 2,
                  valueColor: AlwaysStoppedAnimation<Color>(Colors.red),
                ),
              )
            : Icon(
                _isFavorited ? Icons.favorite : Icons.favorite_border,
                color: _isFavorited ? Colors.red : Colors.grey,
                size: 24,
              ),
      ),
    );
  }
}
```

### Data Models
```dart
class FavoriteModel {
  final String id;
  final String userId;
  final String productId;
  final ProductModel product;
  final DateTime addedAt;
  final DateTime createdAt;
  final int daysSinceAdded;
  final bool isProductAvailable;

  FavoriteModel({
    required this.id,
    required this.userId,
    required this.productId,
    required this.product,
    required this.addedAt,
    required this.createdAt,
    required this.daysSinceAdded,
    required this.isProductAvailable,
  });

  factory FavoriteModel.fromJson(Map<String, dynamic> json) {
    return FavoriteModel(
      id: json['id'].toString(),
      userId: json['user_id'].toString(),
      productId: json['product_id'].toString(),
      product: ProductModel.fromJson(json['product']),
      addedAt: DateTime.parse(json['added_at']),
      createdAt: DateTime.parse(json['created_at']),
      daysSinceAdded: json['days_since_added'],
      isProductAvailable: json['is_product_available'] ?? true,
    );
  }
}

class FavoritesResponse {
  final List<FavoriteModel> favorites;
  final PaginationModel pagination;
  final int totalFavorites;

  FavoritesResponse({
    required this.favorites,
    required this.pagination,
    required this.totalFavorites,
  });

  factory FavoritesResponse.fromJson(Map<String, dynamic> json) {
    return FavoritesResponse(
      favorites: (json['data']['favorites'] as List)
          .map((item) => FavoriteModel.fromJson(item))
          .toList(),
      pagination: PaginationModel.fromJson(json['data']['pagination']),
      totalFavorites: json['data']['total_favorites'],
    );
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
    "timestamp": "2024-01-21T16:45:00Z"
  }
}
```

**404 Product Not Found:**
```json
{
  "success": false,
  "error": {
    "message": "المنتج غير موجود",
    "timestamp": "2024-01-21T16:45:00Z"
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
        "product_id": ["معرف المنتج مطلوب"]
      }
    },
    "timestamp": "2024-01-21T16:45:00Z"
  }
}
```

**409 Already Favorited:**
```json
{
  "success": false,
  "error": {
    "message": "المنتج موجود بالفعل في المفضلة",
    "timestamp": "2024-01-21T16:45:00Z"
  }
}
```

---

## 📈 Best Practices

1. **Caching**: Cache favorites list locally and sync periodically
2. **Optimistic Updates**: Update UI immediately, revert on error
3. **Batch Operations**: Use bulk operations when possible
4. **Performance**: Use check-multiple for product listings
5. **User Experience**: Show loading states and success messages
6. **Offline Support**: Cache favorite status for offline viewing
7. **Real-time Sync**: Sync favorites across multiple devices

---

## 🔄 Business Logic Features

### Smart Recommendations
- Based on user's favorite categories
- Considers product popularity
- Excludes already favorited items
- Randomized for variety

### Statistics & Analytics
- Category breakdown of favorites
- Average price analysis
- Recently added tracking
- Popular products identification

### Performance Optimizations
- Efficient database queries with indexes
- Pagination for large favorite lists
- Bulk operations for multiple products
- Caching of popular products

### User Experience Features
- Toggle functionality (add/remove in one action)
- Bulk favorite management
- Search and filtering within favorites
- Personalized recommendations
- Clear all functionality

---

**Last Updated:** January 21, 2025  
**API Version:** 1.0  
**Status:** Production Ready

This comprehensive Favorites API provides all the functionality needed for a complete wishlist/favorites system, exactly matching the requirements shown in your Flutter app screenshot. The system supports the favorites page layout with proper filtering, statistics, and product management as displayed in your image.
