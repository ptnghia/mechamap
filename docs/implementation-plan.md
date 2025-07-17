# Node.js WebSocket Implementation Plan

## 1. PROJECT STRUCTURE

### Directory Layout
```
mechamap-realtime/
├── package.json
├── .env.example
├── .gitignore
├── README.md
├── src/
│   ├── app.js                 # Application entry point
│   ├── server.js              # HTTP/WebSocket server setup
│   ├── config/
│   │   ├── database.js        # Database configuration
│   │   ├── redis.js           # Redis configuration
│   │   └── ssl.js             # SSL certificate setup
│   ├── middleware/
│   │   ├── auth.js            # JWT authentication
│   │   ├── cors.js            # CORS configuration
│   │   ├── rateLimit.js       # Rate limiting
│   │   └── validation.js      # Input validation
│   ├── websocket/
│   │   ├── socketHandler.js   # Main socket event handler
│   │   ├── channelManager.js  # Channel subscription logic
│   │   ├── eventProcessor.js  # Event processing
│   │   └── connectionManager.js # Connection lifecycle
│   ├── services/
│   │   ├── notificationService.js # Notification broadcasting
│   │   ├── authService.js     # Authentication service
│   │   ├── userService.js     # User data management
│   │   └── metricsService.js  # Performance metrics
│   ├── integrations/
│   │   ├── laravelApi.js      # Laravel backend API
│   │   ├── redisSubscriber.js # Redis pub/sub
│   │   └── databaseClient.js  # Direct DB access
│   ├── utils/
│   │   ├── logger.js          # Structured logging
│   │   ├── validator.js       # Data validation
│   │   └── helpers.js         # Utility functions
│   └── routes/
│       ├── health.js          # Health check endpoints
│       ├── metrics.js         # Metrics endpoints
│       └── admin.js           # Admin dashboard
├── tests/
│   ├── unit/                  # Unit tests
│   ├── integration/           # Integration tests
│   ├── load/                  # Load testing scripts
│   └── fixtures/              # Test data
├── deployment/
│   ├── Dockerfile
│   ├── docker-compose.yml
│   ├── nginx.conf
│   ├── pm2.config.js
│   └── ssl/
└── docs/
    ├── API.md
    ├── DEPLOYMENT.md
    └── TROUBLESHOOTING.md
```

## 2. CORE IMPLEMENTATION

### Main Server Setup (src/server.js)
```javascript
const express = require('express');
const { createServer } = require('https');
const { Server } = require('socket.io');
const fs = require('fs');
const cors = require('cors');
const helmet = require('helmet');

const config = require('./config');
const authMiddleware = require('./middleware/auth');
const socketHandler = require('./websocket/socketHandler');
const logger = require('./utils/logger');

class RealtimeServer {
  constructor() {
    this.app = express();
    this.setupMiddleware();
    this.setupServer();
    this.setupWebSocket();
    this.setupRoutes();
  }

  setupMiddleware() {
    this.app.use(helmet());
    this.app.use(cors({
      origin: [
        'https://mechamap.com',
        'https://www.mechamap.com',
        'https://mechamap.test'
      ],
      credentials: true
    }));
    this.app.use(express.json());
  }

  setupServer() {
    const sslOptions = {
      key: fs.readFileSync(config.ssl.keyPath),
      cert: fs.readFileSync(config.ssl.certPath)
    };
    
    this.server = createServer(sslOptions, this.app);
  }

  setupWebSocket() {
    this.io = new Server(this.server, {
      cors: {
        origin: [
          'https://mechamap.com',
          'https://www.mechamap.com',
          'https://mechamap.test'
        ],
        credentials: true
      },
      transports: ['websocket', 'polling']
    });

    // Authentication middleware
    this.io.use(authMiddleware);
    
    // Connection handler
    this.io.on('connection', socketHandler);
  }

  setupRoutes() {
    this.app.use('/health', require('./routes/health'));
    this.app.use('/metrics', require('./routes/metrics'));
    this.app.use('/admin', require('./routes/admin'));
  }

  start() {
    const port = config.port || 3000;
    this.server.listen(port, () => {
      logger.info(`Realtime server started on port ${port}`);
    });
  }
}

module.exports = RealtimeServer;
```

