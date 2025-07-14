# 🐧 MechaMap Ubuntu 22.04 Deployment Fix

Hướng dẫn khắc phục lỗi deployment trên Ubuntu 22.04

## ❌ Lỗi gặp phải

```
Method Illuminate\Routing\UrlGenerator::forceAssetUrl does not exist.
Script @php artisan package:discover --ansi handling the post-autoload-dump event returned with error code 1
```

## 🔍 Nguyên nhân

- Laravel version conflict với PHP 8.1+ trên Ubuntu 22.04
- Package dependencies không tương thích
- Cache cũ từ development environment

## 🛠️ Giải pháp chi tiết

### **Bước 1: Kiểm tra môi trường**

```bash
# Kiểm tra PHP version
php -v

# Kiểm tra Composer version
composer --version

# Kiểm tra Laravel version
cat composer.json | grep laravel/framework
```

### **Bước 2: Làm sạch hoàn toàn**

```bash
# Xóa vendor và cache
rm -rf vendor/
rm -rf composer.lock
rm -rf bootstrap/cache/*.php
rm -rf storage/framework/cache/data/*
rm -rf storage/framework/sessions/*
rm -rf storage/framework/views/*

# Clear Composer cache
composer clear-cache
```

### **Bước 3: Cập nhật Composer**

```bash
# Update Composer to latest
sudo composer self-update

# Hoặc nếu không có quyền sudo
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### **Bước 4: Install từng bước**

```bash
# 1. Install dependencies mà không chạy scripts
composer install --no-scripts --no-autoloader --no-dev

# 2. Generate autoloader
composer dump-autoload --optimize --no-dev

# 3. Copy environment file
cp .env.production .env

# 4. Generate APP_KEY
php artisan key:generate --force

# 5. Create storage link
php artisan storage:link

# 6. Set permissions
sudo chown -R www-data:www-data storage bootstrap/cache
chmod -R 755 storage bootstrap/cache
```

### **Bước 5: Cấu hình Database**

```bash
# Tạo database
sudo mysql -u root -p
```

```sql
CREATE DATABASE mechamap CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'mechamap_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON mechamap.* TO 'mechamap_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

```bash
# Cập nhật .env
nano .env
```

Cập nhật:
```env
DB_DATABASE=mechamap
DB_USERNAME=mechamap_user
DB_PASSWORD=your_secure_password
APP_URL=https://yourdomain.com
```

### **Bước 6: Import Database**

```bash
# Import database
mysql -u mechamap_user -p mechamap < data_v2_fixed.sql
```

### **Bước 7: Optimize cho Production**

```bash
# Cache configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Verify installation
php scripts/verify_production_config_no_redis.php
```

## 🚨 **Script tự động cho Ubuntu 22.04**

Tạo file `ubuntu-22-04-deploy.sh`:

```bash
#!/bin/bash

echo "🐧 MechaMap Ubuntu 22.04 Deployment"
echo "==================================="

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️ $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

# Check if running as root
if [ "$EUID" -eq 0 ]; then
    print_error "Please don't run this script as root!"
    print_warning "Create a deployment user instead:"
    echo "sudo adduser deployer"
    echo "sudo usermod -aG www-data deployer"
    echo "su - deployer"
    exit 1
fi

# Step 1: Clean environment
echo ""
echo "1️⃣ Cleaning environment..."
rm -rf vendor/ composer.lock
rm -rf bootstrap/cache/*.php
rm -rf storage/framework/cache/data/*
rm -rf storage/framework/sessions/*
rm -rf storage/framework/views/*
print_status "Environment cleaned"

# Step 2: Update Composer
echo ""
echo "2️⃣ Updating Composer..."
composer self-update 2>/dev/null || print_warning "Could not update Composer (permission issue)"
composer clear-cache
print_status "Composer updated"

# Step 3: Install dependencies
echo ""
echo "3️⃣ Installing dependencies..."
composer install --no-scripts --no-autoloader --no-dev
if [ $? -ne 0 ]; then
    print_error "Composer install failed!"
    exit 1
fi
print_status "Dependencies installed"

# Step 4: Generate autoloader
echo ""
echo "4️⃣ Generating autoloader..."
composer dump-autoload --optimize --no-dev
print_status "Autoloader generated"

# Step 5: Setup environment
echo ""
echo "5️⃣ Setting up environment..."
if [ ! -f .env ]; then
    cp .env.production .env
    print_status "Environment file created"
else
    print_warning "Environment file already exists"
fi

# Generate APP_KEY if needed
if ! grep -q "APP_KEY=base64:" .env 2>/dev/null; then
    php artisan key:generate --force
    print_status "APP_KEY generated"
fi

# Create storage link
if [ ! -L "public/storage" ]; then
    php artisan storage:link
    print_status "Storage link created"
fi

# Step 6: Set permissions
echo ""
echo "6️⃣ Setting permissions..."
chmod -R 755 storage bootstrap/cache
print_status "Permissions set"

# Step 7: Database setup prompt
echo ""
echo "7️⃣ Database setup..."
print_warning "Please ensure you have:"
echo "1. Created the database"
echo "2. Updated .env with correct DB credentials"
echo "3. Imported data_v2_fixed.sql"
echo ""
read -p "Have you completed database setup? (y/N): " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    # Step 8: Cache optimization
    echo ""
    echo "8️⃣ Optimizing for production..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    print_status "Production optimization completed"
    
    # Step 9: Verification
    echo ""
    echo "9️⃣ Running verification..."
    if [ -f "scripts/verify_production_config_no_redis.php" ]; then
        php scripts/verify_production_config_no_redis.php
    else
        print_warning "Verification script not found"
    fi
    
    echo ""
    print_status "Deployment completed successfully!"
    echo ""
    echo "🎯 Next steps:"
    echo "1. Configure your web server (Apache/Nginx)"
    echo "2. Setup SSL certificate"
    echo "3. Configure queue worker"
    echo "4. Setup cron jobs"
    echo ""
    echo "🔐 Default admin login:"
    echo "URL: https://yourdomain.com/admin"
    echo "Email: admin@mechapap.vn"
    echo "Password: admin123456"
    echo ""
    print_warning "Remember to change the admin password!"
else
    echo ""
    print_warning "Please complete database setup first, then run:"
    echo "php artisan config:cache"
    echo "php artisan route:cache"
    echo "php artisan view:cache"
fi
```

## 🔧 **Cách sử dụng script**

```bash
# Tải script và chạy
chmod +x ubuntu-22-04-deploy.sh
./ubuntu-22-04-deploy.sh
```

## 🚨 **Lỗi thường gặp khác**

### **1. Permission denied**
```bash
sudo chown -R $USER:www-data .
chmod -R 755 storage bootstrap/cache
```

### **2. MySQL connection refused**
```bash
sudo systemctl start mysql
sudo systemctl enable mysql
```

### **3. PHP extensions missing**
```bash
sudo apt update
sudo apt install php8.1-mysql php8.1-xml php8.1-curl php8.1-zip php8.1-gd php8.1-mbstring
```

### **4. Apache mod_rewrite not enabled**
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

## ✅ **Verification checklist**

- [ ] PHP 8.1+ installed
- [ ] All required PHP extensions installed
- [ ] MySQL/MariaDB running
- [ ] Composer updated to latest
- [ ] All dependencies installed without errors
- [ ] Environment file configured
- [ ] Database imported successfully
- [ ] Storage permissions set correctly
- [ ] Web server configured
- [ ] SSL certificate installed

---

**🎉 MechaMap ready on Ubuntu 22.04!**
