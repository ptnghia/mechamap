# Tài Liệu: Cấu Hình Navigation Assets từ Database

## Tổng Quan
Đã hoàn thành việc chuyển đổi các assets hardcode trong navigation sang sử dụng database settings, bao gồm logo, banner, favicon và site name.

## Các Thay Đổi Đã Thực Hiện

### 1. Cập Nhật Helper Functions (`app/Helpers/SettingHelper.php`)

**Thêm mới:**
- `get_banner_url()` - Lấy URL banner từ database với fallback '/images/banner.webp'
- `get_site_name()` - Lấy tên site từ database với fallback config('app.name')

**Đã có sẵn:**
- `get_logo_url()` - Lấy URL logo từ database với fallback '/images/logo.png'
- `get_favicon_url()` - Lấy URL favicon từ database với fallback '/images/favicon.ico'

### 2. Cập Nhật Navigation Layout (`resources/views/layouts/navigation.blade.php`)

**Thay đổi:**
```blade
<!-- Cũ -->
<img src="{{ asset('images/banner.webp') }}" alt="Banner" class="banner-img">
<img src="{{ asset('images/logo.svg') }}" alt="{{ config('app.name') }}" class="img-fluid">

<!-- Mới -->
<img src="{{ get_banner_url() }}" alt="Banner" class="banner-img">
<img src="{{ get_logo_url() }}" alt="{{ get_site_name() }}" class="img-fluid">
```

### 3. Cập Nhật Guest Layout (`resources/views/layouts/guest.blade.php`)

**Thay đổi:**
```blade
<!-- Cũ -->
<title>{{ config('app.name', 'MechaMap') }} - @yield('title', 'Diễn đàn cộng đồng')</title>
<link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">

<!-- Mới -->
<title>{{ get_site_name() }} - @yield('title', 'Diễn đàn cộng đồng')</title>
<link rel="icon" href="{{ get_favicon_url() }}" type="image/x-icon">
```

### 4. App Layout (`resources/views/layouts/app.blade.php`)
Đã sử dụng `get_favicon_url()` từ trước - không cần thay đổi.

## Trạng Thái Database Settings

Các settings đã được cấu hình trong database:
- `site_logo`: `/images/settings/1749193328_68429270866ca.png`
- `site_favicon`: `/images/settings/1749193328_68429270877ef.png`
- `site_banner`: `/images/settings/1749193328_6842927088a0d.jpg`
- `site_name`: `MechaMap`

## Files Backup

Các files mặc định vẫn được giữ lại làm fallback:
- `/images/logo.svg` ✅
- `/images/favicon.ico` ✅
- `/images/banner.webp` ✅

## Kiểm Tra và Test

### Helper Functions Test
```bash
php artisan tinker --execute="echo 'Logo: ' . get_logo_url() . PHP_EOL;"
```

### Database Settings Check
```bash
php scripts/check_settings_navigation.php
```

## Lợi Ích

1. **Quản lý tập trung**: Tất cả assets được quản lý từ admin panel
2. **Fallback an toàn**: Nếu database lỗi vẫn có file mặc định
3. **Dễ bảo trì**: Không cần thay đổi code khi muốn thay assets
4. **Nhất quán**: Tất cả layouts đều sử dụng cùng nguồn dữ liệu

## Hướng Dẫn Sử Dụng

### Trong Admin Panel
1. Vào **Settings** > **General**
2. Upload logo, favicon, banner mới
3. Cập nhật site name
4. Lưu changes

### Trong Code
```php
// Sử dụng helper functions
$logo = get_logo_url();
$favicon = get_favicon_url();
$banner = get_banner_url();
$siteName = get_site_name();

// Hoặc sử dụng trực tiếp
$logo = setting('site_logo', '/images/logo.png');
```

## Troubleshooting

### Nếu Assets Không Hiển Thị
1. Kiểm tra file tồn tại: `ls -la public/images/settings/`
2. Kiểm tra database: `php artisan tinker --execute="echo setting('site_logo');"`
3. Kiểm tra quyền file: `chmod 644 public/images/settings/*`

### Nếu Helper Functions Lỗi
1. Kiểm tra autoload: `composer dump-autoload`
2. Kiểm tra helper file: `php -l app/Helpers/SettingHelper.php`
3. Clear cache: `php artisan cache:clear`

## Kết Luận

✅ **Hoàn thành**: Navigation assets đã được chuyển đổi thành công sang database-driven
✅ **Tested**: Tất cả helper functions hoạt động đúng
✅ **Fallback**: Cơ chế fallback an toàn
✅ **Maintainable**: Dễ bảo trì và mở rộng

Hệ thống hiện tại đã linh hoạt và có thể quản lý assets từ admin panel mà không cần thay đổi code.
