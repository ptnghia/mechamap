<?php
/**
 * Find Used Translation Keys
 * Comprehensive scan for translation key usage
 */

echo "🔍 Finding Used Translation Keys...\n";

$usedKeys = [];
$files = [];

// Get all PHP files
$directories = [
    'resources/views',
    'app/Http/Controllers',
    'app/Services'
];

foreach ($directories as $dir) {
    if (is_dir($dir)) {
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $files[] = $file->getPathname();
            }
        }
    }
}

echo "Scanning " . count($files) . " files...\n";

foreach ($files as $file) {
    $content = file_get_contents($file);
    $relativePath = str_replace(getcwd() . '/', '', $file);
    
    // Find all __() patterns
    if (preg_match_all('/__(\'([^\']+)\'|\("([^"]+)"\))/', $content, $matches, PREG_OFFSET_CAPTURE)) {
        foreach ($matches[0] as $i => $match) {
            $key = $matches[2][$i][0] ?: $matches[3][$i][0];
            if (!empty($key)) {
                $line = substr_count(substr($content, 0, $match[1]), "\n") + 1;
                $usedKeys[$key][] = [
                    'file' => $relativePath,
                    'line' => $line
                ];
            }
        }
    }
    
    // Find Blade {{ __() }} patterns
    if (preg_match_all('/\{\{\s*__(\'([^\']+)\'|\("([^"]+)"\))\s*\}\}/', $content, $matches, PREG_OFFSET_CAPTURE)) {
        foreach ($matches[0] as $i => $match) {
            $key = $matches[2][$i][0] ?: $matches[3][$i][0];
            if (!empty($key)) {
                $line = substr_count(substr($content, 0, $match[1]), "\n") + 1;
                $usedKeys[$key][] = [
                    'file' => $relativePath,
                    'line' => $line
                ];
            }
        }
    }
}

echo "Found " . count($usedKeys) . " unique keys\n";
echo "Total usages: " . array_sum(array_map('count', $usedKeys)) . "\n\n";

// Top 20 most used keys
echo "🔥 Top 20 Most Used Keys:\n";
$usage = [];
foreach ($usedKeys as $key => $locations) {
    $usage[$key] = count($locations);
}
arsort($usage);

foreach (array_slice($usage, 0, 20, true) as $key => $count) {
    echo "   $key: $count usages\n";
}

echo "\n📁 Key Patterns:\n";
$patterns = [];
foreach (array_keys($usedKeys) as $key) {
    $parts = explode('.', $key);
    $prefix = $parts[0] ?? 'unknown';
    $patterns[$prefix] = ($patterns[$prefix] ?? 0) + 1;
}
arsort($patterns);

foreach (array_slice($patterns, 0, 15, true) as $pattern => $count) {
    echo "   $pattern.*: $count keys\n";
}

// Save results
$results = [
    'timestamp' => date('Y-m-d H:i:s'),
    'summary' => [
        'total_unique_keys' => count($usedKeys),
        'total_usages' => array_sum(array_map('count', $usedKeys)),
        'files_scanned' => count($files)
    ],
    'most_used_keys' => array_slice($usage, 0, 50, true),
    'key_patterns' => $patterns,
    'detailed_usage' => $usedKeys
];

if (!is_dir('storage/localization')) {
    mkdir('storage/localization', 0755, true);
}

file_put_contents(
    'storage/localization/used_keys_analysis.json',
    json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
);

echo "\n✅ Analysis completed!\n";
echo "📊 Results saved to: storage/localization/used_keys_analysis.json\n";
