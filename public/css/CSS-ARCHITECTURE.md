/*
 * MechaMap CSS Architecture Documentation
 * Hướng dẫn sử dụng và tổ chức CSS trong dự án
 *
 * Cấu trúc CSS đã được tổ chức theo mô hình Traditional CSS (không sử dụng Vite)
 * CẬP NHẬT: Unified Components - header.blade.php, sidebar.blade.php, footer.blade.php
 */

/* ========================================
   CẤU TRÚC THƯMỤC CSS
   ======================================== */

/*
public/css/
├── main.css                 # File CSS chính (import tất cả)
├── app.css                  # CSS cũ (deprecated, sẽ được thay thế bởi main.css)
├── dark-mode.css           # Theme tối
├── compact-theme.css       # Theme compact
│
├── components/             # CSS cho các component
│   ├── buttons.css
│   ├── forms.css
│   ├── alerts.css
│   ├── avatar.css
│   ├── custom-header.css
│   ├── sidebar.css
│   ├── mobile-nav.css
│   ├── auth.css
│   ├── auth-modal.css
│   └── admin-pagination.css
│
└── views/                  # CSS riêng cho từng view
    ├── homepage.css        # Trang chủ
    ├── threads.css         # Danh sách & chi tiết threads
    ├── profile.css         # Trang profile
    ├── admin.css          # Admin panel
    ├── auth.css           # Authentication pages
    └── search.css         # Trang tìm kiếm
*/

/* ========================================
   CÁCH SỬ DỤNG
   ======================================== */

/*
1. TRONG BLADE LAYOUTS:
   Thay thế @vite() bằng asset('css/main.css')
   
   Cũ: @vite(['resources/css/app.css', 'resources/js/app.js'])
   Mới: <link rel="stylesheet" href="{{ asset('css/main.css') }}">

2. BOOTSTRAP:
   Load Bootstrap qua CDN trong layout:
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

3. CSS RIÊNG CHO VIEW:
   Nếu view cần CSS đặc biệt, tạo file trong views/ và import vào main.css
   Hoặc load trực tiếp trong blade template:
   <link rel="stylesheet" href="{{ asset('css/views/custom-view.css') }}">

4. CSS VARIABLES:
   Sử dụng CSS variables đã định nghĩa trong main.css:
   var(--primary-color), var(--text-primary), etc.
*/

/* ========================================
   CSS VARIABLES REFERENCE
   ======================================== */

/*
PRIMARY COLORS:
--primary-color: #007bff
--primary-hover: #0056b3
--primary-light: #e3f2fd

BACKGROUND COLORS:
--bg-primary: #ffffff
--bg-secondary: #f8f9fa
--bg-tertiary: #e9ecef

TEXT COLORS:
--text-primary: #212529
--text-secondary: #6c757d
--text-muted: #adb5bd

SPACING & EFFECTS:
--border-color: #dee2e6
--shadow-sm: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075)
--shadow-md: 0 0.5rem 1rem rgba(0, 0, 0, 0.15)
--transition-fast: 0.15s ease
--transition-normal: 0.3s ease
--border-radius-sm: 0.25rem
--border-radius-md: 0.375rem
--border-radius-lg: 0.5rem
*/

/* ========================================
   UTILITY CLASSES AVAILABLE
   ======================================== */

/*
DISPLAY:
.d-flex, .d-block, .d-inline, .d-inline-block, .d-none

FLEXBOX:
.flex-column, .flex-row, .justify-content-center, 
.justify-content-between, .align-items-center, .flex-grow-1

TEXT:
.text-center, .text-left, .text-right
.fw-bold, .fw-medium, .fw-normal

SPACING:
.m-0, .p-0, .mt-3, .mb-3, .pt-3, .pb-3

BORDER & SHADOW:
.border, .border-0, .rounded, .rounded-lg
.shadow-sm, .shadow

SIZE:
.w-100, .h-100, .min-h-screen

RESPONSIVE:
.d-sm-none, .d-md-none, .d-lg-none (và tương ứng cho block)

ANIMATION:
.fade-in, .slide-in-right, .spinner
*/

/* ========================================
   BEST PRACTICES
   ======================================== */

/*
1. NAMING CONVENTION:
   - Component classes: .component-name
   - Modifier classes: .component-name--modifier
   - State classes: .is-active, .is-loading
   - Utility classes: .u-text-center

2. CSS ORGANIZATION:
   - Variables first
   - Base styles
   - Components
   - Utilities
   - Responsive

3. PERFORMANCE:
   - Sử dụng CSS variables thay vì hard-code values
   - Minimize nesting (tối đa 3 levels)
   - Sử dụng efficient selectors
   - Avoid !important

4. MAINTAINABILITY:
   - Comment cho complex styles
   - Group related styles
   - Consistent spacing
   - Follow BEM methodology khi cần

5. RESPONSIVE:
   - Mobile-first approach
   - Use relative units (rem, %, vw, vh)
   - Test on multiple screen sizes
*/

/* ========================================
   MIGRATION NOTES
   ======================================== */

/*
ĐÃ THỰC HIỆN:
✅ Loại bỏ Vite dependencies khỏi package.json
✅ Xóa vite.config.js, tailwind.config.js, postcss.config.js
✅ Xóa thư mục resources/css/ và resources/js/
✅ Tạo public/css/main.css làm file CSS chính
✅ Cập nhật tất cả layout blade files
✅ Load Bootstrap qua CDN
✅ Tạo CSS riêng cho các view chính
✅ Tạo cấu trúc import trong main.css

CẦN LƯU Ý:
- File app.css cũ vẫn tồn tại nhưng không được sử dụng
- Node modules không cần thiết cho CSS/JS traditional
- Performance có thể tốt hơn do ít build steps
- Easier deployment vì không cần build process
*/
