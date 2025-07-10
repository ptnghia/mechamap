#!/bin/bash

echo "=== Testing Thread Bookmark/Follow System ==="
echo ""

# Configuration
BASE_URL="http://localhost:8000"
API_URL="$BASE_URL/api/v1"
TEST_EMAIL="admin@mechamap.test"
TEST_PASSWORD="password"
TEST_THREAD_ID=11

echo "🚀 Starting integration test..."
echo "Base URL: $BASE_URL"
echo "Test Thread ID: $TEST_THREAD_ID"
echo ""

# Function to make API calls
api_call() {
    local method=$1
    local endpoint=$2
    local data=$3
    local headers=$4

    if [[ -n "$data" ]]; then
        curl -s -X "$method" "$API_URL$endpoint" \
            -H "Content-Type: application/json" \
            -H "Accept: application/json" \
            $headers \
            -d "$data"
    else
        curl -s -X "$method" "$API_URL$endpoint" \
            -H "Content-Type: application/json" \
            -H "Accept: application/json" \
            $headers
    fi
}

# Step 1: Login
echo "1️⃣ Testing login..."
LOGIN_DATA="{\"email\":\"$TEST_EMAIL\",\"password\":\"$TEST_PASSWORD\"}"
LOGIN_RESPONSE=$(api_call "POST" "/auth/login" "$LOGIN_DATA")

echo "Login response: $LOGIN_RESPONSE"

# Extract token
TOKEN=$(echo "$LOGIN_RESPONSE" | grep -o '"access_token":"[^"]*"' | cut -d'"' -f4)

if [[ -z "$TOKEN" ]]; then
    echo "❌ Login failed. Trying to get CSRF token and login via web..."

    # Get CSRF token from web page
    CSRF_TOKEN=$(curl -s "$BASE_URL/login" | grep -o 'name="csrf-token" content="[^"]*"' | cut -d'"' -f4)
    echo "CSRF Token: ${CSRF_TOKEN:0:20}..."

    if [[ -n "$CSRF_TOKEN" ]]; then
        # Try web login
        LOGIN_RESPONSE=$(curl -s -X POST "$BASE_URL/login" \
            -H "Content-Type: application/x-www-form-urlencoded" \
            -H "X-CSRF-TOKEN: $CSRF_TOKEN" \
            -d "email=$TEST_EMAIL&password=$TEST_PASSWORD" \
            -c cookies.txt)

        echo "Web login attempted. Now try API with cookies..."
    fi

    exit 1
else
    echo "✅ Login successful! Token: ${TOKEN:0:20}..."
fi

# Step 2: Test Bookmark
echo ""
echo "2️⃣ Testing thread bookmark..."
BOOKMARK_RESPONSE=$(api_call "POST" "/threads/$TEST_THREAD_ID/bookmark" '{}' "-H \"Authorization: Bearer $TOKEN\"")
echo "Bookmark response: $BOOKMARK_RESPONSE"

# Step 3: Test Follow
echo ""
echo "3️⃣ Testing thread follow..."
FOLLOW_RESPONSE=$(api_call "POST" "/threads/$TEST_THREAD_ID/follow" '{}' "-H \"Authorization: Bearer $TOKEN\"")
echo "Follow response: $FOLLOW_RESPONSE"

# Step 4: Check bookmark status
echo ""
echo "4️⃣ Checking bookmark status..."
BOOKMARK_STATUS=$(api_call "GET" "/threads/$TEST_THREAD_ID/bookmark" "" "-H \"Authorization: Bearer $TOKEN\"")
echo "Bookmark status: $BOOKMARK_STATUS"

# Step 5: Check follow status
echo ""
echo "5️⃣ Checking follow status..."
FOLLOW_STATUS=$(api_call "GET" "/threads/$TEST_THREAD_ID/follow" "" "-H \"Authorization: Bearer $TOKEN\"")
echo "Follow status: $FOLLOW_STATUS"

# Step 6: Remove bookmark
echo ""
echo "6️⃣ Testing remove bookmark..."
REMOVE_BOOKMARK=$(api_call "DELETE" "/threads/$TEST_THREAD_ID/bookmark" "" "-H \"Authorization: Bearer $TOKEN\"")
echo "Remove bookmark response: $REMOVE_BOOKMARK"

# Step 7: Remove follow
echo ""
echo "7️⃣ Testing remove follow..."
REMOVE_FOLLOW=$(api_call "DELETE" "/threads/$TEST_THREAD_ID/follow" "" "-H \"Authorization: Bearer $TOKEN\"")
echo "Remove follow response: $REMOVE_FOLLOW"

echo ""
echo "✅ Integration test completed!"
