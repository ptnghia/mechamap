# Thread Quality API Documentation

## Tổng Quan
Thread Quality API cung cấp các endpoint để đánh giá (rating) và bookmark threads trong hệ thống forum.

## Authentication
Tất cả endpoints đều yêu cầu authentication thông qua Bearer token:
```
Authorization: Bearer {your-token}
```

## Base URL
```
{domain}/api/threads/{thread-slug}
```

---

## 📊 Rating Endpoints

### 1. Đánh giá Thread
**POST** `/api/threads/{slug}/rate`

Tạo hoặc cập nhật đánh giá cho thread.

**Request Body:**
```json
{
  "rating": 4  // Required: 1-5
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Rating đã được cập nhật",
  "data": {
    "rating": 4,
    "thread_stats": {
      "average_rating": 3.8,
      "rating_count": 8
    }
  }
}
```

**Validation Rules:**
- `rating`: required|integer|min:1|max:5

---

### 2. Xóa Rating
**DELETE** `/api/threads/{slug}/rate`

Xóa rating của user cho thread.

**Response (200):**
```json
{
  "success": true,
  "message": "Rating đã được xóa",
  "data": {
    "thread_stats": {
      "average_rating": 3.7,
      "rating_count": 7
    }
  }
}
```

---

### 3. Lấy Rating của User
**GET** `/api/threads/{slug}/rating`

Lấy rating hiện tại của user cho thread này.

**Response (200):**
```json
{
  "success": true,
  "data": {
    "rating": 4,
    "created_at": "2024-01-15T10:30:00Z",
    "updated_at": "2024-01-16T14:20:00Z"
  }
}
```

**Response (404) - Chưa rating:**
```json
{
  "success": false,
  "message": "Bạn chưa đánh giá thread này"
}
```

---

### 4. Lấy Tất Cả Ratings của Thread
**GET** `/api/threads/{slug}/ratings`

Lấy danh sách ratings của thread (có phân trang).

**Query Parameters:**
- `page`: số trang (default: 1)
- `per_page`: số items per page (default: 20, max: 100)
- `sort`: sắp xếp theo 'newest', 'oldest', 'highest', 'lowest'

**Response (200):**
```json
{
  "success": true,
  "data": {
    "ratings": {
      "data": [
        {
          "id": 123,
          "rating": 5,
          "created_at": "2024-01-15T10:30:00Z",
          "user": {
            "id": 45,
            "name": "John Doe",
            "avatar": "https://example.com/avatar.jpg"
          }
        }
      ],
      "current_page": 1,
      "last_page": 3,
      "per_page": 20,
      "total": 42
    },
    "stats": {
      "average_rating": 3.8,
      "rating_count": 42,
      "distribution": {
        "5_star": 15,
        "4_star": 12,
        "3_star": 8,
        "2_star": 4,
        "1_star": 3
      }
    }
  }
}
```

---

## 🔖 Bookmark Endpoints

### 1. Bookmark Thread
**POST** `/api/threads/{slug}/bookmark`

Thêm thread vào bookmarks của user.

**Request Body:**
```json
{
  "folder": "Laravel Tips"  // Optional: tên folder
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Thread đã được bookmark",
  "data": {
    "bookmark": {
      "id": 789,
      "folder": "Laravel Tips",
      "created_at": "2024-01-15T10:30:00Z"
    }
  }
}
```

**Response (422) - Đã bookmark:**
```json
{
  "success": false,
  "message": "Thread đã được bookmark trước đó"
}
```

---

### 2. Xóa Bookmark
**DELETE** `/api/threads/{slug}/bookmark`

Xóa bookmark của user cho thread.

**Response (200):**
```json
{
  "success": true,
  "message": "Bookmark đã được xóa"
}
```

**Response (404) - Không tìm thấy bookmark:**
```json
{
  "success": false,
  "message": "Bookmark không tồn tại"
}
```

---

### 3. Cập Nhật Bookmark
**PUT** `/api/threads/{slug}/bookmark`

Cập nhật thông tin bookmark (folder).

**Request Body:**
```json
{
  "folder": "Advanced Laravel"
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Bookmark đã được cập nhật",
  "data": {
    "bookmark": {
      "id": 789,
      "folder": "Advanced Laravel",
      "updated_at": "2024-01-16T14:20:00Z"
    }
  }
}
```

---

### 4. Kiểm Tra Bookmark Status
**GET** `/api/threads/{slug}/bookmark`

Kiểm tra xem user đã bookmark thread này chưa.

