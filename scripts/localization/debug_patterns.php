<?php

$testContent = file_get_contents('resources/views/home.blade.php');

$patterns = [
    // Basic __() patterns
    '/__(\'([^\']+)\')/m',
    '/__\("([^"]+)"\)/m',

    // Blade template patterns
    '/\{\{\s*__(\'([^\']+)\')\s*\}\}/m',
    '/\{\{\s*__\("([^"]+)"\)\s*\}\}/m',

    // @section patterns
    '/@section\([^,]+,\s*__(\'([^\']+)\')\)/m',
    '/@section\([^,]+,\s*__\("([^"]+)"\)\)/m',
];

echo "Testing patterns on home.blade.php:\n\n";

foreach ($patterns as $i => $pattern) {
    echo "Pattern $i: $pattern\n";
    preg_match_all($pattern, $testContent, $matches, PREG_SET_ORDER);
    echo "Matches found: " . count($matches) . "\n";

    foreach ($matches as $match) {
        echo "  - " . $match[0] . " -> " . ($match[2] ?? $match[1]) . "\n";
    }
    echo "\n";
}
