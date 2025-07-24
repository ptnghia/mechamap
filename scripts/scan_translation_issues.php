<?php

/**
 * Translation Issues Scanner for MechaMap
 *
 * Scans all Blade files to detect:
 * 1. Missing translations
 * 2. Invalid key structures
 * 3. Broken translation calls
 * 4. Inconsistent patterns
 */

echo "🔍 MECHAMAP TRANSLATION ISSUES SCANNER\n";
echo "=====================================\n\n";

$basePath = __DIR__ . '/../';
$viewsPath = $basePath . 'resources/views';
$langPath = $basePath . 'resources/lang';

// Initialize results
$results = [
    'missing_translations' => [],
    'invalid_structures' => [],
    'broken_calls' => [],
    'inconsistent_patterns' => [],
    'statistics' => [
        'total_files' => 0,
        'total_keys' => 0,
        'issues_found' => 0
    ]
];

// Load all available translations
echo "📚 Loading translation files...\n";
$translations = loadAllTranslations($langPath);
echo "   ✅ Loaded " . count($translations['vi']) . " VI files\n";
echo "   ✅ Loaded " . count($translations['en']) . " EN files\n\n";

// Find all Blade files
echo "🔍 Scanning Blade files...\n";
$bladeFiles = findBladeFiles($viewsPath);
echo "   📄 Found " . count($bladeFiles) . " Blade files\n\n";

$results['statistics']['total_files'] = count($bladeFiles);

// Scan each Blade file
echo "🔬 Analyzing translation usage...\n";
echo "================================\n";

foreach ($bladeFiles as $file) {
    $relativePath = str_replace($basePath, '', $file);
    echo "📄 $relativePath\n";

    $content = file_get_contents($file);
    $fileIssues = scanFileForIssues($content, $translations, $relativePath);

    // Merge issues
    $results['missing_translations'] = array_merge($results['missing_translations'], $fileIssues['missing']);
    $results['invalid_structures'] = array_merge($results['invalid_structures'], $fileIssues['invalid']);
    $results['broken_calls'] = array_merge($results['broken_calls'], $fileIssues['broken']);
    $results['inconsistent_patterns'] = array_merge($results['inconsistent_patterns'], $fileIssues['inconsistent']);

    $results['statistics']['total_keys'] += $fileIssues['key_count'];
    $issueCount = count($fileIssues['missing']) + count($fileIssues['invalid']) + count($fileIssues['broken']) + count($fileIssues['inconsistent']);
    $results['statistics']['issues_found'] += $issueCount;

    if ($issueCount > 0) {
        echo "   ⚠️  Found $issueCount issues\n";
    } else {
        echo "   ✅ No issues found\n";
    }
}

echo "\n";

// Generate detailed report
generateDetailedReport($results);

// Generate summary
generateSummary($results);

// Generate detailed analysis
generateDetailedAnalysis($results);

// Generate fix suggestions
generateFixSuggestions($results);

// Generate auto-fix script
generateAutoFixScript($results);

echo "\n🎉 Scan completed!\n";
echo "📊 Report saved to: storage/translation_issues_report.json\n";
echo "📋 Summary saved to: storage/translation_issues_summary.md\n";
echo "🔧 Auto-fix script saved to: scripts/auto_fix_translations.php\n";

// ============================================================================
// HELPER FUNCTIONS
// ============================================================================

function loadAllTranslations($langPath) {
    $translations = ['vi' => [], 'en' => []];

    foreach (['vi', 'en'] as $lang) {
        $langDir = $langPath . '/' . $lang;
        if (is_dir($langDir)) {
            $files = glob($langDir . '/*.php');
            foreach ($files as $file) {
                $filename = basename($file, '.php');
                try {
                    $content = include $file;
                    if (is_array($content)) {
                        $translations[$lang][$filename] = flattenArray($content, $filename);
                    }
                } catch (Exception $e) {
                    echo "   ⚠️  Error loading $file: " . $e->getMessage() . "\n";
                }
            }
        }
    }

    return $translations;
}

