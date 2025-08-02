# MechaMap Unified Search API Documentation

## Overview

The Unified Search API provides a comprehensive search functionality across all content types in MechaMap platform, including threads, showcases, marketplace products, and users.

## Endpoint

```
GET /api/v1/search/unified
```

## Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `q` | string | Yes | Search query string |
| `per_category` | integer | No | Results per category (default: 5) |

## Response Format

```json
{
  "success": true,
  "message": "Yêu cầu thành công",
  "data": {
    "success": true,
    "results": {
      "threads": [...],
      "showcases": [...],
      "products": [...],
      "users": [...],
      "meta": {
        "query": "search_term",
        "total": 10,
        "categories": {
          "threads": 3,
          "showcases": 2,
          "products": 3,
          "users": 2
        }
      }
    },
    "advanced_search_url": "https://mechamap.test/forums/search/advanced?q=search_term"
  },
  "meta": {
    "timestamp": "2025-08-02T08:48:35.557714Z",
    "api_version": "v1",
    "status_code": 200
  }
}
```

## Content Types

### 1. Threads (Forum Posts)

**Structure:**
```json
{
  "id": 123,
  "type": "thread",
  "title": "Thread Title",
  "excerpt": "Thread content preview...",
  "url": "https://mechamap.test/threads/thread-slug-123",
  "author": {
    "name": "Author Name",
    "avatar": "https://mechamap.test/images/users/avatars/avatar.jpg"
  },
  "forum": {
    "name": "Forum Name",
    "url": "https://mechamap.test/forums/forum-slug"
  },
  "stats": {
    "views": 150,
    "replies": 5
  },
  "created_at": "2 tuần trước"
}
```

### 2. Showcases (Project Portfolios)

**Structure:**
```json
{
  "id": 456,
  "type": "showcase",
  "title": "Project Title",
  "excerpt": "Project description...",
  "url": "https://mechamap.test/showcase/456",
  "author": {
    "name": "Author Name",
    "avatar": "https://mechamap.test/images/users/avatars/avatar.jpg"
  },
  "category": "design",
  "rating": {
    "average": 4.2,
    "count": 5
  },
  "stats": {
    "views": 521,
    "downloads": 19
  },
  "image": "https://mechamap.test/storage/images/showcase/project.jpg",
  "created_at": "1 tháng trước"
}
```

### 3. Products (Marketplace Items)

**Structure:**
```json
{
  "id": 789,
  "type": "marketplace_product",
  "title": "Product Name",
  "excerpt": "Product description...",
  "url": "https://mechamap.test/marketplace/products/product-slug",
  "seller": {
    "name": "Seller Name",
    "avatar": "https://mechamap.test/images/users/avatars/avatar.jpg"
  },
  "price": {
    "amount": "736.00",
    "currency": "USD",
    "formatted": "$736.00"
  },
  "image": "https://mechamap.test/storage/images/products/product.jpg",
  "stats": {
    "views": 656,
    "purchases": 58
  },
  "created_at": "1 tháng trước"
}
```

### 4. Users (Member Profiles)

**Structure:**
```json
{
  "id": 101,
  "type": "user",
  "name": "User Name",
  "username": "username",
  "company_name": "Company Name",
  "role": "member",
  "avatar": "https://mechamap.test/images/users/avatars/avatar.jpg",
  "url": "https://mechamap.test/users/username",
  "stats": {
    "threads": 10,
    "posts": 25
  }
}
```

## Search Patterns

### 1. General Search
Search across all content types:
```
GET /api/v1/search/unified?q=thiết kế
```

### 2. User Search (@ Prefix)
Search specifically for users using @ prefix:
```
GET /api/v1/search/unified?q=@admin
```

**Behavior:**
- Only returns users when query starts with `@`
- Removes `@` prefix before searching
- Searches in: name, username, company_name fields

### 3. Alphanumeric Username Search
Automatic user search for short alphanumeric queries:
```
GET /api/v1/search/unified?q=member01
```

**Criteria:**
- Query matches pattern: `^[a-zA-Z0-9_]+$`
- Length between 3-15 characters
- Automatically includes user results

## Performance

- **Target Response Time:** < 500ms
- **Typical Response Time:** 300-350ms
- **Rate Limiting:** Not implemented (consider for production)
- **Caching:** Database query optimization

## Error Handling

### Success Response
```json
{
  "success": true,
  "message": "Yêu cầu thành công",
  "data": {...}
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error message",
  "data": null,
  "meta": {
    "timestamp": "2025-08-02T08:48:35.557714Z",
    "api_version": "v1",
    "status_code": 400
  }
}
```

## Usage Examples

### Frontend Integration (JavaScript)

```javascript
// Basic search
async function searchContent(query) {
  try {
    const response = await fetch(`/api/v1/search/unified?q=${encodeURIComponent(query)}`);
    const data = await response.json();
    
    if (data.success) {
      displayResults(data.data.results);
    }
  } catch (error) {
    console.error('Search error:', error);
  }
}

// User search with @ prefix
async function searchUsers(username) {
  const query = username.startsWith('@') ? username : `@${username}`;
  return searchContent(query);
}
```

### cURL Examples

```bash
# General search
curl "http://mechamap.test/api/v1/search/unified?q=thiết%20kế"

# User search
curl "http://mechamap.test/api/v1/search/unified?q=@admin"

# Custom results per category
curl "http://mechamap.test/api/v1/search/unified?q=hydraulic&per_category=10"
```

## Implementation Notes

### Database Queries
- Uses `LIKE` queries with proper escaping
- Searches multiple fields per content type
- Implements relevance-based ordering
- Limits results per category for performance

### Security
- Input sanitization for SQL injection prevention
- XSS protection in response data
- Rate limiting recommended for production

### Localization
- Supports Vietnamese and English content
- Localized response messages
- Date formatting in Vietnamese

## Related Documentation

- [Forum Search API](./forum-search.md)
- [Marketplace API](./marketplace.md)
- [User Profile API](./users.md)
- [Frontend Search Component](../frontend/search-component.md)
