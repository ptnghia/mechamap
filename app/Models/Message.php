<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 *
 *
 * @property int $id
 * @property int $conversation_id
 * @property int $user_id
 * @property string $content
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Conversation $conversation
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereConversationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereUserId($value)
 * @mixin \Eloquent
 */
class Message extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'conversation_id',
        'user_id',
        'content',
        'is_system_message',
        'system_message_type',
        'system_message_data',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_system_message' => 'boolean',
        'system_message_data' => 'array',
    ];

    /**
     * Get the conversation that owns the message.
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Get the user that owns the message.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if this is a system message
     */
    public function isSystemMessage(): bool
    {
        return $this->is_system_message;
    }

    /**
     * Check if this is a user message
     */
    public function isUserMessage(): bool
    {
        return !$this->is_system_message;
    }

    /**
     * Get system message data
     */
    public function getSystemData(string $key = null)
    {
        if (!$this->is_system_message) {
            return null;
        }

        if ($key) {
            return $this->system_message_data[$key] ?? null;
        }

        return $this->system_message_data;
    }

    /**
     * Create a system message
     */
    public static function createSystemMessage(
        int $conversationId,
        string $type,
        string $content,
        array $data = [],
        ?int $userId = null
    ): self {
        return self::create([
            'conversation_id' => $conversationId,
            'user_id' => $userId,
            'content' => $content,
            'is_system_message' => true,
            'system_message_type' => $type,
            'system_message_data' => $data,
        ]);
    }

    /**
     * Scope for system messages
     */
    public function scopeSystemMessages($query)
    {
        return $query->where('is_system_message', true);
    }

    /**
     * Scope for user messages
     */
    public function scopeUserMessages($query)
    {
        return $query->where('is_system_message', false);
    }

    /**
     * Scope for specific system message type
     */
    public function scopeSystemMessageType($query, string $type)
    {
        return $query->where('is_system_message', true)
                    ->where('system_message_type', $type);
    }
}
