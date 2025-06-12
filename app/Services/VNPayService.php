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
     * Handle VNPay IPN (Instant Payment Notification)
     */
    public function handleIpn(array $params): array
    {
        try {
            // Verify signature
            $vnpSecureHash = $params['vnp_SecureHash'] ?? '';
            unset($params['vnp_SecureHash'], $params['vnp_SecureHashType']);

            if (!$this->verifySignature($params, $vnpSecureHash)) {
                Log::warning('VNPay IPN signature verification failed', [
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
                Log::error('Order not found for VNPay IPN', [
                    'order_number' => $orderNumber,
                    'vnp_txn_ref' => $vnpTxnRef
                ]);

                return [
                    'success' => false,
                    'message' => 'Order not found'
                ];
            }

            // Check if payment amount matches
            $expectedAmount = $order->total_amount * 100; // Convert to VNPay format
            if ($vnpAmount != $expectedAmount) {
                Log::error('VNPay IPN amount mismatch', [
                    'order_id' => $order->id,
                    'expected_amount' => $expectedAmount,
                    'received_amount' => $vnpAmount
                ]);

                return [
                    'success' => false,
                    'message' => 'Amount mismatch'
                ];
            }

            if ($vnpResponseCode === '00') {
                // Payment successful - only update if not already processed
                if ($order->status !== 'paid') {
                    $order->update([
                        'status' => 'paid',
                        'payment_status' => 'completed',
                        'paid_at' => now(),
                    ]);

                    Log::info('Order status updated via VNPay IPN', [
                        'order_id' => $order->id,
                        'transaction_no' => $vnpTransactionNo
                    ]);
                }

                return [
                    'success' => true,
                    'message' => 'IPN processed successfully'
                ];

            } else {
                Log::info('VNPay IPN payment failed', [
                    'order_id' => $order->id,
                    'response_code' => $vnpResponseCode
                ]);

                return [
                    'success' => true,
                    'message' => 'IPN processed (payment failed)'
                ];
            }

        } catch (\Exception $e) {
            Log::error('VNPay IPN processing error', [
                'error' => $e->getMessage(),
                'params' => $params
            ]);

            return [
                'success' => false,
                'message' => 'IPN processing failed'
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
     * Get VNPay error message by response code
     */
    protected function getVNPayErrorMessage(string $responseCode): string
    {
        $errorMessages = [
            '00' => 'Giao dịch thành công',
            '07' => 'Trừ tiền thành công. Giao dịch bị nghi ngờ (liên quan tới lừa đảo, giao dịch bất thường)',
            '09' => 'Giao dịch không thành công do: Thẻ/Tài khoản của khách hàng chưa đăng ký dịch vụ InternetBanking tại ngân hàng',
            '10' => 'Giao dịch không thành công do: Khách hàng xác thực thông tin thẻ/tài khoản không đúng quá 3 lần',
            '11' => 'Giao dịch không thành công do: Đã hết hạn chờ thanh toán. Xin quý khách vui lòng thực hiện lại giao dịch',
            '12' => 'Giao dịch không thành công do: Thẻ/Tài khoản của khách hàng bị khóa',
            '13' => 'Giao dịch không thành công do Quý khách nhập sai mật khẩu xác thực giao dịch (OTP)',
            '24' => 'Giao dịch không thành công do: Khách hàng hủy giao dịch',
            '51' => 'Giao dịch không thành công do: Tài khoản của quý khách không đủ số dư để thực hiện giao dịch',
            '65' => 'Giao dịch không thành công do: Tài khoản của Quý khách đã vượt quá hạn mức giao dịch trong ngày',
            '75' => 'Ngân hàng thanh toán đang bảo trì',
            '79' => 'Giao dịch không thành công do: KH nhập sai mật khẩu thanh toán quá số lần quy định',
            '99' => 'Các lỗi khác (lỗi còn lại, không có trong danh sách mã lỗi đã liệt kê)',
        ];

        return $errorMessages[$responseCode] ?? 'Lỗi không xác định';
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
