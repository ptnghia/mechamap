<?php

echo "\n📊 NON-ADMIN TRANSLATION PROGRESS ANALYSIS\n";
echo "==========================================\n\n";

$total_files = 0;
$total_calls = 0;
$successful_calls = 0;
$problematic_calls = 0;

// Translation files
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
    // Try namespace resolution first
    if (strpos($key, '.') !== false) {
        $parts = explode('.', $key, 2);
        $namespace = $parts[0];
        $remaining_key = $parts[1];

        if (isset($translation_files['vi'][$namespace])) {
            return resolveNestedKey($translation_files['vi'][$namespace], $remaining_key);
        }
    }

    // Try all translation files
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

// Find all blade files (excluding admin)
$blade_files = glob('resources/views/**/*.blade.php', GLOB_BRACE);
$non_admin_files = array_filter($blade_files, function($file) {
    return strpos($file, '/admin/') === false;
});

echo "📁 SCANNING " . count($non_admin_files) . " NON-ADMIN FILES...\n\n";

foreach ($non_admin_files as $file) {
    $content = file_get_contents($file);
    $lines = explode("\n", $content);

    $file_calls = 0;
    $file_successful = 0;

    foreach ($lines as $line_num => $line) {
        if (preg_match_all('/__\([\'"]([^\'"]+)[\'"]\)/', $line, $matches)) {
            foreach ($matches[1] as $key) {
                $file_calls++;
                $total_calls++;

                $translated_value = resolveTranslationKey($key, $translation_files);

                if ($translated_value !== null) {
                    $file_successful++;
                    $successful_calls++;
                } else {
                    $problematic_calls++;
                }
            }
        }
    }

    if ($file_calls > 0) {
        $total_files++;
        $success_rate = round(($file_successful / $file_calls) * 100, 1);

        if ($success_rate < 100) {
            $relative_path = str_replace('resources/views/', '', $file);
            echo "⚠️  $relative_path: $success_rate% ($file_successful/$file_calls)\n";
        }
    }
}

echo "\n🎯 FINAL SUMMARY (NON-ADMIN ONLY):\n";
echo "===================================\n";
echo "Files analyzed: $total_files\n";
echo "Total translation calls: $total_calls\n";
echo "Successful calls: $successful_calls\n";
echo "Problematic calls: $problematic_calls\n";

if ($total_calls > 0) {
    $overall_rate = round(($successful_calls / $total_calls) * 100, 1);
    echo "Overall success rate: $overall_rate%\n";

    if ($overall_rate >= 95) {
        echo "\n🎉 EXCELLENT! Almost perfect translation coverage!\n";
    } else if ($overall_rate >= 85) {
        echo "\n👍 GOOD PROGRESS! Getting close to complete coverage!\n";
    } else {
        echo "\n📈 Keep working on translation improvements!\n";
    }
} else {
    echo "No translation calls found.\n";
}

?>
