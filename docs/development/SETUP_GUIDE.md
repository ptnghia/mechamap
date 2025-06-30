# 👨‍💻 MechaMap Development Setup Guide

> **Target Audience**: Developers, DevOps Engineers  
> **Last Updated**: January 2025  
> **Prerequisites**: PHP 8.2+, Composer, Node.js 18+, MySQL 8.0+

---

## 🚀 **QUICK START (5 Minutes)**

```bash
# 1. Clone repository
git clone https://github.com/ptnghia/mechamap.git
cd mechamap

# 2. Install dependencies
composer install
npm install

# 3. Environment setup
cp .env.example .env
php artisan key:generate

# 4. Database setup
php artisan migrate --seed

# 5. Start development server
php artisan serve
```

**🌐 Access**: `http://localhost:8000`  
**👨‍💼 Admin**: `http://localhost:8000/admin`

---

## 📋 **SYSTEM REQUIREMENTS**

### **🖥️ Server Requirements**
| Component | Minimum | Recommended |
|-----------|---------|-------------|
| **PHP** | 8.2+ | 8.3+ |
| **MySQL** | 8.0+ | 8.0.35+ |
| **Redis** | 6.0+ | 7.0+ |
| **Node.js** | 18+ | 20+ |
| **Memory** | 2GB | 4GB+ |
| **Storage** | 10GB | 50GB+ |

### **🔧 PHP Extensions**
```bash
# Required extensions
php -m | grep -E "(bcmath|ctype|fileinfo|json|mbstring|openssl|pdo|tokenizer|xml|zip|gd|curl|redis)"

# Install missing extensions (Ubuntu/Debian)
sudo apt install php8.2-{bcmath,ctype,fileinfo,json,mbstring,openssl,pdo,tokenizer,xml,zip,gd,curl,redis,mysql}
```

---

## 🛠️ **DETAILED SETUP**

### **1. Environment Configuration**

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mechamap
DB_USERNAME=root
DB_PASSWORD=

# Configure Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Configure mail (optional)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
```

### **2. Database Setup**

```bash
# Create database
mysql -u root -p
CREATE DATABASE mechamap CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;

# Run migrations
php artisan migrate

# Seed database with sample data
php artisan db:seed

# Create admin user
php artisan make:admin-user
```

### **3. Storage & Permissions**

```bash
# Create storage link
php artisan storage:link

# Set permissions (Linux/Mac)
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Windows (run as administrator)
icacls storage /grant Everyone:F /T
icacls bootstrap/cache /grant Everyone:F /T
```

### **4. Asset Compilation**

```bash
# Install Node.js dependencies
npm install

# Development build
npm run dev

# Production build
npm run build

# Watch for changes (development)
npm run watch
```

---

## 🐳 **DOCKER SETUP**

### **🚀 Quick Docker Start**

```bash
# Clone and start with Docker
git clone https://github.com/ptnghia/mechamap.git
cd mechamap

# Start with Docker Compose
docker-compose up -d

# Install dependencies
docker-compose exec app composer install
docker-compose exec app npm install

# Setup database
docker-compose exec app php artisan migrate --seed
```

### **📝 Docker Configuration**

```yaml
# docker-compose.yml
version: '3.8'
services:
  app:
    build: .
    ports:
      - "8000:8000"
    volumes:
      - .:/var/www/html
    depends_on:
      - mysql
      - redis

  mysql:
    image: mysql:8.0
    environment:
      MYSQL_DATABASE: mechamap
      MYSQL_ROOT_PASSWORD: secret
    ports:
      - "3306:3306"

  redis:
    image: redis:7-alpine
    ports:
      - "6379:6379"
```

---

## 🔧 **DEVELOPMENT TOOLS**

### **🧪 Testing Setup**

```bash
# Install testing dependencies
composer require --dev phpunit/phpunit pestphp/pest

# Run tests
php artisan test

# Run specific test
php artisan test --filter UserTest

# Generate test coverage
php artisan test --coverage
```

### **🔍 Code Quality**

```bash
# Install code quality tools
composer require --dev laravel/pint phpstan/phpstan

# Fix code style
./vendor/bin/pint

# Static analysis
./vendor/bin/phpstan analyse

# Pre-commit hooks
composer require --dev brianium/paratest
```

### **🐛 Debugging Tools**

```bash
# Install debugging tools
composer require --dev barryvdh/laravel-debugbar
composer require --dev spatie/laravel-ray

# Enable debug mode
APP_DEBUG=true
APP_ENV=local

# Install Telescope (optional)
composer require laravel/telescope
php artisan telescope:install
```

---

## 📊 **PERFORMANCE OPTIMIZATION**

### **⚡ Caching Setup**

```bash
# Configure cache
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Clear and optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### **🗄️ Database Optimization**

```bash
# Index optimization
php artisan db:index-analyze

# Query optimization
php artisan db:query-optimize

# Database maintenance
php artisan db:maintenance
```

---

## 🌐 **LOCAL DOMAIN SETUP**

### **🔧 Virtual Host (Apache)**

```apache
# /etc/apache2/sites-available/mechamap.conf
<VirtualHost *:80>
    ServerName mechamap.test
    DocumentRoot /path/to/mechamap/public
    
    <Directory /path/to/mechamap/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### **🔧 Server Block (Nginx)**

```nginx
# /etc/nginx/sites-available/mechamap
server {
    listen 80;
    server_name mechamap.test;
    root /path/to/mechamap/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
    }
}
```

### **🌐 Hosts File**

```bash
# Add to /etc/hosts (Linux/Mac) or C:\Windows\System32\drivers\etc\hosts (Windows)
127.0.0.1 mechamap.test
```

---

## 🔐 **SECURITY SETUP**

### **🛡️ Security Configuration**

```bash
# Generate secure keys
php artisan key:generate
php artisan jwt:secret

# Setup 2FA
composer require pragmarx/google2fa-laravel

# Configure security headers
SECURE_HEADERS=true
FORCE_HTTPS=false  # Set to true in production
```

### **🔒 SSL Setup (Development)**

```bash
# Generate self-signed certificate
openssl req -x509 -newkey rsa:4096 -keyout key.pem -out cert.pem -days 365 -nodes

# Configure HTTPS
FORCE_HTTPS=true
SESSION_SECURE_COOKIE=true
```

---

## 🆘 **TROUBLESHOOTING**

### **❌ Common Issues**

| Issue | Solution |
|-------|----------|
| **Permission denied** | `chmod -R 775 storage bootstrap/cache` |
| **Key not set** | `php artisan key:generate` |
| **Database connection** | Check `.env` database credentials |
| **Composer errors** | `composer clear-cache && composer install` |
| **NPM errors** | `rm -rf node_modules && npm install` |
| **Cache issues** | `php artisan cache:clear && php artisan config:clear` |

### **🔧 Debug Commands**

```bash
# Clear all caches
php artisan optimize:clear

# Check system status
php artisan about

# Check database connection
php artisan tinker
>>> DB::connection()->getPdo();

# Check queue status
php artisan queue:work --verbose
```

---

## 📚 **NEXT STEPS**

1. **[Coding Standards](./CODING_STANDARDS.md)** - Follow project conventions
2. **[Testing Guide](./TESTING_GUIDE.md)** - Write and run tests
3. **[API Documentation](../api/README.md)** - Understand API structure
4. **[Contributing Guide](./CONTRIBUTING.md)** - Contribute to the project
5. **[Debugging Guide](./DEBUGGING.md)** - Debug effectively

---

**💬 Developer Support**: dev-support@mechamap.com | **📖 Wiki**: [wiki.mechamap.com](https://wiki.mechamap.com)
