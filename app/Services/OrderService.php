<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\ShoppingCart;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class OrderService
{
    protected ShoppingCartService $cartService;

    public function __construct(ShoppingCartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Tạo order từ shopping cart
     */
    public function createOrderFromCart(User $user, array $billingAddress): Order
    {
        // Validate cart trước khi tạo order
        $validation = $this->cartService->validateCartForCheckout($user->id);

        if (!$validation['is_valid']) {
            $errors = $validation['errors'] ?? $validation['issues'] ?? ['Cart validation failed'];
            throw new \Exception('Cart validation failed: ' . implode(', ', $errors));
        }

        DB::beginTransaction();

        try {
            $order = Order::createFromCart($user, $billingAddress);

            DB::commit();
            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Lấy orders của user
     */
    public function getUserOrders(int $userId, array $filters = []): Collection
    {
        $query = Order::where('user_id', $userId)
                     ->with(['items.product', 'transactions'])
                     ->orderBy('created_at', 'desc');

        // Apply filters
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->get();
    }

    /**
     * Lấy order detail
     */
    public function getOrderDetail(int $orderId, int $userId = null): Order
    {
        $query = Order::with([
            'items.product',
            'items.seller',
            'transactions',
            'user'
        ]);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $query->findOrFail($orderId);
    }

    /**
     * Cập nhật order status
     */
    public function updateOrderStatus(int $orderId, string $status, string $notes = null): Order
    {
        $order = Order::findOrFail($orderId);

        $order->update([
            'status' => $status,
            'notes' => $notes,
        ]);

        // Xử lý theo status
        switch ($status) {
            case 'confirmed':
                $order->markAsConfirmed();
                break;
            case 'completed':
                $order->markAsCompleted();
                break;
            case 'cancelled':
                $order->cancel($notes);
                break;
        }

        return $order->fresh();
    }

    /**
     * Hủy order
     */
    public function cancelOrder(int $orderId, int $userId, string $reason = null): Order
    {
        $order = Order::where('id', $orderId)
                     ->where('user_id', $userId)
                     ->firstOrFail();

        if (!$order->canBeCancelled()) {
            throw new \Exception('Đơn hàng không thể hủy ở trạng thái hiện tại');
        }

        $order->cancel($reason);

        return $order;
    }

    /**
     * Xử lý payment thành công
     */
    public function processSuccessfulPayment(Order $order, array $paymentData): void
    {
        DB::beginTransaction();

        try {
            // Cập nhật order payment info
            $order->update([
                'payment_status' => 'completed',
                'payment_intent_id' => $paymentData['payment_intent_id'] ?? null,
                'transaction_id' => $paymentData['transaction_id'] ?? null,
            ]);

            // Kích hoạt license cho tất cả order items
            foreach ($order->items as $item) {
                $item->activateLicense();
            }

            // Đánh dấu order hoàn thành
            $order->markAsCompleted();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Xử lý payment thất bại
     */
    public function processFailedPayment(Order $order, string $reason): void
    {
        $order->update([
            'payment_status' => 'failed',
            'notes' => $reason,
        ]);
    }

    /**
     * Lấy order summary cho user
     */
    public function getUserOrderSummary(int $userId): array
    {
        $orders = Order::where('user_id', $userId)->get();

        $totalOrders = $orders->count();
        $completedOrders = $orders->where('status', 'completed')->count();
        $pendingOrders = $orders->where('status', 'pending')->count();
        $cancelledOrders = $orders->where('status', 'cancelled')->count();

        $totalSpent = $orders->where('payment_status', 'completed')->sum('total_amount');
        $averageOrderValue = $completedOrders > 0 ? $totalSpent / $completedOrders : 0;

        return [
            'total_orders' => $totalOrders,
            'completed_orders' => $completedOrders,
            'pending_orders' => $pendingOrders,
            'cancelled_orders' => $cancelledOrders,
            'total_spent' => (float) $totalSpent,
            'average_order_value' => round($averageOrderValue, 2),
            'formatted' => [
                'total_spent' => number_format($totalSpent, 0, ',', '.') . ' ₫',
                'average_order_value' => number_format($averageOrderValue, 0, ',', '.') . ' ₫',
            ]
        ];
    }

    /**
     * Lấy purchased products của user
     */
    public function getUserPurchasedProducts(int $userId): Collection
    {
        return OrderItem::whereHas('order', function ($query) use ($userId) {
            $query->where('user_id', $userId)
                  ->where('status', 'completed');
        })
        ->where('status', 'active')
        ->with(['product', 'order'])
        ->orderBy('created_at', 'desc')
        ->get();
    }

    /**
     * Kiểm tra user có quyền download product không
     */
    public function canUserDownloadProduct(int $userId, int $productId): bool
    {
        return OrderItem::whereHas('order', function ($query) use ($userId) {
            $query->where('user_id', $userId)
                  ->where('status', 'completed');
        })
        ->where('technical_product_id', $productId)
        ->where('status', 'active')
        ->exists();
    }

    /**
     * Lấy download info cho product
     */
    public function getProductDownloadInfo(int $userId, int $productId): ?array
    {
        $orderItem = OrderItem::whereHas('order', function ($query) use ($userId) {
            $query->where('user_id', $userId)
                  ->where('status', 'completed');
        })
        ->where('technical_product_id', $productId)
        ->where('status', 'active')
        ->with(['product', 'order'])
        ->first();

        if (!$orderItem) {
            return null;
        }

        return [
            'order_item_id' => $orderItem->id,
            'order_number' => $orderItem->order->order_number,
            'purchase_date' => $orderItem->created_at->format('Y-m-d H:i:s'),
            'license_info' => $orderItem->getLicenseStatus(),
            'can_download' => $orderItem->canDownload(),
            'download_count' => $orderItem->download_count,
            'download_limit' => $orderItem->download_limit,
            'downloads_remaining' => $orderItem->getDownloadsRemaining(),
        ];
    }

    /**
     * Xử lý download request
     */
    public function processDownloadRequest(int $userId, int $productId): array
    {
        $orderItem = OrderItem::whereHas('order', function ($query) use ($userId) {
            $query->where('user_id', $userId)
                  ->where('status', 'completed');
        })
        ->where('technical_product_id', $productId)
        ->where('status', 'active')
        ->firstOrFail();

        if (!$orderItem->canDownload()) {
            throw new \Exception('Không thể download sản phẩm này');
        }

        // Tăng download count
        $orderItem->incrementDownloadCount();

        // Tạo secure download token
        $secureDownloadService = app(SecureDownloadService::class);
        $downloadToken = $secureDownloadService->generateDownloadToken(
            $userId,
            $orderItem->id,
            $productId
        );

        return [
            'download_token' => $downloadToken,
            'expires_at' => now()->addHours(24)->toISOString(),
            'downloads_remaining' => $orderItem->getDownloadsRemaining(),
        ];
    }

    /**
     * Lấy orders for seller dashboard
     */
    public function getSellerOrders(int $sellerId, array $filters = []): Collection
    {
        $query = OrderItem::where('seller_id', $sellerId)
                         ->with(['order.user', 'product'])
                         ->orderBy('created_at', 'desc');

        // Apply filters
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->get();
    }

    /**
     * Lấy seller sales summary
     */
    public function getSellerSalesSummary(int $sellerId): array
    {
        $orderItems = OrderItem::where('seller_id', $sellerId)->get();

        $totalSales = $orderItems->count();
        $activeLicenses = $orderItems->where('status', 'active')->count();
        $totalRevenue = $orderItems->sum('total_price');
        $totalEarnings = $orderItems->sum('seller_earnings');

        $averageSaleValue = $totalSales > 0 ? $totalRevenue / $totalSales : 0;

        return [
            'total_sales' => $totalSales,
            'active_licenses' => $activeLicenses,
            'total_revenue' => (float) $totalRevenue,
            'total_earnings' => (float) $totalEarnings,
            'average_sale_value' => round($averageSaleValue, 2),
            'formatted' => [
                'total_revenue' => number_format($totalRevenue, 0, ',', '.') . ' ₫',
                'total_earnings' => number_format($totalEarnings, 0, ',', '.') . ' ₫',
                'average_sale_value' => number_format($averageSaleValue, 0, ',', '.') . ' ₫',
            ]
        ];
    }

    /**
     * Validate order trước khi thanh toán
     */
    public function validateOrderForPayment(Order $order): array
    {
        $issues = [];

        // Kiểm tra order status
        if ($order->status !== 'pending' && $order->status !== 'payment_pending') {
            $issues[] = 'Order status không hợp lệ cho thanh toán';
        }

        // Kiểm tra order có items không
        if ($order->items()->count() === 0) {
            $issues[] = 'Order không có sản phẩm nào';
        }

        // Kiểm tra total amount > 0
        if ($order->total_amount <= 0) {
            $issues[] = 'Tổng số tiền phải lớn hơn 0';
        }

        // Kiểm tra các sản phẩm trong order vẫn còn available
        foreach ($order->items as $item) {
            if (!$item->product || !$item->product->is_active) {
                $issues[] = "Sản phẩm '{$item->product_name}' không còn khả dụng";
            }
        }

        // Kiểm tra user đã verify email
        if (!$order->user->hasVerifiedEmail()) {
            $issues[] = 'Cần xác thực email trước khi thanh toán';
        }

        return [
            'is_valid' => empty($issues),
            'issues' => $issues
        ];
    }
}
