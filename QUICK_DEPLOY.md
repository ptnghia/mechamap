# 🚀 MechaMap Quick Deployment Guide

Hướng dẫn triển khai nhanh MechaMap từ Git repository.

## 📋 Yêu cầu hệ thống

### Server Requirements:
- **PHP**: 8.2 hoặc cao hơn
- **MySQL**: 5.7 hoặc cao hơn (hoặc MariaDB 10.3+)
- **Web Server**: Apache hoặc Nginx
- **Composer**: Latest version
- **Node.js**: 18+ (optional, for frontend assets)

### PHP Extensions Required:
```
BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, 
PDO, Tokenizer, XML, GD, Curl, Zip
```

## 🔧 Bước 1: Tải dự án từ Git

```bash
# Clone repository
git clone https://github.com/your-username/mechamap_backend.git
cd mechamap_backend

# Hoặc tải ZIP và giải nén
wget https://github.com/your-username/mechamap_backend/archive/master.zip
unzip master.zip
cd mechamap_backend-master
```

## ⚙️ Bước 2: Cài đặt Dependencies

### 2.1 Cài đặt thông thường
```bash
# Cài đặt PHP dependencies
composer install --optimize-autoloader --no-dev

# Tạo symbolic link cho storage (nếu cần)
php artisan storage:link
```

### 2.2 Ubuntu 22.04 - Khắc phục lỗi Composer
```bash
# Chạy script tự động (Khuyến nghị)
chmod +x ubuntu-22-04-deploy.sh
./ubuntu-22-04-deploy.sh

# Hoặc khắc phục thủ công:
rm -rf vendor/ composer.lock
composer clear-cache
composer install --no-scripts --no-autoloader --no-dev
composer dump-autoload --optimize --no-dev
```

### 2.3 Các hệ điều hành khác
```bash
# Linux/Mac:
bash scripts/fix_composer_install.sh

# Windows PowerShell:
.\scripts\fix_composer_install.ps1
```

## 🗄️ Bước 3: Cấu hình Database

### 3.1 Tạo Database
```sql
CREATE DATABASE mechamap CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'mechamap_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON mechamap.* TO 'mechamap_user'@'localhost';
FLUSH PRIVILEGES;
```

### 3.2 Cấu hình Environment
```bash
# Copy production environment
cp .env.production .env

# Chỉnh sửa database credentials trong .env
nano .env
```

**Cập nhật các thông tin sau trong `.env`:**
```bash
DB_DATABASE=mechamap
DB_USERNAME=mechamap_user
DB_PASSWORD=your_secure_password

# Cập nhật domain
APP_URL=https://yourdomain.com

# Tạo APP_KEY mới
php artisan key:generate
```

## 🏗️ Bước 4: Chạy Migrations và Seeders

```bash
# Chạy migrations
php artisan migrate --force

# Chạy seeders cơ bản
php artisan db:seed --class=RolesAndPermissionsSeeder
php artisan db:seed --class=CategorySeeder
php artisan db:seed --class=ProductCategorySeeder
php artisan db:seed --class=CountrySeeder

# Import dữ liệu demo (optional)
mysql -u mechamap_user -p mechamap < data_v2_fixed.sql
```

## 🔧 Bước 5: Tối ưu hóa Production

```bash
# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize

# Set permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## 🌐 Bước 6: Cấu hình Web Server

### Apache (.htaccess đã có sẵn)
```apache
# Đảm bảo mod_rewrite được enable
a2enmod rewrite
systemctl restart apache2

# Virtual Host example
<VirtualHost *:80>
    ServerName yourdomain.com
    DocumentRoot /path/to/mechamap_backend/public
    
    <Directory /path/to/mechamap_backend/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### Nginx
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /path/to/mechamap_backend/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

## 🔄 Bước 7: Setup Queue Worker (Production)

```bash
# Tạo supervisor config
sudo nano /etc/supervisor/conf.d/mechamap-worker.conf
```

```ini
[program:mechamap-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/mechamap_backend/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/mechamap_backend/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
# Reload supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start mechamap-worker:*
```

## 📅 Bước 8: Setup Cron Jobs

```bash
# Thêm vào crontab
crontab -e

# Thêm dòng sau:
* * * * * cd /path/to/mechamap_backend && php artisan schedule:run >> /dev/null 2>&1
```

## 🔐 Bước 9: SSL Certificate (Khuyến nghị)

```bash
# Sử dụng Let's Encrypt
sudo apt install certbot python3-certbot-apache
sudo certbot --apache -d yourdomain.com

# Hoặc cho Nginx
sudo certbot --nginx -d yourdomain.com
```

## ✅ Bước 10: Kiểm tra Deployment

```bash
# Chạy verification script
php scripts/verify_production_config_no_redis.php

# Kiểm tra logs
tail -f storage/logs/laravel.log

# Test website
curl -I https://yourdomain.com
```

## 🎯 Tài khoản Admin mặc định

Sau khi chạy seeders, bạn có thể đăng nhập với:
- **Email**: `admin@mechapap.vn`
- **Password**: `admin123456`

**⚠️ Quan trọng**: Đổi mật khẩu admin ngay sau khi đăng nhập!

## 🔧 Troubleshooting

### Lỗi thường gặp:

#### 1. Permission denied
```bash
sudo chown -R www-data:www-data storage bootstrap/cache
chmod -R 755 storage bootstrap/cache
```

#### 2. Database connection failed
```bash
# Kiểm tra MySQL service
sudo systemctl status mysql
sudo systemctl start mysql

# Test connection
mysql -u mechamap_user -p -e "SELECT 1"
```

#### 3. 500 Internal Server Error
```bash
# Kiểm tra logs
tail -f storage/logs/laravel.log
tail -f /var/log/apache2/error.log

# Clear cache
php artisan config:clear
php artisan cache:clear
```

#### 4. Queue not working
```bash
# Restart queue worker
sudo supervisorctl restart mechamap-worker:*

# Check queue status
php artisan queue:work --once
```

#### 5. Composer Install Errors

**Lỗi: Method forceAssetUrl does not exist**
```bash
# Khắc phục:
rm -rf vendor/
composer clear-cache
composer install --no-scripts --no-autoloader --no-dev
composer dump-autoload --optimize --no-dev
php artisan key:generate --force
php artisan storage:link
```

**Lỗi: Do not run Composer as root**
```bash
# Tạo user riêng cho deployment
sudo adduser deployer
sudo usermod -aG www-data deployer
su - deployer
cd /path/to/project
composer install --no-dev
```

**Lỗi: Memory limit exceeded**
```bash
# Tăng memory limit
php -d memory_limit=512M /usr/local/bin/composer install --no-dev
# Hoặc
export COMPOSER_MEMORY_LIMIT=-1
composer install --no-dev
```

## 📞 Hỗ trợ

- **Documentation**: `/docs/deployment/production-no-redis.md`
- **Logs**: `storage/logs/laravel.log`
- **Queue logs**: `storage/logs/worker.log`

---

**🎉 Deployment hoàn tất! MechaMap đã sẵn sàng hoạt động!**
