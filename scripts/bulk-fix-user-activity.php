<?php

echo "\n🔧 BULK FIX: user/activity.blade.php\n";
echo "=====================================\n\n";

$file_path = 'resources/views/user/activity.blade.php';
$content = file_get_contents($file_path);

if (!$content) {
    echo "❌ Error: Cannot read $file_path\n";
    exit;
}

echo "🔍 Original file analysis:\n";
echo "- Path: $file_path\n";
echo "- Size: " . strlen($content) . " bytes\n\n";

// Định nghĩa mapping cho các key cần thay thế
$replacements = [
    // Activity descriptions
    "__('messages.activity_desc')" => "__('activity.activity_desc')",

    // Statistics labels
    "__('messages.total_activities')" => "__('activity.total_activities')",
    "__('messages.today')" => "__('activity.today')",
    "__('messages.this_week')" => "__('activity.this_week')",
    "__('messages.activity_streak')" => "__('activity.activity_streak')",

    // Activity types
    "__('messages.all_activities')" => "__('activity.all_activities')",
    "__('messages.thread_created')" => "__('activity.thread_created')",
    "__('messages.comment_posted')" => "__('activity.comment_posted')",
    "__('messages.thread_bookmarked')" => "__('activity.thread_bookmarked')",
    "__('messages.thread_rated')" => "__('activity.thread_rated')",
    "__('messages.user_followed')" => "__('activity.user_followed')",

    // Time periods
    "__('messages.time_period')" => "__('activity.time_period')",
    "__('messages.all_time')" => "__('activity.all_time')",
    "__('messages.this_month')" => "__('activity.this_month')",
    "__('messages.yesterday')" => "__('activity.yesterday')",

    // Forum and search
    "__('messages.forum')" => "__('activity.forum')",
    "__('messages.all_forums')" => "__('activity.all_forums')",
    "__('messages.search')" => "__('activity.search')",
    "__('messages.search_activities')" => "__('activity.search_activities')",

    // Actions
    "__('messages.view_comment')" => "__('activity.view_comment')",

    // Empty states
    "__('messages.no_activities_yet')" => "__('activity.no_activities_yet')",
    "__('messages.no_activities_desc')" => "__('activity.no_activities_desc')",
    "__('messages.start_participating')" => "__('activity.start_participating')",
];

echo "🔄 Performing bulk replacements:\n";
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

echo "\n✅ Bulk replacement completed!\n";

?>
