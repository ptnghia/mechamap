<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

echo "🔍 ANALYZING CURRENT DATABASE STATE\n";
echo "===================================\n\n";

// 1. Check current users and roles
echo "👥 CURRENT USER ANALYSIS:\n";
echo "=========================\n";

$totalUsers = User::count();
echo "Total users in database: {$totalUsers}\n\n";

if ($totalUsers > 0) {
    // Get role distribution
    $roleDistribution = User::select('role', DB::raw('count(*) as count'))
        ->groupBy('role')
        ->orderBy('count', 'desc')
        ->get();
    
    echo "Role distribution:\n";
    foreach ($roleDistribution as $role) {
        echo "  - {$role->role}: {$role->count} users\n";
    }
    
    // Get sample users for each role
    echo "\nSample users by role:\n";
    foreach ($roleDistribution as $role) {
        $sampleUser = User::where('role', $role->role)->first();
        echo "  - {$role->role}: {$sampleUser->name} ({$sampleUser->email})\n";
    }
}

echo "\n";

// 2. Check foreign key constraints
echo "🔗 FOREIGN KEY CONSTRAINTS ANALYSIS:\n";
echo "====================================\n";

try {
    $constraints = DB::select("
        SELECT 
            TABLE_NAME,
            COLUMN_NAME,
            CONSTRAINT_NAME,
            REFERENCED_TABLE_NAME,
            REFERENCED_COLUMN_NAME
        FROM 
            INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
        WHERE 
            REFERENCED_TABLE_SCHEMA = DATABASE()
            AND REFERENCED_TABLE_NAME = 'users'
        ORDER BY TABLE_NAME
    ");
    
    if (empty($constraints)) {
        echo "✅ No foreign key constraints found referencing users table\n";
    } else {
        echo "⚠️ Found foreign key constraints referencing users table:\n";
        foreach ($constraints as $constraint) {
            echo "  - {$constraint->TABLE_NAME}.{$constraint->COLUMN_NAME} → users.{$constraint->REFERENCED_COLUMN_NAME}\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Error checking constraints: " . $e->getMessage() . "\n";
}

echo "\n";

// 3. Check tables with user-related data
echo "📊 TABLES WITH USER-RELATED DATA:\n";
echo "=================================\n";

$userRelatedTables = [
    'comments' => 'user_id',
    'threads' => 'user_id', 
    'posts' => 'user_id',
    'followers' => ['user_id', 'follower_id'],
    'reactions' => 'user_id',
    'bookmarks' => 'user_id',
    'reports' => 'user_id',
    'messages' => ['sender_id', 'receiver_id'],
    'user_activities' => 'user_id',
    'showcases' => 'user_id'
];

foreach ($userRelatedTables as $table => $columns) {
    if (Schema::hasTable($table)) {
        try {
            $count = DB::table($table)->count();
            echo "  - {$table}: {$count} records\n";
            
            if (is_array($columns)) {
                foreach ($columns as $column) {
                    if (Schema::hasColumn($table, $column)) {
                        $userCount = DB::table($table)->whereNotNull($column)->count();
                        echo "    └─ {$column}: {$userCount} records with user references\n";
                    }
                }
            } else {
                if (Schema::hasColumn($table, $columns)) {
                    $userCount = DB::table($table)->whereNotNull($columns)->count();
                    echo "    └─ {$columns}: {$userCount} records with user references\n";
                }
            }
        } catch (Exception $e) {
            echo "  - {$table}: Error checking - " . $e->getMessage() . "\n";
        }
    } else {
        echo "  - {$table}: Table does not exist\n";
    }
}

echo "\n";

// 4. Check Spatie Permission tables
echo "🔐 SPATIE PERMISSION SYSTEM:\n";
echo "============================\n";

$spatieTable = [
    'roles' => 'Roles',
    'permissions' => 'Permissions', 
    'model_has_roles' => 'User-Role assignments',
    'model_has_permissions' => 'User-Permission assignments',
    'role_has_permissions' => 'Role-Permission assignments'
];

foreach ($spatieTable as $table => $description) {
    if (Schema::hasTable($table)) {
        try {
            $count = DB::table($table)->count();
            echo "  - {$description}: {$count} records\n";
        } catch (Exception $e) {
            echo "  - {$description}: Error checking - " . $e->getMessage() . "\n";
        }
    } else {
        echo "  - {$description}: Table does not exist\n";
    }
}

echo "\n";

// 5. Check migration status
echo "📋 MIGRATION STATUS:\n";
echo "===================\n";

try {
    $migrations = DB::table('migrations')
        ->orderBy('batch', 'desc')
        ->orderBy('id', 'desc')
        ->limit(10)
        ->get();
    
    echo "Last 10 migrations:\n";
    foreach ($migrations as $migration) {
        echo "  - {$migration->migration} (batch {$migration->batch})\n";
    }
} catch (Exception $e) {
    echo "❌ Error checking migrations: " . $e->getMessage() . "\n";
}

echo "\n";

// 6. Recommend cleanup strategy
echo "💡 RECOMMENDED CLEANUP STRATEGY:\n";
echo "================================\n";

if ($totalUsers > 0) {
    echo "1. 🗑️ Clear user-related data in dependency order:\n";
    foreach ($userRelatedTables as $table => $columns) {
        if (Schema::hasTable($table)) {
            echo "   - Clear {$table} table\n";
        }
    }
    echo "   - Clear Spatie permission assignments\n";
    echo "   - Clear users table\n";
    echo "\n2. 🔄 Reset auto-increment counters\n";
    echo "\n3. 🌱 Re-run seeders in correct order\n";
} else {
    echo "✅ Database appears to be clean, ready for seeding\n";
}

echo "\n🎯 Analysis complete! Ready for cleanup and reconstruction.\n";
