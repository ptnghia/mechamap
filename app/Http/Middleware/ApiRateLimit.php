<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Response;

class ApiRateLimit
{
    /**
     * Xử lý rate limiting cho API
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $key
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $key = 'api')
    {
        // Lấy rate limit config từ settings
        $maxAttempts = $this->getMaxAttempts($key);
        $decayMinutes = $this->getDecayMinutes($key);

        // Tạo unique identifier cho rate limiting
        $identifier = $this->resolveRequestSignature($request, $key);

        // Kiểm tra rate limit
        if (RateLimiter::tooManyAttempts($identifier, $maxAttempts)) {
            return $this->buildTooManyAttemptsResponse($identifier, $maxAttempts);
        }

        // Increment attempts
        RateLimiter::increment($identifier, $decayMinutes * 60);

        $response = $next($request);

        // Thêm rate limit headers
        return $this->addHeaders(
            $response,
            $maxAttempts,
            RateLimiter::attempts($identifier),
            RateLimiter::availableIn($identifier)
        );
    }

    /**
     * Resolve request signature cho rate limiting
     */
    protected function resolveRequestSignature(Request $request, string $key): string
    {
        // Nếu user đã đăng nhập, sử dụng user ID
        if ($request->user()) {
            return sha1($key . '|' . $request->user()->id);
        }

        // Nếu chưa đăng nhập, sử dụng IP address
        return sha1($key . '|' . $request->ip());
    }

    /**
     * Lấy max attempts cho key cụ thể
     */
    protected function getMaxAttempts(string $key): int
    {
        $limits = [
            'api' => 60,           // 60 requests per minute cho API chung
            'search' => 30,        // 30 requests per minute cho search
            'auth' => 5,          // 5 attempts per minute cho authentication
            'upload' => 10,       // 10 uploads per minute
            'admin' => 120,       // 120 requests per minute cho admin
        ];

        return $limits[$key] ?? 60;
    }

    /**
     * Lấy decay minutes cho key cụ thể
     */
    protected function getDecayMinutes(string $key): int
    {
        $decayTimes = [
            'api' => 1,           // 1 minute
            'search' => 1,        // 1 minute
            'auth' => 5,          // 5 minutes cho security
            'upload' => 2,        // 2 minutes
            'admin' => 1,         // 1 minute
        ];

        return $decayTimes[$key] ?? 1;
    }

    /**
     * Tạo response khi quá rate limit
     */
    protected function buildTooManyAttemptsResponse(string $key, int $maxAttempts): Response
    {
        $retryAfter = RateLimiter::availableIn($key);

        return response()->json([
            'success' => false,
            'message' => 'Quá nhiều yêu cầu. Vui lòng thử lại sau.',
            'error' => 'rate_limit_exceeded',
            'retry_after' => $retryAfter,
            'max_attempts' => $maxAttempts
        ], 429)->header('Retry-After', $retryAfter);
    }

    /**
     * Thêm rate limit headers vào response
     */
    protected function addHeaders($response, int $maxAttempts, int $attempts, int $retryAfter)
    {
        return $response->withHeaders([
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => max(0, $maxAttempts - $attempts),
            'X-RateLimit-Reset' => time() + $retryAfter,
        ]);
    }
}
