# 📡 **NOTIFICATION API REFERENCE**
## **MechaMap Notification System - Phase 1**

> **Version**: 1.0.0  
> **Last Updated**: July 10, 2025  
> **Base URL**: `https://mechamap.com/api`

---

## 🔧 **CORE SERVICES**

### **NotificationService**

#### **Send Individual Notification**
```php
NotificationService::send(
    User $user,
    string $type,
    string $title, 
    string $message,
    array $data = [],
    bool $sendEmail = false
): bool
```

**Parameters:**
- `$user` - Target user object
- `$type` - Notification type (see types below)
- `$title` - Notification title
- `$message` - Notification message
- `$data` - Additional data array
- `$sendEmail` - Whether to send email

**Example:**
```php
NotificationService::send(
    $user,
    'thread_created',
    'Thread mới trong forum bạn quan tâm',
    "Thread 'Laravel Tips' đã được tạo trong forum PHP",
    [
        'priority' => 'normal',
        'action_url' => route('threads.show', $thread->slug),
        'thread_id' => $thread->id,
        'forum_id' => $thread->forum_id,
        'author_id' => $thread->user_id,
    ],
    false
);
```

#### **Send Bulk Notifications**
```php
NotificationService::sendBulkNotifications(array $notifications): bool
```

**Parameters:**
- `$notifications` - Array of notification data

**Example:**
```php
$notifications = [
    [
        'user_id' => 1,
        'type' => 'system_announcement',
        'title' => 'System Maintenance',
        'message' => 'Scheduled maintenance tonight',
        'data' => ['priority' => 'high'],
        'send_email' => true
    ],
    // ... more notifications
];

NotificationService::sendBulkNotifications($notifications);
```

#### **Get User Notifications**
```php
NotificationService::getUserNotifications(
    User $user, 
    int $page = 1, 
    int $perPage = 15
): array
```

**Returns:** Cached array of user notifications

#### **Get Unread Count**
```php
NotificationService::getUnreadCountOptimized(User $user): int
```

**Returns:** Cached unread notification count

#### **Mark As Read (Bulk)**
```php
NotificationService::markAsReadBulk(
    User $user, 
    array $notificationIds = []
): int
```

**Returns:** Number of notifications marked as read

---

## 🔔 **NOTIFICATION TYPES**

### **Forum Notifications**

#### **thread_created**
- **Trigger**: New thread created in forum
- **Recipients**: Users following other threads in same forum
- **Email**: ❌ Disabled by default
- **Priority**: Normal
- **Data Structure**:
```php
[
    'priority' => 'normal',
    'action_url' => 'https://mechamap.com/threads/{slug}',
    'thread_id' => 123,
    'forum_id' => 456,
    'author_id' => 789
]
```

#### **thread_replied**
- **Trigger**: New reply in followed thread
- **Recipients**: Thread followers (excluding reply author)
- **Email**: ❌ Disabled by default
- **Priority**: Normal
- **Data Structure**:
```php
[
    'priority' => 'normal',
    'action_url' => 'https://mechamap.com/threads/{slug}#comment-{id}',
    'thread_id' => 123,
    'comment_id' => 456,
    'author_id' => 789
]
```

#### **comment_mention**
- **Trigger**: User mentioned in comment (@username)
- **Recipients**: Mentioned users (excluding comment author)
- **Email**: ✅ Enabled (high priority)
- **Priority**: High
- **Data Structure**:
```php
[
    'priority' => 'high',
    'action_url' => 'https://mechamap.com/threads/{slug}#comment-{id}',
    'thread_id' => 123,
    'comment_id' => 456,
    'author_id' => 789
]
```

### **Security Notifications**

#### **login_from_new_device**
- **Trigger**: Login from unrecognized device
- **Recipients**: Account owner
- **Email**: ✅ Enabled (security alert)
- **Priority**: High
- **Data Structure**:
```php
[
    'priority' => 'high',
    'action_url' => 'https://mechamap.com/profile/security',
    'device_id' => 123,
    'device_fingerprint' => 'sha256_hash',
    'device_name' => 'Chrome on Windows',
    'location' => 'Ho Chi Minh City, Vietnam',
    'ip_address' => '192.168.1.1',
    'login_time' => '2025-07-10T10:30:00Z'
]
```

