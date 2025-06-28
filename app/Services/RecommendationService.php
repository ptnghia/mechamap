<?php

namespace App\Services;

use App\Models\User;
use App\Models\MarketplaceProduct;
use App\Models\TechnicalDrawing;
use App\Models\Material;
use App\Models\Thread;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class RecommendationService
{
    /**
     * Get personalized recommendations for a user
     */
    public function getPersonalizedRecommendations($userId, $type = 'all', $limit = 10)
    {
        $cacheKey = "recommendations_{$userId}_{$type}_{$limit}";
        
        return Cache::remember($cacheKey, 3600, function () use ($userId, $type, $limit) {
            $user = User::find($userId);
            if (!$user) {
                return [];
            }

            switch ($type) {
                case 'products':
                    return $this->getProductRecommendations($user, $limit);
                case 'drawings':
                    return $this->getDrawingRecommendations($user, $limit);
                case 'materials':
                    return $this->getMaterialRecommendations($user, $limit);
                case 'threads':
                    return $this->getThreadRecommendations($user, $limit);
                default:
                    return $this->getAllRecommendations($user, $limit);
            }
        });
    }

    /**
     * Get product recommendations based on user behavior
     */
    public function getProductRecommendations($user, $limit = 10)
    {
        // Get user's interaction history
        $userInteractions = $this->getUserProductInteractions($user->id);
        
        // Collaborative filtering - find similar users
        $similarUsers = $this->findSimilarUsers($user->id, 'products');
        
        // Content-based filtering - find similar products
        $contentBasedRecommendations = $this->getContentBasedProductRecommendations($userInteractions);
        
        // Collaborative filtering recommendations
        $collaborativeRecommendations = $this->getCollaborativeProductRecommendations($similarUsers);
        
        // Trending products
        $trendingProducts = $this->getTrendingProducts();
        
        // Combine and score recommendations
        $recommendations = $this->combineRecommendations([
            'content_based' => $contentBasedRecommendations,
            'collaborative' => $collaborativeRecommendations,
            'trending' => $trendingProducts,
        ], $limit);

        return $this->formatProductRecommendations($recommendations);
    }

    /**
     * Get technical drawing recommendations
     */
    public function getDrawingRecommendations($user, $limit = 10)
    {
        $userInteractions = $this->getUserDrawingInteractions($user->id);
        
        // Find drawings based on user's industry/interests
        $industryRecommendations = $this->getIndustryBasedDrawings($user);
        
        // Find drawings similar to user's downloads
        $similarDrawings = $this->getSimilarDrawings($userInteractions);
        
        // Popular drawings in user's field
        $popularDrawings = $this->getPopularDrawingsInField($user);
        
        $recommendations = $this->combineRecommendations([
            'industry_based' => $industryRecommendations,
            'similar' => $similarDrawings,
            'popular' => $popularDrawings,
        ], $limit);

        return $this->formatDrawingRecommendations($recommendations);
    }

    /**
     * Get material recommendations
     */
    public function getMaterialRecommendations($user, $limit = 10)
    {
        // Get materials based on user's projects/interests
        $projectBasedMaterials = $this->getProjectBasedMaterials($user);
        
        // Get materials with similar properties to user's viewed materials
        $similarMaterials = $this->getSimilarMaterials($user);
        
        // Get trending materials in user's industry
        $trendingMaterials = $this->getTrendingMaterials($user);
        
        $recommendations = $this->combineRecommendations([
            'project_based' => $projectBasedMaterials,
            'similar' => $similarMaterials,
            'trending' => $trendingMaterials,
        ], $limit);

        return $this->formatMaterialRecommendations($recommendations);
    }

    /**
     * Get thread/discussion recommendations
     */
    public function getThreadRecommendations($user, $limit = 10)
    {
        // Get threads based on user's forum activity
        $forumActivity = $this->getUserForumActivity($user->id);
        
        // Get threads in user's areas of interest
        $interestBasedThreads = $this->getInterestBasedThreads($user);
        
        // Get trending threads
        $trendingThreads = $this->getTrendingThreads();
        
        $recommendations = $this->combineRecommendations([
            'forum_activity' => $forumActivity,
            'interest_based' => $interestBasedThreads,
            'trending' => $trendingThreads,
        ], $limit);

        return $this->formatThreadRecommendations($recommendations);
    }

    /**
     * Get smart search suggestions
     */
    public function getSearchSuggestions($query, $type = 'all', $limit = 5)
    {
        $cacheKey = "search_suggestions_" . md5($query) . "_{$type}_{$limit}";
        
        return Cache::remember($cacheKey, 1800, function () use ($query, $type, $limit) {
            $suggestions = [];
            
            if ($type === 'all' || $type === 'products') {
                $suggestions['products'] = $this->getProductSuggestions($query, $limit);
            }
            
            if ($type === 'all' || $type === 'drawings') {
                $suggestions['drawings'] = $this->getDrawingSuggestions($query, $limit);
            }
            
            if ($type === 'all' || $type === 'materials') {
                $suggestions['materials'] = $this->getMaterialSuggestions($query, $limit);
            }
            
            if ($type === 'all' || $type === 'threads') {
                $suggestions['threads'] = $this->getThreadSuggestions($query, $limit);
            }
            
            return $suggestions;
        });
    }

    /**
     * Get related items for a specific item
     */
    public function getRelatedItems($itemType, $itemId, $limit = 5)
    {
        $cacheKey = "related_items_{$itemType}_{$itemId}_{$limit}";
        
        return Cache::remember($cacheKey, 3600, function () use ($itemType, $itemId, $limit) {
            switch ($itemType) {
                case 'product':
                    return $this->getRelatedProducts($itemId, $limit);
                case 'drawing':
                    return $this->getRelatedDrawings($itemId, $limit);
                case 'material':
                    return $this->getRelatedMaterials($itemId, $limit);
                case 'thread':
                    return $this->getRelatedThreads($itemId, $limit);
                default:
                    return [];
            }
        });
    }

    /**
     * Private helper methods
     */
    private function getUserProductInteractions($userId)
    {
        // Get user's product views, purchases, likes, etc.
        return DB::table('marketplace_orders')
            ->join('marketplace_order_items', 'marketplace_orders.id', '=', 'marketplace_order_items.order_id')
            ->where('marketplace_orders.customer_id', $userId)
            ->select('marketplace_order_items.product_id', 'marketplace_order_items.product_name')
            ->get();
    }

    private function getUserDrawingInteractions($userId)
    {
        // Get user's drawing downloads, views, etc.
        return TechnicalDrawing::where('created_by', $userId)
            ->orWhere('download_count', '>', 0) // This would need proper tracking
            ->select('id', 'title', 'drawing_type', 'industry_category')
            ->get();
    }

    private function findSimilarUsers($userId, $context = 'products')
    {
        // Implement collaborative filtering to find users with similar behavior
        // This is a simplified version - in production, you'd use more sophisticated algorithms
        
        return User::where('id', '!=', $userId)
            ->whereHas('marketplaceOrders')
            ->limit(10)
            ->get();
    }

    private function getContentBasedProductRecommendations($userInteractions)
    {
        if ($userInteractions->isEmpty()) {
            return collect();
        }

        // Find products similar to user's purchased/viewed products
        $productIds = $userInteractions->pluck('product_id')->toArray();
        
        return MarketplaceProduct::whereNotIn('id', $productIds)
            ->where('status', 'approved')
            ->where('is_active', true)
            ->inRandomOrder()
            ->limit(10)
            ->get();
    }

    private function getCollaborativeProductRecommendations($similarUsers)
    {
        if ($similarUsers->isEmpty()) {
            return collect();
        }

        $userIds = $similarUsers->pluck('id')->toArray();
        
        return MarketplaceProduct::whereHas('orderItems', function($query) use ($userIds) {
                $query->whereHas('order', function($orderQuery) use ($userIds) {
                    $orderQuery->whereIn('customer_id', $userIds);
                });
            })
            ->where('status', 'approved')
            ->where('is_active', true)
            ->withCount('orderItems')
            ->orderBy('order_items_count', 'desc')
            ->limit(10)
            ->get();
    }

    private function getTrendingProducts()
    {
        return MarketplaceProduct::where('status', 'approved')
            ->where('is_active', true)
            ->where('created_at', '>=', now()->subDays(30))
            ->orderBy('view_count', 'desc')
            ->limit(10)
            ->get();
    }

    private function getIndustryBasedDrawings($user)
    {
        // Get drawings based on user's industry or profile
        return TechnicalDrawing::where('status', 'approved')
            ->where('visibility', 'public')
            ->where('industry_category', $user->industry ?? 'mechanical')
            ->orderBy('download_count', 'desc')
            ->limit(10)
            ->get();
    }

    private function getSimilarDrawings($userInteractions)
    {
        if ($userInteractions->isEmpty()) {
            return collect();
        }

        $drawingTypes = $userInteractions->pluck('drawing_type')->unique()->toArray();
        $categories = $userInteractions->pluck('industry_category')->unique()->toArray();
        
        return TechnicalDrawing::where('status', 'approved')
            ->where('visibility', 'public')
            ->where(function($query) use ($drawingTypes, $categories) {
                $query->whereIn('drawing_type', $drawingTypes)
                      ->orWhereIn('industry_category', $categories);
            })
            ->orderBy('download_count', 'desc')
            ->limit(10)
            ->get();
    }

    private function getPopularDrawingsInField($user)
    {
        return TechnicalDrawing::where('status', 'approved')
            ->where('visibility', 'public')
            ->where('created_at', '>=', now()->subDays(30))
            ->orderBy('view_count', 'desc')
            ->limit(10)
            ->get();
    }

    private function combineRecommendations($sources, $limit)
    {
        $combined = collect();
        
        foreach ($sources as $source => $items) {
            foreach ($items as $item) {
                $item->recommendation_source = $source;
                $item->recommendation_score = $this->calculateRecommendationScore($item, $source);
                $combined->push($item);
            }
        }
        
        return $combined->sortByDesc('recommendation_score')->take($limit);
    }

    private function calculateRecommendationScore($item, $source)
    {
        $baseScore = 50;
        
        // Adjust score based on source
        switch ($source) {
            case 'content_based':
                $baseScore += 30;
                break;
            case 'collaborative':
                $baseScore += 25;
                break;
            case 'trending':
                $baseScore += 20;
                break;
            case 'industry_based':
                $baseScore += 35;
                break;
        }
        
        // Adjust based on item popularity
        if (isset($item->view_count)) {
            $baseScore += min($item->view_count / 100, 20);
        }
        
        if (isset($item->download_count)) {
            $baseScore += min($item->download_count / 10, 15);
        }
        
        return $baseScore;
    }

    private function formatProductRecommendations($recommendations)
    {
        return $recommendations->map(function($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'image' => $product->featured_image,
                'seller' => $product->seller->business_name ?? 'N/A',
                'score' => $product->recommendation_score,
                'source' => $product->recommendation_source,
                'url' => route('admin.marketplace.products.show', $product),
            ];
        });
    }

    private function formatDrawingRecommendations($recommendations)
    {
        return $recommendations->map(function($drawing) {
            return [
                'id' => $drawing->id,
                'title' => $drawing->title,
                'type' => $drawing->drawing_type,
                'downloads' => $drawing->download_count,
                'score' => $drawing->recommendation_score,
                'source' => $drawing->recommendation_source,
                'url' => route('admin.technical.drawings.show', $drawing),
            ];
        });
    }

    private function formatMaterialRecommendations($recommendations)
    {
        return $recommendations->map(function($material) {
            return [
                'id' => $material->id,
                'name' => $material->name,
                'category' => $material->category,
                'properties' => [
                    'density' => $material->density,
                    'yield_strength' => $material->yield_strength,
                ],
                'score' => $material->recommendation_score ?? 50,
                'source' => $material->recommendation_source ?? 'default',
                'url' => route('admin.technical.materials.show', $material),
            ];
        });
    }

    private function formatThreadRecommendations($recommendations)
    {
        return $recommendations->map(function($thread) {
            return [
                'id' => $thread->id,
                'title' => $thread->title,
                'forum' => $thread->forum->name ?? 'N/A',
                'replies' => $thread->comment_count,
                'score' => $thread->recommendation_score ?? 50,
                'source' => $thread->recommendation_source ?? 'default',
                'url' => route('admin.forums.threads.show', $thread),
            ];
        });
    }

    // Additional helper methods for other recommendation types...
    private function getProjectBasedMaterials($user) { return collect(); }
    private function getSimilarMaterials($user) { return collect(); }
    private function getTrendingMaterials($user) { return collect(); }
    private function getUserForumActivity($userId) { return collect(); }
    private function getInterestBasedThreads($user) { return collect(); }
    private function getTrendingThreads() { return collect(); }
    private function getProductSuggestions($query, $limit) { return []; }
    private function getDrawingSuggestions($query, $limit) { return []; }
    private function getMaterialSuggestions($query, $limit) { return []; }
    private function getThreadSuggestions($query, $limit) { return []; }
    private function getRelatedProducts($itemId, $limit) { return []; }
    private function getRelatedDrawings($itemId, $limit) { return []; }
    private function getRelatedMaterials($itemId, $limit) { return []; }
    private function getRelatedThreads($itemId, $limit) { return []; }
    private function getAllRecommendations($user, $limit) { return []; }
}
