<?php

/**
 * Notifications Translation File - English
 * Complete translation coverage for notifications functionality
 */

return [
  'new_message' => 'New message',
  'new_comment' => 'New comment',
  'new_reply' => 'New reply',
  'new_follower' => 'New follower',
  'new_like' => 'New like',
  'new_share' => 'New share',
  'new_mention' => 'New mention',
  'new_invitation' => 'New invitation',
  'new_request' => 'New request',
  'system_update' => 'System update',
  'maintenance_notice' => 'Maintenance notice',
  'security_alert' => 'Security alert',
  'account_verified' => 'Account verified',
  'password_changed' => 'Password changed',
  'email_verified' => 'Email verified',
  'profile_updated' => 'Profile updated',
  'settings_saved' => 'Settings saved',
  'subscription_expired' => 'Subscription expired',
  'payment_received' => 'Payment received',
  'order_confirmed' => 'Order confirmed',

  // UI Elements
  'ui' => [
    'header' => 'Notifications',
    'mark_all_read' => 'Mark all as read',
    'mark_as_read' => 'Mark as read',
    'view_all' => 'View all',
    'manage' => 'Manage',
    'view' => 'View',
    'messages' => 'Messages',
    'no_notifications' => 'No notifications',
    'loading' => 'Loading...',
    'error' => 'Error loading notifications',
  ],

  // Stats
  'stats' => [
    'total' => 'Total',
    'unread' => 'Unread',
    'today' => 'Today',
    'this_week' => 'This week',
    'this_month' => 'This month',
  ],

  // Time formats
  'time' => [
    'just_now' => 'Just now',
    'minutes_ago' => ':count minutes ago',
    'hours_ago' => ':count hours ago',
    'days_ago' => ':count days ago',
    'weeks_ago' => ':count weeks ago',
    'months_ago' => ':count months ago',
    'years_ago' => ':count years ago',
  ],

  // Notification Types
  'types' => [
    'new_message' => 'New message',
    'message_reply' => 'Message reply',
    'thread_created' => 'New thread',
    'thread_replied' => 'Thread reply',
    'comment_mention' => 'Mentioned',
    'thread_liked' => 'Thread liked',
    'comment_liked' => 'Comment liked',
    'product_approved' => 'Product approved',
    'product_rejected' => 'Product rejected',
    'order_update' => 'Order update',
    'payment_received' => 'Payment received',
    'commission_paid' => 'Commission paid',
    'system_announcement' => 'System announcement',
    'role_changed' => 'Role changed',
    'account_verified' => 'Account verified',
    'password_changed' => 'Password changed',
    'login_from_new_device' => 'Login from new device',
    'business_verified' => 'Business verified',
    'business_rejected' => 'Business rejected',
    'quote_request' => 'Quote request',
    'user_registered' => 'New user',
    'profile_updated' => 'Profile updated',
    'follow_user' => 'New follower',
  ],

  // Message Templates
  'messages' => [
    'new_message' => 'You have a new message from :sender: :preview',
    'message_reply' => ':sender replied to your message: :preview',
    'thread_created' => ':user created a new thread ":title" in :forum',
    'thread_replied' => ':user replied to thread ":title"',
    'comment_mention' => ':user mentioned you in a comment',
    'thread_liked' => ':user liked your thread ":title"',
    'comment_liked' => ':user liked your comment',
    'product_approved' => 'Your product ":product" has been approved',
    'product_rejected' => 'Your product ":product" was rejected: :reason',
    'order_update' => 'Order #:order_id has been updated: :status',
    'payment_received' => 'Payment :amount received for order #:order_id',
    'commission_paid' => 'Commission :amount has been transferred to your account',
    'system_announcement' => ':title: :content',
    'role_changed' => 'Your role has been changed to :role',
    'account_verified' => 'Your account has been successfully verified',
    'password_changed' => 'Account password was changed at :time',
    'login_from_new_device' => 'Account logged in from new device: :device at :location',
    'business_verified' => 'Business ":business" has been verified',
    'business_rejected' => 'Business ":business" was rejected: :reason',
    'quote_request' => 'New quote request for product ":product" from :customer',
    'user_registered' => 'Welcome :name to MechaMap!',
    'profile_updated' => 'Your profile has been successfully updated',
    'follow_user' => ':user started following you',
  ],

  // Actions
  'actions' => [
    'view_message' => 'View message',
    'view_thread' => 'View thread',
    'view_product' => 'View product',
    'view_order' => 'View order',
    'view_profile' => 'View profile',
    'view_business' => 'View business',
    'view_quote' => 'View quote',
    'manage_account' => 'Manage account',
    'change_password' => 'Change password',
    'view_devices' => 'View devices',
  ],

  // Status messages
  'status' => [
    'marked_as_read' => 'Marked as read',
    'marked_all_as_read' => 'Marked all as read',
    'notification_not_found' => 'Notification not found',
    'failed_to_mark_read' => 'Failed to mark as read',
    'failed_to_load' => 'Failed to load notifications',
  ],

  // Index page (max 3 levels)
  'index' => [
    'title' => 'Notification Management',
    'heading' => 'Notification Management',
    'description' => 'View and manage all your notifications',
    'mark_all_read' => 'Mark all as read',
    'delete_read' => 'Delete read',
    'total_notifications' => 'Total notifications',
    'unread_count' => 'Unread',
    'read_count' => 'Read',
    'notification_type' => 'Notification type',
    'all_types' => 'All types',
    'status_all' => 'All',
    'status_unread' => 'Unread',
    'status_read' => 'Read',
    'notification_list' => 'Notification list',
    'mark_unread' => 'Mark as unread',
    'mark_read' => 'Mark as read',
    'delete_notification' => 'Delete notification',
    'no_notifications' => 'No notifications',
    'no_notifications_type' => 'No notifications found for type',
    'no_read_notifications' => 'No read notifications',
    'no_unread_notifications' => 'No unread notifications',
    'no_notifications_desc' => 'You have no notifications yet. Notifications will appear here when there are new activities.',
    'view_all' => 'View all notifications',
    'confirm_mark_all' => 'Mark all notifications as read?',
    'confirm_delete' => 'Delete this notification?',
  ],
];
