<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CentralizedPayment;
use App\Models\PaymentAuditLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

/**
 * 🏦 Centralized SePay Webhook Controller
 * 
 * Xử lý SePay webhooks cho centralized payment system
 * Tất cả payments từ SePay sẽ đi về Admin account trước
 */
class CentralizedSePayWebhookController extends Controller
{
    /**
     * Handle SePay webhook for centralized payments
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        try {
            Log::info('Centralized SePay webhook received', [
                'headers' => $request->headers->all(),
                'body' => $request->all(),
                'ip' => $request->ip(),
            ]);

            // Validate webhook data
            $webhookData = $this->validateWebhookData($request);
            
            if (!$webhookData['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => $webhookData['error']
                ], 400);
            }

            // Process webhook based on transaction reference
            $result = $this->processWebhook($webhookData['data']);

            if ($result['success']) {
                Log::info('Centralized SePay webhook processed successfully', [
                    'message' => $result['message'],
                    'centralized_payment_id' => $result['centralized_payment_id'] ?? null,
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => $result['message']
                ]);
            } else {
                Log::warning('Centralized SePay webhook processing failed', [
                    'message' => $result['message'],
                    'data' => $webhookData['data']
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('Centralized SePay webhook exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Validate webhook data
     */
    protected function validateWebhookData(Request $request): array
    {
        $requiredFields = [
            'transaction_ref',
            'amount',
            'status',
            'bank_code',
            'account_number',
        ];

        $data = $request->all();

        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                return [
                    'valid' => false,
                    'error' => "Missing required field: {$field}"
                ];
            }
        }

        // Validate transaction reference format
        if (!preg_match('/^MECHAMAP-\d+-\d+$/', $data['transaction_ref'])) {
            return [
                'valid' => false,
                'error' => 'Invalid transaction reference format'
            ];
        }

        // Validate amount
        if (!is_numeric($data['amount']) || $data['amount'] <= 0) {
            return [
                'valid' => false,
                'error' => 'Invalid amount'
            ];
        }

        // Validate status
        $validStatuses = ['success', 'failed', 'pending', 'cancelled'];
        if (!in_array(strtolower($data['status']), $validStatuses)) {
            return [
                'valid' => false,
                'error' => 'Invalid status'
            ];
        }

        return [
            'valid' => true,
            'data' => $data
        ];
    }

    /**
     * Process webhook data
     */
    protected function processWebhook(array $data): array
    {
        // Extract centralized payment ID from transaction reference
        // Format: MECHAMAP-{centralized_payment_id}-{timestamp}
        preg_match('/^MECHAMAP-(\d+)-\d+$/', $data['transaction_ref'], $matches);
        
        if (!isset($matches[1])) {
            return [
                'success' => false,
                'message' => 'Could not extract centralized payment ID from transaction reference'
            ];
        }

        $centralizedPaymentId = $matches[1];
        $centralizedPayment = CentralizedPayment::find($centralizedPaymentId);

        if (!$centralizedPayment) {
            return [
                'success' => false,
                'message' => "Centralized payment not found: {$centralizedPaymentId}"
            ];
        }

        // Verify payment method
        if ($centralizedPayment->payment_method !== 'sepay') {
            return [
                'success' => false,
                'message' => 'Payment method mismatch'
            ];
        }

        // Verify amount
        if (abs($centralizedPayment->gross_amount - $data['amount']) > 0.01) {
            return [
                'success' => false,
                'message' => 'Amount mismatch'
            ];
        }

        // Process based on status
        switch (strtolower($data['status'])) {
            case 'success':
                return $this->handleSuccessfulPayment($centralizedPayment, $data);
            
            case 'failed':
                return $this->handleFailedPayment($centralizedPayment, $data);
            
            case 'cancelled':
                return $this->handleCancelledPayment($centralizedPayment, $data);
            
            default:
                return [
                    'success' => true,
                    'message' => 'Status acknowledged but no action taken',
                    'centralized_payment_id' => $centralizedPayment->id,
                ];
        }
    }

    /**
     * Handle successful payment
     */
    protected function handleSuccessfulPayment(CentralizedPayment $centralizedPayment, array $data): array
    {
        if ($centralizedPayment->status === 'completed') {
            return [
                'success' => true,
                'message' => 'Payment already completed',
                'centralized_payment_id' => $centralizedPayment->id,
            ];
        }

        // SePay typically has no gateway fees for domestic transfers
        $gatewayFee = 0;
        $netReceived = $centralizedPayment->gross_amount;

        // Update centralized payment
        $centralizedPayment->update([
            'gateway_transaction_id' => $data['transaction_ref'],
            'gateway_fee' => $gatewayFee,
            'net_received' => $netReceived,
            'status' => 'completed',
            'paid_at' => now(),
            'confirmed_at' => now(),
            'gateway_response' => $data,
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
                'description' => 'Centralized payment completed successfully via SePay',
                'metadata' => [
                    'transaction_ref' => $data['transaction_ref'],
                    'bank_code' => $data['bank_code'],
                    'account_number' => $data['account_number'],
                ]
            ]
        );

        return [
            'success' => true,
            'message' => 'Centralized SePay payment completed successfully',
            'centralized_payment_id' => $centralizedPayment->id,
        ];
    }

    /**
     * Handle failed payment
     */
    protected function handleFailedPayment(CentralizedPayment $centralizedPayment, array $data): array
    {
        $failureReason = $data['failure_reason'] ?? 'SePay payment failed';

        $centralizedPayment->markAsFailed($failureReason, $data);

        return [
            'success' => true,
            'message' => 'Centralized SePay payment marked as failed',
            'centralized_payment_id' => $centralizedPayment->id,
        ];
    }

    /**
     * Handle cancelled payment
     */
    protected function handleCancelledPayment(CentralizedPayment $centralizedPayment, array $data): array
    {
        $centralizedPayment->update([
            'status' => 'cancelled',
            'gateway_response' => $data,
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
                'description' => 'Centralized SePay payment cancelled',
                'metadata' => ['transaction_ref' => $data['transaction_ref']]
            ]
        );

        return [
            'success' => true,
            'message' => 'Centralized SePay payment cancelled',
            'centralized_payment_id' => $centralizedPayment->id,
        ];
    }

    /**
     * Check payment status for centralized payments
     */
    public function checkPaymentStatus(Request $request): JsonResponse
    {
        $request->validate([
            'centralized_payment_id' => 'required|exists:centralized_payments,id',
        ]);

        try {
            $centralizedPayment = CentralizedPayment::findOrFail($request->centralized_payment_id);

            return response()->json([
                'success' => true,
                'data' => [
                    'centralized_payment_id' => $centralizedPayment->id,
                    'payment_reference' => $centralizedPayment->payment_reference,
                    'status' => $centralizedPayment->status,
                    'payment_method' => $centralizedPayment->payment_method,
                    'gross_amount' => $centralizedPayment->gross_amount,
                    'net_received' => $centralizedPayment->net_received,
                    'gateway_fee' => $centralizedPayment->gateway_fee,
                    'paid_at' => $centralizedPayment->paid_at,
                    'confirmed_at' => $centralizedPayment->confirmed_at,
                    'order_id' => $centralizedPayment->order_id,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to check centralized payment status', [
                'centralized_payment_id' => $request->centralized_payment_id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to check payment status'
            ], 500);
        }
    }
}
