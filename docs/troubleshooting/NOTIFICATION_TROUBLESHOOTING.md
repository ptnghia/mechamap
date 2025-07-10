# 🔧 **NOTIFICATION TROUBLESHOOTING GUIDE**
## **MechaMap Notification System - Phase 1**

> **Quick Reference**: Common issues and their solutions  
> **Emergency Contact**: Development Team  
> **Last Updated**: July 10, 2025

---

## 🚨 **EMERGENCY PROCEDURES**

### **System Down - Critical Issues**

#### **1. Complete Notification Failure**
```bash
# Quick diagnosis
php artisan tinker --execute="
try {
    \$user = App\Models\User::first();
    App\Services\NotificationService::send(\$user, 'test', 'Test', 'Test message');
    echo '✅ Notification system working';
} catch (Exception \$e) {
    echo '❌ CRITICAL: ' . \$e->getMessage();
}
"

# If failed, check database connection
php artisan tinker --execute="DB::connection()->getPdo(); echo 'Database OK';"
```

#### **2. Database Connection Issues**
```bash
# Check database status
php artisan tinker --execute="
try {
    DB::table('notifications')->count();
    echo '✅ Database accessible';
} catch (Exception \$e) {
    echo '❌ Database error: ' . \$e->getMessage();
}
"

# Emergency fallback - disable notifications
# Add to .env temporarily:
NOTIFICATION_SYSTEM_ENABLED=false
```

#### **3. Memory/Performance Issues**
```bash
# Check memory usage
php artisan notification:test-performance --users=1 --notifications=1

# If memory issues, clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## 🔍 **DIAGNOSTIC COMMANDS**

### **System Health Check**
```bash
# Complete system diagnosis
php artisan tinker --execute="
echo '=== NOTIFICATION SYSTEM HEALTH CHECK ===' . PHP_EOL;

// Database tables
echo 'Tables:' . PHP_EOL;
echo '- notifications: ' . (Schema::hasTable('notifications') ? '✅' : '❌') . PHP_EOL;
echo '- user_devices: ' . (Schema::hasTable('user_devices') ? '✅' : '❌') . PHP_EOL;

// Record counts
echo 'Records:' . PHP_EOL;
echo '- Users: ' . DB::table('users')->count() . PHP_EOL;
echo '- Notifications: ' . DB::table('notifications')->count() . PHP_EOL;
echo '- Devices: ' . DB::table('user_devices')->count() . PHP_EOL;

// Services
echo 'Services:' . PHP_EOL;
try {
    new App\Services\NotificationService();
    echo '- NotificationService: ✅' . PHP_EOL;
} catch (Exception \$e) {
    echo '- NotificationService: ❌ ' . \$e->getMessage() . PHP_EOL;
}

try {
    new App\Services\DeviceDetectionService();
    echo '- DeviceDetectionService: ✅' . PHP_EOL;
} catch (Exception \$e) {
    echo '- DeviceDetectionService: ❌ ' . \$e->getMessage() . PHP_EOL;
}

// Cache
echo 'Cache:' . PHP_EOL;
try {
    Cache::put('test', 'ok', 60);
    echo '- Cache driver: ✅ ' . config('cache.default') . PHP_EOL;
} catch (Exception \$e) {
    echo '- Cache driver: ❌ ' . \$e->getMessage() . PHP_EOL;
}

echo '=== END HEALTH CHECK ===' . PHP_EOL;
"
```

### **Performance Diagnosis**
```bash
# Quick performance test
php artisan notification:test-performance --users=2 --notifications=5 --cleanup

# Cache performance
php artisan tinker --execute="
\$stats = App\Services\NotificationCacheService::getCacheStats();
print_r(\$stats);
"

