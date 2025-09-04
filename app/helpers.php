<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

if (!function_exists('highlightSearchQuery')) {
    /**
     * Highlight search query in text
     *
     * @param string $text
     * @param string $query
     * @return string
     */
    function highlightSearchQuery($text, $query)
    {
        if (empty($query)) {
            return $text;
        }

        $words = explode(' ', $query);

        foreach ($words as $word) {
            if (strlen($word) >= 2) {
                $text = preg_replace(
                    '/(' . preg_quote($word, '/') . ')/i',
                    '<span class="highlight">$1</span>',
                    $text
                );
            }
        }

        return $text;
    }
}

if (!function_exists('breadcrumb')) {
    /**
     * Generate breadcrumb for current request
     *
     * @return array
     */
    function breadcrumb()
    {
        $breadcrumbService = app(\App\Services\BreadcrumbService::class);
        return $breadcrumbService->generate(request());
    }
}

if (!function_exists('formatNumber')) {
    /**
     * Format number for display
     *
     * @param int $number
     * @return string
     */
    function formatNumber($number)
    {
        if ($number >= 1000000) {
            return round($number / 1000000, 1) . 'M';
        } elseif ($number >= 1000) {
            return round($number / 1000, 1) . 'K';
        }

        return number_format($number);
    }
}

if (!function_exists('timeAgo')) {
    /**
     * Get time ago string
     *
     * @param mixed $time
     * @return string
     */
    function timeAgo($time)
    {
        return \Carbon\Carbon::parse($time)->diffForHumans();
    }
}

if (!function_exists('stripHtmlTags')) {
    /**
     * Strip HTML tags and return clean text
     *
     * @param string $text
     * @param int $limit
     * @return string
     */
    function stripHtmlTags($text, $limit = null)
    {
        $cleanText = strip_tags($text);

        if ($limit) {
            return Str::limit($cleanText, $limit);
        }

        return $cleanText;
    }
}

if (!function_exists('getForumIcon')) {
    /**
     * Get forum icon based on forum type or name
     *
     * @param string $forumName
     * @return string
     */
    function getForumIcon($forumName)
    {
        $icons = [
            'design' => 'fas fa-drafting-compass',
            'manufacturing' => 'fas fa-industry',
            'materials' => 'fas fa-flask',
            'automation' => 'fas fa-robot',
            'cad' => 'fas fa-cube',
            'cnc' => 'fas fa-cogs',
            'mechanical' => 'fas fa-wrench',
            'engineering' => 'fas fa-hard-hat',
            'default' => 'fas fa-comments'
        ];

        $lowerName = strtolower($forumName);

        foreach ($icons as $keyword => $icon) {
            if (str_contains($lowerName, $keyword)) {
                return $icon;
            }
        }

        return $icons['default'];
    }
}

if (!function_exists('getUserRoleBadge')) {
    /**
     * Get user role badge HTML
     *
     * @param \App\Models\User $user
     * @return string
     */
    function getUserRoleBadge($user)
    {
        if (!$user) {
            return '';
        }

        $badges = [];

        if ($user->hasRole('admin')) {
            $badges[] = '<span class="badge bg-danger">Admin</span>';
        } elseif ($user->hasRole('moderator')) {
            $badges[] = '<span class="badge bg-warning">Moderator</span>';
        } elseif ($user->hasRole('expert')) {
            $badges[] = '<span class="badge bg-success">Expert</span>';
        }

        if ($user->verified_expert) {
            $badges[] = '<span class="badge bg-primary">Verified Expert</span>';
        }

        return implode(' ', $badges);
    }
}

if (!function_exists('getOnlineUsersCount')) {
    /**
     * Get count of online users (users active in last 15 minutes)
     *
     * @return int
     */
    function getOnlineUsersCount()
    {
        return Cache::remember('online_users_count', 300, function () {
            return \App\Models\User::where('last_seen_at', '>=', now()->subMinutes(15))->count();
        });
    }
}

if (!function_exists('formatActivityType')) {
    /**
     * Format activity type with translation
     *
     * @param string $activityType
     * @return string
     */
    function formatActivityType($activityType)
    {
        return __('activity.' . $activityType, [], app()->getLocale()) ?: $activityType;
    }
}

if (!function_exists('getForumStats')) {
    /**
     * Get forum statistics
     *
     * @return array
     */
    function getForumStats()
    {
        return Cache::remember('forum_stats', 1800, function () {
            return [
                'total_users' => \App\Models\User::count(),
                'total_forums' => \App\Models\Forum::count(),
                'total_threads' => \App\Models\Thread::count(),
                'total_posts' => \App\Models\Thread::sum('cached_comments_count') + \App\Models\Thread::count(), // Opening posts + replies
                'online_users' => getOnlineUsersCount(),
                'newest_user' => \App\Models\User::latest()->first(),
            ];
        });
    }
}

