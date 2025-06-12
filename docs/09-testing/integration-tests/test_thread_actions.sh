#!/bin/bash

# Test Integration Script for Thread Bookmark/Follow System
echo "=== Thread Bookmark/Follow System Integration Test ==="
echo ""

# Test authentication endpoint first
echo "1. Testing authentication..."
AUTH_RESPONSE=$(curl -s -X POST http://localhost:8000/api/auth/login \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"email":"admin@example.com","password":"password"}')

echo "Auth Response: $AUTH_RESPONSE"

# Extract token (if successful)
TOKEN=$(echo $AUTH_RESPONSE | grep -o '"access_token":"[^"]*"' | cut -d'"' -f4)

if [ ! -z "$TOKEN" ]; then
    echo "✅ Authentication successful"
    echo "Token: ${TOKEN:0:20}..."

    # Test thread bookmark
    echo ""
    echo "2. Testing thread bookmark..."
    BOOKMARK_RESPONSE=$(curl -s -X POST http://localhost:8000/api/threads/1/bookmark \
        -H "Content-Type: application/json" \
        -H "Accept: application/json" \
        -H "Authorization: Bearer $TOKEN")

    echo "Bookmark Response: $BOOKMARK_RESPONSE"

    # Test thread follow
    echo ""
    echo "3. Testing thread follow..."
    FOLLOW_RESPONSE=$(curl -s -X POST http://localhost:8000/api/threads/1/follow \
        -H "Content-Type: application/json" \
        -H "Accept: application/json" \
        -H "Authorization: Bearer $TOKEN")

    echo "Follow Response: $FOLLOW_RESPONSE"

else
    echo "❌ Authentication failed"
    echo "Creating test user and trying again..."

    # Try to register a test user
    REGISTER_RESPONSE=$(curl -s -X POST http://localhost:8000/api/auth/register \
        -H "Content-Type: application/json" \
        -H "Accept: application/json" \
        -d '{"name":"Test User","email":"test@example.com","password":"password","password_confirmation":"password"}')

    echo "Register Response: $REGISTER_RESPONSE"
fi

echo ""
echo "=== Test completed ==="
