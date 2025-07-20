<?php
/**
 * Quick Batch Audit - Simplified version for overview
 * Get quick statistics on localization status across all directories
 */

echo "🚀 Quick Batch Localization Audit...\n";
echo "====================================\n\n";

$basePath = '/var/www/mechamap_com_usr/data/www/mechamap.com/resources/views';
$directories = [
    'about', 'alerts', 'auth', 'bookmarks', 'brand', 'business', 'categories',
    'chat', 'community', 'components', 'conversations', 'devices', 'docs',
    'emails', 'faq', 'following', 'forums', 'frontend', 'gallery', 'help',
    'knowledge', 'layouts', 'manufacturer', 'marketplace', 'members',
    'new-content', 'news', 'notifications', 'pages', 'partials', 'profile',
    'realtime', 'search', 'showcase', 'showcases', 'student', 'subscription',
    'supplier', 'technical', 'test', 'threads', 'tools', 'user', 'users',
    'vendor', 'whats-new'
];

$results = [];
$totalFiles = 0;
$totalDirectories = 0;

// Quick scan of each directory
foreach ($directories as $dir) {
    $dirPath = $basePath . '/' . $dir;
    if (is_dir($dirPath)) {
        $fileCount = countBladeFiles($dirPath);
        $results[$dir] = [
            'files' => $fileCount,
            'exists' => true,
            'priority' => calculatePriority($dir, $fileCount)
        ];
        $totalFiles += $fileCount;
        $totalDirectories++;
        echo "📁 $dir: $fileCount files\n";
    } else {
        $results[$dir] = [
            'files' => 0,
            'exists' => false,
            'priority' => 'SKIP'
        ];
        echo "⚠️ $dir: Directory not found\n";
    }
}

// Check root files
$rootFiles = ['home.blade.php', 'dashboard.blade.php', 'welcome.blade.php', 'coming-soon.blade.php'];
$rootCount = 0;
foreach ($rootFiles as $file) {
    if (file_exists($basePath . '/' . $file)) {
        $rootCount++;
    }
}
$results['root'] = ['files' => $rootCount, 'exists' => true, 'priority' => 'MEDIUM'];
$totalFiles += $rootCount;

echo "\n📊 SUMMARY:\n";
echo "- Total directories: $totalDirectories\n";
echo "- Total Blade files: $totalFiles\n";
echo "- Average files per directory: " . round($totalFiles / $totalDirectories, 1) . "\n\n";

// Priority analysis
echo "🎯 PRIORITY ANALYSIS:\n";
$highPriority = [];
$mediumPriority = [];
$lowPriority = [];

foreach ($results as $dir => $data) {
    if (!$data['exists']) continue;
    
    switch ($data['priority']) {
        case 'HIGH':
            $highPriority[] = "$dir ({$data['files']} files)";
            break;
        case 'MEDIUM':
            $mediumPriority[] = "$dir ({$data['files']} files)";
            break;
        case 'LOW':
            $lowPriority[] = "$dir ({$data['files']} files)";
            break;
    }
}

echo "\n🔴 HIGH PRIORITY (" . count($highPriority) . " directories):\n";
foreach (array_slice($highPriority, 0, 10) as $item) {
    echo "   - $item\n";
}

echo "\n🟡 MEDIUM PRIORITY (" . count($mediumPriority) . " directories):\n";
foreach (array_slice($mediumPriority, 0, 10) as $item) {
    echo "   - $item\n";
}

echo "\n🟢 LOW PRIORITY (" . count($lowPriority) . " directories):\n";
foreach (array_slice($lowPriority, 0, 5) as $item) {
    echo "   - $item\n";
}

// Generate quick report
generateQuickReport($results, $totalFiles, $totalDirectories);

echo "\n🎉 Quick audit completed!\n";
echo "📊 Report: storage/localization/quick_audit_overview.md\n";

