# Upload Components Documentation

## Tổng quan

MechaMap sử dụng 2 upload components chính được thiết kế để đáp ứng mọi nhu cầu upload file:

1. **Advanced File Upload** - Upload nhiều file với cấu hình đầy đủ
2. **Avatar Upload** - Upload/sửa ảnh đại diện nhanh chóng

---

## 1. Advanced File Upload Component

### Mô tả
Component upload file đa năng với đầy đủ tính năng: drag & drop, preview, validation, progress bar, và hỗ trợ nhiều loại file.

### Cách sử dụng

```blade
<x-advanced-file-upload
    name="files"
    :file-types="['jpg', 'png', 'pdf', 'doc']"
    max-size="10MB"
    :max-files="5"
    :multiple="true"
    context="showcase"
    upload-text="Kéo thả file vào đây"
    accept-description="Hình ảnh và tài liệu"
/>
```

### Tham số

| Tham số | Kiểu | Mặc định | Mô tả |
|---------|------|----------|-------|
| `name` | string | 'files' | Tên field trong form |
| `id` | string | auto | ID của component |
| `file-types` | array | ['jpg','png','gif','pdf'] | Các loại file được phép |
| `max-size` | string | '5MB' | Kích thước tối đa mỗi file |
| `max-files` | int | 10 | Số lượng file tối đa |
| `multiple` | bool | true | Cho phép chọn nhiều file |
| `drag-drop` | bool | true | Bật tính năng drag & drop |
| `show-preview` | bool | true | Hiển thị preview |
| `show-progress` | bool | true | Hiển thị thanh tiến trình |
| `required` | bool | false | Bắt buộc |
| `context` | string | 'default' | Context để tự động cấu hình |
| `upload-text` | string | auto | Text hiển thị trong upload area |
| `accept-description` | string | auto | Mô tả loại file được chấp nhận |

### Context có sẵn

- **showcase**: Dành cho showcase (hình ảnh, tài liệu, CAD)
- **thread**: Dành cho thảo luận (hình ảnh, tài liệu)
- **product**: Dành cho sản phẩm (hình ảnh)
- **document**: Dành cho tài liệu (PDF, Word, Excel)
- **default**: Cấu hình mặc định

### Ví dụ sử dụng

#### Upload hình ảnh cho showcase
```blade
<x-advanced-file-upload
    name="showcase_images"
    :file-types="['jpg', 'png', 'gif', 'webp']"
    max-size="5MB"
    :max-files="10"
    context="showcase"
/>
```

#### Upload tài liệu
```blade
<x-advanced-file-upload
    name="documents"
    :file-types="['pdf', 'doc', 'docx', 'xls', 'xlsx']"
    max-size="10MB"
    :max-files="3"
    context="document"
    :multiple="true"
/>
```

#### Upload single file
```blade
<x-advanced-file-upload
    name="avatar"
    :file-types="['jpg', 'png']"
    max-size="2MB"
    :multiple="false"
    :required="true"
    context="product"
/>
```

---

## 2. Avatar Upload Component

### Mô tả
Component đơn giản dành riêng cho upload/sửa ảnh đại diện với preview tròn/vuông và tính năng AJAX upload.

### Cách sử dụng

```blade
<x-avatar-upload
    name="avatar"
    :current-avatar="$user->getAvatarUrl()"
    :size="120"
    max-size="2MB"
    shape="circle"
    upload-url="{{ route('profile.avatar.upload') }}"
/>
```

### Tham số

| Tham số | Kiểu | Mặc định | Mô tả |
|---------|------|----------|-------|
| `name` | string | 'avatar' | Tên field trong form |
| `id` | string | auto | ID của component |
| `current-avatar` | string | null | URL ảnh hiện tại |
| `size` | int | 120 | Kích thước preview (px) |
| `max-size` | string | '2MB' | Kích thước file tối đa |
| `required` | bool | false | Bắt buộc |
| `shape` | string | 'circle' | Hình dạng: circle/square/rounded |
| `show-remove` | bool | true | Hiển thị nút xóa |
| `placeholder-text` | string | 'Click to upload avatar' | Text placeholder |
| `upload-url` | string | null | URL để AJAX upload |
| `preview-only` | bool | false | Chỉ hiển thị, không upload |

