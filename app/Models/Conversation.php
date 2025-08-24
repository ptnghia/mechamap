<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\GroupRole;

/**
 *
 *
 * @property int $id
 * @property string|null $title
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Message|null $lastMessage
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Message> $messages
 * @property-read int|null $messages_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ConversationParticipant> $participants
 * @property-read int|null $participants_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Conversation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Conversation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Conversation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Conversation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Conversation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Conversation whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Conversation whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
        'conversation_type_id',
        'is_group',
        'group_request_id',
        'max_members',
        'is_public',
        'group_description',
        'group_rules',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_group' => 'boolean',
        'is_public' => 'boolean',
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
     * Get the group members of the conversation.
     */
    public function groupMembers(): HasMany
    {
        return $this->hasMany(GroupMember::class);
    }

    /**
     * Get the conversation type
     */
    public function conversationType(): BelongsTo
    {
        return $this->belongsTo(ConversationType::class);
    }

    /**
     * Get the group request that created this conversation
     */
    public function groupRequest(): BelongsTo
    {
        return $this->belongsTo(GroupRequest::class);
    }

    /**
     * Get the group members for this conversation
     */
    public function members(): HasMany
    {
        return $this->hasMany(GroupMember::class);
    }

    /**
     * Get active group members
     */
    public function activeMembers(): HasMany
    {
        return $this->hasMany(GroupMember::class)->where('is_active', true);
    }

    /**
     * Get group permissions for this conversation
     */
    public function permissions(): HasMany
    {
        return $this->hasMany(GroupPermission::class);
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

    /**
     * Check if this is a group conversation
     */
    public function isGroup(): bool
    {
        return $this->is_group;
    }

    /**
     * Check if user is a member of this group
     */
    public function hasMember(int $userId): bool
    {
        if (!$this->is_group) {
            return $this->participants()->where('user_id', $userId)->exists();
        }

        return $this->activeMembers()->where('user_id', $userId)->exists();
    }

    /**
     * Get member by user ID
     */
    public function getMember(int $userId): ?GroupMember
    {
        if (!$this->is_group) {
            return null;
        }

        return $this->activeMembers()->where('user_id', $userId)->first();
    }

    /**
     * Get member role for user
     */
    public function getMemberRole(int $userId): ?GroupRole
    {
        $member = $this->getMember($userId);
        return $member?->role;
    }

    /**
     * Check if user has specific role in group
     */
    public function userHasRole(int $userId, GroupRole $role): bool
    {
        return $this->getMemberRole($userId) === $role;
    }

    /**
     * Check if user is creator of group
     */
    public function isCreator(int $userId): bool
    {
        return $this->userHasRole($userId, GroupRole::CREATOR);
    }

    /**
     * Check if user is admin of group
     */
    public function isAdmin(int $userId): bool
    {
        return $this->userHasRole($userId, GroupRole::ADMIN);
    }

    /**
     * Check if user can manage group
     */
    public function canManage(int $userId): bool
    {
        $role = $this->getMemberRole($userId);
        return $role && in_array($role, [GroupRole::CREATOR, GroupRole::ADMIN]);
    }

    /**
     * Get group creator
     */
    public function getCreator(): ?GroupMember
    {
        return $this->activeMembers()->where('role', GroupRole::CREATOR)->first();
    }

    /**
     * Scope for group conversations
     */
    public function scopeGroups($query)
    {
        return $query->where('is_group', true);
    }

    /**
     * Scope for private conversations
     */
    public function scopePrivate($query)
    {
        return $query->where('is_group', false);
    }

    /**
     * Scope for public groups
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }
}
