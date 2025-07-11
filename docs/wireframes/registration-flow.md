# 🎨 MechaMap Multi-Step Registration Form - Wireframe & Design

**Created:** 2025-07-12
**Task:** 1.1 Phân tích và thiết kế multi-step registration form
**Purpose:** Thiết kế UX/UI cho wizard registration form với business information collection

---

## 📋 **CURRENT STATE ANALYSIS**

### **Existing Registration Form Issues:**
1. **Single-step form quá dài** - 8 fields trong 1 page
2. **Thiếu business information collection** cho business partners
3. **Không có progress indicator** - users không biết còn bao nhiêu steps
4. **Validation feedback không optimal** - chỉ hiện sau submit
5. **Mobile experience chưa tối ưu** - form quá dài trên mobile

### **Current Form Fields:**
- ✅ Name, Username, Email, Password, Confirm Password
- ✅ Account Type (dropdown với 5 options)
- ✅ Terms & Privacy checkbox
- ❌ **Missing:** Business information cho business partners
- ❌ **Missing:** Document upload capability
- ❌ **Missing:** Step-by-step guidance

---

## 🎯 **DESIGN OBJECTIVES**

### **Primary Goals:**
1. **Reduce cognitive load** - Break form into logical steps
2. **Collect business information** cho manufacturer, supplier, brand
3. **Improve conversion rate** - Better UX = more registrations
4. **Mobile-first design** - Responsive wizard interface
5. **Real-time validation** - Immediate feedback on errors

### **Success Metrics:**
- **Completion Rate:** Target 85%+ (vs current ~70%)
- **Time to Complete:** Target <3 minutes
- **Error Rate:** Target <10% validation errors
- **Mobile Conversion:** Target 80%+ mobile completion

---

## 🔄 **MULTI-STEP FLOW DESIGN**

### **Step 1: Basic Information** (All Users)
```
┌─────────────────────────────────────────────────────────────┐
│  🎯 MechaMap Registration - Step 1 of 2                    │
│  ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ │
│  Progress: ████████████████████████████████████████████▒▒▒▒ │
│                                                             │
│  👤 Thông tin cơ bản                                       │
│                                                             │
│  📝 Họ và tên *                                            │
│  [________________________]                                │
│                                                             │
│  🏷️  Tên đăng nhập *                                       │
│  [________________________] ✓ Available                    │
│                                                             │
│  📧 Email *                                                 │
│  [________________________]                                │
│                                                             │
│  🔒 Mật khẩu *                                              │
│  [________________________] [👁️]                           │
│  ████████▒▒ Strong                                          │
│                                                             │
│  🔒 Xác nhận mật khẩu *                                     │
│  [________________________] ✓ Match                        │
│                                                             │
│  🏷️  Loại tài khoản *                                      │
│  [🌟 Thành viên cộng đồng        ▼]                        │
│    • Member - Thảo luận & chia sẻ                          │
│    • Student - Học tập & nghiên cứu                        │
│  [🏢 Đối tác kinh doanh          ▼]                        │
│    • Manufacturer - Sản xuất sản phẩm                      │
│    • Supplier - Phân phối thiết bị                         │
│    • Brand - Quảng bá thương hiệu                          │
│                                                             │
│  ☑️ Tôi đồng ý với Điều khoản & Chính sách bảo mật         │
│                                                             │
│  [◀ Quay lại]              [Tiếp tục ▶]                    │
│                                                             │
│  Đã có tài khoản? Đăng nhập ngay                           │
└─────────────────────────────────────────────────────────────┘
```

