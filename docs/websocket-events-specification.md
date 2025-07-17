# MechaMap WebSocket Events Specification

> **Version**: 2.0  
> **Last Updated**: 2025-07-17  
> **Purpose**: Comprehensive specification cho real-time events trong MechaMap platform

## 🎯 **Event Naming Convention**

### **Pattern**: `{module}.{action}.{entity}`

```
forum.comment.created
marketplace.stock.updated
user.status.changed
messaging.message.sent
notification.sent
showcase.project.updated
```

## 📊 **Standard Data Structure**

### **Base Event Format**
```json
{
  "event": "forum.comment.created",
  "timestamp": "2025-07-17T10:30:00Z",
  "user_id": 123,
  "channel": "thread.456",
  "priority": "medium",
  "data": {
    // Event-specific payload
  },
  "metadata": {
    "version": "1.0",
    "source": "laravel_api",
    "request_id": "uuid-here"
  }
}
```

## 🏷️ **Channel Strategy**

### **1. User Channels (Private)**
```
user.{user_id}                 # Personal notifications
user.{user_id}.messages        # Private messages
user.{user_id}.orders          # Order updates
user.{user_id}.following       # Following activity feed
```

### **2. Public Channels**
```
thread.{thread_id}             # Thread-specific updates
forum.{forum_id}               # Forum-wide updates
marketplace.global             # Global marketplace updates
showcase.{showcase_id}         # Showcase project updates
```

### **3. Role-based Channels**
```
admin.notifications            # Admin-only alerts
seller.{seller_id}             # Seller-specific updates
moderator.reports              # Moderation alerts
```

## ⚡ **Priority System**

### **HIGH PRIORITY** (Immediate delivery)
- `messaging.message.sent`
- `marketplace.order.status_changed`
- `notification.security.alert`
- `user.status.online`

### **MEDIUM PRIORITY** (1-2s delay acceptable)
- `forum.comment.created`
- `marketplace.stock.low_warning`
- `notification.system.announcement`

### **LOW PRIORITY** (5+ seconds delay acceptable)
- `forum.thread.view_count_updated`
- `user.activity.feed_updated`
- `showcase.project.stats_updated`

## 📋 **Event Specifications**

### **1. Forum Events**

#### **forum.comment.created**
```json
{
  "event": "forum.comment.created",
  "channel": "thread.{thread_id}",
  "priority": "medium",
  "data": {
    "comment": {
      "id": 789,
      "content": "Great explanation!",
      "parent_id": null,
      "user": {
        "id": 123,
        "name": "John Doe",
        "username": "johndoe",
        "avatar_url": "https://..."
      },
      "created_at": "2025-07-17T10:30:00Z"
    },
    "thread": {
      "id": 456,
      "title": "SolidWorks Tips",
      "reply_count": 15
    }
  }
}
```

#### **forum.comment.updated**
```json
{
  "event": "forum.comment.updated",
  "channel": "thread.{thread_id}",
  "priority": "medium",
  "data": {
    "comment_id": 789,
    "content": "Updated content here",
    "edited_at": "2025-07-17T10:35:00Z",
    "edit_reason": "Fixed typo"
  }
}
```

#### **forum.reaction.added**
```json
{
  "event": "forum.reaction.added",
  "channel": "thread.{thread_id}",
  "priority": "low",
  "data": {
    "comment_id": 789,
    "reaction_type": "like",
    "user_id": 123,
    "new_count": 25
  }
}
```

#### **forum.typing.started**
```json
{
  "event": "forum.typing.started",
  "channel": "thread.{thread_id}",
  "priority": "high",
  "data": {
    "user": {
      "id": 123,
      "name": "John Doe"
    },
    "expires_at": "2025-07-17T10:31:00Z"
  }
}
```

### **2. Marketplace Events**

#### **marketplace.stock.updated**
```json
{
  "event": "marketplace.stock.updated",
  "channel": "product.{product_id}",
  "priority": "medium",
  "data": {
    "product_id": 456,
    "old_stock": 10,
    "new_stock": 5,
    "is_low_stock": true,
    "low_stock_threshold": 5
  }
}
```

#### **marketplace.order.status_changed**
```json
{
  "event": "marketplace.order.status_changed",
  "channel": "user.{buyer_id}",
  "priority": "high",
  "data": {
    "order_id": 789,
    "old_status": "processing",
    "new_status": "shipped",
    "tracking_number": "ABC123456",
    "estimated_delivery": "2025-07-20"
  }
}
```

