<?php

/**
 * ADD MISSING TRANSLATION KEYS
 * Tự động thêm các keys thiếu vào file translation
 */

echo "=== ADDING MISSING TRANSLATION KEYS ===\n\n";

// Define translations for missing keys
$missingTranslations = [
    // Direct keys for common.php
    'common' => [
        'vi' => [
            'note' => 'Ghi chú',
            'confirm' => 'Xác nhận',
            'saved' => 'Đã lưu',
        ],
        'en' => [
            'note' => 'Note',
            'confirm' => 'Confirm',
            'saved' => 'Saved',
        ]
    ],

    // Auth keys
    'auth' => [
        'vi' => [
            'password_placeholder' => 'Nhập mật khẩu của bạn',
            'password_help' => 'Mật khẩu phải có ít nhất 8 ký tự',
            'confirm_password_label' => 'Xác nhận mật khẩu',
            'confirm_password_placeholder' => 'Nhập lại mật khẩu',
            'secure_area_message' => 'Đây là khu vực bảo mật của ứng dụng. Vui lòng xác nhận mật khẩu trước khi tiếp tục.',
        ],
        'en' => [
            'password_placeholder' => 'Enter your password',
            'password_help' => 'Password must be at least 8 characters',
            'confirm_password_label' => 'Confirm Password',
            'confirm_password_placeholder' => 'Re-enter your password',
            'secure_area_message' => 'This is a secure area of the application. Please confirm your password before continuing.',
        ]
    ],

    // Content keys
    'content' => [
        'vi' => [
            'join_engineering_community' => 'Tham gia cộng đồng kỹ thuật',
            'bookmark_description' => 'Đánh dấu các chủ đề và bài viết để dễ dàng tìm thấy sau này.',
            'premium_listing_description' => 'Danh sách cao cấp xuất hiện ở đầu kết quả tìm kiếm và trang danh mục, mang lại khả năng hiển thị tối đa cho doanh nghiệp của bạn.',
        ],
        'en' => [
            'join_engineering_community' => 'Join the engineering community',
            'bookmark_description' => 'Bookmark threads and posts to find them easily later.',
            'premium_listing_description' => 'Premium listings appear at the top of search results and category pages, giving your business maximum visibility.',
        ]
    ],

    // Coming soon keys
    'coming_soon' => [
        'vi' => [
            'notify_success' => 'Cảm ơn! Chúng tôi sẽ thông báo cho bạn khi ra mắt.',
            'share_text' => 'Chia sẻ với bạn bè',
            'copied' => 'Đã sao chép!',
        ],
        'en' => [
            'notify_success' => 'Thank you! We will notify you when we launch.',
            'share_text' => 'Share with friends',
            'copied' => 'Copied!',
        ]
    ],

    // Companies keys
    'companies' => [
        'vi' => [
            'company_profile' => 'Hồ sơ công ty',
        ],
        'en' => [
            'company_profile' => 'Company Profile',
        ]
    ],

    // Conversations keys
    'conversations' => [
        'vi' => [
            'no_messages_yet' => 'Chưa có tin nhắn nào',
            'send_message_to_start' => 'Gửi tin nhắn để bắt đầu cuộc trò chuyện',
            'type_your_message' => 'Nhập tin nhắn của bạn',
            'when_someone_follows_you' => 'Khi ai đó theo dõi bạn, họ sẽ xuất hiện ở đây',
            'you_are_not_following_anyone' => 'Bạn chưa theo dõi ai',
        ],
        'en' => [
            'no_messages_yet' => 'No messages yet',
            'send_message_to_start' => 'Send a message to start the conversation',
            'type_your_message' => 'Type your message',
            'when_someone_follows_you' => 'When someone follows you, they will appear here',
            'you_are_not_following_anyone' => 'You are not following anyone yet',
        ]
    ],

    // Navigation keys
    'nav' => [
        'vi' => [
            'follow_other_users' => 'Theo dõi người dùng khác để xem cập nhật của họ trong nguồn cấp dữ liệu của bạn',
        ],
        'en' => [
            'follow_other_users' => 'Follow other users to see their updates in your feed',
        ]
    ],

    // Forums keys
    'forums' => [
        'vi' => [
            'join_conversation_by_commenting' => 'Tham gia cuộc trò chuyện bằng cách bình luận về các chủ đề',
            'you_are_not_watching_any_threads' => 'Bạn không theo dõi chủ đề nào',
            'follow_threads_to_see_them_here' => 'Theo dõi các chủ đề để xem chúng ở đây',
            'no_threads_found' => 'Không tìm thấy chủ đề nào',
            'no_posts_found' => 'Không tìm thấy bài viết nào',
            'no_users_online' => 'Không có người dùng trực tuyến',
            'news_feed_empty' => 'Nguồn cấp tin tức hiện đang trống',
        ],
        'en' => [
            'join_conversation_by_commenting' => 'Join the conversation by commenting on threads',
            'you_are_not_watching_any_threads' => 'You are not watching any threads',
            'follow_threads_to_see_them_here' => 'Follow threads to see them here',
            'no_threads_found' => 'No threads found',
            'no_posts_found' => 'No posts found',
            'no_users_online' => 'No users online',
            'news_feed_empty' => 'The news feed is currently empty',
        ]
    ],

    // Forum keys
    'forum' => [
        'vi' => [
            'give_media_title' => 'Đặt tiêu đề mô tả cho phương tiện của bạn (tùy chọn)',
            'add_media_description' => 'Thêm mô tả cho phương tiện của bạn (tùy chọn)',
            'search_gallery' => 'Tìm kiếm thư viện',
            'no_media_items_found' => 'Không tìm thấy mục phương tiện nào',
            'comments_feature_coming_soon' => 'Tính năng bình luận sắp ra mắt',
            'no_threads_found_matching_criteria' => 'Không tìm thấy chủ đề nào phù hợp với tiêu chí tìm kiếm của bạn',
        ],
        'en' => [
            'give_media_title' => 'Give your media a descriptive title (optional)',
            'add_media_description' => 'Add a description for your media (optional)',
            'search_gallery' => 'Search gallery',
            'no_media_items_found' => 'No media items found',
            'comments_feature_coming_soon' => 'Comments feature coming soon',
            'no_threads_found_matching_criteria' => 'No threads found matching your search criteria',
        ]
    ],

    // UI keys
    'ui' => [
        'vi' => [
            'no_media_to_display' => 'Không có phương tiện để hiển thị',
            'no_information_provided' => 'Không có thông tin được cung cấp',
            'no_recent_activity' => 'Không có hoạt động gần đây',
            'no_posts_found_matching_criteria' => 'Không tìm thấy bài viết nào phù hợp với tiêu chí tìm kiếm của bạn',
        ],
        'en' => [
            'no_media_to_display' => 'No media to display',
            'no_information_provided' => 'No information provided',
            'no_recent_activity' => 'No recent activity',
            'no_posts_found_matching_criteria' => 'No posts found matching your search criteria',
        ]
    ],

    // Marketplace keys
    'marketplace' => [
        'vi' => [
            'seller_dashboard' => 'Bảng điều khiển người bán',
            'my_orders' => 'Đơn hàng của tôi',
        ],
        'en' => [
            'seller_dashboard' => 'Seller Dashboard',
            'my_orders' => 'My Orders',
        ]
    ],

    // Subscription keys
    'subscription' => [
        'vi' => [
            'account_deletion_warning' => 'Khi tài khoản của bạn bị xóa, tất cả tài nguyên và dữ liệu của nó sẽ bị xóa vĩnh viễn',
            'no_profile_posts_yet' => 'Chưa có bài viết hồ sơ nào',
            'ensure_secure_password' => 'Đảm bảo tài khoản của bạn sử dụng mật khẩu dài, ngẫu nhiên để giữ an toàn',
            'continue_access_until_billing_end' => 'Bạn sẽ tiếp tục có quyền truy cập vào các tính năng cao cấp cho đến khi kết thúc chu kỳ thanh toán hiện tại',
            'subscription_will_not_renew' => 'Đăng ký của bạn sẽ không tự động gia hạn',
            'can_resubscribe_anytime' => 'Bạn có thể đăng ký lại bất cứ lúc nào',
            'lose_access_at_billing_end' => 'Bạn sẽ mất quyền truy cập vào các tính năng cao cấp vào cuối chu kỳ thanh toán hiện tại',
        ],
        'en' => [
            'account_deletion_warning' => 'Once your account is deleted, all of its resources and data will be permanently deleted',
            'no_profile_posts_yet' => 'No profile posts yet',
            'ensure_secure_password' => 'Ensure your account is using a long, random password to stay secure',
            'continue_access_until_billing_end' => 'You will continue to have access to premium features until the end of your current billing period',
            'subscription_will_not_renew' => 'Your subscription will not renew automatically',
            'can_resubscribe_anytime' => 'You can resubscribe at any time',
            'lose_access_at_billing_end' => 'You will lose access to premium features at the end of your current billing period',
        ]
    ],

    // Showcase keys
    'showcase' => [
        'vi' => [
            'cancel_subscription_anytime' => 'Có, bạn có thể hủy đăng ký bất cứ lúc nào',
            'custom_enterprise_packages' => 'Có, chúng tôi cung cấp các gói doanh nghiệp tùy chỉnh cho các doanh nghiệp lớn hơn',
            'services_continue_until_billing_end' => 'Dịch vụ của bạn sẽ tiếp tục cho đến khi kết thúc chu kỳ thanh toán hiện tại',
        ],
        'en' => [
            'cancel_subscription_anytime' => 'Yes, you can cancel your subscription at any time',
            'custom_enterprise_packages' => 'Yes, we offer custom enterprise packages for larger businesses with specific needs',
            'services_continue_until_billing_end' => 'Your services will continue until the end of your current billing period',
        ]
    ],

    // Thread keys
    'thread' => [
        'vi' => [
            'enhanced_visual_elements' => 'Chúng cũng bao gồm các yếu tố hình ảnh nâng cao để làm cho danh sách của bạn nổi bật so với đối thủ cạnh tranh',
        ],
        'en' => [
            'enhanced_visual_elements' => 'They also include enhanced visual elements to make your listing stand out from the competition',
        ]
    ],
];