### **Step 2A: Community Members** (Member/Student)
```
┌─────────────────────────────────────────────────────────────┐
│  🎯 MechaMap Registration - Step 2 of 2                    │
│  ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ │
│  Progress: ████████████████████████████████████████████████ │
│                                                             │
│  🌟 Hoàn tất đăng ký thành viên                            │
│                                                             │
│  🎉 Chào mừng bạn đến với MechaMap!                        │
│                                                             │
│  ✅ Tài khoản của bạn đã được tạo thành công               │
│  📧 Email xác minh đã được gửi đến: user@example.com       │
│                                                             │
│  📋 Bước tiếp theo:                                         │
│  1. Kiểm tra email và xác minh tài khoản                   │
│  2. Hoàn thiện profile cá nhân                             │
│  3. Tham gia cộng đồng MechaMap                            │
│                                                             │
│  [🏠 Về Dashboard]          [📧 Gửi lại email]            │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

### **Step 2B: Business Partners** (Manufacturer/Supplier/Brand)
```
┌─────────────────────────────────────────────────────────────┐
│  🎯 MechaMap Registration - Step 2 of 2                    │
│  ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ │
│  Progress: ████████████████████████████████████████████████ │
│                                                             │
│  🏢 Thông tin doanh nghiệp                                 │
│                                                             │
│  🏭 Tên công ty *                                           │
│  [_________________________________]                       │
│                                                             │
│  📄 Giấy phép kinh doanh *                                 │
│  [_________________________________]                       │
│                                                             │
│  🏷️  Mã số thuế *                                          │
│  [_________________________________]                       │
│                                                             │
│  📝 Mô tả hoạt động kinh doanh *                           │
│  [_________________________________]                       │
│  [_________________________________]                       │
│  [_________________________________]                       │
│                                                             │
│  🏷️  Lĩnh vực kinh doanh *                                 │
│  ☑️ Automotive    ☑️ Aerospace    ☐ Manufacturing         │
│  ☐ Materials     ☐ Components    ☐ Industrial             │
│                                                             │
│  📞 Số điện thoại công ty                                  │
│  [_________________________________]                       │
│                                                             │
│  📧 Email công ty                                          │
│  [_________________________________]                       │
│                                                             │
│  📍 Địa chỉ công ty                                        │
│  [_________________________________]                       │
│  [_________________________________]                       │
│                                                             │
│  📎 Tài liệu đính kèm (tùy chọn)                          │
│  [📁 Chọn file...] Chứng chỉ, giấy phép bổ sung          │
│                                                             │
│  [◀ Quay lại]              [Hoàn tất đăng ký]             │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

