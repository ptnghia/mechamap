# 📚 API Documentation - Laravel Forum Professional

## 📋 Tổng Quan

Đây là tài liệu API hoàn chỉnh cho hệ thống Laravel Forum - Mechamap. API được thiết kế theo chuẩn RESTful với versioning v1, sử dụng Laravel Sanctum cho authentication và có hỗ trợ CORS đầy đủ.

## 🌐 Base URLs

- **Production**: `https://mechamap.com/api/v1`
- **Staging**: `https://staging.mechamap.com/api/v1`
- **Development**: `http://localhost:8000/api/v1`

## 🔑 Authentication

### Loại Authentication
- **Sanctum Token**: Cho web và mobile API
- **Session**: Cho web application

### Headers Required
```http
Authorization: Bearer {access_token}
Accept: application/json
Content-Type: application/json
```

### CORS Test Endpoint
```http
GET /cors-test
```
**Response**:
```json
{
  "success": true,
  "message": "CORS test successful",
  "origin": "https://your-domain.com",
  "allowed_origins": ["https://mechamap.com", "https://www.mechamap.com"]
}
```

---

## 🔐 Authentication Endpoints

### 1. Đăng Nhập (Login)
```http
POST /auth/login
```

**Body Parameters**:
```json
{
  "email": "user@example.com",           // hoặc dùng username
  "username": "username",                // thay thế cho email
  "password": "password123",
  "remember_me": true                    // optional
}
```

**Success Response (200)**:
```json
{
  "success": true,
  "message": "Đăng nhập thành công.",
  "data": {
    "user": {
      "id": 1,
      "name": "Nguyễn Văn A",
      "username": "nguyen_van_a",
      "email": "user@example.com",
      "role": "member",
      "avatar_url": "https://...",
      "created_at": "2024-01-15T10:30:00.000000Z"
    },
    "tokens": {
      "access_token": "eyJ0eXAiOiJKV1QiLCJhbG...",
      "refresh_token": "random_string_60_chars",
      "token_type": "Bearer",
      "expires_in": 525600
    }
  }
}
```

**Error Response (401)**:
```json
{
  "success": false,
  "message": "Thông tin đăng nhập không chính xác."
}
```

### 2. Đăng Ký (Register)
```http
POST /auth/register
```

**Body Parameters**:
```json
{
  "name": "Nguyễn Văn A",
  "username": "nguyen_van_a",
  "email": "user@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "terms_accepted": true
}
```

**Success Response (200)**:
```json
{
  "success": true,
  "message": "Đăng ký thành công.",
  "data": {
    "user": {...},
    "tokens": {...}
  }
}
```

### 3. Đăng Xuất (Logout)
```http
POST /auth/logout
```
**Headers**: `Authorization: Bearer {token}`

**Success Response (200)**:
```json
{
  "success": true,
  "message": "Đăng xuất thành công."
}
```

### 4. Quên Mật Khẩu (Forgot Password)
```http
POST /auth/forgot-password
```

**Body Parameters**:
```json
{
  "email": "user@example.com"
}
```

### 5. Đặt Lại Mật Khẩu (Reset Password)
```http
POST /auth/reset-password
```

**Body Parameters**:
```json
{
  "token": "reset_token",
  "email": "user@example.com",
  "password": "new_password",
  "password_confirmation": "new_password"
}
```

### 6. Xác Thực Email (Verify Email)
```http
POST /auth/verify-email
```

### 7. Đăng Nhập Mạng Xã Hội (Social Login)
```http
POST /auth/social/{provider}
```
**Providers**: `google`, `facebook`, `github`

### 8. Thông Tin User Hiện Tại (Me)
```http
GET /auth/me
```
**Headers**: `Authorization: Bearer {token}`

### 9. Làm Mới Token (Refresh)
```http
POST /auth/refresh
```
**Headers**: `Authorization: Bearer {token}`

---

## 👥 User Management Endpoints

### 1. Danh Sách Users
```http
GET /users
```

