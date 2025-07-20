# MechaMap Production Deployment Guide

Hướng dẫn chi tiết để deploy MechaMap lên production server.

## 🎯 **Production Environment Overview**

### **Domains:**
- **Main Website**: `https://mechamap.com`
- **Realtime Server**: `https://realtime.mechamap.com`
- **CDN (Optional)**: `https://cdn.mechamap.com`

### **Infrastructure Requirements:**
- **Web Server**: Nginx + PHP-FPM 8.2+
- **Database**: MySQL 8.0+ / MariaDB 10.6+
- **Cache**: File-based cache (Redis disabled)
- **Queue**: Database-based queues
- **SSL**: Let's Encrypt / Custom certificates
- **Node.js**: v18+ for realtime server

## 🔧 **Pre-deployment Checklist**

### **1. Server Requirements:**
```bash
# PHP Extensions
php-fpm, php-mysql, php-redis, php-curl, php-gd, php-mbstring, 
php-xml, php-zip, php-intl, php-bcmath, php-opcache

# System Requirements
- RAM: 4GB minimum, 8GB recommended
- Storage: 50GB minimum
- CPU: 2 cores minimum
```

### **2. Environment Variables to Update:**

**CRITICAL - Must be changed for production:**

```bash
# Database - MUST UPDATE
DB_HOST=YOUR_PRODUCTION_DB_HOST
DB_DATABASE=mechamap_production
DB_USERNAME=mechamap_prod_user
DB_PASSWORD=YOUR_SECURE_PRODUCTION_DB_PASSWORD

# Redis - DISABLED (using file cache and database sessions/queues)

# Mail - MUST UPDATE
MAIL_USERNAME=noreply@mechamap.com
MAIL_PASSWORD=YOUR_PRODUCTION_EMAIL_PASSWORD

# WebSocket API - MUST UPDATE
WEBSOCKET_API_KEY_HASH=YOUR_PRODUCTION_WEBSOCKET_API_KEY_HASH

# Social Login - MUST UPDATE
GOOGLE_CLIENT_ID=YOUR_PRODUCTION_GOOGLE_CLIENT_ID
GOOGLE_CLIENT_SECRET=YOUR_PRODUCTION_GOOGLE_CLIENT_SECRET
FACEBOOK_CLIENT_ID=YOUR_PRODUCTION_FACEBOOK_CLIENT_ID
FACEBOOK_CLIENT_SECRET=YOUR_PRODUCTION_FACEBOOK_CLIENT_SECRET

# Payment Gateways - MUST UPDATE
STRIPE_KEY=pk_live_YOUR_PRODUCTION_STRIPE_PUBLISHABLE_KEY
STRIPE_SECRET=sk_live_YOUR_PRODUCTION_STRIPE_SECRET_KEY
STRIPE_WEBHOOK_SECRET=whsec_YOUR_PRODUCTION_STRIPE_WEBHOOK_SECRET
SEPAY_ACCOUNT_NUMBER=YOUR_PRODUCTION_SEPAY_ACCOUNT
SEPAY_WEBHOOK_SECRET=YOUR_PRODUCTION_SEPAY_WEBHOOK_SECRET

# Monitoring - OPTIONAL
SENTRY_LARAVEL_DSN=YOUR_SENTRY_DSN
NEW_RELIC_LICENSE_KEY=YOUR_NEW_RELIC_KEY
CLOUDFLARE_ZONE_ID=YOUR_CLOUDFLARE_ZONE_ID
CLOUDFLARE_API_TOKEN=YOUR_CLOUDFLARE_API_TOKEN
```

## 🚀 **Deployment Steps**

### **Step 1: Server Setup**

```bash
# 1. Update system
sudo apt update && sudo apt upgrade -y

# 2. Install required packages (Redis removed)
sudo apt install nginx mysql-server php8.2-fpm php8.2-mysql php8.2-curl php8.2-gd php8.2-mbstring php8.2-xml php8.2-zip php8.2-intl php8.2-bcmath php8.2-opcache

# 3. Install Node.js
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# 4. Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### **Step 2: Database Setup**

```sql
-- Create production database
CREATE DATABASE mechamap_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create production user
CREATE USER 'mechamap_prod_user'@'localhost' IDENTIFIED BY 'YOUR_SECURE_PASSWORD';
GRANT ALL PRIVILEGES ON mechamap_production.* TO 'mechamap_prod_user'@'localhost';
FLUSH PRIVILEGES;
```

### **Step 3: Application Deployment**

```bash
# 1. Clone repository
cd /var/www
sudo git clone https://github.com/your-repo/mechamap.git
sudo chown -R www-data:www-data mechamap
cd mechamap

# 2. Install Laravel dependencies
composer install --no-dev --optimize-autoloader

# 3. Install Node.js dependencies for realtime server
cd realtime-server
npm install --omit=dev
cd ..

# 3. Setup environment
sudo cp .env.production .env
sudo nano .env  # Update all production values

# 4. Generate application key (if needed)
php artisan key:generate

