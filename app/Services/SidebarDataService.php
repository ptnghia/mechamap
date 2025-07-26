<?php

namespace App\Services;

use App\Models\Thread;
use App\Models\User;
use App\Models\Forum;
use App\Models\Category;
use App\Models\Comment;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SidebarDataService
{
    /**
     * Lấy tất cả dữ liệu sidebar với caching tối ưu
     */
    public function getSidebarData(User $user = null): array
    {
        return Cache::remember('sidebar_data_' . ($user?->id ?? 'guest'), 300, function () use ($user) {
            return [
                'site_settings' => $this->getSiteSettings(),
                'community_stats' => $this->getCommunityStats(),
                'featured_threads' => $this->getFeaturedThreads(),
                'top_forums' => $this->getTopForums(),
                'active_members' => $this->getActiveMembers(),
                'trending_topics' => $this->getTrendingTopics(),
                'user_recommendations' => $user ? $this->getUserRecommendations($user) : [],
            ];
        });
    }

    /**
     * Lấy thông tin site từ bảng settings
     */
    private function getSiteSettings(): array
    {
        return Cache::remember('site_settings', 3600, function () {
            $settings = DB::table('settings')
                ->whereIn('key', [
                    'site_name',
                    'site_tagline',
                    'site_logo',
                    'site_favicon',
                    'site_description',
                    'site_language'
                ])
                ->pluck('value', 'key')
                ->toArray();

            return [
                'name' => $settings['site_name'] ?? 'MechaMap',
                'tagline' => $settings['site_tagline'] ?? 'Mạng lưới Kỹ sư Chuyên nghiệp',
                'logo' => !empty($settings['site_logo'])
                    ? (filter_var($settings['site_logo'], FILTER_VALIDATE_URL)
                        ? $settings['site_logo']
                        : asset($settings['site_logo']))  // Không thêm 'storage' vì path đã đầy đủ
                    : asset('images/logo-default.png'),
                'favicon' => !empty($settings['site_favicon'])
                    ? (filter_var($settings['site_favicon'], FILTER_VALIDATE_URL)
                        ? $settings['site_favicon']
                        : asset($settings['site_favicon']))  // Không thêm 'storage' vì path đã đầy đủ
                    : asset('images/favicon-default.ico'),
                'description' => $settings['site_description'] ?? 'Cộng đồng kỹ sư cơ khí Việt Nam',
                'language' => $settings['site_language'] ?? 'vi',
            ];
        });
    }

    /**
     * Thống kê cộng đồng từ database thật
     */
    private function getCommunityStats(): array
    {
        return Cache::remember('community_stats', 600, function () {
            // Kiểm tra tables và columns tồn tại
            $hasEmailVerified = Schema::hasColumn('users', 'email_verified_at');
            $hasDeletedAt = Schema::hasColumn('threads', 'deleted_at');
            $hasCommentsTable = Schema::hasTable('comments');

            // Lấy dữ liệu thật từ database
            $totalThreads = Thread::when($hasDeletedAt, function ($query) {
                $query->whereNull('deleted_at');
            })->count();

            $verifiedUsers = User::when($hasEmailVerified, function ($query) {
                $query->whereNotNull('email_verified_at');
            })->count();

            $totalComments = $hasCommentsTable ? Comment::when(Schema::hasColumn('comments', 'deleted_at'), function ($query) {
                $query->whereNull('deleted_at');
            })->count() : 0;

            $threadsToday = Thread::when($hasDeletedAt, function ($query) {
                $query->whereNull('deleted_at');
            })->whereDate('created_at', today())->count();

            // Tính active users dựa trên hoạt động trong tuần
            $activeUsersWeek = User::where(function ($query) use ($hasDeletedAt, $hasCommentsTable) {
                $query->whereHas('threads', function ($q) use ($hasDeletedAt) {
                    $q->where('created_at', '>=', now()->subWeek())
                        ->when($hasDeletedAt, function ($subq) {
                            $subq->whereNull('deleted_at');
                        });
                });

                if ($hasCommentsTable) {
                    $query->orWhereHas('comments', function ($q) {
                        $q->where('created_at', '>=', now()->subWeek())
                            ->when(Schema::hasColumn('comments', 'deleted_at'), function ($subq) {
                                $subq->whereNull('deleted_at');
                            });
                    });
                }
            })->count();

            return [
                'total_threads' => $totalThreads,
                'verified_users' => $verifiedUsers,
                'total_comments' => $totalComments,
                'threads_today' => $threadsToday,
                'active_users_week' => $activeUsersWeek,
                'growth_rate' => $this->calculateGrowthRate(),
            ];
        });
    }

    /**
     * Threads nổi bật từ database thật
     */
    private function getFeaturedThreads(): array
    {
        return Cache::remember('featured_threads', 300, function () {
            $hasFeatureColumns = Schema::hasColumn('threads', 'is_featured') && Schema::hasColumn('threads', 'is_sticky');
            $hasLikesTable = Schema::hasTable('thread_likes');
            $hasDeletedAt = Schema::hasColumn('threads', 'deleted_at');
            $hasCommentsTable = Schema::hasTable('comments');

            $threads = Thread::select([
                'id',
                'title',
                'slug',
                'user_id',
                'forum_id',
                'view_count',
                'created_at',
                'updated_at'
            ])
                ->with(['user:id,name,username,avatar', 'forum:id,name,slug'])
                ->when($hasDeletedAt, function ($query) {
                    $query->whereNull('deleted_at');
                })
                ->where(function ($query) use ($hasFeatureColumns, $hasLikesTable, $hasCommentsTable) {
                    if ($hasFeatureColumns) {
                        $query->where('is_featured', true)->orWhere('is_sticky', true);
                    }

                    // Fallback: threads có nhiều tương tác
                    $query->orWhere(function ($q) use ($hasLikesTable, $hasCommentsTable) {
                        $likesSubquery = $hasLikesTable
                            ? '(SELECT COUNT(*) FROM thread_likes WHERE thread_id = threads.id) * 3'
                            : '0';

                        $commentsSubquery = $hasCommentsTable
                            ? '(SELECT COUNT(*) FROM comments WHERE thread_id = threads.id) * 2'
                            : '0';

                        $q->whereRaw("
                        (COALESCE(view_count, 0) * 0.1 + {$commentsSubquery} + {$likesSubquery}) >= 5
                    ")->where('created_at', '>=', now()->subDays(14));
                    });
                })
                ->orderByRaw(
                    $hasFeatureColumns
                        ? 'CASE WHEN is_sticky = 1 THEN 3 WHEN is_featured = 1 THEN 2 ELSE 1 END DESC, COALESCE(view_count, 0) DESC'
                        : 'COALESCE(view_count, 0) DESC'
                )
                ->limit(8)
                ->get();

            return $threads->map(function ($thread) {
                return [
                    'id' => $thread->id,
                    'title' => $thread->title,
                    'slug' => $thread->slug ?? Str::slug($thread->title),
                    'author' => [
                        'name' => $thread->user->name,
                        'username' => $thread->user->username ?? Str::slug($thread->user->name),
                        'avatar_url' => $thread->user->getAvatarUrl(),
                    ],
                    'forum' => [
                        'name' => $thread->forum->name,
                        'slug' => $thread->forum->slug ?? Str::slug($thread->forum->name),
                    ],
                    'metrics' => [
                        'views' => $thread->view_count ?? 0,
                        'engagement_score' => $this->calculateEngagementScore($thread),
                    ],
                    'time_ago' => $thread->updated_at->diffForHumans(),
                ];
            })->toArray();
        });
    }

    /**
     * Trending topics từ database thật với fallback logic
     */
    private function getTrendingTopics(): array
    {
        return Cache::remember('trending_topics', 1800, function () {
            $hasForumsTable = Schema::hasTable('forums');
            $hasCategoriesTable = Schema::hasTable('categories');
            $hasDeletedAt = Schema::hasColumn('threads', 'deleted_at');

            // Thử lấy trending topics trong 7 ngày qua
            $trending = $this->getTrendingTopicsForPeriod(7, $hasForumsTable, $hasCategoriesTable, $hasDeletedAt);

            // Nếu không có, thử 30 ngày
            if (empty($trending)) {
                $trending = $this->getTrendingTopicsForPeriod(30, $hasForumsTable, $hasCategoriesTable, $hasDeletedAt);
            }

            // Nếu vẫn không có, lấy top forums theo tổng số threads
            if (empty($trending)) {
                $trending = $this->getTopForumsByTotalThreads($hasForumsTable, $hasCategoriesTable, $hasDeletedAt);
            }

            return collect($trending)->map(function ($topic) {
                return [
                    'name' => $topic->forum_name,
                    'slug' => $topic->forum_slug ?? Str::slug($topic->forum_name),
                    'thread_count' => (int) $topic->thread_count,
                    'trend_score' => max(1, round($topic->thread_count * 2 + ($topic->avg_views ?? 0) * 0.1)),
                    'latest_activity' => isset($topic->latest_activity) && $topic->latest_activity
                        ? \Carbon\Carbon::parse($topic->latest_activity)->diffForHumans()
                        : 'Không có hoạt động',
                ];
            })->toArray();
        });
    }

    /**
     * Lấy trending topics trong khoảng thời gian cụ thể
     */
    private function getTrendingTopicsForPeriod(int $days, bool $hasForumsTable, bool $hasCategoriesTable, bool $hasDeletedAt): array
    {
        if ($hasForumsTable) {
            return DB::select("
                SELECT
                    forums.name as forum_name,
                    forums.slug as forum_slug,
                    COUNT(threads.id) as thread_count,
                    AVG(COALESCE(threads.view_count, 0)) as avg_views,
                    MAX(threads.created_at) as latest_activity
                FROM forums
                LEFT JOIN threads ON forums.id = threads.forum_id
                WHERE threads.created_at >= DATE_SUB(NOW(), INTERVAL {$days} DAY)
                    " . ($hasDeletedAt ? "AND (threads.deleted_at IS NULL)" : "") . "
                GROUP BY forums.id, forums.name, forums.slug
                HAVING COUNT(threads.id) >= 1
                ORDER BY (COUNT(threads.id) * 2 + AVG(COALESCE(threads.view_count, 0)) * 0.1) DESC
                LIMIT 5
            ");
        } elseif ($hasCategoriesTable) {
            return DB::select("
                SELECT
                    categories.name as forum_name,
                    categories.slug as forum_slug,
                    COUNT(threads.id) as thread_count,
                    AVG(COALESCE(threads.view_count, 0)) as avg_views,
                    MAX(threads.created_at) as latest_activity
                FROM categories
                LEFT JOIN threads ON categories.id = threads.category_id
                WHERE threads.created_at >= DATE_SUB(NOW(), INTERVAL {$days} DAY)
                    " . ($hasDeletedAt ? "AND (threads.deleted_at IS NULL)" : "") . "
                GROUP BY categories.id, categories.name, categories.slug
                HAVING COUNT(threads.id) >= 1
                ORDER BY (COUNT(threads.id) * 2 + AVG(COALESCE(threads.view_count, 0)) * 0.1) DESC
                LIMIT 5
            ");
        }

        return [];
    }

    /**
     * Fallback: Lấy top forums theo tổng số threads (không giới hạn thời gian)
     */
    private function getTopForumsByTotalThreads(bool $hasForumsTable, bool $hasCategoriesTable, bool $hasDeletedAt): array
    {
        if ($hasForumsTable) {
            return DB::select("
                SELECT
                    forums.name as forum_name,
                    forums.slug as forum_slug,
                    COUNT(threads.id) as thread_count,
                    AVG(COALESCE(threads.view_count, 0)) as avg_views,
                    MAX(threads.created_at) as latest_activity
                FROM forums
                LEFT JOIN threads ON forums.id = threads.forum_id
                " . ($hasDeletedAt ? "WHERE (threads.deleted_at IS NULL OR threads.deleted_at IS NULL)" : "") . "
                GROUP BY forums.id, forums.name, forums.slug
                HAVING COUNT(threads.id) >= 1
                ORDER BY COUNT(threads.id) DESC, AVG(COALESCE(threads.view_count, 0)) DESC
                LIMIT 5
            ");
        } elseif ($hasCategoriesTable) {
            return DB::select("
                SELECT
                    categories.name as forum_name,
                    categories.slug as forum_slug,
                    COUNT(threads.id) as thread_count,
                    AVG(COALESCE(threads.view_count, 0)) as avg_views,
                    MAX(threads.created_at) as latest_activity
                FROM categories
                LEFT JOIN threads ON categories.id = threads.category_id
                " . ($hasDeletedAt ? "WHERE (threads.deleted_at IS NULL OR threads.deleted_at IS NULL)" : "") . "
                GROUP BY categories.id, categories.name, categories.slug
                HAVING COUNT(threads.id) >= 1
                ORDER BY COUNT(threads.id) DESC, AVG(COALESCE(threads.view_count, 0)) DESC
                LIMIT 5
            ");
        }

        return [];
    }

    /**
     * Top forums/categories từ database thật
     */
    private function getTopForums(): array
    {
        return Cache::remember('top_forums', 600, function () {
            $hasForumsTable = Schema::hasTable('forums');
            $hasCategoriesTable = Schema::hasTable('categories');
            $hasDeletedAt = Schema::hasColumn('threads', 'deleted_at');

            if ($hasForumsTable) {
                $forums = Forum::select(['id', 'name', 'slug', 'description', 'parent_id'])
                    ->withCount([
                        'threads' => function ($query) use ($hasDeletedAt) {
                            $query->where('created_at', '>=', now()->subMonth())
                                ->when($hasDeletedAt, function ($q) {
                                    $q->whereNull('deleted_at');
                                });
                        },
                        'threads as total_threads' => function ($query) use ($hasDeletedAt) {
                            $query->when($hasDeletedAt, function ($q) {
                                $q->whereNull('deleted_at');
                            });
                        }
                    ])
                    ->whereNull('parent_id')
                    ->having('threads_count', '>', 0)
                    ->orderBy('threads_count', 'desc')
                    ->limit(6)
                    ->get();
            } elseif ($hasCategoriesTable) {
                $forums = Category::select(['id', 'name', 'slug', 'description', 'parent_id'])
                    ->withCount([
                        'threads' => function ($query) use ($hasDeletedAt) {
                            $query->where('created_at', '>=', now()->subMonth())
                                ->when($hasDeletedAt, function ($q) {
                                    $q->whereNull('deleted_at');
                                });
                        },
                        'threads as total_threads' => function ($query) use ($hasDeletedAt) {
                            $query->when($hasDeletedAt, function ($q) {
                                $q->whereNull('deleted_at');
                            });
                        }
                    ])
                    ->whereNull('parent_id')
                    ->having('threads_count', '>', 0)
                    ->orderBy('threads_count', 'desc')
                    ->limit(6)
                    ->get();
            } else {
                $forums = collect([]);
            }

            return $forums->map(function ($forum) {
                return [
                    'id' => $forum->id,
                    'name' => $forum->name,
                    'slug' => $forum->slug ?? Str::slug($forum->name),
                    'description' => Str::limit($forum->description ?? '', 60),
                    'recent_threads' => $forum->threads_count ?? 0,
                    'total_threads' => $forum->total_threads ?? 0,
                    'image_url' => $this->getForumImageFromMedia($forum),
                    'activity_level' => $this->getActivityLevel($forum->threads_count ?? 0),
                ];
            })->toArray();
        });
    }

    /**
     * Active members từ database thật
     */
    private function getActiveMembers(): array
    {
        return Cache::remember('active_members', 600, function () {
            $hasCommentsTable = Schema::hasTable('comments');
            $hasDeletedAt = Schema::hasColumn('threads', 'deleted_at');
            $hasEmailVerified = Schema::hasColumn('users', 'email_verified_at');

            $users = User::select(['id', 'name', 'username', 'avatar', 'created_at'])
                ->withCount([
                    'threads' => function ($query) use ($hasDeletedAt) {
                        $query->when($hasDeletedAt, function ($q) {
                            $q->whereNull('deleted_at');
                        });
                    }
                ])
                ->when($hasCommentsTable, function ($query) {
                    $query->withCount('comments');
                })
                ->when($hasEmailVerified, function ($query) {
                    $query->whereNotNull('email_verified_at');
                })
                ->where(function ($query) use ($hasCommentsTable, $hasDeletedAt) {
                    $query->whereHas('threads', function ($q) use ($hasDeletedAt) {
                        $q->where('created_at', '>=', now()->subDays(30))
                            ->when($hasDeletedAt, function ($subq) {
                                $subq->whereNull('deleted_at');
                            });
                    });

                    if ($hasCommentsTable) {
                        $query->orWhereHas('comments', function ($q) {
                            $q->where('created_at', '>=', now()->subDays(30))
                                ->when(Schema::hasColumn('comments', 'deleted_at'), function ($subq) {
                                    $subq->whereNull('deleted_at');
                                });
                        });
                    }
                })
                ->orderByRaw(
                    $hasCommentsTable
                        ? '(threads_count * 3 + comments_count) DESC'
                        : 'threads_count DESC'
                )
                ->limit(8)
                ->get();

            return $users->map(function ($user) use ($hasCommentsTable, $hasDeletedAt) {
                $recentThreads = Thread::where('user_id', $user->id)
                    ->where('created_at', '>=', now()->subDays(30))
                    ->when($hasDeletedAt, function ($q) {
                        $q->whereNull('deleted_at');
                    })
                    ->count();

                $recentComments = $hasCommentsTable
                    ? Comment::where('user_id', $user->id)
                    ->where('created_at', '>=', now()->subDays(30))
                    ->when(Schema::hasColumn('comments', 'deleted_at'), function ($q) {
                        $q->whereNull('deleted_at');
                    })
                    ->count()
                    : 0;

                $commentsCount = $hasCommentsTable ? ($user->comments_count ?? 0) : 0;

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username ?? Str::slug($user->name),
                    'avatar' => $user->getAvatarUrl(),
                    'contribution_score' => $user->threads_count * 3 + $commentsCount,
                    'recent_activity_score' => $recentThreads * 5 + $recentComments * 2,
                    'badge' => $this->getUserBadge($user, $commentsCount),
                    'join_date' => $user->created_at->format('M Y'),
                    'is_recently_active' => ($recentThreads + $recentComments) > 0,
                ];
            })->sortByDesc('contribution_score')->values()->toArray();
        });
    }

    /**
     * User recommendations từ database thật
     */
    private function getUserRecommendations(User $user): array
    {
        return Cache::remember("user_recommendations_{$user->id}", 900, function () use ($user) {
            $hasForumsTable = Schema::hasTable('forums');
            $hasDeletedAt = Schema::hasColumn('threads', 'deleted_at');

            $userForums = $user->threads()
                ->select($hasForumsTable ? 'forum_id' : 'category_id')
                ->groupBy($hasForumsTable ? 'forum_id' : 'category_id')
                ->pluck($hasForumsTable ? 'forum_id' : 'category_id')
                ->filter()
                ->toArray();

            if (empty($userForums)) {
                return [];
            }

            $recommendations = Thread::whereIn($hasForumsTable ? 'forum_id' : 'category_id', $userForums)
                ->where('user_id', '!=', $user->id)
                ->where('created_at', '>=', now()->subDays(3))
                ->when($hasDeletedAt, function ($query) {
                    $query->whereNull('deleted_at');
                })
                ->with(['user:id,name,username'])
                ->when($hasForumsTable, function ($query) {
                    $query->with(['forum:id,name']);
                }, function ($query) {
                    $query->with(['category:id,name']);
                })
                ->orderBy('view_count', 'desc')
                ->limit(5)
                ->get();

            return $recommendations->map(function ($thread) use ($hasForumsTable) {
                $forumOrCategory = $hasForumsTable ? $thread->forum : $thread->category;

                return [
                    'id' => $thread->id,
                    'title' => $thread->title,
                    'author' => $thread->user->name,
                    'forum' => $forumOrCategory ? $forumOrCategory->name : 'Chưa phân loại',
                    'relevance_score' => $this->calculateRelevanceScore($thread),
                ];
            })->toArray();
        });
    }

    /**
     * Helper methods
     */
    private function calculateEngagementScore(Thread $thread): int
    {
        $hasCommentsTable = Schema::hasTable('comments');
        $hasLikesTable = Schema::hasTable('thread_likes');

        $commentsCount = $hasCommentsTable ? $thread->comments()->count() : 0;
        $likesCount = $hasLikesTable ? $thread->likes()->count() : 0;
        $viewsScore = min(($thread->view_count ?? 0) * 0.1, 50);

        return round($commentsCount * 3 + $likesCount * 2 + $viewsScore);
    }

    private function calculateGrowthRate(): float
    {
        $thisWeek = User::where('created_at', '>=', now()->subWeek())->count();
        $lastWeek = User::whereBetween('created_at', [now()->subWeeks(2), now()->subWeek()])->count();

        return $lastWeek > 0 ? round((($thisWeek - $lastWeek) / $lastWeek) * 100, 1) : 0;
    }

    private function calculateRelevanceScore(Thread $thread): int
    {
        $hasCommentsTable = Schema::hasTable('comments');
        $commentsCount = $hasCommentsTable ? $thread->comments()->count() : 0;

        return min(($thread->view_count ?? 0) * 0.2 + $commentsCount * 5, 100);
    }

    private function getForumImageUrl($forum): string
    {
        return $this->getForumImageFromMedia($forum);
    }

    private function getActivityLevel(int $threadCount): string
    {
        if ($threadCount >= 20) return 'high';
        if ($threadCount >= 10) return 'medium';
        return 'low';
    }

    private function getUserBadge(User $user, int $commentsCount = 0): array
    {
        $totalContributions = $user->threads_count + $commentsCount;

        if ($totalContributions >= 100) {
            return ['name' => 'Kỹ sư Chuyên gia', 'class' => 'badge-gold', 'icon' => 'fas fa-star'];
        } elseif ($totalContributions >= 50) {
            return ['name' => 'Thành viên Cao cấp', 'class' => 'badge-silver', 'icon' => 'fas fa-shield-alt'];
        } elseif ($totalContributions >= 20) {
            return ['name' => 'Thành viên Tích cực', 'class' => 'badge-bronze', 'icon' => 'fas fa-trophy'];
        }

        return ['name' => 'Thành viên', 'class' => 'badge-basic', 'icon' => 'fas fa-user'];
    }

    /**
     * Lấy URL avatar từ bảng media hoặc fallback
     */
    private function getUserAvatarFromMedia(User $user): string
    {
        // Kiểm tra nếu user có media avatar
        if (method_exists($user, 'media') && $user->media) {
            // Tìm avatar dựa trên mediable relationship
            $avatarMedia = $user->media()
                ->where('mediable_type', 'App\\Models\\User')
                ->where('mediable_id', $user->id)
                ->where('mime_type', 'like', 'image/%')
                ->orderBy('created_at', 'desc')
                ->first();

            if ($avatarMedia) {
                // Kiểm tra nếu file_path là URL đầy đủ hoặc relative path
                if (filter_var($avatarMedia->file_path, FILTER_VALIDATE_URL)) {
                    return $avatarMedia->file_path;
                } else {
                    // Loại bỏ slash đầu để tránh double slash
                    $cleanPath = ltrim($avatarMedia->file_path, '/');
                    return asset('storage/' . $cleanPath);
                }
            }
        }

        // Kiểm tra media dựa trên user_id và type image
        $directMedia = \Illuminate\Support\Facades\DB::table('media')
            ->where('user_id', $user->id)
            ->where('mediable_type', 'App\\Models\\User')
            ->where('mediable_id', $user->id)
            ->where('mime_type', 'like', 'image/%')
            ->orderBy('created_at', 'desc')
            ->first();

        if ($directMedia) {
            if (filter_var($directMedia->file_path, FILTER_VALIDATE_URL)) {
                return $directMedia->file_path;
            } else {
                // Loại bỏ slash đầu để tránh double slash
                $cleanPath = ltrim($directMedia->file_path, '/');
                return asset('storage/' . $cleanPath);
            }
        }

        // Kiểm tra column avatar trực tiếp trong users table
        if ($user->avatar && !empty($user->avatar)) {
            if (filter_var($user->avatar, FILTER_VALIDATE_URL)) {
                return $user->avatar;
            } else {
                // Loại bỏ slash đầu để tránh double slash
                $cleanPath = ltrim($user->avatar, '/');
                return asset('storage/' . $cleanPath);
            }
        }

        // Fallback to internal avatar generator
        $firstLetter = strtoupper(substr($user->name, 0, 1));
        return route('avatar.generate', ['initial' => $firstLetter]);
    }

    /**
     * Lấy URL hình ảnh forum từ bảng media
     */
    private function getForumImageFromMedia($forum): string
    {
        // Kiểm tra nếu forum có media thông qua polymorphic relationship
        if (method_exists($forum, 'media') && $forum->media) {
            $forumMedia = $forum->media()
                ->where('mediable_type', 'App\\Models\\Forum')
                ->where('mediable_id', $forum->id)
                ->where('mime_type', 'like', 'image/%')
                ->orderBy('created_at', 'desc')
                ->first();

            if ($forumMedia) {
                if (filter_var($forumMedia->file_path, FILTER_VALIDATE_URL)) {
                    return $forumMedia->file_path;
                } else {
                    // Loại bỏ slash đầu để tránh double slash
                    $cleanPath = ltrim($forumMedia->file_path, '/');
                    return asset('storage/' . $cleanPath);
                }
            }
        }

        // Kiểm tra column image/icon trực tiếp trong forum table
        if (isset($forum->image) && !empty($forum->image)) {
            if (filter_var($forum->image, FILTER_VALIDATE_URL)) {
                return $forum->image;
            } else {
                // Loại bỏ slash đầu để tránh double slash
                $cleanPath = ltrim($forum->image, '/');
                return asset('storage/' . $cleanPath);
            }
        }

        if (isset($forum->icon) && !empty($forum->icon)) {
            if (filter_var($forum->icon, FILTER_VALIDATE_URL)) {
                return $forum->icon;
            } else {
                // Loại bỏ slash đầu để tránh double slash
                $cleanPath = ltrim($forum->icon, '/');
                return asset('storage/' . $cleanPath);
            }
        }

        // Fallback to internal avatar generator
        $forumInitials = strtoupper(substr($forum->name, 0, 2));
        return route('avatar.generate', ['initial' => $forumInitials]);
    }
}
