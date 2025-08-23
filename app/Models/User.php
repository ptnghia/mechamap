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
use App\Notifications\CustomVerifyEmail;
use App\Models\Showcase;
use App\Models\Country;
use App\Models\Region;
use App\Models\MarketplaceSeller;
use App\Models\UserFollow;
use App\Models\Achievement;
use App\Models\UserAchievement;
// use Spatie\Permission\Traits\HasRoles; // Temporarily disabled

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
    use HasApiTokens, HasFactory, Notifiable; // HasRoles temporarily disabled

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
        // Business fields
        'company_name',
        'business_license',
        'tax_code',
        'business_description',
        'business_categories',
        'business_phone',
        'business_email',
        'business_address',
        'is_verified_business',
        'business_verified_at',
        'verified_at',
        'verified_by',
        'verification_notes',
        'verification_documents',
        'subscription_level',
        'business_rating',
        'total_reviews',
        // New role system fields
        'role_group',
        'role_permissions',
        'role_updated_at',
        // Localization and notification preferences
        'locale',
        'email_notifications_enabled',
        'browser_notifications_enabled',
        'marketing_emails_enabled',
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
            'business_verified_at' => 'datetime',
            'password' => 'hashed',
            'work_locations' => 'array',
            'expertise_regions' => 'array',
            'business_categories' => 'array',
            'notification_preferences' => 'array',
            'is_verified_business' => 'boolean',
            'email_notifications_enabled' => 'boolean',
            'business_rating' => 'decimal:2',
            'role_permissions' => 'array',
            'role_updated_at' => 'datetime',
        ];
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Tự động gán avatar khi tạo user mới
        static::creating(function ($user) {
            if (empty($user->avatar)) {
                $user->assignDefaultAvatar();
            }
        });
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
     * Check if user has any of the specified roles
     *
     * @param array $roles
     * @return bool
     */
    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles);
    }

    /**
     * Check if user is an admin (any admin level)
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return in_array($this->role, ['super_admin', 'system_admin', 'content_admin']);
    }

    /**
     * Check if user is a moderator (any moderator level)
     *
     * @return bool
     */
    public function isModerator(): bool
    {
        return in_array($this->role, ['content_moderator', 'marketplace_moderator', 'community_moderator']);
    }

    /**
     * Check if user is super admin
     *
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Check if user is system management level
     *
     * @return bool
     */
    public function isSystemManagement(): bool
    {
        return $this->role_group === 'system_management';
    }

    /**
     * Check if user is community management level
     *
     * @return bool
     */
    public function isCommunityManagement(): bool
    {
        return $this->role_group === 'community_management';
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
     * Check if user is a supplier
     *
     * @return bool
     */
    public function isSupplier(): bool
    {
        return $this->role === 'supplier';
    }

    /**
     * Check if user is a manufacturer
     *
     * @return bool
     */
    public function isManufacturer(): bool
    {
        return $this->role === 'manufacturer';
    }

    /**
     * Check if user is a brand
     *
     * @return bool
     */
    public function isBrand(): bool
    {
        return $this->role === 'brand';
    }

    /**
     * Check if user is a guest
     *
     * @return bool
     */
    public function isGuest(): bool
    {
        return $this->role === 'guest';
    }

    /**
     * Check if user is a business account (supplier, manufacturer, brand, verified_partner)
     *
     * @return bool
     */
    public function isBusiness(): bool
    {
        return $this->role_group === 'business_partners';
    }

    /**
     * Check if user can access admin panel
     * Kiểm tra dựa trên multiple roles hoặc role_group
     *
     * @return bool
     */
    public function canAccessAdmin(): bool
    {
        // Kiểm tra theo role_group cũ (backward compatibility)
        if (in_array($this->role_group, ['system_management', 'community_management'])) {
            return true;
        }

        // Kiểm tra theo multiple roles mới
        $adminRoles = [
            'super_admin', 'system_admin', 'content_admin',
            'content_moderator', 'marketplace_moderator', 'community_moderator'
        ];

        return $this->hasAnyActiveRole($adminRoles);
    }

    /**
     * Kiểm tra user có bất kỳ role nào trong danh sách không
     *
     * @param array $roleNames
     * @return bool
     */
    public function hasAnyActiveRole(array $roleNames): bool
    {
        return $this->activeRoles()
            ->whereIn('roles.name', $roleNames)
            ->exists();
    }

    /**
     * Kiểm tra user có role cụ thể không
     *
     * @param string $roleName
     * @return bool
     */
    public function hasActiveRole(string $roleName): bool
    {
        return $this->activeRoles()
            ->where('roles.name', $roleName)
            ->exists();
    }

    /**
     * ✅ UNIFIED: Lấy tất cả permissions từ multiple roles
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllPermissionsFromRoles()
    {
        $permissions = collect();

        // Load roles nếu chưa load
        if (!$this->relationLoaded('roles')) {
            $this->load(['roles.permissions']);
        }

        foreach ($this->activeRoles as $role) {
            if ($role->permissions) {
                $rolePermissions = $role->permissions->where('pivot.is_granted', true);
                $permissions = $permissions->merge($rolePermissions);
            }
        }

        return $permissions->unique('id');
    }

    /**
     * Check if user can access system admin features
     *
     * @return bool
     */
    public function canAccessSystemAdmin(): bool
    {
        return in_array($this->role, ['super_admin', 'system_admin']);
    }

    /**
     * Check if user can access content admin features
     *
     * @return bool
     */
    public function canAccessContentAdmin(): bool
    {
        return in_array($this->role, ['super_admin', 'system_admin', 'content_admin']);
    }

    /**
     * Check if user can access marketplace admin features
     *
     * @return bool
     */
    public function canAccessMarketplaceAdmin(): bool
    {
        return in_array($this->role, ['super_admin', 'system_admin', 'marketplace_moderator']);
    }

    /**
     * Get display name (name or username)
     *
     * @return string
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->name ?: $this->username ?: 'Người dùng';
    }

    /**
     * Get showcases count
     *
     * @return int
     */
    public function getShowcasesCountAttribute(): int
    {
        return $this->showcaseItems()->count();
    }

    /**
     * Get ratings count
     *
     * @return int
     */
    public function getRatingsCountAttribute(): int
    {
        return $this->showcaseRatings()->count();
    }

    /**
     * Get avatar URL with proper fallback logic
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

        // Kiểm tra avatar do user upload
        if ($this->avatar) {
            // Nếu là URL đầy đủ (http/https)
            if (strpos($this->avatar, 'http') === 0) {
                return $this->avatar;
            }

            // Xử lý đường dẫn local
            $avatarPath = $this->avatar;

            // Nếu avatar path bắt đầu bằng /images/ thì dùng asset() trực tiếp
            if (strpos($avatarPath, '/images/') === 0) {
                $fullPath = public_path(ltrim($avatarPath, '/'));
                if (file_exists($fullPath)) {
                    return asset($avatarPath);
                }
            } else {
                // Loại bỏ slash đầu để tránh double slash
                $cleanPath = ltrim($avatarPath, '/');
                $fullPath = public_path('images/' . $cleanPath);
                if (file_exists($fullPath)) {
                    return asset('images/' . $cleanPath);
                }
            }
        }

        // Fallback: Sử dụng avatar generator mới với initials
        $name = $this->name ?: $this->username ?: $this->email;
        $initials = strtoupper(substr($name, 0, 1));

        // Nếu có khoảng trắng, lấy chữ cái đầu của từ đầu và cuối
        if (strpos($name, ' ') !== false) {
            $nameParts = array_filter(explode(' ', trim($name))); // Loại bỏ khoảng trắng thừa
            if (count($nameParts) >= 2) {
                $firstInitial = strtoupper(substr($nameParts[0], 0, 1));
                $lastInitial = strtoupper(substr(end($nameParts), 0, 1));
                $initials = $firstInitial . $lastInitial;
            }
        }

        return route('avatar.generate', ['initial' => $initials]);
    }

    /**
     * Gán avatar mặc định cho user
     */
    public function assignDefaultAvatar(): void
    {
        // Tạo initials từ tên
        $name = $this->name ?: $this->username ?: $this->email;
        $initials = strtoupper(substr($name, 0, 1));

        // Nếu có khoảng trắng, lấy chữ cái đầu của từ đầu và cuối
        if (strpos($name, ' ') !== false) {
            $nameParts = array_filter(explode(' ', trim($name))); // Loại bỏ khoảng trắng thừa
            if (count($nameParts) >= 2) {
                $firstInitial = strtoupper(substr($nameParts[0], 0, 1));
                $lastInitial = strtoupper(substr(end($nameParts), 0, 1));
                $initials = $firstInitial . $lastInitial;
            }
        }

        // Gán avatar URL (sẽ được tạo khi cần)
        $this->avatar = route('avatar.generate', ['initial' => $initials]);
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
            $oldPath = public_path('images/' . ltrim($this->avatar, '/'));
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        // Store new avatar in public/images/users/avatars/
        $filename = time() . '_' . $file->getClientOriginalName();
        $destinationPath = public_path('images/users/avatars');

        // Tạo thư mục nếu chưa tồn tại
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        $file->move($destinationPath, $filename);
        $this->avatar = 'users/avatars/' . $filename;

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
     * Get the user's verification documents.
     */
    public function verificationDocuments(): HasMany
    {
        return $this->hasMany(UserVerificationDocument::class);
    }

    /**
     * Get the user's primary verification documents.
     */
    public function primaryVerificationDocuments(): HasMany
    {
        return $this->hasMany(UserVerificationDocument::class)->where('is_primary', true);
    }

    /**
     * Get documents verified by this user (for admins).
     */
    public function verifiedDocuments(): HasMany
    {
        return $this->hasMany(UserVerificationDocument::class, 'verified_by');
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
     * Unified logic: user is online if last_seen_at is within 15 minutes
     */
    public function isOnline(): bool
    {
        return $this->last_seen_at && $this->last_seen_at->gt(now()->subMinutes(15));
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
        return $this->belongsToMany(User::class, 'user_follows', 'follower_id', 'following_id')
            ->withTimestamps()
            ->withPivot('followed_at');
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
     * 👑 Relationship với roles (many-to-many)
     * User có thể có nhiều roles
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_has_roles')
            ->withPivot([
                'is_primary',
                'assigned_at',
                'expires_at',
                'assigned_by',
                'assignment_reason',
                'assignment_conditions',
                'is_active',
                'deactivated_at',
                'deactivated_by'
            ])
            ->withTimestamps();
    }

    /**
     * Lấy role chính của user
     */
    public function primaryRole(): BelongsToMany
    {
        return $this->roles()->wherePivot('is_primary', true)->wherePivot('is_active', true);
    }

    /**
     * Lấy tất cả roles đang hoạt động
     */
    public function activeRoles(): BelongsToMany
    {
        return $this->roles()->wherePivot('is_active', true)
            ->where(function($query) {
                $query->whereNull('user_has_roles.expires_at')
                      ->orWhere('user_has_roles.expires_at', '>', now());
            });
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
     * Get the alerts for the user (deprecated - use userNotifications).
     * @deprecated Use userNotifications() instead
     */
    public function alerts(): HasMany
    {
        // Redirect to userNotifications for backward compatibility
        return $this->userNotifications();
    }

    /**
     * Get the notifications for the user (Phase 3 notifications).
     */
    public function userNotifications(): HasMany
    {
        return $this->hasMany(\App\Models\Notification::class);
    }

    /**
     * Get unread notifications for the user.
     */
    public function unreadNotifications(): HasMany
    {
        return $this->hasMany(\App\Models\Notification::class)->where('is_read', false);
    }

    /**
     * Get notification count for the user.
     */
    public function getUnreadNotificationCountAttribute(): int
    {
        return $this->unreadNotifications()->count();
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
     * Get the seller earnings for the user.
     */
    public function sellerEarnings(): HasMany
    {
        return $this->hasMany(SellerEarning::class, 'seller_id');
    }

    /**
     * Get the technical products for the user.
     */
    public function technicalProducts(): HasMany
    {
        return $this->hasMany(TechnicalProduct::class, 'user_id');
    }

    /**
     * Get the marketplace seller profile for the user.
     */
    public function marketplaceSeller(): HasOne
    {
        return $this->hasOne(MarketplaceSeller::class, 'user_id');
    }

    /**
     * Get the user's favorite companies.
     */
    public function favoriteCompanies(): BelongsToMany
    {
        return $this->belongsToMany(MarketplaceSeller::class, 'user_favorite_companies', 'user_id', 'marketplace_seller_id')
                    ->withTimestamps();
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
     * Get the showcase items created by the user.
     */
    public function showcaseItems(): HasMany
    {
        return $this->hasMany(Showcase::class);
    }

    /**
     * Get the showcase ratings created by the user.
     */
    public function showcaseRatings(): HasMany
    {
        return $this->hasMany(\App\Models\ShowcaseRating::class);
    }

    /**
     * Lấy màu badge theo role mới
     *
     * @return string
     */
    public function getRoleColor(): string
    {
        return match ($this->role) {
            // System Management
            'super_admin' => 'danger',
            'system_admin' => 'warning',
            'content_admin' => 'info',

            // Community Management
            'content_moderator' => 'primary',
            'marketplace_moderator' => 'success',
            'community_moderator' => 'dark',

            // Community Members
            'senior_member' => 'info',
            'member' => 'primary',
            'guest' => 'secondary',


            // Business Partners
            'manufacturer' => 'dark',
            'supplier' => 'success',
            'brand' => 'purple',
            'verified_partner' => 'gold',

            default => 'secondary'
        };
    }

    /**
     * Lấy tên hiển thị của role mới
     *
     * @return string
     */
    public function getRoleDisplayName(): string
    {
        return match ($this->role) {
            // System Management
            'super_admin' => 'Super Admin',
            'system_admin' => 'System Admin',
            'content_admin' => 'Content Admin',

            // Community Management
            'content_moderator' => 'Content Moderator',
            'marketplace_moderator' => 'Marketplace Moderator',
            'community_moderator' => 'Community Moderator',

            // Community Members
            'senior_member' => 'Thành viên cấp cao',
            'member' => 'Thành viên',
            'guest' => 'Khách',


            // Business Partners
            'manufacturer' => 'Nhà sản xuất',
            'supplier' => 'Nhà cung cấp',
            'brand' => 'Nhãn hàng',
            'verified_partner' => 'Đối tác xác thực',

            default => 'Không xác định'
        };
    }

    /**
     * Lấy tên nhóm role
     *
     * @return string
     */
    public function getRoleGroupDisplayName(): string
    {
        return match ($this->role_group) {
            'system_management' => 'Quản lý hệ thống',
            'community_management' => 'Quản lý cộng đồng',
            'community_members' => 'Thành viên cộng đồng',
            'business_partners' => 'Đối tác kinh doanh',
            default => 'Không xác định'
        };
    }

    /**
     * Kiểm tra có permission không (sử dụng PermissionService)
     * Fallback to old PermissionService if exists
     *
     * @param string $permission
     * @return bool
     */
    public function hasPermissionOld($permission): bool
    {
        if (class_exists('\App\Services\PermissionService')) {
            return \App\Services\PermissionService::hasPermission($this, $permission);
        }
        return false;
    }

    /**
     * Kiểm tra có thể truy cập marketplace không
     *
     * @return bool
     */
    public function canAccessMarketplace(): bool
    {
        return \App\Services\PermissionService::canAccessMarketplace($this);
    }

    /**
     * Kiểm tra có thể bán hàng không
     *
     * @return bool
     */
    public function canSell(): bool
    {
        return \App\Services\PermissionService::canSell($this);
    }

    /**
     * Kiểm tra có thể mua hàng không
     *
     * @return bool
     */
    public function canBuy(): bool
    {
        return \App\Services\PermissionService::canBuy($this);
    }

    /**
     * Kiểm tra có thể mua bất kỳ loại sản phẩm nào không
     * Dùng để hiển thị giỏ hàng trên header
     *
     * @return bool
     */
    public function canBuyAnyProduct(): bool
    {
        // Kiểm tra qua MarketplacePermissionService
        $allowedBuyTypes = \App\Services\MarketplacePermissionService::getAllowedBuyTypes($this->role ?? 'guest');
        return !empty($allowedBuyTypes);
    }

    /**
     * Kiểm tra có thể bán bất kỳ loại sản phẩm nào không
     * Dùng để hiển thị marketplace menu trên header
     *
     * @return bool
     */
    public function canSellAnyProduct(): bool
    {
        // Kiểm tra qua MarketplacePermissionService
        $allowedSellTypes = \App\Services\MarketplacePermissionService::getAllowedSellTypes($this->role ?? 'guest');
        return !empty($allowedSellTypes);
    }

    /**
     * Kiểm tra business đã được verify chưa
     *
     * @return bool
     */
    public function isVerifiedBusiness(): bool
    {
        return $this->isBusiness() && $this->is_verified_business;
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

    /**
     * ✅ HYBRID: Kiểm tra user có permission cụ thể không (Roles + Custom Permissions)
     *
     * @param string $permission
     * @return bool
     */
    public function hasPermission(string $permission): bool
    {
        // Super Admin có tất cả quyền - ALWAYS return true first
        if ($this->role === 'super_admin') {
            return true;
        }

        // System Admin có hầu hết quyền
        if ($this->role === 'system_admin') {
            return true;
        }

        // ✅ HYBRID: Check permissions từ multiple roles system
        if ($this->hasPermissionViaRoles($permission)) {
            return true;
        }

        // ✅ HYBRID: Check custom permissions (legacy system as supplement)
        if ($this->hasCustomPermission($permission)) {
            return true;
        }

        // Final fallback: Admin role có basic permissions
        if ($this->role === 'admin') {
            $basicAdminPermissions = [
                'view_dashboard', 'view_users', 'view_reports', 'moderate-content',
                'approve-content', 'manage-categories', 'manage-forums', 'view_products',
                'view_orders', 'manage_sellers'
            ];
            return in_array($permission, $basicAdminPermissions);
        }

        return false;
    }

    /**
     * ✅ HYBRID: Check custom permissions (supplemental to roles)
     *
     * @param string $permission
     * @return bool
     */
    public function hasCustomPermission(string $permission): bool
    {
        // Check cached custom permissions
        if ($this->role_permissions && is_array($this->role_permissions)) {
            return in_array($permission, $this->role_permissions);
        }

        return false;
    }

    /**
     * ✅ NEW: Check permission via multiple roles system
     *
     * @param string $permission
     * @return bool
     */
    public function hasPermissionViaRoles(string $permission): bool
    {
        // Kiểm tra user có active roles không
        if (!$this->relationLoaded('roles')) {
            $this->load('roles');
        }

        if (!$this->roles || $this->roles->count() === 0) {
            return false;
        }

        // Check permission trong tất cả active roles
        return $this->activeRoles()
            ->whereHas('permissions', function($query) use ($permission) {
                $query->where('permissions.name', $permission)
                      ->where('role_has_permissions.is_granted', true);
            })->exists();
    }

    /**
     * ✅ HYBRID: Get all permissions combined (roles + custom)
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllCombinedPermissions()
    {
        $permissions = collect();

        // Get permissions from roles
        $rolePermissions = $this->getAllPermissionsFromRoles();
        $permissions = $permissions->merge($rolePermissions);

        // Get custom permissions
        if ($this->role_permissions && is_array($this->role_permissions)) {
            $customPermissions = \App\Models\Permission::whereIn('name', $this->role_permissions)->get();
            $permissions = $permissions->merge($customPermissions);
        }

        return $permissions->unique('id');
    }

    /**
     * ✅ HYBRID: Cache permissions từ roles (không override custom permissions)
     *
     * @return void
     */
    public function cachePermissions(): void
    {
        // Chỉ cache permissions từ roles, giữ nguyên custom permissions
        $rolePermissions = $this->getAllPermissionsFromRoles()->pluck('name')->toArray();

        // Merge với existing custom permissions nếu có
        $existingCustom = $this->role_permissions && is_array($this->role_permissions)
            ? $this->role_permissions
            : [];

        $allPermissions = array_unique(array_merge($rolePermissions, $existingCustom));

        $this->update([
            'role_permissions' => $allPermissions,
            'role_updated_at' => now()
        ]);
    }

    /**
     * ✅ HYBRID: Refresh cached permissions từ multiple roles
     *
     * @return void
     */
    public function refreshPermissions(): void
    {
        $this->cachePermissions();
    }

    /**
     * ✅ HYBRID: Get permissions breakdown
     *
     * @return array
     */
    public function getPermissionsBreakdown(): array
    {
        $rolePermissions = $this->getAllPermissionsFromRoles()->pluck('name')->toArray();
        $customPermissions = $this->role_permissions && is_array($this->role_permissions)
            ? array_diff($this->role_permissions, $rolePermissions)
            : [];

        return [
            'role_permissions' => $rolePermissions,
            'custom_permissions' => $customPermissions,
            'total_permissions' => array_unique(array_merge($rolePermissions, $customPermissions))
        ];
    }

    /**
     * Kiểm tra user có bất kỳ permission nào trong danh sách không
     *
     * @param array $permissions
     * @return bool
     */
    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Kiểm tra user có tất cả permissions trong danh sách không
     *
     * @param array $permissions
     * @return bool
     */
    public function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Lấy tất cả permissions của user theo role
     *
     * @return array
     */
    public function getUserPermissions(): array
    {
        $permissions = config('admin_permissions.permissions');
        $userPermissions = [];

        foreach ($permissions as $group => $groupPermissions) {
            foreach ($groupPermissions as $permission => $config) {
                if ($config[$this->role] ?? false) {
                    $userPermissions[] = $permission;
                }
            }
        }

        return $userPermissions;
    }

    /**
     * Kiểm tra user có thể truy cập route không
     *
     * @param string $routeName
     * @return bool
     */
    public function canAccessRoute(string $routeName): bool
    {
        // Super Admin và System Admin có thể truy cập tất cả routes
        if (in_array($this->role, ['super_admin', 'system_admin', 'admin'])) {
            return true;
        }

        $routePermissions = config('admin_permissions.route_permissions');

        // Kiểm tra exact match trước
        if (isset($routePermissions[$routeName])) {
            return $this->hasPermission($routePermissions[$routeName]);
        }

        // Kiểm tra wildcard patterns
        foreach ($routePermissions as $pattern => $permission) {
            if (str_contains($pattern, '*')) {
                $regex = str_replace('*', '.*', $pattern);
                if (preg_match('/^' . $regex . '$/', $routeName)) {
                    return $this->hasPermission($permission);
                }
            }
        }

        // Fallback: Nếu user có quyền admin cơ bản thì cho phép truy cập
        if ($this->canAccessAdmin()) {
            return true;
        }

        return false;
    }

    /**
     * Laravel native compatible method for checking permissions
     * This replaces Spatie Permission's hasPermissionTo() method
     */
    public function hasPermissionTo($permission)
    {
        // Use Laravel native Gate system
        return $this->can($permission);
    }



    /**
     * Check if this user is following another user
     */
    public function isFollowing(User $user): bool
    {
        return UserFollow::isFollowing($this->id, $user->id);
    }

    /**
     * Follow another user
     */
    public function follow(User $user): bool
    {
        if ($this->id === $user->id) {
            return false; // Can't follow yourself
        }

        if ($this->isFollowing($user)) {
            return false; // Already following
        }

        UserFollow::create([
            'follower_id' => $this->id,
            'following_id' => $user->id,
            'followed_at' => now(),
        ]);

        return true;
    }

    /**
     * Unfollow another user
     */
    public function unfollow(User $user): bool
    {
        return UserFollow::where('follower_id', $this->id)
            ->where('following_id', $user->id)
            ->delete() > 0;
    }

    /**
     * Get followers count
     */
    public function getFollowersCountAttribute(): int
    {
        return UserFollow::getFollowersCount($this->id);
    }

    /**
     * Get following count
     */
    public function getFollowingCountAttribute(): int
    {
        return UserFollow::getFollowingCount($this->id);
    }

    /**
     * Get user achievements
     */
    public function achievements()
    {
        return $this->belongsToMany(Achievement::class, 'user_achievements')
            ->withPivot(['unlocked_at', 'progress_data', 'current_progress', 'target_progress', 'is_notified'])
            ->withTimestamps();
    }

    /**
     * Get user achievement records
     */
    public function userAchievements()
    {
        return $this->hasMany(UserAchievement::class);
    }

    /**
     * Get total achievement points
     */
    public function getTotalAchievementPointsAttribute(): int
    {
        return $this->achievements()->sum('points');
    }

    /**
     * Get achievement count by rarity
     */
    public function getAchievementsByRarity(): array
    {
        return $this->achievements()
            ->selectRaw('rarity, COUNT(*) as count')
            ->groupBy('rarity')
            ->pluck('count', 'rarity')
            ->toArray();
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail);
    }

    /**
     * Send the password reset notification.
     *
     * @param string $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\CustomResetPassword($token));
    }
}
