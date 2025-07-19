#!/bin/bash

# Fix WebSocket Configuration Issues on VPS
# Giải quyết lỗi "Undefined variable $configJson"

echo "🔧 FIXING WEBSOCKET CONFIGURATION ON VPS"
echo "========================================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

print_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️ $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

# Check if we're in Laravel project
if [ ! -f "artisan" ]; then
    print_error "This script must be run from Laravel project root"
    exit 1
fi

echo ""
echo "1️⃣ CLEARING ALL CACHES..."
echo "-------------------------"

# Clear all caches
php artisan config:clear
print_success "Config cache cleared"

php artisan cache:clear
print_success "Application cache cleared"

php artisan view:clear
print_success "View cache cleared"

php artisan route:clear
print_success "Route cache cleared"

echo ""
echo "2️⃣ CHECKING ENVIRONMENT CONFIGURATION..."
echo "----------------------------------------"

# Check if .env exists
if [ ! -f ".env" ]; then
    print_error ".env file not found!"
    if [ -f ".env.production.template" ]; then
        print_warning "Copying from .env.production.template"
        cp .env.production.template .env
        print_success ".env created from template"
    else
        print_error "No .env template found. Please create .env file manually."
        exit 1
    fi
fi

# Check WebSocket environment variables
echo ""
echo "Checking WebSocket environment variables..."

# Add WebSocket config to .env if missing
if ! grep -q "WEBSOCKET_SERVER_URL" .env; then
    echo "" >> .env
    echo "# WebSocket Configuration" >> .env
    echo "WEBSOCKET_SERVER_URL=https://realtime.$(grep APP_URL .env | cut -d'=' -f2 | sed 's|https://||')" >> .env
    echo "WEBSOCKET_SERVER_HOST=realtime.$(grep APP_URL .env | cut -d'=' -f2 | sed 's|https://||')" >> .env
    echo "WEBSOCKET_SERVER_PORT=443" >> .env
    echo "WEBSOCKET_SERVER_SECURE=true" >> .env
    echo "REALTIME_SERVER_URL=https://realtime.$(grep APP_URL .env | cut -d'=' -f2 | sed 's|https://||')" >> .env
    print_success "Added WebSocket configuration to .env"
else
    print_success "WebSocket configuration already exists in .env"
fi

echo ""
echo "3️⃣ CHECKING CONFIG FILES..."
echo "---------------------------"

# Check if websocket config exists
if [ ! -f "config/websocket.php" ]; then
    print_error "config/websocket.php not found!"
    print_warning "This file should be in your repository. Please ensure it's deployed."
    exit 1
else
    print_success "config/websocket.php exists"
fi

echo ""
echo "4️⃣ FIXING FILE PERMISSIONS..."
echo "-----------------------------"

# Fix permissions
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
print_success "Fixed storage and cache permissions"

echo ""
echo "5️⃣ REGENERATING AUTOLOAD..."
echo "---------------------------"

composer dump-autoload --optimize
print_success "Autoload regenerated"

echo ""
echo "6️⃣ TESTING WEBSOCKET CONFIGURATION..."
echo "------------------------------------"

# Test WebSocket config
php scripts/debug_websocket_config.php

echo ""
echo "7️⃣ RESTARTING SERVICES..."
echo "-------------------------"

# Restart web server (if using systemd)
if command -v systemctl &> /dev/null; then
    if systemctl is-active --quiet nginx; then
        sudo systemctl reload nginx
        print_success "Nginx reloaded"
    elif systemctl is-active --quiet apache2; then
        sudo systemctl reload apache2
        print_success "Apache reloaded"
    fi
fi

# Restart PHP-FPM if available
if command -v systemctl &> /dev/null; then
    if systemctl is-active --quiet php8.1-fpm; then
        sudo systemctl reload php8.1-fpm
        print_success "PHP-FPM reloaded"
    elif systemctl is-active --quiet php8.2-fpm; then
        sudo systemctl reload php8.2-fpm
        print_success "PHP-FPM reloaded"
    fi
fi

# Restart realtime server if using PM2
if command -v pm2 &> /dev/null; then
    cd realtime-server
    pm2 restart ecosystem.config.js
    print_success "Realtime server restarted"
    cd ..
fi

echo ""
echo "🎉 WEBSOCKET CONFIGURATION FIX COMPLETED!"
echo "========================================="
echo ""
echo "📋 What was fixed:"
echo "• Cleared all Laravel caches"
echo "• Added missing WebSocket environment variables"
echo "• Fixed file permissions"
echo "• Regenerated autoload files"
echo "• Restarted services"
echo ""
echo "🧪 Next steps:"
echo "1. Test your website in browser"
echo "2. Check browser console for WebSocket errors"
echo "3. Verify realtime features work"
echo ""
echo "🔍 If issues persist, check:"
echo "• Browser console errors"
echo "• Laravel logs: storage/logs/laravel.log"
echo "• Realtime server logs: pm2 logs"
echo ""
