<?php

/**
 * Laravel Error Log Checker
 * Check recent errors and identify specific htmlspecialchars() issues
 */

echo "🔍 Laravel Error Log Analysis\n";
echo "============================\n\n";

// Common Laravel log locations
$logPaths = [
    'storage/logs/laravel.log',
    'storage/logs/' . date('Y-m-d') . '.log',
    'storage/logs/laravel-' . date('Y-m-d') . '.log',
];

echo "📋 Checking Laravel log files...\n";

$foundLogs = [];
foreach ($logPaths as $path) {
    if (file_exists($path)) {
        $foundLogs[] = $path;
        echo "✅ Found: {$path}\n";
    } else {
        echo "⚠️  Not found: {$path}\n";
    }
}

if (empty($foundLogs)) {
    echo "\n❌ No Laravel log files found!\n";
    echo "🔍 Checking if storage/logs directory exists...\n";

    if (!is_dir('storage/logs')) {
        echo "❌ storage/logs directory doesn't exist\n";
        echo "📝 Creating storage/logs directory...\n";
        if (mkdir('storage/logs', 0755, true)) {
            echo "✅ Created storage/logs directory\n";
        } else {
            echo "❌ Failed to create storage/logs directory\n";
        }
    } else {
        echo "✅ storage/logs directory exists\n";

        // List all files in storage/logs
        $files = scandir('storage/logs');
        echo "📁 Files in storage/logs:\n";
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                echo "   - {$file}\n";
            }
        }
    }

    echo "\n🎯 Try accessing your website to generate error logs\n";
    exit;
}

echo "\n📋 Analyzing recent errors...\n";
echo str_repeat('=', 50) . "\n";

foreach ($foundLogs as $logPath) {
    echo "\n📄 Analyzing: {$logPath}\n";
    echo str_repeat('-', 30) . "\n";

    if (!is_readable($logPath)) {
        echo "❌ Cannot read log file\n";
        continue;
    }

    $logContent = file_get_contents($logPath);

    if (empty($logContent)) {
        echo "⚠️  Log file is empty\n";
        continue;
    }

    // Look for htmlspecialchars errors
    $htmlspecialcharsErrors = [];
    $lines = explode("\n", $logContent);

    foreach ($lines as $lineNum => $line) {
        if (stripos($line, 'htmlspecialchars') !== false) {
            $htmlspecialcharsErrors[] = [
                'line' => $lineNum + 1,
                'content' => $line
            ];
        }
    }

    if (!empty($htmlspecialcharsErrors)) {
        echo "🚨 Found " . count($htmlspecialcharsErrors) . " htmlspecialchars errors:\n\n";

        foreach (array_slice($htmlspecialcharsErrors, -5) as $error) {
            echo "Line {$error['line']}:\n";
            echo "   {$error['content']}\n\n";
        }
    } else {
        echo "✅ No htmlspecialchars errors found in this log\n";
    }

    // Look for recent errors (last 50 lines)
    $recentLines = array_slice($lines, -50);
    $recentErrors = [];

    foreach ($recentLines as $line) {
        if (stripos($line, 'ERROR') !== false ||
            stripos($line, 'CRITICAL') !== false ||
            stripos($line, 'EMERGENCY') !== false) {
            $recentErrors[] = $line;
        }
    }

    if (!empty($recentErrors)) {
        echo "📝 Recent errors (last 50 lines):\n";
        foreach (array_slice($recentErrors, -3) as $error) {
            echo "   {$error}\n";
        }
        echo "\n";
    }
}

// Check web server error logs
echo "\n📋 Checking Web Server Error Logs...\n";
echo str_repeat('=', 50) . "\n";

$webServerLogs = [
    'C:/xampp/apache/logs/error.log',
    '/var/log/apache2/error.log',
    '/var/log/nginx/error.log',
    '/usr/local/var/log/apache2/error.log',
];

$foundWebLogs = [];
foreach ($webServerLogs as $path) {
    if (file_exists($path)) {
        $foundWebLogs[] = $path;
        echo "✅ Found web server log: {$path}\n";
    }
}

if (!empty($foundWebLogs)) {
    foreach ($foundWebLogs as $logPath) {
        echo "\n📄 Checking recent entries in: {$logPath}\n";

        // Get last 20 lines
        $command = "tail -20 \"$logPath\" 2>/dev/null";
        $output = shell_exec($command);

        if ($output) {
            $lines = explode("\n", trim($output));
            foreach ($lines as $line) {
                if (stripos($line, 'error') !== false ||
                    stripos($line, 'fatal') !== false ||
                    stripos($line, 'php') !== false) {
                    echo "   {$line}\n";
                }
            }
        } else {
            echo "   ⚠️  Cannot read web server log\n";
        }
    }
}

// Generate test error
echo "\n📋 Testing Error Logging...\n";
echo str_repeat('=', 50) . "\n";

echo "🧪 Creating test error to verify logging works...\n";

// Create a simple test file that should trigger an error
$testContent = '<?php
// Test file to trigger htmlspecialchars error
$testArray = ["key" => "value"];
echo $testArray; // This should cause an error
?>';

file_put_contents('test-error.php', $testContent);

echo "✅ Created test-error.php\n";
echo "📝 Try accessing: http://your-domain.com/test-error.php\n";
echo "📝 This should generate an error in the logs\n";

echo "\n🎯 INSTRUCTIONS:\n";
echo "================\n";
echo "1. Access your website now to generate fresh error logs\n";
echo "2. Run this script again: php check-error-logs.php\n";
echo "3. Look for new htmlspecialchars errors\n";
echo "4. Share the specific error message you find\n\n";

echo "🔍 Alternative: Check error logs manually:\n";
echo " - Laravel logs: storage/logs/laravel.log\n";
echo " - XAMPP logs: C:/xampp/apache/logs/error.log\n";
echo " - Look for lines containing 'htmlspecialchars'\n";

?>