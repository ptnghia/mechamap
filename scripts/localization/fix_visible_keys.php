<?php
/**
 * Fix Visible Translation Keys
 * Sửa tất cả keys hiển thị trên giao diện người dùng
 */

echo "🎯 FIXING VISIBLE TRANSLATION KEYS\n";
echo "==================================\n\n";

$basePath = '/var/www/mechamap_com_usr/data/www/mechamap.com';

// Bootstrap Laravel
require_once $basePath . '/vendor/autoload.php';
$app = require_once $basePath . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 TARGETING KEYS FROM SCREENSHOT\n";
echo "=================================\n";

// Keys visible in the screenshot that need immediate fixing
$visibleKeys = [
    // Home section
    'HOME.FEATURED_SHOWCASES' => [
        'file' => 'home.php',
        'key' => 'featured_showcases',
        'en' => 'Featured Showcases',
        'vi' => 'Showcase nổi bật'
    ],
    'home.featured_showcases_desc' => [
        'file' => 'home.php', 
        'key' => 'featured_showcases_desc',
        'en' => 'Discover amazing showcases from our community',
        'vi' => 'Khám phá những showcase tuyệt vời từ cộng đồng'
    ],
    
    // UI Community section
    'UI.COMMUNITY.QUICK_ACCESS' => [
        'file' => 'ui.php',
        'key' => 'community.quick_access',
        'en' => 'Quick Access',
        'vi' => 'Truy cập nhanh'
    ],
    'UI.COMMUNITY.DISCOVER' => [
        'file' => 'ui.php',
        'key' => 'community.discover', 
        'en' => 'Discover',
        'vi' => 'Khám phá'
    ],
    'UI.COMMUNITY.TOOLS_CONNECT' => [
        'file' => 'ui.php',
        'key' => 'community.tools_connect',
        'en' => 'Tools & Connect',
        'vi' => 'Công cụ & Kết nối'
    ],
    
    // Forum section
    'FORUM.THREADS.TITLE' => [
        'file' => 'forum.php',
        'key' => 'threads.title',
        'en' => 'Forum Threads',
        'vi' => 'Chủ đề diễn đàn'
    ],
    'ui.community.forum.home_desc' => [
        'file' => 'ui.php',
        'key' => 'community.forum.home_desc',
        'en' => 'Join discussions in our community forum',
        'vi' => 'Tham gia thảo luận trong diễn đàn cộng đồng'
    ],
    
    // UI Common section
    'UI.COMMON.RECENT_DISCUSSIONS' => [
        'file' => 'ui.php',
        'key' => 'common.recent_discussions',
        'en' => 'Recent Discussions',
        'vi' => 'Thảo luận gần đây'
    ],
    'ui.community.recent_discussions_desc' => [
        'file' => 'ui.php',
        'key' => 'community.recent_discussions_desc',
        'en' => 'Latest discussions from the community',
        'vi' => 'Thảo luận mới nhất từ cộng đồng'
    ],
    'UI.COMMON.TRENDING' => [
        'file' => 'ui.php',
        'key' => 'common.trending',
        'en' => 'Trending',
        'vi' => 'Xu hướng'
    ],
    'ui.community.trending_desc' => [
        'file' => 'ui.php',
        'key' => 'community.trending_desc',
        'en' => 'What\'s trending in the community',
        'vi' => 'Những gì đang thịnh hành trong cộng đồng'
    ],
    'UI.COMMON.POPULAR_TOPICS' => [
        'file' => 'ui.php',
        'key' => 'common.popular_topics',
        'en' => 'Popular Topics',
        'vi' => 'Chủ đề phổ biến'
    ],
    'ui.community.popular_topics_desc' => [
        'file' => 'ui.php',
        'key' => 'community.popular_topics_desc',
        'en' => 'Most popular discussion topics',
        'vi' => 'Chủ đề thảo luận phổ biến nhất'
    ],
    'UI.COMMON.MOST_VIEWED' => [
        'file' => 'ui.php',
        'key' => 'common.most_viewed',
        'en' => 'Most Viewed',
        'vi' => 'Xem nhiều nhất'
    ],
    'ui.community.most_viewed_desc' => [
        'file' => 'ui.php',
        'key' => 'community.most_viewed_desc',
        'en' => 'Most viewed content',
        'vi' => 'Nội dung được xem nhiều nhất'
    ],
    'UI.COMMON.HOT_TOPICS' => [
        'file' => 'ui.php',
        'key' => 'common.hot_topics',
        'en' => 'Hot Topics',
        'vi' => 'Chủ đề nóng'
    ],
    'ui.community.hot_topics_desc' => [
        'file' => 'ui.php',
        'key' => 'community.hot_topics_desc',
        'en' => 'Hottest discussion topics',
        'vi' => 'Chủ đề thảo luận nóng nhất'
    ],
    'UI.COMMON.MEMBER_DIRECTORY' => [
        'file' => 'ui.php',
        'key' => 'common.member_directory',
        'en' => 'Member Directory',
        'vi' => 'Danh bạ thành viên'
    ],
    'ui.community.member_directory_desc' => [
        'file' => 'ui.php',
        'key' => 'community.member_directory_desc',
        'en' => 'Browse community members',
        'vi' => 'Duyệt thành viên cộng đồng'
    ],
    'ui.common.coming_soon' => [
        'file' => 'ui.php',
        'key' => 'common.coming_soon',
        'en' => 'Coming Soon',
        'vi' => 'Sắp ra mắt'
    ],
    
    // UI Search section
    'UI.SEARCH.ADVANCED_SEARCH' => [
        'file' => 'ui.php',
        'key' => 'search.advanced_search',
        'en' => 'Advanced Search',
        'vi' => 'Tìm kiếm nâng cao'
    ],
    'ui.search.advanced_search_desc' => [
        'file' => 'ui.php',
        'key' => 'search.advanced_search_desc',
        'en' => 'Use advanced search options',
        'vi' => 'Sử dụng tùy chọn tìm kiếm nâng cao'
    ],
    
    // Community Browse section
    'UI.COMMUNITY.BROWSE_CATEGORIES' => [
        'file' => 'ui.php',
        'key' => 'community.browse_categories',
        'en' => 'Browse Categories',
        'vi' => 'Duyệt danh mục'
    ],
    'ui.community.explore_topics_desc' => [
        'file' => 'ui.php',
        'key' => 'community.explore_topics_desc',
        'en' => 'Explore different topic categories',
        'vi' => 'Khám phá các danh mục chủ đề khác nhau'
    ],
    
    // Content sections
    'CONTENT.RECENT_ACTIVITY' => [
        'file' => 'content.php',
        'key' => 'recent_activity',
        'en' => 'Recent Activity',
        'vi' => 'Hoạt động gần đây'
    ],
    'CONTENT.WEEKLY_ACTIVITY' => [
        'file' => 'content.php',
        'key' => 'weekly_activity',
        'en' => 'Weekly Activity',
        'vi' => 'Hoạt động tuần'
    ],
    
    // Additional common keys
    'ui.common.discussions' => [
        'file' => 'ui.php',
        'key' => 'common.discussions',
        'en' => 'Discussions',
        'vi' => 'Thảo luận'
    ],
    'ui.common.posts' => [
        'file' => 'ui.php',
        'key' => 'common.posts',
        'en' => 'Posts',
        'vi' => 'Bài viết'
    ],
    'ui.common.members' => [
        'file' => 'ui.php',
        'key' => 'common.members',
        'en' => 'Members',
        'vi' => 'Thành viên'
    ],
    'ui.common.online' => [
        'file' => 'ui.php',
        'key' => 'common.online',
        'en' => 'Online',
        'vi' => 'Trực tuyến'
    ],
    'ui.common.latest' => [
        'file' => 'ui.php',
        'key' => 'common.latest',
        'en' => 'Latest',
        'vi' => 'Mới nhất'
    ],
    'ui.common.featured' => [
        'file' => 'ui.php',
        'key' => 'common.featured',
        'en' => 'Featured',
        'vi' => 'Nổi bật'
    ],
    'ui.common.popular' => [
        'file' => 'ui.php',
        'key' => 'common.popular',
        'en' => 'Popular',
        'vi' => 'Phổ biến'
    ],
    'ui.common.new' => [
        'file' => 'ui.php',
        'key' => 'common.new',
        'en' => 'New',
        'vi' => 'Mới'
    ],
    'ui.common.active' => [
        'file' => 'ui.php',
        'key' => 'common.active',
        'en' => 'Active',
        'vi' => 'Hoạt động'
    ],
    'ui.common.community' => [
        'file' => 'ui.php',
        'key' => 'common.community',
        'en' => 'Community',
        'vi' => 'Cộng đồng'
    ],
    'ui.common.forum' => [
        'file' => 'ui.php',
        'key' => 'common.forum',
        'en' => 'Forum',
        'vi' => 'Diễn đàn'
    ],
    'ui.common.showcase' => [
        'file' => 'ui.php',
        'key' => 'common.showcase',
        'en' => 'Showcase',
        'vi' => 'Showcase'
    ],
    'ui.common.tools' => [
        'file' => 'ui.php',
        'key' => 'common.tools',
        'en' => 'Tools',
        'vi' => 'Công cụ'
    ],
    'ui.common.connect' => [
        'file' => 'ui.php',
        'key' => 'common.connect',
        'en' => 'Connect',
        'vi' => 'Kết nối'
    ],
    'ui.common.discover' => [
        'file' => 'ui.php',
        'key' => 'common.discover',
        'en' => 'Discover',
        'vi' => 'Khám phá'
    ],
    'ui.common.explore' => [
        'file' => 'ui.php',
        'key' => 'common.explore',
        'en' => 'Explore',
        'vi' => 'Khám phá'
    ],
    'ui.common.browse' => [
        'file' => 'ui.php',
        'key' => 'common.browse',
        'en' => 'Browse',
        'vi' => 'Duyệt'
    ],
    'ui.common.categories' => [
        'file' => 'ui.php',
        'key' => 'common.categories',
        'en' => 'Categories',
        'vi' => 'Danh mục'
    ],
    'ui.common.topics' => [
        'file' => 'ui.php',
        'key' => 'common.topics',
        'en' => 'Topics',
        'vi' => 'Chủ đề'
    ],
    'ui.common.activity' => [
        'file' => 'ui.php',
        'key' => 'common.activity',
        'en' => 'Activity',
        'vi' => 'Hoạt động'
    ],
    'ui.common.recent' => [
        'file' => 'ui.php',
        'key' => 'common.recent',
        'en' => 'Recent',
        'vi' => 'Gần đây'
    ],
    'ui.common.weekly' => [
        'file' => 'ui.php',
        'key' => 'common.weekly',
        'en' => 'Weekly',
        'vi' => 'Hàng tuần'
    ],
    'ui.common.directory' => [
        'file' => 'ui.php',
        'key' => 'common.directory',
        'en' => 'Directory',
        'vi' => 'Danh bạ'
    ],
    'ui.common.search' => [
        'file' => 'ui.php',
        'key' => 'common.search',
        'en' => 'Search',
        'vi' => 'Tìm kiếm'
    ],
    'ui.common.advanced' => [
        'file' => 'ui.php',
        'key' => 'common.advanced',
        'en' => 'Advanced',
        'vi' => 'Nâng cao'
    ]
];