### Ví dụ sử dụng

#### Avatar tròn cơ bản
```blade
<x-avatar-upload
    name="avatar"
    :current-avatar="$user->avatar_url"
    :size="120"
    shape="circle"
/>
```

#### Avatar vuông với AJAX upload
```blade
<x-avatar-upload
    name="company_logo"
    :current-avatar="$company->logo_url"
    :size="150"
    shape="square"
    max-size="5MB"
    upload-url="{{ route('company.logo.upload') }}"
    placeholder-text="Click để upload logo"
/>
```

#### Avatar chỉ xem (preview only)
```blade
<x-avatar-upload
    :current-avatar="$user->avatar_url"
    :size="80"
    :preview-only="true"
    shape="rounded"
/>
```

### JavaScript API

#### Cập nhật avatar từ bên ngoài
```javascript
// Cập nhật preview avatar
window.updateAvatarPreview('avatar-upload-id', 'https://example.com/new-avatar.jpg');
```

---

## Migration từ Components cũ

### Từ Enhanced Image Upload
```blade
<!-- Cũ -->
<x-enhanced-image-upload
    name="images"
    :max-files="10"
    max-size="5"
/>

<!-- Mới -->
<x-advanced-file-upload
    name="images"
    :file-types="['jpg', 'png', 'gif', 'webp']"
    max-size="5MB"
    :max-files="10"
    context="showcase"
/>
```

### Từ File Upload
```blade
<!-- Cũ -->
<x-file-upload
    name="documents"
    :file-types="['pdf', 'doc']"
    max-size="10MB"
    label="Upload Documents"
/>

<!-- Mới -->
<x-advanced-file-upload
    name="documents"
    :file-types="['pdf', 'doc']"
    max-size="10MB"
    context="document"
    upload-text="Upload Documents"
/>
```

### Từ Avatar Upload thủ công
```blade
<!-- Cũ -->
<input type="file" name="avatar" accept="image/*">
<img id="preview" src="..." class="rounded-circle">

<!-- Mới -->
<x-avatar-upload
    name="avatar"
    :current-avatar="$user->avatar_url"
    shape="circle"
/>
```

---

## Styling & Customization

### CSS Classes có sẵn
- `.advanced-file-upload` - Container chính
- `.upload-area` - Vùng upload
- `.upload-preview` - Vùng preview
- `.avatar-upload-component` - Avatar container
- `.avatar-preview` - Avatar preview

### Dark Mode Support
Cả 2 components đều hỗ trợ dark mode tự động thông qua `[data-bs-theme="dark"]`.

### Responsive
Components tự động responsive trên mobile với grid layout tối ưu.

---

## Validation & Error Handling

### Client-side Validation
- File type validation
- File size validation  
- File count validation
- Real-time error display

### Server-side Integration
Components tương thích với Laravel validation rules:

```php
$request->validate([
    'images.*' => 'image|max:5120', // 5MB
    'documents.*' => 'mimes:pdf,doc,docx|max:10240', // 10MB
    'avatar' => 'image|max:2048' // 2MB
]);
```

---

## Performance & Best Practices

1. **Lazy Loading**: Components chỉ load khi cần thiết
2. **Memory Management**: Tự động cleanup preview URLs
3. **File Validation**: Validate ngay khi chọn file
4. **Progress Feedback**: Hiển thị tiến trình upload
5. **Error Recovery**: Cho phép retry khi lỗi

---

## Browser Support

- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+
- Mobile browsers (iOS Safari, Chrome Mobile)

---

## Troubleshooting

### Component không hiển thị
- Kiểm tra component class đã được tạo
- Verify view file tồn tại
- Check console errors

### Upload không hoạt động
- Kiểm tra CSRF token
- Verify upload route
- Check file permissions

### Preview không hiển thị
- Kiểm tra file type support
- Verify browser compatibility
- Check console for errors
