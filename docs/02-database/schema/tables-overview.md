# 📋 Database Tables Overview

**Database**: MechaMap Production  
**Tables Count**: 43 tables  
**Last Updated**: June 12, 2025

---

## 🗂️ **TABLES BY CATEGORY**

### 👤 **User Management (8 tables)**
| Table | Purpose | Key Fields |
|-------|---------|------------|
| `users` | Main user accounts | id, username, email, role, status |
| `user_profiles` | Extended user info | user_id, bio, profession, skills |
| `roles` | System roles | id, name, guard_name |
| `permissions` | System permissions | id, name, guard_name |
| `role_has_permissions` | Role-permission mapping | role_id, permission_id |
| `model_has_permissions` | Direct user permissions | model_id, permission_id |
| `model_has_roles` | User-role assignments | model_id, role_id |
| `password_resets` | Password reset tokens | email, token, created_at |

### 📝 **Forum System (12 tables)**
| Table | Purpose | Key Fields |
|-------|---------|------------|
| `categories` | Forum categories | id, name, slug, parent_id |
| `forums` | Forum sections | id, name, category_id, description |
| `threads` | Discussion threads | id, title, content, user_id, forum_id |
| `posts` | Thread replies | id, content, thread_id, user_id |
| `comments` | Post comments | id, content, post_id, user_id |
| `thread_bookmarks` | User bookmarks | id, user_id, thread_id, folder |
| `thread_ratings` | Thread ratings | id, user_id, thread_id, rating |
| `thread_tags` | Thread tagging | id, thread_id, tag_name |
| `thread_attachments` | File attachments | id, thread_id, filename, path |
| `thread_saves` | Saved threads | id, user_id, thread_id |
| `reports` | Content reports | id, user_id, reportable_type, reason |
| `alerts` | User notifications | id, user_id, type, data, read_at |

### 🛒 **E-commerce System (9 tables)**
| Table | Purpose | Key Fields |
|-------|---------|------------|
| `products` | Digital products | id, name, price, category_id, file_path |
| `product_categories` | Product categorization | id, name, slug, parent_id |
| `shopping_carts` | User shopping carts | id, user_id, session_id |
| `cart_items` | Cart item details | id, cart_id, product_id, quantity |
| `orders` | Purchase orders | id, user_id, order_number, total_amount |
| `order_items` | Order item details | id, order_id, product_id, price |
| `payments` | Payment transactions | id, order_id, amount, method, status |
| `secure_downloads` | Download tracking | id, user_id, product_id, token, expires_at |
| `download_logs` | Download audit trail | id, download_id, ip_address, user_agent |

### 📄 **Content Management (8 tables)**
| Table | Purpose | Key Fields |
|-------|---------|------------|
| `pages` | CMS pages | id, title, content, slug, status |
| `page_categories` | Page categorization | id, name, slug |
| `media` | File management | id, filename, path, mime_type, size |
| `showcases` | User portfolios | id, user_id, title, description, images |
| `showcase_categories` | Showcase grouping | id, name, description |
| `faqs` | FAQ system | id, question, answer, category_id |
| `faq_categories` | FAQ categorization | id, name, description |
| `settings` | System settings | id, key, value, type, group |

### 🌍 **Geographic & Utility (6 tables)**
| Table | Purpose | Key Fields |
|-------|---------|------------|
| `countries` | Country list | id, name, code, flag |
| `regions` | Regional data | id, country_id, name, code |
| `failed_jobs` | Queue failures | id, connection, queue, payload |
| `jobs` | Job queue | id, queue, payload, attempts |
| `sessions` | User sessions | id, user_id, ip_address, last_activity |
| `personal_access_tokens` | API tokens | id, tokenable_id, name, token, abilities |

---

## 🔗 **CRITICAL RELATIONSHIPS**

### **Primary Entity Relationships**
```
Users (1:∞) Threads (1:∞) Posts (1:∞) Comments
Users (1:∞) Orders (1:∞) OrderItems (∞:1) Products
Users (1:∞) Bookmarks (∞:1) Threads
Users (1:∞) Ratings (∞:1) Threads
Categories (1:∞) Forums (1:∞) Threads
ProductCategories (1:∞) Products
```

### **Permission System Relationships**
```
Users (∞:∞) Roles [model_has_roles]
Roles (∞:∞) Permissions [role_has_permissions]
Users (∞:∞) Permissions [model_has_permissions]
```

---

## 📊 **TABLE STATISTICS**

