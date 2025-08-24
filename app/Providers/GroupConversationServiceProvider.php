<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\GroupConversationService;
use App\Services\GroupApprovalService;
use App\Services\GroupPermissionService;
use App\Services\UnifiedNotificationService;

class GroupConversationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(GroupPermissionService::class, function ($app) {
            return new GroupPermissionService();
        });

        $this->app->singleton(GroupConversationService::class, function ($app) {
            return new GroupConversationService();
        });

        $this->app->singleton(GroupApprovalService::class, function ($app) {
            return new GroupApprovalService(
                $app->make(GroupConversationService::class),
                $app->make(UnifiedNotificationService::class)
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
