<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductionSecurity
{
    /**
     * Handle an incoming request for production security
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only apply in production environment
        if (env('APP_ENV') !== 'production') {
            return $response;
        }

        // Force HTTPS redirect
        if (config('production.ssl.force_https', true) && !$request->secure() && $request->header('X-Forwarded-Proto') !== 'https') {
            return redirect()->secure($request->getRequestUri(), 301);
        }

        // Add security headers
        $this->addSecurityHeaders($response);

        // Add HSTS header
        $this->addHSTSHeader($response);

        // Add CSP header
        $this->addCSPHeader($response);

        return $response;
    }

    /**
     * Add security headers to response
     */
    private function addSecurityHeaders(Response $response): void
    {
        if (!config('production.security.headers.enabled')) {
            return;
        }

        $headers = [
            'X-Frame-Options' => config('production.security.headers.x_frame_options', 'DENY'),
            'X-Content-Type-Options' => config('production.security.headers.x_content_type_options', 'nosniff'),
            'X-XSS-Protection' => config('production.security.headers.x_xss_protection', '1; mode=block'),
            'Referrer-Policy' => config('production.security.headers.referrer_policy', 'strict-origin-when-cross-origin'),
            'Permissions-Policy' => 'camera=(), microphone=(), geolocation=()',
        ];

        foreach ($headers as $header => $value) {
            $response->headers->set($header, $value);
        }
    }

    /**
     * Add HSTS header for HTTPS enforcement
     */
    private function addHSTSHeader(Response $response): void
    {
        if (config('production.ssl.enabled')) {
            $maxAge = config('production.ssl.hsts_max_age', 31536000);
            $response->headers->set(
                'Strict-Transport-Security',
                "max-age={$maxAge}; includeSubDomains; preload"
            );
        }
    }

    /**
     * Add Content Security Policy header
     */
    private function addCSPHeader(Response $response): void
    {
        if (!config('production.security.headers.csp_enabled')) {
            return;
        }

        $csp = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://www.google-analytics.com https://www.googletagmanager.com https://cdnjs.cloudflare.com",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com",
            "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com",
            "img-src 'self' data: https: blob:",
            "media-src 'self' https:",
            "object-src 'none'",
            "frame-src 'self' https://www.youtube.com https://www.facebook.com",
            "connect-src 'self' https://www.google-analytics.com",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'none'",
        ];

        // Add CDN domain if configured
        if ($cdnUrl = config('production.domain.cdn')) {
            $cdnDomain = parse_url($cdnUrl, PHP_URL_HOST);
            $csp[1] .= " https://{$cdnDomain}"; // script-src
            $csp[2] .= " https://{$cdnDomain}"; // style-src
            $csp[4] .= " https://{$cdnDomain}"; // img-src
        }

        $response->headers->set('Content-Security-Policy', implode('; ', $csp));
    }
}
