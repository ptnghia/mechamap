#!/bin/bash

# 🚀 MechaMap Production Deployment Script
# Version: 2.0.0
# Description: Automated deployment script for MechaMap Business Verification Platform

set -e  # Exit on any error

# Color codes for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
PROJECT_DIR="/var/www/html/mechamap"
BACKUP_DIR="/var/backups/mechamap"
DATE=$(date +%Y%m%d_%H%M%S)
LOG_FILE="/var/log/mechamap-deploy-$DATE.log"

# Functions
log() {
    echo -e "${BLUE}[$(date '+%Y-%m-%d %H:%M:%S')]${NC} $1" | tee -a "$LOG_FILE"
}

success() {
    echo -e "${GREEN}✅ $1${NC}" | tee -a "$LOG_FILE"
}

warning() {
    echo -e "${YELLOW}⚠️  $1${NC}" | tee -a "$LOG_FILE"
}

error() {
    echo -e "${RED}❌ $1${NC}" | tee -a "$LOG_FILE"
    exit 1
}

# Check if running as root or with sudo
check_permissions() {
    if [[ $EUID -ne 0 ]]; then
        error "This script must be run as root or with sudo"
    fi
}

# Pre-deployment checks
pre_deployment_checks() {
    log "🔍 Running pre-deployment checks..."
    
    # Check if project directory exists
    if [ ! -d "$PROJECT_DIR" ]; then
        error "Project directory $PROJECT_DIR does not exist"
    fi
    
    # Check if git is available
    if ! command -v git &> /dev/null; then
        error "Git is not installed"
    fi
    
    # Check if composer is available
    if ! command -v composer &> /dev/null; then
        error "Composer is not installed"
    fi
    
    # Check if PHP is available
    if ! command -v php &> /dev/null; then
        error "PHP is not installed"
    fi
    
    # Check PHP version
    PHP_VERSION=$(php -r "echo PHP_VERSION;")
    if [[ $(echo "$PHP_VERSION 8.2" | awk '{print ($1 >= $2)}') -eq 0 ]]; then
        error "PHP version must be 8.2 or higher. Current: $PHP_VERSION"
    fi
    
    success "Pre-deployment checks passed"
}

# Create backup
create_backup() {
    log "💾 Creating backup..."
    
    # Create backup directory
    mkdir -p "$BACKUP_DIR/$DATE"
    
    # Database backup
    if command -v mysqldump &> /dev/null; then
        log "Backing up database..."
        mysqldump -u mechamap_backup -p mechamap_production | gzip > "$BACKUP_DIR/$DATE/database.sql.gz" 2>/dev/null || warning "Database backup failed - continuing anyway"
    fi
    
    # Application files backup
    log "Backing up application files..."
    tar -czf "$BACKUP_DIR/$DATE/application.tar.gz" -C "$(dirname $PROJECT_DIR)" "$(basename $PROJECT_DIR)" 2>/dev/null || warning "Application backup failed - continuing anyway"
    
    # Storage backup
    log "Backing up storage..."
    if [ -d "$PROJECT_DIR/storage" ]; then
        tar -czf "$BACKUP_DIR/$DATE/storage.tar.gz" -C "$PROJECT_DIR" storage 2>/dev/null || warning "Storage backup failed - continuing anyway"
    fi
    
    success "Backup created at $BACKUP_DIR/$DATE"
}

# Pull latest code
pull_code() {
    log "📥 Pulling latest code..."
    
    cd "$PROJECT_DIR"
    
    # Stash any local changes
    git stash push -m "Auto-stash before deployment $DATE" 2>/dev/null || true
    
    # Pull latest changes
    git pull origin main || error "Failed to pull latest code"
    
    success "Code updated successfully"
}

# Install dependencies
install_dependencies() {
    log "📦 Installing dependencies..."
    
    cd "$PROJECT_DIR"
    
    # Install Composer dependencies
    composer install --no-dev --optimize-autoloader --no-interaction || error "Failed to install Composer dependencies"
    
    success "Dependencies installed successfully"
}

# Clear caches
clear_caches() {
    log "🧹 Clearing caches..."
    
    cd "$PROJECT_DIR"
    
    # Clear Laravel caches
    php artisan cache:clear || warning "Failed to clear cache"
    php artisan config:clear || warning "Failed to clear config cache"
    php artisan route:clear || warning "Failed to clear route cache"
    php artisan view:clear || warning "Failed to clear view cache"
    
    success "Caches cleared successfully"
}

