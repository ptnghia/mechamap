<?php

namespace App\Observers;

use App\Models\MarketplaceOrder;
use App\Services\MarketplaceDownloadService;
use Illuminate\Support\Facades\Log;

class MarketplaceOrderObserver
{
    protected $downloadService;

    public function __construct(MarketplaceDownloadService $downloadService)
    {
        $this->downloadService = $downloadService;
    }

    /**
     * Handle the MarketplaceOrder "created" event.
     */
    public function created(MarketplaceOrder $marketplaceOrder): void
    {
        //
    }

    /**
     * Handle the MarketplaceOrder "updated" event.
     */
    public function updated(MarketplaceOrder $marketplaceOrder): void
    {
        // Handle order status changes
        if ($marketplaceOrder->isDirty('status')) {
            $oldStatus = $marketplaceOrder->getOriginal('status');
            $newStatus = $marketplaceOrder->status;

            \App\Services\MarketplaceNotificationService::handleOrderStatusChange(
                $marketplaceOrder,
                $oldStatus,
                $newStatus
            );
        }

        // Handle payment status changes
        if ($marketplaceOrder->isDirty('payment_status')) {
            $oldPaymentStatus = $marketplaceOrder->getOriginal('payment_status');
            $newPaymentStatus = $marketplaceOrder->payment_status;

            \App\Services\MarketplaceNotificationService::handleOrderPaymentStatusChange(
                $marketplaceOrder,
                $oldPaymentStatus,
                $newPaymentStatus
            );
        }

        // Check if order status changed to completed and payment is paid
        if ($marketplaceOrder->isDirty('status') &&
            $marketplaceOrder->status === 'completed' &&
            $marketplaceOrder->payment_status === 'paid') {

            $this->processDigitalProductAccess($marketplaceOrder);
        }

        // Also check if payment status changed to paid while order is completed
        if ($marketplaceOrder->isDirty('payment_status') &&
            $marketplaceOrder->payment_status === 'paid' &&
            $marketplaceOrder->status === 'completed') {

            $this->processDigitalProductAccess($marketplaceOrder);
        }
    }

    /**
     * Handle the MarketplaceOrder "deleted" event.
     */
    public function deleted(MarketplaceOrder $marketplaceOrder): void
    {
        //
    }

    /**
     * Handle the MarketplaceOrder "restored" event.
     */
    public function restored(MarketplaceOrder $marketplaceOrder): void
    {
        //
    }

    /**
     * Handle the MarketplaceOrder "force deleted" event.
     */
    public function forceDeleted(MarketplaceOrder $marketplaceOrder): void
    {
        //
    }

    /**
     * Process digital product access when order is completed
     */
    protected function processDigitalProductAccess(MarketplaceOrder $order): void
    {
        try {
            // Get all order items with digital products
            $digitalItems = $order->items()
                ->whereHas('product', function ($query) {
                    $query->where(function ($q) {
                        $q->where('product_type', 'digital')
                          ->orWhere('seller_type', 'manufacturer')
                          ->orWhereNotNull('digital_files');
                    });
                })
                ->with(['product'])
                ->get();

            foreach ($digitalItems as $item) {
                $this->setupDownloadAccess($order, $item);
            }

            Log::info('Digital product access processed', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'digital_items_count' => $digitalItems->count(),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to process digital product access', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Setup download access for order item
     */
    protected function setupDownloadAccess(MarketplaceOrder $order, $orderItem): void
    {
        try {
            // Update order item with download information
            $downloadLinks = [];
            $files = $this->downloadService->getDownloadableFiles($orderItem);

            foreach ($files as $index => $file) {
                $downloadLinks[] = [
                    'file_index' => $index,
                    'file_name' => $file['name'],
                    'file_size' => $file['size'],
                    'available_at' => now()->toISOString(),
                ];
            }

            // Update order item
            $orderItem->update([
                'download_links' => $downloadLinks,
                'download_count' => 0,
                'download_limit' => null, // No limit for marketplace downloads
                'download_expires_at' => null, // No expiration for marketplace downloads
            ]);

            Log::info('Download access setup completed', [
                'order_item_id' => $orderItem->id,
                'product_id' => $orderItem->product_id,
                'files_count' => count($files),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to setup download access', [
                'order_item_id' => $orderItem->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
