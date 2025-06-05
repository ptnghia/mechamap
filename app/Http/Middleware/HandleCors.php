<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class HandleCors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Lấy origin từ request
        $origin = $request->header('Origin');

        // Trong development, cho phép tất cả localhost
        if (app()->environment('local') && $origin && (
            str_contains($origin, 'localhost') ||
            str_contains($origin, '127.0.0.1') ||
            str_contains($origin, 'mechamap.test')
        )) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN, Accept');
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
            $response->headers->set('Access-Control-Max-Age', '86400');

            Log::info('CORS: Local development - Allowed origin: ' . $origin);
            return $response;
        }

        // Production: Sử dụng danh sách cố định (không có mechamap.com)
        $allowedOrigins = [
            'http://localhost:3000',
            'https://localhost:3000',
            'https://mechamap.test',
            'http://mechamap.test',
        ];

        // Nếu origin nằm trong danh sách được phép
        if ($origin && in_array($origin, $allowedOrigins)) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN, Accept');
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
            $response->headers->set('Access-Control-Max-Age', '86400');

            Log::info('CORS: Production - Allowed origin: ' . $origin);
        } else {
            Log::info('CORS: Origin not allowed: ' . $origin . ' (Available: ' . implode(', ', $allowedOrigins) . ')');
        }

        return $response;
    }
}