### WebSocket Handler (src/websocket/socketHandler.js)
```javascript
const logger = require('../utils/logger');
const channelManager = require('./channelManager');
const notificationService = require('../services/notificationService');
const metricsService = require('../services/metricsService');

const socketHandler = (socket) => {
  logger.info(`User connected: ${socket.userId}`, {
    userId: socket.userId,
    socketId: socket.id,
    userAgent: socket.handshake.headers['user-agent']
  });

  // Track connection metrics
  metricsService.incrementConnections();
  metricsService.trackUserConnection(socket.userId);

  // Handle channel subscriptions
  socket.on('subscribe', async (data) => {
    try {
      const { channel } = data;
      const authorized = await channelManager.authorize(socket, channel);
      
      if (authorized) {
        socket.join(channel);
        socket.emit('subscribed', { channel, status: 'success' });
        logger.info(`User subscribed to channel: ${channel}`, {
          userId: socket.userId,
          channel
        });
      } else {
        socket.emit('subscription_error', {
          channel,
          error: 'Unauthorized'
        });
      }
    } catch (error) {
      logger.error('Subscription error:', error);
      socket.emit('subscription_error', {
        channel: data.channel,
        error: 'Internal error'
      });
    }
  });

  // Handle unsubscribe
  socket.on('unsubscribe', (data) => {
    const { channel } = data;
    socket.leave(channel);
    socket.emit('unsubscribed', { channel });
    logger.info(`User unsubscribed from channel: ${channel}`, {
      userId: socket.userId,
      channel
    });
  });

  // Handle notification acknowledgment
  socket.on('notification_read', async (data) => {
    try {
      await notificationService.markAsRead(data.notificationId, socket.userId);
      
      // Broadcast to user's other devices
      socket.to(`private-user.${socket.userId}`).emit('notification_read', {
        notificationId: data.notificationId
      });
      
      logger.info('Notification marked as read', {
        userId: socket.userId,
        notificationId: data.notificationId
      });
    } catch (error) {
      logger.error('Error marking notification as read:', error);
    }
  });

  // Handle ping/pong for connection health
  socket.on('ping', () => {
    socket.emit('pong');
  });

  // Handle disconnection
  socket.on('disconnect', (reason) => {
    logger.info(`User disconnected: ${socket.userId}`, {
      userId: socket.userId,
      socketId: socket.id,
      reason
    });
    
    metricsService.decrementConnections();
    metricsService.trackUserDisconnection(socket.userId);
  });

  // Auto-subscribe to user's private channel
  const userChannel = `private-user.${socket.userId}`;
  socket.join(userChannel);
  socket.emit('subscribed', { channel: userChannel, status: 'auto' });
};

module.exports = socketHandler;
```

### Channel Manager (src/websocket/channelManager.js)
```javascript
const userService = require('../services/userService');
const logger = require('../utils/logger');

class ChannelManager {
  async authorize(socket, channel) {
    try {
      // Public channels - always allowed
      if (channel.startsWith('public.')) {
        return true;
      }

      // Private user channels
      if (channel.startsWith('private-user.')) {
        const targetUserId = parseInt(channel.split('.')[1]);
        return socket.userId === targetUserId;
      }

      // Thread presence channels
      if (channel.startsWith('presence-thread.')) {
        const threadId = parseInt(channel.split('.')[1]);
        return await this.checkThreadAccess(socket.userId, threadId);
      }

      // Forum channels
      if (channel.startsWith('forum.')) {
        const forumId = parseInt(channel.split('.')[1]);
        return await this.checkForumAccess(socket.userId, forumId);
      }

      // Admin channels
      if (channel.startsWith('admin.')) {
        const user = await userService.getUser(socket.userId);
        return user.role === 'admin' || user.role === 'super_admin';
      }

      return false;
    } catch (error) {
      logger.error('Channel authorization error:', error);
      return false;
    }
  }

  async checkThreadAccess(userId, threadId) {
    // Check if user has access to thread
    const user = await userService.getUser(userId);
    const thread = await userService.getThread(threadId);
    
    if (!thread) return false;
    
    // Check forum access
    return await this.checkForumAccess(userId, thread.forum_id);
  }

  async checkForumAccess(userId, forumId) {
    // Check if user has access to forum based on role
    const user = await userService.getUser(userId);
    const forum = await userService.getForum(forumId);
    
    if (!forum) return false;
    
    // Role-based access control
    const roleHierarchy = {
      'guest': 1,
      'member': 2,
      'senior_member': 3,
      'moderator': 4,
      'admin': 5,
      'super_admin': 6
    };
    
    const userLevel = roleHierarchy[user.role] || 0;
    const requiredLevel = roleHierarchy[forum.required_role] || 1;
    
    return userLevel >= requiredLevel;
  }
}

module.exports = new ChannelManager();
```

## 3. INTEGRATION WITH LARAVEL

### Laravel Broadcast Service
```php
// app/Services/NodeJsBroadcastService.php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NodeJsBroadcastService
{
    private string $baseUrl;
    private string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('broadcasting.connections.nodejs.url');
        $this->apiKey = config('broadcasting.connections.nodejs.api_key');
    }

    public function broadcast(string $channel, string $event, array $data): bool
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/api/broadcast', [
                'channel' => $channel,
                'event' => $event,
                'data' => $data,
                'timestamp' => now()->toISOString()
            ]);

            if ($response->successful()) {
                Log::info('Broadcast successful', [
                    'channel' => $channel,
                    'event' => $event
                ]);
                return true;
            }

            Log::error('Broadcast failed', [
                'channel' => $channel,
                'event' => $event,
                'status' => $response->status(),
                'response' => $response->body()
            ]);
            return false;

        } catch (\Exception $e) {
            Log::error('Broadcast exception', [
                'channel' => $channel,
                'event' => $event,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
```

### Updated UnifiedNotificationService
```php
// Update app/Services/UnifiedNotificationService.php
public static function send(
    User $user,
    string $type,
    string $title,
    string $message,
    array $data = [],
    array $channels = ['database']
): bool {
    try {
        // ... existing database logic ...

        // Broadcast real-time notification
        if (in_array('database', $channels)) {
            $broadcastService = app(NodeJsBroadcastService::class);
            $broadcastService->broadcast(
                "private-user.{$user->id}",
                'notification.sent',
                [
                    'id' => $notification->id,
                    'type' => $type,
                    'title' => $title,
                    'message' => $message,
                    'data' => $data,
                    'created_at' => $notification->created_at,
                    'unread_count' => $user->userNotifications()
                        ->where('is_read', false)->count()
                ]
            );
        }

        return true;
    } catch (\Exception $e) {
        Log::error('Notification send failed: ' . $e->getMessage());
        return false;
    }
}
```
