<?php
/**
 * Batch Blade Audit Runner
 * Runs localization audit for all view directories (excluding admin)
 */

require_once 'blade_audit_toolkit.php';

echo "🚀 Starting Batch Blade Localization Audit...\n";
echo "==============================================\n\n";

// List of directories to audit (excluding admin)
$directories = [
    'about',
    'alerts', 
    'auth',
    'bookmarks',
    'brand',
    'business',
    'categories',
    'chat',
    'community',
    'components',
    'conversations',
    'devices',
    'docs',
    'emails',
    'faq',
    'following',
    'forums',
    'frontend',
    'gallery',
    'help',
    'knowledge',
    'layouts',
    'manufacturer',
    'marketplace',
    'members',
    'new-content',
    'news',
    'notifications',
    'pages',
    'partials',
    'profile',
    'realtime',
    'search',
    'showcase',
    'showcases',
    'student',
    'subscription',
    'supplier',
    'technical',
    'test',
    'threads',
    'tools',
    'user',
    'users',
    'vendor',
    'whats-new'
];

$toolkit = new BladeAuditToolkit();
$batchResults = [];
$totalFiles = 0;
$totalHardcoded = 0;
$totalExistingKeys = 0;

// Create batch results directory
$batchDir = 'storage/localization/batch_audit_' . date('Y_m_d_H_i_s');
if (!is_dir($batchDir)) {
    mkdir($batchDir, 0755, true);
}

echo "📁 Batch results will be saved to: $batchDir\n\n";

// Process each directory
foreach ($directories as $directory) {
    echo "🔍 Processing: $directory\n";
    
    $results = $toolkit->auditDirectory($directory);
    $batchResults[$directory] = $results;
    
    // Update totals
    $totalFiles += $results['files_processed'];
    $totalHardcoded += count($results['hardcoded_texts']);
    $totalExistingKeys += count($results['existing_keys']);
    
    // Generate individual report
    $reportPath = "$batchDir/audit_report_$directory.md";
    $toolkit->generateReport($results, $reportPath);
    
    echo "   📊 Report: $reportPath\n";
    echo "   📝 Hardcoded: " . count($results['hardcoded_texts']) . "\n";
    echo "   🔑 Existing keys: " . count($results['existing_keys']) . "\n\n";
    
    // Small delay to prevent overwhelming the system
    usleep(100000); // 0.1 second
}

// Process root level files
echo "🔍 Processing: ROOT LEVEL FILES\n";
$rootFiles = ['home.blade.php', 'dashboard.blade.php', 'welcome.blade.php', 'coming-soon.blade.php'];
$rootResults = [
    'directory' => 'root',
    'files_processed' => 0,
    'hardcoded_texts' => [],
    'existing_keys' => [],
    'recommendations' => []
];

foreach ($rootFiles as $file) {
    $filePath = '/var/www/mechamap_com_usr/data/www/mechamap.com/resources/views/' . $file;
    if (file_exists($filePath)) {
        $fileResults = $toolkit->auditFile($filePath);
        $rootResults['files_processed']++;
        $rootResults['hardcoded_texts'] = array_merge($rootResults['hardcoded_texts'], $fileResults['hardcoded']);
        $rootResults['existing_keys'] = array_merge($rootResults['existing_keys'], $fileResults['existing_keys']);
    }
}

$rootResults['hardcoded_texts'] = array_unique($rootResults['hardcoded_texts']);
$rootResults['existing_keys'] = array_unique($rootResults['existing_keys']);
$batchResults['root'] = $rootResults;

$totalFiles += $rootResults['files_processed'];
$totalHardcoded += count($rootResults['hardcoded_texts']);
$totalExistingKeys += count($rootResults['existing_keys']);

// Generate root report
$rootReportPath = "$batchDir/audit_report_root.md";
$toolkit->generateReport($rootResults, $rootReportPath);

echo "   📊 Report: $rootReportPath\n";
echo "   📝 Hardcoded: " . count($rootResults['hardcoded_texts']) . "\n";
echo "   🔑 Existing keys: " . count($rootResults['existing_keys']) . "\n\n";

// Generate comprehensive batch summary
generateBatchSummary($batchResults, $batchDir, $totalFiles, $totalHardcoded, $totalExistingKeys);