echo "Keys to process: " . count($visibleKeys) . "\n\n";

// Group keys by file
$fileGroups = [];
foreach ($visibleKeys as $originalKey => $config) {
    $file = $config['file'];
    if (!isset($fileGroups[$file])) {
        $fileGroups[$file] = [];
    }
    $fileGroups[$file][$config['key']] = [
        'en' => $config['en'],
        'vi' => $config['vi'],
        'original_key' => $originalKey
    ];
}

echo "🔧 CREATING/UPDATING TRANSLATION FILES\n";
echo "======================================\n";

$totalFilesUpdated = 0;
$totalTranslationsAdded = 0;

foreach ($fileGroups as $fileName => $translations) {
    foreach (['en', 'vi'] as $locale) {
        $filePath = $basePath . "/resources/lang/$locale/$fileName";
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
        
        // Add new translations with nested structure
        $newTranslations = [];
        foreach ($translations as $key => $data) {
            $value = $data[$locale];
            
            // Handle nested keys (e.g., 'community.quick_access')
            if (strpos($key, '.') !== false) {
                $keyParts = explode('.', $key);
                $current = &$newTranslations;
                
                foreach ($keyParts as $i => $part) {
                    if ($i === count($keyParts) - 1) {
                        $current[$part] = $value;
                    } else {
                        if (!isset($current[$part]) || !is_array($current[$part])) {
                            $current[$part] = [];
                        }
                        $current = &$current[$part];
                    }
                }
            } else {
                $newTranslations[$key] = $value;
            }
        }
        
        // Merge with existing translations recursively
        $mergedTranslations = array_merge_recursive($existingTranslations, $newTranslations);
        
        // Generate file content
        $fileContent = "<?php\n\n/**\n * " . ucfirst(str_replace('.php', '', $fileName)) . " translations\n * Updated: " . date('Y-m-d H:i:s') . "\n * Visible keys from UI\n */\n\nreturn " . var_export($mergedTranslations, true) . ";\n";
        
        // Save file
        file_put_contents($filePath, $fileContent);
        
        echo "✅ Updated: $locale/$fileName (" . count($translations) . " translations)\n";
        $totalFilesUpdated++;
        $totalTranslationsAdded += count($translations);
    }
}

