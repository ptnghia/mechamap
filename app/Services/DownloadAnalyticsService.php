<?php

namespace App\Services;

use App\Models\DownloadToken;
use App\Models\ProtectedFile;
use App\Models\ProductPurchase;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

/**
 * Download Analytics Service
 * Provides comprehensive analytics for download tracking and business intelligence
 */
class DownloadAnalyticsService
{
    /**
     * Get overall download statistics
     */
    public function getOverallStats(): array
    {
        $stats = [
            'total_downloads' => DownloadToken::where('is_used', true)->count(),
            'unique_users' => DownloadToken::distinct('user_id')->count(),
            'unique_files' => DownloadToken::distinct('protected_file_id')->count(),
            'total_data_transferred' => $this->calculateTotalDataTransferred(),
        ];

        // Add time-based stats
        $stats['downloads_today'] = DownloadToken::where('is_used', true)
            ->where('used_at', '>=', now()->startOfDay())
            ->count();

        $stats['downloads_this_week'] = DownloadToken::where('is_used', true)
            ->where('used_at', '>=', now()->startOfWeek())
            ->count();

        $stats['downloads_this_month'] = DownloadToken::where('is_used', true)
            ->where('used_at', '>=', now()->startOfMonth())
            ->count();

        return $stats;
    }

