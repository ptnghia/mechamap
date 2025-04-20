<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
        'avatar',
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
            'password' => 'hashed',
        ];
    }

    /**
     * Check if user has a specific role
     *
     * @param string $role
     * @return bool
     */
    public function hasRole(string $role): bool
    {
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
            return $this->avatar;
        }

        $firstLetter = strtoupper(substr($this->username ?: $this->name, 0, 1));
        return "https://ui-avatars.com/api/?name={$firstLetter}&background=random&color=fff";
    }

    /**
     * Get the social accounts for the user.
     */
    public function socialAccounts(): HasMany
    {
        return $this->hasMany(SocialAccount::class);
    }
}
