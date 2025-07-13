# 🚀 Production Deployment Guide - MechaMap Business Verification Platform

**Complete Production Deployment Guide for https://mechamap.com/**

[![Production Ready](https://img.shields.io/badge/Status-Production%20Ready-green.svg)](../business-verification/README.md)
[![Laravel 11](https://img.shields.io/badge/Laravel-11.x-red.svg)](#requirements)
[![Security](https://img.shields.io/badge/Security-Enterprise%20Grade-orange.svg)](#security-setup)

---

## 🎯 **Deployment Overview**

Hướng dẫn này cung cấp các bước chi tiết để deploy MechaMap Business Verification Platform lên production server với domain https://mechamap.com/. Hệ thống đã hoàn thành tất cả 4 phases phát triển và sẵn sàng cho production.

### **🏗️ Production Architecture**

```
┌─────────────────────────────────────────────────────────────┐
│                    Production Environment                   │
├─────────────────────────────────────────────────────────────┤
│  🌐 Domain: https://mechamap.com/                          │
│  🔒 SSL Certificate (Let's Encrypt or Commercial)          │
│  🖥️ Web Server (Nginx/Apache)                              │
│  🐘 PHP 8.2+ with Extensions                               │
│  🗄️ MySQL 8.0+ Database                                    │
│  ⚡ Redis Cache & Sessions                                  │
│  📁 Private File Storage                                    │
│  📧 Email Service (SMTP)                                    │
└─────────────────────────────────────────────────────────────┘
```

## 📋 **Pre-Deployment Requirements**

### **🖥️ Server Specifications (Minimum)**
```
Production Server:
├── CPU: 4 cores (2.4GHz+)
├── RAM: 8GB DDR4
├── Storage: 100GB SSD
├── Network: 100Mbps
├── OS: Ubuntu 20.04+ or CentOS 8+
└── Control Panel: cPanel/DirectAdmin (optional)
```

### **💻 Software Requirements**
```
Required Software Stack:
├── PHP 8.2+ with extensions:
│   ├── php-fpm, php-mysql, php-redis
│   ├── php-gd, php-zip, php-curl
│   ├── php-xml, php-mbstring, php-intl
│   └── php-bcmath, php-opcache
├── MySQL 8.0+ or MariaDB 10.6+
├── Redis 6.0+ (for caching)
├── Nginx 1.20+ or Apache 2.4+
├── Composer 2.x
└── Git (for code deployment)
```

## 🔐 **Security Setup**

### **🔒 SSL Certificate Setup**

#### **Option 1: Let's Encrypt (Free)**
```bash
# Install Certbot
sudo apt update
sudo apt install certbot python3-certbot-nginx

# Generate SSL certificate
sudo certbot --nginx -d mechamap.com -d www.mechamap.com

# Auto-renewal setup
sudo crontab -e
# Add: 0 12 * * * /usr/bin/certbot renew --quiet
```

#### **Option 2: Commercial SSL Certificate**
```bash
# Upload certificate files to server
sudo mkdir -p /etc/ssl/mechamap
sudo cp mechamap.com.crt /etc/ssl/mechamap/
sudo cp mechamap.com.key /etc/ssl/mechamap/
sudo cp intermediate.crt /etc/ssl/mechamap/
sudo chmod 600 /etc/ssl/mechamap/*.key
sudo chmod 644 /etc/ssl/mechamap/*.crt
```

### **🛡️ Firewall Configuration**
```bash
# UFW Firewall Setup
sudo ufw enable
sudo ufw allow 22/tcp    # SSH
sudo ufw allow 80/tcp    # HTTP
sudo ufw allow 443/tcp   # HTTPS
sudo ufw deny 3306/tcp   # MySQL (internal only)
sudo ufw deny 6379/tcp   # Redis (internal only)
sudo ufw status
```

## 📦 **Code Deployment**

### **🔄 Initial Deployment**

#### **Step 1: Clone Repository**
```bash
# Navigate to web directory
cd /var/www/html

# Clone repository (replace with your Git URL)
sudo git clone https://github.com/your-org/mechamap-backend.git mechamap
cd mechamap

# Set proper ownership
sudo chown -R www-data:www-data /var/www/html/mechamap
sudo chmod -R 755 /var/www/html/mechamap
```

#### **Step 2: Install Dependencies**
```bash
# Install Composer dependencies
composer install --no-dev --optimize-autoloader

# Set storage permissions
sudo chmod -R 775 storage
sudo chmod -R 775 bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache
```

#### **Step 3: Environment Configuration**
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Edit environment variables
nano .env
```

### **⚙️ Production Environment Variables**
```bash
# /var/www/html/mechamap/.env

# Application Settings
APP_NAME="MechaMap Business Verification"
APP_ENV=production
APP_KEY=base64:your-generated-key-here
APP_DEBUG=false
APP_URL=https://mechamap.com

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mechamap_production
DB_USERNAME=mechamap_user
DB_PASSWORD=your_secure_database_password

# Redis Configuration
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=your_redis_password
REDIS_PORT=6379
REDIS_DB=0

# Cache & Session Configuration
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=noreply@mechamap.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@mechamap.com
MAIL_FROM_NAME="MechaMap"

# Security Settings
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=strict

# File Storage
FILESYSTEM_DISK=private
PRIVATE_STORAGE_PATH=/var/www/html/mechamap/storage/app/private

# Business Verification Settings
VERIFICATION_MANUAL_REVIEW=true
DOCUMENT_MAX_SIZE=10240
SECURITY_MONITORING_ENABLED=true
THREAT_DETECTION_LEVEL=high

# Commission Rates (can be changed via admin)
DEFAULT_COMMISSION_RATE=10.0
VERIFIED_MANUFACTURER_RATE=5.0
VERIFIED_SUPPLIER_RATE=3.0
VERIFIED_PARTNER_RATE=2.0
```

## 🗄️ **Database Setup**

### **📋 Database Creation**
```sql
-- Connect to MySQL as root
mysql -u root -p

-- Create database
CREATE DATABASE mechamap_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create user with proper privileges
CREATE USER 'mechamap_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, ALTER, INDEX, DROP 
ON mechamap_production.* TO 'mechamap_user'@'localhost';

-- Create backup user
CREATE USER 'mechamap_backup'@'localhost' IDENTIFIED BY 'backup_password';
GRANT SELECT, LOCK TABLES ON mechamap_production.* TO 'mechamap_backup'@'localhost';

FLUSH PRIVILEGES;
EXIT;
```

### **🔄 Database Migration**
```bash
# Run migrations
php artisan migrate --force

# Verify migration status
php artisan migrate:status

# Create admin user
php artisan tinker
```

```php
// In Tinker console
use App\Models\User;

User::create([
    'name' => 'System Administrator',
    'email' => 'admin@mechamap.com',
    'username' => 'admin',
    'password' => bcrypt('your_secure_admin_password'),
    'role' => 'super_admin',
    'email_verified_at' => now(),
]);

exit;
```

## 🌐 **Web Server Configuration**

### **🔧 Nginx Configuration**
```nginx
# /etc/nginx/sites-available/mechamap.com
server {
    listen 80;
    server_name mechamap.com www.mechamap.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name mechamap.com www.mechamap.com;
    root /var/www/html/mechamap/public;
    index index.php;

    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/mechamap.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/mechamap.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512;
    ssl_prefer_server_ciphers off;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline';" always;

    # File Upload Limits
    client_max_body_size 10M;
    client_body_timeout 60s;

    # Gzip Compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml;

    # Main location block
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP-FPM Configuration
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    # Static Assets Caching
    location ~* \.(css|js|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        access_log off;
    }

    # Security - Block access to sensitive files
    location ~ /\. {
        deny all;
        access_log off;
        log_not_found off;
    }

    location ~ /(storage|bootstrap|config|database|resources|routes|tests|vendor) {
        deny all;
        access_log off;
        log_not_found off;
    }
}
```

### **⚡ Enable Nginx Site**
```bash
# Enable site
sudo ln -s /etc/nginx/sites-available/mechamap.com /etc/nginx/sites-enabled/

# Test configuration
sudo nginx -t

# Restart Nginx
sudo systemctl restart nginx
```

## ⚙️ **Queue Workers & Scheduler**

### **📋 Supervisor Configuration**
```ini
# /etc/supervisor/conf.d/mechamap-workers.conf
[program:mechamap-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/html/mechamap/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/html/mechamap/storage/logs/worker.log
stopwaitsecs=3600

[program:mechamap-scheduler]
process_name=%(program_name)s
command=php /var/www/html/mechamap/artisan schedule:work
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/www/html/mechamap/storage/logs/scheduler.log
```

### **🔄 Start Services**
```bash
# Update supervisor
sudo supervisorctl reread
sudo supervisorctl update

# Start workers
sudo supervisorctl start mechamap-worker:*
sudo supervisorctl start mechamap-scheduler

# Check status
sudo supervisorctl status
```

## 💾 **Backup Procedures**

### **📋 Pre-Deployment Backup**
```bash
#!/bin/bash
# /usr/local/bin/mechamap-backup.sh

BACKUP_DIR="/var/backups/mechamap"
DATE=$(date +%Y%m%d_%H%M%S)
DB_NAME="mechamap_production"
DB_USER="mechamap_backup"
DB_PASS="backup_password"

# Create backup directory
mkdir -p $BACKUP_DIR/$DATE

# Database backup
mysqldump -u$DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/$DATE/database.sql.gz

# Application files backup
tar -czf $BACKUP_DIR/$DATE/application.tar.gz -C /var/www/html mechamap

# Private storage backup
tar -czf $BACKUP_DIR/$DATE/storage.tar.gz -C /var/www/html/mechamap/storage app

echo "Backup completed: $BACKUP_DIR/$DATE"
```

### **⏰ Automated Backup Cron**
```bash
# Add to crontab
sudo crontab -e

# Daily backup at 2 AM
0 2 * * * /usr/local/bin/mechamap-backup.sh

# Laravel scheduler
* * * * * cd /var/www/html/mechamap && php artisan schedule:run >> /dev/null 2>&1
```

## 🔄 **Deployment Commands**

### **📋 Standard Deployment Process**
```bash
#!/bin/bash
# Standard deployment script

echo "🚀 Starting MechaMap deployment..."

# 1. Backup current version
/usr/local/bin/mechamap-backup.sh

# 2. Pull latest code
cd /var/www/html/mechamap
git pull origin main

# 3. Install/update dependencies
composer install --no-dev --optimize-autoloader

# 4. Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 5. Run migrations
php artisan migrate --force

# 6. Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Set permissions
sudo chown -R www-data:www-data /var/www/html/mechamap
sudo chmod -R 755 /var/www/html/mechamap
sudo chmod -R 775 storage bootstrap/cache

# 8. Restart services
sudo supervisorctl restart mechamap-worker:*
sudo systemctl reload nginx

echo "✅ Deployment completed successfully!"
```

## 🔙 **Rollback Procedures**

### **📋 Emergency Rollback**
```bash
#!/bin/bash
# Emergency rollback script

echo "🔄 Starting emergency rollback..."

# 1. Stop workers
sudo supervisorctl stop mechamap-worker:*

# 2. Restore from latest backup
LATEST_BACKUP=$(ls -t /var/backups/mechamap/ | head -1)
cd /var/www/html

# 3. Restore application files
sudo rm -rf mechamap_backup
sudo mv mechamap mechamap_backup
sudo tar -xzf /var/backups/mechamap/$LATEST_BACKUP/application.tar.gz

# 4. Restore database
mysql -u mechamap_user -p mechamap_production < /var/backups/mechamap/$LATEST_BACKUP/database.sql

# 5. Restore storage
sudo tar -xzf /var/backups/mechamap/$LATEST_BACKUP/storage.tar.gz -C mechamap/storage

# 6. Set permissions
sudo chown -R www-data:www-data mechamap
sudo chmod -R 755 mechamap
sudo chmod -R 775 mechamap/storage mechamap/bootstrap/cache

# 7. Restart services
sudo supervisorctl start mechamap-worker:*
sudo systemctl reload nginx

echo "✅ Rollback completed!"
```

## ✅ **Post-Deployment Verification Checklist**

### **🧪 Functionality Tests**
```bash
# Test application health
curl -f https://mechamap.com/health || echo "❌ Health check failed"

# Test database connectivity
cd /var/www/html/mechamap
php artisan tinker --execute="DB::connection()->getPdo(); echo 'Database OK';"

# Test Redis connectivity
php artisan tinker --execute="Redis::ping(); echo 'Redis OK';"

# Test email functionality
php artisan tinker --execute="Mail::raw('Test', function(\$m) { \$m->to('admin@mechamap.com')->subject('Deploy Test'); });"

# Test business verification system
php artisan mechamap:verify-phase4
```

### **🔒 Security Verification**
```bash
# SSL certificate check
openssl s_client -connect mechamap.com:443 -servername mechamap.com

# Security headers check
curl -I https://mechamap.com/

# File permissions check
find /var/www/html/mechamap -type f -perm /o+w

# Check for exposed sensitive files
curl -f https://mechamap.com/.env && echo "❌ .env exposed!"
curl -f https://mechamap.com/composer.json && echo "❌ composer.json exposed!"
```

### **📊 Performance Verification**
```bash
# Response time check
curl -w "@curl-format.txt" -o /dev/null -s https://mechamap.com/

# Database performance
mysql -u mechamap_user -p -e "SHOW PROCESSLIST; SHOW ENGINE INNODB STATUS\G"

# Redis performance
redis-cli --latency-history -i 1
```

## 🆘 **Troubleshooting**

### **🔧 Common Issues**

#### **Database Connection Issues**
```bash
# Check MySQL service
sudo systemctl status mysql

# Check database credentials
mysql -u mechamap_user -p mechamap_production

# Check Laravel database config
php artisan tinker --execute="config('database.connections.mysql')"
```

#### **Permission Issues**
```bash
# Fix storage permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Fix general permissions
sudo chown -R www-data:www-data /var/www/html/mechamap
sudo chmod -R 755 /var/www/html/mechamap
```

#### **Queue Worker Issues**
```bash
# Check supervisor status
sudo supervisorctl status

# Restart workers
sudo supervisorctl restart mechamap-worker:*

# Check worker logs
tail -f /var/www/html/mechamap/storage/logs/worker.log
```

### **📞 Emergency Contacts**
- **Technical Lead**: tech@mechamap.com
- **System Administrator**: admin@mechamap.com
- **Hosting Provider**: [Provider Support]
- **Domain Registrar**: [Registrar Support]

---

**© 2025 MechaMap. Production Deployment Guide for Business Verification Platform.**
