<?php

/**
 * Detailed analysis of community/companies/contact.blade.php issues
 */

// Bootstrap Laravel
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 DETAILED ANALYSIS: community/companies/contact.blade.php\n";
echo "==========================================================\n\n";

$file = 'resources/views/community/companies/contact.blade.php';

if (!file_exists($file)) {
    echo "❌ File not found: $file\n";
    exit(1);
}

$content = file_get_contents($file);

// Find all translation calls
preg_match_all('/__\([\'"]([^\'"]+)[\'"](?:[^)]+)?\)/', $content, $matches, PREG_OFFSET_CAPTURE);

$issues = [];
$successCount = 0;

foreach ($matches[1] as $index => $match) {
    $key = $match[0];
    $position = $match[1];

    // Get line number
    $lineNumber = substr_count(substr($content, 0, $position), "\n") + 1;

    try {
        $value = __($key);

        if (is_array($value)) {
            $issues[] = [
                'key' => $key,
                'line' => $lineNumber,
                'type' => 'array',
                'value' => $value
            ];
            echo "❌ Line $lineNumber: __('$key') returns ARRAY\n";
            echo "   " . json_encode($value) . "\n\n";
        } elseif ($value === $key) {
            $issues[] = [
                'key' => $key,
                'line' => $lineNumber,
                'type' => 'missing',
                'value' => null
            ];
            echo "⚠️  Line $lineNumber: __('$key') key not found\n\n";
        } else {
            $successCount++;
            echo "✅ Line $lineNumber: __('$key') = '$value'\n";
        }
    } catch (Exception $e) {
        $issues[] = [
            'key' => $key,
            'line' => $lineNumber,
            'type' => 'error',
            'value' => $e->getMessage()
        ];
        echo "💥 Line $lineNumber: __('$key') threw exception: " . $e->getMessage() . "\n\n";
    }
}

echo "\n📊 SUMMARY:\n";
echo "===========\n";
echo "Total translation calls: " . count($matches[1]) . "\n";
echo "Successful calls: $successCount\n";
echo "Problematic calls: " . count($issues) . "\n";
echo "Success rate: " . round(($successCount / count($matches[1])) * 100, 1) . "%\n";

if (!empty($issues)) {
    echo "\n❌ ISSUES TO FIX:\n";
    echo "=================\n";

    foreach ($issues as $issue) {
        echo "Line {$issue['line']}: {$issue['key']} ({$issue['type']})\n";
    }
}

echo "\n";

?>
