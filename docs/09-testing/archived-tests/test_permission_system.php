<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔐 PERMISSION SYSTEM TABLES TEST\n";
echo "===============================\n\n";

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

// 1. SCHEMA VALIDATION
echo "📊 1. SCHEMA VALIDATION\n";
echo "======================\n";

$permissionTables = [
    'permissions',
    'roles',
    'model_has_permissions',
    'model_has_roles',
    'role_has_permissions',
    'personal_access_tokens'
];

foreach ($permissionTables as $table) {
    $exists = Schema::hasTable($table);
    echo "- {$table}: " . ($exists ? '✅ EXISTS' : '❌ MISSING') . "\n";

    if ($exists && in_array($table, ['permissions', 'roles'])) {
        $columns = Schema::getColumnListing($table);
        echo "  Columns: " . implode(', ', $columns) . "\n";
    }
}

// 2. PERMISSION & ROLE CREATION TEST
echo "\n🎯 2. ENGINEERING ROLES & PERMISSIONS TEST\n";
echo "==========================================\n";

try {
    // Tạo permissions cho mechanical engineering forum
    $permissions = [
        'content.create' => 'Create threads and posts',
        'content.edit' => 'Edit own content',
        'content.moderate' => 'Moderate any content',
        'cad.upload' => 'Upload CAD files',
        'cad.review' => 'Review CAD designs',
        'analysis.validate' => 'Validate FEA/CFD results',
        'expert.endorse' => 'Provide expert endorsements',
        'poll.create' => 'Create technical polls',
        'media.upload' => 'Upload technical media',
        'user.ban' => 'Ban users',
        'admin.access' => 'Access admin panel'
    ];

    echo "Creating engineering permissions:\n";
    foreach ($permissions as $name => $description) {
        $permission = Permission::firstOrCreate([
            'name' => $name,
            'guard_name' => 'web'
        ]);
        echo "✅ {$name} - {$description}\n";
    }

    // Tạo roles cho engineering community
    $roles = [
        'admin' => ['admin.access', 'user.ban', 'content.moderate', 'cad.review', 'analysis.validate', 'expert.endorse'],
        'moderator' => ['content.moderate', 'user.ban', 'cad.review', 'analysis.validate'],
        'senior_engineer' => ['content.create', 'content.edit', 'cad.upload', 'cad.review', 'analysis.validate', 'expert.endorse', 'poll.create', 'media.upload'],
        'engineer' => ['content.create', 'content.edit', 'cad.upload', 'poll.create', 'media.upload'],
        'student' => ['content.create', 'content.edit', 'media.upload'],
        'guest' => [] // Read-only
    ];

    echo "\nCreating engineering roles:\n";
    foreach ($roles as $roleName => $rolePermissions) {
        $role = Role::firstOrCreate([
            'name' => $roleName,
            'guard_name' => 'web'
        ]);

        // Assign permissions to role
        if (!empty($rolePermissions)) {
            $role->syncPermissions($rolePermissions);
        }

        echo "✅ {$roleName} - " . count($rolePermissions) . " permissions\n";
    }

    // 3. USER ROLE ASSIGNMENT TEST
    echo "\n👤 3. USER ROLE ASSIGNMENT TEST\n";
    echo "==============================\n";

    // Tìm user để test (hoặc tạo test user)
    $testUser = User::first();
    if (!$testUser) {
        $testUser = User::create([
            'name' => 'Test Engineer',
            'email' => 'test@mechamap.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now()
        ]);
        echo "✅ Created test user: {$testUser->name}\n";
    }

    // Assign role to user
    $testUser->assignRole('engineer');
    echo "✅ Assigned 'engineer' role to {$testUser->name}\n";

    // Test permission checking
    $canUploadCAD = $testUser->can('cad.upload');
    $canModerate = $testUser->can('content.moderate');

    echo "✅ Can upload CAD: " . ($canUploadCAD ? 'YES' : 'NO') . "\n";
    echo "✅ Can moderate: " . ($canModerate ? 'YES' : 'NO') . "\n";

    // 4. PERFORMANCE TEST
    echo "\n⚡ 4. PERFORMANCE TEST\n";
    echo "====================\n";

    $start = microtime(true);

    // Test role checking performance
    for ($i = 0; $i < 100; $i++) {
        $testUser->can('cad.upload');
    }

    $roleCheckTime = (microtime(true) - $start) * 1000;
    echo "✅ 100 role checks: {$roleCheckTime}ms\n";

    // Test permission list query
    $start = microtime(true);
    $userPermissions = $testUser->getAllPermissions();
    $permissionQueryTime = (microtime(true) - $start) * 1000;

    echo "✅ User permissions query: {$permissionQueryTime}ms\n";
    echo "✅ User has " . $userPermissions->count() . " permissions\n";

    // 5. DATA COUNTS
    echo "\n📊 5. DATA SUMMARY\n";
    echo "=================\n";
    echo "✅ Total Permissions: " . Permission::count() . "\n";
    echo "✅ Total Roles: " . Role::count() . "\n";
    echo "✅ Users with roles: " . User::whereHas('roles')->count() . "\n";

    $avgPerformance = ($roleCheckTime + $permissionQueryTime) / 2;
    echo "\n🎯 AVERAGE PERFORMANCE: {$avgPerformance}ms\n";
    echo "🎯 TARGET: <20ms - " . ($avgPerformance < 20 ? '✅ PASSED' : '❌ NEEDS OPTIMIZATION') . "\n";
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n🎉 PERMISSION SYSTEM TEST COMPLETED!\n";
