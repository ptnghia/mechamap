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

- 🔐 **Hệ thống xác thực đa dạng** - Email, Google, Facebook với xác thực 2 lớp
- 👥 **Phân quyền 5 cấp độ** - Admin, Moderator, Senior, Member, Guest với quyền hạn rõ ràng  
- 💬 **Diễn đàn tương tác** - Đăng bài, bình luận, phản ứng, polls và media
- 📁 **Quản lý nội dung** - Phân loại chuyên mục, tag, tìm kiếm nâng cao
- 💌 **Hệ thống tin nhắn** - Chat riêng tư, nhóm và thông báo realtime
- 🛡️ **Bảo mật cao cấp** - Rate limiting, IP whitelist, content moderation
- 🎨 **Giao diện hiện đại** - Responsive design với TailwindCSS, dark mode
- 🌐 **Đa ngôn ngữ** - Hỗ trợ Tiếng Việt và Tiếng Anh
- ⚙️ **Admin Dashboard** - Quản trị toàn diện với 11/16 module hoàn thành
- 📊 **Analytics & SEO** - Tích hợp SEO tools và báo cáo chi tiết

## 🏛️ Kiến Trúc Hệ Thống

### 📋 Phân Quyền Người Dùng

| Cấp độ | Quyền hạn | Mô tả |
|--------|-----------|-------|
| **👑 Admin** | Toàn quyền | Quản lý hệ thống, server, tất cả nội dung và người dùng |
| **🛡️ Moderator** | Quản lý nội dung | Kiểm duyệt bài viết, quản lý người dùng (trừ admin) |
| **⭐ Senior** | Thành viên cao cấp | Đăng bài, bình luận, tin nhắn, báo cáo |
| **👤 Member** | Thành viên cơ bản | Đăng bài, bình luận, tin nhắn |
| **👁️ Guest** | Chỉ xem | Xem nội dung công khai, không tương tác |

### 🔑 Hệ Thống Xác Thực

- **📧 Email truyền thống** - Đăng ký với email/username, xác thực email bắt buộc
- **🌐 Social Login** - Google, Facebook với auto-sync tài khoản
- **🔒 Bảo mật 2FA** - Xác thực 2 lớp với Google Authenticator
- **🔗 Liên kết tài khoản** - Một người dùng có thể đăng nhập bằng nhiều phương thức

## ⚙️ Admin Dashboard - Tổng Quan

Hệ thống quản trị hoàn chỉnh với **11/16 module** đã triển khai (68.75% hoàn thành):

### ✅ Module Đã Hoàn Thành

| Module | Tính năng | Số Settings |
|--------|-----------|-------------|
| **🏢 General** | Logo, favicon, maintenance, site info | 18 |
| **🏪 Company** | Thông tin công ty, địa chỉ, liên hệ | 10 |
| **📞 Contact** | Form liên hệ, thông tin hỗ trợ | 7 |
| **📱 Social** | Facebook, Twitter, Instagram, YouTube | 8 |
| **🔌 API** | Google APIs, Facebook, reCaptcha | 6 |
| **©️ Copyright** | Thông tin bản quyền, footer | 3 |
| **💬 Forum** | Diễn đàn, polls, file attachments | 14 |
| **👥 User** | Đăng ký, profile, avatar, permissions | 12 |
| **📧 Email** | SMTP config, templates, test connection | 7 |
| **🛡️ Security** | 2FA, rate limiting, IP whitelist | 11 |
| **📚 Wiki** | Wiki system, versioning, file uploads | 9 |

### 🚧 Module Đang Phát Triển

- **🎯 SEO** - Meta tags, sitemap, robots.txt (6 settings)
- **🏆 Showcase** - Project showcase, portfolios (14 settings)  
- **🔍 Search** - Search engine, indexing (11 settings)
- **🔔 Alerts** - Notification system (7 settings)
- **💌 Messages** - Private messaging system (10 settings)

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

### 🔗 Endpoints Chính

| Method | Endpoint | Mô tả | Auth |
|--------|----------|-------|------|
| `GET` | `/api/threads` | Danh sách bài viết | ❌ |
| `POST` | `/api/threads` | Tạo bài viết mới | ✅ |
| `GET` | `/api/threads/{id}` | Chi tiết bài viết | ❌ |
| `PUT` | `/api/threads/{id}` | Cập nhật bài viết | ✅ |
| `DELETE` | `/api/threads/{id}` | Xóa bài viết | ✅ |
| `POST` | `/api/auth/login` | Đăng nhập | ❌ |
| `POST` | `/api/auth/register` | Đăng ký | ❌ |
| `POST` | `/api/auth/logout` | Đăng xuất | ✅ |

### 📝 Response Format

```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "Bài viết mẫu",
    "content": "Nội dung bài viết...",
    "author": {
      "id": 1,
      "username": "admin",
      "role": "admin"
    }
  },
  "message": "Thành công",
  "timestamp": "2025-06-01T14:30:00Z"
}
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