if (!function_exists('get_setting')) {
    /**
     * Get application setting
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function get_setting($key, $default = null)
    {
        // For now, return default values for common settings
        $settings = [
            'show_banner' => true,
            'site_name' => 'MechaMap',
            'site_description' => 'Cộng đồng kỹ sư cơ khí Việt Nam',
            'logo_url' => '/images/logo.png',
            'favicon_url' => '/favicon.ico',
            'banner_url' => '/images/banner.jpg',
            'maintenance_mode' => false,
            'registration_enabled' => true,
            'email_verification_required' => false,
        ];

        return $settings[$key] ?? $default;
    }
}

if (!function_exists('get_site_name')) {
    /**
     * Get site name
     *
     * @return string
     */
    function get_site_name()
    {
        return get_setting('site_name', config('app.name', 'MechaMap'));
    }
}

if (!function_exists('get_logo_url')) {
    /**
     * Get logo URL
     *
     * @return string
     */
    function get_logo_url()
    {
        return get_setting('logo_url', '/images/logo.svg');
    }
}

if (!function_exists('get_favicon_url')) {
    /**
     * Get favicon URL
     *
     * @return string
     */
    function get_favicon_url()
    {
        return get_setting('favicon_url', '/favicon.ico');
    }
}

if (!function_exists('get_banner_url')) {
    /**
     * Get banner URL
     *
     * @return string
     */
    function get_banner_url()
    {
        return get_setting('banner_url', '/images/banner.jpg');
    }
}

if (!function_exists('get_copyright_info')) {
    /**
     * Get copyright information
     *
     * @return array
     */
    function get_copyright_info()
    {
        return [
            'text' => t_footer('copyright.all_rights_reserved'),
            'year' => date('Y'),
            'company' => get_site_name(),
        ];
    }
}

if (!function_exists('get_social_links')) {
    /**
     * Get social media links
     *
     * @return array
     */
    function get_social_links()
    {
        return [
            'facebook' => get_setting('social.facebook', ''),
            'twitter' => get_setting('social.twitter', ''),
            'instagram' => get_setting('social.instagram', ''),
            'linkedin' => get_setting('social.linkedin', ''),
            'youtube' => get_setting('social.youtube', ''),
            'github' => get_setting('social.github', ''),
            'discord' => get_setting('social.discord', ''),
        ];
    }
}

if (!function_exists('asset_versioned')) {
    /**
     * Generate versioned asset URL for cache busting
     * Sử dụng file modification time để tạo version
     *
     * @param string $path
     * @return string
     */
    function asset_versioned($path)
    {
        // Kiểm tra nếu versioning bị tắt
        if (!config('assets.versioning_enabled', true)) {
            return asset($path);
        }

        // Trong development mode, có thể force reload
        if (config('assets.development.force_reload', false)) {
            return asset($path) . '?v=' . time();
        }

        $fullPath = public_path($path);

        // Nếu file không tồn tại, trả về asset bình thường
        if (!file_exists($fullPath)) {
            return asset($path);
        }

        // Kiểm tra nếu path bị loại trừ
        $excludedPaths = config('assets.excluded_paths', []);
        foreach ($excludedPaths as $excludedPath) {
            if (str_starts_with($path, $excludedPath)) {
                return asset($path);
            }
        }

        // Tạo version dựa trên method được cấu hình
        $method = config('assets.versioning_method', 'filemtime');
        $version = match($method) {
            'filemtime' => filemtime($fullPath),
            'manual' => config('assets.manual_version', '1.0.0'),
            'git' => substr(exec('git rev-parse HEAD'), 0, 8),
            default => filemtime($fullPath)
        };

        return asset($path) . '?v=' . $version;
    }
}

if (!function_exists('css_versioned')) {
    /**
     * Generate versioned CSS link tag
     *
     * @param string $path
     * @return string
     */
    function css_versioned($path)
    {
        return '<link rel="stylesheet" href="' . asset_versioned($path) . '">';
    }
}

if (!function_exists('js_versioned')) {
    /**
     * Generate versioned JS script tag
     *
     * @param string $path
     * @return string
     */
    function js_versioned($path)
    {
        return '<script src="' . asset_versioned($path) . '"></script>';
    }
}

// ============================================================================
// LOCALIZATION HELPER FUNCTIONS
// ============================================================================

// ===================================================================
// NEW TRANSLATION HELPER FUNCTIONS - 2-LEVEL STRUCTURE
// Format: {file}.{section}.{key}
// Created: 2025-07-21
// ===================================================================

if (!function_exists('t_auth')) {
    /**
     * Get authentication translation
     * @param string $key Format: section.key (e.g., 'login.title')
     */
    function t_auth($key, $replace = [], $locale = null)
    {
        return __("auth.$key", $replace, $locale);
    }
}

