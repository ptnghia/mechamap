# 🔑 MechaMap Translation Key Naming Convention Guide

## 📋 **OVERVIEW**

This document defines the standardized naming conventions for translation keys in MechaMap Laravel backend, specifically for the 5 priority files being converted from hardcoded text to Laravel translation keys.

**Target Files:**
1. `threads/partials/showcase.blade.php`
2. `threads/create.blade.php`
3. `showcase/show.blade.php`
4. `devices/index.blade.php`
5. `layouts/app.blade.php`

---

## 🎯 **GENERAL NAMING PRINCIPLES**

### **📐 Structure Pattern:**
```
{file_group}.{file_name}.{category}.{specific_key}
```

### **🔤 Character Rules:**
- **Lowercase only**: All keys must be lowercase
- **Snake_case**: Use underscores to separate words
- **No special characters**: Only letters, numbers, and underscores
- **Descriptive**: Keys should be self-explanatory
- **Consistent**: Follow established patterns

### **📏 Length Guidelines:**
- **Maximum length**: 50 characters per key segment
- **Minimum length**: 3 characters per key segment
- **Total key length**: Ideally under 80 characters

---

## 📁 **FILE-SPECIFIC NAMING CONVENTIONS**

### **1. 🧩 threads/partials/showcase.blade.php**

**Base Pattern:** `threads.showcase.{category}.{key}`

#### **Categories:**
- **`categories`** - Forum category options
- **`ui`** - UI elements and labels
- **`editor`** - CKEditor configuration
- **`placeholders`** - Input placeholders
- **`titles`** - Section titles and headings
- **`options`** - Dropdown and select options

#### **Examples:**
```php
// Category options
'threads.showcase.categories.mechanical_design' => 'Thiết kế Cơ khí'
'threads.showcase.categories.manufacturing' => 'Công nghệ Chế tạo'

// UI elements
'threads.showcase.ui.close_button' => 'Đóng'
'threads.showcase.ui.save_button' => 'Lưu'

// Editor configuration
'threads.showcase.editor.toolbar_bold' => 'Đậm'
'threads.showcase.editor.toolbar_italic' => 'Nghiêng'

// Placeholders
'threads.showcase.placeholders.search_categories' => 'Tìm kiếm danh mục...'
```

---

### **2. 📝 threads/create.blade.php**

**Base Pattern:** `threads.create.{category}.{key}`

#### **Categories:**
- **`form`** - Form labels and fields
- **`validation`** - Validation messages
- **`actions`** - Buttons and action elements
- **`placeholders`** - Input placeholders
- **`titles`** - Form section titles

#### **Examples:**
```php
// Form elements
'threads.create.form.title_label' => 'Tiêu đề'
'threads.create.form.content_label' => 'Nội dung'
'threads.create.form.category_select' => 'Chọn danh mục'

// Validation messages
'threads.create.validation.title_required' => 'Tiêu đề là bắt buộc'
'threads.create.validation.content_min_length' => 'Nội dung tối thiểu 10 ký tự'

// Actions
'threads.create.actions.submit_button' => 'Đăng bài'
'threads.create.actions.save_draft' => 'Lưu nháp'

// Placeholders
'threads.create.placeholders.title_input' => 'Nhập tiêu đề bài viết...'
```

---

### **3. 🎨 showcase/show.blade.php**

**Base Pattern:** `showcase.show.{category}.{key}`

#### **Categories:**
- **`actions`** - Action buttons and links
- **`status`** - Status labels and indicators
- **`content`** - Content descriptions and text
- **`navigation`** - Navigation elements
- **`titles`** - Page and section titles
- **`placeholders`** - Input placeholders

#### **Examples:**
```php
// Actions
'showcase.show.actions.follow_button' => 'Theo dõi'
'showcase.show.actions.save_bookmark' => 'Lưu'
'showcase.show.actions.share_link' => 'Chia sẻ'

// Status
'showcase.show.status.published' => 'Đã xuất bản'
'showcase.show.status.draft' => 'Bản nháp'

// Content
'showcase.show.content.description_label' => 'Mô tả'
'showcase.show.content.technical_specs' => 'Thông số kỹ thuật'

// Navigation
'showcase.show.navigation.back_to_list' => 'Quay lại danh sách'
```

---

### **4. 🔧 devices/index.blade.php**

**Base Pattern:** `devices.index.{category}.{key}`

#### **Categories:**
- **`table`** - Table headers and columns
- **`filters`** - Filter options and labels
- **`actions`** - Action buttons
- **`status`** - Status indicators
- **`titles`** - Page and section titles
- **`messages`** - User messages and notifications

#### **Examples:**
```php
// Table headers
'devices.index.table.device_name' => 'Tên thiết bị'
'devices.index.table.manufacturer' => 'Nhà sản xuất'
'devices.index.table.status' => 'Trạng thái'

// Filters
'devices.index.filters.category_all' => 'Tất cả danh mục'
'devices.index.filters.status_active' => 'Đang hoạt động'

// Actions
'devices.index.actions.view_details' => 'Xem chi tiết'
'devices.index.actions.edit_device' => 'Chỉnh sửa'

// Messages
'devices.index.messages.no_devices_found' => 'Không tìm thấy thiết bị nào'
```

---

### **5. 🏗️ layouts/app.blade.php**

**Base Pattern:** `layouts.app.{category}.{key}`

#### **Categories:**
- **`navigation`** - Navigation menu items
- **`meta`** - Meta tags and SEO content
- **`ui`** - Global UI components
- **`global`** - Global application elements
- **`actions`** - Global action buttons

