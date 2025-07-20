<?php
/**
 * Process Forum Translation Keys
 * Xử lý 129 Forum keys - core functionality
 */

echo "💬 PROCESSING FORUM TRANSLATION KEYS\n";
echo "====================================\n\n";

$basePath = '/var/www/mechamap_com_usr/data/www/mechamap.com';

// Bootstrap Laravel
require_once $basePath . '/vendor/autoload.php';
$app = require_once $basePath . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Load analysis data
$analysisFile = $basePath . '/storage/localization/direct_keys_analysis.json';
if (!file_exists($analysisFile)) {
    echo "❌ Analysis file not found.\n";
    exit(1);
}

$analysis = json_decode(file_get_contents($analysisFile), true);

// Extract Forum keys
$forumKeys = [];
$allDirectKeys = array_merge(
    $analysis['detailed_patterns']['dot_notation'] ?? [],
    $analysis['detailed_patterns']['slash_notation'] ?? [],
    $analysis['detailed_patterns']['simple_keys'] ?? [],
    $analysis['detailed_patterns']['mixed_notation'] ?? []
);

foreach ($allDirectKeys as $key => $files) {
    if (strpos($key, 'forum.') === 0 || strpos($key, 'forum/') === 0) {
        $forumKeys[$key] = $files;
    }
}

echo "📊 FORUM KEYS ANALYSIS\n";
echo "======================\n";
echo "Total forum keys found: " . count($forumKeys) . "\n";

// Analyze forum key patterns
$forumPatterns = [];
foreach ($forumKeys as $key => $files) {
    // Extract pattern: forum.section.subsection.key
    if (preg_match('/^forum\.([^.]+)\.(.+)$/', $key, $matches)) {
        $section = $matches[1];
        $subkey = $matches[2];
        
        if (!isset($forumPatterns[$section])) {
            $forumPatterns[$section] = [];
        }
        $forumPatterns[$section][$subkey] = [
            'full_key' => $key,
            'files' => $files
        ];
    } elseif (preg_match('/^forum\/([^\/]+)\/(.+)$/', $key, $matches)) {
        $section = $matches[1];
        $subkey = $matches[2];
        
        if (!isset($forumPatterns[$section])) {
            $forumPatterns[$section] = [];
        }
        $forumPatterns[$section][$subkey] = [
            'full_key' => $key,
            'files' => $files
        ];
    }
}

echo "\n📋 FORUM SECTIONS FOUND\n";
echo "=======================\n";
foreach ($forumPatterns as $section => $keys) {
    echo "🔸 forum.$section: " . count($keys) . " keys\n";
    
    // Show examples
    $examples = array_slice(array_keys($keys), 0, 5);
    foreach ($examples as $example) {
        echo "   - forum.$section.$example\n";
    }
    if (count($keys) > 5) {
        echo "   - ... and " . (count($keys) - 5) . " more\n";
    }
    echo "\n";
}

