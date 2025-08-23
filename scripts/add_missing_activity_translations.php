<?php

/**
 * Script to add missing activity translation keys to MechaMap database
 *
 * Usage: php scripts/add_missing_activity_translations.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Load Laravel application
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔧 Adding missing activity translation keys to MechaMap database...\n\n";

// Define missing translation keys with Vietnamese and English content
$missingTranslations = [
    [
        'key' => 'activity.showcase_created_from_thread',
        'vi' => 'Tạo showcase từ chủ đề',
        'en' => 'Created showcase from thread'
    ],
    [
        'key' => 'activity.comment_liked',
        'vi' => 'Thích bình luận',
        'en' => 'Liked comment'
    ],
    [
        'key' => 'activity.showcase_created',
        'vi' => 'Tạo showcase',
        'en' => 'Created showcase'
    ],
    [
        'key' => 'activity.showcase_liked',
        'vi' => 'Thích showcase',
        'en' => 'Liked showcase'
    ],
    [
        'key' => 'activity.thread_liked',
        'vi' => 'Thích chủ đề',
        'en' => 'Liked thread'
    ],
    [
        'key' => 'activity.thread_saved',
        'vi' => 'Lưu chủ đề',
        'en' => 'Saved thread'
    ],
    // Additional missing keys found in activity display
    [
        'key' => 'activity.comment_posted',
        'vi' => 'Đăng bình luận',
        'en' => 'Comment Posted'
    ],
    [
        'key' => 'activity.thread_bookmarked',
        'vi' => 'Đánh dấu chủ đề',
        'en' => 'Thread Bookmarked'
    ],
    [
        'key' => 'activity.user_followed',
        'vi' => 'Theo dõi người dùng',
        'en' => 'User Followed'
    ]
];

$addedCount = 0;
$skippedCount = 0;

foreach ($missingTranslations as $translation) {
    $key = $translation['key'];

    echo "Processing: {$key}\n";

    // Check if Vietnamese translation already exists
    $existingVi = DB::table('translations')
        ->where('key', $key)
        ->where('locale', 'vi')
        ->first();

    // Check if English translation already exists
    $existingEn = DB::table('translations')
        ->where('key', $key)
        ->where('locale', 'en')
        ->first();

    $now = now();

    // Add Vietnamese translation if not exists
    if (!$existingVi) {
        DB::table('translations')->insert([
            'key' => $key,
            'content' => $translation['vi'],
            'locale' => 'vi',
            'group_name' => 'activity',
            'namespace' => null,
            'is_active' => 1,
            'created_by' => null,
            'updated_by' => null,
            'created_at' => $now,
            'updated_at' => $now
        ]);
        echo "  ✅ Added Vietnamese: {$translation['vi']}\n";
        $addedCount++;
    } else {
        echo "  ⏭️  Vietnamese already exists: {$existingVi->content}\n";
        $skippedCount++;
    }

    // Add English translation if not exists
    if (!$existingEn) {
        DB::table('translations')->insert([
            'key' => $key,
            'content' => $translation['en'],
            'locale' => 'en',
            'group_name' => 'activity',
            'namespace' => null,
            'is_active' => 1,
            'created_by' => null,
            'updated_by' => null,
            'created_at' => $now,
            'updated_at' => $now
        ]);
        echo "  ✅ Added English: {$translation['en']}\n";
        $addedCount++;
    } else {
        echo "  ⏭️  English already exists: {$existingEn->content}\n";
        $skippedCount++;
    }

    echo "\n";
}

echo "🎉 Script completed!\n";
echo "📊 Summary:\n";
echo "   - Added: {$addedCount} translations\n";
echo "   - Skipped: {$skippedCount} translations (already exist)\n";
echo "   - Total processed: " . count($missingTranslations) * 2 . " translation entries\n\n";

echo "🔄 You may need to clear cache:\n";
echo "   php artisan cache:clear\n";
echo "   php artisan config:clear\n\n";
