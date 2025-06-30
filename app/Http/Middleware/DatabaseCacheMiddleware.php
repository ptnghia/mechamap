<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Database Cache Middleware
 * Implements intelligent query caching for database optimization
 */
class DatabaseCacheMiddleware
{
    protected $cachePrefix = 'db_cache';
    protected $defaultTtl = 3600; // 1 hour
    protected $enabledRoutes = [
        'threads.index',
        'threads.show',
        'marketplace.products.index',
        'marketplace.products.show',
        'categories.index',
        'forums.index',
        'users.index',
        'search.advanced',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only cache GET requests
        if (!$request->isMethod('GET')) {
            return $next($request);
        }

        // Check if route should be cached
        if (!$this->shouldCacheRoute($request)) {
            return $next($request);
        }

        // Generate cache key
        $cacheKey = $this->generateCacheKey($request);

        // Try to get cached response
        $cachedResponse = Cache::get($cacheKey);
        if ($cachedResponse && $this->isCacheValid($cachedResponse)) {
            Log::info('Database cache hit', [
                'route' => $request->route()->getName(),
                'cache_key' => $cacheKey,
            ]);

            return $this->createResponseFromCache($cachedResponse);
        }

        // Enable query logging for cache analysis
        $this->enableQueryLogging();

        // Process request
        $response = $next($request);

        // Cache successful responses
        if ($response->isSuccessful() && $this->shouldCacheResponse($response)) {
            $this->cacheResponse($cacheKey, $response, $request);
        }

        return $response;
    }

