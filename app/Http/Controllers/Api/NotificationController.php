<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Lấy danh sách tất cả thông báo của user
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            // Lấy thông báo với phân trang
            $notifications = $user->notifications()
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $notifications,
                'message' => 'Lấy danh sách thông báo thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy thông báo',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Lấy danh sách thông báo chưa đọc
     */
    public function getUnread(): JsonResponse
    {
        try {
            $user = Auth::user();

            $unreadNotifications = $user->unreadNotifications()
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $unreadNotifications,
                'count' => $unreadNotifications->count(),
                'message' => 'Lấy thông báo chưa đọc thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy thông báo chưa đọc',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Đánh dấu thông báo đã đọc
     */
    public function markAsRead(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'notification_id' => 'required|string|uuid'
            ]);

            $user = Auth::user();
            $notification = $user->notifications()
                ->where('id', $request->notification_id)
                ->first();

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy thông báo'
                ], 404);
            }

            $notification->markAsRead();

            return response()->json([
                'success' => true,
                'message' => 'Đánh dấu đã đọc thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi đánh dấu thông báo',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Đánh dấu tất cả thông báo đã đọc
     */
    public function markAllAsRead(): JsonResponse
    {
        try {
            $user = Auth::user();
            $user->unreadNotifications->markAsRead();

            return response()->json([
                'success' => true,
                'message' => 'Đánh dấu tất cả thông báo đã đọc thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi đánh dấu tất cả thông báo',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $user = Auth::user();
            $notification = $user->notifications()
                ->where('id', $id)
                ->first();

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy thông báo'
                ], 404);
            }

            $notification->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa thông báo thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa thông báo',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