# Database query analysis
php artisan tinker --execute="
DB::enableQueryLog();
\$user = App\Models\User::first();
App\Models\Notification::getOptimizedNotifications(\$user, 10);
\$queries = DB::getQueryLog();
echo 'Queries executed: ' . count(\$queries) . PHP_EOL;
foreach(\$queries as \$query) {
    echo '- ' . \$query['query'] . ' (' . \$query['time'] . 'ms)' . PHP_EOL;
}
"
```

---

## 🐛 **COMMON ISSUES & SOLUTIONS**

### **1. Notifications Not Sending**

#### **Symptoms:**
- Users not receiving notifications
- No entries in notifications table
- No errors in logs

#### **Diagnosis:**
```bash
# Test notification sending
php artisan tinker --execute="
\$user = App\Models\User::first();
\$result = App\Services\NotificationService::send(
    \$user, 
    'test', 
    'Test Notification', 
    'This is a test message'
);
echo 'Send result: ' . (\$result ? 'SUCCESS' : 'FAILED') . PHP_EOL;

// Check if notification was created
\$count = App\Models\Notification::where('type', 'test')->count();
echo 'Test notifications in DB: ' . \$count . PHP_EOL;
"
```

#### **Solutions:**
```bash
# 1. Check database permissions
php artisan tinker --execute="
try {
    DB::table('notifications')->insert([
        'user_id' => 1,
        'type' => 'test',
        'title' => 'Test',
        'message' => 'Test',
        'data' => '{}',
        'is_read' => false,
        'created_at' => now(),
        'updated_at' => now()
    ]);
    echo '✅ Database write permissions OK';
} catch (Exception \$e) {
    echo '❌ Database write error: ' . \$e->getMessage();
}
"

# 2. Check service configuration
php artisan config:clear
php artisan cache:clear

# 3. Verify user exists
php artisan tinker --execute="
\$userCount = App\Models\User::count();
echo 'Total users: ' . \$userCount . PHP_EOL;
if (\$userCount === 0) {
    echo '❌ No users found - create test user first';
}
"
```

### **2. Email Notifications Not Working**

#### **Symptoms:**
- Notifications created but emails not sent
- Queue jobs failing
- Email templates not rendering

#### **Diagnosis:**
```bash
# Test email configuration
php artisan tinker --execute="
echo 'Mail driver: ' . config('mail.default') . PHP_EOL;
echo 'Mail host: ' . config('mail.mailers.smtp.host') . PHP_EOL;
echo 'Queue driver: ' . config('queue.default') . PHP_EOL;
"

# Test email template rendering
php artisan tinker --execute="
try {
    \$user = App\Models\User::first();
    \$mail = new App\Mail\PasswordChangedNotification(\$user, []);
    echo '✅ Email template renders OK';
} catch (Exception \$e) {
    echo '❌ Email template error: ' . \$e->getMessage();
}
"
```

#### **Solutions:**
```bash
# 1. Check email configuration
cat .env | grep MAIL

# 2. Test email sending
php artisan tinker --execute="
\$user = App\Models\User::first();
try {
    Mail::to(\$user->email)->send(new App\Mail\PasswordChangedNotification(\$user, []));
    echo '✅ Email sent successfully';
} catch (Exception \$e) {
    echo '❌ Email sending failed: ' . \$e->getMessage();
}
"

# 3. Process queue manually
php artisan queue:work --once

# 4. Check failed jobs
php artisan queue:failed
```

### **3. Device Detection Issues**

#### **Symptoms:**
- New device notifications not triggering
- Device fingerprints not unique
- Trusted devices not recognized

#### **Diagnosis:**
```bash
# Test device detection
php artisan tinker --execute="
\$request = request();
\$fingerprint = App\Services\DeviceDetectionService::generateDeviceFingerprint(\$request);
echo 'Device fingerprint: ' . \$fingerprint . PHP_EOL;

\$user = App\Models\User::first();
\$devices = App\Services\DeviceDetectionService::getUserDevices(\$user);
echo 'User devices: ' . \$devices->count() . PHP_EOL;
"
```

#### **Solutions:**
```bash
# 1. Check jenssegers/agent package
composer show jenssegers/agent

