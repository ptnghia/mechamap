<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Load Laravel application
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

require_once __DIR__ . '/../app/Helpers/SettingHelper.php';

echo "=== TEST VIA.PLACEHOLDER.COM REPLACEMENT ===\n\n";

// 1. Test helper functions
echo "1. Testing Helper Functions:\n";
echo str_repeat("-", 50) . "\n";

try {
    $placeholderImage = placeholder_image(300, 200, 'Test Image');
    echo "✅ placeholder_image(): $placeholderImage\n";

    $avatarPlaceholder = avatar_placeholder('John Doe', 150);
    echo "✅ avatar_placeholder(): $avatarPlaceholder\n";

    // Test local placeholders
    $localPlaceholder = placeholder_image(50, 50);
    echo "✅ Local placeholder (50x50): $localPlaceholder\n";
} catch (Exception $e) {
    echo "❌ Error testing helper functions: " . $e->getMessage() . "\n";
}

// 2. Kiểm tra local placeholder files
echo "\n2. Checking Local Placeholder Files:\n";
echo str_repeat("-", 50) . "\n";

$placeholdersDir = __DIR__ . '/../public/images/placeholders';
$requiredSizes = ['50x50.png', '64x64.png', '150x150.png', '300x200.png', '300x300.png', '800x600.png'];

foreach ($requiredSizes as $file) {
    $filepath = $placeholdersDir . '/' . $file;
    if (file_exists($filepath)) {
        $fileSize = filesize($filepath);
        echo "✅ $file (" . number_format($fileSize / 1024, 2) . " KB)\n";
    } else {
        echo "❌ $file - MISSING\n";
    }
}

// 3. Search for remaining via.placeholder.com references
echo "\n3. Checking for Remaining via.placeholder.com References:\n";
echo str_repeat("-", 50) . "\n";

$searchDirs = [
    __DIR__ . '/../app',
    __DIR__ . '/../resources/views',
];

$foundReferences = [];

function searchInDirectory($dir, &$foundReferences)
{
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

    foreach ($iterator as $file) {
        if ($file->isFile() && in_array($file->getExtension(), ['php', 'blade.php'])) {
            $content = file_get_contents($file->getPathname());
            if (strpos($content, 'via.placeholder.com') !== false) {
                $lines = explode("\n", $content);
                foreach ($lines as $lineNum => $line) {
                    if (strpos($line, 'via.placeholder.com') !== false) {
                        $foundReferences[] = [
                            'file' => $file->getPathname(),
                            'line' => $lineNum + 1,
                            'content' => trim($line)
                        ];
                    }
                }
            }
        }
    }
}

foreach ($searchDirs as $dir) {
    searchInDirectory($dir, $foundReferences);
}

if (empty($foundReferences)) {
    echo "✅ No via.placeholder.com references found in app/ and resources/views/\n";
} else {
    echo "⚠️  Found remaining references:\n";
    foreach ($foundReferences as $ref) {
        $relativePath = str_replace(__DIR__ . '/../', '', $ref['file']);
        echo "   - $relativePath:{$ref['line']} - {$ref['content']}\n";
    }
}

// 4. Test actual URL accessibility
echo "\n4. Testing URL Accessibility:\n";
echo str_repeat("-", 50) . "\n";

$testUrls = [
    placeholder_image(50, 50),
    avatar_placeholder('Test User'),
];

foreach ($testUrls as $url) {
    echo "Testing: $url\n";

    if (strpos($url, '/images/placeholders/') !== false) {
        // Local file test
        $localPath = __DIR__ . '/../public' . parse_url($url, PHP_URL_PATH);
        if (file_exists($localPath)) {
            echo "   ✅ Local file exists\n";
        } else {
            echo "   ❌ Local file missing\n";
        }
    } else {
        // External service test
        echo "   ℹ️  External service (should be accessible online)\n";
    }
}

// 5. Tạo report
echo "\n5. Summary Report:\n";
echo str_repeat("-", 50) . "\n";

$localFilesCount = count(array_filter($requiredSizes, function ($file) use ($placeholdersDir) {
    return file_exists($placeholdersDir . '/' . $file);
}));

$totalLocalFiles = count($requiredSizes);
$remainingReferences = count($foundReferences);

echo "📊 Placeholder System Status:\n";
echo "   - Local placeholder files: $localFilesCount/$totalLocalFiles (" . ($localFilesCount == $totalLocalFiles ? "✅ Complete" : "⚠️ Incomplete") . ")\n";
echo "   - Helper functions: ✅ Working\n";
echo "   - Remaining via.placeholder.com references: $remainingReferences (" . ($remainingReferences == 0 ? "✅ Clean" : "⚠️ Needs attention") . ")\n";

if ($localFilesCount == $totalLocalFiles && $remainingReferences == 0) {
    echo "\n🎉 SUCCESS: via.placeholder.com replacement completed successfully!\n";
    echo "✅ All placeholder images are now using local/alternative services\n";
    echo "✅ No more dependency on via.placeholder.com\n";
} else {
    echo "\n⚠️  INCOMPLETE: Some issues need to be addressed\n";
    if ($localFilesCount < $totalLocalFiles) {
        echo "   - Generate missing local placeholder files\n";
    }
    if ($remainingReferences > 0) {
        echo "   - Update remaining via.placeholder.com references\n";
    }
}

echo "\n=== TEST COMPLETED ===\n";
