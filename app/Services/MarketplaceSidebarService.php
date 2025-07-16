<?php

namespace App\Services;

use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class MarketplaceSidebarService
{
    /**
     * Lấy tất cả dữ liệu sidebar marketplace với caching tối ưu
     */
    public function getMarketplaceSidebarData(User $user = null): array
    {
        return Cache::remember('marketplace_sidebar_data_' . ($user?->id ?? 'guest'), 300, function () use ($user) {
            return [
                'marketplace_stats' => $this->getMarketplaceStats(),
                'product_categories' => $this->getProductCategories(),
                'featured_products' => $this->getFeaturedProducts(),
                'top_sellers' => $this->getTopSellers(),
            ];
        });
    }

    /**
     * Lấy thống kê tổng quan marketplace
     */
    private function getMarketplaceStats(): array
    {
        return Cache::remember('marketplace_stats', 600, function () {
            // Kiểm tra xem bảng marketplace_products có tồn tại không
            if (!Schema::hasTable('marketplace_products')) {
                return [
                    'total_products' => 0,
                    'total_sales' => 0,
                    'avg_price' => 0,
                    'active_sellers' => 0,
                ];
            }

            $stats = DB::table('marketplace_products')
                ->selectRaw('
                    COUNT(*) as total_products,
                    COALESCE(SUM(purchase_count), 0) as total_sales,
                    COALESCE(AVG(price), 0) as avg_price
                ')
                ->where('status', 'approved')
                ->where('is_active', true)
                ->whereNull('deleted_at')
                ->first();

            $activeSellers = DB::table('marketplace_products')
                ->distinct('seller_id')
                ->where('status', 'approved')
                ->where('is_active', true)
                ->whereNull('deleted_at')
                ->count();

            return [
                'total_products' => $stats->total_products ?? 0,
                'total_sales' => $stats->total_sales ?? 0,
                'avg_price' => $stats->avg_price ?? 0,
                'active_sellers' => $activeSellers,
            ];
        });
    }

    /**
     * Lấy danh mục sản phẩm
     */
    private function getProductCategories(): array
    {
        return Cache::remember('marketplace_product_categories', 600, function () {
            // Kiểm tra xem bảng marketplace_products có tồn tại không
            if (!Schema::hasTable('marketplace_products')) {
                return $this->getDefaultCategories();
            }

            $categories = DB::table('marketplace_products')
                ->join('product_categories', 'marketplace_products.product_category_id', '=', 'product_categories.id')
                ->select('product_categories.name as category', 'product_categories.slug')
                ->selectRaw('
                    COUNT(*) as product_count,
                    MIN(marketplace_products.price) as min_price,
                    MAX(marketplace_products.price) as max_price,
                    COUNT(CASE WHEN marketplace_products.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as recent_count
                ')
                ->where('marketplace_products.status', 'approved')
                ->where('marketplace_products.is_active', true)
                ->whereNull('marketplace_products.deleted_at')
                ->whereNull('product_categories.deleted_at')
                ->groupBy('product_categories.id', 'product_categories.name', 'product_categories.slug')
                ->orderBy('product_count', 'desc')
                ->limit(6)
                ->get();

            if ($categories->isEmpty()) {
                return $this->getDefaultCategories();
            }

            return $categories->map(function ($category) {
                return [
                    'name' => $category->category,
                    'slug' => $category->slug,
                    'product_count' => $category->product_count,
                    'min_price' => $category->min_price,
                    'max_price' => $category->max_price,
                    'trend' => $category->recent_count > 0 ? 1 : 0,
                    'icon' => $this->getCategoryIcon($category->slug),
                ];
            })->toArray();
        });
    }

    /**
     * Lấy danh mục mặc định khi chưa có dữ liệu
     */
    private function getDefaultCategories(): array
    {
        return [
            [
                'name' => 'Digital Products',
                'slug' => 'digital',
                'product_count' => 0,
                'min_price' => 0,
                'max_price' => 0,
                'trend' => 0,
                'icon' => 'fas fa-download',
            ],
            [
                'name' => 'New Products',
                'slug' => 'new_product',
                'product_count' => 0,
                'min_price' => 0,
                'max_price' => 0,
                'trend' => 0,
                'icon' => 'fas fa-box',
            ],
            [
                'name' => 'Used Products',
                'slug' => 'used_product',
                'product_count' => 0,
                'min_price' => 0,
                'max_price' => 0,
                'trend' => 0,
                'icon' => 'fas fa-recycle',
            ],
        ];
    }

    /**
     * Lấy icon cho danh mục
     */
    private function getCategoryIcon(string $category): string
    {
        $icons = [
            'digital' => 'fas fa-download',
            'new_product' => 'fas fa-box',
            'used_product' => 'fas fa-recycle',
            'software' => 'fas fa-desktop',
            'hardware' => 'fas fa-microchip',
            'tools' => 'fas fa-tools',
            'materials' => 'fas fa-industry',
            'components' => 'fas fa-cogs',
        ];

        return $icons[$category] ?? 'fas fa-tag';
    }

    /**
     * Lấy sản phẩm nổi bật
     */
    private function getFeaturedProducts(): array
    {
        return Cache::remember('marketplace_featured_products', 300, function () {
            // Kiểm tra xem bảng marketplace_products có tồn tại không
            if (!Schema::hasTable('marketplace_products')) {
                return [];
            }

            $products = DB::table('marketplace_products')
                ->join('marketplace_sellers', 'marketplace_products.seller_id', '=', 'marketplace_sellers.id')
                ->join('users', 'marketplace_sellers.user_id', '=', 'users.id')
                ->select(
                    'marketplace_products.id',
                    'marketplace_products.name',
                    'marketplace_products.slug',
                    'marketplace_products.price',
                    'marketplace_products.sale_price',
                    'marketplace_products.product_type',
                    'marketplace_products.view_count',
                    'marketplace_products.purchase_count',
                    'marketplace_products.rating_average',
                    'marketplace_products.is_featured',
                    'marketplace_products.images',
                    'users.name as seller_name'
                )
                ->where('marketplace_products.status', 'approved')
                ->where('marketplace_products.is_active', true)
                ->whereNull('marketplace_products.deleted_at')
                ->whereNull('marketplace_sellers.deleted_at')
                ->where(function ($query) {
                    $query->where('marketplace_products.is_featured', true)
                          ->orWhere('marketplace_products.rating_average', '>=', 4.0)
                          ->orWhere('marketplace_products.view_count', '>=', 50);
                })
                ->orderByDesc('marketplace_products.is_featured')
                ->orderByDesc('marketplace_products.rating_average')
                ->orderByDesc('marketplace_products.view_count')
                ->limit(5)
                ->get();

            return $products->map(function ($product) {
                $images = json_decode($product->images, true);
                $imageUrl = !empty($images) ? $images[0] : asset('images/placeholder-product.jpg');

                $discountPercent = 0;
                if ($product->sale_price && $product->sale_price < $product->price) {
                    $discountPercent = round((($product->price - $product->sale_price) / $product->price) * 100);
                }

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'price' => $product->sale_price ?? $product->price,
                    'original_price' => $product->price,
                    'discount_percent' => $discountPercent,
                    'type' => $product->product_type,
                    'views' => $product->view_count ?? 0,
                    'sales' => $product->purchase_count ?? 0,
                    'rating' => $product->rating_average ?? 0,
                    'is_featured' => $product->is_featured,
                    'image_url' => $imageUrl,
                    'seller' => [
                        'name' => $product->seller_name,
                    ],
                ];
            })->toArray();
        });
    }

    /**
     * Lấy top sellers
     */
    private function getTopSellers(): array
    {
        return Cache::remember('marketplace_top_sellers', 600, function () {
            // Kiểm tra xem bảng marketplace_products có tồn tại không
            if (!Schema::hasTable('marketplace_products')) {
                return [];
            }

            $sellers = DB::table('marketplace_products')
                ->join('marketplace_sellers', 'marketplace_products.seller_id', '=', 'marketplace_sellers.id')
                ->join('users', 'marketplace_sellers.user_id', '=', 'users.id')
                ->select('users.id', 'users.name', 'users.username', 'users.avatar', 'users.role')
                ->selectRaw('
                    COUNT(marketplace_products.id) as product_count,
                    COALESCE(SUM(marketplace_products.purchase_count), 0) as total_sales,
                    COALESCE(AVG(marketplace_products.rating_average), 0) as avg_rating
                ')
                ->where('marketplace_products.status', 'approved')
                ->where('marketplace_products.is_active', true)
                ->whereNull('marketplace_products.deleted_at')
                ->whereNull('marketplace_sellers.deleted_at')
                ->groupBy('users.id', 'users.name', 'users.username', 'users.avatar', 'users.role')
                ->orderBy('total_sales', 'desc')
                ->orderBy('product_count', 'desc')
                ->limit(6)
                ->get();

            return $sellers->map(function ($seller) {
                $roleInfo = $this->getRoleInfo($seller->role);

                return [
                    'name' => $seller->name,
                    'username' => $seller->username,
                    'avatar' => $seller->avatar ?? route('avatar.generate', ['initial' => strtoupper(substr($seller->name, 0, 1)), 'size' => 40]),
                    'product_count' => $seller->product_count,
                    'total_sales' => $seller->total_sales,
                    'avg_rating' => round($seller->avg_rating, 1),
                    'is_verified' => in_array($seller->role, ['verified_partner', 'manufacturer', 'supplier', 'brand']),
                    'role_name' => $roleInfo['name'],
                    'role_icon' => $roleInfo['icon'],
                    'role_class' => $roleInfo['class'],
                ];
            })->toArray();
        });
    }

    /**
     * Lấy thông tin role
     */
    private function getRoleInfo(string $role): array
    {
        $roles = [
            'verified_partner' => [
                'name' => 'Verified Partner',
                'icon' => 'fas fa-certificate',
                'class' => 'badge-verified',
            ],
            'manufacturer' => [
                'name' => 'Manufacturer',
                'icon' => 'fas fa-industry',
                'class' => 'badge-manufacturer',
            ],
            'supplier' => [
                'name' => 'Supplier',
                'icon' => 'fas fa-truck',
                'class' => 'badge-supplier',
            ],
            'brand' => [
                'name' => 'Brand',
                'icon' => 'fas fa-star',
                'class' => 'badge-brand',
            ],
        ];

        return $roles[$role] ?? [
            'name' => 'Member',
            'icon' => 'fas fa-user',
            'class' => 'badge-member',
        ];
    }

    /**
     * Clear cache khi có thay đổi dữ liệu
     */
    public function clearCache(): void
    {
        Cache::forget('marketplace_sidebar_data_guest');
        Cache::forget('marketplace_stats');
        Cache::forget('marketplace_product_categories');
        Cache::forget('marketplace_featured_products');
        Cache::forget('marketplace_top_sellers');

        // Clear user-specific cache
        $userIds = User::pluck('id');
        foreach ($userIds as $userId) {
            Cache::forget('marketplace_sidebar_data_' . $userId);
        }
    }
}
