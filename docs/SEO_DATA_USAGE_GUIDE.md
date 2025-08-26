# 📖 Hướng dẫn sử dụng $seoData ở mọi view

## 🎯 Tổng quan

Sau khi cấu hình, `$seoData` hiện đã có thể sử dụng ở **mọi view** trong ứng dụng MechaMap thông qua:
- **View Composer** tự động chia sẻ dữ liệu
- **Helper functions** để truy cập dễ dàng
- **Blade directives** để sử dụng ngắn gọn
- **Fallback handling** tự động khi không có dữ liệu trong database

## 🚀 Cách sử dụng

### 1. Sử dụng trực tiếp biến `$seoData` trong view

```blade
{{-- Trong bất kỳ view nào --}}
<title>{{ $seoData['title'] ?? 'MechaMap' }}</title>
<meta name="description" content="{{ $seoData['description'] ?? '' }}">
<meta name="keywords" content="{{ $seoData['keywords'] ?? '' }}">

{{-- Kiểm tra dữ liệu có tồn tại --}}
@if(!empty($seoData['og_title']))
    <meta property="og:title" content="{{ $seoData['og_title'] }}">
@endif
```

### 2. Sử dụng các biến được chuẩn bị sẵn

```blade
{{-- Các biến này luôn có sẵn trong mọi view --}}
<title>{{ $currentSeoTitle }}</title>
<meta name="description" content="{{ $currentSeoDescription }}">
<meta name="keywords" content="{{ $currentSeoKeywords }}">
```

### 3. Sử dụng Helper Functions

```blade
{{-- Lấy toàn bộ SEO data --}}
@php $seo = get_seo_data(); @endphp

{{-- Lấy giá trị cụ thể --}}
<title>{{ seo_title() }}</title>
<meta name="description" content="{{ seo_description() }}">

{{-- Lấy title ngắn (phần đầu tiên trước ký tự "|") --}}
<h1>{{ seo_title_short() }}</h1>
<title>{{ seo_title(null, true) }}</title>

{{-- Lấy title ngắn với fallback text tùy chỉnh --}}
<h1>{{ seo_title_short('Trang chủ') }}</h1>
<h1>{{ seo_title_short('Diễn đàn kỹ thuật') }}</h1>
<h1>{{ seo_title_short('Custom Title', 'en') }}</h1>

{{-- Lấy giá trị theo key --}}
<meta property="og:image" content="{{ seo_value('og_image', asset('images/default-og.jpg')) }}">
<link rel="canonical" href="{{ seo_value('canonical_url', url()->current()) }}">
```

### 4. Sử dụng Blade Directive

```blade
{{-- Cú pháp ngắn gọn --}}
<title>@seo('title')</title>
<meta name="description" content="@seo('description')">
<meta property="og:title" content="@seo('og_title')">

{{-- Lấy title ngắn --}}
<h1>@seo_short()</h1>

{{-- Lấy title ngắn với fallback text tùy chỉnh --}}
<h1>@seo_short('Trang chủ')</h1>
<h1>@seo_short('Custom Title', 'en')</h1>
```

### 5. Sử dụng Custom Fallback Text

```blade
{{-- Khi không có dữ liệu trong DB, sẽ hiển thị text tùy chỉnh --}}
<h1 class="title_page">{{ seo_title_short('Trang chủ MechaMap') }}</h1>
<h1 class="title_page">{{ seo_title_short('Diễn đàn kỹ thuật') }}</h1>
<h1 class="title_page">{{ seo_title_short('Showcase dự án') }}</h1>

{{-- Với ngôn ngữ cụ thể --}}
<h1 class="title_page">{{ seo_title_short('Home Page', 'en') }}</h1>
<h1 class="title_page">{{ seo_title_short('Technical Forum', 'en') }}</h1>

{{-- Sử dụng Blade directive --}}
<h1 class="title_page">@seo_short('Trang chủ')</h1>
<h1 class="title_page">@seo_short('Custom Title', 'en')</h1>

{{-- Logic ưu tiên:
1. Nếu có dữ liệu trong DB → dùng dữ liệu DB (phần trước "|")
2. Nếu không có dữ liệu trong DB → dùng $text tùy chỉnh
3. Nếu $text = null → dùng default title theo locale
--}}
```

### 6. Sử dụng trong Controllers

```php
<?php

class ExampleController extends Controller
{
    public function index()
    {
        // Lấy SEO data trong controller
        $seoData = get_seo_data();

        // Hoặc lấy giá trị cụ thể
        $pageTitle = seo_value('title', 'Default Title');

        return view('example.index', compact('seoData', 'pageTitle'));
    }
}
```

## 🌐 Đa ngôn ngữ

```blade
{{-- Lấy SEO data theo ngôn ngữ cụ thể --}}
@php 
    $viSeoData = get_seo_data('vi');
    $enSeoData = get_seo_data('en');
@endphp

{{-- Hoặc sử dụng helper --}}
<title>{{ seo_title('vi') }}</title>
<meta name="description" content="{{ seo_description('en') }}">
```

## 📋 Danh sách các key có sẵn trong $seoData

