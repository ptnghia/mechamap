<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class AvatarController extends Controller
{
    /**
     * Tạo avatar dựa trên ký tự đầu tiên của tên
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generate(Request $request)
    {
        // Lấy tham số từ request
        $name = $request->input('name', 'A');
        $size = $request->input('size', '100');
        $background = $request->input('background', '7f7757'); // Màu chủ đạo của MechaMap
        $color = $request->input('color', 'ffffff');

        // Lấy ký tự đầu tiên của tên
        $firstChar = strtoupper(substr($name, 0, 1));

        // Tạo cache key
        $cacheKey = "avatar_{$firstChar}_{$size}_{$background}_{$color}";

        // Kiểm tra cache
        if (Cache::has($cacheKey)) {
            $imageData = Cache::get($cacheKey);
        } else {
            // Tạo URL cho UI Avatars
            $avatarUrl = "https://ui-avatars.com/api/?name={$firstChar}&size={$size}&background={$background}&color={$color}";

            try {
                // Fetch avatar từ UI Avatars
                $response = Http::get($avatarUrl);

                if ($response->successful()) {
                    $imageData = $response->body();
                    
                    // Lưu vào cache trong 30 ngày
                    Cache::put($cacheKey, $imageData, now()->addDays(30));
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to generate avatar',
                    ], 500);
                }
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error generating avatar: ' . $e->getMessage(),
                ], 500);
            }
        }

        // Trả về hình ảnh với header phù hợp
        return response($imageData)
            ->header('Content-Type', 'image/png')
            ->header('Cache-Control', 'public, max-age=2592000'); // Cache 30 ngày
    }
}
