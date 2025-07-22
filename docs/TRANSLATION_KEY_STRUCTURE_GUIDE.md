# 🗝️ MechaMap Translation Key Structure & Usage Guide

**Generated**: 2025-07-20 09:40:05
**Purpose**: Complete guide for developers working with MechaMap's translation system

---

## 📋 **OVERVIEW**

MechaMap uses a **hierarchical translation system** with 5 main categories:

| Category | Purpose | Helper Function | Blade Directive |
|----------|---------|-----------------|------------------|
| **core** | Authentication, system functions | `t_core()` | `@core()` |
| **ui** | User interface elements | `t_ui()` | `@ui()` |
| **content** | Page content, static text | `t_content()` | `@content()` |
| **features** | Feature-specific translations | `t_feature()` | `@feature()` |
| **user** | User-related content | `t_user()` | `@user()` |

---

## 🏗️ **FILE STRUCTURE**

### **📂 admin/**

**Location**: `resources/lang/vi/admin/`
**Files**: 3

#### **dashboard.php**
```php
'overview' => 'Tổng quan'
'quick_actions.title' => 'Thao tác nhanh'
'quick_actions.manage_users' => 'Quản lý người dùng'
```

#### **system.php**
```php
'settings' => 'Cài đặt'
'maintenance' => 'Bảo trì'
'title' => 'Hệ thống'
```

#### **users.php**
```php
'management.title' => 'Quản lý người dùng'
'management.create' => 'Tạo người dùng'
'management.edit' => 'Chỉnh sửa người dùng'
```


### **📂 auth/**

**Location**: `resources/lang/vi/auth/`
**Files**: 7

#### **login.php**
```php
'title' => 'Đăng nhập'
'email' => 'Email'
'username' => 'Tên đăng nhập'
```

#### **logout.php**
```php
'title' => 'Đăng xuất'
'logout_button' => 'Đăng xuất'
'sign_out' => 'Đăng xuất'
```

#### **password.php**
```php
'forgot_title' => 'Quên mật khẩu'
'reset_title' => 'Đặt lại mật khẩu'
'change_title' => 'Đổi mật khẩu'
```

#### **profile.php**
```php
'title' => 'Hồ sơ'
'my_profile' => 'Hồ sơ của tôi'
'edit_profile' => 'Chỉnh sửa hồ sơ'
```

#### **register.php**
```php
'title' => 'Đăng ký'
'create_account' => 'Tạo tài khoản'
'sign_up' => 'Đăng ký'
```

#### **social.php**
```php
'login_with_google' => 'Đăng nhập bằng Google'
'login_with_facebook' => 'Đăng nhập bằng Facebook'
'login_with_twitter' => 'Đăng nhập bằng Twitter'
```

#### **verification.php**
```php
'title' => 'Xác thực email'
'verify_email' => 'Xác thực email'
'email_verified' => 'Email đã được xác thực'
```


### **📂 root/**

**Location**: `resources/lang/vi/`
**Files**: 14

#### **auth.php**
```php
'login_to_view_notifications' => 'Đăng nhập để xem thông báo'
'register_mechamap_account' => 'Đăng ký tài khoản MechaMap'
```

#### **buttons.php**
```php
'view_all' => 'Xem tất cả'
'load_more' => 'Tải thêm'
'show_more' => 'Hiển thị thêm'
```

#### **coming_soon.php**
```php
'notify_success' => 'Đăng ký thông báo thành công'
'share_text' => 'Chia sẻ với bạn bè'
'copied' => 'Đã sao chép'
```

#### **common.php**
```php
'cancel' => 'Hủy'
'delete' => 'Xóa'
'status' => 'Trạng thái'
```

#### **content.php**
```php
'recent_activity' => 'Hoạt động gần đây'
'weekly_activity' => 'Hoạt động tuần'
```

#### **core.php**
```php
'messages.error_occurred' => 'Có lỗi xảy ra'
'messages.image_not_found' => 'Không tìm thấy hình ảnh'
'messages.loading' => 'Đang tải...'
```

#### **forms.php**
```php
'search_placeholder' => 'Tìm kiếm...'
'email_placeholder' => 'Nhập email của bạn'
'password_placeholder' => 'Nhập mật khẩu của bạn'
```

