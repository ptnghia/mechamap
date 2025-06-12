<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Showcase;
use App\Models\Country;
use App\Models\Region;
use Spatie\Permission\Traits\HasRoles;

/**
 *
 *
 * @property int $id
 * @property string $name
 * @property string $username
 * @property string $email
 * @property string $role
 * @property string|null $permissions
 * @property string $status
 * @property string|null $avatar
 * @property string|null $about_me
 * @property string|null $website
 * @property string|null $location
 * @property string|null $signature
 * @property int $points
 * @property int $reaction_score
 * @property \Illuminate\Support\Carbon|null $last_seen_at
 * @property string|null $last_activity
 * @property int $setup_progress
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property int $is_active
 * @property string|null $last_login_at
 * @property string $password
 * @property string|null $remember_token
 * @property string|null $banned_at
 * @property string|null $banned_reason
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserActivity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Alert> $alerts
 * @property-read int|null $alerts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Bookmark> $bookmarks
 * @property-read int|null $bookmarks_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CommentLike> $commentLikes
 * @property-read int|null $comment_likes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Comment> $comments
 * @property-read int|null $comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Conversation> $conversations
 * @property-read int|null $conversations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Thread> $followedThreads
 * @property-read int|null $followed_threads_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $followers
 * @property-read int|null $followers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $following
 * @property-read int|null $following_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Post> $posts
 * @property-read int|null $posts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProfilePost> $profilePosts
 * @property-read int|null $profile_posts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Reaction> $reactions
 * @property-read int|null $reactions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProfilePost> $receivedProfilePosts
 * @property-read int|null $received_profile_posts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Reaction> $receivedReactions
 * @property-read int|null $received_reactions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Thread> $savedThreads
 * @property-read int|null $saved_threads_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Showcase> $showcaseItems
 * @property-read int|null $showcase_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SocialAccount> $socialAccounts
 * @property-read int|null $social_accounts_count
 * @property-read \App\Models\Subscription|null $subscription
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ThreadFollow> $threadFollows
 * @property-read int|null $thread_follows_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ThreadLike> $threadLikes
 * @property-read int|null $thread_likes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ThreadSave> $threadSaves
 * @property-read int|null $thread_saves_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Thread> $threads
 * @property-read int|null $threads_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserVisit> $visits
 * @property-read int|null $visits_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAboutMe($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereBannedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereBannedReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastActivity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastLoginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastSeenAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePermissions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereReactionScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereSetupProgress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereSignature($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereWebsite($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'status',
        'avatar',
        'about_me',
        'website',
        'location',
        'signature',
        'points',
        'reaction_score',
        'last_seen_at',
        'last_activity',
        'setup_progress',
        'email_verified_at',
        // Thêm support cho geographic location
        'country_id',
        'region_id',
        'work_locations',
        'expertise_regions',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_seen_at' => 'datetime',
            'password' => 'hashed',
            'work_locations' => 'array',
            'expertise_regions' => 'array',
        ];
    }

    /**
     * Check if user has a specific role
     *
     * @param string|array $role
     * @return bool
     */
    public function hasRole($role): bool
    {
        if (is_array($role)) {
            return in_array($this->role, $role);
        }

        return $this->role === $role;
    }

    /**
     * Check if user is an admin
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is a moderator
     *
     * @return bool
     */
    public function isModerator(): bool
    {
        return $this->role === 'moderator';
    }

    /**
     * Check if user is a senior
     *
     * @return bool
     */
    public function isSenior(): bool
    {
        return $this->role === 'senior';
    }

    /**
     * Check if user is a member
     *
     * @return bool
     */
    public function isMember(): bool
    {
        return $this->role === 'member';
    }

    /**
     * Check if user can access admin panel
     *
     * @return bool
     */
    public function canAccessAdmin(): bool
    {
        return in_array($this->role, ['admin', 'moderator']);
    }

    /**
     * Get avatar URL
     *
     * @return string
     */
    public function getAvatarUrl(): string
    {
        // Kiểm tra xem có avatar từ mạng xã hội không
        $socialAccount = $this->socialAccounts()->latest()->first();
        if ($socialAccount && $socialAccount->provider_avatar) {
            return $socialAccount->provider_avatar;
        }

        if ($this->avatar) {
            if (strpos($this->avatar, 'http') === 0) {
                return $this->avatar;
            }
            return asset('storage/' . $this->avatar);
        }

        $firstLetter = strtoupper(substr($this->username ?: $this->name, 0, 1));
        return "https://ui-avatars.com/api/?name={$firstLetter}&background=random&color=fff";
    }

    /**
     * Update user avatar
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return bool
     */
    public function updateAvatar($file): bool
    {
        // Delete old avatar if exists and not a URL
        if ($this->avatar && strpos($this->avatar, 'http') !== 0) {
            Storage::disk('public')->delete($this->avatar);
        }

        // Store new avatar
        $path = $file->store('avatars', 'public');
        $this->avatar = $path;

        return $this->save();
    }

    /**
     * Get the social accounts for the user.
     */
    public function socialAccounts(): HasMany
    {
        return $this->hasMany(SocialAccount::class);
    }

    /**
     * Get the threads created by the user.
     */
    public function threads(): HasMany
    {
        return $this->hasMany(Thread::class);
    }

    /**
     * Get the posts created by the user.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Get the media uploaded by the user.
     */
    public function media(): HasMany
    {
        return $this->hasMany(Media::class);
    }

    // ====================================================================
    // GEOGRAPHIC RELATIONSHIPS - Countries & Regions
    // ====================================================================

    /**
     * Get the country where user is located
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the region where user is located
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * Check if user is from specific country
     */
    public function isFromCountry(string $countryCode): bool
    {
        return $this->country && $this->country->code === $countryCode;
    }

    /**
     * Check if user is from specific region
     */
    public function isFromRegion(string $regionCode): bool
    {
        return $this->region && $this->region->code === $regionCode;
    }

    /**
     * Get user's primary timezone based on country/region
     */
    public function getTimezone(): string
    {
        if ($this->region && $this->region->timezone) {
            return $this->region->timezone;
        }

        if ($this->country && $this->country->timezone) {
            return $this->country->timezone;
        }

        return config('app.timezone', 'UTC');
    }

    /**
     * Get user's measurement system preference
     */
    public function getMeasurementSystem(): string
    {
        return $this->country?->measurement_system ?? 'metric';
    }

    /**
     * Get technical standards relevant to user's location
     */
    public function getRelevantStandards(): array
    {
        $standards = [];

        if ($this->country) {
            $standards = array_merge($standards, $this->country->standard_organizations ?? []);
        }

        if ($this->region) {
            $standards = array_merge($standards, $this->region->local_standards ?? []);
        }

        return array_unique($standards);
    }

    /**
     * Check if the user is online.
     */
    public function isOnline(): bool
    {
        return $this->last_seen_at && $this->last_seen_at->gt(now()->subMinutes(5));
    }





    /**
     * Get the profile posts created by the user.
     */
    public function profilePosts(): HasMany
    {
        return $this->hasMany(ProfilePost::class, 'user_id');
    }

    /**
     * Get the profile posts on the user's profile.
     */
    public function receivedProfilePosts(): HasMany
    {
        return $this->hasMany(ProfilePost::class, 'profile_id');
    }

    /**
     * Get the reactions created by the user.
     */
    public function reactions(): HasMany
    {
        return $this->hasMany(Reaction::class);
    }

    /**
     * Get the users that this user is following.
     */
    public function following(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'following_id')
            ->withTimestamps();
    }

    /**
     * Get the users that are following this user.
     */
    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'followers', 'following_id', 'follower_id')
            ->withTimestamps();
    }



    /**
     * Get the activities of the user.
     */
    public function activities(): HasMany
    {
        return $this->hasMany(UserActivity::class);
    }

    /**
     * Get the visits of the user.
     */
    public function visits(): HasMany
    {
        return $this->hasMany(UserVisit::class);
    }

    /**
     * Get all reactions received on user's content.
     */
    public function receivedReactions(): MorphMany
    {
        return $this->morphMany(Reaction::class, 'reactable');
    }

    /**
     * Update the user's reaction score.
     */
    public function updateReactionScore(): void
    {
        $this->reaction_score = $this->receivedReactions()->count();
        $this->save();
    }

    /**
     * Get the alerts for the user.
     */
    public function alerts(): HasMany
    {
        return $this->hasMany(Alert::class);
    }

    /**
     * Get the conversations that the user is participating in.
     */
    public function conversations(): BelongsToMany
    {
        return $this->belongsToMany(Conversation::class, 'conversation_participants')
            ->withPivot('last_read_at')
            ->withTimestamps();
    }

    /**
     * Get the bookmarks for the user.
     */
    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }



    /**
     * Get the comments created by the user.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get the thread likes created by the user.
     */
    public function threadLikes(): HasMany
    {
        return $this->hasMany(ThreadLike::class);
    }

    /**
     * Get the thread saves created by the user.
     */
    public function threadSaves(): HasMany
    {
        return $this->hasMany(ThreadSave::class);
    }

    /**
     * Get the saved threads for the user.
     */
    public function savedThreads(): BelongsToMany
    {
        return $this->belongsToMany(Thread::class, 'thread_saves')
            ->withTimestamps();
    }

    /**
     * Get the comment likes created by the user.
     */
    public function commentLikes(): HasMany
    {
        return $this->hasMany(CommentLike::class);
    }

    /**
     * Get the subscription for the user.
     */
    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class);
    }

    /**
     * Get the thread follows created by the user.
     */
    public function threadFollows(): HasMany
    {
        return $this->hasMany(ThreadFollow::class);
    }

    /**
     * Get the followed threads for the user.
     */
    public function followedThreads(): BelongsToMany
    {
        return $this->belongsToMany(Thread::class, 'thread_follows')
            ->withTimestamps();
    }

    /**
     * Lấy màu badge theo role
     *
     * @return string
     */
    public function getRoleColor(): string
    {
        return match ($this->role) {
            'admin' => 'danger',
            'moderator' => 'warning',
            'senior' => 'info',
            'member' => 'primary',
            'guest' => 'secondary',
            default => 'secondary'
        };
    }

    /**
     * Lấy tên hiển thị của role
     *
     * @return string
     */
    public function getRoleDisplayName(): string
    {
        return match ($this->role) {
            'admin' => 'Admin',
            'moderator' => 'Moderator',
            'senior' => 'Senior Member',
            'member' => 'Member',
            'guest' => 'Guest',
            default => 'Unknown'
        };
    }

    /**
     * Kiểm tra có permission không (tương thích với Spatie)
     *
     * @param string $permission
     * @return bool
     */
    public function hasPermission($permission): bool
    {
        // Admin có tất cả quyền
        if ($this->role === 'admin') {
            return true;
        }

        // Kiểm tra qua Spatie Permission nếu có cài đặt
        if (method_exists($this, 'hasPermissionTo')) {
            return $this->hasPermissionTo($permission);
        }

        // Fallback: Moderator có một số quyền cơ bản
        if ($this->role === 'moderator') {
            $moderatorPermissions = [
                'view_dashboard',
                'view_reports',
                'manage_users',
                'ban_users',
                'view_user_details',
                'manage_posts',
                'moderate_content',
                'manage_comments',
                'manage_categories',
                'send_notifications'
            ];
            return in_array($permission, $moderatorPermissions);
        }

        return false;
    }

    /**
     * Lấy tất cả permissions (tương thích với Spatie)
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllPermissions()
    {
        // Nếu có Spatie Permission
        if (method_exists($this, 'getPermissionsViaRoles')) {
            return $this->getPermissionsViaRoles();
        }

        // Fallback: Trả về collection rỗng hoặc permissions mặc định
        return collect([]);
    }
}
