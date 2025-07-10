<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SePayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SePayWebhookController extends Controller
{
    protected SePayService $sepayService;

    public function __construct(SePayService $sepayService)
    {
        $this->sepayService = $sepayService;
    }

    /**
     * Handle SePay webhook
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        try {
            // Log webhook request for debugging
            Log::info('SePay webhook received', [
                'headers' => $request->headers->all(),
                'body' => $request->all()
            ]);

            // Get webhook data
            $webhookData = $request->all();
            
            if (empty($webhookData)) {
                Log::warning('SePay webhook: Empty data received');
                return response()->json([
                    'success' => false,
                    'message' => 'No data received'
                ], 400);
            }

            // Process webhook
            $result = $this->sepayService->handleWebhook($webhookData);

            if ($result['success']) {
                Log::info('SePay webhook processed successfully', [
                    'message' => $result['message']
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => $result['message']
                ]);
            } else {
                Log::warning('SePay webhook processing failed', [
                    'message' => $result['message'],
                    'data' => $webhookData
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('SePay webhook exception', [
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
     * Check payment status endpoint for AJAX polling
     */
    public function checkPaymentStatus(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'order_id' => 'required|integer|exists:marketplace_orders,id'
            ]);

            $orderId = $request->order_id;
            $order = \App\Models\MarketplaceOrder::find($orderId);

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            $paymentStatus = $this->sepayService->checkPaymentStatus($order);

            return response()->json([
                'success' => true,
                'payment_status' => $order->payment_status,
                'order_status' => $order->status,
                'data' => $paymentStatus
            ]);

        } catch (\Exception $e) {
            Log::error('SePay payment status check failed', [
                'error' => $e->getMessage(),
                'order_id' => $request->order_id ?? null
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to check payment status'
            ], 500);
        }
    }
}
