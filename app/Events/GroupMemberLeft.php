<?php

namespace App\Events;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Group Member Left Event
 * 
 * Fired when a member leaves a group conversation
 */
class GroupMemberLeft implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    public Conversation $group;
    public string $reason;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, Conversation $group, string $reason = 'left')
    {
        $this->user = $user;
        $this->group = $group;
        $this->reason = $reason;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('group.' . $this->group->id),
        ];
    }

    /**
     * Get the event name for broadcasting
     */
    public function broadcastAs(): string
    {
        return 'group.member.left';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'group_id' => $this->group->id,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'avatar' => $this->user->avatar_url,
            ],
            'reason' => $this->reason,
            'group' => [
                'id' => $this->group->id,
                'title' => $this->group->title,
                'member_count' => $this->group->groupMembers()->where('is_active', true)->count(),
            ],
            'timestamp' => now()->toISOString(),
        ];
    }
}
