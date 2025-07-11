# 🔄 MechaMap Payment System Migration Guide

## Overview
Hướng dẫn migration từ old payment system sang centralized payment system mới. Migration này sẽ chuyển tất cả payments từ direct seller payments sang centralized admin account model.

## 🎯 Migration Goals

### Before (Old System):
- Payments đi trực tiếp đến seller accounts
- Manual commission calculation
- Fragmented payment tracking
- Limited admin oversight

### After (Centralized System):
- Tất cả payments đi về Admin accounts
- Automated commission calculation
- Centralized payment tracking
- Complete admin control và oversight

## 📋 Pre-Migration Checklist

### 1. Data Backup
```bash
# Backup existing payment data
mysqldump --single-transaction mechamap_backend \
  marketplace_orders \
  marketplace_order_items \
  marketplace_sellers \
  payment_transactions > pre_migration_backup.sql
```

### 2. Environment Preparation
```bash
# Ensure all required environment variables are set
grep -E "(STRIPE|SEPAY)_" .env

# Test database connection
php artisan tinker --execute="DB::connection()->getPdo();"
```

### 3. Dependencies Check
```bash
# Verify Laravel version compatibility
php artisan --version

# Check required packages
composer show | grep -E "(stripe|guzzle)"
```

## 🚀 Migration Steps

### Step 1: Run Database Migrations
```bash
# Run centralized payment migrations
php artisan migrate

# Verify new tables were created
php artisan tinker --execute="
use Illuminate\Support\Facades\Schema;
echo 'Centralized Payments: ' . (Schema::hasTable('centralized_payments') ? 'OK' : 'MISSING') . PHP_EOL;
echo 'Commission Settings: ' . (Schema::hasTable('commission_settings') ? 'OK' : 'MISSING') . PHP_EOL;
echo 'Payout Requests: ' . (Schema::hasTable('seller_payout_requests') ? 'OK' : 'MISSING') . PHP_EOL;
"
```

### Step 2: Seed Initial Configuration
```bash
# Seed payment system settings
php artisan db:seed --class=PaymentSystemSettingsSeeder

# Seed commission settings
php artisan db:seed --class=CommissionSettingsTestDataSeeder
```

### Step 3: Migrate Existing Payment Data

#### 3.1 Create Migration Script
```php
<?php
// database/migrations/migrate_existing_payments.php

use Illuminate\Database\Migrations\Migration;
use App\Models\MarketplaceOrder;
use App\Models\CentralizedPayment;
use App\Services\CentralizedPaymentService;

class MigrateExistingPayments extends Migration
{
    public function up()
    {
        $service = new CentralizedPaymentService();
        
        // Get all completed orders without centralized payment
        $orders = MarketplaceOrder::where('payment_status', 'paid')
            ->whereNull('centralized_payment_id')
            ->get();

        foreach ($orders as $order) {
            // Create centralized payment record for historical data
            $centralizedPayment = CentralizedPayment::create([
                'payment_reference' => CentralizedPayment::generatePaymentReference(),
                'order_id' => $order->id,
                'customer_id' => $order->customer_id,
                'customer_email' => $order->customer_email,
                'payment_method' => $order->payment_method ?? 'legacy',
                'gross_amount' => $order->total_amount,
                'gateway_fee' => 0, // Historical data may not have fee info
                'net_received' => $order->total_amount,
                'status' => 'completed',
                'paid_at' => $order->paid_at ?? $order->updated_at,
                'confirmed_at' => $order->paid_at ?? $order->updated_at,
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,
                'gateway_response' => ['migrated' => true, 'original_order' => $order->id],
            ]);

            // Link order to centralized payment
            $order->update(['centralized_payment_id' => $centralizedPayment->id]);
        }
    }
}
```

#### 3.2 Run Migration Script
```bash
php artisan make:migration migrate_existing_payments
# Copy the above code to the migration file
php artisan migrate
```

### Step 4: Update Frontend Integration

#### 4.1 Update Checkout JavaScript
```javascript
// Replace old payment endpoints with centralized ones

// OLD:
// fetch('/api/v1/payment/stripe/create-intent', ...)

// NEW:
fetch('/api/v1/payment/centralized/stripe/create-intent', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer ' + getAuthToken(),
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    },
    body: JSON.stringify({
        order_id: orderId
    })
})
```

#### 4.2 Update SePay Integration
```javascript
// OLD:
// fetch('/api/v1/payment/sepay/create-payment', ...)

// NEW:
fetch('/api/v1/payment/centralized/sepay/create-payment', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer ' + getAuthToken(),
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    },
    body: JSON.stringify({
        order_id: orderId
    })
})
```

