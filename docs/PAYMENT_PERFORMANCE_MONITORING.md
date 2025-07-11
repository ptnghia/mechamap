# 📊 MechaMap Centralized Payment Performance Monitoring

## Overview
Comprehensive monitoring guide cho centralized payment system performance, security, và reliability.

## 🎯 Key Performance Indicators (KPIs)

### 1. Payment Success Metrics
- **Payment Success Rate**: >95% (Target: >98%)
- **Average Processing Time**: <30 seconds (Target: <15 seconds)
- **Webhook Success Rate**: >99% (Target: >99.5%)
- **Gateway Uptime**: >99.9%

### 2. Financial Metrics
- **Daily Transaction Volume**: Track trends
- **Commission Accuracy**: 100% (Zero tolerance for errors)
- **Payout Processing Time**: <48 hours (Target: <24 hours)
- **Failed Payment Recovery Rate**: >80%

### 3. System Performance Metrics
- **Database Query Time**: <100ms average
- **API Response Time**: <500ms (95th percentile)
- **Memory Usage**: <80% of available
- **CPU Usage**: <70% average

## 🔍 Monitoring Setup

### 1. Database Performance Monitoring

#### Query Performance
```sql
-- Monitor slow payment queries
SELECT 
    query_time,
    lock_time,
    rows_sent,
    rows_examined,
    sql_text
FROM mysql.slow_log 
WHERE sql_text LIKE '%centralized_payments%'
ORDER BY query_time DESC
LIMIT 10;
```

#### Table Size Monitoring
```sql
-- Monitor table growth
SELECT 
    table_name,
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size (MB)',
    table_rows
FROM information_schema.tables 
WHERE table_schema = 'mechamap_backend'
AND table_name IN (
    'centralized_payments',
    'seller_payout_requests', 
    'payment_audit_logs',
    'commission_settings'
)
ORDER BY (data_length + index_length) DESC;
```

### 2. Application Performance Monitoring

#### Laravel Monitoring Commands
```bash
# Monitor payment processing performance
php artisan tinker --execute="
use App\Models\CentralizedPayment;
use Carbon\Carbon;

\$last24h = Carbon::now()->subDay();
\$payments = CentralizedPayment::where('created_at', '>=', \$last24h)->get();

echo 'Last 24h Statistics:' . PHP_EOL;
echo 'Total Payments: ' . \$payments->count() . PHP_EOL;
echo 'Success Rate: ' . round((\$payments->where('status', 'completed')->count() / \$payments->count()) * 100, 2) . '%' . PHP_EOL;

\$avgProcessingTime = \$payments->where('status', 'completed')
    ->filter(fn(\$p) => \$p->confirmed_at && \$p->created_at)
    ->avg(fn(\$p) => \$p->confirmed_at->diffInSeconds(\$p->created_at));
    
echo 'Avg Processing Time: ' . round(\$avgProcessingTime, 2) . ' seconds' . PHP_EOL;
"
```

#### API Performance Testing
```bash
# Test API response times
time curl -X GET "https://mechamap.com/api/v1/payment/test/centralized/configuration" \
  -H "Accept: application/json" \
  -w "Response Time: %{time_total}s\n"

# Load test payment creation
for i in {1..10}; do
  time curl -X POST "https://mechamap.com/api/v1/payment/test/centralized/create-order" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer YOUR_TOKEN" \
    -d '{"customer_id": 1, "total_amount": 100000, "items": []}' &
done
wait
```

### 3. Gateway Performance Monitoring

#### Stripe Monitoring
```php
// Monitor Stripe API performance
use Stripe\Stripe;
use Stripe\PaymentIntent;

$start = microtime(true);
try {
    Stripe::setApiKey(config('services.stripe.secret'));
    $intent = PaymentIntent::create([
        'amount' => 100000,
        'currency' => 'vnd',
        'payment_method_types' => ['card'],
    ]);
    $success = true;
} catch (Exception $e) {
    $success = false;
    $error = $e->getMessage();
}
$duration = microtime(true) - $start;

echo "Stripe API Test: " . ($success ? 'SUCCESS' : 'FAILED') . PHP_EOL;
echo "Response Time: " . round($duration * 1000, 2) . "ms" . PHP_EOL;
if (!$success) echo "Error: " . $error . PHP_EOL;
```

