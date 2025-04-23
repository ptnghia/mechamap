<?php

namespace App\Services;

use App\Models\Alert;
use App\Models\Comment;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Support\Str;

class AlertService
{
    /**
     * Tạo thông báo cho người dùng khi có bình luận mới trên thread họ theo dõi
     *
     * @param User $commenter Người bình luận
     * @param Thread $thread Thread được bình luận
     * @param Comment $comment Bình luận mới
     * @return void
     */
    public function createCommentAlert(User $commenter, Thread $thread, Comment $comment): void
    {
        // Lấy danh sách người theo dõi thread
        $followers = $thread->followers()->where('users.id', '!=', $commenter->id)->get();
        
        // Tạo thông báo cho chủ thread nếu không phải là người bình luận
        if ($thread->user_id !== $commenter->id) {
            $this->createThreadOwnerCommentAlert($thread->user, $commenter, $thread, $comment);
        }
        
        // Tạo thông báo cho những người theo dõi thread
        foreach ($followers as $follower) {
            // Bỏ qua nếu người theo dõi là chủ thread (đã được thông báo ở trên)
            if ($follower->id === $thread->user_id) {
                continue;
            }
            
            $this->createThreadFollowerCommentAlert($follower, $commenter, $thread, $comment);
        }
    }
    
    /**
     * Tạo thông báo cho chủ thread khi có bình luận mới
     *
     * @param User $owner Chủ thread
     * @param User $commenter Người bình luận
     * @param Thread $thread Thread được bình luận
     * @param Comment $comment Bình luận mới
     * @return Alert
     */
    private function createThreadOwnerCommentAlert(User $owner, User $commenter, Thread $thread, Comment $comment): Alert
    {
        $title = __('Bình luận mới trên bài viết của bạn');
        $content = __(':user đã bình luận trên bài viết ":thread" của bạn: :preview', [
            'user' => $commenter->name,
            'thread' => $thread->title,
            'preview' => Str::limit(strip_tags($comment->content), 100)
        ]);
        
        return Alert::create([
            'user_id' => $owner->id,
            'title' => $title,
            'content' => $content,
            'type' => 'info',
            'alertable_id' => $comment->id,
            'alertable_type' => Comment::class,
        ]);
    }
    
    /**
     * Tạo thông báo cho người theo dõi thread khi có bình luận mới
     *
     * @param User $follower Người theo dõi
     * @param User $commenter Người bình luận
     * @param Thread $thread Thread được bình luận
     * @param Comment $comment Bình luận mới
     * @return Alert
     */
    private function createThreadFollowerCommentAlert(User $follower, User $commenter, Thread $thread, Comment $comment): Alert
    {
        $title = __('Bình luận mới trên bài viết bạn theo dõi');
        $content = __(':user đã bình luận trên bài viết ":thread" mà bạn đang theo dõi: :preview', [
            'user' => $commenter->name,
            'thread' => $thread->title,
            'preview' => Str::limit(strip_tags($comment->content), 100)
        ]);
        
        return Alert::create([
            'user_id' => $follower->id,
            'title' => $title,
            'content' => $content,
            'type' => 'info',
            'alertable_id' => $comment->id,
            'alertable_type' => Comment::class,
        ]);
    }
}
