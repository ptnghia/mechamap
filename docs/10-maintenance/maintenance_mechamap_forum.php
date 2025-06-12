<?php

/**
 * MechaMap Forum Maintenance & Optimization Script
 *
 * This script performs regular maintenance tasks to keep the forum optimized
 * and running smoothly for the mechanical engineering community.
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔧 MechaMap Forum Maintenance Script\n";
echo "====================================\n\n";

try {
    echo "📊 Updating Forum Statistics...\n";

    // Update forum thread and post counts
    $forums = App\Models\Forum::all();
    $updatedForums = 0;

    foreach ($forums as $forum) {
        $threadCount = $forum->threads()->count();
        $postCount = App\Models\Comment::whereIn('thread_id', $forum->threads()->pluck('id'))->count();
        $lastActivity = $forum->threads()->max('last_activity_at') ?: $forum->threads()->max('created_at');

        $forum->update([
            'thread_count' => $threadCount,
            'post_count' => $postCount,
            'last_activity_at' => $lastActivity,
        ]);

        $updatedForums++;
    }

    echo "  ✅ Updated {$updatedForums} forum statistics\n";

    echo "\n🔄 Updating Thread Statistics...\n";

    // Update thread comment counts and last activity
    $threads = App\Models\Thread::all();
    $updatedThreads = 0;

    foreach ($threads as $thread) {
        $commentCount = $thread->comments()->count();
        $lastActivity = $thread->comments()->max('created_at') ?: $thread->created_at;

        $thread->update([
            'last_activity_at' => $lastActivity,
        ]);

        // Update the reply_count if the column exists
        if (Schema::hasColumn('threads', 'reply_count')) {
            $thread->update(['reply_count' => $commentCount]);
        }

        $updatedThreads++;
    }

    echo "  ✅ Updated {$updatedThreads} thread statistics\n";

    echo "\n🏷️ Cleaning Up Tags...\n";

    // Remove unused tags
    $unusedTags = App\Models\Tag::doesntHave('threads')->get();
    $deletedTags = $unusedTags->count();

    if ($deletedTags > 0) {
        foreach ($unusedTags as $tag) {
            $tag->delete();
        }
        echo "  ✅ Removed {$deletedTags} unused tags\n";
    } else {
        echo "  ✅ No unused tags found\n";
    }

    echo "\n🧹 Database Optimization...\n";

    // Optimize database tables
    $tables = ['categories', 'forums', 'threads', 'comments', 'tags', 'thread_tag'];
    foreach ($tables as $table) {
        DB::statement("OPTIMIZE TABLE {$table}");
    }

    echo "  ✅ Optimized database tables\n";

    echo "\n📈 Performance Report:\n";
    echo "=====================\n";

    // Generate performance report
    $stats = [
        'Users' => App\Models\User::count(),
        'Categories' => App\Models\Category::count(),
        'Forums' => App\Models\Forum::count(),
        'Threads' => App\Models\Thread::count(),
        'Comments' => App\Models\Comment::count(),
        'Tags' => App\Models\Tag::count(),
    ];

    foreach ($stats as $item => $count) {
        echo sprintf("  %-15s: %d\n", $item, $count);
    }

    // Test query performance
    echo "\n⚡ Query Performance Test:\n";
    echo "==========================\n";

    $start = microtime(true);
    $complexQuery = App\Models\Category::with(['forums.threads.user', 'forums.threads.comments'])->get();
    $end = microtime(true);
    $queryTime = round(($end - $start) * 1000, 2);

    echo "  Complex query execution: {$queryTime}ms\n";

    if ($queryTime < 100) {
        echo "  ✅ Performance: EXCELLENT\n";
    } elseif ($queryTime < 500) {
        echo "  ⚠️ Performance: GOOD\n";
    } else {
        echo "  ❌ Performance: NEEDS OPTIMIZATION\n";
    }

    echo "\n🎯 Forum Health Check:\n";
    echo "======================\n";

    // Health checks
    $approvedThreads = App\Models\Thread::where('moderation_status', 'approved')->count();
    $totalThreads = App\Models\Thread::count();
    $approvalRate = $totalThreads > 0 ? round(($approvedThreads / $totalThreads) * 100, 1) : 0;

    $threadsWithComments = App\Models\Thread::has('comments')->count();
    $engagementRate = $totalThreads > 0 ? round(($threadsWithComments / $totalThreads) * 100, 1) : 0;

    echo "  Approval Rate: {$approvalRate}%\n";
    echo "  Engagement Rate: {$engagementRate}%\n";
    echo "  Average Comments/Thread: " . round(App\Models\Comment::count() / max($totalThreads, 1), 1) . "\n";

    // Check for mechanical engineering content
    $mechanicalKeywords = ['CNC', 'SolidWorks', 'ANSYS', 'bánh răng', 'CAD', 'PLC'];
    $mechanicalContentCount = 0;

    foreach ($mechanicalKeywords as $keyword) {
        $count = App\Models\Thread::where('title', 'like', "%{$keyword}%")
            ->orWhere('content', 'like', "%{$keyword}%")
            ->count();
        $mechanicalContentCount += $count;
    }

    $technicalContentRate = $totalThreads > 0 ? round(($mechanicalContentCount / $totalThreads) * 100, 1) : 0;
    echo "  Technical Content: {$technicalContentRate}%\n";

    echo "\n✅ Maintenance Completed Successfully!\n";
    echo "=====================================\n";
    echo "Next maintenance recommended in 24 hours.\n";
    echo "For issues, check logs or contact development team.\n\n";

} catch (Exception $e) {
    echo "❌ Maintenance Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