**Response (200) - Đã bookmark:**
```json
{
  "success": true,
  "data": {
    "bookmarked": true,
    "bookmark": {
      "id": 789,
      "folder": "Laravel Tips",
      "created_at": "2024-01-15T10:30:00Z"
    }
  }
}
```

**Response (200) - Chưa bookmark:**
```json
{
  "success": true,
  "data": {
    "bookmarked": false
  }
}
```

---

## 👤 User Bookmark Endpoints

### 1. Lấy Bookmarks của User
**GET** `/api/user/bookmarks`

Lấy danh sách bookmarks của user hiện tại.

**Query Parameters:**
- `page`: số trang (default: 1)
- `per_page`: số items per page (default: 20, max: 100)
- `folder`: lọc theo folder
- `search`: tìm kiếm theo title thread
- `sort`: sắp xếp theo 'newest', 'oldest', 'title'

**Response (200):**
```json
{
  "success": true,
  "data": {
    "bookmarks": {
      "data": [
        {
          "id": 789,
          "folder": "Laravel Tips",
          "created_at": "2024-01-15T10:30:00Z",
          "thread": {
            "id": 123,
            "title": "Advanced Laravel Techniques",
            "slug": "advanced-laravel-techniques",
            "excerpt": "Learn advanced Laravel development...",
            "view_count": 1250,
            "comment_count": 23,
            "average_rating": 4.2,
            "rating_count": 15,
            "user": {
              "id": 45,
              "name": "John Doe",
              "avatar": "https://example.com/avatar.jpg"
            },
            "created_at": "2024-01-10T08:00:00Z"
          }
        }
      ],
      "current_page": 1,
      "last_page": 2,
      "per_page": 20,
      "total": 37
    },
    "folders": [
      {
        "name": "Laravel Tips",
        "count": 15
      },
      {
        "name": "Vue.js Tutorials",
        "count": 8
      },
      {
        "name": "General",
        "count": 14
      }
    ]
  }
}
```

---

### 2. Lấy Bookmark Folders
**GET** `/api/user/bookmark-folders`

Lấy danh sách folders bookmark của user.

**Response (200):**
```json
{
  "success": true,
  "data": {
    "folders": [
      {
        "name": "Laravel Tips",
        "count": 15,
        "created_at": "2024-01-10T08:00:00Z",
        "latest_bookmark": "2024-01-15T10:30:00Z"
      },
      {
        "name": "Vue.js Tutorials", 
        "count": 8,
        "created_at": "2024-01-12T14:20:00Z",
        "latest_bookmark": "2024-01-14T16:45:00Z"
      }
    ],
    "total_bookmarks": 37,
    "total_folders": 5
  }
}
```

---

## ❌ Error Responses

### Validation Errors (422)
```json
{
  "success": false,
  "message": "The given data was invalid.",
  "errors": {
    "rating": ["Rating phải là số từ 1 đến 5"]
  }
}
```

### Authentication Error (401)
```json
{
  "success": false,
  "message": "Unauthenticated"
}
```

### Not Found (404)
```json
{
  "success": false,
  "message": "Thread không tồn tại"
}
```

### Server Error (500)
```json
{
  "success": false,
  "message": "Có lỗi xảy ra, vui lòng thử lại sau"
}
```

---

## 📝 Notes

1. **Rate Limiting**: Các endpoints có rate limiting 60 requests/minute per user
2. **Slug Format**: Thread slug phải là format kebab-case (ví dụ: 'advanced-laravel-techniques')
3. **Folder Names**: Bookmark folder names có thể chứa unicode, tối đa 100 ký tự
4. **Caching**: Thread ratings và bookmark counts được cache 5 phút
5. **Soft Deletes**: Bookmarks sử dụng soft deletes, có thể restore trong 30 ngày

---

## 🔧 Development Testing

### Using cURL
```bash
# Rate thread
curl -X POST "http://localhost:8000/api/threads/advanced-laravel-techniques/rate" \
  -H "Authorization: Bearer your-token" \
  -H "Content-Type: application/json" \
  -d '{"rating": 4}'

# Bookmark thread  
curl -X POST "http://localhost:8000/api/threads/advanced-laravel-techniques/bookmark" \
  -H "Authorization: Bearer your-token" \
  -H "Content-Type: application/json" \
  -d '{"folder": "Laravel Tips"}'

# Get user bookmarks
curl -X GET "http://localhost:8000/api/user/bookmarks?folder=Laravel Tips" \
  -H "Authorization: Bearer your-token"
```

### Using PHP Test Script
Bạn có thể sử dụng script `test_quality_api.php` đã được tạo để test toàn bộ endpoints.
