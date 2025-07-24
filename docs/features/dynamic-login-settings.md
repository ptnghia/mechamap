# Dynamic Login Settings - MechaMap

## Tổng quan

Tính năng này cho phép trang đăng nhập sử dụng dữ liệu động từ bảng `settings` thay vì hardcode, giúp admin có thể tùy chỉnh nội dung trang login thông qua admin panel.

## Các thay đổi đã thực hiện

### 1. Cập nhật Login View (`resources/views/auth/login.blade.php`)

**Trước:**
```php
<h3 class="fw-bold mb-2">MechaMap</h3>
<p class="fs-5 mb-0 opacity-90">{{ __('auth.knowledge_hub') }}</p>
<p class="text-muted mb-0">{{ __('auth.login_journey_description') }}</p>
```

**Sau:**
```php
<h3 class="fw-bold mb-2">{{ $siteSettings['name'] }}</h3>
<p class="fs-5 mb-0 opacity-90">{{ $siteSettings['slogan'] }}</p>
<p class="text-muted mb-0">{{ $siteSettings['welcome_message'] }}</p>
```

### 2. Tạo SettingsServiceProvider (`app/Providers/SettingsServiceProvider.php`)

Service provider này:
- Chia sẻ settings với tất cả views thông qua biến `$siteSettings`
- Sử dụng caching để tối ưu hiệu suất (cache 1 giờ)
- Kiểm tra table `settings` tồn tại trước khi load
- Cung cấp fallback values nếu settings không tồn tại

### 3. Đăng ký Service Provider (`config/app.php`)

Thêm `App\Providers\SettingsServiceProvider::class` vào mảng providers.

## Settings được sử dụng

| Setting Key | Mô tả | Fallback Value |
|-------------|-------|----------------|
| `site_name` | Tên website | "MechaMap" |
| `site_slogan` | Slogan/tagline | "Cộng đồng Kỹ thuật Cơ khí Việt Nam" |
| `site_welcome_message` | Thông điệp chào mừng | "Chào mừng bạn đến với MechaMap" |

## Cách sử dụng

### Thay đổi settings qua Admin Panel

1. Truy cập Admin Panel → Cài Đặt Hệ Thống → General Settings
2. Cập nhật các trường:
   - **Site Name**: Tên hiển thị của website
   - **Site Slogan**: Slogan/tagline
   - **Site Welcome Message**: Thông điệp chào mừng trên trang login

### Thay đổi settings qua Code

```php
use App\Models\Setting;

// Cập nhật site name
Setting::set('site_name', 'MechaMap Pro');

// Cập nhật slogan
Setting::set('site_slogan', 'Cộng đồng Kỹ sư Chuyên nghiệp');

// Cập nhật welcome message
Setting::set('site_welcome_message', 'Chào mừng đến với cộng đồng kỹ sư hàng đầu!');

// Clear cache để áp dụng thay đổi
Cache::forget('global_site_settings');
```

## Caching

- Settings được cache với key `global_site_settings`
- Thời gian cache: 3600 giây (1 giờ)
- Cache tự động clear khi settings được cập nhật thông qua `Setting::set()`

## Lợi ích

1. **Tùy chỉnh dễ dàng**: Admin có thể thay đổi nội dung trang login mà không cần sửa code
2. **Hiệu suất cao**: Sử dụng caching để giảm truy vấn database
3. **Fallback an toàn**: Có giá trị mặc định nếu settings không tồn tại
4. **Tính nhất quán**: Settings được chia sẻ với tất cả views

## Testing

Để test tính năng:

```bash
# Test thay đổi settings
php artisan tinker
Setting::set('site_name', 'Test Name');
exit

# Clear cache
php artisan cache:clear

# Kiểm tra trang login tại https://mechamap.test/login
```

## Tương lai

Có thể mở rộng để sử dụng settings cho:
- Trang register
- Footer copyright
- Meta tags
- Social media links
- Maintenance page
