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
        'verified_by',
        'subscription_level',
        'business_rating',
        'total_reviews',
        // New role system fields
        'role_group',
        'role_permissions',
        'role_updated_at',
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
            'is_verified_business' => 'boolean',
            'business_rating' => 'decimal:2',
            'role_permissions' => 'array',
            'role_updated_at' => 'datetime',
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
     *
     * @return bool
     */
    public function canAccessAdmin(): bool
    {
        return in_array($this->role_group, ['system_management', 'community_management']);
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
            // Nếu avatar path bắt đầu bằng /images/ thì dùng asset() trực tiếp
            if (strpos($this->avatar, '/images/') === 0) {
                return asset($this->avatar);
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
            'student' => 'light',

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
            'student' => 'Sinh viên',

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
     * Kiểm tra user có permission cụ thể không (sử dụng Spatie Permission)
     *
     * @param string $permission
     * @return bool
     */
    public function hasPermission(string $permission): bool
    {
        // Debug for view_products permission
        if ($permission === 'view_products') {
            \Log::info('hasPermission debug', [
                'user_id' => $this->id,
                'user_role' => $this->role,
                'permission' => $permission,
                'is_super_admin' => $this->role === 'super_admin',
                'role_permissions_count' => is_array($this->role_permissions) ? count($this->role_permissions) : 'not_array',
            ]);
        }

        // Super Admin có tất cả quyền - ALWAYS return true first
        if ($this->role === 'super_admin') {
            if ($permission === 'view_products') {
                \Log::info('Super admin returning true for view_products');
            }
            return true;
        }

        // System Admin có hầu hết quyền
        if ($this->role === 'system_admin') {
            return true;
        }

        // Fallback: Kiểm tra cached permissions trước
        if ($this->role_permissions && is_array($this->role_permissions)) {
            return in_array($permission, $this->role_permissions);
        }

        // Sử dụng Spatie Permission nếu có (cuối cùng)
        if (method_exists($this, 'hasPermissionTo')) {
            try {
                return $this->hasPermissionTo($permission);
            } catch (\Exception $e) {
                // Nếu Spatie Permission lỗi, fallback về false
                return false;
            }
        }

        return false;
    }

    /**
     * Cache permissions cho user
     *
     * @return void
     */
    public function cachePermissions(): void
    {
        if (method_exists($this, 'getAllPermissions')) {
            $permissions = $this->getAllPermissions()->pluck('name')->toArray();
            $this->update([
                'role_permissions' => $permissions,
                'role_updated_at' => now()
            ]);
        }
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
}
