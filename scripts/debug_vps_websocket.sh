#!/bin/bash

# Debug WebSocket Connection Issues on VPS
# Kiểm tra tất cả các vấn đề có thể gây ra lỗi WebSocket connection

echo "🔍 DEBUGGING WEBSOCKET CONNECTION ON VPS"
echo "========================================"

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
LOCAL_PORT="3000"
TIMEOUT=10

echo ""
echo "🌐 Domain: $DOMAIN"
echo "🔌 Local Port: $LOCAL_PORT"
echo "⏱️ Timeout: ${TIMEOUT}s"
echo ""

# Test 1: DNS Resolution
echo "1️⃣ TESTING DNS RESOLUTION"
echo "-------------------------"
dns_result=$(nslookup $DOMAIN 2>/dev/null | grep "Address:" | tail -1 | awk '{print $2}')
if [ ! -z "$dns_result" ]; then
    print_success "DNS resolves to: $dns_result"
else
    print_error "DNS resolution failed for $DOMAIN"
fi
echo ""

# Test 2: Local Realtime Server
echo "2️⃣ TESTING LOCAL REALTIME SERVER"
echo "--------------------------------"
local_health=$(curl -s -w "%{http_code}" -o /tmp/local_health.json --max-time $TIMEOUT "http://localhost:$LOCAL_PORT/api/health" 2>/dev/null)
local_code="${local_health: -3}"

if [ "$local_code" = "200" ]; then
    print_success "Local realtime server is running"
    uptime=$(cat /tmp/local_health.json 2>/dev/null | grep -o '"uptime":[0-9.]*' | cut -d':' -f2)
    echo "   Uptime: ${uptime}s"
else
    print_error "Local realtime server is NOT running (HTTP $local_code)"
    echo "   Run: cd realtime-server && npm run start:production"
fi
echo ""

# Test 3: Process Check
echo "3️⃣ CHECKING REALTIME SERVER PROCESS"
echo "-----------------------------------"
if command -v pm2 &> /dev/null; then
    pm2_status=$(pm2 list | grep "mechamap-realtime" | awk '{print $10}')
    if [ "$pm2_status" = "online" ]; then
        print_success "PM2 process is online"
    else
        print_error "PM2 process is not online: $pm2_status"
        echo "   Run: cd realtime-server && pm2 start ecosystem.config.js --env production"
    fi
else
    # Check with netstat
    port_check=$(netstat -tulpn 2>/dev/null | grep ":$LOCAL_PORT" | grep LISTEN)
    if [ ! -z "$port_check" ]; then
        print_success "Process listening on port $LOCAL_PORT"
    else
        print_error "No process listening on port $LOCAL_PORT"
    fi
fi
echo ""

# Test 4: SSL Certificate
echo "4️⃣ TESTING SSL CERTIFICATE"
echo "---------------------------"
ssl_check=$(echo | openssl s_client -servername $DOMAIN -connect $DOMAIN:443 2>/dev/null | openssl x509 -noout -dates 2>/dev/null)
if [ ! -z "$ssl_check" ]; then
    print_success "SSL certificate exists"
    echo "$ssl_check" | while read line; do
        echo "   $line"
    done
else
    print_error "SSL certificate not found or invalid"
    echo "   Setup SSL: sudo certbot --nginx -d $DOMAIN"
fi
echo ""

# Test 5: HTTPS Health Check
echo "5️⃣ TESTING HTTPS HEALTH CHECK"
echo "-----------------------------"
https_health=$(curl -s -w "%{http_code}" -o /tmp/https_health.json --max-time $TIMEOUT "https://$DOMAIN/api/health" 2>/dev/null)
https_code="${https_health: -3}"

if [ "$https_code" = "200" ]; then
    print_success "HTTPS health check passed"
    status=$(cat /tmp/https_health.json 2>/dev/null | grep -o '"status":"[^"]*' | cut -d'"' -f4)
    echo "   Status: $status"
else
    print_error "HTTPS health check failed (HTTP $https_code)"
    echo "   Check nginx configuration and SSL setup"
fi
echo ""

# Test 6: WebSocket Endpoint
echo "6️⃣ TESTING WEBSOCKET ENDPOINT"
echo "-----------------------------"
ws_test=$(curl -s -w "%{http_code}" -o /tmp/ws_test.txt --max-time $TIMEOUT "https://$DOMAIN/socket.io/" 2>/dev/null)
ws_code="${ws_test: -3}"

if [ "$ws_code" = "400" ] || [ "$ws_code" = "200" ]; then
    print_success "WebSocket endpoint accessible (HTTP $ws_code)"
