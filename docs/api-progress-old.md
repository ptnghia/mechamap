# Theo dõi tiến độ phát triển API cho MechaMap

## Tổng quan

Tài liệu này theo dõi tiến độ phát triển API cho ứng dụng MechaMap, phục vụ cho việc phát triển frontend bằng Next.js. Các API sẽ được phát triển dựa trên các tính năng hiện có của ứng dụng Laravel.

## Danh sách tính năng frontend cần hỗ trợ API

### 1. Xác thực người dùng

-   [ ] Đăng ký tài khoản
-   [ ] Đăng nhập (email/username + password)
-   [ ] Đăng nhập bằng Google
-   [ ] Đăng nhập bằng Facebook
-   [ ] Đăng xuất
-   [ ] Quên mật khẩu
-   [ ] Đặt lại mật khẩu
-   [ ] Xác thực email
-   [ ] Lấy thông tin người dùng hiện tại

### 2. Quản lý người dùng

-   [ ] Xem danh sách người dùng
-   [ ] Xem thông tin chi tiết người dùng
-   [ ] Cập nhật thông tin cá nhân
-   [ ] Cập nhật mật khẩu
-   [ ] Cập nhật avatar
-   [ ] Xem hoạt động người dùng
-   [ ] Theo dõi/bỏ theo dõi người dùng
-   [ ] Xem danh sách người theo dõi
-   [ ] Xem danh sách đang theo dõi

### 3. Diễn đàn và chuyên mục

-   [ ] Lấy danh sách chuyên mục
-   [ ] Lấy danh sách diễn đàn
-   [ ] Xem chi tiết diễn đàn
-   [ ] Lấy danh sách diễn đàn con
-   [ ] Lấy thống kê diễn đàn

### 4. Bài viết (Threads)

-   [ ] Lấy danh sách bài viết mới nhất
-   [ ] Lấy danh sách bài viết nổi bật
-   [ ] Lấy danh sách bài viết theo diễn đàn
-   [ ] Lấy danh sách bài viết theo chuyên mục
-   [ ] Lấy danh sách bài viết của người dùng
-   [ ] Xem chi tiết bài viết
-   [ ] Tạo bài viết mới
-   [ ] Cập nhật bài viết
-   [ ] Xóa bài viết
-   [ ] Đánh dấu bài viết (bookmark)
-   [ ] Thích bài viết
-   [ ] Theo dõi bài viết
-   [ ] Tìm kiếm bài viết

### 5. Bình luận

-   [ ] Lấy danh sách bình luận của bài viết
-   [ ] Lấy danh sách bình luận của người dùng
-   [ ] Tạo bình luận mới
-   [ ] Trả lời bình luận
-   [ ] Cập nhật bình luận
-   [ ] Xóa bình luận
-   [ ] Thích bình luận

### 6. Media

-   [ ] Tải lên hình ảnh
-   [ ] Lấy danh sách hình ảnh của người dùng
-   [ ] Lấy danh sách hình ảnh của bài viết
-   [ ] Xóa hình ảnh

### 7. Thông báo

-   [ ] Lấy danh sách thông báo
-   [ ] Đánh dấu thông báo đã đọc
-   [ ] Xóa thông báo

### 8. Trang chủ

-   [ ] Lấy dữ liệu cho trang chủ (bài viết mới, nổi bật, diễn đàn phổ biến, người đóng góp)
-   [ ] Lấy thêm bài viết (infinite scrolling)

### 9. Trang "What's New"

-   [ ] Lấy nội dung mới nhất (bài viết, bình luận)
-   [ ] Lọc nội dung theo loại

### 10. Tìm kiếm

-   [ ] Tìm kiếm cơ bản
-   [ ] Tìm kiếm nâng cao (theo nhiều tiêu chí)
-   [ ] Gợi ý tìm kiếm

### 11. Cuộc thăm dò ý kiến (Polls)

-   [ ] Lấy thông tin cuộc thăm dò
-   [ ] Bình chọn
-   [ ] Xem kết quả

### 12. Cài đặt và SEO

-   [x] Lấy tất cả cài đặt hệ thống
-   [x] Lấy cài đặt theo nhóm
-   [x] Lấy tất cả cài đặt SEO
-   [x] Lấy cài đặt SEO theo nhóm
-   [x] Lấy cài đặt SEO cho trang cụ thể

## Kế hoạch phát triển API

### Giai đoạn 1: Xác thực và người dùng

