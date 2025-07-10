# 🔍 Database & Models Audit Report - MechaMap

> **Ngày audit**: 24/06/2025  
> **Tổng số bảng**: 79 tables  
> **Tổng số models**: 60+ models  
> **Trạng thái**: Cần tạo seeders cho 45+ bảng

---

## 📊 **TỔNG QUAN DATABASE**

| Metric | Số lượng | Trạng thái |
|--------|----------|------------|
| **Total Tables** | **79** | ✅ Đầy đủ |
| **Tables có dữ liệu** | **22** | ✅ 28% |
| **Tables chưa có dữ liệu** | **57** | ❌ 72% |
| **Models** | **60+** | ✅ Đầy đủ |
| **Media files** | **116** | ✅ Hoàn thành |

---

## ✅ **BẢNG ĐÃ CÓ DỮ LIỆU (22 bảng)**

### **🔐 Authentication & Authorization**
| Bảng | Records | Seeder | Trạng thái |
|------|---------|--------|------------|
| `users` | **27** | MechaMapUserSeeder | ✅ Hoàn thành |
| `roles` | **5** | RolesAndPermissionsSeeder | ✅ Hoàn thành |
| `permissions` | **32** | RolesAndPermissionsSeeder | ✅ Hoàn thành |
| `role_has_permissions` | **82** | RolesAndPermissionsSeeder | ✅ Hoàn thành |
| `model_has_roles` | **14** | RolesAndPermissionsSeeder | ✅ Hoàn thành |

### **📂 Content Structure**
| Bảng | Records | Seeder | Trạng thái |
|------|---------|--------|------------|
| `categories` | **16** | MechaMapCategorySeeder | ✅ Hoàn thành |
| `tags` | **20** | MechanicalEngineeringDataSeeder | ✅ Hoàn thành |
| `media` | **116** | MediaSeeder | ✅ Hoàn thành |

### **⚙️ System Settings**
| Bảng | Records | Seeder | Trạng thái |
|------|---------|--------|------------|
| `settings` | **119** | SettingSeeder | ✅ Hoàn thành |
| `seo_settings` | **20** | SeoSettingSeeder | ✅ Hoàn thành |
| `page_seos` | **8** | PageSeoSeeder | ✅ Hoàn thành |

### **🛒 Marketplace (Partial)**
| Bảng | Records | Seeder | Trạng thái |
|------|---------|--------|------------|
| `product_categories` | **20** | MarketplaceSeeder | ✅ Hoàn thành |
| `technical_products` | **6** | MarketplaceSeeder | ✅ Hoàn thành |
| `protected_files` | **36** | MarketplaceSeeder | ✅ Hoàn thành |
| `orders` | **8** | MarketplaceSeeder | ✅ Hoàn thành |
| `order_items` | **9** | MarketplaceSeeder | ✅ Hoàn thành |
| `product_purchases` | **1** | MarketplaceSeeder | ✅ Hoàn thành |

### **🔧 System Tables**
| Bảng | Records | Seeder | Trạng thái |
|------|---------|--------|------------|
| `personal_access_tokens` | **34** | Auto-generated | ✅ System |
| `download_tokens` | **3** | Auto-generated | ✅ System |
| `cache` | **133** | Auto-generated | ✅ System |
| `sessions` | **4** | Auto-generated | ✅ System |
| `migrations` | **41** | Auto-generated | ✅ System |

---

## ❌ **BẢNG CHƯA CÓ DỮ LIỆU (57 bảng)**

### **🏆 PRIORITY 1: Core Forum Content**
| Bảng | Records | Cần Seeder | Mô tả |
|------|---------|------------|-------|
| `forums` | **0** | ❌ ForumSeeder | Forum categories structure |
| `threads` | **0** | ❌ ThreadSeeder | Discussion threads |
| `comments` | **0** | ❌ CommentSeeder | Thread comments |
| `posts` | **0** | ❌ PostSeeder | Forum posts |

