<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class GenerateWebSocketApiKey extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'websocket:generate-api-key 
                            {--show : Show the generated key}
                            {--env= : Environment (development|production)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate API key for WebSocket server authentication';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $environment = $this->option('env') ?? app()->environment();
        $showKey = $this->option('show');

        // Generate secure API key
        $apiKey = 'mechamap_ws_' . Str::random(64);
        
        // Store in cache for verification
        $cacheKey = "websocket_api_key_{$environment}";
        Cache::put($cacheKey, hash('sha256', $apiKey), now()->addYears(1));

        // Display information
        $this->info("WebSocket API Key generated for environment: {$environment}");
        $this->line('');
        
        if ($showKey) {
            $this->warn('🔑 API Key (copy this to Node.js .env file):');
            $this->line($apiKey);
        } else {
            $this->warn('🔑 API Key (use --show to display):');
            $this->line('Hidden for security');
        }
        
        $this->line('');
        $this->info('📋 Next steps:');
        $this->line('1. Copy the API key to Node.js .env file:');
        $this->line("   LARAVEL_API_KEY={$apiKey}");
        $this->line('');
        $this->line('2. Add to Laravel .env file:');
        $this->line("   WEBSOCKET_API_KEY_HASH=" . hash('sha256', $apiKey));
        $this->line('');
        $this->line('3. Restart both Laravel and Node.js servers');

        return Command::SUCCESS;
    }
}
