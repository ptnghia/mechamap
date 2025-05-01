# Tài liệu API MechaMap

## Giới thiệu

Tài liệu này mô tả chi tiết các API của MechaMap để phát triển frontend bằng Next.js. API được thiết kế theo kiến trúc RESTful và sử dụng JSON làm định dạng dữ liệu.

## Cấu hình cơ bản

-   **Base URL**: `https://api.mechamap.com/api/v1`
-   **Content-Type**: `application/json`
-   **Xác thực**: Bearer Token (Laravel Sanctum)

## Cấu trúc phản hồi

Tất cả các API đều trả về cấu trúc phản hồi thống nhất:

```json
{
  "success": true,
  "data": { ... },
  "message": "Thông báo thành công",
  "errors": null
}
```

Trong trường hợp lỗi:

```json
{
    "success": false,
    "data": null,
    "message": "Thông báo lỗi",
    "errors": {
        "field": ["Lỗi chi tiết"]
    }
}
```

## Mã trạng thái HTTP

-   `200 OK`: Yêu cầu thành công
-   `201 Created`: Tạo mới thành công
-   `400 Bad Request`: Yêu cầu không hợp lệ
-   `401 Unauthorized`: Chưa xác thực
-   `403 Forbidden`: Không có quyền truy cập
-   `404 Not Found`: Không tìm thấy tài nguyên
-   `422 Unprocessable Entity`: Dữ liệu không hợp lệ
-   `500 Internal Server Error`: Lỗi máy chủ

## API Endpoints

### Xác thực

#### Đăng ký tài khoản

```
POST /auth/register
```

**Tham số:**

| Tên                   | Kiểu   | Bắt buộc | Mô tả                                         |
| --------------------- | ------ | -------- | --------------------------------------------- |
| name                  | string | Có       | Tên người dùng                                |
| username              | string | Có       | Tên đăng nhập (không dấu, không khoảng trắng) |
| email                 | string | Có       | Email                                         |
| password              | string | Có       | Mật khẩu (tối thiểu 8 ký tự)                  |
| password_confirmation | string | Có       | Xác nhận mật khẩu                             |

**Phản hồi:**

```json
{
    "success": true,
    "data": {
        "user": {
            "id": 1,
            "name": "Nguyễn Văn A",
            "username": "nguyenvana",
            "email": "nguyenvana@example.com",
            "role": "member",
            "created_at": "2025-05-01T10:00:00.000000Z"
        },
        "token": "1|abcdef123456..."
    },
    "message": "Đăng ký thành công",
    "errors": null
}
```

#### Đăng nhập

```
POST /auth/login
```

**Tham số:**

| Tên      | Kiểu    | Bắt buộc | Mô tả               |
| -------- | ------- | -------- | ------------------- |
| login    | string  | Có       | Email hoặc username |
| password | string  | Có       | Mật khẩu            |
| remember | boolean | Không    | Ghi nhớ đăng nhập   |

**Phản hồi:**

```json
{
    "success": true,
    "data": {
        "user": {
            "id": 1,
            "name": "Nguyễn Văn A",
            "username": "nguyenvana",
            "email": "nguyenvana@example.com",
            "role": "member",
            "avatar": "https://api.mechamap.com/storage/avatars/1.jpg",
            "created_at": "2025-05-01T10:00:00.000000Z"
        },
        "token": "1|abcdef123456..."
    },
    "message": "Đăng nhập thành công",
    "errors": null
}

#### Đăng nhập bằng Google

```

POST /auth/google

```

**Tham số:**

| Tên   | Kiểu   | Bắt buộc | Mô tả              |
| ----- | ------ | -------- | ------------------ |
| token | string | Có       | Google OAuth token |

**Phản hồi:** Tương tự như đăng nhập thông thường

#### Đăng nhập bằng Facebook

```

POST /auth/facebook

```

**Tham số:**

| Tên   | Kiểu   | Bắt buộc | Mô tả                |
| ----- | ------ | -------- | -------------------- |
| token | string | Có       | Facebook OAuth token |

**Phản hồi:** Tương tự như đăng nhập thông thường

#### Đăng xuất

```

POST /auth/logout

```

**Headers:**

```

Authorization: Bearer {token}

````

**Phản hồi:**

