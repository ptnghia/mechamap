<?php

namespace App\Http\Controllers;

use App\Models\UserDevice;
use App\Services\DeviceDetectionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DeviceController extends Controller
{
    /**
     * Display device management page
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get user's devices
        $devices = DeviceDetectionService::getUserDevices($user);
        
        // Get current device fingerprint
        $currentFingerprint = DeviceDetectionService::generateDeviceFingerprint($request);
        
        // Get device statistics
        $stats = [
            'total' => $devices->count(),
            'trusted' => $devices->where('is_trusted', true)->count(),
            'untrusted' => $devices->where('is_trusted', false)->count(),
            'active_last_30_days' => $devices->where('last_seen_at', '>=', now()->subDays(30))->count(),
        ];
        
        return view('devices.index', compact('devices', 'currentFingerprint', 'stats'));
    }

    /**
     * Trust a device (AJAX)
     */
    public function trust(Request $request, UserDevice $device): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if ($device->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            $device->update(['is_trusted' => true]);

            Log::info('Device trusted', [
                'user_id' => $user->id,
                'device_id' => $device->id,
                'device_fingerprint' => $device->device_fingerprint
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Thiết bị đã được đánh dấu là tin cậy'
            ]);

        } catch (\Exception $e) {
            Log::error('Trust device failed', [
                'user_id' => Auth::id(),
                'device_id' => $device->id ?? null,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra'
            ], 500);
        }
    }

    /**
     * Untrust a device (AJAX)
     */
    public function untrust(Request $request, UserDevice $device): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if ($device->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            $device->update(['is_trusted' => false]);

            Log::info('Device untrusted', [
                'user_id' => $user->id,
                'device_id' => $device->id,
                'device_fingerprint' => $device->device_fingerprint
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Thiết bị đã được bỏ đánh dấu tin cậy'
            ]);

        } catch (\Exception $e) {
            Log::error('Untrust device failed', [
                'user_id' => Auth::id(),
                'device_id' => $device->id ?? null,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra'
            ], 500);
        }
    }

    /**
     * Remove a device (AJAX)
     */
    public function remove(Request $request, UserDevice $device): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if ($device->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            // Don't allow removing current device
            $currentFingerprint = DeviceDetectionService::generateDeviceFingerprint($request);
            if ($device->device_fingerprint === $currentFingerprint) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể xóa thiết bị hiện tại'
                ]);
            }

            $deviceName = $device->device_name;
            $device->delete();

            Log::info('Device removed', [
                'user_id' => $user->id,
                'device_id' => $device->id,
                'device_name' => $deviceName,
                'device_fingerprint' => $device->device_fingerprint
            ]);

            return response()->json([
                'success' => true,
                'message' => "Đã xóa thiết bị \"{$deviceName}\""
            ]);

        } catch (\Exception $e) {
            Log::error('Remove device failed', [
                'user_id' => Auth::id(),
                'device_id' => $device->id ?? null,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa thiết bị'
            ], 500);
        }
    }

    /**
     * Get device details (AJAX)
     */
    public function show(Request $request, UserDevice $device): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if ($device->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'device' => [
                    'id' => $device->id,
                    'device_name' => $device->device_name,
                    'device_type' => $device->device_type,
                    'browser' => $device->browser,
                    'platform' => $device->platform,
                    'ip_address' => $device->ip_address,
                    'country' => $device->country,
                    'city' => $device->city,
                    'is_trusted' => $device->is_trusted,
                    'first_seen_at' => $device->first_seen_at?->format('d/m/Y H:i'),
                    'last_seen_at' => $device->last_seen_at?->format('d/m/Y H:i'),
                    'last_seen_human' => $device->last_seen_at?->diffForHumans(),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Get device details failed', [
                'user_id' => Auth::id(),
                'device_id' => $device->id ?? null,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra'
            ], 500);
        }
    }

    /**
     * Clean old untrusted devices (AJAX)
     */
    public function cleanOld(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $days = $request->get('days', 90);
            
            $deletedCount = UserDevice::where('user_id', $user->id)
                ->where('is_trusted', false)
                ->where('last_seen_at', '<', now()->subDays($days))
                ->delete();

            Log::info('Old devices cleaned', [
                'user_id' => $user->id,
                'deleted_count' => $deletedCount,
                'days' => $days
            ]);

            return response()->json([
                'success' => true,
                'message' => "Đã xóa {$deletedCount} thiết bị cũ",
                'deleted_count' => $deletedCount
            ]);

        } catch (\Exception $e) {
            Log::error('Clean old devices failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi dọn dẹp thiết bị cũ'
            ], 500);
        }
    }
}