    /**
     * Get download trends over time
     */
    public function getDownloadTrends(int $days = 30): Collection
    {
        return DownloadToken::select(
                DB::raw('DATE(used_at) as date'),
                DB::raw('COUNT(*) as downloads'),
                DB::raw('COUNT(DISTINCT user_id) as unique_users'),
                DB::raw('COUNT(DISTINCT protected_file_id) as unique_files')
            )
            ->where('is_used', true)
            ->where('used_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    /**
     * Get most popular files
     */
    public function getPopularFiles(int $limit = 10): Collection
    {
        return ProtectedFile::select(
                'protected_files.id',
                'protected_files.original_filename',
                'protected_files.file_size',
                'protected_files.file_type',
                'protected_files.product_id'
            )
            ->addSelect(DB::raw('COUNT(download_tokens.id) as download_count'))
            ->leftJoin('download_tokens', 'protected_files.id', '=', 'download_tokens.protected_file_id')
            ->where('download_tokens.is_used', true)
            ->groupBy('protected_files.id', 'protected_files.original_filename', 'protected_files.file_size', 'protected_files.file_type', 'protected_files.product_id')
            ->orderByDesc('download_count')
            ->limit($limit)
            ->get();
    }

    /**
     * Get user download behavior analysis
     */
    public function getUserBehaviorAnalysis(): array
    {
        $userStats = User::select(
                'users.id',
                'users.name',
                'users.email'
            )
            ->addSelect(DB::raw('COUNT(download_tokens.id) as total_downloads'))
            ->addSelect(DB::raw('COUNT(DISTINCT download_tokens.protected_file_id) as unique_files'))
            ->addSelect(DB::raw('COUNT(DISTINCT DATE(download_tokens.used_at)) as active_days'))
            ->leftJoin('download_tokens', 'users.id', '=', 'download_tokens.user_id')
            ->where('download_tokens.is_used', true)
            ->groupBy('users.id', 'users.name', 'users.email')
            ->having('total_downloads', '>', 0)
            ->get();

        return [
            'total_active_users' => $userStats->count(),
            'average_downloads_per_user' => $userStats->avg('total_downloads'),
            'average_unique_files_per_user' => $userStats->avg('unique_files'),
            'power_users' => $userStats->where('total_downloads', '>', 50)->count(),
            'casual_users' => $userStats->where('total_downloads', '<=', 5)->count(),
            'regular_users' => $userStats->whereBetween('total_downloads', [6, 50])->count(),
        ];
    }

    /**
     * Get license type usage statistics
     */
    public function getLicenseUsageStats(): Collection
    {
        return ProductPurchase::select('license_type')
            ->addSelect(DB::raw('COUNT(*) as purchase_count'))
            ->addSelect(DB::raw('SUM(download_count) as total_downloads'))
            ->addSelect(DB::raw('AVG(download_count) as avg_downloads_per_purchase'))
            ->groupBy('license_type')
            ->get();
    }

    /**
     * Get download performance metrics
     */
    public function getPerformanceMetrics(): array
    {
        $downloadTimes = DownloadToken::where('is_used', true)
            ->where('used_at', '>=', now()->subDays(7))
            ->get(['created_at', 'used_at'])
            ->map(function ($token) {
                return $token->used_at->diffInMilliseconds($token->created_at);
            });

        return [
            'average_download_time_ms' => $downloadTimes->avg(),
            'median_download_time_ms' => $downloadTimes->median(),
            'fastest_download_ms' => $downloadTimes->min(),
            'slowest_download_ms' => $downloadTimes->max(),
            'total_samples' => $downloadTimes->count(),
        ];
    }

    /**
     * Get geographic distribution of downloads
     */
    public function getGeographicDistribution(): array
    {
        // This would require a real IP geolocation service in production
        $ipStats = DownloadToken::select('ip_address')
            ->addSelect(DB::raw('COUNT(*) as download_count'))
            ->where('is_used', true)
            ->groupBy('ip_address')
            ->orderByDesc('download_count')
            ->limit(50)
            ->get();

        return [
            'unique_ip_addresses' => $ipStats->count(),
            'top_ip_addresses' => $ipStats->take(10),
            'geographic_data' => 'Requires IP geolocation service integration'
        ];
    }

    /**
     * Get revenue impact analysis
     */
    public function getRevenueImpact(): array
    {
        $revenueData = ProductPurchase::select('license_type')
            ->addSelect(DB::raw('COUNT(*) as purchases'))
            ->addSelect(DB::raw('SUM(amount_paid) as total_revenue'))
            ->addSelect(DB::raw('AVG(amount_paid) as avg_purchase_value'))
            ->addSelect(DB::raw('SUM(download_count) as total_downloads'))
            ->groupBy('license_type')
            ->get();

        $totalRevenue = $revenueData->sum('total_revenue');
        $totalPurchases = $revenueData->sum('purchases');

        return [
            'total_revenue' => $totalRevenue,
            'total_purchases' => $totalPurchases,
            'average_order_value' => $totalPurchases > 0 ? $totalRevenue / $totalPurchases : 0,
            'revenue_by_license' => $revenueData,
            'download_to_revenue_ratio' => $this->calculateDownloadToRevenueRatio(),
        ];
    }    /**
     * Get abuse and security statistics
     */
    public function getSecurityStats(): array
    {
        $now = now();

        return [
            'expired_tokens_attempted' => DownloadToken::where('expires_at', '<', $now)
                ->where('download_attempts', '>', 0)
                ->count(),

            'multiple_ip_users' => DB::table('download_tokens')
                ->select('user_id')
                ->groupBy('user_id')
                ->havingRaw('COUNT(DISTINCT ip_address) > 5')
                ->count(),

            'high_frequency_downloaders' => DB::table('download_tokens')
                ->select('user_id')
                ->where('created_at', '>=', now()->subDay())
                ->groupBy('user_id')
                ->havingRaw('COUNT(*) > 20')
                ->count(),

            'suspicious_user_agents' => DownloadToken::where('user_agent', 'REGEXP', 'bot|curl|wget|spider|crawler')
                ->distinct('user_id')
                ->count(),
        ];
    }

    /**
     * Generate comprehensive analytics report
     */
    public function generateComprehensiveReport(): array
    {
        return [
            'overview' => $this->getOverallStats(),
            'trends' => $this->getDownloadTrends(30),
            'popular_files' => $this->getPopularFiles(10),
            'user_behavior' => $this->getUserBehaviorAnalysis(),
            'license_usage' => $this->getLicenseUsageStats(),
            'performance' => $this->getPerformanceMetrics(),
            'geographic' => $this->getGeographicDistribution(),
            'revenue_impact' => $this->getRevenueImpact(),
            'security' => $this->getSecurityStats(),
            'generated_at' => now()->toISOString(),
        ];
    }

    /**
     * Export analytics data to CSV
     */
    public function exportToCSV(array $data, string $filename): string
    {
        $filePath = storage_path("app/exports/{$filename}");

        if (!is_dir(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }

        $file = fopen($filePath, 'w');

        // Write headers
        if (!empty($data)) {
            fputcsv($file, array_keys($data[0]));

            // Write data
            foreach ($data as $row) {
                fputcsv($file, $row);
            }
        }

        fclose($file);

        return $filePath;
    }

    /**
     * Calculate total data transferred
     */
    private function calculateTotalDataTransferred(): int
    {
        return DownloadToken::join('protected_files', 'download_tokens.protected_file_id', '=', 'protected_files.id')
            ->where('download_tokens.is_used', true)
            ->sum('protected_files.file_size');
    }

    /**
     * Calculate download to revenue ratio
     */
    private function calculateDownloadToRevenueRatio(): float
    {
        $totalDownloads = DownloadToken::where('is_used', true)->count();
        $totalRevenue = ProductPurchase::sum('amount_paid');

        return $totalRevenue > 0 ? $totalDownloads / $totalRevenue : 0;
    }
}