#### **forum.php**
```php
'poll.votes' => 'Lượt bình chọn'
'poll.closed' => 'Cuộc bình chọn đã đóng'
'poll.vote' => 'Bình chọn'
```

#### **home.php**
```php
'featured_showcases' => 'Showcase nổi bật'
'featured_showcases_desc' => 'Khám phá những showcase tuyệt vời từ cộng đồng'
```

#### **nav.php**
```php
'main.home' => 'Trang chủ'
'main.community' => 'Cộng đồng'
'main.forum' => 'Diễn đàn'
```

#### **pagination.php**
```php
'previous' => 'Trước'
'next' => 'Tiếp'
'showing' => 'Hiển thị'
```

#### **time.php**
```php
'just_now' => 'Vừa xong'
'minutes_ago' => 'phút trước'
'hours_ago' => 'giờ trước'
```

#### **ui.php**
```php
'language.switched_successfully' => 'Đã chuyển đổi ngôn ngữ thành công'
'language.switch_failed' => 'Không thể chuyển đổi ngôn ngữ'
'language.auto_detected' => 'Ngôn ngữ được tự động phát hiện'
```

#### **user.php**
```php
'roles.admin' => 'Quản trị viên'
```


### **📂 content/**

**Location**: `resources/lang/vi/content/`
**Files**: 16

#### **about.php**
```php
'actions.add' => 'Thêm'
'actions.cancel' => 'Hủy'
'actions.delete' => 'Xóa'
```

#### **alerts.php**
```php
'processing' => 'Đang xử lý...'
'error_occurred' => 'Có lỗi xảy ra. Vui lòng thử lại.'
'success.created' => 'Tạo thành công!'
```

#### **business.php**
```php
'actions.add' => 'Thêm'
'actions.cancel' => 'Hủy'
'actions.delete' => 'Xóa'
```

#### **content.php**
```php
'pages.community_rules' => '[VI] Pages.community rules'
'pages.contact' => '[VI] Pages.contact'
'pages.about_us' => '[VI] Pages.about us'
```

#### **docs.php**
```php
'actions.add' => 'Thêm'
'actions.cancel' => 'Hủy'
'actions.delete' => 'Xóa'
```

#### **emails.php**
```php
'actions.activate_account' => 'Kích hoạt tài khoản'
'actions.contact_support' => 'Liên hệ hỗ trợ'
'actions.reset_password' => 'Đặt lại mật khẩu'
```

#### **faq.php**
```php
'actions.add' => 'Thêm'
'actions.cancel' => 'Hủy'
'actions.delete' => 'Xóa'
```

#### **help.php**
```php
'actions.add' => 'Thêm'
'actions.cancel' => 'Hủy'
'actions.delete' => 'Xóa'
```

#### **home.php**
```php
'hero_title' => 'Cộng đồng Kỹ thuật Cơ khí'
'hero_highlight' => 'Hàng đầu Việt Nam'
'hero_subtitle' => 'Nơi hội tụ 10,000+ kỹ sư, chia sẻ kiến thức và phát triển sự nghiệp trong lĩnh vực cơ khí'
```

#### **knowledge.php**
```php
'actions.add' => 'Thêm'
'actions.cancel' => 'Hủy'
'actions.delete' => 'Xóa'
```

#### **news.php**
```php
'actions.add' => 'Thêm'
'actions.cancel' => 'Hủy'
'actions.delete' => 'Xóa'
```

#### **new_content.php**
```php
'actions.add' => 'Thêm'
'actions.cancel' => 'Hủy'
'actions.delete' => 'Xóa'
```

#### **pages.php**
```php
'about.content' => 'Nội dung trang giới thiệu'
'about.description' => 'Tìm hiểu về MechaMap'
'about.title' => 'Giới thiệu'
```

#### **root.php**
```php
'messages.please_come_back_later' => 'Vui lòng quay lại sau'
'navigation.dashboard' => 'Dashboard'
'navigation.home' => 'Trang chủ'
```

#### **technical.php**
```php
'actions.add' => 'Thêm'
'actions.cancel' => 'Hủy'
'actions.delete' => 'Xóa'
```

#### **whats_new.php**
```php
'actions.all_posts' => 'Tất cả bài viết'
'actions.view_more' => 'Xem thêm'
'labels.featured' => 'Nổi bật'
```


### **📂 core/**

