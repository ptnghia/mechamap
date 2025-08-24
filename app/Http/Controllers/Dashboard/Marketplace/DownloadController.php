<?php

namespace App\Http\Controllers\Dashboard\Marketplace;

use App\Http\Controllers\Dashboard\BaseController;
use App\Models\MarketplaceDownload;
use App\Models\MarketplaceOrderItem;
use App\Services\MarketplaceDownloadService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

/**
 * Download Controller cho Dashboard Marketplace
 * 
 * Quản lý downloads của user trong dashboard
 */
class DownloadController extends BaseController
{
    protected $downloadService;

    public function __construct(MarketplaceDownloadService $downloadService)
    {
        parent::__construct();
        $this->downloadService = $downloadService;
    }

    /**
     * Hiển thị danh sách downloads của user
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $productType = $request->get('product_type');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $sort = $request->get('sort', 'newest');

        $query = MarketplaceDownload::with(['orderItem.product', 'orderItem.order'])
            ->where('user_id', $this->user->id);

        // Apply search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('original_filename', 'like', "%{$search}%")
                  ->orWhereHas('orderItem.product', function($productQuery) use ($search) {
                      $productQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by product type
        if ($productType) {
            $query->whereHas('orderItem.product', function($q) use ($productType) {
                $q->where('product_type', $productType);
            });
        }

        // Filter by date range
        if ($dateFrom) {
            $query->whereDate('downloaded_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('downloaded_at', '<=', $dateTo);
        }

        // Apply sorting
        switch ($sort) {
            case 'oldest':
                $query->orderBy('downloaded_at');
                break;
            case 'filename':
                $query->orderBy('original_filename');
                break;
            case 'size':
                $query->orderByDesc('file_size');
                break;
            case 'newest':
            default:
                $query->orderByDesc('downloaded_at');
                break;
        }

        $downloads = $query->paginate(20);

        // Get statistics
        $stats = $this->getDownloadStats();

        // Get available downloads (not yet downloaded)
        $availableDownloads = $this->getAvailableDownloads();

        $breadcrumb = $this->getBreadcrumb([
            ['name' => 'Marketplace', 'route' => null],
            ['name' => 'Downloads', 'route' => 'dashboard.marketplace.downloads']
        ]);

        return $this->dashboardResponse('dashboard.marketplace.downloads.index', [
            'downloads' => $downloads,
            'availableDownloads' => $availableDownloads,
            'stats' => $stats,
            'search' => $search,
            'currentProductType' => $productType,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'currentSort' => $sort]);
    }

    /**
     * Hiển thị files có thể download cho order item
     */
    public function showOrderFiles(MarketplaceOrderItem $orderItem)
    {
        // Verify ownership
        if ($orderItem->order->customer_id !== $this->user->id) {
            abort(403, 'Unauthorized access to order files');
        }

        // Ensure the product is digital and has files
        if (!$orderItem->product || $orderItem->product->product_type !== 'digital') {
            abort(404, 'This product does not have downloadable files');
        }

        // Check if order is paid
        if ($orderItem->order->payment_status !== 'paid') {
            abort(403, 'Order must be paid before downloading files');
        }

        // Get digital files
        $digitalFiles = $orderItem->product->digitalFiles;

        if ($digitalFiles->isEmpty()) {
            abort(404, 'No downloadable files found for this product');
        }

        // Get download history for this order item
        $downloadHistory = MarketplaceDownload::where('user_id', $this->user->id)
            ->where('order_item_id', $orderItem->id)
            ->get()
            ->keyBy('file_path');

        $breadcrumb = $this->getBreadcrumb([
            ['name' => 'Marketplace', 'route' => null],
            ['name' => 'Downloads', 'route' => 'dashboard.marketplace.downloads'],
            ['name' => 'Order Files', 'route' => null]
        ]);

        return $this->dashboardResponse('dashboard.marketplace.downloads.order-files', [
            'orderItem' => $orderItem,
            'digitalFiles' => $digitalFiles,
            'downloadHistory' => $downloadHistory]);
    }

    /**
     * Tạo download token cho file
     */
    public function createDownloadToken(Request $request, MarketplaceOrderItem $orderItem): JsonResponse
    {
        // Verify ownership
        if ($orderItem->order->customer_id !== $this->user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'file_index' => 'required|integer|min:0',
        ]);

