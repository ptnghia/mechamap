# File Upload Component - Hướng dẫn sử dụng

## Tổng quan

File Upload Component là một component Blade có thể tái sử dụng cho việc upload file trong toàn bộ hệ thống MechaMap. Component này cung cấp giao diện drag & drop hiện đại, validation phía client, preview file và nhiều tính năng khác.

## Tính năng chính

- ✅ **Drag & Drop**: Kéo thả file trực tiếp vào vùng upload
- ✅ **File Preview**: Hiển thị thumbnail cho hình ảnh và icon cho file khác
- ✅ **Validation**: Kiểm tra loại file và kích thước phía client
- ✅ **Progress Bar**: Hiển thị tiến trình upload
- ✅ **Multiple Files**: Hỗ trợ upload một hoặc nhiều file
- ✅ **Responsive**: Tương thích với mobile và desktop
- ✅ **Translation**: Hỗ trợ đa ngôn ngữ
- ✅ **Customizable**: Có thể tùy chỉnh theo nhu cầu

## Cài đặt

Component đã được tích hợp sẵn trong hệ thống MechaMap. Các file liên quan:

```
resources/views/components/file-upload.blade.php  # Component chính
public/css/frontend/components/file-upload.css    # CSS styling
public/js/frontend/components/file-upload.js      # JavaScript functionality
```

## Cách sử dụng cơ bản

### Upload một file hình ảnh

```blade
<x-file-upload 
    name="avatar"
    :file-types="['jpg', 'png', 'gif']"
    max-size="2MB"
    :required="true"
/>
```

### Upload nhiều file hình ảnh

```blade
<x-file-upload 
    name="images"
    :file-types="['jpg', 'jpeg', 'png', 'gif', 'webp']"
    max-size="5MB"
    :multiple="true"
    :max-files="10"
/>
```

### Upload file tài liệu

```blade
<x-file-upload 
    name="documents"
    :file-types="['pdf', 'doc', 'docx']"
    max-size="10MB"
    :multiple="true"
    :max-files="5"
    label="Tải lên tài liệu"
/>
```

### Upload file CAD

```blade
<x-file-upload 
    name="cad_files"
    :file-types="['dwg', 'dxf', 'step', 'stp', 'stl', 'obj']"
    max-size="50MB"
    :multiple="true"
    :max-files="3"
    label="Tải lên file CAD"
/>
```

## Tham số (Parameters)

| Tham số | Kiểu | Mặc định | Mô tả |
|---------|------|----------|-------|
| `name` | string | `'files'` | Tên của input field |
| `fileTypes` | array | `['jpg', 'png', 'gif', 'pdf']` | Các loại file được phép |
| `maxSize` | string\|int | `'5MB'` | Dung lượng tối đa mỗi file |
| `multiple` | boolean | `false` | Cho phép upload nhiều file |
| `accept` | string\|null | `null` | MIME types (auto-generate nếu null) |
| `required` | boolean | `false` | Trường bắt buộc |
| `label` | string\|null | `null` | Label cho input (auto-generate nếu null) |
| `helpText` | string\|null | `null` | Text hướng dẫn (auto-generate nếu null) |
| `maxFiles` | int | `10` | Số file tối đa khi multiple=true |
| `showProgress` | boolean | `true` | Hiển thị progress bar |
| `showPreview` | boolean | `true` | Hiển thị preview file |
| `dragDrop` | boolean | `true` | Cho phép drag & drop |
| `id` | string\|null | `null` | ID của component (auto-generate nếu null) |

## Các loại file được hỗ trợ

### Hình ảnh
- `jpg`, `jpeg`, `png`, `gif`, `webp`

### Tài liệu
- `pdf`, `doc`, `docx`

### File CAD
- `dwg`, `dxf`, `step`, `stp`, `stl`, `obj`, `iges`, `igs`

## Ví dụ sử dụng trong form

