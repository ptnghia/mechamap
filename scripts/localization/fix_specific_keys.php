<?php
/**
 * Fix Specific Translation Keys Issues
 */

echo "🔧 FIXING SPECIFIC TRANSLATION KEY ISSUES\n";
echo "=========================================\n\n";

$basePath = '/var/www/mechamap_com_usr/data/www/mechamap.com';

// Bootstrap Laravel
require_once $basePath . '/vendor/autoload.php';
$app = require_once $basePath . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$fixes = [
    [
        'file' => 'resources/views/threads/partials/poll.blade.php',
        'old' => "trans_choice('forum.poll.votes', \$voteCount)",
        'new' => "trans_choice('forum/poll/votes', \$voteCount)",
        'description' => 'Fix forum poll votes key'
    ],
    [
        'file' => 'resources/views/components/notification-dropdown.blade.php', 
        'old' => "t_ui('auth.login_to_view_notifications')",
        'new' => "t_ui('auth/login_to_view_notifications')",
        'description' => 'Fix auth login notification key'
    ],
    [
        'file' => 'resources/views/components/registration-wizard.blade.php',
        'old' => "'title' => {{ t_ui('auth.register_mechamap_account') }},",
        'new' => "'title' => t_ui('auth/register_mechamap_account'),",
        'description' => 'Fix registration wizard title syntax and key'
    ],
    [
        'file' => 'resources/views/components/menu/admin-menu.blade.php',
        'old' => "t_ui('roles.admin')",
        'new' => "t_ui('user/roles/admin')",
        'description' => 'Fix admin role key'
    ]
];

echo "🔄 APPLYING SPECIFIC FIXES...\n";
echo "=============================\n";

foreach ($fixes as $fix) {
    $filePath = $basePath . '/' . $fix['file'];
    
    echo "🔧 " . $fix['description'] . "\n";
    echo "   File: " . $fix['file'] . "\n";
    
    if (!file_exists($filePath)) {
        echo "   ❌ File not found\n\n";
        continue;
    }
    
    $content = file_get_contents($filePath);
    $originalContent = $content;
    
    // Apply the fix
    $content = str_replace($fix['old'], $fix['new'], $content);
    
    if ($content !== $originalContent) {
        file_put_contents($filePath, $content);
        echo "   ✅ Fixed successfully\n";
    } else {
        echo "   ⚠️  No changes made (pattern not found or already fixed)\n";
    }
    
    echo "\n";
}

// Clear Laravel caches
echo "🧹 CLEARING CACHES...\n";
echo "=====================\n";

$commands = [
    'php artisan cache:clear',
    'php artisan view:clear',
    'php artisan config:clear'
];

foreach ($commands as $command) {
    echo "Running: $command\n";
    $output = shell_exec("cd $basePath && $command 2>&1");
    echo "   " . trim($output) . "\n";
}

echo "\n🧪 TESTING FIXES...\n";
echo "===================\n";

$testKeys = [
    'forum/poll/votes',
    'auth/login_to_view_notifications', 
    'auth/register_mechamap_account',
    'ui/language/switched_successfully',
    'ui/language/switch_failed',
    'ui/language/auto_detected',
    'user/roles/admin'
];

$allWorking = true;
foreach ($testKeys as $key) {
    $result = __($key);
    
    if ($result === $key) {
        echo "❌ $key - Still not working\n";
        $allWorking = false;
    } else {
        echo "✅ $key - Working: '$result'\n";
    }
}

if ($allWorking) {
    echo "\n🎉 All translation keys are now working!\n";
} else {
    echo "\n⚠️  Some keys may still need attention.\n";
}

echo "\n📋 SUMMARY\n";
echo "==========\n";
echo "Applied " . count($fixes) . " specific fixes\n";
echo "Cleared Laravel caches\n";
echo "Translation keys should now display properly\n";

echo "\n🎯 NEXT STEPS:\n";
echo "==============\n";
echo "1. Test the affected pages in browser\n";
echo "2. Verify all translations display correctly\n";
echo "3. Check for any remaining raw translation keys\n";
echo "4. Commit changes if everything looks good\n";
