<?php

/**
 * ADD BUSINESS SERVICES KEYS
 * Thêm tất cả keys thiếu cho business/services.blade.php
 */

echo "=== ADDING BUSINESS SERVICES KEYS ===\n\n";

// All business services keys organized by file
$businessServicesKeys = [
    // Business keys
    'business_keys' => [
        'faq_analytics' => ['vi' => 'Tôi có thể theo dõi hiệu suất dịch vụ của mình không?', 'en' => 'Can I track the performance of my services?'],
        'faq_custom_packages' => ['vi' => 'Bạn có cung cấp gói doanh nghiệp tùy chỉnh không?', 'en' => 'Do you offer custom enterprise packages?'],
        'analytics_description' => ['vi' => 'Bảng điều khiển phân tích của chúng tôi cung cấp thông tin chi tiết về lượt xem, nhấp chuột, yêu cầu và số liệu tương tác. Bạn sẽ có thể theo dõi hiệu suất theo thời gian và xem khía cạnh nào của sự hiện diện kinh doanh của bạn đang tạo ra sự quan tâm nhiều nhất.', 'en' => 'Our analytics dashboard provides detailed insights into views, clicks, inquiries, and engagement metrics. You\'ll be able to track performance over time and see which aspects of your business presence are generating the most interest.'],
        'premium_listings_description' => ['vi' => 'Danh sách cao cấp xuất hiện ở đầu kết quả tìm kiếm và trang danh mục, mang lại khả năng hiển thị tối đa cho doanh nghiệp của bạn. Chúng cũng bao gồm các yếu tố hình ảnh nâng cao để làm cho danh sách của bạn nổi bật so với đối thủ cạnh tranh.', 'en' => 'Premium listings appear at the top of search results and category pages, giving your business maximum visibility. They also include enhanced visual elements to make your listing stand out from the competition.'],
        'cancel_subscription_description' => ['vi' => 'Có, bạn có thể hủy đăng ký bất cứ lúc nào. Dịch vụ của bạn sẽ tiếp tục cho đến khi kết thúc chu kỳ thanh toán hiện tại.', 'en' => 'Yes, you can cancel your subscription at any time. Your services will continue until the end of your current billing period.'],
        'custom_packages_description' => ['vi' => 'Có, chúng tôi cung cấp các gói doanh nghiệp tùy chỉnh cho các doanh nghiệp lớn hơn có nhu cầu cụ thể. Vui lòng liên hệ với đội ngũ bán hàng của chúng tôi để thảo luận về yêu cầu của bạn và nhận giải pháp phù hợp.', 'en' => 'Yes, we offer custom enterprise packages for larger businesses with specific needs. Please contact our sales team to discuss your requirements and get a tailored solution.'],
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
    'business_keys' => 'business',
];

$totalAdded = 0;

foreach ($businessServicesKeys as $category => $keys) {
    echo "📁 Processing category: $category\n";
    
    $file = $categoryFileMap[$category];
    
    // Add to Vietnamese file
    $viFile = __DIR__ . "/resources/lang/vi/$file.php";
    if (addKeysToFile($viFile, $keys, 'vi')) {
        $totalAdded += count($keys);
    }
    
    // Add to English file
    $enFile = __DIR__ . "/resources/lang/en/$file.php";
    addKeysToFile($enFile, $keys, 'en');
    
    echo "\n";
}

echo "=== SUMMARY ===\n";
echo "Total business services keys added: $totalAdded\n";
echo "Categories processed: " . count($businessServicesKeys) . "\n";

echo "\n✅ Business services keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