// Function to add keys to translation file
function addKeysToFile($filePath, $newKeys, $language) {
    if (!file_exists($filePath)) {
        echo "❌ File not found: $filePath\n";
        return false;
    }

    try {
        $existingTranslations = include $filePath;
        if (!is_array($existingTranslations)) {
            echo "❌ Invalid translation file: $filePath\n";
            return false;
        }

        // Merge new keys
        $updatedTranslations = array_merge_recursive($existingTranslations, $newKeys);

        // Generate file content
        $fileContent = "<?php\n\n";
        $fileContent .= "/**\n";
        $fileContent .= " * " . ucfirst(basename($filePath, '.php')) . " Translation File - " . ucfirst($language) . " (COMPREHENSIVE)\n";
        $fileContent .= " * Complete translation coverage for " . basename($filePath, '.php') . " functionality\n";
        $fileContent .= " * Auto-updated: " . date('Y-m-d H:i:s') . "\n";
        $fileContent .= " */\n\n";
        $fileContent .= "return " . var_export($updatedTranslations, true) . ";\n";

        if (file_put_contents($filePath, $fileContent)) {
            echo "✅ Updated $filePath with " . count($newKeys) . " new keys\n";
            return true;
        } else {
            echo "❌ Failed to write to $filePath\n";
            return false;
        }

    } catch (Exception $e) {
        echo "❌ Error updating $filePath: " . $e->getMessage() . "\n";
        return false;
    }
}

$viDir = __DIR__ . '/resources/lang/vi/';
$enDir = __DIR__ . '/resources/lang/en/';

$totalAdded = 0;
$filesUpdated = 0;

// Process each translation file
foreach ($missingTranslations as $fileName => $languages) {
    echo "\n🔄 Processing $fileName.php...\n";

    // Update Vietnamese file
    $viFile = $viDir . $fileName . '.php';
    if (isset($languages['vi']) && addKeysToFile($viFile, $languages['vi'], 'vietnamese')) {
        $filesUpdated++;
        $totalAdded += count($languages['vi']);
    }

    // Update English file
    $enFile = $enDir . $fileName . '.php';
    if (isset($languages['en']) && addKeysToFile($enFile, $languages['en'], 'english')) {
        $filesUpdated++;
        $totalAdded += count($languages['en']);
    }
}

echo "\n=== SUMMARY ===\n";
echo "Files updated: $filesUpdated\n";
echo "Total keys added: $totalAdded\n";

echo "\n✅ Process completed at " . date('Y-m-d H:i:s') . "\n";
?>
