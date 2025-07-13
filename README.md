# 🔧 MechaMap - Cộng Đồng Cơ Khí Việt Nam

<div align="center">

![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)

**Nền tảng diễn đàn kỹ thuật hàng đầu dành cho cộng đồng cơ khí, tự động hóa và công nghệ Việt Nam**

[📖 Documentation](docs/) • [🚀 Quick Start](#-quick-start) • [🏗️ Deployment](docs/deployment-guide.md) • [🤝 Contributing](#-contributing)

</div>

---

## 🌟 About MechaMap

**MechaMap** is a professional online platform designed specifically for the mechanical engineering, automation, and technology community in Vietnam. This platform connects engineers, students, and professionals, creating a space for learning and sharing specialized knowledge.

### 🎯 Production Ready
- ✅ **Laravel 11** - Latest stable framework
- ✅ **Production Optimized** - Caching, optimization, security
- ✅ **Multi-language** - Vietnamese & English support
- ✅ **Responsive Design** - Bootstrap 5 with mobile-first approach
- ✅ **SEO Optimized** - Dynamic meta tags, sitemaps, structured data

### ✨ Key Features

#### 🔐 **Authentication & Security**
- Multi-Auth System (Email, Google, Facebook, 2FA)
- Advanced Security (Rate limiting, IP whitelist, CSRF protection)
- Session Management with Redis backend

#### 👥 **8-Level Permission System**
- System Management (Super Admin, System Admin, Content Admin)
- Community Management (Content, Marketplace, Community Moderators)
- Community Members (Senior Member, Member, Guest, Student)
- Business Partners (Manufacturer, Supplier, Brand, Verified Partner)

#### 🏪 **B2B2C Marketplace**
- Multi-Vendor Platform for Suppliers, Manufacturers, Brands
- Product Management (CAD files, technical drawings, physical products)
- Payment Integration with multiple gateways

#### 💬 **Forum & Community**
- Advanced Forum System with categories, threads, polls
- Real-time Chat and private messaging
- Content Management with rich text editor

#### 🎨 **UI/UX & Performance**
- Responsive Design with Bootstrap 5
- Multi-language Support (Vietnamese/English)
- SEO Optimized with dynamic meta tags and sitemaps
- Admin Dashboard with comprehensive management tools

## 🏗️ Architecture

### 🔧 **Tech Stack**
- **Backend**: Laravel 11 (PHP 8.2+)
- **Database**: MySQL 8.0+ / PostgreSQL 13+
- **Frontend**: Bootstrap 5.3+ with Vanilla JS
- **Cache**: Redis 6.0+ (sessions, cache, queues)
- **Icons**: Font Awesome 6.0+

### 📁 **Project Structure**
```
mechamap_backend/
├── 📁 app/                    # Core application
├── 📁 database/               # Migrations, seeders
├── 📁 resources/              # Views, assets
├── 📁 routes/                 # Web, API, admin routes
├── 📁 docs/                   # 📚 Documentation
├── 📁 public/                 # Public assets
└── 📁 storage/                # Logs, cache, uploads
```

## 📚 Documentation

For detailed information, please refer to the documentation in the `/docs` folder:

- **[Deployment Guide](docs/deployment-guide.md)** - Production deployment instructions
- **[User Roles & Permissions](docs/user-roles-and-permissions.md)** - Complete role system documentation
- **[Admin Settings](docs/admin-settings-analysis.md)** - Admin dashboard configuration
- **[API Documentation](docs/api-documentation.md)** - REST API endpoints and usage

## 🔐 Default Admin Account

After installation, you can access the admin panel with:

- **URL**: `https://yourdomain.com/admin`
- **Email**: `admin@mechamap.vn`
- **Password**: `admin123456`

⚠️ **Important**: Change the default admin password immediately after first login!

## 🤝 Contributing

We welcome contributions from the community!

### 🚀 How to Contribute

1. **Fork** this repository
2. **Clone** your fork: `git clone https://github.com/username/mechamap.git`
3. **Create** a new branch: `git checkout -b feature/amazing-feature`
4. **Commit** your changes: `git commit -m 'Add amazing feature'`
5. **Push** to branch: `git push origin feature/amazing-feature`
6. **Create** a Pull Request

### 📝 Coding Standards

- Follow **PSR-12** coding standard
- Write **tests** for new features
- Use **conventional commits**: `feat:`, `fix:`, `docs:`, `refactor:`

## 📞 Support & Contact

- 🌐 **Website**: [mechamap.com](https://mechamap.com)
- 📧 **Email**: support@mechamap.com
- 💬 **Issues**: [GitHub Issues](https://github.com/yourusername/mechamap/issues)

## 📄 License

This project is licensed under the **[MIT License](LICENSE)**.

---

<div align="center">

**⭐ If this project is helpful to you, please give us a star! ⭐**

Made with ❤️ by [MechaMap Team](https://github.com/yourusername)

</div>


