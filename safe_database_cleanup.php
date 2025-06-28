<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "🧹 SAFE DATABASE CLEANUP\n";
echo "========================\n\n";

// Backup current state
echo "💾 Creating backup information...\n";
$backupInfo = [
    'timestamp' => now()->format('Y-m-d H:i:s'),
    'total_users' => DB::table('users')->count(),
    'total_comments' => DB::table('comments')->count(),
    'total_threads' => DB::table('threads')->count(),
    'total_reactions' => DB::table('reactions')->count(),
];

echo "Backup info: " . json_encode($backupInfo, JSON_PRETTY_PRINT) . "\n\n";

// Step 1: Disable foreign key checks temporarily
echo "🔓 Disabling foreign key checks...\n";
DB::statement('SET FOREIGN_KEY_CHECKS=0');

try {
    // Step 2: Clear user-related data in dependency order
    echo "\n🗑️ Clearing user-related data...\n";
    
    $tablesToClear = [
        // Child tables first (tables that reference users)
        'alerts',
        'bookmarks', 
        'comment_dislikes',
        'comment_likes',
        'comments',
        'conversation_participants',
        'download_tokens',
        'followers',
        'media',
        'messages', 
        'orders',
        'order_items',
        'payment_transactions',
        'poll_votes',
        'posts',
        'product_purchases',
        'profile_posts',
        'reactions',
        'reports',
        'search_logs',
        'secure_downloads',
        'seller_earnings',
        'seller_payouts',
        'shopping_carts',
        'showcase_comments',
        'showcase_follows', 
        'showcase_likes',
        'showcases',
        'social_accounts',
        'social_interactions',
        'subscriptions',
        'technical_products',
        'thread_bookmarks',
        'thread_follows',
        'thread_likes', 
        'thread_ratings',
        'thread_saves',
        'threads',
        'user_activities',
        'user_payment_methods',
        'user_visits',
        
        // Spatie permission tables
        'model_has_roles',
        'model_has_permissions',
    ];
    
    foreach ($tablesToClear as $table) {
        if (Schema::hasTable($table)) {
            try {
                $count = DB::table($table)->count();
                if ($count > 0) {
                    DB::table($table)->truncate();
                    echo "  ✅ Cleared {$table} ({$count} records)\n";
                } else {
                    echo "  ⚪ {$table} was already empty\n";
                }
            } catch (Exception $e) {
                echo "  ❌ Error clearing {$table}: " . $e->getMessage() . "\n";
            }
        } else {
            echo "  ⚠️ Table {$table} does not exist\n";
        }
    }
    
    // Step 3: Clear users table
    echo "\n👥 Clearing users table...\n";
    $userCount = DB::table('users')->count();
    if ($userCount > 0) {
        DB::table('users')->truncate();
        echo "  ✅ Cleared users table ({$userCount} records)\n";
    } else {
        echo "  ⚪ Users table was already empty\n";
    }
    
    // Step 4: Reset auto-increment counters
    echo "\n🔄 Resetting auto-increment counters...\n";
    $tablesToReset = array_merge($tablesToClear, ['users']);
    
    foreach ($tablesToReset as $table) {
        if (Schema::hasTable($table)) {
            try {
                DB::statement("ALTER TABLE {$table} AUTO_INCREMENT = 1");
                echo "  ✅ Reset {$table} auto-increment\n";
            } catch (Exception $e) {
                echo "  ⚠️ Could not reset {$table} auto-increment: " . $e->getMessage() . "\n";
            }
        }
    }
    
    // Step 5: Clean up orphaned Spatie permission data
    echo "\n🔐 Cleaning Spatie permission system...\n";
    
    // Keep roles and permissions, but clear assignments
    $spatieCleanup = [
        'model_has_roles' => 'User role assignments',
        'model_has_permissions' => 'User permission assignments'
    ];
    
    foreach ($spatieCleanup as $table => $description) {
        if (Schema::hasTable($table)) {
            try {
                $count = DB::table($table)->count();
                if ($count > 0) {
                    DB::table($table)->truncate();
                    echo "  ✅ Cleared {$description} ({$count} records)\n";
                } else {
                    echo "  ⚪ {$description} was already empty\n";
                }
            } catch (Exception $e) {
                echo "  ❌ Error clearing {$description}: " . $e->getMessage() . "\n";
            }
        }
    }
    
    echo "\n✅ Database cleanup completed successfully!\n";
    
} catch (Exception $e) {
    echo "\n❌ Error during cleanup: " . $e->getMessage() . "\n";
    echo "Rolling back changes...\n";
} finally {
    // Step 6: Re-enable foreign key checks
    echo "\n🔒 Re-enabling foreign key checks...\n";
    DB::statement('SET FOREIGN_KEY_CHECKS=1');
}

// Step 7: Verify cleanup
echo "\n🔍 Verifying cleanup...\n";
$verificationTables = ['users', 'comments', 'threads', 'reactions', 'bookmarks', 'followers'];

foreach ($verificationTables as $table) {
    if (Schema::hasTable($table)) {
        $count = DB::table($table)->count();
        echo "  - {$table}: {$count} records\n";
    }
}

// Check Spatie permission assignments
if (Schema::hasTable('model_has_roles')) {
    $roleAssignments = DB::table('model_has_roles')->count();
    echo "  - User role assignments: {$roleAssignments} records\n";
}

echo "\n🎯 Database is now clean and ready for fresh seeding!\n";
echo "\n💡 Next steps:\n";
echo "  1. Run: php artisan db:seed --class=RolesAndPermissionsSeeder\n";
echo "  2. Run: php artisan db:seed --class=MechaMapUserSeeder\n";
echo "  3. Run: php artisan db:seed --class=BusinessUserSeeder\n";
echo "  4. Run other content seeders as needed\n";