if (!function_exists('t_common')) {
    /**
     * Get common UI translation
     * @param string $key Format: section.key (e.g., 'buttons.save')
     */
    function t_common($key, $replace = [], $locale = null)
    {
        return __("common.$key", $replace, $locale);
    }
}

if (!function_exists('t_navigation')) {
    /**
     * Get navigation translation
     * @param string $key Format: section.key (e.g., 'menu.home')
     */
    function t_navigation($key, $replace = [], $locale = null)
    {
        return __("navigation.$key", $replace, $locale);
    }
}

if (!function_exists('t_validation')) {
    /**
     * Get validation translation
     * @param string $key Format: section.key (e.g., 'rules.required')
     */
    function t_validation($key, $replace = [], $locale = null)
    {
        return __("validation.$key", $replace, $locale);
    }
}

if (!function_exists('t_forums')) {
    /**
     * Get forums translation
     * @param string $key Format: section.key (e.g., 'threads.create')
     */
    function t_forums($key, $replace = [], $locale = null)
    {
        return __("forums.$key", $replace, $locale);
    }
}

if (!function_exists('t_showcase')) {
    /**
     * Get showcase translation
     * @param string $key Format: section.key (e.g., 'projects.title')
     */
    function t_showcase($key, $replace = [], $locale = null)
    {
        return __("showcase.$key", $replace, $locale);
    }
}

if (!function_exists('t_marketplace')) {
    /**
     * Get marketplace translation
     * @param string $key Format: section.key (e.g., 'products.add_to_cart')
     */
    function t_marketplace($key, $replace = [], $locale = null)
    {
        return __("marketplace.$key", $replace, $locale);
    }
}

if (!function_exists('t_user')) {
    /**
     * Get user translation
     * @param string $key Format: section.key (e.g., 'profile.edit')
     */
    function t_user($key, $replace = [], $locale = null)
    {
        return __("user.$key", $replace, $locale);
    }
}

if (!function_exists('t_homepage')) {
    /**
     * Get homepage translation
     * @param string $key Format: section.key (e.g., 'hero.title')
     */
    function t_homepage($key, $replace = [], $locale = null)
    {
        return __("homepage.$key", $replace, $locale);
    }
}

if (!function_exists('t_pages')) {
    /**
     * Get pages translation
     * @param string $key Format: section.key (e.g., 'about.title')
     */
    function t_pages($key, $replace = [], $locale = null)
    {
        return __("pages.$key", $replace, $locale);
    }
}

if (!function_exists('t_search')) {
    /**
     * Get search translation
     * @param string $key Format: section.key (e.g., 'filters.category')
     */
    function t_search($key, $replace = [], $locale = null)
    {
        return __("search.$key", $replace, $locale);
    }
}

if (!function_exists('t_admin')) {
    /**
     * Get admin translation
     * @param string $key Format: section.key (e.g., 'dashboard.title')
     */
    function t_admin($key, $replace = [], $locale = null)
    {
        return __("admin.$key", $replace, $locale);
    }
}

if (!function_exists('t_moderation')) {
    /**
     * Get moderation translation
     * @param string $key Format: section.key (e.g., 'reports.pending')
     */
    function t_moderation($key, $replace = [], $locale = null)
    {
        return __("moderation.$key", $replace, $locale);
    }
}

if (!function_exists('t_notifications')) {
    /**
     * Get notifications translation
     * @param string $key Format: section.key (e.g., 'types.comment')
     */
    function t_notifications($key, $replace = [], $locale = null)
    {
        return __("notifications.$key", $replace, $locale);
    }
}

if (!function_exists('t_seo')) {
    /**
     * Get SEO translation
     * @param string $key Format: section.key (e.g., 'meta.title')
     */
    function t_seo($key, $replace = [], $locale = null)
    {
        return __("seo.$key", $replace, $locale);
    }
}

if (!function_exists('t_emails')) {
    /**
     * Get emails translation
     * @param string $key Format: section.key (e.g., 'welcome.subject')
     */
    function t_emails($key, $replace = [], $locale = null)
    {
        return __("emails.$key", $replace, $locale);
    }
}

if (!function_exists('get_notification_category_color')) {
    /**
     * Get notification category color
     * @param string $category
     * @return string
     */
    function get_notification_category_color(string $category): string
    {
        return match($category) {
            'system' => 'blue',
            'forum' => 'green',
            'marketplace' => 'orange',
            'social' => 'purple',
            'security' => 'red',
            default => 'gray'
        };
    }
}

if (!function_exists('t_errors')) {
    /**
     * Get errors translation
     * @param string $key Format: section.key (e.g., 'http.404')
     */
    function t_errors($key, $replace = [], $locale = null)
    {
        return __("errors.$key", $replace, $locale);
    }
}

