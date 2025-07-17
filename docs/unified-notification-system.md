# 🔔 Unified Laravel Notification System

## Tổng quan

Unified Laravel Notification System là hệ thống thông báo thống nhất cho MechaMap, kết hợp cả custom notification system hiện tại và Laravel built-in notifications để cung cấp một giải pháp toàn diện và linh hoạt.

## Kiến trúc hệ thống

### 1. Database Structure

#### Custom Notifications Table (`custom_notifications`)
```sql
- id (bigint, primary key)
- user_id (bigint, foreign key)
- type (varchar)
- title (varchar)
- message (text)
- data (json)
- priority (enum: low, normal, high, urgent)
- is_read (boolean)
- read_at (timestamp)
- created_at (timestamp)
- updated_at (timestamp)
```

#### Laravel Notifications Table (`notifications`)
```sql
- id (uuid, primary key)
- type (varchar)
- notifiable_type (varchar)
- notifiable_id (bigint)
- data (text/json)
- read_at (timestamp)
- created_at (timestamp)
- updated_at (timestamp)
```

#### Supporting Tables
- `notification_preferences`: User notification preferences
- `notification_templates`: Notification templates
- `notification_logs`: Debugging and analytics logs

### 2. Core Components

#### UnifiedNotificationService
Dịch vụ chính cung cấp interface thống nhất cho cả hai hệ thống:

```php
// Gửi notification
UnifiedNotificationService::send($users, $type, $title, $message, $data, $channels);

// Lấy notifications của user
UnifiedNotificationService::getUserNotifications($user, $page, $perPage);

// Đếm unread notifications
UnifiedNotificationService::getUnreadCount($user);

// Đánh dấu đã đọc
UnifiedNotificationService::markAsRead($user, $notificationId, $source);

// Đánh dấu tất cả đã đọc
UnifiedNotificationService::markAllAsRead($user);

// Lấy thống kê
UnifiedNotificationService::getStats($user);
```

#### UnifiedNotification Class
Laravel Notification class cho hệ thống unified:

```php
use App\Notifications\UnifiedNotification;

$user->notify(new UnifiedNotification([
    'title' => 'Test Notification',
    'message' => 'This is a test message',
    'type' => 'test',
    'data' => ['key' => 'value']
]));
```

## API Endpoints

### Base URL: `/api/v1/unified-notifications`

#### 1. Get Notifications
```http
GET /api/v1/unified-notifications
```

**Parameters:**
- `page` (int, optional): Page number (default: 1)
- `per_page` (int, optional): Items per page (default: 20)

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 123,
            "type": "thread_reply",
            "title": "New Reply",
            "message": "Someone replied to your thread",
            "data": {...},
            "is_read": false,
            "read_at": null,
            "created_at": "2025-07-17T03:00:00Z",
            "source": "custom"
        }
    ],
    "meta": {
        "page": 1,
        "per_page": 20,
        "total": 5
    }
}
```

#### 2. Get Unread Count
```http
GET /api/v1/unified-notifications/count
```

**Response:**
```json
{
    "success": true,
    "count": 18,
    "unread_count": 18,
    "message": "Unread count retrieved successfully"
}
```

#### 3. Get Recent Notifications
```http
GET /api/v1/unified-notifications/recent?limit=5
```

**Response:**
```json
{
    "success": true,
    "notifications": [...],
    "total_unread": 18,
    "message": "Recent notifications retrieved successfully"
}
```

#### 4. Mark as Read
```http
POST /api/v1/unified-notifications/mark-as-read
```

**Body:**
```json
{
    "notification_id": "123",
    "source": "auto" // auto, custom, laravel
}
```

#### 5. Mark All as Read
```http
POST /api/v1/unified-notifications/mark-all-as-read
```

#### 6. Get Statistics
```http
GET /api/v1/unified-notifications/stats
```

**Response:**
```json
{
    "success": true,
    "data": {
        "custom": {
            "total": 10,
            "unread": 9
        },
        "laravel": {
            "total": 0,
            "unread": 0
        },
        "unified": {
            "total": 10,
            "unread": 9
        }
    }
}
```

#### 7. Send Test Notification
```http
POST /api/v1/unified-notifications/send-test
```

**Body:**
```json
{
    "type": "test",
    "title": "Test Notification",
    "message": "This is a test message",
    "channels": ["database", "mail"]
}
```

## Usage Examples

### 1. Gửi notification đơn giản
```php
use App\Services\UnifiedNotificationService;

