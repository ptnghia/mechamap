# 🚀 Unified Upload Migration Plan

## 📋 Tổng Quan

**Mục tiêu**: Thống nhất tất cả upload file về cấu trúc `public/uploads/{user_id}/{category}/`

**Nguyên tắc**: 
- ✅ **Giữ nguyên** tất cả file cũ trong `public/images/` và `storage/app/public/`
- ✅ **Chỉ áp dụng** cấu trúc mới cho upload từ bây giờ
- ✅ **Không broken links** cho file đã tồn tại

## 🎯 Phase 1: Chuẩn Bị Service

### ✅ **Đã hoàn thành:**
1. **UnifiedUploadService.php** - Service thống nhất cho upload
2. **MediaService.php** - Service hiện có (có thể tái sử dụng)
3. **Advanced File Upload Components** - UI components đã sẵn sàng

### 🔄 **Cần làm:**
1. **Test UnifiedUploadService** với các file type khác nhau
2. **Tạo helper methods** cho legacy file handling
3. **Update config** nếu cần thiết

## 🎯 Phase 2: Migration Controllers

### **1. CommentController.php** 
**Priority: HIGH** (Đã test thành công)

**Current:**
```php
$imagePath = $image->store('comment-images', 'public');
// Lưu vào: storage/app/public/comment-images/
```

**Target:**
```php
$media = $this->unifiedUploadService->uploadFile($image, $user, 'comments');
// Lưu vào: public/uploads/{user_id}/comments/
```

**Steps:**
1. Inject UnifiedUploadService vào controller
2. Replace upload logic trong `store()` method
3. Update response để return media info
4. Test với Advanced File Upload component

---

### **2. GalleryController.php**
**Priority: HIGH**

**Current:**
```php
$filePath = $file->storeAs('uploads/gallery', $fileName, 'public');
// Lưu vào: storage/app/public/uploads/gallery/
```

**Target:**
```php
$media = $this->unifiedUploadService->uploadFile($file, $user, 'gallery');
// Lưu vào: public/uploads/{user_id}/gallery/
```

**Steps:**
1. Update `store()` method
2. Handle multiple file uploads
3. Update gallery display logic
4. Test gallery functionality

---

### **3. ThreadController.php**
**Priority: MEDIUM**

**Current:**
```php
$imagePath = $image->store('thread-images', 'public');
// Lưu vào: storage/app/public/thread-images/
```

**Target:**
```php
$media = $this->unifiedUploadService->uploadFile($image, $user, 'threads');
// Lưu vào: public/uploads/{user_id}/threads/
```

**Steps:**
1. Update thread creation logic
2. Handle thread image uploads
3. Update thread display
4. Test thread functionality

---

### **4. ShowcaseController.php**
**Priority: MEDIUM**

**Current:**
```php
$attachmentPath = $attachment->store('showcases/attachments', 'public');
// Lưu vào: storage/app/public/showcases/attachments/
```

**Target:**
```php
$media = $this->unifiedUploadService->uploadFile($attachment, $user, 'showcases');
// Lưu vào: public/uploads/{user_id}/showcases/
```

**Steps:**
1. Update showcase creation
2. Handle multiple attachments
3. Update showcase display
4. Test showcase functionality

---

### **5. Admin/UserController.php (Avatar)**
**Priority: LOW**

**Current:**
```php
$avatarPath = $request->file('avatar')->store('avatars', 'public');
// Lưu vào: storage/app/public/avatars/
```

**Target:**
```php
$media = $this->unifiedUploadService->uploadFile($avatar, $user, 'avatars');
// Lưu vào: public/uploads/{user_id}/avatars/
```

**Steps:**
1. Update avatar upload logic
2. Handle avatar display
3. Update user profile
4. Test avatar functionality

---

### **6. API Controllers**
**Priority: HIGH** (Được sử dụng bởi TinyMCE và AJAX)

#### **ImageUploadController.php & Api/ImageUploadController.php**
**Current:**
```php
$image->move(public_path('images/comments'), $filename);
// Lưu vào: public/images/comments/
```

**Target:**
```php
$media = $this->unifiedUploadService->uploadFile($image, $user, 'comments');
// Lưu vào: public/uploads/{user_id}/comments/
```

#### **Api/TinyMCEController.php**
**Current:**
```php
$image->move($fullPath, $filename);
// Lưu vào: public/images/tinymce/ hoặc public/files/tinymce/
```

**Target:**
```php
$media = $this->unifiedUploadService->uploadFile($image, $user, 'editor');
// Lưu vào: public/uploads/{user_id}/editor/
```

## 🎯 Phase 3: Frontend Updates

### **1. File Display Logic**
- Update các view để handle cả legacy và new file paths
- Tạo helper function để generate correct URLs
- Test display trên tất cả các trang

### **2. Advanced File Upload Components**
- ✅ **Đã hoàn thành** - Components đã sẵn sàng
- Cần test với UnifiedUploadService
- Update response handling nếu cần

### **3. Legacy File Support**
- Tạo middleware hoặc helper để serve legacy files
- Đảm bảo old URLs vẫn hoạt động
- Log access để monitor usage

## 🎯 Phase 4: Testing & Validation

### **Testing Checklist:**
- [ ] Upload file mới vào đúng thư mục `public/uploads/{user_id}/`
- [ ] Legacy files vẫn accessible
- [ ] Database records được tạo đúng
- [ ] File URLs generate đúng
- [ ] Multiple file upload hoạt động
- [ ] File validation hoạt động
- [ ] File deletion hoạt động
- [ ] User permissions được respect

### **Performance Testing:**
- [ ] Upload speed
- [ ] File serving speed
- [ ] Directory structure performance
- [ ] Database query performance

## 📊 Implementation Order

### **Week 1:**
1. ✅ **UnifiedUploadService** (Đã xong)
2. 🔄 **CommentController migration**
3. 🔄 **API Controllers migration**

### **Week 2:**
4. **GalleryController migration**
5. **ThreadController migration**
6. **Frontend updates**

### **Week 3:**
7. **ShowcaseController migration**
8. **UserController migration**
9. **Comprehensive testing**

### **Week 4:**
10. **Performance optimization**
11. **Documentation updates**
12. **Team training**

## ⚠️ Risk Mitigation

### **Rollback Plan:**
1. **Code rollback** - Git revert to previous version
2. **Database rollback** - Restore from backup
3. **File rollback** - Legacy files unchanged, chỉ cần xóa new uploads

### **Monitoring:**
1. **Error logging** cho upload failures
2. **Performance monitoring** cho file access
3. **User feedback** collection
4. **Usage analytics** cho new vs legacy files

## 🚀 Success Metrics

### **Technical:**
- ✅ 100% upload success rate
- ✅ 0% broken legacy links
- ✅ <2s upload time cho files <10MB
- ✅ Proper file organization by user

### **User Experience:**
- ✅ Seamless upload experience
- ✅ Fast file access
- ✅ Intuitive file management
- ✅ No disruption to existing workflows

---

**📝 Next Steps**: Bắt đầu với CommentController migration và test thoroughly trước khi proceed to next controller.
