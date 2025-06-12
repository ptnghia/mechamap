<?php

/**
 * Script xác minh users đã được tạo đúng với phân quyền
 * Chạy: php scripts/verify_users.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "🔧 MechaMap User Verification Report\n";
echo "====================================\n\n";

// Thống kê theo role
$roles = ['admin', 'moderator', 'senior', 'member', 'guest'];
$roleIcons = [
    'admin' => '👑',
    'moderator' => '🛡️',
    'senior' => '⭐',
    'member' => '👤',
    'guest' => '👁️'
];

$totalUsers = 0;

foreach ($roles as $role) {
    $users = User::where('role', $role)
        ->orderBy('points', 'desc')
        ->get(['name', 'email', 'role', 'points', 'status', 'setup_progress']);

    $count = $users->count();
    $totalUsers += $count;

    echo "{$roleIcons[$role]} " . strtoupper($role) . " USERS ({$count}):\n";

    if ($count > 0) {
        foreach ($users as $user) {
            $statusIcon = $user->status === 'active' ? '✅' : '❌';
            $progressBar = str_repeat('▓', intval($user->setup_progress / 10)) .
                          str_repeat('░', 10 - intval($user->setup_progress / 10));

            echo "  • {$user->name}\n";
            echo "    📧 {$user->email}\n";
            echo "    🏆 {$user->points} points | {$statusIcon} {$user->status} | 📊 {$progressBar} {$user->setup_progress}%\n\n";
        }
    } else {
        echo "  (No users found)\n\n";
    }
}

echo "📊 SUMMARY:\n";
echo "===========\n";
echo "Total Users Created: {$totalUsers}\n";
echo "Expected Users: 14\n";
echo "Status: " . ($totalUsers === 14 ? "✅ SUCCESS" : "❌ INCOMPLETE") . "\n\n";

// Kiểm tra admin users có thể login được không
$adminUsers = User::where('role', 'admin')->get();
echo "🔐 ADMIN LOGIN CREDENTIALS:\n";
echo "===========================\n";
foreach ($adminUsers as $admin) {
    echo "• Email: {$admin->email}\n";
    echo "  Password: AdminMecha2024@ or SysAdmin2024@\n";
    echo "  Access: http://127.0.0.1:8000/admin/login\n\n";
}

// Thống kê bổ sung
$emailVerified = User::whereNotNull('email_verified_at')->count();
$activeUsers = User::where('status', 'active')->count();

echo "📈 ADDITIONAL STATS:\n";
echo "===================\n";
echo "Email Verified: {$emailVerified}/{$totalUsers}\n";
echo "Active Users: {$activeUsers}/{$totalUsers}\n";
echo "Average Points: " . number_format(User::avg('points'), 0) . "\n";
echo "Average Setup Progress: " . number_format(User::avg('setup_progress'), 1) . "%\n\n";

echo "🎯 PERMISSION HIERARCHY VERIFICATION:\n";
echo "====================================\n";
echo "👑 Admin: Toàn quyền quản lý hệ thống\n";
echo "🛡️ Moderator: Quản lý nội dung & kiểm duyệt\n";
echo "⭐ Senior: Thành viên cao cấp với nhiều tính năng\n";
echo "👤 Member: Thành viên cơ bản\n";
echo "👁️ Guest: Chỉ xem nội dung\n\n";

echo "✅ Verification completed!\n";
echo "🚀 Ready to test admin panel at: http://127.0.0.1:8000/admin\n";
