<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\UnifiedNotificationManager;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Unified Notification Controller
 * Handles user notifications (custom_notifications) for all users
 * System notifications (Laravel notifications) only for super admin
 */
class UnifiedNotificationController extends Controller
{
    /**
     * Display notifications page
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $perPage = 20;
        $showSystemNotifications = $request->get('show_system', false) && $this->isSuperAdmin($user);

        // Build query with filters - only custom notifications for regular users
        if ($showSystemNotifications) {
            // Super admin viewing system notifications
            $query = $user->notifications()->latest();
            $notifications = $query->paginate($perPage);

            // Transform Laravel notifications to consistent format
            $notifications->getCollection()->transform(function ($notification) {
                $data = $notification->data;

                // Translate title if it's a translation key
                $title = $data['title'] ?? 'System Notification';
                if (str_starts_with($title, 'notifications.types.')) {
                    $title = __($title);
                }

                return (object) [
                    'id' => $notification->id,
                    'type' => $data['type'] ?? 'system',
                    'category' => 'system',
                    'title' => $title,
                    'message' => $data['message'] ?? '',
                    'priority' => $data['priority'] ?? 'normal',
                    'is_read' => $notification->read_at !== null,
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at,
                    'data' => $data['data'] ?? [],
                    'action_url' => $data['action_url'] ?? null,
                    'requires_action' => !empty($data['action_url']),
                    'getIconAttribute' => function() { return 'cog'; }
                ];
            });
        } else {
            // Regular user notifications (custom_notifications)
            $query = $user->userNotifications()->latest();
        }

        if (!$showSystemNotifications) {
            // Apply filters only for custom notifications
            if ($request->filled('category')) {
                $query->where('category', $request->category);
            }

            if ($request->filled('type')) {
                $query->where('type', $request->type);
            }

            if ($request->filled('status')) {
                switch ($request->status) {
                    case 'unread':
                        $query->where('is_read', false);
                        break;
                    case 'read':
                        $query->where('is_read', true);
                        break;
                    case 'archived':
                        $query->where('status', 'archived');
                        break;
                }
            }

            if ($request->filled('priority')) {
                $query->where('priority', $request->priority);
            }

            // Search functionality
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('message', 'like', "%{$search}%");
                });
            }

            $notifications = $query->paginate($perPage);

            // Transform custom notifications to translate titles
            $notifications->getCollection()->transform(function ($notification) {
                // Translate title if it's a translation key
                $title = $notification->title;
                if (str_starts_with($title, 'notifications.types.')) {
                    $title = __($title);
                }

                // Update the title in the notification object
                $notification->title = $title;

                return $notification;
            });
        }

        // Get statistics
        $stats = $this->getNotificationStats($user, $showSystemNotifications);

        // Get available filters
        $filters = $this->getAvailableFilters($user, $showSystemNotifications);

        return view('notifications.unified-index', compact('notifications', 'stats', 'filters', 'showSystemNotifications'));
    }

    /**
     * Get notifications for dropdown (AJAX)
     */
    public function dropdown(Request $request): JsonResponse
    {
        $user = Auth::user();
        $limit = $request->get('limit', 10);

        $notifications = $user->userNotifications()
            ->where('is_read', false)
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function ($notification) {
                // Translate title if it's a translation key
                $title = $notification->title;
                if (str_starts_with($title, 'notifications.types.')) {
                    $title = __($title);
                }

                // Translate notification type for display
                $typeLabel = $this->getNotificationTypeLabel($notification->type);

                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'type_label' => $typeLabel,
                    'category' => $notification->category,
                    'title' => $title,
                    'message' => \Str::limit($notification->message, 100),
                    'icon' => $notification->getIconAttribute(),
                    'color' => $this->getCategoryColor($notification->category),
                    'action_url' => $notification->action_url,
                    'action_text' => $notification->action_text,
                    'requires_action' => $notification->requires_action,
                    'priority' => $notification->priority,
                    'created_at' => $notification->created_at->diffForHumans(),
                    'created_at_iso' => $notification->created_at->toISOString(),
                ];
            });

        $unreadCount = UnifiedNotificationManager::getUnreadCount($user);

        return response()->json([
            'success' => true,
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
            'has_more' => $notifications->count() >= $limit,
        ]);
    }

    /**
     * Get unread count (AJAX)
     */
    public function unreadCount(): JsonResponse
    {
        $user = Auth::user();
        $count = UnifiedNotificationManager::getUnreadCount($user);

        return response()->json([
            'success' => true,
            'unread_count' => $count
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request, Notification $notification): JsonResponse
    {
        if ($notification->user_id !== Auth::id()) {
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
            'unread_count' => UnifiedNotificationManager::getUnreadCount(Auth::user())
        ]);
    }

    /**
     * Mark notification as unread
     */
    public function markAsUnread(Request $request, Notification $notification): JsonResponse
    {
        if ($notification->user_id !== Auth::id()) {
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
            'unread_count' => UnifiedNotificationManager::getUnreadCount(Auth::user())
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(): JsonResponse
    {
        $user = Auth::user();

        $updated = $user->userNotifications()
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
     * Delete notification
     */
    public function delete(Notification $notification): JsonResponse
    {
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted',
            'unread_count' => UnifiedNotificationManager::getUnreadCount(Auth::user())
        ]);
    }

    /**
     * Clear all notifications
     */
    public function clearAll(): JsonResponse
    {
        $user = Auth::user();

        $deleted = $user->userNotifications()->delete();

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
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $notification->update(['status' => 'archived']);

        return response()->json([
            'success' => true,
            'message' => 'Notification archived'
        ]);
    }

    /**
     * Track notification interaction
     */
    public function trackInteraction(Request $request, Notification $notification): JsonResponse
    {
        if ($notification->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $action = $request->get('action', 'click');

        // Increment appropriate counter
        switch ($action) {
            case 'click':
                $notification->increment('click_count');
                break;
            case 'view':
                $notification->increment('view_count');
                break;
        }

        // Update interaction data
        $interactionData = $notification->interaction_data ?? [];
        $interactionData[] = [
            'action' => $action,
            'timestamp' => now()->toISOString(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ];

        $notification->update(['interaction_data' => $interactionData]);

        return response()->json(['success' => true]);
    }

    /**
     * Get notification statistics
     */
    private function getNotificationStats($user, bool $showSystemNotifications = false): array
    {
        if ($showSystemNotifications && $this->isSuperAdmin($user)) {
            // System notifications stats
            return [
                'total' => $user->notifications()->count(),
                'unread' => $user->unreadNotifications()->count(),
                'read' => $user->readNotifications()->count(),
                'archived' => 0, // Laravel notifications don't have archived status
                'by_category' => ['system' => $user->notifications()->count()],
                'by_priority' => [], // Would need to parse from data field
            ];
        }

        // Regular user notifications stats
        return [
            'total' => $user->userNotifications()->count(),
            'unread' => $user->userNotifications()->where('is_read', false)->count(),
            'read' => $user->userNotifications()->where('is_read', true)->count(),
            'archived' => $user->userNotifications()->where('status', 'archived')->count(),
            'by_category' => $user->userNotifications()
                ->select('category', DB::raw('count(*) as count'))
                ->groupBy('category')
                ->pluck('count', 'category')
                ->toArray(),
            'by_priority' => $user->userNotifications()
                ->select('priority', DB::raw('count(*) as count'))
                ->groupBy('priority')
                ->pluck('count', 'priority')
                ->toArray(),
        ];
    }

    /**
     * Get available filters
     */
    private function getAvailableFilters($user, bool $showSystemNotifications = false): array
    {
        if ($showSystemNotifications && $this->isSuperAdmin($user)) {
            // System notifications filters
            return [
                'categories' => ['system' => 'System'],
                'types' => [], // Would need to parse from Laravel notifications
                'priorities' => ['low', 'normal', 'high', 'urgent'],
            ];
        }

        // Regular user notifications filters
        return [
            'categories' => $user->userNotifications()
                ->select('category')
                ->distinct()
                ->pluck('category')
                ->mapWithKeys(fn($cat) => [$cat => UnifiedNotificationManager::CATEGORIES[$cat] ?? $cat])
                ->toArray(),
            'types' => $user->userNotifications()
                ->select('type')
                ->distinct()
                ->pluck('type')
                ->toArray(),
            'priorities' => ['low', 'normal', 'high', 'urgent'],
        ];
    }

    /**
     * Check if user is super admin
     */
    private function isSuperAdmin($user): bool
    {
        return $user->role === 'super_admin' ||
               (method_exists($user, 'hasRole') && $user->hasRole('super_admin'));
    }

    /**
     * Get category color
     */
    private function getCategoryColor(string $category): string
    {
        return match($category) {
            'system' => 'blue',
            'forum' => 'green',
            'marketplace' => 'orange',
            'social' => 'purple',
            'security' => 'red',
            default => 'gray'
        };
    }

    /**
     * Get translated notification type label
     */
    private function getNotificationTypeLabel(string $type): string
    {
        // Convert type to lowercase and replace underscores with underscores for translation key
        $translationKey = 'notifications.types.' . strtolower($type);

        // Try to get translation
        $translation = __($translationKey);

        // If translation found, return it
        if ($translation !== $translationKey) {
            return $translation;
        }

        // Fallback: convert type to readable format
        return str_replace('_', ' ', ucwords(strtolower($type), '_'));
    }
}
