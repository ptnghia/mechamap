<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationPreference extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'notification_preferences';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'channel',
        'type',
        'enabled',
        'settings',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'enabled' => 'boolean',
        'settings' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the preference
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for enabled preferences
     */
    public function scopeEnabled($query)
    {
        return $query->where('enabled', true);
    }

    /**
     * Scope for specific user
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for specific channel
     */
    public function scopeForChannel($query, string $channel)
    {
        return $query->where('channel', $channel);
    }

    /**
     * Scope for specific type
     */
    public function scopeForType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Check if user wants to receive notification
     */
    public static function shouldReceive(int $userId, string $type, string $channel): bool
    {
        $preference = static::forUser($userId)
            ->forType($type)
            ->forChannel($channel)
            ->first();

        // If no preference exists, default to enabled for database, disabled for others
        if (!$preference) {
            return $channel === 'database';
        }

        return $preference->enabled;
    }

    /**
     * Get user's enabled channels for a notification type
     */
    public static function getEnabledChannels(int $userId, string $type): array
    {
        return static::forUser($userId)
            ->forType($type)
            ->enabled()
            ->pluck('channel')
            ->toArray();
    }

    /**
     * Get user's preferences for all types
     */
    public static function getUserPreferences(int $userId): array
    {
        $preferences = static::forUser($userId)->get();
        
        $grouped = [];
        foreach ($preferences as $pref) {
            $grouped[$pref->type][$pref->channel] = [
                'enabled' => $pref->enabled,
                'settings' => $pref->settings,
            ];
        }
        
        return $grouped;
    }

    /**
     * Update user preference
     */
    public static function updatePreference(int $userId, string $type, string $channel, bool $enabled, array $settings = []): void
    {
        static::updateOrCreate(
            [
                'user_id' => $userId,
                'type' => $type,
                'channel' => $channel,
            ],
            [
                'enabled' => $enabled,
                'settings' => $settings,
            ]
        );
    }

    /**
     * Bulk update user preferences
     */
    public static function bulkUpdatePreferences(int $userId, array $preferences): void
    {
        foreach ($preferences as $type => $channels) {
            foreach ($channels as $channel => $config) {
                static::updatePreference(
                    $userId,
                    $type,
                    $channel,
                    $config['enabled'] ?? false,
                    $config['settings'] ?? []
                );
            }
        }
    }

    /**
     * Get default preferences for a user
     */
    public static function getDefaultPreferences(): array
    {
        return [
            // Forum preferences
            'forum_activity' => [
                'database' => ['enabled' => true, 'settings' => []],
                'email' => ['enabled' => false, 'settings' => []],
            ],
            'thread_created' => [
                'database' => ['enabled' => true, 'settings' => []],
                'email' => ['enabled' => false, 'settings' => []],
            ],
            'thread_replied' => [
                'database' => ['enabled' => true, 'settings' => []],
            ],

            // Social preferences
            'user_followed' => [
                'database' => ['enabled' => true, 'settings' => []],
            ],
            'user_registered' => [
                'database' => ['enabled' => true, 'settings' => []],
            ],

            // Marketplace preferences
            'marketplace_activity' => [
                'database' => ['enabled' => true, 'settings' => []],
            ],
            'business_verified' => [
                'database' => ['enabled' => true, 'settings' => []],
                'email' => ['enabled' => true, 'settings' => []],
            ],

            // System preferences
            'system_announcement' => [
                'database' => ['enabled' => true, 'settings' => []],
                'email' => ['enabled' => true, 'settings' => []],
            ],

            // Security preferences (always enabled)
            'security_alert' => [
                'database' => ['enabled' => true, 'settings' => []],
                'email' => ['enabled' => true, 'settings' => []],
            ],

            // Messaging preferences
            'message_received' => [
                'database' => ['enabled' => true, 'settings' => []],
            ],
        ];
    }

    /**
     * Create default preferences for a new user
     */
    public static function createDefaultForUser(int $userId): void
    {
        $defaults = static::getDefaultPreferences();
        static::bulkUpdatePreferences($userId, $defaults);
    }
}
