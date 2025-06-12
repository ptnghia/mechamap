<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\DownloadToken;
use App\Models\ProductPurchase;
use Carbon\Carbon;

class VerifyDownloadAccess
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->route('token');

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Download token required'
            ], 401);
        }

        // Find and validate download token
        $downloadToken = DownloadToken::where('token', $token)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$downloadToken) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired download token'
            ], 401);
        }

        // Check if token is already used
        if ($downloadToken->is_used) {
            return response()->json([
                'success' => false,
                'message' => 'Download token already used'
            ], 403);
        }

        // Verify purchase is still valid
        $purchase = ProductPurchase::find($downloadToken->product_purchase_id);

        if (!$purchase || $purchase->status !== 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Purchase not found or invalid'
            ], 403);
        }

        // Check download limits based on license type
        $maxDownloads = $this->getMaxDownloads($purchase->license_type);

        if ($downloadToken->download_attempts >= $maxDownloads) {
            return response()->json([
                'success' => false,
                'message' => 'Download limit exceeded for this license'
            ], 403);
        }

        // Store token and purchase in request for controller use
        $request->merge([
            'download_token' => $downloadToken,
            'product_purchase' => $purchase
        ]);

        return $next($request);
    }

    private function getMaxDownloads(string $licenseType): int
    {
        return match($licenseType) {
            'standard' => 3,
            'extended' => 10,
            'commercial' => 50,
            default => 1
        };
    }
}
