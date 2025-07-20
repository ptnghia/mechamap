<?php
/**
 * Fix Blade Syntax Errors
 * Automatically fix common syntax errors in Blade templates
 */

echo "🔧 Fixing Blade Syntax Errors...\n";
echo "================================\n\n";

$basePath = '/var/www/mechamap_com_usr/data/www/mechamap.com';
$viewsPath = $basePath . '/resources/views';

// Find all blade files
$bladeFiles = [];
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($viewsPath)
);

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php' && 
        strpos($file->getFilename(), '.blade.') !== false) {
        $bladeFiles[] = $file->getPathname();
    }
}

echo "📁 Found " . count($bladeFiles) . " Blade files\n\n";

$totalFixed = 0;
$totalFiles = 0;

foreach ($bladeFiles as $file) {
    $content = file_get_contents($file);
    $originalContent = $content;
    $fixes = 0;
    
    // Fix 1: placeholder={{ t_ui(...) }} -> placeholder="{{ t_ui(...) }}"
    $content = preg_replace('/placeholder=\{\{\s*(t_[a-z]+\([^}]+\))\s*\}\}/', 'placeholder="{{ $1 }}"', $content, -1, $count);
    $fixes += $count;
    
    // Fix 2: title={{ t_ui(...) }} -> title="{{ t_ui(...) }}"
    $content = preg_replace('/title=\{\{\s*(t_[a-z]+\([^}]+\))\s*\}\}/', 'title="{{ $1 }}"', $content, -1, $count);
    $fixes += $count;
    
    // Fix 3: >{{ t_ui(...) }}/tag> -> >{{ t_ui(...) }}</tag>
    $content = preg_replace('/>\{\{\s*(t_[a-z]+\([^}]+\))\s*\}\}\/([a-z]+)>/', '>{{ $1 }}</$2>', $content, -1, $count);
    $fixes += $count;
    
    // Fix 4: class="..."{{ t_ui(...) }}/tag> -> class="...">{{ t_ui(...) }}</tag>
    $content = preg_replace('/class="[^"]*"\{\{\s*(t_[a-z]+\([^}]+\))\s*\}\}\/([a-z]+)>/', 'class="...">{{ $1 }}</$2>', $content, -1, $count);
    $fixes += $count;
    
    // Fix 5: JavaScript textContent = {{ t_ui(...) }}; -> textContent = '{{ t_ui(...) }}';
    $content = preg_replace('/textContent\s*=\s*\{\{\s*(t_[a-z]+\([^}]+\))\s*\}\};/', "textContent = '{{ $1 }}';", $content, -1, $count);
    $fixes += $count;
    
    // Fix 6: {{ {{ }} }} -> {{ }}
    $content = preg_replace('/\{\{\s*\{\{\s*([^}]+)\s*\}\}\s*\}\}/', '{{ $1 }}', $content, -1, $count);
    $fixes += $count;
    
    // Fix 7: Missing quotes in attributes
    $content = preg_replace('/(\w+)=\{\{\s*(t_[a-z]+\([^}]+\))\s*\}\}/', '$1="{{ $2 }}"', $content, -1, $count);
    $fixes += $count;
    
    if ($content !== $originalContent) {
        file_put_contents($file, $content);
        $totalFiles++;
        $totalFixed += $fixes;
        echo "✅ Fixed: " . basename($file) . " ($fixes fixes)\n";
    }
}

echo "\n🎉 Blade syntax fixing completed!\n";
echo "📊 Files fixed: $totalFiles\n";
echo "📊 Total fixes applied: $totalFixed\n";

// Test a few helper functions
echo "\n🧪 Testing helper functions...\n";
try {
    // Bootstrap Laravel
    require_once $basePath . '/vendor/autoload.php';
    $app = require_once $basePath . '/bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    echo "Testing t_ui('buttons.cancel'): " . t_ui('buttons.cancel') . "\n";
    echo "Testing t_ui('common.light_mode'): " . t_ui('common.light_mode') . "\n";
    echo "Testing t_feature('marketplace.actions.add_to_cart'): " . t_feature('marketplace.actions.add_to_cart') . "\n";
    
    echo "✅ Helper functions are working!\n";
} catch (Exception $e) {
    echo "❌ Error testing helper functions: " . $e->getMessage() . "\n";
}