# Run migrations
run_migrations() {
    log "🗄️ Running database migrations..."
    
    cd "$PROJECT_DIR"
    
    # Run migrations
    php artisan migrate --force || error "Failed to run migrations"
    
    success "Migrations completed successfully"
}

# Optimize for production
optimize_production() {
    log "⚡ Optimizing for production..."
    
    cd "$PROJECT_DIR"
    
    # Cache configuration
    php artisan config:cache || warning "Failed to cache config"
    
    # Cache routes
    php artisan route:cache || warning "Failed to cache routes"
    
    # Cache views
    php artisan view:cache || warning "Failed to cache views"
    
    success "Production optimization completed"
}

# Set permissions
set_permissions() {
    log "🔐 Setting file permissions..."
    
    # Set ownership
    chown -R www-data:www-data "$PROJECT_DIR"
    
    # Set directory permissions
    find "$PROJECT_DIR" -type d -exec chmod 755 {} \;
    
    # Set file permissions
    find "$PROJECT_DIR" -type f -exec chmod 644 {} \;
    
    # Set executable permissions for artisan
    chmod +x "$PROJECT_DIR/artisan"
    
    # Set writable permissions for storage and cache
    chmod -R 775 "$PROJECT_DIR/storage"
    chmod -R 775 "$PROJECT_DIR/bootstrap/cache"
    
    success "Permissions set successfully"
}

# Restart services
restart_services() {
    log "🔄 Restarting services..."
    
    # Restart queue workers
    if command -v supervisorctl &> /dev/null; then
        supervisorctl restart mechamap-worker:* || warning "Failed to restart queue workers"
    fi
    
    # Reload Nginx
    if command -v nginx &> /dev/null; then
        systemctl reload nginx || warning "Failed to reload Nginx"
    fi
    
    # Reload Apache (if using Apache)
    if command -v apache2 &> /dev/null; then
        systemctl reload apache2 || warning "Failed to reload Apache"
    fi
    
    success "Services restarted successfully"
}

# Post-deployment verification
verify_deployment() {
    log "🧪 Verifying deployment..."
    
    cd "$PROJECT_DIR"
    
    # Test database connection
    php artisan tinker --execute="DB::connection()->getPdo(); echo 'Database OK';" || error "Database connection failed"
    
    # Test Redis connection (if configured)
    php artisan tinker --execute="try { Redis::ping(); echo 'Redis OK'; } catch (Exception \$e) { echo 'Redis not configured or failed'; }" 2>/dev/null || warning "Redis test failed"
    
    # Check if application is accessible
    if command -v curl &> /dev/null; then
        HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" http://localhost/ || echo "000")
        if [ "$HTTP_STATUS" -eq 200 ]; then
            success "Application is accessible (HTTP 200)"
        else
            warning "Application returned HTTP $HTTP_STATUS"
        fi
    fi
    
    success "Deployment verification completed"
}

# Cleanup old backups (keep last 7 days)
cleanup_backups() {
    log "🧹 Cleaning up old backups..."
    
    if [ -d "$BACKUP_DIR" ]; then
        find "$BACKUP_DIR" -type d -mtime +7 -exec rm -rf {} \; 2>/dev/null || true
        success "Old backups cleaned up"
    fi
}

# Main deployment function
main() {
    log "🚀 Starting MechaMap production deployment..."
    log "Deployment ID: $DATE"
    log "Project Directory: $PROJECT_DIR"
    log "Backup Directory: $BACKUP_DIR/$DATE"
    
    # Run deployment steps
    check_permissions
    pre_deployment_checks
    create_backup
    pull_code
    install_dependencies
    clear_caches
    run_migrations
    optimize_production
    set_permissions
    restart_services
    verify_deployment
    cleanup_backups
    
    success "🎉 Deployment completed successfully!"
    log "Deployment log saved to: $LOG_FILE"
    log "Backup created at: $BACKUP_DIR/$DATE"
    
    # Display summary
    echo ""
    echo -e "${GREEN}================================${NC}"
    echo -e "${GREEN}  DEPLOYMENT SUMMARY${NC}"
    echo -e "${GREEN}================================${NC}"
    echo -e "Deployment ID: ${BLUE}$DATE${NC}"
    echo -e "Status: ${GREEN}SUCCESS${NC}"
    echo -e "Project: ${BLUE}$PROJECT_DIR${NC}"
    echo -e "Backup: ${BLUE}$BACKUP_DIR/$DATE${NC}"
    echo -e "Log: ${BLUE}$LOG_FILE${NC}"
    echo -e "${GREEN}================================${NC}"
}

# Handle script interruption
trap 'error "Deployment interrupted by user"' INT TERM

# Run main function
main "$@"