        try {
            // Get downloadable files
            $files = $this->downloadService->getDownloadableFiles($orderItem);

            if (!isset($files[$request->file_index])) {
                return response()->json([
                    'success' => false,
                    'message' => 'File không tồn tại'
                ], 404);
            }

            $fileData = $files[$request->file_index];

            // Generate download token
            $tokenData = $this->downloadService->generateDownloadToken($this->user, $orderItem, $fileData);

            return response()->json([
                'success' => true,
                'data' => $tokenData,
                'message' => 'Tạo link download thành công'
            ]);

        } catch (\Exception $e) {
            Log::error('Download token creation failed', [
                'user_id' => $this->user->id,
                'order_item_id' => $orderItem->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Re-download file từ history
     */
    public function redownload(MarketplaceDownload $download)
    {
        // Verify ownership
        if ($download->user_id !== $this->user->id) {
            abort(403, 'Unauthorized access to download');
        }

        $orderItem = $download->orderItem;

        // Verify order is still valid
        if ($orderItem->order->payment_status !== 'paid') {
            return back()->with('error', 'Order must be paid to re-download files');
        }

        try {
            // Create file data from download history
            $fileData = [
                'name' => $download->original_filename,
                'path' => $download->file_path,
                'size' => $download->file_size,
                'mime_type' => $download->mime_type,
                'extension' => pathinfo($download->original_filename, PATHINFO_EXTENSION),
            ];

            // Generate new download token
            $tokenData = $this->downloadService->generateDownloadToken($this->user, $orderItem, $fileData);

            return redirect($tokenData['download_url']);

        } catch (\Exception $e) {
            Log::error('Re-download failed', [
                'download_id' => $download->id,
                'user_id' => $this->user->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Xóa download history
     */
    public function deleteDownload(MarketplaceDownload $download): JsonResponse
    {
        // Verify ownership
        if ($download->user_id !== $this->user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $download->delete();

        return response()->json([
            'success' => true,
            'message' => 'Download history deleted successfully.'
        ]);
    }

    /**
     * Lấy thống kê downloads
     */
    private function getDownloadStats()
    {
        $total = MarketplaceDownload::where('user_id', $this->user->id)->count();
        $totalSize = MarketplaceDownload::where('user_id', $this->user->id)->sum('file_size');
        $uniqueProducts = MarketplaceDownload::where('user_id', $this->user->id)
            ->join('marketplace_order_items', 'marketplace_downloads.order_item_id', '=', 'marketplace_order_items.id')
            ->distinct('marketplace_order_items.product_id')
            ->count();

        $thisMonth = MarketplaceDownload::where('user_id', $this->user->id)
            ->whereBetween('downloaded_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();

        $thisWeek = MarketplaceDownload::where('user_id', $this->user->id)
            ->whereBetween('downloaded_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();

        return [
            'total_downloads' => $total,
            'total_size' => $totalSize,
            'total_size_formatted' => $this->formatBytes($totalSize),
            'unique_products' => $uniqueProducts,
            'this_month' => $thisMonth,
            'this_week' => $thisWeek,
        ];
    }

    /**
     * Lấy danh sách downloads có sẵn (chưa download)
     */
    private function getAvailableDownloads()
    {
        // Get paid order items with digital products that have files
        $orderItems = MarketplaceOrderItem::whereHas('order', function($q) {
                $q->where('customer_id', $this->user->id)
                  ->where('payment_status', 'paid');
            })
            ->whereHas('product', function($q) {
                $q->where('product_type', 'digital');
            })
            ->whereHas('product.digitalFiles')
            ->with(['product.digitalFiles', 'order'])
            ->get();

        $availableDownloads = collect();

        foreach ($orderItems as $orderItem) {
            $downloadedFiles = MarketplaceDownload::where('user_id', $this->user->id)
                ->where('order_item_id', $orderItem->id)
                ->pluck('file_path')
                ->toArray();

            foreach ($orderItem->product->digitalFiles as $file) {
                if (!in_array($file->file_path, $downloadedFiles)) {
                    $availableDownloads->push([
                        'order_item' => $orderItem,
                        'file' => $file,
                        'product' => $orderItem->product,
                        'order' => $orderItem->order,
                    ]);
                }
            }
        }

        return $availableDownloads;
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
