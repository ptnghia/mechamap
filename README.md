# 🔧 MechaMap - Cộng Đồng Cơ Khí Việt Nam

<div align="center">

![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/Tailwind%20CSS-3.4-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)

**Nền tảng diễn đàn kỹ thuật hàng đầu dành cho cộng đồng cơ khí, tự động hóa và công nghệ Việt Nam**

[📖 Tài liệu](#-tài-liệu) • [🚀 Cài đặt](#-cài-đặt-nhanh) • [🏗️ Triển khai](#-triển-khai) • [🤝 Đóng góp](#-đóng-góp)

</div>

---

## 🌟 Giới Thiệu

**MechaMap** là một diễn đàn trực tuyến chuyên nghiệp được thiết kế dành riêng cho cộng đồng kỹ sư cơ khí, tự động hóa và các chuyên gia công nghệ tại Việt Nam. Nền tảng này kết nối các kỹ sư, sinh viên, và chuyên gia, tạo ra một không gian học hỏi và chia sẻ kiến thức chuyên sâu.

### ✨ Tính Năng Nổi Bật

#### 🔐 **Hệ Thống Xác Thực & Bảo Mật**
- **Multi-Auth System** - Email, Google, Facebook với xác thực 2 lớp (2FA)
- **Advanced Security** - Rate limiting, IP whitelist, CSRF protection, XSS filtering
- **Session Management** - Secure session handling với Redis backend
- **Password Security** - Bcrypt hashing, password strength validation

#### 👥 **Mô Hình Phân Quyền 8 Cấp Độ**
- **System Management** - Super Admin, System Admin, Content Admin
- **Community Management** - Content Moderator, Marketplace Moderator, Community Moderator
- **Community Members** - Senior Member, Member, Guest, Student
- **Business Partners** - Manufacturer, Supplier, Brand, Verified Partner

#### 🏪 **Marketplace B2B2C**
- **Multi-Vendor Platform** - Suppliers, Manufacturers, Brands
- **Product Management** - CAD files, technical drawings, physical products
- **Payment Integration** - Multiple payment gateways, escrow system
- **Revenue Streams** - Commission-based, subscription fees, advertising

#### 💬 **Diễn Đàn & Cộng Đồng**
- **Advanced Forum System** - Categories, threads, polls, reactions
- **Real-time Chat** - Private messaging, group chats, notifications
- **Content Management** - Rich text editor, file attachments, media gallery
- **Moderation Tools** - Auto-moderation, report system, content filtering

#### 🎨 **Giao Diện & UX**
- **Responsive Design** - Mobile-first approach với Bootstrap 5
- **Dark/Light Mode** - Theme switcher với user preferences
- **Accessibility** - WCAG 2.1 compliant, screen reader support
- **Performance** - Lazy loading, image optimization, CDN integration

#### 🌐 **Đa Ngôn Ngữ & Localization**
- **Multi-Language Support** - Tiếng Việt, English với 200+ translation keys
- **Dynamic Language Switching** - Session-based language persistence
- **RTL Support** - Right-to-left language support ready
- **Timezone Management** - Auto-detect user timezone

#### ⚙️ **Admin Dashboard**
- **Comprehensive Management** - 11/16 modules hoàn thành (68.75%)
- **Real-time Analytics** - User activity, content statistics, revenue tracking
- **System Monitoring** - Performance metrics, error tracking, health checks
- **Bulk Operations** - Mass user management, content moderation

#### 📊 **Analytics & SEO**
- **SEO Optimization** - Meta tags, sitemap, structured data
- **Performance Tracking** - Page speed, user engagement, conversion rates
- **Business Intelligence** - Revenue analytics, user behavior analysis
- **Reporting System** - Automated reports, custom dashboards

## 🏛️ Kiến Trúc Hệ Thống

### 📋 Mô Hình Phân Quyền Chi Tiết

#### 🏢 **System Management (Quản Lý Hệ Thống)**

| Role | Permissions | Description |
|------|-------------|-------------|
| **👑 Super Admin** | Full System Access | Toàn quyền hệ thống, server management, database access |
| **🔧 System Admin** | System Configuration | Cấu hình hệ thống, user management, security settings |
| **📝 Content Admin** | Content Management | Quản lý toàn bộ nội dung, moderation, SEO settings |

#### 🛡️ **Community Management (Quản Lý Cộng Đồng)**

| Role | Permissions | Description |
|------|-------------|-------------|
| **📋 Content Moderator** | Content Moderation | Kiểm duyệt bài viết, comments, media uploads |
| **🏪 Marketplace Moderator** | Marketplace Management | Quản lý sản phẩm, sellers, transactions |
| **👥 Community Moderator** | User Management | Quản lý thành viên, warnings, bans |

#### 👤 **Community Members (Thành Viên Cộng Đồng)**

| Role | Permissions | Description |
|------|-------------|-------------|
| **⭐ Senior Member** | Advanced Features | Full forum access, private messaging, file uploads |
| **👤 Member** | Basic Features | Post threads, comments, basic messaging |
| **👁️ Guest** | Read Only | Xem nội dung công khai, không tương tác |
| **🎓 Student** | Educational Access | Student-specific features, learning resources |

#### 🏭 **Business Partners (Đối Tác Kinh Doanh)**

| Role | Permissions | Description |
|------|-------------|-------------|
| **🏭 Manufacturer** | Technical Sales | Bán thông tin kỹ thuật, designs, technical data |
| **📦 Supplier** | Product Sales | Bán sản phẩm vật lý, parts, materials |
| **🏷️ Brand** | Marketing Access | View-only access, promotion, brand visibility |
| **✅ Verified Partner** | Premium Features | Enhanced business tools, priority support |

#### 🔐 **Permission Matrix**

| Feature | Guest | Member | Senior | Moderator | Admin |
|---------|-------|--------|--------|-----------|-------|
| View Public Content | ✅ | ✅ | ✅ | ✅ | ✅ |
| Create Threads | ❌ | ✅ | ✅ | ✅ | ✅ |
| Upload Files | ❌ | ❌ | ✅ | ✅ | ✅ |
| Private Messaging | ❌ | ✅ | ✅ | ✅ | ✅ |
| Moderate Content | ❌ | ❌ | ❌ | ✅ | ✅ |
| User Management | ❌ | ❌ | ❌ | ✅ | ✅ |
| System Settings | ❌ | ❌ | ❌ | ❌ | ✅ |
| Marketplace Access | ❌ | ✅ | ✅ | ✅ | ✅ |
| Business Tools | ❌ | ❌ | ❌ | ✅* | ✅ |

*Business roles only

### 🔑 Hệ Thống Xác Thực & Bảo Mật

#### 🔐 **Authentication Methods**
- **📧 Email Authentication** - Traditional email/password với email verification
- **🌐 Social Login** - Google OAuth 2.0, Facebook Login với auto-account linking
- **🔒 Two-Factor Authentication** - TOTP với Google Authenticator, Authy support
- **🔗 Account Linking** - Multiple login methods per user account
- **🎫 Remember Me** - Secure persistent login với encrypted tokens

#### 🛡️ **Security Features**
- **🚫 Rate Limiting** - API rate limiting, login attempt protection
- **🌐 IP Whitelisting** - Admin IP restrictions, geo-blocking support
- **🔍 Content Filtering** - XSS protection, SQL injection prevention
- **📊 Audit Logging** - Comprehensive activity logs, security event tracking
- **🔐 Session Security** - Secure session management, automatic timeout
- **🛡️ CSRF Protection** - Cross-site request forgery protection
- **🔒 Password Security** - Strong password requirements, breach detection

#### 🏪 **Marketplace Security**
- **💳 Payment Security** - PCI DSS compliance, encrypted transactions
- **🔍 Seller Verification** - KYC/KYB verification for business accounts
- **⭐ Rating System** - Buyer/seller ratings, fraud detection
- **🛡️ Escrow System** - Secure payment holding, dispute resolution
- **📋 Transaction Monitoring** - Real-time fraud detection, suspicious activity alerts

## 🏪 Marketplace & Business Model

### 💼 **B2B2C Marketplace Platform**

MechaMap Marketplace là nền tảng thương mại điện tử B2B2C chuyên biệt cho ngành cơ khí, kết nối:

#### 🏭 **Manufacturers (Nhà Sản Xuất)**
- **Technical Data Sales** - Bán thông tin kỹ thuật, specifications, design data
- **CAD File Licensing** - Licensing 3D models, technical drawings
- **Engineering Services** - Consulting, custom design, technical support
- **Revenue Model** - Commission 5-8% + monthly subscription $99-299

#### 📦 **Suppliers (Nhà Cung Cấp)**
- **Physical Products** - Parts, materials, tools, equipment
- **Inventory Management** - Stock tracking, automated reordering
- **Logistics Integration** - Shipping, tracking, delivery management
- **Revenue Model** - Commission 3-5% + listing fees $5-50/product

#### 🏷️ **Brands (Thương Hiệu)**
- **Brand Visibility** - Company profiles, product showcases
- **Marketing Tools** - Sponsored content, banner ads, featured listings
- **Lead Generation** - Contact forms, inquiry management
- **Revenue Model** - Advertising fees $500-5000/month + premium features

#### 💰 **Revenue Streams**
1. **Transaction Commissions** - 3-8% per sale based on category
2. **Subscription Fees** - $99-299/month for business accounts
3. **Advertising Revenue** - $500-5000/month for brand promotion
4. **Premium Features** - Enhanced analytics, priority support
5. **Listing Fees** - $5-50 per product listing
6. **Verification Fees** - $100-500 for business verification

### 🎯 **Target Market**

#### 🏢 **Primary Users**
- **Mechanical Engineers** - Design, analysis, project management
- **Manufacturing Companies** - Production, quality control, supply chain
- **Engineering Students** - Learning resources, project collaboration
- **Technical Professionals** - R&D, consulting, freelancing

#### 🌍 **Geographic Focus**
- **Primary**: Vietnam, Southeast Asia
- **Secondary**: Asia-Pacific region
- **Future**: Global expansion with localized content

#### 📊 **Market Size**
- **Vietnam Manufacturing**: $200B+ industry
- **Engineering Professionals**: 500K+ engineers
- **SME Manufacturers**: 50K+ companies
- **Growth Rate**: 8-12% annually

## ⚙️ Admin Dashboard - Tổng Quan

Hệ thống quản trị toàn diện với **13/16 module** đã triển khai (81.25% hoàn thành):

### ✅ **Core Modules (Hoàn Thành)**

| Module | Features | Settings | Status |
|--------|----------|----------|--------|
| **🏢 General** | Logo, favicon, maintenance, site info | 18 | ✅ Complete |
| **🏪 Company** | Company info, address, contact details | 10 | ✅ Complete |
| **📞 Contact** | Contact forms, support information | 7 | ✅ Complete |
| **📱 Social** | Social media integration | 8 | ✅ Complete |
| **🔌 API** | Google APIs, Facebook, reCaptcha | 6 | ✅ Complete |
| **©️ Copyright** | Copyright info, footer content | 3 | ✅ Complete |
| **💬 Forum** | Forum settings, polls, attachments | 14 | ✅ Complete |
| **👥 User** | Registration, profiles, permissions | 12 | ✅ Complete |
| **📧 Email** | SMTP config, templates, testing | 7 | ✅ Complete |
| **🛡️ Security** | 2FA, rate limiting, IP whitelist | 11 | ✅ Complete |
| **📚 Wiki** | Wiki system, versioning, uploads | 9 | ✅ Complete |

### 🚀 **Advanced Modules (Hoàn Thành)**

| Module | Features | Settings | Status |
|--------|----------|----------|--------|
| **🎯 SEO** | Meta tags, sitemap, robots.txt | 6 | ✅ Complete |
| **🌐 Localization** | Multi-language, translations | 8 | ✅ Complete |

### 🚧 **Modules Đang Phát Triển**

| Module | Features | Priority | ETA |
|--------|----------|----------|-----|
| **🏪 Marketplace** | Product management, orders, payments | High | 4 weeks |
| **🔔 Notifications** | Real-time alerts, email notifications | Medium | 2 weeks |
| **💌 Messaging** | Private messaging, chat system | Medium | 3 weeks |

### 📊 **Admin Dashboard Features**

#### 🎛️ **Management Interfaces**
- **User Management** - CRUD operations, role assignment, bulk actions
- **Content Moderation** - Post approval, comment management, media review
- **Marketplace Admin** - Product listings, seller management, order tracking
- **Analytics Dashboard** - User activity, revenue tracking, performance metrics
- **System Monitoring** - Server health, error logs, performance alerts

#### 🔧 **Administrative Tools**
- **Bulk Operations** - Mass user import/export, content migration
- **Backup Management** - Automated backups, restore functionality
- **Cache Management** - Redis cache control, performance optimization
- **Log Viewer** - System logs, error tracking, audit trails
- **Database Tools** - Query builder, migration management

## 🏗️ Technical Architecture

### 🔧 **Backend Stack**
- **Framework**: Laravel 11 (PHP 8.2+)
- **Database**: MySQL 8.0+ / PostgreSQL 13+
- **Cache**: Redis 6.0+ (sessions, cache, queues)
- **Search**: Elasticsearch 8.0+ (full-text search)
- **Storage**: Local / AWS S3 / DigitalOcean Spaces
- **Queue**: Redis / Database / SQS

### 🎨 **Frontend Stack**
- **CSS Framework**: Bootstrap 5.3+
- **Icons**: Font Awesome 6.0+
- **JavaScript**: Vanilla JS (no framework dependencies)
- **Build Tools**: Laravel Mix / Vite
- **Image Processing**: Intervention Image
- **File Uploads**: Dropzone.js

### 🔐 **Security & Performance**
- **Authentication**: Laravel Sanctum + Social Login
- **Authorization**: Role-based permissions (Spatie)
- **Rate Limiting**: Laravel built-in + Redis
- **CSRF Protection**: Laravel CSRF tokens
- **XSS Protection**: HTML Purifier
- **File Security**: Virus scanning, type validation
- **Performance**: Opcache, Redis caching, CDN ready

### 🌐 **Infrastructure**
- **Web Server**: Nginx / Apache
- **PHP**: PHP-FPM 8.2+
- **SSL**: Let's Encrypt / Custom certificates
- **Monitoring**: Laravel Telescope, Horizon
- **Logging**: Laravel Log, Monolog
- **Backup**: Automated database + file backups

### 📱 **API Architecture**
- **REST API**: Laravel API Resources
- **Authentication**: Sanctum tokens
- **Rate Limiting**: Per-user, per-endpoint
- **Documentation**: OpenAPI 3.0 specification
- **Versioning**: URL-based versioning (/api/v1/)
- **Response Format**: JSON with standardized structure

### 🔄 **Development Workflow**
- **Version Control**: Git with GitFlow
- **Code Quality**: PHP CS Fixer, PHPStan, Laravel Pint
- **Testing**: PHPUnit, Pest, Feature tests
- **CI/CD**: GitHub Actions / GitLab CI
- **Deployment**: Zero-downtime deployment scripts
- **Environment**: Docker support for development

## 🚀 Cài Đặt Nhanh

### 📋 Yêu Cầu Hệ Thống

- **PHP** ≥ 8.2 với extensions: OpenSSL, PDO, Mbstring, Tokenizer, XML, JSON, Ctype, BCMath, GD
- **MySQL** ≥ 8.0 hoặc **PostgreSQL** ≥ 13
- **Composer** ≥ 2.0
- **Node.js** ≥ 18.0 & **NPM** ≥ 9.0
- **Redis** (khuyến nghị cho cache & sessions)

### ⚡ Cài Đặt Tự Động

```bash
# 1. Clone repository
git clone https://github.com/ptnghia/mechamap.git
cd mechamap

# 2. Chạy script setup
chmod +x setup.sh
./setup.sh
```

### 🔧 Cài Đặt Thủ Công

```bash
# 1. Clone repository
git clone https://github.com/ptnghia/mechamap.git
cd mechamap

# 2. Cài đặt dependencies
composer install
npm install

# 3. Cấu hình môi trường
cp .env.example .env
php artisan key:generate

# 4. Cấu hình database trong .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=mechamap
# DB_USERNAME=root
# DB_PASSWORD=

# 5. Chạy migration & seeder
php artisan migrate --seed

# 6. Build assets
npm run build

# 7. Tạo storage link
php artisan storage:link

# 8. Khởi động development server
php artisan serve
```

### 🔑 Cấu Hình Social Login

Trong file `.env`, cập nhật các thông tin sau:

```env
# Google OAuth
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
GOOGLE_REDIRECT_URI=http://127.0.0.1:8000/auth/google/callback

# Facebook OAuth
FACEBOOK_CLIENT_ID=your-facebook-client-id
FACEBOOK_CLIENT_SECRET=your-facebook-client-secret
FACEBOOK_REDIRECT_URI=http://127.0.0.1:8000/auth/facebook/callback

# Email SMTP
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
```

### 🧪 Testing

```bash
# Chạy tất cả tests
php artisan test

# Chạy tests với coverage
php artisan test --coverage

# Chạy tests cụ thể
php artisan test --filter=UserTest
```

## 🏗️ Triển Khai

### 🌐 Production Deployment

Để triển khai lên production server, tham khảo chi tiết:

- 📖 **[Hướng dẫn Deployment](docs/guides/deployment-guide.md)** - Triển khai trên VPS/Server
- 🏠 **[Deployment trên Hosting](docs/guides/deployment-guide-hosting.md)** - Triển khai qua cPanel
- ⚙️ **[Cấu hình Backend](docs/guides/backend-config-guide.md)** - Cấu hình CORS, Sanctum
- 🖥️ **[Yêu cầu Server](docs/guides/server-requirements.md)** - Thông số kỹ thuật chi tiết

### 🔧 Quick Production Setup

```bash
# Sử dụng deployment script
cd mechamap
chmod +x docs/scripts/deploy.sh
./docs/scripts/deploy.sh
```

### 🐳 Docker Deployment

```bash
# Build & run với Docker
docker-compose up -d

# Hoặc với script tự động
./docker-deploy.sh
```

## 📁 Cấu Trúc Dự Án

```
mechamap_backend/
├── 📁 app/                    # Core application
│   ├── Http/Controllers/      # Controllers (Web, API, Admin)
│   ├── Models/               # Eloquent models
│   ├── Services/             # Business logic
│   ├── Helpers/              # Helper functions
│   └── Policies/             # Authorization policies
├── 📁 database/
│   ├── migrations/           # Database migrations
│   ├── seeders/             # Database seeders
│   └── factories/           # Model factories
├── 📁 resources/
│   ├── views/               # Blade templates
│   ├── css/                 # Stylesheets
│   └── js/                  # JavaScript files
├── 📁 routes/
│   ├── web.php              # Web routes
│   ├── api.php              # API routes
│   └── admin.php            # Admin routes
├── 📁 docs/                 # 📚 Tài liệu dự án
│   ├── guides/              # Hướng dẫn triển khai & cấu hình
│   ├── reports/             # Báo cáo phân tích & tiến độ
│   ├── tests/               # File test & utility scripts
│   └── scripts/             # Deployment scripts
└── 📁 public/               # Public assets
```

## 📚 Tài Liệu

### 📖 Hướng Dẫn Chính

- **[Deployment Guide](docs/guides/deployment-guide.md)** - Hướng dẫn triển khai chi tiết
- **[Backend Configuration](docs/guides/backend-config-guide.md)** - Cấu hình backend cho production
- **[Hosting Deployment](docs/guides/deployment-guide-hosting.md)** - Triển khai qua shared hosting
- **[Server Requirements](docs/guides/server-requirements.md)** - Yêu cầu server & thông số kỹ thuật

### 📊 Báo Cáo & Phân Tích

- **[Settings Completion Report](docs/reports/SETTINGS-COMPLETION-REPORT.md)** - Tiến độ hoàn thành admin settings
- **[Admin Settings Analysis](docs/reports/ADMIN-SETTINGS-ANALYSIS-REPORT.md)** - Phân tích hệ thống admin
- **[SEO Integration Report](docs/reports/SEO-SETTINGS-INTEGRATION-REPORT.md)** - Báo cáo tích hợp SEO

### 🔧 Utilities & Tools

- **[Check Permissions](docs/tests/check_permissions.php)** - Kiểm tra quyền file/folder
- **[Check Settings](docs/tests/check_settings.php)** - Validate cấu hình hệ thống
- **[Update Image Paths](docs/tests/update_image_paths.php)** - Update đường dẫn hình ảnh
- **[Deploy Script](docs/scripts/deploy.sh)** - Script triển khai tự động

## 🛠️ API Documentation

### 🔗 **Core API Endpoints**

#### 🔐 **Authentication**
| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| `POST` | `/api/auth/login` | User login | ❌ |
| `POST` | `/api/auth/register` | User registration | ❌ |
| `POST` | `/api/auth/logout` | User logout | ✅ |
| `POST` | `/api/auth/refresh` | Refresh token | ✅ |
| `GET` | `/api/auth/me` | Current user info | ✅ |
| `POST` | `/api/auth/forgot-password` | Password reset | ❌ |

#### 💬 **Forum & Content**
| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| `GET` | `/api/threads` | List threads | ❌ |
| `POST` | `/api/threads` | Create thread | ✅ |
| `GET` | `/api/threads/{id}` | Thread details | ❌ |
| `PUT` | `/api/threads/{id}` | Update thread | ✅ |
| `DELETE` | `/api/threads/{id}` | Delete thread | ✅ |
| `POST` | `/api/threads/{id}/comments` | Add comment | ✅ |
| `GET` | `/api/categories` | List categories | ❌ |

#### 🏪 **Marketplace**
| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| `GET` | `/api/products` | List products | ❌ |
| `POST` | `/api/products` | Create product | ✅ |
| `GET` | `/api/products/{id}` | Product details | ❌ |
| `PUT` | `/api/products/{id}` | Update product | ✅ |
| `DELETE` | `/api/products/{id}` | Delete product | ✅ |
| `POST` | `/api/orders` | Create order | ✅ |
| `GET` | `/api/orders` | List user orders | ✅ |

#### 👥 **User Management**
| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| `GET` | `/api/users` | List users | ✅ |
| `GET` | `/api/users/{id}` | User profile | ❌ |
| `PUT` | `/api/users/{id}` | Update profile | ✅ |
| `POST` | `/api/users/{id}/follow` | Follow user | ✅ |
| `DELETE` | `/api/users/{id}/follow` | Unfollow user | ✅ |

### 📝 **Response Format**

#### ✅ **Success Response**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "Sample Thread",
    "content": "Thread content...",
    "author": {
      "id": 1,
      "username": "admin",
      "role": "admin",
      "avatar": "https://example.com/avatar.jpg"
    },
    "created_at": "2025-06-01T14:30:00Z",
    "updated_at": "2025-06-01T14:30:00Z"
  },
  "message": "Success",
  "meta": {
    "current_page": 1,
    "total": 100,
    "per_page": 15
  }
}
```

#### ❌ **Error Response**
```json
{
  "success": false,
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "The given data was invalid.",
    "details": {
      "title": ["The title field is required."],
      "content": ["The content field is required."]
    }
  },
  "timestamp": "2025-06-01T14:30:00Z"
}
```

### 🔑 **Authentication**
```bash
# Get access token
curl -X POST /api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password"}'