### **Large Tables (Expected High Volume)**
| Table | Est. Records | Growth Rate | Critical Indexes |
|-------|-------------|-------------|------------------|
| `threads` | 10K-100K+ | High | user_id, forum_id, last_activity_at |
| `posts` | 50K-500K+ | Very High | thread_id, user_id, created_at |
| `comments` | 100K-1M+ | Very High | post_id, user_id, created_at |
| `download_logs` | 10K-100K+ | High | download_id, created_at |
| `sessions` | 1K-10K | Medium | user_id, last_activity |

### **Medium Tables (Moderate Volume)**
| Table | Est. Records | Growth Rate | Critical Indexes |
|-------|-------------|-------------|------------------|
| `users` | 1K-50K | Medium | username, email, role |
| `products` | 100-5K | Low-Medium | category_id, status |
| `orders` | 1K-20K | Medium | user_id, created_at |
| `thread_bookmarks` | 5K-50K | Medium | user_id, thread_id |
| `alerts` | 10K-100K | High | user_id, read_at |

### **Reference Tables (Low Volume)**
| Table | Est. Records | Growth Rate | Purpose |
|-------|-------------|-------------|---------|
| `categories` | 10-50 | Very Low | Forum structure |
| `product_categories` | 5-20 | Very Low | Product taxonomy |
| `settings` | 50-200 | Very Low | System config |
| `countries` | 200+ | Static | Geographic data |
| `roles` | 5-10 | Very Low | Access control |

---

## 🔍 **PERFORMANCE CONSIDERATIONS**

### **Critical Indexes**
```sql
-- User authentication & lookups
INDEX users_username_index (username)
INDEX users_email_index (email)

-- Forum performance
INDEX threads_forum_activity (forum_id, last_activity_at)
INDEX posts_thread_created (thread_id, created_at)
INDEX comments_post_created (post_id, created_at)

-- E-commerce queries
INDEX orders_user_date (user_id, created_at)
INDEX products_category_status (category_id, status)

-- Security & downloads
INDEX secure_downloads_user_product (user_id, product_id)
INDEX download_logs_download_date (download_id, created_at)
```

### **Full-Text Search Indexes**
```sql
FULLTEXT threads_search (title, content)
FULLTEXT products_search (name, description)
FULLTEXT pages_search (title, content)
```

---

## 💾 **STORAGE REQUIREMENTS**

### **Estimated Storage by Category**
| Category | Tables | Est. Size | Growth/Month |
|----------|--------|-----------|--------------|
| Forum System | 12 | 500MB-5GB | 200-500MB |
| User Management | 8 | 100MB-1GB | 50-100MB |
| E-commerce | 9 | 200MB-2GB | 100-200MB |
| Content Management | 8 | 100MB-1GB | 20-50MB |
| Utility Tables | 6 | 50MB-500MB | 10-50MB |
| **TOTAL** | **43** | **1GB-10GB** | **380-900MB** |

### **Storage Optimization**
- **Archive old data**: Threads/posts older than 2 years
- **Compress attachments**: Images and files
- **Clean sessions**: Remove expired sessions weekly
- **Purge logs**: Download logs older than 6 months

---

## 🔄 **MIGRATION DEPENDENCIES**

### **Migration Order (Critical)**
```bash
1. countries, regions           # Geographic base data
2. users                       # Core user system
3. roles, permissions          # Permission system
4. model_has_roles, etc.       # Permission relationships
5. categories, forums          # Forum structure
6. product_categories          # E-commerce structure
7. threads, posts, comments    # Forum content
8. products, orders            # E-commerce data
9. All other supporting tables
```

### **Seed Data Requirements**
```bash
# Essential seeds (Production)
- RolesAndPermissionsSeeder    # Access control
- CountrySeeder               # Geographic data
- CategorySeeder              # Forum categories
- ProductCategorySeeder       # Product categories
- SettingsSeeder              # System settings

# Optional seeds (Development/Demo)
- UserSeeder                  # Test users
- ThreadSeeder                # Sample content
- ProductSeeder               # Demo products
```

---

## 🔗 **Quick Navigation**

- [Complete Schema SQL](./schema/complete-schema.sql)
- [Migration Guide](./migrations/migration-guide.md)
- [Relationship Diagram](./schema/relationships.md)
- [Performance Indexes](./schema/indexes.md)
- [Backup Procedures](./backup-restore/backup-guide.md)

---

**📊 Total Tables**: 43  
**🔗 Total Relationships**: 65+  
**📈 Estimated Production Size**: 1-10GB  
**⚡ Performance**: Optimized for 10K+ concurrent users
