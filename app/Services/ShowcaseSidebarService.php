<?php

namespace App\Services;

use App\Models\Showcase;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ShowcaseSidebarService
{
    /**
     * Lấy tất cả dữ liệu sidebar showcase với caching tối ưu
     */
    public function getShowcaseSidebarData(User $user = null): array
    {
        return Cache::remember('showcase_sidebar_data_' . ($user?->id ?? 'guest'), 300, function () use ($user) {
            return [
                'showcase_stats' => $this->getShowcaseStats(),
                'popular_categories' => $this->getPopularCategories(),
                'featured_projects' => $this->getFeaturedProjects(),
                'popular_software' => $this->getPopularSoftware(),
                'top_contributors' => $this->getTopContributors(),
            ];
        });
    }

    /**
     * Lấy thống kê tổng quan showcase
     */
    private function getShowcaseStats(): array
    {
        return Cache::remember('showcase_stats', 600, function () {
            $stats = DB::table('showcases')
                ->selectRaw('
                    COUNT(*) as total_showcases,
                    COALESCE(SUM(download_count), 0) as total_downloads,
                    COALESCE(AVG(rating_average), 0) as avg_rating,
                    COALESCE(SUM(view_count), 0) as total_views
                ')
                ->where('status', 'approved')
                ->first();

            return [
                'total_showcases' => $stats->total_showcases ?? 0,
                'total_downloads' => $stats->total_downloads ?? 0,
                'avg_rating' => $stats->avg_rating ?? 0,
                'total_views' => $stats->total_views ?? 0,
            ];
        });
    }

    /**
     * Lấy danh mục phổ biến
     */
    private function getPopularCategories(): array
    {
        return Cache::remember('showcase_popular_categories', 600, function () {
            $categories = DB::table('showcases')
                ->select('category')
                ->selectRaw('
                    COUNT(*) as project_count,
                    COALESCE(AVG(rating_average), 0) as avg_rating,
                    COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as recent_count
                ')
                ->where('status', 'approved')
                ->whereNotNull('category')
                ->groupBy('category')
                ->orderBy('project_count', 'desc')
                ->limit(6)
                ->get();

            return $categories->map(function ($category) {
                return [
                    'name' => ucfirst($category->category),
                    'slug' => $category->category,
                    'project_count' => $category->project_count,
                    'avg_rating' => round($category->avg_rating, 1),
                    'trend' => $category->recent_count > 0 ? 1 : 0,
                ];
            })->toArray();
        });
    }

    /**
     * Lấy dự án nổi bật
     */
    private function getFeaturedProjects(): array
    {
        return Cache::remember('showcase_featured_projects', 300, function () {
            $projects = Showcase::with(['user'])
                ->where('status', 'approved')
                ->where(function ($query) {
                    $query->where('featured_at', '!=', null)
                          ->orWhere('rating_average', '>=', 4.0)
                          ->orWhere('view_count', '>=', 100);
                })
                ->orderByDesc('rating_average')
                ->orderByDesc('view_count')
                ->limit(5)
                ->get();

            return $projects->map(function ($project) {
                return [
                    'id' => $project->id,
                    'title' => $project->title,
                    'image_url' => $project->getCoverImageUrl(),
                    'complexity_level' => $project->complexity_level,
                    'category' => ucfirst($project->category ?? ''),
                    'views' => $project->view_count ?? 0,
                    'downloads' => $project->download_count ?? 0,
                    'rating' => $project->rating_average ?? 0,
                    'author' => [
                        'name' => $project->user->name ?? 'Unknown',
                        'username' => $project->user->username ?? '',
                    ],
                ];
            })->toArray();
        });
    }

    /**
     * Lấy phần mềm phổ biến
     */
    private function getPopularSoftware(): array
    {
        return Cache::remember('showcase_popular_software', 600, function () {
            // Lấy software từ JSON field
            $software = DB::table('showcases')
                ->where('status', 'approved')
                ->whereNotNull('software_used')
                ->get(['software_used'])
                ->flatMap(function ($showcase) {
                    $softwareList = json_decode($showcase->software_used, true);
                    return is_array($softwareList) ? $softwareList : [];
                })
                ->countBy()
                ->sortDesc()
                ->take(8);

            $softwareIcons = [
                'SolidWorks' => 'fas fa-cube',
                'AutoCAD' => 'fas fa-drafting-compass',
                'ANSYS' => 'fas fa-calculator',
                'MATLAB' => 'fas fa-chart-line',
                'Mastercam' => 'fas fa-cogs',
                'Vericut' => 'fas fa-tools',
                'Fusion 360' => 'fas fa-atom',
                'Inventor' => 'fas fa-project-diagram',
            ];

            return $software->map(function ($count, $name) use ($softwareIcons) {
                return [
                    'name' => $name,
                    'project_count' => $count,
                    'icon' => $softwareIcons[$name] ?? 'fas fa-desktop',
                ];
            })->values()->toArray();
        });
    }

    /**
     * Lấy top contributors
     */
    private function getTopContributors(): array
    {
        return Cache::remember('showcase_top_contributors', 600, function () {
            $contributors = DB::table('showcases')
                ->join('users', 'showcases.user_id', '=', 'users.id')
                ->select('users.id', 'users.name', 'users.username', 'users.avatar')
                ->selectRaw('
                    COUNT(showcases.id) as project_count,
                    COALESCE(SUM(showcases.view_count), 0) as total_views,
                    COALESCE(AVG(showcases.rating_average), 0) as avg_rating
                ')
                ->where('showcases.status', 'approved')
                ->groupBy('users.id', 'users.name', 'users.username', 'users.avatar')
                ->orderBy('project_count', 'desc')
                ->orderBy('total_views', 'desc')
                ->limit(6)
                ->get();

            return $contributors->map(function ($contributor) {
                return [
                    'name' => $contributor->name,
                    'username' => $contributor->username,
                    'avatar' => $contributor->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(strtoupper(substr($contributor->name, 0, 1))) . '&background=6366f1&color=fff&size=40',
                    'project_count' => $contributor->project_count,
                    'total_views' => $contributor->total_views,
                    'avg_rating' => round($contributor->avg_rating, 1),
                ];
            })->toArray();
        });
    }

    /**
     * Clear cache khi có thay đổi dữ liệu
     */
    public function clearCache(): void
    {
        Cache::forget('showcase_sidebar_data_guest');
        Cache::forget('showcase_stats');
        Cache::forget('showcase_popular_categories');
        Cache::forget('showcase_featured_projects');
        Cache::forget('showcase_popular_software');
        Cache::forget('showcase_top_contributors');

        // Clear user-specific cache
        $userIds = User::pluck('id');
        foreach ($userIds as $userId) {
            Cache::forget('showcase_sidebar_data_' . $userId);
        }
    }
}
