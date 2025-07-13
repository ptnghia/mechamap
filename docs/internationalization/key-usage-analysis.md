# 🔍 Language Key Usage Analysis Report

**Analysis Date**: 2025-07-12 08:45:00
**Purpose**: Map current translation key usage patterns before restructuring
**Scope**: All view files in resources/views/ (excluding admin)

---

## 📊 **USAGE STATISTICS**

### **Translation Pattern Distribution**
```
__('messages.*'):    527 references ⚠️ OVERUSED
__('marketplace.*'): 105 references ✅ GOOD
__('nav.*'):           8 references ⚠️ UNDERUSED  
__('auth.*'):          6 references ⚠️ UNDERUSED
```

### **File-Level Usage Intensity**
```
HIGH USAGE (20+ references):
├── header.blade.php: 89 references
├── advanced-search.blade.php: 48 references
├── members/index.blade.php: 43 references
├── categories/index.blade.php: 31 references
├── whats-new/showcases.blade.php: 24 references
├── whats-new/popular.blade.php: 24 references
├── whats-new/media.blade.php: 23 references
├── products/show.blade.php: 23 references
└── members/staff.blade.php: 22 references

MEDIUM USAGE (10-19 references):
├── products/index.blade.php: 21 references
├── categories/show.blade.php: 20 references
├── whats-new/threads.blade.php: 19 references
├── whats-new/replies.blade.php: 19 references
├── threads/index.blade.php: 19 references
├── auth-modal.blade.php: 19 references
├── whats-new/index.blade.php: 18 references
└── marketplace/index.blade.php: 16 references

LOW USAGE (< 10 references):
├── members/online.blade.php: 15 references
├── whats-new/trending.blade.php: 8 references
└── whats-new/most-viewed.blade.php: 8 references
```

## 🔍 **KEY PATTERN ANALYSIS**

### **Common Key Categories Found**
Based on sample analysis of view files:

#### **1. Navigation Keys**
```php
__('messages.navigation.home')
__('messages.navigation.forums')
__('messages.navigation.marketplace')
// Should be: __('nav.home'), __('nav.forums'), __('nav.marketplace')
```

#### **2. Site Meta Keys**
```php
__('messages.site.community_title')
__('messages.site.description')
// Should be: __('common.site.title'), __('common.site.description')
```

#### **3. Thread/Forum Keys**
```php
__('messages.threads.create_new_post')
__('messages.threads.posts')
__('messages.forums.forums')
__('messages.forums.forums_in_category')
// Should be: __('forum.threads.create'), __('forum.posts'), __('forum.forums')
```

#### **4. Common UI Keys**
```php
__('messages.common.views')
__('messages.common.comments')
__('messages.common.updated')
// Should be: __('ui.common.views'), __('ui.common.comments'), __('ui.common.updated')
```

#### **5. Marketplace Keys**
```php
// Already properly structured:
__('marketplace.products.show')
__('marketplace.categories.index')
// These are GOOD examples
```

## 🚨 **CRITICAL FINDINGS**

### **1. Overuse of messages.* Prefix**
- **527 references** to `__('messages.*')` 
- **Should be distributed** across focused files
- **Creates dependency** on oversized messages.php file

### **2. Underutilized Focused Files**
- **nav.php**: Only 8 references (should have 50+)
- **auth.php**: Only 6 references (should have 30+)
- **Indicates**: Keys are incorrectly placed in messages.php

### **3. Inconsistent Naming Patterns**
```php
// INCONSISTENT:
__('messages.navigation.home')  // Navigation in messages
__('nav.home')                  // Navigation in nav
__('messages.forums.forums')    // Forums in messages
__('forum.threads.create')      // Forums in forum

// SHOULD BE CONSISTENT:
__('nav.home')
__('nav.forums')
__('forum.threads.create')
__('forum.categories.show')
```

## 📋 **ORPHANED KEYS ANALYSIS**

### **Methodology**
1. Extract all keys from messages.php
2. Search for usage in view files
3. Identify unused keys

### **Estimated Orphaned Keys**
Based on file size vs. usage patterns:
- **messages.php**: 623 lines (~400 keys estimated)
- **Active usage**: 527 references
- **Potential orphans**: 50-100 keys (need detailed analysis)

## 🎯 **MIGRATION STRATEGY IMPLICATIONS**

### **Priority Files for Migration**
Based on usage intensity:

#### **CRITICAL (Must migrate first)**
```
1. header.blade.php (89 refs) - Core layout
2. advanced-search.blade.php (48 refs) - Search functionality
3. members/index.blade.php (43 refs) - User management
4. categories/index.blade.php (31 refs) - Forum structure
```

#### **HIGH PRIORITY**
```
5-12. Files with 20-30 references each
- Marketplace views
- What's new sections
- Member management
```

#### **MEDIUM PRIORITY**
```
13-20. Files with 10-19 references each
- Thread management
- Auth components
- General marketplace
```

### **Key Distribution Plan**
Based on usage patterns:

#### **nav.php** (Target: 50+ keys)
```php
// Migrate from messages.navigation.*
'home' => 'Trang chủ',
'forums' => 'Diễn đàn',
'marketplace' => 'Thị trường',
'community' => 'Cộng đồng',
// ... ~46 more navigation keys
```

#### **ui.php** (Target: 100+ keys)
```php
// Migrate from messages.common.*
'actions' => [
    'view' => 'Xem',
    'edit' => 'Sửa',
    'delete' => 'Xóa',
    'save' => 'Lưu',
],
'status' => [
    'loading' => 'Đang tải...',
    'success' => 'Thành công',
    'error' => 'Lỗi',
],
// ... ~90 more UI keys
```

#### **forum.php** (Target: 80+ keys)
```php
// Migrate from messages.threads.*, messages.forums.*
'threads' => [
    'create_new_post' => 'Tạo bài viết mới',
    'posts' => 'Bài viết',
],
'forums' => [
    'forums' => 'Diễn đàn',
    'forums_in_category' => 'Diễn đàn trong danh mục :category',
],
// ... ~70 more forum keys
```

#### **common.php** (Target: 60+ keys)
```php
// Migrate from messages.site.*, messages.common.*
'site' => [
    'community_title' => 'Tiêu đề cộng đồng',
    'description' => 'Mô tả trang web',
],
'time' => [
    'updated' => 'Cập nhật',
    'created' => 'Tạo',
],
// ... ~50 more common keys
```

## ✅ **RECOMMENDATIONS**

### **1. Immediate Actions**
- **Stop adding keys** to messages.php
- **Create migration scripts** for systematic redistribution
- **Update high-usage files first** (header.blade.php, etc.)

### **2. Migration Sequence**
1. **Create new focused files** with proper structure
2. **Migrate keys in batches** (navigation → UI → forum → common)
3. **Update view references** in usage-priority order
4. **Test each batch** before proceeding

### **3. Quality Assurance**
- **Automated testing** for missing keys
- **Performance monitoring** during migration
- **Rollback procedures** at each step

---

**Analysis Status**: ✅ COMPLETE
**Next Step**: Create duplicate keys inventory (Task 0.3)
**Risk Level**: 🟡 MEDIUM - Large scope but manageable with systematic approach
