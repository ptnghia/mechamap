<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\MarketplaceSeller;
use App\Models\SellerPayoutRequest;
use App\Models\MarketplaceOrder;
use App\Models\MarketplaceOrderItem;
use App\Models\MarketplaceProduct;
use Carbon\Carbon;

class PayoutTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating test payout data...');

        // Create test sellers if they don't exist
        $sellers = $this->createTestSellers();

        // Create test orders and order items for each seller
        foreach ($sellers as $seller) {
            $this->createOrdersForSeller($seller);
        }

        // Create payout requests
        foreach ($sellers as $seller) {
            $this->createPayoutRequestsForSeller($seller);
        }

        $this->command->info('Payout test data created successfully!');
    }

    /**
     * Create test sellers
     */
    protected function createTestSellers(): array
    {
        $sellers = [];

        for ($i = 1; $i <= 5; $i++) {
            // Create user
            $user = User::firstOrCreate(
                ['email' => "seller{$i}@mechamap.vn"],
                [
                    'name' => "Test Seller {$i}",
                    'username' => "testseller{$i}",
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                ]
            );

            // Create seller account
            $sellerAccount = MarketplaceSeller::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'uuid' => \Illuminate\Support\Str::uuid(),
                    'seller_type' => 'manufacturer',
                    'business_name' => "Business Seller {$i}",
                    'business_type' => 'individual',
                    'contact_person_name' => "Contact Person {$i}",
                    'contact_email' => "seller{$i}@mechamap.vn",
                    'contact_phone' => "090325242700{$i}",
                    'business_address' => "Test Business Address {$i}",
                    'status' => 'active',
                    'commission_rate' => rand(3, 7),
                    'bank_information' => json_encode([
                        'bank_name' => 'MBBank',
                        'account_number' => '090325242700' . $i,
                        'account_name' => "TEST SELLER {$i}",
                    ]),
                    'payout_frequency' => ['weekly', 'biweekly', 'monthly'][rand(0, 2)],
                    'minimum_payout_amount' => 100000,
                    'total_earnings' => 0,
                    'pending_payout' => 0,
                    'total_commission_paid' => 0,
                ]
            );

            $sellers[] = [
                'user' => $user,
                'account' => $sellerAccount
            ];
        }

        return $sellers;
    }

    /**
     * Create orders for seller
     */
    protected function createOrdersForSeller($seller): void
    {
        $user = $seller['user'];
        $sellerAccount = $seller['account'];

        // Create some test products for this seller
        $products = $this->createTestProducts($user);

        // Create orders for the last 60 days
        for ($i = 0; $i < rand(5, 15); $i++) {
            $createdAt = Carbon::now()->subDays(rand(0, 60));

            // Create customer
            $customer = User::firstOrCreate(
                ['email' => "customer{$i}@test.com"],
                [
                    'name' => "Test Customer {$i}",
                    'username' => "testcustomer{$i}",
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                ]
            );

            // Create order
            $order = MarketplaceOrder::create([
                'uuid' => \Illuminate\Support\Str::uuid(),
                'order_number' => 'PAYOUT-TEST-' . time() . '-' . $i,
                'customer_id' => $customer->id,
                'customer_email' => $customer->email,
                'status' => 'completed',
                'payment_status' => 'paid',
                'subtotal' => 0, // Will be calculated
                'tax_amount' => 0,
                'shipping_amount' => rand(0, 50000),
                'discount_amount' => 0,
                'total_amount' => 0, // Will be calculated
                'currency' => 'VND',
                'shipping_address' => [
                    'name' => "Test Customer {$i}",
                    'address' => "Test Address {$i}",
                    'city' => 'Ho Chi Minh City',
                    'country' => 'Vietnam'
                ],
                'billing_address' => [
                    'name' => "Test Customer {$i}",
                    'address' => "Test Address {$i}",
                    'city' => 'Ho Chi Minh City',
                    'country' => 'Vietnam'
                ],
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'paid_at' => $createdAt,
            ]);

            // Create order items
            $subtotal = 0;
            $itemCount = rand(1, 3);

            for ($j = 0; $j < $itemCount; $j++) {
                $product = $products[array_rand($products)];
                $price = rand(50000, 500000);
                $quantity = rand(1, 3);
                $itemTotal = $price * $quantity;
                $commissionRate = $sellerAccount->commission_rate;
                $commissionAmount = ($itemTotal * $commissionRate) / 100;
                $sellerEarnings = $itemTotal - $commissionAmount;

                MarketplaceOrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'seller_id' => $sellerAccount->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'unit_price' => $price,
                    'quantity' => $quantity,
                    'subtotal' => $itemTotal,
                    'total_amount' => $itemTotal,
                    'commission_rate' => $commissionRate,
                    'commission_amount' => $commissionAmount,
                    'seller_earnings' => $sellerEarnings,
                    'admin_commission' => $commissionAmount,
                    'gateway_fee_share' => 0,
                    'included_in_payout' => false,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                $subtotal += $itemTotal;
            }

            // Update order totals
            $order->update([
                'subtotal' => $subtotal,
                'total_amount' => $subtotal + $order->shipping_amount,
            ]);

            // Create centralized payment for this order
            $centralizedPayment = \App\Models\CentralizedPayment::create([
                'payment_reference' => \App\Models\CentralizedPayment::generatePaymentReference(),
                'order_id' => $order->id,
                'customer_id' => $customer->id,
                'customer_email' => $customer->email,
                'payment_method' => ['stripe', 'sepay'][rand(0, 1)],
                'gross_amount' => $order->total_amount,
                'gateway_fee' => 0,
                'net_received' => $order->total_amount,
                'status' => 'completed',
                'paid_at' => $createdAt,
                'confirmed_at' => $createdAt,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            // Update order with centralized payment
            $order->update([
                'centralized_payment_id' => $centralizedPayment->id,
            ]);
        }
    }

    /**
     * Create test products
     */
    protected function createTestProducts($seller): array
    {
        $products = [];

        for ($i = 1; $i <= 3; $i++) {
            $product = MarketplaceProduct::firstOrCreate(
                [
                    'name' => "Test Product {$i} by {$seller->name}",
                    'seller_id' => $seller->id,
                ],
                [
                    'uuid' => \Illuminate\Support\Str::uuid(),
                    'slug' => "test-product-{$i}-seller-{$seller->id}",
                    'description' => "Test product description {$i}",
                    'price' => rand(50000, 500000),
                    'product_type' => ['digital', 'new_product', 'used_product'][rand(0, 2)],
                    'seller_type' => 'manufacturer',
                    'product_category_id' => 1, // Assuming category exists
                    'stock_quantity' => rand(10, 100),
                    'in_stock' => true,
                    'status' => 'approved',
                ]
            );

            $products[] = $product;
        }

        return $products;
    }

    /**
     * Create payout requests for seller
     */
    protected function createPayoutRequestsForSeller($seller): void
    {
        $user = $seller['user'];
        $sellerAccount = $seller['account'];

        // Get completed orders for this seller
        $orderItems = MarketplaceOrderItem::where('seller_id', $sellerAccount->id)
            ->whereHas('order', function ($query) {
                $query->where('status', 'completed')
                      ->where('payment_status', 'paid');
            })
            ->where('included_in_payout', false)
            ->get();

        if ($orderItems->isEmpty()) {
            return;
        }

        // Group items by month for payout requests
        $itemsByMonth = $orderItems->groupBy(function ($item) {
            return $item->created_at->format('Y-m');
        });

        foreach ($itemsByMonth as $month => $items) {
            $monthStart = Carbon::parse($month . '-01');
            $monthEnd = $monthStart->copy()->endOfMonth();

            // Skip current month (too recent for payout)
            if ($monthStart->isCurrentMonth()) {
                continue;
            }

            $totalSales = $items->sum('total_amount');
            $commissionAmount = $items->sum('commission_amount');
            $netPayout = $totalSales - $commissionAmount;

            // Only create payout if amount is above minimum
            if ($netPayout < $sellerAccount->minimum_payout_amount) {
                continue;
            }

            // Random status for variety
            $statuses = ['pending', 'approved', 'completed', 'rejected'];
            $status = $statuses[array_rand($statuses)];

            $payoutRequest = SellerPayoutRequest::create([
                'payout_reference' => SellerPayoutRequest::generatePayoutReference(),
                'seller_id' => $user->id,
                'seller_account_id' => $sellerAccount->id,
                'total_sales' => $totalSales,
                'commission_amount' => $commissionAmount,
                'net_payout' => $netPayout,
                'order_count' => $items->count(),
                'period_from' => $monthStart,
                'period_to' => $monthEnd,
                'bank_details' => $sellerAccount->bank_information,
                'status' => $status,
                'created_at' => $monthEnd->addDays(rand(1, 5)), // Created few days after month end
            ]);

            // Create payout items
            foreach ($items as $item) {
                \App\Models\SellerPayoutItem::create([
                    'payout_request_id' => $payoutRequest->id,
                    'order_id' => $item->order_id,
                    'order_item_id' => $item->id,
                    'centralized_payment_id' => $item->order->centralized_payment_id,
                    'product_id' => $item->product_id,
                    'seller_id' => $sellerAccount->id,
                    'item_price' => $item->unit_price,
                    'quantity' => $item->quantity,
                    'item_total' => $item->total_amount,
                    'commission_rate' => $item->commission_rate,
                    'commission_amount' => $item->commission_amount,
                    'seller_earnings' => $item->seller_earnings,
                    'status' => $status === 'completed' ? 'paid' : 'included',
                ]);

                // Mark item as included in payout
                $item->update([
                    'included_in_payout' => true,
                    'payout_included_at' => $payoutRequest->created_at,
                    'payout_request_id' => $payoutRequest->id,
                ]);
            }

            // Update timestamps based on status
            if (in_array($status, ['approved', 'completed'])) {
                $payoutRequest->update([
                    'approved_at' => $payoutRequest->created_at->addDays(rand(1, 3)),
                    'processed_by' => 1, // Assuming admin user ID 1
                ]);
            }

            if ($status === 'completed') {
                $payoutRequest->update([
                    'completed_at' => $payoutRequest->approved_at->addDays(rand(1, 5)),
                ]);

                // Update seller account
                $sellerAccount->increment('total_earnings', $netPayout);
            } elseif ($status === 'pending') {
                $sellerAccount->increment('pending_payout', $netPayout);
            }

            $this->command->info("Created payout request: {$payoutRequest->payout_reference} for {$user->name}");
        }
    }
}
