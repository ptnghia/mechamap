<?php

/**
 * Script để thêm translation keys cho Showcase và Bookmarks
 * Chạy: php scripts/add_showcase_bookmarks_translations.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🚀 Adding Showcase and Bookmarks Translation Keys...\n\n";

// Translation keys cho Showcase Dashboard
$showcaseKeys = [
    // Dashboard Showcase
    'showcase.my_showcases' => [
        'vi' => 'Showcase của tôi',
        'en' => 'My Showcases'
    ],
    'showcase.manage_description' => [
        'vi' => 'Quản lý và theo dõi các dự án showcase của bạn',
        'en' => 'Manage and track your showcase projects'
    ],
    'showcase.create_new' => [
        'vi' => 'Tạo mới',
        'en' => 'Create New'
    ],
    'showcase.create_first' => [
        'vi' => 'Tạo showcase đầu tiên',
        'en' => 'Create First Showcase'
    ],

    // Stats
    'showcase.stats.total' => [
        'vi' => 'Tổng số',
        'en' => 'Total'
    ],
    'showcase.stats.published' => [
        'vi' => 'Đã xuất bản',
        'en' => 'Published'
    ],
    'showcase.stats.pending' => [
        'vi' => 'Chờ duyệt',
        'en' => 'Pending'
    ],
    'showcase.stats.featured' => [
        'vi' => 'Nổi bật',
        'en' => 'Featured'
    ],
    'showcase.stats.total_views' => [
        'vi' => 'Tổng lượt xem',
        'en' => 'Total Views'
    ],
    'showcase.stats.avg_rating' => [
        'vi' => 'Đánh giá TB',
        'en' => 'Avg Rating'
    ],
    'showcase.stats.views' => [
        'vi' => 'Lượt xem',
        'en' => 'Views'
    ],
    'showcase.stats.likes' => [
        'vi' => 'Lượt thích',
        'en' => 'Likes'
    ],
    'showcase.stats.downloads' => [
        'vi' => 'Lượt tải',
        'en' => 'Downloads'
    ],

    // Create Form
    'showcase.create.title' => [
        'vi' => 'Tạo Showcase Mới',
        'en' => 'Create New Showcase'
    ],
    'showcase.create.step_basic' => [
        'vi' => 'Thông tin cơ bản',
        'en' => 'Basic Info'
    ],
    'showcase.create.step_media' => [
        'vi' => 'Tệp đa phương tiện',
        'en' => 'Media Files'
    ],
    'showcase.create.step_details' => [
        'vi' => 'Chi tiết',
        'en' => 'Details'
    ],
    'showcase.create.step_review' => [
        'vi' => 'Xem lại',
        'en' => 'Review'
    ],
    'showcase.create.basic_info' => [
        'vi' => 'Thông tin cơ bản',
        'en' => 'Basic Information'
    ],
    'showcase.create.title_placeholder' => [
        'vi' => 'Nhập tiêu đề dự án...',
        'en' => 'Enter project title...'
    ],
    'showcase.create.category' => [
        'vi' => 'Danh mục',
        'en' => 'Category'
    ],
    'showcase.create.select_category' => [
        'vi' => 'Chọn danh mục',
        'en' => 'Select Category'
    ],
    'showcase.create.related_thread' => [
        'vi' => 'Bài viết liên quan',
        'en' => 'Related Thread'
    ],
    'showcase.create.no_thread' => [
        'vi' => 'Không có bài viết liên quan',
        'en' => 'No Related Thread'
    ],
    'showcase.create.description' => [
        'vi' => 'Mô tả',
        'en' => 'Description'
    ],
    'showcase.create.description_placeholder' => [
        'vi' => 'Mô tả chi tiết về dự án của bạn...',
        'en' => 'Describe your project in detail...'
    ],
    'showcase.create.media_files' => [
        'vi' => 'Tệp đa phương tiện',
        'en' => 'Media Files'
    ],
    'showcase.create.images' => [
        'vi' => 'Hình ảnh',
        'en' => 'Images'
    ],
    'showcase.create.drag_images' => [
        'vi' => 'Kéo thả hình ảnh vào đây',
        'en' => 'Drag and drop images here'
    ],
    'showcase.create.image_formats' => [
        'vi' => 'Hỗ trợ: JPG, PNG, GIF (tối đa 10MB)',
        'en' => 'Supported: JPG, PNG, GIF (max 10MB)'
    ],
    'showcase.create.select_images' => [
        'vi' => 'Chọn hình ảnh',
        'en' => 'Select Images'
    ],
    'showcase.create.attachments' => [
        'vi' => 'Tệp đính kèm',
        'en' => 'Attachments'
    ],
    'showcase.create.drag_files' => [
        'vi' => 'Kéo thả tệp vào đây',
        'en' => 'Drag and drop files here'
    ],
    'showcase.create.file_formats' => [
        'vi' => 'Hỗ trợ: PDF, DOC, ZIP, CAD files (tối đa 50MB)',
        'en' => 'Supported: PDF, DOC, ZIP, CAD files (max 50MB)'
    ],
    'showcase.create.select_files' => [
        'vi' => 'Chọn tệp',
        'en' => 'Select Files'
    ],
    'showcase.create.additional_details' => [
        'vi' => 'Chi tiết bổ sung',
        'en' => 'Additional Details'
    ],
    'showcase.create.tags' => [
        'vi' => 'Thẻ',
        'en' => 'Tags'
    ],
    'showcase.create.add_tags' => [
        'vi' => 'Thêm thẻ...',
        'en' => 'Add tags...'
    ],
    'showcase.create.tags_help' => [
        'vi' => 'Nhấn Enter hoặc dấu phẩy để thêm thẻ',
        'en' => 'Press Enter or comma to add tags'
    ],
    'showcase.create.featured' => [
        'vi' => 'Đánh dấu nổi bật',
        'en' => 'Mark as Featured'
    ],
    'showcase.create.featured_help' => [
        'vi' => 'Dự án nổi bật sẽ được hiển thị ưu tiên',
        'en' => 'Featured projects will be displayed with priority'
    ],
    'showcase.create.submit' => [
        'vi' => 'Tạo Showcase',
        'en' => 'Create Showcase'
    ],
    'showcase.create.validation_error' => [
        'vi' => 'Vui lòng điền đầy đủ thông tin bắt buộc',
        'en' => 'Please fill in all required information'
    ],

    // Filter
    'showcase.filter.status' => [
        'vi' => 'Trạng thái',
        'en' => 'Status'
    ],
    'showcase.filter.all_status' => [
        'vi' => 'Tất cả trạng thái',
        'en' => 'All Status'
    ],
    'showcase.filter.category' => [
        'vi' => 'Danh mục',
        'en' => 'Category'
    ],
    'showcase.filter.all_categories' => [
        'vi' => 'Tất cả danh mục',
        'en' => 'All Categories'
    ],
    'showcase.filter.search' => [
        'vi' => 'Tìm kiếm',
        'en' => 'Search'
    ],
    'showcase.filter.search_placeholder' => [
        'vi' => 'Tìm kiếm showcase...',
        'en' => 'Search showcases...'
    ],
    'showcase.filter.sort' => [
        'vi' => 'Sắp xếp',
        'en' => 'Sort'
    ],

    // Sort options
    'showcase.sort.newest' => [
        'vi' => 'Mới nhất',
        'en' => 'Newest'
    ],
    'showcase.sort.oldest' => [
        'vi' => 'Cũ nhất',
        'en' => 'Oldest'
    ],
    'showcase.sort.most_viewed' => [
        'vi' => 'Xem nhiều nhất',
        'en' => 'Most Viewed'
    ],
    'showcase.sort.highest_rated' => [
        'vi' => 'Đánh giá cao nhất',
        'en' => 'Highest Rated'
    ],

    // Status
    'showcase.status.pending' => [
        'vi' => 'Chờ duyệt',
        'en' => 'Pending'
    ],
    'showcase.status.approved' => [
        'vi' => 'Đã duyệt',
        'en' => 'Approved'
    ],
    'showcase.status.featured' => [
        'vi' => 'Nổi bật',
        'en' => 'Featured'
    ],
    'showcase.status.rejected' => [
        'vi' => 'Bị từ chối',
        'en' => 'Rejected'
    ],

    // Empty state
    'showcase.empty.title' => [
        'vi' => 'Chưa có showcase nào',
        'en' => 'No Showcases Yet'
    ],
    'showcase.empty.description' => [
        'vi' => 'Bạn chưa tạo showcase nào. Hãy tạo showcase đầu tiên để chia sẻ dự án của bạn với cộng đồng.',
        'en' => 'You haven\'t created any showcases yet. Create your first showcase to share your projects with the community.'
    ],

    // Delete
    'showcase.delete.title' => [
        'vi' => 'Xóa Showcase',
        'en' => 'Delete Showcase'
    ],
    'showcase.delete.confirmation' => [
        'vi' => 'Bạn có chắc chắn muốn xóa showcase này không?',
        'en' => 'Are you sure you want to delete this showcase?'
    ],
    'showcase.delete.warning' => [
        'vi' => 'Hành động này không thể hoàn tác.',
        'en' => 'This action cannot be undone.'
    ],

    // Edit
    'showcase.edit.title' => [
        'vi' => 'Chỉnh sửa Showcase',
        'en' => 'Edit Showcase'
    ],
    'showcase.edit.basic_info' => [
        'vi' => 'Thông tin cơ bản',
        'en' => 'Basic Information'
    ],
    'showcase.edit.current_media' => [
        'vi' => 'Media hiện tại',
        'en' => 'Current Media'
    ],
    'showcase.edit.current_cover' => [
        'vi' => 'Ảnh bìa hiện tại',
        'en' => 'Current Cover Image'
    ],
    'showcase.edit.current_images' => [
        'vi' => 'Hình ảnh hiện tại',
        'en' => 'Current Images'
    ],
    'showcase.edit.new_media' => [
        'vi' => 'Media mới',
        'en' => 'New Media'
    ],
    'showcase.edit.add_images' => [
        'vi' => 'Thêm hình ảnh',
        'en' => 'Add Images'
    ],
    'showcase.edit.add_attachments' => [
        'vi' => 'Thêm tệp đính kèm',
        'en' => 'Add Attachments'
    ],
    'showcase.edit.current_attachments' => [
        'vi' => 'Tệp đính kèm hiện tại',
        'en' => 'Current Attachments'
    ],
    'showcase.edit.update' => [
        'vi' => 'Cập nhật',
        'en' => 'Update'
    ],
    'showcase.edit.validation_error' => [
        'vi' => 'Vui lòng kiểm tra lại thông tin',
        'en' => 'Please check the information'
    ],
    'showcase.edit.delete_attachment_confirm' => [
        'vi' => 'Bạn có chắc chắn muốn xóa tệp đính kèm này?',
        'en' => 'Are you sure you want to delete this attachment?'
    ],

    // Show/Detail
    'showcase.description' => [
        'vi' => 'Mô tả',
        'en' => 'Description'
    ],
    'showcase.tags' => [
        'vi' => 'Thẻ',
        'en' => 'Tags'
    ],
    'showcase.gallery' => [
        'vi' => 'Thư viện ảnh',
        'en' => 'Gallery'
    ],
    'showcase.attachments' => [
        'vi' => 'Tệp đính kèm',
        'en' => 'Attachments'
    ],
    'showcase.related_thread' => [
        'vi' => 'Bài viết liên quan',
        'en' => 'Related Thread'
    ],
    'showcase.thread_created' => [
        'vi' => 'Bài viết được tạo',
        'en' => 'Thread created'
    ],
    'showcase.rating' => [
        'vi' => 'Đánh giá',
        'en' => 'Rating'
    ],
    'showcase.ratings' => [
        'vi' => 'đánh giá',
        'en' => 'ratings'
    ],
    'showcase.quick_actions' => [
        'vi' => 'Thao tác nhanh',
        'en' => 'Quick Actions'
    ],
    'showcase.pending_approval' => [
        'vi' => 'Chờ phê duyệt',
        'en' => 'Pending Approval'
    ],
    'showcase.information' => [
        'vi' => 'Thông tin',
        'en' => 'Information'
    ],
    'showcase.created' => [
        'vi' => 'Được tạo',
        'en' => 'Created'
    ],
    'showcase.updated' => [
        'vi' => 'Cập nhật',
        'en' => 'Updated'
    ],
    'showcase.approved' => [
        'vi' => 'Được duyệt',
        'en' => 'Approved'
    ],
    'showcase.image_preview' => [
        'vi' => 'Xem trước hình ảnh',
        'en' => 'Image Preview'
    ],
    'showcase.view_public' => [
        'vi' => 'Xem công khai',
        'en' => 'View Public'
    ]
];

// Translation keys cho Bookmarks
$bookmarkKeys = [
    // Index page
    'bookmarks.index.heading' => [
        'vi' => 'Đã lưu',
        'en' => 'Bookmarks'
    ],
    'bookmarks.index.description' => [
        'vi' => 'Quản lý các bài viết, showcase và sản phẩm đã lưu',
        'en' => 'Manage your saved threads, showcases and products'
    ],
    'bookmarks.index.create_folder' => [
        'vi' => 'Tạo thư mục',
        'en' => 'Create Folder'
    ],
    'bookmarks.index.total_bookmarks' => [
        'vi' => 'Tổng đã lưu',
        'en' => 'Total Bookmarks'
    ],
    'bookmarks.index.total_folders' => [
        'vi' => 'Tổng thư mục',
        'en' => 'Total Folders'
    ],
    'bookmarks.index.this_week' => [
        'vi' => 'Tuần này',
        'en' => 'This Week'
    ],
    'bookmarks.index.favorites' => [
        'vi' => 'Yêu thích',
        'en' => 'Favorites'
    ],
    'bookmarks.index.search_placeholder' => [
        'vi' => 'Tìm kiếm đã lưu...',
        'en' => 'Search bookmarks...'
    ],
    'bookmarks.index.all_folders' => [
        'vi' => 'Tất cả thư mục',
        'en' => 'All Folders'
    ],
    'bookmarks.index.all_types' => [
        'vi' => 'Tất cả loại',
        'en' => 'All Types'
    ],
    'bookmarks.index.threads' => [
        'vi' => 'Bài viết',
        'en' => 'Threads'
    ],
    'bookmarks.index.showcases' => [
        'vi' => 'Showcase',
        'en' => 'Showcases'
    ],
    'bookmarks.index.products' => [
        'vi' => 'Sản phẩm',
        'en' => 'Products'
    ],
    'bookmarks.index.sort_latest' => [
        'vi' => 'Mới nhất',
        'en' => 'Latest'
    ],
    'bookmarks.index.sort_oldest' => [
        'vi' => 'Cũ nhất',
        'en' => 'Oldest'
    ],
    'bookmarks.index.sort_alphabetical' => [
        'vi' => 'Theo bảng chữ cái',
        'en' => 'Alphabetical'
    ],
    'bookmarks.index.select_all' => [
        'vi' => 'Chọn tất cả',
        'en' => 'Select All'
    ],
    'bookmarks.index.bookmarks_list' => [
        'vi' => 'Danh sách đã lưu',
        'en' => 'Bookmarks List'
    ],
    'bookmarks.index.no_bookmarks' => [
        'vi' => 'Chưa có mục nào được lưu',
        'en' => 'No Bookmarks Yet'
    ],
    'bookmarks.index.no_bookmarks_desc' => [
        'vi' => 'Bạn chưa lưu bài viết, showcase hoặc sản phẩm nào. Hãy khám phá nội dung và lưu những gì bạn quan tâm.',
        'en' => 'You haven\'t saved any threads, showcases or products yet. Explore content and save what interests you.'
    ],
    'bookmarks.index.browse_content' => [
        'vi' => 'Khám phá nội dung',
        'en' => 'Browse Content'
    ]
];

// Merge all keys
$allKeys = array_merge($showcaseKeys, $bookmarkKeys);

$totalAdded = 0;
$totalSkipped = 0;

foreach ($allKeys as $key => $translations) {
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
            'content' => $translations['vi'],
            'locale' => 'vi',
            'group_name' => strpos($key, 'showcase.') === 0 ? 'showcase' : 'bookmarks',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        echo "   ✅ Added VI: {$translations['vi']}\n";
        $totalAdded++;
    }

    // Add English translation
    if (!$existingEn) {
        DB::table('translations')->insert([
            'key' => $key,
            'content' => $translations['en'],
            'locale' => 'en',
            'group_name' => strpos($key, 'showcase.') === 0 ? 'showcase' : 'bookmarks',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        echo "   ✅ Added EN: {$translations['en']}\n";
        $totalAdded++;
    }
}

echo "\n🎉 Translation keys processing completed!\n";
echo "📊 Summary:\n";
echo "   ✅ Added: {$totalAdded} translations\n";
echo "   ⏭️ Skipped: {$totalSkipped} existing keys\n";
echo "   📝 Total processed: " . count($allKeys) . " keys\n\n";

echo "🔄 Clearing translation cache...\n";
try {
    Artisan::call('cache:clear');
    echo "✅ Cache cleared successfully\n";
} catch (Exception $e) {
    echo "⚠️ Cache clear failed: " . $e->getMessage() . "\n";
}

echo "\n✨ All done! You can now test the translations.\n";
