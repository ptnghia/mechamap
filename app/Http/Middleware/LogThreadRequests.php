<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogThreadRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Log thread-related requests for debugging
        if ($request->is('ajax/threads/*')) {
            Log::info('Thread AJAX Request', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'route_params' => $request->route()?->parameters(),
                'user_id' => auth()->id(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        $response = $next($request);

        // Log response for thread requests
        if ($request->is('ajax/threads/*')) {
            Log::info('Thread AJAX Response', [
                'url' => $request->fullUrl(),
                'status' => $response->getStatusCode(),
                'response_size' => strlen($response->getContent()),
            ]);
        }

        return $response;
    }
}
