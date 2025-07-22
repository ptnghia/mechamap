<?php

echo "Testing for remaining htmlspecialchars errors after fix...\n\n";

// Check log for recent htmlspecialchars errors
$logFile = 'storage/logs/laravel-2025-07-21.log';

if (!file_exists($logFile)) {
    echo "❌ Log file not found: $logFile\n";
    exit;
}

echo "📋 Checking log file: $logFile\n";

// Get the last 50 lines
$lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$recentLines = array_slice($lines, -50);

// Look for htmlspecialchars errors in recent entries
$htmlspecialcharsErrors = [];
$currentTime = date('Y-m-d H:');
$recentTime = date('Y-m-d H:', strtotime('-1 hour'));

foreach ($recentLines as $lineNum => $line) {
    if (strpos($line, 'htmlspecialchars') !== false) {
        // Check if it's a recent error (within last hour)
        if (strpos($line, $currentTime) !== false || strpos($line, $recentTime) !== false) {
            $htmlspecialcharsErrors[] = $line;
        }
    }
}

if (empty($htmlspecialcharsErrors)) {
    echo "✅ No recent htmlspecialchars errors found!\n";
    echo "✅ The fix for t_auth('logout') appears successful\n";
} else {
    echo "❌ Found " . count($htmlspecialcharsErrors) . " recent htmlspecialchars errors:\n\n";
    foreach ($htmlspecialcharsErrors as $error) {
        echo "  " . $error . "\n";
    }
}

echo "\n🔍 Summary of the fix applied:\n";
echo "  - Fixed t_auth('logout') to t_auth('logout.title') in header.blade.php\n";
echo "  - Cleared view cache to recompile templates\n";
echo "  - Verified no other __() calls return arrays\n\n";

echo "Test completed at " . date('Y-m-d H:i:s') . "\n";
