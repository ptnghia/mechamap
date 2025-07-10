<?php

namespace App\Events;

use App\Models\TypingIndicator;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TypingStarted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public TypingIndicator $indicator;

    /**
     * Create a new event instance.
     */
    public function __construct(TypingIndicator $indicator)
    {
        $this->indicator = $indicator;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel("typing.{$this->indicator->context_type}.{$this->indicator->context_id}"),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'event' => 'typing_started',
            'indicator' => [
                'id' => $this->indicator->id,
                'user' => [
                    'id' => $this->indicator->user->id,
                    'name' => $this->indicator->user->name,
                    'avatar' => $this->indicator->user->avatar_url,
                ],
                'context_type' => $this->indicator->context_type,
                'context_id' => $this->indicator->context_id,
                'typing_type' => $this->indicator->typing_type,
                'started_at' => $this->indicator->started_at,
                'expires_at' => $this->indicator->expires_at,
                'time_remaining' => $this->indicator->time_remaining,
            ],
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Get the broadcast event name.
     */
    public function broadcastAs(): string
    {
        return 'typing.started';
    }
}
