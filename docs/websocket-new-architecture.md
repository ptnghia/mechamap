# MechaMap WebSocket/Realtime - New Architecture Design

> **Ngày tạo**: 2025-07-17  
> **Mục đích**: Thiết kế kiến trúc mới với TypeScript và proper separation of concerns

## 🏗️ **KIẾN TRÚC TỔNG QUAN**

### 🎯 **Design Principles**

1. **Type Safety First**: 100% TypeScript coverage
2. **Separation of Concerns**: Clear layer boundaries
3. **Testability**: Dependency injection và mocking
4. **Scalability**: Horizontal scaling support
5. **Reliability**: Error handling và recovery
6. **Maintainability**: Clean code và documentation

### 📊 **Architecture Layers**

```
┌─────────────────────────────────────────────────────────┐
│                 Frontend Layer (TypeScript)             │
├─────────────────────────────────────────────────────────┤
│              Node.js WebSocket Server (TypeScript)      │
├─────────────────────────────────────────────────────────┤
│                 Laravel Backend (PHP)                   │
├─────────────────────────────────────────────────────────┤
│                    Data Layer                           │
└─────────────────────────────────────────────────────────┘
```

## 🎨 **FRONTEND LAYER (TypeScript)**

### 📦 **Client Library Structure**

```typescript
// src/client/
├── types/
│   ├── events.types.ts          # Event type definitions
│   ├── connection.types.ts      # Connection interfaces
│   ├── notification.types.ts    # Notification schemas
│   └── config.types.ts          # Configuration types
├── services/
│   ├── WebSocketClient.ts       # Main WebSocket client
│   ├── NotificationManager.ts   # Notification handling
│   ├── ConnectionManager.ts     # Connection lifecycle
│   ├── ChannelManager.ts        # Channel subscriptions
│   └── OfflineManager.ts        # Offline support
├── utils/
│   ├── logger.ts                # Client-side logging
│   ├── storage.ts               # Local storage wrapper
│   └── retry.ts                 # Retry mechanisms
└── index.ts                     # Public API exports
```

### 🔌 **Core Interfaces**

```typescript
// Connection Interface
interface IWebSocketClient {
    connect(token: string): Promise<void>;
    disconnect(): Promise<void>;
    subscribe(channel: string): Promise<void>;
    unsubscribe(channel: string): Promise<void>;
    emit(event: string, data: any): Promise<void>;
    on(event: string, callback: Function): void;
    off(event: string, callback?: Function): void;
}

// Notification Interface
interface INotificationManager {
    show(notification: Notification): void;
    markAsRead(id: string): Promise<void>;
    getHistory(): Notification[];
    clear(): void;
    requestPermission(): Promise<boolean>;
}

// Connection Manager Interface
interface IConnectionManager {
    getStatus(): ConnectionStatus;
    reconnect(): Promise<void>;
    isOnline(): boolean;
    getLatency(): number;
    getConnectionInfo(): ConnectionInfo;
}
```

## 🚀 **NODE.JS SERVER LAYER (TypeScript)**

### 🏗️ **Server Architecture**

```typescript
// src/server/
├── types/
│   ├── auth.types.ts            # Authentication types
│   ├── websocket.types.ts       # WebSocket types
│   ├── channel.types.ts         # Channel types
│   └── monitoring.types.ts      # Monitoring types
├── interfaces/
│   ├── IAuthService.ts          # Auth service contract
│   ├── IChannelManager.ts       # Channel manager contract
│   ├── IBroadcastService.ts     # Broadcasting contract
│   └── IMonitoringService.ts    # Monitoring contract
├── services/
│   ├── AuthService.ts           # Authentication logic
│   ├── ChannelManager.ts        # Channel management
│   ├── BroadcastService.ts      # Event broadcasting
│   ├── NotificationService.ts   # Notification handling
│   └── MonitoringService.ts     # Health monitoring
├── middleware/
│   ├── AuthMiddleware.ts        # Authentication middleware
│   ├── RateLimitMiddleware.ts   # Rate limiting
│   ├── CorsMiddleware.ts        # CORS handling
│   └── LoggingMiddleware.ts     # Request logging
├── handlers/
│   ├── ConnectionHandler.ts     # Connection events
│   ├── ChannelHandler.ts        # Channel operations
│   ├── NotificationHandler.ts   # Notification events
│   └── ErrorHandler.ts          # Error handling
├── utils/
│   ├── logger.ts                # Structured logging
│   ├── config.ts                # Configuration management
│   ├── database.ts              # Database connections
│   └── redis.ts                 # Redis client
└── server.ts                    # Main server entry
```

### 🔐 **Authentication Service**

```typescript
interface IAuthService {
    validateToken(token: string): Promise<AuthResult>;
    getUserPermissions(userId: string): Promise<Permission[]>;
    authorizeChannel(userId: string, channel: string): Promise<boolean>;
    refreshToken(token: string): Promise<string>;
}

class AuthService implements IAuthService {
    constructor(
        private laravelApi: ILaravelApiClient,
        private cache: ICache,
        private logger: ILogger
    ) {}

    async validateToken(token: string): Promise<AuthResult> {
        // Check cache first
        const cached = await this.cache.get(`auth:${token}`);
        if (cached) return cached;

        // Validate with Laravel
        const result = await this.laravelApi.verifyUser(token);
        
        // Cache result
        await this.cache.set(`auth:${token}`, result, 300); // 5 min TTL
        
        return result;
    }
}
```

### 📡 **Channel Management**

