# TinyMCE Integration Documentation - MechaMap

## Tổng quan

TinyMCE đã được tích hợp thống nhất vào toàn bộ MechaMap project, thay thế CKEditor và các editor khác để cung cấp trải nghiệm chỉnh sửa văn bản nhất quán.

## Kiến trúc

### 1. Core Files

- **`public/js/tinymce-config.js`** - Cấu hình TinyMCE chung
- **`public/js/tinymce-uploader.js`** - Xử lý upload file thống nhất
- **`public/js/tinymce-lang/vi.js`** - Gói ngôn ngữ tiếng Việt
- **`public/css/tinymce-mechamap.css`** - Styling tùy chỉnh
- **`app/View/Components/TinyMCEEditor.php`** - Blade component
- **`resources/views/components/tinymce-editor.blade.php`** - Template component
- **`app/Http/Controllers/Api/TinyMCEController.php`** - Upload controller

### 2. Routes

```php
// API routes cho TinyMCE upload
Route::prefix('api/tinymce')->name('api.tinymce.')->middleware(['auth'])->group(function () {
    Route::post('/upload', [TinyMCEController::class, 'upload'])->name('upload');
    Route::get('/config', [TinyMCEController::class, 'getConfig'])->name('config');
    Route::get('/files', [TinyMCEController::class, 'getFiles'])->name('files');
    Route::delete('/files', [TinyMCEController::class, 'deleteFile'])->name('files.delete');
});
```

## Sử dụng

### 1. Blade Component

```blade
<x-tinymce-editor 
    name="content" 
    id="content"
    :value="old('content')"
    placeholder="Nhập nội dung của bạn..."
    context="comment"
    :height="300"
    :required="true"
    class="@error('content') is-invalid @enderror"
/>
```

### 2. Context Types

- **`comment`** - Cho comment forms (default)
- **`admin`** - Cho admin forms với đầy đủ tính năng
- **`showcase`** - Cho showcase descriptions
- **`minimal`** - Cho forms đơn giản

### 3. JavaScript API

```javascript
// Khởi tạo editor
initializeTinyMCEEditor('editor-id', 'comment', {
    height: 300,
    placeholder: 'Placeholder text'
});

// Lấy nội dung
const content = getTinyMCEContent('editor-id');

// Đặt nội dung
setTinyMCEContent('editor-id', '<p>New content</p>');

// Focus editor
focusTinyMCEEditor('editor-id');

// Hủy editor
destroyTinyMCEEditor('editor-id');
```

## Tính năng

### 1. Upload File

- **Hình ảnh**: JPEG, PNG, GIF, WebP (max 5MB)
- **Tài liệu**: PDF, DOC, DOCX (max 50MB)
- **CAD Files**: DWG, DXF, STEP, STL, OBJ, IGES (max 50MB)
- **Drag & Drop**: Hỗ trợ kéo thả file
- **Paste Images**: Dán hình ảnh từ clipboard

### 2. Responsive Design

- **Mobile-first**: Tối ưu cho thiết bị di động
- **Adaptive toolbar**: Toolbar thích ứng với kích thước màn hình
- **Touch-friendly**: Buttons kích thước phù hợp cho touch

### 3. Accessibility

- **Keyboard navigation**: Điều hướng bằng bàn phím
- **Screen reader support**: Hỗ trợ đọc màn hình
- **High contrast mode**: Chế độ tương phản cao
- **Reduced motion**: Hỗ trợ giảm animation

### 4. Validation

- **Real-time validation**: Validation theo thời gian thực
- **Error styling**: Styling lỗi nhất quán
- **Required field support**: Hỗ trợ trường bắt buộc

## Cấu hình

### 1. Context Configurations

#### Comment Context
```javascript
{
    height: 200,
    plugins: ['advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'searchreplace', 'visualblocks', 'code', 'fullscreen', 'insertdatetime', 'table', 'wordcount', 'emoticons', 'autosave'],
    toolbar: ['undo redo | formatselect | bold italic underline | alignleft aligncenter alignright', 'bullist numlist | outdent indent | blockquote | link image | emoticons | code fullscreen']
}
```

#### Admin Context
```javascript
{
    height: 400,
    plugins: ['advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview', 'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen', 'insertdatetime', 'media', 'table', 'help', 'wordcount', 'emoticons', 'autosave', 'save'],
    toolbar: ['undo redo | formatselect | bold italic underline strikethrough | forecolor backcolor', 'alignleft aligncenter alignright alignjustify | bullist numlist | outdent indent', 'blockquote | link image media | table | code fullscreen | save']
}
```

### 2. Upload Configuration

```javascript
{
    images_upload_url: '/api/tinymce/upload',
    images_upload_credentials: true,
    paste_data_images: true,
    automatic_uploads: true,
    file_picker_types: 'image file media'
}
```

## Tích hợp hiện tại

### 1. Thread Creation
- **File**: `resources/views/threads/create.blade.php`
- **Context**: `admin`
- **Features**: Full editor với upload

### 2. Thread Comments
- **File**: `resources/views/threads/show.blade.php`
- **Context**: `comment`
- **Features**: Comment editor với quote functionality

### 3. Admin Forms
- **Pages**: `resources/views/admin/pages/{create,edit}.blade.php`
- **Threads**: `resources/views/admin/threads/{create,edit}.blade.php`
- **FAQs**: `resources/views/admin/faqs/edit.blade.php`
- **Context**: `admin`

