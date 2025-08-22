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

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Message $message;
    public User $sender;
    public array $recipients;

    /**
     * Create a new event instance.
     */
    public function __construct(Message $message, User $sender, array $recipients)
    {
        $this->message = $message;
        $this->sender = $sender;
        $this->recipients = $recipients;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $channels = [];

        // Broadcast to conversation channel for real-time chat updates
        $channels[] = new PrivateChannel('conversation.' . $this->message->conversation_id);

        // Broadcast to each recipient's private channel
        foreach ($this->recipients as $recipient) {
            $channels[] = new PrivateChannel('user.' . $recipient['id']);
        }

        return $channels;
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'message' => [
                'id' => $this->message->id,
                'conversation_id' => $this->message->conversation_id,
                'content' => $this->message->content,
                'created_at' => $this->message->created_at->toISOString(),
                'sender' => [
                    'id' => $this->sender->id,
                    'name' => $this->sender->name,
                    'avatar' => $this->sender->avatar,
                    'role' => $this->sender->role,
                ]
            ],
            'conversation' => [
                'id' => $this->message->conversation_id,
                'title' => $this->message->conversation->title,
                'participants_count' => count($this->recipients) + 1, // +1 for sender
            ],
            'recipients' => $this->recipients,
            'timestamp' => now()->toISOString(),
            'type' => 'message_sent',
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'message.sent';
    }
}
