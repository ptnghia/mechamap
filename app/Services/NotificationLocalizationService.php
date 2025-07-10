<?php

namespace App\Services;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;

class NotificationLocalizationService
{
    /**
     * Supported languages for notifications
     */
    const SUPPORTED_LANGUAGES = [
        'vi' => 'Tiếng Việt',
        'en' => 'English',
        'zh' => '中文',
        'ja' => '日本語',
        'ko' => '한국어',
    ];

    /**
     * Default language
     */
    const DEFAULT_LANGUAGE = 'vi';

    /**
     * Get localized notification content
     */
    public static function getLocalizedNotification(array $notificationData, string $locale = null): array
    {
        $locale = $locale ?? self::getUserLocale();

        $originalLocale = App::getLocale();
        App::setLocale($locale);

        try {
            $localizedData = [
                'title' => self::localizeTitle($notificationData['type'], $notificationData['data'] ?? []),
                'message' => self::localizeMessage($notificationData['type'], $notificationData['data'] ?? []),
                'action_text' => self::localizeActionText($notificationData['type'], $notificationData['data'] ?? []),
                'locale' => $locale,
            ];

            return array_merge($notificationData, $localizedData);
        } finally {
            App::setLocale($originalLocale);
        }
    }

    /**
     * Get user's preferred locale
     */
    public static function getUserLocale($userId = null): string
    {
        if ($userId) {
            $user = \App\Models\User::find($userId);
            if ($user && $user->locale && in_array($user->locale, array_keys(self::SUPPORTED_LANGUAGES))) {
                return $user->locale;
            }
        }

        if (auth()->check() && auth()->user()->locale) {
            return auth()->user()->locale;
        }

        return App::getLocale() ?? self::DEFAULT_LANGUAGE;
    }

    /**
     * Localize notification title
     */
    private static function localizeTitle(string $type, array $data): string
    {
        // Direct translation with fallback
        $translationKey = "notifications.{$type}.title";
        $translated = trans($translationKey, $data);

        // If translation exists and is different from key, return it
        if ($translated !== $translationKey) {
            return $translated;
        }

        // Fallback to default
        return trans('notifications.default.title', $data);
    }

    /**
     * Localize notification message
     */
    private static function localizeMessage(string $type, array $data): string
    {
        // Direct translation with fallback
        $translationKey = "notifications.{$type}.message";
        $translated = trans($translationKey, $data);

        // If translation exists and is different from key, return it
        if ($translated !== $translationKey) {
            return $translated;
        }

        // Fallback to default
        return trans('notifications.default.message', $data);
    }

    /**
     * Localize action text
     */
    private static function localizeActionText(string $type, array $data): string
    {
        // Map notification types to action keys
        $actionMap = [
            'thread_created' => 'view_thread',
            'thread_replied' => 'view_thread',
            'comment_mentioned' => 'view_comment',
            'user_followed' => 'view_profile',
            'achievement_unlocked' => 'view_achievement',
            'product_out_of_stock' => 'view_product',
            'price_drop_alert' => 'view_product',
            'order_status_updated' => 'view_order',
            'review_received' => 'view_review',
            'seller_message' => 'view_message',
            'login_from_new_device' => 'view_devices',
            'password_changed' => 'view_security',
            'weekly_digest' => 'view_digest',
        ];

        $actionKey = $actionMap[$type] ?? 'view_details';
        return trans("notifications.actions.{$actionKey}");
    }

    /**
     * Get supported languages
     */
    public static function getSupportedLanguages(): array
    {
        return self::SUPPORTED_LANGUAGES;
    }

    /**
     * Check if language is supported
     */
    public static function isLanguageSupported(string $locale): bool
    {
        return array_key_exists($locale, self::SUPPORTED_LANGUAGES);
    }

    /**
     * Get language name
     */
    public static function getLanguageName(string $locale): string
    {
        return self::SUPPORTED_LANGUAGES[$locale] ?? $locale;
    }

    /**
     * Localize notification for multiple users
     */
    public static function localizeForUsers(array $notificationData, array $userIds): array
    {
        $localizedNotifications = [];

        // Group users by locale to minimize locale switching
        $usersByLocale = [];
        foreach ($userIds as $userId) {
            $locale = self::getUserLocale($userId);
            $usersByLocale[$locale][] = $userId;
        }

        // Localize for each locale group
        foreach ($usersByLocale as $locale => $users) {
            $localizedData = self::getLocalizedNotification($notificationData, $locale);

            foreach ($users as $userId) {
                $localizedNotifications[$userId] = array_merge($localizedData, [
                    'user_id' => $userId,
                ]);
            }
        }

        return $localizedNotifications;
    }

    /**
     * Get notification template path for locale
     */
    public static function getTemplatePath(string $type, string $locale = null): string
    {
        $locale = $locale ?? self::getUserLocale();

        // Check if localized template exists
        $localizedPath = "notifications.{$locale}.{$type}";
        if (view()->exists($localizedPath)) {
            return $localizedPath;
        }

        // Fallback to default locale
        $defaultPath = "notifications." . self::DEFAULT_LANGUAGE . ".{$type}";
        if (view()->exists($defaultPath)) {
            return $defaultPath;
        }

        // Final fallback to generic template
        return "notifications.{$type}";
    }

    /**
     * Clear localization cache
     */
    public static function clearCache(string $type = null): void
    {
        if ($type) {
            foreach (array_keys(self::SUPPORTED_LANGUAGES) as $locale) {
                Cache::forget("notification_localized_{$type}_{$locale}");
            }
        } else {
            Cache::flush(); // Clear all cache - use with caution
        }
    }

    /**
     * Get time format for locale
     */
    public static function getTimeFormat(string $locale = null): string
    {
        $locale = $locale ?? self::getUserLocale();

        return match ($locale) {
            'en' => 'M j, Y g:i A',
            'zh', 'ja', 'ko' => 'Y年m月d日 H:i',
            'vi' => 'd/m/Y H:i',
            default => 'd/m/Y H:i',
        };
    }

    /**
     * Format date for notification
     */
    public static function formatDate(\DateTime $date, string $locale = null): string
    {
        $locale = $locale ?? self::getUserLocale();
        $format = self::getTimeFormat($locale);

        return $date->format($format);
    }

    /**
     * Get RTL languages
     */
    public static function isRTL(string $locale = null): bool
    {
        $locale = $locale ?? self::getUserLocale();

        // Add RTL languages as needed
        $rtlLanguages = ['ar', 'he', 'fa', 'ur'];

        return in_array($locale, $rtlLanguages);
    }

    /**
     * Get notification statistics by language
     */
    public static function getLanguageStatistics(): array
    {
        return Cache::remember('notification_language_stats', 3600, function () {
            $stats = [];

            foreach (array_keys(self::SUPPORTED_LANGUAGES) as $locale) {
                $stats[$locale] = [
                    'users_count' => \App\Models\User::where('locale', $locale)->count(),
                    'notifications_sent' => \App\Models\Notification::whereJsonContains('data->locale', $locale)->count(),
                    'language_name' => self::getLanguageName($locale),
                ];
            }

            return $stats;
        });
    }
}