# Use token in requests
curl -X GET /api/threads \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

## 🧪 Testing & Quality

### 🔍 Code Quality Tools

```bash
# PHP CS Fixer - Format code
./vendor/bin/php-cs-fixer fix

# PHPStan - Static analysis
./vendor/bin/phpstan analyse

# Pest/PHPUnit - Testing
php artisan test

# Laravel Pint - Code styling
./vendor/bin/pint
```

### 📈 Performance Monitoring

- **Laravel Telescope** - Debug & monitoring dashboard
- **Laravel Horizon** - Queue monitoring
- **Opcache** - PHP bytecode caching
- **Redis** - Caching & session storage

## 🤝 Đóng Góp

Chúng tôi rất hoan nghênh sự đóng góp từ cộng đồng!

### 🚀 Cách Đóng Góp

1. **Fork** repository này
2. **Clone** fork về máy local: `git clone https://github.com/username/mechamap.git`
3. **Tạo branch** mới: `git checkout -b feature/amazing-feature`
4. **Commit** changes: `git commit -m 'Add amazing feature'`
5. **Push** lên branch: `git push origin feature/amazing-feature`
6. **Tạo Pull Request**

### 📝 Coding Standards

- Tuân thủ **PSR-12** coding standard
- Viết **tests** cho các tính năng mới
- **Comment** bằng tiếng Việt, code bằng tiếng Anh
- Sử dụng **conventional commits**: `feat:`, `fix:`, `docs:`, `refactor:`

