<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConnectionEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    public string $eventType;
    public array $connectionData;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, string $eventType, array $connectionData)
    {
        $this->user = $user;
        $this->eventType = $eventType;
        $this->connectionData = $connectionData;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        $channels = [
            new PrivateChannel('user.' . $this->user->id),
        ];

        // Admin monitoring channels
        if ($this->eventType === 'connection_error' || $this->eventType === 'connection_warning') {
            $channels[] = new PrivateChannel('admin.dashboard');
        }

        return $channels;
    }

    /**
     * Get the event name for broadcasting
     */
    public function broadcastAs(): string
    {
        return 'connection.' . $this->eventType;
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'user_id' => $this->user->id,
            'event_type' => $this->eventType,
            'timestamp' => now()->toISOString(),
            'connection_data' => $this->connectionData,
        ];
    }
}
