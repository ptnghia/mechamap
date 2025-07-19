# Node.js WebSocket Server Architecture for MechaMap

## 1. SYSTEM ARCHITECTURE OVERVIEW

```
┌─────────────────────────────────────────────────────────────────┐
│                    MechaMap Real-time Architecture              │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ┌─────────────────┐    ┌─────────────────┐    ┌──────────────┐ │
│  │   Laravel App   │    │   Node.js WS    │    │   Database   │ │
│  │ mechamap.com    │◄──►│ realtime.       │◄──►│   MySQL      │ │
│  │                 │    │ mechamap.com    │    │              │ │
│  └─────────────────┘    └─────────────────┘    └──────────────┘ │
│           │                       │                     │       │
│           │              ┌─────────────────┐           │       │
│           └─────────────►│   Redis Cache   │◄──────────┘       │
│                          │   Pub/Sub       │                   │
│                          └─────────────────┘                   │
│                                                                 │
│  ┌─────────────────────────────────────────────────────────────┤
│  │                    Client Connections                       │
│  │                                                             │
│  │  Browser ◄──WSS──► Load Balancer ◄──WSS──► Node.js Cluster │
│  │  Mobile  ◄──WSS──►    (nginx)    ◄──WSS──►   (PM2/Docker)  │
│  │                                                             │
│  └─────────────────────────────────────────────────────────────┘
└─────────────────────────────────────────────────────────────────┘
```

## 2. DOMAIN & SSL CONFIGURATION

### Domain Setup
- **Primary**: `https://mechamap.com` (Laravel)
- **WebSocket**: `https://realtime.mechamap.com` (Node.js)
- **API**: `https://api.mechamap.com` (Laravel API)

### SSL Certificate Strategy
```bash
# Let's Encrypt SSL for realtime subdomain
certbot certonly --dns-cloudflare \
  --dns-cloudflare-credentials ~/.secrets/cloudflare.ini \
  -d realtime.mechamap.com

# Certificate paths
SSL_CERT=/etc/letsencrypt/live/realtime.mechamap.com/fullchain.pem
SSL_KEY=/etc/letsencrypt/live/realtime.mechamap.com/privkey.pem
```

## 3. NODE.JS SERVER ARCHITECTURE

### Core Components
```
realtime-server/
├── src/
│   ├── server.js              # Main server entry point
│   ├── websocket/
│   │   ├── socketManager.js   # WebSocket connection management
│   │   ├── channelManager.js  # Channel subscription logic
│   │   └── eventHandlers.js   # Event processing
│   ├── auth/
│   │   ├── jwtAuth.js         # JWT token validation
│   │   └── channelAuth.js     # Private channel authorization
│   ├── integrations/
│   │   ├── laravelApi.js      # Laravel backend communication
│   │   ├── redisClient.js     # Redis pub/sub integration
│   │   └── database.js        # Direct database access
│   ├── middleware/
│   │   ├── cors.js            # CORS handling
│   │   ├── rateLimit.js       # Rate limiting
│   │   └── logging.js         # Request/connection logging
│   └── utils/
│       ├── logger.js          # Structured logging
│       ├── metrics.js         # Performance metrics
│       └── health.js          # Health check endpoints
├── config/
│   ├── production.js          # Production configuration
│   ├── development.js         # Development configuration
│   └── ssl/                   # SSL certificates
├── tests/
│   ├── integration/           # Integration tests
│   ├── unit/                  # Unit tests
│   └── load/                  # Load testing
├── docker/
│   ├── Dockerfile             # Container definition
│   └── docker-compose.yml     # Multi-service setup
├── deployment/
│   ├── nginx.conf             # Reverse proxy config
│   ├── pm2.config.js          # Process management
│   └── kubernetes/            # K8s deployment files
└── package.json
```

## 4. TECHNOLOGY STACK

### Core Dependencies
```json
{
  "dependencies": {
    "socket.io": "^4.7.5",           // WebSocket server
    "express": "^4.18.2",           // HTTP server
    "jsonwebtoken": "^9.0.2",       // JWT authentication
    "redis": "^4.6.10",             // Redis client
    "mysql2": "^3.6.5",             // MySQL database
    "cors": "^2.8.5",               // CORS middleware
    "helmet": "^7.1.0",             // Security headers
    "express-rate-limit": "^7.1.5", // Rate limiting
    "winston": "^3.11.0",           // Logging
    "dotenv": "^16.3.1",            // Environment config
    "joi": "^17.11.0"               // Input validation
  },
  "devDependencies": {
    "jest": "^29.7.0",              // Testing framework
    "supertest": "^6.3.3",         // HTTP testing
    "socket.io-client": "^4.7.5",  // WebSocket testing
    "artillery": "^2.0.3",         // Load testing
    "nodemon": "^3.0.2"            // Development server
  }
}
```

### Production Tools
- **Process Manager**: PM2 với clustering
- **Load Balancer**: nginx với WebSocket support
- **Monitoring**: Prometheus + Grafana
- **Logging**: Winston + ELK Stack
- **Container**: Docker với multi-stage builds

## 5. AUTHENTICATION & AUTHORIZATION

### JWT Token Flow
```javascript
// 1. Laravel generates JWT token for user
const token = jwt.sign({
  userId: user.id,
  role: user.role,
  permissions: user.permissions,
  exp: Math.floor(Date.now() / 1000) + (60 * 60) // 1 hour
}, process.env.JWT_SECRET);

// 2. Client connects with token
const socket = io('wss://realtime.mechamap.com', {
  auth: { token: userToken }
});

// 3. Server validates token
socket.use((socket, next) => {
  const token = socket.handshake.auth.token;
  try {
    const decoded = jwt.verify(token, process.env.JWT_SECRET);
    socket.userId = decoded.userId;
    socket.userRole = decoded.role;
    next();
  } catch (err) {
    next(new Error('Authentication failed'));
  }
});
```

