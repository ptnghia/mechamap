<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SidebarDataService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SidebarController extends Controller
{
    private SidebarDataService $sidebarService;

    public function __construct(SidebarDataService $sidebarService)
    {
        $this->sidebarService = $sidebarService;
    }

    /**
     * Get real-time sidebar stats
     */
    public function getStats(): JsonResponse
    {
        try {
            $data = $this->sidebarService->getSidebarData(auth()->user());

            return response()->json([
                'success' => true,
                'stats' => [
                    $data['community_stats']['total_threads'],
                    $data['community_stats']['verified_users'],
                    $data['community_stats']['active_users_week'],
                    $data['community_stats']['growth_rate'],
                ],
                'timestamp' => now()->toISOString(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to fetch stats',
            ], 500);
        }
    }

    /**
     * Get trending topics
     */
    public function getTrendingTopics(): JsonResponse
    {
        try {
            $data = $this->sidebarService->getSidebarData(auth()->user());

            return response()->json([
                'success' => true,
                'data' => $data['trending_topics'],
                'timestamp' => now()->toISOString(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to fetch trending topics',
            ], 500);
        }
    }

    /**
     * Get personalized recommendations
     */
    public function getRecommendations(Request $request): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required',
            ], 401);
        }

        try {
            $data = $this->sidebarService->getSidebarData(auth()->user());

            return response()->json([
                'success' => true,
                'data' => $data['user_recommendations'] ?? [],
                'timestamp' => now()->toISOString(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to fetch recommendations',
            ], 500);
        }
    }

    /**
     * Get complete sidebar data with caching
     */
    public function getSidebarData(): JsonResponse
    {
        try {
            $data = $this->sidebarService->getSidebarData(auth()->user());

            return response()->json([
                'success' => true,
                'data' => $data,
                'cached_until' => now()->addMinutes(5)->toISOString(),
                'timestamp' => now()->toISOString(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to fetch sidebar data',
            ], 500);
        }
    }

    /**
     * Track sidebar interactions for analytics
     */
    public function trackInteraction(Request $request): JsonResponse
    {
        $request->validate([
            'action' => 'required|string',
            'section' => 'required|string',
            'target' => 'nullable|string',
            'metadata' => 'nullable|array',
        ]);

        try {
            // Log interaction for analytics
            \Log::info('Sidebar interaction', [
                'user_id' => auth()->id(),
                'action' => $request->action,
                'section' => $request->section,
                'target' => $request->target,
                'metadata' => $request->metadata,
                'timestamp' => now(),
                'user_agent' => $request->userAgent(),
                'ip' => $request->ip(),
            ]);

            // You can also send to analytics service here
            // event(new SidebarInteractionEvent($request->all()));

            return response()->json([
                'success' => true,
                'message' => 'Interaction tracked',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to track interaction',
            ], 500);
        }
    }
}
