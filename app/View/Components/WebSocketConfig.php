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
     * Create a new component instance.
     *
     * @param bool $autoInit
     * @return void
     */
    public function __construct($autoInit = true)
    {
        $this->autoInit = $autoInit;

        // Auto-detect environment based on domain
        $detectedEnv = $this->detectEnvironment();
        $envConfig = Config::get("websocket.environments.{$detectedEnv}", []);

        // Set server URL based on detected environment or config
        $this->serverUrl = Config::get('websocket.server.url') ?? $envConfig['server_url'];
        $this->serverHost = Config::get('websocket.server.host') ?? parse_url($this->serverUrl, PHP_URL_HOST);
        $this->serverPort = Config::get('websocket.server.port') ?? (parse_url($this->serverUrl, PHP_URL_SCHEME) === 'https' ? 443 : 3000);
        $this->secure = Config::get('websocket.server.secure') ?? (parse_url($this->serverUrl, PHP_URL_SCHEME) === 'https');

        // Set Laravel URL
        $this->laravelUrl = Config::get('app.url');
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
        $config = [
            'server_url' => $this->serverUrl,
            'server_host' => $this->serverHost,
            'server_port' => $this->serverPort,
            'secure' => $this->secure,
            'laravel_url' => $this->laravelUrl,
            'environment' => app()->environment(),
            'auto_init' => $this->autoInit,
        ];

        return json_encode($config);
    }
}
