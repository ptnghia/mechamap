<?php
/**
 * Quick Fix for WebSocket Component Errors
 * Fixes: Undefined variable $configJson and Using $this when not in object context
 */

echo "🔧 Fixing WebSocket Component Errors...\n\n";

// Check if we're in Laravel project
if (!file_exists('artisan')) {
    echo "❌ Error: Please run this script from Laravel project root directory\n";
    exit(1);
}

$errors = 0;
$fixes = 0;

// Fix 1: Check and fix blade template
echo "📝 Step 1: Fixing blade template...\n";
$bladeFile = 'resources/views/components/websocket-config.blade.php';

if (file_exists($bladeFile)) {
    $content = file_get_contents($bladeFile);
    $originalContent = $content;
    
    // Fix incorrect method calls
    $patterns = [
        '/\{\!\!\s*\$configJson\(\)\s*\!\!\}/' => '{!! $configJson !!}',
        '/\{\!\!\s*\$this->configJson\(\)\s*\!\!\}/' => '{!! $configJson !!}',
    ];
    
    foreach ($patterns as $pattern => $replacement) {
        if (preg_match($pattern, $content)) {
            $content = preg_replace($pattern, $replacement, $content);
            echo "✅ Fixed method call pattern: $pattern\n";
            $fixes++;
        }
    }
    
    if ($content !== $originalContent) {
        file_put_contents($bladeFile, $content);
        echo "✅ Blade template updated\n";
    } else {
        echo "ℹ️  Blade template already correct\n";
    }
} else {
    echo "❌ Blade template not found: $bladeFile\n";
    $errors++;
}

// Fix 2: Check and fix component class
echo "\n📝 Step 2: Checking component class...\n";
$componentFile = 'app/View/Components/WebSocketConfig.php';

if (file_exists($componentFile)) {
    $content = file_get_contents($componentFile);
    
    // Check if render method passes configJson variable
    if (strpos($content, "'configJson' => \$this->configJson()") !== false) {
        echo "✅ Component class already passes configJson variable\n";
    } else {
        echo "⚠️  Component class may need manual update\n";
        echo "   Ensure render() method includes: 'configJson' => \$this->configJson()\n";
    }
    
    // Check if configJson method exists
    if (strpos($content, 'public function configJson()') !== false) {
        echo "✅ configJson() method exists\n";
    } else {
        echo "❌ configJson() method not found\n";
        $errors++;
    }
} else {
    echo "❌ Component class not found: $componentFile\n";
    $errors++;
}

// Fix 3: Clear caches
echo "\n🧹 Step 3: Clearing caches...\n";
$commands = [
    'php artisan view:clear' => 'View cache',
    'php artisan config:clear' => 'Config cache',
    'php artisan route:clear' => 'Route cache',
];

foreach ($commands as $command => $description) {
    echo "Clearing $description...\n";
    $output = [];
    $returnCode = 0;
    exec($command . ' 2>&1', $output, $returnCode);
    
    if ($returnCode === 0) {
        echo "✅ $description cleared\n";
    } else {
        echo "⚠️  Failed to clear $description: " . implode(' ', $output) . "\n";
    }
}

// Fix 4: Test component
echo "\n🧪 Step 4: Testing component...\n";

try {
    // Load Laravel
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    // Test component
    $component = new App\View\Components\WebSocketConfig(true);
    $configJson = $component->configJson();
    
    if ($configJson && json_decode($configJson)) {
        echo "✅ Component configJson() method works\n";
        echo "✅ JSON is valid (" . strlen($configJson) . " characters)\n";
    } else {
        echo "❌ Component configJson() method failed\n";
        $errors++;
    }
    
    // Test render method
    $view = $component->render();
    $viewData = $view->getData();
    
    if (isset($viewData['configJson'])) {
        echo "✅ Component passes configJson to view\n";
    } else {
        echo "❌ Component does not pass configJson to view\n";
        echo "Available view data: " . implode(', ', array_keys($viewData)) . "\n";
        $errors++;
    }
    
} catch (Exception $e) {
    echo "❌ Error testing component: " . $e->getMessage() . "\n";
    $errors++;
}

// Summary
echo "\n" . str_repeat("=", 50) . "\n";
echo "🎯 FIX SUMMARY\n";
echo str_repeat("=", 50) . "\n";
echo "Fixes applied: $fixes\n";
echo "Errors found: $errors\n";

if ($errors === 0) {
    echo "\n✅ ALL ISSUES FIXED!\n";
    echo "\n🎉 WebSocket component should now work correctly.\n";
    
    echo "\n📋 NEXT STEPS:\n";
    echo "1. Login to your application\n";
    echo "2. Open browser Developer Tools (F12)\n";
    echo "3. Check Console tab for WebSocket config\n";
    echo "4. Look for: 'MechaMap WebSocket Config'\n";
    
    echo "\n🔍 EXPECTED OUTPUT:\n";
    echo "window.websocketConfig = {\n";
    echo "  server_url: \"...\",\n";
    echo "  server_host: \"...\",\n";
    echo "  server_port: ...,\n";
    echo "  secure: ...,\n";
    echo "  laravel_url: \"...\",\n";
    echo "  environment: \"...\",\n";
    echo "  auto_init: ...\n";
    echo "}\n";
    
} else {
    echo "\n⚠️  SOME ISSUES REMAIN\n";
    echo "\nPlease check the errors above and fix manually:\n";
    echo "1. Ensure blade template uses: {!! \$configJson !!}\n";
    echo "2. Ensure component render() passes: 'configJson' => \$this->configJson()\n";
    echo "3. Ensure configJson() method exists in component class\n";
    echo "4. Clear all caches: php artisan optimize:clear\n";
}

echo "\n📞 TROUBLESHOOTING:\n";
echo "If you still get errors:\n";
echo "1. Check: resources/views/components/websocket-config.blade.php\n";
echo "2. Check: app/View/Components/WebSocketConfig.php\n";
echo "3. Run: php artisan about\n";
echo "4. Check Laravel logs: tail -f storage/logs/laravel.log\n";

echo "\n🔧 Fix completed!\n";
