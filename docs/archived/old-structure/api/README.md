# 🔌 MechaMap API Documentation

> **API Version**: v1  
> **Base URL**: `https://mechamap.test/api/v1`  
> **Authentication**: Bearer Token (Laravel Sanctum)  
> **Rate Limit**: 1000 requests/hour per user

---

## 🚀 **QUICK START**

### **🔑 Authentication**

```bash
# 1. Login to get access token
curl -X POST https://mechamap.test/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com",
    "password": "password"
  }'

# Response
{
  "success": true,
  "data": {
    "token": "1|abc123...",
    "user": {
      "id": 1,
      "username": "john_doe",
      "email": "user@example.com",
      "role": "member"
    }
  }
}

# 2. Use token in subsequent requests
curl -X GET https://mechamap.test/api/v1/threads \
  -H "Authorization: Bearer 1|abc123..."
```

---

## 📚 **API ENDPOINTS OVERVIEW**

### **🔐 Authentication Endpoints**
| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| `POST` | `/auth/login` | User login | ❌ |
| `POST` | `/auth/register` | User registration | ❌ |
| `POST` | `/auth/logout` | User logout | ✅ |
| `POST` | `/auth/refresh` | Refresh token | ✅ |
| `GET` | `/auth/me` | Current user info | ✅ |
| `POST` | `/auth/forgot-password` | Password reset | ❌ |
| `POST` | `/auth/reset-password` | Reset password | ❌ |

### **💬 Forum & Content Endpoints**
| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| `GET` | `/categories` | List categories | ❌ |
| `GET` | `/forums` | List forums | ❌ |
| `GET` | `/threads` | List threads | ❌ |
| `POST` | `/threads` | Create thread | ✅ |
| `GET` | `/threads/{id}` | Thread details | ❌ |
| `PUT` | `/threads/{id}` | Update thread | ✅ |
| `DELETE` | `/threads/{id}` | Delete thread | ✅ |
| `POST` | `/threads/{id}/comments` | Add comment | ✅ |
| `GET` | `/threads/{id}/comments` | List comments | ❌ |

### **🏪 Marketplace Endpoints**
| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| `GET` | `/products` | List products | ❌ |
| `POST` | `/products` | Create product | ✅ |
| `GET` | `/products/{id}` | Product details | ❌ |
| `PUT` | `/products/{id}` | Update product | ✅ |
| `DELETE` | `/products/{id}` | Delete product | ✅ |
| `POST` | `/products/{id}/reviews` | Add review | ✅ |
| `GET` | `/cart` | Get cart items | ✅ |
| `POST` | `/cart/add` | Add to cart | ✅ |
| `POST` | `/orders` | Create order | ✅ |
| `GET` | `/orders` | List user orders | ✅ |

### **👥 User Management Endpoints**
| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| `GET` | `/users` | List users | ✅ |
| `GET` | `/users/{id}` | User profile | ❌ |
| `PUT` | `/users/{id}` | Update profile | ✅ |
| `POST` | `/users/{id}/follow` | Follow user | ✅ |
| `DELETE` | `/users/{id}/follow` | Unfollow user | ✅ |
| `GET` | `/users/{id}/followers` | User followers | ❌ |
| `GET` | `/users/{id}/following` | User following | ❌ |

### **🔍 Search Endpoints**
| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| `GET` | `/search` | Global search | ❌ |
| `GET` | `/search/threads` | Search threads | ❌ |
| `GET` | `/search/products` | Search products | ❌ |
| `GET` | `/search/users` | Search users | ❌ |
| `GET` | `/search/suggestions` | Search suggestions | ❌ |

---

## 📝 **REQUEST/RESPONSE FORMAT**

### **✅ Success Response**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "Sample Thread",
    "content": "Thread content...",
    "author": {
      "id": 1,
      "username": "john_doe",
      "avatar": "https://example.com/avatar.jpg"
    },
    "created_at": "2025-01-15T10:30:00Z",
    "updated_at": "2025-01-15T10:30:00Z"
  },
  "meta": {
    "current_page": 1,
    "total": 100,
    "per_page": 15,
    "last_page": 7
  },
  "message": "Success"
}
```

### **❌ Error Response**
```json
{
  "success": false,
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "The given data was invalid.",
    "details": {
      "title": ["The title field is required."],
      "content": ["The content field is required."]
    }
  },
  "timestamp": "2025-01-15T10:30:00Z"
}
```

### **📊 Pagination Response**
```json
{
  "success": true,
  "data": [...],
  "meta": {
    "current_page": 1,
    "from": 1,
    "to": 15,
    "total": 100,
    "per_page": 15,
    "last_page": 7,
    "path": "https://mechamap.test/api/v1/threads",
    "first_page_url": "https://mechamap.test/api/v1/threads?page=1",
    "last_page_url": "https://mechamap.test/api/v1/threads?page=7",
    "next_page_url": "https://mechamap.test/api/v1/threads?page=2",
    "prev_page_url": null
  }
}
```

---

## 🔐 **AUTHENTICATION**

### **🎫 Bearer Token Authentication**
```bash
# Include token in Authorization header
Authorization: Bearer {your-token-here}

