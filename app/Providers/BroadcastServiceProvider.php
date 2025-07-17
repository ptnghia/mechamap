<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;
use App\Broadcasting\NodejsBroadcaster;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Broadcast::routes();

        // Register custom Node.js broadcaster
        Broadcast::extend('nodejs', function ($app, $config) {
            return new NodejsBroadcaster($config);
        });

        require base_path('routes/channels.php');
    }
}
