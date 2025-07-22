<?php

/**
 * FIX REMAINING FOLLOWING FILES
 * Sửa hardcoded strings trong following/followers.blade.php và following/index.blade.php
 */

echo "=== FIXING REMAINING FOLLOWING FILES ===\n\n";

// Mapping of hardcoded strings to translation keys
$replacements = [
    // Common following keys
    "{{ __('Following') }}" => "{{ __('following.following') }}",
    "{{ __('Followers') }}" => "{{ __('following.followers') }}",
    "{{ __('Followed Threads') }}" => "{{ __('following.followed_threads') }}",
    "{{ __('Participated Discussions') }}" => "{{ __('following.participated_discussions') }}",
    "{{ __('Filters') }}" => "{{ __('following.filters') }}",
    "{{ __('All Forums') }}" => "{{ __('following.all_forums') }}",
    "{{ __('Unfollow') }}" => "{{ __('following.unfollow') }}",
    "{{ __('Follow') }}" => "{{ __('following.follow') }}",
    
    // Followers specific
    "{{ __('People Following You') }}" => "{{ __('following.people_following_you') }}",
    "{{ __('You don\\'t have any followers yet.') }}" => "{{ __('following.no_followers_yet') }}",
    "{{ __('When someone follows you, they will appear here.') }}" => "{{ __('following.when_someone_follows_you') }}",
    
    // Following index specific
    "{{ __('People You Follow') }}" => "{{ __('following.people_you_follow') }}",
    "{{ __('You are not following anyone yet.') }}" => "{{ __('following.not_following_anyone_yet') }}",
    "{{ __('Follow other users to see their updates in your feed.') }}" => "{{ __('following.follow_other_users_to_see_updates') }}",
];

// Files to process
$files = [
    'resources/views/following/followers.blade.php',
    'resources/views/following/index.blade.php',
];

$totalReplacements = 0;

foreach ($files as $file) {
    $fullPath = __DIR__ . '/' . $file;
    
    if (!file_exists($fullPath)) {
        echo "❌ File not found: $file\n";
        continue;
    }
    
    echo "📄 Processing: $file\n";
    
    $content = file_get_contents($fullPath);
    if ($content === false) {
        echo "❌ Failed to read $file\n";
        continue;
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
            $totalReplacements += $fileReplacements;
        } else {
            echo "  ❌ Failed to save $file\n";
        }
    } else {
        echo "  ℹ️  No changes needed for $file\n";
    }
    
    echo "\n";
}

echo "=== SUMMARY ===\n";
echo "Files processed: " . count($files) . "\n";
echo "Total replacements: $totalReplacements\n";

echo "\n✅ Following files fix completed at " . date('Y-m-d H:i:s') . "\n";
echo "\nNext: Run scan to verify all 100% missing files are now fixed.\n";
?>
