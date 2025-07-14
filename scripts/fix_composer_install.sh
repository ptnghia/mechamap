#!/bin/bash

# MechaMap Composer Install Fix Script
echo "🔧 MECHAMAP COMPOSER INSTALL FIX"
echo "================================"
echo ""

# 1. Clear all caches first
echo "1️⃣ CLEARING CACHES..."
echo "────────────────────"
rm -rf vendor/
rm -rf bootstrap/cache/*.php
rm -rf storage/framework/cache/data/*
rm -rf storage/framework/sessions/*
rm -rf storage/framework/views/*

echo "✅ Caches cleared"
echo ""

# 2. Update Composer to latest version
echo "2️⃣ UPDATING COMPOSER..."
echo "─────────────────────"
composer self-update
echo "✅ Composer updated"
echo ""

# 3. Clear Composer cache
echo "3️⃣ CLEARING COMPOSER CACHE..."
echo "────────────────────────────"
composer clear-cache
echo "✅ Composer cache cleared"
echo ""

# 4. Install dependencies with specific flags
echo "4️⃣ INSTALLING DEPENDENCIES..."
echo "────────────────────────────"
composer install --no-scripts --no-autoloader --no-dev
echo "✅ Dependencies installed without scripts"
echo ""

# 5. Generate autoloader
echo "5️⃣ GENERATING AUTOLOADER..."
echo "──────────────────────────"
composer dump-autoload --optimize --no-dev
echo "✅ Autoloader generated"
echo ""

# 6. Run post-install scripts manually
echo "6️⃣ RUNNING POST-INSTALL SCRIPTS..."
echo "─────────────────────────────────"

# Generate APP_KEY if not exists
if ! grep -q "APP_KEY=base64:" .env 2>/dev/null; then
    echo "Generating APP_KEY..."
    php artisan key:generate --force
fi

# Create storage link
if [ ! -L "public/storage" ]; then
    echo "Creating storage link..."
    php artisan storage:link
fi

echo "✅ Post-install scripts completed"
echo ""

# 7. Set proper permissions
echo "7️⃣ SETTING PERMISSIONS..."
echo "────────────────────────"
chmod -R 755 storage
chmod -R 755 bootstrap/cache
echo "✅ Permissions set"
echo ""

echo "🎉 COMPOSER INSTALL FIX COMPLETED!"
echo "================================="
echo ""
echo "Next steps:"
echo "1. Copy .env.production to .env"
echo "2. Update database credentials in .env"
echo "3. Run: php artisan migrate --force"
echo "4. Run: php artisan config:cache"
echo ""
