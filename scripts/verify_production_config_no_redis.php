<?php
/**
 * MechaMap Production Configuration Verification (No Redis)
 * 
 * Script để kiểm tra cấu hình production khi không sử dụng Redis
 * Đảm bảo tất cả các thành phần hoạt động với file cache và database queue
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Config;

// Bootstrap Laravel application
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 MECHAMAP PRODUCTION CONFIG VERIFICATION (NO REDIS)\n";
echo "====================================================\n\n";

// 1. Environment Configuration Check
echo "1️⃣ ENVIRONMENT CONFIGURATION:\n";
echo "─────────────────────────────────\n";

$envChecks = [
    'APP_ENV' => 'production',
    'APP_DEBUG' => 'false',
    'CACHE_STORE' => 'file',
    'SESSION_DRIVER' => 'database',
    'QUEUE_CONNECTION' => 'database',
];

foreach ($envChecks as $key => $expected) {
    $actual = env($key);
    $status = ($actual == $expected) ? '✅' : '❌';
    echo "  {$status} {$key}: {$actual} " . ($actual != $expected ? "(expected: {$expected})" : '') . "\n";
}

echo "\n";

// 2. Database Tables Check
echo "2️⃣ REQUIRED DATABASE TABLES:\n";
echo "─────────────────────────────\n";

$requiredTables = [
    'sessions' => 'Database sessions',
    'jobs' => 'Database queue',
    'failed_jobs' => 'Failed job tracking',
    'job_batches' => 'Job batching',
    'cache' => 'Database cache (optional)',
];

foreach ($requiredTables as $table => $description) {
    try {
        $exists = Schema::hasTable($table);
        $status = $exists ? '✅' : '❌';
        $count = $exists ? DB::table($table)->count() : 0;
        echo "  {$status} {$table}: " . ($exists ? "EXISTS ({$count} records)" : "MISSING") . " - {$description}\n";
    } catch (Exception $e) {
        echo "  ❌ {$table}: ERROR - " . $e->getMessage() . "\n";
    }
}

echo "\n";

// 3. Cache System Test
echo "3️⃣ CACHE SYSTEM TEST:\n";
echo "─────────────────────\n";

try {
    $testKey = 'mechamap_test_' . time();
    $testValue = 'production_config_test';
    
    // Test cache put
    Cache::put($testKey, $testValue, 60);
    echo "  ✅ Cache PUT: Success\n";
    
    // Test cache get
    $retrieved = Cache::get($testKey);
    if ($retrieved === $testValue) {
        echo "  ✅ Cache GET: Success\n";
    } else {
        echo "  ❌ Cache GET: Failed (got: " . var_export($retrieved, true) . ")\n";
    }
    
    // Test cache forget
    Cache::forget($testKey);
    $afterForget = Cache::get($testKey);
    if ($afterForget === null) {
        echo "  ✅ Cache FORGET: Success\n";
    } else {
        echo "  ❌ Cache FORGET: Failed\n";
    }
    
    // Cache driver info
    echo "  ℹ️  Cache Driver: " . config('cache.default') . "\n";
    echo "  ℹ️  Cache Path: " . storage_path('framework/cache') . "\n";
    
} catch (Exception $e) {
    echo "  ❌ Cache Test Failed: " . $e->getMessage() . "\n";
}

echo "\n";

// 4. Session System Test
echo "4️⃣ SESSION SYSTEM TEST:\n";
echo "─────────────────────────\n";

try {
    // Check session configuration
    echo "  ℹ️  Session Driver: " . config('session.driver') . "\n";
    echo "  ℹ️  Session Lifetime: " . config('session.lifetime') . " minutes\n";
    echo "  ℹ️  Session Table: " . config('session.table', 'sessions') . "\n";
    
    // Check if sessions table has proper structure
    if (Schema::hasTable('sessions')) {
        $columns = Schema::getColumnListing('sessions');
        $requiredColumns = ['id', 'user_id', 'ip_address', 'user_agent', 'payload', 'last_activity'];
        $missingColumns = array_diff($requiredColumns, $columns);
        
        if (empty($missingColumns)) {
            echo "  ✅ Session Table Structure: Complete\n";
        } else {
            echo "  ❌ Session Table Structure: Missing columns - " . implode(', ', $missingColumns) . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "  ❌ Session Test Failed: " . $e->getMessage() . "\n";
}

echo "\n";

// 5. Queue System Test
echo "5️⃣ QUEUE SYSTEM TEST:\n";
echo "─────────────────────\n";

try {
    // Check queue configuration
    echo "  ℹ️  Queue Driver: " . config('queue.default') . "\n";
    echo "  ℹ️  Queue Table: " . config('queue.connections.database.table', 'jobs') . "\n";
    
    // Check jobs table structure
    if (Schema::hasTable('jobs')) {
        $jobsCount = DB::table('jobs')->count();
        $failedJobsCount = DB::table('failed_jobs')->count();
        echo "  ✅ Jobs Table: {$jobsCount} pending jobs\n";
        echo "  ✅ Failed Jobs Table: {$failedJobsCount} failed jobs\n";
        
        // Test queue functionality (dispatch a simple test job)
        try {
            // We'll just check if we can access the queue without actually dispatching
            $queueSize = DB::table('jobs')->where('queue', 'default')->count();
            echo "  ✅ Queue Access: Success (default queue has {$queueSize} jobs)\n";
        } catch (Exception $e) {
            echo "  ❌ Queue Access: Failed - " . $e->getMessage() . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "  ❌ Queue Test Failed: " . $e->getMessage() . "\n";
}

echo "\n";

// 6. File Permissions Check
echo "6️⃣ FILE PERMISSIONS CHECK:\n";
echo "──────────────────────────\n";

$directories = [
    'storage/framework/cache' => 'File cache storage',
    'storage/framework/sessions' => 'File sessions (if used)',
    'storage/logs' => 'Application logs',
    'storage/app' => 'Application storage',
    'bootstrap/cache' => 'Bootstrap cache',
];

foreach ($directories as $dir => $description) {
    $fullPath = base_path($dir);
    if (is_dir($fullPath)) {
        $writable = is_writable($fullPath);
        $status = $writable ? '✅' : '❌';
        $perms = substr(sprintf('%o', fileperms($fullPath)), -4);
        echo "  {$status} {$dir}: " . ($writable ? "WRITABLE" : "NOT WRITABLE") . " (perms: {$perms}) - {$description}\n";
    } else {
        echo "  ❌ {$dir}: MISSING - {$description}\n";
    }
}

echo "\n";

// 7. Performance Optimization Check
echo "7️⃣ PERFORMANCE OPTIMIZATION:\n";
echo "────────────────────────────\n";

$optimizations = [
    'config_cached' => file_exists(base_path('bootstrap/cache/config.php')),
    'routes_cached' => file_exists(base_path('bootstrap/cache/routes-v7.php')),
    'views_cached' => is_dir(storage_path('framework/views')) && count(glob(storage_path('framework/views') . '/*')) > 0,
    'opcache_enabled' => function_exists('opcache_get_status') && opcache_get_status() !== false,
];

foreach ($optimizations as $check => $status) {
    $icon = $status ? '✅' : '⚠️';
    $message = $status ? 'ENABLED' : 'DISABLED';
    echo "  {$icon} " . ucwords(str_replace('_', ' ', $check)) . ": {$message}\n";
}

echo "\n";

// 8. Security Configuration Check
echo "8️⃣ SECURITY CONFIGURATION:\n";
echo "──────────────────────────\n";

$securityChecks = [
    'APP_DEBUG' => env('APP_DEBUG') === false || env('APP_DEBUG') === 'false',
    'SESSION_SECURE_COOKIES' => env('SESSION_SECURE_COOKIES') === true || env('SESSION_SECURE_COOKIES') === 'true',
    'SESSION_ENCRYPT' => env('SESSION_ENCRYPT') === true || env('SESSION_ENCRYPT') === 'true',
    'HTTPS_ENABLED' => str_starts_with(env('APP_URL'), 'https://'),
];

foreach ($securityChecks as $check => $status) {
    $icon = $status ? '✅' : '❌';
    $message = $status ? 'SECURE' : 'INSECURE';
    echo "  {$icon} " . ucwords(str_replace('_', ' ', $check)) . ": {$message}\n";
}

echo "\n";

// 9. Recommendations
echo "9️⃣ RECOMMENDATIONS:\n";
echo "───────────────────\n";

$recommendations = [];

if (!$optimizations['config_cached']) {
    $recommendations[] = "Run 'php artisan config:cache' to cache configuration";
}

if (!$optimizations['routes_cached']) {
    $recommendations[] = "Run 'php artisan route:cache' to cache routes";
}

if (!$optimizations['opcache_enabled']) {
    $recommendations[] = "Enable OPcache in PHP for better performance";
}

if (env('APP_DEBUG') !== false && env('APP_DEBUG') !== 'false') {
    $recommendations[] = "Set APP_DEBUG=false in production";
}

if (empty($recommendations)) {
    echo "  ✅ All optimizations are properly configured!\n";
} else {
    foreach ($recommendations as $i => $recommendation) {
        echo "  " . ($i + 1) . ". {$recommendation}\n";
    }
}

echo "\n";

// 10. Summary
echo "🎯 SUMMARY:\n";
echo "──────────\n";

$totalChecks = 0;
$passedChecks = 0;

// Count environment checks
foreach ($envChecks as $key => $expected) {
    $totalChecks++;
    if (env($key) == $expected) $passedChecks++;
}

// Count table checks
foreach ($requiredTables as $table => $description) {
    $totalChecks++;
    if (Schema::hasTable($table)) $passedChecks++;
}

// Count optimization checks
foreach ($optimizations as $check => $status) {
    $totalChecks++;
    if ($status) $passedChecks++;
}

// Count security checks
foreach ($securityChecks as $check => $status) {
    $totalChecks++;
    if ($status) $passedChecks++;
}

$percentage = round(($passedChecks / $totalChecks) * 100, 1);
$status = $percentage >= 90 ? '✅ EXCELLENT' : ($percentage >= 75 ? '⚠️ GOOD' : '❌ NEEDS ATTENTION');

echo "  Configuration Score: {$passedChecks}/{$totalChecks} ({$percentage}%) - {$status}\n";

if ($percentage >= 90) {
    echo "  🎉 Your production configuration is ready for deployment!\n";
} elseif ($percentage >= 75) {
    echo "  👍 Your configuration is mostly ready, but consider the recommendations above.\n";
} else {
    echo "  ⚠️ Your configuration needs attention before production deployment.\n";
}

echo "\n";
echo "✅ Verification completed!\n";
