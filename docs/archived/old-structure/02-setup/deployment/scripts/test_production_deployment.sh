#!/bin/bash

# MECHAMAP PRODUCTION TESTING SCRIPT
# Run this script to test all systems after deployment
# Usage: bash test_production_deployment.sh

set -e

echo "🧪 Testing MechaMap Production Deployment"
echo "========================================="

DOMAIN="mechamap.com"
API_URL="https://$DOMAIN/api/v1"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Test results counter
TOTAL_TESTS=0
PASSED_TESTS=0

# Function to run test
run_test() {
    local test_name="$1"
    local test_command="$2"
    local expected_status="$3"

    TOTAL_TESTS=$((TOTAL_TESTS + 1))
    echo -n "Testing $test_name... "

    if response=$(eval "$test_command" 2>/dev/null); then
        if [ -n "$expected_status" ]; then
            if echo "$response" | grep -q "$expected_status"; then
                echo -e "${GREEN}✅ PASS${NC}"
                PASSED_TESTS=$((PASSED_TESTS + 1))
            else
                echo -e "${RED}❌ FAIL${NC} (Unexpected response)"
                echo "Response: $response"
            fi
        else
            echo -e "${GREEN}✅ PASS${NC}"
            PASSED_TESTS=$((PASSED_TESTS + 1))
        fi
    else
        echo -e "${RED}❌ FAIL${NC}"
        echo "Command failed: $test_command"
    fi
}

echo "🌐 Testing Basic Connectivity"
echo "=============================="

# Test domain resolution
run_test "Domain DNS Resolution" "dig +short $DOMAIN" ""

# Test HTTP redirect to HTTPS
run_test "HTTP to HTTPS Redirect" "curl -s -I http://$DOMAIN | head -n1" "301\|302"

# Test HTTPS SSL
run_test "HTTPS SSL Certificate" "curl -s -I https://$DOMAIN | head -n1" "200"

echo ""
echo "🔌 Testing API Endpoints"
echo "========================"

# Test API base endpoint
run_test "API Base Endpoint" "curl -s -w '%{http_code}' -o /dev/null $API_URL/health" "200"

# Test marketplace endpoints
run_test "Products Listing" "curl -s -w '%{http_code}' -o /dev/null $API_URL/marketplace/products" "200"

run_test "Categories Listing" "curl -s -w '%{http_code}' -o /dev/null $API_URL/marketplace/categories" "200"

run_test "Featured Products" "curl -s -w '%{http_code}' -o /dev/null $API_URL/marketplace/featured" "200"

# Test payment endpoints
run_test "Payment Methods" "curl -s -w '%{http_code}' -o /dev/null $API_URL/payment/methods" "200"

echo ""
echo "🔒 Testing Security Features"
echo "============================"

# Test security headers
run_test "Security Headers" "curl -s -I https://$DOMAIN | grep -i 'x-frame-options\|x-xss-protection\|x-content-type-options'" ""

# Test CORS configuration
run_test "CORS Headers" "curl -s -H 'Origin: https://$DOMAIN' -I $API_URL/marketplace/products | grep -i 'access-control'" ""

# Test API rate limiting
run_test "Rate Limiting Headers" "curl -s -I $API_URL/marketplace/products | grep -i 'x-ratelimit'" ""

echo ""
echo "💾 Testing Database Connectivity"
echo "================================"

# Test database via API (products count)
PRODUCT_COUNT=$(curl -s "$API_URL/marketplace/products" | grep -o '"total":[0-9]*' | cut -d':' -f2 || echo "0")
if [ "$PRODUCT_COUNT" -gt 0 ]; then
    echo -e "Database Products: ${GREEN}✅ $PRODUCT_COUNT products found${NC}"
    PASSED_TESTS=$((PASSED_TESTS + 1))
else
    echo -e "Database Products: ${RED}❌ No products found${NC}"
fi
TOTAL_TESTS=$((TOTAL_TESTS + 1))

echo ""
echo "🔐 Testing Authentication System"
echo "==============================="

# Test user registration endpoint
run_test "User Registration Endpoint" "curl -s -w '%{http_code}' -o /dev/null -X POST $API_URL/auth/register" "422"

# Test login endpoint
run_test "User Login Endpoint" "curl -s -w '%{http_code}' -o /dev/null -X POST $API_URL/auth/login" "422"

echo ""
echo "💳 Testing Payment Gateways"
echo "==========================="

# Test Stripe configuration
STRIPE_TEST=$(curl -s "$API_URL/payment/methods" | grep -o '"stripe"' || echo "")
if [ "$STRIPE_TEST" = '"stripe"' ]; then
    echo -e "Stripe Integration: ${GREEN}✅ Available${NC}"
    PASSED_TESTS=$((PASSED_TESTS + 1))
else
    echo -e "Stripe Integration: ${RED}❌ Not available${NC}"