### 🐛 Báo Cáo Lỗi

Khi báo cáo lỗi, vui lòng bao gồm:
- **Mô tả chi tiết** lỗi
- **Bước tái hiện** lỗi
- **Môi trường** (PHP, Laravel, browser version)
- **Screenshots** (nếu có)

### 💡 Đề Xuất Tính Năng

- Mở **GitHub Issue** với label `enhancement`
- Mô tả **chi tiết** tính năng đề xuất
- Giải thích **lý do** cần tính năng này
- Đề xuất **cách triển khai** (nếu có)

## 🗺️ Roadmap & Future Plans

### 🎯 **Phase 1: Core Platform (Completed)**
- ✅ User authentication & authorization system
- ✅ Forum & community features
- ✅ Admin dashboard (13/16 modules)
- ✅ Multi-language support (Vietnamese/English)
- ✅ Security & performance optimization
- ✅ Basic marketplace structure

### 🚀 **Phase 2: Marketplace Development (In Progress)**
- 🔄 **Product Management** - Advanced product catalog, categories
- 🔄 **Order System** - Shopping cart, checkout, payment integration
- 🔄 **Seller Dashboard** - Vendor management, analytics
- 🔄 **Payment Gateway** - VNPay, PayPal, Stripe integration
- ⏳ **Shipping Integration** - GHN, Viettel Post, J&T Express
- ⏳ **Review System** - Product reviews, seller ratings

