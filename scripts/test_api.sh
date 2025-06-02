#!/bin/bash

# API Testing Script for MechaMap Laravel Forum
# Tác giả: GitHub Copilot
# Ngày tạo: 2025-06-02

echo "🚀 Bắt đầu test API MechaMap Laravel Forum..."

# Cấu hình
BASE_URL="http://localhost:8000/api/v1"
AUTH_TOKEN=""
USER_ID=""
THREAD_SLUG=""

# Colors cho output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function để test endpoint
test_endpoint() {
    local method=$1
    local endpoint=$2
    local data=$3
    local expected_status=$4
    local description=$5

    echo -e "${BLUE}Testing: ${description}${NC}"
    echo -e "${YELLOW}${method} ${BASE_URL}${endpoint}${NC}"

    if [ "$method" = "GET" ]; then
        if [ -n "$AUTH_TOKEN" ]; then
            response=$(curl -s -w "HTTP_STATUS:%{http_code}" \
                -H "Accept: application/json" \
                -H "Authorization: Bearer $AUTH_TOKEN" \
                "${BASE_URL}${endpoint}")
        else
            response=$(curl -s -w "HTTP_STATUS:%{http_code}" \
                -H "Accept: application/json" \
                "${BASE_URL}${endpoint}")
        fi
    elif [ "$method" = "POST" ]; then
        if [ -n "$AUTH_TOKEN" ]; then
            response=$(curl -s -w "HTTP_STATUS:%{http_code}" \
                -X POST \
                -H "Accept: application/json" \
                -H "Content-Type: application/json" \
                -H "Authorization: Bearer $AUTH_TOKEN" \
                -d "$data" \
                "${BASE_URL}${endpoint}")
        else
            response=$(curl -s -w "HTTP_STATUS:%{http_code}" \
                -X POST \
                -H "Accept: application/json" \
                -H "Content-Type: application/json" \
                -d "$data" \
                "${BASE_URL}${endpoint}")
        fi
    fi

    # Extract HTTP status
    http_status=$(echo $response | grep -o "HTTP_STATUS:[0-9]*" | cut -d: -f2)

    # Extract body
    body=$(echo $response | sed -E 's/HTTP_STATUS:[0-9]*$//')

    # Check status
    if [ "$http_status" = "$expected_status" ]; then
        echo -e "${GREEN}✅ PASS - Status: $http_status${NC}"
    else
        echo -e "${RED}❌ FAIL - Expected: $expected_status, Got: $http_status${NC}"
    fi

    # Pretty print JSON if possible
    if command -v jq &> /dev/null; then
        echo "$body" | jq . 2>/dev/null || echo "$body"
    else
        echo "$body"
    fi

    echo "----------------------------------------"

    # Return response for further processing
    echo "$body"
}

# Function để extract value từ JSON response
extract_from_json() {
    local json=$1
    local key=$2

    if command -v jq &> /dev/null; then
        echo "$json" | jq -r "$key" 2>/dev/null
    else
        # Fallback without jq (basic extraction)
        echo "$json" | grep -o "\"$key\":\"[^\"]*\"" | cut -d'"' -f4
    fi
}

echo "📋 Bắt đầu test suite..."
echo ""

# 1. Test CORS
echo "🌐 Test 1: CORS Test"
test_endpoint "GET" "/cors-test" "" "200" "CORS functionality"
echo ""

# 2. Test Authentication - Login
echo "🔐 Test 2: Authentication"
login_data='{"email": "admin@mechamap.com", "password": "password123", "remember_me": true}'
login_response=$(test_endpoint "POST" "/auth/login" "$login_data" "200" "User login")

# Extract token nếu login thành công
if echo "$login_response" | grep -q '"success":true'; then
    AUTH_TOKEN=$(extract_from_json "$login_response" ".data.token")
    USER_ID=$(extract_from_json "$login_response" ".data.user.id")
    echo -e "${GREEN}🎉 Login thành công! Token được lưu.${NC}"
else
    echo -e "${YELLOW}⚠️  Login không thành công, sẽ test với anonymous user${NC}"
fi
echo ""

# 3. Test Get User Profile (nếu có token)
if [ -n "$AUTH_TOKEN" ]; then
    echo "👤 Test 3: Get User Profile"
    test_endpoint "GET" "/auth/me" "" "200" "Get authenticated user profile"
    echo ""
fi

# 4. Test Forums
echo "🏢 Test 4: Forums"
test_endpoint "GET" "/forums" "" "200" "Get forums list"
echo ""

# 5. Test Categories
echo "📂 Test 5: Categories"
test_endpoint "GET" "/categories" "" "200" "Get categories list"
echo ""

# 6. Test Threads
echo "📝 Test 6: Threads"
test_endpoint "GET" "/threads?per_page=5" "" "200" "Get threads list"

