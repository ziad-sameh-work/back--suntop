# 🔔 Notifications API Documentation

## Overview

The Notifications API provides comprehensive notification management for the SunTop platform, including real-time notifications for order updates, loyalty points, offers, and general announcements.

## Base URL

```
http://127.0.0.1:8000/api
```

## Authentication

All notification endpoints require authentication via Bearer token:

```
Authorization: Bearer {access_token}
```

---

## 📱 Customer Notification Endpoints

### 1. Get User Notifications

Retrieve paginated list of notifications for the authenticated user.

**Endpoint:** `GET /notifications`

**Headers:**
```json
{
  "Authorization": "Bearer {access_token}",
  "Accept": "application/json"
}
```

**Query Parameters:**
- `type` (string, optional): Filter by notification type (`shipment`, `offer`, `reward`, `general`, `order_status`, `payment`)
- `is_read` (boolean, optional): Filter by read status (`true`, `false`)
- `priority` (string, optional): Filter by priority (`low`, `medium`, `high`)
- `date_from` (date, optional): Filter notifications from date (YYYY-MM-DD)
- `date_to` (date, optional): Filter notifications to date (YYYY-MM-DD)
- `sort_by` (string, optional): Sort field (default: `created_at`)
- `sort_order` (string, optional): Sort order (`asc`, `desc`, default: `desc`)
- `per_page` (integer, optional): Items per page (default: 20, max: 100)
- `page` (integer, optional): Page number (default: 1)

**Response:**
```json
{
  "success": true,
  "data": {
    "notifications": [
      {
        "id": "1",
        "title": "تم شحن طلبيتك",
        "message": "تم شحن طلبية منتجات سن توب الخاصة بك وستصل خلال يومين",
        "type": "shipment",
        "type_name": "شحنة",
        "priority": "high",
        "priority_name": "عالية",
        "is_read": false,
        "data": {
          "order_id": "1",
          "order_number": "ORD-2024-001",
          "tracking_number": "TRK123456",
          "total_amount": 75.50
        },
        "action_url": "/orders/ORD-2024-001",
        "time_ago": "منذ ساعتين",
        "created_at": "2024-01-20T10:30:00Z",
        "read_at": null
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 20,
      "total": 45,
      "last_page": 3,
      "has_next": true,
      "has_prev": false
    },
    "unread_count": 12
  }
}
```

### 2. Get Specific Notification

Retrieve details of a specific notification and automatically mark it as read.

**Endpoint:** `GET /notifications/{id}`

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
    "id": "1",
    "title": "تم شحن طلبيتك",
    "message": "تم شحن طلبية منتجات سن توب الخاصة بك وستصل خلال يومين",
    "type": "shipment",
    "type_name": "شحنة",
    "priority": "high",
    "priority_name": "عالية",
    "is_read": true,
    "data": {
      "order_id": "1",
      "order_number": "ORD-2024-001",
      "tracking_number": "TRK123456",
      "total_amount": 75.50,
      "merchant_name": "Fresh Juice Corner"
    },
    "action_url": "/orders/ORD-2024-001",
    "time_ago": "منذ ساعتين",
    "created_at": "2024-01-20T10:30:00Z",
    "read_at": "2024-01-20T12:30:00Z"
  }
}
```

### 3. Mark Notification as Read

Mark a specific notification as read.

**Endpoint:** `POST /notifications/{id}/read`

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
  "message": "تم وضع علامة قراءة على الإشعار"
}
```

### 4. Mark All Notifications as Read

Mark all user notifications as read.

**Endpoint:** `POST /notifications/mark-all-read`

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
  "message": "تم وضع علامة قراءة على جميع الإشعارات",
  "data": {
    "marked_count": 12
  }
}
```

### 5. Delete Notification

Delete a specific notification.

**Endpoint:** `DELETE /notifications/{id}`

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
  "message": "تم حذف الإشعار بنجاح"
}
```

### 6. Delete All Notifications

Delete all user notifications.

**Endpoint:** `DELETE /notifications`

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
  "message": "تم حذف جميع الإشعارات بنجاح",
  "data": {
    "deleted_count": 25
  }
}
```

### 7. Get Unread Count

Get the count of unread notifications for the authenticated user.

**Endpoint:** `GET /notifications/unread-count`

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
    "unread_count": 5
  }
}
```

