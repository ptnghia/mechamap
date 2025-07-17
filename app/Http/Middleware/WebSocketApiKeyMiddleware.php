<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class WebSocketApiKeyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-WebSocket-API-Key') ?? 
                  $request->header('Authorization') ?? 
                  $request->get('api_key');

        // Remove 'Bearer ' prefix if present
        if (str_starts_with($apiKey, 'Bearer ')) {
            $apiKey = substr($apiKey, 7);
        }

        if (!$apiKey) {
            Log::warning('WebSocket API: Missing API key', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'API key required',
                'error' => 'MISSING_API_KEY'
            ], 401);
        }

        // Verify API key
        if (!$this->verifyApiKey($apiKey)) {
            Log::warning('WebSocket API: Invalid API key', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
                'api_key_prefix' => substr($apiKey, 0, 10) . '...'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Invalid API key',
                'error' => 'INVALID_API_KEY'
            ], 401);
        }

        // Add API key info to request
        $request->attributes->set('websocket_api_authenticated', true);
        $request->attributes->set('api_key_hash', hash('sha256', $apiKey));

        Log::info('WebSocket API: Authenticated request', [
            'ip' => $request->ip(),
            'url' => $request->fullUrl(),
            'api_key_hash' => hash('sha256', $apiKey)
        ]);

        return $next($request);
    }

    /**
     * Verify API key against stored hash
     */
    private function verifyApiKey(string $apiKey): bool
    {
        $environment = app()->environment();
        $expectedHash = config("websocket.api_key_hash_{$environment}") ?? 
                       env('WEBSOCKET_API_KEY_HASH');

        if (!$expectedHash) {
            // Fallback: check cache
            $cacheKey = "websocket_api_key_{$environment}";
            $expectedHash = Cache::get($cacheKey);
        }

        if (!$expectedHash) {
            Log::error('WebSocket API: No API key hash configured');
            return false;
        }

        $providedHash = hash('sha256', $apiKey);
        
        return hash_equals($expectedHash, $providedHash);
    }
}