```blade
<form action="{{ route('threads.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    
    <div class="mb-3">
        <label for="title" class="form-label">Tiêu đề</label>
        <input type="text" class="form-control" id="title" name="title" required>
    </div>
    
    <div class="mb-3">
        <label for="content" class="form-label">Nội dung</label>
        <textarea class="form-control" id="content" name="content" rows="5" required></textarea>
    </div>
    
    <!-- File Upload Component -->
    <x-file-upload 
        name="attachments"
        :file-types="['jpg', 'png', 'pdf', 'doc', 'docx']"
        max-size="10MB"
        :multiple="true"
        :max-files="5"
        label="Đính kèm file"
    />
    
    <button type="submit" class="btn btn-primary">Đăng bài</button>
</form>
```

## JavaScript API

Component tự động khởi tạo và có thể được truy cập thông qua:

```javascript
// Lấy instance của component
const uploadComponent = window.fileUploadComponents['component-id'];

// Các method có sẵn
uploadComponent.getFiles();           // Lấy danh sách file đã chọn
uploadComponent.clearFiles();         // Xóa tất cả file
uploadComponent.addFiles(files);      // Thêm file programmatically
uploadComponent.setProgress(50);      // Set progress bar (0-100)
uploadComponent.reset();              // Reset component về trạng thái ban đầu
```

## Events

Component phát ra các custom events:

```javascript
// Lắng nghe events
document.addEventListener('fileUpload:filesAdded', function(e) {
    console.log('Files added:', e.detail.files);
});

document.addEventListener('fileUpload:fileRemoved', function(e) {
    console.log('File removed:', e.detail.file);
});

document.addEventListener('fileUpload:validationError', function(e) {
    console.log('Validation error:', e.detail.message);
});
```

## Validation

### Phía Client
- Kiểm tra loại file dựa trên extension
- Kiểm tra kích thước file
- Kiểm tra số lượng file (khi multiple=true)

### Phía Server
Bạn cần thêm validation rules trong Controller:

```php
$request->validate([
    'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB
    'documents.*' => 'required|mimes:pdf,doc,docx|max:10240', // 10MB
]);
```

## Customization

### Custom CSS
Bạn có thể override CSS classes:

```css
.file-upload-component .upload-zone {
    border-color: #your-color;
    background-color: #your-bg-color;
}

.file-upload-component .file-preview-item {
    border-radius: 10px;
    /* Your custom styles */
}
```

### Custom Labels và Help Text

```blade
<x-file-upload 
    name="files"
    :file-types="['jpg', 'png']"
    label="<i class='fas fa-camera'></i> Chọn ảnh đại diện"
    help-text="Chỉ chấp nhận file JPG, PNG. Kích thước tối đa 2MB."
/>
```

## Troubleshooting

### Component không hiển thị
- Kiểm tra CSS và JS files đã được load
- Kiểm tra console browser có lỗi không

### Drag & drop không hoạt động
- Kiểm tra `drag-drop="true"`
- Kiểm tra browser có hỗ trợ HTML5 drag & drop

### File không được upload
- Kiểm tra form có `enctype="multipart/form-data"`
- Kiểm tra server-side validation
- Kiểm tra file size limits trong PHP config

### Preview không hiển thị
- Kiểm tra `show-preview="true"`
- Kiểm tra file có phải là image không

## Migration từ code cũ

Nếu bạn đang sử dụng code upload file cũ, hãy thay thế bằng component:

### Trước (code cũ):
```blade
<div class="file-upload-area">
    <input type="file" name="images[]" multiple accept="image/*">
    <!-- Custom upload code -->
</div>
```

### Sau (sử dụng component):
```blade
<x-file-upload 
    name="images"
    :file-types="['jpg', 'png', 'gif']"
    :multiple="true"
/>
```

## Hỗ trợ

Nếu gặp vấn đề, vui lòng:
1. Kiểm tra documentation này
2. Kiểm tra console browser có lỗi
3. Liên hệ team development