**Location**: `resources/lang/vi/core/`
**Files**: 7

#### **auth.php**
```php
'login.title' => 'Đăng nhập'
'login.email' => 'Email'
'login.password' => 'Mật khẩu'
```

#### **core.php**
```php
'messages.language.switched_successfully' => '[VI] Messages.language.switched successfully'
'messages.language.switch_failed' => '[VI] Messages.language.switch failed'
'messages.language.auto_detect_failed' => '[VI] Messages.language.auto detect failed'
```

#### **messages.php**
```php
'language.switched_successfully' => 'Chuyển ngôn ngữ thành công'
'language.switch_failed' => 'Chuyển ngôn ngữ thất bại'
'language.auto_detect_failed' => 'Tự động phát hiện ngôn ngữ thất bại'
```

#### **notifications.php**
```php
'marked_all_read' => 'Đã đánh dấu tất cả thông báo là đã đọc'
```

#### **pagination.php**
```php
'previous' => '&laquo; Trước'
'next' => 'Tiếp &raquo;'
```

#### **passwords.php**
```php
'reset' => 'Mật khẩu của bạn đã được đặt lại!'
'sent' => 'Chúng tôi đã gửi email liên kết đặt lại mật khẩu của bạn!'
'throttled' => 'Vui lòng đợi trước khi thử lại.'
```

#### **validation.php**
```php
'accepted' => 'Trường :attribute phải được chấp nhận.'
'active_url' => 'Trường :attribute không phải là một URL hợp lệ.'
'after' => 'Trường :attribute phải là một ngày sau :date.'
```


### **📂 features/**

**Location**: `resources/lang/vi/features/`
**Files**: 25

#### **bookmarks.php**
```php
'actions.add' => 'Thêm'
'actions.cancel' => 'Hủy'
'actions.delete' => 'Xóa'
```

#### **brand.php**
```php
'actions.add' => 'Thêm'
'actions.cancel' => 'Hủy'
'actions.delete' => 'Xóa'
```

#### **chat.php**
```php
'actions.add' => 'Thêm'
'actions.cancel' => 'Hủy'
'actions.delete' => 'Xóa'
```

#### **community.php**
```php
'Admin' => 'Quản trị'
'Administrator' => 'Quản trị viên'
'Administrators' => 'Quản trị viên'
```

#### **conversations.php**
```php
'actions.add' => 'Thêm'
'actions.cancel' => 'Hủy'
'actions.delete' => 'Xóa'
```

#### **devices.php**
```php
'actions.add' => 'Thêm'
'actions.cancel' => 'Hủy'
'actions.delete' => 'Xóa'
```

#### **features.php**
```php
'marketplace.actions.reload' => '[VI] Marketplace.actions.reload'
'marketplace.actions.details' => '[VI] Marketplace.actions.details'
'marketplace.actions.cancel' => '[VI] Marketplace.actions.cancel'
```

#### **following.php**
```php
'actions.add' => 'Thêm'
'actions.cancel' => 'Hủy'
'actions.delete' => 'Xóa'
```

#### **forum.php**
```php
'threads.title' => 'Chủ đề'
'threads.no_threads' => 'Chưa có chủ đề nào'
'threads.no_threads_found' => 'Không tìm thấy chủ đề'
```

#### **forums.php**
```php
'actions.create_new_topic' => 'Tạo chủ đề mới'
'actions.delete' => 'Xóa'
'actions.dislike' => 'Không thích'
```

#### **gallery.php**
```php
'actions.add' => 'Thêm'
'actions.cancel' => 'Hủy'
'actions.delete' => 'Xóa'
```

#### **knowledge.php**
```php
'articles.create' => 'Tạo bài viết'
'articles.edit' => 'Chỉnh sửa bài viết'
'articles.publish' => 'Xuất bản'
```

#### **manufacturer.php**
```php
'actions.add' => 'Thêm'
'actions.cancel' => 'Hủy'
'actions.delete' => 'Xóa'
```

#### **marketplace.php**
```php
'account_support' => 'Tài Khoản & Hỗ Trợ'
'actions.add_to_cart' => 'Thêm vào giỏ hàng'
'actions.buy_now' => 'Mua ngay'
```

#### **members.php**
```php
'actions.add' => 'Thêm'
'actions.cancel' => 'Hủy'
'actions.delete' => 'Xóa'
```

