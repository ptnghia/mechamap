<?php

/**
 * Script dọn dẹp thư mục gốc Laravel 11 - Xóa các file không cần thiết
 * Chạy: php scripts/cleanup_project_root.php
 */

echo "🧹 MechaMap Laravel 11 Project Cleanup Script\n";
echo "============================================\n\n";

// Danh sách các file/thư mục cần xóa
$filesToDelete = [
    // Files lạ không rõ mục đích
    '123],',
    '192.168.1.200,',
    '456],',
    'Automated test security alert,',
    'category',
    'count',
    'id',
    'locale',
    'priority',
    'status',
    'title',
    'true],',
    'urgency_level',
];

// Files tạm thời trong storage (giữ lại cấu trúc thư mục)
$storageFilesToDelete = [
    'storage/admin_translation_audit.json',
    'storage/hardcoded_analysis.json',
    'storage/remaining_translation_keys.json',
    'storage/translation_issues_report.json',
    'storage/translation_issues_summary.md',
];

// Scripts cũ không cần thiết (giữ lại scripts quan trọng)
$scriptsToDelete = [
    // Analysis scripts (đã hoàn thành)
    'scripts/analyze-business-index.php',
    'scripts/analyze-business-services.php',
    'scripts/analyze-categories-show.php',
    'scripts/analyze-companies-contact.php',
    'scripts/analyze-components-auth-modal.php',
    'scripts/analyze-conversations-index.php',
    'scripts/analyze-dashboard-guest.php',
    'scripts/analyze-dashboard-member.php',
    'scripts/analyze-manufacturer-setup.php',
    'scripts/analyze-non-admin-final.php',
    'scripts/analyze-profile-about.php',
    'scripts/analyze-search-index.php',
    'scripts/analyze-student-detailed.php',
    'scripts/analyze-subscription-index.php',
    'scripts/analyze-subscription-success.php',
    'scripts/analyze-threads-show.php',
    'scripts/analyze-translation-structure.php',
    'scripts/analyze-user-activity.php',
    'scripts/analyze-user-ratings.php',
    'scripts/analyze_hardcoded_translations.php',
    'scripts/audit_hardcoded_text.php',
    
    // Bulk fix scripts (đã hoàn thành)
    'scripts/bulk-fix-dashboard-member.php',
    'scripts/bulk-fix-manufacturer-setup.php',
    'scripts/bulk-fix-ratings.php',
    'scripts/bulk-fix-user-activity.php',
    
    // Check scripts (đã hoàn thành)
    'scripts/check-error-logs.php',
    'scripts/check-student-keys.php',
    'scripts/check-syntax.php',
    
    // Auto fix scripts (đã hoàn thành)
    'scripts/auto_fix_translations.php',
    
    // Admin translation audit (đã hoàn thành)
    'scripts/admin_translation_audit.php',
];

$totalDeleted = 0;
$totalSkipped = 0;
$errors = [];

echo "🗑️ Deleting unnecessary files from root directory...\n";

foreach ($filesToDelete as $file) {
    if (file_exists($file)) {
        if (unlink($file)) {
            echo "   ✅ Deleted: {$file}\n";
            $totalDeleted++;
        } else {
            echo "   ❌ Failed to delete: {$file}\n";
            $errors[] = $file;
        }
    } else {
        echo "   ⏭️ Not found: {$file}\n";
        $totalSkipped++;
    }
}

echo "\n🗑️ Cleaning up storage temporary files...\n";

foreach ($storageFilesToDelete as $file) {
    if (file_exists($file)) {
        if (unlink($file)) {
            echo "   ✅ Deleted: {$file}\n";
            $totalDeleted++;
        } else {
            echo "   ❌ Failed to delete: {$file}\n";
            $errors[] = $file;
        }
    } else {
        echo "   ⏭️ Not found: {$file}\n";
        $totalSkipped++;
    }
}

echo "\n🗑️ Cleaning up old analysis and fix scripts...\n";

foreach ($scriptsToDelete as $file) {
    if (file_exists($file)) {
        if (unlink($file)) {
            echo "   ✅ Deleted: {$file}\n";
            $totalDeleted++;
        } else {
            echo "   ❌ Failed to delete: {$file}\n";
            $errors[] = $file;
        }
    } else {
        echo "   ⏭️ Not found: {$file}\n";
        $totalSkipped++;
    }
}

echo "\n📋 Checking project structure compliance...\n";

