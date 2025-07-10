# 🔐 MechaMap Unified Permissions System

## 📋 **TỔNG QUAN**

MechaMap đã triển khai thành công **Unified Permissions System** - một hệ thống phân quyền thống nhất dựa trên Multiple Roles, thay thế cho legacy permissions system cũ.

### **🎯 Mục tiêu:**
- ✅ **Thống nhất** hệ thống phân quyền
- ✅ **Scalable** và dễ quản lý
- ✅ **Professional** enterprise-grade system
- ✅ **Backward compatible** với dữ liệu cũ

---

## 🏗️ **KIẾN TRÚC HỆ THỐNG**

### **📊 Database Structure:**

```sql
-- Multiple Roles System
users (id, name, email, role, role_group, role_permissions, ...)
roles (id, name, display_name, role_group, hierarchy_level, ...)
permissions (id, name, display_name, category, ...)
user_has_roles (user_id, role_id, is_primary, is_active, ...)
role_has_permissions (role_id, permission_id, is_granted, ...)
```

### **🔄 Permission Resolution Flow:**

```php
hasPermission($permission) {
    1. Super Admin Check → TRUE
    2. System Admin Check → TRUE  
    3. Multiple Roles Check → hasPermissionViaRoles()
    4. Cached Permissions Check → role_permissions array
    5. Basic Admin Fallback → basic permissions
    6. Return FALSE
}
```

---

## 🚀 **MIGRATION PROCESS**

### **✅ Completed Phases:**

#### **Phase 1: Fix hasPermission() Logic**
- ✅ Updated `User::hasPermission()` method
- ✅ Added `hasPermissionViaRoles()` method
- ✅ Integrated multiple roles checking

#### **Phase 2: Data Migration**
- ✅ Created `MigratePermissionsToRoles` command
- ✅ Migrated 62 users successfully
- ✅ Mapped legacy roles to new roles:
  - `super_admin` → `super_admin`
  - `system_admin` → `system_admin`
  - `admin` → `content_admin`
  - `moderator` → `content_moderator`

#### **Phase 3: Update Permission Checking**
- ✅ Updated `AdminPermissionHelper`
- ✅ Updated middleware systems
- ✅ All Blade directives now use unified system

#### **Phase 4: Legacy System Deprecation**
- ✅ Added deprecation warnings
- ✅ Updated UI to prioritize Multiple Roles
- ✅ Created cleanup commands

#### **Phase 5: Testing & Documentation**
- ✅ Tested unified system functionality
- ✅ Verified UI changes
- ✅ Created comprehensive documentation

---

## 🎯 **USAGE GUIDE**

### **👨‍💼 For Administrators:**

#### **1. Managing User Permissions:**
```php
// Preferred: Use Multiple Roles
Route: /admin/users/{user}/roles

// Deprecated: Legacy Permissions  
Route: /admin/users/admins/{user}/permissions
```

#### **2. Checking Permissions in Code:**
```php
// Unified method (recommended)
if ($user->hasPermission('manage-content')) {
    // User has permission
}

// Blade directives
@adminCan('manage-content')
    <!-- Content for users with permission -->
@endadminCan
```

#### **3. Role Management:**
```php
// Assign role to user
$user->roles()->attach($roleId, [
    'is_primary' => true,
    'assigned_by' => auth()->id(),
    'assignment_reason' => 'Role assignment reason',
    'is_active' => true,
]);

// Refresh user permissions
$user->refreshPermissions();
```

### **🔧 For Developers:**

#### **1. Permission Checking:**
```php
// In Controllers
if (!$user->hasPermission('required-permission')) {
    abort(403, 'Unauthorized');
}

// In Middleware
Route::middleware(['admin.permission:manage-users'])
```

#### **2. Custom Permission Logic:**
```php
// Check multiple permissions
if ($user->hasAnyPermission(['perm1', 'perm2'])) {
    // User has at least one permission
}

// Check all permissions
if ($user->hasAllPermissions(['perm1', 'perm2'])) {
    // User has all permissions
}
```

---

## 📊 **ROLE HIERARCHY**

### **🏢 System Management (Level 1-3):**
- **super_admin** (Level 1) - Toàn quyền hệ thống
- **system_admin** (Level 2) - Quản lý hệ thống
- **content_admin** (Level 3) - Quản lý nội dung

### **👥 Community Management (Level 4-6):**
- **content_moderator** (Level 4) - Kiểm duyệt nội dung
- **marketplace_moderator** (Level 5) - Kiểm duyệt marketplace
- **community_moderator** (Level 6) - Kiểm duyệt cộng đồng

### **👤 Community Members (Level 7-10):**
- **senior_member** (Level 7) - Thành viên cao cấp
- **member** (Level 8) - Thành viên
- **student** (Level 9) - Sinh viên
- **guest** (Level 10) - Khách

### **🏭 Business Partners (Level 11-14):**
- **verified_partner** (Level 11) - Đối tác xác thực
- **manufacturer** (Level 12) - Nhà sản xuất
- **supplier** (Level 13) - Nhà cung cấp
- **brand** (Level 14) - Thương hiệu

---

## 🛠️ **MAINTENANCE COMMANDS**

### **📦 Available Commands:**

```bash
# Migrate legacy permissions to roles
php artisan permissions:migrate-to-roles [--dry-run] [--force]

# Cleanup legacy permissions data
php artisan permissions:cleanup-legacy [--dry-run] [--force]

# Refresh user permissions cache
php artisan cache:clear
```

### **🔄 Regular Maintenance:**

1. **Weekly:** Review role assignments
2. **Monthly:** Cleanup unused permissions
3. **Quarterly:** Audit permission usage

---

## ⚠️ **DEPRECATION NOTICE**

### **🚫 Deprecated Features:**

- ❌ **Legacy Permissions Page** (`/admin/users/admins/{user}/permissions`)
- ❌ **Direct permission assignment** to users
- ❌ **Hardcoded permission checks** in old format

### **✅ Migration Path:**

1. **Use Multiple Roles** instead of direct permissions
2. **Update custom code** to use unified `hasPermission()` method
3. **Remove legacy permission references** in custom modules

---

## 🎉 **BENEFITS ACHIEVED**

### **✅ For Users:**
- 🎯 **Clearer role-based permissions**
- 🔄 **Consistent permission behavior**
- 📱 **Better UI/UX experience**

### **✅ For Developers:**
- 🏗️ **Unified permission checking**
- 📈 **Scalable architecture**
- 🔧 **Easier maintenance**

### **✅ For System:**
- ⚡ **Better performance**
- 🔒 **Enhanced security**
- 📊 **Comprehensive audit trail**

---

## 📞 **SUPPORT**

Nếu gặp vấn đề với unified permissions system:

1. **Check logs:** `storage/logs/laravel.log`
2. **Run diagnostics:** `php artisan permissions:migrate-to-roles --dry-run`
3. **Contact:** MechaMap Development Team

**Last Updated:** 02/07/2025  
**Version:** 1.0.0  
**Status:** ✅ Production Ready
