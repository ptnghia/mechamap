<?php

namespace App\Services;

use App\Models\TypingIndicator;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use App\Events\TypingStarted;
use App\Events\TypingStopped;
use App\Events\TypingUpdated;

class TypingIndicatorService
{
    /**
     * Start typing indicator
     */
    public static function startTyping(
        int $userId,
        string $contextType,
        int $contextId,
        string $typingType = TypingIndicator::TYPE_COMMENT,
        array $metadata = []
    ): array {
        try {
            $user = User::find($userId);
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'User not found',
                ];
            }

            // Create or update typing indicator
            $indicator = TypingIndicator::startTyping(
                $userId,
                $contextType,
                $contextId,
                $typingType,
                $metadata
            );

            // Clear cache for this context
            static::clearContextCache($contextType, $contextId, $typingType);

            // Broadcast typing started event
            Event::dispatch(new TypingStarted($indicator));

            Log::debug("Typing started", [
                'user_id' => $userId,
                'context_type' => $contextType,
                'context_id' => $contextId,
                'typing_type' => $typingType,
                'indicator_id' => $indicator->id,
            ]);

            return [
                'success' => true,
                'message' => 'Typing indicator started',
                'data' => [
                    'indicator_id' => $indicator->id,
                    'expires_at' => $indicator->expires_at,
                    'time_remaining' => $indicator->time_remaining,
                ],
            ];

        } catch (\Exception $e) {
            Log::error("Failed to start typing indicator", [
                'user_id' => $userId,
                'context_type' => $contextType,
                'context_id' => $contextId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to start typing indicator',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Update typing activity
     */
    public static function updateTyping(
        int $userId,
        string $contextType,
        int $contextId,
        string $typingType = TypingIndicator::TYPE_COMMENT,
        int $extensionSeconds = null
    ): array {
        try {
            $indicator = TypingIndicator::where('user_id', $userId)
                ->where('context_type', $contextType)
                ->where('context_id', $contextId)
                ->where('typing_type', $typingType)
                ->first();

            if (!$indicator) {
                // If no indicator exists, start a new one
                return static::startTyping($userId, $contextType, $contextId, $typingType);
            }

            // Update activity
            $indicator->updateActivity($extensionSeconds);

            // Clear cache
            static::clearContextCache($contextType, $contextId, $typingType);

            // Broadcast typing updated event
            Event::dispatch(new TypingUpdated($indicator));

            Log::debug("Typing updated", [
                'user_id' => $userId,
                'context_type' => $contextType,
                'context_id' => $contextId,
                'indicator_id' => $indicator->id,
                'new_expires_at' => $indicator->expires_at,
            ]);

            return [
                'success' => true,
                'message' => 'Typing indicator updated',
                'data' => [
                    'indicator_id' => $indicator->id,
                    'expires_at' => $indicator->expires_at,
                    'time_remaining' => $indicator->time_remaining,
                ],
            ];

        } catch (\Exception $e) {
            Log::error("Failed to update typing indicator", [
                'user_id' => $userId,
                'context_type' => $contextType,
                'context_id' => $contextId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to update typing indicator',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Stop typing indicator
     */
    public static function stopTyping(
        int $userId,
        string $contextType,
        int $contextId,
        string $typingType = TypingIndicator::TYPE_COMMENT
    ): array {
        try {
            $indicator = TypingIndicator::where('user_id', $userId)
                ->where('context_type', $contextType)
                ->where('context_id', $contextId)
                ->where('typing_type', $typingType)
                ->first();

            if (!$indicator) {
                return [
                    'success' => true,
                    'message' => 'No typing indicator to stop',
                ];
            }

            $deleted = TypingIndicator::stopTyping($userId, $contextType, $contextId, $typingType);

            if ($deleted) {
                // Clear cache
                static::clearContextCache($contextType, $contextId, $typingType);

                // Broadcast typing stopped event
                Event::dispatch(new TypingStopped($indicator));

                Log::debug("Typing stopped", [
                    'user_id' => $userId,
                    'context_type' => $contextType,
                    'context_id' => $contextId,
                    'typing_type' => $typingType,
                ]);
            }

            return [
                'success' => true,
                'message' => 'Typing indicator stopped',
                'data' => [
                    'deleted' => $deleted,
                ],
            ];

        } catch (\Exception $e) {
            Log::error("Failed to stop typing indicator", [
                'user_id' => $userId,
                'context_type' => $contextType,
                'context_id' => $contextId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to stop typing indicator',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get active typing indicators for context
     */
    public static function getActiveIndicators(
        string $contextType,
        int $contextId,
        string $typingType = null,
        int $excludeUserId = null
    ): array {
        try {
            $cacheKey = static::getContextCacheKey($contextType, $contextId, $typingType);
            
            return Cache::remember($cacheKey, now()->addSeconds(5), function () use ($contextType, $contextId, $typingType, $excludeUserId) {
                $query = TypingIndicator::active()
                    ->forContext($contextType, $contextId)
                    ->with(['user:id,name,avatar']);

                if ($typingType) {
                    $query->forTypingType($typingType);
                }

                if ($excludeUserId) {
                    $query->where('user_id', '!=', $excludeUserId);
                }

                $indicators = $query->orderBy('started_at', 'desc')->get();

                return $indicators->map(function ($indicator) {
                    return [
                        'id' => $indicator->id,
                        'user' => [
                            'id' => $indicator->user->id,
                            'name' => $indicator->user->name,
                            'avatar' => $indicator->user->avatar_url,
                        ],
                        'context_type' => $indicator->context_type,
                        'context_id' => $indicator->context_id,
                        'typing_type' => $indicator->typing_type,
                        'started_at' => $indicator->started_at,
                        'last_activity_at' => $indicator->last_activity_at,
                        'expires_at' => $indicator->expires_at,
                        'time_remaining' => $indicator->time_remaining,
                        'typing_duration' => $indicator->typing_duration,
                        'metadata' => $indicator->metadata,
                    ];
                })->toArray();
            });

        } catch (\Exception $e) {
            Log::error("Failed to get active typing indicators", [
                'context_type' => $contextType,
                'context_id' => $contextId,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Get user's current typing contexts
     */
    public static function getUserTypingContexts(int $userId): array
    {
        try {
            $cacheKey = "user_typing_contexts_{$userId}";
            
            return Cache::remember($cacheKey, now()->addSeconds(5), function () use ($userId) {
                return TypingIndicator::getUserTypingContexts($userId);
            });

        } catch (\Exception $e) {
            Log::error("Failed to get user typing contexts", [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Clean up expired indicators
     */
    public static function cleanupExpired(): int
    {
        try {
            $deleted = TypingIndicator::cleanupExpired();

            if ($deleted > 0) {
                Log::info("Cleaned up expired typing indicators", [
                    'deleted_count' => $deleted,
                ]);

                // Clear all context caches
                static::clearAllContextCaches();
            }

            return $deleted;

        } catch (\Exception $e) {
            Log::error("Failed to cleanup expired typing indicators", [
                'error' => $e->getMessage(),
            ]);

            return 0;
        }
    }

    /**
     * Get typing statistics
     */
    public static function getTypingStatistics(): array
    {
        try {
            $cacheKey = 'typing_indicator_statistics';
            
            return Cache::remember($cacheKey, now()->addMinutes(5), function () {
                return TypingIndicator::getTypingStatistics();
            });

        } catch (\Exception $e) {
            Log::error("Failed to get typing statistics", [
                'error' => $e->getMessage(),
            ]);

            return [
                'total_active' => 0,
                'by_context_type' => [],
                'by_typing_type' => [],
                'average_duration' => 0,
            ];
        }
    }

    /**
     * Get context cache key
     */
    private static function getContextCacheKey(string $contextType, int $contextId, string $typingType = null): string
    {
        $key = "typing_indicators_{$contextType}_{$contextId}";
        if ($typingType) {
            $key .= "_{$typingType}";
        }
        return $key;
    }

    /**
     * Clear context cache
     */
    private static function clearContextCache(string $contextType, int $contextId, string $typingType = null): void
    {
        $patterns = [
            static::getContextCacheKey($contextType, $contextId),
            static::getContextCacheKey($contextType, $contextId, $typingType),
        ];

        foreach ($patterns as $pattern) {
            Cache::forget($pattern);
        }

        // Also clear user typing contexts cache for all users in this context
        // This is a simplified approach - in production you might want more targeted cache clearing
        Cache::forget('typing_indicator_statistics');
    }

    /**
     * Clear all context caches
     */
    private static function clearAllContextCaches(): void
    {
        // This would clear all typing indicator caches
        // In production, you might want to use cache tags for more efficient clearing
        Cache::forget('typing_indicator_statistics');
    }

    /**
     * Auto-stop typing after inactivity
     */
    public static function autoStopInactiveTyping(): int
    {
        try {
            // Find indicators that haven't been updated for more than 2 minutes
            $inactiveThreshold = now()->subMinutes(2);
            
            $inactiveIndicators = TypingIndicator::where('last_activity_at', '<', $inactiveThreshold)
                ->where('expires_at', '>', now()) // Still technically active but inactive
                ->get();

            $stopped = 0;
            foreach ($inactiveIndicators as $indicator) {
                $indicator->delete();
                $stopped++;

                // Broadcast typing stopped event
                Event::dispatch(new TypingStopped($indicator));
            }

            if ($stopped > 0) {
                Log::info("Auto-stopped inactive typing indicators", [
                    'stopped_count' => $stopped,
                ]);

                static::clearAllContextCaches();
            }

            return $stopped;

        } catch (\Exception $e) {
            Log::error("Failed to auto-stop inactive typing", [
                'error' => $e->getMessage(),
            ]);

            return 0;
        }
    }
}
