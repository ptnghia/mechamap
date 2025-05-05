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

        // Lấy danh sách domain được phép từ cấu hình
        $allowedOrigins = explode(',', env('CORS_ALLOWED_ORIGINS', 'https://mechamap.com,https://www.mechamap.com,http://localhost:3000'));

        // Lấy origin từ request
        $origin = $request->header('Origin');

        // Debug
        Log::info('CORS Request from origin: ' . $origin);
        Log::info('Allowed origins: ' . implode(', ', $allowedOrigins));

        // Nếu origin nằm trong danh sách được phép, thêm header CORS
        if ($origin && in_array($origin, $allowedOrigins)) {
            Log::info('Origin is allowed: ' . $origin);
            $response->headers->set('Access-Control-Allow-Origin', $origin);
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN');
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
            $response->headers->set('Access-Control-Max-Age', '86400');
        } else {
            Log::info('Origin is not allowed: ' . $origin);
        }

        return $response;
    }
}
