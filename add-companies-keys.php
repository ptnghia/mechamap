<?php

/**
 * ADD COMPANIES KEYS
 * Thêm tất cả keys thiếu cho community/companies/index.blade.php
 */

echo "=== ADDING COMPANIES KEYS ===\n\n";

// All companies keys
$companiesKeys = [
    'directory' => ['vi' => 'Danh bạ doanh nghiệp', 'en' => 'Company Directory'],
    'header_description' => ['vi' => 'Khám phá và kết nối với các doanh nghiệp hàng đầu trong ngành cơ khí', 'en' => 'Discover and connect with leading businesses in the mechanical industry'],
    'verified_companies' => ['vi' => 'Doanh nghiệp đã xác thực', 'en' => 'Verified Companies'],
    'industries' => ['vi' => 'Ngành nghề', 'en' => 'Industries'],
    'cities' => ['vi' => 'Thành phố', 'en' => 'Cities'],
    'average_rating' => ['vi' => 'Đánh giá trung bình', 'en' => 'Average Rating'],
    'search_companies' => ['vi' => 'Tìm kiếm doanh nghiệp', 'en' => 'Search Companies'],
    'search_placeholder' => ['vi' => 'Nhập tên công ty...', 'en' => 'Enter company name...'],
    'industry' => ['vi' => 'Ngành nghề', 'en' => 'Industry'],
    'all_industries' => ['vi' => 'Tất cả ngành', 'en' => 'All Industries'],
    'manufacturing' => ['vi' => 'Sản xuất', 'en' => 'Manufacturing'],
    'automotive' => ['vi' => 'Ô tô', 'en' => 'Automotive'],
    'aerospace' => ['vi' => 'Hàng không vũ trụ', 'en' => 'Aerospace'],
    'construction' => ['vi' => 'Xây dựng', 'en' => 'Construction'],
    'energy' => ['vi' => 'Năng lượng', 'en' => 'Energy'],
    'electronics' => ['vi' => 'Điện tử', 'en' => 'Electronics'],
    'company_type' => ['vi' => 'Loại hình', 'en' => 'Company Type'],
    'all_types' => ['vi' => 'Tất cả loại', 'en' => 'All Types'],
    'supplier' => ['vi' => 'Nhà cung cấp', 'en' => 'Supplier'],
    'manufacturer' => ['vi' => 'Nhà sản xuất', 'en' => 'Manufacturer'],
    'distributor' => ['vi' => 'Nhà phân phối', 'en' => 'Distributor'],
    'service_provider' => ['vi' => 'Nhà cung cấp dịch vụ', 'en' => 'Service Provider'],
    'location' => ['vi' => 'Địa điểm', 'en' => 'Location'],
    'all_locations' => ['vi' => 'Tất cả địa điểm', 'en' => 'All Locations'],
    'ho_chi_minh' => ['vi' => 'TP. Hồ Chí Minh', 'en' => 'Ho Chi Minh City'],
    'hanoi' => ['vi' => 'Hà Nội', 'en' => 'Hanoi'],
    'da_nang' => ['vi' => 'Đà Nẵng', 'en' => 'Da Nang'],
    'hai_phong' => ['vi' => 'Hải Phòng', 'en' => 'Hai Phong'],
    'can_tho' => ['vi' => 'Cần Thơ', 'en' => 'Can Tho'],
    'verification' => ['vi' => 'Xác thực', 'en' => 'Verification'],
    'all_companies' => ['vi' => 'Tất cả công ty', 'en' => 'All Companies'],
    'verified_only' => ['vi' => 'Chỉ đã xác thực', 'en' => 'Verified Only'],
    'premium_members' => ['vi' => 'Thành viên cao cấp', 'en' => 'Premium Members'],
    'joined' => ['vi' => 'Tham gia', 'en' => 'Joined'],
    'verified' => ['vi' => 'Đã xác thực', 'en' => 'Verified'],
    'premium' => ['vi' => 'Cao cấp', 'en' => 'Premium'],
    'rating' => ['vi' => 'Đánh giá', 'en' => 'Rating'],
    'reviews' => ['vi' => 'đánh giá', 'en' => 'reviews'],
    'specialties' => ['vi' => 'Chuyên môn', 'en' => 'Specialties'],
    'contact' => ['vi' => 'Liên hệ', 'en' => 'Contact'],
    'products' => ['vi' => 'Sản phẩm', 'en' => 'Products'],
    'processing_time' => ['vi' => 'Thời gian xử lý', 'en' => 'Processing Time'],
    'days' => ['vi' => 'ngày', 'en' => 'days'],
    'view_profile' => ['vi' => 'Xem hồ sơ', 'en' => 'View Profile'],
    'no_companies_found' => ['vi' => 'Không tìm thấy doanh nghiệp nào', 'en' => 'No companies found'],
    'adjust_search_criteria' => ['vi' => 'Hãy điều chỉnh tiêu chí tìm kiếm của bạn', 'en' => 'Please adjust your search criteria'],
    'message_sent_successfully' => ['vi' => 'Tin nhắn đã được gửi thành công', 'en' => 'Message sent successfully'],
    'company_added_to_favorites' => ['vi' => 'Đã thêm công ty vào danh sách yêu thích', 'en' => 'Company added to favorites'],
    'company_removed_from_favorites' => ['vi' => 'Đã xóa công ty khỏi danh sách yêu thích', 'en' => 'Company removed from favorites'],
    'error_updating_favorites' => ['vi' => 'Lỗi khi cập nhật danh sách yêu thích', 'en' => 'Error updating favorites'],
    'please_login_to_add_favorites' => ['vi' => 'Vui lòng đăng nhập để thêm vào yêu thích', 'en' => 'Please login to add to favorites'],
    'company_link_copied' => ['vi' => 'Đã sao chép liên kết công ty', 'en' => 'Company link copied'],
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

echo "📁 Processing companies keys for companies.php\n";

// Add to Vietnamese file
$viFile = __DIR__ . "/resources/lang/vi/companies.php";
if (addKeysToFile($viFile, $companiesKeys, 'vi')) {
    $totalAdded = count($companiesKeys);
}

// Add to English file
$enFile = __DIR__ . "/resources/lang/en/companies.php";
addKeysToFile($enFile, $companiesKeys, 'en');

echo "\n=== SUMMARY ===\n";
echo "Total companies keys added: " . count($companiesKeys) . "\n";

echo "\n✅ Companies keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
