<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SecureDownloadService;
use App\Models\ProductPurchase;
use App\Models\ProtectedFile;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SecureDownloadController extends Controller
{
    protected SecureDownloadService $downloadService;

    public function __construct(SecureDownloadService $downloadService)
    {
        $this->downloadService = $downloadService;
    }

    /**
     * Generate secure download token for purchased product
     */
    public function generateToken(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'purchase_id' => 'required|integer|exists:product_purchases,id',
                'file_id' => 'required|integer|exists:protected_files,id',
            ]);

            $user = Auth::user();
            $purchase = ProductPurchase::where('id', $request->purchase_id)
                ->where('user_id', $user->id)
                ->where('status', 'completed')
                ->first();

            if (!$purchase) {
                return response()->json([
                    'success' => false,
                    'message' => 'Purchase not found or not completed'
                ], 404);
            }

            $file = ProtectedFile::find($request->file_id);

            // Verify file belongs to purchased product
            if ($file->technical_product_id !== $purchase->technical_product_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'File does not belong to purchased product'
                ], 403);
            }

            // Generate download token
            $token = $this->downloadService->generateDownloadToken($purchase, $file, $user);

            return response()->json([
                'success' => true,
                'data' => [
                    'download_token' => $token->token,
                    'expires_at' => $token->expires_at,
                    'download_url' => route('api.downloads.file', ['token' => $token->token]),
                    'file_name' => $file->original_filename,
                    'file_size' => $file->file_size,
                    'license_type' => $purchase->license_type,
                    'downloads_remaining' => $this->downloadService->getMaxDownloadAttempts($purchase->license_type) - $token->download_attempts
                ],
                'message' => 'Download token generated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Download token generation failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate download token'
            ], 500);
        }
    }

    /**
     * Download file using secure token
     */
    public function downloadFile(Request $request, string $token): Response|JsonResponse
    {
        try {
            // Validate token and get download info
            $downloadInfo = $this->downloadService->validateDownloadToken($token);

            if (!$downloadInfo['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => $downloadInfo['reason']
                ], 403);
            }

            $file = $downloadInfo['file'];
            $purchase = $downloadInfo['purchase'];

            // Check download limits based on license type
            $limitCheck = $this->downloadService->checkDownloadLimits($purchase, $file);

            if (!$limitCheck['allowed']) {
                return response()->json([
                    'success' => false,
                    'message' => $limitCheck['reason'],
                    'data' => [
                        'downloads_used' => $limitCheck['downloads_used'],
                        'downloads_limit' => $limitCheck['downloads_limit']
                    ]
                ], 429);
            }

            // Track download attempt
            $this->downloadService->trackDownload($purchase, $file, $request->ip());

            // Stream file securely
            return $this->streamFile($file);

        } catch (\Exception $e) {
            Log::error('Secure download failed', [
                'error' => $e->getMessage(),
                'token' => $token,
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Download failed'
            ], 500);
        }
    }

    /**
     * Get user's download history
     */
    public function downloadHistory(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            $downloads = $this->downloadService->getUserDownloadHistory($user->id, [
                'limit' => $request->get('limit', 20),
                'offset' => $request->get('offset', 0),
                'product_id' => $request->get('product_id'),
                'date_from' => $request->get('date_from'),
                'date_to' => $request->get('date_to'),
            ]);

            return response()->json([
                'success' => true,
                'data' => $downloads,
                'message' => 'Download history retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Download history retrieval failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve download history'
            ], 500);
        }
    }

    /**
     * Get download analytics for a purchase
     */
    public function downloadAnalytics(Request $request, int $purchaseId): JsonResponse
    {
        try {
            $user = Auth::user();

            $purchase = ProductPurchase::where('id', $purchaseId)
                ->where('user_id', $user->id)
                ->first();

            if (!$purchase) {
                return response()->json([
                    'success' => false,
                    'message' => 'Purchase not found'
                ], 404);
            }

            $analytics = $this->downloadService->getPurchaseDownloadAnalytics($purchase);

            return response()->json([
                'success' => true,
                'data' => $analytics,
                'message' => 'Download analytics retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Download analytics retrieval failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'purchase_id' => $purchaseId
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve download analytics'
            ], 500);
        }
    }

    /**
     * Get purchase analytics for a purchase
     */
    public function purchaseAnalytics(Request $request, int $purchaseId): JsonResponse
    {
        try {
            $user = Auth::user();

            $purchase = ProductPurchase::where('id', $purchaseId)
                ->where('user_id', $user->id)
                ->first();

            if (!$purchase) {
                return response()->json([
                    'success' => false,
                    'message' => 'Purchase not found'
                ], 404);
            }

            $analytics = $this->downloadService->getPurchaseAnalytics($purchaseId);

            return response()->json([
                'success' => true,
                'data' => $analytics,
                'message' => 'Purchase analytics retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Purchase analytics failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'purchase_id' => $purchaseId
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve purchase analytics'
            ], 500);
        }
    }

    /**
     * Stream protected file securely
     */
    private function streamFile(ProtectedFile $file): StreamedResponse
    {
        $filePath = storage_path('app/protected/' . $file->file_path);

        // Verify file exists
        if (!file_exists($filePath)) {
            Log::error('Protected file not found', [
                'file_path' => $filePath,
                'file_id' => $file->id
            ]);
            abort(404, 'File not found');
        }

        // Get file info
        $filename = $file->original_filename ?? basename($file->file_path);
        $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';
        $fileSize = filesize($filePath);

        Log::info('Streaming file download', [
            'file_id' => $file->id,
            'filename' => $filename,
            'file_size' => $fileSize,
            'mime_type' => $mimeType
        ]);

        return response()->stream(function () use ($filePath) {
            $stream = fopen($filePath, 'rb');

            if ($stream === false) {
                Log::error('Failed to open file stream', ['file_path' => $filePath]);
                return;
            }

            while (!feof($stream)) {
                echo fread($stream, 8192); // Read in 8KB chunks
                flush();
            }

            fclose($stream);
        }, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Content-Length' => $fileSize,
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'DENY'
        ]);
    }
}
