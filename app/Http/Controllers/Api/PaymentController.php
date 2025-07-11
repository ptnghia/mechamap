<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Services\OrderService;
use App\Services\StripeService;
use App\Services\VNPayService;
use App\Services\SePayService;
use App\Services\CentralizedPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    protected OrderService $orderService;
    protected StripeService $stripeService;
    protected VNPayService $vnpayService;
    protected SePayService $sepayService;
    protected CentralizedPaymentService $centralizedPaymentService;

    public function __construct(
        OrderService $orderService,
        StripeService $stripeService,
        VNPayService $vnpayService,
        SePayService $sepayService,
        CentralizedPaymentService $centralizedPaymentService
    ) {
        $this->orderService = $orderService;
        $this->stripeService = $stripeService;
        $this->vnpayService = $vnpayService;
        $this->sepayService = $sepayService;
        $this->centralizedPaymentService = $centralizedPaymentService;
        $this->middleware('auth:sanctum')->except(['webhook', 'vnpayCallback', 'vnpayIpn', 'paymentMethods']);
    }

    /**
     * Khởi tạo thanh toán cho order
     */
    public function initiate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
            'payment_method' => 'required|string|in:stripe,vnpay',
            'return_url' => 'required|url',
            'cancel_url' => 'url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            $order = Order::where('id', $request->order_id)
                ->where('user_id', $user->id)
                ->where('status', 'pending')
                ->firstOrFail();

            // Validate order trước khi payment
            $validation = $this->orderService->validateOrderForPayment($order);
            if (!$validation['is_valid']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order không hợp lệ để thanh toán',
                    'data' => ['issues' => $validation['issues']]
                ], 400);
            }

            $paymentData = null;
            $paymentMethod = $request->payment_method;

            if ($paymentMethod === 'stripe') {
                $paymentData = $this->stripeService->createPaymentIntent($order);
            } elseif ($paymentMethod === 'vnpay') {
                $clientIp = $request->ip();
                $paymentData = $this->vnpayService->createPaymentUrl($order, $clientIp);
            } elseif ($paymentMethod === 'sepay') {
                $paymentData = $this->sepayService->createPaymentUrl($order);
            }

            // Cập nhật order status
            $order->update([
                'status' => 'processing',
                'payment_method' => $paymentMethod,
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'order_id' => $order->id,
                    'payment_method' => $paymentMethod,
                    'payment_data' => $paymentData,
                ],
                'message' => 'Khởi tạo thanh toán thành công'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy order hoặc order không thể thanh toán'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Lỗi khởi tạo thanh toán', [
                'user_id' => Auth::id(),
                'order_id' => $request->order_id,
                'payment_method' => $request->payment_method,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi khởi tạo thanh toán'
            ], 500);
        }
    }

    /**
     * Xác nhận thanh toán Stripe
     */
    public function confirmStripe(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'payment_intent_id' => 'required|string',
            'order_id' => 'required|exists:orders,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            $order = Order::where('id', $request->order_id)
                ->where('user_id', $user->id)
                ->firstOrFail();

            $result = $this->stripeService->handlePaymentSuccess($request->payment_intent_id);

            if ($result['success']) {
                // Process order completion
                $this->orderService->completeOrder($order);

                return response()->json([
                    'success' => true,
                    'data' => [
                        'order' => $order->fresh()->load(['items.product', 'transactions']),
                        'payment_details' => $result['data'],
                    ],
                    'message' => 'Thanh toán thành công'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Thanh toán thất bại'
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('Lỗi xác nhận thanh toán Stripe', [
                'user_id' => Auth::id(),
                'payment_intent_id' => $request->payment_intent_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xác nhận thanh toán'
            ], 500);
        }
    }

    /**
     * Stripe Webhook Handler
     */
    public function webhook(Request $request): JsonResponse
    {
        try {
            $payload = $request->getContent();
            $sig_header = $request->header('Stripe-Signature');

            // Kiểm tra signature header is required
            if (!$sig_header) {
                Log::warning('Stripe webhook missing signature header');
                return response()->json([
                    'error' => 'Missing Stripe-Signature header',
                    'message' => 'Webhook signature is required for verification'
                ], 400);
            }

            $result = $this->stripeService->handleWebhook($payload, $sig_header);

            if ($result['success']) {
                return response()->json(['received' => true]);
            } else {
                Log::error('Stripe webhook verification failed', [
                    'error' => $result['message']
                ]);
                return response()->json(['error' => 'Webhook verification failed'], 400);
            }

        } catch (\Exception $e) {
            Log::error('Stripe webhook error', [
                'error' => $e->getMessage(),
                'payload' => $request->getContent()
            ]);

            return response()->json(['error' => 'Webhook processing failed'], 500);
        }
    }

    /**
     * Callback từ VNPay
     */
    public function vnpayCallback(Request $request): JsonResponse
    {
        try {
            $result = $this->vnpayService->handleCallback($request->all());

            if ($result['success']) {
                // Redirect to frontend with success
                $frontendUrl = config('app.frontend_url', 'https://mechamap.test');

                return response()->json([
                    'success' => true,
                    'message' => 'Thanh toán thành công',
                    'data' => $result['data'],
                    'redirect_url' => "{$frontendUrl}/payment/success?order_id={$result['data']['order_id']}"
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                    'redirect_url' => config('app.frontend_url', 'https://mechamap.test') . '/payment/failed'
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('VNPay callback error', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Lỗi xử lý callback thanh toán',
                'redirect_url' => config('app.frontend_url', 'https://mechamap.test') . '/payment/error'
            ], 500);
        }
    }

    /**
     * VNPay IPN (Instant Payment Notification) - Server to server
     */
    public function vnpayIpn(Request $request): JsonResponse
    {
        try {
            $result = $this->vnpayService->handleIpn($request->all());

            if ($result['success']) {
                // IPN processing successful
                return response()->json([
                    'RspCode' => '00',
                    'Message' => 'Confirm Success'
                ]);
            } else {
                Log::error('VNPay IPN verification failed', [
                    'error' => $result['message'],
                    'request_data' => $request->all()
                ]);

                return response()->json([
                    'RspCode' => '97',
                    'Message' => 'Invalid Signature'
                ]);
            }

        } catch (\Exception $e) {
            Log::error('VNPay IPN error', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'RspCode' => '99',
                'Message' => 'Unknown Error'
            ]);
        }
    }

    /**
     * Lấy trạng thái thanh toán của order
     */
    public function status(int $orderId): JsonResponse
    {
        try {
            $user = Auth::user();
            $order = Order::where('id', $orderId)
                ->where('user_id', $user->id)
                ->with(['transactions' => function($query) {
                    $query->orderBy('created_at', 'desc');
                }])
                ->firstOrFail();

            $latestTransaction = $order->transactions->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'order_id' => $order->id,
                    'order_status' => $order->status,
                    'payment_status' => $latestTransaction?->status ?? 'unknown',
                    'payment_method' => $order->payment_method,
                    'total_amount' => $order->total_amount,
                    'last_transaction' => $latestTransaction,
                ],
                'message' => 'Lấy trạng thái thanh toán thành công'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy order'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Lỗi lấy trạng thái thanh toán', [
                'user_id' => Auth::id(),
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy trạng thái thanh toán'
            ], 500);
        }
    }

    /**
     * Hủy thanh toán
     */
    public function cancel(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
            'reason' => 'string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            $order = Order::where('id', $request->order_id)
                ->where('user_id', $user->id)
                ->whereIn('status', ['pending', 'processing'])
                ->firstOrFail();

            // Hủy payment intent/transaction nếu cần
            if ($order->payment_method === 'stripe' && $order->payment_intent_id) {
                $this->stripeService->cancelPaymentIntent($order->payment_intent_id);
            }

            // Cập nhật order status
            $order->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => $request->reason ?? 'User cancelled',
            ]);

            // Restore cart items
            $this->orderService->restoreCartFromOrder($order);

            return response()->json([
                'success' => true,
                'data' => [
                    'order_id' => $order->id,
                    'status' => $order->status,
                ],
                'message' => 'Đã hủy thanh toán'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy order hoặc order không thể hủy'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Lỗi hủy thanh toán', [
                'user_id' => Auth::id(),
                'order_id' => $request->order_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi hủy thanh toán'
            ], 500);
        }
    }

    /**
     * Lấy danh sách payment methods của user
     */
    public function paymentMethods(): JsonResponse
    {
        try {
            // Return available payment methods based on configuration
            $availableMethods = [];

            // Check if Stripe is configured
            if ($this->stripeService->isConfigured()) {
                $availableMethods[] = [
                    'id' => 'stripe',
                    'name' => 'Credit/Debit Card',
                    'description' => 'Pay with Visa, Mastercard, or other cards',
                    'icon' => 'credit-card',
                    'is_active' => true,
                    'supported_currencies' => ['USD', 'EUR', 'VND'],
                ];
            }

            // Check if VNPay is configured (DEPRECATED)
            if ($this->vnpayService->isConfigured()) {
                $availableMethods[] = [
                    'id' => 'vnpay',
                    'name' => 'VNPay',
                    'description' => 'Thanh toán qua VNPay (ATM, Internet Banking, QR Code)',
                    'icon' => 'vnpay',
                    'is_active' => false, // Deprecated
                    'supported_currencies' => ['VND'],
                ];
            }

            // Check if SePay is configured
            if ($this->sepayService->isConfigured()) {
                $availableMethods[] = [
                    'id' => 'sepay',
                    'name' => 'SePay',
                    'description' => 'Thanh toán qua chuyển khoản ngân hàng (QR Code)',
                    'icon' => 'bank',
                    'is_active' => true,
                    'supported_currencies' => ['VND'],
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $availableMethods,
                'message' => 'Lấy payment methods thành công'
            ]);

        } catch (\Exception $e) {
            Log::error('Lỗi lấy payment methods', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy payment methods'
            ], 500);
        }
    }

    /**
     * Tạo refund cho order
     */
    public function refund(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
            'amount' => 'numeric|min:0',
            'reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            $order = Order::where('id', $request->order_id)
                ->where('user_id', $user->id)
                ->where('status', 'completed')
                ->firstOrFail();

            $refundAmount = $request->amount ?? $order->total_amount;

            $result = null;
            if ($order->payment_method === 'stripe') {
                $result = $this->stripeService->createRefund($order, $refundAmount, $request->reason);
            }
            // VNPay refund sẽ được implement later

            if ($result && $result['success']) {
                return response()->json([
                    'success' => true,
                    'data' => $result['data'],
                    'message' => 'Yêu cầu hoàn tiền thành công'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Không thể tạo refund'
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('Lỗi tạo refund', [
                'user_id' => Auth::id(),
                'order_id' => $request->order_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tạo refund'
            ], 500);
        }
    }

    /**
     * Tạo Stripe Payment Intent
     */
    public function createStripeIntent(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            $order = Order::where('id', $request->order_id)
                ->where('user_id', $user->id)
                ->where('status', 'pending')
                ->firstOrFail();

            if (!$this->stripeService->isConfigured()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stripe payment gateway không được cấu hình'
                ], 503);
            }

            $paymentIntent = $this->stripeService->createPaymentIntent($order);

            // Cập nhật order status
            $order->update([
                'status' => 'processing',
                'payment_method' => 'stripe',
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'order_id' => $order->id,
                    'client_secret' => $paymentIntent['client_secret'],
                    'payment_intent_id' => $paymentIntent['id'],
                    'amount' => $paymentIntent['amount'],
                    'currency' => $paymentIntent['currency'],
                ],
                'message' => 'Tạo Stripe Payment Intent thành công'
            ]);

        } catch (\Exception $e) {
            Log::error('Lỗi tạo Stripe Payment Intent', [
                'order_id' => $request->order_id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tạo payment intent'
            ], 500);
        }
    }

    /**
     * Tạo VNPay Payment URL
     */
    public function createVNPayPayment(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            $order = Order::where('id', $request->order_id)
                ->where('user_id', $user->id)
                ->where('status', 'pending')
                ->firstOrFail();

            if (!$this->vnpayService->isConfigured()) {
                return response()->json([
                    'success' => false,
                    'message' => 'VNPay payment gateway không được cấu hình'
                ], 503);
            }

            $clientIp = $request->ip();
            $paymentUrl = $this->vnpayService->createPaymentUrl($order, $clientIp);

            // Cập nhật order status
            $order->update([
                'status' => 'processing',
                'payment_method' => 'vnpay',
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'order_id' => $order->id,
                    'payment_url' => $paymentUrl,
                    'redirect_url' => $paymentUrl,
                ],
                'message' => 'Tạo VNPay Payment URL thành công'
            ]);

        } catch (\Exception $e) {
            Log::error('Lỗi tạo VNPay Payment URL', [
                'order_id' => $request->order_id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tạo payment URL'
            ], 500);
        }
    }

    /**
     * Tạo SePay Payment URL
     */
    public function createSePayPayment(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:marketplace_orders,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            $order = \App\Models\MarketplaceOrder::where('id', $request->order_id)
                ->where('customer_id', $user->id)
                ->where('status', 'pending')
                ->firstOrFail();

            if (!$this->sepayService->isConfigured()) {
                return response()->json([
                    'success' => false,
                    'message' => 'SePay payment gateway không được cấu hình'
                ], 503);
            }

            $paymentData = $this->sepayService->createPaymentUrl($order);

            if (!$paymentData['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $paymentData['message']
                ], 400);
            }

            // Cập nhật order status
            $order->update([
                'status' => 'processing',
                'payment_method' => 'sepay',
            ]);

            return response()->json([
                'success' => true,
                'data' => $paymentData['data'],
                'message' => 'Tạo SePay Payment URL thành công'
            ]);

        } catch (\Exception $e) {
            Log::error('Lỗi tạo SePay Payment URL', [
                'order_id' => $request->order_id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tạo payment URL'
            ], 500);
        }
    }

    /**
     * Xác nhận thanh toán cho order
     */
    public function confirmPayment(Request $request, $orderId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'payment_intent_id' => 'required_if:payment_method,stripe|string',
            'vnpay_params' => 'required_if:payment_method,vnpay|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            $order = Order::where('id', $orderId)
                ->where('user_id', $user->id)
                ->where('status', 'processing')
                ->firstOrFail();

            $success = false;
            $message = '';

            if ($order->payment_method === 'stripe' && $request->payment_intent_id) {
                $result = $this->stripeService->confirmPayment($request->payment_intent_id);
                $success = $result['success'];
                $message = $result['message'];
            } elseif ($order->payment_method === 'vnpay' && $request->vnpay_params) {
                $result = $this->vnpayService->handleCallback($request->vnpay_params);
                $success = $result['success'];
                $message = $result['message'];
            }

            if ($success) {
                // Cập nhật order status thành công
                $order->update([
                    'status' => 'completed',
                    'payment_status => "completed"',
                    'paid_at' => now(),
                ]);

                // TODO: Grant download access cho purchased products

                return response()->json([
                    'success' => true,
                    'data' => [
                        'order_id' => $order->id,
                        'status' => $order->status,
                        'payment_status' => $order->payment_status,
                    ],
                    'message' => 'Thanh toán được xác nhận thành công'
                ]);
            } else {
                // Cập nhật order status thất bại
                $order->update([
                    'status' => 'cancelled',
                    'payment_status' => 'failed',
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $message ?: 'Thanh toán thất bại'
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('Lỗi xác nhận thanh toán', [
                'order_id' => $orderId,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xác nhận thanh toán'
            ], 500);
        }
    }

    /**
     * Create Stripe payment intent for marketplace order
     */
    public function createMarketplaceStripeIntent(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|integer|exists:marketplace_orders,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            $order = \App\Models\MarketplaceOrder::where('id', $request->order_id)
                ->where('customer_id', $user->id)
                ->where('status', 'pending')
                ->firstOrFail();

            if (!$this->stripeService->isConfigured()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stripe payment gateway không được cấu hình'
                ], 503);
            }

            // Convert MarketplaceOrder to Order format for StripeService
            $orderData = (object) [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'total_amount' => $order->total_amount,
                'user_id' => $order->customer_id,
                'user' => $user,
            ];

            $paymentData = $this->stripeService->createPaymentIntent($orderData);

            if (!$paymentData['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $paymentData['message']
                ], 400);
            }

            // Cập nhật order status
            $order->update([
                'status' => 'processing',
                'payment_method' => 'stripe',
                'payment_intent_id' => $paymentData['data']['payment_intent_id'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'payment_url' => $paymentData['data']['payment_url'] ?? null,
                    'payment_intent_id' => $paymentData['data']['payment_intent_id'] ?? null,
                    'client_secret' => $paymentData['data']['client_secret'] ?? null,
                ],
                'message' => 'Tạo Stripe Payment Intent thành công'
            ]);

        } catch (\Exception $e) {
            Log::error('Stripe payment creation failed', [
                'order_id' => $request->order_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Lỗi hệ thống khi tạo thanh toán Stripe'
            ], 500);
        }
    }

    /**
     * 🏦 Create Centralized Stripe Payment Intent
     * All payments go to Admin account first
     */
    public function createCentralizedStripeIntent(Request $request): JsonResponse
    {
        $request->validate([
            'order_id' => 'required|exists:marketplace_orders,id',
        ]);

        try {
            $user = Auth::user();
            $order = \App\Models\MarketplaceOrder::where('id', $request->order_id)
                ->where('customer_id', $user->id)
                ->where('status', 'pending')
                ->firstOrFail();

            if (!$this->centralizedPaymentService->isStripeConfigured()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Centralized Stripe payment không được cấu hình'
                ], 503);
            }

            // Create centralized payment record
            $centralizedPayment = $this->centralizedPaymentService->createPayment($order, 'stripe');

            // Create Stripe Payment Intent
            $result = $this->centralizedPaymentService->createStripePaymentIntent($centralizedPayment);

            if ($result['success']) {
                // Update order status
                $order->update([
                    'status' => 'processing',
                    'payment_method' => 'stripe',
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Centralized Stripe Payment Intent created successfully',
                    'data' => [
                        'client_secret' => $result['client_secret'],
                        'payment_intent_id' => $result['payment_intent_id'],
                        'centralized_payment_id' => $centralizedPayment->id,
                        'order_id' => $order->id,
                        'amount' => $centralizedPayment->gross_amount,
                        'currency' => 'VND',
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['error']
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('Centralized Stripe payment creation failed', [
                'error' => $e->getMessage(),
                'order_id' => $request->order_id,
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create centralized payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🏦 Create Centralized SePay Payment
     * All payments go to Admin account first
     */
    public function createCentralizedSePayPayment(Request $request): JsonResponse
    {
        $request->validate([
            'order_id' => 'required|exists:marketplace_orders,id',
        ]);

        try {
            $user = Auth::user();
            $order = \App\Models\MarketplaceOrder::where('id', $request->order_id)
                ->where('customer_id', $user->id)
                ->where('status', 'pending')
                ->firstOrFail();

            if (!$this->centralizedPaymentService->isSePayConfigured()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Centralized SePay payment không được cấu hình'
                ], 503);
            }

            // Create centralized payment record
            $centralizedPayment = $this->centralizedPaymentService->createPayment($order, 'sepay');

            // Create SePay payment
            $result = $this->centralizedPaymentService->createSePayPayment($centralizedPayment);

            if ($result['success']) {
                // Update order status
                $order->update([
                    'status' => 'processing',
                    'payment_method' => 'sepay',
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Centralized SePay payment created successfully',
                    'data' => [
                        'payment_instructions' => $result['payment_instructions'],
                        'transaction_ref' => $result['transaction_ref'],
                        'centralized_payment_id' => $centralizedPayment->id,
                        'order_id' => $order->id,
                        'amount' => $centralizedPayment->gross_amount,
                        'currency' => 'VND',
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['error']
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('Centralized SePay payment creation failed', [
                'error' => $e->getMessage(),
                'order_id' => $request->order_id,
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create centralized SePay payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🏦 Centralized Payment Webhook Handler
     * Handle webhooks for centralized payments
     */
    public function centralizedWebhook(Request $request): JsonResponse
    {
        try {
            $payload = $request->getContent();
            $sig_header = $request->header('Stripe-Signature');

            if (!$sig_header) {
                Log::warning('Centralized payment webhook missing signature header');
                return response()->json([
                    'error' => 'Missing Stripe-Signature header',
                    'message' => 'Webhook signature is required for verification'
                ], 400);
            }

            $result = $this->centralizedPaymentService->handleStripeWebhook($payload, $sig_header);

            if ($result['success']) {
                return response()->json(['received' => true]);
            } else {
                Log::error('Centralized payment webhook verification failed', [
                    'error' => $result['message']
                ]);
                return response()->json(['error' => 'Webhook verification failed'], 400);
            }

        } catch (\Exception $e) {
            Log::error('Centralized payment webhook exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Webhook processing failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