# 5. Run migrations
php artisan migrate --force

# 6. Build frontend assets (if needed)
# Note: Laravel project doesn't have package.json in root
# Frontend assets are already compiled in public/

# 7. Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 7. Set permissions
sudo chown -R www-data:www-data storage bootstrap/cache public/images
sudo chmod -R 775 storage bootstrap/cache public/images
```

### **Step 4: Nginx Configuration**

```nginx
# /etc/nginx/sites-available/mechamap.com
# Note: SSL certificates are managed by hosting provider

server {
    listen 80;
    server_name mechamap.com www.mechamap.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name mechamap.com www.mechamap.com;
    root /var/www/mechamap/public;
    index index.php;

    # SSL Configuration - Managed by hosting provider
    # SSL certificates, protocols, and ciphers are configured by hosting

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

    # Laravel Configuration
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

    # Asset optimization
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

### **Step 5: Realtime Server Setup**

```bash
# 1. Setup realtime server
cd /var/www/mechamap/realtime-server
npm install --omit=dev

# 2. Copy production config
cp .env.production .env

# 3. Create systemd service
sudo nano /etc/systemd/system/mechamap-realtime.service
```

```ini
[Unit]
Description=MechaMap Realtime Server
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/mechamap/realtime-server
Environment=NODE_ENV=production
ExecStart=/usr/bin/node src/app.js
Restart=on-failure
RestartSec=10

[Install]
WantedBy=multi-user.target
```

```bash
# 4. Start realtime server
sudo systemctl enable mechamap-realtime
sudo systemctl start mechamap-realtime
```

### **Step 6: SSL Configuration (Managed by Hosting)**

SSL certificates are managed by the hosting provider. No manual setup required.

**Note**: Ensure your hosting provider has SSL certificates configured for:
- `mechamap.com`
- `www.mechamap.com`
- `realtime.mechamap.com` (if using subdomain for realtime server)

### **Step 7: Queue Workers Setup**

```bash
# Create queue worker service
sudo nano /etc/systemd/system/mechamap-worker.service
```

```ini
[Unit]
Description=MechaMap Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/mechamap
ExecStart=/usr/bin/php artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
Restart=on-failure
RestartSec=10

[Install]
WantedBy=multi-user.target
```

```bash
sudo systemctl enable mechamap-worker
sudo systemctl start mechamap-worker
```

## 🔒 **Security Hardening**

### **1. Firewall Setup:**
```bash
sudo ufw allow ssh
sudo ufw allow 'Nginx Full'
sudo ufw enable
```

### **2. Fail2Ban:**
```bash
sudo apt install fail2ban
sudo systemctl enable fail2ban
```

### **3. File Permissions:**
```bash
sudo chown -R www-data:www-data /var/www/mechamap
sudo find /var/www/mechamap -type f -exec chmod 644 {} \;
sudo find /var/www/mechamap -type d -exec chmod 755 {} \;
sudo chmod -R 775 /var/www/mechamap/storage
sudo chmod -R 775 /var/www/mechamap/bootstrap/cache
```

## 📊 **Monitoring & Maintenance**

### **1. Log Monitoring:**
```bash
# Laravel logs
tail -f /var/www/mechamap/storage/logs/laravel.log

# Nginx logs
tail -f /var/log/nginx/access.log
tail -f /var/log/nginx/error.log

# Realtime server logs
sudo journalctl -u mechamap-realtime -f
```

### **2. Performance Monitoring:**
```bash
# Check services status (Redis removed)
sudo systemctl status nginx php8.2-fpm mysql mechamap-realtime mechamap-worker

# Monitor resources
htop
iotop
```

### **3. Backup Strategy:**
```bash
# Database backup (daily cron)
0 2 * * * mysqldump -u mechamap_prod_user -p mechamap_production > /backups/db_$(date +\%Y\%m\%d).sql

# File backup (weekly)
0 3 * * 0 tar -czf /backups/files_$(date +\%Y\%m\%d).tar.gz /var/www/mechamap
```

## 🚨 **Troubleshooting**

### **Common Issues:**

1. **500 Error**: Check Laravel logs and file permissions
2. **WebSocket not connecting**: Check realtime server status and SSL
3. **Slow performance**: Enable Redis cache and opcache
4. **Database connection**: Verify credentials and firewall

### **Health Check Commands:**
```bash
# Application health
php artisan about
php artisan config:show
php artisan route:list

# Services health (Redis removed)
sudo systemctl status mechamap-realtime
sudo systemctl status mechamap-worker
```

## ✅ **Post-Deployment Verification**

1. ✅ Website loads at `https://mechamap.com`
2. ✅ SSL certificate is valid
3. ✅ Database connections work
4. ✅ File cache is working
5. ✅ Queue workers are running
6. ✅ Realtime server is connected
7. ✅ Email sending works
8. ✅ File uploads work
9. ✅ Payment gateways work
10. ✅ Social login works

**Production deployment completed successfully!** 🎉
