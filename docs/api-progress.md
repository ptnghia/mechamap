# Tiến độ phát triển API

## API Settings

| Endpoint                   | Method | Mô tả                 | Trạng thái    |
| -------------------------- | ------ | --------------------- | ------------- |
| `/api/v1/settings`         | GET    | Lấy tất cả cài đặt    | ✅ Hoàn thành |
| `/api/v1/settings/{group}` | GET    | Lấy cài đặt theo nhóm | ✅ Hoàn thành |
| `/api/v1/favicon`          | GET    | Lấy favicon URL       | ✅ Hoàn thành |

## API SEO

| Endpoint                            | Method | Mô tả                            | Trạng thái    |
| ----------------------------------- | ------ | -------------------------------- | ------------- |
| `/api/v1/seo`                       | GET    | Lấy tất cả cài đặt SEO           | ✅ Hoàn thành |
| `/api/v1/seo/{group}`               | GET    | Lấy cài đặt SEO theo nhóm        | ✅ Hoàn thành |
| `/api/v1/page-seo/{routeName}`      | GET    | Lấy cài đặt SEO theo route name  | ✅ Hoàn thành |
| `/api/v1/page-seo/url/{urlPattern}` | GET    | Lấy cài đặt SEO theo URL pattern | ✅ Hoàn thành |

## API Schema.org

| Endpoint                   | Method | Mô tả                                 | Trạng thái    |
| -------------------------- | ------ | ------------------------------------- | ------------- |
| `/api/v1/settings/general` | GET    | Lấy cài đặt chung cho Schema.org      | ✅ Hoàn thành |
| `/api/v1/settings/company` | GET    | Lấy thông tin công ty cho Schema.org  | ✅ Hoàn thành |
| `/api/v1/seo/general`      | GET    | Lấy cài đặt SEO chung cho Schema.org  | ✅ Hoàn thành |
| `/api/v1/seo/social`       | GET    | Lấy cài đặt SEO social cho Schema.org | ✅ Hoàn thành |

## API Authentication

| Endpoint                | Method | Mô tả                             | Trạng thái         |
| ----------------------- | ------ | --------------------------------- | ------------------ |
| `/api/v1/auth/login`    | POST   | Đăng nhập                         | 🔄 Đang phát triển |
| `/api/v1/auth/register` | POST   | Đăng ký                           | 🔄 Đang phát triển |
| `/api/v1/auth/logout`   | POST   | Đăng xuất                         | 🔄 Đang phát triển |
| `/api/v1/auth/refresh`  | POST   | Làm mới token                     | 🔄 Đang phát triển |
| `/api/v1/auth/me`       | GET    | Lấy thông tin người dùng hiện tại | 🔄 Đang phát triển |

## API User

| Endpoint                    | Method | Mô tả                         | Trạng thái         |
| --------------------------- | ------ | ----------------------------- | ------------------ |
| `/api/v1/users`             | GET    | Lấy danh sách người dùng      | 🔄 Đang phát triển |
| `/api/v1/users/{id}`        | GET    | Lấy thông tin người dùng      | 🔄 Đang phát triển |
| `/api/v1/users/{id}`        | PUT    | Cập nhật thông tin người dùng | 🔄 Đang phát triển |
| `/api/v1/users/{id}/avatar` | POST   | Cập nhật avatar               | 🔄 Đang phát triển |

## API Forum

| Endpoint                  | Method | Mô tả                  | Trạng thái         |
| ------------------------- | ------ | ---------------------- | ------------------ |
| `/api/v1/forums`          | GET    | Lấy danh sách diễn đàn | 🔄 Đang phát triển |
| `/api/v1/forums/{id}`     | GET    | Lấy thông tin diễn đàn | 🔄 Đang phát triển |
| `/api/v1/categories`      | GET    | Lấy danh sách danh mục | 🔄 Đang phát triển |
| `/api/v1/categories/{id}` | GET    | Lấy thông tin danh mục | 🔄 Đang phát triển |

## API Thread

| Endpoint                      | Method | Mô tả                | Trạng thái         |
| ----------------------------- | ------ | -------------------- | ------------------ |
| `/api/v1/threads`             | GET    | Lấy danh sách chủ đề | 🔄 Đang phát triển |
| `/api/v1/threads`             | POST   | Tạo chủ đề mới       | 🔄 Đang phát triển |
| `/api/v1/threads/{id}`        | GET    | Lấy thông tin chủ đề | 🔄 Đang phát triển |
| `/api/v1/threads/{id}`        | PUT    | Cập nhật chủ đề      | 🔄 Đang phát triển |
| `/api/v1/threads/{id}`        | DELETE | Xóa chủ đề           | 🔄 Đang phát triển |
| `/api/v1/threads/{id}/like`   | POST   | Thích chủ đề         | 🔄 Đang phát triển |
| `/api/v1/threads/{id}/save`   | POST   | Lưu chủ đề           | 🔄 Đang phát triển |
| `/api/v1/threads/{id}/follow` | POST   | Theo dõi chủ đề      | 🔄 Đang phát triển |

## API Comment

| Endpoint                        | Method | Mô tả                   | Trạng thái         |
| ------------------------------- | ------ | ----------------------- | ------------------ |
| `/api/v1/threads/{id}/comments` | GET    | Lấy danh sách bình luận | 🔄 Đang phát triển |
| `/api/v1/threads/{id}/comments` | POST   | Tạo bình luận mới       | 🔄 Đang phát triển |
| `/api/v1/comments/{id}`         | GET    | Lấy thông tin bình luận | 🔄 Đang phát triển |
| `/api/v1/comments/{id}`         | PUT    | Cập nhật bình luận      | 🔄 Đang phát triển |
| `/api/v1/comments/{id}`         | DELETE | Xóa bình luận           | 🔄 Đang phát triển |
| `/api/v1/comments/{id}/like`    | POST   | Thích bình luận         | 🔄 Đang phát triển |

## API Media

| Endpoint             | Method | Mô tả               | Trạng thái         |
| -------------------- | ------ | ------------------- | ------------------ |
| `/api/v1/media`      | POST   | Tải lên media       | 🔄 Đang phát triển |
| `/api/v1/media/{id}` | GET    | Lấy thông tin media | 🔄 Đang phát triển |
| `/api/v1/media/{id}` | DELETE | Xóa media           | 🔄 Đang phát triển |

## API Search

| Endpoint         | Method | Mô tả    | Trạng thái         |
| ---------------- | ------ | -------- | ------------------ |
| `/api/v1/search` | GET    | Tìm kiếm | 🔄 Đang phát triển |

## API Notification

| Endpoint                         | Method | Mô tả                   | Trạng thái         |
| -------------------------------- | ------ | ----------------------- | ------------------ |
| `/api/v1/notifications`          | GET    | Lấy danh sách thông báo | 🔄 Đang phát triển |
| `/api/v1/notifications/{id}`     | PUT    | Đánh dấu đã đọc         | 🔄 Đang phát triển |
| `/api/v1/notifications/read-all` | PUT    | Đánh dấu tất cả đã đọc  | 🔄 Đang phát triển |

## Chú thích

-   ✅ Hoàn thành: API đã được phát triển và hoạt động tốt
-   🔄 Đang phát triển: API đang được phát triển
-   ❌ Chưa phát triển: API chưa được phát triển
