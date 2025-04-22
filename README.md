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

## Cấu trúc CSS và JS

Dự án MechaMap được tổ chức với cấu trúc CSS và JS rõ ràng để dễ dàng bảo trì và mở rộng.

### Cấu trúc CSS

Các file CSS được tổ chức trong thư mục `resources/css` với cấu trúc như sau:

```
resources/css/
├── app.css                # File CSS chính, import tất cả các file CSS khác
├── components/            # Các component UI
│   ├── buttons.css        # Các kiểu nút
│   ├── cards.css          # Các kiểu card
│   ├── forms.css          # Các kiểu form
│   ├── notifications.css  # Các kiểu thông báo
│   └── tables.css         # Các kiểu bảng
├── layouts/               # Layout và grid
│   └── grid.css           # Grid system
├── pages/                 # CSS cho các trang cụ thể
│   └── auth.css           # CSS cho trang xác thực
└── theme/                 # Theme và style chung
    ├── base.css           # Style cơ bản (scrollbar, animations, container)
    ├── colors.css         # Màu sắc và các biến thể (badges, alerts)
    └── elements.css       # Các element cơ bản (cards, buttons, inputs, tooltips)
```

#### Cách tùy chỉnh CSS

1. **Thêm CSS cho component mới**: Tạo file mới trong thư mục `resources/css/components/` và import vào `app.css`
2. **Tùy chỉnh CSS cho trang cụ thể**: Tạo file mới trong thư mục `resources/css/pages/` và import vào `app.css`
3. **Thay đổi màu sắc và biến thể**: Chỉnh sửa file `resources/css/theme/colors.css`
4. **Tùy chỉnh style cơ bản**: Chỉnh sửa file `resources/css/theme/base.css`
5. **Tùy chỉnh các element cơ bản**: Chỉnh sửa file `resources/css/theme/elements.css`
6. **Thay đổi biến màu gốc**: Chỉnh sửa biến màu trong `app.css`

### Cấu trúc JS

Các file JS được tổ chức trong thư mục `resources/js` với cấu trúc như sau:

```
resources/js/
├── app.js                 # File JS chính, import tất cả các file JS khác
├── bootstrap.js           # Bootstrap JS
├── components/            # Các component JS
│   └── notifications.js   # Component thông báo
├── pages/                 # JS cho các trang cụ thể
├── theme/                 # JS cho theme
│   ├── darkMode.js        # Chế độ tối/sáng
│   ├── index.js           # Khởi tạo theme
│   ├── navigation.js      # Điều hướng và menu
│   └── tooltips.js        # Tooltips
└── utils/                 # Các utility function
    ├── dom.js             # Các hàm thao tác với DOM
    ├── helpers.js         # Các hàm helper
    └── validation.js      # Các hàm kiểm tra dữ liệu
```

#### Cách tùy chỉnh JS

1. **Thêm JS cho component mới**: Tạo file mới trong thư mục `resources/js/components/` và import vào `app.js`
2. **Tùy chỉnh JS cho trang cụ thể**: Tạo file mới trong thư mục `resources/js/pages/` và import vào `app.js`
3. **Tùy chỉnh theme**: Chỉnh sửa các file trong thư mục `resources/js/theme/`
4. **Thêm utility function**:
    - **Helper functions**: Thêm vào file `resources/js/utils/helpers.js`
    - **DOM functions**: Thêm vào file `resources/js/utils/dom.js`
    - **Validation functions**: Thêm vào file `resources/js/utils/validation.js`

### Sử dụng hình ảnh trong CSS

Hình ảnh được lưu trữ trong thư mục `resources/images/` và có thể được sử dụng trong CSS như sau:

```css
.element {
    background-image: url("@/images/your-image.svg");
}
```

Alias `@` sẽ được thay thế bằng đường dẫn đến thư mục `resources` trong quá trình build.

## Sử dụng Settings Helper Functions

MechaMap cung cấp các helper function để truy cập cài đặt hệ thống từ bất kỳ đâu trong ứng dụng. Các cài đặt này có thể được quản lý trong trang admin tại `/admin/settings/general`.

### Các Helper Function Cơ Bản

```php
// Lấy giá trị của một cài đặt cụ thể
setting('key', 'default_value');

// Lấy tất cả cài đặt trong một nhóm
settings_group('group_name');
```

### Các Helper Function Chuyên Biệt

```php
// Lấy URL của logo
get_logo_url();

// Lấy URL của favicon
get_favicon_url();

// Lấy thông tin công ty
$companyInfo = get_company_info();
// Truy cập các thông tin: $companyInfo['name'], $companyInfo['address'], $companyInfo['phone'], etc.

// Lấy thông tin liên hệ
$contactInfo = get_contact_info();
// Truy cập các thông tin: $contactInfo['email'], $contactInfo['phone'], $contactInfo['address'], etc.

// Lấy liên kết mạng xã hội
$socialLinks = get_social_links();
// Truy cập các liên kết: $socialLinks['facebook'], $socialLinks['twitter'], $socialLinks['instagram'], etc.

// Lấy API keys
$apiKeys = get_api_keys();
// Truy cập các key: $apiKeys['google_client_id'], $apiKeys['facebook_app_id'], $apiKeys['recaptcha_site_key'], etc.

// Lấy thông tin bản quyền
$copyrightInfo = get_copyright_info();
// Truy cập các thông tin: $copyrightInfo['text'], $copyrightInfo['owner'], $copyrightInfo['year'], etc.
```

### Sử dụng trong Views

```php
<!-- Sử dụng trong Blade templates -->
<title>{{ setting('site_name', config('app.name')) }}</title>
<link rel="icon" href="{{ get_favicon_url() }}" type="image/x-icon">

<!-- Hiển thị thông tin bản quyền -->
<p>{{ get_copyright_info()['text'] }}</p>

<!-- Hiển thị liên kết mạng xã hội -->
@php
    $socialLinks = get_social_links();
@endphp

@if(!empty($socialLinks['facebook']))
    <a href="{{ $socialLinks['facebook'] }}" target="_blank">Facebook</a>
@endif
```

### Sử dụng trong Controllers

```php
public function index()
{
    $companyInfo = get_company_info();
    $contactEmail = get_contact_info()['email'];

    return view('welcome', [
        'companyName' => $companyInfo['name'],
        'contactEmail' => $contactEmail
    ]);
}
```

### Cập nhật Cài đặt

```php
use App\Models\Setting;

// Cập nhật một cài đặt
Setting::set('site_name', 'MechaMap', 'general');

// Xóa cache
Setting::clearCache();
```

## Đóng góp

Nếu bạn muốn đóng góp vào dự án, vui lòng tạo pull request hoặc báo cáo vấn đề trên GitHub.

## Giấy phép

Dự án này được phát hành dưới giấy phép [MIT](https://opensource.org/licenses/MIT).