**Query Parameters**:
- `role` (string): Lọc theo vai trò (admin, moderator, member)
- `status` (string): Lọc theo trạng thái (active, inactive)
- `search` (string): Tìm kiếm theo tên, username, email
- `sort_by` (string): Sắp xếp theo (created_at, name, username)
- `sort_order` (string): desc, asc
- `per_page` (int): Số items mỗi trang (1-50)

**Success Response (200)**:
```json
{
  "success": true,
  "data": {
    "data": [
      {
        "id": 1,
        "name": "Nguyễn Văn A",
        "username": "nguyen_van_a",
        "email": "user@example.com",
        "role": "member",
        "status": "active",
        "avatar_url": "https://...",
        "threads_count": 15,
        "comments_count": 45,
        "created_at": "2024-01-15T10:30:00.000000Z"
      }
    ],
    "links": {...},
    "meta": {...}
  }
}
```

### 2. Chi Tiết User
```http
GET /users/{username}
```

**Success Response (200)**:
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "name": "Nguyễn Văn A",
      "username": "nguyen_van_a",
      "email": "user@example.com",
      "role": "member",
      "bio": "Mô tả về bản thân...",
      "location": "Hà Nội, Việt Nam",
      "website": "https://example.com",
      "avatar_url": "https://...",
      "threads_count": 15,
      "comments_count": 45,
      "followers_count": 10,
      "following_count": 8,
      "is_following": false,
      "joined_at": "2024-01-15T10:30:00.000000Z"
    }
  }
}
```

### 3. Threads của User
```http
GET /users/{username}/threads
```

**Query Parameters**:
- `status` (string): approved, pending, rejected
- `per_page` (int): 1-50

### 4. Comments của User
```http
GET /users/{username}/comments
```

### 5. Activities của User
```http
GET /users/{username}/activities
```

### 6. Followers của User
```http
GET /users/{username}/followers
```

### 7. Following của User
```http
GET /users/{username}/following
```

### 8. Cập Nhật Profile (Protected)
```http
PUT /users/{username}
```
**Headers**: `Authorization: Bearer {token}`

**Body Parameters**:
```json
{
  "name": "Tên mới",
  "bio": "Mô tả mới...",
  "location": "Thành phố mới",
  "website": "https://newsite.com"
}
```

### 9. Xóa User (Protected)
```http
DELETE /users/{username}
```
**Headers**: `Authorization: Bearer {token}`

### 10. Follow/Unfollow User (Protected)
```http
POST /users/{username}/follow
DELETE /users/{username}/follow
```
**Headers**: `Authorization: Bearer {token}`

### 11. Cập Nhật Avatar (Protected)
```http
POST /users/avatar
```
**Headers**: `Authorization: Bearer {token}`
**Content-Type**: `multipart/form-data`

**Body Parameters**:
```
avatar: file (image, max 5MB)
```

---

## 🏛️ Forum Management Endpoints

### 1. Danh Sách Forums
```http
GET /forums
```

**Query Parameters**:
- `parent_id` (int): Lọc forums con của forum cha
- `with_sub_forums` (boolean): Bao gồm sub-forums
- `category_id` (int): Lọc theo category

**Success Response (200)**:
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Thảo Luận Chung",
      "slug": "thao-luan-chung",
      "description": "Nơi thảo luận các chủ đề chung...",
      "parent_id": null,
      "category_id": 1,
      "order": 1,
      "threads_count": 150,
      "comments_count": 500,
      "last_thread": {
        "id": 10,
        "title": "Thread mới nhất",
        "user": {...}
      },
      "sub_forums": [...]
    }
  ]
}
```

### 2. Chi Tiết Forum
```http
GET /forums/{slug}
```

### 3. Threads của Forum
```http
GET /forums/{slug}/threads
```

**Query Parameters**:
- `status` (string): approved, pending
- `is_sticky` (boolean): Threads được ghim
- `is_locked` (boolean): Threads bị khóa
- `sort_by` (string): created_at, activity, view_count, comment_count
- `sort_order` (string): desc, asc
- `per_page` (int): 1-50

