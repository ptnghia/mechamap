<?php

namespace App\Services;

use App\Models\MarketplaceProduct;
use App\Models\MarketplaceOrder;
use App\Models\MarketplaceOrderItem;
use App\Models\MarketplaceDownloadHistory;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class MarketplaceDownloadService
{
    /**
     * Generate secure download token for marketplace product
     */
    public function generateDownloadToken(User $user, MarketplaceOrderItem $orderItem, array $fileData): array
    {
        // Verify user has purchased this product
        if (!$this->verifyPurchaseAccess($user, $orderItem)) {
            throw new \Exception('Bạn không có quyền tải xuống file này');
        }

        // Generate secure token
        $token = Str::random(64);
        $expiresAt = Carbon::now()->addHours(24); // Token expires in 24 hours

        // Store token data in cache or database
        $tokenData = [
            'token' => $token,
            'user_id' => $user->id,
            'order_item_id' => $orderItem->id,
            'product_id' => $orderItem->product_id,
            'file_data' => $fileData,
            'expires_at' => $expiresAt,
            'created_at' => Carbon::now(),
        ];

        // Store in cache for 24 hours
        cache()->put("download_token:{$token}", $tokenData, $expiresAt);

        return [
            'token' => $token,
            'download_url' => route('marketplace.download.file', ['token' => $token]),
            'expires_at' => $expiresAt,
            'file_name' => $fileData['name'],
            'file_size' => $fileData['size'] ?? 0,
        ];
    }

    /**
     * Verify user has access to download file
     */
    public function verifyPurchaseAccess(User $user, MarketplaceOrderItem $orderItem): bool
    {
        // Check if order belongs to user
        if ($orderItem->order->customer_id !== $user->id) {
            return false;
        }

        // Check if order is paid
        if ($orderItem->order->payment_status !== 'paid') {
            return false;
        }

        // Check if order is in valid status for digital downloads
        // For digital products, confirmed + paid is sufficient
        $validStatuses = ['completed', 'processing', 'confirmed', 'shipped', 'delivered'];
        if (!in_array($orderItem->order->status, $validStatuses)) {
            return false;
        }

        return true;
    }

    /**
     * Process secure download with token
     */
    public function processSecureDownload(string $token, string $ipAddress, string $userAgent = null): array
    {
        // Retrieve token data from cache
        $tokenData = cache()->get("download_token:{$token}");

        if (!$tokenData) {
            throw new \Exception('Token không hợp lệ hoặc đã hết hạn');
        }

        // Verify token hasn't expired
        if (Carbon::now()->gt($tokenData['expires_at'])) {
            cache()->forget("download_token:{$token}");
            throw new \Exception('Token đã hết hạn');
        }

        // Get user and order item
        $user = User::find($tokenData['user_id']);
        $orderItem = MarketplaceOrderItem::find($tokenData['order_item_id']);

        if (!$user || !$orderItem) {
            throw new \Exception('Dữ liệu không hợp lệ');
        }

        // Verify access again
        if (!$this->verifyPurchaseAccess($user, $orderItem)) {
            throw new \Exception('Không có quyền truy cập');
        }

        // Get file data
        $fileData = $tokenData['file_data'];
        $filePath = $fileData['path'];

        // Check if file exists
        if (!Storage::disk('private')->exists($filePath)) {
            throw new \Exception('File không tồn tại');
        }

        // Track download
        $this->trackDownload($user, $orderItem, $fileData, $ipAddress, $userAgent, $token);

        // Return file download data
        return [
            'file_path' => $filePath,
            'file_name' => $fileData['name'],
            'mime_type' => $fileData['mime_type'] ?? 'application/octet-stream',
            'file_size' => $fileData['size'] ?? 0,
        ];
    }

    /**
     * Track download activity
     */
    public function trackDownload(
        User $user,
        MarketplaceOrderItem $orderItem,
        array $fileData,
        string $ipAddress,
        string $userAgent = null,
        string $token = null
    ): void {
        try {
            MarketplaceDownloadHistory::create([
                'user_id' => $user->id,
                'order_id' => $orderItem->order_id,
                'order_item_id' => $orderItem->id,
                'product_id' => $orderItem->product_id,
                'file_name' => $fileData['name'],
                'file_path' => $fileData['path'],
                'original_filename' => $fileData['name'],
                'file_size' => $fileData['size'] ?? 0,
                'mime_type' => $fileData['mime_type'] ?? 'application/octet-stream',
                'downloaded_at' => Carbon::now(),
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'download_method' => 'token',
                'download_token' => $token,
                'is_valid_download' => true,
                'validation_status' => 'success',
                'metadata' => [
                    'file_extension' => $fileData['extension'] ?? null,
                    'order_number' => $orderItem->order->order_number ?? null,
                ],
            ]);

            // Update order item download count
            $orderItem->increment('download_count');

            // Update product download count
            $orderItem->product->increment('download_count');

            Log::info('Download tracked successfully', [
                'user_id' => $user->id,
                'product_id' => $orderItem->product_id,
                'file_name' => $fileData['name'],
                'ip_address' => $ipAddress,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to track download', [
                'user_id' => $user->id,
                'product_id' => $orderItem->product_id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get user's download history
     */
    public function getUserDownloadHistory(User $user, int $perPage = 20): \Illuminate\Pagination\LengthAwarePaginator
    {
        return MarketplaceDownloadHistory::where('user_id', $user->id)
            ->with(['order', 'orderItem', 'product'])
            ->orderBy('downloaded_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get product download statistics
     */
    public function getProductDownloadStats(MarketplaceProduct $product): array
    {
        $totalDownloads = MarketplaceDownloadHistory::where('product_id', $product->id)
            ->where('is_valid_download', true)
            ->count();

        $uniqueUsers = MarketplaceDownloadHistory::where('product_id', $product->id)
            ->where('is_valid_download', true)
            ->distinct('user_id')
            ->count();

        $recentDownloads = MarketplaceDownloadHistory::where('product_id', $product->id)
            ->where('is_valid_download', true)
            ->where('downloaded_at', '>=', Carbon::now()->subDays(30))
            ->count();

        return [
            'total_downloads' => $totalDownloads,
            'unique_users' => $uniqueUsers,
            'recent_downloads' => $recentDownloads,
            'average_downloads_per_user' => $uniqueUsers > 0 ? round($totalDownloads / $uniqueUsers, 2) : 0,
        ];
    }

    /**
     * Get downloadable files for order item
     */
    public function getDownloadableFiles(MarketplaceOrderItem $orderItem): array
    {
        $product = $orderItem->product;
        $files = [];

        // Get files from digital_files JSON field
        if (!empty($product->digital_files)) {
            foreach ($product->digital_files as $file) {
                $files[] = [
                    'type' => 'digital_file',
                    'name' => $file['name'],
                    'path' => $file['path'],
                    'size' => $file['size'] ?? 0,
                    'mime_type' => $file['mime_type'] ?? 'application/octet-stream',
                    'extension' => $file['extension'] ?? '',
                ];
            }
        }

        // Get files from Media relationship
        foreach ($product->digitalFiles as $media) {
            $files[] = [
                'type' => 'media_file',
                'id' => $media->id,
                'name' => $media->file_name,
                'path' => $media->file_path,
                'size' => $media->file_size,
                'mime_type' => $media->mime_type,
                'extension' => $media->file_extension,
            ];
        }

        return $files;
    }

    /**
     * Check if user can download files (no time limit for marketplace)
     */
    public function canUserDownload(User $user, MarketplaceOrderItem $orderItem): array
    {
        if (!$this->verifyPurchaseAccess($user, $orderItem)) {
            return [
                'can_download' => false,
                'reason' => 'Bạn chưa mua sản phẩm này hoặc đơn hàng chưa được thanh toán'
            ];
        }

        // For marketplace, no download time limit (as per requirement)
        return [
            'can_download' => true,
            'reason' => 'Có quyền tải xuống không giới hạn thời gian'
        ];
    }

    /**
     * Cleanup expired tokens
     */
    public function cleanupExpiredTokens(): int
    {
        // This would be called by a scheduled job
        // For now, we rely on cache expiration
        return 0;
    }
}
