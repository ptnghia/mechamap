<?php

namespace App\Services;

use App\Models\MarketplaceProduct;
use App\Models\MarketplaceOrder;
use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class MarketplaceNotificationService
{
    /**
     * Handle order status change notifications
     */
    public static function handleOrderStatusChange(MarketplaceOrder $order, string $oldStatus, string $newStatus): void
    {
        try {
            Log::info('Order status changed', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'customer_id' => $order->customer_id
            ]);

            // Notify customer about order status change
            static::notifyCustomerOrderStatusChange($order, $oldStatus, $newStatus);

            // Notify sellers about order status change
            static::notifySellersOrderStatusChange($order, $oldStatus, $newStatus);

        } catch (\Exception $e) {
            Log::error('Failed to handle order status change notifications', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle order payment status change notifications
     */
    public static function handleOrderPaymentStatusChange(MarketplaceOrder $order, string $oldStatus, string $newStatus): void
    {
        try {
            Log::info('Order payment status changed', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'old_payment_status' => $oldStatus,
                'new_payment_status' => $newStatus
            ]);

            // Notify customer about payment status change
            static::notifyCustomerPaymentStatusChange($order, $oldStatus, $newStatus);

            // Notify sellers about payment confirmation
            if ($newStatus === 'paid') {
                static::notifySellersPaymentReceived($order);
            }

        } catch (\Exception $e) {
            Log::error('Failed to handle order payment status change notifications', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle product approval/rejection notifications
     */
    public static function handleProductApprovalStatusChange(MarketplaceProduct $product, string $oldStatus, string $newStatus, ?string $reason = null): void
    {
        try {
            $seller = $product->seller ? $product->seller->user : null;
            if (!$seller) {
                Log::warning('Seller not found for product approval notification', [
                    'product_id' => $product->id
                ]);
                return;
            }

            Log::info('Product approval status changed', [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'seller_id' => $seller->id
            ]);

            if ($newStatus === 'approved') {
                static::notifySellerProductApproved($product, $seller);
            } elseif ($newStatus === 'rejected') {
                static::notifySellerProductRejected($product, $seller, $reason);
            }

        } catch (\Exception $e) {
            Log::error('Failed to handle product approval status change notifications', [
                'product_id' => $product->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Notify customer about order status change
     */
    private static function notifyCustomerOrderStatusChange(MarketplaceOrder $order, string $oldStatus, string $newStatus): void
    {
        $customer = $order->customer;
        if (!$customer) {
            return;
        }

        $statusMessages = [
            'pending' => 'đang chờ xử lý',
            'confirmed' => 'đã được xác nhận',
            'processing' => 'đang được xử lý',
            'shipped' => 'đã được gửi đi',
            'delivered' => 'đã được giao',
            'completed' => 'đã hoàn thành',
            'cancelled' => 'đã bị hủy',
            'refunded' => 'đã được hoàn tiền'
        ];

        $title = 'Cập nhật trạng thái đơn hàng';
        $statusText = isset($statusMessages[$newStatus]) ? $statusMessages[$newStatus] : $newStatus;
        $message = "Đơn hàng #{$order->order_number} {$statusText}";

        $data = [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'status_text' => $statusText,
            'total_amount' => $order->total_amount,
            'action_url' => route('marketplace.orders.show', $order->id),
        ];

        // Send email for important status changes
        $emailStatuses = ['confirmed', 'shipped', 'delivered', 'completed', 'cancelled'];
        $sendEmail = in_array($newStatus, $emailStatuses);

        NotificationService::send(
            $customer,
            'order_status_changed',
            $title,
            $message,
            $data,
            $sendEmail
        );
    }

    /**
     * Notify sellers about order status change
     */
    private static function notifySellersOrderStatusChange(MarketplaceOrder $order, string $oldStatus, string $newStatus): void
    {
        // Get unique sellers from order items
        $sellerIds = $order->items()->distinct('seller_id')->pluck('seller_id');

        foreach ($sellerIds as $sellerId) {
            $marketplaceSeller = \App\Models\MarketplaceSeller::find($sellerId);
            $seller = $marketplaceSeller ? $marketplaceSeller->user : null;
            if (!$seller) {
                continue;
            }

            $title = 'Cập nhật đơn hàng';
            $message = "Đơn hàng #{$order->order_number} đã chuyển sang trạng thái: {$newStatus}";

            $data = [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'customer_name' => $order->customer ? $order->customer->name : 'Khách hàng',
                'action_url' => route('marketplace.seller.orders.show', $order->id),
            ];

            NotificationService::send(
                $seller,
                'seller_order_status_changed',
                $title,
                $message,
                $data,
                false // No email for sellers on status change
            );
        }
    }

    /**
     * Notify customer about payment status change
     */
    private static function notifyCustomerPaymentStatusChange(MarketplaceOrder $order, string $oldStatus, string $newStatus): void
    {
        $customer = $order->customer;
        if (!$customer) {
            return;
        }

        $statusMessages = [
            'pending' => 'đang chờ thanh toán',
            'paid' => 'đã thanh toán thành công',
            'failed' => 'thanh toán thất bại',
            'refunded' => 'đã được hoàn tiền',
            'cancelled' => 'đã hủy thanh toán'
        ];

        $title = 'Cập nhật thanh toán';
        $paymentStatusText = isset($statusMessages[$newStatus]) ? $statusMessages[$newStatus] : $newStatus;
        $message = "Thanh toán cho đơn hàng #{$order->order_number} {$paymentStatusText}";

        $data = [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'old_payment_status' => $oldStatus,
            'new_payment_status' => $newStatus,
            'payment_status_text' => $paymentStatusText,
            'total_amount' => $order->total_amount,
            'action_url' => route('marketplace.orders.show', $order->id),
        ];

        // Send email for payment confirmations and failures
        $emailStatuses = ['paid', 'failed', 'refunded'];
        $sendEmail = in_array($newStatus, $emailStatuses);

        NotificationService::send(
            $customer,
            'order_payment_status_changed',
            $title,
            $message,
            $data,
            $sendEmail
        );
    }

    /**
     * Notify sellers about payment received
     */
    private static function notifySellersPaymentReceived(MarketplaceOrder $order): void
    {
        $sellerIds = $order->items()->distinct('seller_id')->pluck('seller_id');

        foreach ($sellerIds as $sellerId) {
            $marketplaceSeller = \App\Models\MarketplaceSeller::find($sellerId);
            $seller = $marketplaceSeller ? $marketplaceSeller->user : null;
            if (!$seller) {
                continue;
            }

            $title = 'Thanh toán đã nhận';
            $message = "Đã nhận thanh toán cho đơn hàng #{$order->order_number}";

            $data = [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'total_amount' => $order->total_amount,
                'customer_name' => $order->customer ? $order->customer->name : 'Khách hàng',
                'action_url' => route('marketplace.seller.orders.show', $order->id),
            ];

            NotificationService::send(
                $seller,
                'seller_payment_received',
                $title,
                $message,
                $data,
                true // Send email for payment confirmations
            );
        }
    }

    /**
     * Notify seller about product approval
     */
    private static function notifySellerProductApproved(MarketplaceProduct $product, User $seller): void
    {
        $title = 'Sản phẩm được duyệt';
        $message = "Sản phẩm \"{$product->name}\" đã được duyệt và hiển thị công khai";

        $data = [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_slug' => $product->slug,
            'status' => 'approved',
            'action_url' => route('marketplace.products.show', $product->slug),
        ];

        NotificationService::send(
            $seller,
            'product_approved',
            $title,
            $message,
            $data,
            true // Send email for approvals
        );
    }

    /**
     * Notify seller about product rejection
     */
    private static function notifySellerProductRejected(MarketplaceProduct $product, User $seller, ?string $reason = null): void
    {
        $title = 'Sản phẩm bị từ chối';
        $message = "Sản phẩm \"{$product->name}\" đã bị từ chối";
        if ($reason) {
            $message .= ". Lý do: {$reason}";
        }

        $data = [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_slug' => $product->slug,
            'status' => 'rejected',
            'reason' => $reason,
            'action_url' => route('marketplace.seller.products.edit', $product->id),
        ];

        NotificationService::send(
            $seller,
            'product_rejected',
            $title,
            $message,
            $data,
            true // Send email for rejections
        );
    }
}