---

## 📝 Thread Management Endpoints

### 1. Danh Sách Threads
```http
GET /threads
```

**Query Parameters**:
- `forum_id` (int): Lọc theo forum
- `category_id` (int): Lọc theo category
- `user_id` (int): Lọc theo tác giả
- `status` (string): approved, pending, rejected
- `is_sticky` (boolean): Threads được ghim
- `is_locked` (boolean): Threads bị khóa
- `is_featured` (boolean): Threads nổi bật
- `search` (string): Tìm kiếm trong title và content
- `sort_by` (string): created_at, activity, view_count, comment_count
- `sort_order` (string): desc, asc
- `per_page` (int): 1-50

**Success Response (200)**:
```json
{
  "success": true,
  "data": {
    "data": [
      {
        "id": 1,
        "title": "Tiêu đề thread",
        "slug": "tieu-de-thread",
        "content": "Nội dung thread...",
        "status": "approved",
        "is_sticky": false,
        "is_locked": false,
        "is_featured": true,
        "view_count": 100,
        "comment_count": 15,
        "like_count": 25,
        "is_liked": false,
        "is_saved": false,
        "is_following": true,
        "created_at": "2024-01-15T10:30:00.000000Z",
        "updated_at": "2024-01-16T08:20:00.000000Z",
        "user": {
          "id": 1,
          "name": "Nguyễn Văn A",
          "username": "nguyen_van_a",
          "avatar_url": "https://..."
        },
        "forum": {
          "id": 1,
          "name": "Thảo Luận Chung",
          "slug": "thao-luan-chung"
        },
        "tags": [
          {
            "id": 1,
            "name": "Laravel",
            "slug": "laravel"
          }
        ]
      }
    ],
    "links": {...},
    "meta": {...}
  }
}
```

### 2. Chi Tiết Thread
```http
GET /threads/{slug}
```

### 3. Comments của Thread
```http
GET /threads/{slug}/comments
```

**Query Parameters**:
- `sort_by` (string): created_at, like_count
- `sort_order` (string): desc, asc
- `per_page` (int): 1-50

### 4. Media của Thread
```http
GET /threads/{slug}/media
```

### 5. Tags của Thread
```http
GET /threads/{slug}/tags
```

### 6. Threads Cần Phản Hồi
```http
GET /threads/need-replies
```

### 7. Tạo Thread Mới (Protected)
```http
POST /threads
```
**Headers**: `Authorization: Bearer {token}`

**Body Parameters**:
```json
{
  "title": "Tiêu đề thread mới",
  "content": "Nội dung thread...",
  "forum_id": 1,
  "tags": ["laravel", "php"],
  "is_sticky": false,
  "is_locked": false
}
```

### 8. Cập Nhật Thread (Protected)
```http
PUT /threads/{slug}
```
**Headers**: `Authorization: Bearer {token}`

### 9. Xóa Thread (Protected)
```http
DELETE /threads/{slug}
```
**Headers**: `Authorization: Bearer {token}`

### 10. Like/Unlike Thread (Protected)
```http
POST /threads/{slug}/like
DELETE /threads/{slug}/like
```
**Headers**: `Authorization: Bearer {token}`

### 11. Save/Unsave Thread (Protected)
```http
POST /threads/{slug}/save
DELETE /threads/{slug}/save
```
**Headers**: `Authorization: Bearer {token}`

### 12. Follow/Unfollow Thread (Protected)
```http
POST /threads/{slug}/follow
DELETE /threads/{slug}/follow
```
**Headers**: `Authorization: Bearer {token}`

### 13. Thêm/Xóa Tags (Protected)
```http
POST /threads/{slug}/tags
DELETE /threads/{slug}/tags
```
**Headers**: `Authorization: Bearer {token}`

**Body Parameters** (POST):
```json
{
  "tags": ["laravel", "php", "framework"]
}
```

