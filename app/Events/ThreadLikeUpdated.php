<?php

namespace App\Events;

use App\Models\Thread;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ThreadLikeUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Thread $thread;
    public User $user;
    public bool $isLiked;
    public int $likeCount;

    /**
     * Create a new event instance.
     */
    public function __construct(Thread $thread, User $user, bool $isLiked, int $likeCount)
    {
        $this->thread = $thread;
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
            new Channel('thread.' . $this->thread->id),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'type' => 'thread_like_updated',
            'thread_id' => $this->thread->id,
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
        return 'thread.like.updated';
    }
}
