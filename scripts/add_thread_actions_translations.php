<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel application
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "\n🔧 ADDING THREAD ACTIONS TRANSLATION KEYS\n";
echo "==========================================\n\n";

// Define translation keys for thread actions
$translationKeys = [
    // Bookmark actions
    'forums.actions.bookmark' => [
        'vi' => 'Đánh dấu',
        'en' => 'Bookmark',
        'group' => 'forums'
    ],
    'forums.actions.bookmarked' => [
        'vi' => 'Đã đánh dấu',
        'en' => 'Bookmarked',
        'group' => 'forums'
    ],
    'forums.actions.bookmark_add' => [
        'vi' => 'Thêm đánh dấu',
        'en' => 'Add bookmark',
        'group' => 'forums'
    ],
    'forums.actions.bookmark_remove' => [
        'vi' => 'Xóa đánh dấu',
        'en' => 'Remove bookmark',
        'group' => 'forums'
    ],

    // Follow actions
    'forums.actions.follow' => [
        'vi' => 'Theo dõi',
        'en' => 'Follow',
        'group' => 'forums'
    ],
    'forums.actions.following' => [
        'vi' => 'Đang theo dõi',
        'en' => 'Following',
        'group' => 'forums'
    ],
    'forums.actions.follow_thread' => [
        'vi' => 'Theo dõi chủ đề',
        'en' => 'Follow thread',
        'group' => 'forums'
    ],
    'forums.actions.unfollow_thread' => [
        'vi' => 'Bỏ theo dõi chủ đề',
        'en' => 'Unfollow thread',
        'group' => 'forums'
    ],

    // Status labels
    'forums.status.pinned' => [
        'vi' => 'Đã ghim',
        'en' => 'Pinned',
        'group' => 'forums'
    ],
    'forums.status.locked' => [
        'vi' => 'Đã khóa',
        'en' => 'Locked',
        'group' => 'forums'
    ],

    // Meta information
    'forums.meta.views' => [
        'vi' => 'lượt xem',
        'en' => 'views',
        'group' => 'forums'
    ],
    'forums.meta.replies' => [
        'vi' => 'phản hồi',
        'en' => 'replies',
        'group' => 'forums'
    ],

    // Messages
    'forums.messages.bookmark_added' => [
        'vi' => 'Đã lưu bài viết.',
        'en' => 'Thread bookmarked.',
        'group' => 'forums'
    ],
    'forums.messages.bookmark_removed' => [
        'vi' => 'Đã bỏ lưu bài viết.',
        'en' => 'Bookmark removed.',
        'group' => 'forums'
    ],
    'forums.messages.follow_added' => [
        'vi' => 'Đã theo dõi chủ đề.',
        'en' => 'Following thread.',
        'group' => 'forums'
    ],
    'forums.messages.follow_removed' => [
        'vi' => 'Đã bỏ theo dõi chủ đề.',
        'en' => 'Unfollowed thread.',
        'group' => 'forums'
    ],
    'forums.messages.request_error' => [
        'vi' => 'Có lỗi xảy ra khi gửi yêu cầu. Vui lòng thử lại.',
        'en' => 'An error occurred while processing the request. Please try again.',
        'group' => 'forums'
    ],
];

$totalAdded = 0;
$totalSkipped = 0;
$totalErrors = 0;

foreach ($translationKeys as $key => $data) {
    echo "📝 Processing key: {$key}\n";

    try {
        // Check if key already exists
        $existingVi = DB::table('translations')
            ->where('key', $key)
            ->where('locale', 'vi')
            ->first();

        $existingEn = DB::table('translations')
            ->where('key', $key)
            ->where('locale', 'en')
            ->first();

        if ($existingVi && $existingEn) {
            echo "   ⏭️ Skipped: Key already exists\n";
            $totalSkipped++;
            continue;
        }

        // Add Vietnamese translation
        if (!$existingVi) {
            DB::table('translations')->insert([
                'key' => $key,
                'content' => $data['vi'],
                'locale' => 'vi',
                'group_name' => $data['group'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            echo "   ✅ Added VI: {$data['vi']}\n";
        }

        // Add English translation
        if (!$existingEn) {
            DB::table('translations')->insert([
                'key' => $key,
                'content' => $data['en'],
                'locale' => 'en',
                'group_name' => $data['group'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            echo "   ✅ Added EN: {$data['en']}\n";
        }

        $totalAdded++;

    } catch (\Exception $e) {
        echo "   ❌ Error: {$e->getMessage()}\n";
        $totalErrors++;
    }

    echo "\n";
}

echo "\n📊 SUMMARY\n";
echo "==========\n";
echo "✅ Added: {$totalAdded} keys\n";
echo "⏭️ Skipped: {$totalSkipped} keys\n";
echo "❌ Errors: {$totalErrors} keys\n";

if ($totalErrors === 0) {
    echo "\n🎉 All translation keys processed successfully!\n";
} else {
    echo "\n⚠️ Some errors occurred. Please check the output above.\n";
}

echo "\n";
