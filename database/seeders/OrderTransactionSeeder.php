<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\TechnicalProduct;
use App\Models\MarketplaceProduct;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentTransaction;
use App\Models\MarketplaceOrder;
use App\Models\MarketplaceOrderItem;
use Carbon\Carbon;

class OrderTransactionSeeder extends Seeder
{
    public function run()
    {
        echo "🛒 Bắt đầu seed orders và transactions...\n";

        // Get users and products
        $buyers = User::take(20)->get();
        $sellers = User::take(10)->get();

        $technicalProducts = TechnicalProduct::take(15)->get();
        $marketplaceProducts = MarketplaceProduct::take(30)->get();

        if ($buyers->isEmpty() || $sellers->isEmpty()) {
            echo "❌ Không có đủ users để tạo orders\n";
            return;
        }

        // Create Technical Product Orders
        $technicalOrders = 0;
        foreach ($buyers->take(15) as $buyer) {
            if ($technicalProducts->isNotEmpty()) {
                $subtotal = rand(50, 500);
                $taxAmount = $subtotal * 0.1;
                $totalAmount = $subtotal + $taxAmount;

                $order = Order::create([
                    'user_id' => $buyer->id,
                    'order_number' => 'ORD-' . strtoupper(uniqid()),
                    'status' => collect(['pending', 'confirmed', 'processing', 'completed', 'cancelled'])->random(),
                    'subtotal' => $subtotal,
                    'tax_amount' => $taxAmount,
                    'processing_fee' => 0,
                    'discount_amount' => 0,
                    'total_amount' => $totalAmount,
                    'payment_status' => collect(['pending', 'processing', 'completed', 'failed', 'cancelled'])->random(),
                    'payment_method' => collect(['stripe', 'vnpay', 'bank_transfer'])->random(),
                    'billing_address' => json_encode([
                        'name' => $buyer->name,
                        'address' => 'Địa chỉ thanh toán ' . rand(1, 100),
                        'city' => collect(['Hà Nội', 'TP.HCM', 'Đà Nẵng'])->random(),
                        'country' => 'Vietnam'
                    ]),
                    'notes' => 'Technical product order for mechanical engineering',
                    'created_at' => Carbon::now()->subDays(rand(1, 30)),
                    'updated_at' => Carbon::now()->subDays(rand(0, 5))
                ]);

                // Create order items
                $selectedProducts = $technicalProducts->random(rand(1, 3));
                foreach ($selectedProducts as $product) {
                    $quantity = rand(1, 5);
                    $price = rand(10, 100);

                    OrderItem::create([
                        'order_id' => $order->id,
                        'technical_product_id' => $product->id,
                        'seller_id' => $product->seller_id,
                        'product_title' => $product->title ?? 'Technical Product',
                        'product_description' => $product->description ?? 'Technical product for mechanical engineering',
                        'product_snapshot' => json_encode([
                            'title' => $product->title ?? 'Technical Product',
                            'price' => $price,
                            'category' => $product->category->name ?? 'General',
                            'seller' => $product->seller->name ?? 'Seller'
                        ]),
                        'quantity' => $quantity,
                        'unit_price' => $price,
                        'total_price' => $quantity * $price,
                        'seller_earnings' => ($quantity * $price) * 0.8,
                        'platform_fee' => ($quantity * $price) * 0.2,
                        'license_type' => collect(['single', 'commercial', 'extended'])->random(),
                        'download_count' => 0,
                        'download_limit' => rand(5, 50),
                        'status' => collect(['pending', 'active'])->random(),
                        'created_at' => $order->created_at,
                        'updated_at' => $order->updated_at
                    ]);
                }

                // Create payment transaction
                if ($order->payment_status === 'completed') {
                    $feeAmount = $order->total_amount * 0.03;
                    PaymentTransaction::create([
                        'user_id' => $buyer->id,
                        'order_id' => $order->id,
                        'transaction_id' => 'TXN-' . strtoupper(uniqid()),
                        'payment_method' => $order->payment_method,
                        'gateway_transaction_id' => 'GTW-' . strtoupper(uniqid()),
                        'type' => 'payment',
                        'status' => 'completed',
                        'amount' => $order->total_amount,
                        'currency' => 'USD',
                        'fee_amount' => $feeAmount,
                        'net_amount' => $order->total_amount - $feeAmount,
                        'processed_at' => $order->created_at->addMinutes(rand(5, 60)),
                        'created_at' => $order->created_at,
                        'updated_at' => $order->updated_at
                    ]);
                }

                $technicalOrders++;
            }
        }

        // Create Marketplace Orders
        $marketplaceOrders = 0;
        foreach ($buyers->take(20) as $buyer) {
            if ($marketplaceProducts->isNotEmpty()) {
                $subtotal = rand(100, 1000);
                $taxAmount = $subtotal * 0.1;
                $shippingAmount = rand(10, 50);
                $totalAmount = $subtotal + $taxAmount + $shippingAmount;

                $order = MarketplaceOrder::create([
                    'customer_id' => $buyer->id,
                    'customer_email' => $buyer->email,
                    'order_number' => 'MKT-' . strtoupper(uniqid()),
                    'order_type' => collect(['product_purchase', 'service_booking'])->random(),
                    'subtotal' => $subtotal,
                    'tax_amount' => $taxAmount,
                    'shipping_amount' => $shippingAmount,
                    'discount_amount' => 0,
                    'total_amount' => $totalAmount,
                    'currency' => 'USD',
                    'status' => collect(['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'completed'])->random(),
                    'payment_status' => collect(['pending', 'processing', 'paid', 'failed'])->random(),
                    'shipping_address' => json_encode([
                        'name' => $buyer->name,
                        'address' => 'Địa chỉ giao hàng ' . rand(1, 100),
                        'city' => collect(['Hà Nội', 'TP.HCM', 'Đà Nẵng', 'Hải Phòng'])->random(),
                        'country' => 'Vietnam',
                        'postal_code' => rand(10000, 99999)
                    ]),
                    'billing_address' => json_encode([
                        'name' => $buyer->name,
                        'address' => 'Địa chỉ thanh toán ' . rand(1, 100),
                        'city' => collect(['Hà Nội', 'TP.HCM', 'Đà Nẵng', 'Hải Phòng'])->random(),
                        'country' => 'Vietnam',
                        'postal_code' => rand(10000, 99999)
                    ]),
                    'payment_method' => collect(['credit_card', 'paypal', 'bank_transfer', 'vnpay'])->random(),
                    'customer_notes' => 'Marketplace order for mechanical parts',
                    'created_at' => Carbon::now()->subDays(rand(1, 60)),
                    'updated_at' => Carbon::now()->subDays(rand(0, 10))
                ]);

                // Create marketplace order items
                $selectedProducts = $marketplaceProducts->random(rand(1, 4));
                foreach ($selectedProducts as $product) {
                    $quantity = rand(1, 10);
                    $price = rand(20, 200);

                    $itemSubtotal = $quantity * $price;
                    $itemTax = $itemSubtotal * 0.1;
                    $itemTotal = $itemSubtotal + $itemTax;
                    $commissionRate = 15.0;
                    $commissionAmount = $itemSubtotal * ($commissionRate / 100);

                    MarketplaceOrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'seller_id' => $product->seller_id,
                        'product_name' => $product->title ?? 'Marketplace Product',
                        'product_sku' => 'SKU-' . strtoupper(uniqid()),
                        'product_description' => $product->description ?? 'Mechanical engineering product',
                        'unit_price' => $price,
                        'sale_price' => $price,
                        'quantity' => $quantity,
                        'subtotal' => $itemSubtotal,
                        'tax_amount' => $itemTax,
                        'total_amount' => $itemTotal,
                        'download_count' => 0,
                        'download_limit' => rand(5, 20),
                        'fulfillment_status' => collect(['pending', 'processing', 'ready_to_ship', 'shipped'])->random(),
                        'commission_rate' => $commissionRate,
                        'commission_amount' => $commissionAmount,
                        'seller_earnings' => $itemSubtotal - $commissionAmount,
                        'created_at' => $order->created_at,
                        'updated_at' => $order->updated_at
                    ]);
                }

                $marketplaceOrders++;
            }
        }

        echo "✅ Đã tạo $technicalOrders technical orders\n";
        echo "✅ Đã tạo $marketplaceOrders marketplace orders\n";
        echo "✅ Đã tạo " . PaymentTransaction::count() . " payment transactions\n";
        echo "🛒 Hoàn thành seed orders và transactions!\n";
    }
}
