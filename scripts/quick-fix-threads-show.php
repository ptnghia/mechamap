<?php

echo "\n🔧 QUICK FIX: threads/show.blade.php (3 remaining issues)\n";
echo "===============================================================\n\n";

$file_path = 'resources/views/threads/show.blade.php';
$content = file_get_contents($file_path);

if (!$content) {
    echo "❌ Error: Cannot read $file_path\n";
    exit;
}

echo "🔍 Original file analysis:\n";
echo "- Path: $file_path\n";
echo "- Size: " . strlen($content) . " bytes\n\n";

// Định nghĩa mapping cho 3 key cần thay thế
$replacements = [
    "__('forms.upload.attach_images_optional')" => "__('thread.attach_images_optional')",
    "__('forms.related.related_topics')" => "__('thread.related_topics')",
    "__('thread.form_submission_error')" => "__('thread.form_submission_error')",
];

echo "🔄 Performing quick replacements:\n";
$replacement_count = 0;

foreach ($replacements as $old => $new) {
    $old_content = $content;
    $content = str_replace($old, $new, $content);

    if ($content !== $old_content) {
        $count = substr_count($old_content, $old);
        echo "✅ $old → $new ($count occurrences)\n";
        $replacement_count += $count;
    }
}

echo "\n📊 Replacement Summary:\n";
echo "Total replacements made: $replacement_count\n\n";

// Lưu file đã được sửa
file_put_contents($file_path, $content);
echo "💾 File saved: $file_path\n";

echo "\n✅ Quick fix completed!\n";

?>
