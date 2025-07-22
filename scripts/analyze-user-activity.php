<?php

echo "\n🔍 DETAILED ANALYSIS: user/activity.blade.php\n";
echo "==========================================================\n\n";

// Đọc file user/activity.blade.php
$file_path = 'resources/views/user/activity.blade.php';
$content = file_get_contents($file_path);

if (!$content) {
    echo "❌ Error: Cannot read $file_path\n";
    exit;
}

$lines = explode("\n", $content);
$translation_calls = [];
$successful_count = 0;
$problematic_count = 0;

// Tải tất cả translation files
$translation_files = [
    'vi' => [],
    'en' => []
];

// Load Vietnamese translations
foreach (glob('resources/lang/vi/*.php') as $file) {
    $key = basename($file, '.php');
    $translation_files['vi'][$key] = include $file;
}

function resolveTranslationKey($key, $translation_files) {
    // Nếu key có dấu chấm, tách namespace
    if (strpos($key, '.') !== false) {
        $parts = explode('.', $key, 2);
        $namespace = $parts[0];
        $remaining_key = $parts[1];

        if (isset($translation_files['vi'][$namespace])) {
            return resolveNestedKey($translation_files['vi'][$namespace], $remaining_key);
        }
    }

    // Key đơn, tìm trong tất cả các file
    foreach ($translation_files['vi'] as $namespace => $translations) {
        if (isset($translations[$key])) {
            return $translations[$key];
        }
    }

    return null;
}

function resolveNestedKey($array, $key) {
    if (strpos($key, '.') === false) {
        return $array[$key] ?? null;
    }

    $parts = explode('.', $key, 2);
    $first = $parts[0];
    $remaining = $parts[1];

    if (isset($array[$first]) && is_array($array[$first])) {
        return resolveNestedKey($array[$first], $remaining);
    }

    return $array[$key] ?? null;
}

// Tìm tất cả translation calls
foreach ($lines as $line_num => $line) {
    if (preg_match_all('/__\([\'"]([^\'"]+)[\'"]\)/', $line, $matches)) {
        foreach ($matches[1] as $key) {
            $line_number = $line_num + 1;
            $translation_calls[] = [
                'line' => $line_number,
                'key' => $key,
                'full_line' => trim($line)
            ];

            $translated_value = resolveTranslationKey($key, $translation_files);

            if ($translated_value !== null) {
                echo "✅ Line $line_number: __('$key') = '$translated_value'\n";
                $successful_count++;
            } else {
                echo "❌ Line $line_number: __('$key') - MISSING TRANSLATION\n";
                echo "   Full line: " . trim($line) . "\n";
                $problematic_count++;
            }
        }
    }
}

echo "\n📊 SUMMARY:\n";
echo "===========\n";
echo "Total translation calls: " . count($translation_calls) . "\n";
echo "Successful calls: $successful_count\n";
echo "Problematic calls: $problematic_count\n";
echo "Success rate: " . (count($translation_calls) > 0 ? round(($successful_count / count($translation_calls)) * 100, 1) : 0) . "%\n";

?>