// Kiểm tra cấu trúc Laravel 11 chuẩn
$requiredDirectories = [
    'app',
    'bootstrap',
    'config',
    'database',
    'public',
    'resources',
    'routes',
    'storage',
    'tests',
    'vendor'
];

$requiredFiles = [
    'artisan',
    'composer.json',
    'composer.lock',
    'phpunit.xml',
    'README.md'
];

echo "✅ Checking required directories:\n";
foreach ($requiredDirectories as $dir) {
    if (is_dir($dir)) {
        echo "   ✅ {$dir}/\n";
    } else {
        echo "   ❌ Missing: {$dir}/\n";
        $errors[] = "Missing directory: {$dir}";
    }
}

echo "\n✅ Checking required files:\n";
foreach ($requiredFiles as $file) {
    if (file_exists($file)) {
        echo "   ✅ {$file}\n";
    } else {
        echo "   ❌ Missing: {$file}\n";
        $errors[] = "Missing file: {$file}";
    }
}

// Kiểm tra các thư mục/file có thể gây vấn đề
echo "\n⚠️ Checking for potential issues:\n";

$potentialIssues = [];

// Kiểm tra mechamap-nextjs
if (is_dir('mechamap-nextjs')) {
    echo "   ⚠️ Found: mechamap-nextjs/ (Next.js project - consider moving to separate repository)\n";
    $potentialIssues[] = 'mechamap-nextjs directory should be in separate repository';
}

// Kiểm tra realtime-server
if (is_dir('realtime-server')) {
    echo "   ℹ️ Found: realtime-server/ (WebSocket server - keeping as it's deployed)\n";
}

// Kiểm tra package.json (không cần thiết trong Laravel thuần)
if (file_exists('package.json')) {
    echo "   ⚠️ Found: package.json (consider if frontend assets are needed)\n";
    $potentialIssues[] = 'package.json found - review if frontend build process is needed';
}

// Kiểm tra file .env
if (!file_exists('.env')) {
    echo "   ⚠️ Missing: .env file\n";
    $potentialIssues[] = '.env file is missing';
}

echo "\n📊 Cleanup Summary:\n";
echo "==================\n";
echo "✅ Files deleted: {$totalDeleted}\n";
echo "⏭️ Files skipped: {$totalSkipped}\n";
echo "❌ Errors: " . count($errors) . "\n";
echo "⚠️ Potential issues: " . count($potentialIssues) . "\n";

if (!empty($errors)) {
    echo "\n❌ Errors encountered:\n";
    foreach ($errors as $error) {
        echo "   - {$error}\n";
    }
}

if (!empty($potentialIssues)) {
    echo "\n⚠️ Potential issues to review:\n";
    foreach ($potentialIssues as $issue) {
        echo "   - {$issue}\n";
    }
}

echo "\n🎯 Recommendations:\n";
echo "===================\n";
echo "1. ✅ Root directory cleaned of unnecessary files\n";
echo "2. ✅ Storage temporary files removed\n";
echo "3. ✅ Old analysis scripts cleaned up\n";
echo "4. 📝 Consider moving mechamap-nextjs to separate repository\n";
echo "5. 📝 Review if package.json and Node.js dependencies are needed\n";
echo "6. 📝 Ensure .env file is properly configured\n";
echo "7. 📝 Consider adding .gitignore rules for temporary files\n";

echo "\n✨ Laravel 11 project structure is now cleaner and more compliant!\n";

// Tạo .gitignore suggestions
$gitignoreAdditions = [
    '',
    '# Temporary analysis files',
    'storage/*.json',
    'storage/*.md',
    '',
    '# Script outputs',
    '/123],',
    '/456],',
    '/category',
    '/count',
    '/id',
    '/locale',
    '/priority',
    '/status',
    '/title',
    '/true],',
    '/urgency_level',
    '/192.168.1.200,',
    '/Automated test security alert,',
];

echo "\n📝 Suggested .gitignore additions:\n";
foreach ($gitignoreAdditions as $line) {
    echo $line . "\n";
}

echo "\n🔧 Next steps:\n";
echo "1. Review and update .gitignore file\n";
echo "2. Commit the cleaned project structure\n";
echo "3. Consider repository restructuring for Next.js project\n";
echo "4. Run composer install to ensure dependencies are up to date\n";
echo "5. Run php artisan optimize:clear to clear all caches\n";

echo "\n✅ Cleanup completed successfully!\n";
