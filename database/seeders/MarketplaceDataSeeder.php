<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TechnicalProduct;
use App\Models\User;
use App\Models\ShoppingCart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentTransaction;
use App\Models\ProductReview;
use Illuminate\Support\Facades\DB;

class MarketplaceDataSeeder extends Seeder
{
    /**
     * Seed marketplace data: shopping carts, orders, transactions
     * Creates realistic marketplace activity for MechaMap
     */
    public function run(): void
    {
        $this->command->info('🛒 Bắt đầu seed marketplace data...');

        // Get data - use TechnicalProduct instead of Product for compatibility
        $products = TechnicalProduct::where('status', 'approved')->get();
        $users = User::whereIn('role', ['member', 'senior', 'admin', 'moderator'])->get();
        $businessUsers = User::whereIn('role', ['supplier', 'manufacturer', 'brand'])->get();

        if ($products->isEmpty()) {
            $this->command->error('❌ Cần có products trước!');
            return;
        }

        if ($users->isEmpty()) {
            $this->command->error('❌ Cần có users trước!');
            return;
        }

        // Create shopping carts
        $carts = $this->createShoppingCarts($users, $products);
        $this->command->info('✅ Đã tạo ' . count($carts) . ' shopping carts');

        // Create orders and transactions
        $orders = $this->createOrdersAndTransactions($users, $products);
        $this->command->info('✅ Đã tạo ' . count($orders) . ' orders với transactions');

        // Skip product reviews for now (need to create TechnicalProductReview model)
        // $reviews = $this->createProductReviews($users, $products);
        // $this->command->info('✅ Đã tạo ' . count($reviews) . ' product reviews');

        // Update product statistics
        $this->updateProductStatistics();
        $this->command->info('✅ Đã cập nhật product statistics');

        $this->command->info('🎉 Hoàn thành seed marketplace data!');
    }

    private function createShoppingCarts($users, $products): array
    {
        $carts = [];

        // Create carts for 30% of users
        $usersWithCarts = $users->random(intval($users->count() * 0.3));

        foreach ($usersWithCarts as $user) {
            // Each user has 1-3 items in cart
            $cartProducts = $products->random(rand(1, 3));

            foreach ($cartProducts as $product) {
                $quantity = 1; // Most digital products are quantity 1
                $unitPrice = $product->sale_price ?? $product->price;

                // Skip if already in cart
                $existingCart = ShoppingCart::where('user_id', $user->id)
                    ->where('technical_product_id', $product->id)
                    ->first();

                if ($existingCart) {
                    continue;
                }

                $cart = ShoppingCart::create([
                    'user_id' => $user->id,
                    'technical_product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $unitPrice * $quantity,
                    'license_type' => 'standard',
                    'product_snapshot' => [
                        'title' => $product->title,
                        'description' => $product->description,
                        'price' => $product->price,
                        'sale_price' => $product->sale_price,
                    ],
                    'status' => 'active',
                    'expires_at' => now()->addDays(7),
                ]);

                $carts[] = $cart;
            }
        }

        return $carts;
    }

    private function createOrdersAndTransactions($users, $products): array
    {
        $orders = [];

        // Create orders for 40% of users
        $usersWithOrders = $users->random(intval($users->count() * 0.4));

        foreach ($usersWithOrders as $user) {
            // Each user has 1-3 orders
            $orderCount = rand(1, 3);

            for ($i = 0; $i < $orderCount; $i++) {
                $order = $this->createSingleOrder($user, $products);
                $orders[] = $order;
            }
        }

        return $orders;
    }

