# 🗄️ MechaMap Database Documentation

**Last Updated**: June 12, 2025  
**Database Version**: MySQL 8.0+  
**Laravel Version**: 10.x  

---

## 📋 **DATABASE OVERVIEW**

MechaMap sử dụng MySQL database với Laravel Eloquent ORM để quản lý dữ liệu cho hệ thống forum cộng đồng kỹ thuật cơ khí và marketplace.

### **🎯 Database Purpose**
- **Forum System**: Quản lý threads, posts, comments, categories
- **User Management**: Users, roles, permissions, profiles
- **E-commerce**: Products, orders, payments, downloads
- **Content Management**: Pages, media, showcases
- **Security**: Anti-piracy, access control, audit logs

---

## 📊 **DATABASE SCHEMA OVERVIEW**

### **Core Tables Structure**
```
📁 User Management (8 tables)
├── users                 # User accounts & profiles
├── user_profiles         # Extended user information
├── roles                 # User roles (admin, moderator, etc.)
├── permissions           # System permissions
├── role_has_permissions  # Role-permission mapping
├── model_has_permissions # Direct user permissions
├── model_has_roles       # User-role assignments
└── password_resets       # Password reset tokens

📁 Forum System (12 tables)
├── categories            # Forum categories
├── forums                # Forum sections
├── threads               # Discussion threads
├── posts                 # Thread posts/replies
├── comments              # Post comments
├── thread_bookmarks      # User bookmarks
├── thread_ratings        # Thread ratings
├── thread_tags           # Thread tagging
├── thread_attachments    # File attachments
├── thread_saves          # Saved threads
├── reports               # Content reports
└── alerts                # User notifications

📁 E-commerce System (9 tables)
├── products              # Digital products
├── product_categories    # Product categorization
├── shopping_carts        # User shopping carts
├── cart_items            # Cart item details
├── orders                # Purchase orders
├── order_items           # Order item details
├── payments              # Payment transactions
├── secure_downloads      # Download tracking
└── download_logs         # Download audit trail

📁 Content Management (8 tables)
├── pages                 # CMS pages
├── page_categories       # Page categorization
├── media                 # File management
├── showcases             # User portfolios
├── showcase_categories   # Showcase grouping
├── faqs                  # FAQ system
├── faq_categories        # FAQ categorization
└── settings              # System settings

📁 Geographic & Utility (6 tables)
├── countries             # Country list
├── regions               # Regional data
├── failed_jobs           # Queue failures
├── jobs                  # Job queue
├── sessions              # User sessions
└── personal_access_tokens # API tokens
```

### **📈 Key Statistics**
- **Total Tables**: 43 tables
- **Primary Entities**: Users, Threads, Products, Orders
- **Relationship Types**: One-to-Many, Many-to-Many, Polymorphic
- **Storage Engine**: InnoDB (ACID compliance)
- **Character Set**: utf8mb4_unicode_ci (full Unicode support)

---

## 🔗 **TABLE RELATIONSHIPS**

### **🧑‍🤝‍🧑 User System Relationships**
```sql
users (1) ──── (∞) threads
users (1) ──── (∞) posts  
users (1) ──── (∞) comments
users (1) ──── (∞) thread_bookmarks
users (1) ──── (∞) thread_ratings
users (1) ──── (∞) orders
users (1) ──── (∞) shopping_carts
users (∞) ──── (∞) roles [model_has_roles]
users (∞) ──── (∞) permissions [model_has_permissions]
```

### **📋 Forum System Relationships**
```sql
categories (1) ──── (∞) forums
forums (1) ──── (∞) threads
threads (1) ──── (∞) posts
posts (1) ──── (∞) comments
threads (∞) ──── (∞) tags [thread_tags]
threads (1) ──── (∞) attachments
threads (1) ──── (∞) bookmarks
threads (1) ──── (∞) ratings
```

### **🛒 E-commerce Relationships**
```sql
product_categories (1) ──── (∞) products
users (1) ──── (∞) shopping_carts
shopping_carts (1) ──── (∞) cart_items
products (1) ──── (∞) cart_items
users (1) ──── (∞) orders
orders (1) ──── (∞) order_items
products (1) ──── (∞) order_items
orders (1) ──── (∞) payments
orders (1) ──── (∞) secure_downloads
```

### **📄 Content Management Relationships**
```sql
page_categories (1) ──── (∞) pages
showcase_categories (1) ──── (∞) showcases
users (1) ──── (∞) showcases
faq_categories (1) ──── (∞) faqs
```

---

## 🔑 **CRITICAL TABLES DETAILED**

