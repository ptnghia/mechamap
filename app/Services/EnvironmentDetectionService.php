<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class EnvironmentDetectionService
{
    /**
     * Detect environment based on current domain and configuration
     */
    public static function detectEnvironment(): string
    {
        $currentUrl = Config::get('app.url');
        $domain = parse_url($currentUrl, PHP_URL_HOST);
        
        $domainMapping = Config::get('websocket.domain_mapping', [
            'mechamap.test' => 'local',
            'localhost' => 'local',
            '127.0.0.1' => 'local',
            'mechamap.com' => 'production',
            'www.mechamap.com' => 'production',
        ]);
        
        $detectedEnv = $domainMapping[$domain] ?? app()->environment();
        
        Log::info('Environment detected', [
            'domain' => $domain,
            'detected_env' => $detectedEnv,
            'app_env' => app()->environment(),
            'app_url' => $currentUrl
        ]);
        
        return $detectedEnv;
    }

    /**
     * Get WebSocket configuration based on detected environment
     */
    public static function getWebSocketConfig(): array
    {
        $detectedEnv = self::detectEnvironment();
        
        // Get environment-specific config
        $envConfig = Config::get("websocket.environments.{$detectedEnv}", []);
        
        // Get base config with fallbacks
        $config = [
            'server_url' => Config::get('websocket.server.url') ?? $envConfig['server_url'] ?? 'http://localhost:3000',
            'server_host' => Config::get('websocket.server.host') ?? $envConfig['server_host'] ?? 'localhost',
            'server_port' => Config::get('websocket.server.port') ?? $envConfig['server_port'] ?? 3000,
            'secure' => Config::get('websocket.server.secure') ?? $envConfig['secure'] ?? false,
            'laravel_url' => Config::get('app.url'),
            'environment' => $detectedEnv,
            'cors_origins' => $envConfig['cors_origins'] ?? [],
        ];
        
        // Auto-detect secure based on URL scheme
        if (!isset($envConfig['secure'])) {
            $config['secure'] = parse_url($config['server_url'], PHP_URL_SCHEME) === 'https';
        }
        
        // Auto-detect port based on URL
        if (!isset($envConfig['server_port'])) {
            $urlPort = parse_url($config['server_url'], PHP_URL_PORT);
            $config['server_port'] = $urlPort ?? ($config['secure'] ? 443 : 3000);
        }
        
        // Auto-detect host based on URL
        if (!isset($envConfig['server_host'])) {
            $config['server_host'] = parse_url($config['server_url'], PHP_URL_HOST) ?? 'localhost';
        }
        
        return $config;
    }

    /**
     * Get CORS origins for current environment
     */
    public static function getCorsOrigins(): array
    {
        $config = self::getWebSocketConfig();
        
        $origins = $config['cors_origins'] ?? [];
        
        // Always include current Laravel URL
        $laravelUrl = Config::get('app.url');
        if (!in_array($laravelUrl, $origins)) {
            $origins[] = $laravelUrl;
        }
        
        return $origins;
    }

    /**
     * Check if current environment is production
     */
    public static function isProduction(): bool
    {
        $detectedEnv = self::detectEnvironment();
        return $detectedEnv === 'production' || app()->environment('production');
    }

    /**
     * Check if current environment is local/development
     */
    public static function isLocal(): bool
    {
        $detectedEnv = self::detectEnvironment();
        return $detectedEnv === 'local' || app()->environment('local');
    }

    /**
     * Get API key hash for current environment
     */
    public static function getApiKeyHash(): ?string
    {
        $detectedEnv = self::detectEnvironment();
        
        // Try environment-specific hash first
        $hash = Config::get("websocket.api_key_hash_{$detectedEnv}");
        
        if (!$hash) {
            // Fallback to general hash
            $hash = Config::get('websocket.api_key_hash') ?? env('WEBSOCKET_API_KEY_HASH');
        }
        
        return $hash;
    }

    /**
     * Get environment-specific configuration for Node.js
     */
    public static function getNodeJsConfig(): array
    {
        $config = self::getWebSocketConfig();
        
        return [
            'NODE_ENV' => self::isProduction() ? 'production' : 'development',
            'LARAVEL_API_URL' => $config['laravel_url'],
            'CORS_ORIGIN' => implode(',', $config['cors_origins']),
            'SSL_ENABLED' => $config['secure'] ? 'true' : 'false',
            'PORT' => $config['server_port'],
            'HOST' => $config['server_host'],
        ];
    }

    /**
     * Validate environment configuration
     */
    public static function validateConfig(): array
    {
        $config = self::getWebSocketConfig();
        $issues = [];
        
        // Check required configurations
        if (empty($config['server_url'])) {
            $issues[] = 'WebSocket server URL is not configured';
        }
        
        if (empty($config['laravel_url'])) {
            $issues[] = 'Laravel URL is not configured';
        }
        
        // Check API key hash
        $apiKeyHash = self::getApiKeyHash();
        if (empty($apiKeyHash)) {
            $issues[] = 'WebSocket API key hash is not configured';
        }
        
        // Check CORS origins
        if (empty($config['cors_origins'])) {
            $issues[] = 'CORS origins are not configured';
        }
        
        // Check URL accessibility (basic validation)
        if (!filter_var($config['server_url'], FILTER_VALIDATE_URL)) {
            $issues[] = 'WebSocket server URL is not a valid URL';
        }
        
        if (!filter_var($config['laravel_url'], FILTER_VALIDATE_URL)) {
            $issues[] = 'Laravel URL is not a valid URL';
        }
        
        return [
            'valid' => empty($issues),
            'issues' => $issues,
            'config' => $config
        ];
    }
}