# Create thread nếu có auth
if [ -n "$AUTH_TOKEN" ]; then
    echo ""
    echo "✍️  Test 6.1: Create Thread"
    thread_data='{"title": "Test API Thread", "content": "Đây là thread test từ API script", "forum_id": 1, "tags": ["api", "test"]}'
    create_response=$(test_endpoint "POST" "/threads" "$thread_data" "201" "Create new thread")

    # Extract thread slug
    if echo "$create_response" | grep -q '"success":true'; then
        THREAD_SLUG=$(extract_from_json "$create_response" ".data.slug")
        echo -e "${GREEN}📝 Thread created với slug: $THREAD_SLUG${NC}"
    fi
fi
echo ""

# 7. Test Search
echo "🔍 Test 7: Search"
test_endpoint "GET" "/search?q=test&type=threads" "" "200" "Search functionality"
echo ""

# 8. Test Search Suggestions
echo "💡 Test 8: Search Suggestions"
test_endpoint "GET" "/search/suggestions?q=lar" "" "200" "Search suggestions"
echo ""

# 9. Test Showcases
echo "🎨 Test 9: Showcases"
test_endpoint "GET" "/showcases?per_page=5" "" "200" "Get showcases list"
test_endpoint "GET" "/showcases/recent" "" "200" "Get recent showcases"
echo ""

# 10. Test Statistics
echo "📊 Test 10: Statistics"
test_endpoint "GET" "/stats/forum" "" "200" "Forum statistics"
test_endpoint "GET" "/stats/forums/popular" "" "200" "Popular forums"
test_endpoint "GET" "/stats/users/active" "" "200" "Active users"
echo ""

# 11. Test Tags
echo "🏷️  Test 11: Tags"
test_endpoint "GET" "/tags?per_page=10" "" "200" "Get tags list"
echo ""

# 12. Test Settings
echo "⚙️  Test 12: Settings"
test_endpoint "GET" "/settings" "" "200" "Get all settings"
test_endpoint "GET" "/settings/general" "" "200" "Get settings by group"
echo ""

# 13. Test SEO
echo "🔧 Test 13: SEO"
test_endpoint "GET" "/seo" "" "200" "Get SEO settings"
echo ""

# 14. Test Comments (nếu có thread)
if [ -n "$THREAD_SLUG" ] && [ -n "$AUTH_TOKEN" ]; then
    echo "💬 Test 14: Comments"
    comment_data='{"content": "Đây là comment test từ API script"}'
    test_endpoint "POST" "/threads/$THREAD_SLUG/comments" "$comment_data" "201" "Add comment to thread"
    echo ""
fi

# 15. Test Alerts (nếu có auth)
if [ -n "$AUTH_TOKEN" ]; then
    echo "🔔 Test 15: Alerts"
    test_endpoint "GET" "/alerts?per_page=5" "" "200" "Get user alerts"
    echo ""
fi

# 16. Test Thread Actions (nếu có thread và auth)
if [ -n "$THREAD_SLUG" ] && [ -n "$AUTH_TOKEN" ]; then
    echo "❤️  Test 16: Thread Actions"
    test_endpoint "POST" "/threads/$THREAD_SLUG/like" "" "200" "Like thread"
    test_endpoint "POST" "/threads/$THREAD_SLUG/save" "" "200" "Save thread"
    test_endpoint "POST" "/threads/$THREAD_SLUG/follow" "" "200" "Follow thread"
    echo ""
fi

# 17. Test Admin Endpoints (nếu có auth)
if [ -n "$AUTH_TOKEN" ]; then
    echo "🛡️  Test 17: Admin Endpoints"
    test_endpoint "GET" "/admin/showcases" "" "200" "Admin showcases (might return 403)"
    test_endpoint "GET" "/admin/reports" "" "200" "Admin reports (might return 403)"
    echo ""
fi

# 18. Test Error Handling
echo "⚠️  Test 18: Error Handling"
test_endpoint "POST" "/auth/login" '{"email": "invalid", "password": ""}' "422" "Invalid login data"
test_endpoint "GET" "/threads/non-existent-slug" "" "404" "Non-existent thread"
echo ""

# Summary
echo "🎯 Test Summary"
echo "=============="
echo -e "Base URL: ${BLUE}$BASE_URL${NC}"
echo -e "Auth Token: ${YELLOW}${AUTH_TOKEN:0:20}...${NC}"
echo -e "User ID: ${GREEN}$USER_ID${NC}"
echo -e "Thread Slug: ${GREEN}$THREAD_SLUG${NC}"
echo ""
echo -e "${GREEN}✅ API testing completed!${NC}"
echo ""
echo "📝 Notes:"
echo "- Nếu có lỗi 401/403, kiểm tra authentication"
echo "- Nếu có lỗi 404, kiểm tra routes và database"
echo "- Nếu có lỗi 500, kiểm tra logs: storage/logs/laravel.log"
echo ""
echo "🔧 Để chạy Laravel tests:"
echo "cd /d/xampp/htdocs/laravel/mechamap_backend"
echo "php artisan test tests/Feature/Api/ApiTestSuite.php"
