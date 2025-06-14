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
