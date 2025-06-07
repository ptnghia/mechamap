<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Thread;
use App\Models\Comment;

class ModerationPolicy
{
    /**
     * Kiểm tra user có thể moderate content không.
     */
    public function moderateContent(User $user): bool
    {
        // TODO: Implement role system
        // return $user->hasRole(['admin', 'moderator']);

        // Temporary: Chỉ check admin hoặc có flag is_moderator
        return $user->role === 'admin' ||
            (method_exists($user, 'is_moderator') && $user->is_moderator);
    }

    /**
     * Kiểm tra user có thể flag thread không.
     */
    public function flagThread(User $user, Thread $thread): bool
    {
        // Không thể flag thread của chính mình
        if ($user->id === $thread->user_id) {
            return false;
        }

        // User đã authenticated có thể flag
        return true;
    }

    /**
     * Kiểm tra user có thể unflag thread không.
     */
    public function unflagThread(User $user, Thread $thread): bool
    {
        return $this->moderateContent($user);
    }

    /**
     * Kiểm tra user có thể đánh dấu thread là spam không.
     */
    public function markAsSpam(User $user): bool
    {
        return $this->moderateContent($user);
    }

    /**
     * Kiểm tra user có thể archive thread không.
     */
    public function archiveThread(User $user, Thread $thread): bool
    {
        // Author hoặc moderator có thể archive
        return $user->id === $thread->user_id || $this->moderateContent($user);
    }

    /**
     * Kiểm tra user có thể hide thread không.
     */
    public function hideThread(User $user): bool
    {
        return $this->moderateContent($user);
    }

    /**
     * Kiểm tra user có thể update moderation status không.
     */
    public function updateModerationStatus(User $user): bool
    {
        return $this->moderateContent($user);
    }

    /**
     * Kiểm tra user có thể flag comment không.
     */
    public function flagComment(User $user, Comment $comment): bool
    {
        // Không thể flag comment của chính mình
        if ($user->id === $comment->user_id) {
            return false;
        }

        // User đã authenticated có thể flag
        return true;
    }

    /**
     * Kiểm tra user có thể unflag comment không.
     */
    public function unflagComment(User $user): bool
    {
        return $this->moderateContent($user);
    }

    /**
     * Kiểm tra user có thể mark comment as solution không.
     */
    public function markCommentAsSolution(User $user, Comment $comment): bool
    {
        $thread = $comment->thread;

        // Author của thread hoặc moderator có thể mark solution
        return $user->id === $thread->user_id || $this->moderateContent($user);
    }

    /**
     * Kiểm tra user có thể bump thread không.
     */
    public function bumpThread(User $user, Thread $thread): bool
    {
        // Author hoặc moderator có thể bump
        return $user->id === $thread->user_id || $this->moderateContent($user);
    }

    /**
     * Kiểm tra user có thể update thread type không.
     */
    public function updateThreadType(User $user, Thread $thread): bool
    {
        // Author hoặc moderator có thể update type
        return $user->id === $thread->user_id || $this->moderateContent($user);
    }

    /**
     * Kiểm tra user có thể update thread SEO không.
     */
    public function updateThreadSEO(User $user, Thread $thread): bool
    {
        // Author hoặc moderator có thể update SEO
        return $user->id === $thread->user_id || $this->moderateContent($user);
    }

    /**
     * Kiểm tra user có thể access moderation dashboard không.
     */
    public function viewModerationDashboard(User $user): bool
    {
        return $this->moderateContent($user);
    }

    /**
     * Kiểm tra user có thể thực hiện batch operations không.
     */
    public function batchModeration(User $user): bool
    {
        return $this->moderateContent($user);
    }
}
