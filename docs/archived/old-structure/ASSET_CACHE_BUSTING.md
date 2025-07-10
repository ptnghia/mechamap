# Asset Cache Busting System - MechaMap

## Tổng quan

Hệ thống cache busting cho phép tự động thêm version parameters vào CSS/JS files để buộc trình duyệt tải lại assets mới khi có thay đổi, thay vì phải bấm Ctrl+F5.

## Cách hoạt động

### 1. Helper Functions

```php
// Tạo URL với version parameter
asset_versioned('css/main.css') 
// Output: https://mechamap.test/css/main.css?v=1751624482

// Tạo CSS link tag với version
css_versioned('css/main.css')
// Output: <link rel="stylesheet" href="https://mechamap.test/css/main.css?v=1751624482">

// Tạo JS script tag với version
js_versioned('js/app.js')
// Output: <script src="https://mechamap.test/js/app.js?v=1751624482"></script>
```

### 2. Blade Directives

```blade
{{-- Sử dụng directive @css và @js --}}
@css('css/main.css')
@js('js/app.js')

{{-- Hoặc sử dụng helper function --}}
<link rel="stylesheet" href="{{ asset_versioned('css/main.css') }}">
<script src="{{ asset_versioned('js/app.js') }}"></script>
```

### 3. Cấu hình

File `config/assets.php` chứa các cấu hình:

```php
// Bật/tắt versioning
'versioning_enabled' => true,

// Phương thức: 'filemtime', 'manual', 'git'
'versioning_method' => 'filemtime',

// Version thủ công
'manual_version' => '1.0.0',

// Cache duration
'cache_duration' => [
    'versioned' => 31536000,    // 1 năm
    'non_versioned' => 3600,    // 1 giờ
],
```

## Sử dụng

### 1. Trong Layout Files

```blade
{{-- resources/views/layouts/app.blade.php --}}
<link rel="stylesheet" href="{{ asset_versioned('css/main-user.css') }}">
<link rel="stylesheet" href="{{ asset_versioned('css/dark-mode.css') }}">

<script src="{{ asset_versioned('js/app.js') }}"></script>
<script src="{{ asset_versioned('js/dark-mode.js') }}"></script>
```

### 2. Clear Cache Command

```bash
# Clear asset cache và touch tất cả files
php artisan assets:clear-cache

# Force clear không cần confirm
php artisan assets:clear-cache --force
```

### 3. Development Mode

```env
# .env file
ASSET_VERSIONING_ENABLED=true
ASSET_VERSIONING_METHOD=filemtime
ASSET_FORCE_RELOAD=false  # true để force reload mỗi lần
```

## Lợi ích

### ✅ Trước khi có Cache Busting
- Phải bấm Ctrl+F5 để reload CSS/JS mới
- Users có thể thấy giao diện cũ
- Khó debug CSS/JS changes

### ✅ Sau khi có Cache Busting  
- Tự động load CSS/JS mới khi có thay đổi
- Users luôn thấy version mới nhất
- Dễ dàng deploy updates

## Workflow

1. **Developer thay đổi CSS/JS**
2. **File modification time thay đổi**
3. **Version parameter tự động update**
4. **Browser tự động load file mới**

## Commands

```bash
# Clear tất cả cache
php artisan assets:clear-cache --force

# Clear Laravel cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Troubleshooting

### Nếu vẫn thấy CSS cũ:
1. Chạy `php artisan assets:clear-cache --force`
2. Kiểm tra file có tồn tại không
3. Kiểm tra permissions của file
4. Hard refresh browser (Ctrl+Shift+R)

### Nếu version không thay đổi:
1. Kiểm tra `ASSET_VERSIONING_ENABLED=true` trong .env
2. Kiểm tra file đã được touch chưa
3. Kiểm tra filemtime() function

## Best Practices

1. **Luôn sử dụng `asset_versioned()` cho local assets**
2. **Không dùng cho CDN assets (Bootstrap, jQuery, etc.)**
3. **Chạy `assets:clear-cache` sau mỗi deployment**
4. **Sử dụng `manual_version` cho production builds**

## Kết luận

Hệ thống cache busting giúp giải quyết vấn đề cache CSS/JS một cách tự động và hiệu quả, không cần phải bấm Ctrl+F5 nữa!