### 🌟 **Phase 3: Advanced Features (Q2 2025)**
- ⏳ **Real-time Chat** - WebSocket-based messaging
- ⏳ **Video Conferencing** - Integrated video calls for consultations
- ⏳ **Mobile App** - React Native / Flutter mobile application
- ⏳ **AI Integration** - Smart recommendations, content moderation
- ⏳ **Advanced Analytics** - Business intelligence, predictive analytics
- ⏳ **API Marketplace** - Third-party integrations, developer portal

### 🌍 **Phase 4: Global Expansion (Q3-Q4 2025)**
- ⏳ **Multi-currency Support** - USD, EUR, regional currencies
- ⏳ **International Shipping** - Global logistics integration
- ⏳ **Localization** - Additional languages (Chinese, Japanese, Korean)
- ⏳ **Regional Compliance** - GDPR, local regulations
- ⏳ **Partner Network** - International distributor network
- ⏳ **White-label Solution** - Customizable platform for enterprises

### 📈 **Business Milestones**
- **2025 Q1**: 10,000+ registered users, 1,000+ products
- **2025 Q2**: $100K+ monthly GMV, 100+ verified sellers
- **2025 Q3**: Break-even point, international expansion
- **2025 Q4**: $1M+ annual revenue, mobile app launch

### 🔮 **Future Vision**
- **Industry 4.0 Integration** - IoT, smart manufacturing
- **Blockchain Integration** - Supply chain transparency, smart contracts
- **AR/VR Features** - 3D product visualization, virtual showrooms
- **Machine Learning** - Predictive maintenance, demand forecasting
- **Sustainability Focus** - Carbon footprint tracking, green manufacturing