UnifiedNotificationService::send(
    $user,
    'thread_reply',
    'New Reply to Your Thread',
    'Someone has replied to your thread "How to use Laravel"',
    [
        'thread_id' => 123,
        'reply_id' => 456,
        'author' => 'John Doe'
    ],
    ['database']
);
```

### 2. Gửi notification cho nhiều users
```php
$users = User::whereIn('id', [1, 2, 3])->get();

UnifiedNotificationService::send(
    $users,
    'system_announcement',
    'System Maintenance',
    'The system will be under maintenance tonight',
    ['maintenance_time' => '2025-07-17 22:00:00'],
    ['database', 'mail']
);
```

### 3. Lấy notifications với pagination
```php
$notifications = UnifiedNotificationService::getUserNotifications($user, 1, 10);

foreach ($notifications as $notification) {
    echo $notification['title'] . ' (' . $notification['source'] . ')';
}
```

### 4. Kiểm tra unread count
```php
$unreadCount = UnifiedNotificationService::getUnreadCount($user);
echo "You have {$unreadCount} unread notifications";
```

## Migration Guide

### Từ Custom System sang Unified System

1. **Backup dữ liệu hiện tại**
```bash
php artisan db:backup
```

2. **Chạy migration**
```bash
php artisan migrate --path=database/migrations/2025_07_17_100000_create_unified_notification_system.php
```

3. **Update code sử dụng UnifiedNotificationService**
```php
// Cũ
$user->userNotifications()->create([...]);

// Mới
UnifiedNotificationService::send($user, $type, $title, $message, $data);
```

4. **Update API calls**
```javascript
// Cũ
fetch('/api/notifications/count')

// Mới
fetch('/api/v1/unified-notifications/count')
```

## Best Practices

### 1. Notification Types
Sử dụng naming convention rõ ràng cho notification types:
- `thread_reply`: Reply to thread
- `thread_like`: Thread liked
- `user_follow`: User followed
- `system_announcement`: System announcements
- `marketplace_order`: Marketplace orders

### 2. Data Structure
Luôn include đầy đủ thông tin trong data field:
```php
[
    'entity_type' => 'thread',
    'entity_id' => 123,
    'actor_id' => 456,
    'actor_name' => 'John Doe',
    'action' => 'replied',
    'url' => '/threads/123#reply-456'
]
```

### 3. Error Handling
```php
try {
    $result = UnifiedNotificationService::send(...);
    if (!$result) {
        Log::error('Failed to send notification');
    }
} catch (\Exception $e) {
    Log::error('Notification error: ' . $e->getMessage());
}
```

## Testing

### Unit Tests
```bash
php artisan test --filter UnifiedNotificationTest
```

### API Tests
```bash
# Test count endpoint
curl -X GET "https://mechamap.test/api/v1/unified-notifications/count"

# Test send notification
curl -X POST "https://mechamap.test/api/v1/unified-notifications/send-test" \
  -H "Content-Type: application/json" \
  -d '{"type":"test","title":"Test","message":"Test message"}'
```

## Monitoring & Analytics

### Notification Logs
Tất cả notifications được log vào `notification_logs` table để:
- Debug issues
- Analytics và reporting
- Performance monitoring

### Performance Metrics
- Average notification delivery time
- Success/failure rates
- User engagement rates
- Channel effectiveness

## Future Enhancements

1. **Real-time notifications** với WebSocket
2. **Push notifications** cho mobile apps
3. **Email templates** system
4. **Notification scheduling**
5. **A/B testing** cho notification content
6. **Machine learning** cho personalized notifications

---

## Kết luận

Unified Laravel Notification System cung cấp một giải pháp toàn diện, linh hoạt và có thể mở rộng cho hệ thống thông báo của MechaMap. Hệ thống này đảm bảo backward compatibility với custom system hiện tại trong khi cung cấp khả năng migration dần sang Laravel built-in notifications.
