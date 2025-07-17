<?php

namespace App\Events;

use App\Models\Comment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $comment;

    /**
     * Create a new event instance.
     */
    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $channels = [];
        
        // Broadcast to thread channel for real-time updates
        $channels[] = new Channel('thread.' . $this->comment->thread_id);
        
        // Broadcast to thread followers
        $followers = $this->comment->thread->followers()
            ->where('users.id', '!=', $this->comment->user_id)
            ->pluck('users.id');
            
        foreach ($followers as $userId) {
            $channels[] = new PrivateChannel('user.' . $userId);
        }
        
        // Broadcast to thread owner if not the commenter
        if ($this->comment->thread->user_id !== $this->comment->user_id) {
            $channels[] = new PrivateChannel('user.' . $this->comment->thread->user_id);
        }
        
        return $channels;
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        $this->comment->load(['user', 'thread']);
        
        return [
            'type' => 'comment_created',
            'comment' => [
                'id' => $this->comment->id,
                'content' => $this->comment->content,
                'created_at' => $this->comment->created_at->toISOString(),
                'user' => [
                    'id' => $this->comment->user->id,
                    'name' => $this->comment->user->name,
                    'username' => $this->comment->user->username,
                    'avatar_url' => $this->comment->user->getAvatarUrl(),
                ],
                'thread' => [
                    'id' => $this->comment->thread->id,
                    'title' => $this->comment->thread->title,
                    'slug' => $this->comment->thread->slug,
                ],
                'parent_id' => $this->comment->parent_id,
            ],
            'message' => $this->comment->user->name . ' đã bình luận trong thread "' . $this->comment->thread->title . '"',
            'action_url' => route('threads.show', $this->comment->thread->slug) . '#comment-' . $this->comment->id,
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'comment.created';
    }
}
