<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AssetCacheHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Chỉ áp dụng cho static assets
        if ($this->isStaticAsset($request)) {
            // Nếu có version parameter, cache lâu hơn
            if ($request->has('v')) {
                // Cache 1 năm cho versioned assets
                $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
                $response->headers->set('Expires', gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
            } else {
                // Cache ngắn hạn cho non-versioned assets
                $response->headers->set('Cache-Control', 'public, max-age=3600');
                $response->headers->set('Expires', gmdate('D, d M Y H:i:s', time() + 3600) . ' GMT');
            }

            // Thêm ETag cho better caching
            $etag = md5($response->getContent());
            $response->headers->set('ETag', '"' . $etag . '"');

            // Check if client has cached version
            if ($request->header('If-None-Match') === '"' . $etag . '"') {
                return response('', 304);
            }
        }

        return $response;
    }

    /**
     * Determine if the request is for a static asset
     */
    private function isStaticAsset(Request $request): bool
    {
        $path = $request->path();
        
        return preg_match('/\.(css|js|png|jpg|jpeg|gif|svg|ico|woff|woff2|ttf|eot)$/i', $path);
    }
}