### Step 5: Configure Webhooks

#### 5.1 Update Stripe Webhooks
```bash
# Old webhook endpoint (disable):
# https://mechamap.com/api/v1/payment/stripe/webhook

# New webhook endpoint (enable):
# https://mechamap.com/api/v1/payment/centralized/webhook
```

#### 5.2 Update SePay Webhooks
```bash
# Old webhook endpoint (disable):
# https://mechamap.com/api/v1/payment/sepay/webhook

# New webhook endpoint (enable):
# https://mechamap.com/api/v1/payment/centralized/sepay/webhook
```

### Step 6: Test Migration

#### 6.1 Test Configuration
```bash
curl -X GET "https://mechamap.com/api/v1/payment/test/centralized/configuration" \
  -H "Accept: application/json"
```

#### 6.2 Test Payment Creation
```bash
# Create test order
curl -X POST "https://mechamap.com/api/v1/payment/test/centralized/create-order" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "customer_id": 1,
    "total_amount": 100000,
    "items": [{"product_id": 1, "quantity": 1, "price": 100000}]
  }'

# Test Stripe payment
curl -X POST "https://mechamap.com/api/v1/payment/test/centralized/stripe" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{"order_id": TEST_ORDER_ID}'
```

## 🔄 Rollback Plan

### If Migration Fails:

#### 1. Restore Database
```bash
# Restore from backup
mysql mechamap_backend < pre_migration_backup.sql

# Rollback migrations
php artisan migrate:rollback --step=5
```

#### 2. Revert Frontend Changes
```bash
# Revert checkout files
git checkout HEAD~1 -- resources/views/marketplace/checkout/
```

#### 3. Restore Webhook Endpoints
- Re-enable old webhook endpoints
- Disable new centralized webhook endpoints

## ✅ Post-Migration Verification

### 1. Data Integrity Check
```php
// Verify all orders have centralized payments
$ordersWithoutCentralized = MarketplaceOrder::where('payment_status', 'paid')
    ->whereNull('centralized_payment_id')
    ->count();

echo "Orders without centralized payment: {$ordersWithoutCentralized}" . PHP_EOL;
```

### 2. Payment Flow Test
```bash
# Test complete payment flow
php artisan tinker --execute="
use App\Services\CentralizedPaymentService;
\$service = new CentralizedPaymentService();
echo 'Available methods: ' . json_encode(\$service->getAvailablePaymentMethods()) . PHP_EOL;
"
```

### 3. Admin Panel Access
- Login to admin panel: `https://mechamap.com/admin`
- Navigate to Payment Management: `/admin/payment-management`
- Verify dashboard shows correct data
- Test payout management: `/admin/payout-management`

## 📊 Migration Monitoring

### Key Metrics to Monitor:
- Payment success rate (should remain >95%)
- Average processing time
- Webhook failure rate
- Commission calculation accuracy

### Monitoring Commands:
```bash
# Check recent payments
php artisan tinker --execute="
use App\Models\CentralizedPayment;
echo 'Recent payments: ' . CentralizedPayment::where('created_at', '>', now()->subHour())->count() . PHP_EOL;
"

# Check system health
curl -X GET "https://mechamap.com/api/v1/payment/test/centralized/status"
```

## 🚨 Common Issues & Solutions

### Issue 1: Webhook Verification Failures
**Solution:**
```bash
# Check webhook secret configuration
grep STRIPE_WEBHOOK_SECRET .env
grep SEPAY_WEBHOOK_SECRET .env
```

### Issue 2: Commission Calculation Errors
**Solution:**
```bash
# Verify commission settings
php artisan tinker --execute="
use App\Models\CommissionSetting;
CommissionSetting::active()->get(['seller_role', 'commission_rate']);
"
```

### Issue 3: Database Connection Issues
**Solution:**
```bash
# Check database configuration
php artisan tinker --execute="
try {
    DB::connection()->getPdo();
    echo 'Database connection: OK' . PHP_EOL;
} catch (Exception \$e) {
    echo 'Database error: ' . \$e->getMessage() . PHP_EOL;
}
"
```

## 📞 Support

- **Migration Issues**: tech@mechamap.com
- **Payment Issues**: payments@mechamap.com
- **Emergency Rollback**: +84-xxx-xxx-xxxx

---

**⚠️ Important**: Perform migration during low-traffic hours và có sẵn rollback plan. Test thoroughly trong staging environment trước khi migrate production.