// Define comprehensive forum translations
$forumTranslations = [
    'threads' => [
        'title' => ['en' => 'Threads', 'vi' => 'Chủ đề'],
        'new_thread' => ['en' => 'New Thread', 'vi' => 'Chủ đề mới'],
        'create_thread' => ['en' => 'Create Thread', 'vi' => 'Tạo chủ đề'],
        'edit_thread' => ['en' => 'Edit Thread', 'vi' => 'Chỉnh sửa chủ đề'],
        'delete_thread' => ['en' => 'Delete Thread', 'vi' => 'Xóa chủ đề'],
        'lock_thread' => ['en' => 'Lock Thread', 'vi' => 'Khóa chủ đề'],
        'unlock_thread' => ['en' => 'Unlock Thread', 'vi' => 'Mở khóa chủ đề'],
        'pin_thread' => ['en' => 'Pin Thread', 'vi' => 'Ghim chủ đề'],
        'unpin_thread' => ['en' => 'Unpin Thread', 'vi' => 'Bỏ ghim chủ đề'],
        'move_thread' => ['en' => 'Move Thread', 'vi' => 'Di chuyển chủ đề'],
        'merge_thread' => ['en' => 'Merge Thread', 'vi' => 'Gộp chủ đề'],
        'split_thread' => ['en' => 'Split Thread', 'vi' => 'Tách chủ đề'],
        'sticky' => ['en' => 'Sticky', 'vi' => 'Ghim'],
        'locked' => ['en' => 'Locked', 'vi' => 'Khóa'],
        'pinned' => ['en' => 'Pinned', 'vi' => 'Đã ghim'],
        'featured' => ['en' => 'Featured', 'vi' => 'Nổi bật'],
        'closed' => ['en' => 'Closed', 'vi' => 'Đã đóng'],
        'open' => ['en' => 'Open', 'vi' => 'Mở'],
        'solved' => ['en' => 'Solved', 'vi' => 'Đã giải quyết'],
        'unsolved' => ['en' => 'Unsolved', 'vi' => 'Chưa giải quyết'],
        'hot' => ['en' => 'Hot', 'vi' => 'Nóng'],
        'trending' => ['en' => 'Trending', 'vi' => 'Xu hướng'],
        'latest' => ['en' => 'Latest', 'vi' => 'Mới nhất'],
        'oldest' => ['en' => 'Oldest', 'vi' => 'Cũ nhất'],
        'most_replies' => ['en' => 'Most Replies', 'vi' => 'Nhiều phản hồi nhất'],
        'most_views' => ['en' => 'Most Views', 'vi' => 'Nhiều lượt xem nhất'],
        'no_replies' => ['en' => 'No Replies', 'vi' => 'Chưa có phản hồi'],
        'last_post' => ['en' => 'Last Post', 'vi' => 'Bài viết cuối'],
        'first_post' => ['en' => 'First Post', 'vi' => 'Bài viết đầu'],
        'thread_starter' => ['en' => 'Thread Starter', 'vi' => 'Người tạo chủ đề'],
        'participants' => ['en' => 'Participants', 'vi' => 'Người tham gia'],
        'watchers' => ['en' => 'Watchers', 'vi' => 'Người theo dõi'],
        'subscribers' => ['en' => 'Subscribers', 'vi' => 'Người đăng ký'],
        'tags' => ['en' => 'Tags', 'vi' => 'Thẻ'],
        'prefix' => ['en' => 'Prefix', 'vi' => 'Tiền tố'],
        'category' => ['en' => 'Category', 'vi' => 'Danh mục'],
        'subcategory' => ['en' => 'Subcategory', 'vi' => 'Danh mục con'],
        'forum' => ['en' => 'Forum', 'vi' => 'Diễn đàn'],
        'subforum' => ['en' => 'Subforum', 'vi' => 'Diễn đàn con'],
    ],
    
    'posts' => [
        'title' => ['en' => 'Posts', 'vi' => 'Bài viết'],
        'new_post' => ['en' => 'New Post', 'vi' => 'Bài viết mới'],
        'reply' => ['en' => 'Reply', 'vi' => 'Trả lời'],
        'quote' => ['en' => 'Quote', 'vi' => 'Trích dẫn'],
        'edit_post' => ['en' => 'Edit Post', 'vi' => 'Chỉnh sửa bài viết'],
        'delete_post' => ['en' => 'Delete Post', 'vi' => 'Xóa bài viết'],
        'report_post' => ['en' => 'Report Post', 'vi' => 'Báo cáo bài viết'],
        'like_post' => ['en' => 'Like Post', 'vi' => 'Thích bài viết'],
        'unlike_post' => ['en' => 'Unlike Post', 'vi' => 'Bỏ thích bài viết'],
        'share_post' => ['en' => 'Share Post', 'vi' => 'Chia sẻ bài viết'],
        'bookmark_post' => ['en' => 'Bookmark Post', 'vi' => 'Đánh dấu bài viết'],
        'permalink' => ['en' => 'Permalink', 'vi' => 'Liên kết cố định'],
        'post_number' => ['en' => 'Post #', 'vi' => 'Bài viết #'],
        'original_post' => ['en' => 'Original Post', 'vi' => 'Bài viết gốc'],
        'quoted_post' => ['en' => 'Quoted Post', 'vi' => 'Bài viết được trích dẫn'],
        'edited_by' => ['en' => 'Edited by', 'vi' => 'Được chỉnh sửa bởi'],
        'edited_at' => ['en' => 'Edited at', 'vi' => 'Chỉnh sửa lúc'],
        'posted_by' => ['en' => 'Posted by', 'vi' => 'Đăng bởi'],
        'posted_at' => ['en' => 'Posted at', 'vi' => 'Đăng lúc'],
        'post_count' => ['en' => 'Post Count', 'vi' => 'Số bài viết'],
        'reputation' => ['en' => 'Reputation', 'vi' => 'Danh tiếng'],
        'likes_received' => ['en' => 'Likes Received', 'vi' => 'Lượt thích nhận được'],
        'thanks_received' => ['en' => 'Thanks Received', 'vi' => 'Lời cảm ơn nhận được'],
        'best_answer' => ['en' => 'Best Answer', 'vi' => 'Câu trả lời hay nhất'],
        'mark_as_solution' => ['en' => 'Mark as Solution', 'vi' => 'Đánh dấu là giải pháp'],
        'unmark_solution' => ['en' => 'Unmark Solution', 'vi' => 'Bỏ đánh dấu giải pháp'],
        'helpful' => ['en' => 'Helpful', 'vi' => 'Hữu ích'],
        'not_helpful' => ['en' => 'Not Helpful', 'vi' => 'Không hữu ích'],
        'spam' => ['en' => 'Spam', 'vi' => 'Thư rác'],
        'inappropriate' => ['en' => 'Inappropriate', 'vi' => 'Không phù hợp'],
        'off_topic' => ['en' => 'Off Topic', 'vi' => 'Lạc đề'],
        'duplicate' => ['en' => 'Duplicate', 'vi' => 'Trùng lặp'],
        'low_quality' => ['en' => 'Low Quality', 'vi' => 'Chất lượng thấp'],
    ],
    
    'poll' => [
        'title' => ['en' => 'Poll', 'vi' => 'Cuộc bình chọn'],
        'create_poll' => ['en' => 'Create Poll', 'vi' => 'Tạo cuộc bình chọn'],
        'edit_poll' => ['en' => 'Edit Poll', 'vi' => 'Chỉnh sửa cuộc bình chọn'],
        'delete_poll' => ['en' => 'Delete Poll', 'vi' => 'Xóa cuộc bình chọn'],
        'vote' => ['en' => 'Vote', 'vi' => 'Bình chọn'],
        'votes' => ['en' => 'vote|votes', 'vi' => 'lượt bình chọn|lượt bình chọn'],
        'change_vote' => ['en' => 'Change Vote', 'vi' => 'Thay đổi phiếu bầu'],
        'update_vote' => ['en' => 'Update Vote', 'vi' => 'Cập nhật phiếu bầu'],
        'remove_vote' => ['en' => 'Remove Vote', 'vi' => 'Gỡ bỏ phiếu bầu'],
        'view_results' => ['en' => 'View Results', 'vi' => 'Xem kết quả'],
        'hide_results' => ['en' => 'Hide Results', 'vi' => 'Ẩn kết quả'],
        'show_voters' => ['en' => 'Show Voters', 'vi' => 'Hiện người bình chọn'],
        'hide_voters' => ['en' => 'Hide Voters', 'vi' => 'Ẩn người bình chọn'],
        'voters' => ['en' => 'Voters', 'vi' => 'Người bình chọn'],
        'total_votes' => ['en' => 'Total Votes', 'vi' => 'Tổng số phiếu'],
        'poll_question' => ['en' => 'Poll Question', 'vi' => 'Câu hỏi bình chọn'],
        'poll_options' => ['en' => 'Poll Options', 'vi' => 'Tùy chọn bình chọn'],
        'add_option' => ['en' => 'Add Option', 'vi' => 'Thêm tùy chọn'],
        'remove_option' => ['en' => 'Remove Option', 'vi' => 'Gỡ bỏ tùy chọn'],
        'multiple_choice' => ['en' => 'Multiple Choice', 'vi' => 'Nhiều lựa chọn'],
        'single_choice' => ['en' => 'Single Choice', 'vi' => 'Một lựa chọn'],
        'allow_multiple' => ['en' => 'Allow Multiple Selections', 'vi' => 'Cho phép chọn nhiều'],
        'max_choices' => ['en' => 'Maximum Choices', 'vi' => 'Số lựa chọn tối đa'],
        'poll_duration' => ['en' => 'Poll Duration', 'vi' => 'Thời gian bình chọn'],
        'poll_expires' => ['en' => 'Poll Expires', 'vi' => 'Cuộc bình chọn hết hạn'],
        'poll_expired' => ['en' => 'Poll Expired', 'vi' => 'Cuộc bình chọn đã hết hạn'],
        'poll_active' => ['en' => 'Poll Active', 'vi' => 'Cuộc bình chọn đang hoạt động'],
        'poll_closed' => ['en' => 'Poll Closed', 'vi' => 'Cuộc bình chọn đã đóng'],
        'close_poll' => ['en' => 'Close Poll', 'vi' => 'Đóng cuộc bình chọn'],
        'reopen_poll' => ['en' => 'Reopen Poll', 'vi' => 'Mở lại cuộc bình chọn'],
        'anonymous_voting' => ['en' => 'Anonymous Voting', 'vi' => 'Bình chọn ẩn danh'],
        'public_voting' => ['en' => 'Public Voting', 'vi' => 'Bình chọn công khai'],
        'loading_results' => ['en' => 'Loading results...', 'vi' => 'Đang tải kết quả...'],
        'no_votes_yet' => ['en' => 'No votes yet', 'vi' => 'Chưa có phiếu bầu nào'],
        'you_voted' => ['en' => 'You voted', 'vi' => 'Bạn đã bình chọn'],
        'you_have_not_voted' => ['en' => 'You have not voted', 'vi' => 'Bạn chưa bình chọn'],
        'vote_to_see_results' => ['en' => 'Vote to see results', 'vi' => 'Bình chọn để xem kết quả'],
        'percentage' => ['en' => 'Percentage', 'vi' => 'Phần trăm'],
        'vote_count' => ['en' => 'Vote Count', 'vi' => 'Số phiếu bầu'],
    ],
    
    'categories' => [
        'title' => ['en' => 'Categories', 'vi' => 'Danh mục'],
        'all_categories' => ['en' => 'All Categories', 'vi' => 'Tất cả danh mục'],
        'category' => ['en' => 'Category', 'vi' => 'Danh mục'],
        'subcategory' => ['en' => 'Subcategory', 'vi' => 'Danh mục con'],
        'parent_category' => ['en' => 'Parent Category', 'vi' => 'Danh mục cha'],
        'child_categories' => ['en' => 'Child Categories', 'vi' => 'Danh mục con'],
        'category_description' => ['en' => 'Category Description', 'vi' => 'Mô tả danh mục'],
        'category_rules' => ['en' => 'Category Rules', 'vi' => 'Quy tắc danh mục'],
        'category_moderators' => ['en' => 'Category Moderators', 'vi' => 'Người điều hành danh mục'],
        'threads_count' => ['en' => 'Threads', 'vi' => 'Chủ đề'],
        'posts_count' => ['en' => 'Posts', 'vi' => 'Bài viết'],
        'last_activity' => ['en' => 'Last Activity', 'vi' => 'Hoạt động cuối'],
        'no_threads' => ['en' => 'No threads in this category', 'vi' => 'Không có chủ đề nào trong danh mục này'],
        'create_first_thread' => ['en' => 'Create the first thread', 'vi' => 'Tạo chủ đề đầu tiên'],
        'private_category' => ['en' => 'Private Category', 'vi' => 'Danh mục riêng tư'],
        'public_category' => ['en' => 'Public Category', 'vi' => 'Danh mục công khai'],
        'restricted_category' => ['en' => 'Restricted Category', 'vi' => 'Danh mục hạn chế'],
        'archived_category' => ['en' => 'Archived Category', 'vi' => 'Danh mục lưu trữ'],
        'featured_category' => ['en' => 'Featured Category', 'vi' => 'Danh mục nổi bật'],
    ],
    
    'search' => [
        'title' => ['en' => 'Search', 'vi' => 'Tìm kiếm'],
        'search_forums' => ['en' => 'Search Forums', 'vi' => 'Tìm kiếm diễn đàn'],
        'search_threads' => ['en' => 'Search Threads', 'vi' => 'Tìm kiếm chủ đề'],
        'search_posts' => ['en' => 'Search Posts', 'vi' => 'Tìm kiếm bài viết'],
        'search_users' => ['en' => 'Search Users', 'vi' => 'Tìm kiếm người dùng'],
        'search_results' => ['en' => 'Search Results', 'vi' => 'Kết quả tìm kiếm'],
        'no_results' => ['en' => 'No results found', 'vi' => 'Không tìm thấy kết quả'],
        'search_query' => ['en' => 'Search Query', 'vi' => 'Từ khóa tìm kiếm'],
        'search_in' => ['en' => 'Search in', 'vi' => 'Tìm kiếm trong'],
        'search_by' => ['en' => 'Search by', 'vi' => 'Tìm kiếm theo'],
        'search_author' => ['en' => 'Search by Author', 'vi' => 'Tìm kiếm theo tác giả'],
        'search_date' => ['en' => 'Search by Date', 'vi' => 'Tìm kiếm theo ngày'],
        'search_category' => ['en' => 'Search in Category', 'vi' => 'Tìm kiếm trong danh mục'],
        'advanced_search' => ['en' => 'Advanced Search', 'vi' => 'Tìm kiếm nâng cao'],
        'quick_search' => ['en' => 'Quick Search', 'vi' => 'Tìm kiếm nhanh'],
        'search_placeholder' => ['en' => 'Search forums...', 'vi' => 'Tìm kiếm diễn đàn...'],
        'search_tips' => ['en' => 'Search Tips', 'vi' => 'Mẹo tìm kiếm'],
        'search_help' => ['en' => 'Search Help', 'vi' => 'Trợ giúp tìm kiếm'],
        'recent_searches' => ['en' => 'Recent Searches', 'vi' => 'Tìm kiếm gần đây'],
        'popular_searches' => ['en' => 'Popular Searches', 'vi' => 'Tìm kiếm phổ biến'],
        'saved_searches' => ['en' => 'Saved Searches', 'vi' => 'Tìm kiếm đã lưu'],
        'save_search' => ['en' => 'Save Search', 'vi' => 'Lưu tìm kiếm'],
        'delete_search' => ['en' => 'Delete Search', 'vi' => 'Xóa tìm kiếm'],
        'search_filters' => ['en' => 'Search Filters', 'vi' => 'Bộ lọc tìm kiếm'],
        'filter_by_date' => ['en' => 'Filter by Date', 'vi' => 'Lọc theo ngày'],
        'filter_by_author' => ['en' => 'Filter by Author', 'vi' => 'Lọc theo tác giả'],
        'filter_by_category' => ['en' => 'Filter by Category', 'vi' => 'Lọc theo danh mục'],
        'sort_by_relevance' => ['en' => 'Sort by Relevance', 'vi' => 'Sắp xếp theo độ liên quan'],
        'sort_by_date' => ['en' => 'Sort by Date', 'vi' => 'Sắp xếp theo ngày'],
        'sort_by_replies' => ['en' => 'Sort by Replies', 'vi' => 'Sắp xếp theo phản hồi'],
        'sort_by_views' => ['en' => 'Sort by Views', 'vi' => 'Sắp xếp theo lượt xem'],
    ]
];