if (!function_exists('t_ui')) {
    /**
     * Get UI translation
     * @param string $key Format: section.key (e.g., 'buttons.save')
     */
    function t_ui($key, $replace = [], $locale = null)
    {
        return __("ui.$key", $replace, $locale);
    }
}

if (!function_exists('t_core')) {
    /**
     * Get core translation
     * @param string $key Format: section.key (e.g., 'messages.success')
     */
    function t_core($key, $replace = [], $locale = null)
    {
        return __("core.$key", $replace, $locale);
    }
}

if (!function_exists('t_content')) {
    /**
     * Get content translation
     * @param string $key Format: section.key (e.g., 'home.hero_title')
     */
    function t_content($key, $replace = [], $locale = null)
    {
        return __("content.$key", $replace, $locale);
    }
}

if (!function_exists('t_feature')) {
    /**
     * Get feature translation
     * @param string $key Format: section.key (e.g., 'chat.send_message')
     */
    function t_feature($key, $replace = [], $locale = null)
    {
        return __("features.$key", $replace, $locale);
    }
}

if (!function_exists('t_sidebar')) {
    /**
     * Get sidebar translation
     * @param string $key Format: section.key (e.g., 'main.featured_topics')
     */
    function t_sidebar($key, $replace = [], $locale = null)
    {
        return __("sidebar.$key", $replace, $locale);
    }
}

if (!function_exists('t_footer')) {
    /**
     * Get footer translation
     * @param string $key Format: section.key (e.g., 'copyright.all_rights_reserved')
     */
    function t_footer($key, $replace = [], $locale = null)
    {
        return __("footer.$key", $replace, $locale);
    }
}

if (!function_exists('t_notification')) {
    /**
     * Get notification translation
     * @param string $key Format: section.key (e.g., 'ui.header', 'types.new_message', 'messages.new_message')
     */
    function t_notification($key, $replace = [], $locale = null)
    {
        return __("notifications.$key", $replace, $locale);
    }
}

if (!function_exists('t_nav')) {
    /**
     * Get navigation translation
     * @param string $key Format: section.key (e.g., 'user.profile')
     */
    function t_nav($key, $replace = [], $locale = null)
    {
        return __("nav.$key", $replace, $locale);
    }
}

if (!function_exists('t_setting')) {
    /**
     * Get setting translation
     * @param string $key Format: setting key (e.g., 'show_banner')
     */
    function t_setting($key, $replace = [], $locale = null)
    {
        return __("setting.$key", $replace, $locale);
    }
}

if (!function_exists('t_badges')) {
    /**
     * Get badges translation
     * @param string $key Format: badge key (e.g., 'complete')
     */
    function t_badges($key, $replace = [], $locale = null)
    {
        return __("badges.$key", $replace, $locale);
    }
}

if (!function_exists('t_forms')) {
    /**
     * Get forms translation
     * @param string $key Format: section.key (e.g., 'upload.attach_files')
     */
    function t_forms($key, $replace = [], $locale = null)
    {
        return __("forms.$key", $replace, $locale);
    }
}

if (!function_exists('t_versioned')) {
    /**
     * Get versioned assets translation
     * @param string $key Format: path key (e.g., 'css/frontend/components/file-upload.css')
     */
    function t_versioned($key, $replace = [], $locale = null)
    {
        return __("versioned.$key", $replace, $locale);
    }
}

if (!function_exists('t_language')) {
    /**
     * Get language translation
     * @param string $key Format: language key (e.g., 'vietnamese')
     */
    function t_language($key, $replace = [], $locale = null)
    {
        return __("language.$key", $replace, $locale);
    }
}

if (!function_exists('t_thread')) {
    /**
     * Get thread translation
     * @param string $key Format: thread key (e.g., 'following')
     */
    function t_thread($key, $replace = [], $locale = null)
    {
        return __("thread.$key", $replace, $locale);
    }
}

if (!function_exists('t_forum')) {
    /**
     * Get forum translation (singular)
     * @param string $key Format: forum key (e.g., 'search.advanced_search')
     */
    function t_forum($key, $replace = [], $locale = null)
    {
        return __("forum.$key", $replace, $locale);
    }
}

if (!function_exists('trans_e')) {
    /**
     * Escape translated string for safe HTML output (alias: trans_e)
     * Example: {!! trans_e('notifications.ui.header') !!}
     */
    function trans_e($key, $replace = [], $locale = null)
    {
        return e(__($key, $replace, $locale));
    }
}

if (!function_exists('trans_raw')) {
    /**
     * Return raw translation without escaping (explicit)
     */
    function trans_raw($key, $replace = [], $locale = null)
    {
        return __($key, $replace, $locale);
    }
}
