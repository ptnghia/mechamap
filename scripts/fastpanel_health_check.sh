#!/bin/bash

# MechaMap FastPanel Health Check Script
# Verifies all services are running correctly

set -e

echo "🔍 MechaMap FastPanel Health Check"
echo "=================================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

print_status() {
    echo -e "${BLUE}[CHECK]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[OK]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check FastPanel service
print_status "Checking FastPanel service..."
if systemctl is-active --quiet fastpanel; then
    print_success "FastPanel is running"
else
    print_error "FastPanel is not running"
fi

# Check MySQL service
print_status "Checking MySQL service..."
if systemctl is-active --quiet mysql; then
    print_success "MySQL is running"
else
    print_error "MySQL is not running"
fi

# Check PM2 realtime server
print_status "Checking realtime server (PM2)..."
if pm2 list | grep -q "mechamap-realtime"; then
    STATUS=$(pm2 list | grep "mechamap-realtime" | awk '{print $10}')
    if [ "$STATUS" = "online" ]; then
        print_success "Realtime server is online"
    else
        print_error "Realtime server status: $STATUS"
    fi
else
    print_error "Realtime server not found in PM2"
fi

# Check Laravel application
print_status "Checking Laravel application..."
if [ -f "/var/www/mechamap/artisan" ]; then
    print_success "Laravel application files found"

    # Check if .env.production exists
    if [ -f "/var/www/mechamap/.env" ]; then
        print_success "Environment configuration found"
    else
        print_warning "Environment configuration not found"
    fi
else
    print_error "Laravel application not found at /var/www/mechamap"
fi

# Test HTTP endpoints
print_status "Testing HTTP endpoints..."

# Test main site
if curl -s -o /dev/null -w "%{http_code}" https://mechamap.com | grep -q "200\|301\|302"; then
    print_success "mechamap.com is responding"
else
    print_error "mechamap.com is not responding"
fi

# Test realtime server
if curl -s -o /dev/null -w "%{http_code}" https://realtime.mechamap.com/api/health | grep -q "200"; then
    print_success "realtime.mechamap.com health check passed"
else
    print_error "realtime.mechamap.com health check failed"
fi

# Check SSL certificates
print_status "Checking SSL certificates..."
MECHAMAP_SSL=$(echo | openssl s_client -servername mechamap.com -connect mechamap.com:443 2>/dev/null | openssl x509 -noout -dates 2>/dev/null | grep "notAfter" | cut -d= -f2)
REALTIME_SSL=$(echo | openssl s_client -servername realtime.mechamap.com -connect realtime.mechamap.com:443 2>/dev/null | openssl x509 -noout -dates 2>/dev/null | grep "notAfter" | cut -d= -f2)

if [ ! -z "$MECHAMAP_SSL" ]; then
    print_success "mechamap.com SSL certificate valid until: $MECHAMAP_SSL"
else
    print_error "mechamap.com SSL certificate check failed"
fi

if [ ! -z "$REALTIME_SSL" ]; then
    print_success "realtime.mechamap.com SSL certificate valid until: $REALTIME_SSL"
else
    print_error "realtime.mechamap.com SSL certificate check failed"
fi

# Check disk space
print_status "Checking disk space..."
DISK_USAGE=$(df / | tail -1 | awk '{print $5}' | sed 's/%//')
if [ "$DISK_USAGE" -lt 80 ]; then
    print_success "Disk usage: ${DISK_USAGE}% (OK)"
elif [ "$DISK_USAGE" -lt 90 ]; then
    print_warning "Disk usage: ${DISK_USAGE}% (Warning)"
else
    print_error "Disk usage: ${DISK_USAGE}% (Critical)"
fi

# Check memory usage
print_status "Checking memory usage..."
MEM_USAGE=$(free | grep Mem | awk '{printf "%.0f", $3/$2 * 100.0}')
if [ "$MEM_USAGE" -lt 80 ]; then
    print_success "Memory usage: ${MEM_USAGE}% (OK)"
elif [ "$MEM_USAGE" -lt 90 ]; then
    print_warning "Memory usage: ${MEM_USAGE}% (Warning)"
else
    print_error "Memory usage: ${MEM_USAGE}% (Critical)"
fi

echo ""
echo "=================================="
echo "🎯 Health Check Summary:"
echo "  - FastPanel: $(systemctl is-active fastpanel)"
echo "  - MySQL: $(systemctl is-active mysql)"
echo "  - Realtime Server: $(pm2 list | grep mechamap-realtime | awk '{print $10}' || echo 'not found')"
echo "  - Disk Usage: ${DISK_USAGE}%"
echo "  - Memory Usage: ${MEM_USAGE}%"
echo ""
echo "📋 Quick Commands:"
echo "  - View PM2 status: pm2 status"
echo "  - View PM2 logs: pm2 logs mechamap-realtime"
echo "  - Check FastPanel: systemctl status fastpanel"
echo "  - Laravel logs: tail -f /var/www/mechamap/storage/logs/laravel.log"
echo "=================================="