### **👤 users**
**Purpose**: Core user account management
```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    username VARCHAR(255) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','moderator','senior','member','guest') DEFAULT 'member',
    status ENUM('active','inactive','banned') DEFAULT 'active',
    avatar VARCHAR(255) NULL,
    about_me TEXT NULL,
    website VARCHAR(255) NULL,
    location VARCHAR(255) NULL,
    signature TEXT NULL,
    points INT DEFAULT 0,
    last_seen_at TIMESTAMP NULL,
    banned_at TIMESTAMP NULL,
    banned_by BIGINT UNSIGNED NULL,
    ban_reason TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_status (status),
    INDEX idx_last_seen (last_seen_at),
    FOREIGN KEY (banned_by) REFERENCES users(id)
);
```

### **📝 threads**
**Purpose**: Forum discussion threads
```sql
CREATE TABLE threads (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content LONGTEXT NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    forum_id BIGINT UNSIGNED NOT NULL,
    status ENUM('published','draft','archived') DEFAULT 'published',
    thread_type ENUM('discussion','question','tutorial','showcase') DEFAULT 'discussion',
    is_pinned BOOLEAN DEFAULT FALSE,
    is_locked BOOLEAN DEFAULT FALSE,
    is_featured BOOLEAN DEFAULT FALSE,
    view_count INT DEFAULT 0,
    reply_count INT DEFAULT 0,
    like_count INT DEFAULT 0,
    bookmark_count INT DEFAULT 0,
    average_rating DECIMAL(3,2) DEFAULT 0.00,
    ratings_count INT DEFAULT 0,
    quality_score INT DEFAULT 0,
    last_activity_at TIMESTAMP NULL,
    moderation_status ENUM('pending','approved','rejected','flagged') DEFAULT 'approved',
    moderated_by BIGINT UNSIGNED NULL,
    moderated_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_user_id (user_id),
    INDEX idx_forum_id (forum_id),
    INDEX idx_status (status),
    INDEX idx_type (thread_type),
    INDEX idx_pinned (is_pinned),
    INDEX idx_activity (last_activity_at),
    INDEX idx_moderation (moderation_status),
    FULLTEXT idx_search (title, content),
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (forum_id) REFERENCES forums(id) ON DELETE CASCADE,
    FOREIGN KEY (moderated_by) REFERENCES users(id)
);
```

### **🛍️ products**
**Purpose**: Digital product catalog
```sql
CREATE TABLE products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description LONGTEXT NOT NULL,
    short_description TEXT NULL,
    category_id BIGINT UNSIGNED NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    sale_price DECIMAL(10,2) NULL,
    sku VARCHAR(100) UNIQUE NULL,
    status ENUM('active','inactive','draft') DEFAULT 'active',
    type ENUM('digital','physical','service') DEFAULT 'digital',
    file_path VARCHAR(500) NULL,
    file_size BIGINT NULL,
    download_limit INT DEFAULT 3,
    download_expiry_hours INT DEFAULT 168,
    preview_url VARCHAR(500) NULL,
    thumbnail VARCHAR(500) NULL,
    tags JSON NULL,
    metadata JSON NULL,
    view_count INT DEFAULT 0,
    purchase_count INT DEFAULT 0,
    average_rating DECIMAL(3,2) DEFAULT 0.00,
    ratings_count INT DEFAULT 0,
    is_featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_category_id (category_id),
    INDEX idx_status (status),
    INDEX idx_type (type),
    INDEX idx_price (price),
    INDEX idx_featured (is_featured),
    FULLTEXT idx_search (name, description),
    
    FOREIGN KEY (category_id) REFERENCES product_categories(id)
);
```

### **📦 orders**
**Purpose**: Purchase order management
```sql
CREATE TABLE orders (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    status ENUM('pending','processing','completed','cancelled','refunded') DEFAULT 'pending',
    total_amount DECIMAL(10,2) NOT NULL,
    tax_amount DECIMAL(10,2) DEFAULT 0.00,
    discount_amount DECIMAL(10,2) DEFAULT 0.00,
    currency VARCHAR(3) DEFAULT 'VND',
    payment_method ENUM('stripe','vnpay','paypal') NULL,
    payment_status ENUM('pending','paid','failed','refunded') DEFAULT 'pending',
    payment_id VARCHAR(255) NULL,
    notes TEXT NULL,
    billing_info JSON NULL,
    completed_at TIMESTAMP NULL,
    cancelled_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_user_id (user_id),
    INDEX idx_order_number (order_number),
    INDEX idx_status (status),
    INDEX idx_payment_status (payment_status),
    INDEX idx_created_at (created_at),
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

---

## 🔒 **SECURITY & PERMISSIONS**

### **🛡️ Permission System Tables**

#### **roles**
```sql
CREATE TABLE roles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(125) NOT NULL,
    guard_name VARCHAR(125) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    UNIQUE KEY roles_name_guard_name_unique (name, guard_name)
);
```

#### **permissions**  
```sql
CREATE TABLE permissions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(125) NOT NULL,
    guard_name VARCHAR(125) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    UNIQUE KEY permissions_name_guard_name_unique (name, guard_name)
);
```

### **🔐 Security Features**
- **Password Hashing**: bcrypt with cost factor 12
- **API Authentication**: Laravel Sanctum tokens
- **Role-Based Access**: Spatie Permission package
- **Download Protection**: Secure download tokens
- **Anti-Piracy**: Device fingerprinting, download limits
- **Audit Trail**: Download logs, payment tracking

---

## 📊 **INDEXES & PERFORMANCE**

### **🚀 Critical Indexes**
```sql
-- User lookups
INDEX idx_users_username (username)
INDEX idx_users_email (email)
INDEX idx_users_role (role)

