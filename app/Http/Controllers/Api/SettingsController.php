<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SettingsController extends Controller
{
    /**
     * Lấy tất cả cài đặt
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $settings = Setting::all()->groupBy('group');
        
        $formattedSettings = [];
        foreach ($settings as $group => $items) {
            $formattedSettings[$group] = $items->pluck('value', 'key')->toArray();
        }
        
        return response()->json([
            'success' => true,
            'data' => $formattedSettings,
            'message' => 'Lấy cài đặt thành công'
        ]);
    }
    
    /**
     * Lấy cài đặt theo nhóm
     *
     * @param string $group
     * @return JsonResponse
     */
    public function getByGroup(string $group): JsonResponse
    {
        $settings = Setting::where('group', $group)->get();
        
        if ($settings->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy nhóm cài đặt'
            ], 404);
        }
        
        $formattedSettings = $settings->pluck('value', 'key')->toArray();
        
        return response()->json([
            'success' => true,
            'data' => $formattedSettings,
            'message' => 'Lấy cài đặt thành công'
        ]);
    }
}
