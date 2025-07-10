<?php

namespace App\Services;

use App\Models\User;
use App\Models\NotificationPreference;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

class NotificationTargetingService
{
    /**
     * Target users based on basic rules
     */
    public static function getTargetUsers(array $criteria): Collection
    {
        $cacheKey = 'notification_targeting_' . md5(serialize($criteria));

        return Cache::remember($cacheKey, 300, function () use ($criteria) {
            $query = User::query();

            // Apply basic targeting rules
            self::applyRoleFilter($query, $criteria);
            self::applyInterestFilter($query, $criteria);
            self::applyActivityFilter($query, $criteria);
            self::applyLocationFilter($query, $criteria);
            self::applyPreferenceFilter($query, $criteria);
            self::applyEngagementFilter($query, $criteria);

            return $query->get();
        });
    }

    /**
     * Apply role-based filtering
     */
    private static function applyRoleFilter($query, array $criteria): void
    {
        if (isset($criteria['roles']) && !empty($criteria['roles'])) {
            $query->whereIn('role', $criteria['roles']);
        }

        if (isset($criteria['exclude_roles']) && !empty($criteria['exclude_roles'])) {
            $query->whereNotIn('role', $criteria['exclude_roles']);
        }

        // Business account filtering
        if (isset($criteria['business_verified'])) {
            $query->where('business_verified', $criteria['business_verified']);
        }

        // Premium users
        if (isset($criteria['is_premium'])) {
            $query->where('is_premium', $criteria['is_premium']);
        }
    }

    /**
     * Apply interest-based filtering
     */
    private static function applyInterestFilter($query, array $criteria): void
    {
        if (isset($criteria['interests']) && !empty($criteria['interests'])) {
            $query->whereHas('interests', function ($q) use ($criteria) {
                $q->whereIn('name', $criteria['interests']);
            });
        }

        // Forum categories user follows
        if (isset($criteria['followed_categories']) && !empty($criteria['followed_categories'])) {
            $query->whereHas('followedCategories', function ($q) use ($criteria) {
                $q->whereIn('category_id', $criteria['followed_categories']);
            });
        }

        // Product categories user is interested in
        if (isset($criteria['product_interests']) && !empty($criteria['product_interests'])) {
            $query->whereHas('productInterests', function ($q) use ($criteria) {
                $q->whereIn('category_id', $criteria['product_interests']);
            });
        }
    }

    /**
     * Apply activity-based filtering
     */
    private static function applyActivityFilter($query, array $criteria): void
    {
        // Active users (logged in within X days)
        if (isset($criteria['active_within_days'])) {
            $date = now()->subDays($criteria['active_within_days']);
            $query->where('last_login_at', '>=', $date);
        }

        // Users who posted recently
        if (isset($criteria['posted_within_days'])) {
            $date = now()->subDays($criteria['posted_within_days']);
            $query->whereHas('threads', function ($q) use ($date) {
                $q->where('created_at', '>=', $date);
            })->orWhereHas('comments', function ($q) use ($date) {
                $q->where('created_at', '>=', $date);
            });
        }

        // Users with minimum post count
        if (isset($criteria['min_post_count'])) {
            $query->where('posts_count', '>=', $criteria['min_post_count']);
        }

        // Users with minimum reputation
        if (isset($criteria['min_reputation'])) {
            $query->where('reputation', '>=', $criteria['min_reputation']);
        }
    }

    /**
     * Apply location-based filtering
     */
    private static function applyLocationFilter($query, array $criteria): void
    {
        if (isset($criteria['countries']) && !empty($criteria['countries'])) {
            $query->whereIn('country', $criteria['countries']);
        }

        if (isset($criteria['cities']) && !empty($criteria['cities'])) {
            $query->whereIn('city', $criteria['cities']);
        }

        if (isset($criteria['timezones']) && !empty($criteria['timezones'])) {
            $query->whereIn('timezone', $criteria['timezones']);
        }
    }

    /**
     * Apply notification preference filtering
     */
    private static function applyPreferenceFilter($query, array $criteria): void
    {
        // Only users who have email notifications enabled
        if (isset($criteria['email_notifications_enabled']) && $criteria['email_notifications_enabled']) {
            $query->where('email_notifications_enabled', true);
        }

        // Only users who have browser notifications enabled
        if (isset($criteria['browser_notifications_enabled']) && $criteria['browser_notifications_enabled']) {
            $query->where('browser_notifications_enabled', true);
        }

        // Filter by specific notification type preferences
        if (isset($criteria['notification_type'])) {
            $query->whereHas('notificationPreferences', function ($q) use ($criteria) {
                $q->where('type', $criteria['notification_type'])
                  ->where('enabled', true);
            });
        }

        // Exclude users who opted out of marketing
        if (isset($criteria['exclude_marketing_optout']) && $criteria['exclude_marketing_optout']) {
            $query->where('marketing_emails_enabled', true);
        }
    }