-- Thread performance
INDEX idx_threads_forum_activity (forum_id, last_activity_at)
INDEX idx_threads_user_created (user_id, created_at)
FULLTEXT idx_threads_search (title, content)

-- Product searches
INDEX idx_products_category_status (category_id, status)
INDEX idx_products_price_range (price, status)
FULLTEXT idx_products_search (name, description)

-- Order tracking
INDEX idx_orders_user_created (user_id, created_at)
INDEX idx_orders_status_date (status, created_at)

-- Performance optimization
INDEX idx_secure_downloads_tracking (user_id, product_id, created_at)
```

### **⚡ Query Optimization**
- **Eager Loading**: Relations pre-loaded to avoid N+1 queries
- **Pagination**: Efficient pagination with indexed sorting
- **Caching**: Query result caching for static data
- **Full-Text Search**: MySQL full-text indexes for content search

---

## 🗂️ **DATA TYPES & CONSTRAINTS**

### **📏 Field Standards**
```sql
-- Standard field sizes
VARCHAR(255)     # Names, titles, URLs
VARCHAR(500)     # Long URLs, file paths  
TEXT             # Short descriptions, comments
LONGTEXT         # Long content, rich text
JSON             # Structured metadata
DECIMAL(10,2)    # Currency amounts
BIGINT UNSIGNED  # Primary keys, foreign keys
TIMESTAMP        # Date/time fields
BOOLEAN          # True/false flags
ENUM             # Fixed value sets
```

### **🔒 Constraints & Validation**
```sql
-- Required constraints
NOT NULL         # Essential fields
UNIQUE           # Unique identifiers
PRIMARY KEY      # Table primary keys
FOREIGN KEY      # Referential integrity

-- Default values
DEFAULT 'active' # Status fields
DEFAULT 0        # Counters
DEFAULT FALSE    # Boolean flags
DEFAULT CURRENT_TIMESTAMP # Timestamps
```

---

## 🔄 **DATABASE OPERATIONS**

### **🚀 Quick Setup Commands**
```bash
# Create database
CREATE DATABASE mechamap_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Run migrations
php artisan migrate

# Seed essential data
php artisan db:seed --class=RolesAndPermissionsSeeder
php artisan db:seed --class=CategorySeeder
php artisan db:seed --class=ProductCategorySeeder

# Generate application key
php artisan key:generate

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### **💾 Backup Procedures**
```bash
# Full database backup
mysqldump -u username -p mechamap_production > backup_$(date +%Y%m%d_%H%M%S).sql

# Restore from backup
mysql -u username -p mechamap_production < backup_file.sql

# Automated backup script (add to crontab)
0 2 * * * /path/to/backup_script.sh
```

---

## 📋 **MAINTENANCE TASKS**

### **🧹 Regular Maintenance**
```sql
-- Clean old sessions (weekly)
DELETE FROM sessions WHERE last_activity < UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 30 DAY));

-- Clean expired download tokens (daily)
DELETE FROM secure_downloads WHERE expires_at < NOW();

-- Update thread statistics (hourly)
UPDATE threads SET reply_count = (SELECT COUNT(*) FROM posts WHERE thread_id = threads.id);

-- Clean failed jobs (weekly)
DELETE FROM failed_jobs WHERE failed_at < DATE_SUB(NOW(), INTERVAL 7 DAY);
```

### **📊 Performance Monitoring**
```sql
-- Check table sizes
SELECT 
    table_name AS 'Table',
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size (MB)'
FROM information_schema.tables 
WHERE table_schema = 'mechamap_production'
ORDER BY (data_length + index_length) DESC;

-- Monitor slow queries
SHOW PROCESSLIST;
SHOW FULL PROCESSLIST;

-- Index usage analysis
SHOW INDEX FROM threads;
EXPLAIN SELECT * FROM threads WHERE forum_id = 1 ORDER BY last_activity_at DESC;
```

---

## 🔗 **Related Documentation**

- [Database Migration Guide](./migrations/migration-guide.md)
- [Backup & Restore Procedures](./backup-restore/backup-guide.md)
- [Performance Optimization](../10-maintenance/performance-monitoring.md)
- [Security Configuration](../08-deployment/security-setup.md)

---

**📅 Last Updated**: June 12, 2025  
**✅ Status**: Production Ready  
**🔗 Schema Version**: 1.0.0
