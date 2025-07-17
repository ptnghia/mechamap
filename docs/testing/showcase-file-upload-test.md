# Test Plan: Showcase File Upload Feature

## Mục tiêu
Kiểm tra tính năng upload file đính kèm trong modal tạo showcase từ thread.

## Chuẩn bị Test

### 1. Tạo test files
```bash
# Tạo thư mục test files
mkdir -p storage/test-files

# Tạo file test nhỏ
echo "Test PDF content" > storage/test-files/test-document.pdf
echo "Test CAD content" > storage/test-files/test-model.dwg
echo "Test image content" > storage/test-files/test-image.jpg
```

### 2. Kiểm tra permissions
```bash
# Đảm bảo thư mục có quyền ghi
chmod 755 public/images/showcases/attachments
```

## Test Cases

### Test Case 1: UI File Upload
**Mục tiêu:** Kiểm tra giao diện upload file

**Bước thực hiện:**
1. Mở trang thread detail có thể tạo showcase
2. Click button "Tạo Showcase"
3. Điều hướng đến Step 2 (Content)
4. Kiểm tra section "File đính kèm"

**Kết quả mong đợi:**
- [x] Hiển thị drag & drop area
- [x] Button "chọn file" hoạt động
- [x] Hiển thị text hướng dẫn về file types và size limits
- [x] Input file có attribute multiple và accept đúng

### Test Case 2: File Selection và Preview
**Mục tiêu:** Kiểm tra chọn file và preview

**Bước thực hiện:**
1. Click "chọn file" hoặc drag & drop files
2. Chọn các loại file khác nhau (image, PDF, CAD)
3. Kiểm tra preview hiển thị

**Kết quả mong đợi:**
- [x] File được thêm vào danh sách
- [x] Preview hiển thị đúng icon cho từng loại file
- [x] Hiển thị tên file và kích thước
- [x] Button remove file hoạt động
- [x] Image files hiển thị thumbnail

### Test Case 3: File Validation
**Mục tiêu:** Kiểm tra validation file

**Test data:**
- File quá lớn (>50MB)
- File không được hỗ trợ (.exe, .bat)
- Quá 10 files

**Kết quả mong đợi:**
- [x] Hiển thị error message cho file quá lớn
- [x] Hiển thị error message cho file type không hỗ trợ
- [x] Hiển thị error message khi chọn quá 10 files

### Test Case 4: Form Submission
**Mục tiêu:** Kiểm tra submit form với file attachments

**Bước thực hiện:**
1. Điền đầy đủ form tạo showcase
2. Upload 2-3 files đính kèm
3. Submit form

**Kết quả mong đợi:**
- [x] Form submit thành công
- [x] Files được upload vào thư mục đúng
- [x] Database records được tạo trong bảng media
- [x] Showcase được tạo với file_attachments JSON

### Test Case 5: Showcase Detail Display
**Mục tiêu:** Kiểm tra hiển thị file attachments trong showcase detail

**Bước thực hiện:**
1. Truy cập showcase vừa tạo
2. Kiểm tra section "Tài liệu đính kèm"

**Kết quả mong đợi:**
- [x] Hiển thị danh sách file attachments
- [x] Icon đúng cho từng loại file
- [x] Tên file và kích thước hiển thị chính xác
- [x] Link download hoạt động
- [x] Phân biệt được images và non-image files

## Test Commands

### Kiểm tra database
```sql
-- Kiểm tra showcase được tạo
SELECT * FROM showcases ORDER BY created_at DESC LIMIT 5;

-- Kiểm tra media files
SELECT * FROM media WHERE mediable_type = 'App\\Models\\Showcase' ORDER BY created_at DESC LIMIT 10;

-- Kiểm tra file_attachments JSON
SELECT id, title, file_attachments FROM showcases WHERE file_attachments IS NOT NULL;
```

### Kiểm tra files trong storage
```bash
# Kiểm tra files được upload
ls -la public/images/showcases/attachments/

# Kiểm tra kích thước files
du -h public/images/showcases/attachments/*
```

## Debugging

### Log files để kiểm tra
```bash
# Laravel logs
tail -f storage/logs/laravel.log

# Web server logs
tail -f /var/log/nginx/error.log  # hoặc Apache logs
```

### Common Issues và Solutions

1. **File không upload được**
   - Kiểm tra PHP upload_max_filesize và post_max_size
   - Kiểm tra permissions thư mục
   - Kiểm tra disk space

2. **Validation errors**
   - Kiểm tra file types trong validation rules
   - Kiểm tra file size limits

3. **Database errors**
   - Kiểm tra foreign key constraints
   - Kiểm tra JSON format trong file_attachments

## Performance Testing

### Test với nhiều files
```bash
# Tạo nhiều test files
for i in {1..10}; do
    echo "Test content $i" > storage/test-files/test-file-$i.pdf
done
```

### Monitor resource usage
```bash
# Kiểm tra memory usage khi upload
top -p $(pgrep php-fpm)

# Kiểm tra disk I/O
iostat -x 1
```

## Security Testing

### Test malicious files
1. Upload file với extension giả mạo (.jpg.exe)
2. Upload file với tên chứa ký tự đặc biệt
3. Upload file với content không khớp extension

**Kết quả mong đợi:**
- [x] Hệ thống từ chối file malicious
- [x] Không có code injection
- [x] File được lưu với tên safe

## Cleanup sau test
```bash
# Xóa test files
rm -rf storage/test-files/
rm -rf public/images/showcases/attachments/test-*

# Reset test data trong database nếu cần
# php artisan migrate:refresh --seed
```
