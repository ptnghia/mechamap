<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductPurchase;
use App\Models\ProtectedFile;
use App\Models\SecureDownload;
use App\Services\SecureDownloadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DownloadController extends Controller
{
    protected SecureDownloadService $downloadService;

    public function __construct(SecureDownloadService $downloadService)
    {
        $this->downloadService = $downloadService;
        $this->middleware('auth:sanctum');
    }

    /**
     * Lấy danh sách files đã mua của user
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $perPage = min($request->per_page ?? 20, 50);

            $purchases = ProductPurchase::where('buyer_id', $user->id)
                ->where('status', 'active')
                ->with(['product.files', 'product.category'])
                ->when($request->search, function($query) use ($request) {
                    $query->whereHas('product', function($q) use ($request) {
                        $q->where('title', 'like', "%{$request->search}%");
                    });
                })
                ->when($request->category_id, function($query) use ($request) {
                    $query->whereHas('product', function($q) use ($request) {
                        $q->where('category_id', $request->category_id);
                    });
                })
                ->orderBy('purchased_at', 'desc')
                ->paginate($perPage);

            // Tính toán download statistics cho mỗi purchase
            $purchasesWithStats = $purchases->through(function($purchase) {
                $totalFiles = $purchase->product->files->count();
                $downloadedFiles = SecureDownload::where('purchase_id', $purchase->id)
                    ->where('user_id', $purchase->buyer_id)
                    ->distinct('file_id')
                    ->count();

                $purchase->download_stats = [
                    'total_files' => $totalFiles,
                    'downloaded_files' => $downloadedFiles,
                    'remaining_downloads' => max(0, $purchase->download_limit - $purchase->downloads_used),
                    'progress_percentage' => $totalFiles > 0 ? round(($downloadedFiles / $totalFiles) * 100, 1) : 0,
                ];

                return $purchase;
            });

            return response()->json([
                'success' => true,
                'data' => $purchasesWithStats,
                'message' => 'Lấy danh sách purchases thành công'
            ]);

        } catch (\Exception $e) {
            Log::error('Lỗi lấy danh sách purchases', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy danh sách purchases'
            ], 500);
        }
    }

    /**
     * Lấy chi tiết files của một purchase
     */
    public function purchaseFiles(int $purchaseId): JsonResponse
    {
        try {
            $user = Auth::user();
            $purchase = ProductPurchase::where('id', $purchaseId)
                ->where('buyer_id', $user->id)
                ->where('status', 'active')
                ->with(['product.files'])
                ->firstOrFail();

            // Kiểm tra license có còn valid không
            if ($purchase->expires_at && $purchase->expires_at < now()) {
                return response()->json([
                    'success' => false,
                    'message' => 'License đã hết hạn'
                ], 403);
            }

            $files = $purchase->product->files->map(function($file) use ($purchase, $user) {
                // Lấy download history cho file này
                $downloadHistory = SecureDownload::where('purchase_id', $purchase->id)
                    ->where('user_id', $user->id)
                    ->where('file_id', $file->id)
                    ->orderBy('created_at', 'desc')
                    ->get();

                return [
                    'id' => $file->id,
                    'filename' => $file->filename,
                    'original_filename' => $file->original_filename,
                    'file_type' => $file->file_type,
                    'file_size' => $file->file_size,
                    'file_size_human' => $this->formatFileSize($file->file_size),
                    'description' => $file->description,
                    'version' => $file->version,
                    'is_preview' => $file->is_preview,
                    'download_count' => $downloadHistory->count(),
                    'last_downloaded' => $downloadHistory->first()?->created_at,
                    'can_download' => $this->canDownloadFile($purchase, $file),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'purchase' => $purchase,
                    'files' => $files,
                    'download_stats' => [
                        'remaining_downloads' => max(0, $purchase->download_limit - $purchase->downloads_used),
                        'expires_at' => $purchase->expires_at,
                        'license_type' => $purchase->license_type,
                    ],
                ],
                'message' => 'Lấy danh sách files thành công'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy purchase hoặc bạn không có quyền truy cập'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Lỗi lấy chi tiết purchase files', [
                'user_id' => Auth::id(),
                'purchase_id' => $purchaseId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy chi tiết files'
            ], 500);
        }
    }

    /**
     * Tạo secure download link
     */
    public function generateDownloadLink(Request $request): JsonResponse
    {
        $request->validate([
            'purchase_id' => 'required|exists:product_purchases,id',
            'file_id' => 'required|exists:protected_files,id',
        ]);

        try {
            $user = Auth::user();
            $purchase = ProductPurchase::where('id', $request->purchase_id)
                ->where('buyer_id', $user->id)
                ->where('status', 'active')
                ->firstOrFail();

            $file = ProtectedFile::where('id', $request->file_id)
                ->where('product_id', $purchase->product_id)
                ->firstOrFail();

            // Validate có thể download không
            $canDownload = $this->canDownloadFile($purchase, $file);
            if (!$canDownload['can_download']) {
                return response()->json([
                    'success' => false,
                    'message' => $canDownload['reason']
                ], 403);
            }

            // Tạo secure download link
            $downloadData = $this->downloadService->generateSecureDownloadLink(
                $user,
                $purchase,
                $file,
                $request->ip()
            );

            return response()->json([
                'success' => true,
                'data' => $downloadData,
                'message' => 'Tạo download link thành công'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy purchase hoặc file'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Lỗi tạo download link', [
                'user_id' => Auth::id(),
                'purchase_id' => $request->purchase_id,
                'file_id' => $request->file_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tạo download link'
            ], 500);
        }
    }

    /**
     * Download file với secure token
     */
    public function download(string $token, Request $request)
    {
        try {
            $result = $this->downloadService->processSecureDownload(
                $token,
                $request->ip(),
                $request->userAgent()
            );

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], $result['status_code'] ?? 403);
            }

            $file = $result['file'];
            $decryptedPath = $result['decrypted_path'];

            // Kiểm tra file có tồn tại không
            if (!Storage::exists($decryptedPath)) {
                Log::error('File không tồn tại', [
                    'file_id' => $file->id,
                    'path' => $decryptedPath
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'File không tồn tại'
                ], 404);
            }

            // Streaming file với proper headers
            $fileContent = Storage::get($decryptedPath);
            $mimeType = Storage::mimeType($decryptedPath) ?? 'application/octet-stream';

            return response($fileContent, 200)
                ->header('Content-Type', $mimeType)
                ->header('Content-Disposition', 'attachment; filename="' . $file->original_filename . '"')
                ->header('Content-Length', strlen($fileContent))
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');

        } catch (\Exception $e) {
            Log::error('Lỗi download file', [
                'token' => $token,
                'ip' => $request->ip(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi download file'
            ], 500);
        }
    }

    /**
     * Lấy download history của user
     */
    public function history(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $perPage = min($request->per_page ?? 20, 50);

            $downloads = SecureDownload::where('user_id', $user->id)
                ->with(['file', 'purchase.product'])
                ->when($request->purchase_id, function($query) use ($request) {
                    $query->where('purchase_id', $request->purchase_id);
                })
                ->when($request->file_type, function($query) use ($request) {
                    $query->whereHas('file', function($q) use ($request) {
                        $q->where('file_type', $request->file_type);
                    });
                })
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $downloads,
                'message' => 'Lấy download history thành công'
            ]);

        } catch (\Exception $e) {
            Log::error('Lỗi lấy download history', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy download history'
            ], 500);
        }
    }

    /**
     * Kiểm tra có thể download file không
     */
    private function canDownloadFile(ProductPurchase $purchase, ProtectedFile $file): array
    {
        // Kiểm tra license còn valid không
        if ($purchase->expires_at && $purchase->expires_at < now()) {
            return [
                'can_download' => false,
                'reason' => 'License đã hết hạn'
            ];
        }

        // Kiểm tra download limit
        if ($purchase->download_limit > 0 && $purchase->downloads_used >= $purchase->download_limit) {
            return [
                'can_download' => false,
                'reason' => 'Đã hết lượt download'
            ];
        }

        // Kiểm tra license type với file type
        if ($file->requires_extended_license && $purchase->license_type === 'standard') {
            return [
                'can_download' => false,
                'reason' => 'File này yêu cầu Extended License'
            ];
        }

        return [
            'can_download' => true,
            'reason' => null
        ];
    }

    /**
     * Format file size để hiển thị
     */
    private function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}