#### **realtime.php**
```php
'actions.add' => 'Thêm'
'actions.cancel' => 'Hủy'
'actions.delete' => 'Xóa'
```

#### **search.php**
```php
'actions.add' => 'Thêm'
'actions.cancel' => 'Hủy'
'actions.delete' => 'Xóa'
```

#### **showcase.php**
```php
'2_plus_stars' => '2+ sao'
'3_plus_stars' => '3+ sao'
'4_plus_stars' => '4+ sao'
```

#### **showcases.php**
```php
'actions.add' => 'Thêm'
'actions.cancel' => 'Hủy'
'actions.delete' => 'Xóa'
```

#### **student.php**
```php
'actions.add' => 'Thêm'
'actions.cancel' => 'Hủy'
'actions.delete' => 'Xóa'
```

#### **subscription.php**
```php
'actions.add' => 'Thêm'
'actions.cancel' => 'Hủy'
'actions.delete' => 'Xóa'
```

#### **supplier.php**
```php
'labels.customers' => 'Khách hàng'
'labels.inventory_management' => 'Quản lý kho'
'labels.orders' => 'Đơn hàng'
```

#### **threads.php**
```php
'actions.delete' => 'Xóa'
'actions.edit' => 'Chỉnh sửa'
'actions.follow' => 'Theo dõi'
```

#### **tools.php**
```php
'actions.add' => 'Thêm'
'actions.cancel' => 'Hủy'
'actions.delete' => 'Xóa'
```

#### **users.php**
```php
'actions.add' => 'Thêm'
'actions.cancel' => 'Hủy'
'actions.delete' => 'Xóa'
```


### **📂 forum/**

**Location**: `resources/lang/vi/forum/`
**Files**: 5

#### **categories.php**
```php
'title' => 'Danh mục'
'all_categories' => 'Tất cả danh mục'
'category' => 'Danh mục'
```

#### **poll.php**
```php
'votes' => 'lượt bình chọn|lượt bình chọn'
'closed' => 'Cuộc bình chọn đã đóng'
'vote' => 'Bình chọn'
```

#### **posts.php**
```php
'title' => 'Bài viết'
'new_post' => 'Bài viết mới'
'reply' => 'Trả lời'
```

#### **search.php**
```php
'title' => 'Tìm kiếm'
'search_forums' => 'Tìm kiếm diễn đàn'
'search_threads' => 'Tìm kiếm chủ đề'
```

#### **threads.php**
```php
'sticky' => 'Ghim'
'locked' => 'Khóa'
'title' => 'Chủ đề'
```


### **📂 ui/**

**Location**: `resources/lang/vi/ui/`
**Files**: 23

#### **actions.php**
```php
'view_full_showcase' => 'Xem showcase đầy đủ'
'view_details' => 'Xem chi tiết'
'read_more' => 'Đọc thêm'
```

#### **alerts.php**
```php
'actions.add' => 'Thêm'
'actions.cancel' => 'Hủy'
'actions.delete' => 'Xóa'
```

#### **auth.php**
```php
'login_to_view_notifications' => 'Đăng nhập để xem thông báo'
'register_mechamap_account' => 'Đăng ký tài khoản MechaMap'
```

#### **buttons.php**
```php
'activate' => 'Kích hoạt'
'add' => 'Thêm'
'add_to_cart' => 'Thêm vào giỏ'
```

#### **categories.php**
```php
'actions.add' => 'Thêm'
'actions.cancel' => 'Hủy'
'actions.delete' => 'Xóa'
```

#### **common.php**
```php
'about_mechamap' => 'Về MechaMap'
'about_us' => 'Về chúng tôi'
'add' => 'Thêm'
```

#### **core.php**
```php
'notifications.marked_all_read' => '[VI] Notifications.marked all read'
```

#### **forms.php**
```php
'actions.submit' => 'Gửi'
'actions.save' => 'Lưu'
'actions.cancel' => 'Hủy'
```

#### **frontend.php**
```php
'actions.add' => 'Thêm'
'actions.cancel' => 'Hủy'
'actions.delete' => 'Xóa'
```

#### **language.php**
```php
'switched_successfully.0' => 'Đã chuyển đổi ngôn ngữ thành công'
'switched_successfully.1' => '[VI] Switched successfully'
'switch_failed.0' => 'Không thể chuyển đổi ngôn ngữ'
```

