<?php

/**
 * Add Translation Keys for Inline Comment Edit Feature
 *
 * This script adds all necessary translation keys for the new inline comment editing feature
 * to the MechaMap database-based translation system.
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Load Laravel application
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🚀 Adding Inline Edit Translation Keys to MechaMap Database\n";
echo "=" . str_repeat("=", 60) . "\n\n";

// Translation keys for inline edit feature
$translationKeys = [
    // Edit buttons and actions
    'thread.edit_comment' => [
        'vi' => 'Chỉnh sửa bình luận',
        'en' => 'Edit comment',
        'group' => 'thread'
    ],
    'thread.edit_reply' => [
        'vi' => 'Chỉnh sửa phản hồi',
        'en' => 'Edit reply',
        'group' => 'thread'
    ],
    'thread.save_changes' => [
        'vi' => 'Lưu thay đổi',
        'en' => 'Save changes',
        'group' => 'thread'
    ],
    'thread.cancel' => [
        'vi' => 'Hủy',
        'en' => 'Cancel',
        'group' => 'thread'
    ],

    // Attachment management
    'thread.existing_attachments' => [
        'vi' => 'Hình ảnh hiện có',
        'en' => 'Existing attachments',
        'group' => 'thread'
    ],
    'thread.add_new_images' => [
        'vi' => 'Thêm hình ảnh mới',
        'en' => 'Add new images',
        'group' => 'thread'
    ],
    'thread.remove_attachment' => [
        'vi' => 'Xóa hình ảnh',
        'en' => 'Remove attachment',
        'group' => 'thread'
    ],
    'thread.confirm_remove_attachment' => [
        'vi' => 'Bạn có chắc chắn muốn xóa hình ảnh này?',
        'en' => 'Are you sure you want to remove this attachment?',
        'group' => 'thread'
    ],
    'thread.images_only' => [
        'vi' => 'Chỉ hình ảnh (JPG, PNG, GIF, WebP)',
        'en' => 'Images only (JPG, PNG, GIF, WebP)',
        'group' => 'thread'
    ],

    // Form validation and status
    'thread.content_required' => [
        'vi' => 'Nội dung không được để trống',
        'en' => 'Content is required',
        'group' => 'thread'
    ],
    'thread.saving' => [
        'vi' => 'Đang lưu...',
        'en' => 'Saving...',
        'group' => 'thread'
    ],
    'thread.comment_updated_successfully' => [
        'vi' => 'Bình luận đã được cập nhật thành công',
        'en' => 'Comment updated successfully',
        'group' => 'thread'
    ],
    'thread.update_failed' => [
        'vi' => 'Cập nhật thất bại. Vui lòng thử lại.',
        'en' => 'Update failed. Please try again.',
        'group' => 'thread'
    ],

    // Placeholders
    'thread.edit_comment_placeholder' => [
        'vi' => 'Chỉnh sửa nội dung bình luận của bạn...',
        'en' => 'Edit your comment content...',
        'group' => 'thread'
    ],
    'thread.edit_reply_placeholder' => [
        'vi' => 'Chỉnh sửa nội dung phản hồi của bạn...',
        'en' => 'Edit your reply content...',
        'group' => 'thread'
    ],
];

$successCount = 0;
$errorCount = 0;

foreach ($translationKeys as $key => $data) {
    echo "📝 Processing key: {$key}\n";

    try {
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
        } else {
            echo "   ⚠️  VI already exists\n";
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
        } else {
            echo "   ⚠️  EN already exists\n";
        }

        if (!$existingVi || !$existingEn) {
            $successCount++;
        }

    } catch (Exception $e) {
        echo "   ❌ Error: " . $e->getMessage() . "\n";
        $errorCount++;
    }

    echo "\n";
}

echo "=" . str_repeat("=", 60) . "\n";
echo "🎉 SUMMARY:\n";
echo "✅ Successfully processed: {$successCount} keys\n";
echo "❌ Errors: {$errorCount} keys\n";
echo "\n";

if ($successCount > 0) {
    echo "🔄 Clearing translation cache...\n";
    try {
        Artisan::call('cache:clear');
        echo "✅ Cache cleared successfully\n";
    } catch (Exception $e) {
        echo "⚠️  Cache clear failed: " . $e->getMessage() . "\n";
    }
}

echo "\n🚀 Inline Edit Translation Keys Setup Complete!\n";
echo "🌐 Access translation management: https://mechamap.test/translations\n";
