<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🚀 **MechaMap Database Performance Optimization Test**\n";
echo str_repeat("=", 60) . "\n\n";

// 1. Kiểm tra tổng quan database
echo "📊 **Tổng quan Database hiện tại:**\n";
$tables = ['users', 'threads', 'comments', 'showcases', 'reactions', 'polls', 'alerts'];
foreach ($tables as $table) {
    if (Schema::hasTable($table)) {
        $count = DB::table($table)->count();
        echo "  • {$table}: " . number_format($count) . " records\n";
    }
}
echo "\n";

// 2. Kiểm tra indexes đã tạo
echo "🔍 **Indexes đã được tạo:**\n";
$tablesToCheck = ['threads', 'comments', 'showcases', 'reactions', 'thread_ratings', 'thread_bookmarks'];

foreach ($tablesToCheck as $table) {
    if (Schema::hasTable($table)) {
        echo "\n📋 **Bảng: {$table}**\n";
        $indexes = DB::select("SHOW INDEX FROM {$table}");

        $indexGroups = [];
        foreach ($indexes as $index) {
            if (!isset($indexGroups[$index->Key_name])) {
                $indexGroups[$index->Key_name] = [];
            }
            $indexGroups[$index->Key_name][] = $index->Column_name;
        }

        foreach ($indexGroups as $indexName => $columns) {
            if ($indexName !== 'PRIMARY') {
                echo "  ✅ {$indexName}: " . implode(', ', $columns) . "\n";
            }
        }
    }
}

echo "\n" . str_repeat("=", 60) . "\n";

// 3. Performance Testing
echo "⚡ **Performance Testing:**\n\n";

// Test 1: Thread search performance
echo "🔎 **Test 1: Thread Search Performance**\n";
$start = microtime(true);
$results = DB::table('threads')
    ->where('title', 'LIKE', '%CAD%')
    ->orderBy('created_at', 'desc')
    ->limit(10)
    ->get();
$duration1 = (microtime(true) - $start) * 1000;
echo "  Tìm kiếm threads với keyword 'CAD': {$duration1:.2f}ms (" . count($results) . " kết quả)\n";

// Test 2: Forum trending threads
echo "\n📈 **Test 2: Trending Threads Query**\n";
$start = microtime(true);
$trending = DB::table('threads')
    ->select('threads.*', DB::raw('(threads.views_count + threads.likes_count * 2) as trend_score'))
    ->where('threads.created_at', '>=', now()->subDays(7))
    ->orderBy('trend_score', 'desc')
    ->limit(10)
    ->get();
$duration2 = (microtime(true) - $start) * 1000;
echo "  Trending threads (7 ngày gần đây): {$duration2:.2f}ms (" . count($trending) . " kết quả)\n";

// Test 3: Comment threading performance
echo "\n💬 **Test 3: Comment Threading Performance**\n";
$start = microtime(true);
$threadId = DB::table('threads')->first()->id ?? 1;
$comments = DB::table('comments')
    ->where('thread_id', $threadId)
    ->orderBy('created_at', 'asc')
    ->get();
$duration3 = (microtime(true) - $start) * 1000;
echo "  Load comments cho thread #{$threadId}: {$duration3:.2f}ms (" . count($comments) . " comments)\n";

// Test 4: User activity dashboard
echo "\n👤 **Test 4: User Activity Dashboard**\n";
$start = microtime(true);
$userId = DB::table('users')->where('role', 'member')->first()->id ?? 1;
$userActivity = [
    'threads' => DB::table('threads')->where('user_id', $userId)->count(),
    'comments' => DB::table('comments')->where('user_id', $userId)->count(),
    'ratings' => DB::table('thread_ratings')->where('user_id', $userId)->count(),
    'bookmarks' => DB::table('thread_bookmarks')->where('user_id', $userId)->count(),
];
$duration4 = (microtime(true) - $start) * 1000;
echo "  User activity cho user #{$userId}: {$duration4:.2f}ms\n";
echo "  • Threads: " . $userActivity['threads'] . "\n";
echo "  • Comments: " . $userActivity['comments'] . "\n";
echo "  • Ratings: " . $userActivity['ratings'] . "\n";
echo "  • Bookmarks: " . $userActivity['bookmarks'] . "\n";

// Test 5: Showcase polymorphic queries
echo "\n🎯 **Test 5: Showcase Polymorphic Queries**\n";
$start = microtime(true);
$showcases = DB::table('showcases')
    ->join('users', 'showcases.user_id', '=', 'users.id')
    ->select('showcases.*', 'users.name as user_name')
    ->orderBy('showcases.created_at', 'desc')
    ->limit(10)
    ->get();
$duration5 = (microtime(true) - $start) * 1000;
echo "  Showcase listing với user info: {$duration5:.2f}ms (" . count($showcases) . " showcases)\n";

// Test 6: Complex aggregation queries
echo "\n📊 **Test 6: Complex Aggregation Queries**\n";
$start = microtime(true);
$stats = DB::table('threads')
    ->select([
        DB::raw('COUNT(*) as total_threads'),
        DB::raw('AVG(views_count) as avg_views'),
        DB::raw('AVG(likes_count) as avg_likes'),
        DB::raw('SUM(CASE WHEN is_featured = 1 THEN 1 ELSE 0 END) as featured_count'),
        DB::raw('SUM(CASE WHEN is_sticky = 1 THEN 1 ELSE 0 END) as sticky_count')
    ])
    ->first();
$duration6 = (microtime(true) - $start) * 1000;
echo "  Thread statistics aggregation: {$duration6:.2f}ms\n";
echo "  • Total threads: " . number_format($stats->total_threads) . "\n";
echo "  • Average views: " . number_format($stats->avg_views, 1) . "\n";
echo "  • Average likes: " . number_format($stats->avg_likes, 1) . "\n";
echo "  • Featured threads: " . $stats->featured_count . "\n";
echo "  • Sticky threads: " . $stats->sticky_count . "\n";

// Summary
echo "\n" . str_repeat("=", 60) . "\n";
echo "📋 **Performance Summary:**\n";
$totalTime = $duration1 + $duration2 + $duration3 + $duration4 + $duration5 + $duration6;
echo "  • Thread Search: {$duration1:.2f}ms\n";
echo "  • Trending Query: {$duration2:.2f}ms\n";
echo "  • Comment Threading: {$duration3:.2f}ms\n";
echo "  • User Activity: {$duration4:.2f}ms\n";
echo "  • Showcase Queries: {$duration5:.2f}ms\n";
echo "  • Aggregation Stats: {$duration6:.2f}ms\n";
echo "  **Total Test Time: {$totalTime:.2f}ms**\n\n";

// Performance rating
if ($totalTime < 50) {
    echo "🚀 **Excellent Performance!** Database indexes hoạt động tối ưu.\n";
} elseif ($totalTime < 100) {
    echo "✅ **Good Performance!** Database đã được tối ưu hóa tốt.\n";
} elseif ($totalTime < 200) {
    echo "⚠️ **Acceptable Performance.** Có thể cần thêm optimization.\n";
} else {
    echo "🐌 **Poor Performance.** Cần review lại indexes và queries.\n";
}

echo "\n🎉 **Database Performance Optimization Test Complete!**\n";

?>
