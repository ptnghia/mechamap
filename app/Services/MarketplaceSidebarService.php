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
            // Kiểm tra xem bảng products có tồn tại không
            if (!Schema::hasTable('products')) {
                return [
                    'total_products' => 0,
                    'total_sales' => 0,
                    'avg_price' => 0,
                    'active_sellers' => 0,
                ];
            }

            $stats = DB::table('products')
                ->selectRaw('
                    COUNT(*) as total_products,
                    COALESCE(SUM(sales_count), 0) as total_sales,
                    COALESCE(AVG(price), 0) as avg_price
                ')
                ->where('status', 'active')
                ->first();

            $activeSellers = DB::table('products')
                ->distinct('seller_id')
                ->where('status', 'active')
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
            // Kiểm tra xem bảng products có tồn tại không
            if (!Schema::hasTable('products')) {
                return $this->getDefaultCategories();
            }

            $categories = DB::table('products')
                ->select('category')
                ->selectRaw('
                    COUNT(*) as product_count,
                    MIN(price) as min_price,
                    MAX(price) as max_price,
                    COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as recent_count
                ')
                ->where('status', 'active')
                ->whereNotNull('category')
                ->groupBy('category')
                ->orderBy('product_count', 'desc')
                ->limit(6)
                ->get();

            if ($categories->isEmpty()) {
                return $this->getDefaultCategories();
            }

            return $categories->map(function ($category) {
                return [
                    'name' => ucfirst($category->category),
                    'slug' => $category->category,
                    'product_count' => $category->product_count,
                    'min_price' => $category->min_price,
                    'max_price' => $category->max_price,
                    'trend' => $category->recent_count > 0 ? 1 : 0,
                    'icon' => $this->getCategoryIcon($category->category),
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
            // Kiểm tra xem bảng products có tồn tại không
            if (!Schema::hasTable('products')) {
                return [];
            }

            $products = DB::table('products')
                ->join('users', 'products.seller_id', '=', 'users.id')
                ->select(
                    'products.id',
                    'products.name',
                    'products.price',
                    'products.original_price',
                    'products.type',
                    'products.views',
                    'products.sales_count',
                    'products.rating',
                    'products.is_featured',
                    'products.images',
                    'users.name as seller_name'
                )
                ->where('products.status', 'active')
                ->where(function ($query) {
                    $query->where('products.is_featured', true)
                          ->orWhere('products.rating', '>=', 4.0)
                          ->orWhere('products.views', '>=', 50);
                })
                ->orderByDesc('products.is_featured')
                ->orderByDesc('products.rating')
                ->orderByDesc('products.views')
                ->limit(5)
                ->get();

            return $products->map(function ($product) {
                $images = json_decode($product->images, true);
                $imageUrl = !empty($images) ? $images[0] : asset('images/placeholder-product.jpg');

                $discountPercent = 0;
                if ($product->original_price && $product->original_price > $product->price) {
                    $discountPercent = round((($product->original_price - $product->price) / $product->original_price) * 100);
                }

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'original_price' => $product->original_price,
                    'discount_percent' => $discountPercent,
                    'type' => $product->type,
                    'views' => $product->views ?? 0,
                    'sales' => $product->sales_count ?? 0,
                    'rating' => $product->rating ?? 0,
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
            // Kiểm tra xem bảng products có tồn tại không
            if (!Schema::hasTable('products')) {
                return [];
            }

            $sellers = DB::table('products')
                ->join('users', 'products.seller_id', '=', 'users.id')
                ->select('users.id', 'users.name', 'users.username', 'users.avatar', 'users.role')
                ->selectRaw('
                    COUNT(products.id) as product_count,
                    COALESCE(SUM(products.sales_count), 0) as total_sales,
                    COALESCE(AVG(products.rating), 0) as avg_rating
                ')
                ->where('products.status', 'active')
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
