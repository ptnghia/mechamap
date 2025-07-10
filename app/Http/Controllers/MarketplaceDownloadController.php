<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceOrder;
use App\Models\MarketplaceOrderItem;
use App\Models\MarketplaceProduct;
use App\Models\MarketplaceDownloadHistory;
use App\Services\MarketplaceDownloadService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class MarketplaceDownloadController extends Controller
{
    protected $downloadService;

    public function __construct(MarketplaceDownloadService $downloadService)
    {
        $this->downloadService = $downloadService;
        $this->middleware('auth')->except(['downloadFile']);
    }

    /**
     * Show user's download history
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $downloads = $this->downloadService->getUserDownloadHistory($user, 20);

        return view('marketplace.downloads.index', compact('downloads'));
    }

    /**
     * Show downloadable files for an order
     */
    public function orderFiles(MarketplaceOrder $order)
    {
        $user = Auth::user();

        // Verify order belongs to user
        if ($order->customer_id !== $user->id) {
            abort(403, 'Bạn không có quyền truy cập đơn hàng này');
        }

        // Get order items with digital products only
        $digitalItems = $order->items()
            ->whereHas('product', function ($query) {
                $query->where('product_type', 'digital')
                      ->whereNotNull('digital_files');
            })
            ->with(['product'])
            ->get();

        return view('marketplace.downloads.order-files', compact('order', 'digitalItems'));
    }

    /**
     * Show downloadable files for an order item
     */
    public function itemFiles(MarketplaceOrderItem $orderItem)
    {
        $user = Auth::user();

        // Verify order item belongs to user
        if ($orderItem->order->customer_id !== $user->id) {
            abort(403, 'Bạn không có quyền truy cập');
        }

        // Check if user can download
        $accessCheck = $this->downloadService->canUserDownload($user, $orderItem);
        if (!$accessCheck['can_download']) {
            return back()->with('error', $accessCheck['reason']);
        }

        // Get downloadable files
        $files = $this->downloadService->getDownloadableFiles($orderItem);

        return view('marketplace.downloads.item-files', compact('orderItem', 'files'));
    }

    /**
     * Generate download token for a file
     */
    public function generateToken(Request $request)
    {
        $request->validate([
            'order_item_id' => 'required|exists:marketplace_order_items,id',
            'file_index' => 'required|integer|min:0',
        ]);

        $user = Auth::user();
        $orderItem = MarketplaceOrderItem::findOrFail($request->order_item_id);

        // Verify access
        if ($orderItem->order->customer_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền truy cập'
            ], 403);
        }

        // Check if user can download
        $accessCheck = $this->downloadService->canUserDownload($user, $orderItem);
        if (!$accessCheck['can_download']) {
            return response()->json([
                'success' => false,
                'message' => $accessCheck['reason']
            ], 403);
        }

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
            $tokenData = $this->downloadService->generateDownloadToken($user, $orderItem, $fileData);

            return response()->json([
                'success' => true,
                'data' => $tokenData,
                'message' => 'Tạo link download thành công'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to generate download token', [
                'user_id' => $user->id,
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
     * Download file with token (public endpoint)
     */
    public function downloadFile(string $token, Request $request)
    {
        try {
            $ipAddress = $request->ip();
            $userAgent = $request->userAgent();

            // Process secure download
            $downloadData = $this->downloadService->processSecureDownload($token, $ipAddress, $userAgent);

            // Check if file exists
            if (!Storage::disk('private')->exists($downloadData['file_path'])) {
                abort(404, 'File không tồn tại');
            }

            // Return file download response
            return Storage::disk('private')->download(
                $downloadData['file_path'],
                $downloadData['file_name'],
                [
                    'Content-Type' => $downloadData['mime_type'],
                    'Content-Length' => $downloadData['file_size'],
                ]
            );

        } catch (\Exception $e) {
            Log::error('Download failed', [
                'token' => $token,
                'ip' => $request->ip(),
                'error' => $e->getMessage()
            ]);

            abort(403, $e->getMessage());
        }
    }

    /**
     * Get download statistics for user
     */
    public function downloadStats()
    {
        $user = Auth::user();

        $stats = [
            'total_downloads' => MarketplaceDownloadHistory::where('user_id', $user->id)
                ->where('is_valid_download', true)
                ->count(),

            'unique_products' => MarketplaceDownloadHistory::where('user_id', $user->id)
                ->where('is_valid_download', true)
                ->distinct('product_id')
                ->count(),

            'recent_downloads' => MarketplaceDownloadHistory::where('user_id', $user->id)
                ->where('is_valid_download', true)
                ->where('downloaded_at', '>=', now()->subDays(30))
                ->count(),

            'total_file_size' => MarketplaceDownloadHistory::where('user_id', $user->id)
                ->where('is_valid_download', true)
                ->sum('file_size'),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get download history with filters
     */
    public function downloadHistory(Request $request)
    {
        $user = Auth::user();

        $query = MarketplaceDownloadHistory::where('user_id', $user->id)
            ->with(['order', 'orderItem', 'product']);

        // Apply filters
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('date_from')) {
            $query->where('downloaded_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('downloaded_at', '<=', $request->date_to);
        }

        $downloads = $query->orderBy('downloaded_at', 'desc')
            ->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $downloads
        ]);
    }

    /**
     * Re-download a previously downloaded file
     */
    public function redownload(MarketplaceDownloadHistory $download, Request $request)
    {
        $user = Auth::user();

        // Verify download belongs to user
        if ($download->user_id !== $user->id) {
            abort(403, 'Bạn không có quyền truy cập');
        }

        // Get order item
        $orderItem = $download->orderItem;

        // Check if user still has access
        $accessCheck = $this->downloadService->canUserDownload($user, $orderItem);
        if (!$accessCheck['can_download']) {
            return back()->with('error', $accessCheck['reason']);
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
            $tokenData = $this->downloadService->generateDownloadToken($user, $orderItem, $fileData);

            return redirect($tokenData['download_url']);

        } catch (\Exception $e) {
            Log::error('Re-download failed', [
                'download_id' => $download->id,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
