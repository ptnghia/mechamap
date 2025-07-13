# 🔍 Duplicate Keys Inventory & Resolution Plan

**Analysis Date**: 2025-07-12 08:50:00
**Purpose**: Complete inventory of duplicate translation keys across language files
**Scope**: All Vietnamese language files in resources/lang/vi/

---

## 🚨 **CRITICAL DUPLICATES FOUND**

### **1. 'home' Key - 7 Occurrences**
**Files & Locations:**
```php
buttons.php:33     → 'home' => 'Trang chủ',
forums.php:181     → 'home' => 'Trang chủ',
messages.php:6     → 'home' => 'Trang chủ',
messages.php:297   → 'home' => 'Trang chủ',
messages.php:573   → 'home' => 'Trang chủ',
messages.php:620   → 'home' => 'Trang chủ',
nav.php:5          → 'home' => 'Trang chủ',
```
**Resolution**: Consolidate to `nav.php` only
**Impact**: 7 files need updates

### **2. 'marketplace' Key - 5 Occurrences (INCONSISTENT VALUES!)**
**Files & Locations:**
```php
messages.php:7     → 'marketplace' => 'Sản phẩm',      ❌ WRONG
messages.php:408   → 'marketplace' => [array],         ❌ WRONG TYPE
messages.php:574   → 'marketplace' => 'Thị trường',    ✅ CORRECT
nav.php:4          → 'marketplace' => 'Thị trường',    ✅ CORRECT
notifications.php:127 → 'marketplace' => 'Thị trường', ✅ CORRECT
```
**Resolution**: Consolidate to `nav.php` with value 'Thị trường'
**Impact**: 4 files need updates, 1 value correction

### **3. 'login' Key - 7 Occurrences**
**Files & Locations:**
```php
alerts.php:16      → 'login' => 'Đăng nhập thành công!', ❌ DIFFERENT CONTEXT
auth.php:20        → 'login' => 'Đăng nhập',             ✅ CORRECT
buttons.php:38     → 'login' => 'Đăng nhập',             ✅ CORRECT
companies.php:101  → 'login' => 'Đăng nhập',             ✅ CORRECT
messages.php:276   → 'login' => 'Đăng nhập',             ✅ CORRECT
messages.php:379   → 'login' => 'Đăng nhập',             ✅ CORRECT
nav.php:16         → 'login' => 'Đăng nhập',             ✅ CORRECT
```
**Resolution**: Consolidate to `auth.php`, keep alerts.php for success message
**Impact**: 5 files need updates

### **4. 'search' Key - 13 Occurrences**
**Files & Locations:**
```php
buttons.php:71     → 'search' => 'Tìm kiếm',
forms.php:31       → 'search' => 'Tìm kiếm',
forum.php:32       → 'search' => [array],
forums.php:26      → 'search' => 'Tìm kiếm',
forums.php:41      → 'search' => 'Tìm kiếm',
forums.php:47      → 'search' => [array],
forums.php:185     → 'search' => 'Tìm kiếm',
messages.php:199   → 'search' => 'Tìm kiếm',
messages.php:249   → 'search' => 'Tìm kiếm',
messages.php:283   → 'search' => 'Tìm kiếm',
messages.php:316   → 'search' => [array],
nav.php:23         → 'search' => 'Tìm kiếm',
showcase.php:48    → 'search' => 'Tìm kiếm',
```
**Resolution**: Consolidate simple 'search' to `ui.php`, keep arrays in domain files
**Impact**: 9 files need updates

## 📊 **DUPLICATE STATISTICS**

### **High-Frequency Duplicates (5+ occurrences)**
```
'title'        → 40 occurrences ⚠️ CRITICAL
'message'      → 25 occurrences ⚠️ CRITICAL  
'Diễn đàn'     → 16 occurrences ⚠️ HIGH
'Danh mục'     → 14 occurrences ⚠️ HIGH
'Tìm kiếm'     → 13 occurrences ⚠️ HIGH
'search'       → 13 occurrences ⚠️ HIGH
'description'  → 13 occurrences ⚠️ HIGH
'Đăng ký'      → 12 occurrences ⚠️ HIGH
'Chủ đề'       → 12 occurrences ⚠️ HIGH
'categories'   → 10 occurrences ⚠️ MEDIUM
'Xóa'          → 9 occurrences ⚠️ MEDIUM
'search_placeholder' → 9 occurrences ⚠️ MEDIUM
'threads'      → 8 occurrences ⚠️ MEDIUM
'forums'       → 8 occurrences ⚠️ MEDIUM
'Đăng nhập'    → 8 occurrences ⚠️ MEDIUM
'advanced_search' → 8 occurrences ⚠️ MEDIUM
'Trang chủ'    → 7 occurrences ⚠️ MEDIUM
'Trả lời'      → 7 occurrences ⚠️ MEDIUM
'home'         → 7 occurrences ⚠️ MEDIUM
'login'        → 7 occurrences ⚠️ MEDIUM
```

### **Total Duplicate Impact**
- **Estimated total duplicates**: 60+ keys
- **Files affected**: All 20 language files
- **Critical inconsistencies**: 5+ keys with different values
- **Maintenance overhead**: HIGH

## 🎯 **RESOLUTION STRATEGY**

