# 🏦 MechaMap Centralized Payment System - Production Setup Guide

## Overview
Hệ thống thanh toán tập trung MechaMap cho phép tất cả payments từ customers đi về Admin accounts trước khi được distribute cho sellers thông qua payout system.

## 🔧 Environment Configuration

### 1. Stripe Configuration (International Payments)

```env
# Stripe Payment Gateway (International)
STRIPE_KEY=pk_live_your_publishable_key_here
STRIPE_SECRET=sk_live_your_secret_key_here
STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret_here
STRIPE_WEBHOOK_URL=https://mechamap.com/api/v1/payment/centralized/webhook

# 🏦 Centralized Payment Admin Accounts
STRIPE_ADMIN_ACCOUNT_ID=acct_your_admin_account_id
```

### 2. SePay Configuration (Vietnam Domestic Payments)

```env
# SePay Configuration (Vietnam Banking)
SEPAY_API_KEY=your_sepay_api_key
SEPAY_SECRET_KEY=your_sepay_secret_key
SEPAY_WEBHOOK_URL=https://mechamap.com/api/v1/payment/centralized/sepay/webhook

# SePay Admin Bank Account
SEPAY_ADMIN_BANK_CODE=MBBank
SEPAY_ADMIN_ACCOUNT_NUMBER=your_admin_account_number
SEPAY_ADMIN_ACCOUNT_NAME="CONG TY CO PHAN CONG NGHE MECHAMAP"
```

### 3. Database Configuration

```env
# Ensure these are set for production
DB_CONNECTION=mysql
DB_HOST=your_production_db_host
DB_PORT=3306
DB_DATABASE=mechamap_production
DB_USERNAME=your_db_username
DB_PASSWORD=your_secure_db_password
```

## 🚀 Deployment Steps

### Step 1: Database Migration
```bash
# Run centralized payment migrations
php artisan migrate

# Seed payment system settings
php artisan db:seed --class=PaymentSystemSettingsSeeder
```

### Step 2: Configure Payment Gateways

#### Stripe Setup:
1. Create Stripe Connect account for platform
2. Set up webhook endpoint: `https://mechamap.com/api/v1/payment/centralized/webhook`
3. Configure webhook events:
   - `payment_intent.succeeded`
   - `payment_intent.payment_failed`
   - `payment_intent.canceled`

#### SePay Setup:
1. Register business account with SePay
2. Configure webhook endpoint: `https://mechamap.com/api/v1/payment/centralized/sepay/webhook`
3. Set up bank account for receiving payments

### Step 3: Update Payment System Settings
```bash
php artisan tinker
```

```php
use App\Models\PaymentSystemSetting;

// Update Stripe admin account
PaymentSystemSetting::set('admin_bank_account_stripe', [
    'account_id' => env('STRIPE_ADMIN_ACCOUNT_ID'),
    'account_name' => 'MechaMap Admin Account',
    'currency' => 'VND',
    'country' => 'VN',
    'description' => 'Production Stripe account for centralized payments'
]);

// Update SePay admin account
PaymentSystemSetting::set('admin_bank_account_sepay', [
    'bank_code' => env('SEPAY_ADMIN_BANK_CODE'),
    'account_number' => env('SEPAY_ADMIN_ACCOUNT_NUMBER'),
    'account_name' => env('SEPAY_ADMIN_ACCOUNT_NAME'),
    'currency' => 'VND',
    'description' => 'Production SePay account for centralized payments'
]);
```

## 🔐 Security Configuration

### 1. Webhook Security
- Verify webhook signatures for all incoming requests
- Use HTTPS for all webhook endpoints
- Implement rate limiting for webhook endpoints

### 2. Database Security
- Enable SSL for database connections
- Use encrypted backups for financial data
- Implement row-level security for sensitive tables

### 3. API Security
- Use API rate limiting
- Implement request signing for sensitive operations
- Log all financial transactions for audit

## 📊 Monitoring & Alerts

