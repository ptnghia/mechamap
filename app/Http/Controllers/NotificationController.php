<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\NotificationService;
use App\Services\NotificationCacheService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /**
     * Display notification management page
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get filter parameters
        $type = $request->get('type');
        $status = $request->get('status'); // 'read', 'unread', 'all'
        $perPage = $request->get('per_page', 20);

        // Build query
        $query = Notification::where('user_id', $user->id);

        // Apply filters
        if ($type && $type !== 'all') {
            $query->where('type', $type);
        }

        if ($status === 'read') {
            $query->where('is_read', true);
        } elseif ($status === 'unread') {
            $query->where('is_read', false);
        }

        // Get notifications with pagination
        $notifications = $query->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        // Get notification types for filter
        $notificationTypes = Notification::where('user_id', $user->id)
            ->select('type')
            ->distinct()
            ->pluck('type')
            ->map(function ($type) {
                return [
                    'value' => $type,
                    'label' => $this->getNotificationTypeLabel($type),
                    'icon' => $this->getNotificationIcon($type),
                    'color' => $this->getNotificationColor($type)
                ];
            })
            ->sortBy('label');

        // Get statistics
        $stats = [
            'total' => Notification::where('user_id', $user->id)->count(),
            'unread' => Notification::where('user_id', $user->id)->where('is_read', false)->count(),
            'read' => Notification::where('user_id', $user->id)->where('is_read', true)->count(),
        ];

        return view('notifications.index', compact(
            'notifications',
            'notificationTypes',
            'stats',
            'type',
            'status'
        ));
    }

    /**
     * Get user's notifications for dropdown (AJAX)
     */
    public function dropdown(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            // Get recent notifications (last 10)
            $notifications = NotificationCacheService::getUserNotifications($user, 1, 10);

            // Get unread count
            $unreadCount = NotificationCacheService::getUnreadCount($user);

            // Format notifications for frontend
            $formattedNotifications = collect($notifications)->map(function ($notification) {
                return [
                    'id' => $notification['id'],
                    'type' => $notification['type'],
                    'title' => $notification['title'],
                    'message' => $notification['message'],
                    'is_read' => $notification['is_read'],
                    'created_at' => $notification['created_at'],
                    'time_ago' => \Carbon\Carbon::parse($notification['created_at'])->diffForHumans(),
                    'icon' => $this->getNotificationIcon($notification['type']),
                    'color' => $this->getNotificationColor($notification['type']),
                    'action_url' => $notification['data']['action_url'] ?? null,
                ];
            });

            return response()->json([
                'success' => true,
                'notifications' => $formattedNotifications,
                'unread_count' => $unreadCount,
                'has_more' => count($notifications) >= 10
            ]);

        } catch (\Exception $e) {
            Log::error('Notification dropdown failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải thông báo'
            ], 500);
        }
    }

    /**
     * Get unread notification count (AJAX)
     */
    public function unreadCount(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => true,
                    'unread_count' => 0
                ]);
            }

            $unreadCount = NotificationCacheService::getUnreadCount($user);

            return response()->json([
                'success' => true,
                'unread_count' => $unreadCount
            ]);

        } catch (\Exception $e) {
            Log::error('Get unread count failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'unread_count' => 0
            ]);
        }
    }

    /**
     * Mark notification as read (AJAX)
     */
    public function markAsRead(Request $request, Notification $notification): JsonResponse
    {
        try {
            $user = Auth::user();

            if (!$user || $notification->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            if (!$notification->is_read) {
                $notification->update([
                    'is_read' => true,
                    'read_at' => now()
                ]);

                // Update cache
                NotificationCacheService::decrementUnreadCount($user);
                NotificationCacheService::invalidateUserCache($user);
            }

            return response()->json([
                'success' => true,
                'message' => 'Đã đánh dấu là đã đọc'
            ]);

        } catch (\Exception $e) {
            Log::error('Mark notification as read failed', [
                'user_id' => Auth::id(),
                'notification_id' => $notification->id ?? null,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra'
            ], 500);
        }
    }

    /**
     * Mark all notifications as read (AJAX)
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            $markedCount = NotificationService::markAsReadBulk($user);

            // Update cache
            NotificationCacheService::invalidateUserCache($user);

            return response()->json([
                'success' => true,
                'message' => "Đã đánh dấu {$markedCount} thông báo là đã đọc",
                'marked_count' => $markedCount
            ]);

        } catch (\Exception $e) {
            Log::error('Mark all notifications as read failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra'
            ], 500);
        }
    }

    /**
     * Delete notification (AJAX)
     */
    public function delete(Request $request, Notification $notification): JsonResponse
    {
        try {
            $user = Auth::user();

            if (!$user || $notification->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            $notification->delete();

            // Update cache
            if (!$notification->is_read) {
                NotificationCacheService::decrementUnreadCount($user);
            }
            NotificationCacheService::invalidateUserCache($user);

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa thông báo'
            ]);

        } catch (\Exception $e) {
            Log::error('Delete notification failed', [
                'user_id' => Auth::id(),
                'notification_id' => $notification->id ?? null,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa thông báo'
            ], 500);
        }
    }

    /**
     * Get notification icon based on type
     */
    private function getNotificationIcon(string $type): string
    {
        return match($type) {
            'thread_created' => 'fas fa-plus-circle',
            'thread_replied' => 'fas fa-reply',
            'comment_mention' => 'fas fa-at',
            'login_from_new_device' => 'fas fa-shield-alt',
            'password_changed' => 'fas fa-key',
            'product_out_of_stock' => 'fas fa-exclamation-triangle',
            'price_drop_alert' => 'fas fa-arrow-down',
            'wishlist_available' => 'fas fa-heart',
            'review_received' => 'fas fa-star',
            'seller_message' => 'fas fa-envelope',
            'user_followed' => 'fas fa-user-plus',
            'achievement_unlocked' => 'fas fa-trophy',
            'weekly_digest' => 'fas fa-newspaper',
            default => 'fas fa-bell'
        };
    }

    /**
     * Get notification color based on type
     */
    private function getNotificationColor(string $type): string
    {
        return match($type) {
            'thread_created' => 'success',
            'thread_replied' => 'info',
            'comment_mention' => 'warning',
            'login_from_new_device' => 'warning',
            'password_changed' => 'danger',
            'product_out_of_stock' => 'danger',
            'price_drop_alert' => 'success',
            'wishlist_available' => 'primary',
            'review_received' => 'warning',
            'seller_message' => 'info',
            'user_followed' => 'primary',
            'achievement_unlocked' => 'warning',
            'weekly_digest' => 'secondary',
            default => 'primary'
        };
    }

    /**
     * Get notification type label
     */
    private function getNotificationTypeLabel(string $type): string
    {
        return match($type) {
            'thread_created' => 'Thread mới',
            'thread_replied' => 'Reply thread',
            'comment_mention' => 'Được nhắc đến',
            'login_from_new_device' => 'Đăng nhập thiết bị mới',
            'password_changed' => 'Đổi mật khẩu',
            'product_out_of_stock' => 'Hết hàng',
            'price_drop_alert' => 'Giảm giá',
            'wishlist_available' => 'Wishlist có hàng',
            'review_received' => 'Nhận đánh giá',
            'seller_message' => 'Tin nhắn seller',
            'user_followed' => 'Được theo dõi',
            'achievement_unlocked' => 'Thành tựu mới',
            'weekly_digest' => 'Tổng hợp tuần',
            default => ucfirst(str_replace('_', ' ', $type))
        };
    }
}
