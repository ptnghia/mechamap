<?php

namespace App\Services;

use App\Models\CentralizedPayment;
use App\Models\MarketplaceOrder;
use App\Models\PaymentAuditLog;
use App\Models\PaymentSystemSetting;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Webhook;

/**
 * 🏦 Centralized Payment Service
 *
 * Quản lý tất cả payments từ customers đi về Admin account
 * Trước khi distribute cho sellers thông qua payout system
 */
class CentralizedPaymentService
{
    protected ?string $stripeApiKey;
    protected ?string $stripeWebhookSecret;
    protected array $adminBankAccounts;

    public function __construct()
    {
        $this->stripeApiKey = config('services.stripe.secret');
        $this->stripeWebhookSecret = config('services.stripe.webhook_secret');
        $this->adminBankAccounts = PaymentSystemSetting::getAdminBankAccounts();

        if ($this->stripeApiKey) {
            Stripe::setApiKey($this->stripeApiKey);
        }
    }

    /**
     * Create centralized payment for order
     */
    public function createPayment(MarketplaceOrder $order, string $paymentMethod): CentralizedPayment
    {
        $centralizedPayment = CentralizedPayment::create([
            'payment_reference' => CentralizedPayment::generatePaymentReference(),
            'order_id' => $order->id,
            'customer_id' => $order->customer_id,
            'customer_email' => $order->customer_email,
            'payment_method' => $paymentMethod,
            'gross_amount' => $order->total_amount,
            'gateway_fee' => 0, // Will be updated after payment processing
            'net_received' => $order->total_amount, // Will be updated after fees
            'status' => 'pending',
        ]);

        // Update order with centralized payment reference
        $order->update([
            'centralized_payment_id' => $centralizedPayment->id,
        ]);

        // Log creation
        PaymentAuditLog::logPaymentEvent(
            'payment_created',
            'centralized_payment',
            $centralizedPayment->id,
            [
                'user_id' => $order->customer_id,
                'amount_impact' => $order->total_amount,
                'description' => "Centralized payment created for order {$order->order_number}",
                'metadata' => [
                    'order_id' => $order->id,
                    'payment_method' => $paymentMethod,
                ]
            ]
        );

        return $centralizedPayment;
    }

