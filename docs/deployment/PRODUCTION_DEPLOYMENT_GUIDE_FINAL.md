# MechaMap Production Deployment Guide - FINAL

Hướng dẫn deploy MechaMap với cấu hình production đã được kiểm tra và sửa lỗi.

## 🎯 **PRODUCTION ARCHITECTURE**

```
Frontend: https://mechamap.com (Hosting)
    ↓ WebSocket Connection
Realtime: https://realtime.mechamap.com:443 (VPS)
    ↓ API Calls
Backend: https://mechamap.com/api (Hosting)
```

## ✅ **CONFIGURATION STATUS**

### **✅ Laravel Configuration (FIXED)**
```bash
APP_ENV=production
APP_URL=https://mechamap.com
WEBSOCKET_SERVER_URL=https://realtime.mechamap.com
WEBSOCKET_SERVER_HOST=realtime.mechamap.com
WEBSOCKET_SERVER_PORT=443
WEBSOCKET_SERVER_SECURE=true
BROADCAST_CONNECTION=nodejs
NODEJS_BROADCAST_URL=https://realtime.mechamap.com
```

### **✅ Realtime Server Configuration (FIXED)**
```bash
NODE_ENV=production
PORT=443
HOST=0.0.0.0
SSL_ENABLED=true
SSL_CERT_PATH=/etc/letsencrypt/live/realtime.mechamap.com/fullchain.pem
SSL_KEY_PATH=/etc/letsencrypt/live/realtime.mechamap.com/privkey.pem
LARAVEL_API_URL=https://mechamap.com
CORS_ORIGIN=https://mechamap.com,https://www.mechamap.com
```

### **✅ JWT Synchronization (VERIFIED)**
```bash
JWT_SECRET=cc779c53b425a9c6efab2e9def898a025bc077dec144726be95bd50916345e02d2535935490f7c047506c7ae494d5d4372d38189a5c4d8922a326d79090ae744
```

## 🚀 **DEPLOYMENT STEPS**

### **Step 1: Deploy Realtime Server to VPS**

```bash
# 1. Upload realtime-server folder to VPS
scp -r realtime-server/ user@your-vps:/var/www/

# 2. Install dependencies on VPS
cd /var/www/realtime-server
npm install --omit=dev

# 3. Setup SSL certificates (Let's Encrypt)
sudo certbot certonly --nginx -d realtime.mechamap.com

# 4. Copy production config
cp .env.production .env

# 5. Start with PM2
pm2 start src/app.js --name mechamap-realtime
pm2 startup
pm2 save

# 6. Configure Nginx reverse proxy
sudo nano /etc/nginx/sites-available/realtime.mechamap.com
```

**Nginx Configuration for Realtime Server:**
```nginx
server {
    listen 80;
    server_name realtime.mechamap.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name realtime.mechamap.com;

    ssl_certificate /etc/letsencrypt/live/realtime.mechamap.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/realtime.mechamap.com/privkey.pem;

    location / {
        proxy_pass http://localhost:443;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_cache_bypass $http_upgrade;
    }
}
```

### **Step 2: Deploy Laravel to Hosting**

```bash
# 1. Upload Laravel files to hosting
# Upload all files to public_html/ or domain folder

# 2. Copy production config
cp .env.production .env

# 3. Update database credentials in .env
DB_HOST=your_hosting_db_host
DB_DATABASE=your_production_db_name
DB_USERNAME=your_db_username
DB_PASSWORD=your_db_password

# 4. Update email settings
MAIL_HOST=your_hosting_smtp
MAIL_USERNAME=noreply@mechamap.com
MAIL_PASSWORD=your_email_password

# 5. Install dependencies
composer install --no-dev --optimize-autoloader

# 6. Run migrations
php artisan migrate --force

# 7. Setup required tables
php artisan session:table
php artisan queue:table
php artisan queue:failed-table
php artisan migrate --force

# 8. Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 9. Set permissions
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
chmod -R 775 public/images/
```

### **Step 3: DNS Configuration**

```bash
# Add DNS records
realtime.mechamap.com A YOUR_VPS_IP
mechamap.com A YOUR_HOSTING_IP
www.mechamap.com CNAME mechamap.com
```

### **Step 4: SSL Certificates**

```bash
# For Realtime Server (VPS)
sudo certbot certonly --nginx -d realtime.mechamap.com

# For Main Site (Hosting)
# Usually managed by hosting provider automatically
```

## 🧪 **TESTING PRODUCTION SETUP**

### **Test 1: Realtime Server Health**
```bash
curl -I https://realtime.mechamap.com/health
# Expected: HTTP/1.1 200 OK
```

### **Test 2: WebSocket Connection**
```javascript
// In browser console at https://mechamap.com
console.log('Testing WebSocket connection...');
// Should see: "MechaMap WebSocket: Connecting to https://realtime.mechamap.com"
// Should see: "WebSocket connected successfully"
```

### **Test 3: CORS Headers**
```bash
curl -H "Origin: https://mechamap.com" -I https://realtime.mechamap.com
# Should include: Access-Control-Allow-Origin: https://mechamap.com
```

## 🔧 **TROUBLESHOOTING**

### **Issue 1: WebSocket Connection Failed**
```bash
# Check realtime server logs
pm2 logs mechamap-realtime

# Check SSL certificates
sudo certbot certificates

# Test direct connection
telnet realtime.mechamap.com 443
```

### **Issue 2: CORS Errors**
```bash
# Update CORS in realtime server
CORS_ORIGIN=https://mechamap.com,https://www.mechamap.com

# Restart realtime server
pm2 restart mechamap-realtime
```

### **Issue 3: SSL Certificate Issues**
```bash
# Renew certificates
sudo certbot renew

# Check certificate validity
openssl x509 -in /etc/letsencrypt/live/realtime.mechamap.com/fullchain.pem -text -noout
```

## 📊 **MONITORING**

### **Realtime Server Monitoring**
```bash
# Check server status
pm2 status

# Monitor logs
pm2 logs mechamap-realtime --lines 100

# Monitor resources
pm2 monit
```

### **Laravel Monitoring**
```bash
# Check Laravel logs
tail -f storage/logs/laravel.log

# Check queue workers
php artisan queue:work --once
```

## 🎉 **DEPLOYMENT CHECKLIST**

- [ ] ✅ Realtime server deployed to VPS
- [ ] ✅ SSL certificates configured for realtime.mechamap.com
- [ ] ✅ Laravel deployed to hosting
- [ ] ✅ SSL certificates configured for mechamap.com
- [ ] ✅ DNS records configured
- [ ] ✅ Database credentials updated
- [ ] ✅ Email settings configured
- [ ] ✅ WebSocket connection tested
- [ ] ✅ CORS headers verified
- [ ] ✅ Real-time notifications working

## 🔐 **SECURITY NOTES**

1. **API Keys**: Update all production API keys
2. **Database**: Use strong passwords
3. **SSL**: Ensure all connections use HTTPS
4. **CORS**: Restrict to production domains only
5. **Firewall**: Configure VPS firewall properly

## 📞 **SUPPORT**

If deployment fails:
1. Check server logs
2. Verify DNS propagation
3. Test SSL certificates
4. Confirm WebSocket connectivity
5. Monitor browser console for errors

**Production deployment is now ready with all configurations verified!** 🚀