# 2. Clear device cache
php artisan tinker --execute="
Cache::forget('device_fingerprints');
echo 'Device cache cleared';
"

# 3. Test device fingerprint generation
php artisan tinker --execute="
\$agent = new Jenssegers\Agent\Agent();
echo 'Browser: ' . \$agent->browser() . PHP_EOL;
echo 'Platform: ' . \$agent->platform() . PHP_EOL;
echo 'Device: ' . \$agent->device() . PHP_EOL;
"
```

### **4. Cache Performance Issues**

#### **Symptoms:**
- Slow notification loading
- Cache misses
- Memory usage high

#### **Diagnosis:**
```bash
# Check cache statistics
php artisan tinker --execute="
\$stats = App\Services\NotificationCacheService::getCacheStats();
print_r(\$stats);
"

# Test cache operations
php artisan tinker --execute="
\$user = App\Models\User::first();
\$start = microtime(true);
\$count = App\Services\NotificationCacheService::getUnreadCount(\$user);
\$time = (microtime(true) - \$start) * 1000;
echo 'Cache read time: ' . round(\$time, 2) . 'ms' . PHP_EOL;
echo 'Unread count: ' . \$count . PHP_EOL;
"
```

#### **Solutions:**
```bash
# 1. Clear all caches
php artisan cache:clear
App\Services\NotificationCacheService::clearAllCache();

# 2. Warm up cache
php artisan tinker --execute="
App\Services\NotificationCacheService::warmUpCache();
echo 'Cache warmed up';
"

# 3. Switch cache driver if Redis issues
echo "CACHE_DRIVER=file" >> .env
php artisan config:clear
```

### **5. Database Performance Issues**

#### **Symptoms:**
- Slow notification queries
- High CPU usage
- Timeout errors

#### **Diagnosis:**
```bash
# Check database indexes
php artisan tinker --execute="
\$indexes = DB::select('SHOW INDEX FROM notifications');
echo 'Notification indexes:' . PHP_EOL;
foreach(\$indexes as \$index) {
    echo '- ' . \$index->Key_name . ' on ' . \$index->Column_name . PHP_EOL;
}
"

# Test query performance
php artisan tinker --execute="
\$user = App\Models\User::first();
\$start = microtime(true);
\$notifications = App\Models\Notification::getOptimizedNotifications(\$user, 20);
\$time = (microtime(true) - \$start) * 1000;
echo 'Query time: ' . round(\$time, 2) . 'ms' . PHP_EOL;
echo 'Notifications loaded: ' . \$notifications->count() . PHP_EOL;
"
```

#### **Solutions:**
```bash
# 1. Re-run index migration
php artisan migrate:refresh --path=database/migrations/2025_07_10_102413_optimize_notification_system_indexes.php

# 2. Analyze table structure
php artisan tinker --execute="
\$result = DB::select('ANALYZE TABLE notifications');
print_r(\$result);
"

# 3. Clean old notifications
php artisan tinker --execute="
\$deleted = App\Models\Notification::cleanOldNotifications(30);
echo 'Cleaned ' . \$deleted . ' old notifications';
"
```

---

## 🔧 **MAINTENANCE COMMANDS**

### **Daily Maintenance**
```bash
# Clean old notifications (run daily)
php artisan tinker --execute="
\$deleted = App\Services\NotificationService::cleanOldNotificationsOptimized(90);
echo 'Cleaned ' . \$deleted . ' old notifications';
"

# Clean old devices (run daily)
php artisan tinker --execute="
\$deleted = App\Models\UserDevice::where('last_seen_at', '<', now()->subDays(90))
    ->where('is_trusted', false)
    ->delete();
echo 'Cleaned ' . \$deleted . ' old devices';
"

