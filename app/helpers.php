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
     * Get time ago format
     *
     * @param \Carbon\Carbon $date
     * @return string
     */
    function timeAgo($date)
    {
        return $date->diffForHumans();
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
            return \App\Models\User::where('last_activity', '>=', now()->subMinutes(15))->count();
        });
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
        return get_setting('logo_url', '/images/logo.png');
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

if (!function_exists('t_core')) {
    /**
     * Get core translation
     *
     * @param string $key
     * @param array $replace
     * @param string|null $locale
     * @return string
     */
    function t_core($key, $replace = [], $locale = null)
    {
        return __("core/$key", $replace, $locale);
    }
}

if (!function_exists('t_ui')) {
    /**
     * Get UI translation
     *
     * @param string $key
     * @param array $replace
     * @param string|null $locale
     * @return string
     */
    function t_ui($key, $replace = [], $locale = null)
    {
        return __("ui/$key", $replace, $locale);
    }
}

if (!function_exists('t_content')) {
    /**
     * Get content translation
     *
     * @param string $key
     * @param array $replace
     * @param string|null $locale
     * @return string
     */
    function t_content($key, $replace = [], $locale = null)
    {
        return __("content/$key", $replace, $locale);
    }
}

if (!function_exists('t_feature')) {
    /**
     * Get feature translation
     *
     * @param string $key
     * @param array $replace
     * @param string|null $locale
     * @return string
     */
    function t_feature($key, $replace = [], $locale = null)
    {
        return __("features/$key", $replace, $locale);
    }
}

if (!function_exists('t_user')) {
    /**
     * Get user translation
     *
     * @param string $key
     * @param array $replace
     * @param string|null $locale
     * @return string
     */
    function t_user($key, $replace = [], $locale = null)
    {
        return __("user/$key", $replace, $locale);
    }
}