## 📈 Performance Benchmarks

### Baseline Performance Targets

| Metric | Target | Warning | Critical |
|--------|--------|---------|----------|
| Payment Success Rate | >98% | <95% | <90% |
| API Response Time | <500ms | >1s | >2s |
| Database Query Time | <100ms | >200ms | >500ms |
| Webhook Processing | <5s | >10s | >30s |
| Memory Usage | <80% | >85% | >95% |
| CPU Usage | <70% | >80% | >90% |

### Load Testing Results

#### Payment Creation Load Test
```bash
# Test with 100 concurrent payment creations
ab -n 100 -c 10 -H "Authorization: Bearer TOKEN" \
   -H "Content-Type: application/json" \
   -p payment_data.json \
   https://mechamap.com/api/v1/payment/centralized/stripe/create-intent
```

**Expected Results:**
- Requests per second: >50
- Average response time: <1000ms
- 95th percentile: <2000ms
- Error rate: <1%

#### Database Load Test
```sql
-- Simulate high-volume payment queries
DELIMITER $$
CREATE PROCEDURE test_payment_load()
BEGIN
    DECLARE i INT DEFAULT 0;
    WHILE i < 1000 DO
        SELECT * FROM centralized_payments 
        WHERE status = 'completed' 
        AND created_at >= DATE_SUB(NOW(), INTERVAL 1 DAY)
        ORDER BY created_at DESC 
        LIMIT 10;
        SET i = i + 1;
    END WHILE;
END$$
DELIMITER ;

-- Run load test
CALL test_payment_load();
```

## 🚨 Alerting & Notifications

### 1. Critical Alerts (Immediate Response)

#### Payment Failure Spike
```php
// Alert if payment failure rate > 10% in last hour
$failureRate = CentralizedPayment::where('created_at', '>', now()->subHour())
    ->where('status', 'failed')
    ->count() / CentralizedPayment::where('created_at', '>', now()->subHour())->count();

if ($failureRate > 0.1) {
    // Send immediate alert
    Mail::to('admin@mechamap.com')->send(new PaymentFailureAlert($failureRate));
}
```

#### Database Connection Issues
```php
// Monitor database connectivity
try {
    DB::connection()->getPdo();
} catch (Exception $e) {
    // Send critical alert
    Mail::to('tech@mechamap.com')->send(new DatabaseConnectionAlert($e->getMessage()));
}
```

### 2. Warning Alerts (Monitor Closely)

#### Slow Query Detection
```sql
-- Alert on queries taking >1 second
SELECT 
    query_time,
    sql_text
FROM mysql.slow_log 
WHERE query_time > 1
AND start_time > DATE_SUB(NOW(), INTERVAL 1 HOUR)
AND sql_text LIKE '%centralized_payments%';
```

#### High Memory Usage
```bash
# Monitor memory usage
MEMORY_USAGE=$(free | grep Mem | awk '{printf "%.2f", $3/$2 * 100.0}')
if (( $(echo "$MEMORY_USAGE > 85" | bc -l) )); then
    echo "WARNING: High memory usage: ${MEMORY_USAGE}%"
fi
```

### 3. Automated Monitoring Script

