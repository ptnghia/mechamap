<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Http\Middleware\DatabaseCacheMiddleware;

/**
 * Cache Invalidation Observer
 * Automatically invalidates relevant caches when models are modified
 */
class CacheInvalidationObserver
{
    protected $cachePrefix = 'db_cache';

    /**
     * Handle the model "created" event.
     */
    public function created(Model $model): void
    {
        $this->invalidateModelCache($model, 'created');
    }

    /**
     * Handle the model "updated" event.
     */
    public function updated(Model $model): void
    {
        $this->invalidateModelCache($model, 'updated');
    }

    /**
     * Handle the model "deleted" event.
     */
    public function deleted(Model $model): void
    {
        $this->invalidateModelCache($model, 'deleted');
    }

    /**
     * Handle the model "restored" event.
     */
    public function restored(Model $model): void
    {
        $this->invalidateModelCache($model, 'restored');
    }

    /**
     * Handle the model "force deleted" event.
     */
    public function forceDeleted(Model $model): void
    {
        $this->invalidateModelCache($model, 'force_deleted');
    }

    /**
     * Invalidate cache based on model changes
     */
    private function invalidateModelCache(Model $model, string $action): void
    {
        try {
            $modelClass = get_class($model);
            $modelName = class_basename($modelClass);

            Log::info('Cache invalidation triggered', [
                'model' => $modelName,
                'action' => $action,
                'id' => $model->getKey(),
            ]);

            // Get cache invalidation patterns for this model
            $patterns = $this->getCacheInvalidationPatterns($model, $action);

            // Invalidate caches
            foreach ($patterns as $pattern) {
                DatabaseCacheMiddleware::invalidateCache($pattern);
            }

            // Model-specific cache invalidation
            $this->invalidateModelSpecificCache($model, $action);

        } catch (\Exception $e) {
            Log::error('Cache invalidation failed', [
                'model' => get_class($model),
                'action' => $action,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get cache invalidation patterns for a model
     */
    private function getCacheInvalidationPatterns(Model $model, string $action): array
    {
        $modelName = class_basename(get_class($model));
        $patterns = [];

        switch ($modelName) {
            case 'Thread':
                $patterns = [
                    'threads.index',
                    'threads.show',
                    'forums.index',
                    'categories.index',
                    'search.advanced',
                ];
                
                // If thread belongs to a specific forum/category
                if (isset($model->forum_id)) {
                    $patterns[] = "forums.show.{$model->forum_id}";
                }
                if (isset($model->category_id)) {
                    $patterns[] = "categories.show.{$model->category_id}";
                }
                break;

            case 'Post':
                $patterns = [
                    'threads.show',
                    'posts.index',
                ];
                
                // If post belongs to a specific thread
                if (isset($model->thread_id)) {
                    $patterns[] = "threads.show.{$model->thread_id}";
                }
                break;

            case 'MarketplaceProduct':
                $patterns = [
                    'marketplace.products.index',
                    'marketplace.products.show',
                    'categories.index',
                    'search.advanced',
                ];
                
                // If product belongs to a specific category
                if (isset($model->category_id)) {
                    $patterns[] = "categories.show.{$model->category_id}";
                }
                break;

            case 'MarketplaceOrder':
                $patterns = [
                    'marketplace.orders.index',
                    'marketplace.orders.show',
                    'users.orders',
                ];
                
                // User-specific cache
                if (isset($model->user_id)) {
                    $patterns[] = "users.show.{$model->user_id}";
                }
                if (isset($model->seller_id)) {
                    $patterns[] = "users.show.{$model->seller_id}";
                }
                break;

            case 'User':
                $patterns = [
                    'users.index',
                    'users.show',
                    'search.advanced',
                ];
                break;

            case 'Category':
                $patterns = [
                    'categories.index',
                    'categories.show',
                    'threads.index',
                    'marketplace.products.index',
                ];
                break;

            case 'Forum':
                $patterns = [
                    'forums.index',
                    'forums.show',
                    'threads.index',
                    'categories.index',
                ];
                break;

            case 'Notification':
                // User-specific notifications
                if (isset($model->user_id)) {
                    $patterns[] = "users.notifications.{$model->user_id}";
                }
                break;

            default:
                // Generic patterns for unknown models
                $patterns = [
                    strtolower($modelName) . '.index',
                    strtolower($modelName) . '.show',
                ];
        }

        return $patterns;
    }

    /**
     * Handle model-specific cache invalidation
     */
    private function invalidateModelSpecificCache(Model $model, string $action): void
    {
        $modelName = class_basename(get_class($model));

        switch ($modelName) {
            case 'Thread':
                $this->invalidateThreadCache($model, $action);
                break;

            case 'MarketplaceProduct':
                $this->invalidateProductCache($model, $action);
                break;

            case 'User':
                $this->invalidateUserCache($model, $action);
                break;

            case 'MarketplaceOrder':
                $this->invalidateOrderCache($model, $action);
                break;
        }
    }

    /**
     * Invalidate thread-specific cache
     */
    private function invalidateThreadCache(Model $thread, string $action): void
    {
        // Invalidate thread-specific caches
        Cache::forget("thread_views_{$thread->id}");
        Cache::forget("thread_posts_count_{$thread->id}");
        Cache::forget("thread_latest_post_{$thread->id}");

        // Invalidate forum statistics
        if (isset($thread->forum_id)) {
            Cache::forget("forum_stats_{$thread->forum_id}");
            Cache::forget("forum_latest_threads_{$thread->forum_id}");
        }

        // Invalidate category statistics
        if (isset($thread->category_id)) {
            Cache::forget("category_stats_{$thread->category_id}");
            Cache::forget("category_thread_count_{$thread->category_id}");
        }

        // Invalidate user statistics
        if (isset($thread->user_id)) {
            Cache::forget("user_thread_count_{$thread->user_id}");
            Cache::forget("user_latest_threads_{$thread->user_id}");
        }
    }

    /**
     * Invalidate product-specific cache
     */
    private function invalidateProductCache(Model $product, string $action): void
    {
        // Invalidate product-specific caches
        Cache::forget("product_views_{$product->id}");
        Cache::forget("product_reviews_{$product->id}");
        Cache::forget("product_related_{$product->id}");

        // Invalidate category statistics
        if (isset($product->category_id)) {
            Cache::forget("category_product_count_{$product->category_id}");
            Cache::forget("category_featured_products_{$product->category_id}");
        }

        // Invalidate user statistics
        if (isset($product->user_id)) {
            Cache::forget("user_product_count_{$product->user_id}");
            Cache::forget("seller_stats_{$product->user_id}");
        }

        // Invalidate marketplace statistics
        Cache::forget('marketplace_stats');
        Cache::forget('featured_products');
        Cache::forget('latest_products');
    }

    /**
     * Invalidate user-specific cache
     */
    private function invalidateUserCache(Model $user, string $action): void
    {
        // Invalidate user-specific caches
        Cache::forget("user_profile_{$user->id}");
        Cache::forget("user_stats_{$user->id}");
        Cache::forget("user_activities_{$user->id}");
        Cache::forget("user_notifications_count_{$user->id}");

        // Invalidate role-based caches
        if (isset($user->role)) {
            Cache::forget("users_by_role_{$user->role}");
        }

        // Invalidate global user statistics
        Cache::forget('total_users_count');
        Cache::forget('online_users_count');
        Cache::forget('user_registration_stats');
    }

    /**
     * Invalidate order-specific cache
     */
    private function invalidateOrderCache(Model $order, string $action): void
    {
        // Invalidate order-specific caches
        Cache::forget("order_details_{$order->id}");
        Cache::forget("order_items_{$order->id}");

        // Invalidate user order statistics
        if (isset($order->user_id)) {
            Cache::forget("user_orders_{$order->user_id}");
            Cache::forget("user_order_stats_{$order->user_id}");
        }

        // Invalidate seller statistics
        if (isset($order->seller_id)) {
            Cache::forget("seller_orders_{$order->seller_id}");
            Cache::forget("seller_revenue_{$order->seller_id}");
        }

        // Invalidate marketplace statistics
        Cache::forget('marketplace_order_stats');
        Cache::forget('daily_revenue');
        Cache::forget('monthly_revenue');
    }

    /**
     * Invalidate search-related cache
     */
    public static function invalidateSearchCache(): void
    {
        try {
            $patterns = [
                'search.advanced',
                'search.autocomplete',
                'search.suggestions',
            ];

            foreach ($patterns as $pattern) {
                DatabaseCacheMiddleware::invalidateCache($pattern);
            }

            // Clear search-specific caches
            Cache::forget('popular_searches');
            Cache::forget('trending_searches');
            Cache::forget('search_statistics');

            Log::info('Search cache invalidated');

        } catch (\Exception $e) {
            Log::error('Search cache invalidation failed', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Invalidate dashboard cache
     */
    public static function invalidateDashboardCache(): void
    {
        try {
            $patterns = [
                'admin.dashboard',
                'dashboard.metrics',
                'dashboard.stats',
            ];

            foreach ($patterns as $pattern) {
                DatabaseCacheMiddleware::invalidateCache($pattern);
            }

            // Clear dashboard-specific caches
            Cache::forget('dashboard_metrics');
            Cache::forget('dashboard_charts');
            Cache::forget('dashboard_recent_activities');

            Log::info('Dashboard cache invalidated');

        } catch (\Exception $e) {
            Log::error('Dashboard cache invalidation failed', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
