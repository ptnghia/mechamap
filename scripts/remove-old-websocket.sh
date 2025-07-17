#!/bin/bash

# MechaMap - Remove Old WebSocket Implementation Script
echo "🗑️  MECHAMAP - REMOVE OLD WEBSOCKET IMPLEMENTATION"
echo "================================================="
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print status
print_status() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

print_info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

# Check if we're in the right directory
if [ ! -f "composer.json" ]; then
    print_error "This script must be run from the Laravel project root directory"
    exit 1
fi

echo "🔍 ANALYZING CURRENT WEBSOCKET IMPLEMENTATION..."
echo "──────────────────────────────────────────────"

# 1. List Laravel Reverb packages
echo ""
echo "1️⃣ CHECKING LARAVEL REVERB PACKAGES..."
echo "────────────────────────────────────────"

if grep -q "laravel/reverb" composer.json; then
    print_warning "Found Laravel Reverb in composer.json"
    echo "   - laravel/reverb: $(grep 'laravel/reverb' composer.json | cut -d'"' -f4)"
else
    print_info "No Laravel Reverb found in composer.json"
fi

# 2. List WebSocket Controllers
echo ""
echo "2️⃣ CHECKING WEBSOCKET CONTROLLERS..."
echo "──────────────────────────────────────"

WEBSOCKET_CONTROLLERS=(
    "app/Http/Controllers/WebSocketController.php"
    "app/Http/Controllers/RealTimeController.php"
)

for controller in "${WEBSOCKET_CONTROLLERS[@]}"; do
    if [ -f "$controller" ]; then
        print_warning "Found: $controller"
    fi
done

# 3. List Broadcasting Events
echo ""
echo "3️⃣ CHECKING BROADCASTING EVENTS..."
echo "────────────────────────────────────"

BROADCASTING_EVENTS=(
    "app/Events/ConnectionEvent.php"
    "app/Events/ChatMessageSent.php"
    "app/Events/UserOnlineStatusChanged.php"
    "app/Events/RealTimeNotification.php"
    "app/Events/UserActivityUpdate.php"
    "app/Events/DashboardMetricsUpdate.php"
    "app/Events/TypingStarted.php"
    "app/Events/SecurityIncidentDetected.php"
)

for event in "${BROADCASTING_EVENTS[@]}"; do
    if [ -f "$event" ]; then
        print_warning "Found: $event"
    fi
done

# 4. List WebSocket Services
echo ""
echo "4️⃣ CHECKING WEBSOCKET SERVICES..."
echo "───────────────────────────────────"

WEBSOCKET_SERVICES=(
    "app/Services/WebSocketService.php"
    "app/Services/WebSocketConnectionService.php"
    "app/Services/RealTimeNotificationService.php"
    "app/Services/TypingIndicatorService.php"
    "app/Services/ConnectionManagementService.php"
    "app/Services/ConnectionHealthMonitor.php"
)

for service in "${WEBSOCKET_SERVICES[@]}"; do
    if [ -f "$service" ]; then
        print_warning "Found: $service"
    fi
done

# 5. Check Frontend WebSocket files
echo ""
echo "5️⃣ CHECKING FRONTEND WEBSOCKET FILES..."
echo "─────────────────────────────────────────"

FRONTEND_WEBSOCKET_FILES=(
    "public/js/realtime-client.js"
    "public/js/notification-service.js"
    "resources/js/realtime-client.js"
    "resources/js/notification-service.js"
)

for file in "${FRONTEND_WEBSOCKET_FILES[@]}"; do
    if [ -f "$file" ]; then
        print_warning "Found: $file"
    fi
done

# 6. Check Configuration files
echo ""
echo "6️⃣ CHECKING CONFIGURATION FILES..."
echo "────────────────────────────────────"

CONFIG_FILES=(
    "config/reverb.php"
    "config/broadcasting.php"
)

for config in "${CONFIG_FILES[@]}"; do
    if [ -f "$config" ]; then
        print_warning "Found: $config"
    fi
done

# 7. Check Routes
echo ""
echo "7️⃣ CHECKING WEBSOCKET ROUTES..."
echo "─────────────────────────────────"

if grep -q "websocket" routes/api.php; then
    print_warning "Found WebSocket routes in routes/api.php"
fi

if grep -q "realtime" routes/web.php; then
    print_warning "Found realtime routes in routes/web.php"
