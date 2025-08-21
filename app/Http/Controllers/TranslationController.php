<?php

namespace App\Http\Controllers;

use App\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TranslationController extends Controller
{
    /**
     * Get translations for JavaScript
     * Endpoint: /api/translations/js
     */
    public function getJavaScriptTranslations(Request $request)
    {
        $locale = $request->get('locale', app()->getLocale());
        $groups = $request->get('groups', ['notifications']); // Default to notifications group
        
        if (is_string($groups)) {
            $groups = explode(',', $groups);
        }

        $cacheKey = "js_translations.{$locale}." . implode('_', $groups);

        $translations = Cache::remember($cacheKey, 3600, function () use ($locale, $groups) {
            $result = [];

            foreach ($groups as $group) {
                $groupTranslations = Translation::active()
                    ->forLocale($locale)
                    ->forGroup($group)
                    ->pluck('content', 'key')
                    ->toArray();

                // Build nested array structure
                foreach ($groupTranslations as $key => $content) {
                    $this->setNestedValue($result, $key, $content);
                }
            }

            return $result;
        });

        return response()->json([
            'success' => true,
            'locale' => $locale,
            'translations' => $translations
        ]);
    }

    /**
     * Get specific translation keys for JavaScript
     * Endpoint: /api/translations/keys
     */
    public function getSpecificKeys(Request $request)
    {
        $locale = $request->get('locale', app()->getLocale());
        $keys = $request->get('keys', []);
        
        if (is_string($keys)) {
            $keys = explode(',', $keys);
        }

        $cacheKey = "js_specific_translations.{$locale}." . md5(implode('|', $keys));

        $translations = Cache::remember($cacheKey, 3600, function () use ($locale, $keys) {
            $result = [];

            foreach ($keys as $key) {
                $translation = Translation::getTranslation($key, $locale);
                if ($translation !== null) {
                    $this->setNestedValue($result, $key, $translation);
                }
            }

            return $result;
        });

        return response()->json([
            'success' => true,
            'locale' => $locale,
            'translations' => $translations
        ]);
    }

    /**
     * Get notification-specific translations
     * Endpoint: /api/translations/notifications
     */
    public function getNotificationTranslations(Request $request)
    {
        $locale = $request->get('locale', app()->getLocale());
        
        $cacheKey = "notification_translations.{$locale}";

        $translations = Cache::remember($cacheKey, 3600, function () use ($locale) {
            // Get all notification-related translation keys
            $notificationKeys = [
                // Default notifications
                'notifications.default.title',
                'notifications.default.message',
                
                // UI elements
                'notifications.ui.header',
                'notifications.ui.mark_all_read',
                'notifications.ui.clear_all',
                'notifications.ui.loading',
                'notifications.ui.loading_notifications',
                'notifications.ui.no_notifications',
                'notifications.ui.view_all',
                'notifications.ui.new_badge',
                'notifications.ui.delete_notification',
                'notifications.ui.unread_notifications',
                
                // Actions and messages
                'notifications.actions.marked_all_read',
                'notifications.actions.notification_deleted',
                'notifications.actions.error_occurred',
                'notifications.actions.error_loading',
                'notifications.actions.error_deleting',
                
                // Types
                'notifications.types.comment',
                'notifications.types.reply',
                'notifications.types.mention',
                'notifications.types.follow',
                'notifications.types.like',
                'notifications.types.system',
                
                // Auth
                'notifications.auth.login_to_view',
                
                // Enhanced features
                'notifications.enhanced.sound_toggle',
                'notifications.enhanced.settings',
            ];

            $result = [];
            foreach ($notificationKeys as $key) {
                $translation = Translation::getTranslation($key, $locale);
                if ($translation !== null) {
                    $this->setNestedValue($result, $key, $translation);
                }
            }

            return $result;
        });

        return response()->json([
            'success' => true,
            'locale' => $locale,
            'translations' => $translations
        ]);
    }

    /**
     * Set nested array value using dot notation
     */
    private function setNestedValue(&$array, $key, $value)
    {
        $keys = explode('.', $key);
        $current = &$array;

        foreach ($keys as $k) {
            if (!isset($current[$k]) || !is_array($current[$k])) {
                $current[$k] = [];
            }
            $current = &$current[$k];
        }

        $current = $value;
    }

    /**
     * Clear translation cache
     * Endpoint: /api/translations/clear-cache
     */
    public function clearCache(Request $request)
    {
        // Clear all translation-related cache
        $patterns = [
            'js_translations.*',
            'js_specific_translations.*',
            'notification_translations.*',
            'translation.*',
            'translations.*'
        ];

        $cleared = 0;
        foreach ($patterns as $pattern) {
            $keys = Cache::getRedis()->keys($pattern);
            foreach ($keys as $key) {
                Cache::forget($key);
                $cleared++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Cleared {$cleared} translation cache entries",
            'cleared_count' => $cleared
        ]);
    }
}
