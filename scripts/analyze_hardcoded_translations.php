<?php

/**
 * Hardcoded Translation Analyzer for MechaMap
 *
 * Analyzes Blade files to detect hardcoded text in translation functions
 * and suggests proper translation key structures.
 */

echo "🔍 HARDCODED TRANSLATION ANALYZER\n";
echo "=================================\n\n";

$basePath = __DIR__ . '/../';
$viewsPath = $basePath . 'resources/views';

// Common hardcoded patterns and their suggested keys
$commonPatterns = [
    // Actions
    'Xem' => 'ui.actions.view',
    'Sửa' => 'ui.actions.edit',
    'Xóa' => 'ui.actions.delete',
    'Tạo' => 'ui.actions.create',
    'Lưu' => 'ui.actions.save',
    'Hủy' => 'ui.actions.cancel',
    'Thêm' => 'ui.actions.add',
    'Cập nhật' => 'ui.actions.update',

    // Common fields
    'ID' => 'common.fields.id',
    'Tên' => 'common.fields.name',
    'Mô tả' => 'common.fields.description',
    'Ngày tạo' => 'common.fields.created_at',
    'Ngày cập nhật' => 'common.fields.updated_at',
    'Trạng thái' => 'common.fields.status',
    'Thứ tự' => 'common.fields.order',

    // Messages
    'Xác nhận xóa' => 'common.messages.confirm_delete',
    'Bạn có chắc chắn' => 'common.messages.are_you_sure',
    'Không có dữ liệu' => 'common.messages.no_data',
    'Thành công' => 'common.messages.success',
    'Lỗi' => 'common.messages.error',

    // Admin specific
    'Danh sách' => 'admin.common.list',
    'Quản lý' => 'admin.common.manage',
    'Thống kê' => 'admin.common.statistics',
    'Cấu hình' => 'admin.common.configuration',
    'Báo cáo' => 'admin.common.reports',
];

// Find all Blade files
$bladeFiles = findBladeFiles($viewsPath);
echo "📄 Found " . count($bladeFiles) . " Blade files\n\n";

$results = [
    'hardcoded_texts' => [],
    'suggested_keys' => [],
    'statistics' => [
        'total_files' => count($bladeFiles),
        'files_with_issues' => 0,
        'total_hardcoded' => 0,
        'unique_hardcoded' => 0
    ]
];

$allHardcodedTexts = [];

// Analyze each file
echo "🔬 Analyzing hardcoded translations...\n";
echo "=====================================\n";

foreach ($bladeFiles as $file) {
    $relativePath = str_replace($basePath, '', $file);
    $content = file_get_contents($file);

    $hardcodedInFile = findHardcodedTranslations($content);

    if (!empty($hardcodedInFile)) {
        $results['statistics']['files_with_issues']++;
        echo "⚠️  $relativePath - " . count($hardcodedInFile) . " hardcoded texts\n";

        foreach ($hardcodedInFile as $text) {
            $results['hardcoded_texts'][] = [
                'file' => $relativePath,
                'text' => $text,
                'suggested_key' => suggestTranslationKey($text, $commonPatterns, $relativePath)
            ];

            if (!isset($allHardcodedTexts[$text])) {
                $allHardcodedTexts[$text] = 0;
            }
            $allHardcodedTexts[$text]++;
            $results['statistics']['total_hardcoded']++;
        }
    }
}

$results['statistics']['unique_hardcoded'] = count($allHardcodedTexts);

echo "\n📊 ANALYSIS RESULTS\n";
echo "===================\n";
echo "Files with hardcoded translations: " . $results['statistics']['files_with_issues'] . "\n";
echo "Total hardcoded instances: " . $results['statistics']['total_hardcoded'] . "\n";
echo "Unique hardcoded texts: " . $results['statistics']['unique_hardcoded'] . "\n\n";

// Show most common hardcoded texts
arsort($allHardcodedTexts);
echo "🔥 Most common hardcoded texts:\n";
$count = 0;
foreach ($allHardcodedTexts as $text => $frequency) {
    if ($count >= 20) break;
    echo "   $frequency times - \"$text\"\n";
    $count++;
}

// Generate suggestions
echo "\n💡 SUGGESTED FIXES\n";
echo "==================\n";

$suggestions = generateSuggestions($allHardcodedTexts, $commonPatterns);
foreach ($suggestions as $category => $items) {
    echo "\n📂 $category:\n";
    foreach ($items as $text => $key) {
        echo "   \"$text\" → $key\n";
    }
}

// Generate fix script
generateFixScript($results, $commonPatterns);

echo "\n🎉 Analysis completed!\n";
echo "📊 Results saved to: storage/hardcoded_analysis.json\n";
echo "🔧 Fix script saved to: scripts/fix_hardcoded_translations.php\n";

// ============================================================================
// HELPER FUNCTIONS
// ============================================================================

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

function findHardcodedTranslations($content) {
    $hardcoded = [];

    // Patterns to find translation calls
    $patterns = [
        '/__\([\'"]([^\'"]+)[\'"]\)/',
        '/trans\([\'"]([^\'"]+)[\'"]\)/',
        '/@lang\([\'"]([^\'"]+)[\'"]\)/',
    ];

    foreach ($patterns as $pattern) {
        preg_match_all($pattern, $content, $matches);

        foreach ($matches[1] as $text) {
            // Check if it's hardcoded text (contains Vietnamese characters or spaces)
            if (isHardcodedText($text)) {
                $hardcoded[] = $text;
            }
        }
    }

    return array_unique($hardcoded);
}

