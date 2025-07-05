<?php
/**
 * Fix Icon Replacement Issues
 * Script để sửa các lỗi replacement từ Bootstrap Icons sang Font Awesome
 */

// Get all frontend blade files (exclude admin directory)
function getAllFrontendBladeFiles() {
    $files = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator('resources/views/')
    );

    foreach ($iterator as $file) {
        if ($file->isFile() &&
            $file->getExtension() === 'php' &&
            strpos($file->getFilename(), '.blade.php') !== false &&
            strpos($file->getPathname(), '/admin/') === false) {
            $files[] = str_replace('\\', '/', $file->getPathname());
        }
    }

    return $files;
}

$filesToFix = getAllFrontendBladeFiles();

// Fix patterns
$fixPatterns = [
    // Fix double class issues like "bi fas fa-icon"
    '/bi\s+(fas|far|fab)\s+fa-/' => '$1 fa-',

    // Fix remaining bi- prefixes
    '/\bbi-/' => '',

    // Fix specific icon issues
    '/class="fas fa-shopping-cart3"/' => 'class="fas fa-shopping-cart"',
    '/class="fas fa-shopping-cartx"/' => 'class="fas fa-shopping-cart"',
    '/class="fas fa-times-lg"/' => 'class="fas fa-times"',
    '/class="fas fa-plus-lg"/' => 'class="fas fa-plus"',
    '/class="fas fa-hourglass-halfsplit"/' => 'class="fas fa-hourglass-half"',

    // Clean up any remaining Bootstrap icon classes
    '/\bbootstrap-icons\b/' => '',
    '/\bbi\s+/' => '',
];

echo "🔧 FIXING ICON REPLACEMENT ISSUES\n";
echo "=================================\n";
echo "Cleaning up icon replacement errors...\n\n";

$totalFixes = 0;
$processedFiles = 0;

foreach ($filesToFix as $file) {
    if (!file_exists($file)) {
        echo "⚠️  File not found: $file\n";
        continue;
    }

    $content = file_get_contents($file);
    $originalContent = $content;
    $fileFixes = 0;

    // Apply fix patterns
    foreach ($fixPatterns as $pattern => $replacement) {
        $fixCount = 0;
        $content = preg_replace($pattern, $replacement, $content, -1, $fixCount);
        $fileFixes += $fixCount;
    }

    // Additional cleanup: remove empty class attributes
    $content = preg_replace('/class="\s*"/', '', $content);

    // Remove double spaces in class attributes
    $content = preg_replace('/class="([^"]*)\s+([^"]*)"/', 'class="$1 $2"', $content);
    $content = preg_replace('/class="([^"]*)\s{2,}([^"]*)"/', 'class="$1 $2"', $content);

    if ($content !== $originalContent) {
        file_put_contents($file, $content);
        echo "✅ $file: Fixed\n";
        $processedFiles++;
        $totalFixes++;
    } else {
        echo "ℹ️  $file: No fixes needed\n";
    }
}

echo "\n📊 FIX SUMMARY\n";
echo "==============\n";
echo "Files processed: $processedFiles\n";
echo "Total fixes applied: $totalFixes\n";

if ($totalFixes > 0) {
    echo "\n✅ Icon fixes completed successfully!\n";
    echo "🔧 Next steps:\n";
    echo "1. Test all pages for proper icon display\n";
    echo "2. Check for any remaining Bootstrap icon references\n";
    echo "3. Verify Font Awesome icons are working correctly\n";
} else {
    echo "\nℹ️  No fixes were needed.\n";
}

// Additional check: scan for remaining Bootstrap icon references
echo "\n🔍 SCANNING FOR REMAINING BOOTSTRAP ICONS\n";
echo "=========================================\n";

$remainingIcons = [];

foreach ($filesToFix as $file) {
    if (!file_exists($file)) continue;

    $content = file_get_contents($file);
    $lines = explode("\n", $content);

    foreach ($lines as $lineNum => $line) {
        // Check for remaining bi- classes
        if (preg_match('/\bbi-\w+/', $line, $matches)) {
            $remainingIcons[] = [
                'file' => $file,
                'line' => $lineNum + 1,
                'content' => trim($line),
                'icon' => $matches[0]
            ];
        }

        // Check for bi bi- patterns
        if (preg_match('/\bbi\s+bi-\w+/', $line, $matches)) {
            $remainingIcons[] = [
                'file' => $file,
                'line' => $lineNum + 1,
                'content' => trim($line),
                'icon' => $matches[0]
            ];
        }
    }
}

if (!empty($remainingIcons)) {
    echo "⚠️  Found " . count($remainingIcons) . " remaining Bootstrap icon references:\n\n";
    foreach ($remainingIcons as $icon) {
        echo "📁 {$icon['file']}:{$icon['line']}\n";
        echo "   Icon: {$icon['icon']}\n";
        echo "   Line: {$icon['content']}\n\n";
    }
    echo "🔧 These need to be fixed manually.\n";
} else {
    echo "✅ No remaining Bootstrap icon references found!\n";
}