#### **layouts.php**
```php
'navigation.about' => 'Về chúng tôi'
'navigation.community' => 'Cộng đồng'
'navigation.contact' => 'Liên hệ'
```

#### **messages.php**
```php
'new_message' => 'Tin nhắn mới'
```

#### **modals.php**
```php
'confirm_delete.title' => 'Xác nhận xóa'
'confirm_delete.message' => 'Bạn có chắc chắn muốn xóa?'
'confirm_delete.confirm' => 'Xóa'
```

#### **navigation.php**
```php
'home' => 'Trang chủ'
'forums' => 'Diễn đàn'
'marketplace' => 'Thị trường'
```

#### **notifications.php**
```php
'actions.add' => 'Thêm'
'actions.cancel' => 'Hủy'
'actions.delete' => 'Xóa'
```

#### **pagination.php**
```php
'page' => 'Trang'
'previous' => 'Trước'
'next' => 'Tiếp'
```

#### **partials.php**
```php
'actions.view_all' => 'Xem tất cả'
'options.all' => 'Tất cả'
'options.newest' => 'Mới nhất'
```

#### **roles.php**
```php
'admin' => 'Quản trị'
'brand' => 'Thương hiệu'
'supplier' => 'Nhà cung cấp'
```

#### **status.php**
```php
'sticky' => 'Ghim'
'coming_soon' => 'Sắp ra mắt'
```

#### **test.php**
```php
'actions.add' => 'Thêm'
'actions.cancel' => 'Hủy'
'actions.delete' => 'Xóa'
```

#### **ui.php**
```php
'forms.search_conversations_placeholder' => '[VI] Forms.search conversations placeholder'
'forms.enter_message_placeholder' => '[VI] Forms.enter message placeholder'
'forms.search_members_placeholder' => '[VI] Forms.search members placeholder'
```

#### **user.php**
```php
'roles.admin' => '[VI] Roles.admin'
```

#### **vendor.php**
```php
'pagination.first' => 'Trang đầu'
'pagination.last' => 'Trang cuối'
'pagination.next' => 'Trang sau'
```


### **📂 user/**

**Location**: `resources/lang/vi/user/`
**Files**: 6

#### **messages.php**
```php
'nav.community' => 'Diễn đàn'
'nav.technical_resources' => 'Tài nguyên kỹ thuật'
'nav.knowledge' => 'Kiến thức'
```

#### **notifications.php**
```php
'default.title' => 'Thông báo mới'
'default.message' => 'Bạn có một thông báo mới.'
'thread_created.title' => 'Thread mới trong diễn đàn'
```

#### **profile.php**
```php
'account' => 'Tài khoản'
'achievements' => 'Thành tích'
'actions.cancel_order' => 'Hủy đơn'
```

#### **roles.php**
```php
'admin' => 'Quản trị viên'
```

#### **settings.php**
```php
'account.title' => 'Cài đặt tài khoản'
'account.password' => 'Đổi mật khẩu'
'account.email' => 'Đổi email'
```

#### **user.php**
```php
'actions.logout' => 'Đăng xuất'
'labels.activity' => 'Hoạt động'
'labels.reports' => 'Báo cáo'
```


---

## 🔧 **USAGE METHODS**

### **1. Helper Functions (Recommended)**

```php
// Core translations (auth, system)
t_core('auth.login.title')           // → 'Đăng nhập'
t_core('notifications.marked_read')  // → 'Đã đánh dấu đã đọc'

// UI translations (buttons, forms)
t_ui('buttons.save')                 // → 'Lưu'
t_ui('forms.search_placeholder')     // → 'Tìm kiếm...'

// Content translations (pages, static)
t_content('home.hero.title')         // → 'Chào mừng đến MechaMap'
t_content('about.mission')           // → 'Sứ mệnh của chúng tôi'

// Feature translations (specific features)
t_feature('marketplace.cart.empty')  // → 'Giỏ hàng trống'
t_feature('forum.thread.create')     // → 'Tạo chủ đề mới'

// User translations (profiles, roles)
t_user('profile.edit.title')         // → 'Chỉnh sửa hồ sơ'
t_user('roles.admin')                // → 'Quản trị viên'
```

