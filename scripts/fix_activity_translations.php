<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔧 Fixing Activity Page Translation Keys...\n\n";

// Define missing activity translation keys
$activityTranslations = [
    // Main activity keys
    'activity.recent_activity' => [
        'vi' => 'Hoạt động gần đây',
        'en' => 'Recent Activity'
    ],
    'activity.no_activities' => [
        'vi' => 'Chưa có hoạt động',
        'en' => 'No Activities'
    ],
    'activity.no_activities_desc' => [
        'vi' => 'Bạn chưa có hoạt động nào. Hãy bắt đầu tham gia thảo luận để theo dõi hoạt động của mình.',
        'en' => 'You have no activities yet. Start participating in discussions to track your activities.'
    ],
    'activity.start_activity' => [
        'vi' => 'Bắt đầu hoạt động',
        'en' => 'Start Activity'
    ],
    'activity.view_details' => [
        'vi' => 'Xem chi tiết',
        'en' => 'View Details'
    ],
    
    // Stats labels
    'activity.total_activities' => [
        'vi' => 'Tổng hoạt động',
        'en' => 'Total Activities'
    ],
    'activity.today' => [
        'vi' => 'Hôm nay',
        'en' => 'Today'
    ],
    'activity.this_week' => [
        'vi' => 'Tuần này',
        'en' => 'This Week'
    ],
    'activity.this_month' => [
        'vi' => 'Tháng này',
        'en' => 'This Month'
    ],
    'activity.all_time' => [
        'vi' => 'Tất cả thời gian',
        'en' => 'All Time'
    ],
    'activity.activity_streak' => [
        'vi' => 'Chuỗi hoạt động',
        'en' => 'Activity Streak'
    ],
    'activity.activity_desc' => [
        'vi' => 'Theo dõi tất cả hoạt động của bạn trong cộng đồng MechaMap',
        'en' => 'Track all your activities in the MechaMap community'
    ],
    
    // Activity types
    'activity.thread_created' => [
        'vi' => 'Tạo chủ đề',
        'en' => 'Thread Created'
    ],
    'activity.comment_posted' => [
        'vi' => 'Đăng bình luận',
        'en' => 'Comment Posted'
    ],
    'activity.thread_bookmarked' => [
        'vi' => 'Đánh dấu chủ đề',
        'en' => 'Thread Bookmarked'
    ],
    'activity.thread_liked' => [
        'vi' => 'Thích chủ đề',
        'en' => 'Liked Thread'
    ],
    'activity.comment_liked' => [
        'vi' => 'Thích bình luận',
        'en' => 'Liked Comment'
    ],
    'activity.showcase_created' => [
        'vi' => 'Tạo showcase',
        'en' => 'Created Showcase'
    ],
    'activity.showcase_liked' => [
        'vi' => 'Thích showcase',
        'en' => 'Liked Showcase'
    ],
    'activity.profile_updated' => [
        'vi' => 'Cập nhật hồ sơ',
        'en' => 'Updated Profile'
    ],
    'activity.user_followed' => [
        'vi' => 'Theo dõi người dùng',
        'en' => 'Followed User'
    ]
];

$addedCount = 0;
$skippedCount = 0;

foreach ($activityTranslations as $key => $translations) {
    echo "Processing key: {$key}\n";
    
    // Check if Vietnamese translation exists
    $existingVi = DB::table('translations')
        ->where('key', $key)
        ->where('locale', 'vi')
        ->first();
    
    // Check if English translation exists
    $existingEn = DB::table('translations')
        ->where('key', $key)
        ->where('locale', 'en')
        ->first();
    
    $now = now();
    
    // Add Vietnamese translation if not exists
    if (!$existingVi) {
        DB::table('translations')->insert([
            'key' => $key,
            'content' => $translations['vi'],
            'locale' => 'vi',
            'group_name' => 'activity',
            'namespace' => null,
            'is_active' => 1,
            'created_by' => null,
            'updated_by' => null,
            'created_at' => $now,
            'updated_at' => $now
        ]);
        echo "  ✅ Added Vietnamese: {$translations['vi']}\n";
        $addedCount++;
    } else {
        echo "  ⏭️  Vietnamese already exists: {$existingVi->content}\n";
        $skippedCount++;
    }
    
    // Add English translation if not exists
    if (!$existingEn) {
        DB::table('translations')->insert([
            'key' => $key,
            'content' => $translations['en'],
            'locale' => 'en',
            'group_name' => 'activity',
            'namespace' => null,
            'is_active' => 1,
            'created_by' => null,
            'updated_by' => null,
            'created_at' => $now,
            'updated_at' => $now
        ]);
        echo "  ✅ Added English: {$translations['en']}\n";
        $addedCount++;
    } else {
        echo "  ⏭️  English already exists: {$existingEn->content}\n";
        $skippedCount++;
    }
    
    echo "\n";
}

echo "🎉 Activity Translation Fix Complete!\n";
echo "✅ Added: {$addedCount} translations\n";
echo "⏭️  Skipped: {$skippedCount} existing translations\n\n";

echo "🔄 Clearing translation cache...\n";
try {
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    echo "✅ Cache cleared successfully!\n";
} catch (Exception $e) {
    echo "⚠️  Cache clear failed: " . $e->getMessage() . "\n";
}

echo "\n🚀 Activity page translations are now ready!\n";
echo "📝 Visit: https://mechamap.test/dashboard/activity to see the changes\n";
