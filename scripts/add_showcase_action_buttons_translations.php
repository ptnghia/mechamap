<?php

/**
 * Add Translation Keys for Showcase Action Buttons
 * Thêm các translation keys cho nút Lưu và Theo dõi showcase
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔧 ADDING SHOWCASE ACTION BUTTONS TRANSLATION KEYS\n";
echo "==================================================\n\n";

// Translation keys cho Showcase Action Buttons
$translationKeys = [
    // Bookmark Actions
    'ui.actions.bookmark' => [
        'vi' => 'Lưu',
        'en' => 'Save'
    ],
    'ui.actions.bookmarked' => [
        'vi' => 'Đã lưu',
        'en' => 'Saved'
    ],
    'ui.actions.remove_bookmark' => [
        'vi' => 'Bỏ lưu',
        'en' => 'Remove'
    ],

    // Follow Actions
    'ui.actions.follow' => [
        'vi' => 'Theo dõi',
        'en' => 'Follow'
    ],
    'ui.actions.following' => [
        'vi' => 'Đang theo dõi',
        'en' => 'Following'
    ],
    'ui.actions.unfollow' => [
        'vi' => 'Bỏ theo dõi',
        'en' => 'Unfollow'
    ],

    // Tooltips
    'ui.tooltips.bookmark_showcase' => [
        'vi' => 'Lưu showcase này vào danh sách yêu thích',
        'en' => 'Save this showcase to your favorites'
    ],
    'ui.tooltips.remove_bookmark_showcase' => [
        'vi' => 'Bỏ lưu showcase khỏi danh sách yêu thích',
        'en' => 'Remove this showcase from your favorites'
    ],
    'ui.tooltips.follow_showcase_author' => [
        'vi' => 'Theo dõi tác giả để nhận thông báo về showcase mới',
        'en' => 'Follow author to get notified about new showcases'
    ],
    'ui.tooltips.unfollow_showcase_author' => [
        'vi' => 'Bỏ theo dõi tác giả',
        'en' => 'Unfollow author'
    ],

    // Success Messages
    'ui.messages.showcase_bookmarked' => [
        'vi' => 'Đã lưu showcase vào danh sách yêu thích',
        'en' => 'Showcase saved to favorites'
    ],
    'ui.messages.showcase_bookmark_removed' => [
        'vi' => 'Đã bỏ lưu showcase khỏi danh sách yêu thích',
        'en' => 'Showcase removed from favorites'
    ],
    'ui.messages.showcase_author_followed' => [
        'vi' => 'Đã theo dõi tác giả showcase',
        'en' => 'Now following showcase author'
    ],
    'ui.messages.showcase_author_unfollowed' => [
        'vi' => 'Đã bỏ theo dõi tác giả showcase',
        'en' => 'Unfollowed showcase author'
    ],

    // Error Messages
    'ui.errors.bookmark_failed' => [
        'vi' => 'Không thể lưu showcase. Vui lòng thử lại.',
        'en' => 'Failed to bookmark showcase. Please try again.'
    ],
    'ui.errors.follow_failed' => [
        'vi' => 'Không thể theo dõi tác giả. Vui lòng thử lại.',
        'en' => 'Failed to follow author. Please try again.'
    ],
    'ui.errors.login_required' => [
        'vi' => 'Vui lòng đăng nhập để sử dụng tính năng này',
        'en' => 'Please login to use this feature'
    ],
    'ui.errors.cannot_follow_yourself' => [
        'vi' => 'Bạn không thể theo dõi chính mình',
        'en' => 'You cannot follow yourself'
    ],

    // Loading States
    'ui.loading.saving' => [
        'vi' => 'Đang lưu...',
        'en' => 'Saving...'
    ],
    'ui.loading.following' => [
        'vi' => 'Đang theo dõi...',
        'en' => 'Following...'
    ],
    'ui.loading.processing' => [
        'vi' => 'Đang xử lý...',
        'en' => 'Processing...'
    ],
];

$totalAdded = 0;
$totalSkipped = 0;

foreach ($translationKeys as $key => $translations) {
    echo "📝 Processing key: {$key}\n";

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
            'locale' => 'vi',
            'content' => $translations['vi'],
            'group_name' => explode('.', $key)[0],
            'is_active' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        echo "   ✅ Added Vietnamese: {$translations['vi']}\n";
    }

    // Add English translation
    if (!$existingEn) {
        DB::table('translations')->insert([
            'key' => $key,
            'locale' => 'en',
            'content' => $translations['en'],
            'group_name' => explode('.', $key)[0],
            'is_active' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        echo "   ✅ Added English: {$translations['en']}\n";
    }

    $totalAdded++;
}

echo "\n📊 SUMMARY\n";
echo "==========\n";
echo "✅ Keys added: {$totalAdded}\n";
echo "⏭️ Keys skipped: {$totalSkipped}\n";
echo "🎯 Total processed: " . ($totalAdded + $totalSkipped) . "\n\n";

echo "🎉 Showcase action buttons translation keys have been added successfully!\n";
echo "🔗 You can now use these keys in your Blade templates:\n";
echo "   - {{ __('ui.actions.bookmark') }}\n";
echo "   - {{ __('ui.actions.follow') }}\n";
echo "   - {{ __('ui.tooltips.bookmark_showcase') }}\n";
echo "   - {{ __('ui.messages.showcase_bookmarked') }}\n\n";
