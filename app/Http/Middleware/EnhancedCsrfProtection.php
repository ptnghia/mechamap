<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class EnhancedCsrfProtection
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip CSRF for GET, HEAD, OPTIONS requests
        if (in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'])) {
            return $next($request);
        }

        // Enhanced CSRF validation
        if (!$this->validateCsrfToken($request)) {
            return $this->handleCsrfFailure($request);
        }

        // Rate limiting for form submissions
        if (!$this->checkRateLimit($request)) {
            return $this->handleRateLimitExceeded($request);
        }

        // Additional security checks
        if (!$this->performSecurityChecks($request)) {
            return $this->handleSecurityViolation($request);
        }

        return $next($request);
    }

    /**
     * Validate CSRF token with enhanced checks
     */
    private function validateCsrfToken(Request $request): bool
    {
        $token = $request->input('_token') ?: $request->header('X-CSRF-TOKEN');
        
        if (!$token) {
            Log::warning('CSRF token missing', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
                'user_id' => auth()->id(),
            ]);
            return false;
        }

        // Validate token format
        if (!$this->isValidTokenFormat($token)) {
            Log::warning('Invalid CSRF token format', [
                'token_length' => strlen($token),
                'ip' => $request->ip(),
                'user_id' => auth()->id(),
            ]);
            return false;
        }

        // Check if token is in session
        $sessionToken = $request->session()->token();
        if (!hash_equals($sessionToken, $token)) {
            Log::warning('CSRF token mismatch', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
                'user_id' => auth()->id(),
            ]);
            return false;
        }

        // Check token age (tokens older than 2 hours are suspicious)
        if (!$this->isTokenFresh($request)) {
            Log::warning('CSRF token too old', [
                'ip' => $request->ip(),
                'user_id' => auth()->id(),
            ]);
            return false;
        }

        return true;
    }

    /**
     * Check if token format is valid
     */
    private function isValidTokenFormat(string $token): bool
    {
        // Laravel CSRF tokens should be 40 characters long
        if (strlen($token) !== 40) {
            return false;
        }

        // Should only contain alphanumeric characters
        if (!preg_match('/^[a-zA-Z0-9]+$/', $token)) {
            return false;
        }

        return true;
    }

    /**
     * Check if token is fresh (not too old)
     */
    private function isTokenFresh(Request $request): bool
    {
        $sessionStartTime = $request->session()->get('_token_time');
        
        if (!$sessionStartTime) {
            // If no timestamp, assume token is fresh
            $request->session()->put('_token_time', now()->timestamp);
            return true;
        }

        $tokenAge = now()->timestamp - $sessionStartTime;
        
        // Tokens older than 2 hours are considered stale
        return $tokenAge < 7200;
    }

    /**
     * Check rate limits for form submissions
     */
    private function checkRateLimit(Request $request): bool
    {
        $key = 'form_submission:' . $request->ip();
        
        // Allow 30 form submissions per minute
        if (RateLimiter::tooManyAttempts($key, 30)) {
            Log::warning('Form submission rate limit exceeded', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'user_id' => auth()->id(),
            ]);
            return false;
        }

        RateLimiter::hit($key, 60); // 1 minute window
        
        // Additional rate limiting for file uploads
        if ($request->hasFile('cover_image') || $request->hasFile('file_attachments')) {
            $uploadKey = 'file_upload:' . $request->ip();
            
            // Allow 10 file uploads per minute
            if (RateLimiter::tooManyAttempts($uploadKey, 10)) {
                Log::warning('File upload rate limit exceeded', [
                    'ip' => $request->ip(),
                    'user_id' => auth()->id(),
                ]);
                return false;
            }
            
            RateLimiter::hit($uploadKey, 60);
        }

        return true;
    }

    /**
     * Perform additional security checks
     */
    private function performSecurityChecks(Request $request): bool
    {
        // Check for suspicious user agents
        if (!$this->validateUserAgent($request)) {
            return false;
        }

        // Check for suspicious referrer
        if (!$this->validateReferrer($request)) {
            return false;
        }

        // Check for request size anomalies
        if (!$this->validateRequestSize($request)) {
            return false;
        }

        // Check for suspicious headers
        if (!$this->validateHeaders($request)) {
            return false;
        }

        return true;
    }

    /**
     * Validate user agent
     */
    private function validateUserAgent(Request $request): bool
    {
        $userAgent = $request->userAgent();
        
        if (!$userAgent) {
            Log::warning('Missing user agent', [
                'ip' => $request->ip(),
                'user_id' => auth()->id(),
            ]);
            return false;
        }

        // Check for suspicious user agents
        $suspiciousPatterns = [
            '/bot/i',
            '/crawler/i',
            '/spider/i',
            '/scraper/i',
            '/curl/i',
            '/wget/i',
            '/python/i',
            '/perl/i',
            '/java/i',
        ];

        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $userAgent)) {
                Log::warning('Suspicious user agent detected', [
                    'user_agent' => $userAgent,
                    'ip' => $request->ip(),
                    'user_id' => auth()->id(),
                ]);
                return false;
            }
        }

        return true;
    }

    /**
     * Validate referrer
     */
    private function validateReferrer(Request $request): bool
    {
        $referrer = $request->header('referer');
        
        if ($referrer) {
            $allowedDomains = [
                'mechamap.test',
                'mechamap.com',
                'localhost',
                '127.0.0.1',
            ];
            
            $referrerHost = parse_url($referrer, PHP_URL_HOST);
            
            if (!in_array($referrerHost, $allowedDomains)) {
                Log::warning('Suspicious referrer detected', [
                    'referrer' => $referrer,
                    'ip' => $request->ip(),
                    'user_id' => auth()->id(),
                ]);
                return false;
            }
        }

        return true;
    }

    /**
     * Validate request size
     */
    private function validateRequestSize(Request $request): bool
    {
        $contentLength = $request->header('content-length');
        
        if ($contentLength) {
            // Maximum request size: 100MB
            if ($contentLength > 100 * 1024 * 1024) {
                Log::warning('Excessive request size', [
                    'content_length' => $contentLength,
                    'ip' => $request->ip(),
                    'user_id' => auth()->id(),
                ]);
                return false;
            }
        }

        return true;
    }

    /**
     * Validate headers for suspicious patterns
     */
    private function validateHeaders(Request $request): bool
    {
        $suspiciousHeaders = [
            'X-Forwarded-For',
            'X-Real-IP',
            'X-Originating-IP',
            'X-Remote-IP',
            'X-Remote-Addr',
        ];

        foreach ($suspiciousHeaders as $header) {
            $value = $request->header($header);
            if ($value && $this->containsSuspiciousContent($value)) {
                Log::warning('Suspicious header content', [
                    'header' => $header,
                    'value' => $value,
                    'ip' => $request->ip(),
                    'user_id' => auth()->id(),
                ]);
                return false;
            }
        }

        return true;
    }

    /**
     * Check if content contains suspicious patterns
     */
    private function containsSuspiciousContent(string $content): bool
    {
        $suspiciousPatterns = [
            '/[<>"\']/',
            '/javascript:/i',
            '/vbscript:/i',
            '/data:/i',
            '/\x00/',
        ];

        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Handle CSRF failure
     */
    private function handleCsrfFailure(Request $request): Response
    {
        // Increment failure counter
        $key = 'csrf_failures:' . $request->ip();
        $failures = Cache::increment($key, 1);
        Cache::put($key, $failures, now()->addHour());

        // Block IP after 5 failures
        if ($failures >= 5) {
            Log::error('IP blocked due to repeated CSRF failures', [
                'ip' => $request->ip(),
                'failures' => $failures,
                'user_id' => auth()->id(),
            ]);
            
            return response()->json([
                'error' => 'Access denied due to security violations.'
            ], 403);
        }

        return response()->json([
            'error' => 'CSRF token mismatch. Please refresh the page and try again.'
        ], 419);
    }

    /**
     * Handle rate limit exceeded
     */
    private function handleRateLimitExceeded(Request $request): Response
    {
        return response()->json([
            'error' => 'Too many requests. Please wait before trying again.'
        ], 429);
    }

    /**
     * Handle security violation
     */
    private function handleSecurityViolation(Request $request): Response
    {
        return response()->json([
            'error' => 'Request blocked due to security policy violation.'
        ], 403);
    }
}
