<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Conversation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
    ];

    /**
     * Get the participants of the conversation.
     */
    public function participants(): HasMany
    {
        return $this->hasMany(ConversationParticipant::class);
    }

    /**
     * Get the messages of the conversation.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Get the last message of the conversation.
     */
    public function lastMessage(): HasOne
    {
        return $this->hasOne(Message::class)->latest();
    }



    /**
     * Check if the conversation has unread messages for a user.
     */
    public function hasUnreadMessages(int $userId): bool
    {
        $participant = $this->participants()->where('user_id', $userId)->first();

        if (!$participant) {
            return false;
        }

        return $this->messages()
            ->where('created_at', '>', $participant->last_read_at ?? '1970-01-01')
            ->where('user_id', '!=', $userId)
            ->exists();
    }

    /**
     * Get the number of unread messages for a user.
     */
    public function unreadMessagesCount(int $userId): int
    {
        $participant = $this->participants()->where('user_id', $userId)->first();

        if (!$participant) {
            return 0;
        }

        return $this->messages()
            ->where('created_at', '>', $participant->last_read_at ?? '1970-01-01')
            ->where('user_id', '!=', $userId)
            ->count();
    }

    /**
     * Mark the conversation as read for a user.
     */
    public function markAsRead(int $userId): void
    {
        $this->participants()
            ->where('user_id', $userId)
            ->update(['last_read_at' => now()]);
    }
}
