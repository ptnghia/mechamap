<?php

namespace App\Http\Controllers;

use App\Services\WeeklyDigestService;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class WeeklyDigestController extends Controller
{
    /**
     * Get current user's latest digest
     */
    public function myLatestDigest(Request $request): JsonResponse
    {
        $user = auth()->user();
        
        $latestDigest = Notification::where('user_id', $user->id)
            ->where('type', 'weekly_digest')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$latestDigest) {
            return response()->json([
                'success' => false,
                'message' => 'Chưa có digest nào được tạo',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'digest' => [
                    'id' => $latestDigest->id,
                    'title' => $latestDigest->title,
                    'message' => $latestDigest->message,
                    'created_at' => $latestDigest->created_at,
                    'is_read' => $latestDigest->is_read,
                    'data' => $latestDigest->data,
                ],
            ],
        ]);
    }

    /**
     * Get user's digest history
     */
    public function myDigestHistory(Request $request): JsonResponse
    {
        $request->validate([
            'limit' => 'integer|min:1|max:50',
            'offset' => 'integer|min:0',
        ]);

        $user = auth()->user();
        $limit = $request->input('limit', 10);
        $offset = $request->input('offset', 0);

        $digests = Notification::where('user_id', $user->id)
            ->where('type', 'weekly_digest')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->get()
            ->map(function ($digest) {
                return [
                    'id' => $digest->id,
                    'title' => $digest->title,
                    'message' => $digest->message,
                    'created_at' => $digest->created_at,
                    'is_read' => $digest->is_read,
                    'week_period' => $digest->data['week_start'] ?? null . ' - ' . $digest->data['week_end'] ?? null,
                    'summary' => $this->extractDigestSummary($digest->data),
                ];
            });

        $totalCount = Notification::where('user_id', $user->id)
            ->where('type', 'weekly_digest')
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'digests' => $digests,
                'total_count' => $totalCount,
                'limit' => $limit,
                'offset' => $offset,
                'has_more' => $digests->count() === $limit,
            ],
        ]);
    }

    /**
     * Generate preview digest for current user
     */
    public function previewDigest(Request $request): JsonResponse
    {
        $user = auth()->user();
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();

        // Generate digest data
        $digestData = $this->generateDigestDataForUser($user, $weekStart, $weekEnd);

        return response()->json([
            'success' => true,
            'data' => [
                'preview' => $digestData,
                'would_send' => $this->shouldSendDigest($digestData),
                'message' => $this->shouldSendDigest($digestData) ? 
                    'Digest sẽ được gửi với nội dung này' : 
                    'Digest sẽ không được gửi do thiếu nội dung',
            ],
        ]);
    }

    /**
     * Update digest preferences
     */
    public function updatePreferences(Request $request): JsonResponse
    {
        $request->validate([
            'weekly_digest_enabled' => 'required|boolean',
            'digest_day' => 'string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'digest_time' => 'string|regex:/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/',
        ]);

        $user = auth()->user();
        $preferences = $user->notification_preferences ?? [];

        $preferences['weekly_digest'] = $request->boolean('weekly_digest_enabled');
        
        if ($request->has('digest_day')) {
            $preferences['digest_day'] = $request->input('digest_day');
        }
        
        if ($request->has('digest_time')) {
            $preferences['digest_time'] = $request->input('digest_time');
        }

        $user->update(['notification_preferences' => $preferences]);

        return response()->json([
            'success' => true,
            'message' => 'Cài đặt digest đã được cập nhật',
            'data' => [
                'preferences' => [
                    'weekly_digest_enabled' => $preferences['weekly_digest'],
                    'digest_day' => $preferences['digest_day'] ?? 'monday',
                    'digest_time' => $preferences['digest_time'] ?? '09:00',
                ],
            ],
        ]);
    }

    /**
     * Get digest statistics (admin)
     */
    public function statistics(Request $request): JsonResponse
    {
        // Check if user has admin permissions
        if (!auth()->user()->can('view_admin_statistics')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $statistics = WeeklyDigestService::getDigestStatistics();

        return response()->json([
            'success' => true,
            'data' => $statistics,
        ]);
    }

    /**
     * Send digest manually (admin)
     */
    public function sendDigest(Request $request): JsonResponse
    {
        // Check if user has admin permissions
        if (!auth()->user()->can('manage_notifications')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $request->validate([
            'user_id' => 'integer|exists:users,id',
            'force' => 'boolean',
        ]);

        try {
            if ($request->has('user_id')) {
                // Send to specific user
                $user = User::findOrFail($request->input('user_id'));
                $result = $this->sendDigestToUser($user, $request->boolean('force'));
            } else {
                // Send to all users
                $result = WeeklyDigestService::sendWeeklyDigests();
            }

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Digest đã được gửi thành công',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể gửi digest: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get digest engagement metrics (admin)
     */
    public function engagementMetrics(Request $request): JsonResponse
    {
        // Check if user has admin permissions
        if (!auth()->user()->can('view_admin_statistics')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $request->validate([
            'weeks' => 'integer|min:1|max:52',
        ]);

        $weeks = $request->input('weeks', 4);
        $startDate = now()->subWeeks($weeks);

        $metrics = [
            'total_sent' => Notification::where('type', 'weekly_digest')
                ->where('created_at', '>=', $startDate)
                ->count(),
            'total_read' => Notification::where('type', 'weekly_digest')
                ->where('created_at', '>=', $startDate)
                ->where('is_read', true)
                ->count(),
            'weekly_breakdown' => $this->getWeeklyBreakdown($weeks),
            'top_engaged_users' => $this->getTopEngagedUsers($weeks),
            'content_performance' => $this->getContentPerformance($weeks),
        ];

        $metrics['engagement_rate'] = $metrics['total_sent'] > 0 ? 
            round(($metrics['total_read'] / $metrics['total_sent']) * 100, 2) : 0;

        return response()->json([
            'success' => true,
            'data' => $metrics,
        ]);
    }

    /**
     * Extract digest summary from data
     */
    private function extractDigestSummary(array $data): array
    {
        $digestData = $data['digest_data'] ?? [];
        $activitySummary = $digestData['activity_summary'] ?? [];

        return [
            'threads_created' => $activitySummary['threads_created'] ?? 0,
            'comments_posted' => $activitySummary['comments_posted'] ?? 0,
            'new_followers' => $activitySummary['new_followers'] ?? 0,
            'achievements_unlocked' => $activitySummary['achievements_unlocked'] ?? 0,
            'new_content_count' => count($digestData['new_threads'] ?? []),
            'popular_content_count' => count($digestData['popular_threads'] ?? []),
        ];
    }

    /**
     * Generate digest data for user (simplified version)
     */
    private function generateDigestDataForUser(User $user, Carbon $weekStart, Carbon $weekEnd): array
    {
        // This is a simplified version for preview
        // In production, you might want to use the full WeeklyDigestService method
        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
            ],
            'period' => [
                'start' => $weekStart->format('d/m/Y'),
                'end' => $weekEnd->format('d/m/Y'),
                'week_number' => $weekStart->weekOfYear,
            ],
            'activity_summary' => [
                'threads_created' => $user->threads()->whereBetween('created_at', [$weekStart, $weekEnd])->count(),
                'comments_posted' => $user->comments()->whereBetween('created_at', [$weekStart, $weekEnd])->count(),
                'new_followers' => \App\Models\UserFollow::where('following_id', $user->id)
                    ->whereBetween('followed_at', [$weekStart, $weekEnd])->count(),
                'achievements_unlocked' => $user->userAchievements()
                    ->whereBetween('unlocked_at', [$weekStart, $weekEnd])->count(),
            ],
        ];
    }

    /**
     * Check if digest should be sent
     */
    private function shouldSendDigest(array $digestData): bool
    {
        $activitySummary = $digestData['activity_summary'] ?? [];
        
        return ($activitySummary['threads_created'] ?? 0) > 0 ||
               ($activitySummary['comments_posted'] ?? 0) > 0 ||
               ($activitySummary['new_followers'] ?? 0) > 0 ||
               ($activitySummary['achievements_unlocked'] ?? 0) > 0;
    }

    /**
     * Send digest to specific user
     */
    private function sendDigestToUser(User $user, bool $force = false): array
    {
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();

        // Check if already sent this week (unless forced)
        if (!$force) {
            $existingDigest = Notification::where('user_id', $user->id)
                ->where('type', 'weekly_digest')
                ->whereBetween('created_at', [$weekStart, $weekEnd])
                ->first();

            if ($existingDigest) {
                return [
                    'sent' => false,
                    'message' => 'Digest đã được gửi trong tuần này',
                ];
            }
        }

        $digestData = $this->generateDigestDataForUser($user, $weekStart, $weekEnd);

        if (!$this->shouldSendDigest($digestData) && !$force) {
            return [
                'sent' => false,
                'message' => 'Không có nội dung để gửi digest',
            ];
        }

        // Create notification
        Notification::create([
            'user_id' => $user->id,
            'type' => 'weekly_digest',
            'title' => 'Tóm tắt hoạt động tuần',
            'message' => "Tóm tắt hoạt động tuần {$digestData['period']['week_number']} của bạn",
            'data' => [
                'digest_data' => $digestData,
                'week_start' => $digestData['period']['start'],
                'week_end' => $digestData['period']['end'],
                'action_url' => '/notifications',
                'action_text' => 'Xem chi tiết',
            ],
            'priority' => 'low',
        ]);

        return [
            'sent' => true,
            'message' => 'Digest đã được gửi thành công',
        ];
    }

    /**
     * Get weekly breakdown
     */
    private function getWeeklyBreakdown(int $weeks): array
    {
        $breakdown = [];
        
        for ($i = 0; $i < $weeks; $i++) {
            $weekStart = now()->subWeeks($i)->startOfWeek();
            $weekEnd = now()->subWeeks($i)->endOfWeek();
            
            $sent = Notification::where('type', 'weekly_digest')
                ->whereBetween('created_at', [$weekStart, $weekEnd])
                ->count();
                
            $read = Notification::where('type', 'weekly_digest')
                ->whereBetween('created_at', [$weekStart, $weekEnd])
                ->where('is_read', true)
                ->count();

            $breakdown[] = [
                'week' => $weekStart->format('d/m/Y') . ' - ' . $weekEnd->format('d/m/Y'),
                'sent' => $sent,
                'read' => $read,
                'engagement_rate' => $sent > 0 ? round(($read / $sent) * 100, 2) : 0,
            ];
        }

        return array_reverse($breakdown);
    }

    /**
     * Get top engaged users
     */
    private function getTopEngagedUsers(int $weeks): array
    {
        $startDate = now()->subWeeks($weeks);

        return \DB::table('notifications')
            ->join('users', 'notifications.user_id', '=', 'users.id')
            ->where('notifications.type', 'weekly_digest')
            ->where('notifications.created_at', '>=', $startDate)
            ->where('notifications.is_read', true)
            ->select('users.id', 'users.name', \DB::raw('COUNT(*) as read_count'))
            ->groupBy('users.id', 'users.name')
            ->orderBy('read_count', 'desc')
            ->limit(10)
            ->get()
            ->toArray();
    }

    /**
     * Get content performance
     */
    private function getContentPerformance(int $weeks): array
    {
        // This would analyze which types of content in digests get the most engagement
        // For now, return placeholder data
        return [
            'most_clicked_sections' => [
                'new_threads' => 45,
                'popular_threads' => 38,
                'achievements' => 25,
                'new_followers' => 20,
                'trending_topics' => 15,
            ],
            'average_sections_per_digest' => 3.2,
            'most_active_day' => 'Monday',
        ];
    }
}
