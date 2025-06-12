# Hướng dẫn triển khai MechaMap lên hosting thông qua Git Version Control

## Yêu cầu hệ thống

- PHP 8.2 hoặc cao hơn
- MySQL 5.7 hoặc cao hơn
- Composer 2.x
- Git

## Vấn đề về phiên bản PHP và Composer

Dự án yêu cầu PHP 8.2 trở lên, nhưng Composer trên hosting đang chạy với PHP 7.4.33. Để giải quyết vấn đề này, bạn cần thực hiện một trong các cách sau:

1. **Cài đặt Composer trên máy local với PHP 8.2+, sau đó upload vendor folder lên hosting**:
   ```bash
   composer install --no-dev --optimize-autoloader
   ```

2. **Chỉ định phiên bản PHP khi chạy Composer trên hosting**:
   ```bash
   /usr/local/bin/php8.3 /path/to/composer.phar install --no-dev --optimize-autoloader
   ```

3. **Cấu hình Composer sử dụng PHP 8.3 trên hosting**:
   ```bash
   alias composer='/usr/local/bin/php8.3 /path/to/composer.phar'
   ```

## Các bước triển khai

### 1. Chuẩn bị trên máy local

1. Đảm bảo tất cả các thay đổi đã được commit:
   ```bash
   git add .
   git commit -m "Chuẩn bị triển khai lên hosting"
   ```

2. Đẩy code lên GitHub:
   ```bash
   git push origin master
   ```

### 2. Cấu hình Git Version Control trên cPanel

1. Đăng nhập vào cPanel của bạn
2. Tìm và mở "Git™ Version Control"
3. Nhấp vào "Create" để tạo một repository mới
4. Điền thông tin:
   - Clone URL: https://github.com/ptnghia/mechamap.git
   - Repository Path: public_html/mechamap (hoặc đường dẫn bạn muốn)
   - Repository Name: mechamap
   - Chọn branch: master
5. Nhấp vào "Create" để tạo repository

### 3. Cấu hình môi trường trên hosting

1. Tạo database mới trên hosting (nếu chưa có)
2. Tạo file .env trên hosting dựa trên .env.example:
   ```bash
   cp .env.example .env
   ```
3. Cập nhật thông tin trong file .env:
   - APP_KEY: Tạo key mới hoặc sử dụng key hiện tại
   - DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD: Thông tin database trên hosting
   - MAIL_* : Thông tin email
   - Các thông tin khác như GOOGLE_CLIENT_ID, FACEBOOK_CLIENT_ID, v.v.

4. Cấu hình quyền truy cập:
   ```bash
   chmod -R 755 .
   chmod -R 777 storage bootstrap/cache
   ```

### 4. Cài đặt dependencies và chạy migrations

1. Cài đặt dependencies (sử dụng PHP 8.3):
   ```bash
   /usr/local/bin/php8.3 /path/to/composer.phar install --no-dev --optimize-autoloader
   ```

2. Tạo key mới (nếu chưa có):
   ```bash
   php artisan key:generate
   ```

3. Chạy migrations:
   ```bash
   php artisan migrate
   ```

4. Tối ưu hóa:
   ```bash
   php artisan optimize
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

5. Tạo symbolic link cho storage:
   ```bash
   php artisan storage:link
   ```

### 5. Cập nhật code từ GitHub

Khi có thay đổi mới trên GitHub, bạn có thể cập nhật code trên hosting bằng cách:

1. Đăng nhập vào cPanel
2. Mở "Git™ Version Control"
3. Tìm repository của bạn và nhấp vào "Manage"
4. Nhấp vào "Pull or Deploy" và chọn "Update from Remote"

### 6. Xử lý lỗi

Nếu gặp lỗi liên quan đến phiên bản PHP, hãy đảm bảo rằng:

1. Phiên bản PHP mặc định trên hosting là 8.2 hoặc cao hơn
2. Composer được cấu hình để sử dụng PHP 8.2 hoặc cao hơn
3. Các lệnh PHP Artisan được chạy với PHP 8.2 hoặc cao hơn

## Lưu ý bảo mật

1. Đảm bảo file .env không thể truy cập từ bên ngoài
2. Đặt mật khẩu mạnh cho database
3. Cấu hình APP_DEBUG=false trong môi trường production
4. Cấu hình HTTPS cho website
