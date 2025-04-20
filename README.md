# MechaMap - Diễn đàn cộng đồng

<p align="center">
<img src="https://img.shields.io/badge/Laravel-11-red" alt="Laravel Version">
<img src="https://img.shields.io/badge/PHP-8.2-blue" alt="PHP Version">
<img src="https://img.shields.io/badge/Tailwind%20CSS-3.4-38b2ac" alt="Tailwind CSS">
<img src="https://img.shields.io/badge/License-MIT-green" alt="License">
</p>

## Giới thiệu dự án

MechaMap là một diễn đàn cộng đồng hiện đại, được xây dựng trên nền tảng Laravel 11 và Tailwind CSS. Dự án này cung cấp một nơi để người dùng có thể chia sẻ, thảo luận và tương tác với nhau thông qua các bài viết, bình luận và tin nhắn riêng.

Các tính năng chính:

-   Hệ thống xác thực người dùng đa dạng (email, Google, Facebook)
-   Phân quyền người dùng 5 cấp (Admin, Moderator, Senior, Member, Guest)
-   Đăng bài, bình luận, tương tác
-   Quản lý chuyên mục và bài viết
-   Tin nhắn riêng giữa các thành viên
-   Hệ thống báo cáo nội dung vi phạm
-   Giao diện người dùng hiện đại với Tailwind CSS
-   Hỗ trợ đa ngôn ngữ (mặc định: Tiếng Việt)

## Hệ thống phân quyền người dùng

MechaMap sử dụng hệ thống phân quyền 5 cấp độ người dùng:

### 1. Admin

-   Quyền truy cập đầy đủ vào hệ thống
-   Quản lý hệ thống & máy chủ
-   Thêm, sửa, xóa chuyên mục
-   Kiểm duyệt bài viết
-   Chỉnh sửa, xóa bài viết người khác
-   Cảnh báo, khóa tài khoản
-   Truy cập trang quản trị (/admin)

### 2. Moderator

-   Quyền tương tự Admin, ngoại trừ:
    -   Không quản lý hệ thống & máy chủ
    -   Không thêm, sửa, xóa chuyên mục
-   Truy cập trang quản trị (/admin)

### 3. Senior & Member

-   Đăng bài viết mới
-   Bình luận vào bài viết
-   Chỉnh sửa bài viết của mình
-   Gửi tin nhắn riêng
-   Báo cáo bài viết vi phạm
-   Xem nội dung công khai
-   Theo dõi người khác
-   Cập nhật trạng thái người khác

### 4. Guest

-   Không cần đăng ký, đăng nhập
-   Chỉ có thể xem nội dung công khai
-   Theo dõi người khác
-   Cập nhật trạng thái người khác

## Hệ thống xác thực người dùng

MechaMap hỗ trợ nhiều phương thức xác thực người dùng:

### 1. Đăng ký truyền thống

-   Đăng ký bằng email, username và mật khẩu
-   Xác thực email bắt buộc
-   Đăng nhập bằng username hoặc email

### 2. Đăng nhập mạng xã hội

-   Đăng nhập bằng Google
-   Đăng nhập bằng Facebook
-   Tự động xác thực email khi đăng nhập bằng mạng xã hội
-   Tạo mật khẩu ngẫu nhiên và gửi email thông báo cho người dùng mới

### 3. Đồng bộ tài khoản

-   Nếu email đã tồn tại, đồng bộ tài khoản mạng xã hội với tài khoản hiện có
-   Một người dùng có thể đăng nhập bằng nhiều phương thức khác nhau

## Cài đặt và cấu hình

### Yêu cầu hệ thống

-   PHP >= 8.2
-   MySQL >= 8.0
-   Composer
-   Node.js & NPM

### Cài đặt

1. Clone dự án:

```bash
git clone https://github.com/ptnghia/mechamap.git
cd mechamap
```

2. Cài đặt các gói PHP:

```bash
composer install
```

3. Cài đặt các gói JavaScript:

```bash
npm install
```

4. Sao chép file .env.example thành .env và cấu hình cơ sở dữ liệu:

```bash
cp .env.example .env
```

5. Tạo khóa ứng dụng:

```bash
php artisan key:generate
```

6. Chạy migration và seeder:

```bash
php artisan migrate --seed
```

7. Build assets:

```bash
npm run build
```

8. Khởi động máy chủ phát triển:

```bash
php artisan serve
```

### Cấu hình đăng nhập mạng xã hội

1. Tạo ứng dụng trên Google Cloud Console và Facebook Developer
2. Cập nhật các biến môi trường trong file .env:

```
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
GOOGLE_REDIRECT_URI=http://127.0.0.1:8000/auth/google/callback

FACEBOOK_CLIENT_ID=your-facebook-client-id
FACEBOOK_CLIENT_SECRET=your-facebook-client-secret
FACEBOOK_REDIRECT_URI=https://mechamap.test/auth/facebook/callback
```

## Đóng góp

Nếu bạn muốn đóng góp vào dự án, vui lòng tạo pull request hoặc báo cáo vấn đề trên GitHub.

## Giấy phép

Dự án này được phát hành dưới giấy phép [MIT](https://opensource.org/licenses/MIT).
