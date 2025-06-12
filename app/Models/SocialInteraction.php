<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * 
 *
 * @property int $id
 * @property string $interaction_type Loại tương tác: like, share, follow, bookmark, rate, endorse, mention
 * @property string $interactable_type
 * @property int $interactable_id
 * @property int $user_id Người thực hiện tương tác
 * @property int|null $target_user_id Người nhận tương tác (cho follow, mention, endorse)
 * @property array<array-key, mixed>|null $metadata Metadata bổ sung: {"rating": 4.5, "platform": "linkedin", "expertise_area": "FEA"}
 * @property string $context Ngữ cảnh tương tác: trong thread, comment, showcase, profile user
 * @property numeric|null $rating_value Giá trị đánh giá kỹ thuật (1.00-5.00 cho technical accuracy)
 * @property string|null $interaction_note Ghi chú cho tương tác phức tạp (lý do endorse, feedback chi tiết)
 * @property string|null $endorsement_type Loại endorsement chuyên môn cho mechanical engineers
 * @property string|null $expertise_areas Lĩnh vực chuyên môn được endorse: ["CAD", "FEA", "Manufacturing", "Materials"]
 * @property string $status Trạng thái tương tác
 * @property \Illuminate\Support\Carbon $interaction_date Thời gian thực hiện tương tác
 * @property string|null $ip_address IP address cho audit trail
 * @property string|null $user_agent User agent cho analytics
 * @property string|null $referrer_url URL nguồn của tương tác
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string|null $formatted_rating
 * @property-read Model|\Eloquent $interactable
 * @property-read \App\Models\User|null $targetUser
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction inContext(string $context)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction ofType(string $type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction recent()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction whereContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction whereEndorsementType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction whereExpertiseAreas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction whereInteractableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction whereInteractableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction whereInteractionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction whereInteractionNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction whereInteractionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction whereRatingValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction whereReferrerUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction whereTargetUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialInteraction whereUserId($value)
 * @mixin \Eloquent
 */
class SocialInteraction extends Model
{
    use HasFactory;

    protected $table = 'social_interactions';

    protected $fillable = [
        'interaction_type',
        'interactable_id',
        'interactable_type',
        'user_id',
        'target_user_id',
        'metadata',
        'context',
        'rating_value',
        'interaction_note',
        'status',
        'interaction_date',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'metadata' => 'array',
        'interaction_date' => 'datetime',
        'rating_value' => 'decimal:2',
    ];

    const INTERACTION_TYPES = [
        'follow' => 'Follow',
        'share' => 'Share',
        'mention' => 'Mention',
        'rate' => 'Rate',
        'endorse' => 'Endorse Solution',
        'collaborate' => 'Collaboration Request',
        'cite' => 'Technical Citation',
        'recommend' => 'Recommend',
    ];

    const CONTEXTS = [
        'thread' => 'Thread Discussion',
        'comment' => 'Comment',
        'showcase' => 'Project Showcase',
        'user' => 'User Profile',
        'general' => 'General',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function targetUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    public function interactable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('interaction_type', $type);
    }

    public function scopeInContext($query, string $context)
    {
        return $query->where('context', $context);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeRecent($query)
    {
        return $query->where('interaction_date', '>=', now()->subDays(30));
    }

    public function isRating(): bool
    {
        return $this->interaction_type === 'rate' && !is_null($this->rating_value);
    }

    public function isCollaborative(): bool
    {
        return in_array($this->interaction_type, ['collaborate', 'endorse', 'cite']);
    }

    public function getFormattedRatingAttribute(): ?string
    {
        if (!$this->isRating()) {
            return null;
        }
        return number_format($this->rating_value, 1) . '/5.0';
    }

    public function getTypeLabel(): string
    {
        return self::INTERACTION_TYPES[$this->interaction_type] ?? ucfirst($this->interaction_type);
    }

    public function getContextLabel(): string
    {
        return self::CONTEXTS[$this->context] ?? ucfirst($this->context);
    }

    public static function createFollow(User $follower, User $followed): self
    {
        return self::create([
            'interaction_type' => 'follow',
            'user_id' => $follower->id,
            'target_user_id' => $followed->id,
            'context' => 'user',
            'status' => 'active',
            'interaction_date' => now(),
        ]);
    }

    public static function createRating(User $user, Model $rateable, float $rating, string $note = null): self
    {
        return self::create([
            'interaction_type' => 'rate',
            'user_id' => $user->id,
            'interactable_id' => $rateable->id,
            'interactable_type' => get_class($rateable),
            'rating_value' => $rating,
            'interaction_note' => $note,
            'context' => $rateable instanceof Thread ? 'thread' : ($rateable instanceof Comment ? 'comment' : ($rateable instanceof Showcase ? 'showcase' : 'general')),
            'status' => 'active',
            'interaction_date' => now(),
        ]);
    }

    public static function createEndorsement(User $user, Model $endorseable, string $reason = null): self
    {
        return self::create([
            'interaction_type' => 'endorse',
            'user_id' => $user->id,
            'interactable_id' => $endorseable->id,
            'interactable_type' => get_class($endorseable),
            'interaction_note' => $reason,
            'context' => $endorseable instanceof Thread ? 'thread' : ($endorseable instanceof Comment ? 'comment' : 'general'),
            'status' => 'active',
            'interaction_date' => now(),
        ]);
    }
}
