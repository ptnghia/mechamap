# 👤 Guest Strategy V2 - Follow-Only Experience

**Created:** 2025-07-12  
**Proposed by:** User  
**Status:** ✅ APPROVED & IMPLEMENTED  

---

## 🎯 **STRATEGY OVERVIEW**

### **💡 Core Concept:**
Guest vẫn cần đăng ký như Member, nhưng có một số hạn chế:
- ✅ **Có quyền xem** nội dung công khai ở diễn đàn và showcase
- ✅ **Có quyền theo dõi** Member và bài đăng để cập nhật nội dung mới
- ❌ **Không có quyền đăng bài** hoặc bình luận
- ❌ **Không có quyền marketplace**

### **🎨 User Experience Design:**
```
Guest = "Trial Member" với limited permissions
↓
Engagement through following & notifications
↓
Incentive to upgrade to full Member
```

---

## 📊 **PERMISSION MATRIX**

### **✅ GUEST PERMISSIONS:**

**Viewing & Browsing:**
- ✅ `view-content` - Xem tất cả nội dung công khai
- ✅ `browse-forums` - Duyệt forums và categories
- ✅ `browse-showcases` - Xem showcases và projects
- ✅ `view-user-profiles` - Xem profile thành viên

**Social Features (Follow Only):**
- ✅ `follow-users` - Theo dõi Member (max 50)
- ✅ `follow-threads` - Theo dõi bài đăng (max 100)
- ✅ `follow-showcases` - Theo dõi showcases (max 30)
- ✅ `receive-notifications` - Nhận thông báo updates
- ✅ `view-following-feed` - Xem bảng tin theo dõi

### **❌ GUEST RESTRICTIONS:**

**Content Creation:**
- ❌ `create-threads` - Không tạo chủ đề
- ❌ `create-comments` - Không bình luận
- ❌ `create-showcases` - Không tạo showcase
- ❌ `edit-content` - Không chỉnh sửa
- ❌ `delete-content` - Không xóa

**Interactions:**
- ❌ `vote-polls` - Không bình chọn
- ❌ `rate-content` - Không đánh giá
- ❌ `like-content` - Không like
- ❌ `share-content` - Không share
- ❌ `report-content` - Không báo cáo

**Advanced Features:**
- ❌ `marketplace-access` - Không marketplace
- ❌ `send-messages` - Không tin nhắn
- ❌ `upload-files` - Không upload
- ❌ `manage-profile` - Không chỉnh sửa profile

---

## 🔄 **USER JOURNEY**

### **Phase 1: Guest Registration**
```
User visits MechaMap → Registers as Guest → Limited dashboard access
```

**Registration Flow:**
1. **Standard registration** form (same as Member)
2. **Role assignment:** Guest (Level 10)
3. **Welcome tutorial:** Explain Guest limitations
4. **Upgrade prompt:** Clear path to Member

### **Phase 2: Guest Experience**
```
Browse content → Follow interesting users/threads → Receive notifications → 
Want to participate → Upgrade to Member
```

**Daily Activities:**
- **Morning:** Check following feed for updates
- **Browse:** Explore forums and showcases
- **Follow:** Discover and follow interesting content
- **Evening:** Review notifications from followed content

### **Phase 3: Upgrade Incentive**
```
Guest tries restricted action → Upgrade prompt → Registration benefits → 
Convert to Member
```

**Conversion Triggers:**
- Clicking "Reply" on interesting thread
- Trying to create showcase
- Attempting to rate content
- Accessing marketplace
- Reaching follow limits

---

## 🎯 **BUSINESS BENEFITS**

### **1. User Acquisition:**
- ✅ **Lower barrier** to entry (Guest vs no account)
- ✅ **Content discovery** drives engagement
- ✅ **Social features** create connection
- ✅ **Clear upgrade path** with value proposition

### **2. Engagement Strategy:**
- ✅ **Following system** creates habit formation
- ✅ **Notifications** bring users back
- ✅ **FOMO effect** from restricted features
- ✅ **Community connection** without spam

### **3. Quality Control:**
- ✅ **No guest spam** in forums
- ✅ **Authenticated users** for all content
- ✅ **Gradual trust building** through observation
- ✅ **Reduced moderation** burden

