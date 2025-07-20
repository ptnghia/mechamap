# MechaMap Realtime Server - FastPanel Deployment Guide

🚀 **Production deployment guide for VPS with FastPanel**

## 🎯 **Architecture Overview**

```
Internet
    ↓ HTTPS:443
FastPanel Proxy (realtime.mechamap.com)
    ↓ HTTP:3000
Realtime Server (localhost:3000)

Internet
    ↓ HTTPS:443
FastPanel Proxy (mechamap.com)
    ↓ HTTP:80/8080
Laravel Backend (localhost)
```

## ✅ **Why This Setup is PERFECT**

### **🔒 Security Benefits**
- **Realtime server không expose trực tiếp** - chỉ qua proxy
- **SSL termination tại FastPanel** - tự động quản lý certificates
- **Firewall protection** - chỉ port 80/443 open ra ngoài
- **Internal communication** - Laravel ↔ Realtime qua localhost

### **⚡ Performance Benefits**
- **Same VPS** - ultra-low latency giữa Laravel và Realtime
- **FastPanel optimization** - built-in caching và compression
- **Direct proxy** - minimal overhead
- **WebSocket efficiency** - optimized proxy configuration

### **🛠️ Management Benefits**
- **FastPanel UI** - easy SSL, domain, proxy management
- **Auto SSL renewal** - Let's Encrypt integration
- **Monitoring built-in** - server metrics và logs
- **Backup integration** - automated backups

## 🔧 **Realtime Server Configuration**

### **Update .env.production**

```bash
# =============================================================================
# MechaMap Realtime Server - FastPanel Production Configuration
# =============================================================================

# Server Configuration - IMPORTANT: No SSL, FastPanel handles it
NODE_ENV=production
PORT=3000
HOST=127.0.0.1  # Only listen on localhost

# SSL Configuration - DISABLED (FastPanel handles SSL)
SSL_ENABLED=false

# Proxy Configuration - IMPORTANT for FastPanel
TRUST_PROXY=true
PROXY_TIMEOUT=300000

# Database Configuration
DB_HOST=localhost
DB_PORT=3306
DB_NAME=mechamap_production
DB_USER=mechamap_user
DB_PASSWORD=YOUR_SECURE_PASSWORD

# Laravel Integration
LARAVEL_API_URL=https://mechamap.com
LARAVEL_API_KEY=mechamap_ws_kCTy45s4obktB6IJJH6DpKHzoveEJLgrnmbST8fxwufexn0u80RnqMSO51ubWVQ3

# CORS Configuration - Allow your domains
CORS_ORIGIN=https://mechamap.com,https://www.mechamap.com
CORS_CREDENTIALS=true

# WebSocket Configuration - Optimized for proxy
WS_PING_TIMEOUT=60000
WS_PING_INTERVAL=25000
WS_UPGRADE_TIMEOUT=10000
WS_TRANSPORTS=websocket,polling

# Connection Limits
MAX_CONNECTIONS=5000
MAX_CONNECTIONS_PER_USER=5
CONNECTION_TIMEOUT=30000

# Logging - Production level
LOG_LEVEL=info
LOG_FILE=./logs/app.log

# Security
HELMET_ENABLED=true
COMPRESSION_ENABLED=true

# JWT Configuration
JWT_SECRET=cc779c53b425a9c6efab2e9def898a025bc077dec144726be95bd50916345e02d2535935490f7c047506c7ae494d5d4372d38189a5c4d8922a326d79090ae744
```

## 🌐 **FastPanel Configuration**

### **1. Domain Setup**

#### **realtime.mechamap.com**
```
Domain: realtime.mechamap.com
Document Root: /var/www/mechamap-realtime
SSL: Enabled (Let's Encrypt)
Proxy: Enabled
Proxy Target: http://127.0.0.1:3000
WebSocket: Enabled
```

#### **mechamap.com**
```
Domain: mechamap.com
Document Root: /var/www/mechamap-laravel/public
SSL: Enabled (Let's Encrypt)
PHP Version: 8.2+
```

### **2. Proxy Configuration for realtime.mechamap.com**

**FastPanel Proxy Settings:**
```nginx
# Proxy configuration (FastPanel generates this)
location / {
    proxy_pass http://127.0.0.1:3000;
    proxy_http_version 1.1;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection 'upgrade';
    proxy_set_header Host $host;
    proxy_set_header X-Real-IP $remote_addr;
    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    proxy_set_header X-Forwarded-Proto $scheme;
    proxy_cache_bypass $http_upgrade;
    proxy_read_timeout 300;
    proxy_connect_timeout 300;
    proxy_send_timeout 300;
}
```

### **3. SSL Configuration**

FastPanel tự động cấu hình SSL với Let's Encrypt:
- **Auto-renewal**: Enabled
- **Force HTTPS**: Enabled
- **HSTS**: Enabled
- **Certificate**: Let's Encrypt

## 🚀 **Deployment Steps**

### **Step 1: Prepare VPS**

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install Node.js (if not already installed)
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Install PM2
sudo npm install -g pm2
```

### **Step 2: Deploy Realtime Server**

```bash
# Create directory
sudo mkdir -p /var/www/mechamap-realtime
sudo chown $USER:$USER /var/www/mechamap-realtime