### 14. Report Thread (Protected)
```http
POST /threads/{slug}/report
```
**Headers**: `Authorization: Bearer {token}`

### 15. Threads Đã Lưu (Protected)
```http
GET /threads/saved
```
**Headers**: `Authorization: Bearer {token}`

### 16. Threads Đang Follow (Protected)
```http
GET /threads/followed
```
**Headers**: `Authorization: Bearer {token}`

### 17. Tạo Comment cho Thread (Protected)
```http
POST /threads/{slug}/comments
```
**Headers**: `Authorization: Bearer {token}`

**Body Parameters**:
```json
{
  "content": "Nội dung comment...",
  "parent_id": 5  // optional, cho reply
}
```

---

## 💬 Comment Management Endpoints

### 1. Comments Gần Đây (Public)
```http
GET /comments/recent
```

**Query Parameters**:
- `per_page` (int): 1-50

### 2. Cập Nhật Comment (Protected)
```http
PUT /comments/{id}
```
**Headers**: `Authorization: Bearer {token}`

**Body Parameters**:
```json
{
  "content": "Nội dung comment đã chỉnh sửa..."
}
```

### 3. Xóa Comment (Protected)
```http
DELETE /comments/{id}
```
**Headers**: `Authorization: Bearer {token}`

### 4. Like/Unlike Comment (Protected)
```http
POST /comments/{id}/like
DELETE /comments/{id}/like
```
**Headers**: `Authorization: Bearer {token}`

### 5. Report Comment (Protected)
```http
POST /comments/{id}/report
```
**Headers**: `Authorization: Bearer {token}`

### 6. Replies của Comment (Protected)
```http
GET /comments/{id}/replies
```
**Headers**: `Authorization: Bearer {token}`

### 7. Tạo Reply cho Comment (Protected)
```http
POST /comments/{id}/replies
```
**Headers**: `Authorization: Bearer {token}`

---

## 🔍 Search Endpoints

### 1. Tìm Kiếm Tổng Hợp
```http
GET /search
```

**Query Parameters**:
- `query` (string, required): Từ khóa tìm kiếm (tối thiểu 2 ký tự)
- `type` (string): all, threads, forums, users
- `per_page` (int): 1-50

**Success Response (200)**:
```json
{
  "success": true,
  "data": {
    "query": "laravel",
    "results": {
      "threads": {
        "data": [...],
        "total": 25
      },
      "forums": {
        "data": [...],
        "total": 5
      },
      "users": {
        "data": [...],
        "total": 10
      }
    },
    "total_results": 40,
    "response_time_ms": 150
  }
}
```

### 2. Gợi Ý Tìm Kiếm
```http
GET /search/suggestions
```

**Query Parameters**:
- `query` (string, required): Từ khóa
- `limit` (int): Số gợi ý (mặc định 5)

---

## 🎨 Showcase Endpoints

### 1. Danh Sách Showcases (Public)
```http
GET /showcases
```

**Query Parameters**:
- `user_id` (int): Lọc theo user
- `status` (string): approved, pending (mặc định approved)
- `is_featured` (boolean): Showcases nổi bật
- `search` (string): Tìm kiếm trong title và description
- `sort_by` (string): created_at, view_count, like_count
- `sort_order` (string): desc, asc
- `per_page` (int): 1-50

### 2. Showcases Gần Đây (Public)
```http
GET /showcases/recent
```

### 3. Chi Tiết Showcase (Public)
```http
GET /showcases/{slug}
```

### 4. Tạo Showcase Mới (Protected)
```http
POST /showcases
```
**Headers**: `Authorization: Bearer {token}`
**Content-Type**: `multipart/form-data`

**Body Parameters**:
```
title: string (required)
description: string (required)
location: string (optional)
usage: string (optional)
floors: integer (optional)
cover_image: file (required, image, max 5MB)
media_ids: array (optional)
```

### 5. Cập Nhật Showcase (Protected)
```http
PUT /showcases/{slug}
```
**Headers**: `Authorization: Bearer {token}`

