# 🧪 MechaMap Permissions Testing Scenarios

## 📋 **TỔNG QUAN TESTING**

Kịch bản test toàn diện cho **Hybrid Permissions System** của MechaMap, đảm bảo:
- ✅ **Permission Logic**: Hoạt động chính xác
- ✅ **Sidebar Display**: Hiển thị đúng menu items
- ✅ **Access Control**: Chặn/cho phép truy cập đúng
- ✅ **UI/UX**: Interface phản ánh permissions

---

## 🎯 **TEST SCENARIOS**

### **📊 SCENARIO 1: SUPER ADMIN (Baseline Test)**

#### **🎯 Setup:**
```php
User: Super Admin
Role: super_admin (system role)
Expected: Full access to everything
```

#### **🧪 Test Steps:**
1. **Login as Super Admin**
2. **Check Sidebar**: Tất cả menu items hiển thị
3. **Test Access**: Truy cập tất cả pages thành công
4. **Permission Check**: `hasPermission()` return true cho mọi permission

#### **✅ Expected Results:**
- ✅ Sidebar: All menu items visible
- ✅ Access: All pages accessible
- ✅ Permissions: All return true

---

### **📊 SCENARIO 2: CONTENT ADMIN (Multiple Roles Only)**

#### **🎯 Setup:**
```php
User: Content Admin Test
Multiple Roles: ['content_admin']
Custom Permissions: None
Expected: Content management access only
```

#### **🧪 Test Steps:**
1. **Assign Role**: Gán role 'content_admin' via Multiple Roles
2. **Check Sidebar**: Verify content-related menus
3. **Test Access**: 
   - ✅ Content management pages
   - ❌ System/marketplace pages
4. **Permission Check**: Specific content permissions

#### **✅ Expected Results:**
- ✅ Sidebar: Quản Lý Nội Dung, Diễn Đàn, Trang & Tri Thức
- ❌ Sidebar: Thị Trường, Hệ Thống, Analytics
- ✅ Access: `/admin/content/*`, `/admin/forums/*`
- ❌ Access: `/admin/marketplace/*`, `/admin/system/*`

---

### **📊 SCENARIO 3: MARKETPLACE MODERATOR (Multiple Roles Only)**

#### **🎯 Setup:**
```php
User: Marketplace Moderator Test
Multiple Roles: ['marketplace_moderator']
Custom Permissions: None
Expected: Marketplace access only
```

#### **🧪 Test Steps:**
1. **Assign Role**: Gán role 'marketplace_moderator'
2. **Check Sidebar**: Verify marketplace menus
3. **Test Access**: Marketplace pages only
4. **Permission Check**: Marketplace permissions

#### **✅ Expected Results:**
- ✅ Sidebar: Thị Trường Cơ Khí, Quản Lý Đơn Hàng
- ❌ Sidebar: Quản Lý Nội Dung, Hệ Thống
- ✅ Access: `/admin/marketplace/*`, `/admin/orders/*`
- ❌ Access: `/admin/content/*`, `/admin/system/*`

---

### **📊 SCENARIO 4: HYBRID USER (Roles + Custom Permissions)**

#### **🎯 Setup:**
```php
User: Hybrid Test User
Multiple Roles: ['content_moderator'] // Base permissions
Custom Permissions: ['view-analytics', 'manage-marketplace'] // Additional
Expected: Content moderation + Analytics + Marketplace
```

#### **🧪 Test Steps:**
1. **Assign Base Role**: 'content_moderator' via Multiple Roles
2. **Add Custom Permissions**: Analytics + Marketplace via Custom Permissions
3. **Check Sidebar**: Combined menu items
4. **Test Access**: All assigned areas
5. **Permission Breakdown**: Verify hybrid logic

#### **✅ Expected Results:**
- ✅ Sidebar: Nội Dung (from role) + Thống Kê + Thị Trường (from custom)
- ✅ Access: Content moderation + Analytics + Marketplace pages
- ✅ Permission Logic: `hasPermission('view-analytics')` = true
- ✅ Breakdown: Role permissions + Custom permissions

---

### **📊 SCENARIO 5: LIMITED ADMIN (Special Case)**

#### **🎯 Setup:**
```php
User: Limited Admin Test
Multiple Roles: ['member'] // Minimal base
Custom Permissions: ['view_dashboard', 'view_users', 'manage_reports'] // Specific admin tasks
Expected: Dashboard + Users + Reports only
```

#### **🧪 Test Steps:**
1. **Assign Minimal Role**: 'member'
2. **Add Specific Permissions**: Dashboard, Users, Reports
3. **Check Sidebar**: Only assigned items
4. **Test Access**: Limited admin access
5. **Verify Restrictions**: No system/marketplace access

#### **✅ Expected Results:**
- ✅ Sidebar: Bảng Điều Khiển, Người Dùng, Báo Cáo
- ❌ Sidebar: Hệ Thống, Thị Trường, Nội Dung
- ✅ Access: Dashboard, user management, reports
- ❌ Access: System settings, marketplace, content management