## 📊 Thống Kê Dự Án

<div align="center">

![Contributors](https://img.shields.io/github/contributors/ptnghia/mechamap?style=for-the-badge)
![Forks](https://img.shields.io/github/forks/ptnghia/mechamap?style=for-the-badge)
![Stars](https://img.shields.io/github/stars/ptnghia/mechamap?style=for-the-badge)
![Issues](https://img.shields.io/github/issues/ptnghia/mechamap?style=for-the-badge)
![License](https://img.shields.io/github/license/ptnghia/mechamap?style=for-the-badge)

</div>

## 📞 Hỗ Trợ & Liên Hệ

- 🌐 **Website**: [mechamap.com](https://mechamap.com)
- 📧 **Email**: support@mechamap.com
- 💬 **Discord**: [MechaMap Community](https://discord.gg/mechamap)
- 📱 **Facebook**: [MechaMap Việt Nam](https://facebook.com/mechamap.vn)

## 📄 Giấy Phép

Dự án này được phát hành dưới giấy phép **[MIT License](LICENSE)**.

```
MIT License

Copyright (c) 2025 MechaMap Team

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```

---

<div align="center">

**⭐ Nếu dự án này hữu ích cho bạn, hãy cho chúng tôi một star! ⭐**

Made with ❤️ by [MechaMap Team](https://github.com/ptnghia)

</div>
