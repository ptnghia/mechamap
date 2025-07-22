<?php

/**
 * ADD SUPPLIER SIDEBAR KEYS
 * Thêm tất cả keys thiếu cho supplier/partials/sidebar.blade.php
 */

echo "=== ADDING SUPPLIER SIDEBAR KEYS ===\n\n";

// All supplier sidebar keys organized by file
$supplierSidebarKeys = [
    // Navigation keys
    'navigation_keys' => [
        'dashboard' => ['vi' => 'Bảng điều khiển', 'en' => 'Dashboard'],
        'products' => ['vi' => 'Sản phẩm', 'en' => 'Products'],
        'orders' => ['vi' => 'Đơn hàng', 'en' => 'Orders'],
        'analytics' => ['vi' => 'Phân tích', 'en' => 'Analytics'],
        'settings' => ['vi' => 'Cài đặt', 'en' => 'Settings'],
    ],
    
    // Dashboard keys
    'dashboard_keys' => [
        'quick_stats' => ['vi' => 'Thống kê nhanh', 'en' => 'Quick Stats'],
        'products' => ['vi' => 'Sản phẩm', 'en' => 'Products'],
        'orders' => ['vi' => 'Đơn hàng', 'en' => 'Orders'],
        'total_revenue' => ['vi' => 'Tổng doanh thu', 'en' => 'Total Revenue'],
    ],
    
    // Roles keys
    'roles_keys' => [
        'supplier' => ['vi' => 'Nhà cung cấp', 'en' => 'Supplier'],
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
    'navigation_keys' => 'navigation',
    'dashboard_keys' => 'dashboard',
    'roles_keys' => 'roles',
];

$totalAdded = 0;

foreach ($supplierSidebarKeys as $category => $keys) {
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
echo "Total supplier sidebar keys added: $totalAdded\n";
echo "Categories processed: " . count($supplierSidebarKeys) . "\n";

echo "\n✅ Supplier sidebar keys addition completed at " . date('Y-m-d H:i:s') . "\n";
?>
