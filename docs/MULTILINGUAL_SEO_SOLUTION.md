# 🌐 Giải pháp SEO Đa ngôn ngữ cho MechaMap

## 📋 Tổng quan

Tài liệu này mô tả giải pháp lưu trữ và quản lý dữ liệu SEO đa ngôn ngữ cho MechaMap, hỗ trợ tiếng Việt và tiếng Anh.

## 🎯 Giải pháp được chọn: JSON Columns

### ✅ Ưu điểm
- **Đơn giản**: Không cần tạo bảng mới
- **Performance**: Truy vấn nhanh, không cần JOIN
- **Flexible**: Dễ thêm ngôn ngữ mới
- **Backward Compatible**: Tương thích với dữ liệu cũ

### 📊 Cấu trúc Database

```sql
-- Thêm các cột JSON cho đa ngôn ngữ
ALTER TABLE page_seos ADD COLUMN title_i18n JSON;
ALTER TABLE page_seos ADD COLUMN description_i18n JSON;
ALTER TABLE page_seos ADD COLUMN keywords_i18n JSON;
ALTER TABLE page_seos ADD COLUMN og_title_i18n JSON;
ALTER TABLE page_seos ADD COLUMN og_description_i18n JSON;
ALTER TABLE page_seos ADD COLUMN twitter_title_i18n JSON;
ALTER TABLE page_seos ADD COLUMN twitter_description_i18n JSON;
```

### 📝 Cấu trúc dữ liệu JSON

```json
{
  "vi": "Diễn đàn Kỹ thuật Cơ khí",
  "en": "Mechanical Engineering Forums"
}
```

## 🔧 Implementation

### 1. Model PageSeo

```php
// Casts cho JSON columns
protected $casts = [
    'title_i18n' => 'array',
    'description_i18n' => 'array',
    'keywords_i18n' => 'array',
    // ...
];

// Methods để lấy dữ liệu theo ngôn ngữ
public function getLocalizedTitle(?string $locale = null): ?string
{
    $locale = $locale ?: app()->getLocale();
    
    if ($this->title_i18n && isset($this->title_i18n[$locale])) {
        return $this->title_i18n[$locale];
    }
    
    return $this->title; // Fallback
}
```

### 2. MultilingualSeoService

```php
// Lấy dữ liệu SEO với đa ngôn ngữ
$seoService = app(\App\Services\MultilingualSeoService::class);
$seoData = $seoService->getSeoData(request(), 'vi');
```

### 3. BreadcrumbService Integration

```php
// Sử dụng dữ liệu đa ngôn ngữ trong breadcrumb
$homeTitle = $homeSeo ? 
    $this->extractBreadcrumbTitle($homeSeo->getLocalizedTitle()) : 
    __('breadcrumb.home');
```

## 🚀 Sử dụng

### 1. Trong Blade Templates

```blade
{{-- Sử dụng component --}}
<x-seo-meta :locale="app()->getLocale()" />

{{-- Hoặc sử dụng helper functions --}}
<title>{{ seo_title() }}</title>
<meta name="description" content="{{ seo_description() }}">

{{-- Breadcrumb title --}}
{{ breadcrumb_title() }}
```

### 2. Trong Controllers

```php
// Lấy dữ liệu SEO
$seoData = page_seo_data('vi');

// Tạo/cập nhật dữ liệu SEO
$seoService = app(\App\Services\MultilingualSeoService::class);
$seoService->createOrUpdateSeoData('home', [
    'title_i18n' => [
        'vi' => 'Trang chủ',
        'en' => 'Home'
    ],
    'description_i18n' => [
        'vi' => 'Mô tả tiếng Việt',
        'en' => 'English description'
    ]
]);
```

### 3. Seeding Data

```bash
# Chạy migration để thêm cột JSON
php artisan migrate

# Seed dữ liệu đa ngôn ngữ
php artisan db:seed --class=MultilingualPageSeoSeeder
```

## 📁 Files Structure