### **🏆 PRIORITY 2: User Interactions**
| Bảng | Records | Cần Seeder | Mô tả |
|------|---------|------------|-------|
| `thread_likes` | **0** | ❌ ThreadInteractionSeeder | Thread likes |
| `thread_bookmarks` | **0** | ❌ ThreadInteractionSeeder | Thread bookmarks |
| `thread_follows` | **0** | ❌ ThreadInteractionSeeder | Thread follows |
| `thread_ratings` | **0** | ❌ ThreadInteractionSeeder | Thread ratings |
| `comment_likes` | **0** | ❌ CommentInteractionSeeder | Comment likes |
| `comment_dislikes` | **0** | ❌ CommentInteractionSeeder | Comment dislikes |
| `bookmarks` | **0** | ❌ BookmarkSeeder | General bookmarks |
| `followers` | **0** | ❌ FollowerSeeder | User follows |

### **🏆 PRIORITY 3: Showcase System**
| Bảng | Records | Cần Seeder | Mô tả |
|------|---------|------------|-------|
| `showcases` | **0** | ❌ ShowcaseSeeder | Project showcases |
| `showcase_comments` | **0** | ❌ ShowcaseInteractionSeeder | Showcase comments |
| `showcase_likes` | **0** | ❌ ShowcaseInteractionSeeder | Showcase likes |
| `showcase_follows` | **0** | ❌ ShowcaseInteractionSeeder | Showcase follows |

### **🔄 PRIORITY 4: Content Management**
| Bảng | Records | Cần Seeder | Mô tả |
|------|---------|------------|-------|
| `pages` | **0** | ❌ PageSeeder | Static pages |
| `content_blocks` | **0** | ❌ ContentSeeder | Content blocks |
| `content_categories` | **0** | ❌ ContentSeeder | Content categories |
| `faqs` | **0** | ❌ FaqSeeder | FAQ entries |
| `faq_categories` | **0** | ❌ FaqSeeder | FAQ categories |
| `knowledge_articles` | **0** | ❌ KnowledgeSeeder | Knowledge base |

### **💬 PRIORITY 5: Messaging System**
| Bảng | Records | Cần Seeder | Mô tả |
|------|---------|------------|-------|
| `conversations` | **0** | ❌ MessagingSeeder | Private conversations |
| `conversation_participants` | **0** | ❌ MessagingSeeder | Conversation participants |
| `messages` | **0** | ❌ MessagingSeeder | Private messages |
| `messaging` | **0** | ❌ MessagingSeeder | Messaging system |

### **📊 PRIORITY 6: Polls & Surveys**
| Bảng | Records | Cần Seeder | Mô tả |
|------|---------|------------|-------|
| `polls` | **0** | ❌ PollSeeder | Forum polls |
| `poll_options` | **0** | ❌ PollSeeder | Poll options |
| `poll_votes` | **0** | ❌ PollSeeder | Poll votes |

### **👥 PRIORITY 7: Social Features**
| Bảng | Records | Cần Seeder | Mô tả |
|------|---------|------------|-------|
| `social_accounts` | **0** | ❌ SocialSeeder | Social media accounts |
| `social_interactions` | **0** | ❌ SocialSeeder | Social interactions |
| `user_activities` | **0** | ❌ ActivitySeeder | User activity logs |
| `user_visits` | **0** | ❌ ActivitySeeder | User visit tracking |
| `profile_posts` | **0** | ❌ ProfileSeeder | Profile posts |

### **🛒 PRIORITY 8: Extended Marketplace**
| Bảng | Records | Cần Seeder | Mô tả |
|------|---------|------------|-------|
| `shopping_carts` | **0** | ❌ MarketplaceExtendedSeeder | Shopping carts |
| `seller_earnings` | **0** | ❌ MarketplaceExtendedSeeder | Seller earnings |
| `seller_payouts` | **0** | ❌ MarketplaceExtendedSeeder | Seller payouts |
| `user_payment_methods` | **0** | ❌ MarketplaceExtendedSeeder | Payment methods |
| `payment_transactions` | **0** | ❌ MarketplaceExtendedSeeder | Payment transactions |
| `secure_downloads` | **0** | ❌ MarketplaceExtendedSeeder | Secure downloads |

