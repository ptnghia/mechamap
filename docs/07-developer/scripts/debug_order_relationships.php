<?php
/**
 * Debug Order Model Relationships
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

echo "🔍 DEBUGGING ORDER MODEL RELATIONSHIPS\n";
echo "======================================\n\n";

try {
    echo "📝 Step 1: Test basic Order query...\n";
    $order = \App\Models\Order::find(6);
    if ($order) {
        echo "✅ Order 6 found: {$order->order_number}\n";
    } else {
        echo "❌ Order 6 not found\n";
        exit(1);
    }

    echo "\n📝 Step 2: Test Order with items...\n";
    $orderWithItems = \App\Models\Order::with('items')->find(6);
    if ($orderWithItems && $orderWithItems->items) {
        echo "✅ Order items loaded: " . count($orderWithItems->items) . " items\n";
    } else {
        echo "❌ Failed to load order items\n";
    }

    echo "\n📝 Step 3: Test Order with items.product...\n";
    try {
        $orderWithProduct = \App\Models\Order::with(['items.product'])->find(6);
        if ($orderWithProduct) {
            echo "✅ Order with items.product loaded successfully\n";
            foreach ($orderWithProduct->items as $item) {
                if ($item->product) {
                    echo "  - Item {$item->id}: Product '{$item->product->title}'\n";
                } else {
                    echo "  - Item {$item->id}: No product relationship\n";
                }
            }
        }
    } catch (Exception $e) {
        echo "❌ Error loading items.product: " . $e->getMessage() . "\n";
    }

    echo "\n📝 Step 4: Test exact relationships from OrderController...\n";
    try {
        $orderFromController = \App\Models\Order::where('id', 6)
            ->where('user_id', 1)
            ->with([
                'items.product.category',
                'items.product.files',
                'transactions' => function($query) {
                    $query->orderBy('created_at', 'desc');
                }
            ])
            ->first();

        if ($orderFromController) {
            echo "✅ OrderController query successful\n";
            echo "  - Order: {$orderFromController->order_number}\n";
            echo "  - Items: " . count($orderFromController->items) . "\n";
            echo "  - Transactions: " . count($orderFromController->transactions) . "\n";

            foreach ($orderFromController->items as $item) {
                echo "  - Item {$item->id}:\n";
                echo "    - Product: " . ($item->product ? $item->product->title : 'NULL') . "\n";
                echo "    - Category: " . ($item->product && $item->product->category ? $item->product->category->name : 'NULL') . "\n";
                echo "    - Files: " . ($item->product && $item->product->files ? count($item->product->files) : '0') . "\n";
            }
        } else {
            echo "❌ OrderController query failed\n";
        }
    } catch (Exception $e) {
        echo "❌ Error in OrderController query: " . $e->getMessage() . "\n";
        echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    }

    echo "\n📝 Step 5: Check model relationships...\n";
    $item = \App\Models\OrderItem::find(7);
    if ($item) {
        echo "✅ OrderItem 7 found\n";
        echo "  - Technical Product ID: {$item->technical_product_id}\n";

        try {
            $product = $item->product;
            if ($product) {
                echo "  - Product loaded: {$product->title}\n";
            } else {
                echo "  - Product is NULL\n";
            }
        } catch (Exception $e) {
            echo "  - Error loading product: " . $e->getMessage() . "\n";
        }
    }

} catch (Exception $e) {
    echo "❌ Fatal error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n✅ Debug completed!\n";
