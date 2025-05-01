<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PageSeo;
use App\Models\SeoSetting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SeoController extends Controller
{
    /**
     * Lấy tất cả cài đặt SEO
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $settings = SeoSetting::all()->groupBy('group');
        
        $formattedSettings = [];
        foreach ($settings as $group => $items) {
            $formattedSettings[$group] = $items->pluck('value', 'key')->toArray();
        }
        
        return response()->json([
            'success' => true,
            'data' => $formattedSettings,
            'message' => 'Lấy cài đặt SEO thành công'
        ]);
    }
    
    /**
     * Lấy cài đặt SEO theo nhóm
     *
     * @param string $group
     * @return JsonResponse
     */
    public function getByGroup(string $group): JsonResponse
    {
        $settings = SeoSetting::where('group', $group)->get();
        
        if ($settings->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy nhóm cài đặt SEO'
            ], 404);
        }
        
        $formattedSettings = $settings->pluck('value', 'key')->toArray();
        
        return response()->json([
            'success' => true,
            'data' => $formattedSettings,
            'message' => 'Lấy cài đặt SEO thành công'
        ]);
    }
    
    /**
     * Lấy cài đặt SEO cho trang theo route name
     *
     * @param string $routeName
     * @return JsonResponse
     */
    public function getPageSeoByRoute(string $routeName): JsonResponse
    {
        $pageSeo = PageSeo::where('route_name', $routeName)
            ->where('is_active', true)
            ->first();
        
        if (!$pageSeo) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy cài đặt SEO cho trang này'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $pageSeo,
            'message' => 'Lấy cài đặt SEO cho trang thành công'
        ]);
    }
    
    /**
     * Lấy cài đặt SEO cho trang theo URL pattern
     *
     * @param string $urlPattern
     * @return JsonResponse
     */
    public function getPageSeoByUrl(string $urlPattern): JsonResponse
    {
        $pageSeo = PageSeo::findByUrl($urlPattern);
        
        if (!$pageSeo) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy cài đặt SEO cho URL này'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $pageSeo,
            'message' => 'Lấy cài đặt SEO cho URL thành công'
        ]);
    }
}