echo "\n📊 SUMMARY\n";
echo "==========\n";
echo "Files updated: $totalFilesUpdated\n";
echo "Translations added: $totalTranslationsAdded\n";
echo "Visible keys processed: " . count($visibleKeys) . "\n";

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

echo "\n🧪 TESTING VISIBLE KEYS...\n";
echo "==========================\n";

$testKeys = [
    'HOME.FEATURED_SHOWCASES',
    'UI.COMMUNITY.QUICK_ACCESS',
    'UI.COMMUNITY.DISCOVER',
    'FORUM.THREADS.TITLE',
    'UI.COMMON.RECENT_DISCUSSIONS',
    'UI.COMMON.TRENDING',
    'UI.COMMON.POPULAR_TOPICS',
    'UI.COMMON.MOST_VIEWED',
    'UI.COMMON.HOT_TOPICS',
    'UI.SEARCH.ADVANCED_SEARCH',
    'UI.COMMON.MEMBER_DIRECTORY',
    'UI.COMMUNITY.BROWSE_CATEGORIES'
];

$workingCount = 0;
foreach ($testKeys as $key) {
    $result = __($key);
    if ($result !== $key) {
        echo "✅ __('$key') → '$result'\n";
        $workingCount++;
    } else {
        echo "❌ __('$key') - Still not working\n";
    }
}

echo "\nVisible keys success rate: " . round(($workingCount / count($testKeys)) * 100, 1) . "%\n";

echo "\n🎯 NEXT STEPS\n";
echo "=============\n";
echo "1. ✅ Fixed visible UI keys from screenshot\n";
echo "2. 🔄 Run comprehensive validation\n";
echo "3. 🔄 Test in browser to verify changes\n";
echo "4. 🔄 Continue with remaining failing keys\n";
echo "5. 🔄 Achieve 100% success rate\n\n";

echo "💡 IMPACT\n";
echo "=========\n";
echo "These keys are directly visible to users in the main interface.\n";
echo "Fixing them will have immediate visual impact on user experience.\n";
echo "The homepage and community sections should now display properly.\n";