#### **Examples:**
```php
// Navigation
'layouts.app.navigation.home_link' => 'Trang chủ'
'layouts.app.navigation.forums_link' => 'Diễn đàn'
'layouts.app.navigation.marketplace_link' => 'Thị trường'

// Meta
'layouts.app.meta.site_title' => 'MechaMap - Cộng đồng Kỹ sư Cơ khí'
'layouts.app.meta.site_description' => 'Nền tảng chia sẻ kiến thức kỹ thuật'

// UI components
'layouts.app.ui.search_placeholder' => 'Tìm kiếm...'
'layouts.app.ui.loading_text' => 'Đang tải...'

// Global actions
'layouts.app.actions.login_button' => 'Đăng nhập'
'layouts.app.actions.register_button' => 'Đăng ký'
```

---

## 🎨 **CATEGORY DEFINITIONS**

### **📋 Standard Categories:**

| Category | Purpose | Examples |
|----------|---------|----------|
| **`actions`** | Buttons, links, clickable elements | save, delete, edit, submit |
| **`form`** | Form labels, field names | title, content, email, password |
| **`validation`** | Error messages, validation text | required, min_length, invalid_format |
| **`placeholders`** | Input placeholder text | enter_title, search_here, select_option |
| **`titles`** | Headings, section titles | page_title, section_header |
| **`status`** | Status indicators, labels | active, inactive, pending, approved |
| **`navigation`** | Menu items, breadcrumbs | home, back, next, previous |
| **`messages`** | User notifications, alerts | success, error, warning, info |
| **`ui`** | General UI elements | close, open, expand, collapse |
| **`content`** | Content descriptions, text | description, summary, details |
| **`table`** | Table headers, columns | name, date, status, actions |
| **`filters`** | Filter options, search | all, active, category, type |
| **`meta`** | SEO, meta information | title, description, keywords |
| **`global`** | Application-wide elements | loading, error, success |
| **`options`** | Dropdown, select options | yes, no, all, none |
| **`editor`** | Text editor elements | bold, italic, link, image |

---

## 🔧 **IMPLEMENTATION GUIDELINES**

### **✅ Best Practices:**

1. **Consistency First**
   - Follow established patterns in existing translation files
   - Use same category names across similar contexts
   - Maintain consistent key structure

2. **Descriptive Naming**
   - Keys should be self-explanatory
   - Avoid abbreviations unless commonly understood
   - Use context-specific terms

3. **Logical Grouping**
   - Group related keys under same category
   - Use hierarchical structure for complex forms
   - Separate concerns clearly

4. **Future-Proof Design**
   - Consider potential expansion
   - Use generic enough names for reusability
   - Avoid overly specific contexts

### **❌ Common Mistakes to Avoid:**

1. **Inconsistent Casing**
   ```php
   // Wrong
   'threads.create.Form.titleLabel'
   
   // Correct
   'threads.create.form.title_label'
   ```

2. **Too Generic Keys**
   ```php
   // Wrong
   'common.text1'
   
   // Correct
   'threads.create.form.title_label'
   ```

3. **Too Specific Keys**
   ```php
   // Wrong
   'threads.create.form.title_label_for_mechanical_design_posts'
   
   // Correct
   'threads.create.form.title_label'
   ```

4. **Mixed Languages in Keys**
   ```php
   // Wrong
   'threads.create.tiêu_đề'
   
   // Correct
   'threads.create.form.title_label'
   ```

---

## 📊 **VALIDATION CHECKLIST**

### **🔍 Key Quality Check:**
- [ ] **Lowercase only** - No uppercase characters
- [ ] **Snake_case** - Underscores between words
- [ ] **Descriptive** - Self-explanatory meaning
- [ ] **Consistent** - Follows established patterns
- [ ] **Appropriate length** - Not too long or short
- [ ] **Proper category** - Correctly categorized
- [ ] **No duplicates** - Unique within scope
- [ ] **Future-proof** - Reusable and extensible

### **📋 File Structure Check:**
- [ ] **Correct base pattern** - Follows file-specific convention
- [ ] **Logical categorization** - Keys grouped appropriately
- [ ] **Consistent hierarchy** - Same depth levels
- [ ] **Complete coverage** - All hardcoded text addressed

---

## 🎯 **EXAMPLES FROM ACTUAL CONVERSION**

### **✅ Good Examples:**
```php
// From threads/partials/showcase.blade.php
'threads.showcase.categories.mechanical_design' => 'Thiết kế Cơ khí'
'threads.showcase.ui.close_button' => 'Đóng'
'threads.showcase.placeholders.search_categories' => 'Tìm kiếm danh mục...'

// From showcase/show.blade.php
'showcase.show.actions.follow_button' => 'Theo dõi'
'showcase.show.status.published' => 'Đã xuất bản'
'showcase.show.content.description_label' => 'Mô tả'
```

### **🔧 Auto-Generated Examples:**
```php
// Generated by script with conflict resolution
'threads.create.form.bytes_1' => 'bytes'
'showcase.show.actions.post_1' => 'POST'
'devices.index.table.patch_1' => 'PATCH'
```

---

## 🚀 **CONCLUSION**

This naming convention ensures:
- **Consistency** across all translation files
- **Maintainability** for future development
- **Clarity** for developers and translators
- **Scalability** for project growth
- **Quality** in internationalization implementation

**Follow these guidelines for all future translation key creation in MechaMap project.**