```bash
#!/bin/bash
# payment_monitor.sh - Run every 5 minutes via cron

LOG_FILE="/var/log/mechamap/payment_monitor.log"
DATE=$(date '+%Y-%m-%d %H:%M:%S')

echo "[$DATE] Starting payment system monitoring..." >> $LOG_FILE

# Check payment success rate
SUCCESS_RATE=$(php artisan tinker --execute="
use App\Models\CentralizedPayment;
\$total = CentralizedPayment::where('created_at', '>', now()->subHour())->count();
\$success = CentralizedPayment::where('created_at', '>', now()->subHour())->where('status', 'completed')->count();
echo \$total > 0 ? round((\$success / \$total) * 100, 2) : 100;
")

if (( $(echo "$SUCCESS_RATE < 95" | bc -l) )); then
    echo "[$DATE] ALERT: Payment success rate below 95%: ${SUCCESS_RATE}%" >> $LOG_FILE
    # Send alert email
fi

# Check API response time
API_RESPONSE_TIME=$(curl -o /dev/null -s -w '%{time_total}' https://mechamap.com/api/v1/payment/test/centralized/configuration)
if (( $(echo "$API_RESPONSE_TIME > 2" | bc -l) )); then
    echo "[$DATE] ALERT: API response time high: ${API_RESPONSE_TIME}s" >> $LOG_FILE
fi

# Check database connectivity
php artisan tinker --execute="
try {
    DB::connection()->getPdo();
    echo 'DB_OK';
} catch (Exception \$e) {
    echo 'DB_ERROR: ' . \$e->getMessage();
    exit(1);
}
" >> $LOG_FILE 2>&1

echo "[$DATE] Monitoring completed." >> $LOG_FILE
```

### 4. Cron Setup
```bash
# Add to crontab
*/5 * * * * /path/to/payment_monitor.sh
0 */6 * * * /path/to/payment_cleanup.sh
0 2 * * * /path/to/payment_backup.sh
```

## 📊 Reporting & Analytics

### Daily Performance Report
```php
// Generate daily performance report
$yesterday = Carbon::yesterday();
$payments = CentralizedPayment::whereDate('created_at', $yesterday)->get();

$report = [
    'date' => $yesterday->format('Y-m-d'),
    'total_payments' => $payments->count(),
    'successful_payments' => $payments->where('status', 'completed')->count(),
    'failed_payments' => $payments->where('status', 'failed')->count(),
    'total_volume' => $payments->where('status', 'completed')->sum('net_received'),
    'average_processing_time' => $payments->where('status', 'completed')
        ->avg(fn($p) => $p->confirmed_at->diffInSeconds($p->created_at)),
    'stripe_payments' => $payments->where('payment_method', 'stripe')->count(),
    'sepay_payments' => $payments->where('payment_method', 'sepay')->count(),
];

// Email report to stakeholders
Mail::to('management@mechamap.com')->send(new DailyPaymentReport($report));
```

### Weekly Trend Analysis
```sql
-- Weekly payment trends
SELECT 
    WEEK(created_at) as week_number,
    COUNT(*) as total_payments,
    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as successful_payments,
    ROUND(AVG(net_received), 2) as avg_payment_amount,
    SUM(net_received) as total_volume
FROM centralized_payments 
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 8 WEEK)
GROUP BY WEEK(created_at)
ORDER BY week_number DESC;
```

## 🔧 Performance Optimization

### Database Optimization
```sql
-- Add performance indexes
CREATE INDEX idx_centralized_payments_status_created ON centralized_payments(status, created_at);
CREATE INDEX idx_centralized_payments_method_status ON centralized_payments(payment_method, status);
CREATE INDEX idx_audit_logs_created_entity ON payment_audit_logs(created_at, entity_type, entity_id);

-- Optimize queries
ANALYZE TABLE centralized_payments;
ANALYZE TABLE seller_payout_requests;
ANALYZE TABLE payment_audit_logs;
```

### Application Optimization
```php
// Cache commission settings
Cache::remember('commission_settings_active', 3600, function () {
    return CommissionSetting::active()->get();
});

// Optimize payment queries
CentralizedPayment::with(['order', 'customer'])
    ->where('status', 'completed')
    ->latest()
    ->paginate(20);
```

---

**📞 Support**: Liên hệ tech@mechamap.com để được hỗ trợ performance optimization.