echo "\n🔧 CREATING FORUM TRANSLATION FILES...\n";
echo "======================================\n";

$createdFiles = 0;
$totalTranslations = 0;

foreach ($forumTranslations as $section => $translations) {
    foreach (['en', 'vi'] as $locale) {
        $filePath = $basePath . "/resources/lang/$locale/forum/$section.php";
        $dirPath = dirname($filePath);
        
        // Create directory if needed
        if (!is_dir($dirPath)) {
            mkdir($dirPath, 0755, true);
            echo "📁 Created directory: " . str_replace($basePath . '/', '', $dirPath) . "\n";
        }
        
        // Load existing translations
        $existingTranslations = [];
        if (file_exists($filePath)) {
            $existingTranslations = include $filePath;
            if (!is_array($existingTranslations)) {
                $existingTranslations = [];
            }
        }
        
        // Add new translations
        $newTranslations = [];
        foreach ($translations as $key => $localeTranslations) {
            $newTranslations[$key] = $localeTranslations[$locale];
        }
        
        // Merge with existing
        $mergedTranslations = array_merge($existingTranslations, $newTranslations);
        
        // Generate file content
        $fileContent = "<?php\n\n/**\n * Forum $section translations\n * Updated: " . date('Y-m-d H:i:s') . "\n */\n\nreturn " . var_export($mergedTranslations, true) . ";\n";
        
        // Save file
        file_put_contents($filePath, $fileContent);
        
        echo "✅ Updated: $locale/forum/$section.php (" . count($newTranslations) . " translations)\n";
        $createdFiles++;
        $totalTranslations += count($newTranslations);
    }
}