### 4. Showcase Forms
- **File**: `resources/views/threads/create.blade.php`
- **Context**: `showcase`
- **Features**: Project description editor

## Migration từ CKEditor

### 1. Thay đổi đã thực hiện

- ✅ Thay thế CKEditor bằng TinyMCE trong thread creation
- ✅ Cập nhật comment forms
- ✅ Tích hợp vào admin forms
- ✅ Xóa CKEditor scripts cũ
- ✅ Thống nhất upload functionality

### 2. Breaking Changes

- **JavaScript API**: Thay đổi từ CKEditor API sang TinyMCE API
- **Upload endpoints**: Chuyển từ `/upload-images` sang `/api/tinymce/upload`
- **Configuration**: Cấu hình mới thông qua component props

## Troubleshooting

### 1. Editor không khởi tạo

```javascript
// Kiểm tra TinyMCE đã load
if (typeof tinymce === 'undefined') {
    console.error('TinyMCE not loaded');
}

// Kiểm tra element tồn tại
const element = document.getElementById('editor-id');
if (!element) {
    console.error('Editor element not found');
}
```

### 2. Upload không hoạt động

- Kiểm tra CSRF token
- Verify file size và type
- Check network requests trong DevTools
- Kiểm tra permissions

### 3. Mobile issues

- Kiểm tra viewport meta tag
- Verify touch events
- Test trên thiết bị thực

## Performance

### 1. Optimization

- **Lazy loading**: TinyMCE chỉ load khi cần
- **CDN**: Sử dụng TinyMCE CDN cho performance
- **Caching**: Browser caching cho assets
- **Minification**: CSS và JS đã minified

### 2. Bundle Size

- **TinyMCE Core**: ~200KB (gzipped)
- **Plugins**: ~50KB (gzipped)
- **Language pack**: ~10KB (gzipped)
- **Custom code**: ~15KB (gzipped)

## Security

### 1. Upload Security

- File type validation
- File size limits
- CSRF protection
- Authentication required
- Path traversal protection

### 2. Content Security

- HTML sanitization
- XSS prevention
- Content filtering
- Safe HTML output

## Browser Support

- **Chrome**: 90+
- **Firefox**: 88+
- **Safari**: 14+
- **Edge**: 90+
- **Mobile Safari**: 14+
- **Chrome Mobile**: 90+

## Future Enhancements

1. **Real-time collaboration**: Thêm tính năng collaborative editing
2. **Advanced media**: Video và audio embedding
3. **Templates**: Pre-defined content templates
4. **Spell check**: Vietnamese spell checking
5. **Math equations**: LaTeX support cho công thức toán học
6. **Code highlighting**: Syntax highlighting cho code blocks

## Migration Guide cho Developers

### 1. Thêm TinyMCE vào form mới

```blade
{{-- Thay thế textarea cũ --}}
<textarea name="content" id="content">{{ old('content') }}</textarea>

{{-- Bằng TinyMCE component --}}
<x-tinymce-editor
    name="content"
    id="content"
    :value="old('content')"
    placeholder="Nhập nội dung..."
    context="comment"
    :height="300"
    :required="true"
/>
```

### 2. JavaScript Integration

```javascript
// Cũ - CKEditor
ClassicEditor.create(document.querySelector('#content'))
    .then(editor => {
        window.editor = editor;
    });

// Mới - TinyMCE (tự động qua component)
// Hoặc manual:
initializeTinyMCEEditor('content', 'comment', {
    height: 300,
    placeholder: 'Placeholder text'
});
```

### 3. Form Validation

```javascript
// Cũ - CKEditor
const content = window.editor.getData();

// Mới - TinyMCE
const content = getTinyMCEContent('content');
// Hoặc
const editor = tinymce.get('content');
const content = editor ? editor.getContent() : '';
```

### 4. Upload Handling

```javascript
// Upload endpoint đã thay đổi
// Cũ: /upload-images
// Mới: /api/tinymce/upload

// Validation cũng đã cập nhật
// Images: max 5MB
// Documents: max 50MB
// CAD files: max 50MB
```

## Testing Checklist

### 1. Functional Testing

- [ ] Editor khởi tạo thành công
- [ ] Content được save đúng
- [ ] Upload hình ảnh hoạt động
- [ ] Upload documents hoạt động
- [ ] Drag & drop hoạt động
- [ ] Paste images hoạt động
- [ ] Validation hiển thị đúng
- [ ] Quote functionality hoạt động
- [ ] Mobile responsive

### 2. Browser Testing

- [ ] Chrome (latest)
- [ ] Firefox (latest)
- [ ] Safari (latest)
- [ ] Edge (latest)
- [ ] Mobile Safari
- [ ] Chrome Mobile

### 3. Performance Testing

- [ ] Page load time < 3s
- [ ] Editor initialization < 1s
- [ ] Upload response < 5s
- [ ] No memory leaks
- [ ] Smooth scrolling

## Support

Để được hỗ trợ về TinyMCE integration:

1. Kiểm tra documentation này
2. Xem browser console cho errors
3. Test trên browsers khác nhau
4. Kiểm tra network requests
5. Verify file permissions và paths
6. Check TinyMCE version compatibility
7. Validate CSRF tokens
8. Test upload file sizes và types