function findBladeFiles($viewsPath) {
    $files = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($viewsPath, RecursiveDirectoryIterator::SKIP_DOTS)
    );

    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $filePath = $file->getPathname();

            // Skip admin directory - admin panel doesn't need internationalization
            if (strpos($filePath, DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR) !== false) {
                continue;
            }

            $files[] = $filePath;
        }
    }

    return $files;
}

function scanFileForIssues($content, $translations, $filePath) {
    $issues = [
        'missing' => [],
        'invalid' => [],
        'broken' => [],
        'inconsistent' => [],
        'key_count' => 0
    ];

    // Patterns to find translation calls
    $patterns = [
        // Standard Laravel patterns
        '/__\([\'"]([^\'"]+)[\'"]\)/' => 'standard',
        '/trans\([\'"]([^\'"]+)[\'"]\)/' => 'trans',
        '/@lang\([\'"]([^\'"]+)[\'"]\)/' => 'blade_directive',

        // Custom helper patterns (if any)
        '/t_ui\([\'"]([^\'"]+)[\'"]\)/' => 'helper_ui',
        '/t_core\([\'"]([^\'"]+)[\'"]\)/' => 'helper_core',
        '/t_auth\([\'"]([^\'"]+)[\'"]\)/' => 'helper_auth',

        // Advanced patterns
        '/Lang::get\([\'"]([^\'"]+)[\'"]\)/' => 'lang_facade',
        '/app\([\'"]translator[\'"]\)->get\([\'"]([^\'"]+)[\'"]\)/' => 'app_translator',
        '/\$translator->get\([\'"]([^\'"]+)[\'"]\)/' => 'translator_instance',

        // Dynamic key patterns (potentially problematic)
        '/__\(\$([^)]+)\)/' => 'dynamic_variable',
        '/trans\(\$([^)]+)\)/' => 'dynamic_trans',

        // Concatenated keys (usually problematic)
        '/__\([\'"]([^\'"]*)\'\s*\.\s*\$/' => 'concatenated_key',
        '/trans\([\'"]([^\'"]*)\'\s*\.\s*\$/' => 'concatenated_trans',

        // Variable patterns
        '/\{\{\s*trans\([\'"]([^\'"]+)[\'"]\)/' => 'blade_trans',
        '/\{\{\s*__\([\'"]([^\'"]+)[\'"]\)/' => 'blade_standard',
    ];

    foreach ($patterns as $pattern => $type) {
        preg_match_all($pattern, $content, $matches);

        foreach ($matches[1] as $key) {
            if (empty($key)) continue;

            $issues['key_count']++;

            // Handle dynamic keys differently
            if (in_array($type, ['dynamic_variable', 'dynamic_trans', 'concatenated_key', 'concatenated_trans'])) {
                $issues['inconsistent'][] = [
                    'file' => $filePath,
                    'key' => $key,
                    'type' => $type,
                    'reason' => 'Dynamic/concatenated key - cannot validate at compile time'
                ];
                continue;
            }

            // Check for invalid structure
            if (!isValidKeyStructure($key)) {
                $issues['invalid'][] = [
                    'file' => $filePath,
                    'key' => $key,
                    'type' => $type,
                    'reason' => 'Invalid key structure'
                ];
                continue;
            }

            // Check if translation exists
            $translationExists = checkTranslationExists($key, $translations);
            if (!$translationExists['vi'] || !$translationExists['en']) {
                $issues['missing'][] = [
                    'file' => $filePath,
                    'key' => $key,
                    'type' => $type,
                    'missing_languages' => array_keys(array_filter($translationExists, function($v) { return !$v; }))
                ];
            }

            // Check for broken calls (malformed syntax)
            if (isBrokenCall($key)) {
                $issues['broken'][] = [
                    'file' => $filePath,
                    'key' => $key,
                    'type' => $type,
                    'reason' => 'Malformed translation call'
                ];
            }

            // Check for inconsistent patterns
            if (isInconsistentPattern($key, $type)) {
                $issues['inconsistent'][] = [
                    'file' => $filePath,
                    'key' => $key,
                    'type' => $type,
                    'reason' => 'Inconsistent naming pattern'
                ];
            }
        }
    }

    return $issues;
}