### Private Channel Authorization
```javascript
// Channel naming convention
const channels = {
  public: 'public.announcements',
  private: `private-user.${userId}`,
  presence: `presence-thread.${threadId}`
};

// Authorization middleware
const authorizeChannel = (socket, channel) => {
  if (channel.startsWith('private-user.')) {
    const targetUserId = channel.split('.')[1];
    return socket.userId === parseInt(targetUserId);
  }
  
  if (channel.startsWith('presence-thread.')) {
    const threadId = channel.split('.')[1];
    return checkThreadAccess(socket.userId, threadId);
  }
  
  return true; // Public channels
};
```

## 6. PERFORMANCE OPTIMIZATIONS

### Connection Management
```javascript
// Connection pooling và clustering
const cluster = require('cluster');
const numCPUs = require('os').cpus().length;

if (cluster.isMaster) {
  // Fork workers
  for (let i = 0; i < numCPUs; i++) {
    cluster.fork();
  }
} else {
  // Worker process
  const server = createWebSocketServer();
  server.listen(process.env.PORT || 3000);
}
```

### Memory Management
```javascript
// Connection limits và cleanup
const connectionLimits = {
  maxConnections: 10000,
  maxConnectionsPerUser: 5,
  connectionTimeout: 30000,
  heartbeatInterval: 25000
};

// Automatic cleanup
setInterval(() => {
  cleanupStaleConnections();
  logConnectionMetrics();
}, 60000);
```

### Caching Strategy
```javascript
// Redis caching cho user data
const userCache = {
  get: async (userId) => {
    const cached = await redis.get(`user:${userId}`);
    if (cached) return JSON.parse(cached);
    
    const user = await database.getUser(userId);
    await redis.setex(`user:${userId}`, 300, JSON.stringify(user));
    return user;
  }
};
```

## 12. TROUBLESHOOTING & RECENT FIXES

### 12.1 Common Issues & Solutions

#### **Issue 1: "Undefined variable $configJson" (FIXED - 2025-07-19)**
```
Error: Undefined variable $configJson in websocket-config.blade.php
```

**Root Cause:** Laravel component property not set in constructor

**Solution Applied:**
```php
// Fixed in app/View/Components/WebSocketConfig.php
public function __construct($autoInit = true) {
    // ... existing code ...
    $this->configJson = $this->generateConfigJson();
}

private function generateConfigJson(): string {
    // Robust config generation with fallbacks
}
```

#### **Issue 2: "TRANSPORT_HANDSHAKE_ERROR" (FIXED - 2025-07-19)**
```
Error: WebSocket connection failed with Bad request
```

**Root Cause:** Nginx missing WebSocket support headers

**Solution Applied:**
```nginx
# Added to nginx config
location / {
    include proxy_params;
    proxy_pass http://realtime.mechamap.com;

    # WebSocket support
    proxy_http_version 1.1;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection "upgrade";
    proxy_set_header Host $host;
    proxy_cache_bypass $http_upgrade;
}
```

#### **Issue 3: Authentication Middleware Not Called**
**Root Cause:** Token passed via query parameter, not auth headers

**Solution Applied:**
```javascript
// Fixed in src/middleware/auth.js
const token = socket.handshake.auth.token ||
              socket.handshake.headers.authorization?.replace("Bearer ", "") ||
              socket.handshake.query.token; // Added this line
```

### 12.2 Deployment Verification

#### **Quick Health Check**
```bash
# 1. Check Laravel component
php artisan tinker --execute="echo (new App\View\Components\WebSocketConfig())->configJson();"

# 2. Check Nginx WebSocket headers
curl -I -H "Connection: Upgrade" -H "Upgrade: websocket" https://realtime.mechamap.com/

# 3. Check realtime server
pm2 status
pm2 logs --lines 10

# 4. Test WebSocket connection
node test-websocket.js
```

#### **Expected Results**
```
✅ Laravel Component: Valid JSON config output
✅ Nginx: HTTP/1.1 101 Switching Protocols
✅ Realtime Server: Online status, no errors
✅ WebSocket Test: Connection successful
```

### 12.3 Monitoring & Logs

#### **Log Locations**
```bash
# Laravel logs
tail -f storage/logs/laravel.log

# Realtime server logs
pm2 logs mechamap-realtime

# Nginx logs
tail -f /var/log/nginx/realtime.mechamap.com.access.log
tail -f /var/log/nginx/realtime.mechamap.com.error.log
```

#### **Key Metrics to Monitor**
- WebSocket connection success rate
- Authentication success rate
- Memory usage (realtime server)
- Response times
- Error rates

### 12.4 Performance Optimization

#### **Current Status (2025-07-19)**
- ✅ WebSocket connections: Working
- ✅ Authentication: Sanctum token validation
- ✅ Real-time notifications: Functional
- ✅ Error handling: Comprehensive
- ✅ Fallback mechanisms: Implemented

#### **Future Improvements**
- [ ] Connection pooling optimization
- [ ] Redis cluster setup
- [ ] Load balancer configuration
- [ ] Monitoring dashboard
- [ ] Automated health checks

---

**Last Updated:** 2025-07-19
**Status:** ✅ Production Ready
**Next Review:** 2025-08-19
