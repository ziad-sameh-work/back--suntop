# API Documentation - Chat

Base URL: `https://suntop-eg.com`

## Chat Endpoints

### Get or Create Chat (Protected)
- **URL**: `/api/chat/start`
- **Method**: `GET`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Accept: application/json`
- **Query Parameters**:
  - `subject` (optional): Chat subject (default: "دعم عام")
  - `priority` (optional): Chat priority (default: "medium")
- **Response (200 OK)**:
```json
{
  "success": true,
  "data": {
    "chat": {
      "id": "1",
      "subject": "دعم عام",
      "status": "open",
      "priority": "medium",
      "created_at": "2024-01-20T10:30:00Z",
      "assigned_admin": {
        "id": "2",
        "name": "مدير الدعم",
        "full_name": "أحمد علي"
      },
      "unread_count": 0
    },
    "messages": [
      {
        "id": "1",
        "message": "مرحباً، كيف يمكنني مساعدتك؟",
        "message_type": "text",
        "sender_type": "admin",
        "sender": {
          "id": "2",
          "name": "مدير الدعم",
          "full_name": "أحمد علي"
        },
        "attachment_url": null,
        "attachment_name": null,
        "created_at": "2024-01-20T10:30:05Z",
        "formatted_time": "10:30",
        "is_read": false
      }
    ]
  }
}
```

### Send Chat Message (Protected)
- **URL**: `/api/chat/send`
- **Method**: `POST`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Accept: application/json`
  - `Content-Type: multipart/form-data` (if sending an attachment)
- **Request Body**:
```
chat_id: 1
message: "أريد الاستفسار عن منتج"
attachment: [FILE] (optional)
```
- **Response (200 OK)**:
```json
{
  "success": true,
  "data": {
    "message": {
      "id": "2",
      "message": "أريد الاستفسار عن منتج",
      "message_type": "text",
      "sender_type": "customer",
      "sender": {
        "id": "1",
        "name": "محمد أحمد",
        "full_name": "محمد أحمد علي"
      },
      "attachment_url": null,
      "attachment_name": null,
      "created_at": "2024-01-20T10:35:00Z",
      "formatted_time": "10:35"
    }
  }
}
```

### Get Chat Messages (Protected)
- **URL**: `/api/chat/{chatId}/messages`
- **Method**: `GET`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Accept: application/json`
- **Query Parameters**:
  - `per_page` (optional): Messages per page (default: 50)
  - `page` (optional): Page number (default: 1)
- **Response (200 OK)**:
```json
{
  "success": true,
  "data": {
    "messages": [
      {
        "id": "1",
        "message": "مرحباً، كيف يمكنني مساعدتك؟",
        "message_type": "text",
        "sender_type": "admin",
        "sender": {
          "id": "2",
          "name": "مدير الدعم",
          "full_name": "أحمد علي"
        },
        "attachment_url": null,
        "attachment_name": null,
        "created_at": "2024-01-20T10:30:05Z",
        "formatted_time": "10:30",
        "is_read": true
      },
      {
        "id": "2",
        "message": "أريد الاستفسار عن منتج",
        "message_type": "text",
        "sender_type": "customer",
        "sender": {
          "id": "1",
          "name": "محمد أحمد",
          "full_name": "محمد أحمد علي"
        },
        "attachment_url": null,
        "attachment_name": null,
        "created_at": "2024-01-20T10:35:00Z",
        "formatted_time": "10:35",
        "is_read": true
      }
    ],
    "pagination": {
      "current_page": 1,
      "last_page": 1,
      "per_page": 50,
      "total": 2,
      "has_more": false
    }
  }
}
```

### Get Chat History (Protected)
- **URL**: `/api/chat/history`
- **Method**: `GET`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Accept: application/json`
- **Query Parameters**:
  - `per_page` (optional): Chats per page (default: 20)
  - `page` (optional): Page number (default: 1)
- **Response (200 OK)**:
```json
{
  "success": true,
  "data": {
    "chats": [
      {
        "id": "1",
        "subject": "دعم عام",
        "status": "open",
        "priority": "medium",
        "created_at": "2024-01-20T10:30:00Z",
        "last_message_at": "2024-01-20T10:35:00Z",
        "unread_count": 1,
        "assigned_admin": {
          "id": "2",
          "name": "مدير الدعم",
          "full_name": "أحمد علي"
        },
        "latest_message": {
          "message": "أريد الاستفسار عن منتج",
          "sender_type": "customer",
          "created_at": "2024-01-20T10:35:00Z"
        }
      },
      {
        "id": "2",
        "subject": "استفسار عن طلب",
        "status": "closed",
        "priority": "low",
        "created_at": "2024-01-15T09:00:00Z",
        "last_message_at": "2024-01-15T11:00:00Z",
        "unread_count": 0,
        "assigned_admin": {
          "id": "3",
          "name": "مدير المبيعات",
          "full_name": "محمود علي"
        },
        "latest_message": {
          "message": "شكراً لك، تم حل المشكلة",
          "sender_type": "customer",
          "created_at": "2024-01-15T11:00:00Z"
        }
      }
    ],
    "pagination": {
      "current_page": 1,
      "last_page": 1,
      "per_page": 20,
      "total": 2
    }
  }
}
```

### Mark Messages as Read (Protected)
- **URL**: `/api/chat/{chatId}/read`
- **Method**: `POST`
- **Headers**:
  - `Authorization: Bearer {access_token}`
  - `Accept: application/json`
- **Response (200 OK)**:
```json
{
  "success": true,
  "message": "Messages marked as read successfully."
}
```

## Error Responses

### Unauthorized (401)
```json
{
  "success": false,
  "error": {
    "message": "Unauthorized. Customer access required.",
    "timestamp": "2024-01-20T14:30:00Z"
  }
}
```

### Validation Error (422)
```json
{
  "success": false,
  "error": {
    "message": "Validation failed",
    "errors": {
      "chat_id": [
        "The chat id field is required."
      ],
      "message": [
        "The message field is required when attachment is not present."
      ]
    }
  }
}
```

### Forbidden (403)
```json
{
  "success": false,
  "error": {
    "message": "Unauthorized to send message in this chat.",
    "timestamp": "2024-01-20T14:30:00Z"
  }
}
```
