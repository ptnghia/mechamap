# 🌐 Threads.js Multilingual Integration Guide

## 📋 **OVERVIEW**

Đã chuyển đổi file `public/js/frontend/page/threads.js` từ text cố định tiếng Việt sang hệ thống đa ngôn ngữ sử dụng `translation-service.js`.

## 🔧 **CHANGES MADE**

### **1. Updated JavaScript Functions**
- ✅ **Like functionality**: Thay thế "Thích", "Bỏ thích" bằng `trans('ui.actions.like')`, `trans('ui.actions.unlike')`
- ✅ **Save functionality**: Thay thế "Đánh dấu", "Đã đánh dấu" bằng `trans('ui.actions.save')`, `trans('ui.actions.saved')`
- ✅ **Follow functionality**: Thay thế "Theo dõi", "Đang theo dõi" bằng `trans('ui.actions.follow')`, `trans('ui.actions.following')`
- ✅ **Processing states**: Thay thế "Đang xử lý" bằng `trans('ui.status.processing')`
- ✅ **Error messages**: Thay thế các thông báo lỗi bằng translation keys
- ✅ **Delete confirmations**: Sử dụng translation keys cho các xác nhận xóa

### **2. Translation Keys Added**
```php
// UI Actions
'ui.actions.like' => 'Thích' / 'Like'
'ui.actions.unlike' => 'Bỏ thích' / 'Unlike'
'ui.actions.save' => 'Đánh dấu' / 'Save'
'ui.actions.saved' => 'Đã đánh dấu' / 'Saved'
'ui.actions.unsave' => 'Bỏ đánh dấu' / 'Remove bookmark'
'ui.actions.follow' => 'Theo dõi' / 'Follow'
'ui.actions.following' => 'Đang theo dõi' / 'Following'
'ui.actions.unfollow' => 'Bỏ theo dõi' / 'Unfollow'

// UI Status
'ui.status.processing' => 'Đang xử lý' / 'Processing'
'ui.status.loading_comments' => 'Đang tải bình luận...' / 'Loading comments...'

// UI Messages
'ui.messages.error_occurred' => 'Có lỗi xảy ra' / 'An error occurred'
'ui.messages.request_error' => 'Có lỗi xảy ra khi gửi yêu cầu. Vui lòng thử lại.' / 'An error occurred while sending the request. Please try again.'
'ui.messages.comments_sorted' => 'Bình luận đã được sắp xếp' / 'Comments have been sorted'
'ui.messages.delete_image_error' => 'Có lỗi xảy ra khi xóa hình ảnh.' / 'An error occurred while deleting the image.'

// UI Confirmations
'ui.confirmations.delete_image' => 'hình ảnh này' / 'this image'

// Features - Threads
'features.threads.delete_comment_message' => 'Bạn có chắc chắn muốn xóa bình luận này?' / 'Are you sure you want to delete this comment?'
'features.threads.delete_reply_message' => 'Bạn có chắc chắn muốn xóa phản hồi này?' / 'Are you sure you want to delete this reply?'
```

### **3. New Files Created**
- ✅ `public/js/frontend/page/threads-init.js` - Initialization script with translation support
- ✅ `scripts/add_threads_js_translation_keys.php` - Script to add translation keys
- ✅ `scripts/check_threads_translation_keys.php` - Script to verify keys exist

## 🚀 **INTEGRATION STEPS**

### **Step 1: Include Required Scripts**
Trong view sử dụng threads functionality, đảm bảo load scripts theo thứ tự:

```html
<!-- Translation Service (required first) -->
<script src="{{ asset('js/translation-service.js') }}"></script>

<!-- Threads functionality -->
<script src="{{ asset('js/frontend/page/threads.js') }}"></script>

<!-- Threads initialization (required last) -->
<script src="{{ asset('js/frontend/page/threads-init.js') }}"></script>
```

### **Step 2: Verify Translation Keys**
Chạy script để kiểm tra translation keys:
```bash
php scripts/check_threads_translation_keys.php
```

### **Step 3: Test Functionality**
1. **Load page**: Kiểm tra translations load đúng
2. **Switch language**: Test chuyển đổi ngôn ngữ
3. **Thread actions**: Test like, save, follow functionality
4. **Comment actions**: Test comment like, delete functionality

## 🔄 **LANGUAGE SWITCHING SUPPORT**

### **Automatic Updates**
Khi người dùng chuyển đổi ngôn ngữ:
1. Event `languageChanged` được trigger
2. Translation service reload translations
3. UI elements tự động cập nhật với ngôn ngữ mới

### **Manual Refresh**
Có thể gọi function để cập nhật UI:
```javascript
// Refresh all thread UI elements with current language
window.refreshThreadTranslations();
```

## 🧪 **TESTING CHECKLIST**

### **Basic Functionality**
- [ ] Page loads without JavaScript errors
- [ ] Translation service initializes correctly
- [ ] Thread actions work (like, save, follow)
- [ ] Comment actions work (like, delete)
- [ ] Error messages display in correct language

### **Language Switching**
- [ ] UI updates when language is changed
- [ ] Button text changes correctly
- [ ] Tooltips update to new language
- [ ] Error messages show in new language
- [ ] Processing messages show in new language

### **Fallback Behavior**
- [ ] Works if translation service fails to load
- [ ] Shows translation keys as fallback text
- [ ] No JavaScript errors in console

## 🐛 **TROUBLESHOOTING**

### **Common Issues**

**1. Translations not loading**
- Check if `translation-service.js` is loaded before `threads.js`
- Verify API endpoints `/api/translations/js` are working
- Check browser console for errors

**2. UI not updating on language change**
- Ensure `threads-init.js` is loaded
- Check if `languageChanged` event is being triggered
- Verify `updateThreadsUILanguage()` function exists

**3. Fallback text showing**
- Check if translation keys exist in database
- Verify translation service is loading correct groups ('ui', 'features')
- Check network requests in browser dev tools

### **Debug Commands**
```javascript
// Check if translation service is available
console.log(window.translationService);

// Check loaded translations
console.log(window.translationService.getAllTranslations());

// Test specific translation
console.log(trans('ui.actions.like'));

// Check current locale
console.log(window.translationService.getLocale());
```

## ✅ **COMPLETION STATUS**

- ✅ **JavaScript conversion**: All hardcoded text replaced with translation keys
- ✅ **Translation keys**: Added to database via script
- ✅ **Initialization script**: Created for proper loading sequence
- ✅ **Language switching**: Implemented automatic UI updates
- ✅ **Documentation**: Complete integration guide
- ⏳ **Testing**: Requires manual testing on live site

## 🎯 **NEXT STEPS**

1. **Deploy changes** to test environment
2. **Test all functionality** with both languages
3. **Verify performance** impact of translation loading
4. **Apply same pattern** to other JavaScript files if needed
5. **Update other pages** that use similar functionality

---

**Created**: 2025-01-03  
**Status**: Ready for Testing  
**Files Modified**: 3 files  
**Translation Keys Added**: 17 keys