```typescript
interface IChannelManager {
    createChannel(name: string, type: ChannelType): Promise<Channel>;
    subscribe(userId: string, channelName: string): Promise<boolean>;
    unsubscribe(userId: string, channelName: string): Promise<void>;
    broadcast(channelName: string, event: string, data: any): Promise<void>;
    getChannelUsers(channelName: string): Promise<string[]>;
}

class ChannelManager implements IChannelManager {
    private channels: Map<string, Channel> = new Map();
    private userChannels: Map<string, Set<string>> = new Map();

    async subscribe(userId: string, channelName: string): Promise<boolean> {
        // Authorize subscription
        const authorized = await this.authService.authorizeChannel(userId, channelName);
        if (!authorized) return false;

        // Get or create channel
        const channel = await this.getOrCreateChannel(channelName);
        
        // Add user to channel
        channel.addUser(userId);
        
        // Track user's channels
        if (!this.userChannels.has(userId)) {
            this.userChannels.set(userId, new Set());
        }
        this.userChannels.get(userId)!.add(channelName);

        this.logger.info('User subscribed to channel', { userId, channelName });
        return true;
    }
}
```

### 🔔 **Broadcasting Service**

```typescript
interface IBroadcastService {
    broadcastToChannel(channel: string, event: string, data: any): Promise<void>;
    broadcastToUser(userId: string, event: string, data: any): Promise<void>;
    broadcastToRole(role: string, event: string, data: any): Promise<void>;
    scheduleNotification(notification: ScheduledNotification): Promise<void>;
}

class BroadcastService implements IBroadcastService {
    constructor(
        private io: Server,
        private channelManager: IChannelManager,
        private queue: IQueue,
        private logger: ILogger
    ) {}

    async broadcastToChannel(channel: string, event: string, data: any): Promise<void> {
        const users = await this.channelManager.getChannelUsers(channel);
        
        for (const userId of users) {
            const socket = this.getSocketByUserId(userId);
            if (socket) {
                socket.emit(event, data);
                this.logger.debug('Event sent to user', { userId, event, channel });
            } else {
                // Queue for offline delivery
                await this.queue.add('offline-notification', {
                    userId,
                    event,
                    data,
                    timestamp: Date.now()
                });
            }
        }
    }
}
```

## 🔧 **LARAVEL BACKEND INTEGRATION**

### 📡 **Event Broadcasting**

```php
// Laravel Event Broadcasting to Node.js
class NotificationBroadcaster
{
    private $nodeJsClient;
    
    public function __construct(NodeJsApiClient $client)
    {
        $this->nodeJsClient = $client;
    }
    
    public function broadcast(BroadcastEvent $event): void
    {
        $payload = [
            'channel' => $event->getChannel(),
            'event' => $event->getEventName(),
            'data' => $event->getData(),
            'timestamp' => now()->toISOString()
        ];
        
        $this->nodeJsClient->post('/api/broadcast', $payload);
    }
}

// Usage in Laravel
class UserNotificationCreated implements ShouldBroadcast
{
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("user.{$this->notification->user_id}")
        ];
    }
    
    public function broadcastWith(): array
    {
        return [
            'id' => $this->notification->id,
            'type' => $this->notification->type,
            'data' => $this->notification->data,
            'created_at' => $this->notification->created_at
        ];
    }
}
```

## 🧪 **TESTING ARCHITECTURE**

### 🔬 **Testing Strategy**

```typescript
// tests/
├── unit/
│   ├── services/
│   │   ├── AuthService.test.ts
│   │   ├── ChannelManager.test.ts
│   │   └── BroadcastService.test.ts
│   ├── handlers/
│   │   ├── ConnectionHandler.test.ts
│   │   └── NotificationHandler.test.ts
│   └── utils/
│       ├── logger.test.ts
│       └── config.test.ts
├── integration/
│   ├── laravel-nodejs.test.ts
│   ├── websocket-flow.test.ts
│   └── notification-delivery.test.ts
├── e2e/
│   ├── user-journey.test.ts
│   ├── multi-device.test.ts
│   └── offline-sync.test.ts
└── load/
    ├── connection-stress.test.js
    ├── message-throughput.test.js
    └── scaling-limits.test.js
```

### 🎯 **Test Examples**

```typescript
// Unit Test Example
describe('AuthService', () => {
    let authService: AuthService;
    let mockLaravelApi: jest.Mocked<ILaravelApiClient>;
    let mockCache: jest.Mocked<ICache>;

    beforeEach(() => {
        mockLaravelApi = createMockLaravelApi();
        mockCache = createMockCache();
        authService = new AuthService(mockLaravelApi, mockCache, mockLogger);
    });

    it('should validate token successfully', async () => {
        const token = 'valid-token';
        const expectedResult = { userId: '123', role: 'user' };
        
        mockLaravelApi.verifyUser.mockResolvedValue(expectedResult);
        
        const result = await authService.validateToken(token);
        
        expect(result).toEqual(expectedResult);
        expect(mockCache.set).toHaveBeenCalledWith(`auth:${token}`, expectedResult, 300);
    });
});

// Integration Test Example
describe('WebSocket Integration', () => {
    let server: TestServer;
    let client: SocketIOClient;

    beforeAll(async () => {
        server = await createTestServer();
        client = createTestClient();
    });

    it('should handle notification broadcasting', async () => {
        await client.authenticate('valid-token');
        await client.subscribe('user.123');
        
        // Trigger notification from Laravel
        await server.broadcast('user.123', 'notification', {
            id: '1',
            message: 'Test notification'
        });
        
        const notification = await client.waitForEvent('notification');
        expect(notification.message).toBe('Test notification');
    });
});
```

---

**📊 Status**: Architecture Design Complete ✅  
**👥 Team**: Development Team  
**📅 Next Review**: After migration plan completion
