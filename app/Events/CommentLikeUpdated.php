<?php

namespace App\Events;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentLikeUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Comment $comment;
    public User $user;
    public bool $isLiked;
    public int $likeCount;

    /**
     * Create a new event instance.
     */
    public function __construct(Comment $comment, User $user, bool $isLiked, int $likeCount)
    {
        $this->comment = $comment;
        $this->user = $user;
        $this->isLiked = $isLiked;
        $this->likeCount = $likeCount;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('thread.' . $this->comment->thread_id),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'type' => 'comment_like_updated',
            'comment_id' => $this->comment->id,
            'thread_id' => $this->comment->thread_id,
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'is_liked' => $this->isLiked,
            'like_count' => $this->likeCount,
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'comment.like.updated';
    }
}
