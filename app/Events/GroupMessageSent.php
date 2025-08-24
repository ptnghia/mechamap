<?php

namespace App\Events;

use App\Models\Message;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Group Message Sent Event
 * 
 * Fired when a message is sent to a group conversation
 * Handles real-time broadcasting to group members
 */
class GroupMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Message $message;
    public User $sender;
    public array $groupMembers;

    /**
     * Create a new event instance.
     */
    public function __construct(Message $message, User $sender, array $groupMembers)
    {
        $this->message = $message;
        $this->sender = $sender;
        $this->groupMembers = $groupMembers;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $channels = [];

        // Broadcast to group channel
        $channels[] = new PrivateChannel('group.' . $this->message->conversation_id);

        // Broadcast to each member's private channel
        foreach ($this->groupMembers as $member) {
            $channels[] = new PrivateChannel('user.' . $member['id']);
        }

        return $channels;
    }

    /**
     * Get the event name for broadcasting
     */
    public function broadcastAs(): string
    {
        return 'group.message.sent';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'message' => [
                'id' => $this->message->id,
                'group_id' => $this->message->conversation_id,
                'content' => $this->message->content,
                'type' => $this->message->type,
                'is_system' => $this->message->is_system,
                'created_at' => $this->message->created_at->toISOString(),
                'attachments' => $this->message->attachments ?? [],
                'sender' => [
                    'id' => $this->sender->id,
                    'name' => $this->sender->name,
                    'avatar' => $this->sender->avatar_url,
                    'role' => $this->sender->role,
                ],
            ],
            'group' => [
                'id' => $this->message->conversation_id,
                'title' => $this->message->conversation->title,
                'member_count' => count($this->groupMembers),
            ],
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Determine if this event should broadcast.
     */
    public function shouldBroadcast(): bool
    {
        return $this->message->conversation->type === 'group' && 
               count($this->groupMembers) > 0;
    }
}
