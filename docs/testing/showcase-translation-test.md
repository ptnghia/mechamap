# Test Plan: Showcase Translation Keys

## Mục tiêu
Kiểm tra tất cả translation keys trong modal tạo showcase đã được internationalize đúng cách.

## Translation Keys Added

### Vietnamese (resources/lang/vi/showcase.php)
```php
// File Upload
'file_attachments' => 'File đính kèm',
'file_attachments_optional' => 'Tùy chọn',
'file_upload_area' => 'Kéo thả file vào đây hoặc',
'browse_files' => 'chọn file',
'file_upload_help' => 'Hỗ trợ: Images (JPG, PNG, GIF, WebP), Documents (PDF, DOC, DOCX), CAD Files (DWG, DXF, STEP, STL)',
'file_upload_limits' => 'Tối đa 10 file, mỗi file không quá 50MB',
'file_upload_description' => 'Thêm file CAD, hình ảnh, tài liệu kỹ thuật để minh họa cho dự án của bạn',
'files_selected' => 'File đã chọn:',
'file_too_large' => 'File ":filename" quá lớn. Kích thước tối đa là 50MB.',
'file_type_not_supported' => 'File ":filename" không được hỗ trợ.',
'max_files_exceeded' => 'Chỉ được chọn tối đa 10 file',
'file_validation_failed' => 'Vui lòng kiểm tra lại các file đã chọn',

// File Types
'file_types' => [
    'image' => 'Hình ảnh',
    'document' => 'Tài liệu',
    'cad' => 'File CAD',
    'spreadsheet' => 'Bảng tính',
    'presentation' => 'Thuyết trình',
    'archive' => 'File nén',
    'other' => 'Khác',
],
```

### English (resources/lang/en/showcase.php)
```php
// File Upload
'file_attachments' => 'File Attachments',
'file_attachments_optional' => 'Optional',
'file_upload_area' => 'Drag and drop files here or',
'browse_files' => 'browse files',
'file_upload_help' => 'Supported: Images (JPG, PNG, GIF, WebP), Documents (PDF, DOC, DOCX), CAD Files (DWG, DXF, STEP, STL)',
'file_upload_limits' => 'Maximum 10 files, each file no more than 50MB',
'file_upload_description' => 'Add CAD files, images, technical documents to illustrate your project',
'files_selected' => 'Selected files:',
'file_too_large' => 'File ":filename" is too large. Maximum size is 50MB.',
'file_type_not_supported' => 'File ":filename" is not supported.',
'max_files_exceeded' => 'Maximum 10 files allowed',
'file_validation_failed' => 'Please check the selected files',

// File Types
'file_types' => [
    'image' => 'Image',
    'document' => 'Document',
    'cad' => 'CAD File',
    'spreadsheet' => 'Spreadsheet',
    'presentation' => 'Presentation',
    'archive' => 'Archive',
    'other' => 'Other',
],
```

## Test Cases

### Test Case 1: Modal Header và Steps
**Kiểm tra:**
- [x] `{{ __('showcase.create_from_thread_title') }}`
- [x] `{{ __('showcase.basic_info') }}`
- [x] `{{ __('showcase.content') }}`
- [x] `{{ __('showcase.complete') }}`

### Test Case 2: Form Labels
**Kiểm tra:**
- [x] `{{ __('showcase.showcase_title') }}`
- [x] `{{ __('showcase.category') }}`
- [x] `{{ __('showcase.project_type') }}`
- [x] `{{ __('showcase.project_description') }}`
- [x] `{{ __('showcase.cover_image') }}`
- [x] `{{ __('showcase.complexity_level') }}`
- [x] `{{ __('showcase.industry_application') }}`

### Test Case 3: File Upload Section
**Kiểm tra:**
- [x] `{{ __('showcase.file_attachments') }}`
- [x] `{{ __('showcase.file_attachments_optional') }}`
- [x] `{{ __('showcase.file_upload_area') }}`
- [x] `{{ __('showcase.browse_files') }}`
- [x] `{{ __('showcase.file_upload_help') }}`
- [x] `{{ __('showcase.file_upload_limits') }}`
- [x] `{{ __('showcase.files_selected') }}`

### Test Case 4: Validation Messages
**Kiểm tra:**
- [x] `{{ __('showcase.title_required') }}`
- [x] `{{ __('showcase.category_required') }}`
- [x] `{{ __('showcase.description_required') }}`
- [x] `{{ __('showcase.cover_image_required') }}`
- [x] `{{ __('showcase.file_size_error') }}`
- [x] `{{ __('showcase.terms_required') }}`

### Test Case 5: Dynamic Messages
**Kiểm tra:**
- [x] File size validation với filename parameter
- [x] File type validation với filename parameter
- [x] Max files exceeded message

### Test Case 6: Buttons
**Kiểm tra:**
- [x] `{{ __('showcase.previous') }}`
- [x] `{{ __('showcase.next') }}`
- [x] `{{ __('showcase.create_showcase') }}`
- [x] `{{ __('showcase.creating') }}`
- [x] `{{ __('ui.actions.cancel') }}`

## Manual Testing Steps

### 1. Test Vietnamese Language
```bash
# Set app locale to Vietnamese
php artisan config:cache
```

1. Mở trang thread detail
2. Click "Tạo Showcase"
3. Kiểm tra tất cả text hiển thị bằng tiếng Việt
4. Test validation messages
5. Test file upload messages

### 2. Test English Language
```bash
# Change locale in config/app.php to 'en'
# Or use language switcher if available
```

1. Open thread detail page
2. Click "Create Showcase"
3. Verify all text displays in English
4. Test validation messages
5. Test file upload messages

## Automated Testing

### Check Missing Keys
```bash
# Search for hardcoded Vietnamese text
grep -r "[\u00C0-\u1EF9]" resources/views/threads/partials/showcase.blade.php

# Search for hardcoded English text
grep -r -E "(Please|Click|Submit|Cancel|Save|Delete|Edit|View|Search)" resources/views/threads/partials/showcase.blade.php

# Check for missing translation calls
grep -r -v "__(" resources/views/threads/partials/showcase.blade.php | grep -E ">[^<]*[a-zA-Z]{3,}[^<]*<"
```

### Validate Translation Files
```bash
# Check syntax
php -l resources/lang/vi/showcase.php
php -l resources/lang/en/showcase.php

# Check for missing keys
php artisan lang:check
```

## Expected Results

### ✅ All Hardcoded Text Replaced
- No Vietnamese hardcoded text in blade file
- No English hardcoded text in blade file
- All user-facing text uses translation keys

### ✅ Translation Keys Work
- Vietnamese locale shows Vietnamese text
- English locale shows English text
- Parameter replacement works (e.g., :filename)

### ✅ Consistent Naming
- All keys follow `showcase.*` pattern
- File upload keys grouped logically
- Validation keys grouped together

## Common Issues & Solutions

### Issue 1: Missing Translation Key
**Error:** `Translation key [showcase.xxx] not found`
**Solution:** Add missing key to both vi and en files

### Issue 2: Parameter Not Replaced
**Error:** `:filename` shows literally instead of actual filename
**Solution:** Check parameter syntax in translation and usage

### Issue 3: Hardcoded Text Still Visible
**Error:** Some text not translated
**Solution:** Search and replace remaining hardcoded text

## Completion Checklist

- [x] All form labels translated
- [x] All validation messages translated
- [x] All button text translated
- [x] All help text translated
- [x] All file upload messages translated
- [x] All JavaScript alert messages translated
- [x] Parameter replacement working
- [x] Both Vietnamese and English complete
- [x] No hardcoded text remaining
- [x] Translation files syntax valid