### 8. Get User Statistics

Get notification statistics for the authenticated user.

**Endpoint:** `GET /notifications/statistics`

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
    "total": 48,
    "unread": 5,
    "read": 43,
    "by_type": {
      "shipment": 15,
      "offer": 8,
      "reward": 12,
      "general": 10,
      "order_status": 3
    }
  }
}
```

### 9. Get Notification Types

Get available notification types and their Arabic names.

**Endpoint:** `GET /notifications/types`

**Headers:**
```json
{
  "Accept": "application/json"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "types": [
      {
        "key": "shipment",
        "name": "شحنة"
      },
      {
        "key": "offer",
        "name": "عرض"
      },
      {
        "key": "reward",
        "name": "مكافأة"
      },
      {
        "key": "general",
        "name": "عام"
      },
      {
        "key": "order_status",
        "name": "حالة الطلب"
      },
      {
        "key": "payment",
        "name": "دفع"
      }
    ]
  }
}
```

---

## 🛡️ Admin Notification Endpoints

### 1. Get All Notifications (Admin)

Get all notifications with advanced filtering options.

**Endpoint:** `GET /admin/notifications`

**Headers:**
```json
{
  "Authorization": "Bearer {admin_access_token}",
  "Accept": "application/json"
}
```

**Query Parameters:**
- `user_id` (integer, optional): Filter by specific user
- `type` (string, optional): Filter by notification type
- `is_read` (boolean, optional): Filter by read status
- `priority` (string, optional): Filter by priority
- `search` (string, optional): Search in title and message
- `sort_by` (string, optional): Sort field (default: `created_at`)
- `sort_order` (string, optional): Sort order (default: `desc`)
- `per_page` (integer, optional): Items per page (default: 20)

**Response:**
```json
{
  "success": true,
  "data": {
    "notifications": [
      {
        "id": "1",
        "title": "تم شحن طلبيتك",
        "message": "تم شحن طلبية منتجات سن توب الخاصة بك وستصل خلال يومين",
        "type": "shipment",
        "type_name": "شحنة",
        "priority": "high",
        "priority_name": "عالية",
        "is_read": false,
        "data": {
          "order_id": "1",
          "tracking_number": "TRK123456"
        },
        "action_url": "/orders/1",
        "is_sent": false,
        "scheduled_at": null,
        "time_ago": "منذ ساعتين",
        "created_at": "2024-01-20T10:30:00Z",
        "read_at": null,
        "user": {
          "id": "1",
          "name": "أحمد محمد",
          "email": "ahmed@example.com"
        }
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 20,
      "total": 248,
      "last_page": 13,
      "has_next": true,
      "has_prev": false
    }
  }
}
```

### 2. Create Single Notification

Create a notification for a specific user.

**Endpoint:** `POST /admin/notifications`

**Headers:**
```json
{
  "Authorization": "Bearer {admin_access_token}",
  "Content-Type": "application/json",
  "Accept": "application/json"
}
```

**Request Body:**
```json
{
  "user_id": 1,
  "title": "عرض خاص لك",
  "message": "احصل على خصم 25% على جميع المنتجات",
  "type": "offer",
  "priority": "medium",
  "data": {
    "offer_code": "SPECIAL25",
    "discount_percentage": 25
  },
  "action_url": "/offers",
  "scheduled_at": "2024-01-25T10:00:00Z"
}
```

**Response:**
```json
{
  "success": true,
  "message": "تم إنشاء الإشعار بنجاح",
  "data": {
    "id": "25",
    "title": "عرض خاص لك",
    "message": "احصل على خصم 25% على جميع المنتجات",
    "type": "offer",
    "priority": "medium",
    "created_at": "2024-01-20T14:30:00Z"
  }
}
```

### 3. Create Bulk Notifications

Create notifications for multiple users.

**Endpoint:** `POST /admin/notifications/bulk`

**Headers:**
```json
{
  "Authorization": "Bearer {admin_access_token}",
  "Content-Type": "application/json",
  "Accept": "application/json"
}
```

**Request Body:**
```json
{
  "user_ids": [1, 2, 3, 4, 5],
  "title": "منتجات جديدة متاحة",
  "message": "اكتشف مجموعة جديدة من عصائر سن توب الطبيعية",
  "type": "general",
  "priority": "low",
  "data": {
    "category": "منتجات جديدة"
  },
  "action_url": "/products/new"
}
```

**Response:**
```json
{
  "success": true,
  "message": "تم إنشاء 5 إشعار بنجاح",
  "data": {
    "created_count": 5
  }
}
```

### 4. Send to All Users

Send notification to all active users.

**Endpoint:** `POST /admin/notifications/send-to-all`

**Headers:**
```json
{
  "Authorization": "Bearer {admin_access_token}",
  "Content-Type": "application/json",
  "Accept": "application/json"
}
```

**Request Body:**
```json
{
  "title": "إعلان مهم",
  "message": "سيتم إجراء صيانة على النظام يوم الجمعة من 2-4 صباحاً",
  "type": "general",
  "priority": "high",
  "action_url": "/maintenance-info"
}
```

**Response:**
```json
{
  "success": true,
  "message": "تم إرسال الإشعار إلى 156 مستخدم",
  "data": {
    "created_count": 156
  }
}
```

### 5. Get Admin Statistics

Get comprehensive notification statistics.

**Endpoint:** `GET /admin/notifications/statistics`

**Headers:**
```json
{
  "Authorization": "Bearer {admin_access_token}",
  "Accept": "application/json"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "total": 2456,
    "unread": 342,
    "read": 2114,
    "by_type": {
      "shipment": 856,
      "offer": 234,
      "reward": 445,
      "general": 621,
      "order_status": 234,
      "payment": 66
    },
    "by_priority": {
      "low": 1234,
      "medium": 987,
      "high": 235
    }
  }
}
```

### 6. Delete Notification (Admin)

Delete any notification.

**Endpoint:** `DELETE /admin/notifications/{id}`

**Headers:**
```json
{
  "Authorization": "Bearer {admin_access_token}",
  "Accept": "application/json"
}
```

**Response:**
```json
{
  "success": true,
  "message": "تم حذف الإشعار بنجاح"
}
```

### 7. Clean Old Notifications

Delete notifications older than specified days.

**Endpoint:** `POST /admin/notifications/clean-old`

**Headers:**
```json
{
  "Authorization": "Bearer {admin_access_token}",
  "Content-Type": "application/json",
  "Accept": "application/json"
}
```

**Request Body:**
```json
{
  "days_old": 30
}
```

**Response:**
```json
{
  "success": true,
  "message": "تم حذف 145 إشعار قديم",
  "data": {
    "deleted_count": 145
  }
}
```

### 8. Get Users for Targeting

Get list of users for notification targeting.

**Endpoint:** `GET /admin/notifications/users`

**Headers:**
```json
{
  "Authorization": "Bearer {admin_access_token}",
  "Accept": "application/json"
}
```

**Query Parameters:**
- `role` (string, optional): Filter by user role (`customer`, `merchant`, `admin`)
- `search` (string, optional): Search in name and email
- `per_page` (integer, optional): Items per page (default: 50)

**Response:**
```json
{
  "success": true,
  "data": {
    "users": [
      {
        "id": 1,
        "name": "أحمد محمد",
        "email": "ahmed@example.com",
        "role": "customer"
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 50,
      "total": 156,
      "last_page": 4
    }
  }
}
```

---

## 📊 Notification Types

| Type | Arabic Name | Description | Auto-Generated |
|------|-------------|-------------|----------------|
| `shipment` | شحنة | Order shipping notifications | ✅ |
| `offer` | عرض | Special offers and promotions | ✅ |
| `reward` | مكافأة | Loyalty points and rewards | ✅ |
| `general` | عام | General announcements | ❌ |
| `order_status` | حالة الطلب | Order status updates | ✅ |
| `payment` | دفع | Payment confirmations | ❌ |

## 🎯 Priority Levels

| Priority | Arabic Name | Color | Use Case |
|----------|-------------|-------|----------|
| `low` | منخفضة | Secondary | General info, product updates |
| `medium` | متوسطة | Warning | Offers, loyalty points |
| `high` | عالية | Danger | Order updates, urgent info |

## 🔄 Auto-Generated Notifications

The system automatically generates notifications for:

### Order Events
- **Order Created**: When a new order is placed
- **Order Confirmed**: When merchant confirms the order
- **Order Shipped**: When order is marked as shipped
- **Order Delivered**: When order is delivered

### Loyalty Events
- **Points Earned**: When user earns loyalty points from purchases
- **Bonus Points**: When user receives bonus points for bulk purchases
- **Points Expiring**: Before loyalty points expire (future feature)

### Offer Events
- **New Offers**: When admin creates active offers
- **Offer Expiring**: Before offers expire (future feature)

## 🚀 Integration Examples

### Flutter/Mobile App Integration

```dart
class NotificationService {
  static const String baseUrl = 'http://127.0.0.1:8000/api';
  
  // Get notifications
  Future<NotificationsResponse> getNotifications({
    int page = 1,
    int perPage = 20,
    String? type,
    bool? isRead,
  }) async {
    final response = await http.get(
      Uri.parse('$baseUrl/notifications').replace(queryParameters: {
        'page': page.toString(),
        'per_page': perPage.toString(),
        if (type != null) 'type': type,
        if (isRead != null) 'is_read': isRead.toString(),
      }),
      headers: {
        'Authorization': 'Bearer $accessToken',
        'Accept': 'application/json',
      },
    );
    
    return NotificationsResponse.fromJson(json.decode(response.body));
  }
  
  // Mark as read
  Future<void> markAsRead(String notificationId) async {
    await http.post(
      Uri.parse('$baseUrl/notifications/$notificationId/read'),
      headers: {
        'Authorization': 'Bearer $accessToken',
        'Accept': 'application/json',
      },
    );
  }
  
  // Get unread count
  Future<int> getUnreadCount() async {
    final response = await http.get(
      Uri.parse('$baseUrl/notifications/unread-count'),
      headers: {
        'Authorization': 'Bearer $accessToken',
        'Accept': 'application/json',
      },
    );
    
    final data = json.decode(response.body);
    return data['data']['unread_count'];
  }
}
```

### JavaScript/Web Integration

```javascript
class NotificationAPI {
  constructor(baseUrl, accessToken) {
    this.baseUrl = baseUrl;
    this.accessToken = accessToken;
  }

  async getNotifications(params = {}) {
    const queryParams = new URLSearchParams(params);
    const response = await fetch(`${this.baseUrl}/notifications?${queryParams}`, {
      headers: {
        'Authorization': `Bearer ${this.accessToken}`,
        'Accept': 'application/json',
      },
    });
    return response.json();
  }

  async markAsRead(notificationId) {
    const response = await fetch(`${this.baseUrl}/notifications/${notificationId}/read`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${this.accessToken}`,
        'Accept': 'application/json',
      },
    });
    return response.json();
  }

  async getUnreadCount() {
    const response = await fetch(`${this.baseUrl}/notifications/unread-count`, {
      headers: {
        'Authorization': `Bearer ${this.accessToken}`,
        'Accept': 'application/json',
      },
    });
    const data = await response.json();
    return data.data.unread_count;
  }
}
```

## 🛠️ Error Handling

### Common Error Responses

**401 Unauthorized:**
```json
{
  "success": false,
  "error": {
    "message": "غير مصرح لك بالوصول",
    "timestamp": "2024-01-20T14:30:00Z"
  }
}
```

**404 Not Found:**
```json
{
  "success": false,
  "error": {
    "message": "الإشعار غير موجود",
    "timestamp": "2024-01-20T14:30:00Z"
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
        "title": ["عنوان الإشعار مطلوب"],
        "message": ["محتوى الإشعار مطلوب"]
      }
    },
    "timestamp": "2024-01-20T14:30:00Z"
  }
}
```

## 📈 Best Practices

1. **Polling**: Check for new notifications every 30-60 seconds
2. **Caching**: Cache notification lists locally and update incrementally
3. **Pagination**: Use pagination for better performance
4. **Error Handling**: Implement proper error handling for network issues
5. **User Experience**: Show loading states and empty states appropriately
6. **Real-time**: Consider implementing WebSocket for real-time notifications (future feature)

---

## 📝 Changelog

### Version 1.0.0
- Initial notification system implementation
- Customer and admin endpoints
- Auto-generation for orders, loyalty, and offers
- Comprehensive filtering and pagination
- Multi-language support (Arabic/English)

---

**Last Updated:** January 20, 2025  
**API Version:** 1.0  
**Status:** Production Ready
