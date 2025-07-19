#!/bin/bash

# Setup WebSocket on VPS for MechaMap
# Cài đặt và cấu hình WebSocket server trên VPS

echo "🚀 SETTING UP WEBSOCKET ON VPS"
echo "==============================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
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

print_info() {
    echo -e "${BLUE}ℹ️ $1${NC}"
}

# Configuration
DOMAIN="realtime.mechamap.com"
MAIN_DOMAIN="mechamap.com"
PROJECT_DIR="/var/www/mechamap"
REALTIME_DIR="$PROJECT_DIR/realtime-server"

echo ""
echo "🌐 Domain: $DOMAIN"
echo "📁 Project Directory: $PROJECT_DIR"
echo ""

# Check if running as root or with sudo
if [ "$EUID" -ne 0 ]; then
    print_error "Please run this script with sudo"
    exit 1
fi

# Step 1: Install dependencies
echo "1️⃣ INSTALLING DEPENDENCIES"
echo "--------------------------"

# Update system
apt update
print_success "System updated"

# Install Node.js (if not installed)
if ! command -v node &> /dev/null; then
    curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
    apt-get install -y nodejs
    print_success "Node.js installed"
else
    print_success "Node.js already installed: $(node --version)"
fi

# Install PM2 globally
if ! command -v pm2 &> /dev/null; then
    npm install -g pm2
    print_success "PM2 installed"
else
    print_success "PM2 already installed: $(pm2 --version)"
fi

# Install nginx (if not installed)
if ! command -v nginx &> /dev/null; then
    apt install -y nginx
    print_success "Nginx installed"
else
    print_success "Nginx already installed"
fi

# Install certbot for SSL
if ! command -v certbot &> /dev/null; then
    apt install -y certbot python3-certbot-nginx
    print_success "Certbot installed"
else
    print_success "Certbot already installed"
fi

echo ""

# Step 2: Setup realtime server
echo "2️⃣ SETTING UP REALTIME SERVER"
echo "-----------------------------"

if [ -d "$REALTIME_DIR" ]; then
    cd "$REALTIME_DIR"
    
    # Install dependencies
    print_info "Installing Node.js dependencies..."
    npm install --production
    print_success "Dependencies installed"
    
    # Create production environment file
    if [ ! -f ".env.production" ]; then
        print_info "Creating .env.production file..."
        cat > .env.production << EOF
# MechaMap Realtime Server - Production Configuration
NODE_ENV=production
PORT=3000
HOST=127.0.0.1

# SSL Configuration
SSL_ENABLED=false

# Database Configuration
DB_HOST=localhost
DB_PORT=3306
DB_NAME=mechamap_db
DB_USER=mechamap_user
DB_PASSWORD=YPF1Dt5JyTgxJ95R
DB_CONNECTION_LIMIT=20
DB_TIMEOUT=30000

# Redis Configuration (if available)
REDIS_HOST=localhost
REDIS_PORT=6379
REDIS_PASSWORD=
REDIS_DB=1
REDIS_PREFIX=mechamap_realtime_prod:

# JWT Configuration - MUST MATCH LARAVEL
JWT_SECRET=cc779c53b425a9c6efab2e9def898a025bc077dec144726be95bd50916345e02d2535935490f7c047506c7ae494d5d4372d38189a5c4d8922a326d79090ae744
JWT_EXPIRES_IN=24h
JWT_ALGORITHM=HS256

# Laravel Integration
LARAVEL_API_URL=https://$MAIN_DOMAIN
LARAVEL_API_KEY=mechamap_ws_kCTy45s4obktB6IJJH6DpKHzoveEJLgrnmbST8fxwufexn0u80RnqMSO51ubWVQ3
LARAVEL_DB_CONNECTION=true

# CORS Configuration
CORS_ORIGIN=https://$MAIN_DOMAIN,https://www.$MAIN_DOMAIN
CORS_CREDENTIALS=true

# Admin Configuration
ADMIN_KEY=prod_admin_key_change_this_in_production

# Rate Limiting
RATE_LIMIT_WINDOW_MS=60000
RATE_LIMIT_MAX_REQUESTS=100
RATE_LIMIT_SKIP_SUCCESSFUL_REQUESTS=false

# WebSocket Configuration
WS_PING_TIMEOUT=60000
WS_PING_INTERVAL=25000
WS_MAX_HTTP_BUFFER_SIZE=1e6
WS_TRANSPORTS=websocket,polling
WS_UPGRADE_TIMEOUT=10000

# Connection Limits
MAX_CONNECTIONS=1000
MAX_CONNECTIONS_PER_USER=5
CONNECTION_TIMEOUT=30000
HEARTBEAT_INTERVAL=25000

# Logging Configuration
LOG_LEVEL=info
LOG_FILE=./logs/app.log
LOG_MAX_SIZE=50m
LOG_MAX_FILES=10
LOG_DATE_PATTERN=YYYY-MM-DD

# Monitoring & Health Checks
METRICS_ENABLED=true
HEALTH_CHECK_INTERVAL=30000
HEALTH_CHECK_TIMEOUT=5000
PROMETHEUS_METRICS=true

# Performance Tuning
CLUSTER_ENABLED=true
CLUSTER_INSTANCES=2
MEMORY_LIMIT=1024
CPU_LIMIT=2

# Security Settings
TRUST_PROXY=true

# Production Settings
DEBUG_MODE=false
VERBOSE_LOGGING=false
MOCK_LARAVEL_API=false

