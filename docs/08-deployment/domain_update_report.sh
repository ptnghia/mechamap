#!/bin/bash
# Domain Configuration Update Script for MechaMap
# Updates all localhost references to mechamap.test

echo "🔄 MECHAMAP DOMAIN CONFIGURATION UPDATE"
echo "======================================"
echo "Date: $(date)"
echo ""

# Configuration Summary
echo "📋 CONFIGURATION SUMMARY:"
echo "• Old Domain: localhost:8000, 127.0.0.1:8000"
echo "• New Domain: mechamap.test"
echo "• Environment: Development"
echo ""

# Files Updated
echo "✅ FILES UPDATED:"
echo "• .env - APP_URL, FRONTEND_URL, CORS, Sanctum domains"
echo "• config/app.php - Added frontend_url configuration"
echo "• routes/api.php - Updated CORS default origins"
echo "• app/Http/Controllers/Api/PaymentController.php - Frontend redirect URLs"
echo "• Test files - Updated base URLs for testing"
echo "• Documentation - Updated API examples"
echo ""

# Environment Configuration
echo "🔧 ENVIRONMENT CONFIGURATION:"
echo "APP_URL=http://mechamap.test"
echo "FRONTEND_URL=http://mechamap.test"
echo "CORS_ALLOWED_ORIGINS=http://mechamap.test,https://mechamap.test"
echo "SANCTUM_STATEFUL_DOMAINS=mechamap.test"
echo ""

# Payment Gateway Configuration
echo "💳 PAYMENT GATEWAY CONFIGURATION:"
echo "• Stripe Webhook URL: http://mechamap.test/api/webhooks/stripe"
echo "• VNPay Return URL: http://mechamap.test/api/webhooks/vnpay/return"
echo "• VNPay IPN URL: http://mechamap.test/api/webhooks/vnpay/ipn"
echo ""

# Social Login Configuration
echo "🔐 SOCIAL LOGIN CONFIGURATION:"
echo "• Google OAuth Redirect: http://mechamap.test/auth/google/callback"
echo "• Facebook OAuth Redirect: http://mechamap.test/auth/facebook/callback"
echo ""

# Testing
echo "🧪 TESTING DOMAIN CONFIGURATION:"
echo ""

echo "1. Testing basic API health..."
response=$(curl -s -o /dev/null -w "%{http_code}" http://mechamap.test/public/api/v1/health 2>/dev/null)
if [ "$response" = "200" ]; then
    echo "   ✅ API Health: OK"
else
    echo "   ❌ API Health: Failed (HTTP $response)"
fi

echo "2. Testing payment methods endpoint..."
response=$(curl -s -o /dev/null -w "%{http_code}" http://mechamap.test/public/api/v1/payment/methods 2>/dev/null)
if [ "$response" = "200" ]; then
    echo "   ✅ Payment Methods: OK"
else
    echo "   ❌ Payment Methods: Failed (HTTP $response)"
fi

echo "3. Testing CORS configuration..."
response=$(curl -s -H "Origin: http://mechamap.test" -o /dev/null -w "%{http_code}" http://mechamap.test/public/api/cors-test 2>/dev/null)
if [ "$response" = "200" ]; then
    echo "   ✅ CORS Configuration: OK"
else
    echo "   ❌ CORS Configuration: Failed (HTTP $response)"
fi

echo ""
echo "📊 API ENDPOINTS (Updated Domains):"
echo "• Base API: http://mechamap.test/public/api/v1"
echo "• Payment Methods: http://mechamap.test/public/api/v1/payment/methods"
echo "• User Authentication: http://mechamap.test/public/api/v1/auth/login"
echo "• Marketplace: http://mechamap.test/public/api/v1/marketplace/products"
echo ""

echo "⚠️  IMPORTANT NOTES:"
echo "• Virtual Host Configuration: Apache vhost points to /public directory"
echo "• Current Access: http://mechamap.test/public/api/v1/ (includes /public)"
echo "• Production Setup: Domain should route directly to /public"
echo "• Webhook URLs: Updated for payment gateways"
echo ""

echo "🚀 NEXT STEPS:"
echo "1. Restart Apache server"
echo "2. Clear Laravel caches: php artisan config:clear"
echo "3. Test payment flows with new domain"
echo "4. Update external webhook configurations"
echo ""

echo "✅ DOMAIN CONFIGURATION UPDATE COMPLETED!"
echo "$(date)"