### **Step 3: Business Registration Success**
```
┌─────────────────────────────────────────────────────────────┐
│  🎯 Đăng ký doanh nghiệp thành công!                       │
│                                                             │
│  🎉 Chào mừng đến với MechaMap Business!                   │
│                                                             │
│  ✅ Tài khoản doanh nghiệp đã được tạo                     │
│  📧 Email xác minh đã gửi đến: business@company.com        │
│  ⏳ Đang chờ xác thực từ admin                             │
│                                                             │
│  📋 Trạng thái tài khoản:                                  │
│  ✅ Thông tin cơ bản: Hoàn thành                           │
│  ✅ Thông tin doanh nghiệp: Hoàn thành                     │
│  ⏳ Xác thực admin: Đang chờ                               │
│                                                             │
│  📝 Bước tiếp theo:                                         │
│  1. Xác minh email trong hộp thư                           │
│  2. Chờ admin xác thực thông tin doanh nghiệp              │
│  3. Nhận thông báo khi tài khoản được kích hoạt            │
│                                                             │
│  💡 Thời gian xác thực: 1-3 ngày làm việc                  │
│                                                             │
│  [🏠 Về Dashboard]    [📞 Liên hệ hỗ trợ]                 │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

---

## 📱 **MOBILE RESPONSIVE DESIGN**

### **Mobile Step Layout:**
```
┌─────────────────────┐
│ 🎯 MechaMap Step 1  │
│ ████████████▒▒▒▒    │
│                     │
│ 👤 Thông tin cơ bản │
│                     │
│ 📝 Họ và tên *      │
│ [_________________] │
│                     │
│ 🏷️ Tên đăng nhập *  │
│ [_________________] │
│ ✓ Available         │
│                     │
│ 📧 Email *          │
│ [_________________] │
│                     │
│ 🔒 Mật khẩu *       │
│ [_______________][👁️] │
│ ████████▒▒ Strong   │
│                     │
│ 🔒 Xác nhận *       │
│ [_________________] │
│ ✓ Match             │
│                     │
│ 🏷️ Loại tài khoản * │
│ [Member         ▼] │
│                     │
│ ☑️ Đồng ý điều khoản │
│                     │
│ [    Tiếp tục ▶   ] │
│                     │
│ Đã có TK? Đăng nhập │
└─────────────────────┘
```

---

## 🎨 **UI/UX DESIGN SPECIFICATIONS**

### **Color Scheme:**
- **Primary:** #007bff (MechaMap Blue)
- **Success:** #28a745 (Validation Success)
- **Warning:** #ffc107 (Validation Warning)
- **Error:** #dc3545 (Validation Error)
- **Background:** #f8f9fa (Light Gray)
- **Text:** #212529 (Dark Gray)

### **Typography:**
- **Headers:** Inter Bold, 24px/28px
- **Labels:** Inter Medium, 14px/20px
- **Input Text:** Inter Regular, 16px/24px
- **Helper Text:** Inter Regular, 12px/16px

### **Spacing:**
- **Container:** max-width: 600px, margin: 0 auto
- **Field Spacing:** margin-bottom: 24px
- **Button Height:** 48px (mobile), 40px (desktop)
- **Border Radius:** 8px (modern, friendly)

### **Animation & Transitions:**
- **Step Transition:** 300ms ease-in-out
- **Validation Feedback:** 200ms ease-out
- **Progress Bar:** 400ms ease-in-out
- **Button Hover:** 150ms ease-out

---

## 🔧 **TECHNICAL SPECIFICATIONS**

### **Frontend Framework:**
- **Base:** Laravel Blade Templates
- **CSS:** Bootstrap 5 + Custom CSS
- **JavaScript:** Vanilla JS (no jQuery dependency)
- **Icons:** Font Awesome 6
- **Validation:** Real-time client-side + server-side

### **Session Management:**
- **Storage:** Laravel Session (encrypted)
- **Timeout:** 30 minutes inactivity
- **Data Persistence:** Between steps
- **Cleanup:** After completion or timeout

### **Validation Strategy:**
- **Step 1:** Real-time validation on blur/change
- **Step 2:** Conditional validation based on account_type
- **Server-side:** Laravel Form Requests
- **Client-side:** Custom JavaScript validation

### **Accessibility (WCAG 2.1 AA):**
- **Keyboard Navigation:** Full tab support
- **Screen Readers:** ARIA labels and descriptions
- **Color Contrast:** 4.5:1 minimum ratio
- **Focus Indicators:** Clear visual focus states
- **Error Announcements:** Live regions for validation

---

## 📊 **USER FLOW ANALYSIS**

### **Decision Points:**
1. **Account Type Selection** → Determines Step 2 content
2. **Business Partner Path** → Requires additional information
3. **Community Member Path** → Direct to completion
4. **Validation Errors** → Stay on current step
5. **Session Timeout** → Redirect to Step 1 with data recovery

### **Exit Points:**
- **Step 1:** "Quay lại" → Landing page
- **Step 2:** "Quay lại" → Step 1 (data preserved)
- **Any Step:** "Đã có tài khoản?" → Login page
- **Completion:** → Dashboard or Email Verification

### **Error Handling:**
- **Network Errors:** Retry mechanism with user feedback
- **Validation Errors:** Inline messages with correction guidance
- **Session Timeout:** Auto-save with recovery option
- **Server Errors:** Graceful degradation with support contact

---

## 🎯 **CONVERSION OPTIMIZATION**

### **Psychological Principles:**
1. **Progress Indication** → Reduces abandonment
2. **Chunking** → Reduces cognitive load
3. **Immediate Feedback** → Builds confidence
4. **Clear Value Proposition** → Motivates completion
5. **Social Proof** → Trust indicators

### **A/B Testing Opportunities:**
- **Progress Bar Style:** Linear vs Circular
- **Button Text:** "Tiếp tục" vs "Bước tiếp theo"
- **Field Order:** Email first vs Name first
- **Account Type Presentation:** Dropdown vs Radio buttons

### **Performance Targets:**
- **Page Load:** <2 seconds
- **Step Transition:** <300ms
- **Validation Response:** <100ms
- **Form Submission:** <1 second

---

**🎨 DESIGN STATUS:** ✅ Wireframe Complete - Ready for Implementation**