    /**
     * Create Stripe Payment Intent for centralized payment
     */
    public function createStripePaymentIntent(CentralizedPayment $centralizedPayment): array
    {
        try {
            if (!$this->stripeApiKey) {
                throw new \Exception('Stripe is not configured');
            }

            $order = $centralizedPayment->order;
            $adminAccount = $this->adminBankAccounts['stripe'];

            $paymentIntent = PaymentIntent::create([
                'amount' => $this->convertToStripeAmount($centralizedPayment->gross_amount),
                'currency' => 'vnd',
                'payment_method_types' => ['card'],
                'metadata' => [
                    'centralized_payment_id' => $centralizedPayment->id,
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_id' => $order->customer_id,
                    'platform' => 'mechamap_centralized',
                    'admin_account' => $adminAccount['account_name'] ?? 'MechaMap Admin',
                ],
                'description' => "MechaMap Order {$order->order_number} - Centralized Payment",
                'receipt_email' => $centralizedPayment->customer_email,
                'transfer_data' => [
                    'destination' => $adminAccount['account_id'] ?? null, // Admin Stripe account
                ],
                'application_fee_amount' => 0, // No platform fee since we're the platform
            ]);

            // Update centralized payment with Stripe details
            $centralizedPayment->update([
                'gateway_payment_intent_id' => $paymentIntent->id,
                'status' => 'processing',
            ]);

            // Log Stripe payment intent creation
            PaymentAuditLog::logPaymentEvent(
                'stripe_intent_created',
                'centralized_payment',
                $centralizedPayment->id,
                [
                    'user_id' => $order->customer_id,
                    'description' => 'Stripe Payment Intent created for centralized payment',
                    'metadata' => [
                        'payment_intent_id' => $paymentIntent->id,
                        'amount' => $centralizedPayment->gross_amount,
                    ]
                ]
            );

            return [
                'success' => true,
                'payment_intent' => $paymentIntent,
                'client_secret' => $paymentIntent->client_secret,
                'payment_intent_id' => $paymentIntent->id,
            ];

        } catch (\Exception $e) {
            Log::error('Failed to create Stripe Payment Intent for centralized payment', [
                'centralized_payment_id' => $centralizedPayment->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Handle Stripe webhook for centralized payments
     */
    public function handleStripeWebhook(string $payload, string $signature): array
    {
        try {
            $event = Webhook::constructEvent(
                $payload,
                $signature,
                $this->stripeWebhookSecret
            );

            Log::info('Stripe webhook received for centralized payment', [
                'event_type' => $event->type,
                'event_id' => $event->id,
            ]);

            switch ($event->type) {
                case 'payment_intent.succeeded':
                    return $this->handlePaymentIntentSucceeded($event->data->object);

                case 'payment_intent.payment_failed':
                    return $this->handlePaymentIntentFailed($event->data->object);

                case 'payment_intent.canceled':
                    return $this->handlePaymentIntentCanceled($event->data->object);

                default:
                    Log::info('Unhandled Stripe webhook event type for centralized payment', [
                        'event_type' => $event->type
                    ]);
                    return [
                        'success' => true,
                        'message' => 'Event type not handled but acknowledged'
                    ];
            }

        } catch (\Exception $e) {
            Log::error('Stripe webhook verification failed for centralized payment', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Webhook verification failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Handle successful payment intent
     */
    protected function handlePaymentIntentSucceeded($paymentIntent): array
    {
        $centralizedPaymentId = $paymentIntent->metadata->centralized_payment_id ?? null;

        if (!$centralizedPaymentId) {
            Log::warning('Payment intent succeeded but no centralized_payment_id in metadata', [
                'payment_intent_id' => $paymentIntent->id,
            ]);
            return ['success' => false, 'message' => 'No centralized payment ID found'];
        }

        $centralizedPayment = CentralizedPayment::find($centralizedPaymentId);

        if (!$centralizedPayment) {
            Log::warning('Centralized payment not found for successful payment intent', [
                'centralized_payment_id' => $centralizedPaymentId,
                'payment_intent_id' => $paymentIntent->id,
            ]);
            return ['success' => false, 'message' => 'Centralized payment not found'];
        }

        // Calculate gateway fees
        $gatewayFee = $this->calculateStripeGatewayFee($paymentIntent->amount);
        $netReceived = $this->convertFromStripeAmount($paymentIntent->amount) - $gatewayFee;

        // Update centralized payment
        $centralizedPayment->update([
            'gateway_transaction_id' => $paymentIntent->id,
            'gateway_fee' => $gatewayFee,
            'net_received' => $netReceived,
            'status' => 'completed',
            'paid_at' => now(),
            'confirmed_at' => now(),
            'gateway_response' => $paymentIntent->toArray(),
        ]);

        // Update related order
        $centralizedPayment->order->update([
            'payment_status' => 'completed',
            'paid_at' => now(),
        ]);

        // Log successful payment
        PaymentAuditLog::logPaymentEvent(
            'payment_completed',
            'centralized_payment',
            $centralizedPayment->id,
            [
                'user_id' => $centralizedPayment->customer_id,
                'amount_impact' => $centralizedPayment->net_received,
                'description' => 'Centralized payment completed successfully via Stripe',
                'metadata' => [
                    'payment_intent_id' => $paymentIntent->id,
                    'gateway_fee' => $gatewayFee,
                    'net_received' => $netReceived,
                ]
            ]
        );

        return [
            'success' => true,
            'message' => 'Centralized payment completed successfully',
            'centralized_payment_id' => $centralizedPayment->id,
        ];
    }

    /**
     * Handle failed payment intent
     */
    protected function handlePaymentIntentFailed($paymentIntent): array
    {
        $centralizedPaymentId = $paymentIntent->metadata->centralized_payment_id ?? null;

        if (!$centralizedPaymentId) {
            return ['success' => false, 'message' => 'No centralized payment ID found'];
        }

        $centralizedPayment = CentralizedPayment::find($centralizedPaymentId);

        if (!$centralizedPayment) {
            return ['success' => false, 'message' => 'Centralized payment not found'];
        }

        $failureReason = $paymentIntent->last_payment_error->message ?? 'Payment failed';

        $centralizedPayment->markAsFailed($failureReason, $paymentIntent->toArray());

        return [
            'success' => true,
            'message' => 'Centralized payment marked as failed',
            'centralized_payment_id' => $centralizedPayment->id,
        ];
    }

    /**
     * Handle canceled payment intent
     */
    protected function handlePaymentIntentCanceled($paymentIntent): array
    {
        $centralizedPaymentId = $paymentIntent->metadata->centralized_payment_id ?? null;

        if (!$centralizedPaymentId) {
            return ['success' => false, 'message' => 'No centralized payment ID found'];
        }

        $centralizedPayment = CentralizedPayment::find($centralizedPaymentId);

        if (!$centralizedPayment) {
            return ['success' => false, 'message' => 'Centralized payment not found'];
        }

        $centralizedPayment->update([
            'status' => 'cancelled',
            'gateway_response' => $paymentIntent->toArray(),
        ]);

        $centralizedPayment->order->update([
            'payment_status' => 'cancelled',
        ]);

        // Log cancellation
        PaymentAuditLog::logPaymentEvent(
            'payment_cancelled',
            'centralized_payment',
            $centralizedPayment->id,
            [
                'user_id' => $centralizedPayment->customer_id,
                'description' => 'Centralized payment cancelled',
                'metadata' => ['payment_intent_id' => $paymentIntent->id]
            ]
        );

        return [
            'success' => true,
            'message' => 'Centralized payment cancelled',
            'centralized_payment_id' => $centralizedPayment->id,
        ];
    }

    /**
     * Convert amount to Stripe format (smallest currency unit)
     */
    protected function convertToStripeAmount(float $amount): int
    {
        // VND doesn't have decimal places, so multiply by 1
        return (int) round($amount);
    }

    /**
     * Convert amount from Stripe format
     */
    protected function convertFromStripeAmount(int $amount): float
    {
        // VND doesn't have decimal places
        return (float) $amount;
    }

    /**
     * Calculate Stripe gateway fee
     */
    protected function calculateStripeGatewayFee(int $stripeAmount): float
    {
        // Stripe VND fee: 3.4% + 10,000 VND per transaction
        $amount = $this->convertFromStripeAmount($stripeAmount);
        return ($amount * 0.034) + 10000;
    }

    /**
     * Create SePay payment for centralized payment
     */
    public function createSePayPayment(CentralizedPayment $centralizedPayment): array
    {
        try {
            $order = $centralizedPayment->order;
            $adminAccount = $this->adminBankAccounts['sepay'];

            if (empty($adminAccount['account_number'])) {
                throw new \Exception('SePay admin account not configured');
            }

            // Generate unique transaction reference
            $transactionRef = 'MECHAMAP-' . $centralizedPayment->id . '-' . time();

            // Create SePay payment data
            $paymentData = [
                'bank_code' => $adminAccount['bank_code'],
                'account_number' => $adminAccount['account_number'],
                'account_name' => $adminAccount['account_name'],
                'amount' => $centralizedPayment->gross_amount,
                'transaction_ref' => $transactionRef,
                'description' => "MechaMap Order {$order->order_number} - Centralized Payment",
                'customer_email' => $centralizedPayment->customer_email,
                'return_url' => route('marketplace.payment.sepay.return'),
                'webhook_url' => route('api.payment.sepay.webhook'),
            ];

            // Update centralized payment with SePay details
            $centralizedPayment->update([
                'gateway_transaction_id' => $transactionRef,
                'status' => 'processing',
                'gateway_response' => $paymentData,
            ]);

            // Log SePay payment creation
            PaymentAuditLog::logPaymentEvent(
                'sepay_payment_created',
                'centralized_payment',
                $centralizedPayment->id,
                [
                    'user_id' => $order->customer_id,
                    'description' => 'SePay payment created for centralized payment',
                    'metadata' => [
                        'transaction_ref' => $transactionRef,
                        'amount' => $centralizedPayment->gross_amount,
                        'admin_account' => $adminAccount['account_number'],
                    ]
                ]
            );

            return [
                'success' => true,
                'payment_data' => $paymentData,
                'transaction_ref' => $transactionRef,
                'payment_instructions' => $this->generateSePayInstructions($paymentData),
            ];

        } catch (\Exception $e) {
            Log::error('Failed to create SePay payment for centralized payment', [
                'centralized_payment_id' => $centralizedPayment->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Generate SePay payment instructions
     */
    protected function generateSePayInstructions(array $paymentData): array
    {
        return [
            'bank_name' => $this->getBankName($paymentData['bank_code']),
            'account_number' => $paymentData['account_number'],
            'account_name' => $paymentData['account_name'],
            'amount' => number_format($paymentData['amount'], 0, ',', '.') . ' VNĐ',
            'transaction_ref' => $paymentData['transaction_ref'],
            'description' => $paymentData['description'],
            'qr_code_url' => $this->generateQRCode($paymentData),
        ];
    }

    /**
     * Get bank name from bank code
     */
    protected function getBankName(string $bankCode): string
    {
        $bankNames = [
            'MBBank' => 'Ngân hàng TMCP Quân đội (MB Bank)',
            'VCB' => 'Ngân hàng TMCP Ngoại thương Việt Nam (Vietcombank)',
            'TCB' => 'Ngân hàng TMCP Kỹ thương Việt Nam (Techcombank)',
            'ACB' => 'Ngân hàng TMCP Á Châu (ACB)',
            'VPBank' => 'Ngân hàng TMCP Việt Nam Thịnh vượng (VPBank)',
        ];

        return $bankNames[$bankCode] ?? $bankCode;
    }

    /**
     * Generate QR code for SePay payment
     */
    protected function generateQRCode(array $paymentData): string
    {
        // This would integrate with a QR code generation service
        // For now, return a placeholder URL
        return route('api.payment.sepay.qr', [
            'account' => $paymentData['account_number'],
            'amount' => $paymentData['amount'],
            'ref' => $paymentData['transaction_ref'],
        ]);
    }

    /**
     * Check if Stripe is configured for centralized payments
     */
    public function isStripeConfigured(): bool
    {
        return !empty($this->stripeApiKey) &&
               !empty($this->adminBankAccounts['stripe']['account_id']);
    }

    /**
     * Check if SePay is configured for centralized payments
     */
    public function isSePayConfigured(): bool
    {
        $adminAccount = $this->adminBankAccounts['sepay'];
        return !empty($adminAccount['account_number']) &&
               !empty($adminAccount['account_name']);
    }

    /**
     * Get available payment methods for centralized payments
     */
    public function getAvailablePaymentMethods(): array
    {
        $methods = [];

        if ($this->isStripeConfigured()) {
            $methods['stripe'] = [
                'name' => 'Stripe (International Cards)',
                'description' => 'Pay with international credit/debit cards',
                'currencies' => ['VND'],
                'fees' => '3.4% + 10,000 VNĐ per transaction',
            ];
        }

        if ($this->isSePayConfigured()) {
            $methods['sepay'] = [
                'name' => 'SePay (Vietnam Banking)',
                'description' => 'Pay via Vietnamese bank transfer',
                'currencies' => ['VND'],
                'fees' => 'Free for customers',
            ];
        }

        return $methods;
    }
}