fi

if [ -f "routes/channels.php" ]; then
    print_warning "Found broadcasting channels in routes/channels.php"
fi

echo ""
echo "🤔 READY TO REMOVE OLD WEBSOCKET IMPLEMENTATION?"
echo "═══════════════════════════════════════════════"
echo ""
echo "This will remove:"
echo "  • Laravel Reverb package"
echo "  • WebSocket Controllers & Services"
echo "  • Broadcasting Events"
echo "  • Frontend WebSocket files"
echo "  • WebSocket routes"
echo "  • Configuration files"
echo ""
read -p "Continue? (y/N): " -n 1 -r
echo ""

if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    print_info "Operation cancelled by user"
    exit 0
fi

echo ""
echo "🗑️  REMOVING OLD WEBSOCKET IMPLEMENTATION..."
echo "═══════════════════════════════════════════"

# Step 1: Remove Laravel Reverb package
echo ""
echo "1️⃣ REMOVING LARAVEL REVERB PACKAGE..."
echo "────────────────────────────────────────"

if grep -q "laravel/reverb" composer.json; then
    print_info "Removing Laravel Reverb from composer.json..."
    composer remove laravel/reverb --no-interaction
    if [ $? -eq 0 ]; then
        print_status "Laravel Reverb package removed"
    else
        print_error "Failed to remove Laravel Reverb package"
    fi
else
    print_info "Laravel Reverb not found in composer.json"
fi

# Step 2: Remove WebSocket Controllers
echo ""
echo "2️⃣ REMOVING WEBSOCKET CONTROLLERS..."
echo "──────────────────────────────────────"

for controller in "${WEBSOCKET_CONTROLLERS[@]}"; do
    if [ -f "$controller" ]; then
        rm -f "$controller"
        print_status "Removed: $controller"
    fi
done

# Step 3: Remove Broadcasting Events
echo ""
echo "3️⃣ REMOVING BROADCASTING EVENTS..."
echo "────────────────────────────────────"

for event in "${BROADCASTING_EVENTS[@]}"; do
    if [ -f "$event" ]; then
        rm -f "$event"
        print_status "Removed: $event"
    fi
done

# Step 4: Remove WebSocket Services
echo ""
echo "4️⃣ REMOVING WEBSOCKET SERVICES..."
echo "───────────────────────────────────"

for service in "${WEBSOCKET_SERVICES[@]}"; do
    if [ -f "$service" ]; then
        rm -f "$service"
        print_status "Removed: $service"
    fi
done

# Step 5: Remove Frontend WebSocket files
echo ""
echo "5️⃣ REMOVING FRONTEND WEBSOCKET FILES..."
echo "─────────────────────────────────────────"

for file in "${FRONTEND_WEBSOCKET_FILES[@]}"; do
    if [ -f "$file" ]; then
        rm -f "$file"
        print_status "Removed: $file"
    fi
done

# Step 6: Remove Configuration files
echo ""
echo "6️⃣ REMOVING CONFIGURATION FILES..."
echo "────────────────────────────────────"

# Remove reverb.php config
if [ -f "config/reverb.php" ]; then
    rm -f "config/reverb.php"
    print_status "Removed: config/reverb.php"
fi

# Update broadcasting.php to remove reverb driver
if [ -f "config/broadcasting.php" ]; then
    print_info "Updating config/broadcasting.php to remove reverb driver..."
    # Create backup
    cp "config/broadcasting.php" "config/broadcasting.php.backup"

    # Remove reverb configuration block
    sed -i '/reverb.*=>/,/],/d' "config/broadcasting.php"
    print_status "Updated: config/broadcasting.php (backup created)"
fi

# Step 7: Clean WebSocket routes
echo ""
echo "7️⃣ CLEANING WEBSOCKET ROUTES..."
echo "─────────────────────────────────"

# Clean API routes
if grep -q "websocket" routes/api.php; then
    print_info "Cleaning WebSocket routes from routes/api.php..."
    cp "routes/api.php" "routes/api.php.backup"

    # Remove WebSocket API routes block
    sed -i '/WebSocket API routes/,/});/d' "routes/api.php"
    print_status "Cleaned: routes/api.php (backup created)"
fi

