<?php

// Bootstrap Laravel
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$kernel->handle($request);

echo "=== Kiểm tra translation keys trong components ===\n\n";

// Tìm tất cả file blade trong components
$componentFiles = [];
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator('resources/views/components')
);

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $componentFiles[] = $file->getPathname();
    }
}

echo "📁 Tìm thấy " . count($componentFiles) . " component files\n\n";

$allKeys = [];
$untranslatedKeys = [];
$problematicKeys = [];

foreach ($componentFiles as $file) {
    $relativePath = str_replace(realpath('.') . DIRECTORY_SEPARATOR, '', $file);
    echo "🔍 Checking: $relativePath\n";

    $content = file_get_contents($file);
    $fileKeys = 0;

    // Find __() calls
    preg_match_all('/__\([\'"]([^\'"]*)[\'"](?:\s*,\s*[^)]+)?\)/', $content, $matches);
    foreach ($matches[1] as $key) {
        $allKeys[] = ['type' => '__', 'key' => $key, 'file' => $relativePath];
        $fileKeys++;
    }

    // Find t_xxx() calls
    preg_match_all('/t_(\w+)\([\'"]([^\'"]*)[\'"](?:\s*,\s*[^)]+)?\)/', $content, $matches, PREG_SET_ORDER);
    foreach ($matches as $match) {
        $function = 't_' . $match[1];
        $key = $match[2];
        $allKeys[] = ['type' => $function, 'key' => $key, 'file' => $relativePath];
        $fileKeys++;
    }

    echo "  → Found $fileKeys translation calls\n";
}

echo "\n=== Testing " . count($allKeys) . " translation keys ===\n\n";

$groupedByStatus = [
    'working' => [],
    'untranslated' => [],
    'array_return' => [],
    'missing' => [],
    'exception' => []
];

foreach ($allKeys as $i => $keyInfo) {
    $type = $keyInfo['type'];
    $key = $keyInfo['key'];
    $file = basename($keyInfo['file']);

    if (($i + 1) % 50 == 0) {
        echo "Progress: " . ($i + 1) . "/" . count($allKeys) . "\n";
    }

    try {
        if ($type === '__') {
            $result = __($key);
        } else {
            $result = $type($key);
        }

        if (is_array($result)) {
            $groupedByStatus['array_return'][] = $keyInfo;
            $problematicKeys[] = $keyInfo;
        } elseif ($result === $key) {
            // Key trả về chính nó = chưa được dịch
            $groupedByStatus['untranslated'][] = $keyInfo;
            $untranslatedKeys[] = $keyInfo;
        } else {
            $groupedByStatus['working'][] = $keyInfo;
        }
    } catch (Exception $e) {
        $groupedByStatus['exception'][] = $keyInfo + ['error' => $e->getMessage()];
        $problematicKeys[] = $keyInfo;
    }
}

echo "\n=== SUMMARY REPORT ===\n";
echo "Total keys tested: " . count($allKeys) . "\n";
echo "✅ Working translations: " . count($groupedByStatus['working']) . "\n";
echo "⚠️  Untranslated keys: " . count($groupedByStatus['untranslated']) . "\n";
echo "❌ Array returns: " . count($groupedByStatus['array_return']) . "\n";
echo "💥 Exceptions: " . count($groupedByStatus['exception']) . "\n\n";

// Show untranslated keys grouped by file
if (count($untranslatedKeys) > 0) {
    echo "=== UNTRANSLATED KEYS BY FILE ===\n";
    $keysByFile = [];
    foreach ($untranslatedKeys as $keyInfo) {
        $file = basename($keyInfo['file']);
        if (!isset($keysByFile[$file])) {
            $keysByFile[$file] = [];
        }
        $keysByFile[$file][] = $keyInfo;
    }

    foreach ($keysByFile as $file => $keys) {
        echo "\n📄 $file (" . count($keys) . " untranslated keys):\n";
        foreach ($keys as $keyInfo) {
            echo "  - {$keyInfo['type']}('{$keyInfo['key']}')\n";
        }
    }
}

// Show problematic keys
if (count($problematicKeys) > 0) {
    echo "\n=== PROBLEMATIC KEYS ===\n";
    foreach ($problematicKeys as $keyInfo) {
        $error = isset($keyInfo['error']) ? " - Error: {$keyInfo['error']}" : "";
        echo "❌ {$keyInfo['type']}('{$keyInfo['key']}') in {$keyInfo['file']}$error\n";
    }
}

// Generate translation key suggestions
if (count($untranslatedKeys) > 0) {
    echo "\n=== SUGGESTED TRANSLATION KEYS TO ADD ===\n";
    $keysByCategory = [];

    foreach ($untranslatedKeys as $keyInfo) {
        $key = $keyInfo['key'];
        $parts = explode('.', $key);

        if (count($parts) >= 2) {
            $category = $parts[0];
            if (!isset($keysByCategory[$category])) {
                $keysByCategory[$category] = [];
            }
            $keysByCategory[$category][] = $key;
        }
    }

    foreach ($keysByCategory as $category => $keys) {
        echo "\n📋 Category: $category.php\n";
        $uniqueKeys = array_unique($keys);
        foreach ($uniqueKeys as $key) {
            echo "  '$key' => '$key',\n";
        }
    }
}

echo "\n✅ Analysis completed at " . date('Y-m-d H:i:s') . "\n";
