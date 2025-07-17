# Node.js WebSocket Server Deployment Guide

## 1. INFRASTRUCTURE SETUP

### Domain Configuration
```bash
# DNS Records (Cloudflare/Route53)
realtime.mechamap.com    A     YOUR_SERVER_IP
*.realtime.mechamap.com  A     YOUR_SERVER_IP  # For load balancing
```

### SSL Certificate Setup
```bash
# Install Certbot
sudo apt update
sudo apt install certbot python3-certbot-nginx

# Generate SSL certificate
sudo certbot certonly --nginx \
  -d realtime.mechamap.com \
  --email admin@mechamap.com \
  --agree-tos \
  --non-interactive

# Auto-renewal setup
sudo crontab -e
# Add: 0 12 * * * /usr/bin/certbot renew --quiet
```

## 2. SERVER CONFIGURATION

### Nginx Reverse Proxy
```nginx
# /etc/nginx/sites-available/realtime.mechamap.com
upstream nodejs_backend {
    least_conn;
    server 127.0.0.1:3000;
    server 127.0.0.1:3001;
    server 127.0.0.1:3002;
    server 127.0.0.1:3003;
}

server {
    listen 80;
    server_name realtime.mechamap.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name realtime.mechamap.com;

    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/realtime.mechamap.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/realtime.mechamap.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512;
    ssl_prefer_server_ciphers off;

    # Security Headers
    add_header Strict-Transport-Security "max-age=63072000" always;
    add_header X-Frame-Options DENY;
    add_header X-Content-Type-Options nosniff;

    # WebSocket Configuration
    location / {
        proxy_pass http://nodejs_backend;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        
        # WebSocket specific
        proxy_cache_bypass $http_upgrade;
        proxy_read_timeout 86400;
        proxy_send_timeout 86400;
    }

    # Health check endpoint
    location /health {
        proxy_pass http://nodejs_backend;
        access_log off;
    }

    # Metrics endpoint (restrict access)
    location /metrics {
        allow 127.0.0.1;
        allow YOUR_MONITORING_IP;
        deny all;
        proxy_pass http://nodejs_backend;
    }
}
```

### PM2 Configuration
```javascript
// pm2.config.js
module.exports = {
  apps: [
    {
      name: 'mechamap-realtime',
      script: './src/app.js',
      instances: 4, // Number of CPU cores
      exec_mode: 'cluster',
      env: {
        NODE_ENV: 'production',
        PORT: 3000
      },
      env_production: {
        NODE_ENV: 'production',
        PORT: 3000
      },
      // Logging
      log_file: './logs/combined.log',
      out_file: './logs/out.log',
      error_file: './logs/error.log',
      log_date_format: 'YYYY-MM-DD HH:mm:ss Z',
      
      // Process management
      max_memory_restart: '1G',
      restart_delay: 4000,
      max_restarts: 10,
      min_uptime: '10s',
      
      // Monitoring
      monitoring: false,
      pmx: true
    }
  ]
};
```

### Environment Configuration (.env.production)
```bash
# Server Configuration
NODE_ENV=production
PORT=3000
HOST=0.0.0.0

# SSL Configuration
SSL_CERT_PATH=/etc/letsencrypt/live/realtime.mechamap.com/fullchain.pem
SSL_KEY_PATH=/etc/letsencrypt/live/realtime.mechamap.com/privkey.pem

# Database Configuration
DB_HOST=localhost
DB_PORT=3306
DB_NAME=mechamap_production
DB_USER=mechamap_user
DB_PASSWORD=secure_password

# Redis Configuration
REDIS_HOST=localhost
REDIS_PORT=6379
REDIS_PASSWORD=redis_password
REDIS_DB=0

# JWT Configuration
JWT_SECRET=your_super_secure_jwt_secret_key_here
JWT_EXPIRES_IN=1h

# Laravel Integration
LARAVEL_API_URL=https://api.mechamap.com
LARAVEL_API_KEY=your_laravel_api_key

# CORS Configuration
ALLOWED_ORIGINS=https://mechamap.com,https://www.mechamap.com

# Rate Limiting
RATE_LIMIT_WINDOW_MS=60000
RATE_LIMIT_MAX_REQUESTS=100

# Logging
LOG_LEVEL=info
LOG_FILE=./logs/app.log

# Monitoring
METRICS_ENABLED=true
HEALTH_CHECK_INTERVAL=30000

# Performance
MAX_CONNECTIONS=10000
CONNECTION_TIMEOUT=30000
HEARTBEAT_INTERVAL=25000
```

## 3. DOCKER DEPLOYMENT

### Dockerfile
```dockerfile
FROM node:18-alpine AS builder

WORKDIR /app
COPY package*.json ./
RUN npm ci --only=production

FROM node:18-alpine AS runtime

# Security: Create non-root user
RUN addgroup -g 1001 -S nodejs
RUN adduser -S nodejs -u 1001

WORKDIR /app

# Copy dependencies
COPY --from=builder /app/node_modules ./node_modules
COPY --chown=nodejs:nodejs . .

# Create logs directory
RUN mkdir -p logs && chown nodejs:nodejs logs

USER nodejs

EXPOSE 3000

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
  CMD node healthcheck.js

CMD ["node", "src/app.js"]
```

### Docker Compose
```yaml
version: '3.8'

services:
  realtime-server:
    build: .
    restart: unless-stopped
    ports:
      - "3000-3003:3000"
    environment:
      - NODE_ENV=production
      - DATABASE_URL=${DATABASE_URL}
      - REDIS_URL=${REDIS_URL}
      - JWT_SECRET=${JWT_SECRET}
    volumes:
      - ./logs:/app/logs
      - ./ssl:/app/ssl:ro
    depends_on:
      - redis
    deploy:
      replicas: 4
      resources:
        limits:
          memory: 1G
        reservations:
          memory: 512M

  redis:
    image: redis:7-alpine
    restart: unless-stopped
    command: redis-server --appendonly yes
    volumes:
      - redis_data:/data
    ports:
      - "6379:6379"

  nginx:
    image: nginx:alpine
    restart: unless-stopped
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./nginx.conf:/etc/nginx/conf.d/default.conf:ro
      - /etc/letsencrypt:/etc/letsencrypt:ro
    depends_on:
      - realtime-server

volumes:
  redis_data:
```

## 4. DEPLOYMENT SCRIPT

```bash
#!/bin/bash
# deploy.sh

set -e

echo "🚀 Deploying MechaMap Realtime Server..."

# Variables
APP_DIR="/var/www/mechamap-realtime"
BACKUP_DIR="/var/backups/mechamap-realtime"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)

# Create backup
echo "📦 Creating backup..."
sudo mkdir -p $BACKUP_DIR
sudo tar -czf $BACKUP_DIR/backup_$TIMESTAMP.tar.gz -C $APP_DIR .

# Pull latest code
echo "📥 Pulling latest code..."
cd $APP_DIR
git pull origin main

# Install dependencies
echo "📦 Installing dependencies..."
npm ci --production

# Run tests
echo "🧪 Running tests..."
npm test

# Update SSL certificates if needed
echo "🔒 Checking SSL certificates..."
sudo certbot renew --quiet

# Restart services
echo "🔄 Restarting services..."
pm2 reload pm2.config.js
sudo systemctl reload nginx

# Health check
echo "🏥 Performing health check..."
sleep 10
curl -f https://realtime.mechamap.com/health || {
  echo "❌ Health check failed! Rolling back..."
  pm2 reload pm2.config.js
  exit 1
}

echo "✅ Deployment completed successfully!"
```
