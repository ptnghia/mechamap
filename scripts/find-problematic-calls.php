<?php

/**
 * Find Problematic __() Calls that Return Arrays
 * Location: scripts/find-problematic-calls.php
 */

// Bootstrap Laravel
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Finding Problematic __() Calls\n";
echo "===================================\n\n";

// Get all blade files
$bladeFiles = [];
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator('resources/views')
);

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $bladeFiles[] = $file->getPathname();
    }
}

echo "📁 Found " . count($bladeFiles) . " blade files\n\n";

$problematicCalls = [];
$totalCallsFound = 0;

foreach ($bladeFiles as $file) {
    $content = file_get_contents($file);
    $relativePath = str_replace(getcwd() . DIRECTORY_SEPARATOR, '', $file);

    // Find all __() calls
    preg_match_all('/__\([\'"]([^\'"]+)[\'"]\)/', $content, $matches, PREG_OFFSET_CAPTURE);

    if (!empty($matches[1])) {
        echo "📄 $relativePath:\n";

        foreach ($matches[1] as $match) {
            $key = $match[0];
            $totalCallsFound++;

            // Test this specific key
            try {
                $result = __($key);
                $type = gettype($result);

                if ($type === 'array') {
                    echo "   ❌ '$key' returns ARRAY\n";
                    $problematicCalls[] = [
                        'file' => $relativePath,
                        'key' => $key,
                        'result' => $result
                    ];
                } elseif ($type === 'string') {
                    if ($result === $key) {
                        echo "   ⚠️  '$key' = '$result' (untranslated)\n";
                    } else {
                        echo "   ✅ '$key' = '$result'\n";
                    }
                } else {
                    echo "   ❓ '$key' returns $type\n";
                }
            } catch (Exception $e) {
                echo "   💥 '$key' threw exception: " . $e->getMessage() . "\n";
                $problematicCalls[] = [
                    'file' => $relativePath,
                    'key' => $key,
                    'error' => $e->getMessage()
                ];
            }
        }
        echo "\n";
    }
}

echo "📊 SUMMARY\n";
echo "==========\n";
echo "Total __() calls found: $totalCallsFound\n";
echo "Problematic calls (returning arrays): " . count($problematicCalls) . "\n\n";

if (!empty($problematicCalls)) {
    echo "🚨 PROBLEMATIC CALLS DETAILS:\n";
    echo "==============================\n";

    foreach ($problematicCalls as $i => $call) {
        echo ($i + 1) . ". File: {$call['file']}\n";
        echo "   Key: {$call['key']}\n";

        if (isset($call['result'])) {
            echo "   Returns: " . json_encode($call['result'], JSON_PRETTY_PRINT) . "\n";
        }

        if (isset($call['error'])) {
            echo "   Error: {$call['error']}\n";
        }
        echo "\n";
    }

    // Generate fix script
    echo "🔧 Generating fix script...\n";
    generateFixScript($problematicCalls);
} else {
    echo "✅ No problematic __() calls found!\n";
    echo "The htmlspecialchars error might be coming from elsewhere.\n\n";

    echo "🔍 Let's check for other potential sources:\n";
    echo "1. Variables passed to blade templates\n";
    echo "2. Model attributes\n";
    echo "3. Dynamic translation keys\n";
}

function generateFixScript($problematicCalls) {
    $script = "<?php\n\n";
    $script .= "/**\n * Auto-generated Fix Script for Problematic __() Calls\n */\n\n";
    $script .= "// Bootstrap Laravel\n";
    $script .= "require_once __DIR__ . '/../vendor/autoload.php';\n";
    $script .= "\$app = require_once __DIR__ . '/../bootstrap/app.php';\n";
    $script .= "\$app->make('Illuminate\\Contracts\\Console\\Kernel')->bootstrap();\n\n";

    $script .= "echo \"🔧 Fixing Problematic __() Calls\\n\";\n";
    $script .= "echo \"==============================\\n\\n\";\n\n";

    // Group by file
    $byFile = [];
    foreach ($problematicCalls as $call) {
        $byFile[$call['file']][] = $call;
    }

    foreach ($byFile as $file => $calls) {
        $script .= "// Fix file: $file\n";
        $script .= "echo \"📄 Fixing $file...\\n\";\n";
        $script .= "\$content = file_get_contents('$file');\n";
        $script .= "\$changed = false;\n\n";

        foreach ($calls as $call) {
            $key = $call['key'];

            // Determine appropriate helper function
            $parts = explode('.', $key);
            $domain = $parts[0] ?? '';

            $helperMap = [
                'auth' => 't_auth',
                'common' => 't_common',
                'navigation' => 't_navigation',
                'forums' => 't_forums',
                'marketplace' => 't_marketplace',
                'search' => 't_search',
                'user' => 't_user',
                'admin' => 't_admin',
                'pages' => 't_pages',
                'showcase' => 't_showcase',
                'sidebar' => 't_sidebar',
                'homepage' => 't_homepage',
                'footer' => 't_footer',
            ];

            if (isset($helperMap[$domain])) {
                $helper = $helperMap[$domain];
                $newKey = implode('.', array_slice($parts, 1));

                $script .= "// Fix: $key\n";
                $script .= "if (strpos(\$content, '__(\\'$key\\')') !== false) {\n";
                $script .= "    \$content = str_replace('__(\\'$key\\')', '$helper(\\'$newKey\\')', \$content);\n";
                $script .= "    \$changed = true;\n";
                $script .= "    echo \"   ✅ Fixed: $key → $helper('$newKey')\\n\";\n";
                $script .= "}\n\n";
            }
        }

        $script .= "if (\$changed) {\n";
        $script .= "    file_put_contents('$file', \$content);\n";
        $script .= "    echo \"✅ Updated: $file\\n\";\n";
        $script .= "}\n\n";
    }

    $script .= "echo \"✅ Fix script completed!\\n\";\n";

    file_put_contents('scripts/fix-problematic-calls.php', $script);
    echo "✅ Generated: scripts/fix-problematic-calls.php\n";
}

echo "✅ Analysis completed!\n";