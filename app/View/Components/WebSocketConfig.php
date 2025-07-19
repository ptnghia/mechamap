<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Config;

class WebSocketConfig extends Component
{
    /**
     * WebSocket server URL
     *
     * @var string
     */
    public $serverUrl;

    /**
     * WebSocket server host
     *
     * @var string
     */
    public $serverHost;

    /**
     * WebSocket server port
     *
     * @var int
     */
    public $serverPort;

    /**
     * Whether to use secure WebSocket (WSS)
     *
     * @var bool
     */
    public $secure;

    /**
     * Laravel API URL
     *
     * @var string
     */
    public $laravelUrl;

    /**
     * Auto-initialize WebSocket connection
     *
     * @var bool
     */
    public $autoInit;

    /**
     * Configuration JSON string
     *
     * @var string
     */
    public $configJson;

    /**
     * Create a new component instance.
     *
     * @param bool $autoInit
     * @return void
     */
    public function __construct($autoInit = true)
    {
        try {
            $this->autoInit = $autoInit;

            // Auto-detect environment based on domain
            $detectedEnv = $this->detectEnvironment();
            $envConfig = Config::get("websocket.environments.{$detectedEnv}", []);

            // Set server URL based on detected environment or config
            $this->serverUrl = Config::get('websocket.server.url') ?? $envConfig['server_url'] ?? 'http://localhost:3000';
            $this->serverHost = Config::get('websocket.server.host') ?? parse_url($this->serverUrl, PHP_URL_HOST) ?? 'localhost';
            $this->serverPort = Config::get('websocket.server.port') ?? (parse_url($this->serverUrl, PHP_URL_SCHEME) === 'https' ? 443 : 3000);
            $this->secure = Config::get('websocket.server.secure') ?? (parse_url($this->serverUrl, PHP_URL_SCHEME) === 'https');

            // Set Laravel URL
            $this->laravelUrl = Config::get('app.url');

            // Set configJson after all properties are set
            $this->configJson = $this->generateConfigJson();
        } catch (\Exception $e) {
            // Fallback values if configuration fails
            \Log::error('WebSocketConfig constructor error: ' . $e->getMessage());

            $this->autoInit = $autoInit;
            $this->serverUrl = 'http://localhost:3000';
            $this->serverHost = 'localhost';
            $this->serverPort = 3000;
            $this->secure = false;
            $this->laravelUrl = config('app.url', 'http://localhost');

            // Set fallback configJson
            $this->configJson = $this->generateConfigJson();
        }
    }

    /**
     * Generate configuration JSON using current property values
     */
    private function generateConfigJson(): string
    {
        try {
            $config = [
                'server_url' => $this->serverUrl ?? 'https://realtime.mechamap.com',
                'server_host' => $this->serverHost ?? 'realtime.mechamap.com',
                'server_port' => $this->serverPort ?? 443,
                'secure' => $this->secure ?? true,
                'laravel_url' => $this->laravelUrl ?? config('app.url'),
                'environment' => app()->environment(),
                'auto_init' => $this->autoInit ?? true,
            ];

            return json_encode($config);
        } catch (\Exception $e) {
            \Log::error('generateConfigJson error: ' . $e->getMessage());

            // Return minimal fallback config
            return json_encode([
                'server_url' => 'https://realtime.mechamap.com',
                'server_host' => 'realtime.mechamap.com',
                'server_port' => 443,
                'secure' => true,
                'laravel_url' => config('app.url', 'https://mechamap.com'),
                'environment' => 'production',
                'auto_init' => true,
                'error' => true
            ]);
        }
    }

    /**
     * Auto-detect environment based on current domain
     */
    private function detectEnvironment(): string
    {
        $currentUrl = Config::get('app.url');
        $domain = parse_url($currentUrl, PHP_URL_HOST);

        $domainMapping = Config::get('websocket.domain_mapping', []);

        return $domainMapping[$domain] ?? app()->environment();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.websocket-config');
    }

    /**
     * Get WebSocket configuration as JSON
     *
     * @return string
     */
    public function configJson()
    {
        try {
            $config = [
                'server_url' => $this->serverUrl ?? 'http://localhost:3000',
                'server_host' => $this->serverHost ?? 'localhost',
                'server_port' => $this->serverPort ?? 3000,
                'secure' => $this->secure ?? false,
                'laravel_url' => $this->laravelUrl ?? config('app.url'),
                'environment' => app()->environment(),
                'auto_init' => $this->autoInit ?? true,
            ];

            return json_encode($config);
        } catch (\Exception $e) {
            // Fallback configuration if there's an error
            \Log::error('WebSocketConfig configJson error: ' . $e->getMessage());

            return json_encode([
                'server_url' => 'http://localhost:3000',
                'server_host' => 'localhost',
                'server_port' => 3000,
                'secure' => false,
                'laravel_url' => config('app.url'),
                'environment' => app()->environment(),
                'auto_init' => true,
                'error' => true
            ]);
        }
    }
}