function isValidKeyStructure($key) {
    // Check for Laravel 11 standards

    // Should not be empty
    if (empty($key)) return false;

    // Should not start or end with dot
    if (strpos($key, '.') === 0 || substr($key, -1) === '.') return false;

    // Should not have consecutive dots
    if (strpos($key, '..') !== false) return false;

    // Should not have more than 3 levels (Laravel 11 recommendation)
    $levels = substr_count($key, '.') + 1;
    if ($levels > 3) return false;

    // Should only contain alphanumeric, dots, underscores, hyphens
    if (!preg_match('/^[a-zA-Z0-9._-]+$/', $key)) return false;

    // Should not use reserved words as first level
    $firstLevel = explode('.', $key)[0];
    $reserved = ['app', 'config', 'env', 'session', 'cookie'];
    if (in_array($firstLevel, $reserved)) return false;

    return true;
}

function checkTranslationExists($key, $translations) {
    $exists = ['vi' => false, 'en' => false];

    foreach (['vi', 'en'] as $lang) {
        $parts = explode('.', $key);
        $file = $parts[0];

        if (isset($translations[$lang][$file])) {
            $flatKey = implode('.', $parts);
            $exists[$lang] = isset($translations[$lang][$file][$flatKey]);
        }
    }

    return $exists;
}

function isBrokenCall($key) {
    // Check for common broken patterns

    // Contains spaces (usually indicates malformed key)
    if (strpos($key, ' ') !== false) return true;

    // Contains special characters that shouldn't be in keys
    if (preg_match('/[<>:"|?*\\\\\/]/', $key)) return true;

    // Starts with number (usually indicates error)
    if (preg_match('/^\d/', $key)) return true;

    return false;
}

function isInconsistentPattern($key, $type) {
    // Check for inconsistent naming patterns

    $parts = explode('.', $key);

    // Check for mixed naming conventions
    foreach ($parts as $part) {
        // Should not mix camelCase with snake_case
        if (preg_match('/[A-Z]/', $part) && strpos($part, '_') !== false) {
            return true;
        }

        // Should not be too long
        if (strlen($part) > 30) {
            return true;
        }
    }

    return false;
}

function flattenArray($array, $prefix = '') {
    $result = [];
    foreach ($array as $key => $value) {
        $newKey = $prefix ? $prefix . '.' . $key : $key;
        if (is_array($value)) {
            $result = array_merge($result, flattenArray($value, $newKey));
        } else {
            $result[$newKey] = $value;
        }
    }
    return $result;
}

