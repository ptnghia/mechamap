<?php

namespace App\Services;

use App\Models\Thread;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Service xử lý logic moderation states và visibility
 * Quản lý việc hiển thị content dựa trên trạng thái moderation
 */
class ModerationService
{
    /**
     * Kiểm tra thread có thể hiển thị công khai không
     */
    public function canViewThreadPublic(Thread $thread, ?User $user = null): bool
    {
        $user = $user ?? Auth::user();

        // Admin và moderator luôn xem được tất cả
        if ($user && ($user->isAdmin() || $user->isModerator())) {
            return true;
        }

        // Chủ sở hữu thread luôn xem được
        if ($user && $thread->user_id === $user->id) {
            return true;
        }

        // Kiểm tra moderation status
        switch ($thread->moderation_status) {
            case 'clean':
            case 'approved':
                return true;

            case 'flagged':
            case 'under_review':
                // Chỉ chủ sở hữu và staff mới xem được
                return false;

            case 'spam':
                // Hoàn toàn bị ẩn
                return false;

            default:
                return false;
        }
    }

    /**
     * Alias method for canViewThreadPublic - để tương thích với ThreadQualityController
     */
    public function canViewThread(Thread $thread, ?User $user = null): bool
    {
        return $this->canViewThreadPublic($thread, $user);
    }

    /**
     * Kiểm tra comment có thể hiển thị công khai không
     */
    public function canViewCommentPublic(Comment $comment, ?User $user = null): bool
    {
        $user = $user ?? Auth::user();

        // Admin và moderator luôn xem được tất cả
        if ($user && ($user->isAdmin() || $user->isModerator())) {
            return true;
        }

        // Chủ sở hữu comment luôn xem được
        if ($user && $comment->user_id === $user->id) {
            return true;
        }

        // Kiểm tra thread cha trước
        if (!$this->canViewThreadPublic($comment->thread, $user)) {
            return false;
        }

        // Kiểm tra comment có bị spam không
        if ($comment->is_spam) {
            return false;
        }

        // Kiểm tra visibility
        if (!$comment->is_visible) {
            return false;
        }

        return true;
    }

    /**
     * Lấy threads với filtering theo moderation status cho admin
     */
    public function getThreadsForModeration(array $filters = []): \Illuminate\Database\Eloquent\Builder
    {
        $query = Thread::with(['user', 'forum', 'tags']);

        // Filter theo moderation status
        if (isset($filters['moderation_status'])) {
            $query->where('moderation_status', $filters['moderation_status']);
        }

        // Filter theo thread type
        if (isset($filters['thread_type'])) {
            $query->where('thread_type', $filters['thread_type']);
        }

        // Filter theo forum
        if (isset($filters['forum_id'])) {
            $query->where('forum_id', $filters['forum_id']);
        }

        // Filter theo user
        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        // Filter theo thời gian
        if (isset($filters['period'])) {
            $this->applyPeriodFilter($query, $filters['period']);
        }

        // Sort
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';
        $query->orderBy($sortBy, $sortDirection);

        return $query;
    }

