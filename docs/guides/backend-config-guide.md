# Hướng dẫn cấu hình Backend cho Mechamap

Tài liệu này cung cấp hướng dẫn chi tiết để cấu hình backend Laravel cho hoạt động với frontend Next.js trên tên miền mechamap.com.

## 1. Cấu hình môi trường

Sao chép file `.env.production` thành `.env` trên máy chủ:

```bash
cp .env.production .env
```

Cập nhật các thông tin sau trong file `.env`:

- `APP_KEY`: Tạo key mới bằng lệnh `php artisan key:generate`
- `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`: Thông tin database
- `MAIL_*`: Thông tin email
- `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`: Thông tin đăng nhập Google
- `FACEBOOK_CLIENT_ID`, `FACEBOOK_CLIENT_SECRET`: Thông tin đăng nhập Facebook

## 2. Cấu hình CORS

Đảm bảo file `config/cors.php` có cấu hình sau:

```php
'paths' => ['api/*', 'auth/*', 'sanctum/csrf-cookie'],

'allowed_methods' => ['*'],

'allowed_origins' => explode(',', env('CORS_ALLOWED_ORIGINS', 'https://mechamap.com,https://www.mechamap.com,http://localhost:3000')),

'allowed_origins_patterns' => [],

'allowed_headers' => ['*'],

'exposed_headers' => [],

'max_age' => 0,

'supports_credentials' => true,
```

## 3. Cấu hình Sanctum

Đảm bảo file `config/sanctum.php` có cấu hình sau:

```php
'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', sprintf(
    '%s%s',
    'mechamap.com,www.mechamap.com,localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1',
    Sanctum::currentApplicationUrlWithPort()
))),
```

## 4. Cấu hình Session

Đảm bảo file `config/session.php` có cấu hình sau:

```php
'domain' => env('SESSION_DOMAIN', '.mechamap.com'),

'secure' => env('SESSION_SECURE_COOKIE', true),

'same_site' => env('SESSION_SAME_SITE', 'none'),
```

## 5. Đăng ký Middleware CORS

Thêm middleware `HandleCors` vào danh sách middleware trong `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware) {
    // Đăng ký middleware toàn cục
    $middleware->append(\App\Http\Middleware\TrackUserActivity::class);
    $middleware->append(\App\Http\Middleware\ApplySeoSettings::class);
    $middleware->append(\App\Http\Middleware\HandleCors::class);

    // ...
})
```

## 6. Cấu hình Apache/Nginx

### Apache (.htaccess)

Đảm bảo file `.htaccess` trong thư mục gốc có cấu hình sau:

```apache
<IfModule mod_headers.c>
    Header set Access-Control-Allow-Origin "https://mechamap.com"
    Header set Access-Control-Allow-Methods "GET, POST, PUT, DELETE, OPTIONS"
    Header set Access-Control-Allow-Headers "Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN"
    Header set Access-Control-Allow-Credentials "true"
    
    # Respond to OPTIONS requests
    RewriteEngine On
    RewriteCond %{REQUEST_METHOD} OPTIONS
    RewriteRule ^(.*)$ $1 [R=200,L]
</IfModule>
```

### Nginx

Nếu sử dụng Nginx, thêm cấu hình sau vào file cấu hình server:

```nginx
location / {
    add_header 'Access-Control-Allow-Origin' 'https://mechamap.com' always;
    add_header 'Access-Control-Allow-Methods' 'GET, POST, PUT, DELETE, OPTIONS' always;
    add_header 'Access-Control-Allow-Headers' 'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN' always;
    add_header 'Access-Control-Allow-Credentials' 'true' always;
    
    if ($request_method = 'OPTIONS') {
        add_header 'Access-Control-Allow-Origin' 'https://mechamap.com' always;
        add_header 'Access-Control-Allow-Methods' 'GET, POST, PUT, DELETE, OPTIONS' always;
        add_header 'Access-Control-Allow-Headers' 'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN' always;
        add_header 'Access-Control-Allow-Credentials' 'true' always;
        add_header 'Content-Type' 'text/plain charset=UTF-8';
        add_header 'Content-Length' '0';
        return 204;
    }
    
    try_files $uri $uri/ /index.php?$query_string;
}
```

## 7. Tối ưu hóa

Sau khi cấu hình xong, chạy các lệnh sau để tối ưu hóa ứng dụng:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

## 8. Kiểm tra

Kiểm tra API bằng cách gọi một endpoint từ frontend:

```bash
curl -X OPTIONS https://backend.mechamap.com/api/v1/auth/me \
  -H "Origin: https://mechamap.com" \
  -H "Access-Control-Request-Method: GET" \
  -v
```

Kết quả trả về nên có các header CORS phù hợp.

## 9. Xử lý lỗi phổ biến

### Lỗi CORS

Nếu gặp lỗi CORS, kiểm tra:
- Cấu hình CORS trong `config/cors.php`
- Middleware `HandleCors` đã được đăng ký
- Cấu hình web server (Apache/Nginx)

### Lỗi xác thực

Nếu gặp lỗi xác thực:
- Kiểm tra cấu hình Sanctum trong `config/sanctum.php`
- Kiểm tra cấu hình Session trong `config/session.php`
- Đảm bảo cookie domain được cấu hình chính xác

### Lỗi SSL

Nếu gặp lỗi SSL:
- Đảm bảo chứng chỉ SSL hợp lệ
- Kiểm tra cấu hình `SESSION_SECURE_COOKIE=true`
- Kiểm tra cấu hình `APP_URL=https://backend.mechamap.com`
