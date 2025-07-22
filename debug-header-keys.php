<?php

// Bootstrap Laravel
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$kernel->handle($request);

echo "Testing all translation keys used in header.blade.php:\n\n";

// All translation keys found in header.blade.php
$testKeys = [
    '__("search.form.placeholder")',
    '__("common.buttons.search")',
    '__("search.scope.all_content")',
    '__("search.scope.in_thread")',
    '__("search.scope.in_forum")',
    '__("navigation.main.marketplace")',
    '__("search.actions.advanced")',
    't_navigation("main.community")',
    't_navigation("main.showcase")',
    't_navigation("main.marketplace")',
    't_navigation("actions.add")',
    '__("common.technical.resources")',
    '__("common.technical.database")',
    '__("common.technical.materials_database")',
    '__("common.technical.engineering_standards")',
    '__("common.technical.manufacturing_processes")',
    '__("common.technical.design_resources")',
    '__("common.technical.cad_library")',
    '__("common.technical.technical_drawings")',
    '__("common.technical.tools_calculators")',
    '__("common.technical.material_cost_calculator")',
    '__("common.technical.process_selector")',
    '__("common.technical.standards_compliance")',
    '__("common.knowledge.title")',
];

foreach ($testKeys as $i => $testKey) {
    echo "Test " . ($i + 1) . ": {$testKey}\n";

    try {
        // Extract function and key
        if (strpos($testKey, 't_navigation(') === 0) {
            preg_match('/t_navigation\("([^"]+)"\)/', $testKey, $matches);
            $key = $matches[1];
            $result = t_navigation($key);
        } else {
            preg_match('/__\("([^"]+)"\)/', $testKey, $matches);
            $key = $matches[1];
            $result = __($key);
        }

        if (is_array($result)) {
            echo "  ❌ ERROR: Returns array!\n";
            echo "  Array content: " . json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
        } else {
            echo "  ✅ OK: \"" . mb_substr($result, 0, 50) . "\"\n";
        }
    } catch (Exception $e) {
        echo "  ❌ EXCEPTION: " . $e->getMessage() . "\n";
    }
    echo "\n";
}

echo "Testing completed.\n";