# Clone and setup
cd /var/www/mechamap-realtime
git clone https://github.com/your-repo/mechamap-realtime.git .
npm install --omit=dev

# Copy production config
cp .env.production .env

# Start with PM2
pm2 start ecosystem.config.js --env production
pm2 startup
pm2 save
```

### **Step 3: Configure FastPanel**

1. **Add Domain**: `realtime.mechamap.com`
2. **Enable SSL**: Let's Encrypt
3. **Configure Proxy**: 
   - Target: `http://127.0.0.1:3000`
   - Enable WebSocket support
   - Set timeouts: 300 seconds
4. **Test Configuration**

### **Step 4: Configure Laravel**

**Update Laravel .env:**
```bash
# WebSocket Configuration
WEBSOCKET_SERVER_URL=https://realtime.mechamap.com
WEBSOCKET_SERVER_HOST=realtime.mechamap.com
WEBSOCKET_SERVER_PORT=443
WEBSOCKET_SERVER_SECURE=true

# Broadcasting
BROADCAST_CONNECTION=nodejs
NODEJS_BROADCAST_URL=https://realtime.mechamap.com
```

## 🧪 **Testing & Verification**

### **1. Test Realtime Server**

```bash
# Check if server is running
pm2 status

# Test local connection
curl http://127.0.0.1:3000/health

# Test through proxy
curl https://realtime.mechamap.com/health
```

### **2. Test WebSocket Connection**

```javascript
// In browser console at https://mechamap.com
const socket = io('https://realtime.mechamap.com');
socket.on('connect', () => {
    console.log('✅ WebSocket connected successfully!');
});
```

### **3. Test CORS**

```bash
curl -H "Origin: https://mechamap.com" -I https://realtime.mechamap.com
# Should return: Access-Control-Allow-Origin: https://mechamap.com
```

## 📊 **Monitoring & Maintenance**

### **FastPanel Monitoring**
- **Server Resources**: CPU, RAM, Disk usage
- **Domain Status**: SSL certificate expiry
- **Proxy Status**: Connection health
- **Logs**: Access và error logs

### **PM2 Monitoring**
```bash
# Check status
pm2 status

# View logs
pm2 logs mechamap-realtime

# Monitor resources
pm2 monit

# Restart if needed
pm2 restart mechamap-realtime
```

### **Health Checks**
```bash
# Automated health check script
#!/bin/bash
HEALTH_URL="https://realtime.mechamap.com/health"
RESPONSE=$(curl -s -o /dev/null -w "%{http_code}" $HEALTH_URL)

if [ $RESPONSE -eq 200 ]; then
    echo "✅ Realtime server is healthy"
else
    echo "❌ Realtime server is down - restarting..."
    pm2 restart mechamap-realtime
fi
```

## 🔧 **Troubleshooting**

### **Common Issues**

1. **WebSocket Connection Failed**
   ```bash
   # Check proxy configuration
   # Verify WebSocket support is enabled in FastPanel
   # Check firewall settings
   ```

2. **SSL Certificate Issues**
   ```bash
   # Renew certificate in FastPanel
   # Check domain DNS settings
   # Verify certificate installation
   ```

3. **CORS Errors**
   ```bash
   # Check CORS_ORIGIN in realtime server .env
   # Verify domain spelling
   # Check proxy headers
   ```

### **Debug Commands**

```bash
# Check if port 3000 is listening
sudo netstat -tlnp | grep :3000

# Check FastPanel proxy logs
sudo tail -f /var/log/nginx/access.log
sudo tail -f /var/log/nginx/error.log

# Check realtime server logs
pm2 logs mechamap-realtime --lines 100
```

## ✅ **Production Checklist**

- [ ] ✅ Realtime server deployed to `/var/www/mechamap-realtime`
- [ ] ✅ PM2 configured and auto-start enabled
- [ ] ✅ FastPanel domain `realtime.mechamap.com` configured
- [ ] ✅ SSL certificate installed and auto-renewal enabled
- [ ] ✅ Proxy configuration with WebSocket support
- [ ] ✅ Laravel backend configured with production URLs
- [ ] ✅ CORS configuration verified
- [ ] ✅ Health checks passing
- [ ] ✅ WebSocket connections tested
- [ ] ✅ Monitoring setup completed

## 🎉 **Advantages of This Setup**

### **🔒 Security**
- Realtime server không accessible từ internet trực tiếp
- SSL termination tại proxy level
- Firewall protection
- Secure internal communication

### **⚡ Performance**
- Same VPS = ultra-low latency
- FastPanel optimization
- Efficient proxy configuration
- WebSocket connection pooling

### **🛠️ Management**
- FastPanel UI for easy management
- Automated SSL certificate renewal
- Built-in monitoring và logging
- Easy backup và restore

### **💰 Cost Effective**
- Single VPS cho cả Laravel và Realtime
- No additional infrastructure needed
- FastPanel license covers everything
- Efficient resource utilization

**Setup này là production-grade và rất được recommend cho MechaMap platform!** 🚀
