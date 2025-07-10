# 🚀 **PHASE 1 MIGRATION GUIDE**
## **MechaMap Notification System Upgrade**

> **Target Audience**: Developers, DevOps Engineers  
> **Estimated Time**: 30-60 minutes  
> **Downtime Required**: ~5 minutes (for database migrations)

---

## 📋 **PRE-MIGRATION CHECKLIST**

### **✅ Requirements Verification**
- [ ] **PHP Version**: 8.1 or higher
- [ ] **Laravel Version**: 10.x or higher  
- [ ] **Database**: MySQL 8.0+ or MariaDB 10.4+
- [ ] **Redis**: Optional but recommended for caching
- [ ] **Queue System**: Configured for email processing
- [ ] **Backup**: Current database backup created

### **✅ Environment Preparation**
```bash
# 1. Check PHP version
php --version

# 2. Check Laravel version  
php artisan --version

# 3. Check database connection
php artisan tinker --execute="DB::connection()->getPdo(); echo 'Database connected';"

# 4. Check Redis connection (if using)
php artisan tinker --execute="Cache::store('redis')->put('test', 'ok'); echo 'Redis connected';"
```

---

## 🛡️ **BACKUP STRATEGY**

### **1. Create Database Backup**
```bash
# Using built-in backup command
php artisan db:backup

# Or manual mysqldump
mysqldump -u username -p database_name > backup_$(date +%Y%m%d_%H%M%S).sql
```

### **2. Backup Critical Files**
```bash
# Create backup directory
mkdir -p storage/backups/pre-phase1

# Backup configuration files
cp .env storage/backups/pre-phase1/
cp -r config/ storage/backups/pre-phase1/

# Backup current notification files (if any)
cp -r app/Services/NotificationService.php storage/backups/pre-phase1/ 2>/dev/null || true
```

### **3. Git Backup**
```bash
# Create backup branch
git checkout -b backup-pre-phase1
git add .
git commit -m "Backup before Phase 1 notification upgrade"
git checkout main
```

---

## 📦 **DEPENDENCY INSTALLATION**

### **1. Install Required Packages**
```bash
# Install device detection package
composer require jenssegers/agent

# Verify installation
composer show jenssegers/agent
```

### **2. Verify Package Dependencies**
```bash
# Check for conflicts
composer check-platform-reqs

# Update autoloader
composer dump-autoload
```

---

## 🗄️ **DATABASE MIGRATION**

### **1. Check Migration Status**
```bash
# View current migration status
php artisan migrate:status

# Check for pending migrations
php artisan migrate --pretend
```

### **2. Run Migrations (CRITICAL STEP)**
```bash
# Run migrations step by step for safety
php artisan migrate --step

# If you see "Nothing to migrate", check if tables exist:
php artisan tinker --execute="
echo 'notifications table: ' . (Schema::hasTable('notifications') ? 'EXISTS' : 'MISSING') . PHP_EOL;
echo 'user_devices table: ' . (Schema::hasTable('user_devices') ? 'EXISTS' : 'MISSING') . PHP_EOL;
"
```

### **3. Handle Migration Issues**

#### **If notifications table already exists:**
```bash
# Mark migration as completed
php artisan tinker --execute="
DB::table('migrations')->insert([
    'migration' => '2025_07_05_203504_create_notifications_table',
    'batch' => DB::table('migrations')->max('batch') + 1
]);
echo 'Notifications migration marked as completed';
"
```

#### **If user_devices table already exists:**
```bash
# Mark migration as completed
php artisan tinker --execute="
DB::table('migrations')->insert([
    'migration' => '2025_07_10_094824_create_user_devices_table', 
    'batch' => DB::table('migrations')->max('batch') + 1
]);
echo 'User devices migration marked as completed';
"
```

### **4. Run Index Optimization**
```bash
# This should run automatically, but verify:
php artisan migrate

# Check if indexes were created
php artisan tinker --execute="
\$indexes = DB::select('SHOW INDEX FROM notifications');
echo 'Notification indexes: ' . count(\$indexes) . PHP_EOL;
foreach(\$indexes as \$index) {
    echo '- ' . \$index->Key_name . ' on ' . \$index->Column_name . PHP_EOL;
}
"
```

---

## ⚙️ **CONFIGURATION UPDATES**

### **1. Environment Configuration**
```bash
# Add to .env file
cat >> .env << 'EOF'

# Notification System Configuration
NOTIFICATION_CACHE_TTL=3600
NOTIFICATION_UNREAD_CACHE_TTL=300
NOTIFICATION_EMAIL_ENABLED=true

# Device Detection Configuration  
DEVICE_FINGERPRINT_ENABLED=true
DEVICE_CLEANUP_DAYS=90

EOF
```

