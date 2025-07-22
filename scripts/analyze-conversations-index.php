<?php

// Analyze conversations/index.blade.php for translation issues
$file = 'D:/xampp/htdocs/laravel/mechamap_backend/resources/views/conversations/index.blade.php';

echo "📍 DETAILED ANALYSIS: conversations/index.blade.php\n";
echo "==========================================================\n\n";

// Load all translation files
$allTranslations = [];
$langPath = 'D:/xampp/htdocs/laravel/mechamap_backend/resources/lang/vi';
$files = glob($langPath . '/*.php');

foreach ($files as $langFile) {
    $namespace = basename($langFile, '.php');
    $translations = include $langFile;
    if (is_array($translations)) {
        $allTranslations[$namespace] = $translations;
    }
}

// Function to get nested value
function getNestedValue($array, $path) {
    $keys = explode('.', $path);
    $current = $array;

    foreach ($keys as $key) {
        if (!is_array($current) || !array_key_exists($key, $current)) {
            return null;
        }
        $current = $current[$key];
    }

    return $current;
}

// Function to resolve translation
function resolveTranslation($key, $allTranslations) {
    if (strpos($key, '.') === false) {
        return isset($allTranslations[$key]) ? $allTranslations[$key] : null;
    }

    $parts = explode('.', $key, 2);
    $namespace = $parts[0];
    $subKey = $parts[1];

    if (!isset($allTranslations[$namespace])) {
        return null;
    }

    // Direct key access first
    if (isset($allTranslations[$namespace][$subKey])) {
        return $allTranslations[$namespace][$subKey];
    }

    // Then try nested access
    return getNestedValue($allTranslations[$namespace], $subKey);
}

// Read and analyze the file
if (!file_exists($file)) {
    echo "❌ File not found: $file\n";
    exit(1);
}

$content = file_get_contents($file);
$lines = explode("\n", $content);

$totalCalls = 0;
$successfulCalls = 0;
$problematicCalls = 0;
$issues = [];

foreach ($lines as $lineNumber => $line) {
    // Find all __() calls
    if (preg_match_all("/__\(\s*['\"]([^'\"]+)['\"](?:\s*,\s*[^)]+)?\s*\)/", $line, $matches, PREG_OFFSET_CAPTURE)) {
        foreach ($matches[1] as $match) {
            $key = $match[0];
            $totalCalls++;

            $translation = resolveTranslation($key, $allTranslations);

            if ($translation !== null) {
                echo "✅ Line " . ($lineNumber + 1) . ": __('$key') = '$translation'\n";
                $successfulCalls++;
            } else {
                echo "❌ Line " . ($lineNumber + 1) . ": __('$key') = NOT FOUND\n";
                $problematicCalls++;
                $issues[] = [
                    'line' => $lineNumber + 1,
                    'key' => $key,
                    'context' => trim($line)
                ];
            }
        }
    }
}

echo "\n📊 SUMMARY:\n";
echo "===========\n";
echo "Total translation calls: $totalCalls\n";
echo "Successful calls: $successfulCalls\n";
echo "Problematic calls: $problematicCalls\n";
echo "Success rate: " . round(($successfulCalls / $totalCalls) * 100, 1) . "%\n";

if (!empty($issues)) {
    echo "\n🔍 MISSING KEYS:\n";
    echo "================\n";
    foreach ($issues as $issue) {
        echo "Line {$issue['line']}: {$issue['key']}\n";
        echo "Context: {$issue['context']}\n\n";
    }
}
