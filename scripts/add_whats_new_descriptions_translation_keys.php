<?php

/**
 * Script to add translation keys for whats-new page descriptions
 * Based on the logic and functionality of each page
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🚀 Adding Whats-New Descriptions Translation Keys...\n";
echo "=" . str_repeat("=", 60) . "\n";

$totalAdded = 0;
$totalSkipped = 0;
$totalErrors = 0;

// Define translation keys for whats-new descriptions based on controller logic
$translationKeys = [
    // Main whats-new page (index) - shows recent posts with Laravel pagination
    'ui.whats_new.main.title' => [
        'vi' => 'Nội dung mới nhất',
        'en' => 'Latest Content',
        'group' => 'ui'
    ],
    'ui.whats_new.main.description' => [
        'vi' => 'Khám phá những bài viết, thảo luận và nội dung mới nhất từ cộng đồng kỹ sư cơ khí MechaMap. Được sắp xếp theo thời gian tạo mới nhất.',
        'en' => 'Discover the latest posts, discussions and content from the MechaMap mechanical engineering community. Sorted by newest creation time.',
        'group' => 'ui'
    ],

    // Popular page - trending and most viewed content with timeframe filters
    'ui.whats_new.popular.title' => [
        'vi' => 'Nội dung phổ biến',
        'en' => 'Popular Content',
        'group' => 'ui'
    ],
    'ui.whats_new.popular.description' => [
        'vi' => 'Những bài viết được quan tâm nhất dựa trên điểm trending (lượt xem, bình luận, thời gian tạo) hoặc lượt xem cao nhất. Có thể lọc theo khung thời gian từ hôm nay đến tất cả thời gian.',
        'en' => 'Most popular posts based on trending score (views, comments, creation time) or highest view count. Filter by timeframe from today to all time.',
        'group' => 'ui'
    ],

    // Threads page - newest threads ordered by creation date
    'ui.whats_new.threads.title' => [
        'vi' => 'Chủ đề mới',
        'en' => 'New Threads',
        'group' => 'ui'
    ],
    'ui.whats_new.threads.description' => [
        'vi' => 'Danh sách các chủ đề thảo luận mới nhất được tạo bởi cộng đồng. Sắp xếp theo thời gian tạo từ mới nhất đến cũ nhất.',
        'en' => 'List of newest discussion threads created by the community. Sorted by creation time from newest to oldest.',
        'group' => 'ui'
    ],

    // Hot topics page - high engagement threads with hot score calculation
    'ui.whats_new.hot_topics.title' => [
        'vi' => 'Chủ đề nóng',
        'en' => 'Hot Topics',
        'group' => 'ui'
    ],
    'ui.whats_new.hot_topics.description' => [
        'vi' => 'Những chủ đề có mức độ tương tác cao gần đây. Điểm "nóng" được tính dựa trên lượt xem, số bình luận và hoạt động trong 24 giờ qua.',
        'en' => 'Topics with high recent engagement. "Hot" score calculated based on views, comments count and activity in the last 24 hours.',
        'group' => 'ui'
    ],

    // Media page - recent media files from threads
    'ui.whats_new.media.title' => [
        'vi' => 'Phương tiện mới',
        'en' => 'New Media',
        'group' => 'ui'
    ],
    'ui.whats_new.media.description' => [
        'vi' => 'Hình ảnh, video và file đính kèm mới nhất được tải lên trong các chủ đề thảo luận. Chỉ hiển thị media từ các chủ đề công khai và chưa bị khóa.',
        'en' => 'Latest images, videos and attachments uploaded in discussion threads. Only shows media from public and unlocked threads.',
        'group' => 'ui'
    ],

    // Showcases page - recent project showcases
    'ui.whats_new.showcases.title' => [
        'vi' => 'Showcase mới',
        'en' => 'New Showcases',
        'group' => 'ui'
    ],
    'ui.whats_new.showcases.description' => [
        'vi' => 'Những dự án kỹ thuật mới nhất được trưng bày bởi cộng đồng. Bao gồm các dự án thiết kế, phân tích, sản xuất và nghiên cứu.',
        'en' => 'Latest engineering projects showcased by the community. Includes design, analysis, manufacturing and research projects.',
        'group' => 'ui'
    ],

    // Replies page - threads looking for answers (few or no replies)
    'ui.whats_new.replies.title' => [
        'vi' => 'Tìm kiếm trả lời',
        'en' => 'Looking for Replies',
        'group' => 'ui'
    ],
    'ui.whats_new.replies.description' => [
        'vi' => 'Những chủ đề đang cần sự giúp đỡ từ cộng đồng. Hiển thị các bài viết chưa có trả lời hoặc có ít hơn 5 bình luận.',
        'en' => 'Topics that need help from the community. Shows posts with no replies or fewer than 5 comments.',
        'group' => 'ui'
    ],
];

foreach ($translationKeys as $key => $data) {
    echo "\n📝 Processing key: {$key}\n";
    
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
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "📊 SUMMARY:\n";
echo "   ✅ Keys added: {$totalAdded}\n";
echo "   ⏭️ Keys skipped: {$totalSkipped}\n";
echo "   ❌ Errors: {$totalErrors}\n";
echo "   📝 Total processed: " . ($totalAdded + $totalSkipped + $totalErrors) . "\n";
echo "\n🎉 Whats-New descriptions translation keys setup completed!\n";
echo "📋 Next steps:\n";
echo "   1. The view files have already been updated with these translation keys\n";
echo "   2. Test the pages to ensure descriptions display correctly\n";
echo "   3. Adjust translations if needed via the admin panel\n";
