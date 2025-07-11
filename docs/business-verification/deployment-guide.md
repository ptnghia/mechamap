# 🚀 Deployment Guide - Business Verification Platform

**Production Deployment Guide for MechaMap Business Verification System**

[![Production Ready](https://img.shields.io/badge/Status-Production%20Ready-green.svg)](README.md)
[![Laravel 11](https://img.shields.io/badge/Laravel-11.x-red.svg)](#requirements)
[![PHP 8.2+](https://img.shields.io/badge/PHP-8.2+-blue.svg)](#requirements)

---

## 🎯 **Deployment Overview**

Hướng dẫn này cung cấp các bước chi tiết để deploy MechaMap Business Verification Platform lên production environment với enterprise-grade security và performance.

### **🏗️ Architecture Overview**

```
┌─────────────────────────────────────────────────────────────┐
│                    Production Architecture                  │
├─────────────────────────────────────────────────────────────┤
│  🌐 Load Balancer (Nginx/HAProxy)                         │
│  ├── SSL Termination                                       │
│  ├── Rate Limiting                                         │
│  └── Health Checks                                         │
├─────────────────────────────────────────────────────────────┤
│  🖥️ Application Servers (Multiple Instances)              │
│  ├── Laravel 11 Application                                │
│  ├── PHP 8.2+ with OPcache                                │
│  └── Queue Workers                                         │
├─────────────────────────────────────────────────────────────┤
│  🗄️ Database Layer                                         │
│  ├── MySQL 8.0+ (Master/Slave)                            │
│  ├── Redis (Cache & Sessions)                              │
│  └── Backup Storage                                        │
├─────────────────────────────────────────────────────────────┤
│  📁 File Storage                                           │
│  ├── Private Document Storage                              │
│  ├── Encrypted Backups                                     │
│  └── CDN for Static Assets                                 │
└─────────────────────────────────────────────────────────────┘
```

## 📋 **System Requirements**

### **🖥️ Server Specifications**

#### **Production Environment (Recommended)**
```
Application Server:
├── CPU: 8 cores (Intel Xeon or AMD EPYC)
├── RAM: 32GB DDR4
├── Storage: 500GB NVMe SSD
├── Network: 1Gbps
└── OS: Ubuntu 22.04 LTS or CentOS 8

Database Server:
├── CPU: 16 cores
├── RAM: 64GB DDR4
├── Storage: 1TB NVMe SSD (RAID 10)
├── Network: 10Gbps
└── Backup: 2TB for automated backups
```

#### **Minimum Requirements**
```
Single Server Setup:
├── CPU: 4 cores
├── RAM: 16GB
├── Storage: 200GB SSD
├── Network: 100Mbps
└── OS: Ubuntu 20.04+ or CentOS 7+
```

### **💻 Software Requirements**

```bash
Required Software Stack:
├── PHP 8.2+ with extensions:
│   ├── php-fpm, php-mysql, php-redis
│   ├── php-gd, php-imagick, php-zip
│   ├── php-curl, php-xml, php-mbstring
│   └── php-intl, php-bcmath, php-opcache
├── MySQL 8.0+ or MariaDB 10.6+
├── Redis 6.0+
├── Nginx 1.20+ or Apache 2.4+
├── Composer 2.x
├── Node.js 18+ & NPM
└── Supervisor (for queue workers)
```

## 🔧 **Pre-Deployment Setup**

### **🔐 Security Preparation**

#### **1. SSL Certificate Setup**
```bash
# Using Let's Encrypt
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d mechapap.com -d www.mechapap.com

# Or upload commercial SSL certificate
sudo cp your-certificate.crt /etc/ssl/certs/
sudo cp your-private-key.key /etc/ssl/private/
sudo chmod 600 /etc/ssl/private/your-private-key.key
```

#### **2. Firewall Configuration**
```bash
# UFW Firewall Setup
sudo ufw enable
sudo ufw allow 22/tcp    # SSH
sudo ufw allow 80/tcp    # HTTP
sudo ufw allow 443/tcp   # HTTPS
sudo ufw deny 3306/tcp   # MySQL (internal only)
sudo ufw deny 6379/tcp   # Redis (internal only)
```

#### **3. User & Permissions Setup**
```bash
# Create application user
sudo adduser --system --group --home /var/www mechamap
sudo usermod -a -G www-data mechamap

# Set directory permissions
sudo mkdir -p /var/www/mechamap
sudo chown -R mechamap:www-data /var/www/mechamap
sudo chmod -R 755 /var/www/mechamap
```

### **🗄️ Database Setup**

#### **1. MySQL Installation & Configuration**
```bash
# Install MySQL 8.0
sudo apt update
sudo apt install mysql-server-8.0

# Secure installation
sudo mysql_secure_installation

# Create database and user
sudo mysql -u root -p
```

```sql
-- Create database
CREATE DATABASE mechamap_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create user with limited privileges
CREATE USER 'mechamap_user'@'localhost' IDENTIFIED BY 'secure_password_here';
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, ALTER, INDEX, DROP 
ON mechamap_production.* TO 'mechamap_user'@'localhost';

-- Create backup user
CREATE USER 'mechamap_backup'@'localhost' IDENTIFIED BY 'backup_password_here';
GRANT SELECT, LOCK TABLES ON mechamap_production.* TO 'mechamap_backup'@'localhost';

FLUSH PRIVILEGES;
```

#### **2. MySQL Performance Tuning**
```ini
# /etc/mysql/mysql.conf.d/mysqld.cnf
[mysqld]
# Performance settings
innodb_buffer_pool_size = 16G
innodb_log_file_size = 1G
innodb_flush_log_at_trx_commit = 2
innodb_flush_method = O_DIRECT

# Security settings
bind-address = 127.0.0.1
skip-networking = false
local-infile = 0

# Logging
slow_query_log = 1
slow_query_log_file = /var/log/mysql/slow.log
long_query_time = 2
```

#### **3. Redis Installation & Configuration**
```bash
# Install Redis
sudo apt install redis-server

# Configure Redis
sudo nano /etc/redis/redis.conf
```

```ini
# /etc/redis/redis.conf
bind 127.0.0.1
port 6379
requirepass your_redis_password_here
maxmemory 8gb
maxmemory-policy allkeys-lru
save 900 1
save 300 10
save 60 10000
```

## 📦 **Application Deployment**

### **🔄 Deployment Process**

#### **1. Code Deployment**
```bash
# Switch to application user
sudo su - mechamap

# Clone repository
cd /var/www
git clone https://github.com/your-org/mechamap-backend.git mechamap
cd mechamap

# Checkout production branch
git checkout production

# Install dependencies
composer install --no-dev --optimize-autoloader
npm ci --production
npm run build
```

#### **2. Environment Configuration**
```bash
# Copy environment file
cp .env.production .env

# Generate application key
php artisan key:generate

# Set proper permissions
sudo chown -R mechamap:www-data /var/www/mechamap
sudo chmod -R 755 /var/www/mechamap
sudo chmod -R 775 /var/www/mechamap/storage
sudo chmod -R 775 /var/www/mechamap/bootstrap/cache
```

#### **3. Production Environment Variables**
```bash
# /var/www/mechamap/.env
APP_NAME="MechaMap Business Verification"
APP_ENV=production
APP_KEY=base64:your-generated-key-here
APP_DEBUG=false
APP_URL=https://mechapap.com

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mechamap_production
DB_USERNAME=mechamap_user
DB_PASSWORD=secure_password_here

# Redis Configuration
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=your_redis_password_here
REDIS_PORT=6379
REDIS_DB=0

# Cache Configuration
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=noreply@mechapap.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@mechapap.com
MAIL_FROM_NAME="MechaMap"

# Security Settings
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=strict

# File Storage
FILESYSTEM_DISK=private
PRIVATE_STORAGE_PATH=/var/www/mechamap/storage/app/private

# Security Monitoring
SECURITY_MONITORING_ENABLED=true
THREAT_DETECTION_LEVEL=high
```

### **🗄️ Database Migration**

#### **1. Run Migrations**
```bash
# Run database migrations
php artisan migrate --force

# Seed initial data (if needed)
php artisan db:seed --class=ProductionSeeder

# Verify database structure
php artisan migrate:status
```

#### **2. Create Admin User**
```bash
# Create initial admin user
php artisan make:admin-user \
    --name="System Administrator" \
    --email="admin@mechapap.com" \
    --password="secure_admin_password" \
    --role="super_admin"
```

### **🔧 Web Server Configuration**

#### **1. Nginx Configuration**
```nginx
# /etc/nginx/sites-available/mechapap.com
server {
    listen 80;
    server_name mechapap.com www.mechapap.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name mechapap.com www.mechapap.com;
    root /var/www/mechamap/public;
    index index.php;

    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/mechapap.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/mechapap.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512;
    ssl_prefer_server_ciphers off;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline';" always;

    # Rate Limiting
    limit_req_zone $binary_remote_addr zone=api:10m rate=60r/m;
    limit_req_zone $binary_remote_addr zone=login:10m rate=5r/m;

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

    # API Rate Limiting
    location /api/ {
        limit_req zone=api burst=20 nodelay;
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Login Rate Limiting
    location /login {
        limit_req zone=login burst=3 nodelay;
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Static Assets
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

#### **2. PHP-FPM Configuration**
```ini
# /etc/php/8.2/fpm/pool.d/mechamap.conf
[mechamap]
user = mechamap
group = www-data
listen = /var/run/php/php8.2-fpm-mechamap.sock
listen.owner = www-data
listen.group = www-data
listen.mode = 0660

pm = dynamic
pm.max_children = 50
pm.start_servers = 10
pm.min_spare_servers = 5
pm.max_spare_servers = 20
pm.max_requests = 1000

# Security
php_admin_value[disable_functions] = exec,passthru,shell_exec,system,proc_open,popen
php_admin_flag[allow_url_fopen] = off
php_admin_flag[allow_url_include] = off

# Performance
php_value[memory_limit] = 512M
php_value[max_execution_time] = 300
php_value[upload_max_filesize] = 10M
php_value[post_max_size] = 10M
```

### **⚙️ Queue Workers Setup**

#### **1. Supervisor Configuration**
```ini
# /etc/supervisor/conf.d/mechamap-workers.conf
[program:mechamap-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/mechamap/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=mechamap
numprocs=4
redirect_stderr=true
stdout_logfile=/var/www/mechamap/storage/logs/worker.log
stopwaitsecs=3600

[program:mechamap-scheduler]
process_name=%(program_name)s
command=php /var/www/mechamap/artisan schedule:work
autostart=true
autorestart=true
user=mechamap
redirect_stderr=true
stdout_logfile=/var/www/mechamap/storage/logs/scheduler.log
```

#### **2. Start Services**
```bash
# Reload supervisor configuration
sudo supervisorctl reread
sudo supervisorctl update

# Start workers
sudo supervisorctl start mechamap-worker:*
sudo supervisorctl start mechamap-scheduler

# Check status
sudo supervisorctl status
```

## 🔒 **Security Hardening**

### **🛡️ Application Security**

#### **1. File Permissions**
```bash
# Set secure permissions
sudo find /var/www/mechamap -type f -exec chmod 644 {} \;
sudo find /var/www/mechamap -type d -exec chmod 755 {} \;
sudo chmod -R 775 /var/www/mechamap/storage
sudo chmod -R 775 /var/www/mechamap/bootstrap/cache
sudo chmod 600 /var/www/mechamap/.env
```

#### **2. Log Rotation**
```bash
# /etc/logrotate.d/mechamap
/var/www/mechamap/storage/logs/*.log {
    daily
    missingok
    rotate 52
    compress
    delaycompress
    notifempty
    create 644 mechamap www-data
    postrotate
        sudo supervisorctl restart mechamap-worker:*
    endscript
}
```

### **📊 Monitoring Setup**

#### **1. Health Check Endpoint**
```bash
# Test application health
curl -f https://mechapap.com/health || exit 1

# Database connectivity check
php artisan tinker --execute="DB::connection()->getPdo();"

# Redis connectivity check
php artisan tinker --execute="Redis::ping();"
```

#### **2. Log Monitoring**
```bash
# Monitor application logs
tail -f /var/www/mechamap/storage/logs/laravel.log

# Monitor security incidents
tail -f /var/www/mechamap/storage/logs/security.log

# Monitor worker logs
tail -f /var/www/mechamap/storage/logs/worker.log
```

## 🔄 **Backup & Recovery**

### **💾 Automated Backup Script**
```bash
#!/bin/bash
# /usr/local/bin/mechamap-backup.sh

BACKUP_DIR="/var/backups/mechamap"
DATE=$(date +%Y%m%d_%H%M%S)
DB_NAME="mechamap_production"
DB_USER="mechamap_backup"
DB_PASS="backup_password_here"

# Create backup directory
mkdir -p $BACKUP_DIR/$DATE

# Database backup
mysqldump -u$DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/$DATE/database.sql.gz

# Application files backup
tar -czf $BACKUP_DIR/$DATE/application.tar.gz -C /var/www mechamap

# Private storage backup
tar -czf $BACKUP_DIR/$DATE/storage.tar.gz -C /var/www/mechamap/storage app

# Cleanup old backups (keep 30 days)
find $BACKUP_DIR -type d -mtime +30 -exec rm -rf {} \;

echo "Backup completed: $BACKUP_DIR/$DATE"
```

### **⏰ Cron Job Setup**
```bash
# Add to crontab
sudo crontab -e

# Daily backup at 2 AM
0 2 * * * /usr/local/bin/mechamap-backup.sh

# Laravel scheduler
* * * * * cd /var/www/mechamap && php artisan schedule:run >> /dev/null 2>&1
```

## ✅ **Post-Deployment Verification**

### **🧪 System Tests**
```bash
# Run application tests
cd /var/www/mechamap
php artisan test --env=production

# Verify business verification system
php artisan mechamap:verify-phase4

# Check queue workers
php artisan queue:monitor

# Verify email functionality
php artisan tinker --execute="Mail::raw('Test email', function(\$message) { \$message->to('admin@mechapap.com')->subject('Deployment Test'); });"
```

### **📊 Performance Verification**
```bash
# Check application response time
curl -w "@curl-format.txt" -o /dev/null -s https://mechapap.com/

# Database performance check
mysql -u mechamap_user -p -e "SHOW PROCESSLIST; SHOW ENGINE INNODB STATUS\G"

# Redis performance check
redis-cli --latency-history -i 1
```

### **🔒 Security Verification**
```bash
# SSL certificate check
openssl s_client -connect mechapap.com:443 -servername mechapap.com

# Security headers check
curl -I https://mechapap.com/

# File permissions check
find /var/www/mechamap -type f -perm /o+w -exec ls -la {} \;
```

---

**© 2025 MechaMap. Production Deployment Guide.**