#### **password_changed**
- **Trigger**: Password successfully changed
- **Recipients**: Account owner
- **Email**: ✅ Enabled (security confirmation)
- **Priority**: High
- **Data Structure**:
```php
[
    'priority' => 'high',
    'action_url' => 'https://mechamap.com/profile/security',
    'ip_address' => '192.168.1.1',
    'changed_at' => '2025-07-10T10:30:00Z'
]
```

---

## 🛡️ **DEVICE DETECTION SERVICE**

### **DeviceDetectionService**

#### **Detect and Track Device**
```php
DeviceDetectionService::detectAndTrackDevice(
    User $user, 
    Request $request
): UserDevice
```

**Returns:** UserDevice model (new or existing)

**Automatic Integration:**
- Called automatically on login
- Triggers new device notification if needed
- Updates last_seen_at for existing devices

#### **Check Device Trust**
```php
DeviceDetectionService::isDeviceTrusted(
    User $user, 
    Request $request
): bool
```

**Returns:** Boolean indicating if device is trusted

#### **Get User Devices**
```php
DeviceDetectionService::getUserDevices(User $user): Collection
```

**Returns:** Collection of user's devices ordered by last_seen_at

#### **Trust Device**
```php
DeviceDetectionService::trustDevice(
    User $user, 
    string $deviceFingerprint
): bool
```

**Returns:** Success boolean

#### **Remove Device**
```php
DeviceDetectionService::removeDevice(
    User $user, 
    string $deviceFingerprint
): bool
```

**Returns:** Success boolean

---

## ⚡ **CACHING SERVICE**

### **NotificationCacheService**

#### **Get Cached Unread Count**
```php
NotificationCacheService::getUnreadCount(User $user): int
```

**Cache Key**: `notifications:unread_count:{user_id}`  
**TTL**: 5 minutes  
**Returns:** Cached unread count

#### **Get Cached User Notifications**
```php
NotificationCacheService::getUserNotifications(
    User $user, 
    int $page = 1, 
    int $perPage = 20
): array
```

**Cache Key**: `notifications:user_notifications:{user_id}:{page}:{perPage}`  
**TTL**: 1 hour  
**Returns:** Array of notification data

#### **Invalidate User Cache**
```php
NotificationCacheService::invalidateUserCache(User $user): void
```

**Effect**: Clears all notification caches for user

#### **Cache Statistics**
```php
NotificationCacheService::getCacheStats(): array
```

**Returns:**
```php
[
    'cache_driver' => 'redis',
    'redis_connected' => true,
    'total_keys' => 1250,
    'memory_usage' => '2.5MB'
]
```

---

## 📧 **EMAIL SYSTEM**

### **Mailable Classes**

#### **ThreadCreatedNotification**
```php
new ThreadCreatedNotification(Thread $thread, User $recipient)
```

**Template**: `emails.notifications.thread-created`  
**Subject**: "Thread mới trong forum bạn quan tâm - {thread_title}"

#### **ThreadRepliedNotification**
```php
new ThreadRepliedNotification(Comment $comment, User $recipient)
```

**Template**: `emails.notifications.thread-replied`  
**Subject**: "Reply mới trong thread bạn theo dõi - {thread_title}"

#### **CommentMentionNotification**
```php
new CommentMentionNotification(Comment $comment, User $recipient)
```

**Template**: `emails.notifications.comment-mention`  
**Subject**: "Bạn được nhắc đến trong bình luận - {thread_title}"

#### **NewDeviceLoginNotification**
```php
new NewDeviceLoginNotification(UserDevice $device, User $recipient)
```

**Template**: `emails.notifications.new-device-login`  
**Subject**: "Cảnh báo bảo mật: Đăng nhập từ thiết bị mới - {device_name}"

