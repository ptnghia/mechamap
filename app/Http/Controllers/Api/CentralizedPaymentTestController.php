<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CentralizedPayment;
use App\Models\MarketplaceOrder;
use App\Models\PaymentAuditLog;
use App\Services\CentralizedPaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

/**
 * 🧪 Centralized Payment Test Controller
 * 
 * Testing endpoints cho centralized payment system
 * Development và debugging purposes only
 */
class CentralizedPaymentTestController extends Controller
{
    protected CentralizedPaymentService $centralizedPaymentService;

    public function __construct(CentralizedPaymentService $centralizedPaymentService)
    {
        $this->centralizedPaymentService = $centralizedPaymentService;
    }

    /**
     * Test system configuration
     */
    public function testConfiguration(): JsonResponse
    {
        try {
            $config = [
                'stripe_configured' => $this->centralizedPaymentService->isStripeConfigured(),
                'sepay_configured' => $this->centralizedPaymentService->isSePayConfigured(),
                'available_methods' => $this->centralizedPaymentService->getAvailablePaymentMethods(),
                'environment' => app()->environment(),
                'timestamp' => now()->toISOString(),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Centralized payment system configuration',
                'data' => $config
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Configuration test failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create test order for payment testing
     */
    public function createTestOrder(Request $request): JsonResponse
    {
        $request->validate([
            'customer_id' => 'required|exists:users,id',
            'total_amount' => 'required|numeric|min:1000',
            'items' => 'required|array|min:1',
        ]);

        try {
            // Create test marketplace order
            $order = MarketplaceOrder::create([
                'uuid' => \Illuminate\Support\Str::uuid(),
                'order_number' => 'TEST-' . time(),
                'customer_id' => $request->customer_id,
                'customer_email' => \App\Models\User::find($request->customer_id)->email,
                'status' => 'pending',
                'payment_status' => 'pending',
                'subtotal' => $request->total_amount,
                'tax_amount' => 0,
                'shipping_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => $request->total_amount,
                'currency' => 'VND',
                'shipping_address' => [
                    'name' => 'Test Customer',
                    'address' => 'Test Address',
                    'city' => 'Ho Chi Minh City',
                    'country' => 'Vietnam'
                ],
                'billing_address' => [
                    'name' => 'Test Customer',
                    'address' => 'Test Address',
                    'city' => 'Ho Chi Minh City',
                    'country' => 'Vietnam'
                ],
                'metadata' => [
                    'test_order' => true,
                    'created_by' => 'centralized_payment_test'
                ]
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Test order created successfully',
                'data' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'total_amount' => $order->total_amount,
                    'customer_id' => $order->customer_id,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create test order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test Stripe payment creation
     */
    public function testStripePayment(Request $request): JsonResponse
    {
        $request->validate([
            'order_id' => 'required|exists:marketplace_orders,id',
        ]);

        try {
            $order = MarketplaceOrder::findOrFail($request->order_id);

            // Create centralized payment
            $centralizedPayment = $this->centralizedPaymentService->createPayment($order, 'stripe');

            // Create Stripe Payment Intent
            $result = $this->centralizedPaymentService->createStripePaymentIntent($centralizedPayment);

            return response()->json([
                'success' => $result['success'],
                'message' => $result['success'] ? 'Stripe payment test successful' : 'Stripe payment test failed',
                'data' => [
                    'centralized_payment_id' => $centralizedPayment->id,
                    'payment_reference' => $centralizedPayment->payment_reference,
                    'stripe_result' => $result,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Stripe payment test failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test SePay payment creation
     */
    public function testSePayPayment(Request $request): JsonResponse
    {
        $request->validate([
            'order_id' => 'required|exists:marketplace_orders,id',
        ]);

        try {
            $order = MarketplaceOrder::findOrFail($request->order_id);

            // Create centralized payment
            $centralizedPayment = $this->centralizedPaymentService->createPayment($order, 'sepay');

            // Create SePay payment
            $result = $this->centralizedPaymentService->createSePayPayment($centralizedPayment);

            return response()->json([
                'success' => $result['success'],
                'message' => $result['success'] ? 'SePay payment test successful' : 'SePay payment test failed',
                'data' => [
                    'centralized_payment_id' => $centralizedPayment->id,
                    'payment_reference' => $centralizedPayment->payment_reference,
                    'sepay_result' => $result,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'SePay payment test failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Simulate Stripe webhook
     */
    public function simulateStripeWebhook(Request $request): JsonResponse
    {
        $request->validate([
            'centralized_payment_id' => 'required|exists:centralized_payments,id',
            'event_type' => 'required|in:payment_intent.succeeded,payment_intent.payment_failed,payment_intent.canceled',
        ]);

        try {
            $centralizedPayment = CentralizedPayment::findOrFail($request->centralized_payment_id);

            // Create mock Stripe event
            $mockPaymentIntent = (object) [
                'id' => $centralizedPayment->gateway_payment_intent_id ?? 'pi_test_' . time(),
                'amount' => $centralizedPayment->gross_amount,
                'metadata' => (object) [
                    'centralized_payment_id' => $centralizedPayment->id,
                    'order_id' => $centralizedPayment->order_id,
                ],
                'toArray' => function() use ($centralizedPayment) {
                    return [
                        'id' => $centralizedPayment->gateway_payment_intent_id ?? 'pi_test_' . time(),
                        'amount' => $centralizedPayment->gross_amount,
                        'status' => 'succeeded',
                        'metadata' => [
                            'centralized_payment_id' => $centralizedPayment->id,
                            'order_id' => $centralizedPayment->order_id,
                        ]
                    ];
                }
            ];

            // Simulate webhook processing
            switch ($request->event_type) {
                case 'payment_intent.succeeded':
                    $result = $this->simulatePaymentIntentSucceeded($mockPaymentIntent);
                    break;
                case 'payment_intent.payment_failed':
                    $result = $this->simulatePaymentIntentFailed($mockPaymentIntent);
                    break;
                case 'payment_intent.canceled':
                    $result = $this->simulatePaymentIntentCanceled($mockPaymentIntent);
                    break;
                default:
                    throw new \Exception('Unsupported event type');
            }

            return response()->json([
                'success' => true,
                'message' => 'Stripe webhook simulation completed',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Stripe webhook simulation failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Simulate SePay webhook
     */
    public function simulateSePayWebhook(Request $request): JsonResponse
    {
        $request->validate([
            'centralized_payment_id' => 'required|exists:centralized_payments,id',
            'status' => 'required|in:success,failed,cancelled',
        ]);

        try {
            $centralizedPayment = CentralizedPayment::findOrFail($request->centralized_payment_id);

            // Create mock SePay webhook data
            $mockWebhookData = [
                'transaction_ref' => $centralizedPayment->gateway_transaction_id ?? 'MECHAMAP-' . $centralizedPayment->id . '-' . time(),
                'amount' => $centralizedPayment->gross_amount,
                'status' => $request->status,
                'bank_code' => 'MBBank',
                'account_number' => '0903252427001',
                'timestamp' => now()->toISOString(),
            ];

            // Simulate webhook processing
            $webhookController = new \App\Http\Controllers\Api\CentralizedSePayWebhookController();
            $mockRequest = new Request($mockWebhookData);
            
            $result = $webhookController->handleWebhook($mockRequest);

            return response()->json([
                'success' => true,
                'message' => 'SePay webhook simulation completed',
                'data' => [
                    'webhook_data' => $mockWebhookData,
                    'webhook_result' => $result->getData(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'SePay webhook simulation failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get system status
     */
    public function getSystemStatus(): JsonResponse
    {
        try {
            $stats = [
                'total_centralized_payments' => CentralizedPayment::count(),
                'completed_payments' => CentralizedPayment::where('status', 'completed')->count(),
                'pending_payments' => CentralizedPayment::where('status', 'pending')->count(),
                'failed_payments' => CentralizedPayment::where('status', 'failed')->count(),
                'total_audit_logs' => PaymentAuditLog::count(),
                'recent_payments' => CentralizedPayment::latest()->take(5)->get(['id', 'payment_reference', 'status', 'gross_amount', 'created_at']),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Centralized payment system status',
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get system status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cleanup test data
     */
    public function cleanupTestData(): JsonResponse
    {
        try {
            // Delete test orders and related data
            $testOrders = MarketplaceOrder::whereJsonContains('metadata->test_order', true)->get();
            
            foreach ($testOrders as $order) {
                // Delete related centralized payments
                CentralizedPayment::where('order_id', $order->id)->delete();
                
                // Delete related audit logs
                PaymentAuditLog::where('entity_type', 'centralized_payment')
                    ->whereIn('entity_id', function($query) use ($order) {
                        $query->select('id')
                              ->from('centralized_payments')
                              ->where('order_id', $order->id);
                    })->delete();
                
                // Delete the order
                $order->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Test data cleaned up successfully',
                'data' => [
                    'deleted_orders' => $testOrders->count(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cleanup test data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Helper methods for webhook simulation
    private function simulatePaymentIntentSucceeded($paymentIntent): array
    {
        $centralizedPaymentId = $paymentIntent->metadata->centralized_payment_id;
        $centralizedPayment = CentralizedPayment::find($centralizedPaymentId);
        
        if ($centralizedPayment) {
            $centralizedPayment->markAsCompleted($paymentIntent->toArray());
        }
        
        return ['status' => 'succeeded', 'centralized_payment_id' => $centralizedPaymentId];
    }

    private function simulatePaymentIntentFailed($paymentIntent): array
    {
        $centralizedPaymentId = $paymentIntent->metadata->centralized_payment_id;
        $centralizedPayment = CentralizedPayment::find($centralizedPaymentId);
        
        if ($centralizedPayment) {
            $centralizedPayment->markAsFailed('Simulated payment failure', $paymentIntent->toArray());
        }
        
        return ['status' => 'failed', 'centralized_payment_id' => $centralizedPaymentId];
    }

    private function simulatePaymentIntentCanceled($paymentIntent): array
    {
        $centralizedPaymentId = $paymentIntent->metadata->centralized_payment_id;
        $centralizedPayment = CentralizedPayment::find($centralizedPaymentId);
        
        if ($centralizedPayment) {
            $centralizedPayment->update([
                'status' => 'cancelled',
                'gateway_response' => $paymentIntent->toArray(),
            ]);
        }
        
        return ['status' => 'cancelled', 'centralized_payment_id' => $centralizedPaymentId];
    }
}
