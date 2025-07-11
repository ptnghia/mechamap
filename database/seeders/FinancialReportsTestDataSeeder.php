<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CentralizedPayment;
use App\Models\MarketplaceOrder;
use App\Models\MarketplaceOrderItem;
use App\Models\User;
use App\Models\MarketplaceSeller;
use App\Models\MarketplaceProduct;
use Carbon\Carbon;

class FinancialReportsTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating financial reports test data...');

        // Create test data for the last 90 days
        $this->createFinancialTestData();

        $this->command->info('Financial reports test data created successfully!');
    }

    /**
     * Create comprehensive financial test data
     */
    protected function createFinancialTestData(): void
    {
        // Get existing sellers or create some
        $sellers = $this->getOrCreateSellers();

        // Get existing products or create some
        $products = $this->getOrCreateProducts($sellers);

        // Create orders and payments for the last 90 days
        $startDate = Carbon::now()->subDays(90);
        $endDate = Carbon::now();

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            // Skip some days randomly to create realistic patterns
            if (rand(1, 10) <= 2) continue; // 20% chance to skip a day

            // Create 1-10 orders per day
            $ordersPerDay = rand(1, 10);

            for ($i = 0; $i < $ordersPerDay; $i++) {
                $this->createOrderWithPayment($date->copy(), $sellers, $products);
            }
        }
    }

    /**
     * Get or create test sellers
     */
    protected function getOrCreateSellers(): array
    {
        $sellers = [];

        // Try to get existing sellers first
        $existingSellers = MarketplaceSeller::with('user')->take(5)->get();

        if ($existingSellers->count() >= 3) {
            $sellersArray = [];
            foreach ($existingSellers as $sellerAccount) {
                $sellersArray[] = [
                    'user' => $sellerAccount->user,
                    'account' => $sellerAccount
                ];
            }
            return $sellersArray;
        }

        // Create additional sellers if needed
        for ($i = 1; $i <= 5; $i++) {
            $user = User::firstOrCreate(
                ['email' => "financial_test_seller{$i}@mechamap.vn"],
                [
                    'name' => "Financial Test Seller {$i}",
                    'username' => "financialtestseller{$i}",
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                ]
            );

            $sellerAccount = MarketplaceSeller::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'uuid' => \Illuminate\Support\Str::uuid(),
                    'seller_type' => ['manufacturer', 'supplier', 'brand'][rand(0, 2)],
                    'business_name' => "Financial Test Business {$i}",
                    'business_type' => 'individual',
                    'contact_person_name' => "Contact Person {$i}",
                    'contact_email' => "financial_test_seller{$i}@mechamap.vn",
                    'contact_phone' => "090325242700{$i}",
                    'business_address' => "Test Business Address {$i}",
                    'status' => 'active',
                    'commission_rate' => rand(3, 7),
                    'bank_information' => json_encode([
                        'bank_name' => 'MBBank',
                        'account_number' => '090325242700' . $i,
                        'account_name' => "FINANCIAL TEST SELLER {$i}",
                    ]),
                    'payout_frequency' => 'monthly',
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
     * Get or create test products
     */
    protected function getOrCreateProducts($sellers): array
    {
        $products = [];

        foreach ($sellers as $seller) {
            for ($i = 1; $i <= 3; $i++) {
                $product = MarketplaceProduct::firstOrCreate(
                    [
                        'name' => "Financial Test Product {$i} by {$seller['user']->name}",
                        'seller_id' => $seller['user']->id,
                    ],
                    [
                        'uuid' => \Illuminate\Support\Str::uuid(),
                        'slug' => "financial-test-product-{$i}-seller-{$seller['user']->id}",
                        'description' => "Financial test product description {$i}",
                        'price' => rand(50000, 1000000), // 50k - 1M VND
                        'product_type' => ['digital', 'new_product', 'used_product'][rand(0, 2)],
                        'seller_type' => $seller['account']->seller_type,
                        'product_category_id' => 1,
                        'stock_quantity' => rand(10, 100),
                        'in_stock' => true,
                        'status' => 'approved',
                    ]
                );

                $products[] = [
                    'product' => $product,
                    'seller' => $seller
                ];
            }
        }

        return $products;
    }

    /**
     * Create order with payment
     */
    protected function createOrderWithPayment($date, $sellers, $products): void
    {
        // Create customer
        $customerId = rand(1, 100);
        $customer = User::firstOrCreate(
            ['email' => "financial_test_customer{$customerId}@test.com"],
            [
                'name' => "Financial Test Customer {$customerId}",
                'username' => "financialtestcustomer{$customerId}",
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        // Random payment method
        $paymentMethod = ['stripe', 'sepay'][rand(0, 1)];

        // Create order
        $order = MarketplaceOrder::create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'order_number' => 'FINANCIAL-TEST-' . $date->format('Ymd') . '-' . rand(1000, 9999),
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
                'name' => "Financial Test Customer {$customerId}",
                'address' => "Test Address {$customerId}",
                'city' => 'Ho Chi Minh City',
                'country' => 'Vietnam'
            ],
            'billing_address' => [
                'name' => "Financial Test Customer {$customerId}",
                'address' => "Test Address {$customerId}",
                'city' => 'Ho Chi Minh City',
                'country' => 'Vietnam'
            ],
            'created_at' => $date,
            'updated_at' => $date,
            'paid_at' => $date,
        ]);

        // Create order items
        $subtotal = 0;
        $itemCount = rand(1, 3);

        for ($j = 0; $j < $itemCount; $j++) {
            $productData = $products[array_rand($products)];
            $product = $productData['product'];
            $seller = $productData['seller'];

            $price = $product->price;
            $quantity = rand(1, 2);
            $itemTotal = $price * $quantity;
            $commissionRate = $seller['account']->commission_rate;
            $commissionAmount = ($itemTotal * $commissionRate) / 100;
            $sellerEarnings = $itemTotal - $commissionAmount;

            MarketplaceOrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'seller_id' => $seller['account']->id,
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
                'created_at' => $date,
                'updated_at' => $date,
            ]);

            $subtotal += $itemTotal;
        }

        // Update order totals
        $totalAmount = $subtotal + $order->shipping_amount;
        $order->update([
            'subtotal' => $subtotal,
            'total_amount' => $totalAmount,
        ]);

        // Create centralized payment
        $gatewayFee = $paymentMethod === 'stripe' ? ($totalAmount * 0.034) + 10000 : 0; // 3.4% + 10k VND for Stripe
        $netReceived = $totalAmount - $gatewayFee;

        $centralizedPayment = CentralizedPayment::create([
            'payment_reference' => CentralizedPayment::generatePaymentReference(),
            'order_id' => $order->id,
            'customer_id' => $customer->id,
            'customer_email' => $customer->email,
            'payment_method' => $paymentMethod,
            'gross_amount' => $totalAmount,
            'gateway_fee' => $gatewayFee,
            'net_received' => $netReceived,
            'status' => 'completed',
            'gateway_payment_intent_id' => 'test_' . \Illuminate\Support\Str::random(20),
            'gateway_transaction_id' => 'test_txn_' . \Illuminate\Support\Str::random(15),
            'paid_at' => $date,
            'confirmed_at' => $date->copy()->addMinutes(rand(1, 10)),
            'created_at' => $date,
            'updated_at' => $date,
            'gateway_response' => [
                'test_data' => true,
                'payment_method' => $paymentMethod,
                'amount' => $totalAmount,
                'currency' => 'VND'
            ],
        ]);

        // Update order with centralized payment
        $order->update([
            'centralized_payment_id' => $centralizedPayment->id,
        ]);

        // Add some variation to dates for more realistic data
        $randomMinutes = rand(-30, 30);
        $finalDate = $date->copy()->addMinutes($randomMinutes);

        $order->update(['created_at' => $finalDate, 'updated_at' => $finalDate]);
        $centralizedPayment->update(['created_at' => $finalDate, 'updated_at' => $finalDate]);
    }
}
