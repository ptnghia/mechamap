<?php

namespace App\Services;

use App\Models\MarketplaceOrder;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SePayService
{
    protected $bankCode;
    protected $accountNumber;
    protected $accountName;
    protected $webhookSecret;

    public function __construct()
    {
        $this->bankCode = config('services.sepay.bank_code');
        $this->accountNumber = config('services.sepay.account_number');
        $this->accountName = config('services.sepay.account_name');
        $this->webhookSecret = config('services.sepay.webhook_secret');
    }

    /**
     * Tạo URL thanh toán SePay (QR Code)
     */
    public function createPaymentUrl(MarketplaceOrder $order): array
    {
        try {
            $orderCode = 'DH' . $order->id;
            $amount = intval($order->total_amount);

            // Tạo QR Code URL
            $qrUrl = $this->generateQRUrl($amount, $orderCode);

            // Tạo payment transaction
            $transaction = PaymentTransaction::create([
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'payment_method' => 'sepay',
                'type' => 'payment',
                'status' => 'pending',
                'amount' => $order->total_amount,
                'currency' => 'VND',
                'gateway_response' => [
                    'order_code' => $orderCode,
                    'qr_url' => $qrUrl,
                    'bank_code' => $this->bankCode,
                    'account_number' => $this->accountNumber,
                    'account_name' => $this->accountName,
                ]
            ]);

            return [
                'success' => true,
                'data' => [
                    'transaction_id' => $transaction->transaction_id,
                    'qr_url' => $qrUrl,
                    'bank_info' => [
                        'bank_code' => $this->bankCode,
                        'account_number' => $this->accountNumber,
                        'account_name' => $this->accountName,
                        'amount' => number_format($amount, 0, ',', '.'),
                        'content' => $orderCode,
                    ],
                    'order_code' => $orderCode,
                    'amount' => $amount,
                ]
            ];

        } catch (\Exception $e) {
            Log::error('SePay payment creation failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Không thể tạo thanh toán SePay: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Tạo QR Code URL
     */
    protected function generateQRUrl(int $amount, string $orderCode): string
    {
        $params = [
            'bank' => $this->bankCode,
            'acc' => $this->accountNumber,
            'template' => 'compact',
            'amount' => $amount,
            'des' => $orderCode,
        ];

        return 'https://qr.sepay.vn/img?' . http_build_query($params);
    }

    /**
     * Xử lý webhook từ SePay
     */
    public function handleWebhook(array $webhookData): array
    {
        try {
            // Validate webhook data
            if (!$this->validateWebhookData($webhookData)) {
                return [
                    'success' => false,
                    'message' => 'Invalid webhook data'
                ];
            }

            // Chỉ xử lý giao dịch tiền vào
            if ($webhookData['transferType'] !== 'in') {
                return [
                    'success' => true,
                    'message' => 'Ignored outgoing transaction'
                ];
            }

            // Tách mã đơn hàng từ nội dung
            $orderCode = $this->extractOrderCode($webhookData['content']);
            if (!$orderCode) {
                Log::warning('SePay webhook: Could not extract order code', [
                    'content' => $webhookData['content']
                ]);
                return [
                    'success' => false,
                    'message' => 'Could not extract order code'
                ];
            }

            // Tìm đơn hàng
            $orderId = str_replace('DH', '', $orderCode);
            $order = MarketplaceOrder::find($orderId);

            if (!$order) {
                Log::warning('SePay webhook: Order not found', [
                    'order_code' => $orderCode,
                    'order_id' => $orderId
                ]);
                return [
                    'success' => false,
                    'message' => 'Order not found'
                ];
            }

            // Kiểm tra số tiền
            if ($webhookData['transferAmount'] < $order->total_amount) {
                Log::warning('SePay webhook: Amount mismatch', [
                    'order_id' => $order->id,
                    'expected' => $order->total_amount,
                    'received' => $webhookData['transferAmount']
                ]);
                return [
                    'success' => false,
                    'message' => 'Amount mismatch'
                ];
            }

            // Cập nhật transaction
            $this->updatePaymentTransaction($order, $webhookData);

            // Cập nhật order status
            $order->update([
                'payment_status' => 'paid',
                'status' => 'processing',
                'paid_at' => now(),
            ]);

            Log::info('SePay payment completed successfully', [
                'order_id' => $order->id,
                'amount' => $webhookData['transferAmount'],
                'reference' => $webhookData['referenceCode']
            ]);

            return [
                'success' => true,
                'message' => 'Payment processed successfully'
            ];

        } catch (\Exception $e) {
            Log::error('SePay webhook processing failed', [
                'error' => $e->getMessage(),
                'webhook_data' => $webhookData
            ]);

            return [
                'success' => false,
                'message' => 'Webhook processing failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Validate webhook data
     */
    protected function validateWebhookData(array $data): bool
    {
        $requiredFields = [
            'gateway', 'transactionDate', 'accountNumber',
            'transferType', 'transferAmount', 'content', 'referenceCode'
        ];

        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Tách mã đơn hàng từ nội dung chuyển khoản
     */
    protected function extractOrderCode(string $content): ?string
    {
        // Tìm pattern DH + số
        if (preg_match('/DH(\d+)/', $content, $matches)) {
            return $matches[0]; // Trả về DH123
        }

        return null;
    }

    /**
     * Cập nhật payment transaction
     */
    protected function updatePaymentTransaction(MarketplaceOrder $order, array $webhookData): void
    {
        $transaction = PaymentTransaction::where('order_id', $order->id)
                                        ->where('payment_method', 'sepay')
                                        ->where('status', 'pending')
                                        ->first();

        if ($transaction) {
            $transaction->markAsCompleted([
                'sepay_transaction_id' => $webhookData['id'] ?? null,
                'reference_code' => $webhookData['referenceCode'],
                'bank_gateway' => $webhookData['gateway'],
                'transaction_date' => $webhookData['transactionDate'],
                'transfer_amount' => $webhookData['transferAmount'],
                'accumulated' => $webhookData['accumulated'] ?? null,
                'webhook_data' => $webhookData
            ]);
        }
    }

    /**
     * Kiểm tra trạng thái thanh toán
     */
    public function checkPaymentStatus(MarketplaceOrder $order): array
    {
        $transaction = PaymentTransaction::where('order_id', $order->id)
                                        ->where('payment_method', 'sepay')
                                        ->latest()
                                        ->first();

        if (!$transaction) {
            return [
                'status' => 'not_found',
                'message' => 'Transaction not found'
            ];
        }

        return [
            'status' => $transaction->status,
            'payment_status' => $order->payment_status,
            'transaction_id' => $transaction->transaction_id,
            'amount' => $transaction->amount,
        ];
    }

    /**
     * Kiểm tra SePay có được cấu hình không
     */
    public function isConfigured(): bool
    {
        return !empty($this->bankCode) &&
               !empty($this->accountNumber) &&
               !empty($this->accountName);
    }
}