---

### **📊 SCENARIO 6: TESTING ACCOUNT (Beta Features)**

#### **🎯 Setup:**
```php
User: Beta Tester
Multiple Roles: ['senior_member'] // Community role
Custom Permissions: ['access-beta-features', 'manage-test-data'] // Testing permissions
Expected: Community access + Beta features
```

#### **🧪 Test Steps:**
1. **Assign Community Role**: 'senior_member'
2. **Add Beta Permissions**: Testing-specific permissions
3. **Check Sidebar**: Community + Beta items
4. **Test Beta Access**: Special testing features
5. **Verify Isolation**: No production admin access

#### **✅ Expected Results:**
- ✅ Sidebar: Community features + Beta testing menu
- ❌ Sidebar: Production admin features
- ✅ Access: Community pages + Beta testing tools
- ❌ Access: Production admin areas

---

## 🔧 **TECHNICAL TESTING**

### **📊 PERMISSION LOGIC TESTS:**

#### **🧪 Test 1: Permission Resolution Order**
```php
// Test permission checking hierarchy
1. Super Admin → Always true
2. System Admin → Always true  
3. Multiple Roles → Check role permissions
4. Custom Permissions → Check cached permissions
5. Basic Admin → Fallback permissions
6. Default → False
```

#### **🧪 Test 2: Permission Breakdown**
```php
$breakdown = $user->getPermissionsBreakdown();
// Verify:
- role_permissions: From assigned roles
- custom_permissions: Additional permissions
- total_permissions: Combined unique list
```

#### **🧪 Test 3: Cache Consistency**
```php
// Test permission caching
1. Assign roles → Check cached permissions
2. Add custom permissions → Verify cache update
3. Remove roles → Ensure custom permissions preserved
4. Refresh permissions → Verify consistency
```

---

## 🎨 **UI/UX TESTING**

### **📊 SIDEBAR VISIBILITY TESTS:**

#### **🧪 Test 1: Menu Item Visibility**
```javascript
// Check sidebar menu items based on permissions
@adminCan('manage-content') → Show Content menu
@adminCan('manage-marketplace') → Show Marketplace menu
@adminCan('view-analytics') → Show Analytics menu
@adminCan('manage-system') → Show System menu
```

#### **🧪 Test 2: Submenu Access**
```javascript
// Verify submenu items
Content Menu:
- @adminCan('manage-categories') → Categories submenu
- @adminCan('manage-forums') → Forums submenu
- @adminCan('moderate-content') → Moderation submenu
```

#### **🧪 Test 3: Button/Link Visibility**
```javascript
// Check action buttons
@adminCan('create-content') → Show Create button
@adminCan('edit-content') → Show Edit button
@adminCan('delete-content') → Show Delete button
```

---

## 📋 **TESTING CHECKLIST**

### **✅ Pre-Test Setup:**
- [ ] Database backup created
- [ ] Test users created for each scenario
- [ ] Roles and permissions seeded
- [ ] Browser dev tools ready

### **✅ For Each Scenario:**
- [ ] User login successful
- [ ] Sidebar displays correctly
- [ ] Menu items match permissions
- [ ] Page access works as expected
- [ ] Restricted areas blocked
- [ ] Permission checks return correct values
- [ ] UI elements show/hide properly

### **✅ Post-Test Verification:**
- [ ] No errors in logs
- [ ] Performance acceptable
- [ ] Security restrictions working
- [ ] User experience smooth

---

## 🚨 **COMMON ISSUES TO CHECK**

### **❌ Potential Problems:**
1. **Sidebar not updating** after permission changes
2. **Menu items visible** but pages inaccessible
3. **Permission caching** not refreshing
4. **Custom permissions** not combining with roles
5. **UI elements** not reflecting permissions

### **🔧 Debugging Steps:**
1. **Check logs**: `storage/logs/laravel.log`
2. **Clear cache**: `php artisan cache:clear`
3. **Refresh permissions**: `$user->refreshPermissions()`
4. **Verify database**: Check role_has_permissions table
5. **Test permission methods**: `$user->hasPermission('test')`

---

## 📊 **SUCCESS CRITERIA**

### **✅ System Passes If:**
- ✅ All scenarios work as expected
- ✅ Sidebar reflects permissions accurately
- ✅ Access control functions properly
- ✅ No unauthorized access possible
- ✅ UI/UX is intuitive and consistent
- ✅ Performance is acceptable
- ✅ No errors or warnings in logs

### **❌ System Fails If:**
- ❌ Unauthorized access possible
- ❌ Sidebar shows incorrect items
- ❌ Permission logic inconsistent
- ❌ UI elements don't match permissions
- ❌ Performance significantly degraded
- ❌ Errors in logs during testing

---

**Testing Duration:** ~2-3 hours for complete scenarios  
**Required:** Admin access, test environment, browser dev tools  
**Documentation:** Record all results and issues found
