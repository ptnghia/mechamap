#!/bin/bash

# =============================================================================
# MechaMap Environment Switcher
# Switch between development and production environments
# =============================================================================

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to display usage
usage() {
    echo "Usage: $0 [development|production] [--domain=DOMAIN]"
    echo ""
    echo "Examples:"
    echo "  $0 development                    # Switch to development (mechamap.test)"
    echo "  $0 production                     # Switch to production (mechamap.com)"
    echo "  $0 production --domain=yourdomain.com  # Switch to production with custom domain"
    echo ""
    exit 1
}

# Function to log messages
log() {
    echo -e "${GREEN}[$(date +'%H:%M:%S')]${NC} $1"
}

error() {
    echo -e "${RED}[$(date +'%H:%M:%S')] ERROR:${NC} $1"
}

warning() {
    echo -e "${YELLOW}[$(date +'%H:%M:%S')] WARNING:${NC} $1"
}

# Parse arguments
ENVIRONMENT=""
CUSTOM_DOMAIN=""

for arg in "$@"; do
    case $arg in
        development|production)
            ENVIRONMENT="$arg"
            ;;
        --domain=*)
            CUSTOM_DOMAIN="${arg#*=}"
            ;;
        *)
            echo "Unknown argument: $arg"
            usage
            ;;
    esac
done

if [ -z "$ENVIRONMENT" ]; then
    echo "Error: Environment not specified"
    usage
fi

echo "🔄 MechaMap Environment Switcher"
echo "================================"
echo ""

# Set domain based on environment and custom domain
if [ "$ENVIRONMENT" = "development" ]; then
    DOMAIN="mechamap.test"
    WEBSOCKET_DOMAIN="localhost:3000"
    WEBSOCKET_URL="http://localhost:3000"
    WEBSOCKET_SECURE="false"
elif [ "$ENVIRONMENT" = "production" ]; then
    if [ -n "$CUSTOM_DOMAIN" ]; then
        DOMAIN="$CUSTOM_DOMAIN"
        WEBSOCKET_DOMAIN="realtime.$CUSTOM_DOMAIN"
    else
        DOMAIN="mechamap.com"
        WEBSOCKET_DOMAIN="realtime.mechamap.com"
    fi
    WEBSOCKET_URL="https://$WEBSOCKET_DOMAIN"
    WEBSOCKET_SECURE="true"
fi

log "Switching to $ENVIRONMENT environment"
log "Domain: $DOMAIN"
log "WebSocket: $WEBSOCKET_URL"
echo ""

# 1. Laravel Environment Setup
log "📝 Setting up Laravel environment..."

# Check if environment template exists
ENV_TEMPLATE=".env.$ENVIRONMENT.template"
ENV_FILE=".env"

if [ -f "$ENV_TEMPLATE" ]; then
    log "Using template: $ENV_TEMPLATE"
    cp "$ENV_TEMPLATE" "$ENV_FILE"
else
    warning "Template $ENV_TEMPLATE not found, creating from current .env"
fi

# Update domain in .env file
if [ -f "$ENV_FILE" ]; then
    # Update APP_URL
    sed -i.bak "s|APP_URL=.*|APP_URL=https://$DOMAIN|g" "$ENV_FILE"
    
    # Update WebSocket configuration
    sed -i.bak "s|WEBSOCKET_SERVER_URL=.*|WEBSOCKET_SERVER_URL=$WEBSOCKET_URL|g" "$ENV_FILE"
    sed -i.bak "s|WEBSOCKET_SERVER_HOST=.*|WEBSOCKET_SERVER_HOST=${WEBSOCKET_DOMAIN%:*}|g" "$ENV_FILE"
    sed -i.bak "s|WEBSOCKET_SERVER_SECURE=.*|WEBSOCKET_SERVER_SECURE=$WEBSOCKET_SECURE|g" "$ENV_FILE"
    
    # Update broadcasting URL
    sed -i.bak "s|NODEJS_BROADCAST_URL=.*|NODEJS_BROADCAST_URL=$WEBSOCKET_URL|g" "$ENV_FILE"
    
    # Remove backup file
    rm -f "$ENV_FILE.bak"
    
    log "✅ Laravel environment updated"
else
    error "❌ .env file not found"
    exit 1
fi

# 2. Node.js Environment Setup
log "📝 Setting up Node.js environment..."

NODEJS_DIR="realtime-server"
if [ -d "$NODEJS_DIR" ]; then
    cd "$NODEJS_DIR"
    
    # Check if Node.js environment template exists
    NODEJS_ENV_TEMPLATE=".env.$ENVIRONMENT.template"
    NODEJS_ENV_FILE=".env"
    
    if [ -f "$NODEJS_ENV_TEMPLATE" ]; then
        log "Using Node.js template: $NODEJS_ENV_TEMPLATE"
        cp "$NODEJS_ENV_TEMPLATE" "$NODEJS_ENV_FILE"
    else
        warning "Node.js template $NODEJS_ENV_TEMPLATE not found"
    fi
    
    # Update domain in Node.js .env file
    if [ -f "$NODEJS_ENV_FILE" ]; then
        # Update Laravel API URL
        sed -i.bak "s|LARAVEL_API_URL=.*|LARAVEL_API_URL=https://$DOMAIN|g" "$NODEJS_ENV_FILE"
        
        # Update CORS origins
        if [ "$ENVIRONMENT" = "development" ]; then
            CORS_ORIGINS="https://$DOMAIN,http://localhost:8000,https://localhost:8000"
        else
            CORS_ORIGINS="https://$DOMAIN,https://www.$DOMAIN"
        fi
        sed -i.bak "s|CORS_ORIGIN=.*|CORS_ORIGIN=$CORS_ORIGINS|g" "$NODEJS_ENV_FILE"
        
        # Remove backup file
        rm -f "$NODEJS_ENV_FILE.bak"
        
        log "✅ Node.js environment updated"
    else
        warning "⚠️  Node.js .env file not found"
    fi
    
    cd ..
else
    warning "⚠️  Node.js directory not found: $NODEJS_DIR"
fi

# 3. Clear Laravel caches
log "🧹 Clearing Laravel caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 4. Validate configuration
log "🔍 Validating configuration..."
php artisan websocket:validate-config

# 5. Restart Node.js server if running
log "🔄 Restarting Node.js server..."
if command -v pm2 &> /dev/null; then
    cd "$NODEJS_DIR"
    pm2 restart mechamap-realtime 2>/dev/null || log "PM2 process not running"
    cd ..
else
    log "PM2 not found, please restart Node.js server manually"
fi

echo ""
log "✅ Environment switched successfully!"
echo ""
echo "📋 Summary:"
echo "   Environment: $ENVIRONMENT"
echo "   Laravel URL: https://$DOMAIN"
echo "   WebSocket URL: $WEBSOCKET_URL"
echo ""
echo "🔧 Next steps:"
if [ "$ENVIRONMENT" = "production" ]; then
    echo "   1. Update DNS records to point $DOMAIN to your server IP"
    echo "   2. Configure SSL certificates for $DOMAIN and $WEBSOCKET_DOMAIN"
    echo "   3. Update Nginx configuration for both domains"
    echo "   4. Update database credentials in .env files"
    echo "   5. Generate new API keys: php artisan websocket:generate-api-key --env=production"
else
    echo "   1. Ensure $DOMAIN points to 127.0.0.1 in your hosts file"
    echo "   2. Start Laravel development server: php artisan serve --host=$DOMAIN"
    echo "   3. Start Node.js server: cd realtime-server && npm start"
fi
echo ""
