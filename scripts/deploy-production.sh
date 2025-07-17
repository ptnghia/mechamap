#!/bin/bash

# =============================================================================
# MechaMap Laravel Backend - Production Deployment Script
# =============================================================================

set -e  # Exit on any error

echo "🚀 MechaMap Laravel Backend - Production Deployment"
echo "=================================================="

# Configuration
PROJECT_DIR="/var/www/mechamap"
BACKUP_DIR="/var/backups/mechamap"
LOG_FILE="/var/log/mechamap/deploy.log"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Logging function
log() {
    echo -e "${GREEN}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1" | tee -a $LOG_FILE
}

error() {
    echo -e "${RED}[$(date +'%Y-%m-%d %H:%M:%S')] ERROR:${NC} $1" | tee -a $LOG_FILE
}

warning() {
    echo -e "${YELLOW}[$(date +'%Y-%m-%d %H:%M:%S')] WARNING:${NC} $1" | tee -a $LOG_FILE
}

# Check if running as correct user
if [ "$(whoami)" != "mechamap" ]; then
    error "This script should be run as 'mechamap' user"
    exit 1
fi

# Create necessary directories
mkdir -p $BACKUP_DIR
mkdir -p $(dirname $LOG_FILE)

log "Starting deployment process..."

# 1. Backup current application
log "📦 Creating backup..."
BACKUP_NAME="mechamap-backup-$(date +%Y%m%d_%H%M%S)"
tar -czf "$BACKUP_DIR/$BACKUP_NAME.tar.gz" -C $(dirname $PROJECT_DIR) $(basename $PROJECT_DIR) --exclude=node_modules --exclude=vendor

# 2. Pull latest code
log "📥 Pulling latest code..."
cd $PROJECT_DIR
git fetch origin
git reset --hard origin/main

# 3. Check environment configuration
log "🔍 Validating environment configuration..."
if [ ! -f ".env.production" ]; then
    error ".env.production file not found!"
    error "Please copy .env.production.template to .env.production and configure it"
    exit 1
fi

# Copy production environment
cp .env.production .env

# 4. Install/Update dependencies
log "📦 Installing dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# 5. Clear and cache configuration
log "⚙️  Optimizing application..."
php artisan config:clear
php artisan config:cache
php artisan route:clear
php artisan route:cache
php artisan view:clear
php artisan view:cache

# 6. Run database migrations
log "🗄️  Running database migrations..."
php artisan migrate --force

# 7. Seed production data (if needed)
if [ "$1" = "--seed" ]; then
    log "🌱 Seeding production data..."
    php artisan db:seed --class=ProductionSeeder
fi

# 8. Clear application cache
log "🧹 Clearing application cache..."
php artisan cache:clear
php artisan queue:clear

# 9. Generate application key if needed
if grep -q "APP_KEY=base64:your_app_key_here" .env; then
    log "🔑 Generating application key..."
    php artisan key:generate --force
fi

# 10. Validate WebSocket configuration
log "🔍 Validating WebSocket configuration..."
php artisan websocket:validate-config

# 11. Set proper permissions
log "🔒 Setting file permissions..."
sudo chown -R mechamap:www-data $PROJECT_DIR
sudo chmod -R 755 $PROJECT_DIR
sudo chmod -R 775 $PROJECT_DIR/storage
sudo chmod -R 775 $PROJECT_DIR/bootstrap/cache

# 12. Restart services
log "🔄 Restarting services..."
sudo systemctl reload nginx
sudo systemctl restart php8.2-fpm

# 13. Restart queue workers
log "👷 Restarting queue workers..."
php artisan queue:restart

# 14. Test application
log "🧪 Testing application..."
HEALTH_CHECK=$(curl -s -o /dev/null -w "%{http_code}" https://mechamap.com/api/health)
if [ "$HEALTH_CHECK" = "200" ]; then
    log "✅ Application health check passed"
else
    error "❌ Application health check failed (HTTP $HEALTH_CHECK)"
    exit 1
fi

# 15. Test WebSocket API
log "🧪 Testing WebSocket API..."
WEBSOCKET_HEALTH=$(curl -s -o /dev/null -w "%{http_code}" https://mechamap.com/api/websocket-api/health -H "X-WebSocket-API-Key: $(grep LARAVEL_API_KEY .env | cut -d '=' -f2)")
if [ "$WEBSOCKET_HEALTH" = "200" ]; then
    log "✅ WebSocket API health check passed"
else
    warning "⚠️  WebSocket API health check failed (HTTP $WEBSOCKET_HEALTH)"
fi

# 16. Clean up old backups (keep last 7 days)
log "🧹 Cleaning up old backups..."
find $BACKUP_DIR -name "mechamap-backup-*.tar.gz" -mtime +7 -delete

log "✅ Deployment completed successfully!"
log "📊 Deployment Summary:"
log "   - Backup created: $BACKUP_NAME.tar.gz"
log "   - Application URL: https://mechamap.com"
log "   - WebSocket URL: https://realtime.mechamap.com"
log "   - Log file: $LOG_FILE"

echo ""
echo "🎉 MechaMap Laravel Backend deployed successfully!"
echo "   Monitor logs: tail -f $LOG_FILE"
echo "   Check status: systemctl status nginx php8.2-fpm"
