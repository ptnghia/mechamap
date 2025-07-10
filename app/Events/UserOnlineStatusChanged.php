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

class UserOnlineStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    public bool $isOnline;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, bool $isOnline)
    {
        $this->user = $user;
        $this->isOnline = $isOnline;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        $channels = [];

        // Admin dashboard channel
        $channels[] = new PrivateChannel('admin.dashboard');

        // Role-specific channels
        if (method_exists($this->user, 'getRoleNames')) {
            foreach ($this->user->getRoleNames() as $role) {
                $channels[] = new PrivateChannel('dashboard.' . strtolower($role));
            }
        }

        return $channels;
    }

    /**
     * Get the event name for broadcasting
     */
    public function broadcastAs(): string
    {
        return 'user.online.status.changed';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'is_online' => $this->isOnline,
            'status_changed_at' => now()->toISOString(),
            'user_role' => method_exists($this->user, 'getRoleNames') 
                ? $this->user->getRoleNames()->first() 
                : 'Member',
        ];
    }
}
