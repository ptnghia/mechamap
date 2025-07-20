#!/bin/bash

# MechaMap Production Deployment Script for FastPanel
# Domain: https://mechamap.com
# Realtime Server: https://realtime.mechamap.com
# Environment: VPS with FastPanel

set -e  # Exit on any error

echo "🚀 Starting MechaMap Production Deployment (FastPanel)..."
echo "========================================================"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if we're in the correct directory
if [ ! -f "artisan" ]; then
    print_error "artisan file not found. Please run this script from the Laravel root directory."
    exit 1
fi

# Backup current .env if exists
if [ -f ".env" ]; then
    print_status "Backing up current .env file..."
    cp .env .env.backup.$(date +%Y%m%d_%H%M%S)
    print_success "Environment backup created"
fi

# Copy production environment
print_status "Setting up production environment..."
if [ -f ".env.production" ]; then
    cp .env.production .env
    print_success "Production environment configured"
else
    print_error ".env.production file not found!"
    if [ -f ".env.production.template" ]; then
        print_warning "Please create .env.production from template:"
        print_warning "cp .env.production.template .env.production"
        print_warning "Then edit .env.production with your actual secrets"
    else
        print_error ".env.production.template also not found!"
    fi
    exit 1
fi

# Install/Update Composer dependencies (production)
print_status "Installing Composer dependencies for production..."
composer install --no-dev --optimize-autoloader --no-interaction
print_success "Composer dependencies installed"

# Generate application key if needed
print_status "Checking application key..."
if ! grep -q "APP_KEY=base64:" .env; then
    php artisan key:generate --force
    print_success "Application key generated"
else
    print_success "Application key already exists"
fi

# Clear all caches
print_status "Clearing application caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
print_success "Caches cleared"

# Cache configurations for production
print_status "Caching configurations for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
print_success "Configurations cached"

# Database setup note
print_status "Database setup..."
print_warning "Database should be restored from data_v2_fixed.sql file"
print_warning "Run: mysql -u username -p database_name < data_v2_fixed.sql"
print_success "Database setup instructions provided"

# Create storage symlink if not exists
print_status "Creating storage symlink..."
php artisan storage:link
print_success "Storage symlink created"

# Set proper permissions
print_status "Setting file permissions..."
chmod -R 755 storage bootstrap/cache
print_success "File permissions set"

# Optimize for production
print_status "Optimizing for production..."
php artisan optimize
print_success "Application optimized"

# Queue work setup (optional)
print_warning "Remember to set up queue workers for production:"
print_warning "  - Configure supervisor or systemd for queue:work"
print_warning "  - Set up cron job for schedule:run"

echo ""
echo "========================================================"
print_success "🎉 Production deployment completed successfully!"
echo ""
print_status "FastPanel Configuration Steps:"
echo "  1. ✅ Laravel application deployed to /var/www/mechamap"
echo "  2. 🔧 Configure FastPanel website:"
echo "     - Domain: mechamap.com"
echo "     - Document Root: /var/www/mechamap/public"
echo "     - PHP Version: 8.2"
echo "  3. 🔒 Enable SSL certificate (Let's Encrypt) in FastPanel"
echo "  4. 🚀 Deploy realtime server: ./realtime_deploy_production.sh"
echo "  5. 📊 Set up monitoring and backup scripts"
echo ""
print_status "Important URLs:"
echo "  - Main site: https://mechamap.com"
echo "  - Realtime server: https://realtime.mechamap.com"
echo "  - Admin panel: https://mechamap.com/admin"
echo ""
print_warning "FastPanel Post-Deployment Checklist:"
echo "  - ✅ Create website in FastPanel for mechamap.com"
echo "  - ✅ Configure reverse proxy for realtime.mechamap.com"
echo "  - ✅ Install SSL certificates via Let's Encrypt"
echo "  - ✅ Configure firewall rules (block port 3000 externally)"
echo "  - ✅ Update Google/Facebook OAuth for production domains"
echo "  - ✅ Set up payment gateway webhooks for production URLs"
echo ""
print_status "FastPanel Configuration Guide: fastpanel_configuration.md"
echo "========================================================"
