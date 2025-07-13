# 🌐 Frontend Internationalization Audit Plan

**Comprehensive audit plan for MechaMap frontend internationalization**

[![Audit Status](https://img.shields.io/badge/Status-In%20Progress-yellow.svg)](#audit-progress)
[![Priority](https://img.shields.io/badge/Priority-High-red.svg)](#priority-areas)
[![Target](https://img.shields.io/badge/Target-100%25%20i18n-green.svg)](#completion-criteria)

---

## 🎯 **Audit Objectives**

### **Primary Goals:**
1. **Identify all hardcoded text** trong frontend views (excluding admin)
2. **Ensure consistent translation key usage** (`__('key')` hoặc `@lang('key')`)
3. **Create comprehensive translation key list** cần bổ sung
4. **Prioritize critical user-facing areas** for immediate fixes
5. **Establish i18n standards** cho future development

### **Scope:**
- ✅ **Include**: All frontend user views (`resources/views/` except `/admin`)
- ❌ **Exclude**: Admin panel views (`resources/views/admin/`)
- ✅ **Include**: Components, partials, layouts
- ✅ **Include**: JavaScript files với user-facing text
- ✅ **Include**: CSS files với content text

---

## 📋 **Audit Checklist**

### **🏗️ Phase 1: Core Layout & Navigation (Priority: CRITICAL)**

#### **1.1 Main Layout Files**
- [ ] `resources/views/layouts/app.blade.php`
- [ ] `resources/views/components/header.blade.php`
- [ ] `resources/views/components/footer.blade.php`
- [ ] `resources/views/components/sidebar.blade.php`
- [ ] `resources/views/components/sidebar-improved.blade.php`
- [ ] `resources/views/components/sidebar-professional.blade.php`

**Expected Issues:**
- Navigation menu items
- Button labels
- Dropdown text
- Footer links and copyright
- Search placeholders

#### **1.2 Authentication System**
- [ ] `resources/views/auth/login.blade.php`
- [ ] `resources/views/auth/register.blade.php`
- [ ] `resources/views/auth/forgot-password.blade.php`
- [ ] `resources/views/auth/reset-password.blade.php`
- [ ] `resources/views/auth/verify-email.blade.php`
- [ ] `resources/views/components/auth-modal.blade.php`
- [ ] `resources/views/auth/wizard/step1.blade.php`
- [ ] `resources/views/auth/wizard/step2.blade.php`

**Expected Issues:**
- Form labels and placeholders
- Validation messages
- Success/error messages
- Button text
- Help text

### **🛒 Phase 2: Marketplace & E-commerce (Priority: HIGH)**

#### **2.1 Marketplace Core**
- [ ] `resources/views/marketplace/index.blade.php`
- [ ] `resources/views/marketplace/products/index.blade.php`
- [ ] `resources/views/marketplace/products/show.blade.php`
- [ ] `resources/views/marketplace/products/create.blade.php`
- [ ] `resources/views/marketplace/categories/index.blade.php`
- [ ] `resources/views/marketplace/categories/show.blade.php`

#### **2.2 Shopping & Orders**
- [ ] `resources/views/marketplace/cart/index.blade.php`
- [ ] `resources/views/marketplace/checkout/index.blade.php`
- [ ] `resources/views/marketplace/checkout/success.blade.php`
- [ ] `resources/views/marketplace/orders/index.blade.php`
- [ ] `resources/views/marketplace/orders/show.blade.php`
- [ ] `resources/views/marketplace/downloads/index.blade.php`

#### **2.3 Marketplace Components**
- [ ] `resources/views/components/product-card.blade.php`
- [ ] `resources/views/components/marketplace/` (all files)

**Expected Issues:**
- Product descriptions
- Price labels
- Category names
- Filter options
- Checkout steps
- Order status text

### **🗣️ Phase 3: Community & Forums (Priority: HIGH)**

#### **3.1 Forum System**
- [ ] `resources/views/forums/index.blade.php`
- [ ] `resources/views/forums/show.blade.php`
- [ ] `resources/views/forums/search.blade.php`
- [ ] `resources/views/forums/search-advanced.blade.php`
- [ ] `resources/views/forums/search-by-category.blade.php`
- [ ] `resources/views/categories/show.blade.php`

#### **3.2 Threads & Posts**
- [ ] `resources/views/threads/index.blade.php`
- [ ] `resources/views/threads/show.blade.php`
- [ ] `resources/views/threads/create.blade.php`
- [ ] `resources/views/threads/edit.blade.php`
- [ ] `resources/views/threads/saved.blade.php`

#### **3.3 Forum Components**
- [ ] `resources/views/partials/thread-item.blade.php`
- [ ] `resources/views/components/thread-follow-button.blade.php`
- [ ] `resources/views/components/thread-creation-sidebar.blade.php`

**Expected Issues:**
- Forum category names
- Thread status labels
- Post actions (reply, edit, delete)
- Search filters
- Pagination text

### **🏆 Phase 4: Showcases & Projects (Priority: MEDIUM)**

#### **4.1 Showcase System**
- [ ] `resources/views/showcase/public.blade.php`
- [ ] `resources/views/showcase/show.blade.php`
- [ ] `resources/views/showcase/create.blade.php`
- [ ] `resources/views/showcases/partials/` (all files)

#### **4.2 Showcase Components**
- [ ] `resources/views/components/showcase-card.blade.php`
- [ ] `resources/views/components/showcase-image.blade.php`
- [ ] `resources/views/partials/showcase-item.blade.php`

### **👤 Phase 5: User Profile & Dashboard (Priority: MEDIUM)**

#### **5.1 User Management**
- [ ] `resources/views/profile/index.blade.php`
- [ ] `resources/views/profile/show.blade.php`
- [ ] `resources/views/profile/edit.blade.php`
- [ ] `resources/views/profile/activities.blade.php`
- [ ] `resources/views/profile/orders.blade.php`

#### **5.2 Dashboard & Settings**
- [ ] `resources/views/dashboard.blade.php`
- [ ] `resources/views/user/dashboard.blade.php`
- [ ] `resources/views/user/settings.blade.php`
- [ ] `resources/views/user/bookmarks.blade.php`

### **📄 Phase 6: Content Pages (Priority: LOW)**

#### **6.1 Static Pages**
- [ ] `resources/views/home.blade.php`
- [ ] `resources/views/about/index.blade.php`
- [ ] `resources/views/pages/about.blade.php`
- [ ] `resources/views/pages/contact.blade.php`
- [ ] `resources/views/pages/rules.blade.php`
- [ ] `resources/views/help/index.blade.php`
- [ ] `resources/views/faq/index.blade.php`

#### **6.2 Business & Technical**
- [ ] `resources/views/business/index.blade.php`
- [ ] `resources/views/business/services.blade.php`
- [ ] `resources/views/technical/index.blade.php`
- [ ] `resources/views/frontend/business/` (all files)

---

## 🔍 **Audit Methodology**

### **Step 1: Automated Scanning**
```bash
# Find hardcoded Vietnamese text
grep -r "[\u00C0-\u1EF9]" resources/views/ --include="*.blade.php" --exclude-dir=admin

# Find hardcoded English text patterns
grep -r -E "(Please|Click|Submit|Cancel|Save|Delete|Edit|View|Search)" resources/views/ --include="*.blade.php" --exclude-dir=admin

# Find missing translation patterns
grep -r -E ">[^<]*[a-zA-Z]{3,}[^<]*<" resources/views/ --include="*.blade.php" --exclude-dir=admin
```

### **Step 2: Manual Review**
1. **Open each file** trong checklist
2. **Scan for hardcoded text** trong:
   - HTML content between tags
   - Attribute values (title, placeholder, alt)
   - JavaScript strings
   - CSS content properties
3. **Check translation key usage**:
   - `{{ __('key') }}` ✅
   - `@lang('key')` ✅
   - `"Hardcoded text"` ❌
4. **Document findings** trong audit spreadsheet

### **Step 3: Translation Key Creation**
1. **Extract all hardcoded text**
2. **Create meaningful key names**:
   - `messages.auth.login` instead of `login`
   - `marketplace.product.add_to_cart` instead of `add_cart`
3. **Group by functionality**:
   - `nav.*` - Navigation items
   - `auth.*` - Authentication
   - `marketplace.*` - E-commerce
   - `forum.*` - Community features

---

## 📊 **Expected Findings Categories**

### **🔴 Critical Issues (Fix Immediately)**
- Navigation menu items
- Authentication forms
- Error messages
- Call-to-action buttons

### **🟡 High Priority Issues**
- Product listings
- Forum content
- User dashboard
- Search functionality

### **🟢 Medium Priority Issues**
- Static page content
- Help documentation
- Footer links
- Breadcrumbs

### **⚪ Low Priority Issues**
- Admin-facing text in frontend
- Debug messages
- Developer comments

---

## 📈 **Progress Tracking**

### **Completion Criteria:**
- [ ] **100% of critical views audited**
- [ ] **All hardcoded text identified**
- [ ] **Translation keys created**
- [ ] **Implementation plan ready**
- [ ] **Testing checklist prepared**

### **Deliverables:**
1. **Detailed audit report** với findings
2. **Complete translation key list** for `messages.php`
3. **Implementation priority matrix**
4. **Code fix recommendations**
5. **Testing and QA plan**

---

## 🛠️ **Tools & Resources**

### **Audit Tools:**
- **grep/ripgrep** for text scanning
- **VS Code** với regex search
- **Browser DevTools** for runtime text
- **Translation key validator** script

### **Reference Files:**
- `resources/lang/vi/messages.php` - Current translations
- `resources/lang/en/messages.php` - English translations
- Laravel i18n documentation

---

**Next Step: Begin Phase 1 audit of core layout files**