```json
{
    "success": true,
    "data": null,
    "message": "Đăng xuất thành công",
    "errors": null
}
````

#### Quên mật khẩu

```
POST /auth/forgot-password
```

**Tham số:**

| Tên   | Kiểu   | Bắt buộc | Mô tả            |
| ----- | ------ | -------- | ---------------- |
| email | string | Có       | Email đã đăng ký |

**Phản hồi:**

```json
{
    "success": true,
    "data": null,
    "message": "Đã gửi email đặt lại mật khẩu",
    "errors": null
}
```

#### Đặt lại mật khẩu

```
POST /auth/reset-password
```

**Tham số:**

| Tên                   | Kiểu   | Bắt buộc | Mô tả                 |
| --------------------- | ------ | -------- | --------------------- |
| token                 | string | Có       | Token từ email        |
| email                 | string | Có       | Email                 |
| password              | string | Có       | Mật khẩu mới          |
| password_confirmation | string | Có       | Xác nhận mật khẩu mới |

**Phản hồi:**

```json
{
    "success": true,
    "data": null,
    "message": "Đặt lại mật khẩu thành công",
    "errors": null
}
```

#### Lấy thông tin người dùng hiện tại

```
GET /auth/user
```

**Headers:**

```
Authorization: Bearer {token}
```

**Phản hồi:**

```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "Nguyễn Văn A",
        "username": "nguyenvana",
        "email": "nguyenvana@example.com",
        "role": "member",
        "avatar": "https://api.mechamap.com/storage/avatars/1.jpg",
        "about_me": "Giới thiệu về tôi",
        "location": "Hà Nội",
        "website": "https://example.com",
        "signature": "Chữ ký của tôi",
        "points": 100,
        "reaction_score": 50,
        "last_seen_at": "2025-05-01T15:30:00.000000Z",
        "created_at": "2025-05-01T10:00:00.000000Z"
    },
    "message": "Lấy thông tin người dùng thành công",
    "errors": null
}
```

### Bài viết (Threads)

#### Lấy danh sách bài viết

```
GET /threads
```

**Tham số query:**

| Tên         | Kiểu    | Bắt buộc | Mô tả                                |
| ----------- | ------- | -------- | ------------------------------------ |
| page        | integer | Không    | Trang (mặc định: 1)                  |
| per_page    | integer | Không    | Số bài viết mỗi trang (mặc định: 10) |
| sort        | string  | Không    | Sắp xếp (latest, oldest, popular)    |
| category_id | integer | Không    | Lọc theo chuyên mục                  |
| forum_id    | integer | Không    | Lọc theo diễn đàn                    |
| user_id     | integer | Không    | Lọc theo người dùng                  |

**Phản hồi:**

```json
{
    "success": true,
    "data": {
        "threads": [
            {
                "id": 1,
                "title": "Tiêu đề bài viết",
                "slug": "tieu-de-bai-viet",
                "content_preview": "Nội dung tóm tắt...",
                "user": {
                    "id": 1,
                    "name": "Nguyễn Văn A",
                    "username": "nguyenvana",
                    "avatar": "https://api.mechamap.com/storage/avatars/1.jpg"
                },
                "category": {
                    "id": 1,
                    "name": "Chuyên mục",
                    "slug": "chuyen-muc"
                },
                "forum": {
                    "id": 1,
                    "name": "Diễn đàn",
                    "slug": "dien-dan"
                },
                "comments_count": 5,
                "view_count": 100,
                "is_sticky": false,
                "is_locked": false,
                "created_at": "2025-05-01T10:00:00.000000Z",
                "updated_at": "2025-05-01T10:00:00.000000Z"
            }
        ],
        "pagination": {
            "total": 50,
            "per_page": 10,
            "current_page": 1,
            "last_page": 5,
            "from": 1,
            "to": 10
        }
    },
    "message": "Lấy danh sách bài viết thành công",
    "errors": null
}
```

#### Xem chi tiết bài viết

```
GET /threads/{slug}
```

**Phản hồi:**

```json
{
    "success": true,
    "data": {
        "id": 1,
        "title": "Tiêu đề bài viết",
        "slug": "tieu-de-bai-viet",
        "content": "Nội dung đầy đủ của bài viết...",
        "user": {
            "id": 1,
            "name": "Nguyễn Văn A",
            "username": "nguyenvana",
            "avatar": "https://api.mechamap.com/storage/avatars/1.jpg"
        },
        "category": {
            "id": 1,
            "name": "Chuyên mục",
            "slug": "chuyen-muc"
        },
        "forum": {
            "id": 1,
            "name": "Diễn đàn",
            "slug": "dien-dan"
        },
        "is_sticky": false,
        "is_locked": false,
        "is_featured": false,
        "view_count": 100,
        "participant_count": 3,
        "location": "Hà Nội",
        "usage": "Residential",
        "floors": 30,
        "status": "Under Construction",
        "media": [
            {
                "id": 1,
                "file_name": "image1.jpg",
                "file_path": "threads/1/image1.jpg",
                "file_type": "image/jpeg",
                "file_size": 1024000,
                "title": "Hình ảnh 1",
                "description": "Mô tả hình ảnh 1",
                "url": "https://api.mechamap.com/storage/threads/1/image1.jpg"
            }
        ],
        "poll": {
            "id": 1,
            "question": "Câu hỏi thăm dò ý kiến?",
            "options": [
                {
                    "id": 1,
                    "text": "Lựa chọn 1",
                    "votes_count": 10
                },
                {
                    "id": 2,
                    "text": "Lựa chọn 2",
                    "votes_count": 5
                }
            ],
            "max_options": 1,
            "allow_change_vote": true,
            "show_votes_publicly": false,
            "allow_view_without_vote": true,
            "close_at": "2025-06-01T00:00:00.000000Z",
            "user_vote": [1]
        },
        "is_liked": true,
        "is_followed": false,
        "is_saved": true,
        "created_at": "2025-05-01T10:00:00.000000Z",
        "updated_at": "2025-05-01T15:00:00.000000Z"
    },
    "message": "Lấy chi tiết bài viết thành công",
    "errors": null
}
```
