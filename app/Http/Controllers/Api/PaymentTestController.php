<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\StripeService;
use App\Services\VNPayService;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

/**
 * Payment Testing Controller
 * Development and testing utilities for payment system
 */
class PaymentTestController extends Controller
{
    private StripeService $stripeService;
    private VNPayService $vnpayService;

    public function __construct(
        StripeService $stripeService,
        VNPayService $vnpayService
    ) {
        $this->stripeService = $stripeService;
        $this->vnpayService = $vnpayService;

        // Only allow in development environment
        if (!app()->environment(['local', 'development', 'testing'])) {
            abort(404);
        }
    }

    /**
     * Test payment gateway configurations
     */
    public function testConfigurations(): JsonResponse
    {
        $results = [];

        // Test Stripe configuration
        $results['stripe'] = [
            'configured' => $this->stripeService->isConfigured(),
            'connection' => null,
        ];

        if ($results['stripe']['configured']) {
            $results['stripe']['connection'] = $this->stripeService->testConnection();
        }

        // Test VNPay configuration
        $results['vnpay'] = [
            'configured' => $this->vnpayService->isConfigured(),
        ];

        return response()->json([
            'success' => true,
            'data' => $results,
            'message' => 'Payment gateway configurations tested'
        ]);
    }

    /**
     * Create test order for payment testing
     */
    public function createTestOrder(Request $request): JsonResponse
    {
        try {
            // Get or create test user
            $user = User::where('email', 'test@mechamap.com')->first();
            if (!$user) {
                $user = User::create([
                    'name' => 'Test User',
                    'email' => 'test@mechamap.com',
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                ]);
            }

            // Create test order
            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => $request->input('amount', 50000), // 50,000 VND default
                'currency' => 'VND',
                'status' => 'pending',
                'payment_method' => null,
                'order_data' => [
                    'test_order' => true,
                    'created_for_testing' => now()->toDateTimeString(),
                ],
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'order_id' => $order->id,
                    'user_id' => $user->id,
                    'amount' => $order->total_amount,
                    'currency' => $order->currency,
                ],
                'message' => 'Test order created successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to create test order', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create test order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test Stripe payment intent creation
     */
    public function testStripePayment(Request $request): JsonResponse
    {
        if (!$this->stripeService->isConfigured()) {
            return response()->json([
                'success' => false,
                'message' => 'Stripe not configured'
            ], 400);
        }

        try {
            $orderId = $request->input('order_id');
            $order = Order::findOrFail($orderId);

            $paymentData = $this->stripeService->createPaymentIntent($order);

            return response()->json([
                'success' => true,
                'data' => $paymentData,
                'message' => 'Stripe payment intent created for testing'
            ]);

        } catch (\Exception $e) {
            Log::error('Stripe payment test failed', [
                'error' => $e->getMessage(),
                'order_id' => $request->input('order_id'),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Stripe payment test failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test VNPay payment URL generation
     */
    public function testVNPayPayment(Request $request): JsonResponse
    {
        if (!$this->vnpayService->isConfigured()) {
            return response()->json([
                'success' => false,
                'message' => 'VNPay not configured'
            ], 400);
        }

        try {
            $orderId = $request->input('order_id');
            $order = Order::findOrFail($orderId);
            $clientIp = $request->ip();

            $paymentData = $this->vnpayService->createPaymentUrl($order, $clientIp);

            return response()->json([
                'success' => true,
                'data' => $paymentData,
                'message' => 'VNPay payment URL created for testing'
            ]);

        } catch (\Exception $e) {
            Log::error('VNPay payment test failed', [
                'error' => $e->getMessage(),
                'order_id' => $request->input('order_id'),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'VNPay payment test failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Simulate webhook events for testing
     */
    public function simulateWebhook(Request $request): JsonResponse
    {
        $eventType = $request->input('event_type', 'payment_intent.succeeded');
        $orderId = $request->input('order_id');

        try {
            $order = Order::findOrFail($orderId);

            // Create mock webhook payload
            $mockPayload = json_encode([
                'id' => 'evt_' . uniqid(),
                'object' => 'event',
                'type' => $eventType,
                'data' => [
                    'object' => [
                        'id' => 'pi_' . uniqid(),
                        'object' => 'payment_intent',
                        'amount' => $order->total_amount,
                        'currency' => strtolower($order->currency),
                        'status' => $eventType === 'payment_intent.succeeded' ? 'succeeded' : 'failed',
                        'metadata' => [
                            'order_id' => $order->id,
                        ],
                    ],
                ],
                'created' => time(),
            ]);

            // Create mock signature (this won't pass real verification)
            $mockSignature = 't=' . time() . ',v1=' . hash_hmac('sha256', $mockPayload, 'test_signature');

            return response()->json([
                'success' => true,
                'data' => [
                    'payload' => $mockPayload,
                    'signature' => $mockSignature,
                    'event_type' => $eventType,
                    'order_id' => $order->id,
                ],
                'message' => 'Mock webhook data generated',
                'note' => 'This is for testing only - use real webhook endpoints for actual payment processing'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to simulate webhook: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clean up test data
     */
    public function cleanupTestData(): JsonResponse
    {
        try {
            $deletedOrders = Order::where('order_data->test_order', true)->delete();
            $testUser = User::where('email', 'test@mechamap.com')->first();

            if ($testUser) {
                $userOrdersCount = $testUser->orders()->count();
                if ($userOrdersCount === 0) {
                    $testUser->delete();
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'deleted_orders' => $deletedOrders,
                    'test_user_deleted' => $testUser && $userOrdersCount === 0,
                ],
                'message' => 'Test data cleaned up successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clean up test data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payment system status
     */
    public function getSystemStatus(): JsonResponse
    {
        $status = [
            'environment' => app()->environment(),
            'stripe' => [
                'configured' => $this->stripeService->isConfigured(),
                'webhook_secret_set' => !empty(config('services.stripe.webhook_secret')),
            ],
            'vnpay' => [
                'configured' => $this->vnpayService->isConfigured(),
            ],
            'database' => [
                'orders_table' => \Schema::hasTable('orders'),
                'order_items_table' => \Schema::hasTable('order_items'),
            ],
            'cache' => [
                'working' => $this->testCache(),
            ],
        ];

        // Test database connectivity
        try {
            $orderCount = Order::count();
            $status['database']['accessible'] = true;
            $status['database']['order_count'] = $orderCount;
        } catch (\Exception $e) {
            $status['database']['accessible'] = false;
            $status['database']['error'] = $e->getMessage();
        }

        return response()->json([
            'success' => true,
            'data' => $status,
            'message' => 'Payment system status retrieved'
        ]);
    }

    /**
     * Test cache functionality
     */
    private function testCache(): bool
    {
        try {
            $testKey = 'payment_cache_test_' . time();
            $testValue = 'test_value';

            \Cache::put($testKey, $testValue, 60);
            $retrieved = \Cache::get($testKey);
            \Cache::forget($testKey);

            return $retrieved === $testValue;
        } catch (\Exception $e) {
            return false;
        }
    }
}