### **Phase 1: Critical Inconsistencies (Immediate)**
**Priority**: 🔴 CRITICAL - Fix value conflicts first

#### **1.1: Fix 'marketplace' Value Conflict**
```php
// BEFORE (inconsistent):
messages.php:7     → 'marketplace' => 'Sản phẩm',      ❌
messages.php:574   → 'marketplace' => 'Thị trường',    ✅
nav.php:4          → 'marketplace' => 'Thị trường',    ✅

// AFTER (consistent):
nav.php:4          → 'marketplace' => 'Thị trường',    ✅ ONLY
```

#### **1.2: Resolve Context-Specific Duplicates**
```php
// BEFORE:
alerts.php:16      → 'login' => 'Đăng nhập thành công!',
auth.php:20        → 'login' => 'Đăng nhập',

// AFTER:
alerts.php:16      → 'login_success' => 'Đăng nhập thành công!',
auth.php:20        → 'login' => 'Đăng nhập',
```

### **Phase 2: High-Frequency Duplicates**
**Priority**: 🟡 HIGH - Consolidate most duplicated keys

#### **2.1: Navigation Keys → nav.php**
```php
// Consolidate to nav.php:
'home' => 'Trang chủ',
'marketplace' => 'Thị trường',
'forums' => 'Diễn đàn',
'search' => 'Tìm kiếm',
```

#### **2.2: UI Action Keys → ui.php**
```php
// Consolidate to ui.php:
'actions' => [
    'delete' => 'Xóa',
    'edit' => 'Sửa',
    'save' => 'Lưu',
    'cancel' => 'Hủy',
],
```

#### **2.3: Common Terms → common.php**
```php
// Consolidate to common.php:
'terms' => [
    'categories' => 'Danh mục',
    'threads' => 'Chủ đề',
    'description' => 'Mô tả',
    'title' => 'Tiêu đề',
],
```

### **Phase 3: Domain-Specific Duplicates**
**Priority**: 🟢 MEDIUM - Organize by functionality

#### **3.1: Forum Keys → forum.php**
```php
// Keep domain-specific arrays in forum.php:
'search' => [
    'placeholder' => 'Tìm kiếm trong diễn đàn...',
    'advanced' => 'Tìm kiếm nâng cao',
    'results' => 'Kết quả tìm kiếm',
],
```

#### **3.2: Auth Keys → auth.php**
```php
// Keep auth-specific keys in auth.php:
'login' => 'Đăng nhập',
'register' => 'Đăng ký',
'logout' => 'Đăng xuất',
```

## 📋 **CONSOLIDATION PLAN**

### **Target File Distribution**

#### **nav.php** (Navigation)
```php
return [
    'home' => 'Trang chủ',
    'marketplace' => 'Thị trường',
    'forums' => 'Diễn đàn',
    'search' => 'Tìm kiếm',
    'categories' => 'Danh mục',
    // Remove from: buttons.php, forums.php, messages.php (multiple)
];
```

#### **ui.php** (UI Elements)
```php
return [
    'actions' => [
        'delete' => 'Xóa',
        'edit' => 'Sửa',
        'save' => 'Lưu',
        'search' => 'Tìm kiếm',
    ],
    'common' => [
        'title' => 'Tiêu đề',
        'description' => 'Mô tả',
        'message' => 'Tin nhắn',
    ],
    // Remove from: buttons.php, forms.php, messages.php (multiple)
];
```

#### **auth.php** (Authentication)
```php
return [
    'login' => 'Đăng nhập',
    'register' => 'Đăng ký',
    'logout' => 'Đăng xuất',
    // Remove from: buttons.php, companies.php, messages.php (multiple), nav.php
];
```

#### **forum.php** (Forums - Keep domain arrays)
```php
return [
    'search' => [
        'placeholder' => 'Tìm kiếm trong diễn đàn...',
        'advanced' => 'Tìm kiếm nâng cao',
    ],
    'threads' => [
        'create' => 'Tạo chủ đề',
        'reply' => 'Trả lời',
    ],
    // Keep domain-specific arrays, remove simple duplicates
];
```

## ⚠️ **MIGRATION RISKS & MITIGATION**

### **High Risk Areas**
1. **Value Conflicts**: 'marketplace' has different meanings
2. **Context Sensitivity**: 'login' vs 'login_success'
3. **Array vs String**: Some keys are arrays, others are strings
4. **View Dependencies**: 527+ view references need updates

### **Mitigation Strategies**
1. **Backup before changes** ✅ Already done
2. **Automated testing** for missing keys
3. **Batch migration** with validation
4. **Rollback procedures** at each step

## ✅ **SUCCESS CRITERIA**

### **Completion Metrics**
- ✅ Zero duplicate keys across all files
- ✅ Consistent values for same concepts
- ✅ Proper domain organization
- ✅ All view references updated
- ✅ No missing translations

### **Quality Gates**
- ✅ Automated duplicate detection passes
- ✅ Language switching works correctly
- ✅ No broken view references
- ✅ Performance maintained or improved

---

**Inventory Status**: ✅ COMPLETE
**Next Step**: Design new file structure schema (Task 0.4)
**Estimated Cleanup Time**: 8-12 hours
**Risk Level**: 🟡 MEDIUM - Manageable with systematic approach