# Environment Detection
ENVIRONMENT_TYPE=production
IS_LOCAL=false
IS_PRODUCTION=true
EOF
        print_success ".env.production created"
    else
        print_success ".env.production already exists"
    fi
    
    # Start with PM2
    print_info "Starting realtime server with PM2..."
    pm2 start ecosystem.config.js --env production
    pm2 save
    pm2 startup
    print_success "Realtime server started with PM2"
    
else
    print_error "Realtime server directory not found: $REALTIME_DIR"
    print_info "Make sure you've deployed the project to $PROJECT_DIR"
    exit 1
fi

echo ""

# Step 3: Setup Nginx
echo "3️⃣ SETTING UP NGINX CONFIGURATION"
echo "----------------------------------"

# Create nginx config
print_info "Creating nginx configuration..."
cat > /etc/nginx/sites-available/$DOMAIN << 'EOF'
# Nginx Configuration for MechaMap Realtime WebSocket Server

upstream realtime_backend {
    server 127.0.0.1:3000;
    keepalive 32;
}

server {
    listen 80;
    server_name realtime.mechamap.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name realtime.mechamap.com;
    
    # SSL will be configured by certbot
    
    # Security Headers
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
    add_header X-Frame-Options DENY always;
    add_header X-Content-Type-Options nosniff always;
    
    # Logging
    access_log /var/log/nginx/realtime.mechamap.com.access.log;
    error_log /var/log/nginx/realtime.mechamap.com.error.log;
    
    location / {
        proxy_pass http://realtime_backend;
        
        # Basic proxy headers
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        
        # WebSocket headers
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
        
        # Timeouts
        proxy_connect_timeout 60s;
        proxy_send_timeout 60s;
        proxy_read_timeout 86400s;
        
        # Disable buffering
        proxy_buffering off;
        proxy_request_buffering off;
        
        # CORS
        add_header Access-Control-Allow-Origin "https://mechamap.com" always;
        add_header Access-Control-Allow-Credentials "true" always;
    }
}
EOF

# Enable site
ln -sf /etc/nginx/sites-available/$DOMAIN /etc/nginx/sites-enabled/
print_success "Nginx configuration created and enabled"

# Test nginx config
if nginx -t; then
    print_success "Nginx configuration is valid"
else
    print_error "Nginx configuration has errors"
    exit 1
fi

echo ""

# Step 4: Setup SSL
echo "4️⃣ SETTING UP SSL CERTIFICATE"
echo "------------------------------"

print_info "Obtaining SSL certificate for $DOMAIN..."
certbot --nginx -d $DOMAIN --non-interactive --agree-tos --email admin@$MAIN_DOMAIN

if [ $? -eq 0 ]; then
    print_success "SSL certificate obtained and configured"
else
    print_warning "SSL certificate setup failed, but continuing..."
fi

echo ""

# Step 5: Setup Firewall
echo "5️⃣ CONFIGURING FIREWALL"
echo "-----------------------"

# Allow necessary ports
ufw allow 22    # SSH
ufw allow 80    # HTTP
ufw allow 443   # HTTPS
ufw --force enable

print_success "Firewall configured"

echo ""

# Step 6: Start services
echo "6️⃣ STARTING SERVICES"
echo "--------------------"

# Reload nginx
systemctl reload nginx
print_success "Nginx reloaded"

# Restart PM2
pm2 restart all
print_success "PM2 processes restarted"

echo ""

# Step 7: Test setup
echo "7️⃣ TESTING SETUP"
echo "----------------"

sleep 5  # Wait for services to start

# Test local server
local_test=$(curl -s -w "%{http_code}" -o /dev/null --max-time 10 "http://localhost:3000/api/health")
if [ "$local_test" = "200" ]; then
    print_success "Local realtime server is responding"
else
    print_warning "Local realtime server test failed (HTTP $local_test)"
fi

# Test HTTPS
https_test=$(curl -s -w "%{http_code}" -o /dev/null --max-time 10 "https://$DOMAIN/api/health")
if [ "$https_test" = "200" ]; then
    print_success "HTTPS endpoint is working"
else
    print_warning "HTTPS endpoint test failed (HTTP $https_test)"
fi

echo ""

# Summary
echo "🎉 WEBSOCKET SETUP COMPLETED!"
echo "============================="
echo ""
echo "✅ Services Status:"
echo "   • Realtime Server: Running on port 3000"
echo "   • PM2: Managing realtime server process"
echo "   • Nginx: Proxying requests to realtime server"
echo "   • SSL: Certificate configured for $DOMAIN"
echo "   • Firewall: Ports 80, 443 opened"
echo ""
echo "🔗 Test URLs:"
echo "   • Health: https://$DOMAIN/api/health"
echo "   • WebSocket: wss://$DOMAIN/socket.io/"
echo "   • Metrics: https://$DOMAIN/api/monitoring/metrics"
echo ""
echo "📋 Next Steps:"
echo "1. Update your Laravel .env with:"
echo "   WEBSOCKET_SERVER_URL=https://$DOMAIN"
echo "   REALTIME_SERVER_URL=https://$DOMAIN"
echo ""
echo "2. Test WebSocket connection from your website"
echo ""
echo "3. Monitor logs:"
echo "   • PM2 logs: pm2 logs"
echo "   • Nginx logs: tail -f /var/log/nginx/realtime.mechamap.com.error.log"
echo ""
echo "🔧 Management Commands:"
echo "   • Restart realtime: pm2 restart mechamap-realtime"
echo "   • Reload nginx: sudo systemctl reload nginx"
echo "   • Check status: pm2 status"
echo ""
