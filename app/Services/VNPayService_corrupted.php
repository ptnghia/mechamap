<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class VNPayService
{
    protected ?string $vnpTmnCode;
    protected ?string $vnpHashSecret;
    protected ?string $vnpUrl;
    protected ?string $vnpReturnUrl;
    protected ?string $vnpIpnUrl;

    public function __construct()
    {
        $this->vnpTmnCode = config('services.vnpay.tmn_code');
        $this->vnpHashSecret = config('services.vnpay.hash_secret');
        $this->vnpUrl = config('services.vnpay.url');
        $this->vnpReturnUrl = config('services.vnpay.return_url');
        $this->vnpIpnUrl = config('services.vnpay.ipn_url');
    }

    /**
     * Check if VNPay is configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->vnpTmnCode) && !empty($this->vnpHashSecret);
    }

    /**
     * Tạo payment URL cho VNPay
     */
    public function createPaymentUrl(Order $order, string $clientIp = '127.0.0.1'): array
    {
        try {
            $vnpTxnRef = $order->order_number . '_' . time();
            $vnpAmount = $order->total_amount * 100; // VNPay amount in VND x100
            $vnpLocale = 'vn';
            $vnpBankCode = '';
            $vnpIpAddr = $clientIp;

            // Tạo transaction record
            $transaction = PaymentTransaction::create([
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'payment_method' => 'vnpay',
                'gateway_transaction_id' => $vnpTxnRef,
                'type' => 'payment',
                'status' => 'pending',
                'amount' => $order->total_amount,
                'currency' => 'VND',
                'fee_amount' => $order->processing_fee,
                'net_amount' => $order->total_amount - $order->processing_fee,
                'gateway_response' => [
                    'vnp_TxnRef' => $vnpTxnRef,
                    'created_at' => now()->toISOString(),
                ],
            ]);

            // Cập nhật order
            $order->update([
                'transaction_id' => $vnpTxnRef,
                'payment_method' => 'vnpay',
            ]);

            $inputData = [
                "vnp_Version" => "2.1.0",
                "vnp_TmnCode" => $this->vnpTmnCode,
                "vnp_Amount" => $vnpAmount,
                "vnp_Command" => "pay",
                "vnp_CreateDate" => date('YmdHis'),
                "vnp_CurrCode" => "VND",
                "vnp_IpAddr" => $vnpIpAddr,
                "vnp_Locale" => $vnpLocale,
                "vnp_OrderInfo" => "Thanh toan don hang MechaMap {$order->order_number}",
                "vnp_OrderType" => "other",
                "vnp_ReturnUrl" => $this->vnpReturnUrl,
                "vnp_TxnRef" => $vnpTxnRef,
                "vnp_ExpireDate" => date('YmdHis', strtotime('+15 minutes')),
            ];

            if (!empty($vnpBankCode)) {
                $inputData['vnp_BankCode'] = $vnpBankCode;
            }

            ksort($inputData);
            $query = "";
            $i = 0;
            $hashdata = "";

            foreach ($inputData as $key => $value) {
                if ($i == 1) {
                    $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
                } else {
                    $hashdata .= urlencode($key) . "=" . urlencode($value);
                    $i = 1;
                }
                $query .= urlencode($key) . "=" . urlencode($value) . '&';
            }

            $vnpUrl = $this->vnpUrl . "?" . $query;
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $this->vnpHashSecret);
            $vnpUrl .= 'vnp_SecureHash=' . $vnpSecureHash;

            Log::info('VNPay payment URL created', [
                'order_id' => $order->id,
                'vnp_TxnRef' => $vnpTxnRef,
                'vnp_Amount' => $vnpAmount,
                'transaction_id' => $transaction->transaction_id,
            ]);

            return [
                'success' => true,
                'payment_url' => $vnpUrl,
                'vnp_TxnRef' => $vnpTxnRef,
                'transaction_id' => $transaction->transaction_id,
                'amount' => $order->total_amount,
                'expires_at' => now()->addMinutes(15)->toISOString(),
            ];

        } catch (\Exception $e) {
            Log::error('VNPay payment URL creation failed', [
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
     * Xử lý response từ VNPay (return URL)
     */
    public function handleReturn(array $vnpayData): array
    {
        try {
            $vnpSecureHash = $vnpayData['vnp_SecureHash'] ?? '';
            unset($vnpayData['vnp_SecureHash']);
            unset($vnpayData['vnp_SecureHashType']);

            ksort($vnpayData);
            $hashData = "";
            $i = 0;

            foreach ($vnpayData as $key => $value) {
                if ($i == 1) {
                    $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
                } else {
                    $hashData .= urlencode($key) . "=" . urlencode($value);
                    $i = 1;
                }
            }

            $secureHash = hash_hmac('sha512', $hashData, $this->vnpHashSecret);

            if ($secureHash !== $vnpSecureHash) {
                Log::warning('VNPay invalid signature', [
                    'vnp_TxnRef' => $vnpayData['vnp_TxnRef'] ?? 'unknown',
                    'received_hash' => $vnpSecureHash,
                    'calculated_hash' => $secureHash,
                ]);

                return [
                    'success' => false,
                    'error' => 'Invalid signature',
                    'code' => 'INVALID_SIGNATURE',
                ];
            }

            $vnpTxnRef = $vnpayData['vnp_TxnRef'];
            $vnpAmount = $vnpayData['vnp_Amount'];
            $vnpResponseCode = $vnpayData['vnp_ResponseCode'];
            $vnpTransactionNo = $vnpayData['vnp_TransactionNo'] ?? null;

            // Tìm transaction
            $transaction = PaymentTransaction::where('gateway_transaction_id', $vnpTxnRef)->first();

            if (!$transaction) {
                Log::warning('VNPay transaction not found', [
                    'vnp_TxnRef' => $vnpTxnRef,
                ]);

                return [
                    'success' => false,
                    'error' => 'Transaction not found',
                    'code' => 'TRANSACTION_NOT_FOUND',
                ];
            }

            $order = $transaction->order;

            if ($vnpResponseCode == '00') {
                // Payment thành công
                $transaction->markAsCompleted([
                    'vnpay_response_code' => $vnpResponseCode,
                    'vnpay_transaction_no' => $vnpTransactionNo,
                    'vnpay_amount' => $vnpAmount,
                    'vnpay_data' => $vnpayData,
                ]);

                // Xử lý order thành công
                $orderService = app(OrderService::class);
                $orderService->processSuccessfulPayment($order, [
                    'transaction_id' => $transaction->transaction_id,
                    'vnpay_transaction_no' => $vnpTransactionNo,
                    'gateway' => 'vnpay',
                ]);

                Log::info('VNPay payment successful', [
                    'order_id' => $order->id,
                    'vnp_TxnRef' => $vnpTxnRef,
                    'vnp_TransactionNo' => $vnpTransactionNo,
                    'amount' => $vnpAmount / 100,
                ]);

                return [
                    'success' => true,
                    'status' => 'success',
                    'order_number' => $order->order_number,
                    'amount' => $vnpAmount / 100,
                    'transaction_no' => $vnpTransactionNo,
                    'message' => 'Payment successful',
                ];

            } else {
                // Payment thất bại
                $errorMessage = $this->getVNPayErrorMessage($vnpResponseCode);

                $transaction->markAsFailed($errorMessage, [
                    'vnpay_response_code' => $vnpResponseCode,
                    'vnpay_data' => $vnpayData,
                ]);

                // Xử lý order thất bại
                $orderService = app(OrderService::class);
                $orderService->processFailedPayment($order, "VNPay payment failed: {$errorMessage}");

                Log::warning('VNPay payment failed', [
                    'order_id' => $order->id,
                    'vnp_TxnRef' => $vnpTxnRef,
                    'vnp_ResponseCode' => $vnpResponseCode,
                    'error_message' => $errorMessage,
                ]);

                return [
                    'success' => false,
                    'status' => 'failed',
                    'order_number' => $order->order_number,
                    'error_code' => $vnpResponseCode,
                    'error_message' => $errorMessage,
                ];
            }

        } catch (\Exception $e) {
            Log::error('VNPay return processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'vnpay_data' => $vnpayData,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'code' => 'PROCESSING_ERROR',
            ];
        }
    }

    /**
     * Handle VNPay return callback (user redirected after payment)
     */
    public function handleCallback(array $params): array
    {
        try {
            // Verify signature
            $vnpSecureHash = $params['vnp_SecureHash'] ?? '';
            unset($params['vnp_SecureHash'], $params['vnp_SecureHashType']);
            
            if (!$this->verifySignature($params, $vnpSecureHash)) {
                Log::warning('VNPay callback signature verification failed', [
                    'params' => $params
                ]);
                
                return [
                    'success' => false,
                    'message' => 'Invalid signature'
                ];
            }

            $vnpResponseCode = $params['vnp_ResponseCode'] ?? '';
            $vnpTransactionNo = $params['vnp_TransactionNo'] ?? '';
            $vnpTxnRef = $params['vnp_TxnRef'] ?? '';
            $vnpAmount = $params['vnp_Amount'] ?? 0;

            // Extract order ID from transaction reference
            $orderNumber = explode('_', $vnpTxnRef)[0] ?? '';
            $order = Order::where('order_number', $orderNumber)->first();

            if (!$order) {
                Log::error('Order not found for VNPay callback', [
                    'order_number' => $orderNumber,
                    'vnp_txn_ref' => $vnpTxnRef
                ]);
                
                return [
                    'success' => false,
                    'message' => 'Order not found'
                ];
            }

            if ($vnpResponseCode === '00') {
                // Payment successful
                $order->update([
                    'status' => 'paid',
                    'payment_status' => 'completed',
                    'paid_at' => now(),
                ]);

                // Update or create transaction record
                $transaction = PaymentTransaction::where('order_id', $order->id)
                    ->where('payment_method', 'vnpay')
                    ->first();

                if ($transaction) {
                    $transaction->update([
                        'transaction_id' => $vnpTransactionNo,
                        'status' => 'completed',
                        'gateway_response' => json_encode($params),
                        'processed_at' => now(),
                    ]);
                } else {
                    PaymentTransaction::create([
                        'order_id' => $order->id,
                        'payment_method' => 'vnpay',
                        'transaction_id' => $vnpTransactionNo,
                        'amount' => $vnpAmount / 100, // Convert back from VNPay format
                        'currency' => 'VND',
                        'status' => 'completed',
                        'gateway_response' => json_encode($params),
                        'processed_at' => now(),
                    ]);
                }

                Log::info('VNPay payment successful', [
                    'order_id' => $order->id,
                    'transaction_no' => $vnpTransactionNo,
                    'amount' => $vnpAmount
                ]);

                return [
                    'success' => true,
                    'message' => 'Payment successful',
                    'data' => [
                        'order_id' => $order->id,
                        'transaction_id' => $vnpTransactionNo,
                        'amount' => $vnpAmount / 100
                    ]
                ];

            } else {
                // Payment failed
                $order->update([
                    'status' => 'payment_failed',
                    'payment_status' => 'failed',
                ]);

                // Update transaction record
                $transaction = PaymentTransaction::where('order_id', $order->id)
                    ->where('payment_method', 'vnpay')
                    ->first();

                if ($transaction) {
                    $transaction->update([
                        'status' => 'failed',
                        'gateway_response' => json_encode($params),
                        'processed_at' => now(),
                    ]);
                }

                $errorMessage = $this->getVNPayErrorMessage($vnpResponseCode);
                
                Log::info('VNPay payment failed', [
                    'order_id' => $order->id,
                    'response_code' => $vnpResponseCode,
                    'error_message' => $errorMessage
                ]);

                return [
                    'success' => false,
                    'message' => $errorMessage,
                    'data' => [
                        'order_id' => $order->id,
                        'response_code' => $vnpResponseCode
                    ]
                ];
            }

        } catch (\Exception $e) {
            Log::error('VNPay callback processing error', [
                'error' => $e->getMessage(),
                'params' => $params
            ]);

            return [
                'success' => false,
                'message' => 'Callback processing failed'
            ];
        }
    }

    /**
     * Xử lý IPN (Instant Payment Notification) từ VNPay
     */
    public function handleIPN(array $vnpayData): array
    {
        try {
            $vnpSecureHash = $vnpayData['vnp_SecureHash'] ?? '';
            unset($vnpayData['vnp_SecureHash']);
            unset($vnpayData['vnp_SecureHashType']);

            ksort($vnpayData);
            $hashData = "";
            $i = 0;

            foreach ($vnpayData as $key => $value) {
                if ($i == 1) {
                    $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
                } else {
                    $hashData .= urlencode($key) . "=" . urlencode($value);
                    $i = 1;
                }
            }

            $secureHash = hash_hmac('sha512', $hashData, $this->vnpHashSecret);

            if ($secureHash !== $vnpSecureHash) {
                Log::warning('VNPay IPN invalid signature', [
                    'vnp_TxnRef' => $vnpayData['vnp_TxnRef'] ?? 'unknown',
                ]);

                return [
                    'RspCode' => '97',
                    'Message' => 'Invalid signature'
                ];
            }

            $vnpTxnRef = $vnpayData['vnp_TxnRef'];
            $vnpAmount = $vnpayData['vnp_Amount'];
            $vnpResponseCode = $vnpayData['vnp_ResponseCode'];

            // Tìm transaction
            $transaction = PaymentTransaction::where('gateway_transaction_id', $vnpTxnRef)->first();

            if (!$transaction) {
                Log::warning('VNPay IPN transaction not found', [
                    'vnp_TxnRef' => $vnpTxnRef,
                ]);

                return [
                    'RspCode' => '01',
                    'Message' => 'Order not found'
                ];
            }

            // Kiểm tra amount
            $expectedAmount = $transaction->amount * 100;
            if ($vnpAmount != $expectedAmount) {
                Log::warning('VNPay IPN amount mismatch', [
                    'vnp_TxnRef' => $vnpTxnRef,
                    'expected_amount' => $expectedAmount,
                    'received_amount' => $vnpAmount,
                ]);

                return [
                    'RspCode' => '04',
                    'Message' => 'Invalid amount'
                ];
            }

            // Kiểm tra trạng thái transaction
            if ($transaction->status === 'completed') {
                Log::info('VNPay IPN for already completed transaction', [
                    'vnp_TxnRef' => $vnpTxnRef,
                ]);

                return [
                    'RspCode' => '00',
                    'Message' => 'Confirm success'
                ];
            }

            if ($vnpResponseCode == '00') {
                // Cập nhật transaction status (nếu chưa được cập nhật)
                if ($transaction->status === 'pending') {
                    $transaction->markAsCompleted([
                        'vnpay_ipn_data' => $vnpayData,
                        'vnpay_response_code' => $vnpResponseCode,
                    ]);

                    // Process order nếu chưa được process
                    $order = $transaction->order;
                    if ($order->payment_status !== 'completed') {
                        $orderService = app(OrderService::class);
                        $orderService->processSuccessfulPayment($order, [
                            'transaction_id' => $transaction->transaction_id,
                            'gateway' => 'vnpay',
                        ]);
                    }
                }

                Log::info('VNPay IPN processed successfully', [
                    'vnp_TxnRef' => $vnpTxnRef,
                ]);

                return [
                    'RspCode' => '00',
                    'Message' => 'Confirm success'
                ];

            } else {
                Log::warning('VNPay IPN payment failed', [
                    'vnp_TxnRef' => $vnpTxnRef,
                    'vnp_ResponseCode' => $vnpResponseCode,
                ]);

                return [
                    'RspCode' => '00',
                    'Message' => 'Confirm success'
                ];
            }

        } catch (\Exception $e) {
            Log::error('VNPay IPN processing failed', [
                'error' => $e->getMessage(),
                'vnpay_data' => $vnpayData,
            ]);

            return [
                'RspCode' => '99',
                'Message' => 'Unknown error'
            ];
        }
    }

    /**
     * Query transaction status từ VNPay
     */
    public function queryTransaction(string $vnpTxnRef, string $transactionDate): array
    {
        try {
            $inputData = [
                "vnp_Version" => "2.1.0",
                "vnp_Command" => "querydr",
                "vnp_TmnCode" => $this->vnpTmnCode,
                "vnp_TxnRef" => $vnpTxnRef,
                "vnp_OrderInfo" => "Query transaction",
                "vnp_TransactionDate" => $transactionDate,
                "vnp_CreateDate" => date('YmdHis'),
                "vnp_IpAddr" => "127.0.0.1",
            ];

            ksort($inputData);
            $hashData = "";
            $i = 0;

            foreach ($inputData as $key => $value) {
                if ($i == 1) {
                    $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
                } else {
                    $hashData .= urlencode($key) . "=" . urlencode($value);
                    $i = 1;
                }
            }

            $vnpSecureHash = hash_hmac('sha512', $hashData, $this->vnpHashSecret);
            $inputData['vnp_SecureHash'] = $vnpSecureHash;

            // Gửi request đến VNPay API
            $response = $this->sendQueryRequest($inputData);

            return [
                'success' => true,
                'response' => $response,
            ];

        } catch (\Exception $e) {
            Log::error('VNPay query transaction failed', [
                'vnp_TxnRef' => $vnpTxnRef,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Gửi query request đến VNPay
     */
    protected function sendQueryRequest(array $data): array
    {
        // Implement HTTP client để gửi request đến VNPay API
        // Hiện tại chưa implement vì cần thêm HTTP client
        return [
            'message' => 'Query function not implemented yet',
            'data' => $data,
        ];
    }

    /**
     * Test VNPay connection configuration
     */
    public function testConnection(): array
    {
        try {
            if (empty($this->vnpTmnCode) || empty($this->vnpHashSecret) || empty($this->vnpUrl)) {
                return [
                    'success' => false,
                    'error' => 'VNPay configuration incomplete',
                    'message' => 'Missing TMN Code, Hash Secret, or URL',
                ];
            }

            return [
                'success' => true,
                'tmn_code' => $this->vnpTmnCode,
                'url' => $this->vnpUrl,
                'return_url' => $this->vnpReturnUrl,
                'ipn_url' => $this->vnpIpnUrl,
                'message' => 'VNPay configuration is valid',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'VNPay configuration test failed',
            ];
        }
    }
}
    {
        try {
            // Verify signature
            $vnpSecureHash = $params['vnp_SecureHash'] ?? '';
            unset($params['vnp_SecureHash'], $params['vnp_SecureHashType']);
            
            if (!$this->verifySignature($params, $vnpSecureHash)) {
                Log::warning('VNPay callback signature verification failed', [
                    'params' => $params
                ]);
                
                return [
                    'success' => false,
                    'message' => 'Invalid signature'
                ];
            }

            $vnpResponseCode = $params['vnp_ResponseCode'] ?? '';
            $vnpTransactionNo = $params['vnp_TransactionNo'] ?? '';
            $vnpTxnRef = $params['vnp_TxnRef'] ?? '';
            $vnpAmount = $params['vnp_Amount'] ?? 0;

            // Extract order ID from transaction reference
            $orderNumber = explode('_', $vnpTxnRef)[0] ?? '';
            $order = Order::where('order_number', $orderNumber)->first();

            if (!$order) {
                Log::error('Order not found for VNPay callback', [
                    'order_number' => $orderNumber,
                    'vnp_txn_ref' => $vnpTxnRef
                ]);
                
                return [
                    'success' => false,
                    'message' => 'Order not found'
                ];
            }

            if ($vnpResponseCode === '00') {
                // Payment successful
                $order->update([
                    'status' => 'paid',
                    'payment_status' => 'completed',
                    'paid_at' => now(),
                ]);

                // Update or create transaction record
                $transaction = PaymentTransaction::where('order_id', $order->id)
                    ->where('payment_method', 'vnpay')
                    ->first();

                if ($transaction) {
                    $transaction->update([
                        'transaction_id' => $vnpTransactionNo,
                        'status' => 'completed',
                        'gateway_response' => json_encode($params),
                        'processed_at' => now(),
                    ]);
                } else {
                    PaymentTransaction::create([
                        'order_id' => $order->id,
                        'payment_method' => 'vnpay',
                        'transaction_id' => $vnpTransactionNo,
                        'amount' => $vnpAmount / 100, // Convert back from VNPay format
                        'currency' => 'VND',
                        'status' => 'completed',
                        'gateway_response' => json_encode($params),
                        'processed_at' => now(),
                    ]);
                }

                Log::info('VNPay payment successful', [
                    'order_id' => $order->id,
                    'transaction_no' => $vnpTransactionNo,
                    'amount' => $vnpAmount
                ]);

                return [
                    'success' => true,
                    'message' => 'Payment successful',
                    'data' => [
                        'order_id' => $order->id,
                        'transaction_id' => $vnpTransactionNo,
                        'amount' => $vnpAmount / 100
                    ]
                ];

            } else {
                // Payment failed
                $order->update([
                    'status' => 'payment_failed',
                    'payment_status' => 'failed',
                ]);

                // Update transaction record
                $transaction = PaymentTransaction::where('order_id', $order->id)
                    ->where('payment_method', 'vnpay')
                    ->first();

                if ($transaction) {
                    $transaction->update([
                        'status' => 'failed',
                        'gateway_response' => json_encode($params),
                        'processed_at' => now(),
                    ]);
                }

                $errorMessage = $this->getVNPayErrorMessage($vnpResponseCode);
                
                Log::info('VNPay payment failed', [
                    'order_id' => $order->id,
                    'response_code' => $vnpResponseCode,
                    'error_message' => $errorMessage
                ]);

                return [
                    'success' => false,
                    'message' => $errorMessage,
                    'data' => [
                        'order_id' => $order->id,
                        'response_code' => $vnpResponseCode
                    ]
                ];
            }

        } catch (\Exception $e) {
            Log::error('VNPay callback processing error', [
                'error' => $e->getMessage(),
                'params' => $params
            ]);

            return [
                'success' => false,
                'message' => 'Callback processing failed'
            ];
        }
    }

    /**
     * Xử lý IPN (Instant Payment Notification) từ VNPay
     */
    public function handleIPN(array $vnpayData): array
    {
        try {
            $vnpSecureHash = $vnpayData['vnp_SecureHash'] ?? '';
            unset($vnpayData['vnp_SecureHash']);
            unset($vnpayData['vnp_SecureHashType']);

            ksort($vnpayData);
            $hashData = "";
            $i = 0;

            foreach ($vnpayData as $key => $value) {
                if ($i == 1) {
                    $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
                } else {
                    $hashData .= urlencode($key) . "=" . urlencode($value);
                    $i = 1;
                }
            }

            $secureHash = hash_hmac('sha512', $hashData, $this->vnpHashSecret);

            if ($secureHash !== $vnpSecureHash) {
                Log::warning('VNPay IPN invalid signature', [
                    'vnp_TxnRef' => $vnpayData['vnp_TxnRef'] ?? 'unknown',
                ]);

                return [
                    'RspCode' => '97',
                    'Message' => 'Invalid signature'
                ];
            }

            $vnpTxnRef = $vnpayData['vnp_TxnRef'];
            $vnpAmount = $vnpayData['vnp_Amount'];
            $vnpResponseCode = $vnpayData['vnp_ResponseCode'];

            // Tìm transaction
            $transaction = PaymentTransaction::where('gateway_transaction_id', $vnpTxnRef)->first();

            if (!$transaction) {
                Log::warning('VNPay IPN transaction not found', [
                    'vnp_TxnRef' => $vnpTxnRef,
                ]);

                return [
                    'RspCode' => '01',
                    'Message' => 'Order not found'
                ];
            }

            // Kiểm tra amount
            $expectedAmount = $transaction->amount * 100;
            if ($vnpAmount != $expectedAmount) {
                Log::warning('VNPay IPN amount mismatch', [
                    'vnp_TxnRef' => $vnpTxnRef,
                    'expected_amount' => $expectedAmount,
                    'received_amount' => $vnpAmount,
                ]);

                return [
                    'RspCode' => '04',
                    'Message' => 'Invalid amount'
                ];
            }

            // Kiểm tra trạng thái transaction
            if ($transaction->status === 'completed') {
                Log::info('VNPay IPN for already completed transaction', [
                    'vnp_TxnRef' => $vnpTxnRef,
                ]);

                return [
                    'RspCode' => '00',
                    'Message' => 'Confirm success'
                ];
            }

            if ($vnpResponseCode == '00') {
                // Cập nhật transaction status (nếu chưa được cập nhật)
                if ($transaction->status === 'pending') {
                    $transaction->markAsCompleted([
                        'vnpay_ipn_data' => $vnpayData,
                        'vnpay_response_code' => $vnpResponseCode,
                    ]);

                    // Process order nếu chưa được process
                    $order = $transaction->order;
                    if ($order->payment_status !== 'completed') {
                        $orderService = app(OrderService::class);
                        $orderService->processSuccessfulPayment($order, [
                            'transaction_id' => $transaction->transaction_id,
                            'gateway' => 'vnpay',
                        ]);
                    }
                }

                Log::info('VNPay IPN processed successfully', [
                    'vnp_TxnRef' => $vnpTxnRef,
                ]);

                return [
                    'RspCode' => '00',
                    'Message' => 'Confirm success'
                ];

            } else {
                Log::warning('VNPay IPN payment failed', [
                    'vnp_TxnRef' => $vnpTxnRef,
                    'vnp_ResponseCode' => $vnpResponseCode,
                ]);

                return [
                    'RspCode' => '00',
                    'Message' => 'Confirm success'
                ];
            }

        } catch (\Exception $e) {
            Log::error('VNPay IPN processing failed', [
                'error' => $e->getMessage(),
                'vnpay_data' => $vnpayData,
            ]);

            return [
                'RspCode' => '99',
                'Message' => 'Unknown error'
            ];
        }
    }

    /**
     * Verify VNPay signature
     */
    protected function verifySignature(array $params, string $secureHash): bool
    {
        ksort($params);
        $hashData = '';
        
        foreach ($params as $key => $value) {
            if (strlen($value) > 0 && (substr($key, 0, 4) === "vnp_")) {
                $hashData .= $key . '=' . $value . '&';
            }
        }
        
        $hashData = rtrim($hashData, '&');
        $secureHashGenerated = hash_hmac('sha512', $hashData, $this->vnpHashSecret);
        
        return hash_equals($secureHashGenerated, $secureHash);
    }

    /**
     * Lấy error message từ VNPay response code
     */
    protected function getVNPayErrorMessage(string $responseCode): string
    {
        $messages = [
            '00' => 'Giao dịch thành công',
            '07' => 'Trừ tiền thành công. Giao dịch bị nghi ngờ (liên quan tới lừa đảo, giao dịch bất thường).',
            '09' => 'Giao dịch không thành công do: Thẻ/Tài khoản của khách hàng chưa đăng ký dịch vụ InternetBanking tại ngân hàng.',
            '10' => 'Giao dịch không thành công do: Khách hàng xác thực thông tin thẻ/tài khoản không đúng quá 3 lần',
            '11' => 'Giao dịch không thành công do: Đã hết hạn chờ thanh toán. Xin quý khách vui lòng thực hiện lại giao dịch.',
            '12' => 'Giao dịch không thành công do: Thẻ/Tài khoản của khách hàng bị khóa.',
            '13' => 'Giao dịch không thành công do Quý khách nhập sai mật khẩu xác thực giao dịch (OTP).',
            '24' => 'Giao dịch không thành công do: Khách hàng hủy giao dịch',
            '51' => 'Giao dịch không thành công do: Tài khoản của quý khách không đủ số dư để thực hiện giao dịch.',
            '65' => 'Giao dịch không thành công do: Tài khoản của Quý khách đã vượt quá hạn mức giao dịch trong ngày.',
            '75' => 'Ngân hàng thanh toán đang bảo trì.',
            '79' => 'Giao dịch không thành công do: KH nhập sai mật khẩu thanh toán quá số lần quy định.',
            '99' => 'Các lỗi khác (lỗi còn lại, không có trong danh sách mã lỗi đã liệt kê)',
        ];

        return $messages[$responseCode] ?? "Lỗi không xác định (Code: {$responseCode})";
    }

    /**
     * Kiểm tra cấu hình VNPay
     */
    public function testConnection(): array
    {
        try {
            if (empty($this->vnpTmnCode) || empty($this->vnpHashSecret) || empty($this->vnpUrl)) {
                return [
                    'success' => false,
                    'error' => 'VNPay configuration incomplete',
                    'message' => 'Missing TMN Code, Hash Secret, or URL',
                ];
            }

            return [
                'success' => true,
                'tmn_code' => $this->vnpTmnCode,
                'url' => $this->vnpUrl,
                'return_url' => $this->vnpReturnUrl,
                'ipn_url' => $this->vnpIpnUrl,
                'message' => 'VNPay configuration is valid',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'VNPay configuration test failed',
            ];
        }
    }
}