### **2. Blade Directives (Template)**

```blade
{{-- Core translations --}}
@core('auth.login.title')
@core('notifications.success')

{{-- UI translations --}}
@ui('buttons.submit')
@ui('common.loading')

{{-- Content translations --}}
@content('home.welcome')
@content('footer.copyright')

{{-- Feature translations --}}
@feature('marketplace.product.add')
@feature('forum.reply.button')

{{-- User translations --}}
@user('dashboard.overview')
@user('settings.privacy')
```

### **3. Laravel Standard (Fallback)**

```php
// When helper functions are not available
__('core/auth.login.title')
__('ui/buttons.save')
__('features/marketplace.cart.add')

// With parameters
__('user/messages.welcome', ['name' => $user->name])
__('core/time.updated_at', ['time' => $updatedAt])
```

---

## 📝 **NAMING CONVENTIONS**

### **Key Structure Pattern**
```
Format: {category}.{section}.{specific_key}
Example: ui.buttons.save
         ↑    ↑      ↑
      category section key
```

### **Category Guidelines**

| Category | Use For | Examples |
|----------|---------|----------|
| **core** | System functions, auth, notifications | `auth.login`, `system.error` |
| **ui** | Interface elements, buttons, forms | `buttons.save`, `forms.required` |
| **content** | Static content, pages | `home.title`, `about.description` |
| **features** | Feature-specific text | `marketplace.cart`, `forum.thread` |
| **user** | User-related content | `profile.edit`, `roles.admin` |

### **Section Guidelines**

- **actions**: Action buttons, verbs (`save`, `delete`, `create`)
- **labels**: Form labels, field names (`email`, `password`, `title`)
- **messages**: Status messages, alerts (`success`, `error`, `warning`)
- **navigation**: Menu items, links (`home`, `profile`, `settings`)
- **placeholders**: Input placeholders (`search_here`, `enter_email`)
- **validation**: Validation messages (`required`, `invalid_format`)

---

## ✅ **BEST PRACTICES**

### **1. Choose the Right Method**
- ✅ **Use helper functions** in PHP code: `t_ui('buttons.save')`
- ✅ **Use Blade directives** in templates: `@ui('buttons.save')`
- ⚠️ **Use Laravel standard** only when helpers unavailable

### **2. Key Naming**
- ✅ **Use descriptive names**: `marketplace.cart.empty_message`
- ❌ **Avoid generic names**: `text1`, `label`, `message`
- ✅ **Use snake_case**: `search_placeholder`
- ❌ **Avoid camelCase**: `searchPlaceholder`

### **3. Organization**
- ✅ **Group related keys**: All buttons in `ui.buttons.*`
- ✅ **Use consistent sections**: `actions`, `labels`, `messages`
- ✅ **Keep files focused**: One feature per file when possible

### **4. Maintenance**
- ✅ **Always add both VI and EN**: Maintain 100% coverage
- ✅ **Test translations**: Verify keys work in both languages
- ✅ **Document new keys**: Update this guide when adding categories

---

## 🚀 **QUICK REFERENCE**

### **Common Patterns**
```php
// Navigation
t_ui('navigation.home')              // Menu items
t_ui('navigation.marketplace')       // Main navigation

// Buttons
t_ui('buttons.save')                 // Action buttons
t_ui('buttons.cancel')               // Common actions

// Forms
t_ui('forms.email_label')            // Form labels
t_ui('forms.search_placeholder')     // Input placeholders

// Messages
t_core('messages.success')           // System messages
t_core('messages.error')             // Error handling

// Features
t_feature('marketplace.add_to_cart') // Feature-specific
t_feature('forum.create_thread')     // Module actions
```

### **File Locations Quick Map**
```
resources/lang/vi/
├── core/           # System, auth, notifications
├── ui/             # Interface elements
├── content/        # Static content
├── features/       # Feature-specific
├── user/           # User-related
└── *.php           # Root level files
```

---

## 📞 **SUPPORT**

- **Helper Functions**: Defined in `app/helpers.php`
- **Blade Directives**: Registered in `app/Providers/AppServiceProvider.php`
- **Translation Files**: Located in `resources/lang/vi/` and `resources/lang/en/`
- **Documentation**: This guide and related docs in `docs/` folder

**Last Updated**: 2025-07-20 09:40:05
