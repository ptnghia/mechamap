#!/bin/bash

# MechaMap Realtime Server Production Deployment Script for FastPanel
# Domain: https://realtime.mechamap.com
# Environment: VPS with FastPanel (SSL termination at proxy level)

set -e  # Exit on any error

echo "🚀 Starting MechaMap Realtime Server Production Deployment (FastPanel)..."
echo "========================================================================="

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
if [ ! -f "realtime-server/package.json" ]; then
    print_error "realtime-server/package.json not found. Please run this script from the Laravel root directory."
    exit 1
fi

# Navigate to realtime server directory
cd realtime-server

print_status "Current directory: $(pwd)"

# Check Node.js version
print_status "Checking Node.js version..."
NODE_VERSION=$(node --version)
print_success "Node.js version: $NODE_VERSION"

# Check if PM2 is installed
if ! command -v pm2 &> /dev/null; then
    print_warning "PM2 not found. Installing PM2 globally..."
    npm install -g pm2
    print_success "PM2 installed"
else
    print_success "PM2 is already installed"
fi

# Install/Update dependencies for production
print_status "Installing Node.js dependencies for production..."
npm ci --production
print_success "Dependencies installed"

# Create logs directory if not exists
print_status "Creating logs directory..."
mkdir -p logs
print_success "Logs directory ready"

# Stop existing PM2 processes
print_status "Stopping existing PM2 processes..."
pm2 stop mechamap-realtime 2>/dev/null || echo "No existing process to stop"
pm2 delete mechamap-realtime 2>/dev/null || echo "No existing process to delete"

# Start with PM2 in production mode
print_status "Starting realtime server with PM2..."
pm2 start ecosystem.config.js --env production
print_success "Realtime server started with PM2"

# Save PM2 configuration
print_status "Saving PM2 configuration..."
pm2 save
print_success "PM2 configuration saved"

# Setup PM2 startup script
print_status "Setting up PM2 startup script..."
pm2 startup
print_warning "Please run the command shown above as root to enable PM2 auto-start"

# Show PM2 status
print_status "PM2 Status:"
pm2 status

# Show logs
print_status "Recent logs:"
pm2 logs mechamap-realtime --lines 10

echo ""
echo "========================================================================="
print_success "🎉 Realtime Server deployment completed successfully!"
echo ""
print_status "Service Information:"
echo "  - Service Name: mechamap-realtime"
echo "  - Internal Port: 3000 (127.0.0.1:3000)"
echo "  - Public URL: https://realtime.mechamap.com"
echo "  - Environment: production (FastPanel)"
echo "  - Process Manager: PM2"
echo "  - SSL: Handled by FastPanel proxy"
echo ""
print_status "Useful PM2 Commands:"
echo "  - View logs: pm2 logs mechamap-realtime"
echo "  - Restart: pm2 restart mechamap-realtime"
echo "  - Stop: pm2 stop mechamap-realtime"
echo "  - Monitor: pm2 monit"
echo "  - Status: pm2 status"
echo ""
print_status "Health Check URLs:"
echo "  - Health: https://realtime.mechamap.com/api/health"
echo "  - Metrics: https://realtime.mechamap.com/api/monitoring/metrics"
echo "  - Prometheus: https://realtime.mechamap.com/api/monitoring/prometheus"
echo ""
print_warning "FastPanel Configuration Steps:"
echo "  1. ✅ Node.js service running on 127.0.0.1:3000"
echo "  2. 🔧 Configure FastPanel reverse proxy:"
echo "     - Domain: realtime.mechamap.com"
echo "     - Target: http://127.0.0.1:3000"
echo "     - Enable WebSocket support"
echo "  3. 🔒 Install SSL certificate for realtime.mechamap.com in FastPanel"
echo "  4. 🔥 Configure firewall to block direct access to port 3000"
echo "  5. 📊 Set up monitoring and log rotation"
echo ""
print_status "FastPanel Proxy Configuration:"
echo "  - Source: https://realtime.mechamap.com"
echo "  - Target: http://127.0.0.1:3000"
echo "  - WebSocket: Enabled"
echo "  - SSL: Let's Encrypt (FastPanel managed)"
echo "========================================================================="