# Clean web routes
if grep -q "realtime" routes/web.php; then
    print_info "Cleaning realtime routes from routes/web.php..."
    cp "routes/web.php" "routes/web.php.backup"

    # Remove realtime routes block
    sed -i '/Route::prefix.*realtime/,/});/d' "routes/web.php"
    print_status "Cleaned: routes/web.php (backup created)"
fi

# Remove broadcasting channels
if [ -f "routes/channels.php" ]; then
    print_info "Cleaning broadcasting channels..."
    cp "routes/channels.php" "routes/channels.php.backup"

    # Keep only the basic structure, remove all channel definitions
    cat > "routes/channels.php" << 'EOF'
<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Broadcasting channels will be managed by Node.js WebSocket server
// This file is kept for Laravel compatibility but channels are not used
EOF
    print_status "Cleaned: routes/channels.php (backup created)"
fi

# Step 8: Clean Frontend Layout
echo ""
echo "8️⃣ CLEANING FRONTEND LAYOUT..."
echo "─────────────────────────────"

# Remove Pusher.js initialization from app.blade.php
if [ -f "resources/views/layouts/app.blade.php" ]; then
    print_info "Removing Pusher.js initialization from app.blade.php..."
    cp "resources/views/layouts/app.blade.php" "resources/views/layouts/app.blade.php.backup"

    # Remove Pusher.js script block
    sed -i '/Initialize Pusher.js directly for Reverb/,/});/d' "resources/views/layouts/app.blade.php"
    print_status "Cleaned: resources/views/layouts/app.blade.php (backup created)"
fi

# Step 9: Update Environment Variables
echo ""
echo "9️⃣ UPDATING ENVIRONMENT VARIABLES..."
echo "──────────────────────────────────────"

if [ -f ".env" ]; then
    print_info "Updating .env file..."
    cp ".env" ".env.backup"

    # Comment out or remove Reverb-related variables
    sed -i 's/^BROADCAST_CONNECTION=.*/BROADCAST_CONNECTION=log/' ".env"
    sed -i 's/^REVERB_/#REVERB_/' ".env"

    print_status "Updated: .env (backup created)"
    print_info "Set BROADCAST_CONNECTION=log (disabled broadcasting)"
fi

# Step 10: Clear Caches
echo ""
echo "🔟 CLEARING CACHES..."
echo "───────────────────"

print_info "Clearing Laravel caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

if [ $? -eq 0 ]; then
    print_status "Laravel caches cleared"
else
    print_warning "Some caches may not have been cleared properly"
fi

# Step 11: Regenerate Autoloader
echo ""
echo "1️⃣1️⃣ REGENERATING AUTOLOADER..."
echo "─────────────────────────────"

print_info "Regenerating Composer autoloader..."
composer dump-autoload --optimize

if [ $? -eq 0 ]; then
    print_status "Composer autoloader regenerated"
else
    print_error "Failed to regenerate autoloader"
fi

# Final Summary
echo ""
echo "🎉 OLD WEBSOCKET IMPLEMENTATION REMOVED!"
echo "═══════════════════════════════════════"
echo ""
echo "✅ Removed Components:"
echo "   • Laravel Reverb package"
echo "   • WebSocket Controllers ($(echo "${WEBSOCKET_CONTROLLERS[@]}" | wc -w) files)"
echo "   • Broadcasting Events ($(echo "${BROADCASTING_EVENTS[@]}" | wc -w) files)"
echo "   • WebSocket Services ($(echo "${WEBSOCKET_SERVICES[@]}" | wc -w) files)"
echo "   • Frontend WebSocket files"
echo "   • WebSocket routes"
echo "   • Configuration files"
echo "   • Pusher.js initialization"
echo ""
echo "📁 Backup Files Created:"
echo "   • .env.backup"
echo "   • config/broadcasting.php.backup"
echo "   • routes/api.php.backup"
echo "   • routes/web.php.backup"
echo "   • routes/channels.php.backup"
echo "   • resources/views/layouts/app.blade.php.backup"
echo ""
echo "🔧 Next Steps:"
echo "   1. Test the application to ensure no errors"
echo "   2. Integrate Node.js WebSocket server"
echo "   3. Update frontend to connect to Node.js server"
echo "   4. Remove backup files when satisfied"
echo ""
echo "⚠️  Note: Broadcasting is now disabled (BROADCAST_CONNECTION=log)"
echo "   This will be re-enabled when Node.js WebSocket server is integrated"
echo ""
print_status "Old WebSocket implementation removal completed successfully!"