echo "\n📊 SUMMARY\n";
echo "==========\n";
echo "Forum sections processed: " . count($forumTranslations) . "\n";
echo "Translation files created/updated: $createdFiles\n";
echo "Total translations added: $totalTranslations\n";

// Clear caches
echo "\n🧹 CLEARING CACHES...\n";
echo "=====================\n";

$commands = [
    'php artisan cache:clear',
    'php artisan view:clear',
    'php artisan config:clear'
];

foreach ($commands as $command) {
    echo "Running: $command\n";
    $output = shell_exec("cd $basePath && $command 2>&1");
    echo "   " . trim($output) . "\n";
}

echo "\n🧪 TESTING FORUM KEYS...\n";
echo "========================\n";

$testForumKeys = [
    'forum.threads.title',
    'forum.threads.sticky',
    'forum.threads.locked',
    'forum.posts.reply',
    'forum.poll.vote',
    'forum.poll.votes',
    'forum.poll.total_votes',
    'forum.categories.title',
    'forum.search.title'
];

$workingCount = 0;
foreach ($testForumKeys as $key) {
    $result = __($key);
    if ($result !== $key) {
        echo "✅ __('$key') → '$result'\n";
        $workingCount++;
    } else {
        echo "❌ __('$key') - Not found\n";
    }
}

echo "\nForum keys success rate: " . round(($workingCount / count($testForumKeys)) * 100, 1) . "%\n";

echo "\n🎯 NEXT STEPS\n";
echo "=============\n";
echo "1. ✅ Created comprehensive forum translations\n";
echo "2. 🔄 Process auth keys (138 keys) next\n";
echo "3. 🔄 Run comprehensive validation to see overall improvement\n";
echo "4. 🔄 Test critical forum functionality in browser\n";
echo "5. 🔄 Consider processing remaining categories\n\n";

echo "💡 IMPACT ASSESSMENT\n";
echo "====================\n";
echo "Forum keys are core functionality - success here improves user engagement.\n";
echo "Poll functionality should now work better with proper translations.\n";
echo "Thread and post management should display proper labels.\n";
