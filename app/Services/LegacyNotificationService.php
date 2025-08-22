<?php

namespace App\Services;

use App\Models\User;

/**
 * Legacy Notification Service Wrapper
 * @deprecated Use UnifiedNotificationService or UnifiedNotificationManager instead
 * 
 * This class provides backward compatibility by wrapping the new unified services.
 * It maintains the same interface as the old NotificationService while using
 * the new unified notification system under the hood.
 */
class LegacyNotificationService
{
    /**
     * Send notification using unified service
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
        $channels = ['database'];
        if ($sendEmail) {
            $channels[] = 'mail';
        }
        
        return UnifiedNotificationService::send($users, $type, $title, $message, $data, $channels);
    }

    /**
     * Get user notifications with pagination
     */
    public static function getUserNotifications(User $user, int $page = 1, int $perPage = 15)
    {
        return UnifiedNotificationManager::getUserNotifications($user, $page, $perPage);
    }

    /**
     * Get unread count
     */
    public static function getUnreadCountOptimized(User $user): int
    {
        return UnifiedNotificationManager::getUnreadCount($user);
    }

    /**
     * Mark notifications as read in bulk
     */
    public static function markAsReadBulk(User $user, array $notificationIds = []): int
    {
        $count = 0;
        foreach ($notificationIds as $id) {
            if (UnifiedNotificationService::markAsRead($user, $id)) {
                $count++;
            }
        }
        return $count;
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
     * Send thread notification
     */
    public static function sendThreadNotification(User $user, string $type, array $data): bool
    {
        $titles = [
            'thread_created' => 'Thread mới được tạo',
            'thread_replied' => 'Có phản hồi mới cho thread của bạn',
            'thread_liked' => 'Thread của bạn được thích',
            'thread_mentioned' => 'Bạn được nhắc đến trong một thread',
        ];

        $messages = [
            'thread_created' => 'Thread "{title}" đã được tạo trong forum {forum}',
            'thread_replied' => '{author} đã phản hồi thread "{title}" của bạn',
            'thread_liked' => '{author} đã thích thread "{title}" của bạn',
            'thread_mentioned' => '{author} đã nhắc đến bạn trong thread "{title}"',
        ];

        $title = $titles[$type] ?? 'Thông báo thread';
        $message = $messages[$type] ?? 'Có hoạt động mới liên quan đến thread';

        // Replace placeholders in message
        foreach ($data as $key => $value) {
            $message = str_replace('{' . $key . '}', $value, $message);
        }

        return self::send($user, $type, $title, $message, $data, false);
    }

    /**
     * Send marketplace notification
     */
    public static function sendMarketplaceNotification(User $user, string $type, array $data): bool
    {
        $titles = [
            'product_purchased' => 'Sản phẩm đã được mua',
            'product_sold' => 'Sản phẩm của bạn đã được bán',
            'order_status_changed' => 'Trạng thái đơn hàng đã thay đổi',
            'payment_received' => 'Đã nhận được thanh toán',
        ];

        $messages = [
            'product_purchased' => 'Bạn đã mua thành công sản phẩm "{product_name}"',
            'product_sold' => 'Sản phẩm "{product_name}" của bạn đã được bán',
            'order_status_changed' => 'Đơn hàng #{order_id} đã chuyển sang trạng thái {status}',
            'payment_received' => 'Bạn đã nhận được thanh toán {amount} cho đơn hàng #{order_id}',
        ];

        $title = $titles[$type] ?? 'Thông báo marketplace';
        $message = $messages[$type] ?? 'Có hoạt động mới trong marketplace';

        // Replace placeholders in message
        foreach ($data as $key => $value) {
            $message = str_replace('{' . $key . '}', $value, $message);
        }

        return self::send($user, $type, $title, $message, $data, true);
    }

    /**
     * Clean old notifications
     */
    public static function cleanOldNotificationsOptimized(int $days = 90): int
    {
        return \App\Models\Notification::where('created_at', '<', now()->subDays($days))->delete();
    }

    /**
     * Get notification statistics
     */
    public static function getStats(User $user): array
    {
        return UnifiedNotificationService::getStats($user);
    }
}
