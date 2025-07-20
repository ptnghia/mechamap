# MechaMap Shared Hosting Deployment Guide

Hướng dẫn deploy MechaMap trên shared hosting với SSL được quản lý bởi hosting provider.

## 🎯 **Shared Hosting Requirements**

### **Hosting Features Required:**
- ✅ **PHP 8.2+** with required extensions
- ✅ **MySQL 8.0+** database
- ✅ **SSL Certificate** (managed by hosting)
- ✅ **Node.js support** (for realtime server)
- ✅ **Cron jobs** support
- ✅ **File upload** (20MB+)
- ✅ **Custom domains** support

### **PHP Extensions Required:**
```
php-mysql, php-curl, php-gd, php-mbstring, php-xml, 
php-zip, php-intl, php-bcmath, php-opcache
```

## 🚀 **Deployment Steps**

### **Step 1: Upload Files**

```bash
# 1. Upload Laravel files to hosting
# - Upload all files to public_html/ or domain folder
# - Ensure .env.production is renamed to .env

# 2. Set correct file permissions
chmod 755 public_html/
chmod 644 public_html/.env
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
chmod -R 775 public/images/
```

### **Step 2: Database Setup**

```sql
-- Create database via hosting control panel
-- Database name: mechamap_production
-- Import database structure and data
```

### **Step 3: Environment Configuration**

Update `.env` file with hosting-specific values:

```bash
# Application
APP_ENV=production
APP_DEBUG=false
APP_URL=https://mechamap.com

# Database - Update with hosting credentials
DB_HOST=localhost
DB_DATABASE=your_database_name
DB_USERNAME=your_db_username
DB_PASSWORD=your_db_password

# Cache & Sessions - File-based (no Redis)
CACHE_STORE=file
SESSION_DRIVER=database
QUEUE_CONNECTION=database

# Mail - Update with hosting SMTP
MAIL_HOST=mail.mechamap.com
MAIL_USERNAME=noreply@mechamap.com
MAIL_PASSWORD=your_email_password

# WebSocket - Update with your domain
WEBSOCKET_SERVER_URL=https://mechamap.com:3000
# Or if using subdomain:
# WEBSOCKET_SERVER_URL=https://realtime.mechamap.com

# Payment Gateways - Production keys
STRIPE_KEY=pk_live_your_stripe_key
STRIPE_SECRET=sk_live_your_stripe_secret
SEPAY_ACCOUNT_NUMBER=your_sepay_account
```

### **Step 4: Laravel Setup Commands**

Run these commands via hosting terminal or SSH:

```bash
# 1. Install dependencies (if composer is available)
composer install --no-dev --optimize-autoloader

# 2. Generate application key (if needed)
php artisan key:generate

# 3. Create required database tables
php artisan session:table
php artisan queue:table
php artisan queue:failed-table
php artisan migrate --force

# 4. Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 5. Create storage link
php artisan storage:link
```

### **Step 5: Realtime Server Setup**

If hosting supports Node.js:

```bash
# 1. Navigate to realtime-server directory
cd realtime-server/

# 2. Install dependencies
npm install --omit=dev

# 3. Copy production config
cp .env.production .env

# 4. Update .env with hosting-specific values
# LARAVEL_API_URL=https://mechamap.com
# DB_HOST=localhost (same as Laravel)

# 5. Start server (method depends on hosting)
# Option A: PM2 (if available)
pm2 start src/app.js --name mechamap-realtime

# Option B: Forever (if available)
forever start src/app.js

# Option C: Background process
nohup node src/app.js > realtime.log 2>&1 &
```

### **Step 6: Cron Jobs Setup**

Setup via hosting control panel:

```bash
# Laravel Scheduler (every minute)
* * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1

# Queue Worker (every 5 minutes - restart if needed)
*/5 * * * * cd /path/to/your/project && php artisan queue:restart && php artisan queue:work --stop-when-empty

# Optional: Clear cache daily
0 2 * * * cd /path/to/your/project && php artisan cache:clear
```

## 🔧 **Hosting-Specific Configurations**

### **cPanel Hosting:**
- Use **File Manager** to upload files
- Use **MySQL Databases** to create database
- Use **Cron Jobs** to setup schedulers
- Use **SSL/TLS** is usually auto-enabled

### **Plesk Hosting:**
- Use **File Manager** or **Git** to deploy
- Use **Databases** to setup MySQL
- Use **Scheduled Tasks** for cron jobs
- **SSL certificates** are auto-managed

### **DirectAdmin Hosting:**
- Upload via **File Manager**
- Create database via **MySQL Management**
- Setup **Cronjobs** for schedulers
- **SSL** is typically included

## 🔒 **Security for Shared Hosting**

### **File Permissions:**
```bash
# Secure file permissions
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
chmod -R 775 public/images/
chmod 600 .env
```

### **Hide Sensitive Files:**
Create `.htaccess` in root directory:

```apache
# Hide sensitive files
<Files ".env">
    Order allow,deny
    Deny from all
</Files>

<Files "composer.json">
    Order allow,deny
    Deny from all
</Files>

<Files "composer.lock">
    Order allow,deny
    Deny from all
</Files>

# Redirect to public folder
RewriteEngine On
RewriteRule ^(.*)$ public/$1 [L]
```

## 📊 **Performance Optimization**

### **Enable OPcache:**
Add to `.htaccess` or ask hosting to enable:

```apache
# Enable OPcache
php_value opcache.enable 1
php_value opcache.memory_consumption 128
php_value opcache.max_accelerated_files 4000
```

### **File Compression:**
Add to `.htaccess`:

```apache
# Enable Gzip compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>

# Browser caching
<IfModule mod_expires.c>
    ExpiresActive on
    ExpiresByType text/css "access plus 1 year"
    ExpiresByType application/javascript "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/ico "access plus 1 year"
    ExpiresByType image/svg+xml "access plus 1 year"
</IfModule>
```

## 🐛 **Troubleshooting**

### **Common Issues:**

1. **500 Internal Server Error**
   - Check file permissions
   - Check `.env` configuration
   - Check error logs in hosting control panel

2. **Database Connection Error**
   - Verify database credentials in `.env`
   - Check if database exists
   - Ensure database user has proper permissions

3. **WebSocket Not Working**
   - Check if hosting supports Node.js
   - Verify realtime server is running
   - Check firewall/port restrictions

4. **File Upload Issues**
   - Check `upload_max_filesize` in PHP settings
   - Verify storage directory permissions
   - Check disk space quota

### **Debug Commands:**
```bash
# Check Laravel configuration
php artisan about
php artisan config:show

# Check database connection
php artisan migrate:status

# Check queue status
php artisan queue:work --once

# Check storage permissions
ls -la storage/
```

## ✅ **Deployment Checklist**

- [ ] Files uploaded to hosting
- [ ] Database created and configured
- [ ] `.env` file updated with production values
- [ ] Database migrations run
- [ ] Laravel optimized for production
- [ ] Storage link created
- [ ] File permissions set correctly
- [ ] Cron jobs configured
- [ ] Realtime server running (if supported)
- [ ] SSL certificate active (managed by hosting)
- [ ] Website accessible at https://mechamap.com
- [ ] Admin panel working
- [ ] Payment gateways tested
- [ ] Email sending tested

## 🎉 **Success!**

Your MechaMap application is now running on shared hosting with:
- ✅ SSL managed by hosting provider
- ✅ Database-based sessions and queues
- ✅ File-based caching
- ✅ Production-optimized configuration
- ✅ Security hardening applied

**No manual SSL certificate management required!** 🔒
