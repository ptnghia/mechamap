# 🌐 MechaMap Laravel 11 Translation Key Standard

> **Cấu trúc key translation chuẩn cho MechaMap theo Laravel 11 best practices**  
> **Version**: 1.0 | **Created**: 2025-07-21 | **Status**: Draft

---

## 🎯 **NGUYÊN TẮC THIẾT KẾ**

### **1. Laravel 11 Best Practices**
- Sử dụng **dot notation** cho nested keys
- **Snake_case** cho tất cả keys
- **Singular/Plural** rõ ràng
- **Namespace** theo chức năng

### **2. Naming Convention**
```
{file}.{section}.{subsection}.{key}
```

**Ví dụ:**
```php
// ✅ Chuẩn
__('auth.login.form.email')
__('common.buttons.save')
__('forums.threads.actions.create')

// ❌ Không chuẩn  
__('ui/navigation.auth.login')
__('content.processing')
__('form.email')
```

---

## 📁 **CẤU TRÚC FILE TRANSLATION**

### **Core Files (Hệ thống cốt lõi)**

#### **1. auth.php** - Authentication & Authorization
```php
return [
    'login' => [
        'title' => 'Đăng nhập',
        'form' => [
            'email' => 'Email',
            'password' => 'Mật khẩu',
            'remember' => 'Ghi nhớ đăng nhập',
        ],
        'actions' => [
            'submit' => 'Đăng nhập',
            'forgot_password' => 'Quên mật khẩu?',
        ],
        'messages' => [
            'success' => 'Đăng nhập thành công',
            'failed' => 'Thông tin đăng nhập không chính xác',
        ]
    ],
    'register' => [
        'title' => 'Đăng ký',
        // ...
    ]
];
```

#### **2. common.php** - UI Elements chung
```php
return [
    'buttons' => [
        'save' => 'Lưu',
        'cancel' => 'Hủy',
        'delete' => 'Xóa',
        'edit' => 'Sửa',
        'create' => 'Tạo',
        'view_all' => 'Xem tất cả',
    ],
    'status' => [
        'active' => 'Hoạt động',
        'inactive' => 'Không hoạt động',
        'pending' => 'Chờ xử lý',
    ],
    'messages' => [
        'success' => 'Thành công!',
        'error' => 'Có lỗi xảy ra!',
        'loading' => 'Đang tải...',
    ]
];
```

#### **3. navigation.php** - Menu & Navigation
```php
return [
    'main' => [
        'home' => 'Trang chủ',
        'forums' => 'Diễn đàn',
        'marketplace' => 'Thị trường',
        'showcase' => 'Trưng bày',
    ],
    'user' => [
        'profile' => 'Hồ sơ',
        'settings' => 'Cài đặt',
        'logout' => 'Đăng xuất',
    ]
];
```

### **Feature Files (Tính năng cụ thể)**

#### **4. forums.php** - Forum functionality
```php
return [
    'threads' => [
        'title' => 'Chủ đề diễn đàn',
        'actions' => [
            'create' => 'Tạo chủ đề',
            'edit' => 'Sửa chủ đề',
            'delete' => 'Xóa chủ đề',
            'reply' => 'Trả lời',
        ],
        'status' => [
            'pinned' => 'Ghim',
            'locked' => 'Khóa',
            'featured' => 'Nổi bật',
        ]
    ],
    'posts' => [
        'actions' => [
            'create' => 'Viết bài',
            'edit' => 'Sửa bài',
            'quote' => 'Trích dẫn',
        ]
    ]
];
```

#### **5. marketplace.php** - Marketplace features
```php
return [
    'products' => [
        'title' => 'Sản phẩm',
        'actions' => [
            'add_to_cart' => 'Thêm vào giỏ',
            'buy_now' => 'Mua ngay',
            'view_details' => 'Xem chi tiết',
        ],
        'types' => [
            'digital' => 'Sản phẩm số',
            'new_product' => 'Sản phẩm mới',
            'used_product' => 'Sản phẩm đã qua sử dụng',
        ]
    ],
    'cart' => [
        'title' => 'Giỏ hàng',
        'empty' => 'Giỏ hàng trống',
        'total' => 'Tổng cộng',
    ]
];
```

---

## 🔧 **CÁCH SỬ DỤNG TRONG CODE**

### **1. Trong Controllers**
```php
// ✅ Chuẩn
return redirect()->with('success', __('auth.login.messages.success'));
$title = __('forums.threads.title');

// ❌ Không chuẩn
return redirect()->with('success', __('messages.login_success'));
```

### **2. Trong Blade Templates**
```blade
{{-- ✅ Chuẩn --}}
<h1>{{ __('forums.threads.title') }}</h1>
<button>{{ __('common.buttons.save') }}</button>

{{-- ❌ Không chuẩn --}}
<h1>{{ __('forum.title') }}</h1>
<button>{{ __('buttons.save') }}</button>
```

### **3. Với Helper Functions**
```php
// Giữ nguyên helper functions hiện tại nhưng map sang structure mới
function t_common($key, $replace = [], $locale = null) {
    return __("common.$key", $replace, $locale);
}

// Sử dụng:
t_common('buttons.save')  // → __('common.buttons.save')
```

---

## 📋 **DANH SÁCH FILE TRANSLATION CHUẨN**

### **Core Files**
1. **auth.php** - Authentication, login, register, passwords
2. **common.php** - Buttons, status, messages, time, units
3. **navigation.php** - Menus, breadcrumbs, links
4. **validation.php** - Form validation messages
5. **errors.php** - Error pages, HTTP errors

### **Feature Files**  
6. **forums.php** - Forum, threads, posts, categories
7. **marketplace.php** - Products, cart, orders, payments
8. **showcase.php** - Showcases, projects, galleries
9. **user.php** - Profile, settings, notifications
10. **search.php** - Search functionality, filters

### **Content Files**
11. **homepage.php** - Homepage content, hero sections
12. **pages.php** - Static pages, about, contact
13. **emails.php** - Email templates, notifications

### **Admin Files**
14. **admin.php** - Admin dashboard, management
15. **moderation.php** - Content moderation, reports

### **SEO Files**
16. **seo.php** - Meta titles, descriptions, keywords

---

## ✅ **MIGRATION CHECKLIST**

- [ ] Backup existing translation files
- [ ] Create new file structure
- [ ] Map old keys to new keys
- [ ] Update all .blade.php files
- [ ] Update controllers and helpers
- [ ] Test all translations
- [ ] Update documentation

---

## 🎯 **BENEFITS**

1. **Consistency** - Unified naming convention
2. **Maintainability** - Easy to find and update
3. **Scalability** - Clear structure for new features
4. **Laravel 11 Compliance** - Follows best practices
5. **Developer Experience** - Better IDE support
6. **Performance** - Optimized key lookup
