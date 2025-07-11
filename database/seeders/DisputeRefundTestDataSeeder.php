<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentDispute;
use App\Models\PaymentRefund;
use App\Models\CentralizedPayment;
use App\Models\MarketplaceOrder;
use App\Models\User;
use Carbon\Carbon;

class DisputeRefundTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating dispute and refund test data...');

        // Get existing completed payments
        $payments = CentralizedPayment::where('status', 'completed')
            ->with(['order', 'order.customer'])
            ->take(20)
            ->get();

        if ($payments->isEmpty()) {
            $this->command->warn('No completed payments found. Please run FinancialReportsTestDataSeeder first.');
            return;
        }

        // Get admin users
        $admins = User::where('email', 'admin@mechamap.vn')->get();

        if ($admins->isEmpty()) {
            $this->command->warn('No admin users found.');
            return;
        }

        // Create disputes
        $this->createDisputes($payments, $admins);

        // Create standalone refunds
        $this->createRefunds($payments, $admins);

        $this->command->info('Dispute and refund test data created successfully!');
    }

    /**
     * Create test disputes
     */
    protected function createDisputes($payments, $admins): void
    {
        $disputeTypes = [
            'chargeback',
            'payment_not_received',
            'unauthorized',
            'duplicate',
            'product_not_received',
            'product_defective',
            'service_issue',
            'billing_error',
            'other'
        ];

        $statuses = ['pending', 'investigating', 'evidence_required', 'escalated', 'resolved', 'lost'];
        $priorities = ['low', 'medium', 'high', 'urgent'];

        foreach ($payments->take(10) as $payment) {
            $disputeType = $disputeTypes[array_rand($disputeTypes)];
            $status = $statuses[array_rand($statuses)];
            $priority = $priorities[array_rand($priorities)];

            $disputeDate = Carbon::now()->subDays(rand(1, 30));
            $gatewayDeadline = $disputeDate->copy()->addDays(rand(7, 21));

            $dispute = PaymentDispute::create([
                'centralized_payment_id' => $payment->id,
                'order_id' => $payment->order_id,
                'customer_id' => $payment->customer_id,
                'customer_email' => $payment->customer_email,
                'dispute_type' => $disputeType,
                'status' => $status,
                'priority' => $priority,
                'disputed_amount' => $payment->gross_amount,
                'currency' => $payment->currency ?? 'VND',
                'gateway_dispute_id' => 'dp_test_' . \Illuminate\Support\Str::random(16),
                'gateway_reason_code' => $this->getReasonCode($disputeType),
                'customer_reason' => $this->getCustomerReason($disputeType),
                'customer_description' => $this->getCustomerDescription($disputeType),
                'dispute_date' => $disputeDate,
                'gateway_deadline' => $gatewayDeadline,
                'created_at' => $disputeDate,
                'updated_at' => $disputeDate,
            ]);

            // Assign to admin if investigating or later
            if (in_array($status, ['investigating', 'evidence_required', 'escalated', 'resolved', 'lost'])) {
                $admin = $admins->random();
                $dispute->update([
                    'assigned_to' => $admin->id,
                ]);

                // Add merchant response for some disputes
                if (rand(1, 3) === 1) {
                    $dispute->update([
                        'merchant_response' => $this->getMerchantResponse($disputeType),
                        'merchant_evidence' => [
                            [
                                'type' => 'merchant_response',
                                'text' => $this->getMerchantResponse($disputeType),
                                'files' => [],
                                'added_by' => $admin->name,
                                'added_at' => now()->toISOString(),
                            ]
                        ],
                    ]);
                }
            }

            // Mark as resolved with resolution details
            if ($status === 'resolved') {
                $resolutionTypes = ['full_refund', 'partial_refund', 'no_refund', 'replacement', 'store_credit'];
                $resolutionType = $resolutionTypes[array_rand($resolutionTypes)];

                $dispute->update([
                    'resolution_type' => $resolutionType,
                    'resolution_summary' => $this->getResolutionSummary($resolutionType),
                    'resolved_at' => $disputeDate->copy()->addDays(rand(1, 10)),
                    'closed_at' => $disputeDate->copy()->addDays(rand(1, 10)),
                ]);

                // Create refund if resolution involves refund
                if (in_array($resolutionType, ['full_refund', 'partial_refund'])) {
                    $refundAmount = $resolutionType === 'full_refund'
                        ? $payment->gross_amount
                        : $payment->gross_amount * (rand(30, 80) / 100);

                    $this->createRefundFromDispute($dispute, $refundAmount, $admins->random());
                }
            }

            $this->command->info("Created dispute: {$dispute->dispute_reference} - {$disputeType} - {$status}");
        }
    }

    /**
     * Create test refunds
     */
    protected function createRefunds($payments, $admins): void
    {
        $refundTypes = ['full', 'partial', 'shipping', 'tax', 'item', 'goodwill'];
        $reasons = [
            'customer_request',
            'product_defective',
            'wrong_item',
            'not_delivered',
            'damaged_shipping',
            'duplicate_payment',
            'billing_error',
            'goodwill',
            'admin_error'
        ];
        $statuses = ['pending', 'approved', 'processing', 'completed', 'failed', 'rejected'];

        foreach ($payments->take(8) as $payment) {
            $refundType = $refundTypes[array_rand($refundTypes)];
            $reason = $reasons[array_rand($reasons)];
            $status = $statuses[array_rand($statuses)];

            $refundAmount = $refundType === 'full'
                ? $payment->gross_amount
                : $payment->gross_amount * (rand(20, 80) / 100);

            $requestedAt = Carbon::now()->subDays(rand(1, 15));

            $refund = PaymentRefund::create([
                'centralized_payment_id' => $payment->id,
                'order_id' => $payment->order_id,
                'customer_id' => $payment->customer_id,
                'refund_type' => $refundType,
                'reason' => $reason,
                'status' => $status,
                'original_amount' => $payment->gross_amount,
                'refund_amount' => $refundAmount,
                'gateway_fee' => 0,
                'net_refund' => $refundAmount,
                'currency' => $payment->currency ?? 'VND',
                'payment_method' => $payment->payment_method,
                'customer_reason' => $this->getRefundCustomerReason($reason),
                'admin_reason' => $this->getRefundAdminReason($reason),
                'requested_by' => $admins->random()->id,
                'requested_at' => $requestedAt,
                'created_at' => $requestedAt,
                'updated_at' => $requestedAt,
            ]);

            // Update timestamps based on status
            if (in_array($status, ['approved', 'processing', 'completed', 'rejected'])) {
                $admin = $admins->random();
                $refund->update([
                    'approved_by' => $admin->id,
                    'approved_at' => $requestedAt->copy()->addHours(rand(1, 48)),
                ]);
            }

            if (in_array($status, ['processing', 'completed'])) {
                $refund->update([
                    'processed_by' => $admins->random()->id,
                    'processed_at' => $refund->approved_at->copy()->addHours(rand(1, 24)),
                ]);
            }

            if ($status === 'completed') {
                $refund->update([
                    'completed_at' => $refund->processed_at->copy()->addHours(rand(1, 12)),
                    'gateway_refund_id' => 're_test_' . \Illuminate\Support\Str::random(16),
                    'gateway_response' => [
                        'id' => 're_test_' . \Illuminate\Support\Str::random(16),
                        'amount' => $refundAmount,
                        'currency' => 'vnd',
                        'status' => 'succeeded',
                        'created' => $refund->processed_at->timestamp,
                    ],
                ]);
            }

            if ($status === 'failed' && $refund->processed_at) {
                $refund->update([
                    'failed_at' => $refund->processed_at->copy()->addHours(rand(1, 6)),
                    'gateway_error' => 'Test error: Insufficient funds in merchant account',
                ]);
            }

            $this->command->info("Created refund: {$refund->refund_reference} - {$refundType} - {$status}");
        }
    }

    /**
     * Create refund from dispute
     */
    protected function createRefundFromDispute($dispute, $refundAmount, $admin): void
    {
        $refund = PaymentRefund::create([
            'centralized_payment_id' => $dispute->centralized_payment_id,
            'order_id' => $dispute->order_id,
            'customer_id' => $dispute->customer_id,
            'dispute_id' => $dispute->id,
            'refund_type' => $refundAmount >= $dispute->disputed_amount ? 'full' : 'partial',
            'reason' => 'dispute_resolution',
            'status' => 'completed',
            'original_amount' => $dispute->disputed_amount,
            'refund_amount' => $refundAmount,
            'gateway_fee' => 0,
            'net_refund' => $refundAmount,
            'currency' => $dispute->currency,
            'payment_method' => $dispute->centralizedPayment->payment_method,
            'admin_reason' => "Refund issued as resolution for dispute {$dispute->dispute_reference}",
            'requested_by' => $admin->id,
            'approved_by' => $admin->id,
            'processed_by' => $admin->id,
            'requested_at' => $dispute->resolved_at,
            'approved_at' => $dispute->resolved_at,
            'processed_at' => $dispute->resolved_at,
            'completed_at' => $dispute->resolved_at->copy()->addHours(1),
            'gateway_refund_id' => 're_dispute_' . \Illuminate\Support\Str::random(16),
        ]);

        $dispute->update(['refund_amount' => $refundAmount]);
    }

    /**
     * Helper methods for generating realistic content
     */
    protected function getReasonCode($disputeType): string
    {
        return match($disputeType) {
            'chargeback' => 'chargeback_dispute',
            'unauthorized' => 'fraudulent',
            'duplicate' => 'duplicate',
            'product_not_received' => 'product_not_received',
            'product_defective' => 'product_unacceptable',
            default => 'general'
        };
    }

    protected function getCustomerReason($disputeType): string
    {
        return match($disputeType) {
            'chargeback' => 'I did not authorize this payment and want my money back.',
            'unauthorized' => 'This transaction was not made by me. My card was stolen.',
            'duplicate' => 'I was charged twice for the same order.',
            'product_not_received' => 'I never received the product I ordered.',
            'product_defective' => 'The product I received was damaged and unusable.',
            'service_issue' => 'The service was not delivered as promised.',
            'billing_error' => 'I was charged the wrong amount.',
            default => 'I have an issue with this payment.'
        };
    }

    protected function getCustomerDescription($disputeType): string
    {
        return match($disputeType) {
            'chargeback' => 'I contacted my bank because I do not recognize this charge on my statement.',
            'unauthorized' => 'My card was stolen last week and this charge appeared after that.',
            'duplicate' => 'I can see two identical charges on my statement for the same order.',
            'product_not_received' => 'It has been over 2 weeks and I still have not received my order.',
            'product_defective' => 'The product arrived broken and does not work as described.',
            default => 'Additional details about the dispute.'
        };
    }

    protected function getMerchantResponse($disputeType): string
    {
        return match($disputeType) {
            'chargeback' => 'We have provided evidence of the legitimate transaction including delivery confirmation.',
            'unauthorized' => 'The transaction was processed with valid payment details and delivered to the billing address.',
            'duplicate' => 'We have verified that only one charge was processed. The customer may be seeing a pending authorization.',
            'product_not_received' => 'We have tracking information showing the product was delivered to the customer address.',
            'product_defective' => 'We offer a return policy and the customer did not contact us before disputing.',
            default => 'We believe this dispute is invalid and have provided supporting evidence.'
        };
    }

    protected function getResolutionSummary($resolutionType): string
    {
        return match($resolutionType) {
            'full_refund' => 'Full refund issued to customer as dispute resolution.',
            'partial_refund' => 'Partial refund issued to cover shipping costs and inconvenience.',
            'no_refund' => 'Dispute resolved in favor of merchant. No refund issued.',
            'replacement' => 'Replacement product sent to customer.',
            'store_credit' => 'Store credit issued to customer account.',
            default => 'Dispute resolved through alternative arrangement.'
        };
    }

    protected function getRefundCustomerReason($reason): string
    {
        return match($reason) {
            'customer_request' => 'I would like to return this item as I no longer need it.',
            'product_defective' => 'The product arrived damaged and does not work properly.',
            'wrong_item' => 'I received the wrong item. This is not what I ordered.',
            'not_delivered' => 'I never received my order despite tracking showing delivered.',
            'damaged_shipping' => 'The package was damaged during shipping.',
            default => 'I need a refund for this order.'
        };
    }

    protected function getRefundAdminReason($reason): string
    {
        return match($reason) {
            'customer_request' => 'Customer requested refund within return policy period.',
            'product_defective' => 'Product quality issue confirmed. Refund approved.',
            'wrong_item' => 'Shipping error confirmed. Wrong item sent to customer.',
            'not_delivered' => 'Delivery issue confirmed with shipping carrier.',
            'goodwill' => 'Goodwill refund to maintain customer satisfaction.',
            'admin_error' => 'Admin error in processing. Refund to correct mistake.',
            default => 'Refund approved after review.'
        };
    }
}