```php
$seoData = [
    'title' => 'Tiêu đề trang',
    'description' => 'Mô tả trang',
    'keywords' => 'Từ khóa SEO',
    'og_title' => 'Tiêu đề Open Graph',
    'og_description' => 'Mô tả Open Graph',
    'og_image' => 'Hình ảnh Open Graph',
    'twitter_title' => 'Tiêu đề Twitter Card',
    'twitter_description' => 'Mô tả Twitter Card',
    'twitter_image' => 'Hình ảnh Twitter Card',
    'canonical_url' => 'URL canonical',
    'no_index' => 'Có nên index không (boolean)',
    'extra_meta' => 'Meta tags bổ sung (HTML)',
];
```

## 🎨 Ví dụ thực tế

### Trong layout chính

```blade
{{-- resources/views/layouts/app.blade.php --}}
<head>
    <title>{{ $currentSeoTitle }}</title>
    <meta name="description" content="{{ $currentSeoDescription }}">
    <meta name="keywords" content="{{ $currentSeoKeywords }}">
    
    {{-- Open Graph --}}
    <meta property="og:title" content="@seo('og_title')">
    <meta property="og:description" content="@seo('og_description')">
    <meta property="og:image" content="{{ seo_value('og_image', asset('images/default-og.jpg')) }}">
    
    {{-- Canonical URL --}}
    <link rel="canonical" href="@seo('canonical_url')">
</head>
```

### Trong component

```blade
{{-- resources/views/components/page-header.blade.php --}}
<div class="page-header">
    <h1>{{ seo_value('title', 'Trang chủ') }}</h1>
    <p class="lead">{{ seo_value('description', 'Chào mừng đến với MechaMap') }}</p>
</div>
```

### Trong partial view

```blade
{{-- resources/views/partials/breadcrumb.blade.php --}}
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ url('/') }}">{{ __('ui.navigation.home') }}</a>
        </li>
        <li class="breadcrumb-item active">
            {{ breadcrumb_title() }}
        </li>
    </ol>
</nav>
```

## ⚡ Performance Tips

1. **Cache SEO data** nếu cần thiết:
```php
// Trong controller hoặc service
$seoData = Cache::remember("seo_data_" . request()->path(), 3600, function() {
    return get_seo_data();
});
```

2. **Sử dụng lazy loading** cho dữ liệu không cần thiết ngay lập tức.

3. **Kiểm tra tồn tại** trước khi sử dụng:
```blade
@if(!empty($seoData['og_image']))
    <meta property="og:image" content="{{ $seoData['og_image'] }}">
@endif
```

## �️ Fallback Handling - Xử lý khi không có dữ liệu

### Tự động fallback khi không có dữ liệu trong database:

```blade
{{-- Hệ thống tự động fallback về default values --}}
<title>{{ seo_title() }}</title> {{-- Luôn có giá trị, không bao giờ trống --}}
<meta name="description" content="{{ seo_description() }}"> {{-- Có fallback description --}}

{{-- Sử dụng default values trực tiếp --}}
<title>{{ seo_default_title() }}</title>
<meta name="description" content="{{ seo_default_description() }}">
<meta name="keywords" content="{{ seo_default_keywords() }}">
```

### Default values theo ngôn ngữ:

**Tiếng Việt:**
- Title: "MechaMap - Cộng đồng Kỹ thuật Cơ khí Việt Nam"
- Description: "Nền tảng forum hàng đầu cho cộng đồng kỹ sư cơ khí Việt Nam..."
- Keywords: "cơ khí, kỹ thuật, CAD, CAM, thiết kế máy móc..."

**English:**
- Title: "MechaMap - Vietnam Mechanical Engineering Community"
- Description: "Leading forum platform for Vietnam's mechanical engineering community..."
- Keywords: "mechanical engineering, CAD, CAM, machine design..."

### Error handling:
- Tất cả helper functions có try-catch để xử lý lỗi
- Log warnings khi có lỗi xảy ra
- Luôn trả về giá trị mặc định thay vì null hoặc error

## �🔧 Troubleshooting

### Nếu $seoData không có sẵn:
1. Kiểm tra `AppServiceProvider` đã được đăng ký đúng
2. Chạy `composer dump-autoload` để load lại helper functions
3. Clear cache: `php artisan config:clear && php artisan view:clear`

### Nếu helper functions không hoạt động:
1. Kiểm tra `composer.json` có include `SeoHelper.php` không
2. Chạy `composer dump-autoload`

### Nếu fallback values không hiển thị:
1. Kiểm tra log files để xem có lỗi gì: `storage/logs/laravel.log`
2. Đảm bảo `MultilingualSeoService` hoạt động bình thường
3. Test với helper functions: `seo_default_title()`, `seo_default_description()`

## 📝 Lưu ý quan trọng

- `$seoData` được tự động load cho **tất cả views** thông qua View Composer
- Dữ liệu được cache trong request lifecycle để tối ưu performance
- Fallback values được cung cấp để tránh lỗi khi không có dữ liệu
- Hỗ trợ đầy đủ đa ngôn ngữ (vi/en)
