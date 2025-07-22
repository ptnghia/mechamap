<?php

/**
 * Replace ui.common keys with common keys in blade files
 * Fix translation key references to use existing structure
 */

echo "🔄 Replacing ui.common.* keys with common.* keys\n";
echo "===============================================\n\n";

// Find all blade files
$bladeFiles = glob('resources/views/**/*.blade.php', GLOB_BRACE);
$bladeFiles = array_merge($bladeFiles, glob('resources/views/**/**/*.blade.php', GLOB_BRACE));
$bladeFiles = array_merge($bladeFiles, glob('resources/views/**/**/**/*.blade.php', GLOB_BRACE));

$totalChanges = 0;
$filesChanged = 0;

foreach ($bladeFiles as $file) {
    if (!file_exists($file)) continue;

    echo "🔍 Processing: {$file}\n";

    $content = file_get_contents($file);
    $originalContent = $content;

    // Common replacements for language-related keys
    $replacements = [
        // Language keys we just created
        "__('ui.common.language.switch')" => "__('common.language.switch')",
        "__('ui.common.language.select')" => "__('common.language.select')",
        "__('ui.common.language.auto_detect')" => "__('common.language.auto_detect')",

        // Basic common keys that should exist
        "__('ui.common.popular')" => "__('common.buttons.popular')",
        "__('ui.common.today')" => "__('common.time.today')",
        "__('ui.common.this_week')" => "__('common.time.this_week')",
        "__('ui.common.this_month')" => "__('common.time.this_month')",
        "__('ui.common.this_year')" => "__('common.time.this_year')",
        "__('ui.common.all_time')" => "__('common.time.all_time')",
        "__('ui.common.latest')" => "__('common.buttons.latest')",
        "__('ui.common.category')" => "__('common.labels.category')",
        "__('ui.common.replies')" => "__('common.labels.replies')",
        "__('ui.common.by')" => "__('common.labels.by')",
        "__('ui.common.forum')" => "__('common.labels.forum')",

        // For now, keep complex member keys as placeholders
        // We'll add these to common.php if needed
    ];

    $fileChanges = 0;
    foreach ($replacements as $search => $replace) {
        if (strpos($content, $search) !== false) {
            $content = str_replace($search, $replace, $content);
            $fileChanges++;
        }
    }

    // Write back if changed
    if ($content !== $originalContent && $fileChanges > 0) {
        file_put_contents($file, $content);
        echo "  ✅ Made {$fileChanges} replacements\n";
        $totalChanges += $fileChanges;
        $filesChanged++;
    } else {
        echo "  ➖ No changes needed\n";
    }
}

echo "\n📊 Summary:\n";
echo "===========\n";
echo "Files processed: " . count($bladeFiles) . "\n";
echo "Files changed: {$filesChanged}\n";
echo "Total replacements: {$totalChanges}\n\n";

if ($totalChanges > 0) {
    echo "🎯 Next: Add missing keys to common.php files\n";
    echo "Keys that may need to be added:\n";
    echo "- common.buttons.popular\n";
    echo "- common.time.today, this_week, this_month, etc.\n";
    echo "- common.labels.category, replies, by, forum\n\n";
}

?>