function isHardcodedText($text) {
    // Check for Vietnamese characters
    if (preg_match('/[àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđ]/i', $text)) {
        return true;
    }

    // Check for spaces (usually indicates sentence/phrase rather than key)
    if (strpos($text, ' ') !== false) {
        return true;
    }

    // Check for common hardcoded patterns
    $hardcodedPatterns = [
        '/^[A-Z]+$/', // All caps like "ID", "URL"
        '/^[A-Z][a-z]+$/', // Title case single words like "Xem", "Sửa"
        '/\d+/', // Contains numbers
    ];

    foreach ($hardcodedPatterns as $pattern) {
        if (preg_match($pattern, $text)) {
            return true;
        }
    }

    return false;
}

function suggestTranslationKey($text, $commonPatterns, $filePath) {
    // Check common patterns first
    if (isset($commonPatterns[$text])) {
        return $commonPatterns[$text];
    }

    // Generate key based on file path and text
    $pathParts = explode('/', $filePath);

    // Determine category based on file path
    if (strpos($filePath, 'admin/') !== false) {
        $category = 'admin';
    } elseif (strpos($filePath, 'auth/') !== false) {
        $category = 'auth';
    } elseif (strpos($filePath, 'marketplace/') !== false) {
        $category = 'marketplace';
    } elseif (strpos($filePath, 'forum') !== false) {
        $category = 'forum';
    } else {
        $category = 'ui';
    }

    // Generate key based on text content
    $keyPart = generateKeyFromText($text);

    return "$category.common.$keyPart";
}

function generateKeyFromText($text) {
    // Convert Vietnamese text to English-like key
    $replacements = [
        'Xem' => 'view',
        'Sửa' => 'edit',
        'Xóa' => 'delete',
        'Tạo' => 'create',
        'Thêm' => 'add',
        'Lưu' => 'save',
        'Hủy' => 'cancel',
        'Danh sách' => 'list',
        'Quản lý' => 'manage',
        'Cấu hình' => 'settings',
        'Thống kê' => 'statistics',
        'ID' => 'id',
        'Tên' => 'name',
        'Mô tả' => 'description',
    ];

    if (isset($replacements[$text])) {
        return $replacements[$text];
    }

    // Fallback: create key from text
    $key = strtolower($text);
    $key = preg_replace('/[^a-z0-9]/', '_', $key);
    $key = preg_replace('/_+/', '_', $key);
    $key = trim($key, '_');

    return $key ?: 'unknown';
}

function generateSuggestions($allHardcodedTexts, $commonPatterns) {
    $suggestions = [
        'UI Actions' => [],
        'Common Fields' => [],
        'Messages' => [],
        'Admin Specific' => [],
        'Other' => []
    ];

    foreach ($allHardcodedTexts as $text => $frequency) {
        if ($frequency < 2) continue; // Only suggest for frequently used texts

        $key = isset($commonPatterns[$text]) ? $commonPatterns[$text] : null;

        if ($key) {
            if (strpos($key, 'ui.actions') === 0) {
                $suggestions['UI Actions'][$text] = $key;
            } elseif (strpos($key, 'common.fields') === 0) {
                $suggestions['Common Fields'][$text] = $key;
            } elseif (strpos($key, 'common.messages') === 0) {
                $suggestions['Messages'][$text] = $key;
            } elseif (strpos($key, 'admin') === 0) {
                $suggestions['Admin Specific'][$text] = $key;
            } else {
                $suggestions['Other'][$text] = $key;
            }
        } else {
            $suggestions['Other'][$text] = 'NEEDS_MANUAL_REVIEW';
        }
    }

    return $suggestions;
}

function generateFixScript($results, $commonPatterns) {
    $scriptPath = __DIR__ . '/fix_hardcoded_translations.php';

    $script = "<?php\n\n";
    $script .= "/**\n";
    $script .= " * Auto-generated Hardcoded Translation Fix Script\n";
    $script .= " * Generated: " . date('Y-m-d H:i:s') . "\n";
    $script .= " * \n";
    $script .= " * This script helps fix hardcoded translations in Blade files.\n";
    $script .= " * REVIEW CAREFULLY before executing!\n";
    $script .= " */\n\n";

    $script .= "echo \"🔧 FIXING HARDCODED TRANSLATIONS\\n\";\n";
    $script .= "echo \"================================\\n\\n\";\n\n";

    $script .= "// Common replacements\n";
    $script .= "\$replacements = [\n";
    foreach ($commonPatterns as $text => $key) {
        $script .= "    '$text' => '$key',\n";
    }
    $script .= "];\n\n";

    $script .= "// TODO: Add translation keys to language files\n";
    $script .= "// TODO: Replace hardcoded texts in Blade files\n";
    $script .= "// TODO: Test all translations work correctly\n\n";

    $script .= "echo \"⚠️  Manual review and implementation required!\\n\";\n";

    file_put_contents($scriptPath, $script);

    // Save detailed results
    $reportPath = __DIR__ . '/../storage/hardcoded_analysis.json';
    $storageDir = dirname($reportPath);
    if (!is_dir($storageDir)) {
        mkdir($storageDir, 0755, true);
    }
    file_put_contents($reportPath, json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}
