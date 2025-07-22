<?php

echo "=== Kiểm tra translation keys trong components (Simple Mode) ===\n\n";

// Load translation files automatically
$translationFiles = [];
$langDir = 'resources/lang/vi/';
if (is_dir($langDir)) {
    $langFiles = scandir($langDir);
    foreach ($langFiles as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) === 'php' && $file !== '.' && $file !== '..') {
            $name = pathinfo($file, PATHINFO_FILENAME);
            $filePath = $langDir . $file;
            if (file_exists($filePath)) {
                $translationFiles[$name] = $filePath;
            }
        }
    }
}

$translations = [];
foreach ($translationFiles as $name => $file) {
    if (file_exists($file)) {
        $translations[$name] = include $file;
        echo "✅ Loaded: $name.php\n";
    } else {
        echo "❌ Missing: $file\n";
    }
}

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

echo "\n📁 Tìm thấy " . count($componentFiles) . " component files\n\n";

$allKeys = [];
$untranslatedKeys = [];
$missingFiles = [];

foreach ($componentFiles as $file) {
    $relativePath = str_replace(realpath('.') . DIRECTORY_SEPARATOR, '', $file);
    $relativePath = str_replace('\\', '/', $relativePath);

    $content = file_get_contents($file);
    $fileKeys = 0;

    // Find __() calls
    preg_match_all('/__\([\'"]([^\'"]*)[\'"](?:\s*,\s*[^)]+)?\)/', $content, $matches);
    foreach ($matches[1] as $key) {
        $allKeys[] = ['type' => '__', 'key' => $key, 'file' => $relativePath];
        $fileKeys++;

        // Check if key exists in translations
        $parts = explode('.', $key);
        if (count($parts) >= 2) {
            $langFile = $parts[0];
            if (!isset($translations[$langFile])) {
                $missingFiles[$langFile] = true;
                $untranslatedKeys[] = ['type' => '__', 'key' => $key, 'file' => $relativePath, 'reason' => 'Missing lang file'];
            } else {
                $current = $translations[$langFile];
                $keyExists = true;

                for ($i = 1; $i < count($parts); $i++) {
                    if (!isset($current[$parts[$i]])) {
                        $keyExists = false;
                        break;
                    }
                    $current = $current[$parts[$i]];
                }

                if (!$keyExists) {
                    $untranslatedKeys[] = ['type' => '__', 'key' => $key, 'file' => $relativePath, 'reason' => 'Key not found'];
                } elseif (is_array($current)) {
                    $untranslatedKeys[] = ['type' => '__', 'key' => $key, 'file' => $relativePath, 'reason' => 'Returns array'];
                }
            }
        }
    }

    // Find t_xxx() calls (but exclude asset_xxx functions)
    preg_match_all('/\bt_(\w+)\([\'"]([^\'"]*)[\'"](?:\s*,\s*[^)]+)?\)/', $content, $matches, PREG_SET_ORDER);
    foreach ($matches as $match) {
        $function = 't_' . $match[1];
        $key = $match[2];
        $langFile = $match[1]; // auth, common, navigation, etc.

        $allKeys[] = ['type' => $function, 'key' => $key, 'file' => $relativePath];
        $fileKeys++;

        // Check if helper file exists and key exists
        if (!isset($translations[$langFile])) {
            $missingFiles[$langFile] = true;
            $untranslatedKeys[] = ['type' => $function, 'key' => $key, 'file' => $relativePath, 'reason' => 'Missing lang file'];
        } else {
            $parts = explode('.', $key);
            $current = $translations[$langFile];
            $keyExists = true;

            for ($i = 0; $i < count($parts); $i++) {
                if (!isset($current[$parts[$i]])) {
                    $keyExists = false;
                    break;
                }
                $current = $current[$parts[$i]];
            }

            if (!$keyExists) {
                $untranslatedKeys[] = ['type' => $function, 'key' => $key, 'file' => $relativePath, 'reason' => 'Key not found'];
            } elseif (is_array($current)) {
                $untranslatedKeys[] = ['type' => $function, 'key' => $key, 'file' => $relativePath, 'reason' => 'Returns array'];
            }
        }
    }

    if ($fileKeys > 0) {
        echo "🔍 " . basename($relativePath) . ": $fileKeys keys\n";
    }
}

echo "\n=== SUMMARY REPORT ===\n";
echo "Total translation calls: " . count($allKeys) . "\n";
echo "Problematic calls: " . count($untranslatedKeys) . "\n";
echo "Success rate: " . round((count($allKeys) - count($untranslatedKeys)) / count($allKeys) * 100, 1) . "%\n\n";

// Missing language files
if (count($missingFiles) > 0) {
    echo "=== MISSING LANGUAGE FILES ===\n";
    foreach (array_keys($missingFiles) as $file) {
        echo "❌ Missing: resources/lang/vi/$file.php\n";
    }
    echo "\n";
}

// Group untranslated keys by file
if (count($untranslatedKeys) > 0) {
    echo "=== UNTRANSLATED KEYS BY COMPONENT ===\n";
    $keysByFile = [];
    foreach ($untranslatedKeys as $keyInfo) {
        $file = basename($keyInfo['file']);
        if (!isset($keysByFile[$file])) {
            $keysByFile[$file] = [];
        }
        $keysByFile[$file][] = $keyInfo;
    }

    foreach ($keysByFile as $file => $keys) {
        echo "\n📄 $file (" . count($keys) . " issues):\n";
        foreach ($keys as $keyInfo) {
            echo "  ❌ {$keyInfo['type']}('{$keyInfo['key']}') - {$keyInfo['reason']}\n";
        }
    }
}

// Group by language file
echo "\n=== MISSING KEYS BY LANGUAGE FILE ===\n";
$keysByLangFile = [];
foreach ($untranslatedKeys as $keyInfo) {
    if ($keyInfo['reason'] === 'Key not found') {
        $key = $keyInfo['key'];
        if ($keyInfo['type'] === '__') {
            $parts = explode('.', $key);
            $langFile = $parts[0];
        } else {
            $langFile = str_replace('t_', '', $keyInfo['type']);
        }

        if (!isset($keysByLangFile[$langFile])) {
            $keysByLangFile[$langFile] = [];
        }
        $keysByLangFile[$langFile][] = $key;
    }
}

foreach ($keysByLangFile as $langFile => $keys) {
    echo "\n📋 $langFile.php (" . count($keys) . " missing keys):\n";
    $uniqueKeys = array_unique($keys);
    foreach ($uniqueKeys as $key) {
        echo "  '$key' => '$key', // TODO: Add translation\n";
    }
}

echo "\n✅ Analysis completed at " . date('Y-m-d H:i:s') . "\n";
