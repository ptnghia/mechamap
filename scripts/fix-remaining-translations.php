<?php

/**
 * FINAL Translation Keys Quick Fix
 * Replace all remaining ui/common.* and ui.common.* keys
 */

echo "🚀 MechaMap Translation Quick Fix - FINAL ROUND\n";
echo "===============================================\n\n";

// Get all blade files recursively
function getAllBladeFiles($dir) {
    $files = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($iterator as $file) {
        if ($file->getExtension() === 'php' && strpos($file->getFilename(), '.blade.') !== false) {
            $files[] = $file->getPathname();
        }
    }
    return $files;
}

$bladeFiles = getAllBladeFiles('resources/views');
$totalChanges = 0;
$filesChanged = 0;

echo "📁 Found " . count($bladeFiles) . " blade files\n\n";

foreach ($bladeFiles as $file) {
    echo "🔍 Processing: " . str_replace('\\', '/', $file) . "\n";

    $content = file_get_contents($file);
    $originalContent = $content;
    $fileChanges = 0;

    // Pattern 1: ui/common.* (slash pattern) - Replace with common.*
    $patterns = [
        // ui/common.technical_* -> common.technical.*
        '/__\([\'"]ui\/common\.technical_([^\'"\)]+)[\'"]\)/' => '__("common.technical.$1")',

        // ui/common.knowledge -> common.knowledge
        '/__\([\'"]ui\/common\.knowledge[\'"]\)/' => '__("common.knowledge")',

        // ui/common.members.* -> common.members.*
        '/__\([\'"]ui\/common\.members\.([^\'"\)]+)[\'"]\)/' => '__("common.members.$1")',

        // ui.common.* (dot pattern) -> common.*
        '/__\([\'"]ui\.common\.([^\'"\)]+)[\'"]\)/' => '__("common.$1")',

        // core/messages.* -> common.messages.*
        '/__\([\'"]core\/messages\.([^\'"\)]+)[\'"]\)/' => '__("common.messages.$1")',
    ];

    foreach ($patterns as $pattern => $replacement) {
        $newContent = preg_replace($pattern, $replacement, $content);
        if ($newContent !== $content) {
            $matches = preg_match_all($pattern, $content);
            $fileChanges += $matches;
            $content = $newContent;
            echo "  ✅ Applied pattern: " . substr($pattern, 0, 50) . "... ({$matches} matches)\n";
        }
    }

    // Write back if changed
    if ($content !== $originalContent && $fileChanges > 0) {
        file_put_contents($file, $content);
        echo "  💾 Saved {$fileChanges} changes\n";
        $totalChanges += $fileChanges;
        $filesChanged++;
    } else {
        echo "  ➖ No changes needed\n";
    }

    echo "\n";
}

echo "📊 SUMMARY:\n";
echo "===========\n";
echo "Files processed: " . count($bladeFiles) . "\n";
echo "Files changed: {$filesChanged}\n";
echo "Total replacements: {$totalChanges}\n\n";

// Now add missing keys to common.php
echo "📝 Adding missing translation keys...\n";
echo "====================================\n";

$missingKeys = [
    'technical' => [
        'resources' => 'Tài nguyên kỹ thuật',
        'database' => 'Cơ sở dữ liệu kỹ thuật',
        'materials_database' => 'Cơ sở dữ liệu vật liệu',
        'engineering_standards' => 'Tiêu chuẩn kỹ thuật',
        'manufacturing_processes' => 'Quy trình sản xuất',
        'design_resources' => 'Tài nguyên thiết kế',
        'cad_library' => 'Thư viện CAD',
        'drawings' => 'Bản vẽ kỹ thuật',
        'tools_calculators' => 'Công cụ & máy tính',
        'material_cost_calculator' => 'Máy tính chi phí vật liệu',
        'process_selector' => 'Bộ chọn quy trình',
        'standards_compliance' => 'Tuân thủ tiêu chuẩn',
    ],
    'knowledge' => 'Kiến thức',
    'members' => [
        'staff_title' => 'Đội ngũ quản lý',
        'staff_description' => 'Gặp gỡ đội ngũ điều hành MechaMap',
        'all_members' => 'Tất cả thành viên',
        'online_now' => 'Đang trực tuyến',
        'staff' => 'Đội ngũ',
        'administrators' => 'Quản trị viên',
        'administrator' => 'Quản trị viên',
        'moderators' => 'Kiểm duyệt viên',
        'moderator' => 'Kiểm duyệt viên',
        'online' => 'Trực tuyến',
        'no_bio_available' => 'Chưa có thông tin tiểu sử',
        'posts' => 'Bài viết',
        'threads' => 'Chủ đề',
        'joined' => 'Tham gia',
        'last_seen' => 'Truy cập cuối',
        'admin' => 'Quản trị',
        'online_title' => 'Thành viên trực tuyến',
        'online_description' => 'Danh sách thành viên đang hoạt động',
        'online_members_info' => 'Thông tin thành viên trực tuyến',
        'no_administrators_found' => 'Không tìm thấy quản trị viên',
        'no_moderators_found' => 'Không tìm thấy kiểm duyệt viên',
        'no_members_online' => 'Không có thành viên nào trực tuyến',
    ],
    'messages' => [
        'switched_successfully' => 'Đã chuyển đổi thành công',
        'switch_failed' => 'Chuyển đổi thất bại',
        'auto_detect_failed' => 'Không thể tự động phát hiện',
    ],
];

