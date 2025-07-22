<?php

/**
 * COMPLETE ALL REMAINING TASKS
 * Xử lý tất cả các tasks còn lại trong task list
 */

echo "=== COMPLETING ALL REMAINING TASKS ===\n\n";

// All remaining keys organized by file/category
$allRemainingKeys = [
    // Mobile Navigation keys
    'mobile_nav' => [
        'forum.title' => ['vi' => 'Diễn đàn', 'en' => 'Forum'],
        'ui.menu.toggle' => ['vi' => 'Chuyển đổi menu', 'en' => 'Toggle menu'],
        'ui.menu.close' => ['vi' => 'Đóng menu', 'en' => 'Close menu'],
        'marketplace.browse' => ['vi' => 'Duyệt marketplace', 'en' => 'Browse marketplace'],
        'marketplace.categories' => ['vi' => 'Danh mục', 'en' => 'Categories'],
        'marketplace.featured' => ['vi' => 'Nổi bật', 'en' => 'Featured'],
        'marketplace.new_arrivals' => ['vi' => 'Hàng mới về', 'en' => 'New Arrivals'],
        'marketplace.deals' => ['vi' => 'Ưu đãi', 'en' => 'Deals'],
    ],
    
    // Auth Wizard keys
    'auth_wizard' => [
        'register.wizard.step2.title' => ['vi' => 'Bước 2: Thông tin bổ sung', 'en' => 'Step 2: Additional Information'],
        'register.wizard.step2.company_info' => ['vi' => 'Thông tin công ty', 'en' => 'Company Information'],
        'register.wizard.step2.industry_selection' => ['vi' => 'Chọn ngành nghề', 'en' => 'Industry Selection'],
        'register.wizard.step2.company_size' => ['vi' => 'Quy mô công ty', 'en' => 'Company Size'],
        'register.wizard.step2.job_function' => ['vi' => 'Chức năng công việc', 'en' => 'Job Function'],
        'register.wizard.step2.experience_level' => ['vi' => 'Mức độ kinh nghiệm', 'en' => 'Experience Level'],
        'register.wizard.step2.interests' => ['vi' => 'Sở thích', 'en' => 'Interests'],
        'register.wizard.step2.marketing_preferences' => ['vi' => 'Tùy chọn marketing', 'en' => 'Marketing Preferences'],
        'register.wizard.step2.newsletter' => ['vi' => 'Đăng ký nhận bản tin', 'en' => 'Subscribe to newsletter'],
        'register.wizard.step2.complete' => ['vi' => 'Hoàn thành đăng ký', 'en' => 'Complete registration'],
    ],
    
    // Forum Search keys
    'forum_search' => [
        'search.advanced.title' => ['vi' => 'Tìm kiếm nâng cao', 'en' => 'Advanced Search'],
        'search.advanced.keywords' => ['vi' => 'Từ khóa', 'en' => 'Keywords'],
        'search.advanced.author' => ['vi' => 'Tác giả', 'en' => 'Author'],
        'search.advanced.forum' => ['vi' => 'Diễn đàn', 'en' => 'Forum'],
        'search.advanced.date_range' => ['vi' => 'Khoảng thời gian', 'en' => 'Date Range'],
        'search.advanced.sort_by' => ['vi' => 'Sắp xếp theo', 'en' => 'Sort By'],
        'search.advanced.results_per_page' => ['vi' => 'Kết quả mỗi trang', 'en' => 'Results per page'],
        'search.advanced.search_in' => ['vi' => 'Tìm trong', 'en' => 'Search in'],
        'search.advanced.titles_only' => ['vi' => 'Chỉ tiêu đề', 'en' => 'Titles only'],
        'search.advanced.content_only' => ['vi' => 'Chỉ nội dung', 'en' => 'Content only'],
    ],
    
    // Thread Create keys
    'thread_create' => [
        'create.title' => ['vi' => 'Tạo chủ đề mới', 'en' => 'Create New Thread'],
        'create.thread_title' => ['vi' => 'Tiêu đề chủ đề', 'en' => 'Thread Title'],
        'create.content' => ['vi' => 'Nội dung', 'en' => 'Content'],
        'create.tags' => ['vi' => 'Thẻ', 'en' => 'Tags'],
        'create.attachments' => ['vi' => 'Tệp đính kèm', 'en' => 'Attachments'],
        'create.poll' => ['vi' => 'Tạo bình chọn', 'en' => 'Create Poll'],
        'create.preview' => ['vi' => 'Xem trước', 'en' => 'Preview'],
        'create.post_thread' => ['vi' => 'Đăng chủ đề', 'en' => 'Post Thread'],
        'create.save_draft' => ['vi' => 'Lưu bản nháp', 'en' => 'Save Draft'],
        'create.guidelines' => ['vi' => 'Hướng dẫn đăng bài', 'en' => 'Posting Guidelines'],
    ],
    
    // Basic Search keys
    'basic_search' => [
        'basic.placeholder' => ['vi' => 'Tìm kiếm...', 'en' => 'Search...'],
        'basic.search_button' => ['vi' => 'Tìm kiếm', 'en' => 'Search'],
        'basic.advanced_link' => ['vi' => 'Tìm kiếm nâng cao', 'en' => 'Advanced Search'],
        'basic.recent_searches' => ['vi' => 'Tìm kiếm gần đây', 'en' => 'Recent Searches'],
        'basic.popular_searches' => ['vi' => 'Tìm kiếm phổ biến', 'en' => 'Popular Searches'],
        'basic.no_results' => ['vi' => 'Không tìm thấy kết quả', 'en' => 'No results found'],
        'basic.results_for' => ['vi' => 'Kết quả cho', 'en' => 'Results for'],
        'basic.showing_results' => ['vi' => 'Hiển thị kết quả', 'en' => 'Showing results'],
        'basic.clear_search' => ['vi' => 'Xóa tìm kiếm', 'en' => 'Clear search'],
    ],
    
    // Community Companies keys
    'companies' => [
        'list.title' => ['vi' => 'Danh sách công ty', 'en' => 'Companies List'],
        'list.featured' => ['vi' => 'Công ty nổi bật', 'en' => 'Featured Companies'],
        'list.verified' => ['vi' => 'Đã xác thực', 'en' => 'Verified'],
        'list.employees' => ['vi' => 'nhân viên', 'en' => 'employees'],
        'list.industry' => ['vi' => 'Ngành', 'en' => 'Industry'],
        'list.location' => ['vi' => 'Địa điểm', 'en' => 'Location'],
        'list.view_profile' => ['vi' => 'Xem hồ sơ', 'en' => 'View Profile'],
        'list.contact' => ['vi' => 'Liên hệ', 'en' => 'Contact'],
        'list.follow' => ['vi' => 'Theo dõi', 'en' => 'Follow'],
    ],
    
    // Final keys
    'final_keys' => [
        'thread.last_post_by' => ['vi' => 'Bài cuối bởi', 'en' => 'Last post by'],
        'feature.marketplace.actions.cancel' => ['vi' => 'Hủy', 'en' => 'Cancel'],
        'buttons.view_all' => ['vi' => 'Xem tất cả', 'en' => 'View All'],
        'buttons.learn_more' => ['vi' => 'Tìm hiểu thêm', 'en' => 'Learn More'],
        'buttons.get_started' => ['vi' => 'Bắt đầu', 'en' => 'Get Started'],
    ],
];

