#!/bin/bash

# Test MechaMap Realtime Server
# Kiểm tra tất cả endpoints của realtime server

echo "🔍 TESTING MECHAMAP REALTIME SERVER"
echo "=================================="

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

# Server configuration
SERVER_URL="http://localhost:3000"
TIMEOUT=10

echo ""
echo "🌐 Server URL: $SERVER_URL"
echo "⏱️ Timeout: ${TIMEOUT}s"
echo ""

# Test 1: Health Check
echo "1️⃣ TESTING HEALTH CHECK"
echo "----------------------"
response=$(curl -s -w "%{http_code}" -o /tmp/health_response.json --max-time $TIMEOUT "$SERVER_URL/api/health" 2>/dev/null)
http_code="${response: -3}"

if [ "$http_code" = "200" ]; then
    print_success "Health check passed"
    echo "   Response: $(cat /tmp/health_response.json | jq -r '.status // "N/A"')"
    uptime=$(cat /tmp/health_response.json | jq -r '.uptime // "N/A"')
    echo "   Uptime: ${uptime}s"
else
    print_error "Health check failed (HTTP $http_code)"
fi
echo ""

# Test 2: Monitoring Metrics
echo "2️⃣ TESTING MONITORING METRICS"
echo "-----------------------------"
response=$(curl -s -w "%{http_code}" -o /tmp/metrics_response.json --max-time $TIMEOUT "$SERVER_URL/api/monitoring/metrics" 2>/dev/null)
http_code="${response: -3}"

if [ "$http_code" = "200" ]; then
    print_success "Metrics endpoint working"
    connections=$(cat /tmp/metrics_response.json | jq -r '.data.connections.total // "N/A"')
    echo "   Total connections: $connections"
    uptime=$(cat /tmp/metrics_response.json | jq -r '.data.uptime.seconds // "N/A"')
    echo "   Uptime: ${uptime}s"
else
    print_error "Metrics endpoint failed (HTTP $http_code)"
fi
echo ""

# Test 3: Monitoring Health
echo "3️⃣ TESTING MONITORING HEALTH"
echo "----------------------------"
response=$(curl -s -w "%{http_code}" -o /tmp/monitoring_health_response.json --max-time $TIMEOUT "$SERVER_URL/api/monitoring/health" 2>/dev/null)
http_code="${response: -3}"

if [ "$http_code" = "200" ]; then
    print_success "Monitoring health endpoint working"
    status=$(cat /tmp/monitoring_health_response.json | jq -r '.status // "N/A"')
    echo "   Status: $status"
else
    print_error "Monitoring health endpoint failed (HTTP $http_code)"
fi
echo ""

# Test 4: Socket.IO Endpoint
echo "4️⃣ TESTING SOCKET.IO ENDPOINT"
echo "-----------------------------"
response=$(curl -s -w "%{http_code}" -o /tmp/socketio_response.txt --max-time $TIMEOUT "$SERVER_URL/socket.io/" 2>/dev/null)
http_code="${response: -3}"

if [ "$http_code" = "400" ] || [ "$http_code" = "200" ]; then
    print_success "Socket.IO endpoint accessible"
    echo "   HTTP Code: $http_code (expected 400 for GET request)"
else
    print_error "Socket.IO endpoint failed (HTTP $http_code)"
fi
echo ""

# Test 5: CORS Headers
echo "5️⃣ TESTING CORS HEADERS"
echo "-----------------------"
cors_headers=$(curl -s -I --max-time $TIMEOUT "$SERVER_URL/api/health" 2>/dev/null | grep -i "access-control")

if [ ! -z "$cors_headers" ]; then
    print_success "CORS headers present"
    echo "$cors_headers" | while read line; do
        echo "   $line"
    done
else
    print_warning "No CORS headers found"
fi
echo ""

# Test 6: Server Response Time
echo "6️⃣ TESTING SERVER RESPONSE TIME"
echo "-------------------------------"
start_time=$(date +%s%N)
curl -s -o /dev/null --max-time $TIMEOUT "$SERVER_URL/api/health" 2>/dev/null
end_time=$(date +%s%N)
response_time=$(( (end_time - start_time) / 1000000 ))

if [ $response_time -lt 1000 ]; then
    print_success "Response time: ${response_time}ms (excellent)"
elif [ $response_time -lt 3000 ]; then
    print_warning "Response time: ${response_time}ms (acceptable)"
else
    print_error "Response time: ${response_time}ms (slow)"
fi
echo ""

# Test 7: Server Process Check
echo "7️⃣ CHECKING SERVER PROCESS"
echo "--------------------------"
if command -v netstat &> /dev/null; then
    port_check=$(netstat -an | grep ":3000" | grep LISTEN)
    if [ ! -z "$port_check" ]; then
        print_success "Server listening on port 3000"
        echo "   $port_check"
    else
        print_error "No process listening on port 3000"
    fi
else
    print_info "netstat not available, skipping port check"
fi
echo ""

# Summary
echo "📋 TEST SUMMARY"
echo "==============="

# Count successful tests
success_count=0
total_tests=6

# Health check
if [ "$(cat /tmp/health_response.json 2>/dev/null | jq -r '.status // ""')" = "healthy" ]; then
    ((success_count++))
fi

# Metrics
if [ "$(cat /tmp/metrics_response.json 2>/dev/null | jq -r '.success // ""')" = "true" ]; then
    ((success_count++))
fi

# Monitoring health
if [ "$(cat /tmp/monitoring_health_response.json 2>/dev/null | jq -r '.status // ""')" = "healthy" ]; then
    ((success_count++))
fi

# Socket.IO (400 or 200 is acceptable)
socketio_code=$(curl -s -w "%{http_code}" -o /dev/null --max-time $TIMEOUT "$SERVER_URL/socket.io/" 2>/dev/null)
if [ "$socketio_code" = "400" ] || [ "$socketio_code" = "200" ]; then
    ((success_count++))
fi

# CORS
if [ ! -z "$(curl -s -I --max-time $TIMEOUT "$SERVER_URL/api/health" 2>/dev/null | grep -i "access-control")" ]; then
    ((success_count++))
fi

# Response time
if [ $response_time -lt 3000 ]; then
    ((success_count++))
fi

echo "Tests passed: $success_count/$total_tests"

if [ $success_count -eq $total_tests ]; then
    print_success "All tests passed! Realtime server is working correctly."
    echo ""
    echo "🎉 REALTIME SERVER STATUS: HEALTHY"
    echo "✅ Ready for WebSocket connections"
    echo "✅ Monitoring endpoints working"
    echo "✅ CORS configured properly"
    echo "✅ Response times acceptable"
elif [ $success_count -gt $((total_tests / 2)) ]; then
    print_warning "Most tests passed, but some issues detected."
    echo ""
    echo "⚠️ REALTIME SERVER STATUS: PARTIALLY WORKING"
else
    print_error "Multiple tests failed. Server may have issues."
    echo ""
    echo "❌ REALTIME SERVER STATUS: ISSUES DETECTED"
fi

echo ""
echo "🔗 WebSocket URL: ws://localhost:3000"
echo "📊 Metrics URL: http://localhost:3000/api/monitoring/metrics"
echo "🏥 Health URL: http://localhost:3000/api/health"

# Cleanup
rm -f /tmp/health_response.json /tmp/metrics_response.json /tmp/monitoring_health_response.json /tmp/socketio_response.txt
