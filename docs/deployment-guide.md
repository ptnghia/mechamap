# 🚀 MechaMap Production Deployment Guide

This guide provides step-by-step instructions for deploying MechaMap to production.

## 📋 Prerequisites

### System Requirements
- **PHP** ≥ 8.2 with extensions: OpenSSL, PDO, Mbstring, Tokenizer, XML, JSON, Ctype, BCMath, GD
- **MySQL** ≥ 8.0 or **PostgreSQL** ≥ 13
- **Composer** ≥ 2.0
- **Node.js** ≥ 18.0 & **NPM** ≥ 9.0
- **Redis** (recommended for cache & sessions)
- **Web Server**: Nginx or Apache
- **SSL Certificate** (Let's Encrypt recommended)

### Server Specifications (Recommended)
- **CPU**: 2+ cores
- **RAM**: 4GB+ (8GB recommended)
- **Storage**: 20GB+ SSD
- **Bandwidth**: 100Mbps+

## 🔧 Step 1: Server Setup

### 1.1 Update System
```bash
sudo apt update && sudo apt upgrade -y
```

### 1.2 Install Required Packages
```bash
# Install PHP 8.2 and extensions
sudo apt install php8.2 php8.2-fpm php8.2-mysql php8.2-redis php8.2-xml php8.2-gd php8.2-mbstring php8.2-curl php8.2-zip php8.2-bcmath php8.2-intl -y

# Install MySQL
sudo apt install mysql-server -y

# Install Redis
sudo apt install redis-server -y

# Install Nginx
sudo apt install nginx -y

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install nodejs -y
```

## 📦 Step 2: Deploy Application

### 2.1 Clone Repository
```bash
cd /var/www
sudo git clone https://github.com/yourusername/mechamap.git
sudo chown -R www-data:www-data mechamap
cd mechamap
```

### 2.2 Install Dependencies
```bash
# Install PHP dependencies (production)
composer install --optimize-autoloader --no-dev

# Install and build frontend assets
npm install
npm run build
```

### 2.3 Environment Configuration
```bash
# Copy production environment file
cp .env.production.example .env

# Generate application key
php artisan key:generate

# Edit environment variables
sudo nano .env
```

### 2.4 Configure .env File
```env
APP_NAME="MechaMap"
APP_ENV=production
APP_KEY=base64:YOUR_GENERATED_KEY
APP_DEBUG=false
APP_URL=https://mechamap.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mechamap_production
DB_USERNAME=mechamap_user
DB_PASSWORD=your_secure_password

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@mechamap.com
MAIL_FROM_NAME="MechaMap"

# Social Login (Optional)
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
GOOGLE_REDIRECT_URI=https://mechamap.com/auth/google/callback

FACEBOOK_CLIENT_ID=your-facebook-client-id
FACEBOOK_CLIENT_SECRET=your-facebook-client-secret
FACEBOOK_REDIRECT_URI=https://mechamap.com/auth/facebook/callback
```

## 🗄️ Step 3: Database Setup

### 3.1 Create Database and User
```bash
sudo mysql -u root -p
```

```sql
CREATE DATABASE mechamap_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'mechamap_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON mechamap_production.* TO 'mechamap_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 3.2 Run Migrations and Seeders
```bash
php artisan migrate --force
php artisan db:seed --class=ProductionSeeder
```

## 🌐 Step 4: Web Server Configuration

### 4.1 Nginx Configuration
Create `/etc/nginx/sites-available/mechamap`:

```nginx
server {
    listen 80;
    server_name mechamap.com www.mechamap.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name mechamap.com www.mechamap.com;
    root /var/www/mechamap/public;
    index index.php index.html;

    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/mechamap.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/mechamap.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512:ECDHE-RSA-AES256-GCM-SHA384:DHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;

    # Gzip Compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript application/javascript application/xml+rss application/json;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Cache static assets
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|woff|woff2)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

### 4.2 Enable Site
```bash
sudo ln -s /etc/nginx/sites-available/mechamap /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

## 🔒 Step 5: SSL Certificate

### 5.1 Install Certbot
```bash
sudo apt install certbot python3-certbot-nginx -y
```

### 5.2 Obtain SSL Certificate
```bash
sudo certbot --nginx -d mechamap.com -d www.mechamap.com
```

### 5.3 Auto-renewal
```bash
sudo crontab -e
# Add this line:
0 12 * * * /usr/bin/certbot renew --quiet
```

## ⚡ Step 6: Performance Optimization

### 6.1 Laravel Optimization
```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Create storage link
php artisan storage:link

# Optimize autoloader
composer dump-autoload --optimize
```

### 6.2 PHP-FPM Optimization
Edit `/etc/php/8.2/fpm/pool.d/www.conf`:

```ini
pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35
pm.max_requests = 500
```

### 6.3 Redis Configuration
Edit `/etc/redis/redis.conf`:

```conf
maxmemory 256mb
maxmemory-policy allkeys-lru
```

## 🔄 Step 7: Process Management

### 7.1 Queue Worker (Supervisor)
```bash
sudo apt install supervisor -y
```

Create `/etc/supervisor/conf.d/mechamap-worker.conf`:

```ini
[program:mechamap-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/mechamap/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/mechamap/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start mechamap-worker:*
```

## 📊 Step 8: Monitoring & Logging

### 8.1 Log Rotation
Create `/etc/logrotate.d/mechamap`:

```
/var/www/mechamap/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    notifempty
    create 0644 www-data www-data
}
```

### 8.2 Health Check Script
Create `/var/www/mechamap/health-check.sh`:

```bash
#!/bin/bash
curl -f https://mechamap.com/health || exit 1
```

## 🔐 Step 9: Security Hardening

### 9.1 File Permissions
```bash
sudo chown -R www-data:www-data /var/www/mechamap
sudo find /var/www/mechamap -type f -exec chmod 644 {} \;
sudo find /var/www/mechamap -type d -exec chmod 755 {} \;
sudo chmod -R 775 /var/www/mechamap/storage
sudo chmod -R 775 /var/www/mechamap/bootstrap/cache
```

### 9.2 Firewall Configuration
```bash
sudo ufw allow ssh
sudo ufw allow 'Nginx Full'
sudo ufw --force enable
```

## 🚀 Step 10: Final Steps

### 10.1 Restart Services
```bash
sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm
sudo systemctl restart redis-server
sudo systemctl restart mysql
```

### 10.2 Verify Deployment
- Visit `https://mechamap.com`
- Check admin panel: `https://mechamap.com/admin`
- Test key functionality
- Monitor logs for errors

## 📝 Post-Deployment Checklist

- [ ] SSL certificate is working
- [ ] All pages load correctly
- [ ] Admin panel is accessible
- [ ] Database connections are working
- [ ] Redis cache is functioning
- [ ] Queue workers are running
- [ ] Email sending is working
- [ ] File uploads are working
- [ ] Social login is configured (if used)
- [ ] Monitoring is set up
- [ ] Backups are configured

## 🔄 Maintenance

### Daily Tasks
- Monitor application logs
- Check queue worker status
- Verify backup completion

### Weekly Tasks
- Update system packages
- Review performance metrics
- Check SSL certificate status

### Monthly Tasks
- Update Laravel dependencies
- Review security logs
- Performance optimization review

## 🆘 Troubleshooting

### Common Issues

**500 Internal Server Error**
```bash
# Check Laravel logs
tail -f /var/www/mechamap/storage/logs/laravel.log

# Check Nginx error logs
tail -f /var/log/nginx/error.log
```

**Queue Jobs Not Processing**
```bash
# Check supervisor status
sudo supervisorctl status

# Restart queue workers
sudo supervisorctl restart mechamap-worker:*
```

**Database Connection Issues**
```bash
# Test database connection
php artisan tinker
DB::connection()->getPdo();
```

For additional support, refer to the [troubleshooting documentation](troubleshooting.md) or contact support@mechamap.com.
