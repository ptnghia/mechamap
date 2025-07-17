<?php

namespace App\Broadcasting;

use Illuminate\Broadcasting\Broadcasters\Broadcaster;
use Illuminate\Broadcasting\BroadcastException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NodejsBroadcaster extends Broadcaster
{
    /**
     * The Node.js server URL.
     */
    protected $nodeUrl;

    /**
     * Create a new broadcaster instance.
     */
    public function __construct(array $config)
    {
        $this->nodeUrl = $config['url'] ?? 'http://localhost:3000';
    }

    /**
     * Authenticate the incoming request for a given channel.
     */
    public function auth($request)
    {
        $channel = $request->input('channel_name');

        // For private channels, check if user is authenticated
        if (str_starts_with($channel, 'private-')) {
            return $request->user() ? ['user_id' => $request->user()->id] : false;
        }

        // For presence channels, return user info
        if (str_starts_with($channel, 'presence-')) {
            if ($user = $request->user()) {
                return [
                    'user_id' => $user->id,
                    'user_info' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'avatar' => $user->getAvatarUrl(),
                    ]
                ];
            }
            return false;
        }

        // Public channels are always authorized
        return true;
    }

    /**
     * Return the valid authentication response.
     */
    public function validAuthenticationResponse($request, $result)
    {
        return response()->json($result);
    }

    /**
     * Broadcast the given event.
     */
    public function broadcast(array $channels, $event, array $payload = [])
    {
        try {
            $data = [
                'channels' => $channels,
                'event' => $event,
                'data' => $payload,
                'timestamp' => now()->toISOString(),
            ];

            $response = Http::timeout(5)->post($this->nodeUrl . '/api/laravel-broadcast', $data);

            if (!$response->successful()) {
                Log::warning('Node.js broadcast failed', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                    'data' => $data
                ]);
            }

            return $response->successful();

        } catch (\Exception $e) {
            Log::error('Node.js broadcast error: ' . $e->getMessage(), [
                'channels' => $channels,
                'event' => $event,
                'payload' => $payload
            ]);

            return false;
        }
    }
}