# Example request
curl -X GET https://mechamap.test/api/v1/threads \
  -H "Authorization: Bearer 1|abc123..." \
  -H "Accept: application/json"
```

### **🔄 Token Refresh**
```bash
# Refresh expired token
curl -X POST https://mechamap.test/api/v1/auth/refresh \
  -H "Authorization: Bearer {expired-token}" \
  -H "Accept: application/json"
```

### **🚪 Logout**
```bash
# Revoke current token
curl -X POST https://mechamap.test/api/v1/auth/logout \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

---

## 📊 **RATE LIMITING**

### **🚦 Rate Limits**
| User Type | Requests/Hour | Burst Limit |
|-----------|---------------|-------------|
| **Guest** | 100 | 10/minute |
| **Member** | 1000 | 60/minute |
| **Premium** | 5000 | 300/minute |
| **API Key** | 10000 | 600/minute |

### **📈 Rate Limit Headers**
```http
X-RateLimit-Limit: 1000
X-RateLimit-Remaining: 999
X-RateLimit-Reset: 1642694400
Retry-After: 3600
```

### **⚠️ Rate Limit Exceeded**
```json
{
  "success": false,
  "error": {
    "code": "RATE_LIMIT_EXCEEDED",
    "message": "Too many requests. Please try again later.",
    "retry_after": 3600
  }
}
```

---

## 🔍 **FILTERING & SORTING**

### **🔎 Query Parameters**
```bash
# Pagination
GET /api/v1/threads?page=2&per_page=20

# Filtering
GET /api/v1/threads?category=mechanical&status=published

# Sorting
GET /api/v1/threads?sort=created_at&order=desc

# Search
GET /api/v1/threads?search=engineering&author=john_doe

# Multiple filters
GET /api/v1/products?category=tools&price_min=100&price_max=500&sort=price&order=asc
```

### **📋 Available Filters**

#### **Threads**
- `category` - Filter by category slug
- `author` - Filter by author username
- `status` - published, draft, pending
- `featured` - true/false
- `search` - Search in title and content

#### **Products**
- `category` - Product category
- `seller` - Seller username
- `price_min` - Minimum price
- `price_max` - Maximum price
- `rating_min` - Minimum rating
- `in_stock` - true/false

#### **Users**
- `role` - User role
- `status` - active, inactive, banned
- `verified` - true/false
- `location` - User location

---

## 📤 **FILE UPLOADS**

### **📁 Upload Endpoint**
```bash
# Upload file
curl -X POST https://mechamap.test/api/v1/upload \
  -H "Authorization: Bearer {token}" \
  -F "file=@/path/to/file.jpg" \
  -F "type=avatar"
```

### **📋 Upload Types**
- `avatar` - User avatar (max 2MB, jpg/png)
- `attachment` - Thread attachment (max 10MB)
- `product_image` - Product image (max 5MB, jpg/png)
- `document` - Document file (max 20MB, pdf/doc)

### **✅ Upload Response**
```json
{
  "success": true,
  "data": {
    "id": 123,
    "filename": "avatar.jpg",
    "url": "https://mechamap.test/storage/uploads/avatar.jpg",
    "size": 1024000,
    "mime_type": "image/jpeg"
  }
}
```

---

## 🔔 **WEBHOOKS**

### **📡 Webhook Events**
- `user.registered` - New user registration
- `thread.created` - New thread posted
- `product.created` - New product listed
- `order.completed` - Order completed
- `payment.received` - Payment received

### **🔧 Webhook Configuration**
```bash
# Register webhook
curl -X POST https://mechamap.test/api/v1/webhooks \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "url": "https://your-site.com/webhook",
    "events": ["user.registered", "order.completed"],
    "secret": "your-webhook-secret"
  }'
```

---

## 🆘 **ERROR CODES**

| Code | HTTP Status | Description |
|------|-------------|-------------|
| `VALIDATION_ERROR` | 422 | Request validation failed |
| `UNAUTHORIZED` | 401 | Authentication required |
| `FORBIDDEN` | 403 | Insufficient permissions |
| `NOT_FOUND` | 404 | Resource not found |
| `RATE_LIMIT_EXCEEDED` | 429 | Too many requests |
| `SERVER_ERROR` | 500 | Internal server error |
| `MAINTENANCE_MODE` | 503 | System maintenance |

---

## 📚 **ADDITIONAL RESOURCES**

- **[Authentication Guide](./AUTHENTICATION.md)** - Detailed auth documentation
- **[Rate Limiting](./RATE_LIMITING.md)** - Rate limiting policies
- **[Error Handling](./ERROR_HANDLING.md)** - Error handling guide
- **[Webhooks](./WEBHOOKS.md)** - Webhook integration
- **[Postman Collection](./postman/)** - Ready-to-use API collection

---

**📞 API Support**: api-support@mechamap.com | **📖 Interactive Docs**: [api.mechamap.com](https://api.mechamap.com)
