<?php

echo "🔧 BULK REPLACE: Converting messages.* to ratings.* in user/ratings.blade.php\n";
echo "=================================================================\n\n";

$file_path = 'resources/views/user/ratings.blade.php';
$content = file_get_contents($file_path);

if (!$content) {
    echo "❌ Error: Cannot read $file_path\n";
    exit;
}

// Define replacements
$replacements = [
    "__('messages.ratings_distribution')" => "__('ratings.ratings_distribution')",
    "__('messages.ratings_given_distribution')" => "__('ratings.ratings_given_distribution')",
    "__('messages.ratings_received_distribution')" => "__('ratings.ratings_received_distribution')",
    "__('messages.ratings_given')" => "__('ratings.ratings_given')",
    "__('messages.ratings_received')" => "__('ratings.ratings_received')",
    "__('messages.rating_value')" => "__('ratings.rating_value')",
    "__('messages.all_ratings')" => "__('ratings.all_ratings')",
    "__('messages.forum')" => "__('ratings.forum')",
    "__('messages.all_forums')" => "__('ratings.all_forums')",
    "__('messages.time_period')" => "__('ratings.time_period')",
    "__('messages.all_time')" => "__('ratings.all_time')",
    "__('messages.this_week')" => "__('ratings.this_week')",
    "__('messages.this_month')" => "__('ratings.this_month')",
    "__('messages.this_year')" => "__('ratings.this_year')",
    "__('messages.search')" => "__('ratings.search')",
    "__('messages.search_threads')" => "__('ratings.search_threads')",
    "__('messages.view_thread')" => "__('ratings.view_thread')",
    "__('messages.edit_rating')" => "__('ratings.edit_rating')",
    "__('messages.no_ratings_given_yet')" => "__('ratings.no_ratings_given_yet')",
    "__('messages.no_ratings_given_desc')" => "__('ratings.no_ratings_given_desc')",
    "__('messages.browse_threads')" => "__('ratings.browse_threads')",
    "__('messages.rated_by')" => "__('ratings.rated_by')",
    "__('messages.view_profile')" => "__('ratings.view_profile')",
    "__('messages.no_ratings_received_yet')" => "__('ratings.no_ratings_received_yet')",
    "__('messages.no_ratings_received_desc')" => "__('ratings.no_ratings_received_desc')",
    "__('messages.create_thread')" => "__('ratings.create_thread')",
];

$changes_made = 0;

foreach ($replacements as $search => $replace) {
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
