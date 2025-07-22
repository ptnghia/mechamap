<?php

/**
 * FIX BOOKMARKS BLADE FILE
 * Sửa hardcoded strings trong bookmarks/index.blade.php
 */

echo "=== FIXING BOOKMARKS BLADE FILE ===\n\n";

// Mapping of hardcoded strings to translation keys
$replacements = [
    "{{ __('Reply in') }}" => "{{ __('bookmarks.reply_in') }}",
    "{{ __('Bookmarked item') }}" => "{{ __('bookmarks.bookmarked_item') }}",
    "{{ __('Notes') }}" => "{{ __('bookmarks.notes') }}",
    "{{ __('Bookmarked') }}" => "{{ __('bookmarks.bookmarked') }}",
    "{{ __('Remove') }}" => "{{ __('bookmarks.remove') }}",
    "{{ __('Bookmark threads and posts to find them easily later.') }}" => "{{ __('bookmarks.help_text') }}",
];

$file = 'resources/views/bookmarks/index.blade.php';
$fullPath = __DIR__ . '/' . $file;

if (!file_exists($fullPath)) {
    echo "❌ File not found: $file\n";
    exit(1);
}

echo "📄 Processing: $file\n";

$content = file_get_contents($fullPath);
if ($content === false) {
    echo "❌ Failed to read $file\n";
    exit(1);
}

$originalContent = $content;
$fileReplacements = 0;

// Apply replacements
foreach ($replacements as $search => $replace) {
    $newContent = str_replace($search, $replace, $content);
    if ($newContent !== $content) {
        $count = substr_count($content, $search);
        $fileReplacements += $count;
        $content = $newContent;
        echo "  ✅ Replaced '$search' ($count times)\n";
    }
}

// Write back if changes were made
if ($content !== $originalContent) {
    if (file_put_contents($fullPath, $content)) {
        echo "  💾 Saved $file with $fileReplacements replacements\n";
    } else {
        echo "  ❌ Failed to save $file\n";
        exit(1);
    }
} else {
    echo "  ℹ️  No changes needed for $file\n";
}

echo "\n=== SUMMARY ===\n";
echo "File processed: $file\n";
echo "Total replacements: $fileReplacements\n";

echo "\n✅ Bookmarks blade fix completed at " . date('Y-m-d H:i:s') . "\n";
?>