#### **marketplace.price.changed**
```json
{
  "event": "marketplace.price.changed",
  "channel": "product.{product_id}",
  "priority": "medium",
  "data": {
    "product_id": 456,
    "old_price": 99.99,
    "new_price": 79.99,
    "discount_percentage": 20,
    "sale_ends_at": "2025-07-24T23:59:59Z"
  }
}
```

### **3. User Activity Events**

#### **user.status.changed**
```json
{
  "event": "user.status.changed",
  "channel": "following.{follower_id}",
  "priority": "high",
  "data": {
    "user_id": 123,
    "status": "online",
    "last_seen_at": "2025-07-17T10:30:00Z",
    "activity": "viewing_thread"
  }
}
```

#### **user.activity.feed_updated**
```json
{
  "event": "user.activity.feed_updated",
  "channel": "following.{follower_id}",
  "priority": "low",
  "data": {
    "activity": {
      "type": "thread_created",
      "user_id": 123,
      "content_id": 456,
      "content_title": "New SolidWorks Tutorial",
      "created_at": "2025-07-17T10:30:00Z"
    }
  }
}
```

### **4. Messaging Events**

#### **messaging.message.sent**
```json
{
  "event": "messaging.message.sent",
  "channel": "user.{recipient_id}",
  "priority": "high",
  "data": {
    "message": {
      "id": 789,
      "conversation_id": 456,
      "content": "Hello there!",
      "sender": {
        "id": 123,
        "name": "John Doe",
        "avatar_url": "https://..."
      },
      "created_at": "2025-07-17T10:30:00Z"
    }
  }
}
```

#### **messaging.typing.started**
```json
{
  "event": "messaging.typing.started",
  "channel": "conversation.{conversation_id}",
  "priority": "high",
  "data": {
    "user_id": 123,
    "conversation_id": 456,
    "expires_at": "2025-07-17T10:31:00Z"
  }
}
```

### **5. Notification Events**

#### **notification.sent**
```json
{
  "event": "notification.sent",
  "channel": "user.{user_id}",
  "priority": "high",
  "data": {
    "notification": {
      "id": 789,
      "type": "order_shipped",
      "title": "Your order has been shipped",
      "message": "Order #12345 is on its way",
      "icon": "truck",
      "action_url": "/orders/12345",
      "created_at": "2025-07-17T10:30:00Z"
    },
    "unread_count": 5
  }
}
```

### **6. Showcase Events**

#### **showcase.project.updated**
```json
{
  "event": "showcase.project.updated",
  "channel": "showcase.{showcase_id}",
  "priority": "medium",
  "data": {
    "showcase_id": 456,
    "update_type": "new_image",
    "content": {
      "image_url": "https://...",
      "description": "Latest progress photo"
    },
    "author": {
      "id": 123,
      "name": "John Doe"
    }
  }
}
```

## 🔐 **Authentication & Authorization**

### **JWT Token Verification**
```javascript
// Client connection
const socket = io('wss://realtime.mechamap.com', {
  auth: {
    token: 'Bearer jwt_token_here'
  }
});
```

### **Channel Authorization**
```javascript
// Server-side channel authorization
socket.on('join_channel', async (channel, callback) => {
  const authorized = await checkChannelPermission(socket.userId, channel);
  if (authorized) {
    socket.join(channel);
    callback({ success: true });
  } else {
    callback({ success: false, error: 'Unauthorized' });
  }
});
```

## 📈 **Performance Considerations**

### **Batching Strategy**
- **High Priority**: Immediate delivery
- **Medium Priority**: Batch every 2 seconds
- **Low Priority**: Batch every 10 seconds

### **Rate Limiting**
- **Per User**: 100 events/minute
- **Per Channel**: 1000 events/minute
- **Global**: 10,000 events/minute

### **Connection Limits**
- **Per User**: 5 concurrent connections
- **Total**: 10,000 concurrent connections

## 🧪 **Testing Strategy**

### **Unit Tests**
- Event serialization/deserialization
- Channel authorization logic
- Rate limiting functionality

### **Integration Tests**
- Laravel → Node.js event broadcasting
- WebSocket client connection flow
- Database trigger → real-time event flow

### **Load Tests**
- 1,000 concurrent users
- 10,000 events/minute throughput
- Connection stability over 24 hours

### **E2E Tests**
- Complete user journey với real-time features
- Cross-browser compatibility
- Mobile responsiveness
