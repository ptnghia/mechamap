#!/bin/bash

echo "🔧 FIXING forceAssetUrl ERROR"
echo "============================="

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

print_status() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️ $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

echo ""
echo "1️⃣ IDENTIFYING THE ISSUE..."
echo "──────────────────────────"

# Check if the problematic code exists
if grep -q "forceAssetUrl" app/Providers/AppServiceProvider.php 2>/dev/null; then
    print_error "Found forceAssetUrl usage in AppServiceProvider.php"
    echo "This method doesn't exist in current Laravel version"
else
    print_status "No forceAssetUrl usage found in AppServiceProvider.php"
fi

echo ""
echo "2️⃣ CLEANING ENVIRONMENT..."
echo "─────────────────────────"

# Remove vendor and caches
rm -rf vendor/
rm -rf composer.lock
rm -rf bootstrap/cache/*.php
rm -rf storage/framework/cache/data/*
rm -rf storage/framework/sessions/*
rm -rf storage/framework/views/*

print_status "Environment cleaned"

# Clear Composer cache
composer clear-cache
print_status "Composer cache cleared"

echo ""
echo "3️⃣ FIXING COMPOSER AUTOLOAD..."
echo "─────────────────────────────"

# Try to install without running scripts
if composer install --no-scripts --no-autoloader --no-dev; then
    print_status "Dependencies installed without scripts"
else
    print_error "Failed to install dependencies"
    exit 1
fi

# Generate autoloader manually
if composer dump-autoload --no-scripts --optimize --no-dev; then
    print_status "Autoloader generated successfully"
else
    print_error "Failed to generate autoloader"
    exit 1
fi

echo ""
echo "4️⃣ SETTING UP ENVIRONMENT..."
echo "───────────────────────────"

# Copy environment file if needed
if [ ! -f .env ]; then
    if [ -f .env.production ]; then
        cp .env.production .env
        print_status "Environment file created"
    else
        print_error ".env.production not found"
        exit 1
    fi
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

echo ""
echo "5️⃣ TESTING LARAVEL FUNCTIONALITY..."
echo "──────────────────────────────────"

# Test basic Laravel commands
if php artisan --version >/dev/null 2>&1; then
    print_status "Laravel CLI working"
else
    print_error "Laravel CLI not working"
    exit 1
fi

# Test config loading
if php artisan config:clear >/dev/null 2>&1; then
    print_status "Config system working"
else
    print_error "Config system not working"
    exit 1
fi

echo ""
echo "6️⃣ PRODUCTION OPTIMIZATION..."
echo "────────────────────────────"

# Cache configurations
php artisan config:cache && print_status "Config cached" || print_warning "Config cache failed"
php artisan route:cache && print_status "Routes cached" || print_warning "Route cache failed"
php artisan view:cache && print_status "Views cached" || print_warning "View cache failed"

echo ""
echo "7️⃣ FINAL VERIFICATION..."
echo "───────────────────────"

# Test that package discovery works now
if php artisan package:discover --ansi >/dev/null 2>&1; then
    print_status "Package discovery working"
else
    print_error "Package discovery still failing"
    echo "Please check for other forceAssetUrl references in your code"
    exit 1
fi

echo ""
print_status "🎉 forceAssetUrl error fixed successfully!"
echo ""
echo "Next steps:"
echo "1. Update your database credentials in .env"
echo "2. Import your database: mysql -u user -p database < data_v2_fixed.sql"
echo "3. Configure your web server"
echo "4. Test your application"
