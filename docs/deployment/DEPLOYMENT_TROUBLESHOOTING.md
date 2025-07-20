# MechaMap Deployment Troubleshooting Guide

Common issues and solutions during MechaMap deployment.

## 🚨 **NPM Installation Errors**

### **Error: `npm error enoent Could not read package.json`**

```bash
npm error path /var/www/mechamap.com/package.json
npm error errno -2
npm error enoent Could not read package.json
```

**❌ Problem**: Running `npm install` in Laravel root directory (which doesn't have package.json)

**✅ Solution**: 
```bash
# DON'T run npm install in Laravel root
# Laravel root directory doesn't have package.json

# ONLY run npm install in realtime-server directory
cd realtime-server/
npm install --omit=dev

# Then go back to Laravel root
cd ..
```

### **Error: `npm warn config production Use --omit=dev instead`**

**❌ Old command**: `npm install --production`
**✅ New command**: `npm install --omit=dev`

## 🗄️ **Database Connection Errors**

### **Error: `SQLSTATE[HY000] [1045] Access denied`**

**✅ Solutions**:
```bash
# 1. Check database credentials in .env
DB_HOST=localhost
DB_DATABASE=your_actual_database_name
DB_USERNAME=your_actual_username
DB_PASSWORD=your_actual_password

# 2. Test database connection
php artisan tinker
>>> DB::connection()->getPdo();

# 3. Create database if not exists
mysql -u root -p
CREATE DATABASE mechamap_production;
```

### **Error: `Base table or view not found`**

**✅ Solutions**:
```bash
# 1. Run migrations
php artisan migrate --force

# 2. Create required tables for sessions/queues
php artisan session:table
php artisan queue:table
php artisan queue:failed-table
php artisan migrate --force
```

## 🔑 **Laravel Setup Errors**

### **Error: `No application encryption key has been specified`**

**✅ Solution**:
```bash
# Generate new application key
php artisan key:generate --force

# Or copy from existing .env if you have one
# APP_KEY=base64:your_existing_key_here
```

### **Error: `The storage link could not be created`**

**✅ Solutions**:
```bash
# 1. Remove existing link if exists
rm public/storage

# 2. Create storage link
php artisan storage:link

# 3. Check file permissions
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

## 🔌 **Realtime Server Errors**

### **Error: `Cannot find module 'express'`**

**✅ Solution**:
```bash
# Make sure you're in realtime-server directory
cd realtime-server/

# Install dependencies
npm install --omit=dev

# Check if node_modules exists
ls -la node_modules/
```

### **Error: `Port 3000 already in use`**

**✅ Solutions**:
```bash
# 1. Find process using port 3000
netstat -tulpn | grep :3000
# or
lsof -i :3000

# 2. Kill the process
kill -9 PID_NUMBER

# 3. Or use different port in .env
PORT=3001
```

## 📁 **File Permission Errors**

### **Error: `Permission denied` when writing to storage**

**✅ Solutions**:
```bash
# 1. Set correct ownership
chown -R www-data:www-data storage/
chown -R www-data:www-data bootstrap/cache/
chown -R www-data:www-data public/images/

# 2. Set correct permissions
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
chmod -R 775 public/images/

# 3. For shared hosting
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chmod -R 755 public/images/
```

### **Error: `Failed to upload image` or `Permission denied` for images**

**✅ Solutions**:
```bash
# 1. Create images directories if not exist
php setup_images_directories.php

# 2. Set correct permissions for images
chmod -R 775 public/images/
chown -R www-data:www-data public/images/

# 3. Check if directories exist
ls -la public/images/
ls -la public/images/users/avatars/
ls -la public/images/threads/attachments/

# 4. Test write permissions
touch public/images/test.txt
rm public/images/test.txt
```

### **Error: `Failed to open stream: Permission denied` for .env**

**✅ Solution**:
```bash
# Set correct .env permissions
chmod 600 .env
chown www-data:www-data .env
```

## 🌐 **Web Server Errors**

### **Error: `500 Internal Server Error`**

**✅ Debug steps**:
```bash
# 1. Check Laravel logs
tail -f storage/logs/laravel.log

# 2. Check web server error logs
tail -f /var/log/nginx/error.log
# or
tail -f /var/log/apache2/error.log

# 3. Enable debug temporarily
APP_DEBUG=true
php artisan config:clear

# 4. Check file permissions
ls -la storage/
ls -la bootstrap/cache/
```

### **Error: `404 Not Found` for all routes**

**✅ Solutions**:
```bash
# 1. Check if .htaccess exists in public/
ls -la public/.htaccess

# 2. For Nginx, check configuration
# Make sure try_files directive is correct:
# try_files $uri $uri/ /index.php?$query_string;

# 3. Clear route cache
php artisan route:clear
php artisan route:cache
```

## 💳 **Payment Gateway Errors**

### **Error: `Invalid API key provided`**

**✅ Solutions**:
```bash
# 1. Check if using correct keys for environment
# Development: pk_test_... / sk_test_...
# Production: pk_live_... / sk_live_...

# 2. Update .env with correct keys
STRIPE_KEY=pk_live_your_production_key
STRIPE_SECRET=sk_live_your_production_secret

# 3. Clear config cache
php artisan config:clear
php artisan config:cache
```

## 🔌 **WebSocket Component Errors**

### **Error: `Undefined variable $configJson` or `Using $this when not in object context`**

```
Undefined variable $configJson resources/views/components/websocket-config.blade.php:4
Using $this when not in object context
```

**❌ Problem**: Blade component variable not passed correctly

**✅ Solution**:
```bash
# 1. Fix blade template variable reference
# Use: {!! $configJson !!}
# NOT: {!! $configJson() !!} or {!! $this->configJson() !!}

# 2. Ensure component passes variable in render() method
# Component should pass 'configJson' => $this->configJson()

# 3. Clear view cache
php artisan view:clear

# 4. Clear config cache
php artisan config:clear

# 5. Test component
php test_websocket_component.php
```

### **Error: `Undefined variable` in WebSocket component**

**✅ Solutions**:
```bash
# 1. Check component class exists
ls -la app/View/Components/WebSocketConfig.php

# 2. Verify component registration
php artisan about

# 3. Clear all caches
php artisan optimize:clear

# 4. Check view file syntax
cat resources/views/components/websocket-config.blade.php
```

## 📧 **Email Configuration Errors**

### **Error: `Connection could not be established with host`**

**✅ Solutions**:
```bash
# 1. Check SMTP settings
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_ENCRYPTION=tls

# 2. Test email configuration
php artisan tinker
>>> Mail::raw('Test email', function($msg) { $msg->to('test@example.com')->subject('Test'); });

# 3. Check firewall/port restrictions
telnet smtp.gmail.com 587
```

## 🔧 **Quick Deployment Script**

Use the automated deployment script to avoid common errors:

```bash
# Make script executable
chmod +x deploy_production.sh

# Run deployment script
./deploy_production.sh
```

This script will:
- ✅ Install dependencies in correct directories
- ✅ Setup environment files
- ✅ Create database tables
- ✅ Set file permissions
- ✅ Optimize for production

## 📞 **Getting Help**

If you encounter issues not covered here:

1. **Check logs first**:
   ```bash
   tail -f storage/logs/laravel.log
   tail -f realtime-server/logs/app.log
   ```

2. **Test individual components**:
   ```bash
   php artisan about
   php artisan config:show
   php artisan migrate:status
   ```

3. **Verify file structure**:
   ```bash
   ls -la
   ls -la realtime-server/
   ls -la storage/
   ```

4. **Check service status**:
   ```bash
   systemctl status nginx
   systemctl status mysql
   ps aux | grep node
   ```

## ✅ **Deployment Success Checklist**

- [ ] Laravel dependencies installed (`composer install`)
- [ ] Realtime server dependencies installed (`cd realtime-server && npm install`)
- [ ] Environment files configured (`.env` and `realtime-server/.env`)
- [ ] Database connected and migrated
- [ ] Storage link created
- [ ] File permissions set correctly
- [ ] Laravel optimized for production
- [ ] Realtime server running (if applicable)
- [ ] Website accessible via HTTPS
- [ ] Admin panel working
- [ ] No errors in logs

**Remember**: Always run `npm install` in the `realtime-server/` directory, not in the Laravel root directory! 🎯