    /**
     * Cập nhật moderation status của thread
     */
    public function updateThreadModerationStatus(Thread $thread, string $status, ?User $moderator = null, ?string $reason = null): bool
    {
        try {
            $moderator = $moderator ?? Auth::user();
            $oldStatus = $thread->moderation_status;

            // Validate status
            $allowedStatuses = ['clean', 'flagged', 'under_review', 'spam', 'approved'];
            if (!in_array($status, $allowedStatuses)) {
                throw new \InvalidArgumentException("Invalid moderation status: {$status}");
            }

            // Update thread
            $thread->update([
                'moderation_status' => $status,
                'moderated_by' => $moderator->id,
                'moderated_at' => now(),
                'moderation_reason' => $reason,
            ]);

            // Log moderation action
            Log::info('Thread moderation status updated', [
                'thread_id' => $thread->id,
                'old_status' => $oldStatus,
                'new_status' => $status,
                'moderator_id' => $moderator->id,
                'reason' => $reason,
            ]);

            // Trigger events/notifications nếu cần
            $this->handleModerationStatusChange($thread, $oldStatus, $status, $moderator);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to update thread moderation status', [
                'thread_id' => $thread->id,
                'status' => $status,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Bulk update moderation status cho nhiều threads
     */
    public function bulkUpdateThreadsModerationStatus(array $threadIds, string $status, ?User $moderator = null, ?string $reason = null): array
    {
        $moderator = $moderator ?? Auth::user();
        $results = ['success' => [], 'failed' => []];

        foreach ($threadIds as $threadId) {
            $thread = Thread::find($threadId);

            if (!$thread) {
                $results['failed'][] = ['id' => $threadId, 'reason' => 'Thread not found'];
                continue;
            }

            if ($this->updateThreadModerationStatus($thread, $status, $moderator, $reason)) {
                $results['success'][] = $thread->id;
            } else {
                $results['failed'][] = ['id' => $thread->id, 'reason' => 'Update failed'];
            }
        }

        return $results;
    }

    /**
     * Lấy statistics moderation
     */
    public function getModerationStatistics(): array
    {
        return [
            'threads' => [
                'total' => Thread::count(),
                'clean' => Thread::where('moderation_status', 'clean')->count(),
                'flagged' => Thread::where('moderation_status', 'flagged')->count(),
                'under_review' => Thread::where('moderation_status', 'under_review')->count(),
                'spam' => Thread::where('moderation_status', 'spam')->count(),
                'approved' => Thread::where('moderation_status', 'approved')->count(),
            ],
            'comments' => [
                'total' => Comment::count(),
                'visible' => Comment::where('is_visible', true)->count(),
                'hidden' => Comment::where('is_visible', false)->count(),
                'spam' => Comment::where('is_spam', true)->count(),
            ],
            'pending_review' => [
                'threads' => Thread::whereIn('moderation_status', ['flagged', 'under_review'])->count(),
                'comments' => Comment::where('is_visible', false)->where('is_spam', false)->count(),
            ]
        ];
    }

    /**
     * Lấy trending threads công khai
     */
    public function getTrendingThreadsPublic(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return Thread::publicVisible()
            ->trending()
            ->with(['user', 'forum', 'tags'])
            ->limit($limit)
            ->get();
    }

    /**
     * Lấy top rated threads công khai
     */
    public function getTopRatedThreadsPublic(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return Thread::publicVisible()
            ->whereNotNull('average_rating')
            ->where('total_ratings', '>=', 3) // Ít nhất 3 ratings
            ->minRating(4.0) // Rating >= 4.0
            ->orderBy('average_rating', 'desc')
            ->orderBy('total_ratings', 'desc')
            ->with(['user', 'forum', 'tags'])
            ->limit($limit)
            ->get();
    }

    /**
     * Apply period filter
     */
    private function applyPeriodFilter($query, string $period): void
    {
        switch ($period) {
            case 'today':
                $query->whereDate('created_at', today());
                break;
            case 'week':
                $query->where('created_at', '>=', now()->subWeek());
                break;
            case 'month':
                $query->where('created_at', '>=', now()->subMonth());
                break;
            case 'year':
                $query->where('created_at', '>=', now()->subYear());
                break;
        }
    }

    /**
     * Handle moderation status change events
     */
    private function handleModerationStatusChange(Thread $thread, string $oldStatus, string $newStatus, User $moderator): void
    {
        // TODO: Implement notifications, emails, etc.
        // Ví dụ: gửi email cho thread owner khi status thay đổi

        // Nếu thread được approve sau khi bị flag
        if ($oldStatus === 'flagged' && $newStatus === 'approved') {
            // Send notification to thread owner
        }

        // Nếu thread bị mark spam
        if ($newStatus === 'spam') {
            // Send warning to thread owner
        }
    }
}