// Function to add keys to translation files
function addKeysToFile($filePath, $keys, $lang) {
    if (!file_exists($filePath)) {
        echo "❌ File not found: $filePath\n";
        return false;
    }
    
    $content = file_get_contents($filePath);
    if ($content === false) {
        echo "❌ Failed to read $filePath\n";
        return false;
    }
    
    // Find the last closing bracket (either ]; or );)
    $lastBracketPos = strrpos($content, '];');
    if ($lastBracketPos === false) {
        $lastBracketPos = strrpos($content, ');');
        if ($lastBracketPos === false) {
            echo "❌ Could not find closing bracket in $filePath\n";
            return false;
        }
    }
    
    // Build new keys string
    $newKeysString = '';
    $addedCount = 0;
    
    foreach ($keys as $key => $translations) {
        if (isset($translations[$lang])) {
            $value = $translations[$lang];
            // Escape single quotes in the value
            $value = str_replace("'", "\\'", $value);
            $newKeysString .= "  '$key' => '$value',\n";
            $addedCount++;
        }
    }
    
    if (empty($newKeysString)) {
        echo "ℹ️  No keys to add for $lang\n";
        return true;
    }
    
    // Insert new keys before the closing bracket
    $beforeClosing = substr($content, 0, $lastBracketPos);
    $afterClosing = substr($content, $lastBracketPos);
    
    $newContent = $beforeClosing . $newKeysString . $afterClosing;
    
    // Write back to file
    if (file_put_contents($filePath, $newContent)) {
        echo "✅ Added $addedCount keys to $filePath\n";
        return true;
    } else {
        echo "❌ Failed to write $filePath\n";
        return false;
    }
}

// Map categories to files
$categoryFileMap = [
    'mobile_nav' => ['forum', 'ui', 'marketplace'],
    'auth_wizard' => ['auth'],
    'forum_search' => ['forum'],
    'thread_create' => ['forum'],
    'basic_search' => ['search'],
    'companies' => ['companies'],
    'final_keys' => ['common'],
];

$totalAdded = 0;

foreach ($allRemainingKeys as $category => $keys) {
    echo "📁 Processing category: $category\n";
    
    $files = $categoryFileMap[$category] ?? ['common'];
    
    foreach ($files as $file) {
        // Filter keys for this file
        $fileKeys = [];
        foreach ($keys as $key => $translations) {
            if (strpos($key, $file . '.') === 0 || $file === 'common') {
                $fileKeys[$key] = $translations;
            }
        }
        
        if (empty($fileKeys)) continue;
        
        // Add to Vietnamese file
        $viFile = __DIR__ . "/resources/lang/vi/$file.php";
        if (addKeysToFile($viFile, $fileKeys, 'vi')) {
            $totalAdded += count($fileKeys);
        }
        
        // Add to English file
        $enFile = __DIR__ . "/resources/lang/en/$file.php";
        addKeysToFile($enFile, $fileKeys, 'en');
    }
    
    echo "\n";
}

echo "=== SUMMARY ===\n";
echo "Total remaining keys added: $totalAdded\n";
echo "Categories processed: " . count($allRemainingKeys) . "\n";

echo "\n✅ All remaining tasks completed at " . date('Y-m-d H:i:s') . "\n";
?>