    /**
     * Check if route should be cached
     */
    private function shouldCacheRoute(Request $request): bool
    {
        $routeName = $request->route()?->getName();
        
        if (!$routeName) {
            return false;
        }

        // Check if route is in enabled list
        foreach ($this->enabledRoutes as $enabledRoute) {
            if (str_starts_with($routeName, $enabledRoute)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate cache key for request
     */
    private function generateCacheKey(Request $request): string
    {
        $routeName = $request->route()->getName();
        $parameters = $request->route()->parameters();
        $queryParams = $request->query();

        // Sort parameters for consistent cache keys
        ksort($parameters);
        ksort($queryParams);

        $keyData = [
            'route' => $routeName,
            'params' => $parameters,
            'query' => $queryParams,
            'user_role' => auth()->user()?->role ?? 'guest',
        ];

        $keyString = md5(serialize($keyData));
        
        return $this->cachePrefix . ':' . $routeName . ':' . $keyString;
    }

    /**
     * Check if cached data is still valid
     */
    private function isCacheValid(array $cachedData): bool
    {
        $expiresAt = $cachedData['expires_at'] ?? 0;
        return time() < $expiresAt;
    }

    /**
     * Create response from cached data
     */
    private function createResponseFromCache(array $cachedData): Response
    {
        $response = response($cachedData['content'], $cachedData['status']);
        
        // Restore headers
        foreach ($cachedData['headers'] as $name => $value) {
            $response->header($name, $value);
        }

        // Add cache headers
        $response->header('X-Cache-Status', 'HIT');
        $response->header('X-Cache-Key', $cachedData['cache_key'] ?? '');
        
        return $response;
    }

    /**
     * Check if response should be cached
     */
    private function shouldCacheResponse(Response $response): bool
    {
        // Only cache successful responses
        if (!$response->isSuccessful()) {
            return false;
        }

        // Don't cache responses with errors
        $content = $response->getContent();
        if (str_contains($content, 'error') || str_contains($content, 'exception')) {
            return false;
        }

        // Don't cache very large responses (> 1MB)
        if (strlen($content) > 1024 * 1024) {
            return false;
        }

        return true;
    }

    /**
     * Cache the response
     */
    private function cacheResponse(string $cacheKey, Response $response, Request $request): void
    {
        try {
            $ttl = $this->getCacheTtl($request);
            
            $cacheData = [
                'content' => $response->getContent(),
                'status' => $response->getStatusCode(),
                'headers' => $this->getResponseHeaders($response),
                'cache_key' => $cacheKey,
                'created_at' => time(),
                'expires_at' => time() + $ttl,
                'route' => $request->route()->getName(),
                'query_count' => $this->getQueryCount(),
            ];

            Cache::put($cacheKey, $cacheData, $ttl);

            Log::info('Database cache stored', [
                'route' => $request->route()->getName(),
                'cache_key' => $cacheKey,
                'ttl' => $ttl,
                'query_count' => $cacheData['query_count'],
                'size' => strlen($response->getContent()),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to cache response', [
                'cache_key' => $cacheKey,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get cache TTL based on route
     */
    private function getCacheTtl(Request $request): int
    {
        $routeName = $request->route()->getName();

        $ttlMap = [
            'threads.index' => 1800,      // 30 minutes
            'threads.show' => 3600,       // 1 hour
            'marketplace.products.index' => 1800,  // 30 minutes
            'marketplace.products.show' => 3600,   // 1 hour
            'categories.index' => 7200,   // 2 hours
            'forums.index' => 7200,       // 2 hours
            'users.index' => 1800,        // 30 minutes
            'search.advanced' => 900,     // 15 minutes
        ];

        foreach ($ttlMap as $route => $ttl) {
            if (str_starts_with($routeName, $route)) {
                return $ttl;
            }
        }

        return $this->defaultTtl;
    }

    /**
     * Get response headers to cache
     */
    private function getResponseHeaders(Response $response): array
    {
        $headers = [];
        $allowedHeaders = [
            'Content-Type',
            'Content-Encoding',
            'Vary',
            'ETag',
        ];

        foreach ($allowedHeaders as $header) {
            if ($response->headers->has($header)) {
                $headers[$header] = $response->headers->get($header);
            }
        }

        return $headers;
    }

    /**
     * Enable query logging for analysis
     */
    private function enableQueryLogging(): void
    {
        DB::enableQueryLog();
    }

    /**
     * Get number of queries executed
     */
    private function getQueryCount(): int
    {
        return count(DB::getQueryLog());
    }

    /**
     * Invalidate cache for specific patterns
     */
    public static function invalidateCache(string $pattern): void
    {
        try {
            // For Redis cache driver
            if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
                $redis = Cache::getStore()->getRedis();
                $keys = $redis->keys("db_cache:{$pattern}:*");
                if (!empty($keys)) {
                    $redis->del($keys);
                }
            } else {
                // For other cache drivers, we need to track keys manually
                // This is a simplified approach
                Cache::forget("db_cache:{$pattern}");
            }

            Log::info('Database cache invalidated', [
                'pattern' => $pattern,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to invalidate cache', [
                'pattern' => $pattern,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Clear all database cache
     */
    public static function clearAllCache(): void
    {
        try {
            // For Redis cache driver
            if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
                $redis = Cache::getStore()->getRedis();
                $keys = $redis->keys('db_cache:*');
                if (!empty($keys)) {
                    $redis->del($keys);
                }
            } else {
                // For other drivers, flush all cache (not ideal)
                Cache::flush();
            }

            Log::info('All database cache cleared');

        } catch (\Exception $e) {
            Log::error('Failed to clear database cache', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get cache statistics
     */
    public static function getCacheStats(): array
    {
        try {
            $stats = [
                'total_keys' => 0,
                'total_size' => 0,
                'hit_rate' => 0,
                'routes' => [],
            ];

            // For Redis cache driver
            if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
                $redis = Cache::getStore()->getRedis();
                $keys = $redis->keys('db_cache:*');
                
                $stats['total_keys'] = count($keys);
                
                foreach ($keys as $key) {
                    $data = $redis->get($key);
                    if ($data) {
                        $decoded = unserialize($data);
                        if (isset($decoded['route'])) {
                            $route = $decoded['route'];
                            if (!isset($stats['routes'][$route])) {
                                $stats['routes'][$route] = 0;
                            }
                            $stats['routes'][$route]++;
                        }
                        $stats['total_size'] += strlen($data);
                    }
                }
            }

            return $stats;

        } catch (\Exception $e) {
            Log::error('Failed to get cache stats', [
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }
}
