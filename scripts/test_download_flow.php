<?php

/**
 * Test Download Flow for Order 25, Item 50
 */

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\MarketplaceOrder;
use App\Models\MarketplaceOrderItem;
use App\Services\MarketplaceDownloadService;

echo "🧪 Testing Download Flow for Order 25, Item 50\n";
echo str_repeat("=", 60) . "\n\n";

// Get order and item
$order = MarketplaceOrder::find(25);
$orderItem = MarketplaceOrderItem::find(50);

if (!$order || !$orderItem) {
    echo "❌ Order or OrderItem not found\n";
    exit(1);
}

echo "📋 Order Information:\n";
echo "  Order ID: {$order->id}\n";
echo "  Order Number: {$order->order_number}\n";
echo "  Customer ID: {$order->customer_id}\n";
echo "  Status: {$order->status}\n";
echo "  Payment Status: {$order->payment_status}\n\n";

echo "📦 Order Item Information:\n";
echo "  Item ID: {$orderItem->id}\n";
echo "  Product: {$orderItem->product->name}\n";
echo "  Product Type: {$orderItem->product->product_type}\n";
echo "  Seller Type: {$orderItem->product->seller_type}\n\n";

// Get customer
$customer = $order->customer;
if (!$customer) {
    echo "❌ Customer not found\n";
    exit(1);
}

echo "👤 Customer Information:\n";
echo "  Customer ID: {$customer->id}\n";
echo "  Name: {$customer->name}\n";
echo "  Email: {$customer->email}\n\n";

// Check digital files
echo "📁 Digital Files Check:\n";

// Check digital_files JSON field
$digitalFilesJson = $orderItem->product->digital_files;
echo "  JSON digital_files: " . ($digitalFilesJson ? count($digitalFilesJson) . " files" : "null") . "\n";

// Check Media relationship
$mediaFiles = $orderItem->product->digitalFiles;
echo "  Media relationship: {$mediaFiles->count()} files\n";

if ($mediaFiles->count() > 0) {
    foreach ($mediaFiles as $index => $media) {
        echo "    [{$index}] {$media->file_name} ({$media->file_category})\n";
    }
}

echo "\n";

// Test download service
echo "🔧 Testing Download Service:\n";

$downloadService = app(MarketplaceDownloadService::class);

try {
    // Test purchase verification
    $hasAccess = $downloadService->verifyPurchaseAccess($customer, $orderItem);
    echo "  ✅ Purchase Access: " . ($hasAccess ? "GRANTED" : "DENIED") . "\n";
    
    if (!$hasAccess) {
        echo "  ❌ Cannot proceed - no purchase access\n";
        exit(1);
    }
    
    // Test download permission
    $canDownload = $downloadService->canUserDownload($customer, $orderItem);
    echo "  ✅ Download Permission: " . ($canDownload['can_download'] ? "GRANTED" : "DENIED") . "\n";
    echo "     Reason: {$canDownload['reason']}\n";
    
    // Get downloadable files
    $files = $downloadService->getDownloadableFiles($orderItem);
    echo "  ✅ Downloadable Files: " . count($files) . " files found\n";
    
    if (count($files) > 0) {
        foreach ($files as $index => $file) {
            echo "    [{$index}] {$file['name']} (" . formatFileSize($file['size']) . ")\n";
        }
        
        // Test token generation for first file
        echo "\n🔐 Testing Token Generation:\n";
        $tokenData = $downloadService->generateDownloadToken($customer, $orderItem, $files[0]);
        echo "  ✅ Token Generated: {$tokenData['token']}\n";
        echo "  ✅ Download URL: {$tokenData['download_url']}\n";
        echo "  ✅ Expires At: {$tokenData['expires_at']}\n";
        
        // Test download processing (without actual file download)
        echo "\n⬇️ Testing Download Processing:\n";
        try {
            $downloadData = $downloadService->processSecureDownload(
                $tokenData['token'], 
                '127.0.0.1', 
                'Test Script'
            );
            echo "  ✅ Download Processing: SUCCESS\n";
            echo "  ✅ File Path: {$downloadData['file_path']}\n";
            echo "  ✅ File Name: {$downloadData['file_name']}\n";
            echo "  ✅ MIME Type: {$downloadData['mime_type']}\n";
            
        } catch (Exception $e) {
            echo "  ❌ Download Processing: FAILED\n";
            echo "     Error: {$e->getMessage()}\n";
        }
    }
    
} catch (Exception $e) {
    echo "  ❌ Download Service Error: {$e->getMessage()}\n";
}

echo "\n";

// Test routes
echo "🌐 Testing Routes:\n";

// Test legacy route
echo "  Legacy Route: /marketplace/orders/25/items/50/download\n";
echo "    Controller: MarketplaceOrderController@downloadFile\n";
echo "    Expected: Show download page with files\n";

// Test new secure routes
echo "  New Route: /marketplace/downloads/items/50/files\n";
echo "    Controller: MarketplaceDownloadController@itemFiles\n";
echo "    Expected: Show secure download interface\n";

echo "  Token Route: /marketplace/downloads/generate-token\n";
echo "    Controller: MarketplaceDownloadController@generateToken\n";
echo "    Expected: Generate secure download token\n";

echo "  Download Route: /download/{token}\n";
echo "    Controller: MarketplaceDownloadController@downloadFile\n";
echo "    Expected: Secure file download\n";

echo "\n";

// Summary
echo "📊 SUMMARY:\n";
echo "  Order Status: " . ($order->payment_status === 'paid' ? "✅ PAID" : "❌ NOT PAID") . "\n";
echo "  Digital Product: " . ($orderItem->product->product_type === 'digital' ? "✅ YES" : "❌ NO") . "\n";
echo "  Files Available: " . ($mediaFiles->count() > 0 ? "✅ YES ({$mediaFiles->count()})" : "❌ NO") . "\n";
echo "  Download Access: " . ($hasAccess ? "✅ GRANTED" : "❌ DENIED") . "\n";

if ($hasAccess && $mediaFiles->count() > 0) {
    echo "\n🎉 READY FOR DOWNLOAD!\n";
    echo "   User can access: https://mechamap.test/marketplace/downloads/items/50/files\n";
    echo "   Legacy URL: https://mechamap.test/marketplace/orders/25/items/50/download\n";
} else {
    echo "\n⚠️ DOWNLOAD NOT AVAILABLE\n";
    if (!$hasAccess) {
        echo "   Reason: No purchase access\n";
    }
    if ($mediaFiles->count() === 0) {
        echo "   Reason: No digital files available\n";
    }
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "Test completed!\n";

function formatFileSize($bytes) {
    if ($bytes === 0) return '0 Bytes';
    $k = 1024;
    $sizes = ['Bytes', 'KB', 'MB', 'GB'];
    $i = floor(log($bytes) / log($k));
    return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
}
