<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Admin Notification Controller
 * Quản lý thông báo trong admin panel
 */
class NotificationController extends Controller
{
    /**
     * Display notifications index page
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            // Fallback for unauthenticated users
            $notifications = collect();
            $stats = [
                'total' => 0,
                'unread' => 0,
                'read' => 0,
                'today' => 0,
            ];
            return view('admin.notifications.index', compact('notifications', 'stats'));
        }

        // Get notifications with pagination
        $notifications = $user->userNotifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Get unread count
        $unreadCount = $user->unreadNotifications()->count();

        // Get notification stats
        $stats = [
            'total' => $user->userNotifications()->count(),
            'unread' => $unreadCount,
            'read' => $user->userNotifications()->where('is_read', true)->count(),
            'today' => $user->userNotifications()->whereDate('created_at', today())->count(),
        ];

        return view('admin.notifications.index', compact('notifications', 'stats'));
    }

    /**
     * Show specific notification
     */
    public function show($id)
    {
        $user = Auth::user();
        $notification = $user->userNotifications()->findOrFail($id);

        // Mark as read if not already read
        if (!$notification->is_read) {
            $notification->markAsRead();
        }

        // If notification has action URL, redirect there
        if ($notification->hasActionUrl()) {
            return redirect($notification->data['action_url']);
        }

        return view('admin.notifications.show', compact('notification'));
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $user = Auth::user();
        $notification = $user->userNotifications()->findOrFail($id);

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read'
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $user = Auth::user();

        $updated = $user->unreadNotifications()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        Log::info('Admin marked all notifications as read', [
            'admin_id' => $user->id,
            'updated_count' => $updated
        ]);

        return response()->json([
            'success' => true,
            'message' => "Đã đánh dấu {$updated} thông báo là đã đọc",
            'updated_count' => $updated
        ]);
    }

    /**
     * Delete notification
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $notification = $user->userNotifications()->findOrFail($id);

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted successfully'
        ]);
    }

    /**
     * Get unread notifications count (API endpoint)
     */
    public function getUnreadCount()
    {
        $user = Auth::user();
        $count = $user->unreadNotifications()->count();

        return response()->json([
            'count' => $count,
            'formatted' => $count > 99 ? '99+' : $count
        ]);
    }

    /**
     * Get recent notifications for header dropdown (API endpoint)
     */
    public function getRecent(Request $request)
    {
        $user = Auth::user();
        $limit = $request->get('limit', 5);

        $notifications = $user->unreadNotifications()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => \Str::limit($notification->message, 50),
                    'icon' => $notification->icon,
                    'color' => $notification->color,
                    'time_ago' => $notification->time_ago,
                    'action_url' => $notification->hasActionUrl() ? $notification->data['action_url'] : null,
                ];
            });

        return response()->json([
            'notifications' => $notifications,
            'count' => $notifications->count(),
            'total_unread' => $user->unreadNotifications()->count()
        ]);
    }

    /**
     * Test notification creation (for development)
     */
    public function createTest()
    {
        $user = Auth::user();

        $testNotifications = [
            [
                'type' => 'user_registered',
                'title' => 'Người dùng mới đăng ký',
                'message' => 'Có người dùng mới vừa đăng ký tài khoản và cần phê duyệt.',
                'priority' => 'normal',
                'data' => ['action_url' => route('admin.users.index')]
            ],
            [
                'type' => 'system_announcement',
                'title' => 'Cập nhật hệ thống',
                'message' => 'Hệ thống sẽ được bảo trì vào 2:00 AM ngày mai.',
                'priority' => 'high',
                'data' => []
            ],
            [
                'type' => 'forum_activity',
                'title' => 'Hoạt động diễn đàn',
                'message' => 'Có 5 bài đăng mới cần kiểm duyệt trong diễn đàn.',
                'priority' => 'normal',
                'data' => ['action_url' => route('admin.threads.index')]
            ]
        ];

        foreach ($testNotifications as $notificationData) {
            Notification::create([
                'user_id' => $user->id,
                'type' => $notificationData['type'],
                'title' => $notificationData['title'],
                'message' => $notificationData['message'],
                'priority' => $notificationData['priority'],
                'data' => $notificationData['data'],
                'is_read' => false,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Created ' . count($testNotifications) . ' test notifications'
        ]);
    }
}
