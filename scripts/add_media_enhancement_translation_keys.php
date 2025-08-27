<?php

/**
 * Add Media Enhancement Translation Keys
 *
 * This script adds translation keys for the enhanced media gallery functionality
 * including preview, share, file information, and technical details.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Load Laravel application
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🎨 Adding Media Enhancement Translation Keys\n";
echo "==========================================\n\n";

// Translation keys for media enhancements
$translationKeys = [
    // UI Actions for Media
    'ui.actions.preview' => [
        'vi' => 'Xem trước',
        'en' => 'Preview'
    ],
    'ui.actions.download' => [
        'vi' => 'Tải xuống',
        'en' => 'Download'
    ],
    'ui.actions.share' => [
        'vi' => 'Chia sẻ',
        'en' => 'Share'
    ],
    'ui.actions.save' => [
        'vi' => 'Lưu',
        'en' => 'Save'
    ],
    'ui.actions.view_thread' => [
        'vi' => 'Xem chủ đề',
        'en' => 'View Thread'
    ],
    'ui.actions.close' => [
        'vi' => 'Đóng',
        'en' => 'Close'
    ],

    // Media Information Labels
    'ui.media.file_information' => [
        'vi' => 'Thông tin file',
        'en' => 'File Information'
    ],
    'ui.media.additional_info' => [
        'vi' => 'Thông tin bổ sung',
        'en' => 'Additional Information'
    ],
    'ui.media.filename' => [
        'vi' => 'Tên file',
        'en' => 'Filename'
    ],
    'ui.media.size' => [
        'vi' => 'Kích thước',
        'en' => 'Size'
    ],
    'ui.media.type' => [
        'vi' => 'Loại file',
        'en' => 'File Type'
    ],
    'ui.media.dimensions' => [
        'vi' => 'Kích thước',
        'en' => 'Dimensions'
    ],
    'ui.media.uploaded' => [
        'vi' => 'Tải lên',
        'en' => 'Uploaded'
    ],
    'ui.media.downloads' => [
        'vi' => 'Lượt tải',
        'en' => 'Downloads'
    ],
    'ui.media.uploaded_by' => [
        'vi' => 'Tải lên bởi',
        'en' => 'Uploaded by'
    ],
    'ui.media.from_thread' => [
        'vi' => 'Từ chủ đề',
        'en' => 'From Thread'
    ],

    // CAD/Technical Information
    'ui.media.cad_software' => [
        'vi' => 'Phần mềm CAD',
        'en' => 'CAD Software'
    ],
    'ui.media.scale' => [
        'vi' => 'Tỷ lệ',
        'en' => 'Scale'
    ],
    'ui.media.material' => [
        'vi' => 'Vật liệu',
        'en' => 'Material'
    ],
    'ui.media.drawing_scale' => [
        'vi' => 'Tỷ lệ bản vẽ',
        'en' => 'Drawing Scale'
    ],
    'ui.media.units' => [
        'vi' => 'Đơn vị',
        'en' => 'Units'
    ],
    'ui.media.revision' => [
        'vi' => 'Phiên bản',
        'en' => 'Revision'
    ],
    'ui.media.standard' => [
        'vi' => 'Tiêu chuẩn',
        'en' => 'Standard'
    ],

    // Messages and Notifications
    'ui.messages.link_copied' => [
        'vi' => 'Đã sao chép liên kết!',
        'en' => 'Link copied to clipboard!'
    ],
    'ui.messages.copy_failed' => [
        'vi' => 'Không thể sao chép liên kết',
        'en' => 'Failed to copy link'
    ],
    'ui.messages.share_failed' => [
        'vi' => 'Không thể chia sẻ',
        'en' => 'Failed to share'
    ],

    // Empty States
    'ui.common.no_media_found' => [
        'vi' => 'Không tìm thấy phương tiện nào',
        'en' => 'No media found'
    ],
    'ui.common.no_media_description' => [
        'vi' => 'Chưa có hình ảnh, video hoặc file nào được tải lên gần đây.',
        'en' => 'No images, videos or files have been uploaded recently.'
    ],

    // File Categories
    'ui.media.categories.image' => [
        'vi' => 'Hình ảnh',
        'en' => 'Image'
    ],
    'ui.media.categories.video' => [
        'vi' => 'Video',
        'en' => 'Video'
    ],
    'ui.media.categories.audio' => [
        'vi' => 'Âm thanh',
        'en' => 'Audio'
    ],
    'ui.media.categories.document' => [
        'vi' => 'Tài liệu',
        'en' => 'Document'
    ],
    'ui.media.categories.cad' => [
        'vi' => 'File CAD',
        'en' => 'CAD File'
    ],
    'ui.media.categories.archive' => [
        'vi' => 'File nén',
        'en' => 'Archive'
    ],
    'ui.media.categories.engineering' => [
        'vi' => 'Kỹ thuật',
        'en' => 'Engineering'
    ],
    'ui.media.categories.simulation' => [
        'vi' => 'Mô phỏng',
        'en' => 'Simulation'
    ],
    'ui.media.categories.manufacturing' => [
        'vi' => 'Sản xuất',
        'en' => 'Manufacturing'
    ],
    'ui.media.categories.other' => [
        'vi' => 'Khác',
        'en' => 'Other'
    ],

    // Quality Indicators
    'ui.media.quality.hd' => [
        'vi' => 'HD',
        'en' => 'HD'
    ],
    'ui.media.quality.hd_ready' => [
        'vi' => 'HD Ready',
        'en' => 'HD Ready'
    ],
    'ui.media.quality.standard' => [
        'vi' => 'Tiêu chuẩn',
        'en' => 'Standard'
    ],
    'ui.media.quality.4k' => [
        'vi' => '4K',
        'en' => '4K'
    ],
    'ui.media.quality.unknown' => [
        'vi' => 'Không rõ',
        'en' => 'Unknown'
    ],

    // Processing Status
    'ui.media.status.processing' => [
        'vi' => 'Đang xử lý',
        'en' => 'Processing'
    ],
    'ui.media.status.completed' => [
        'vi' => 'Hoàn thành',
        'en' => 'Completed'
    ],
    'ui.media.status.failed' => [
        'vi' => 'Thất bại',
        'en' => 'Failed'
    ],
    'ui.media.status.pending' => [
        'vi' => 'Đang chờ',
        'en' => 'Pending'
    ],

    // Tooltips and Help Text
    'ui.tooltips.preview_media' => [
        'vi' => 'Xem trước file trong cửa sổ popup',
        'en' => 'Preview file in popup window'
    ],
    'ui.tooltips.download_media' => [
        'vi' => 'Tải file về máy tính của bạn',
        'en' => 'Download file to your computer'
    ],
    'ui.tooltips.share_media' => [
        'vi' => 'Chia sẻ liên kết file này',
        'en' => 'Share link to this file'
    ],
    'ui.tooltips.save_media' => [
        'vi' => 'Lưu file vào bộ sưu tập của bạn',
        'en' => 'Save file to your collection'
    ],

    // Media Gallery
    'ui.media.gallery.title' => [
        'vi' => 'Thư viện phương tiện',
        'en' => 'Media Gallery'
    ],
    'ui.media.gallery.enhanced' => [
        'vi' => 'Thư viện phương tiện nâng cao',
        'en' => 'Enhanced Media Gallery'
    ],
];

// Function to add translation key to database
function addTranslationKey($key, $translations) {
    try {
        foreach ($translations as $locale => $value) {
            // Check if key already exists
            $existing = DB::table('translations')
                ->where('key', $key)
                ->where('locale', $locale)
                ->first();

            if (!$existing) {
                DB::table('translations')->insert([
                    'key' => $key,
                    'locale' => $locale,
                    'content' => $value,
                    'group_name' => 'ui',
                    'namespace' => 'frontend',
                    'is_active' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                echo "   ✅ Added: {$key} ({$locale})\n";
            } else {
                echo "   ⚠️ Exists: {$key} ({$locale})\n";
            }
        }
    } catch (Exception $e) {
        echo "   ❌ Error adding {$key}: " . $e->getMessage() . "\n";
    }
}

// Add all translation keys
$totalAdded = 0;
$totalSkipped = 0;

foreach ($translationKeys as $key => $translations) {
    echo "🔑 Processing: {$key}\n";

    $added = false;
    foreach ($translations as $locale => $value) {
        $existing = DB::table('translations')
            ->where('key', $key)
            ->where('locale', $locale)
            ->first();

        if (!$existing) {
            DB::table('translations')->insert([
                'key' => $key,
                'locale' => $locale,
                'content' => $value,
                'group_name' => 'ui',
                'namespace' => 'frontend',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $totalAdded++;
            $added = true;
        } else {
            $totalSkipped++;
        }
    }

    if ($added) {
        echo "   ✅ Added to database\n";
    } else {
        echo "   ⚠️ Already exists\n";
    }
}

echo "\n==========================================\n";
echo "🎉 Media Enhancement Translation Keys Added!\n";
echo "📊 Summary:\n";
echo "   • Total keys processed: " . count($translationKeys) . "\n";
echo "   • New translations added: {$totalAdded}\n";
echo "   • Existing translations skipped: {$totalSkipped}\n";
echo "\n💡 You can now use these keys in your Blade templates!\n";
echo "🔗 Manage translations at: https://mechamap.test/translations\n";
