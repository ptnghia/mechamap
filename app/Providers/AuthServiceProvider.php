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
