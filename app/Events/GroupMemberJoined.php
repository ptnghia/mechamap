<?php

namespace App\Events;

use App\Models\GroupMember;
use App\Models\User;
use App\Models\Conversation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Group Member Joined Event
 * 
 * Fired when a new member joins a group conversation
 */
class GroupMemberJoined implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public GroupMember $member;
    public User $user;
    public Conversation $group;

    /**
     * Create a new event instance.
     */
    public function __construct(GroupMember $member)
    {
        $this->member = $member;
        $this->user = $member->user;
        $this->group = $member->conversation;
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
        return 'group.member.joined';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'group_id' => $this->group->id,
            'member' => [
                'id' => $this->member->id,
                'user_id' => $this->user->id,
                'user_name' => $this->user->name,
                'user_avatar' => $this->user->avatar_url,
                'role' => $this->member->role->value,
                'joined_at' => $this->member->joined_at->toISOString(),
            ],
            'group' => [
                'id' => $this->group->id,
                'title' => $this->group->title,
                'member_count' => $this->group->groupMembers()->where('is_active', true)->count(),
            ],
            'timestamp' => now()->toISOString(),
        ];
    }
}
