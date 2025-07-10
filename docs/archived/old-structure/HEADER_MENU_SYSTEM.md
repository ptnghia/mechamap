# Header Menu System - Role-based Navigation

## 📋 Tổng quan

Hệ thống menu header đã được cập nhật để hỗ trợ phân quyền theo từng nhóm user, cung cấp truy cập nhanh đến các dashboard và chức năng phù hợp với role của từng người dùng.

## 🎯 Mục tiêu

- **Phân quyền rõ ràng**: Mỗi role có menu riêng biệt
- **Truy cập nhanh**: Quick access đến dashboard và chức năng chính
- **UX tốt**: Giao diện trực quan, dễ sử dụng
- **Responsive**: Hoạt động tốt trên mọi thiết bị

## 🏗️ Cấu trúc Menu

### 1. **Admin & Moderator**
```
Quản trị (Dropdown)
├── Dashboard Admin
├── Quản lý người dùng
├── Quản lý diễn đàn
└── Quản lý marketplace
```

### 2. **Supplier (Nhà cung cấp)**
```
Nhà cung cấp (Dropdown)
├── Dashboard
├── Sản phẩm của tôi
├── Đơn hàng
└── Báo cáo
```

### 3. **Manufacturer (Nhà sản xuất)**
```
Nhà sản xuất (Dropdown)
├── Dashboard
├── Thiết kế của tôi
├── Đơn hàng download
└── Phân tích
```

### 4. **Brand (Thương hiệu)**
```
Thương hiệu (Dropdown)
├── Dashboard
├── Market Insights
├── Phân tích Marketplace
└── Cơ hội quảng cáo
```

### 5. **Member & Guest**
- Không có menu đặc biệt
- Chỉ có menu chung: Trang chủ, Marketplace, Diễn đàn, CAD/Thiết kế, Mới

## 🎨 User Dropdown Menu

### Header thông tin user
```
[Avatar] Tên người dùng
         Role (tiếng Việt)
```

### Menu items theo role

#### **Admin/Moderator**
- Dashboard Admin
- Quản lý người dùng
- Quản lý diễn đàn
- Quản lý marketplace

#### **Business Users (Supplier/Manufacturer/Brand)**
- Dashboard riêng
- Chức năng chuyên biệt
- Trạng thái xác minh (Supplier/Manufacturer)

#### **Tất cả users**
- Hồ sơ của tôi
- Đang theo dõi
- Thông báo
- Tin nhắn
- Đã lưu
- My Showcase
- Cài đặt tài khoản
- Doanh nghiệp của tôi (business users)
- Gói đăng ký của tôi
- Đăng xuất

## 🎨 Styling & Colors

### Role-specific colors
- **Admin**: `#dc3545` (Red)
- **Supplier**: `#198754` (Green)
- **Manufacturer**: `#0d6efd` (Blue)
- **Brand**: `#fd7e14` (Orange)

### CSS Classes
```css
#adminDropdown { color: #dc3545 !important; }
#supplierDropdown { color: #198754 !important; }
#manufacturerDropdown { color: #0d6efd !important; }
#brandDropdown { color: #fd7e14 !important; }
```

## 📱 Responsive Design

### Desktop
- Horizontal dropdown menus
- Hover effects
- Animation transitions

### Mobile
- Vertical stacked menus
- Touch-friendly
- Collapsible navigation

## 🔧 Implementation Details

### Files Modified
1. `resources/views/components/unified-header.blade.php`
   - Added role-based navigation menus
   - Updated user dropdown
   - Added CSS styling

2. `app/Models/User.php`
   - Confirmed `getRoleDisplayName()` method exists
   - Confirmed `hasRole()` and `hasAnyRole()` methods

### Key Features
- **Conditional rendering**: `@if(Auth::user()->hasRole('role'))`
- **Role checking**: `hasRole()` and `hasAnyRole()` methods
- **Vietnamese localization**: Role names in Vietnamese
- **Icon integration**: Boxicons for consistency

## 🧪 Testing

### Test Page
- URL: `/test-header`
- Shows current user info
- Lists all test accounts
- Quick access buttons

### Test Accounts
| Role | Email | Password | Dashboard |
|------|-------|----------|-----------|
| Admin | admin@mechamap.test | password123 | /admin |
| Moderator | moderator@mechamap.test | password123 | /admin |
| Supplier | supplier@mechamap.test | password123 | /supplier/dashboard |
| Manufacturer | manufacturer@mechamap.test | password123 | /manufacturer/dashboard |
| Brand | brand@mechamap.test | password123 | /brand/dashboard |
| Member | member@mechamap.test | password123 | /home |
| Guest | guest@mechamap.test | password123 | /home |

### Test Scenarios
1. **Login with different roles**
2. **Check menu visibility**
3. **Test dashboard access**
4. **Verify permission redirects**
5. **Mobile responsiveness**

## 🚀 Usage Instructions

### For Developers
1. **Adding new menu items**:
   ```blade
   @if(Auth::user()->hasRole('role_name'))
   <li><a class="dropdown-item" href="{{ route('route.name') }}">
       <i class="bx bx-icon me-2"></i>Menu Item
   </a></li>
   @endif
   ```

2. **Role checking**:
   ```php
   // Single role
   Auth::user()->hasRole('admin')
   
   // Multiple roles
   Auth::user()->hasAnyRole(['admin', 'moderator'])
   ```

### For Content Managers
1. **Test with different accounts**
2. **Verify menu items work correctly**
3. **Check responsive design**
4. **Validate permissions**

## 🔒 Security Notes

- Menu visibility ≠ Access control
- Always implement backend permission checks
- Use middleware for route protection
- Validate user roles in controllers

## 📈 Future Enhancements

1. **Dynamic menu loading**
2. **User preferences**
3. **Menu customization**
4. **Advanced permissions**
5. **Menu analytics**

## 🐛 Troubleshooting

### Common Issues
1. **Menu not showing**: Check user role and authentication
2. **Wrong redirects**: Verify middleware and routes
3. **Styling issues**: Check CSS classes and responsive design
4. **Permission errors**: Validate role assignments

### Debug Commands
```bash
# Check user roles
php artisan tinker
>>> User::find(1)->role
>>> User::find(1)->hasRole('admin')

# Clear cache
php artisan cache:clear
php artisan view:clear
```

---

**Last Updated**: 2024-12-19
**Version**: 1.0
**Author**: MechaMap Development Team