fi
TOTAL_TESTS=$((TOTAL_TESTS + 1))

# Test VNPay configuration
VNPAY_TEST=$(curl -s "$API_URL/payment/methods" | grep -o '"vnpay"' || echo "")
if [ "$VNPAY_TEST" = '"vnpay"' ]; then
    echo -e "VNPay Integration: ${GREEN}✅ Available${NC}"
    PASSED_TESTS=$((PASSED_TESTS + 1))
else
    echo -e "VNPay Integration: ${RED}❌ Not available${NC}"
fi
TOTAL_TESTS=$((TOTAL_TESTS + 1))

echo ""
echo "📁 Testing File System"
echo "======================"

# Test storage directory permissions
if [ -d "/var/www/mechamap/storage" ]; then
    STORAGE_PERMS=$(stat -c %a /var/www/mechamap/storage)
    if [ "$STORAGE_PERMS" = "775" ]; then
        echo -e "Storage Permissions: ${GREEN}✅ Correct ($STORAGE_PERMS)${NC}"
        PASSED_TESTS=$((PASSED_TESTS + 1))
    else
        echo -e "Storage Permissions: ${YELLOW}⚠️ Warning ($STORAGE_PERMS, expected 775)${NC}"
    fi
else
    echo -e "Storage Directory: ${RED}❌ Not found${NC}"
fi
TOTAL_TESTS=$((TOTAL_TESTS + 1))

# Test protected files directory
if [ -d "/var/www/mechamap/storage/app/protected" ]; then
    echo -e "Protected Files Directory: ${GREEN}✅ Exists${NC}"
    PASSED_TESTS=$((PASSED_TESTS + 1))
else
    echo -e "Protected Files Directory: ${RED}❌ Missing${NC}"
fi
TOTAL_TESTS=$((TOTAL_TESTS + 1))

echo ""
echo "🚀 Testing Performance"
echo "======================"

# Test page load time
LOAD_TIME=$(curl -w "%{time_total}\n" -o /dev/null -s https://$DOMAIN)
if (( $(echo "$LOAD_TIME < 2.0" | bc -l) )); then
    echo -e "Page Load Time: ${GREEN}✅ ${LOAD_TIME}s (Good)${NC}"
    PASSED_TESTS=$((PASSED_TESTS + 1))
else
    echo -e "Page Load Time: ${YELLOW}⚠️ ${LOAD_TIME}s (Slow)${NC}"
fi
TOTAL_TESTS=$((TOTAL_TESTS + 1))

# Test API response time
API_TIME=$(curl -w "%{time_total}\n" -o /dev/null -s $API_URL/marketplace/products)
if (( $(echo "$API_TIME < 1.0" | bc -l) )); then
    echo -e "API Response Time: ${GREEN}✅ ${API_TIME}s (Excellent)${NC}"
    PASSED_TESTS=$((PASSED_TESTS + 1))
else
    echo -e "API Response Time: ${YELLOW}⚠️ ${API_TIME}s (Acceptable)${NC}"
fi
TOTAL_TESTS=$((TOTAL_TESTS + 1))

echo ""
echo "📊 Testing Results Summary"
echo "=========================="
echo "Total Tests: $TOTAL_TESTS"
echo "Passed: $PASSED_TESTS"
echo "Failed: $((TOTAL_TESTS - PASSED_TESTS))"

PASS_RATE=$((PASSED_TESTS * 100 / TOTAL_TESTS))
echo "Pass Rate: $PASS_RATE%"

if [ $PASS_RATE -ge 90 ]; then
    echo -e "${GREEN}🎉 EXCELLENT! System is production ready${NC}"
    EXIT_CODE=0
elif [ $PASS_RATE -ge 80 ]; then
    echo -e "${YELLOW}⚠️ GOOD. Minor issues to address${NC}"
    EXIT_CODE=0
else
    echo -e "${RED}❌ CRITICAL ISSUES. Deployment needs attention${NC}"
    EXIT_CODE=1
fi

echo ""
echo "🔧 System Information"
echo "====================="
echo "Server: $(curl -s ifconfig.me || echo 'Unknown')"
echo "Domain: $DOMAIN"
echo "PHP Version: $(php -v | head -n1 || echo 'Unknown')"
echo "Laravel Version: $(cd /var/www/mechamap && php artisan --version || echo 'Unknown')"
echo "Database: $(mysql --version || echo 'Unknown')"
echo "Web Server: $(nginx -v 2>&1 || echo 'Unknown')"

echo ""
if [ $EXIT_CODE -eq 0 ]; then
    echo -e "${GREEN}✅ MechaMap is ready for production use!${NC}"
    echo "🚀 You can announce the launch!"
else
    echo -e "${RED}❌ Please fix critical issues before going live${NC}"
    echo "🔧 Review failed tests and re-run deployment"
fi

exit $EXIT_CODE