-   [ ] Thiết kế cấu trúc API authentication (JWT/Sanctum)
-   [ ] Phát triển API đăng ký, đăng nhập, đăng xuất
-   [ ] Phát triển API quản lý thông tin người dùng
-   [ ] Phát triển API đăng nhập mạng xã hội

### Giai đoạn 2: Diễn đàn và bài viết

-   [ ] Phát triển API lấy danh sách chuyên mục và diễn đàn
-   [ ] Phát triển API quản lý bài viết
-   [ ] Phát triển API quản lý bình luận
-   [ ] Phát triển API tương tác (thích, đánh dấu, theo dõi)

### Giai đoạn 3: Tính năng bổ sung

-   [ ] Phát triển API quản lý media
-   [ ] Phát triển API thông báo
-   [ ] Phát triển API tìm kiếm
-   [ ] Phát triển API cuộc thăm dò ý kiến
-   [x] Phát triển API cài đặt và SEO

### Giai đoạn 4: Tối ưu hóa và hoàn thiện

-   [ ] Tối ưu hóa hiệu suất API
-   [ ] Bổ sung tài liệu API
-   [ ] Kiểm thử và sửa lỗi
-   [ ] Triển khai API lên môi trường production

## Tiến độ phát triển

| Ngày       | Tính năng          | Trạng thái | Ghi chú                                  |
| ---------- | ------------------ | ---------- | ---------------------------------------- |
| 01/05/2025 | API Cài đặt và SEO | Hoàn thành | Đã tạo API để lấy dữ liệu cài đặt và SEO |

## Tài liệu API

Tài liệu API sẽ được cập nhật tại đây khi các API được phát triển.

### Cấu trúc cơ bản

Tất cả các API sẽ sử dụng định dạng JSON và tuân theo cấu trúc phản hồi sau:

```json
{
  "success": true/false,
  "data": { ... },
  "message": "Thông báo",
  "errors": { ... }
}
```

### Xác thực

API sẽ sử dụng Laravel Sanctum để xác thực. Các yêu cầu xác thực sẽ cần gửi token trong header:

```
Authorization: Bearer {token}
```

### Endpoint cơ bản

Base URL: `https://api.mechamap.com/api/v1`

#### Xác thực

-   `POST /auth/register` - Đăng ký tài khoản
-   `POST /auth/login` - Đăng nhập
-   `POST /auth/logout` - Đăng xuất
-   `POST /auth/forgot-password` - Quên mật khẩu
-   `POST /auth/reset-password` - Đặt lại mật khẩu
-   `GET /auth/user` - Lấy thông tin người dùng hiện tại

#### Người dùng

-   `GET /users` - Lấy danh sách người dùng
-   `GET /users/{username}` - Lấy thông tin chi tiết người dùng
-   `PUT /users/profile` - Cập nhật thông tin cá nhân
-   `PUT /users/password` - Cập nhật mật khẩu
-   `POST /users/avatar` - Cập nhật avatar

#### Diễn đàn và chuyên mục

-   `GET /categories` - Lấy danh sách chuyên mục
-   `GET /forums` - Lấy danh sách diễn đàn
-   `GET /forums/{slug}` - Xem chi tiết diễn đàn

#### Bài viết

-   `GET /threads` - Lấy danh sách bài viết
-   `GET /threads/featured` - Lấy danh sách bài viết nổi bật
-   `GET /threads/{slug}` - Xem chi tiết bài viết
-   `POST /threads` - Tạo bài viết mới
-   `PUT /threads/{slug}` - Cập nhật bài viết
-   `DELETE /threads/{slug}` - Xóa bài viết

#### Bình luận

-   `GET /threads/{slug}/comments` - Lấy danh sách bình luận của bài viết
-   `POST /threads/{slug}/comments` - Tạo bình luận mới
-   `PUT /comments/{id}` - Cập nhật bình luận
-   `DELETE /comments/{id}` - Xóa bình luận

#### Cài đặt và SEO

-   `GET /settings` - Lấy tất cả cài đặt hệ thống
-   `GET /settings/{group}` - Lấy cài đặt theo nhóm (general, company, contact, social, api, copyright)
-   `GET /seo` - Lấy tất cả cài đặt SEO
-   `GET /seo/{group}` - Lấy cài đặt SEO theo nhóm (general, social, advanced, robots)
-   `GET /page-seo/{routeName}` - Lấy cài đặt SEO cho trang cụ thể theo route name
-   `GET /page-seo/url/{urlPattern}` - Lấy cài đặt SEO cho trang cụ thể theo URL pattern
