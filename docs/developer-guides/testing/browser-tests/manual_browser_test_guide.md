# 🧪 Hướng Dẫn Test Thủ Công Thread Creation Flow

## 📋 Checklist Kiểm Tra Quy Trình Tạo Thread

### Bước 1: Đăng Nhập
- [ ] Truy cập: `http://127.0.0.1:8000/login`
- [ ] Đăng nhập với:
  - Email: `leminh.cnc@gmail.com`
  - Password: `password123`
- [ ] Kiểm tra đăng nhập thành công

### Bước 2: Test Forum Selection Flow
- [ ] Truy cập: `http://127.0.0.1:8000/create-thread`
- [ ] Kiểm tra hiển thị danh sách forums
- [ ] Chọn một forum bất kỳ
- [ ] Kiểm tra redirect đến `/threads/create?forum_id=X`

### Bước 3: Test Thread Creation Form
- [ ] Truy cập trực tiếp: `http://127.0.0.1:8000/threads/create`
- [ ] Kiểm tra redirect về forum selection nếu không có `forum_id`
- [ ] Truy cập với forum_id: `http://127.0.0.1:8000/threads/create?forum_id=1`
- [ ] Kiểm tra form thread creation hiển thị đúng
- [ ] Kiểm tra các trường form:
  - [ ] Title
  - [ ] Content
  - [ ] Tags
  - [ ] Privacy settings
  - [ ] Buttons

### Bước 4: Test Form Submission
- [ ] Điền thông tin thread:
  - Title: `Test Thread Manual Creation`
  - Content: `Nội dung test thủ công quy trình tạo thread`
  - Tags: `test, manual`
- [ ] Submit form
- [ ] Kiểm tra redirect về thread detail page
- [ ] Kiểm tra thread hiển thị đúng thông tin

### Bước 5: Verification
- [ ] Kiểm tra thread trong database
- [ ] Kiểm tra thread xuất hiện trong forum
- [ ] Kiểm tra thread trong profile user
- [ ] Test các chức năng: edit, delete, comment

## 🐛 Troubleshooting

### Nếu gặp lỗi 404:
1. Kiểm tra routes: `php artisan route:list --name=threads`
2. Clear cache: `php artisan route:clear`
3. Kiểm tra middleware authentication

### Nếu gặp lỗi CSRF:
1. Kiểm tra `@csrf` token trong form
2. Kiểm tra middleware `VerifyCsrfToken` trong Kernel.php

### Nếu gặp lỗi validation:
1. Kiểm tra `ThreadRequest` validation rules
2. Kiểm tra required fields trong form

## 📊 Expected Results

### Successful Flow:
1. `/login` → Dashboard/Home
2. `/create-thread` → Forum selection page
3. Select forum → `/threads/create?forum_id=X`
4. Fill form → Submit → `/threads/{id}` (thread detail)

### Database Records:
- New thread trong `threads` table
- Thread-forum relationship
- User-thread relationship
- Tags relationships (nếu có)

## 🔍 Debug Commands

```bash
# Check routes
php artisan route:list --name=threads

# Clear all cache
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Check database
php artisan tinker
>>> App\Models\Thread::latest()->take(5)->get(['id', 'title', 'created_at'])
>>> App\Models\User::find(1)->threads->count()
```

## 📝 Test Results Log

**Date**: ___________
**Tester**: ___________

### Results:
- [ ] ✅ Login successful
- [ ] ✅ Forum selection works  
- [ ] ✅ Thread creation form displays
- [ ] ✅ Form submission successful
- [ ] ✅ Thread saved to database
- [ ] ✅ Thread displays correctly

### Issues Found:
_Write any issues here_

### Notes:
_Additional observations_
