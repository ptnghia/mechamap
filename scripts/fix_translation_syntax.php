<?php

/**
 * Fix Translation Syntax Script
 * Converts __('namespace.key') to helper functions like t_content('key')
 */

$basePath = dirname(__DIR__);
$viewsPath = $basePath . '/resources/views';

// Find all Blade files
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($viewsPath)
);

$bladeFiles = [];
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php' &&
        strpos($file->getFilename(), '.blade.') !== false) {

        $relativePath = str_replace($basePath . '/', '', $file->getPathname());

        // Skip admin directories
        if (strpos($relativePath, '/admin/') !== false ||
            strpos($relativePath, 'admin.') !== false) {
            continue;
        }

        $bladeFiles[] = $file->getPathname();
    }
}

echo "🔧 Found " . count($bladeFiles) . " Blade files to fix\n\n";

// Define replacement patterns
$replacements = [
    // Content namespace
    '/__(\'content\.([^\']+)\')/m' => "t_content('$1')",
    '/__\("content\.([^"]+)"\)/m' => 't_content("$1")',

    // UI namespace
    '/__(\'ui\.([^\']+)\')/m' => "t_ui('$1')",
    '/__\("ui\.([^"]+)"\)/m' => 't_ui("$1")',

    // Core namespace
    '/__(\'core\.([^\']+)\')/m' => "t_core('$1')",
    '/__\("core\.([^"]+)"\)/m' => 't_core("$1")',

    // Features namespace
    '/__(\'features\.([^\']+)\')/m' => "t_feature('$1')",
    '/__\("features\.([^"]+)"\)/m' => 't_feature("$1")',

    // User namespace
    '/__(\'user\.([^\']+)\')/m' => "t_user('$1')",
    '/__\("user\.([^"]+)"\)/m' => 't_user("$1")',

    // Messages namespace (use content helper)
    '/__(\'messages\.([^\']+)\')/m' => "t_content('$1')",
    '/__\("messages\.([^"]+)"\)/m' => 't_content("$1")',

    // Nav namespace (use ui helper)
    '/__(\'nav\.([^\']+)\')/m' => "t_ui('$1')",
    '/__\("nav\.([^"]+)"\)/m' => 't_ui("$1")',

    // Forum namespace (use existing forum.php file)
    '/__(\'forum\.([^\']+)\')/m' => "__('forum.$1')",
    '/__\("forum\.([^"]+)"\)/m' => '__("forum.$1")',

    // Forums namespace (use existing forums.php file)
    '/__(\'forums\.([^\']+)\')/m' => "__('forums.$1')",
    '/__\("forums\.([^"]+)"\)/m' => '__("forums.$1")',

    // Auth namespace (use existing auth.php file)
    '/__(\'auth\.([^\']+)\')/m' => "__('auth.$1')",
    '/__\("auth\.([^"]+)"\)/m' => '__("auth.$1")',
];

$totalReplacements = 0;
$filesModified = [];

foreach ($bladeFiles as $file) {
    $content = file_get_contents($file);
    $originalContent = $content;

    foreach ($replacements as $pattern => $replacement) {
        $newContent = preg_replace($pattern, $replacement, $content);
        if ($newContent !== $content) {
            $matches = [];
            preg_match_all($pattern, $content, $matches);
            $count = count($matches[0]);
            $totalReplacements += $count;
            echo "   Fixed $count keys in " . basename($file) . "\n";
            $content = $newContent;
        }
    }

    if ($content !== $originalContent) {
        file_put_contents($file, $content);
        $filesModified[] = basename($file);
    }
}

echo "\n✅ COMPLETED!\n";
echo "📊 Total replacements: $totalReplacements\n";
echo "📁 Files modified: " . count($filesModified) . "\n";

if (count($filesModified) > 0) {
    echo "\n📝 Modified files:\n";
    foreach ($filesModified as $file) {
        echo "   - $file\n";
    }
}

echo "\n🎯 Next steps:\n";
echo "   1. Clear Laravel cache: php artisan cache:clear\n";
echo "   2. Clear view cache: php artisan view:clear\n";
echo "   3. Test the website to verify translations work\n";
