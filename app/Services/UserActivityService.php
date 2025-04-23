<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserActivity;
use App\Models\Thread;
use App\Models\Comment;
use App\Models\ThreadFollow;
use App\Models\ThreadLike;
use App\Models\ThreadSave;

class UserActivityService
{
    /**
     * Ghi lại hoạt động tạo thread mới
     *
     * @param User $user
     * @param Thread $thread
     * @return UserActivity
     */
    public function logThreadCreated(User $user, Thread $thread): UserActivity
    {
        return UserActivity::create([
            'user_id' => $user->id,
            'activity_type' => 'thread_created',
            'activity_id' => $thread->id,
        ]);
    }

    /**
     * Ghi lại hoạt động bình luận trên thread
     *
     * @param User $user
     * @param Comment $comment
     * @return UserActivity
     */
    public function logCommentCreated(User $user, Comment $comment): UserActivity
    {
        return UserActivity::create([
            'user_id' => $user->id,
            'activity_type' => 'comment_created',
            'activity_id' => $comment->id,
        ]);
    }

    /**
     * Ghi lại hoạt động thích thread
     *
     * @param User $user
     * @param Thread $thread
     * @return UserActivity
     */
    public function logThreadLiked(User $user, Thread $thread): UserActivity
    {
        return UserActivity::create([
            'user_id' => $user->id,
            'activity_type' => 'thread_liked',
            'activity_id' => $thread->id,
        ]);
    }

    /**
     * Ghi lại hoạt động lưu thread
     *
     * @param User $user
     * @param Thread $thread
     * @return UserActivity
     */
    public function logThreadSaved(User $user, Thread $thread): UserActivity
    {
        return UserActivity::create([
            'user_id' => $user->id,
            'activity_type' => 'thread_saved',
            'activity_id' => $thread->id,
        ]);
    }

    /**
     * Ghi lại hoạt động cập nhật thông tin cá nhân
     *
     * @param User $user
     * @return UserActivity
     */
    public function logProfileUpdated(User $user): UserActivity
    {
        return UserActivity::create([
            'user_id' => $user->id,
            'activity_type' => 'profile_updated',
            'activity_id' => null,
        ]);
    }

    /**
     * Ghi lại hoạt động theo dõi thread
     *
     * @param User $user
     * @param Thread $thread
     * @return UserActivity
     */
    public function logThreadFollowed(User $user, Thread $thread): UserActivity
    {
        return UserActivity::create([
            'user_id' => $user->id,
            'activity_type' => 'thread_followed',
            'activity_id' => $thread->id,
        ]);
    }
}