    private function createSingleOrder($user, $products): Order
    {
        // Select 1-2 products for this order
        $orderProducts = $products->random(rand(1, 2));

        $subtotal = 0;
        $orderItems = [];

        // Calculate order total
        foreach ($orderProducts as $product) {
            $quantity = 1; // Digital products usually quantity 1
            $unitPrice = $product->sale_price ?? $product->price;
            $itemTotal = $unitPrice * $quantity;
            $subtotal += $itemTotal;

            $orderItems[] = [
                'technical_product_id' => $product->id, // Use correct field name
                'seller_id' => $product->seller_id,
                'product_title' => $product->title,
                'product_description' => $product->description,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $itemTotal,
                'product_snapshot' => [
                    'title' => $product->title,
                    'price' => $product->price,
                    'sale_price' => $product->sale_price,
                ],
            ];
        }

        $tax = $subtotal * 0.1; // 10% VAT
        $total = $subtotal + $tax;

        // Create order
        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => 'MECHA-' . strtoupper(uniqid()),
            'status' => $this->getRandomOrderStatus(),
            'subtotal' => $subtotal,
            'tax_amount' => $tax,
            'total_amount' => $total,
            'payment_status' => $this->getRandomPaymentStatus(),
            'payment_method' => $this->getRandomPaymentMethod(),
            'billing_address' => $this->getBillingAddress(),
            'notes' => $this->getOrderNotes(),
            'created_at' => now()->subDays(rand(0, 30)),
        ]);

        // Create order items
        foreach ($orderItems as $itemData) {
            OrderItem::create([
                'order_id' => $order->id,
                'technical_product_id' => $itemData['technical_product_id'],
                'seller_id' => $itemData['seller_id'],
                'product_title' => $itemData['product_title'],
                'product_description' => $itemData['product_description'],
                'product_snapshot' => $itemData['product_snapshot'],
                'quantity' => $itemData['quantity'],
                'unit_price' => $itemData['unit_price'],
                'total_price' => $itemData['total_price'],
                'seller_earnings' => $itemData['total_price'] * 0.85, // 85% for seller
                'platform_fee' => $itemData['total_price'] * 0.15, // 15% platform fee
                'license_type' => 'single',
                'download_limit' => 10,
                'status' => 'pending',
            ]);
        }

        // Create payment transaction
        if ($order->payment_status !== 'pending') {
            PaymentTransaction::create([
                'transaction_id' => 'TXN-' . strtoupper(uniqid()),
                'order_id' => $order->id,
                'user_id' => $user->id,
                'payment_method' => $order->payment_method,
                'type' => 'payment',
                'status' => $order->payment_status === 'completed' ? 'completed' : 'failed',
                'amount' => $order->total_amount,
                'currency' => 'VND',
                'fee_amount' => $order->total_amount * 0.029, // 2.9% fee
                'net_amount' => $order->total_amount * 0.971,
                'gateway_response' => ['status' => 'success', 'ref' => uniqid()],
                'processed_at' => $order->created_at->addMinutes(rand(1, 30)),
            ]);
        }

        return $order;
    }

    private function createProductReviews($users, $products): array
    {
        $reviews = [];

        // Create reviews for 60% of products
        $reviewedProducts = $products->random(intval($products->count() * 0.6));

        foreach ($reviewedProducts as $product) {
            // Each product gets 2-8 reviews
            $reviewCount = rand(2, 8);
            $reviewUsers = $users->random($reviewCount);

            foreach ($reviewUsers as $user) {
                $rating = rand(3, 5); // Most reviews are positive

                // Skip if user already reviewed this product
                $existingReview = ProductReview::where('product_id', $product->id)
                    ->where('user_id', $user->id)
                    ->first();

                if ($existingReview) {
                    continue;
                }

                $review = ProductReview::create([
                    'product_id' => $product->id, // Keep as product_id for ProductReview
                    'user_id' => $user->id,
                    'rating' => $rating,
                    'title' => $this->getReviewTitle($rating),
                    'content' => $this->getReviewContent($rating, $product),
                    'is_verified_purchase' => rand(0, 1),
                    'helpful_count' => rand(0, 15),
                    'created_at' => now()->subDays(rand(0, 60)),
                ]);

                $reviews[] = $review;
            }
        }

        return $reviews;
    }

    private function updateProductStatistics(): void
    {
        // Update product ratings and counts
        $products = TechnicalProduct::all();

        foreach ($products as $product) {
            // Skip review updates for now
            // $reviews = ProductReview::where('product_id', $product->id)->get();

            // if ($reviews->isNotEmpty()) {
            //     $averageRating = $reviews->avg('rating');
            //     $reviewCount = $reviews->count();

            //     $product->update([
            //         'rating_average' => round($averageRating, 2),
            //         'rating_count' => $reviewCount,
            //     ]);
            // }

            // Update sales count from orders
            $salesCount = OrderItem::where('technical_product_id', $product->id)
                ->whereHas('order', function($query) {
                    $query->whereIn('status', ['completed']);
                })
                ->sum('quantity');

            $product->update(['sales_count' => $salesCount]);
        }
    }

    // Helper methods
    private function getProductOptions($product): ?array
    {
        if ($product->product_type === 'physical') {
            return [
                'color' => ['Red', 'Blue', 'Silver', 'Black'][array_rand(['Red', 'Blue', 'Silver', 'Black'])],
                'warranty' => rand(0, 1) ? '12 months' : '24 months'
            ];
        }

        return null;
    }

    private function getCartNotes(): ?string
    {
        $notes = [
            'Cần giao hàng trước ngày 15',
            'Kiểm tra kỹ chất lượng trước khi giao',
            'Liên hệ trước khi giao hàng',
            null, null, null // Most carts don't have notes
        ];

        return $notes[array_rand($notes)];
    }

    private function calculateShippingFee($products): float
    {
        $hasPhysical = $products->contains(function($product) {
            return $product->requires_shipping;
        });

        if (!$hasPhysical) {
            return 0;
        }

        return rand(30000, 100000); // 30k-100k VND shipping
    }

    private function getRandomOrderStatus(): string
    {
        $statuses = ['pending', 'confirmed', 'processing', 'completed', 'cancelled'];
        $weights = [10, 15, 20, 50, 5]; // Higher chance for completed orders

        return $this->weightedRandom($statuses, $weights);
    }

    private function getRandomPaymentStatus(): string
    {
        $statuses = ['pending', 'completed', 'failed', 'refunded'];
        $weights = [10, 70, 15, 5]; // Most orders are completed

        return $this->weightedRandom($statuses, $weights);
    }

    private function getRandomPaymentMethod(): string
    {
        $methods = ['stripe', 'vnpay', 'bank_transfer'];
        return $methods[array_rand($methods)];
    }

    private function getShippingAddress(): array
    {
        $addresses = [
            [
                'name' => 'Nguyễn Văn A',
                'phone' => '0901234567',
                'address' => '123 Đường ABC, Phường XYZ',
                'city' => 'TP. Hồ Chí Minh',
                'district' => 'Quận 1',
                'postal_code' => '70000'
            ],
            [
                'name' => 'Trần Thị B',
                'phone' => '0987654321',
                'address' => '456 Đường DEF, Phường UVW',
                'city' => 'Hà Nội',
                'district' => 'Quận Ba Đình',
                'postal_code' => '10000'
            ]
        ];

        return $addresses[array_rand($addresses)];
    }

    private function getBillingAddress(): array
    {
        return $this->getShippingAddress(); // Same as shipping for simplicity
    }

    private function getOrderNotes(): ?string
    {
        $notes = [
            'Giao hàng trong giờ hành chính',
            'Kiểm tra hàng trước khi thanh toán',
            'Liên hệ trước 30 phút',
            null, null // Most orders don't have notes
        ];

        return $notes[array_rand($notes)];
    }

    private function getReviewTitle($rating): string
    {
        if ($rating >= 5) {
            return ['Sản phẩm tuyệt vời!', 'Chất lượng xuất sắc', 'Rất hài lòng'][array_rand(['Sản phẩm tuyệt vời!', 'Chất lượng xuất sắc', 'Rất hài lòng'])];
        } elseif ($rating >= 4) {
            return ['Sản phẩm tốt', 'Đáng mua', 'Chất lượng ổn'][array_rand(['Sản phẩm tốt', 'Đáng mua', 'Chất lượng ổn'])];
        } else {
            return ['Bình thường', 'Có thể cải thiện', 'Tạm được'][array_rand(['Bình thường', 'Có thể cải thiện', 'Tạm được'])];
        }
    }

    private function getReviewContent($rating, $product): string
    {
        if ($rating >= 5) {
            return "Sản phẩm {$product->name} rất chất lượng, đúng như mô tả. Giao hàng nhanh, đóng gói cẩn thận. Sẽ mua lại lần sau.";
        } elseif ($rating >= 4) {
            return "Sản phẩm {$product->name} tốt, chất lượng ổn định. Giá cả hợp lý. Có thể cải thiện thêm về bao bì.";
        } else {
            return "Sản phẩm {$product->name} tạm được, chất lượng bình thường. Cần cải thiện thêm về chất lượng và dịch vụ.";
        }
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