```
app/
├── Models/
│   └── PageSeo.php                    # Model với multilingual support
├── Services/
│   ├── MultilingualSeoService.php     # Service quản lý SEO đa ngôn ngữ
│   └── BreadcrumbService.php          # Updated với multilingual support
└── Helpers/
    └── SeoHelper.php                  # Helper functions

database/
├── migrations/
│   └── 2025_01_21_add_multilingual_support_to_page_seos.php
└── seeders/
    └── MultilingualPageSeoSeeder.php

resources/views/components/
└── seo-meta.blade.php                # SEO meta component
```

## 🔄 Migration Process

### Bước 1: Backup dữ liệu
```bash
php artisan db:backup
```

### Bước 2: Chạy migration
```bash
php artisan migrate
```

### Bước 3: Seed dữ liệu đa ngôn ngữ
```bash
php artisan db:seed --class=MultilingualPageSeoSeeder
```

### Bước 4: Test
```bash
# Test breadcrumb
curl https://mechamap.test/
curl https://mechamap.test/showcase

# Test language switching
curl -H "Accept-Language: en" https://mechamap.test/
```

## 🎨 Helper Functions

### seo_meta()
Tạo tất cả meta tags SEO cho trang hiện tại

### seo_title() / seo_description()
Lấy title/description theo ngôn ngữ hiện tại

### breadcrumb_title()
Lấy title cho breadcrumb (đã loại bỏ site name)

### hreflang_tags()
Tạo hreflang tags cho multilingual SEO

### structured_data()
Tạo JSON-LD structured data

## 🔍 Advanced Features

### 1. Dynamic Placeholders
```php
// Trong SEO data
'title_i18n' => [
    'vi' => 'Hồ sơ {user_name} | MechaMap',
    'en' => '{user_name} Profile | MechaMap'
]

// Tự động thay thế khi render
// Result: "Hồ sơ John Doe | MechaMap"
```

### 2. Fallback Mechanism
```php
// Nếu không có dữ liệu cho ngôn ngữ hiện tại
// → Fallback về ngôn ngữ mặc định (vi)
// → Fallback về cột cũ (title, description)
// → Fallback về default values
```

### 3. Validation
```php
$errors = $seoService->validateMultilingualData([
    'title_i18n' => ['vi' => 'Title', 'en' => ''],
    'description_i18n' => ['vi' => 'Desc', 'en' => 'Description']
]);
```

## 📊 Performance Considerations

### 1. Indexing
```sql
-- Index cho JSON queries (MySQL 5.7+)
CREATE INDEX idx_title_vi ON page_seos ((JSON_EXTRACT(title_i18n, '$.vi')));
CREATE INDEX idx_title_en ON page_seos ((JSON_EXTRACT(title_i18n, '$.en')));
```

### 2. Caching
```php
// Cache SEO data
Cache::remember("seo.{$routeName}.{$locale}", 3600, function() {
    return $pageSeo->getLocalizedData($locale);
});
```

## 🧪 Testing

### Unit Tests
```php
// Test multilingual methods
$this->assertEquals('Home', $pageSeo->getLocalizedTitle('en'));
$this->assertEquals('Trang chủ', $pageSeo->getLocalizedTitle('vi'));
```

### Feature Tests
```php
// Test SEO meta rendering
$response = $this->get('/');
$response->assertSee('<title>MechaMap - Vietnam Mechanical Engineering Community</title>', false);
```

## 🔮 Future Enhancements

1. **Admin Interface**: Tạo giao diện quản lý SEO đa ngôn ngữ
2. **Auto Translation**: Tích hợp Google Translate API
3. **SEO Analytics**: Theo dõi performance theo ngôn ngữ
4. **More Languages**: Thêm hỗ trợ ngôn ngữ khác (zh, ja, ko)
5. **Regional SEO**: Tối ưu SEO theo khu vực địa lý

## 📞 Support

Nếu có vấn đề với implementation, vui lòng:
1. Kiểm tra logs: `storage/logs/laravel.log`
2. Test với Tinker: `php artisan tinker`
3. Verify database: Check JSON columns có data chưa
4. Clear cache: `php artisan cache:clear`