# Warm up cache (run after cache clear)
php artisan tinker --execute="
App\Services\NotificationCacheService::warmUpCache();
echo 'Cache warmed up';
"
```

### **Weekly Maintenance**
```bash
# Performance check
php artisan notification:test-performance --users=5 --notifications=10 --cache --cleanup

# Database optimization
php artisan tinker --execute="
DB::statement('OPTIMIZE TABLE notifications');
DB::statement('OPTIMIZE TABLE user_devices');
echo 'Database tables optimized';
"

# Clear failed queue jobs
php artisan queue:flush
```

### **Monthly Maintenance**
```bash
# Full database backup
php artisan db:backup

# Analyze notification patterns
php artisan tinker --execute="
echo 'Notification Statistics (Last 30 days):' . PHP_EOL;
\$types = DB::table('notifications')
    ->select('type', DB::raw('count(*) as count'))
    ->where('created_at', '>=', now()->subDays(30))
    ->groupBy('type')
    ->orderBy('count', 'desc')
    ->get();
    
foreach(\$types as \$type) {
    echo '- ' . \$type->type . ': ' . \$type->count . PHP_EOL;
}
"
```

---

## 📊 **MONITORING & ALERTS**

### **Key Metrics to Monitor**
```bash
# Notification creation rate
php artisan tinker --execute="
\$today = DB::table('notifications')->whereDate('created_at', today())->count();
\$yesterday = DB::table('notifications')->whereDate('created_at', yesterday())->count();
echo 'Notifications today: ' . \$today . PHP_EOL;
echo 'Notifications yesterday: ' . \$yesterday . PHP_EOL;
echo 'Change: ' . ((\$today - \$yesterday) / max(\$yesterday, 1) * 100) . '%' . PHP_EOL;
"

# Cache hit rate
php artisan tinker --execute="
\$stats = App\Services\NotificationCacheService::getCacheStats();
if (isset(\$stats['hit_rate'])) {
    echo 'Cache hit rate: ' . \$stats['hit_rate'] . '%' . PHP_EOL;
}
"

# Queue status
php artisan queue:monitor
```

### **Alert Thresholds**
- **Notification creation rate**: < 100/second = Warning
- **Cache hit rate**: < 80% = Warning  
- **Database query time**: > 100ms = Warning
- **Failed queue jobs**: > 10 = Critical
- **Memory usage**: > 512MB = Warning

---

## 🆘 **EMERGENCY CONTACTS**

### **Escalation Procedure**
1. **Level 1**: Check this troubleshooting guide
2. **Level 2**: Contact development team
3. **Level 3**: Database administrator
4. **Level 4**: System administrator

### **Emergency Disable**
```bash
# Temporarily disable notification system
echo "NOTIFICATION_SYSTEM_ENABLED=false" >> .env
php artisan config:clear

# Re-enable when fixed
sed -i '/NOTIFICATION_SYSTEM_ENABLED=false/d' .env
php artisan config:clear
```

---

## 📝 **LOGGING & DEBUGGING**

### **Enable Debug Logging**
```bash
# Add to .env for detailed logging
echo "LOG_LEVEL=debug" >> .env
echo "NOTIFICATION_DEBUG=true" >> .env
php artisan config:clear
```

### **Monitor Logs**
```bash
# Watch notification logs
tail -f storage/logs/laravel.log | grep -i notification

# Watch error logs
tail -f storage/logs/laravel.log | grep -i error

# Watch queue logs
tail -f storage/logs/laravel.log | grep -i queue
```

### **Debug Specific Issues**
```bash
# Debug notification sending
php artisan tinker --execute="
Log::info('Testing notification debug');
\$user = App\Models\User::first();
App\Services\NotificationService::send(\$user, 'debug_test', 'Debug', 'Debug message');
echo 'Check logs for debug information';
"
```

---

**🔧 Remember: When in doubt, check the logs first! Most issues leave traces in the Laravel log files.**

**📞 For urgent issues, contact the development team immediately.**