### 1. Payment Monitoring
```php
// Set up monitoring for payment failures
PaymentSystemSetting::set('payment_failure_alert_threshold', 5); // Alert after 5 failures
PaymentSystemSetting::set('payment_failure_alert_email', 'admin@mechamap.com');
```

### 2. Payout Monitoring
```php
// Set up payout monitoring
PaymentSystemSetting::set('payout_approval_timeout_hours', 48); // Alert if not approved in 48h
PaymentSystemSetting::set('large_payout_threshold', 10000000); // 10M VND requires special approval
```

### 3. System Health Checks
- Monitor payment success rates (should be >95%)
- Track average processing times
- Alert on webhook failures
- Monitor database performance for financial tables

## 🔄 Backup & Recovery

### 1. Database Backups
```bash
# Daily backup of financial tables
mysqldump --single-transaction mechamap_production \
  centralized_payments \
  seller_payout_requests \
  seller_payout_items \
  commission_settings \
  payment_audit_logs \
  payment_system_settings > backup_$(date +%Y%m%d).sql
```

### 2. Configuration Backups
- Backup .env file securely
- Document all payment gateway configurations
- Keep encrypted copies of API keys

## 🧪 Testing in Production

### 1. Payment Flow Testing
```bash
# Test centralized payment configuration
curl -X GET "https://mechamap.com/api/v1/payment/test/centralized/configuration" \
  -H "Accept: application/json"
```

### 2. Webhook Testing
```bash
# Test webhook endpoints
curl -X POST "https://mechamap.com/api/v1/payment/centralized/webhook" \
  -H "Content-Type: application/json" \
  -H "Stripe-Signature: test_signature" \
  -d '{"test": true}'
```

## 📈 Performance Optimization

### 1. Database Indexing
```sql
-- Add indexes for frequently queried columns
CREATE INDEX idx_centralized_payments_status ON centralized_payments(status);
CREATE INDEX idx_centralized_payments_created_at ON centralized_payments(created_at);
CREATE INDEX idx_payout_requests_status ON seller_payout_requests(status);
CREATE INDEX idx_audit_logs_entity ON payment_audit_logs(entity_type, entity_id);
```

### 2. Caching
```php
// Cache commission settings
PaymentSystemSetting::set('cache_commission_settings', true);
PaymentSystemSetting::set('commission_cache_ttl', 3600); // 1 hour
```

## 🚨 Troubleshooting

### Common Issues:

1. **Webhook Verification Failures**
   - Check webhook secret configuration
   - Verify HTTPS certificate
   - Check request signature format

2. **Payment Processing Delays**
   - Monitor database performance
   - Check webhook processing times
   - Verify gateway API response times

3. **Commission Calculation Errors**
   - Verify commission settings are active
   - Check effective date ranges
   - Test calculation with known values

### Debug Commands:
```bash
# Check system configuration
php artisan tinker --execute="
use App\Services\CentralizedPaymentService;
\$service = new CentralizedPaymentService();
echo 'Stripe: ' . (\$service->isStripeConfigured() ? 'OK' : 'ERROR') . PHP_EOL;
echo 'SePay: ' . (\$service->isSePayConfigured() ? 'OK' : 'ERROR') . PHP_EOL;
"

# Check recent payment logs
php artisan tinker --execute="
use App\Models\PaymentAuditLog;
PaymentAuditLog::latest()->take(10)->get(['event_type', 'description', 'created_at']);
"
```

## 📞 Support Contacts

- **Technical Issues**: tech@mechamap.com
- **Payment Issues**: payments@mechamap.com
- **Emergency**: +84-xxx-xxx-xxxx

## 📝 Changelog

### Version 1.0.0 (Current)
- Initial centralized payment system
- Stripe and SePay integration
- Admin panel for payment management
- Commission settings management
- Payout request workflow

---

**⚠️ Important**: Always test payment flows in staging environment before deploying to production. Keep encrypted backups of all financial data and API configurations.
