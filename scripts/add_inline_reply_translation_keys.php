<?php

/**
 * Add translation keys for inline reply feature
 * 
 * Usage: php scripts/add_inline_reply_translation_keys.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Load Laravel app
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Translation;

// Translation keys for inline reply feature
$translationKeys = [
    // Reply actions
    'thread.replying_to' => [
        'vi' => 'Đang trả lời',
        'en' => 'Replying to',
        'group' => 'thread'
    ],
    'thread.reply_will_appear_below' => [
        'vi' => 'Phản hồi sẽ xuất hiện bên dưới',
        'en' => 'Reply will appear below',
        'group' => 'thread'
    ],
    'thread.add_images' => [
        'vi' => 'Thêm hình ảnh',
        'en' => 'Add images',
        'group' => 'thread'
    ],
    'thread.images_only' => [
        'vi' => 'Chỉ hình ảnh (JPG, PNG, GIF, WebP)',
        'en' => 'Images only (JPG, PNG, GIF, WebP)',
        'group' => 'thread'
    ],
    
    // Common actions (if not exist)
    'common.cancel' => [
        'vi' => 'Hủy',
        'en' => 'Cancel',
        'group' => 'common'
    ],
    
    // Success messages
    'thread.reply_posted_successfully' => [
        'vi' => 'Phản hồi đã được đăng thành công',
        'en' => 'Reply posted successfully',
        'group' => 'thread'
    ],
    'thread.reply_posting_error' => [
        'vi' => 'Có lỗi xảy ra khi đăng phản hồi',
        'en' => 'Error occurred while posting reply',
        'group' => 'thread'
    ],
    
    // Form validation
    'thread.reply_content_required' => [
        'vi' => 'Nội dung phản hồi là bắt buộc',
        'en' => 'Reply content is required',
        'group' => 'thread'
    ],
];

echo "🚀 Adding inline reply translation keys...\n";
echo "==========================================\n";

$addedCount = 0;
$updatedCount = 0;
$skippedCount = 0;

foreach ($translationKeys as $key => $translations) {
    foreach (['vi', 'en'] as $locale) {
        if (!isset($translations[$locale])) {
            echo "⚠️  Missing {$locale} translation for key: {$key}\n";
            continue;
        }
        
        $content = $translations[$locale];
        $group = $translations['group'] ?? 'general';
        
        // Check if translation exists
        $existing = Translation::where('key', $key)
            ->where('locale', $locale)
            ->first();
        
        if ($existing) {
            if ($existing->content !== $content) {
                $existing->update([
                    'content' => $content,
                    'group_name' => $group,
                    'updated_by' => 1, // System user
                ]);
                $updatedCount++;
                echo "✅ Updated {$locale}: {$key} = '{$content}'\n";
            } else {
                $skippedCount++;
                echo "⏭️  Skipped {$locale}: {$key} (unchanged)\n";
            }
        } else {
            Translation::create([
                'key' => $key,
                'locale' => $locale,
                'content' => $content,
                'group_name' => $group,
                'is_active' => true,
                'created_by' => 1, // System user
                'updated_by' => 1,
            ]);
            $addedCount++;
            echo "✅ Added {$locale}: {$key} = '{$content}'\n";
        }
    }
    echo "\n";
}

echo "🎯 SUMMARY:\n";
echo "===========\n";
echo "✅ Added: {$addedCount} translations\n";
echo "🔄 Updated: {$updatedCount} translations\n";
echo "⏭️  Skipped: {$skippedCount} translations\n";
echo "\n";

// Clear translation cache
if (function_exists('cache')) {
    cache()->tags(['translations'])->flush();
    echo "🧹 Translation cache cleared\n";
}

echo "✨ Inline reply translation keys setup completed!\n";
echo "\n";
echo "🔗 Next steps:\n";
echo "1. Test the inline reply form\n";
echo "2. Verify translations appear correctly\n";
echo "3. Check both Vietnamese and English versions\n";

?>
