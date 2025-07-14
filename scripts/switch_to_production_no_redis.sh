#!/bin/bash

# MechaMap Production Deployment Script (No Redis)
# Chuyển đổi từ development sang production environment

echo "🚀 MECHAMAP PRODUCTION DEPLOYMENT (NO REDIS)"
echo "============================================="
echo ""

# Change to Laravel directory
cd "d:\xampp\htdocs\laravel\mechamap_backend" || exit 1

echo "📁 Current directory: $(pwd)"
echo ""

# 1. Backup current .env
echo "1️⃣ BACKING UP CURRENT CONFIGURATION..."
echo "────────────────────────────────────────"
if [ -f .env ]; then
    cp .env .env.backup.$(date +%Y%m%d_%H%M%S)
    echo "✅ Current .env backed up"
else
    echo "⚠️ No .env file found"
fi

# 2. Copy production environment
echo ""
echo "2️⃣ APPLYING PRODUCTION CONFIGURATION..."
echo "─────────────────────────────────────────"
if [ -f .env.production ]; then
    cp .env.production .env
    echo "✅ Production .env applied"
else
    echo "❌ .env.production file not found!"
    exit 1
fi

# 3. Clear all caches
echo ""
echo "3️⃣ CLEARING CACHES..."
echo "────────────────────"
php artisan config:clear
echo "✅ Config cache cleared"

php artisan route:clear
echo "✅ Route cache cleared"

php artisan view:clear
echo "✅ View cache cleared"

php artisan cache:clear
echo "✅ Application cache cleared"

# 4. Optimize for production
echo ""
echo "4️⃣ OPTIMIZING FOR PRODUCTION..."
echo "──────────────────────────────"
php artisan config:cache
echo "✅ Config cached"

php artisan route:cache
echo "✅ Routes cached"

php artisan view:cache
echo "✅ Views cached"

# 5. Set proper file permissions
echo ""
echo "5️⃣ SETTING FILE PERMISSIONS..."
echo "─────────────────────────────"
chmod -R 755 storage
chmod -R 755 bootstrap/cache
echo "✅ File permissions set"

# 6. Run database migrations (if needed)
echo ""
echo "6️⃣ CHECKING DATABASE..."
echo "─────────────────────"
php artisan migrate --force
echo "✅ Database migrations completed"

# 7. Verify production configuration
echo ""
echo "7️⃣ VERIFYING CONFIGURATION..."
echo "────────────────────────────"
php scripts/verify_production_config_no_redis.php

echo ""
echo "🎉 PRODUCTION DEPLOYMENT COMPLETED!"
echo "=================================="
echo ""
echo "📋 NEXT STEPS:"
echo "1. Restart your web server (Apache/Nginx)"
echo "2. Start queue worker: php artisan queue:work --daemon"
echo "3. Set up cron job for scheduled tasks"
echo "4. Monitor logs: tail -f storage/logs/laravel.log"
echo ""
echo "⚠️ IMPORTANT NOTES:"
echo "• Redis is disabled - using file cache and database queue"
echo "• Make sure to backup database before going live"
echo "• Monitor performance and consider Redis if needed"
echo ""
