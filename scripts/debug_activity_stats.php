<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\User;
use Carbon\Carbon;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Debugging Activity Stats for Member 04...\n\n";

// Find Member 04
$user = User::where('username', 'member04')->first();

if (!$user) {
    echo "❌ User 'member04' not found!\n";
    exit(1);
}

echo "✅ Found user: {$user->username} (ID: {$user->id})\n\n";

// Check threads
echo "📝 THREADS:\n";
$threads = $user->threads()->get();
echo "Total threads: " . $threads->count() . "\n";
foreach ($threads as $thread) {
    echo "  - {$thread->title} (created: {$thread->created_at})\n";
}
echo "\n";

// Check comments
echo "💬 COMMENTS:\n";
$comments = $user->comments()->get();
echo "Total comments: " . $comments->count() . "\n";
foreach ($comments as $comment) {
    echo "  - Comment on thread {$comment->thread_id} (created: {$comment->created_at})\n";
}
echo "\n";

// Check bookmarks
echo "🔖 BOOKMARKS:\n";
$bookmarks = $user->bookmarks()->get();
echo "Total bookmarks: " . $bookmarks->count() . "\n";
foreach ($bookmarks as $bookmark) {
    echo "  - Bookmark {$bookmark->bookmarkable_type} #{$bookmark->bookmarkable_id} (created: {$bookmark->created_at})\n";
}
echo "\n";

// Test today activities
echo "📅 TODAY ACTIVITIES:\n";
$today = Carbon::today();
$tomorrow = Carbon::tomorrow();

$threadsToday = $user->threads()
    ->whereBetween('created_at', [$today, $tomorrow])
    ->count();
    
$commentsToday = $user->comments()
    ->whereBetween('created_at', [$today, $tomorrow])
    ->count();
    
$bookmarksToday = $user->bookmarks()
    ->whereBetween('created_at', [$today, $tomorrow])
    ->count();

echo "Threads today: {$threadsToday}\n";
echo "Comments today: {$commentsToday}\n";
echo "Bookmarks today: {$bookmarksToday}\n";
echo "Total today: " . ($threadsToday + $commentsToday + $bookmarksToday) . "\n\n";

// Test week activities
echo "📅 WEEK ACTIVITIES:\n";
$weekStart = Carbon::now()->startOfWeek();
$weekEnd = Carbon::now()->endOfWeek();

$threadsWeek = $user->threads()
    ->whereBetween('created_at', [$weekStart, $weekEnd])
    ->count();
    
$commentsWeek = $user->comments()
    ->whereBetween('created_at', [$weekStart, $weekEnd])
    ->count();
    
$bookmarksWeek = $user->bookmarks()
    ->whereBetween('created_at', [$weekStart, $weekEnd])
    ->count();

echo "Week start: {$weekStart}\n";
echo "Week end: {$weekEnd}\n";
echo "Threads this week: {$threadsWeek}\n";
echo "Comments this week: {$commentsWeek}\n";
echo "Bookmarks this week: {$bookmarksWeek}\n";
echo "Total this week: " . ($threadsWeek + $commentsWeek + $bookmarksWeek) . "\n\n";

// Test total activities
echo "📊 TOTAL ACTIVITIES:\n";
$totalThreads = $user->threads()->count();
$totalComments = $user->comments()->count();
$totalBookmarks = $user->bookmarks()->count();

echo "Total threads: {$totalThreads}\n";
echo "Total comments: {$totalComments}\n";
echo "Total bookmarks: {$totalBookmarks}\n";
echo "Grand total: " . ($totalThreads + $totalComments + $totalBookmarks) . "\n\n";

// Test activity streak
echo "🔥 ACTIVITY STREAK:\n";
$streak = 0;
$currentDate = Carbon::today();

for ($i = 0; $i < 7; $i++) { // Check last 7 days
    $dayStart = $currentDate->copy()->subDays($i)->startOfDay();
    $dayEnd = $currentDate->copy()->subDays($i)->endOfDay();
    
    $hasActivity = $user->threads()
        ->whereBetween('created_at', [$dayStart, $dayEnd])
        ->exists() ||
        $user->comments()
        ->whereBetween('created_at', [$dayStart, $dayEnd])
        ->exists() ||
        $user->bookmarks()
        ->whereBetween('created_at', [$dayStart, $dayEnd])
        ->exists();
        
    echo "Day -{$i} ({$dayStart->format('Y-m-d')}): " . ($hasActivity ? "✅ Active" : "❌ No activity") . "\n";
    
    if ($hasActivity) {
        $streak++;
    } else {
        if ($i > 0) {
            break;
        }
    }
}

echo "Activity streak: {$streak} days\n\n";

echo "🎯 SUMMARY:\n";
echo "Expected stats:\n";
echo "- Total activities: " . ($totalThreads + $totalComments + $totalBookmarks) . "\n";
echo "- Today activities: " . ($threadsToday + $commentsToday + $bookmarksToday) . "\n";
echo "- Week activities: " . ($threadsWeek + $commentsWeek + $bookmarksWeek) . "\n";
echo "- Activity streak: {$streak} days\n";
