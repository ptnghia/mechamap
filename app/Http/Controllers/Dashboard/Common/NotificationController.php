<?php

namespace App\Http\Controllers\Dashboard\Common;

use App\Http\Controllers\Dashboard\BaseController;
use App\Models\Notification;
use App\Services\UnifiedNotificationManager;
use App\Services\NotificationCategoryService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * Notification Controller cho Dashboard
 *
 * Quản lý thông báo của user trong dashboard
 */
class NotificationController extends BaseController
{
    /**
     * Hiển thị danh sách notifications
     */
    public function index(Request $request)
    {
        $perPage = 20;
        $filter = $request->get('filter', 'all');
        $category = $request->get('category', 'all');
        $type = $request->get('type');
        $priority = $request->get('priority');
        $search = $request->get('search');
        $sender = $request->get('sender');
        $dateRange = $request->get('date_range');
        $archived = $request->get('archived') === '1';

        // Build query
        $query = $this->user->userNotifications()->latest();

        // Apply archive filter first
        if ($archived) {
            $query->where('status', 'archived');
        } else {
            $query->where('status', '!=', 'archived');
        }

        // Apply status filters
        if ($filter === 'unread') {
            $query->where('is_read', false);
        } elseif ($filter === 'read') {
            $query->where('is_read', true);
        } elseif ($filter === 'archived') {
            $query->where('status', 'archived');
        } elseif ($filter === 'requires_action') {
            $query->where('requires_action', true);
        }

        // Apply category filter
        if ($category && $category !== 'all') {
            $categoryTypes = NotificationCategoryService::getCategoryMapping()[$category] ?? [];
            if (!empty($categoryTypes)) {
                $query->whereIn('type', $categoryTypes);
            }
        }

        // Apply type filter
        if ($type) {
            $query->where('type', $type);
        }

        // Apply priority filter
        if ($priority) {
            $query->where('priority', $priority);
        }

        // Apply search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%")
                  ->orWhere('data', 'like', "%{$search}%");
            });
        }

        // Apply sender filter
        if ($sender) {
            $query->where(function($q) use ($sender) {
                $q->where('data', 'like', "%\"sender_name\":\"{$sender}\"%")
                  ->orWhere('data', 'like', "%\"author_name\":\"{$sender}\"%");
            });
        }

        // Apply date range filter
        if ($dateRange) {
            $dateFilter = $this->getDateRangeFilter($dateRange);
            if ($dateFilter) {
                $query->whereBetween('created_at', $dateFilter);
            }
        }

        // Get notifications with pagination
        $notifications = $query->paginate($perPage)->withQueryString();

        // Get enhanced statistics
        $stats = $this->getEnhancedNotificationStats();

        // Get category data
        $categories = NotificationCategoryService::getCategories();
        $categoryCounts = NotificationCategoryService::getCategoryCounts($this->user->id);
        $unreadCategoryCounts = NotificationCategoryService::getUnreadCategoryCounts($this->user->id);

        // Get available filters
        $filters = $this->getAvailableFilters();

        $breadcrumb = $this->getBreadcrumb([
            ['name' => 'Notifications', 'route' => 'dashboard.notifications']
        ]);

        return $this->dashboardResponse('dashboard.common.notifications.index', [
            'notifications' => $notifications,
            'stats' => $stats,
            'categories' => $categories,
            'categoryCounts' => $categoryCounts,
            'unreadCategoryCounts' => $unreadCategoryCounts,
            'archivedCount' => $stats['archived'] ?? 0,
            'filters' => $filters,
            'currentFilter' => $filter,
            'currentCategory' => $category,
            'currentType' => $type,
            'currentPriority' => $priority,
            'currentSearch' => $search,
            'currentSender' => $sender,
            'currentDateRange' => $dateRange,
            'isArchiveView' => $archived,
            'breadcrumb' => $breadcrumb
        ]);
    }

    /**
     * Đánh dấu notification đã đọc
     */
    public function markAsRead(Request $request, Notification $notification): JsonResponse
    {
        if ($notification->user_id !== $this->user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $notification->update([
            'is_read' => true,
            'read_at' => now(),
            'status' => 'read'
        ]);

        // Increment view count
        $notification->increment('view_count');

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
            'unread_count' => UnifiedNotificationManager::getUnreadCount($this->user)
        ]);
    }

    /**
     * Đánh dấu notification chưa đọc
     */
    public function markAsUnread(Request $request, Notification $notification): JsonResponse
    {
        if ($notification->user_id !== $this->user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $notification->update([
            'is_read' => false,
            'read_at' => null,
            'status' => 'sent'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as unread',
            'unread_count' => UnifiedNotificationManager::getUnreadCount($this->user)
        ]);
    }

    /**
     * Đánh dấu tất cả notifications đã đọc
     */
    public function markAllAsRead(): JsonResponse
    {
        $updated = $this->user->userNotifications()
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
                'status' => 'read'
            ]);

        return response()->json([
            'success' => true,
            'message' => "Marked {$updated} notifications as read",
            'unread_count' => 0
        ]);
    }

    /**
     * Xóa notification
     */
    public function delete(Notification $notification): JsonResponse
    {
        if ($notification->user_id !== $this->user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted',
            'unread_count' => UnifiedNotificationManager::getUnreadCount($this->user)
        ]);
    }

    /**
     * Xóa tất cả notifications
     */
    public function clearAll(): JsonResponse
    {
        $deleted = $this->user->userNotifications()->delete();

        return response()->json([
            'success' => true,
            'message' => "Cleared {$deleted} notifications",
            'unread_count' => 0
        ]);
    }

    /**
     * Archive notification
     */
    public function archive(Notification $notification): JsonResponse
    {
        if ($notification->user_id !== $this->user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $notification->update([
            'status' => 'archived',
            'archived_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notification archived'
        ]);
    }

    /**
     * Lấy thống kê notifications
     */
    private function getNotificationStats()
    {
        $total = $this->user->userNotifications()->count();
        $unread = $this->user->userNotifications()->where('is_read', false)->count();
        $read = $total - $unread;

        return [
            'total' => $total,
            'unread' => $unread,
            'read' => $read,
            'archived' => $this->user->userNotifications()->where('status', 'archived')->count(),
            'today' => $this->user->userNotifications()->whereDate('created_at', today())->count(),
            'this_week' => $this->user->userNotifications()->whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
        ];
    }

    /**
     * Lấy thống kê notifications nâng cao với category breakdown
     */
    private function getEnhancedNotificationStats()
    {
        $basic = $this->getNotificationStats();

        // Thêm category breakdown
        $categoryStats = [];
        $categories = NotificationCategoryService::getCategoryMapping();

        foreach ($categories as $category => $types) {
            $categoryStats[$category] = [
                'total' => $this->user->userNotifications()->whereIn('type', $types)->count(),
                'unread' => $this->user->userNotifications()->whereIn('type', $types)->where('is_read', false)->count(),
            ];
        }

        // Thêm priority breakdown
        $priorityStats = [];
        $priorities = ['urgent', 'high', 'normal', 'low'];

        foreach ($priorities as $priority) {
            $priorityStats[$priority] = [
                'total' => $this->user->userNotifications()->where('priority', $priority)->count(),
                'unread' => $this->user->userNotifications()->where('priority', $priority)->where('is_read', false)->count(),
            ];
        }

        return array_merge($basic, [
            'categories' => $categoryStats,
            'priorities' => $priorityStats,
        ]);
    }

    /**
     * Lấy các filter có sẵn
     */
    private function getAvailableFilters()
    {
        // Lấy types thực tế từ user notifications
        $userTypes = $this->user->userNotifications()
            ->select('type')
            ->distinct()
            ->pluck('type')
            ->filter()
            ->values();

        // Lấy priorities thực tế từ user notifications
        $userPriorities = $this->user->userNotifications()
            ->select('priority')
            ->distinct()
            ->pluck('priority')
            ->filter()
            ->values();

        // Lấy senders (từ data JSON)
        $senders = $this->user->userNotifications()
            ->whereNotNull('data')
            ->get()
            ->map(function($notification) {
                $data = json_decode($notification->data, true);
                return $data['sender_name'] ?? $data['author_name'] ?? null;
            })
            ->filter()
            ->unique()
            ->values();

        return [
            'types' => $userTypes,
            'priorities' => $userPriorities,
            'senders' => $senders,
            'status_options' => [
                'all' => __('notifications.index.status_all'),
                'unread' => __('notifications.index.status_unread'),
                'read' => __('notifications.index.status_read'),
                'archived' => __('notifications.index.archived'),
                'requires_action' => __('notifications.index.requires_action')
            ],
            'priority_options' => [
                'urgent' => __('notifications.index.priority_urgent'),
                'high' => __('notifications.index.priority_high'),
                'normal' => __('notifications.index.priority_normal'),
                'low' => __('notifications.index.priority_low')
            ],
            'date_ranges' => [
                'today' => __('notifications.index.today'),
                'yesterday' => __('notifications.index.yesterday'),
                'this_week' => __('notifications.index.this_week'),
                'last_week' => __('notifications.index.last_week'),
                'this_month' => __('notifications.index.this_month'),
                'last_month' => __('notifications.index.last_month')
            ]
        ];
    }

    /**
     * Lấy date range filter
     */
    private function getDateRangeFilter($range): ?array
    {
        $now = now();

        return match($range) {
            'today' => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            'yesterday' => [
                $now->copy()->subDay()->startOfDay(),
                $now->copy()->subDay()->endOfDay()
            ],
            'this_week' => [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()],
            'last_week' => [
                $now->copy()->subWeek()->startOfWeek(),
                $now->copy()->subWeek()->endOfWeek()
            ],
            'this_month' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            'last_month' => [
                $now->copy()->subMonth()->startOfMonth(),
                $now->copy()->subMonth()->endOfMonth()
            ],
            default => null,
        };
    }

    /**
     * Lấy unread count (AJAX)
     */
    public function unreadCount(): JsonResponse
    {
        $count = UnifiedNotificationManager::getUnreadCount($this->user);

        return response()->json([
            'success' => true,
            'unread_count' => $count
        ]);
    }

    /**
     * Lấy notifications cho dropdown (AJAX)
     */
    public function dropdown(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 10);

        $notifications = $this->user->userNotifications()
            ->latest()
            ->take($limit)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'type' => $notification->type,
                    'priority' => $notification->priority,
                    'is_read' => $notification->is_read,
                    'created_at' => $notification->created_at,
                    'action_url' => $notification->action_url,
                    'icon' => $notification->getIconAttribute(),
                ];
            });

        $unreadCount = UnifiedNotificationManager::getUnreadCount($this->user);

        return response()->json([
            'success' => true,
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }

    /**
     * Handle bulk operations on notifications
     */
    public function bulk(Request $request)
    {
        $request->validate([
            'action' => 'required|in:mark-read,archive,delete,restore',
            'notification_ids' => 'required|json'
        ]);

        $action = $request->input('action');
        $notificationIds = json_decode($request->input('notification_ids'), true);

        if (empty($notificationIds)) {
            return response()->json([
                'success' => false,
                'message' => __('notifications.index.no_notifications_selected')
            ], 400);
        }

        try {
            // Get notifications belonging to the current user
            $notifications = $this->user->userNotifications()
                ->whereIn('id', $notificationIds)
                ->get();

            if ($notifications->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid notifications found'
                ], 404);
            }

            $processedCount = 0;

            switch ($action) {
                case 'mark-read':
                    $processedCount = $notifications->where('is_read', false)->count();
                    $this->user->userNotifications()
                        ->whereIn('id', $notificationIds)
                        ->where('is_read', false)
                        ->update(['is_read' => true, 'read_at' => now()]);
                    break;

                case 'archive':
                    $processedCount = $notifications->where('status', '!=', 'archived')->count();
                    $this->user->userNotifications()
                        ->whereIn('id', $notificationIds)
                        ->where('status', '!=', 'archived')
                        ->update(['status' => 'archived', 'archived_at' => now()]);
                    break;

                case 'restore':
                    $processedCount = $notifications->where('status', 'archived')->count();
                    $this->user->userNotifications()
                        ->whereIn('id', $notificationIds)
                        ->where('status', 'archived')
                        ->update(['status' => 'sent', 'archived_at' => null]);
                    break;

                case 'delete':
                    $processedCount = $notifications->count();
                    $this->user->userNotifications()
                        ->whereIn('id', $notificationIds)
                        ->delete();
                    break;
            }

            return response()->json([
                'success' => true,
                'message' => __('notifications.index.bulk_success', ['count' => $processedCount]),
                'processed_count' => $processedCount
            ]);

        } catch (\Exception $e) {
            \Log::error('Bulk notification operation failed', [
                'user_id' => $this->user->id,
                'action' => $action,
                'notification_ids' => $notificationIds,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the request'
            ], 500);
        }
    }
}
