# 🎯 MechaMap Hybrid Permissions System

## 📋 **TỔNG QUAN**

MechaMap sử dụng **Hybrid Permissions System** - kết hợp tốt nhất của cả hai thế giới: **Multiple Roles** cho base permissions và **Custom Permissions** cho fine-tuning.

### **🎯 Công thức:**
```
Final Permissions = Multiple Roles Permissions + Custom Permissions
```

---

## 🏗️ **KIẾN TRÚC HYBRID SYSTEM**

### **📊 Two-Layer Permission Model:**

#### **🎯 Layer 1: Multiple Roles (Base Permissions)**
- ✅ **Purpose**: Cung cấp base permissions theo vai trò
- ✅ **Scope**: Bulk permission management
- ✅ **Use Case**: Standard role assignments
- ✅ **Management**: `/admin/users/{user}/roles`

#### **🎯 Layer 2: Custom Permissions (Additional Permissions)**
- ✅ **Purpose**: Fine-tuning và special cases
- ✅ **Scope**: Granular permission control
- ✅ **Use Case**: Edge cases, testing, special requirements
- ✅ **Management**: `/admin/users/admins/{user}/permissions`

---

## 🚀 **WORKFLOW HƯỚNG DẪN**

### **📅 Step 1: Assign Base Roles**
```php
// Gán roles cơ bản cho user
1. Vào Multiple Roles Management
2. Chọn primary role (required)
3. Chọn additional roles (optional)
4. Save → User có base permissions
```

### **📅 Step 2: Add Custom Permissions (Optional)**
```php
// Thêm permissions tùy chỉnh nếu cần
1. Vào Custom Permissions Management
2. Chọn additional permissions không có trong roles
3. Nhập lý do thay đổi
4. Save → User có combined permissions
```

### **📅 Step 3: Verify Final Permissions**
```php
// Kiểm tra permissions cuối cùng
$user->hasPermission('specific-permission'); // Returns true/false
$breakdown = $user->getPermissionsBreakdown();
// Returns: ['role_permissions', 'custom_permissions', 'total_permissions']
```

---

## 🎯 **USE CASES & EXAMPLES**

### **✅ A. STANDARD SCENARIOS (Chỉ cần Multiple Roles):**

#### **1. Content Moderator:**
```php
Roles: ['content_moderator']
Permissions: moderate-content, approve-content, manage-categories, etc.
Custom: Không cần
```

#### **2. Marketplace Admin:**
```php
Roles: ['content_admin', 'marketplace_moderator']
Permissions: All content + marketplace permissions
Custom: Không cần
```

### **✅ B. SPECIAL SCENARIOS (Cần Custom Permissions):**

#### **1. Limited Admin:**
```php
Roles: ['content_moderator'] // Base permissions
Custom: ['view-analytics', 'export-data'] // Additional specific permissions
Final: Content moderation + Analytics access
```

#### **2. Testing Account:**
```php
Roles: ['member'] // Minimal base
Custom: ['manage-test-features', 'access-beta-functions']
Final: Member permissions + Testing capabilities
```

#### **3. Project-Specific Admin:**
```php
Roles: ['content_admin'] // Standard admin
Custom: ['manage-special-project', 'access-external-api']
Final: Admin permissions + Project-specific access
```

---

## 🔧 **TECHNICAL IMPLEMENTATION**

### **📊 Permission Resolution Logic:**
```php
public function hasPermission(string $permission): bool
{
    // 1. Super Admin check
    if ($this->role === 'super_admin') return true;
    
    // 2. System Admin check  
    if ($this->role === 'system_admin') return true;
    
    // 3. Multiple Roles check
    if ($this->hasPermissionViaRoles($permission)) return true;
    
    // 4. Custom Permissions check
    if ($this->hasCustomPermission($permission)) return true;
    
    // 5. Basic Admin fallback
    if ($this->role === 'admin' && in_array($permission, $basicPermissions)) return true;
    
    return false;
}
```