else
    print_error "WebSocket endpoint failed (HTTP $ws_code)"
fi
echo ""

# Test 7: Nginx Configuration
echo "7️⃣ CHECKING NGINX CONFIGURATION"
echo "-------------------------------"
if [ -f "/etc/nginx/sites-available/$DOMAIN" ]; then
    print_success "Nginx config file exists"
    
    # Check for WebSocket headers
    if grep -q "proxy_set_header Upgrade" "/etc/nginx/sites-available/$DOMAIN"; then
        print_success "WebSocket headers configured"
    else
        print_error "WebSocket headers missing in nginx config"
        echo "   Add: proxy_set_header Upgrade \$http_upgrade;"
        echo "   Add: proxy_set_header Connection \"upgrade\";"
    fi
    
    # Check for SSL
    if grep -q "ssl_certificate" "/etc/nginx/sites-available/$DOMAIN"; then
        print_success "SSL configured in nginx"
    else
        print_warning "SSL not configured in nginx"
    fi
else
    print_error "Nginx config file not found: /etc/nginx/sites-available/$DOMAIN"
fi
echo ""

# Test 8: Firewall Check
echo "8️⃣ CHECKING FIREWALL"
echo "--------------------"
if command -v ufw &> /dev/null; then
    ufw_status=$(ufw status | grep "Status:")
    echo "   UFW: $ufw_status"
    
    if ufw status | grep -q "443.*ALLOW"; then
        print_success "Port 443 (HTTPS) is allowed"
    else
        print_warning "Port 443 may not be allowed"
        echo "   Run: sudo ufw allow 443"
    fi
    
    if ufw status | grep -q "80.*ALLOW"; then
        print_success "Port 80 (HTTP) is allowed"
    else
        print_warning "Port 80 may not be allowed"
        echo "   Run: sudo ufw allow 80"
    fi
else
    print_info "UFW not available, checking iptables..."
    if command -v iptables &> /dev/null; then
        iptables_443=$(iptables -L | grep -i "443\|https")
        if [ ! -z "$iptables_443" ]; then
            print_success "Port 443 rules found in iptables"
        else
            print_warning "No port 443 rules in iptables"
        fi
    fi
fi
echo ""

# Test 9: CORS Headers
echo "9️⃣ TESTING CORS HEADERS"
echo "-----------------------"
cors_headers=$(curl -s -I --max-time $TIMEOUT "https://$DOMAIN/api/health" 2>/dev/null | grep -i "access-control")
if [ ! -z "$cors_headers" ]; then
    print_success "CORS headers present"
    echo "$cors_headers" | while read line; do
        echo "   $line"
    done
else
    print_warning "No CORS headers found"
fi
echo ""

# Summary and Recommendations
echo "📋 SUMMARY & RECOMMENDATIONS"
echo "============================"

issues_found=0

# Check each component
if [ "$local_code" != "200" ]; then
    print_error "Issue: Local realtime server not running"
    echo "   Fix: cd realtime-server && pm2 start ecosystem.config.js --env production"
    ((issues_found++))
fi

if [ "$https_code" != "200" ]; then
    print_error "Issue: HTTPS endpoint not accessible"
    echo "   Fix: Check nginx config and SSL certificate"
    ((issues_found++))
fi

if [ -z "$ssl_check" ]; then
    print_error "Issue: SSL certificate missing"
    echo "   Fix: sudo certbot --nginx -d $DOMAIN"
    ((issues_found++))
fi

if [ ! -f "/etc/nginx/sites-available/$DOMAIN" ]; then
    print_error "Issue: Nginx configuration missing"
    echo "   Fix: Create nginx config with WebSocket support"
    ((issues_found++))
fi

if [ $issues_found -eq 0 ]; then
    print_success "No major issues found!"
    echo ""
    echo "🔧 If WebSocket still fails, try:"
    echo "1. Restart nginx: sudo systemctl restart nginx"
    echo "2. Restart realtime server: pm2 restart mechamap-realtime"
    echo "3. Check browser console for detailed errors"
    echo "4. Test with: wscat -c wss://$DOMAIN/socket.io/"
else
    print_warning "$issues_found issue(s) found that need attention"
fi

echo ""
echo "🔗 Test URLs:"
echo "   Health: https://$DOMAIN/api/health"
echo "   WebSocket: wss://$DOMAIN/socket.io/"
echo "   Metrics: https://$DOMAIN/api/monitoring/metrics"

# Cleanup
rm -f /tmp/local_health.json /tmp/https_health.json /tmp/ws_test.txt
