<?php

namespace App\Services;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

/**
 * Advanced Notification Service - Phase 3
 * Hệ thống thông báo nâng cao với real-time và email
 */
class NotificationService
{
    /**
     * Send notification to user(s)
     *
     * @param User|array $users
     * @param string $type
     * @param string $title
     * @param string $message
     * @param array $data
     * @param bool $sendEmail
     * @return bool
     */
    public static function send($users, string $type, string $title, string $message, array $data = [], bool $sendEmail = false): bool
    {
        try {
            if (!is_array($users)) {
                $users = [$users];
            }

            foreach ($users as $user) {
                // Create database notification
                $notification = Notification::create([
                    'user_id' => $user->id,
                    'type' => $type,
                    'title' => $title,
                    'message' => $message,
                    'data' => $data,
                    'is_read' => false,
                    'priority' => $data['priority'] ?? 'normal',
                ]);

                // Cache the notification
                \App\Services\NotificationCacheService::cacheNotification($notification);

                // Increment unread count cache
                \App\Services\NotificationCacheService::incrementUnreadCount($user);

                // Invalidate user notifications cache
                \App\Services\NotificationCacheService::invalidateUserCache($user);

                // Send email if requested and user has email notifications enabled
                if ($sendEmail && $user->email_notifications_enabled) {
                    self::sendEmail($user, $notification);
                }

                // Send real-time notification (WebSocket/Pusher)
                self::sendRealtime($user, $notification);
            }

            return true;

        } catch (\Exception $e) {
            Log::error('Notification send failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send business verification notification
     */
    public static function sendBusinessVerification(User $user, bool $approved, string $reason = ''): bool
    {
        $type = $approved ? 'business_verified' : 'business_rejected';
        $title = $approved ? 'Tài khoản kinh doanh đã được xác thực' : 'Tài khoản kinh doanh bị từ chối';

        $message = $approved
            ? 'Chúc mừng! Tài khoản kinh doanh của bạn đã được xác thực thành công. Bạn có thể bắt đầu bán hàng trên marketplace.'
            : "Tài khoản kinh doanh của bạn bị từ chối. Lý do: {$reason}";

        $data = [
            'priority' => 'high',
            'action_url' => route('seller.dashboard'),
            'approved' => $approved,
            'reason' => $reason,
        ];

        return self::send($user, $type, $title, $message, $data, true);
    }

    /**
     * Send product approval notification
     */
    public static function sendProductApproval(User $seller, $product, bool $approved, string $reason = ''): bool
    {
        $type = $approved ? 'product_approved' : 'product_rejected';
        $title = $approved ? 'Sản phẩm đã được duyệt' : 'Sản phẩm bị từ chối';

        $message = $approved
            ? "Sản phẩm '{$product->name}' đã được duyệt và có thể bán trên marketplace."
            : "Sản phẩm '{$product->name}' bị từ chối. Lý do: {$reason}";

        $data = [
            'priority' => 'normal',
            'action_url' => route('seller.products.show', $product),
            'product_id' => $product->id,
            'approved' => $approved,
            'reason' => $reason,
        ];

        return self::send($seller, $type, $title, $message, $data, true);
    }

    /**
     * Send order notification
     */
    public static function sendOrderNotification(User $user, $order, string $status): bool
    {
        $statusMessages = [
            'created' => 'Đơn hàng mới đã được tạo',
            'confirmed' => 'Đơn hàng đã được xác nhận',
            'shipped' => 'Đơn hàng đã được giao cho đơn vị vận chuyển',
            'delivered' => 'Đơn hàng đã được giao thành công',
            'cancelled' => 'Đơn hàng đã bị hủy',
        ];

        $title = $statusMessages[$status] ?? 'Cập nhật đơn hàng';
        $message = "Đơn hàng #{$order->order_number} - {$title}";

        $data = [
            'priority' => in_array($status, ['created', 'delivered']) ? 'high' : 'normal',
            'action_url' => route('orders.show', $order),
            'order_id' => $order->id,
            'status' => $status,
        ];

        return self::send($user, 'order_update', $title, $message, $data, true);
    }

    /**
     * Send role change notification
     */
    public static function sendRoleChange(User $user, string $oldRole, string $newRole, string $reason): bool
    {
        $title = 'Vai trò tài khoản đã được thay đổi';
        $message = "Vai trò của bạn đã được thay đổi từ {$oldRole} thành {$newRole}. Lý do: {$reason}";

        $data = [
            'priority' => 'high',
            'action_url' => route('profile.show'),
            'old_role' => $oldRole,
            'new_role' => $newRole,
            'reason' => $reason,
        ];

        return self::send($user, 'role_changed', $title, $message, $data, true);
    }

    /**
     * Send commission payment notification
     */
    public static function sendCommissionPayment(User $seller, $commission): bool
    {
        $title = 'Hoa hồng đã được thanh toán';
        $message = "Hoa hồng {$commission->amount}đ cho tháng {$commission->period} đã được chuyển vào tài khoản của bạn.";

        $data = [
            'priority' => 'normal',
            'action_url' => route('seller.commissions.show', $commission),
            'commission_id' => $commission->id,
            'amount' => $commission->amount,
        ];

        return self::send($seller, 'commission_paid', $title, $message, $data, true);
    }

    /**
     * Send bulk notification to role group
     */
    public static function sendToRoleGroup(string $roleGroup, string $type, string $title, string $message, array $data = [], bool $sendEmail = false): bool
    {
        $users = User::where('role_group', $roleGroup)->get();

        if ($users->isEmpty()) {
            return false;
        }

        return self::send($users->toArray(), $type, $title, $message, $data, $sendEmail);
    }

    /**
     * Send system announcement
     */
    public static function sendSystemAnnouncement(string $title, string $message, array $targetRoles = [], string $priority = 'normal'): bool
    {
        $query = User::query();

        if (!empty($targetRoles)) {
            $query->whereIn('role', $targetRoles);
        }

        $users = $query->get();

        $data = [
            'priority' => $priority,
            'is_system_announcement' => true,
            'action_url' => route('announcements.index'),
        ];

        return self::send($users->toArray(), 'system_announcement', $title, $message, $data, true);
    }

    /**
     * Send email notification
     */
    private static function sendEmail(User $user, Notification $notification): void
    {
        try {
            // Send appropriate email based on notification type
            switch ($notification->type) {
                case 'thread_created':
                    if (isset($notification->data['thread_id'])) {
                        $thread = \App\Models\Thread::find($notification->data['thread_id']);
                        if ($thread) {
                            \Mail::to($user->email)->queue(new \App\Mail\ThreadCreatedNotification($thread, $user));
                        }
                    }
                    break;

                case 'thread_replied':
                    if (isset($notification->data['comment_id'])) {
                        $comment = \App\Models\Comment::find($notification->data['comment_id']);
                        if ($comment) {
                            \Mail::to($user->email)->queue(new \App\Mail\ThreadRepliedNotification($comment, $user));
                        }
                    }
                    break;

                case 'comment_mention':
                    if (isset($notification->data['comment_id'])) {
                        $comment = \App\Models\Comment::find($notification->data['comment_id']);
                        if ($comment) {
                            \Mail::to($user->email)->queue(new \App\Mail\CommentMentionNotification($comment, $user));
                        }
                    }
                    break;

                case 'login_from_new_device':
                    if (isset($notification->data['device_id'])) {
                        $device = \App\Models\UserDevice::find($notification->data['device_id']);
                        if ($device) {
                            \Mail::to($user->email)->queue(new \App\Mail\NewDeviceLoginNotification($device, $user));
                        }
                    }
                    break;

                case 'password_changed':
                    \Mail::to($user->email)->queue(new \App\Mail\PasswordChangedNotification($user, $notification->data));
                    break;

                default:
                    // For other notification types, log for now
                    Log::info("Email notification sent to {$user->email}: {$notification->title}");
                    break;
            }
        } catch (\Exception $e) {
            Log::error("Failed to send email notification: " . $e->getMessage());
        }
    }

    /**
     * Send real-time notification
     */
    private static function sendRealtime(User $user, Notification $notification): void
    {
        try {
            // TODO: Implement WebSocket/Pusher notification
            // broadcast(new NotificationEvent($user, $notification));
            Log::info("Real-time notification sent to user {$user->id}: {$notification->title}");
        } catch (\Exception $e) {
            Log::error("Failed to send real-time notification: " . $e->getMessage());
        }
    }

    /**
     * Mark notification as read
     */
    public static function markAsRead(int $notificationId, int $userId): bool
    {
        try {
            $notification = Notification::where('id', $notificationId)
                ->where('user_id', $userId)
                ->first();

            if ($notification) {
                $notification->update([
                    'is_read' => true,
                    'read_at' => now(),
                ]);
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error("Failed to mark notification as read: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mark all notifications as read for user
     */
    public static function markAllAsRead(int $userId): bool
    {
        try {
            Notification::where('user_id', $userId)
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'read_at' => now(),
                ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to mark all notifications as read: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get unread notification count for user
     */
    public static function getUnreadCount(int $userId): int
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Get recent notifications for user
     */
    public static function getRecent(int $userId, int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Clean old notifications
     */
    public static function cleanOldNotifications(int $daysOld = 30): int
    {
        return Notification::where('created_at', '<', now()->subDays($daysOld))
            ->where('is_read', true)
            ->delete();
    }

    /**
     * Get notification statistics
     */
    public static function getStats(): array
    {
        return [
            'total' => Notification::count(),
            'unread' => Notification::where('is_read', false)->count(),
            'today' => Notification::whereDate('created_at', today())->count(),
            'by_type' => Notification::select('type', \DB::raw('count(*) as count'))
                ->groupBy('type')
                ->pluck('count', 'type')
                ->toArray(),
        ];
    }

    /**
     * Send thread created notification
     */
    public static function sendThreadCreatedNotification(\App\Models\Thread $thread): bool
    {
        try {
            // Get users who are following other threads in the same forum
            $forumFollowers = \App\Models\User::whereHas('threadFollows', function ($query) use ($thread) {
                $query->whereHas('thread', function ($subQuery) use ($thread) {
                    $subQuery->where('forum_id', $thread->forum_id)
                             ->where('id', '!=', $thread->id); // Exclude the new thread itself
                });
            })
            ->where('id', '!=', $thread->user_id) // Exclude thread creator
            ->distinct()
            ->get();

            if ($forumFollowers->isEmpty()) {
                return true; // No followers to notify
            }

            $title = 'Thread mới trong forum bạn quan tâm';
            $message = "Thread '{$thread->title}' đã được tạo trong forum {$thread->forum->name}";

            $data = [
                'priority' => 'normal',
                'action_url' => route('threads.show', $thread->slug),
                'thread_id' => $thread->id,
                'forum_id' => $thread->forum_id,
                'author_id' => $thread->user_id,
            ];

            foreach ($forumFollowers as $user) {
                self::send($user, 'thread_created', $title, $message, $data, false);
            }

            return true;

        } catch (\Exception $e) {
            Log::error('Thread created notification failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send thread replied notification
     */
    public static function sendThreadRepliedNotification(\App\Models\Comment $comment): bool
    {
        try {
            $thread = $comment->thread;

            // Get users following this thread (excluding comment author)
            $followers = $thread->followers()
                ->where('users.id', '!=', $comment->user_id)
                ->get();

            if ($followers->isEmpty()) {
                return true; // No followers to notify
            }

            $title = 'Reply mới trong thread bạn theo dõi';
            $message = "{$comment->user->name} đã reply trong thread '{$thread->title}'";

            $data = [
                'priority' => 'normal',
                'action_url' => route('threads.show', $thread->slug) . '#comment-' . $comment->id,
                'thread_id' => $thread->id,
                'comment_id' => $comment->id,
                'author_id' => $comment->user_id,
            ];

            foreach ($followers as $user) {
                self::send($user, 'thread_replied', $title, $message, $data, false);
            }

            return true;

        } catch (\Exception $e) {
            Log::error('Thread replied notification failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send comment mention notification
     */
    public static function sendCommentMentionNotification(\App\Models\Comment $comment, array $mentionedUsernames): bool
    {
        try {
            if (empty($mentionedUsernames)) {
                return true; // No mentions to process
            }

            $thread = $comment->thread;

            // Get mentioned users (excluding comment author)
            $mentionedUsers = \App\Models\User::whereIn('username', $mentionedUsernames)
                ->where('id', '!=', $comment->user_id)
                ->get();

            if ($mentionedUsers->isEmpty()) {
                return true; // No valid mentioned users
            }

            $title = 'Bạn được nhắc đến trong bình luận';
            $message = "{$comment->user->name} đã nhắc đến bạn trong thread '{$thread->title}'";

            $data = [
                'priority' => 'high',
                'action_url' => route('threads.show', $thread->slug) . '#comment-' . $comment->id,
                'thread_id' => $thread->id,
                'comment_id' => $comment->id,
                'author_id' => $comment->user_id,
            ];

            foreach ($mentionedUsers as $user) {
                self::send($user, 'comment_mention', $title, $message, $data, true); // Send email for mentions
            }

            return true;

        } catch (\Exception $e) {
            Log::error('Comment mention notification failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Extract mentioned usernames from content
     */
    public static function extractMentions(string $content): array
    {
        // Use word boundary to avoid matching emails and ensure @ is at start of word
        preg_match_all('/(?<!\w)@([a-zA-Z0-9_]+)(?!\w)/', $content, $matches);
        return array_values(array_unique($matches[1] ?? []));
    }

    /**
     * Send new device login notification
     */
    public static function sendNewDeviceNotification(\App\Models\User $user, \App\Models\UserDevice $device): bool
    {
        try {
            $title = 'Đăng nhập từ thiết bị mới';
            $message = "Tài khoản của bạn đã được đăng nhập từ thiết bị mới: {$device->display_name} tại {$device->location}";

            $data = [
                'priority' => 'high',
                'action_url' => route('profile.security'),
                'device_id' => $device->id,
                'device_fingerprint' => $device->device_fingerprint,
                'device_name' => $device->display_name,
                'location' => $device->location,
                'ip_address' => $device->ip_address,
                'login_time' => $device->first_seen_at->toISOString(),
            ];

            return self::send($user, 'login_from_new_device', $title, $message, $data, true);

        } catch (\Exception $e) {
            Log::error('New device notification failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send password changed notification
     */
    public static function sendPasswordChangedNotification(\App\Models\User $user, string $ipAddress = null): bool
    {
        try {
            $title = 'Mật khẩu đã được thay đổi';
            $message = 'Mật khẩu tài khoản của bạn đã được thay đổi thành công. Nếu bạn không thực hiện thay đổi này, vui lòng liên hệ hỗ trợ ngay lập tức.';

            $data = [
                'priority' => 'high',
                'action_url' => url('/profile/security'),
                'ip_address' => $ipAddress,
                'changed_at' => now()->toISOString(),
            ];

            return self::send($user, 'password_changed', $title, $message, $data, true);

        } catch (\Exception $e) {
            Log::error('Password changed notification failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get user's notifications with pagination (optimized with cache)
     */
    public static function getUserNotifications(\App\Models\User $user, int $page = 1, int $perPage = 15)
    {
        return \App\Services\NotificationCacheService::getUserNotifications($user, $page, $perPage);
    }

    /**
     * Get user's unread notifications count (optimized with cache)
     */
    public static function getUnreadCountOptimized(\App\Models\User $user): int
    {
        return \App\Services\NotificationCacheService::getUnreadCount($user);
    }

    /**
     * Mark notifications as read in bulk
     */
    public static function markAsReadBulk(\App\Models\User $user, array $notificationIds = []): int
    {
        return \App\Models\Notification::markAsReadBulk($user, $notificationIds);
    }

    /**
     * Send bulk notifications efficiently
     */
    public static function sendBulkNotifications(array $notifications): bool
    {
        try {
            // Group notifications by type for batch processing
            $groupedNotifications = collect($notifications)->groupBy('type');

            foreach ($groupedNotifications as $type => $typeNotifications) {
                $insertData = [];
                $emailQueue = [];

                foreach ($typeNotifications as $notification) {
                    // Prepare database insert data
                    $insertData[] = [
                        'user_id' => $notification['user_id'],
                        'type' => $notification['type'],
                        'title' => $notification['title'],
                        'message' => $notification['message'],
                        'data' => json_encode($notification['data'] ?? []),
                        'priority' => $notification['priority'] ?? 'normal',
                        'is_read' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    // Prepare email queue if needed
                    if ($notification['send_email'] ?? false) {
                        $emailQueue[] = $notification;
                    }
                }

                // Bulk insert notifications
                if (!empty($insertData)) {
                    \App\Models\Notification::insert($insertData);
                }

                // Queue emails
                foreach ($emailQueue as $emailNotification) {
                    $user = \App\Models\User::find($emailNotification['user_id']);
                    if ($user) {
                        self::queueEmail($user, $emailNotification);
                    }
                }
            }

            return true;

        } catch (\Exception $e) {
            Log::error('Bulk notification sending failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Queue email for background processing
     */
    private static function queueEmail(\App\Models\User $user, array $notification): void
    {
        // Create a temporary notification object for email processing
        $tempNotification = new \App\Models\Notification($notification);
        $tempNotification->user_id = $user->id;
        $tempNotification->data = $notification['data'] ?? [];

        self::sendEmail($user, $tempNotification);
    }

    /**
     * Clean old notifications (optimized)
     */
    public static function cleanOldNotificationsOptimized(int $days = 90): int
    {
        return \App\Models\Notification::cleanOldNotifications($days);
    }
}