### **2. Cache Configuration**
```bash
# If using Redis (recommended)
echo "CACHE_DRIVER=redis" >> .env

# If using file cache (fallback)
echo "CACHE_DRIVER=file" >> .env

# Clear config cache
php artisan config:clear
php artisan config:cache
```

### **3. Queue Configuration**
```bash
# Configure queue for email processing
echo "QUEUE_CONNECTION=database" >> .env

# Create queue table if not exists
php artisan queue:table
php artisan migrate

# Or use Redis queue (recommended)
echo "QUEUE_CONNECTION=redis" >> .env
```

---

## 🧪 **POST-MIGRATION TESTING**

### **1. Database Verification**
```bash
# Test database structure
php artisan tinker --execute="
echo 'Testing database structure...' . PHP_EOL;
try {
    \$userCount = DB::table('users')->count();
    \$notificationCount = DB::table('notifications')->count();
    \$deviceCount = DB::table('user_devices')->count();
    echo '✅ Users: ' . \$userCount . PHP_EOL;
    echo '✅ Notifications: ' . \$notificationCount . PHP_EOL;
    echo '✅ User Devices: ' . \$deviceCount . PHP_EOL;
    echo '✅ Database structure OK' . PHP_EOL;
} catch (Exception \$e) {
    echo '❌ Database error: ' . \$e->getMessage() . PHP_EOL;
}
"
```

### **2. Service Testing**
```bash
# Test notification service
php artisan tinker --execute="
echo 'Testing NotificationService...' . PHP_EOL;
try {
    \$user = App\Models\User::first();
    if (\$user) {
        \$result = App\Services\NotificationService::send(
            \$user,
            'test_migration',
            'Migration Test',
            'Testing notification system after migration',
            ['test' => true],
            false
        );
        echo (\$result ? '✅' : '❌') . ' NotificationService: ' . (\$result ? 'OK' : 'FAILED') . PHP_EOL;
    } else {
        echo '⚠️  No users found for testing' . PHP_EOL;
    }
} catch (Exception \$e) {
    echo '❌ NotificationService error: ' . \$e->getMessage() . PHP_EOL;
}
"
```

### **3. Performance Testing**
```bash
# Run performance test with small dataset
php artisan notification:test-performance --users=2 --notifications=5 --cleanup

# Expected output should show:
# - Creation rate > 100 notifications/second
# - No database errors
# - Memory usage < 5MB
```

### **4. Email Template Testing**
```bash
# Test email templates render correctly
php artisan tinker --execute="
echo 'Testing email templates...' . PHP_EOL;
try {
    \$user = App\Models\User::first();
    \$thread = App\Models\Thread::first();
    
    if (\$user && \$thread) {
        \$mail = new App\Mail\ThreadCreatedNotification(\$thread, \$user);
        echo '✅ ThreadCreatedNotification: OK' . PHP_EOL;
    }
    
    \$mail2 = new App\Mail\PasswordChangedNotification(\$user, []);
    echo '✅ PasswordChangedNotification: OK' . PHP_EOL;
    
} catch (Exception \$e) {
    echo '❌ Email template error: ' . \$e->getMessage() . PHP_EOL;
}
"
```

---

## 🔧 **INTEGRATION VERIFICATION**

### **1. Forum Integration Test**
```bash
# Test forum notification integration
php artisan tinker --execute="
echo 'Testing forum integration...' . PHP_EOL;
try {
    // Test mention extraction
    \$mentions = App\Services\NotificationService::extractMentions('Hello @testuser and @admin');
    echo 'Mention extraction: ' . json_encode(\$mentions) . PHP_EOL;
    
    // Test device detection service
    \$service = new App\Services\DeviceDetectionService();
    echo '✅ DeviceDetectionService: OK' . PHP_EOL;
    
} catch (Exception \$e) {
    echo '❌ Integration error: ' . \$e->getMessage() . PHP_EOL;
}
"
```

### **2. Security Integration Test**
```bash
# Test device detection
php artisan tinker --execute="
echo 'Testing device detection...' . PHP_EOL;
try {
    \$user = App\Models\User::first();
    if (\$user) {
        \$devices = App\Services\DeviceDetectionService::getUserDevices(\$user);
        echo 'User devices count: ' . \$devices->count() . PHP_EOL;
        echo '✅ Device detection: OK' . PHP_EOL;
    }
} catch (Exception \$e) {
    echo '❌ Device detection error: ' . \$e->getMessage() . PHP_EOL;
}
"
```

---

## 🚨 **TROUBLESHOOTING**

### **Common Issues & Solutions**

