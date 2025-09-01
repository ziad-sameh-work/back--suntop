# SunTop API Documentation

## Introduction

This documentation provides detailed information about the SunTop API endpoints, request parameters, and response formats. Use this documentation to integrate with the SunTop platform.

## Base URL

```
https://suntop-eg.com
```

For production environments, the base URL will be different. Please contact the system administrator for the production URL.

## Authentication

Most API endpoints require authentication via Bearer token. To authenticate, include the following header in your requests:

```
Authorization: Bearer {access_token}
```

You can obtain an access token by calling the login endpoint described in the Authentication section.

## API Endpoints

### Customer API Endpoints

1. [Authentication](./1_authentication.md)
   - Login
   - Refresh Token
   - Logout
   - Reset Password
   - Get User Profile

2. [Products](./2_products.md)
   - Get All Products
   - Get Featured Products
   - Get Product Categories
   - Search Products
   - Get Product Details

3. [Orders](./3_orders.md)
   - Get All Orders
   - Create New Order
   - Get Order Details
   - Get Order Tracking
   - Cancel Order
   - Reorder

4. [Chat](./4_chat.md)
   - Get or Create Chat
   - Send Chat Message
   - Get Chat Messages
   - Get Chat History
   - Mark Messages as Read

### Admin API Endpoints

5. [Admin Analytics](./5_admin_analytics.md)
   - Dashboard Analytics
   - Sales Analytics
   - Customer Analytics
   - Product Analytics
   - Financial Analytics

6. [Admin User Management](./6_admin_users.md)
   - List Users
   - Create User
   - Get User Details
   - Update User
   - Delete User
   - Toggle User Status
   - Reset User Password
   - Get User Statistics

7. [Admin Product Management](./7_admin_products.md)
   - List Products
   - Create Product
   - Get Product Details
   - Update Product
   - Delete Product
   - Toggle Product Availability
   - Toggle Featured Status
   - Update Stock
   - Bulk Actions
   - List Product Categories
   - Product Analytics

8. [Admin Order Management](./8_admin_orders.md)
   - List Orders
   - Get Order Details
   - Update Order Status
   - Cancel Order
   - Add Tracking Information
   - Get Orders Statistics
   - Export Orders

9. [Admin Merchant Management](./9_admin_merchants.md)
   - List Merchants
   - Create Merchant
   - Get Merchant Details
   - Update Merchant
   - Delete Merchant
   - Toggle Merchant Status
   - Toggle Merchant Open Status
   - Get Merchant Statistics

10. [Admin Offers Management](./10_admin_offers.md)
    - List Offers
    - Create Offer
    - Get Offer Details
    - Update Offer
    - Delete Offer
    - Toggle Offer Status
    - Get Offer Statistics

11. [Admin Loyalty Points Management](./11_admin_loyalty.md)
    - List Loyalty Transactions
    - Award Points
    - Deduct Points
    - Get User Loyalty Summary
    - Get Loyalty Statistics
    - Clean Expired Points

12. [Admin User Categories Management](./12_admin_user_categories.md)
    - List User Categories
    - Create User Category
    - Get User Category Details
    - Update User Category
    - Delete User Category
    - Toggle User Category Status
    - Get Users in Category
    - Get User Category Statistics
    - Recalculate User Categories
    - Test Amount for Category

13. [Loyalty Points API (Customer)](./13_loyalty_api.md)
    - Get Loyalty Points Summary
    - Get Loyalty Transactions History
    - User Authentication with Loyalty Points and User Category

14. [Real-Time Chat API (Mobile Apps)](./14_realtime_chat_api.md)
    - Get or Create Real-Time Chat
    - Send Real-Time Chat Message
    - Get Real-Time Chat Messages

15. [Long-Polling Chat API (Mobile Apps)](./15_long_polling_chat_api.md)
    - Get or Create Chat
    - Send Chat Message 
    - Get Chat Messages
    - Poll for New Messages

16. [Notifications API](./16_notifications_api.md)
    - Get User Notifications
    - Mark Notifications as Read
    - Delete Notifications
    - Get Unread Count
    - Get Notification Statistics
    - Admin Notification Management

17. [Offers & Loyalty System API](./17_offers_loyalty_api.md)
    - Comprehensive Offers Management (Discount, BOGO, Freebies, Cashback)
    - Loyalty Points & Transactions
    - Rewards & Redemption System
    - Tier-based Benefits (Bronze, Silver, Gold, Platinum)
    - Offer Validation & Redemption
    - User Analytics & Earning Opportunities

18. [Favorites API](./18_favorites_api.md)
    - Add/Remove Products from Favorites
    - Toggle Favorite Status
    - Get User Favorites with Filtering
    - Bulk Operations
    - Personalized Recommendations
    - Popular Products & Statistics

19. [Featured Offers & Home Page API](./19_featured_offers_home_api.md)
    - Featured Offers for Home Page
    - Quick Statistics & Metrics
    - Trending Offers
    - Specialized Home Page Data
    - Flutter Integration Examples
    - Performance Optimized Responses

## Common Error Responses

- **401 Unauthorized**: Authentication failed or invalid token
- **403 Forbidden**: Insufficient permissions
- **404 Not Found**: Resource not found
- **422 Validation Error**: Invalid request parameters
- **500 Server Error**: Internal server error

For detailed error format examples, refer to specific endpoint documentation.

## Pagination

Most list endpoints support pagination with the following query parameters:

- `page`: Page number (starting from 1)
- `limit` or `per_page`: Number of items per page

The response includes pagination metadata:

```json
{
  "pagination": {
    "current_page": 1,
    "per_page": 20,
    "total": 248,
    "total_pages": 13,
    "has_next": true,
    "has_prev": false
  }
}
```

## Sorting and Filtering

Most list endpoints support sorting and filtering with the following query parameters:

- `sort`: Field to sort by
- `order`: Sort order ('asc' or 'desc')
- Additional filter parameters specific to each endpoint

## Rate Limiting

The API has rate limiting in place to prevent abuse. If you exceed the rate limit, you'll receive a `429 Too Many Requests` response.

## Support

For any questions or issues regarding the API, please contact the support team.
