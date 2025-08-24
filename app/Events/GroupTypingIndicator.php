<?php

namespace App\Events;

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
 * Group Typing Indicator Event
 * 
 * Fired when a user starts or stops typing in a group conversation
 */
class GroupTypingIndicator implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    public Conversation $group;
    public bool $isTyping;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, Conversation $group, bool $isTyping)
    {
        $this->user = $user;
        $this->group = $group;
        $this->isTyping = $isTyping;
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
        return $this->isTyping ? 'group.typing.start' : 'group.typing.stop';
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
            'is_typing' => $this->isTyping,
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Determine if this event should broadcast.
     */
    public function shouldBroadcast(): bool
    {
        return $this->group->type === 'group';
    }
}
