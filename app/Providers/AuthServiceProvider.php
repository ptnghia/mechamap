<?php

namespace App\Providers;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        \App\Models\Thread::class => \App\Policies\ThreadPolicy::class,
        \App\Models\Comment::class => \App\Policies\CommentPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        $this->registerGates();
    }

    /**
     * Register authorization gates.
     */
    private function registerGates(): void
    {
        // Admin Dashboard Access
        \Illuminate\Support\Facades\Gate::define('access-admin', function ($user) {
            return in_array($user->role, [
                'super_admin', 'system_admin', 'content_admin',
                'admin', 'moderator', 'content_moderator',
                'marketplace_moderator', 'community_moderator'
            ]);
        });

        // System Management (Full Admin Access)
        \Illuminate\Support\Facades\Gate::define('system-management', function ($user) {
            return in_array($user->role, ['super_admin', 'system_admin', 'content_admin']);
        });

        // Community Management (Limited Admin Access)
        \Illuminate\Support\Facades\Gate::define('community-management', function ($user) {
            return in_array($user->role, [
                'admin', 'moderator', 'content_moderator',
                'marketplace_moderator', 'community_moderator'
            ]);
        });

        // Business Partner Access
        \Illuminate\Support\Facades\Gate::define('business-partner', function ($user) {
            return in_array($user->role, ['supplier', 'manufacturer', 'brand', 'verified_partner']);
        });

        // Marketplace Access
        \Illuminate\Support\Facades\Gate::define('marketplace-access', function ($user) {
            return in_array($user->role, [
                'supplier', 'manufacturer', 'brand', 'verified_partner',
                'senior_member', 'member'
            ]);
        });

        // Content Moderation
        \Illuminate\Support\Facades\Gate::define('moderate-content', function ($user) {
            return in_array($user->role, [
                'super_admin', 'system_admin', 'content_admin',
                'admin', 'moderator', 'content_moderator'
            ]);
        });

        // Marketplace Moderation
        \Illuminate\Support\Facades\Gate::define('moderate-marketplace', function ($user) {
            return in_array($user->role, [
                'super_admin', 'system_admin', 'content_admin',
                'admin', 'moderator', 'marketplace_moderator'
            ]);
        });

        // User Management
        \Illuminate\Support\Facades\Gate::define('manage-users', function ($user) {
            return in_array($user->role, ['super_admin', 'system_admin', 'admin']);
        });

        // System Settings
        \Illuminate\Support\Facades\Gate::define('manage-settings', function ($user) {
            return in_array($user->role, ['super_admin', 'system_admin']);
        });

        // Additional Gates for existing permissions
        \Illuminate\Support\Facades\Gate::define('view-users', function ($user) {
            return in_array($user->role, ['super_admin', 'system_admin', 'content_admin', 'admin']);
        });

        \Illuminate\Support\Facades\Gate::define('manage-all-users', function ($user) {
            return in_array($user->role, ['super_admin', 'system_admin', 'admin']);
        });

        \Illuminate\Support\Facades\Gate::define('verify-business-accounts', function ($user) {
            return in_array($user->role, ['super_admin', 'system_admin', 'content_admin', 'admin']);
        });

        \Illuminate\Support\Facades\Gate::define('manage-user-roles', function ($user) {
            return in_array($user->role, ['super_admin', 'system_admin', 'admin']);
        });

        \Illuminate\Support\Facades\Gate::define('view-analytics', function ($user) {
            return in_array($user->role, [
                'super_admin', 'system_admin', 'content_admin', 'admin',
                'moderator', 'marketplace_moderator'
            ]);
        });

        \Illuminate\Support\Facades\Gate::define('view-system-logs', function ($user) {
            return in_array($user->role, ['super_admin', 'system_admin']);
        });

        \Illuminate\Support\Facades\Gate::define('manage-content', function ($user) {
            return in_array($user->role, [
                'super_admin', 'system_admin', 'content_admin', 'admin',
                'moderator', 'content_moderator'
            ]);
        });

        \Illuminate\Support\Facades\Gate::define('manage-community', function ($user) {
            return in_array($user->role, [
                'super_admin', 'system_admin', 'content_admin', 'admin',
                'moderator', 'community_moderator'
            ]);
        });

        \Illuminate\Support\Facades\Gate::define('approve-products', function ($user) {
            return in_array($user->role, [
                'super_admin', 'system_admin', 'content_admin', 'admin',
                'moderator', 'marketplace_moderator'
            ]);
        });

        \Illuminate\Support\Facades\Gate::define('manage-seller-accounts', function ($user) {
            return in_array($user->role, [
                'super_admin', 'system_admin', 'content_admin', 'admin',
                'moderator', 'marketplace_moderator'
            ]);
        });

        \Illuminate\Support\Facades\Gate::define('view-marketplace-analytics', function ($user) {
            return in_array($user->role, [
                'super_admin', 'system_admin', 'content_admin', 'admin',
                'moderator', 'marketplace_moderator'
            ]);
        });

        \Illuminate\Support\Facades\Gate::define('manage-commissions', function ($user) {
            return in_array($user->role, ['super_admin', 'system_admin', 'admin']);
        });

        \Illuminate\Support\Facades\Gate::define('export-data', function ($user) {
            return in_array($user->role, [
                'super_admin', 'system_admin', 'content_admin', 'admin'
            ]);
        });

        \Illuminate\Support\Facades\Gate::define('view-reports', function ($user) {
            return in_array($user->role, [
                'super_admin', 'system_admin', 'content_admin', 'admin',
                'moderator'
            ]);
        });

        \Illuminate\Support\Facades\Gate::define('access-b2b-features', function ($user) {
            return in_array($user->role, ['supplier', 'manufacturer', 'brand', 'verified_partner']);
        });

        \Illuminate\Support\Facades\Gate::define('sell-products', function ($user) {
            return in_array($user->role, ['supplier', 'manufacturer']);
        });

        \Illuminate\Support\Facades\Gate::define('view-content', function ($user) {
            return !in_array($user->role, ['guest']) || auth()->check();
        });

        // Legacy Gates for existing moderation system
        \Illuminate\Support\Facades\Gate::define('flag-thread', function ($user, $thread) {
            return app(\App\Policies\ModerationPolicy::class)->flagThread($user, $thread);
        });

        \Illuminate\Support\Facades\Gate::define('flag-comment', function ($user, $comment) {
            return app(\App\Policies\ModerationPolicy::class)->flagComment($user, $comment);
        });

        \Illuminate\Support\Facades\Gate::define('mark-solution', function ($user, $comment) {
            return app(\App\Policies\ModerationPolicy::class)->markCommentAsSolution($user, $comment);
        });

        \Illuminate\Support\Facades\Gate::define('bump-thread', function ($user, $thread) {
            return app(\App\Policies\ModerationPolicy::class)->bumpThread($user, $thread);
        });

        // Đăng ký provider tùy chỉnh cho guard admin
        Auth::provider('admin-users', function ($app, array $config) {
            // Lấy hasher instance
            $hasher = $app->make('hash');

            // Trả về một instance của Illuminate\Contracts\Auth\UserProvider...
            return new class($hasher, $config) extends EloquentUserProvider {
                public function validateCredentials(Authenticatable $user, array $credentials)
                {
                    $plain = $credentials['password'];

                    // Kiểm tra mật khẩu
                    return Hash::check($plain, $user->getAuthPassword());
                }

                // Helper method to get the first credential key
                protected function firstCredentialKey(array $credentials)
                {
                    foreach ($credentials as $key => $value) {
                        return $key;
                    }
                    return null;
                }

                public function retrieveByCredentials(array $credentials)
                {
                    if (
                        empty($credentials) ||
                        (count($credentials) === 1 &&
                            $this->firstCredentialKey($credentials) &&
                            str_contains($this->firstCredentialKey($credentials), 'password'))
                    ) {
                        return;
                    }

                    // Xác định trường đăng nhập (email hoặc username)
                    if (isset($credentials['email'])) {
                        $loginField = 'email';
                        $loginValue = $credentials['email'];
                    } elseif (isset($credentials['username'])) {
                        $loginField = 'username';
                        $loginValue = $credentials['username'];
                    } else {
                        return;
                    }

                    // Tạo query
                    $query = $this->newModelQuery();

                    // Tìm người dùng theo email hoặc username
                    $query->where($loginField, $loginValue);

                    return $query->first();
                }
            };
        });
    }
}
