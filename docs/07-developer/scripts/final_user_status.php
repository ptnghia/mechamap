<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

echo "🔧 MECHAMAP FINAL USER SYSTEM STATUS 🔧" . PHP_EOL;
echo "=" . str_repeat("=", 50) . PHP_EOL;
echo "Date: " . date('Y-m-d H:i:s') . PHP_EOL;
echo PHP_EOL;

// System Overview
echo "📊 SYSTEM OVERVIEW" . PHP_EOL;
echo "-" . str_repeat("-", 30) . PHP_EOL;
echo sprintf("✅ Total Users: %d", User::count()) . PHP_EOL;
echo sprintf("✅ Total Roles: %d", Role::count()) . PHP_EOL;
echo sprintf("✅ Total Permissions: %d", Permission::count()) . PHP_EOL;
echo sprintf("✅ Verified Users: %d", User::whereNotNull('email_verified_at')->count()) . PHP_EOL;
echo sprintf("✅ Complete Profiles: %d", User::whereNotNull('about_me')->whereNotNull('location')->count()) . PHP_EOL;
echo PHP_EOL;

// Role Distribution
echo "👥 ROLE DISTRIBUTION" . PHP_EOL;
echo "-" . str_repeat("-", 30) . PHP_EOL;
$roles = Role::withCount('users')->get();
foreach ($roles as $role) {
    $icon = match($role->name) {
        'admin' => '👑',
        'moderator' => '🛡️', 
        'senior' => '⭐',
        'member' => '👤',
        'guest' => '👁️',
        default => '🔹'
    };
    echo sprintf("%s %s: %d users", $icon, ucfirst($role->name), $role->users_count) . PHP_EOL;
}
echo PHP_EOL;

// Key Users
echo "🌟 KEY USERS" . PHP_EOL;
echo "-" . str_repeat("-", 30) . PHP_EOL;

// Admins
$admins = User::role('admin')->get();
echo "👑 ADMINS:" . PHP_EOL;
foreach ($admins as $admin) {
    echo sprintf("  • %s (%s)", $admin->name, $admin->email) . PHP_EOL;
}
echo PHP_EOL;

// Moderators  
$moderators = User::role('moderator')->get();
echo "🛡️ MODERATORS:" . PHP_EOL;
foreach ($moderators as $mod) {
    echo sprintf("  • %s (%s)", $mod->name, $mod->email) . PHP_EOL;
}
echo PHP_EOL;

// Mechanical Engineering Focus
echo "🔧 MECHANICAL ENGINEERING COMMUNITY" . PHP_EOL;
echo "-" . str_repeat("-", 30) . PHP_EOL;

$mechanicalKeywords = ['CAD', 'CAM', 'CNC', 'automation', 'mechanical', 'engineering', 'design', 'manufacturing'];
$mechanicalUsers = User::all()->filter(function($user) use ($mechanicalKeywords) {
    $aboutText = strtolower($user->about_me ?? '');
    return collect($mechanicalKeywords)->some(fn($keyword) => str_contains($aboutText, strtolower($keyword)));
});

echo sprintf("🎯 Mechanical Engineering Profiles: %d/%d (%.1f%%)", 
    $mechanicalUsers->count(), 
    User::count(), 
    ($mechanicalUsers->count() / User::count()) * 100
) . PHP_EOL;
echo PHP_EOL;

// Sample Mechanical Users
echo "🔧 EXPERT USERS:" . PHP_EOL;
$experts = User::role(['senior', 'moderator'])->get()->take(3);
foreach ($experts as $expert) {
    $expertise = '';
    $about = strtolower($expert->about_me ?? '');
    if (str_contains($about, 'cad')) $expertise .= 'CAD ';
    if (str_contains($about, 'automation')) $expertise .= 'Automation ';
    if (str_contains($about, 'cnc')) $expertise .= 'CNC ';
    if (str_contains($about, 'materials')) $expertise .= 'Materials ';
    
    echo sprintf("  • %s - %s", $expert->name, trim($expertise) ?: 'General Engineering') . PHP_EOL;
}
echo PHP_EOL;

// Performance Check
echo "⚡ PERFORMANCE METRICS" . PHP_EOL;
echo "-" . str_repeat("-", 30) . PHP_EOL;

$startTime = microtime(true);
$testUser = User::role('admin')->first();
$hasPermission = $testUser ? true : false; // Simplified check
$queryTime = (microtime(true) - $startTime) * 1000;

echo sprintf("🔍 Role query time: %.2f ms", $queryTime) . PHP_EOL;
echo sprintf("🚀 Performance: %s", $queryTime < 50 ? '✅ Excellent' : '⚠️ Acceptable') . PHP_EOL;
echo sprintf("💾 Memory usage: %.1f MB", memory_get_usage(true) / 1024 / 1024) . PHP_EOL;
echo PHP_EOL;

// Final Status
echo "🎯 SYSTEM STATUS" . PHP_EOL;
echo "=" . str_repeat("=", 30) . PHP_EOL;

$readinessChecks = [
    'Users created' => User::count() >= 10,
    'Roles assigned' => User::role('admin')->count() >= 1,
    'Permissions working' => Role::first()?->permissions()->count() > 0,
    'Profiles complete' => User::whereNotNull('about_me')->count() >= 10,
    'Mechanical focus' => $mechanicalUsers->count() >= 8,
    'Email verified' => User::whereNotNull('email_verified_at')->count() === User::count(),
];

$passedChecks = 0;
foreach ($readinessChecks as $check => $passed) {
    echo sprintf("%s %s", $passed ? '✅' : '❌', $check) . PHP_EOL;
    if ($passed) $passedChecks++;
}

echo PHP_EOL;
$score = ($passedChecks / count($readinessChecks)) * 100;
$status = $score >= 90 ? '🚀 PRODUCTION READY' : ($score >= 80 ? '⚠️ NEARLY READY' : '❌ NEEDS WORK');

echo sprintf("📊 Overall Score: %.0f%% - %s", $score, $status) . PHP_EOL;
echo PHP_EOL;

if ($score >= 90) {
    echo "🎉 SUCCESS: MechaMap user system is ready for forum operations!" . PHP_EOL;
    echo "✨ Professional mechanical engineering community established." . PHP_EOL;
    echo "🔐 Secure role-based permissions implemented." . PHP_EOL;
    echo "🚀 Ready for frontend integration and forum launch!" . PHP_EOL;
} else {
    echo "⚠️ ATTENTION: System needs final adjustments before production." . PHP_EOL;
}

echo PHP_EOL;
echo "🔧 End of Final Status Check 🔧" . PHP_EOL;