#### **1. Migration Already Exists Error**
```bash
# Error: "Table 'notifications' already exists"
# Solution: Mark migration as completed
php artisan tinker --execute="
DB::table('migrations')->insert([
    'migration' => '2025_07_05_203504_create_notifications_table',
    'batch' => DB::table('migrations')->max('batch') + 1
]);
"
```

#### **2. Foreign Key Constraint Error**
```bash
# Error: "Cannot add foreign key constraint"
# Solution: Check if users table exists and has correct structure
php artisan tinker --execute="
echo 'Users table exists: ' . (Schema::hasTable('users') ? 'YES' : 'NO') . PHP_EOL;
echo 'Users table has id column: ' . (Schema::hasColumn('users', 'id') ? 'YES' : 'NO') . PHP_EOL;
"
```

#### **3. Class Not Found Error**
```bash
# Error: "Class 'App\Services\NotificationService' not found"
# Solution: Clear autoloader and cache
composer dump-autoload
php artisan clear-compiled
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

#### **4. Cache Driver Error**
```bash
# Error: "Cache store [redis] is not defined"
# Solution: Use file cache as fallback
echo "CACHE_DRIVER=file" >> .env
php artisan config:clear
```

#### **5. Queue Connection Error**
```bash
# Error: "Queue connection [redis] is not defined"
# Solution: Use database queue
echo "QUEUE_CONNECTION=database" >> .env
php artisan queue:table
php artisan migrate
```

---

## 🔄 **ROLLBACK PROCEDURE**

### **If Migration Fails**
```bash
# 1. Stop application
php artisan down

# 2. Restore database backup
mysql -u username -p database_name < backup_file.sql

# 3. Restore files
cp storage/backups/pre-phase1/.env .env
cp storage/backups/pre-phase1/NotificationService.php app/Services/ 2>/dev/null || true

# 4. Clear caches
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 5. Restart application
php artisan up
```

### **Git Rollback**
```bash
# Rollback to backup branch
git checkout backup-pre-phase1
git checkout -b rollback-phase1

# Or reset to specific commit
git reset --hard <commit-hash>
```

---

## ✅ **MIGRATION COMPLETION CHECKLIST**

### **Database**
- [ ] ✅ `notifications` table exists with correct structure
- [ ] ✅ `user_devices` table exists with correct structure  
- [ ] ✅ All indexes created successfully
- [ ] ✅ Foreign key constraints working
- [ ] ✅ Migration status shows all completed

### **Services**
- [ ] ✅ `NotificationService` working correctly
- [ ] ✅ `DeviceDetectionService` working correctly
- [ ] ✅ `NotificationCacheService` working correctly
- [ ] ✅ Email templates rendering correctly

### **Integration**
- [ ] ✅ Forum notifications triggering
- [ ] ✅ Security notifications triggering
- [ ] ✅ Device detection working on login
- [ ] ✅ Password change notifications working

### **Performance**
- [ ] ✅ Performance test passing
- [ ] ✅ Cache working (if enabled)
- [ ] ✅ Queue processing emails
- [ ] ✅ No memory leaks detected

### **Configuration**
- [ ] ✅ Environment variables set
- [ ] ✅ Cache driver configured
- [ ] ✅ Queue driver configured
- [ ] ✅ Email settings verified

---

## 📞 **POST-MIGRATION SUPPORT**

### **Monitoring Commands**
```bash
# Monitor notification performance
php artisan notification:test-performance --users=1 --notifications=1

# Check cache statistics
php artisan tinker --execute="print_r(App\Services\NotificationCacheService::getCacheStats());"

# Monitor queue processing
php artisan queue:work --once

# Check database health
php artisan tinker --execute="
echo 'Notification count: ' . App\Models\Notification::count() . PHP_EOL;
echo 'Device count: ' . App\Models\UserDevice::count() . PHP_EOL;
"
```

### **Log Monitoring**
```bash
# Monitor application logs
tail -f storage/logs/laravel.log | grep -i notification

# Monitor queue logs
tail -f storage/logs/laravel.log | grep -i queue
```

---

## 🎯 **NEXT STEPS**

### **Immediate Actions**
1. **Monitor Performance**: Watch for any performance degradation
2. **Check Logs**: Monitor for any errors in the first 24 hours
3. **User Feedback**: Collect feedback on new notification features
4. **Queue Processing**: Ensure email queue is processing correctly

### **Phase 2 Preparation**
1. **Review Phase 1**: Analyze performance metrics
2. **Plan Phase 2**: Marketplace and real-time notifications
3. **Infrastructure**: Prepare for WebSocket integration
4. **Testing**: Plan comprehensive testing for Phase 2

---

**🎉 Migration Complete! Phase 1 notification system is now active. 🚀**

**📞 Support**: If you encounter any issues, refer to `/docs/troubleshooting/` or contact the development team.
