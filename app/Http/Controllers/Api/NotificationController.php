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

            // Lấy thông báo với phân trang từ custom system
            $notifications = $user->userNotifications()
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

            $unreadNotifications = $user->userNotifications()
                ->where('is_read', false)
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
                'notification_id' => 'required|integer'
            ]);

            $user = Auth::user();
            $notification = $user->userNotifications()
                ->where('id', $request->notification_id)
                ->first();

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy thông báo'
                ], 404);
            }

            $notification->is_read = true;
            $notification->read_at = now();
            $notification->save();

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
            $user->userNotifications()
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'read_at' => now()
                ]);

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
            $notification = $user->userNotifications()
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

    /**
     * Lấy thông báo gần đây cho header dropdown
     */
    public function getRecent(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $limit = $request->get('limit', 5);

            // Sử dụng custom notification system để tránh table structure conflict
            $notifications = $user->userNotifications()
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($notification) {
                    $data = is_array($notification->data) ? $notification->data : json_decode($notification->data ?? '{}', true);
                    return [
                        'id' => $notification->id,
                        'title' => $notification->title ?? $data['title'] ?? 'Thông báo',
                        'message' => \Str::limit($notification->message ?? $data['message'] ?? $notification->type, 50),
                        'icon' => $data['icon'] ?? 'fas fa-bell',
                        'color' => $data['color'] ?? 'primary',
                        'time_ago' => $notification->created_at->diffForHumans(),
                        'is_read' => $notification->is_read,
                        'action_url' => $data['action_url'] ?? null,
                        'created_at' => $notification->created_at,
                    ];
                });

            // Đếm tổng số chưa đọc từ custom notifications
            $totalUnread = $user->userNotifications()->where('is_read', false)->count();

            return response()->json([
                'success' => true,
                'notifications' => $notifications,
                'total_unread' => $totalUnread,
                'message' => 'Lấy thông báo gần đây thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'notifications' => [],
                'total_unread' => 0,
                'message' => 'Có lỗi xảy ra khi lấy thông báo gần đây',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get alert color based on type
     */
    private function getAlertColor(string $type): string
    {
        return match ($type) {
            'success' => 'success',
            'warning' => 'warning',
            'error' => 'danger',
            'system_update' => 'primary',
            default => 'secondary',
        };
    }
}