#### **PasswordChangedNotification**
```php
new PasswordChangedNotification(User $recipient, array $data = [])
```

**Template**: `emails.notifications.password-changed`  
**Subject**: "Mật khẩu đã được thay đổi - MechaMap"

---

## 🔍 **HELPER METHODS**

### **Mention Extraction**
```php
NotificationService::extractMentions(string $content): array
```

**Regex**: `/(?<!\w)@([a-zA-Z0-9_]+)(?!\w)/`  
**Returns**: Array of unique usernames

**Example:**
```php
$content = "Hello @john and @jane, check this out!";
$mentions = NotificationService::extractMentions($content);
// Returns: ['john', 'jane']
```

### **Notification Icons & Colors**
```php
$notification->icon;    // Font Awesome icon name
$notification->color;   // Bootstrap color class
$notification->time_ago; // Human readable time
```

**Icon Mapping:**
```php
'thread_created' => 'plus-circle',
'thread_replied' => 'reply',
'comment_mention' => 'at',
'login_from_new_device' => 'shield-alt',
'password_changed' => 'key'
```

**Color Mapping:**
```php
'thread_created' => 'success',
'thread_replied' => 'info', 
'comment_mention' => 'warning',
'login_from_new_device' => 'warning',
'password_changed' => 'danger'
```

---

## 🧪 **TESTING COMMANDS**

### **Performance Testing**
```bash
php artisan notification:test-performance [options]
```

**Options:**
- `--users=N` - Number of test users (default: 10)
- `--notifications=N` - Notifications per user (default: 100)
- `--cache` - Test with cache enabled
- `--cleanup` - Clean up test data after testing

**Example:**
```bash
php artisan notification:test-performance --users=5 --notifications=10 --cache --cleanup
```

### **Database Backup**
```bash
php artisan db:backup [options]
```

**Options:**
- `--tables=table1,table2` - Specific tables to backup

---

## 🚨 **ERROR HANDLING**

### **Common Exceptions**
```php
// Service exceptions
NotificationException::class
DeviceDetectionException::class
CacheException::class

// Usage
try {
    NotificationService::send($user, $type, $title, $message);
} catch (NotificationException $e) {
    Log::error('Notification failed: ' . $e->getMessage());
}
```

### **Logging**
All notification operations are logged:
```php
Log::info('Notification sent', [
    'user_id' => $user->id,
    'type' => $type,
    'title' => $title
]);

Log::error('Notification failed', [
    'user_id' => $user->id,
    'error' => $e->getMessage()
]);
```

---

## 📊 **RATE LIMITS & QUOTAS**

### **Performance Limits**
- **Creation Rate**: 444 notifications/second
- **Bulk Rate**: 2,586 notifications/second
- **Cache TTL**: 1 hour (notifications), 5 minutes (counts)
- **Email Queue**: Background processing

### **Database Limits**
- **Max Notifications per User**: No limit
- **Cleanup Policy**: Old read notifications after 90 days
- **Device Retention**: Untrusted devices after 90 days

---

## 🔗 **INTEGRATION EXAMPLES**

### **Forum Integration**
```php
// In ThreadController@store
$thread = Thread::create($data);
NotificationService::sendThreadCreatedNotification($thread);

// In CommentController@store  
$comment = Comment::create($data);
NotificationService::sendThreadRepliedNotification($comment);

$mentions = NotificationService::extractMentions($comment->content);
if (!empty($mentions)) {
    NotificationService::sendCommentMentionNotification($comment, $mentions);
}
```

### **Security Integration**
```php
// In AuthController@login
Auth::attempt($credentials);
DeviceDetectionService::detectAndTrackDevice(Auth::user(), $request);

// In PasswordController@update
$user->update(['password' => Hash::make($newPassword)]);
NotificationService::sendPasswordChangedNotification($user, $request->ip());
```

---

**📚 For more detailed examples, see `/docs/examples/` directory.**
