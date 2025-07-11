<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Security Incident Detected Event
 * 
 * Triggered when a security incident is detected in the system
 * Broadcasts to admin users for real-time notifications
 */
class SecurityIncidentDetected implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $incident;

    /**
     * Create a new event instance.
     */
    public function __construct(array $incident)
    {
        $this->incident = $incident;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('admin-security-alerts'),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'incident' => $this->incident,
            'timestamp' => now()->toISOString(),
            'severity' => $this->incident['threat_level'] ?? 'low',
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'security.incident.detected';
    }
}
