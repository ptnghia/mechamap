<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EnvironmentDetectionService;
use Illuminate\Support\Facades\Http;

class ValidateWebSocketConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'websocket:validate-config
                            {--test-connection : Test actual connection to WebSocket server}
                            {--show-config : Show full configuration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Validate WebSocket configuration for current environment';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Validating WebSocket Configuration');
        $this->line('');

        // Detect environment
        $detectedEnv = EnvironmentDetectionService::detectEnvironment();
        $this->info("📍 Detected Environment: {$detectedEnv}");
        $this->line('');

        // Validate configuration
        $validation = EnvironmentDetectionService::validateConfig();

        if ($validation['valid']) {
            $this->info('✅ Configuration is valid');
        } else {
            $this->error('❌ Configuration has issues:');
            foreach ($validation['issues'] as $issue) {
                $this->line("   • {$issue}");
            }
            $this->line('');
        }

        // Show configuration if requested
        if ($this->option('show-config')) {
            $this->showConfiguration($validation['config']);
        }

        // Test connection if requested
        if ($this->option('test-connection')) {
            $this->testConnection($validation['config']);
        }

        // Show Node.js configuration
        $this->showNodeJsConfig();

        return $validation['valid'] ? Command::SUCCESS : Command::FAILURE;
    }

    /**
     * Show current configuration
     */
    private function showConfiguration(array $config): void
    {
        $this->line('');
        $this->info('📋 Current Configuration:');
        $this->table(
            ['Setting', 'Value'],
            [
                ['Environment', $config['environment']],
                ['WebSocket URL', $config['server_url']],
                ['WebSocket Host', $config['server_host']],
                ['WebSocket Port', $config['server_port']],
                ['Secure (SSL)', $config['secure'] ? 'Yes' : 'No'],
                ['Laravel URL', $config['laravel_url']],
                ['CORS Origins', implode(', ', $config['cors_origins'])],
                ['API Key Hash', EnvironmentDetectionService::getApiKeyHash() ? 'Configured' : 'Not Set'],
            ]
        );
    }

    /**
     * Test connection to WebSocket server
     */
    private function testConnection(array $config): void
    {
        $this->line('');
        $this->info('🔗 Testing WebSocket Server Connection...');

        $healthUrl = rtrim($config['server_url'], '/') . '/api/health';

        try {
            $response = Http::timeout(10)->get($healthUrl);

            if ($response->successful()) {
                $data = $response->json();
                $this->info('✅ WebSocket server is responding');
                $this->line("   Status: " . (isset($data['status']) ? $data['status'] : 'unknown'));
                $this->line("   Environment: " . (isset($data['environment']) ? $data['environment'] : 'unknown'));
                $this->line("   Version: " . (isset($data['version']) ? $data['version'] : 'unknown'));
            } else {
                $this->error("❌ WebSocket server returned HTTP " . $response->status());
            }
        } catch (\Exception $e) {
            $this->error('❌ Failed to connect to WebSocket server');
            $this->line("   Error: {$e->getMessage()}");
            $this->line("   URL: {$healthUrl}");
        }
    }

    /**
     * Show Node.js configuration
     */
    private function showNodeJsConfig(): void
    {
        $this->line('');
        $this->info('⚙️  Node.js Environment Variables:');

        $nodeConfig = EnvironmentDetectionService::getNodeJsConfig();

        foreach ($nodeConfig as $key => $value) {
            $this->line("   {$key}={$value}");
        }

        $this->line('');
        $this->comment('💡 Copy these to your Node.js .env file');
    }
}
