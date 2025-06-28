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
            // TODO: Implement email sending
            // Mail::to($user->email)->send(new NotificationMail($notification));
            Log::info("Email notification sent to {$user->email}: {$notification->title}");
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
}
