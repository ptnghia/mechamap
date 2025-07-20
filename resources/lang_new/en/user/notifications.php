<?php

/**
 * English translations for user/notifications
 * User notifications and alerts
 * 
 * Structure: user.notifications.*
 * Populated: 2025-07-20 02:39:45
 * Keys: 86
 */

return [
    'default' => [
        'title' => 'New Notification',
        'message' => 'You have a new notification.',
    ],
    'thread_created' => [
        'title' => 'New Thread in Forum',
        'message' => ':user_name created a new thread \":thread_title\" in :forum_name forum.',
    ],
    'thread_replied' => [
        'title' => 'New Reply',
        'message' => ':user_name replied to thread \":thread_title\".',
    ],
    'comment_mentioned' => [
        'title' => 'You were mentioned',
        'message' => ':user_name mentioned you in a comment.',
    ],
    'user_followed' => [
        'title' => 'New Follower',
        'message' => ':follower_name started following you.',
    ],
    'achievement_unlocked' => [
        'title' => 'Achievement Unlocked!',
        'message' => 'Congratulations! You unlocked the \":achievement_name\" achievement.',
    ],
    'product_out_of_stock' => [
        'title' => 'Product Out of Stock',
        'message' => 'Product \":product_name\" is now out of stock.',
    ],
    'price_drop_alert' => [
        'title' => 'Price Drop Alert',
        'message' => 'Product \":product_name\" price dropped from :old_price to :new_price.',
    ],
    'wishlist_available' => [
        'title' => 'Wishlist Item Available',
        'message' => 'Product \":product_name\" from your wishlist is now available.',
    ],
    'order_status_updated' => [
        'title' => 'Order Update',
        'message' => 'Your order #:order_id status has been updated to: :status.',
    ],
    'review_received' => [
        'title' => 'New Review',
        'message' => 'Your product \":product_name\" received a :rating star review.',
    ],
    'seller_message' => [
        'title' => 'Message from Seller',
        'message' => ':seller_name sent you a message about product \":product_name\".',
    ],
    'login_from_new_device' => [
        'title' => 'Login from New Device',
        'message' => 'Your account was accessed from a new device: :device_info at :location.',
    ],
    'password_changed' => [
        'title' => 'Password Changed',
        'message' => 'Your account password was successfully changed at :time.',
    ],
    'business_verified' => [
        'title' => 'Business Verified',
        'message' => 'Your business account has been successfully verified.',
    ],
    'business_rejected' => [
        'title' => 'Business Rejected',
        'message' => 'Your business verification request was rejected. Reason: :reason.',
    ],
    'weekly_digest' => [
        'title' => 'Weekly Digest',
        'message' => 'Here\'s your weekly activity summary.',
    ],
    'system_announcement' => [
        'title' => 'System Announcement',
        'message' => ':message',
    ],
    'maintenance_scheduled' => [
        'title' => 'System Maintenance',
        'message' => 'System maintenance is scheduled from :start_time to :end_time.',
    ],
    'actions' => [
        'mark_read' => 'Mark as Read',
        'mark_all_read' => 'Mark All as Read',
    ],
    'categories' => [
        'forum' => 'Forum',
        'social' => 'Social',
        'marketplace' => 'Marketplace',
        'security' => 'Security',
        'system' => 'System',
        'business' => 'Business',
    ],
    'time' => [
        'just_now' => 'just now',
        'minutes_ago' => ':count minutes ago',
        'hours_ago' => ':count hours ago',
        'days_ago' => ':count days ago',
        'weeks_ago' => ':count weeks ago',
        'months_ago' => ':count months ago',
        'years_ago' => ':count years ago',
    ],
    'preferences' => [
        'email_notifications' => 'Email Notifications',
        'browser_notifications' => 'Browser Notifications',
        'mobile_notifications' => 'Mobile Notifications',
        'digest_frequency' => 'Digest Frequency',
        'notification_types' => 'Notification Types',
    ],
    'frequency' => [
        'immediately' => 'Immediately',
        'hourly' => 'Hourly',
        'daily' => 'Daily',
        'weekly' => 'Weekly',
        'never' => 'Never',
    ],
    'status' => [
        'unread' => 'Unread',
        'read' => 'Read',
        'archived' => 'Archived',
        'deleted' => 'Deleted',
    ],
    'bulk_actions' => [
        'mark_all_read' => 'Mark All as Read',
        'mark_selected_read' => 'Mark Selected as Read',
        'delete_all' => 'Delete All',
        'delete_selected' => 'Delete Selected',
        'archive_all' => 'Archive All',
        'archive_selected' => 'Archive Selected',
    ],
    'errors' => [
        'notification_not_found' => 'Notification not found.',
        'permission_denied' => 'You don\'t have permission to perform this action.',
        'invalid_notification_type' => 'Invalid notification type.',
        'failed_to_send' => 'Failed to send notification.',
        'failed_to_update' => 'Failed to update notification.',
    ],
    'success' => [
        'notification_sent' => 'Notification sent successfully.',
        'notification_read' => 'Notification marked as read.',
        'notification_deleted' => 'Notification deleted.',
        'preferences_updated' => 'Notification preferences updated.',
        'all_marked_read' => 'All notifications marked as read.',
    ],
    'types' => [
        'mention' => 'Mentioned',
        'reply' => 'Reply',
        'follow' => 'Follow',
    ],
];