### **🔧 PRIORITY 9: System & Admin**
| Bảng | Records | Cần Seeder | Mô tả |
|------|---------|------------|-------|
| `alerts` | **0** | ❌ SystemSeeder | System alerts |
| `reports` | **0** | ❌ SystemSeeder | User reports |
| `search_logs` | **0** | ❌ SystemSeeder | Search logs |
| `reactions` | **0** | ❌ SystemSeeder | Emoji reactions |
| `subscriptions` | **0** | ❌ SystemSeeder | Subscriptions |

### **🔗 PRIORITY 10: Pivot Tables**
| Bảng | Records | Cần Seeder | Mô tả |
|------|---------|------------|-------|
| `thread_tag` | **0** | ❌ Auto-populated | Thread-Tag pivot |
| `page_categories` | **0** | ❌ Auto-populated | Page-Category pivot |
| `content_revisions` | **0** | ❌ Auto-populated | Content revisions |

---

## 📸 **MEDIA FILES AUDIT**

### **✅ Files được sử dụng trong Database (116 records)**
- `/images/category-forum/` - **9 files** (category icons)
- `/images/users/` - **27 files** (user avatars, cycled from 10 unique)
- `/images/showcase/` - **25 files** (showcase images)
- `/images/threads/` - **31 files** (thread images)
- `/images/setting/` - **3 files** (site assets)
- **Demo images** - **21 files** (gallery, demo showcase, demo threads)

### **🗑️ Files không được sử dụng (cần cleanup)**
- `public/images/avata.jpg` - Typo, không dùng
- `public/images/city-illustration.svg` - Không dùng
- `public/images/favicon.svg` - Duplicate với setting/favicon.png
- `public/images/hero-bg.svg` - Không dùng
- `public/images/logo.svg` - Duplicate với setting/logo.png
- `public/images/no-image.svg` - Có thể giữ làm placeholder
- `public/images/placeholder.svg` - Có thể giữ làm placeholder
- `public/images/post.jpg` - Không dùng
- `public/images/settings/*` - Files cũ, không dùng (12 files)
- `public/images/placeholders/*` - Có thể giữ (6 files)

### **📊 Cleanup Summary**
- **Total files**: ~90 files
- **Used files**: 116 database records (some files used multiple times)
- **Unused files**: ~20 files cần xóa
- **Keep files**: ~6 placeholder files

---

## 🎯 **SEEDER CREATION PLAN**

### **Phase 1: Core Content (Week 1)**
1. **ForumSeeder** - Tạo forum structure
2. **ThreadSeeder** - Tạo discussion threads với nội dung cơ khí
3. **CommentSeeder** - Tạo comments cho threads
4. **PostSeeder** - Tạo forum posts

### **Phase 2: Interactions (Week 2)**
5. **ThreadInteractionSeeder** - Likes, bookmarks, follows, ratings
6. **CommentInteractionSeeder** - Comment likes/dislikes
7. **BookmarkSeeder** - General bookmarks
8. **FollowerSeeder** - User follows

### **Phase 3: Extended Features (Week 3)**
9. **ShowcaseSeeder** - Project showcases
10. **ShowcaseInteractionSeeder** - Showcase interactions
11. **MessagingSeeder** - Private messaging
12. **PollSeeder** - Forum polls

### **Phase 4: Content & Social (Week 4)**
13. **PageSeeder** - Static pages
14. **ContentSeeder** - Content management
15. **FaqSeeder** - FAQ system
16. **SocialSeeder** - Social features
17. **ActivitySeeder** - User activities

### **Phase 5: System & Marketplace (Week 5)**
18. **MarketplaceExtendedSeeder** - Extended marketplace
19. **SystemSeeder** - System features
20. **KnowledgeSeeder** - Knowledge base

---

## 📋 **NEXT STEPS**

1. **Media cleanup** - Xóa unused files
2. **Tạo seeders theo priority** - Bắt đầu với Phase 1
3. **Web search content** - Lấy nội dung chuyên ngành từ internet
4. **Test relationships** - Verify foreign keys
5. **Performance testing** - Test với large dataset

**Estimated completion time: 5 weeks**