    /**
     * Apply engagement-based filtering
     */
    private static function applyEngagementFilter($query, array $criteria): void
    {
        // High engagement users (based on recent activity)
        if (isset($criteria['high_engagement']) && $criteria['high_engagement']) {
            $query->where(function ($q) {
                // Use existing columns or fallback to basic activity
                if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'threads_count')) {
                    $q->where('threads_count', '>=', 5)
                      ->orWhere('comments_count', '>=', 20)
                      ->orWhere('likes_received', '>=', 50);
                } else {
                    // Fallback to posts_count if available
                    $q->where('posts_count', '>=', 10)
                      ->orWhere('reputation', '>=', 100);
                }
            });
        }

        // Users who haven't been active recently (re-engagement)
        if (isset($criteria['inactive_days'])) {
            $date = now()->subDays($criteria['inactive_days']);
            $query->where('last_login_at', '<=', $date)
                  ->where('last_login_at', '>=', now()->subDays(90)); // Not completely dormant
        }

        // Users with specific achievement levels
        if (isset($criteria['achievement_levels']) && !empty($criteria['achievement_levels'])) {
            $query->whereHas('achievements', function ($q) use ($criteria) {
                $q->whereIn('level', $criteria['achievement_levels']);
            });
        }
    }

    /**
     * Get users for forum notifications
     */
    public static function getForumNotificationUsers(int $forumId, string $notificationType): Collection
    {
        return self::getTargetUsers([
            'notification_type' => $notificationType,
            'followed_categories' => [\App\Models\Forum::find($forumId)?->category_id],
            'active_within_days' => 30,
            'email_notifications_enabled' => true,
        ]);
    }

    /**
     * Get users for marketplace notifications
     */
    public static function getMarketplaceNotificationUsers(array $productCategories, string $notificationType): Collection
    {
        return self::getTargetUsers([
            'notification_type' => $notificationType,
            'product_interests' => $productCategories,
            'roles' => ['member', 'supplier', 'manufacturer'],
            'active_within_days' => 14,
        ]);
    }

    /**
     * Get users for system announcements
     */
    public static function getSystemAnnouncementUsers(string $priority = 'normal'): Collection
    {
        $criteria = [
            'notification_type' => 'system_announcement',
            'email_notifications_enabled' => true,
        ];

        // For high priority announcements, target all active users
        if ($priority === 'high') {
            $criteria['active_within_days'] = 60;
        } else {
            $criteria['active_within_days'] = 30;
        }

        return self::getTargetUsers($criteria);
    }

    /**
     * Get users for re-engagement campaigns
     */
    public static function getReEngagementUsers(): Collection
    {
        return self::getTargetUsers([
            'inactive_days' => 14,
            'min_post_count' => 1, // Users who have posted at least once
            'marketing_emails_enabled' => true,
            'exclude_marketing_optout' => true,
        ]);
    }

    /**
     * Get high-value users for special notifications
     */
    public static function getHighValueUsers(): Collection
    {
        return self::getTargetUsers([
            'high_engagement' => true,
            'min_reputation' => 100,
            'active_within_days' => 7,
            'roles' => ['member', 'supplier', 'manufacturer', 'brand'],
        ]);
    }

    /**
     * Get users by time zone for optimal delivery timing
     */
    public static function getUsersByTimeZone(string $timezone): Collection
    {
        return self::getTargetUsers([
            'timezones' => [$timezone],
            'active_within_days' => 30,
        ]);
    }

    /**
     * Get notification statistics for targeting
     */
    public static function getTargetingStatistics(array $criteria): array
    {
        $users = self::getTargetUsers($criteria);

        return [
            'total_users' => $users->count(),
            'by_role' => $users->groupBy('role')->map->count(),
            'by_country' => $users->groupBy('country')->map->count(),
            'by_locale' => $users->groupBy('locale')->map->count(),
            'active_users' => $users->where('last_login_at', '>=', now()->subDays(7))->count(),
            'email_enabled' => $users->where('email_notifications_enabled', true)->count(),
            'browser_enabled' => $users->where('browser_notifications_enabled', true)->count(),
        ];
    }

    /**
     * Validate targeting criteria
     */
    public static function validateCriteria(array $criteria): array
    {
        $errors = [];

        // Check for valid roles
        if (isset($criteria['roles'])) {
            $validRoles = ['guest', 'member', 'supplier', 'manufacturer', 'brand', 'moderator', 'admin'];
            $invalidRoles = array_diff($criteria['roles'], $validRoles);
            if (!empty($invalidRoles)) {
                $errors[] = 'Invalid roles: ' . implode(', ', $invalidRoles);
            }
        }

        // Check for reasonable date ranges
        if (isset($criteria['active_within_days']) && $criteria['active_within_days'] > 365) {
            $errors[] = 'Active within days should not exceed 365';
        }

        // Check for minimum counts
        if (isset($criteria['min_post_count']) && $criteria['min_post_count'] < 0) {
            $errors[] = 'Minimum post count cannot be negative';
        }

        return $errors;
    }

    /**
     * Clear targeting cache
     */
    public static function clearCache(): void
    {
        Cache::tags(['notification_targeting'])->flush();
    }
}
