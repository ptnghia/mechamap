<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class VerifyStripeWebhook
{
    /**
     * Handle an incoming request.
     * Verify Stripe webhook signature before processing
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Chỉ áp dụng cho Stripe webhook endpoint
        if ($request->is('api/payment/webhook')) {
            $signature = $request->header('Stripe-Signature');

            if (!$signature) {
                Log::warning('Stripe webhook attempt without signature', [
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);

                return response()->json([
                    'error' => 'Missing Stripe-Signature header',
                    'message' => 'Webhook signature verification required'
                ], 400);
            }

            // Rate limiting for webhook endpoint
            $key = 'webhook_attempts:' . $request->ip();
            $attempts = cache()->get($key, 0);

            if ($attempts > 10) { // Max 10 attempts per minute
                Log::warning('Stripe webhook rate limit exceeded', [
                    'ip' => $request->ip(),
                    'attempts' => $attempts,
                ]);

                return response()->json([
                    'error' => 'Rate limit exceeded',
                    'message' => 'Too many webhook attempts'
                ], 429);
            }

            cache()->put($key, $attempts + 1, 60); // 1 minute expiry
        }

        return $next($request);
    }
}