### 6. Xóa Showcase (Protected)
```http
DELETE /showcases/{slug}
```
**Headers**: `Authorization: Bearer {token}`

---

## 📊 Statistics Endpoints

### 1. Thống Kê Forum
```http
GET /stats/forum
```

**Success Response (200)**:
```json
{
  "success": true,
  "data": {
    "stats": {
      "total_threads": 1250,
      "total_comments": 8500,
      "total_users": 350,
      "active_users_today": 45,
      "new_threads_today": 12,
      "new_comments_today": 85
    }
  }
}
```

### 2. Forums Phổ Biến
```http
GET /stats/forums/popular
```

### 3. Users Hoạt Động
```http
GET /stats/users/active
```

### 4. Threads Nổi Bật
```http
GET /stats/threads/featured
```

---

## 🔔 Alert Endpoints (Protected)

### 1. Danh Sách Alerts
```http
GET /alerts
```
**Headers**: `Authorization: Bearer {token}`

**Query Parameters**:
- `read` (boolean): Lọc theo trạng thái đọc
- `sort_by` (string): created_at
- `sort_order` (string): desc, asc
- `per_page` (int): 1-50

### 2. Đánh Dấu Alert Đã Đọc
```http
POST /alerts/{id}/read
```
**Headers**: `Authorization: Bearer {token}`

### 3. Xóa Alert
```http
DELETE /alerts/{id}
```
**Headers**: `Authorization: Bearer {token}`

### 4. Đánh Dấu Tất Cả Đã Đọc
```http
POST /alerts/read-all
```
**Headers**: `Authorization: Bearer {token}`

---

## 💬 Conversation Endpoints (Protected)

### 1. Danh Sách Conversations
```http
GET /conversations
```
**Headers**: `Authorization: Bearer {token}`

### 2. Tạo Conversation Mới
```http
POST /conversations
```
**Headers**: `Authorization: Bearer {token}`

**Body Parameters**:
```json
{
  "recipient_id": 5,
  "subject": "Tiêu đề cuộc trò chuyện",
  "message": "Tin nhắn đầu tiên..."
}
```

### 3. Chi Tiết Conversation
```http
GET /conversations/{id}
```
**Headers**: `Authorization: Bearer {token}`

### 4. Gửi Tin Nhắn
```http
POST /conversations/{id}/messages
```
**Headers**: `Authorization: Bearer {token}`

**Body Parameters**:
```json
{
  "message": "Nội dung tin nhắn..."
}
```

### 5. Đánh Dấu Đã Đọc
```http
POST /conversations/{id}/read
```
**Headers**: `Authorization: Bearer {token}`

---

## 📁 Media Endpoints

### 1. Media Gần Đây (Public)
```http
GET /media/recent
```

### 2. Danh Sách Media của User (Protected)
```http
GET /media
```
**Headers**: `Authorization: Bearer {token}`

**Query Parameters**:
- `type` (string): Lọc theo loại file
- `sort_by` (string): created_at, file_size
- `sort_order` (string): desc, asc
- `per_page` (int): 1-50

### 3. Upload Media (Protected)
```http
POST /media
```
**Headers**: `Authorization: Bearer {token}`
**Content-Type**: `multipart/form-data`

**Body Parameters**:
```
file: file (required, max 10MB)
thread_id: integer (optional)
description: string (optional)
```

### 4. Chi Tiết Media (Protected)
```http
GET /media/{id}
```
**Headers**: `Authorization: Bearer {token}`

### 5. Cập Nhật Media (Protected)
```http
PUT /media/{id}
```
**Headers**: `Authorization: Bearer {token}`

### 6. Xóa Media (Protected)
```http
DELETE /media/{id}
```
**Headers**: `Authorization: Bearer {token}`

---

## 🏷️ Tag Endpoints

### 1. Danh Sách Tags (Public)
```http
GET /tags
```

