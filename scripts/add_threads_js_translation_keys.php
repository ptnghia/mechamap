<?php

/**
 * Add Translation Keys for threads.js JavaScript file
 * Converts hardcoded Vietnamese text to translation keys
 */

// Set working directory to Laravel root
chdir(__DIR__ . '/..');

// Initialize Laravel
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Translation;

echo "🔧 ADDING TRANSLATION KEYS FOR THREADS.JS\n";
echo "==========================================\n";

// Translation keys for threads.js
$translationKeys = [
    // Like actions
    'ui.actions.like' => [
        'vi' => 'Thích',
        'en' => 'Like',
        'group' => 'ui'
    ],
    'ui.actions.unlike' => [
        'vi' => 'Bỏ thích',
        'en' => 'Unlike',
        'group' => 'ui'
    ],

    // Save/Bookmark actions
    'ui.actions.save' => [
        'vi' => 'Đánh dấu',
        'en' => 'Save',
        'group' => 'ui'
    ],
    'ui.actions.saved' => [
        'vi' => 'Đã đánh dấu',
        'en' => 'Saved',
        'group' => 'ui'
    ],
    'ui.actions.unsave' => [
        'vi' => 'Bỏ đánh dấu',
        'en' => 'Remove bookmark',
        'group' => 'ui'
    ],

    // Follow actions
    'ui.actions.follow' => [
        'vi' => 'Theo dõi',
        'en' => 'Follow',
        'group' => 'ui'
    ],
    'ui.actions.following' => [
        'vi' => 'Đang theo dõi',
        'en' => 'Following',
        'group' => 'ui'
    ],
    'ui.actions.unfollow' => [
        'vi' => 'Bỏ theo dõi',
        'en' => 'Unfollow',
        'group' => 'ui'
    ],

    // Processing states
    'ui.status.processing' => [
        'vi' => 'Đang xử lý',
        'en' => 'Processing',
        'group' => 'ui'
    ],
    'ui.status.loading_comments' => [
        'vi' => 'Đang tải bình luận...',
        'en' => 'Loading comments...',
        'group' => 'ui'
    ],

    // Error messages
    'ui.messages.error_occurred' => [
        'vi' => 'Có lỗi xảy ra',
        'en' => 'An error occurred',
        'group' => 'ui'
    ],
    'ui.messages.request_error' => [
        'vi' => 'Có lỗi xảy ra khi gửi yêu cầu. Vui lòng thử lại.',
        'en' => 'An error occurred while sending the request. Please try again.',
        'group' => 'ui'
    ],

    // Success messages
    'ui.messages.comments_sorted' => [
        'vi' => 'Bình luận đã được sắp xếp',
        'en' => 'Comments have been sorted',
        'group' => 'ui'
    ],

    // Delete confirmations
    'ui.confirmations.delete_image' => [
        'vi' => 'hình ảnh này',
        'en' => 'this image',
        'group' => 'ui'
    ],
    'ui.messages.delete_image_error' => [
        'vi' => 'Có lỗi xảy ra khi xóa hình ảnh.',
        'en' => 'An error occurred while deleting the image.',
        'group' => 'ui'
    ],

    // Thread-specific messages (using features group)
    'features.threads.delete_comment_message' => [
        'vi' => 'Bạn có chắc chắn muốn xóa bình luận này?',
        'en' => 'Are you sure you want to delete this comment?',
        'group' => 'features'
    ],
    'features.threads.delete_reply_message' => [
        'vi' => 'Bạn có chắc chắn muốn xóa phản hồi này?',
        'en' => 'Are you sure you want to delete this reply?',
        'group' => 'features'
    ],
];

$totalAdded = 0;
$totalSkipped = 0;
$totalErrors = 0;

foreach ($translationKeys as $key => $data) {
    echo "📝 Processing key: {$key}\n";

    try {
        // Check if Vietnamese translation exists
        $viTranslation = Translation::where('group_name', $data['group'])
            ->where('key', $key)
            ->where('locale', 'vi')
            ->first();

        if (!$viTranslation) {
            Translation::create([
                'group_name' => $data['group'],
                'key' => $key,
                'content' => $data['vi'],
                'locale' => 'vi',
                'is_active' => true
            ]);
            $totalAdded++;
            echo "   ✅ Created VI: {$data['vi']}\n";
        } else {
            $totalSkipped++;
            echo "   ⏭️ Skipped VI: already exists\n";
        }

        // Check if English translation exists
        $enTranslation = Translation::where('group_name', $data['group'])
            ->where('key', $key)
            ->where('locale', 'en')
            ->first();

        if (!$enTranslation) {
            Translation::create([
                'group_name' => $data['group'],
                'key' => $key,
                'content' => $data['en'],
                'locale' => 'en',
                'is_active' => true
            ]);
            $totalAdded++;
            echo "   ✅ Created EN: {$data['en']}\n";
        } else {
            $totalSkipped++;
            echo "   ⏭️ Skipped EN: already exists\n";
        }

    } catch (\Exception $e) {
        $totalErrors++;
        echo "   ❌ Error processing {$key}: {$e->getMessage()}\n";
    }

    echo "\n";
}

echo "📊 SUMMARY:\n";
echo "===========\n";
echo "✅ Added: {$totalAdded} translations\n";
echo "⏭️ Skipped: {$totalSkipped} translations\n";
echo "❌ Errors: {$totalErrors} translations\n";
echo "\n";

if ($totalAdded > 0) {
    echo "🎉 Translation keys added successfully!\n";
    echo "💡 Next steps:\n";
    echo "   1. Update threads.js to use translation service\n";
    echo "   2. Load translations when page loads\n";
    echo "   3. Test language switching functionality\n";
} else {
    echo "ℹ️ No new translation keys were added.\n";
}

echo "\n✨ Script completed!\n";
