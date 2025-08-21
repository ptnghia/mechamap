<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SellerEarning;
use App\Models\SellerPayout;
use App\Models\ProductPurchase;
use App\Models\OrderItem;
use App\Models\Order;
use App\Models\User;
use App\Models\TechnicalProduct;

class SellerEarningsSeeder extends Seeder
{
    /**
     * Seed seller earnings and payouts from completed orders
     */
    public function run(): void
    {
        $this->command->info('💰 Bắt đầu tạo seller earnings và payouts...');

        // Get completed orders
        $completedOrders = Order::where('status', 'completed')
            ->where('payment_status', 'completed')
            ->with(['items'])
            ->get();

        if ($completedOrders->isEmpty()) {
            $this->command->error('❌ Không có completed orders để tạo earnings!');
            return;
        }

        $earningsCreated = 0;
        $purchasesCreated = 0;
        $payoutsCreated = 0;

        foreach ($completedOrders as $order) {
            foreach ($order->items as $orderItem) {
                // Check if purchase already exists
                $existingPurchase = ProductPurchase::where('product_id', $orderItem->technical_product_id)
                    ->where('buyer_id', $order->user_id)
                    ->where('status', 'active')
                    ->first();

                if ($existingPurchase) {
                    $this->command->info("Skipping duplicate purchase for product {$orderItem->technical_product_id} by user {$order->user_id}");
                    continue; // Skip if already exists
                }

                $this->command->info("Creating purchase for product {$orderItem->technical_product_id} by user {$order->user_id}");

                // Create ProductPurchase record
                $purchase = ProductPurchase::create([
                    'product_id' => $orderItem->technical_product_id,
                    'buyer_id' => $order->user_id,
                    'seller_id' => $orderItem->seller_id,
                    'purchase_token' => 'PUR-' . strtoupper(uniqid()),
                    'amount_paid' => $orderItem->total_price,
                    'currency' => 'VND',
                    'platform_fee' => $orderItem->platform_fee,
                    'seller_revenue' => $orderItem->seller_earnings,
                    'payment_method' => $order->payment_method,
                    'payment_id' => 'PAY-' . strtoupper(uniqid()),
                    'payment_status' => 'completed',
                    'payment_gateway' => $order->payment_method,
                    'license_type' => $this->mapLicenseType($orderItem->license_type),
                    'license_key' => 'LIC-' . strtoupper(uniqid()),
                    'download_limit' => $orderItem->download_limit,
                    'download_count' => rand(0, 3),
                    'expires_at' => now()->addYear(),
                    'download_token' => 'DL-' . strtoupper(uniqid()),
                    'last_download_at' => rand(0, 1) ? now()->subDays(rand(1, 30)) : null,
                    'download_ip_addresses' => ['192.168.1.' . rand(1, 255)],
                    'status' => 'active',
                    'created_at' => $order->created_at,
                ]);
                $purchasesCreated++;

                // Create SellerEarning record
                $earning = SellerEarning::create([
                    'seller_id' => $orderItem->seller_id,
                    'order_item_id' => $orderItem->id,
                    'technical_product_id' => $orderItem->technical_product_id,
                    'gross_amount' => $orderItem->total_price,
                    'platform_fee' => $orderItem->platform_fee,
                    'payment_fee' => $orderItem->total_price * 0.029, // 2.9% payment fee
                    'tax_amount' => $orderItem->seller_earnings * 0.1, // 10% tax
                    'net_amount' => $orderItem->seller_earnings * 0.9, // After tax
                    'platform_fee_rate' => 0.15,
                    'payment_fee_rate' => 0.029,
                    'payout_status' => $this->getRandomPayoutStatus(),
                    'available_at' => $order->created_at->addDays(7), // Available after 7 days
                    'paid_at' => $this->getRandomPayoutStatus() === 'paid' ? $order->created_at->addDays(14) : null,
                    'created_at' => $order->created_at,
                ]);
                $earningsCreated++;
            }
        }

        // Skip payouts for now - complex structure
        // Will implement later when needed

        $this->command->info("✅ Đã tạo {$purchasesCreated} product purchases");
        $this->command->info("✅ Đã tạo {$earningsCreated} seller earnings");
    }

    private function getRandomPayoutStatus(): string
    {
        $statuses = ['pending', 'available', 'paid', 'failed'];
        $weights = [10, 20, 65, 5]; // Most are paid

        return $this->weightedRandom($statuses, $weights);
    }

    private function getRandomPayoutMethod(): string
    {
        $methods = ['bank_transfer', 'paypal', 'stripe'];
        return $methods[array_rand($methods)];
    }

    private function generateBankAccount(): array
    {
        $banks = [
            'Vietcombank' => '1234567890',
            'Techcombank' => '9876543210',
            'BIDV' => '5555666677',
            'VietinBank' => '1111222233',
        ];

        $bank = array_rand($banks);
        return [
            'bank_name' => $bank,
            'account_number' => $banks[$bank],
            'account_holder' => 'Nguyen Van A',
            'branch' => 'Chi nhanh TP.HCM',
        ];
    }

    private function mapLicenseType($orderItemLicenseType): string
    {
        return match($orderItemLicenseType) {
            'single' => 'single_use',
            'commercial' => 'commercial',
            'extended' => 'unlimited',
            default => 'single_use'
        };
    }

    private function weightedRandom($items, $weights): string
    {
        $totalWeight = array_sum($weights);
        $random = rand(1, $totalWeight);

        $currentWeight = 0;
        foreach ($items as $index => $item) {
            $currentWeight += $weights[$index];
            if ($random <= $currentWeight) {
                return $item;
            }
        }

        return $items[0]; // Fallback
    }
}