**Query Parameters**:
- `search` (string): Tìm kiếm tag
- `popular` (boolean): Tags phổ biến
- `limit` (int): Giới hạn số lượng

### 2. Chi Tiết Tag (Public)
```http
GET /tags/{slug}
```

### 3. Threads của Tag (Public)
```http
GET /tags/{slug}/threads
```

### 4. Tạo Tag Mới (Protected - Admin)
```http
POST /tags
```
**Headers**: `Authorization: Bearer {token}`

---

## 📄 Settings & SEO Endpoints

### 1. Cài Đặt Hệ Thống
```http
GET /settings
GET /settings/{group}
```

### 2. SEO Metadata
```http
GET /seo
GET /seo/{group}
GET /page-seo/{routeName}
GET /page-seo/url/{urlPattern}
```

### 3. SEO cho các trang cụ thể
```http
GET /seo/pages/{slug}
GET /seo/threads/{slug}
GET /seo/forums/{slug}
GET /seo/categories/{slug}
GET /seo/users/{username}
```

### 4. Favicon
```http
GET /favicon
```

---

## 🎭 Avatar Generator

### 1. Tạo Avatar Động
```http
GET /avatar
```

**Query Parameters**:
- `name` (string): Tên để tạo avatar
- `size` (int): Kích thước (default 150)
- `background` (string): Màu nền hex
- `color` (string): Màu chữ hex

---

## 📰 Article Endpoints

### 1. Bài Viết Gần Đây
```http
GET /articles/recent
```

**Query Parameters**:
- `per_page` (int): 1-50

---

## 🛡️ Admin Endpoints (Protected - Admin Role)

### 1. Quản Lý Showcases
```http
GET /admin/showcases
POST /admin/showcases/add
DELETE /admin/showcases/{id}
```

### 2. Quản Lý Reports
```http
GET /admin/reports
PUT /admin/reports/{id}
```

---

## 📋 Categories Endpoints

### 1. Danh Sách Categories
```http
GET /categories
```

### 2. Chi Tiết Category
```http
GET /categories/{slug}
```

### 3. Forums của Category
```http
GET /categories/{slug}/forums
```

---

## ⚠️ Error Handling

### Mã Lỗi Chung
- **200**: Thành công
- **201**: Tạo mới thành công
- **400**: Bad Request
- **401**: Unauthorized
- **403**: Forbidden
- **404**: Not Found
- **422**: Validation Error
- **429**: Too Many Requests
- **500**: Server Error

### Format Lỗi Chuẩn
```json
{
  "success": false,
  "message": "Thông báo lỗi",
  "errors": {
    "field": ["Chi tiết lỗi validation"]
  },
  "error_code": "VALIDATION_ERROR"
}
```

---

## 🚦 Rate Limiting

- **Authentication**: 60 requests/minute
- **General API**: 1000 requests/hour
- **Search**: 100 requests/minute
- **Upload**: 10 requests/minute

---

## 📊 Pagination

### Format Response
```json
{
  "data": [...],
  "links": {
    "first": "...",
    "last": "...",
    "prev": null,
    "next": "..."
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 10,
    "path": "...",
    "per_page": 15,
    "to": 15,
    "total": 150
  }
}
```

---

## 🔧 Testing & Development

### Test Endpoints (Development Only)
```http
GET /test/add-to-showcase
GET /test/add-single-to-showcase
```

---

## 📞 Support & Contact

- **Email**: support@mechamap.com
- **Documentation**: https://mechamap.com/api/docs
- **GitHub**: https://github.com/mechamap/laravel-forum
- **API Status**: https://status.mechamap.com

---

## 📋 Changelog

### Version 1.0.0 (2024-01-15)
- Initial API release
- Complete authentication system
- Full CRUD operations for all entities
- Advanced search functionality
- Media management
- Real-time notifications
- Admin management endpoints

---

**Cập nhật lần cuối**: 15/01/2024
**Phiên bản API**: v1.0.0
**Tác giả**: Team Mechamap Development
