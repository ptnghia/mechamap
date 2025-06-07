# MechaMap - Hướng Dẫn CSS/JS Truyền Thống

## 📋 Tổng Quan

Dự án đã được chuyển đổi từ Vite sang sử dụng CSS/JS truyền thống để đơn giản hóa quá trình phát triển và triển khai.

## 📁 Cấu Trúc File

### CSS Files (public/css/)
```
public/css/
├── app.css              # CSS chính tổng hợp
├── dark-mode.css        # Hỗ trợ chế độ tối
├── auth-modal.css       # Styles cho modal đăng nhập
├── search.css           # Styles cho tính năng tìm kiếm
├── home.css            # Styles cho trang chủ
├── whats-new.css       # Styles cho trang What's New
├── activity.css        # Styles cho trang hoạt động
├── alerts.css          # Styles cho thông báo
├── sidebar.css         # Styles cho sidebar
├── compact-theme.css   # Theme compact
├── buttons.css         # Styles cho buttons
├── forms.css           # Styles cho forms
└── avatar.css          # Styles cho avatar
```

### JavaScript Files (public/js/)
```
public/js/
├── app.js              # JavaScript chính tổng hợp
├── dark-mode.js        # Xử lý chuyển đổi theme
├── auth-modal.js       # Xử lý modal đăng nhập
├── search.js           # Xử lý tìm kiếm
└── manual-dropdown.js  # Xử lý dropdown thủ công
```

## 🚀 Cách Sử Dụng

### 1. Load CSS/JS trong Layout
```blade
<!-- CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
<link rel="stylesheet" href="{{ asset('css/dark-mode.css') }}">

<!-- JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/app.js') }}"></script>
<script src="{{ asset('js/dark-mode.js') }}"></script>
```

### 2. Sử dụng JavaScript Utilities

#### HTTP Requests (thay thế axios)
```javascript
// GET request
const data = await window.http.get('/api/users');

// POST request
const response = await window.http.post('/api/users', {
    name: 'John Doe',
    email: 'john@example.com'
});

// PUT request
await window.http.put('/api/users/1', userData);

// DELETE request
await window.http.delete('/api/users/1');
```

#### DOM Manipulation
```javascript
// Query selectors
const element = window.dom.$('#my-element');
const elements = window.dom.$$('.my-class');

// Event listeners
window.dom.on('#button', 'click', function() {
    console.log('Button clicked!');
});

// Show/Hide elements
window.dom.show('#modal');
window.dom.hide('#modal');
window.dom.toggle('#sidebar');
```

#### Form Validation
```javascript
// Validate form
const validation = window.validation.validateForm(form);
if (!validation.isValid) {
    window.validation.displayErrors(validation.errors);
}

// Individual validations
const isEmail = window.validation.isEmail('test@example.com');
const isRequired = window.validation.isRequired('value');
```

#### Notifications
```javascript
// Show notifications
window.showNotification('Thành công!', 'success');
window.showNotification('Có lỗi xảy ra!', 'error');
window.showNotification('Cảnh báo!', 'warning');
window.showNotification('Thông tin', 'info');
```

#### Theme Management
```javascript
// Get current theme
const theme = window.theme.get();

// Set theme
window.theme.set('dark');

// Toggle theme
window.theme.toggle();
```

#### Form Submission
```javascript
// Submit form with validation
window.forms.submit('#my-form', {
    onSuccess: (response) => {
        console.log('Success:', response);
    },
    onError: (error) => {
        console.log('Error:', error);
    }
});

// Reset form
window.forms.reset('#my-form');
```

### 3. HTML Data Attributes

#### Form Validation
```html
<form data-validate="true">
    <input name="email" data-validate="required|email" type="email">
    <input name="password" data-validate="required|min:8" type="password">
    <button type="submit">Đăng nhập</button>
</form>
```

#### Confirmation Dialogs
```html
<button data-confirm="Bạn có chắc chắn muốn xóa?">
    Xóa
</button>
```

#### Modal Controls
```html
<button data-bs-dismiss="modal">Đóng Modal</button>
```

## 🎨 CSS Classes và Utilities

### Custom Button Classes
```html
<button class="btn-modern btn-primary">Button Modern</button>
<button class="btn-modern btn-secondary">Button Secondary</button>
<button class="btn-modern btn-outline">Button Outline</button>
```

### Form Classes
```html
<div class="form-modern">
    <label class="form-label">Email</label>
    <input class="form-control" type="email">
    <div class="invalid-feedback">Email không hợp lệ</div>
</div>
```

### Card Classes
```html
<div class="card-modern">
    <div class="card-header">Tiêu đề</div>
    <div class="card-body">Nội dung</div>
    <div class="card-footer">Footer</div>
</div>
```

### Utility Classes
```html
<!-- Typography -->
<p class="text-xs">Text extra small</p>
<p class="text-sm">Text small</p>
<p class="text-lg">Text large</p>

<!-- Colors -->
<p class="text-primary">Primary text</p>
<p class="text-success">Success text</p>
<div class="bg-primary">Primary background</div>

<!-- Spacing -->
<div class="p-3 m-2 gap-4">Padding, margin, gap</div>

<!-- Border radius -->
<div class="rounded-sm">Small radius</div>
<div class="rounded-lg">Large radius</div>

<!-- Shadows -->
<div class="shadow-sm">Small shadow</div>
<div class="shadow-lg">Large shadow</div>
```

## 🌙 Dark Mode

Dark mode được quản lý tự động:

```html
<!-- Theme toggle button -->
<button id="theme-toggle">
    <i class="bi bi-sun dark-icon"></i>
    <i class="bi bi-moon-stars light-icon d-none"></i>
</button>
```

Theme được lưu trong localStorage và áp dụng qua attribute `data-theme="dark"` trên `<html>`.

## 📱 Responsive Design

Tất cả components đều responsive với Bootstrap breakpoints:
- `xs`: < 576px
- `sm`: ≥ 576px  
- `md`: ≥ 768px
- `lg`: ≥ 992px
- `xl`: ≥ 1200px
- `xxl`: ≥ 1400px

## 🔧 Development Tips

### 1. Thêm CSS Mới
- Tạo file CSS trong `public/css/`
- Include trong layout blade template
- Hoặc thêm vào `public/css/app.css`

### 2. Thêm JavaScript Mới
- Tạo file JS trong `public/js/`
- Include trong layout blade template  
- Hoặc thêm vào `public/js/app.js`

### 3. Debug
- Sử dụng browser DevTools
- Console.log để debug JavaScript
- CSS inspector để debug styles

### 4. Performance
- CSS/JS được load từ CDN (Bootstrap) và local files
- Minify CSS/JS cho production
- Sử dụng browser caching

## 📚 Dependencies

### External CDN:
- Bootstrap 5.3.2 (CSS & JS)
- Bootstrap Icons 1.11.3
- Font Awesome 6.5.1
- jQuery 3.7.1 (cho Lightbox)
- Lightbox2 2.11.4
- CKEditor 5 (conditional load)

### Local Files:
- Custom CSS/JS files trong `public/`
- Google Fonts (Inter)

## 🚀 Deployment

1. Upload toàn bộ thư mục `public/` lên server
2. Đảm bảo server có thể serve static files
3. Không cần build process hay npm commands
4. Chỉ cần PHP và web server (Apache/Nginx)

## 📞 Support

Nếu gặp vấn đề:
1. Kiểm tra browser console cho JavaScript errors
2. Kiểm tra Network tab cho failed requests
3. Đảm bảo file paths đúng
4. Xác minh CSS/JS files được load thành công
