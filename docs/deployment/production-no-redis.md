# 🚀 MechaMap Production Deployment (No Redis)

Hướng dẫn triển khai MechaMap lên production environment mà không sử dụng Redis.

## 📋 Tổng quan

Cấu hình này sử dụng:
- **File Cache** thay vì Redis Cache
- **Database Sessions** thay vì Redis Sessions  
- **Database Queue** thay vì Redis Queue
- **File-based** storage cho các tính năng khác

## 🔧 Cấu hình Environment

### `.env.production` đã được cấu hình với:

```bash
# Cache Configuration - Using File Cache
CACHE_STORE=file
CACHE_PREFIX=mechamap_cache

# Session Configuration - Using Database Sessions
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIES=true

# Queue Configuration - Using Database Queue
QUEUE_CONNECTION=database
QUEUE_FAILED_DRIVER=database-uuids

# Redis Configuration - Disabled
# REDIS_HOST=127.0.0.1
# REDIS_PASSWORD=null
# REDIS_PORT=6379
```

## 🗄️ Database Tables Required

Các bảng cần thiết đã được tạo trong migrations:

- `sessions` - Lưu trữ session data
- `jobs` - Queue jobs
- `failed_jobs` - Failed jobs tracking
- `job_batches` - Job batching
- `cache` - Database cache (optional)

## 🚀 Deployment Steps

### 1. Automatic Deployment (Recommended)

```bash
# Windows PowerShell
.\scripts\switch_to_production_no_redis.ps1

# Linux/Mac Bash
bash scripts/switch_to_production_no_redis.sh
```

### 2. Manual Deployment

```bash
# 1. Backup current configuration
cp .env .env.backup.$(date +%Y%m%d_%H%M%S)

# 2. Apply production configuration
cp .env.production .env

# 3. Clear caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# 4. Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Run migrations
php artisan migrate --force

# 6. Set permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

## 📊 Performance Optimization

### File Cache Optimization

```bash
# Ensure cache directory exists and is writable
mkdir -p storage/framework/cache/data
chmod -R 755 storage/framework/cache

# Monitor cache performance
php artisan cache:clear
php artisan config:cache
```

### Database Optimization

```sql
-- Optimize sessions table
ALTER TABLE sessions ADD INDEX sessions_last_activity_index (last_activity);
ALTER TABLE sessions ADD INDEX sessions_user_id_index (user_id);

-- Optimize jobs table
ALTER TABLE jobs ADD INDEX jobs_queue_index (queue);
ALTER TABLE jobs ADD INDEX jobs_reserved_at_index (reserved_at);
```

### Queue Worker Setup

```bash
# Start queue worker
php artisan queue:work --verbose --tries=3 --timeout=90

# For production, use supervisor or systemd
# Create: /etc/supervisor/conf.d/mechamap-worker.conf
[program:mechamap-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/mechamap/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/mechamap/storage/logs/worker.log
```

## 🔍 Monitoring & Verification

### Verification Script

```bash
# Run verification
php scripts/verify_production_config_no_redis.php
```

### Manual Checks

```bash
# Check cache functionality
php artisan tinker
>>> Cache::put('test', 'value', 60);
>>> Cache::get('test');

# Check queue functionality
>>> Queue::push(new \App\Jobs\TestJob());

# Check sessions
>>> DB::table('sessions')->count();
```

## 📈 Performance Considerations

### Pros of No-Redis Setup:
- ✅ Simpler deployment (no Redis dependency)
- ✅ Lower memory usage
- ✅ Easier backup/restore
- ✅ No Redis configuration complexity

### Cons of No-Redis Setup:
- ❌ Slower cache performance
- ❌ No real-time features (broadcasting)
- ❌ Limited scalability
- ❌ No cache tags support

## 🔧 Troubleshooting

### Common Issues

#### Cache Permission Errors
```bash
# Fix cache permissions
sudo chown -R www-data:www-data storage/framework/cache
chmod -R 755 storage/framework/cache
```

#### Session Issues
```bash
# Clear sessions
php artisan session:table
php artisan migrate
DB::table('sessions')->truncate();
```

#### Queue Not Processing
```bash
# Check queue status
php artisan queue:work --once
php artisan queue:failed

# Restart queue worker
php artisan queue:restart
```

### Performance Issues

#### Slow File Cache
```bash
# Use APCu if available
# Add to .env:
CACHE_STORE=apc

# Or optimize file cache
# Ensure SSD storage for cache directory
# Use tmpfs for cache (Linux):
# mount -t tmpfs -o size=512M tmpfs storage/framework/cache
```

#### Database Queue Bottleneck
```bash
# Add database indexes
php artisan tinker
>>> Schema::table('jobs', function($table) {
    $table->index(['queue', 'reserved_at']);
});
```

## 🔄 Migration to Redis (Future)

Nếu sau này muốn chuyển sang Redis:

```bash
# 1. Install Redis
sudo apt-get install redis-server

# 2. Update .env
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# 3. Clear caches and restart
php artisan config:clear
php artisan cache:clear
```

## 📝 Maintenance Tasks

### Daily Tasks
```bash
# Clean old sessions (add to cron)
0 2 * * * cd /path/to/mechamap && php artisan session:gc

# Clean failed jobs
0 3 * * * cd /path/to/mechamap && php artisan queue:prune-failed --hours=168
```

### Weekly Tasks
```bash
# Optimize database
0 1 * * 0 cd /path/to/mechamap && php artisan db:optimize

# Clear old cache files
0 2 * * 0 find storage/framework/cache -name "*.php" -mtime +7 -delete
```

## 🎯 Production Checklist

- [ ] `.env.production` applied
- [ ] All caches optimized
- [ ] Database migrations run
- [ ] File permissions set
- [ ] Queue worker running
- [ ] Cron jobs configured
- [ ] Monitoring setup
- [ ] Backup strategy implemented
- [ ] SSL certificate installed
- [ ] Web server optimized

## 📞 Support

Nếu gặp vấn đề:
1. Chạy verification script
2. Kiểm tra logs: `tail -f storage/logs/laravel.log`
3. Kiểm tra web server logs
4. Kiểm tra database connectivity

---

**🎉 MechaMap Production Ready (No Redis)!**
