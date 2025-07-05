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
├── main.css                 # File CSS cũ (deprecated)
├── main-user.css           # File CSS cũ (deprecated)
├── main-admin.css          # CSS cho admin panel (KHÔNG THAY ĐỔI)
├── admin/                  # Thư mục admin CSS (KHÔNG THAY ĐỔI)
│   ├── admin-*.css         # Các file admin
│   └── main-admin.css      # Main admin CSS
│
├── frontend/               # ✨ CẤU TRÚC MỚI - Frontend User CSS
│   ├── main-user-optimized.css  # File CSS chính được tối ưu hóa
│   │
│   ├── components/         # CSS cho các component
│   │   ├── buttons.css
│   │   ├── forms.css
│   │   ├── alerts.css
│   │   ├── avatar.css
│   │   ├── auth-modal.css
│   │   ├── mobile-nav.css
│   │   ├── sidebar.css
│   │   └── thread-form.css
│   │
│   ├── views/              # CSS riêng cho từng view
│   │   ├── homepage.css    # Trang chủ
│   │   ├── threads.css     # Danh sách & chi tiết threads
│   │   ├── profile.css     # Trang profile
│   │   ├── auth.css        # Authentication pages
│   │   ├── search.css      # Trang tìm kiếm
│   │   ├── home.css        # Home page styles
│   │   ├── activity.css    # Activity page
│   │   ├── whats-new.css   # What's new page
│   │   ├── thread-*.css    # Thread related styles
│   │   └── showcase-*.css  # Showcase styles
│   │
│   └── utilities/          # CSS utilities và themes
│       ├── utilities.css   # File utilities tổng hợp
│       ├── dark-mode.css   # Theme tối
│       ├── compact-theme.css # Theme compact
│       └── enhanced-menu.css # Enhanced menu
│
└── [legacy files]          # Các file CSS cũ còn lại (auth.css, sidebar.css, etc.)
*/

/* ========================================
   CÁCH SỬ DỤNG
   ======================================== */

/*
1. FRONTEND USER LAYOUTS:
   Sử dụng file CSS tối ưu hóa mới:

   Mới: <link rel="stylesheet" href="{{ asset('css/frontend/main-user-optimized.css') }}">

   Page-specific CSS (conditional loading):
   @if(Route::currentRouteName() === 'home')
   <link rel="stylesheet" href="{{ asset('css/frontend/views/home.css') }}">
   @endif

2. ADMIN LAYOUTS:
   Giữ nguyên cấu trúc admin (KHÔNG THAY ĐỔI):
   <link href="{{ asset_versioned('css/main-admin.css') }}" rel="stylesheet">

3. BOOTSTRAP:
   Load Bootstrap qua CDN trong layout:
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

4. CSS VARIABLES:
   Sử dụng CSS variables đã định nghĩa:
   - User frontend: var(--user-primary), var(--user-text-primary), etc.
   - Admin panel: var(--admin-primary), var(--admin-text-primary), etc.

5. THÊM CSS MỚI:
   - Components: Thêm vào frontend/components/
   - Views: Thêm vào frontend/views/
   - Utilities: Thêm vào frontend/utilities/
   - Import vào main-user-optimized.css nếu cần
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
   MIGRATION NOTES - CSS OPTIMIZATION COMPLETED
   ======================================== */

/*
✅ COMPLETED OPTIMIZATION (2025-07-05):

1. CREATED NEW STRUCTURE:
   - frontend/components/ - All reusable components
   - frontend/views/ - Page-specific styles
   - frontend/utilities/ - Themes, utilities, helpers
   - main-user-optimized.css - Single optimized entry point

2. PERFORMANCE IMPROVEMENTS:
   - Reduced HTTP requests by consolidating imports
   - Organized CSS by functionality
   - Removed duplicate files
   - Optimized import structure

3. MAINTAINED COMPATIBILITY:
   - Admin panel CSS untouched (admin/ directory)
   - All existing functionality preserved
   - Backward compatibility maintained

4. REMOVED FILES:
   - Old component CSS from root (buttons.css, forms.css, etc.)
   - Old view CSS from root (home.css, search.css, etc.)
   - Old utility CSS from root (dark-mode.css, etc.)
   - Duplicate directories (components/, views/)

5. UPDATED REFERENCES:
   - app.blade.php now uses frontend/main-user-optimized.css
   - Conditional loading for page-specific CSS
   - Removed redundant CSS links

BACKUP LOCATION: public/css_backup_[timestamp]/

/*
