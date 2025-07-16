<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceProduct;
use App\Models\MarketplaceSeller;
use App\Models\MarketplaceCart;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MarketplaceStatsController extends Controller
{
    /**
     * Get quick stats for marketplace mega menu
     */
    public function quickStats(): JsonResponse
    {
        try {
            $stats = Cache::remember('marketplace_quick_stats', 300, function () {
                return [
                    'total_products' => MarketplaceProduct::where('status', 'approved')
                        ->where('is_active', true)
                        ->count(),
                    
                    'digital_products' => MarketplaceProduct::where('status', 'approved')
                        ->where('is_active', true)
                        ->where('type', 'digital')
                        ->count(),
                    
                    'new_products' => MarketplaceProduct::where('status', 'approved')
                        ->where('is_active', true)
                        ->where('type', 'new_product')
                        ->count(),
                    
                    'used_products' => MarketplaceProduct::where('status', 'approved')
                        ->where('is_active', true)
                        ->where('type', 'used_product')
                        ->count(),
                    
                    'total_suppliers' => MarketplaceSeller::where('status', 'active')
                        ->count(),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading marketplace stats',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get cart count for authenticated user
     */
    public function cartCount(Request $request): JsonResponse
    {
        try {
            if (!auth()->check()) {
                return response()->json([
                    'success' => true,
                    'data' => ['count' => 0]
                ]);
            }

            $count = MarketplaceCart::where('user_id', auth()->id())
                ->sum('quantity');

            return response()->json([
                'success' => true,
                'data' => ['count' => $count]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading cart count',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get marketplace overview stats
     */
    public function overview(): JsonResponse
    {
        try {
            $stats = Cache::remember('marketplace_overview_stats', 600, function () {
                $totalProducts = MarketplaceProduct::where('status', 'approved')
                    ->where('is_active', true)
                    ->count();

                $totalSellers = MarketplaceSeller::where('status', 'active')
                    ->count();

                $totalOrders = \DB::table('marketplace_orders')
                    ->where('status', '!=', 'cancelled')
                    ->count();

                $totalRevenue = \DB::table('marketplace_orders')
                    ->where('status', 'completed')
                    ->sum('total_amount');

                return [
                    'total_products' => $totalProducts,
                    'total_sellers' => $totalSellers,
                    'total_orders' => $totalOrders,
                    'total_revenue' => $totalRevenue,
                    'avg_order_value' => $totalOrders > 0 ? round($totalRevenue / $totalOrders, 2) : 0,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading marketplace overview',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get trending products stats
     */
    public function trending(): JsonResponse
    {
        try {
            $trending = Cache::remember('marketplace_trending_stats', 300, function () {
                return MarketplaceProduct::where('status', 'approved')
                    ->where('is_active', true)
                    ->orderBy('view_count', 'desc')
                    ->limit(5)
                    ->get(['id', 'name', 'slug', 'view_count', 'price'])
                    ->map(function ($product) {
                        return [
                            'id' => $product->id,
                            'name' => $product->name,
                            'slug' => $product->slug,
                            'view_count' => $product->view_count,
                            'price' => $product->price,
                            'url' => route('marketplace.products.show', $product->slug)
                        ];
                    });
            });

            return response()->json([
                'success' => true,
                'data' => $trending
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading trending products',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