### **📊 Permission Breakdown:**
```php
$breakdown = $user->getPermissionsBreakdown();
// Returns:
[
    'role_permissions' => ['perm1', 'perm2', 'perm3'], // From roles
    'custom_permissions' => ['perm4', 'perm5'],       // Additional custom
    'total_permissions' => ['perm1', 'perm2', 'perm3', 'perm4', 'perm5']
]
```

---

## 🎨 **UI/UX DESIGN**

### **📱 Admin Edit Interface:**
```html
<!-- Two clear buttons with descriptions -->
<a href="/admin/users/{user}/roles" class="btn btn-primary">
    Multiple Roles
    <small>Base permissions</small>
</a>

<a href="/admin/users/admins/{user}/permissions" class="btn btn-success">
    Custom Permissions  
    <small>Additional permissions</small>
</a>

<!-- Clear explanation -->
<small>Hybrid System: Sử dụng Multiple Roles cho base permissions, 
Custom Permissions cho fine-tuning</small>
```

### **📱 Custom Permissions Page:**
```html
<!-- Clear purpose statement -->
🎯 CUSTOM PERMISSIONS: Trang này cho phép bạn thêm permissions tùy chỉnh 
bổ sung cho Multiple Roles hiện có.
Permissions cuối cùng = Roles Permissions + Custom Permissions

<!-- Current roles display -->
Current Roles: [Content Admin] [Marketplace Moderator]
0 permissions từ roles

<!-- Permission selection with all categories -->
[All permission categories available for selection]
```

---

## 📊 **BENEFITS ACHIEVED**

### **✅ For Administrators:**
- 🎯 **Flexibility**: Best of both worlds approach
- 🔧 **Granular Control**: Fine-tune permissions when needed
- 📊 **Clear Workflow**: Roles first, custom second
- 🎨 **User-Friendly**: Clear UI guidance

### **✅ For System:**
- ⚡ **Performance**: Efficient permission checking
- 🔒 **Security**: Layered permission model
- 📈 **Scalability**: Handle both standard and edge cases
- 🔧 **Maintainability**: Clean separation of concerns

### **✅ For Use Cases:**
- 👥 **Standard Users**: Simple role-based permissions
- 🎯 **Special Cases**: Custom permissions for unique requirements
- 🧪 **Testing**: Flexible permission combinations
- 📊 **Analytics**: Clear breakdown of permission sources

---

## 🎯 **BEST PRACTICES**

### **📋 Recommended Workflow:**
1. ✅ **Start with Roles**: Always assign appropriate roles first
2. ✅ **Add Custom Sparingly**: Only when roles don't cover requirements
3. ✅ **Document Reasons**: Always provide clear reasons for custom permissions
4. ✅ **Regular Review**: Periodically review and cleanup custom permissions

### **📋 When to Use Custom Permissions:**
- ✅ **Edge Cases**: Requirements not covered by standard roles
- ✅ **Temporary Access**: Short-term special permissions
- ✅ **Testing**: Beta features or experimental access
- ✅ **Project-Specific**: Unique project requirements

### **📋 When NOT to Use Custom Permissions:**
- ❌ **Standard Cases**: Use roles instead
- ❌ **Permanent Needs**: Create new roles instead
- ❌ **Multiple Users**: Create role templates instead
- ❌ **Security-Critical**: Use established roles instead

---

## 🎉 **CONCLUSION**

MechaMap Hybrid Permissions System cung cấp:

- 🎯 **Flexibility**: Handle both standard và special cases
- 🔧 **Granular Control**: Fine-tune permissions when needed
- 📊 **Clear Workflow**: Intuitive two-step process
- ⚡ **Performance**: Efficient permission resolution
- 🔒 **Security**: Layered permission model

**Perfect balance between simplicity and flexibility! 🚀**

---

**Last Updated:** 02/07/2025  
**Version:** 1.0.0  
**Status:** ✅ Production Ready
