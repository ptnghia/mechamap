<?php

echo "🔧 BULK REPLACE: Converting messages.* to dashboard.* in user/dashboard-member.blade.php\n";
echo "=======================================================================\n\n";

$file_path = 'resources/views/user/dashboard-member.blade.php';
$content = file_get_contents($file_path);

if (!$content) {
    echo "❌ Error: Cannot read $file_path\n";
    exit;
}

// Define replacements for messages.*
$replacements = [
    "__('messages.welcome_back')" => "__('dashboard.welcome_back')",
    "__('messages.level')" => "__('dashboard.level')",
    "__('messages.activity_level')" => "__('dashboard.activity_level')",
    "__('messages.reputation_score')" => "__('dashboard.reputation_score')",
    "__('messages.threads_created')" => "__('dashboard.threads_created')",
    "__('messages.comments_count')" => "__('dashboard.comments_count')",
    "__('messages.bookmarks')" => "__('dashboard.bookmarks')",
    "__('messages.avg_rating')" => "__('dashboard.avg_rating')",
    "__('messages.view_all')" => "__('dashboard.view_all')",
    "__('messages.published')" => "__('dashboard.published')",
    "__('messages.pending')" => "__('dashboard.pending')",
    "__('messages.no_threads_yet')" => "__('dashboard.no_threads_yet')",
    "__('messages.create_first_thread')" => "__('dashboard.create_first_thread')",
    "__('messages.achievements')" => "__('dashboard.achievements')",
    "__('messages.forum_participation')" => "__('dashboard.forum_participation')",
    "__('messages.threads')" => "__('dashboard.threads')",
    "__('messages.comments')" => "__('dashboard.comments')",
    "__('messages.total_contributions')" => "__('dashboard.total_contributions')",
    "__('messages.upgrade_to_senior')" => "__('dashboard.upgrade_to_senior')",
    "__('messages.senior_member_benefits_desc')" => "__('dashboard.senior_member_benefits_desc')",
    "__('messages.requirements')" => "__('dashboard.requirements')",
    "__('messages.create_10_threads')" => "__('dashboard.create_10_threads')",
    "__('messages.post_50_comments')" => "__('dashboard.post_50_comments')",
    "__('messages.maintain_4_star_rating')" => "__('dashboard.maintain_4_star_rating')",
    "__('messages.upgrade_now')" => "__('dashboard.upgrade_now')",
    "__('messages.keep_contributing')" => "__('dashboard.keep_contributing')",
];

// Define replacements for auth.*
$auth_replacements = [
    "__('auth.member_role')" => "__('dashboard.member_role')",
    "__('auth.member_role_desc')" => "__('dashboard.member_role_desc')",
];

$all_replacements = array_merge($replacements, $auth_replacements);
$changes_made = 0;

foreach ($all_replacements as $search => $replace) {
    if (strpos($content, $search) !== false) {
        $content = str_replace($search, $replace, $content);
        $changes_made++;
        echo "✅ Replaced: $search → $replace\n";
    }
}

// Write the updated content back to file
file_put_contents($file_path, $content);

echo "\n📊 SUMMARY:\n";
echo "===========\n";
echo "Total replacements made: $changes_made\n";
echo "File updated successfully!\n";

?>
