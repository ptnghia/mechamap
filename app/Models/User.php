<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use App\Models\Showcase;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

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

    /**
     * Get the showcase items of the user.
     */
    public function showcaseItems(): HasMany
    {
        return $this->hasMany(Showcase::class);
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
}