echo "🎉 Batch audit completed!\n";
echo "📊 Total files processed: $totalFiles\n";
echo "📝 Total hardcoded texts: $totalHardcoded\n";
echo "🔑 Total existing keys: $totalExistingKeys\n";
echo "📁 Results directory: $batchDir\n";

function generateBatchSummary($batchResults, $batchDir, $totalFiles, $totalHardcoded, $totalExistingKeys) {
    $summary = "# Batch Blade Localization Audit - Summary Report\n\n";
    $summary .= "**Generated:** " . date('Y-m-d H:i:s') . "\n";
    $summary .= "**Total directories processed:** " . count($batchResults) . "\n";
    $summary .= "**Total files processed:** $totalFiles\n";
    $summary .= "**Total hardcoded texts found:** $totalHardcoded\n";
    $summary .= "**Total existing keys found:** $totalExistingKeys\n\n";
    
    $summary .= "## 📊 Directory Summary\n\n";
    $summary .= "| Directory | Files | Hardcoded | Existing Keys |\n";
    $summary .= "|-----------|-------|-----------|---------------|\n";
    
    foreach ($batchResults as $dir => $results) {
        $files = $results['files_processed'];
        $hardcoded = count($results['hardcoded_texts']);
        $existing = count($results['existing_keys']);
        $summary .= "| $dir | $files | $hardcoded | $existing |\n";
    }
    
    $summary .= "\n## 🎯 Priority Directories (Most Hardcoded Text)\n\n";
    
    // Sort by hardcoded text count
    $priorityDirs = $batchResults;
    uasort($priorityDirs, function($a, $b) {
        return count($b['hardcoded_texts']) - count($a['hardcoded_texts']);
    });
    
    $count = 0;
    foreach ($priorityDirs as $dir => $results) {
        if ($count >= 10) break; // Top 10
        $hardcodedCount = count($results['hardcoded_texts']);
        if ($hardcodedCount > 0) {
            $summary .= "### $dir ($hardcodedCount hardcoded texts)\n";
            $summary .= "Priority: " . ($hardcodedCount > 20 ? "🔴 HIGH" : ($hardcodedCount > 10 ? "🟡 MEDIUM" : "🟢 LOW")) . "\n\n";
            $count++;
        }
    }
    
    $summary .= "\n## 📋 Next Steps\n\n";
    $summary .= "1. **Start with high-priority directories** (most hardcoded text)\n";
    $summary .= "2. **Review individual reports** for detailed recommendations\n";
    $summary .= "3. **Apply localization fixes** using suggested keys and helper functions\n";
    $summary .= "4. **Validate translations** exist in both VI and EN\n";
    $summary .= "5. **Test functionality** after applying changes\n\n";
    
    $summary .= "## 🛠️ Tools Available\n\n";
    $summary .= "- **Helper functions:** `t_core()`, `t_ui()`, `t_content()`, `t_feature()`, `t_user()`\n";
    $summary .= "- **Blade directives:** `@core()`, `@ui()`, `@content()`, `@feature()`, `@user()`\n";
    $summary .= "- **Translation structure:** `resources/lang_new/`\n";
    $summary .= "- **Validation tools:** Available in existing localization toolkit\n\n";
    
    $summary .= "**Status:** 🚀 Ready for systematic localization improvements\n";
    
    file_put_contents("$batchDir/BATCH_SUMMARY.md", $summary);
    
    // Also create a CSV for easy analysis
    $csv = "Directory,Files,Hardcoded,Existing Keys,Priority\n";
    foreach ($batchResults as $dir => $results) {
        $files = $results['files_processed'];
        $hardcoded = count($results['hardcoded_texts']);
        $existing = count($results['existing_keys']);
        $priority = $hardcoded > 20 ? "HIGH" : ($hardcoded > 10 ? "MEDIUM" : "LOW");
        $csv .= "$dir,$files,$hardcoded,$existing,$priority\n";
    }
    file_put_contents("$batchDir/batch_summary.csv", $csv);
    
    echo "📊 Batch summary: $batchDir/BATCH_SUMMARY.md\n";
    echo "📈 CSV data: $batchDir/batch_summary.csv\n";
}
