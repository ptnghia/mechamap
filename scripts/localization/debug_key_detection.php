<?php
/**
 * Debug Key Detection
 * Kiểm tra xem tại sao không tìm được keys trong Blade templates
 */

echo "🔍 DEBUG KEY DETECTION\n";
echo "======================\n\n";

$basePath = '/var/www/mechamap_com_usr/data/www/mechamap.com';

// Test với một file cụ thể mà chúng ta biết có translation keys
$testFile = $basePath . '/resources/views/home.blade.php';

if (!file_exists($testFile)) {
    echo "❌ Test file not found: $testFile\n";
    exit(1);
}

$content = file_get_contents($testFile);
echo "📄 Testing file: resources/views/home.blade.php\n";
echo "File size: " . strlen($content) . " bytes\n\n";

// Show a sample of the content
echo "📝 SAMPLE CONTENT (first 500 chars):\n";
echo "=====================================\n";
echo substr($content, 0, 500) . "...\n\n";

// Test different patterns
$patterns = [
    'simple_underscore' => '/__(\'([^\']+)\')/m',
    'simple_double_quote' => '/__\("([^"]+)"\)/m',
    'both_quotes' => '/__(\'([^\']+)\'|"([^"]+)")/m',
    'with_spaces' => '/\{\{\s*__(\'([^\']+)\'|"([^"]+)")\s*\}\}/m',
    'any_underscore' => '/__\(/m'
];

echo "🧪 TESTING PATTERNS:\n";
echo "====================\n";

foreach ($patterns as $name => $pattern) {
    if (preg_match_all($pattern, $content, $matches, PREG_SET_ORDER)) {
        echo "✅ $name: Found " . count($matches) . " matches\n";
        
        // Show first few matches
        for ($i = 0; $i < min(3, count($matches)); $i++) {
            echo "   Match $i: " . trim($matches[$i][0]) . "\n";
            if (isset($matches[$i][1])) {
                echo "   Key: " . $matches[$i][1] . "\n";
            }
            if (isset($matches[$i][2])) {
                echo "   Key2: " . $matches[$i][2] . "\n";
            }
        }
        echo "\n";
    } else {
        echo "❌ $name: No matches\n\n";
    }
}

// Manual search for specific strings
echo "🔍 MANUAL SEARCH:\n";
echo "=================\n";

$searchTerms = [
    "__('home.featured_showcases')",
    "__('buttons.view_all')",
    "{{ __(",
    "__(",
    "home.featured_showcases",
    "featured_showcases"
];

foreach ($searchTerms as $term) {
    $count = substr_count($content, $term);
    echo ($count > 0 ? "✅" : "❌") . " '$term': $count occurrences\n";
}

echo "\n🔍 FIND ALL __() PATTERNS:\n";
echo "==========================\n";

// More comprehensive search
if (preg_match_all('/__(\'[^\']*\'|"[^"]*")/', $content, $matches)) {
    echo "Found " . count($matches[0]) . " __() calls:\n";
    foreach ($matches[0] as $i => $match) {
        echo "  $i: $match\n";
        if ($i >= 10) {
            echo "  ... and " . (count($matches[0]) - 10) . " more\n";
            break;
        }
    }
} else {
    echo "No __() calls found\n";
}

echo "\n🔍 FIND ALL TRANSLATION-LIKE PATTERNS:\n";
echo "======================================\n";

// Look for any translation-like patterns
$allPatterns = [
    '__(' => '/__(/',
    't_ui(' => '/t_ui\(/',
    '@lang(' => '/@lang\(/',
    'trans(' => '/trans\(/',
    '{{ __(' => '/\{\{\s*__\(/',
];

foreach ($allPatterns as $desc => $pattern) {
    if (preg_match_all($pattern, $content, $matches)) {
        echo "✅ $desc: " . count($matches[0]) . " occurrences\n";
    } else {
        echo "❌ $desc: 0 occurrences\n";
    }
}

echo "\n📊 TESTING WITH MULTIPLE FILES:\n";
echo "===============================\n";

// Test with a few more files
$testFiles = [
    '/resources/views/whats-new/trending.blade.php',
    '/resources/views/components/sidebar.blade.php',
    '/resources/views/auth/login.blade.php'
];

foreach ($testFiles as $file) {
    $fullPath = $basePath . $file;
    if (file_exists($fullPath)) {
        $fileContent = file_get_contents($fullPath);
        $count = preg_match_all('/__(\'[^\']*\'|"[^"]*")/', $fileContent, $matches);
        echo "📄 $file: $count __() calls\n";
        
        if ($count > 0) {
            echo "   Sample keys:\n";
            for ($i = 0; $i < min(3, count($matches[0])); $i++) {
                echo "   - " . $matches[0][$i] . "\n";
            }
        }
    } else {
        echo "❌ $file: File not found\n";
    }
}

echo "\n💡 ANALYSIS:\n";
echo "============\n";
echo "If we're not finding __() calls in files we know have them,\n";
echo "the issue might be:\n";
echo "1. Pattern matching is too strict\n";
echo "2. Files are using different translation methods\n";
echo "3. Keys are dynamically generated\n";
echo "4. Files are using helper functions instead of __() directly\n\n";

// Test our known working keys
echo "🧪 TESTING KNOWN WORKING KEYS:\n";
echo "==============================\n";

require_once $basePath . '/vendor/autoload.php';
$app = require_once $basePath . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$knownKeys = [
    'home.featured_showcases',
    'ui.common.trending',
    'ui.community.quick_access',
    'buttons.view_all'
];

foreach ($knownKeys as $key) {
    $result = __($key);
    $status = ($result !== $key) ? "✅ WORKS" : "❌ MISSING";
    echo "$status '$key' → '$result'\n";
}

echo "\nThis will help us understand the gap between available and detected keys.\n";