$langFiles = [
    'resources/lang/vi/common.php' => 'vi',
    'resources/lang/en/common.php' => 'en',
];

foreach ($langFiles as $file => $lang) {
    echo "📝 Processing: {$file}\n";

    if (!file_exists($file)) {
        echo "  ❌ File not found\n";
        continue;
    }

    // Backup
    $backupFile = $file . '.backup.' . date('Y-m-d-H-i-s');
    copy($file, $backupFile);

    $content = file_get_contents($file);

    // Add missing sections before the closing );
    $newSections = [];

    foreach ($missingKeys as $section => $keys) {
        if (is_array($keys)) {
            $newSections[] = "  '{$section}' =>\n  array (";
            foreach ($keys as $key => $value) {
                // For English, translate some basic terms
                if ($lang === 'en') {
                    $value = translateToEnglish($value);
                }
                $newSections[] = "    '{$key}' => '{$value}',";
            }
            $newSections[] = "  ),";
        } else {
            $value = $keys;
            if ($lang === 'en') {
                $value = translateToEnglish($value);
            }
            $newSections[] = "  '{$section}' => '{$value}',";
        }
    }

    // Insert before closing );
    $newContent = str_replace(
        "\n);",
        "\n  " . implode("\n  ", $newSections) . "\n);",
        $content
    );

    if ($newContent !== $content) {
        file_put_contents($file, $newContent);
        echo "  ✅ Added " . count($missingKeys) . " new sections\n";
        echo "  💾 Backup: {$backupFile}\n";
    } else {
        echo "  ➖ No changes needed\n";
        unlink($backupFile);
    }
}

function translateToEnglish($vietnamese) {
    $translations = [
        'Tài nguyên kỹ thuật' => 'Technical Resources',
        'Cơ sở dữ liệu kỹ thuật' => 'Technical Database',
        'Cơ sở dữ liệu vật liệu' => 'Materials Database',
        'Tiêu chuẩn kỹ thuật' => 'Engineering Standards',
        'Quy trình sản xuất' => 'Manufacturing Processes',
        'Tài nguyên thiết kế' => 'Design Resources',
        'Thư viện CAD' => 'CAD Library',
        'Bản vẽ kỹ thuật' => 'Technical Drawings',
        'Công cụ & máy tính' => 'Tools & Calculators',
        'Máy tính chi phí vật liệu' => 'Material Cost Calculator',
        'Bộ chọn quy trình' => 'Process Selector',
        'Tuân thủ tiêu chuẩn' => 'Standards Compliance',
        'Kiến thức' => 'Knowledge',
        'Đội ngũ quản lý' => 'Management Team',
        'Gặp gỡ đội ngũ điều hành MechaMap' => 'Meet the MechaMap management team',
        'Tất cả thành viên' => 'All Members',
        'Đang trực tuyến' => 'Online Now',
        'Đội ngũ' => 'Staff',
        'Quản trị viên' => 'Administrator',
        'Kiểm duyệt viên' => 'Moderator',
        'Trực tuyến' => 'Online',
        'Chưa có thông tin tiểu sử' => 'No bio available',
        'Bài viết' => 'Posts',
        'Chủ đề' => 'Threads',
        'Tham gia' => 'Joined',
        'Truy cập cuối' => 'Last Seen',
        'Quản trị' => 'Admin',
        'Thành viên trực tuyến' => 'Online Members',
        'Danh sách thành viên đang hoạt động' => 'List of active members',
        'Thông tin thành viên trực tuyến' => 'Online member information',
        'Không tìm thấy quản trị viên' => 'No administrators found',
        'Không tìm thấy kiểm duyệt viên' => 'No moderators found',
        'Không có thành viên nào trực tuyến' => 'No members online',
        'Đã chuyển đổi thành công' => 'Successfully switched',
        'Chuyển đổi thất bại' => 'Switch failed',
        'Không thể tự động phát hiện' => 'Auto detect failed',
    ];

    return $translations[$vietnamese] ?? $vietnamese;
}

echo "\n🎯 FINAL STEPS:\n";
echo "===============\n";
echo "1. Clear Laravel cache: php artisan cache:clear\n";
echo "2. Clear view cache: php artisan view:clear\n";
echo "3. Test website functionality\n";
echo "4. Check error logs: php check-error-logs.php\n\n";

echo "✅ Translation Quick Fix COMPLETED!\n";
echo "Expected result: Significant reduction in htmlspecialchars() errors\n\n";

?>
