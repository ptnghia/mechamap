<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FaviconController extends Controller
{
    /**
     * Lấy favicon URL
     *
     * @return JsonResponse
     */
    public function getFavicon(): JsonResponse
    {
        $favicon = Setting::where('key', 'site_favicon')->first();
        
        if (!$favicon) {
            return response()->json([
                'success' => false,
                'message' => 'Favicon không tồn tại'
            ], 404);
        }
        
        // Lấy domain từ request
        $domain = request()->getSchemeAndHttpHost();
        
        // Xử lý đường dẫn favicon
        $faviconUrl = $favicon->value;
        if (!str_starts_with($faviconUrl, 'http')) {
            // Đảm bảo không có dấu / trùng lặp
            if (str_starts_with($faviconUrl, '/') && str_ends_with($domain, '/')) {
                $faviconUrl = $domain . substr($faviconUrl, 1);
            } else if (!str_starts_with($faviconUrl, '/') && !str_ends_with($domain, '/')) {
                $faviconUrl = $domain . '/' . $faviconUrl;
            } else {
                $faviconUrl = $domain . $faviconUrl;
            }
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'favicon' => $faviconUrl
            ],
            'message' => 'Lấy favicon thành công'
        ]);
    }
}