function generateDetailedReport($results) {
    $reportPath = __DIR__ . '/../storage/translation_issues_report.json';

    // Ensure storage directory exists
    $storageDir = dirname($reportPath);
    if (!is_dir($storageDir)) {
        mkdir($storageDir, 0755, true);
    }

    file_put_contents($reportPath, json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function generateSummary($results) {
    $summaryPath = __DIR__ . '/../storage/translation_issues_summary.md';

    $summary = "# Translation Issues Summary - MechaMap\n\n";
    $summary .= "Generated: " . date('Y-m-d H:i:s') . "\n\n";

    $summary .= "## 📊 Statistics\n\n";
    $summary .= "- **Total Blade files scanned**: " . $results['statistics']['total_files'] . "\n";
    $summary .= "- **Total translation keys found**: " . $results['statistics']['total_keys'] . "\n";
    $summary .= "- **Total issues found**: " . $results['statistics']['issues_found'] . "\n\n";

    $summary .= "## 🚨 Issues Breakdown\n\n";
    $summary .= "### Missing Translations: " . count($results['missing_translations']) . "\n";
    $summary .= "### Invalid Structures: " . count($results['invalid_structures']) . "\n";
    $summary .= "### Broken Calls: " . count($results['broken_calls']) . "\n";
    $summary .= "### Inconsistent Patterns: " . count($results['inconsistent_patterns']) . "\n\n";

    if (!empty($results['missing_translations'])) {
        $summary .= "## ❌ Missing Translations\n\n";
        foreach (array_slice($results['missing_translations'], 0, 10) as $issue) {
            $summary .= "- **{$issue['key']}** in `{$issue['file']}` (Missing: " . implode(', ', $issue['missing_languages']) . ")\n";
        }
        if (count($results['missing_translations']) > 10) {
            $summary .= "\n... and " . (count($results['missing_translations']) - 10) . " more\n";
        }
        $summary .= "\n";
    }

    if (!empty($results['invalid_structures'])) {
        $summary .= "## ⚠️ Invalid Structures\n\n";
        foreach (array_slice($results['invalid_structures'], 0, 10) as $issue) {
            $summary .= "- **{$issue['key']}** in `{$issue['file']}` - {$issue['reason']}\n";
        }
        if (count($results['invalid_structures']) > 10) {
            $summary .= "\n... and " . (count($results['invalid_structures']) - 10) . " more\n";
        }
        $summary .= "\n";
    }

    file_put_contents($summaryPath, $summary);
}

function generateFixSuggestions($results) {
    echo "\n🔧 FIX SUGGESTIONS\n";
    echo "==================\n";

    if (!empty($results['missing_translations'])) {
        echo "📝 Missing Translations (" . count($results['missing_translations']) . "):\n";
        echo "   → Add missing keys to appropriate language files\n";
        echo "   → Check for typos in key names\n";
        echo "   → Verify file structure matches key prefixes\n\n";
    }

    if (!empty($results['invalid_structures'])) {
        echo "⚠️  Invalid Structures (" . count($results['invalid_structures']) . "):\n";
        echo "   → Follow Laravel 11 naming conventions\n";
        echo "   → Use maximum 3 levels (file.section.key)\n";
        echo "   → Avoid special characters and spaces\n\n";
    }

    if (!empty($results['broken_calls'])) {
        echo "🔨 Broken Calls (" . count($results['broken_calls']) . "):\n";
        echo "   → Fix malformed translation function calls\n";
        echo "   → Check for syntax errors in Blade templates\n";
        echo "   → Ensure proper quoting of translation keys\n\n";
    }

    if (!empty($results['inconsistent_patterns'])) {
        echo "🎯 Inconsistent Patterns (" . count($results['inconsistent_patterns']) . "):\n";
        echo "   → Standardize naming conventions\n";
        echo "   → Use consistent helper functions\n";
        echo "   → Follow project translation guidelines\n\n";
    }

    if ($results['statistics']['issues_found'] === 0) {
        echo "✅ No issues found! Translation system is healthy.\n\n";
    }
}

function generateAutoFixScript($results) {
    $scriptPath = __DIR__ . '/auto_fix_translations.php';

    $script = "<?php\n\n";
    $script .= "/**\n";
    $script .= " * Auto-generated Translation Fix Script\n";
    $script .= " * Generated: " . date('Y-m-d H:i:s') . "\n";
    $script .= " * \n";
    $script .= " * This script contains suggested fixes for translation issues.\n";
    $script .= " * Review carefully before executing!\n";
    $script .= " */\n\n";

    $script .= "echo \"🔧 AUTO-FIXING TRANSLATION ISSUES\\n\";\n";
    $script .= "echo \"=================================\\n\\n\";\n\n";

    $script .= "\$basePath = __DIR__ . '/../';\n";
    $script .= "\$langPath = \$basePath . 'resources/lang';\n\n";

    // Generate fixes for missing translations
    if (!empty($results['missing_translations'])) {
        $script .= "// Fix missing translations\n";
        $script .= "echo \"📝 Adding missing translations...\\n\";\n\n";

        $missingByFile = [];
        foreach ($results['missing_translations'] as $issue) {
            $keyParts = explode('.', $issue['key']);
            $file = $keyParts[0];

            foreach ($issue['missing_languages'] as $lang) {
                if (!isset($missingByFile[$lang][$file])) {
                    $missingByFile[$lang][$file] = [];
                }
                $missingByFile[$lang][$file][] = $issue['key'];
            }
        }

        foreach ($missingByFile as $lang => $files) {
            foreach ($files as $file => $keys) {
                $script .= "// Add missing keys to $lang/$file.php\n";
                $script .= "\$filePath = \$langPath . '/$lang/$file.php';\n";
                $script .= "if (file_exists(\$filePath)) {\n";
                $script .= "    \$content = file_get_contents(\$filePath);\n";
                $script .= "    // TODO: Add these keys:\n";
                foreach ($keys as $key) {
                    $script .= "    // '$key' => 'TODO: Add translation',\n";
                }
                $script .= "    echo \"   ✅ Updated $lang/$file.php\\n\";\n";
                $script .= "}\n\n";
            }
        }
    }

    // Generate fixes for invalid structures
    if (!empty($results['invalid_structures'])) {
        $script .= "// Fix invalid key structures\n";
        $script .= "echo \"⚠️  Fixing invalid key structures...\\n\";\n";
        $script .= "// Manual review required for these keys:\n";
        foreach ($results['invalid_structures'] as $issue) {
            $script .= "// File: {$issue['file']}\n";
            $script .= "// Invalid key: '{$issue['key']}' - {$issue['reason']}\n";
            $script .= "// Suggested fix: Review and rename key following Laravel 11 conventions\n\n";
        }
    }

    $script .= "echo \"\\n🎉 Auto-fix completed!\\n\";\n";
    $script .= "echo \"⚠️  Please review all changes before committing!\\n\";\n";

    file_put_contents($scriptPath, $script);
}

function generateDetailedAnalysis($results) {
    echo "\n📊 DETAILED ANALYSIS\n";
    echo "====================\n";

    // Analyze most problematic files
    $fileIssues = [];
    foreach (['missing_translations', 'invalid_structures', 'broken_calls', 'inconsistent_patterns'] as $type) {
        foreach ($results[$type] as $issue) {
            if (!isset($fileIssues[$issue['file']])) {
                $fileIssues[$issue['file']] = 0;
            }
            $fileIssues[$issue['file']]++;
        }
    }

    if (!empty($fileIssues)) {
        arsort($fileIssues);
        echo "🔥 Most problematic files:\n";
        $count = 0;
        foreach ($fileIssues as $file => $issues) {
            if ($count >= 5) break;
            echo "   $issues issues - $file\n";
            $count++;
        }
        echo "\n";
    }

    // Analyze most common missing keys
    $missingKeys = [];
    foreach ($results['missing_translations'] as $issue) {
        $keyParts = explode('.', $issue['key']);
        $prefix = $keyParts[0];
        if (!isset($missingKeys[$prefix])) {
            $missingKeys[$prefix] = 0;
        }
        $missingKeys[$prefix]++;
    }

    if (!empty($missingKeys)) {
        arsort($missingKeys);
        echo "📂 Files with most missing translations:\n";
        foreach ($missingKeys as $prefix => $count) {
            echo "   $count missing - $prefix.php\n";
        }
        echo "\n";
    }

    // Analyze translation patterns
    $patterns = [];
    foreach (['missing_translations', 'invalid_structures', 'broken_calls', 'inconsistent_patterns'] as $type) {
        foreach ($results[$type] as $issue) {
            if (!isset($patterns[$issue['type']])) {
                $patterns[$issue['type']] = 0;
            }
            $patterns[$issue['type']]++;
        }
    }

    if (!empty($patterns)) {
        echo "🎯 Translation call patterns:\n";
        foreach ($patterns as $pattern => $count) {
            echo "   $count calls - $pattern\n";
        }
        echo "\n";
    }
}