function countBladeFiles($directory) {
    $count = 0;
    if (!is_dir($directory)) return 0;
    
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS)
    );
    
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php' && 
            strpos($file->getFilename(), '.blade.') !== false) {
            $count++;
        }
    }
    
    return $count;
}

function calculatePriority($directory, $fileCount) {
    // High priority: Core functionality and high file count
    $highPriorityDirs = ['auth', 'layouts', 'components', 'partials', 'forums', 'marketplace', 'user', 'profile'];
    
    if (in_array($directory, $highPriorityDirs) || $fileCount > 20) {
        return 'HIGH';
    } elseif ($fileCount > 5) {
        return 'MEDIUM';
    } else {
        return 'LOW';
    }
}

function generateQuickReport($results, $totalFiles, $totalDirectories) {
    $report = "# Quick Localization Audit Overview\n\n";
    $report .= "**Generated:** " . date('Y-m-d H:i:s') . "\n";
    $report .= "**Total directories scanned:** $totalDirectories\n";
    $report .= "**Total Blade files found:** $totalFiles\n\n";
    
    $report .= "## 📊 Directory Statistics\n\n";
    $report .= "| Directory | Files | Priority | Status |\n";
    $report .= "|-----------|-------|----------|--------|\n";
    
    foreach ($results as $dir => $data) {
        $status = $data['exists'] ? '✅' : '❌';
        $files = $data['files'];
        $priority = $data['priority'];
        $report .= "| $dir | $files | $priority | $status |\n";
    }
    
    $report .= "\n## 🎯 Recommended Audit Order\n\n";
    
    // Sort by priority and file count
    $sorted = $results;
    uasort($sorted, function($a, $b) {
        $priorityOrder = ['HIGH' => 3, 'MEDIUM' => 2, 'LOW' => 1, 'SKIP' => 0];
        $aPriority = $priorityOrder[$a['priority']] ?? 0;
        $bPriority = $priorityOrder[$b['priority']] ?? 0;
        
        if ($aPriority === $bPriority) {
            return $b['files'] - $a['files']; // More files first
        }
        return $bPriority - $aPriority; // Higher priority first
    });
    
    $order = 1;
    foreach ($sorted as $dir => $data) {
        if (!$data['exists'] || $data['files'] === 0) continue;
        
        $emoji = $data['priority'] === 'HIGH' ? '🔴' : ($data['priority'] === 'MEDIUM' ? '🟡' : '🟢');
        $report .= "$order. $emoji **$dir** ({$data['files']} files) - {$data['priority']} priority\n";
        $order++;
        
        if ($order > 20) break; // Top 20 only
    }
    
    $report .= "\n## 📋 Next Steps\n\n";
    $report .= "1. **Start with HIGH priority directories** (core functionality)\n";
    $report .= "2. **Use the full audit toolkit** for detailed analysis:\n";
    $report .= "   ```bash\n";
    $report .= "   php scripts/localization/blade_audit_toolkit.php <directory>\n";
    $report .= "   ```\n";
    $report .= "3. **Apply fixes systematically** using the localization fixer\n";
    $report .= "4. **Test each directory** after applying fixes\n\n";
    
    $report .= "## 🛠️ Tools Available\n\n";
    $report .= "- **Audit toolkit:** `blade_audit_toolkit.php`\n";
    $report .= "- **Batch runner:** `run_blade_audit_batch.php`\n";
    $report .= "- **Fix applier:** `apply_localization_fixes.php`\n";
    $report .= "- **Helper functions:** Available in existing localization system\n\n";
    
    $report .= "**Estimated effort:** " . estimateEffort($totalFiles) . "\n";
    
    file_put_contents('storage/localization/quick_audit_overview.md', $report);
}

function estimateEffort($totalFiles) {
    $hoursPerFile = 0.25; // 15 minutes per file on average
    $totalHours = $totalFiles * $hoursPerFile;
    $days = ceil($totalHours / 8); // 8 hours per day
    
    return "$totalHours hours (~$days working days)";
}
