<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripeService
{
    protected ?string $apiKey;
    protected ?string $webhookSecret;

    public function __construct()
    {
        $this->apiKey = config('services.stripe.secret');
        $this->webhookSecret = config('services.stripe.webhook_secret');

        if ($this->apiKey) {
            Stripe::setApiKey($this->apiKey);
        }
    }

    /**
     * Check if Stripe is configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Tạo Payment Intent cho order
     */
    public function createPaymentIntent(Order $order): array
    {
        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $this->convertToStripeAmount($order->total_amount),
                'currency' => 'vnd',
                'payment_method_types' => ['card'],
                'metadata' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'user_id' => $order->user_id,
                    'platform' => 'mechamap',
                ],
                'description' => "MechaMap Order {$order->order_number}",
                'receipt_email' => $order->user->email,
                'setup_future_usage' => 'off_session', // Cho phép save payment method
            ]);

            // Cập nhật order với payment intent
            $order->update([
                'payment_intent_id' => $paymentIntent->id,
                'payment_method' => 'stripe',
            ]);

            // Tạo transaction record
            $transaction = PaymentTransaction::create([
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'payment_method' => 'stripe',
                'payment_intent_id' => $paymentIntent->id,
                'type' => 'payment',
                'status' => 'pending',
                'amount' => $order->total_amount,
                'currency' => 'VND',
                'fee_amount' => $order->processing_fee,
                'net_amount' => $order->total_amount - $order->processing_fee,
                'gateway_response' => [
                    'payment_intent_id' => $paymentIntent->id,
                    'client_secret' => $paymentIntent->client_secret,
                    'status' => $paymentIntent->status,
                ],
            ]);

            return [
                'success' => true,
                'payment_intent_id' => $paymentIntent->id,
                'client_secret' => $paymentIntent->client_secret,
                'transaction_id' => $transaction->transaction_id,
                'amount' => $this->convertToStripeAmount($order->total_amount),
                'currency' => 'vnd',
            ];

        } catch (\Exception $e) {
            Log::error('Stripe Payment Intent creation failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ];
        }
    }

    /**
     * Confirm payment intent
     */
    public function confirmPaymentIntent(string $paymentIntentId, string $paymentMethodId = null): array
    {
        try {
            $params = [];
            if ($paymentMethodId) {
                $params['payment_method'] = $paymentMethodId;
            }

            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);
            $paymentIntent = $paymentIntent->confirm($params);

            return [
                'success' => true,
                'status' => $paymentIntent->status,
                'payment_intent' => $paymentIntent,
            ];

        } catch (\Exception $e) {
            Log::error('Stripe Payment Intent confirmation failed', [
                'payment_intent_id' => $paymentIntentId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ];
        }
    }

    /**
     * Handle Stripe Webhook
     */
    public function handleWebhook(string $payload, string $sigHeader): array
    {
        if (!$this->webhookSecret) {
            return [
                'success' => false,
                'message' => 'Webhook secret not configured'
            ];
        }

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                $this->webhookSecret
            );

            Log::info('Stripe webhook received', [
                'event_type' => $event->type,
                'event_id' => $event->id,
            ]);

            // Handle different event types
            switch ($event->type) {
                case 'payment_intent.succeeded':
                    return $this->handlePaymentIntentSucceeded($event->data->object);

                case 'payment_intent.cancelled':
                    return $this->handlePaymentIntentFailed($event->data->object);

                case 'payment_intent.canceled':
                    return $this->handlePaymentIntentCanceled($event->data->object);

                default:
                    Log::info('Unhandled Stripe webhook event type', [
                        'event_type' => $event->type
                    ]);
                    return [
                        'success' => true,
                        'message' => 'Event type not handled but acknowledged'
                    ];
            }

        } catch (SignatureVerificationException $e) {
            Log::error('Stripe webhook signature verification failed', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Invalid signature'
            ];
        } catch (\Exception $e) {
            Log::error('Stripe webhook processing error', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Webhook processing failed'
            ];
        }
    }

    /**
     * Handle successful payment intent
     */
    protected function handlePaymentIntentSucceeded($paymentIntent): array
    {
        try {
            $orderId = $paymentIntent->metadata->order_id ?? null;

            if (!$orderId) {
                Log::warning('Payment intent succeeded but no order ID in metadata', [
                    'payment_intent_id' => $paymentIntent->id
                ]);
                return ['success' => false, 'message' => 'No order ID found'];
            }

            $order = Order::find($orderId);
            if (!$order) {
                Log::error('Order not found for successful payment', [
                    'order_id' => $orderId,
                    'payment_intent_id' => $paymentIntent->id
                ]);
                return ['success' => false, 'message' => 'Order not found'];
            }

            // Update order status
            $order->update([
                'status => "completed"',
                'payment_status' => 'completed',
                'completed_at' => now(),
            ]);

            // Create payment transaction record
            PaymentTransaction::create([
                'order_id' => $order->id,
                'payment_method' => 'stripe',
                'transaction_id' => $paymentIntent->id,
                'amount' => $this->convertFromStripeAmount($paymentIntent->amount),
                'currency' => strtoupper($paymentIntent->currency),
                'status' => 'completed',
                'gateway_response' => json_encode($paymentIntent),
                'processed_at' => now(),
            ]);

            Log::info('Payment processed successfully via webhook', [
                'order_id' => $order->id,
                'payment_intent_id' => $paymentIntent->id,
                'amount' => $paymentIntent->amount
            ]);

            return [
                'success' => true,
                'message' => 'Payment processed successfully',
                'order_id' => $order->id
            ];

        } catch (\Exception $e) {
            Log::error('Error processing successful payment intent', [
                'payment_intent_id' => $paymentIntent->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Error processing payment'
            ];
        }
    }

    /**
     * Handle failed payment intent
     */
    protected function handlePaymentIntentFailed($paymentIntent): array
    {
        try {
            $orderId = $paymentIntent->metadata->order_id ?? null;

            if ($orderId) {
                $order = Order::find($orderId);
                if ($order) {
                    $order->update([
                        'status' => 'cancelled',
                        'payment_status' => 'failed',
                    ]);

                    // Create failed transaction record
                    PaymentTransaction::create([
                        'order_id' => $order->id,
                        'payment_method' => 'stripe',
                        'transaction_id' => $paymentIntent->id,
                        'amount' => $this->convertFromStripeAmount($paymentIntent->amount),
                        'currency' => strtoupper($paymentIntent->currency),
                        'status' => 'failed',
                        'gateway_response' => json_encode($paymentIntent),
                        'processed_at' => now(),
                    ]);
                }
            }

            Log::info('Payment failed via webhook', [
                'order_id' => $orderId,
                'payment_intent_id' => $paymentIntent->id,
                'last_payment_error' => $paymentIntent->last_payment_error->message ?? 'Unknown error'
            ]);

            return [
                'success' => true,
                'message' => 'Payment failure processed'
            ];

        } catch (\Exception $e) {
            Log::error('Error processing failed payment intent', [
                'payment_intent_id' => $paymentIntent->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Error processing payment failure'
            ];
        }
    }

    /**
     * Handle canceled payment intent
     */
    protected function handlePaymentIntentCanceled($paymentIntent): array
    {
        try {
            $orderId = $paymentIntent->metadata->order_id ?? null;

            if ($orderId) {
                $order = Order::find($orderId);
                if ($order) {
                    $order->update([
                        'status' => 'cancelled',
                        'payment_status' => 'cancelled',
                    ]);
                }
            }

            Log::info('Payment canceled via webhook', [
                'order_id' => $orderId,
                'payment_intent_id' => $paymentIntent->id
            ]);

            return [
                'success' => true,
                'message' => 'Payment cancellation processed'
            ];

        } catch (\Exception $e) {
            Log::error('Error processing canceled payment intent', [
                'payment_intent_id' => $paymentIntent->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Error processing payment cancellation'
            ];
        }
    }

    /**
     * Xử lý webhook từ Stripe
     */
    public function handleOldWebhook(string $payload, string $signature): array
    {
        try {
            $event = Webhook::constructEvent(
                $payload,
                $signature,
                $this->webhookSecret
            );

            Log::info('Stripe webhook received', [
                'event_type' => $event->type,
                'event_id' => $event->id,
            ]);

            switch ($event->type) {
                case 'payment_intent.succeeded':
                    return $this->handlePaymentSucceeded($event->data->object);

                case 'payment_intent.cancelled':
                    return $this->handlePaymentFailed($event->data->object);

                case 'payment_intent.canceled':
                    return $this->handlePaymentCanceled($event->data->object);

                case 'payment_method.attached':
                    return $this->handlePaymentMethodAttached($event->data->object);

                default:
                    Log::info('Unhandled Stripe webhook event', [
                        'event_type' => $event->type,
                    ]);
                    return ['success' => true, 'message' => 'Event not handled'];
            }

        } catch (SignatureVerificationException $e) {
            Log::error('Stripe webhook signature verification failed', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Invalid signature',
                'code' => 400,
            ];

        } catch (\Exception $e) {
            Log::error('Stripe webhook processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'code' => 500,
            ];
        }
    }

    /**
     * Xử lý payment thành công
     */
    protected function handlePaymentSucceeded($paymentIntent): array
    {
        $orderId = $paymentIntent->metadata->order_id ?? null;

        if (!$orderId) {
            Log::warning('Payment succeeded but no order_id in metadata', [
                'payment_intent_id' => $paymentIntent->id,
            ]);
            return ['success' => false, 'error' => 'No order ID'];
        }

        $order = Order::find($orderId);
        if (!$order) {
            Log::warning('Payment succeeded but order not found', [
                'order_id' => $orderId,
                'payment_intent_id' => $paymentIntent->id,
            ]);
            return ['success' => false, 'error' => 'Order not found'];
        }

        // Cập nhật transaction
        $transaction = PaymentTransaction::where('payment_intent_id', $paymentIntent->id)->first();
        if ($transaction) {
            $transaction->markAsCompleted([
                'stripe_charge_id' => $paymentIntent->latest_charge,
                'stripe_status' => $paymentIntent->status,
                'amount_received' => $paymentIntent->amount_received,
            ]);
        }

        // Xử lý order thành công
        $orderService = app(OrderService::class);
        $orderService->processSuccessfulPayment($order, [
            'payment_intent_id' => $paymentIntent->id,
            'transaction_id' => $transaction?->transaction_id,
            'charge_id' => $paymentIntent->latest_charge,
        ]);

        Log::info('Payment processed successfully', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'amount' => $paymentIntent->amount_received,
        ]);

        return ['success' => true, 'message' => 'Payment processed'];
    }

    /**
     * Xử lý payment thất bại
     */
    protected function handlePaymentFailed($paymentIntent): array
    {
        $orderId = $paymentIntent->metadata->order_id ?? null;

        if (!$orderId) {
            return ['success' => false, 'error' => 'No order ID'];
        }

        $order = Order::find($orderId);
        if (!$order) {
            return ['success' => false, 'error' => 'Order not found'];
        }

        // Cập nhật transaction
        $transaction = PaymentTransaction::where('payment_intent_id', $paymentIntent->id)->first();
        if ($transaction) {
            $failureReason = $paymentIntent->last_payment_error->message ?? 'Payment failed';
            $transaction->markAsFailed($failureReason, [
                'stripe_error' => $paymentIntent->last_payment_error,
                'stripe_status' => $paymentIntent->status,
            ]);
        }

        // Xử lý order thất bại
        $orderService = app(OrderService::class);
        $orderService->processFailedPayment($order, 'Payment failed via Stripe');

        Log::warning('Payment failed', [
            'order_id' => $order->id,
            'payment_intent_id' => $paymentIntent->id,
            'error' => $paymentIntent->last_payment_error->message ?? 'Unknown error',
        ]);

        return ['success' => true, 'message' => 'Payment failure processed'];
    }

    /**
     * Xử lý payment bị cancel
     */
    protected function handlePaymentCanceled($paymentIntent): array
    {
        $orderId = $paymentIntent->metadata->order_id ?? null;

        if ($orderId) {
            $order = Order::find($orderId);
            if ($order && $order->canBeCancelled()) {
                $order->cancel('Payment canceled by user');
            }
        }

        return ['success' => true, 'message' => 'Payment cancellation processed'];
    }

    /**
     * Xử lý payment method attached
     */
    protected function handlePaymentMethodAttached($paymentMethod): array
    {
        // Logic để save payment method cho user
        // Sẽ implement sau khi có customer management

        Log::info('Payment method attached', [
            'payment_method_id' => $paymentMethod->id,
            'type' => $paymentMethod->type,
        ]);

        return ['success' => true, 'message' => 'Payment method processed'];
    }

    /**
     * Tạo refund
     */
    public function createRefund(PaymentTransaction $transaction, float $amount, string $reason = null): array
    {
        try {
            if (!$transaction->canBeRefunded()) {
                throw new \Exception('Transaction cannot be refunded');
            }

            $refund = \Stripe\Refund::create([
                'payment_intent' => $transaction->payment_intent_id,
                'amount' => $this->convertToStripeAmount($amount),
                'reason' => 'requested_by_customer',
                'metadata' => [
                    'original_transaction_id' => $transaction->transaction_id,
                    'refund_reason' => $reason,
                    'platform' => 'mechamap',
                ],
            ]);

            // Tạo refund transaction
            $refundTransaction = $transaction->createRefund($amount, $reason);
            $refundTransaction->update([
                'gateway_transaction_id' => $refund->id,
                'status' => 'completed',
                'processed_at' => now(),
                'gateway_response' => [
                    'refund_id' => $refund->id,
                    'status' => $refund->status,
                    'amount' => $refund->amount,
                ],
            ]);

            Log::info('Refund created successfully', [
                'refund_id' => $refund->id,
                'amount' => $amount,
                'original_transaction' => $transaction->transaction_id,
            ]);

            return [
                'success' => true,
                'refund_id' => $refund->id,
                'refund_transaction_id' => $refundTransaction->transaction_id,
                'amount' => $amount,
                'status' => $refund->status,
            ];

        } catch (\Exception $e) {
            Log::error('Stripe refund creation failed', [
                'transaction_id' => $transaction->transaction_id,
                'amount' => $amount,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ];
        }
    }

    /**
     * Convert VND amount to Stripe amount (cents)
     */
    protected function convertToStripeAmount(float $amount): int
    {
        // VND không có decimal, nên amount * 1
        return (int) $amount;
    }

    /**
     * Convert Stripe amount to VND
     */
    protected function convertFromStripeAmount(int $amount): float
    {
        return (float) $amount;
    }

    /**
     * Lấy thông tin payment intent
     */
    public function getPaymentIntent(string $paymentIntentId): ?array
    {
        try {
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);

            return [
                'id' => $paymentIntent->id,
                'status' => $paymentIntent->status,
                'amount' => $this->convertFromStripeAmount($paymentIntent->amount),
                'currency' => strtoupper($paymentIntent->currency),
                'client_secret' => $paymentIntent->client_secret,
                'created' => $paymentIntent->created,
                'metadata' => $paymentIntent->metadata->toArray(),
            ];

        } catch (\Exception $e) {
            Log::error('Failed to retrieve payment intent', [
                'payment_intent_id' => $paymentIntentId,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Kiểm tra connection với Stripe
     */
    public function testConnection(): array
    {
        try {
            // Test bằng cách retrieve account info
            $account = \Stripe\Account::retrieve();

            return [
                'success' => true,
                'account_id' => $account->id,
                'country' => $account->country,
                'currencies_supported' => $account->capabilities->card_payments ?? 'unknown',
                'message' => 'Stripe connection successful',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Stripe connection failed',
            ];
        }
    }
}
