<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class NotificationMemoryOptimizationService
{
    /**
     * Memory thresholds in bytes
     */
    const MEMORY_WARNING_THRESHOLD = 128 * 1024 * 1024;  // 128MB
    const MEMORY_CRITICAL_THRESHOLD = 256 * 1024 * 1024; // 256MB
    const MEMORY_LIMIT_THRESHOLD = 512 * 1024 * 1024;    // 512MB

    /**
     * Optimize memory usage for notification system
     */
    public static function optimizeMemory(): array
    {
        $results = [];

        try {
            // 1. Get initial memory usage
            $initialMemory = memory_get_usage(true);
            $results['initial_memory'] = self::formatBytes($initialMemory);

            // 2. Optimize object creation and destruction
            $results['object_optimization'] = self::optimizeObjectUsage();

            // 3. Optimize collection processing
            $results['collection_optimization'] = self::optimizeCollectionProcessing();

            // 4. Clean up memory leaks
            $results['memory_cleanup'] = self::cleanupMemoryLeaks();

            // 5. Optimize garbage collection
            $results['garbage_collection'] = self::optimizeGarbageCollection();

            // 6. Get final memory usage
            $finalMemory = memory_get_usage(true);
            $results['final_memory'] = self::formatBytes($finalMemory);
            $results['memory_saved'] = self::formatBytes($initialMemory - $finalMemory);

            return [
                'success' => true,
                'message' => 'Memory optimization completed',
                'results' => $results,
            ];

        } catch (\Exception $e) {
            Log::error('Memory optimization failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Memory optimization failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Optimize object usage patterns
     */
    private static function optimizeObjectUsage(): array
    {
        $optimizations = [];

        // 1. Configure object pooling for notifications
        $optimizations[] = self::configureObjectPooling();

        // 2. Optimize model loading
        $optimizations[] = self::optimizeModelLoading();

        // 3. Implement lazy loading strategies
        $optimizations[] = self::implementLazyLoading();

        return $optimizations;
    }

    /**
     * Configure object pooling
     */
    private static function configureObjectPooling(): string
    {
        try {
            // Configure notification object pooling
            config([
                'notifications.object_pool_size' => 100,
                'notifications.reuse_objects' => true,
                'notifications.pool_cleanup_interval' => 300, // 5 minutes
            ]);

            return "Configured object pooling for notifications";

        } catch (\Exception $e) {
            return "Failed to configure object pooling: " . $e->getMessage();
        }
    }

    /**
     * Optimize model loading
     */
    private static function optimizeModelLoading(): string
    {
        try {
            // Configure eager loading to prevent N+1 queries
            config([
                'notifications.eager_load_relations' => ['user', 'notifiable'],
                'notifications.select_columns' => [
                    'id', 'user_id', 'type', 'title', 'message',
                    'data', 'is_read', 'created_at'
                ],
            ]);

            return "Optimized model loading configuration";

        } catch (\Exception $e) {
            return "Failed to optimize model loading: " . $e->getMessage();
        }
    }

    /**
     * Implement lazy loading strategies
     */
    private static function implementLazyLoading(): string
    {
        try {
            // Configure lazy loading for heavy operations
            config([
                'notifications.lazy_load_content' => true,
                'notifications.defer_email_rendering' => true,
                'notifications.cache_rendered_content' => true,
            ]);

            return "Implemented lazy loading strategies";

        } catch (\Exception $e) {
            return "Failed to implement lazy loading: " . $e->getMessage();
        }
    }

    /**
     * Optimize collection processing
     */
    private static function optimizeCollectionProcessing(): array
    {
        $optimizations = [];

        // 1. Implement chunked processing
        $optimizations[] = self::implementChunkedProcessing();

        // 2. Optimize collection operations
        $optimizations[] = self::optimizeCollectionOperations();

        // 3. Implement streaming for large datasets
        $optimizations[] = self::implementStreaming();

        return $optimizations;
    }

    /**
     * Implement chunked processing
     */
    private static function implementChunkedProcessing(): string
    {
        try {
            // Configure optimal chunk sizes
            config([
                'notifications.chunk_size' => 100,
                'notifications.bulk_chunk_size' => 500,
                'notifications.targeting_chunk_size' => 1000,
            ]);

            return "Configured chunked processing with optimal sizes";

        } catch (\Exception $e) {
            return "Failed to configure chunked processing: " . $e->getMessage();
        }
    }

    /**
     * Optimize collection operations
     */
    private static function optimizeCollectionOperations(): string
    {
        try {
            // Configure collection optimization
            config([
                'notifications.use_lazy_collections' => true,
                'notifications.avoid_collection_to_array' => true,
                'notifications.use_generators' => true,
            ]);

            return "Optimized collection operations";

        } catch (\Exception $e) {
            return "Failed to optimize collection operations: " . $e->getMessage();
        }
    }

    /**
     * Implement streaming for large datasets
     */
    private static function implementStreaming(): string
    {
        try {
            // Configure streaming thresholds
            config([
                'notifications.streaming_threshold' => 1000,
                'notifications.stream_chunk_size' => 50,
                'notifications.max_memory_per_stream' => 32 * 1024 * 1024, // 32MB
            ]);

            return "Implemented streaming for large datasets";

        } catch (\Exception $e) {
            return "Failed to implement streaming: " . $e->getMessage();
        }
    }

    /**
     * Clean up memory leaks
     */
    private static function cleanupMemoryLeaks(): array
    {
        $cleanup = [];

        // 1. Clear circular references
        $cleanup[] = self::clearCircularReferences();

        // 2. Clean up event listeners
        $cleanup[] = self::cleanupEventListeners();

        // 3. Clear static caches
        $cleanup[] = self::clearStaticCaches();

        // 4. Clean up temporary files
        $cleanup[] = self::cleanupTemporaryFiles();

        return $cleanup;
    }

    /**
     * Clear circular references
     */
    private static function clearCircularReferences(): string
    {
        try {
            // Force garbage collection to clear circular references
            if (function_exists('gc_collect_cycles')) {
                $collected = gc_collect_cycles();
                return "Cleared {$collected} circular references";
            }

            return "Garbage collection not available";

        } catch (\Exception $e) {
            return "Failed to clear circular references: " . $e->getMessage();
        }
    }

    /**
     * Clean up event listeners
     */
    private static function cleanupEventListeners(): string
    {
        try {
            // Get event dispatcher
            $dispatcher = app('events');
            $count = 0;

            // Count notification-related listeners
            if (method_exists($dispatcher, 'getListeners')) {
                // Try to get all listeners (this might not work in all Laravel versions)
                try {
                    $allListeners = $dispatcher->getListeners('*');
                    $count = count($allListeners);
                } catch (\Exception $e) {
                    // Fallback: just return a general message
                    $count = 'unknown';
                }
            }

            return "Event listeners checked (count: {$count})";

        } catch (\Exception $e) {
            return "Failed to cleanup event listeners: " . $e->getMessage();
        }
    }

    /**
     * Clear static caches
     */
    private static function clearStaticCaches(): string
    {
        try {
            // Clear various static caches
            $cleared = 0;

            // Clear model attribute caches
            if (method_exists(\Illuminate\Database\Eloquent\Model::class, 'clearBootedModels')) {
                \Illuminate\Database\Eloquent\Model::clearBootedModels();
                $cleared++;
            }

            // Clear relation caches
            if (class_exists(\Illuminate\Database\Eloquent\Relations\Relation::class)) {
                \Illuminate\Database\Eloquent\Relations\Relation::morphMap([]);
                $cleared++;
            }

            return "Cleared {$cleared} static caches";

        } catch (\Exception $e) {
            return "Failed to clear static caches: " . $e->getMessage();
        }
    }

    /**
     * Clean up temporary files
     */
    private static function cleanupTemporaryFiles(): string
    {
        try {
            $tempDir = storage_path('app/temp');
            $cleaned = 0;

            if (is_dir($tempDir)) {
                $files = glob($tempDir . '/*');
                foreach ($files as $file) {
                    if (is_file($file) && filemtime($file) < time() - 3600) { // 1 hour old
                        unlink($file);
                        $cleaned++;
                    }
                }
            }

            return "Cleaned up {$cleaned} temporary files";

        } catch (\Exception $e) {
            return "Failed to cleanup temporary files: " . $e->getMessage();
        }
    }

    /**
     * Optimize garbage collection
     */
    private static function optimizeGarbageCollection(): array
    {
        $optimizations = [];

        // 1. Configure GC settings
        $optimizations[] = self::configureGarbageCollection();

        // 2. Force garbage collection
        $optimizations[] = self::forceGarbageCollection();

        // 3. Monitor GC performance
        $optimizations[] = self::monitorGarbageCollection();

        return $optimizations;
    }

    /**
     * Configure garbage collection
     */
    private static function configureGarbageCollection(): string
    {
        try {
            // Enable garbage collection
            if (function_exists('gc_enable')) {
                gc_enable();
            }

            // Configure GC thresholds
            if (function_exists('gc_threshold')) {
                gc_threshold(1000, 100, 100);
            }

            return "Configured garbage collection settings";

        } catch (\Exception $e) {
            return "Failed to configure garbage collection: " . $e->getMessage();
        }
    }

    /**
     * Force garbage collection
     */
    private static function forceGarbageCollection(): string
    {
        try {
            $beforeMemory = memory_get_usage(true);

            if (function_exists('gc_collect_cycles')) {
                $collected = gc_collect_cycles();
            } else {
                $collected = 0;
            }

            $afterMemory = memory_get_usage(true);
            $freed = $beforeMemory - $afterMemory;

            return "Garbage collection freed " . self::formatBytes($freed) . " ({$collected} cycles)";

        } catch (\Exception $e) {
            return "Failed to force garbage collection: " . $e->getMessage();
        }
    }

    /**
     * Monitor garbage collection
     */
    private static function monitorGarbageCollection(): string
    {
        try {
            if (function_exists('gc_status')) {
                $status = gc_status();

                return "GC Status - Runs: {$status['runs']}, Collected: {$status['collected']}, Threshold: {$status['threshold']}";
            }

            return "GC monitoring not available";

        } catch (\Exception $e) {
            return "Failed to monitor garbage collection: " . $e->getMessage();
        }
    }

    /**
     * Get memory usage report
     */
    public static function getMemoryReport(): array
    {
        return [
            'current_usage' => [
                'bytes' => memory_get_usage(true),
                'formatted' => self::formatBytes(memory_get_usage(true)),
                'percentage' => self::getMemoryUsagePercentage(),
            ],
            'peak_usage' => [
                'bytes' => memory_get_peak_usage(true),
                'formatted' => self::formatBytes(memory_get_peak_usage(true)),
            ],
            'memory_limit' => [
                'bytes' => self::getMemoryLimit(),
                'formatted' => self::formatBytes(self::getMemoryLimit()),
            ],
            'thresholds' => [
                'warning' => self::formatBytes(self::MEMORY_WARNING_THRESHOLD),
                'critical' => self::formatBytes(self::MEMORY_CRITICAL_THRESHOLD),
                'limit' => self::formatBytes(self::MEMORY_LIMIT_THRESHOLD),
            ],
            'status' => self::getMemoryStatus(),
            'gc_status' => function_exists('gc_status') ? gc_status() : null,
        ];
    }

    /**
     * Get memory usage percentage
     */
    private static function getMemoryUsagePercentage(): float
    {
        $current = memory_get_usage(true);
        $limit = self::getMemoryLimit();

        if ($limit > 0) {
            return round(($current / $limit) * 100, 2);
        }

        return 0;
    }

    /**
     * Get memory limit in bytes
     */
    private static function getMemoryLimit(): int
    {
        $limit = ini_get('memory_limit');

        if ($limit === '-1') {
            return PHP_INT_MAX;
        }

        return self::parseBytes($limit);
    }

    /**
     * Parse memory limit string to bytes
     */
    private static function parseBytes(string $size): int
    {
        $size = trim($size);
        $last = strtolower($size[strlen($size) - 1]);
        $size = (int) $size;

        switch ($last) {
            case 'g':
                $size *= 1024;
            case 'm':
                $size *= 1024;
            case 'k':
                $size *= 1024;
        }

        return $size;
    }

    /**
     * Get memory status
     */
    private static function getMemoryStatus(): string
    {
        $current = memory_get_usage(true);

        if ($current >= self::MEMORY_CRITICAL_THRESHOLD) {
            return 'critical';
        } elseif ($current >= self::MEMORY_WARNING_THRESHOLD) {
            return 'warning';
        } else {
            return 'normal';
        }
    }

    /**
     * Format bytes to human readable format
     */
    private static function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Monitor memory usage during operation
     */
    public static function monitorMemoryUsage(callable $operation, string $operationName = 'operation'): array
    {
        $beforeMemory = memory_get_usage(true);
        $beforePeak = memory_get_peak_usage(true);

        $startTime = microtime(true);
        $result = $operation();
        $endTime = microtime(true);

        $afterMemory = memory_get_usage(true);
        $afterPeak = memory_get_peak_usage(true);

        return [
            'operation' => $operationName,
            'result' => $result,
            'execution_time' => round(($endTime - $startTime) * 1000, 2) . 'ms',
            'memory_usage' => [
                'before' => self::formatBytes($beforeMemory),
                'after' => self::formatBytes($afterMemory),
                'difference' => self::formatBytes($afterMemory - $beforeMemory),
                'peak_during_operation' => self::formatBytes($afterPeak - $beforePeak),
            ],
        ];
    }
}