### **4. Conversion Optimization:**
- ✅ **Multiple touchpoints** for upgrade prompts
- ✅ **Value demonstration** through limited access
- ✅ **Social proof** from followed members
- ✅ **Clear benefits** of Member upgrade

---

## 🛠️ **TECHNICAL IMPLEMENTATION**

### **Permission System:**
```php
// GuestPermissionService.php
public static function canPerform(string $action): bool
{
    $permissions = [
        'view-content' => true,
        'follow-users' => true,
        'create-threads' => false,
        'marketplace-access' => false,
    ];
    
    return $permissions[$action] ?? false;
}
```

### **Follow Limits:**
```php
$limits = [
    'users' => 50,      // Max 50 users to follow
    'threads' => 100,   // Max 100 threads to follow  
    'showcases' => 30,  // Max 30 showcases to follow
];
```

### **Upgrade Prompts:**
```php
public static function getUpgradeIncentive(string $action): string
{
    return match($action) {
        'create-threads' => 'Đăng ký thành Member để tạo chủ đề!',
        'create-comments' => 'Đăng ký thành Member để bình luận!',
        'marketplace-access' => 'Đăng ký thành Member để mua/bán!',
        default => 'Đăng ký thành Member để sử dụng tính năng này!'
    };
}
```

---

## 📱 **UI/UX DESIGN**

### **Guest Dashboard Features:**
1. **Following Feed** - Updates from followed users/threads
2. **Trending Content** - Popular content discovery
3. **Recommended Follows** - Suggested users to follow
4. **Upgrade Prompt** - Clear Member benefits

### **Upgrade Prompts:**
- **Modal popups** when trying restricted actions
- **Sidebar banners** highlighting Member benefits
- **Progress indicators** showing Guest limitations
- **Success stories** from upgraded Members

### **Follow Interface:**
- **Follow buttons** on user profiles and threads
- **Following count** with limits displayed
- **Unfollow management** in user dashboard
- **Notification preferences** for followed content

---

## 🧪 **A/B TESTING OPPORTUNITIES**

### **Test 1: Follow Limits**
- **A:** 50 users, 100 threads, 30 showcases
- **B:** 25 users, 50 threads, 15 showcases
- **Metric:** Conversion rate to Member

### **Test 2: Upgrade Prompts**
- **A:** Modal popups with benefits
- **B:** Inline messages with social proof
- **Metric:** Click-through rate to upgrade

### **Test 3: Guest Dashboard**
- **A:** Following feed prominent
- **B:** Trending content prominent
- **Metric:** Engagement and time on site

---

## 📊 **SUCCESS METRICS**

### **Engagement Metrics:**
- **Daily active guests** using follow features
- **Average follows per guest** (users, threads, showcases)
- **Notification open rates** from followed content
- **Time spent browsing** content

### **Conversion Metrics:**
- **Guest to Member conversion rate**
- **Time to conversion** (days as Guest before upgrade)
- **Conversion trigger analysis** (which restricted action prompted upgrade)
- **Retention rate** of converted Members

### **Quality Metrics:**
- **Content quality** (no guest spam)
- **Community health** (positive interactions)
- **Moderation workload** reduction
- **User satisfaction** scores

---

## 🎊 **CONCLUSION**

### **✅ Strategy Benefits:**
1. **Smart user acquisition** with lower barrier
2. **Quality content** protection from guest spam
3. **Strong conversion incentives** through limited access
4. **Social engagement** without contribution requirements
5. **Scalable permission system** for future growth

### **🎯 Expected Outcomes:**
- **Higher registration rates** (Guest vs no account)
- **Better conversion rates** (Guest to Member)
- **Improved content quality** (no guest posts)
- **Stronger community** (authenticated engagement)

### **🚀 Implementation Priority:**
1. **Phase 1:** Guest permission system ✅
2. **Phase 2:** Follow features and limits
3. **Phase 3:** Upgrade prompts and UI
4. **Phase 4:** Analytics and optimization

**🎊 GUEST STRATEGY V2:** ✅ **APPROVED** - Balanced approach providing value while creating strong upgrade incentives!
