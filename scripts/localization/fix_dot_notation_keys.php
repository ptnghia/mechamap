<?php
/**
 * Fix Dot Notation Keys to Slash Notation
 * Convert ui.common.* to ui/common.* in Blade files
 */

echo "🔧 FIXING DOT NOTATION TO SLASH NOTATION\n";
echo "========================================\n\n";

$basePath = '/var/www/mechamap_com_usr/data/www/mechamap.com';

// Files to fix
$filesToFix = [
    'resources/views/components/header.blade.php',
    'resources/views/components/footer.blade.php'
];

$totalReplacements = 0;
$totalFiles = 0;

foreach ($filesToFix as $file) {
    $fullPath = $basePath . '/' . $file;
    
    if (!file_exists($fullPath)) {
        echo "⚠️ File not found: $file\n";
        continue;
    }
    
    echo "📁 Processing: $file\n";
    
    $content = file_get_contents($fullPath);
    $originalContent = $content;
    $replacements = 0;
    
    // Define replacement patterns
    $patterns = [
        // ui.common.* -> ui/common.*
        '/__(\'ui\.common\.([^\']+)\')/m' => "__('ui/common.$1')",
        '/__\("ui\.common\.([^"]+)"\)/m' => '__("ui/common.$1")',
        
        // ui.buttons.* -> ui/buttons.*
        '/__(\'ui\.buttons\.([^\']+)\')/m' => "__('ui/buttons.$1')",
        '/__\("ui\.buttons\.([^"]+)"\)/m' => '__("ui/buttons.$1")',
        
        // ui.forms.* -> ui/forms.*
        '/__(\'ui\.forms\.([^\']+)\')/m' => "__('ui/forms.$1')",
        '/__\("ui\.forms\.([^"]+)"\)/m' => '__("ui/forms.$1")',
        
        // ui.alerts.* -> ui/alerts.*
        '/__(\'ui\.alerts\.([^\']+)\')/m' => "__('ui/alerts.$1')",
        '/__\("ui\.alerts\.([^"]+)"\)/m' => '__("ui/alerts.$1")',
        
        // ui.navigation.* -> ui/navigation.*
        '/__(\'ui\.navigation\.([^\']+)\')/m' => "__('ui/navigation.$1')",
        '/__\("ui\.navigation\.([^"]+)"\)/m' => '__("ui/navigation.$1")',
        
        // core.auth.* -> core/auth.*
        '/__(\'core\.auth\.([^\']+)\')/m' => "__('core/auth.$1')",
        '/__\("core\.auth\.([^"]+)"\)/m' => '__("core/auth.$1")',
        
        // core.validation.* -> core/validation.*
        '/__(\'core\.validation\.([^\']+)\')/m' => "__('core/validation.$1')",
        '/__\("core\.validation\.([^"]+)"\)/m' => '__("core/validation.$1")',
        
        // features.marketplace.* -> features/marketplace.*
        '/__(\'features\.marketplace\.([^\']+)\')/m' => "__('features/marketplace.$1')",
        '/__\("features\.marketplace\.([^"]+)"\)/m' => '__("features/marketplace.$1")',
        
        // features.forums.* -> features/forums.*
        '/__(\'features\.forums\.([^\']+)\')/m' => "__('features/forums.$1')",
        '/__\("features\.forums\.([^"]+)"\)/m' => '__("features/forums.$1")',
        
        // user.profile.* -> user/profile.*
        '/__(\'user\.profile\.([^\']+)\')/m' => "__('user/profile.$1')",
        '/__\("user\.profile\.([^"]+)"\)/m' => '__("user/profile.$1")',
        
        // user.dashboard.* -> user/dashboard.*
        '/__(\'user\.dashboard\.([^\']+)\')/m' => "__('user/dashboard.$1')",
        '/__\("user\.dashboard\.([^"]+)"\)/m' => '__("user/dashboard.$1")',
        
        // admin.dashboard.* -> admin/dashboard.*
        '/__(\'admin\.dashboard\.([^\']+)\')/m' => "__('admin/dashboard.$1')",
        '/__\("admin\.dashboard\.([^"]+)"\)/m' => '__("admin/dashboard.$1")',
        
        // admin.users.* -> admin/users.*
        '/__(\'admin\.users\.([^\']+)\')/m' => "__('admin/users.$1')",
        '/__\("admin\.users\.([^"]+)"\)/m' => '__("admin/users.$1")',
        
        // content.pages.* -> content/pages.*
        '/__(\'content\.pages\.([^\']+)\')/m' => "__('content/pages.$1')",
        '/__\("content\.pages\.([^"]+)"\)/m' => '__("content/pages.$1")',
    ];
    
    foreach ($patterns as $pattern => $replacement) {
        $newContent = preg_replace($pattern, $replacement, $content, -1, $count);
        if ($count > 0) {
            $content = $newContent;
            $replacements += $count;
            echo "   ✅ Fixed $count occurrences of pattern: $pattern\n";
        }
    }
    
    if ($content !== $originalContent) {
        file_put_contents($fullPath, $content);
        $totalFiles++;
        $totalReplacements += $replacements;
        echo "   📝 Saved $file with $replacements replacements\n";
    } else {
        echo "   ℹ️ No changes needed in $file\n";
    }
    
    echo "\n";
}

echo "📊 SUMMARY\n";
echo "==========\n";
echo "Files processed: " . count($filesToFix) . "\n";
echo "Files modified: $totalFiles\n";
echo "Total replacements: $totalReplacements\n";

if ($totalReplacements > 0) {
    echo "\n🧪 Testing some fixed keys:\n";
    
    // Bootstrap Laravel to test
    require_once $basePath . '/vendor/autoload.php';
    $app = require_once $basePath . '/bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    $testKeys = [
        'ui/common.community',
        'ui/common.showcase', 
        'ui/common.marketplace',
        'ui/common.add'
    ];
    
    foreach ($testKeys as $key) {
        $result = __($key);
        $status = ($result === $key) ? "❌ FAIL" : "✅ WORK";
        echo "   $status __('$key') → '$result'\n";
    }
}

echo "\n🎯 NEXT STEPS:\n";
echo "==============\n";
echo "1. Clear view cache: php artisan view:clear\n";
echo "2. Test the website navigation\n";
echo "3. Check for any remaining dot notation keys\n";
echo "4. Add any missing translation keys if needed\n";
