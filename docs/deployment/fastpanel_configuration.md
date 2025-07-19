# FastPanel Configuration Guide for MechaMap

## 🚀 Overview

This guide covers the FastPanel configuration for MechaMap production deployment on a single VPS with:
- **Main Application**: https://mechamap.com (Laravel)
- **Realtime Server**: https://realtime.mechamap.com (Node.js → 127.0.0.1:3000)

## 📋 Prerequisites

- VPS with FastPanel installed and configured
- Domain DNS records pointing to VPS IP:
  - `mechamap.com` → VPS IP
  - `realtime.mechamap.com` → VPS IP

## 🔧 FastPanel Configuration Steps

### 1. Main Laravel Application (mechamap.com)

#### 1.1 Create Website in FastPanel
1. **Login to FastPanel** → Websites → Add Website
2. **Domain**: `mechamap.com`
3. **Document Root**: `/var/www/mechamap/public`
4. **PHP Version**: 8.2
5. **Enable SSL**: Yes (Let's Encrypt)

#### 1.2 PHP Configuration
```ini
# In FastPanel → Websites → mechamap.com → PHP Settings
memory_limit = 512M
max_execution_time = 300
upload_max_filesize = 10M
post_max_size = 10M
```

#### 1.3 Database Configuration
1. **FastPanel** → Databases → Create Database
2. **Database Name**: `mechamap_db`
3. **Username**: `mechamap_user`
4. **Password**: `YPF1Dt5JyTgxJ95R`
5. **Restore Database**: Upload and restore `data_v2_fixed.sql`
   ```bash
   mysql -u mechamap_user -p'YPF1Dt5JyTgxJ95R' mechamap_db < data_v2_fixed.sql
   ```

#### 1.4 Environment Configuration
1. **Copy template**: `cp .env.production.template .env.production`
2. **Configure secrets**: Update the following values in `.env.production`:
   - `GOOGLE_CLIENT_ID` and `GOOGLE_CLIENT_SECRET`
   - `FACEBOOK_CLIENT_ID` and `FACEBOOK_CLIENT_SECRET`
   - `STRIPE_KEY`, `STRIPE_SECRET`, and `STRIPE_WEBHOOK_SECRET`
   - `STRIPE_ADMIN_ACCOUNT_ID`
   - Other API keys as needed

### 2. Realtime Server (realtime.mechamap.com)

#### 2.1 Create Reverse Proxy in FastPanel
1. **FastPanel** → Websites → Add Website
2. **Domain**: `realtime.mechamap.com`
3. **Type**: Reverse Proxy
4. **Target URL**: `http://127.0.0.1:3000`
5. **Enable SSL**: Yes (Let's Encrypt)

#### 2.2 Proxy Configuration
```nginx
# FastPanel will generate this automatically, but verify these settings:
proxy_pass http://127.0.0.1:3000;
proxy_http_version 1.1;
proxy_set_header Upgrade $http_upgrade;
proxy_set_header Connection 'upgrade';
proxy_set_header Host $host;
proxy_set_header X-Real-IP $remote_addr;
proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
proxy_set_header X-Forwarded-Proto $scheme;
proxy_cache_bypass $http_upgrade;
proxy_read_timeout 86400;
```

#### 2.3 WebSocket Support
- ✅ **Enable WebSocket** in FastPanel proxy settings
- ✅ **Connection Upgrade** headers automatically configured
- ✅ **Long-lived connections** supported

### 3. SSL Certificate Configuration

#### 3.1 mechamap.com SSL
1. **FastPanel** → Websites → mechamap.com → SSL
2. **Certificate Type**: Let's Encrypt
3. **Auto-renewal**: Enabled
4. **Force HTTPS**: Yes

#### 3.2 realtime.mechamap.com SSL
1. **FastPanel** → Websites → realtime.mechamap.com → SSL
2. **Certificate Type**: Let's Encrypt
3. **Auto-renewal**: Enabled
4. **Force HTTPS**: Yes

## 🔒 Security Configuration

### 1. Firewall Rules (FastPanel)
```bash
# Allow only necessary ports
- Port 22 (SSH) - Restricted to admin IPs
- Port 80 (HTTP) - Redirect to HTTPS
- Port 443 (HTTPS) - Public access
- Port 3000 - BLOCKED from external access (only localhost)
```

### 2. FastPanel Security Settings
1. **Enable Fail2Ban** for SSH protection
2. **Configure ModSecurity** for web application firewall
3. **Enable DDoS protection**
4. **Set up automatic security updates**

## 📊 Monitoring & Maintenance

### 1. FastPanel Monitoring
- **System Resources**: CPU, RAM, Disk usage
- **Website Status**: Uptime monitoring
- **SSL Certificate**: Expiry alerts
- **Database**: Performance metrics

### 2. Application Monitoring
- **Laravel Logs**: `/var/www/mechamap/storage/logs/`
- **Realtime Server**: PM2 monitoring
- **Health Checks**:
  - https://mechamap.com/api/health
  - https://realtime.mechamap.com/api/health

## 🚀 Deployment Process

### 1. Initial Setup
```bash
# 1. Upload code to VPS
cd /var/www
git clone https://github.com/your-repo/mechamap.git
chown -R www-data:www-data mechamap

# 2. Deploy Laravel
cd mechamap
./deploy_production.sh

# 3. Deploy Realtime Server
./realtime_deploy_production.sh
```

### 2. FastPanel Configuration
1. **Create websites** in FastPanel as described above
2. **Configure SSL** certificates
3. **Set up reverse proxy** for realtime server
4. **Configure firewall** rules

### 3. Verification
```bash
# Test Laravel
curl -k https://mechamap.com/api/health

# Test Realtime Server
curl -k https://realtime.mechamap.com/api/health

# Test WebSocket (from browser console)
const socket = io('https://realtime.mechamap.com');
socket.on('connect', () => console.log('Connected!'));
```

## 🔄 Updates & Maintenance

### Laravel Updates
```bash
cd /var/www/mechamap
git pull origin main
./deploy_production.sh
```

### Realtime Server Updates
```bash
cd /var/www/mechamap
git pull origin main
./realtime_deploy_production.sh
```

### FastPanel Maintenance
- **SSL Renewal**: Automatic via Let's Encrypt
- **System Updates**: FastPanel auto-update feature
- **Backups**: Configure in FastPanel → Backups
- **Monitoring**: Check FastPanel dashboard regularly

## 🚨 Troubleshooting

### Common Issues

#### 1. WebSocket Connection Failed
```bash
# Check realtime server status
pm2 status mechamap-realtime
pm2 logs mechamap-realtime

# Check FastPanel proxy logs
tail -f /var/log/nginx/realtime.mechamap.com.access.log
tail -f /var/log/nginx/realtime.mechamap.com.error.log
```

#### 2. SSL Certificate Issues
- **Check FastPanel** → Websites → SSL status
- **Verify DNS** records are pointing correctly
- **Renew manually** if auto-renewal fails

#### 3. Database Connection Issues
- **Check FastPanel** → Databases → Connection status
- **Verify credentials** in `.env.production`
- **Check MySQL** service status

### Log Locations
```bash
# Laravel Application
/var/www/mechamap/storage/logs/laravel.log

# Realtime Server
/var/www/mechamap/realtime-server/logs/app.log

# FastPanel/Nginx
/var/log/nginx/mechamap.com.access.log
/var/log/nginx/mechamap.com.error.log
/var/log/nginx/realtime.mechamap.com.access.log
/var/log/nginx/realtime.mechamap.com.error.log

# System
/var/log/syslog
/var/log/auth.log
```

## 📞 Support Checklist

When reporting issues, provide:
1. **FastPanel version** and configuration
2. **Error messages** from logs
3. **Steps to reproduce** the issue
4. **System resources** status (CPU, RAM, Disk)
5. **SSL certificate** status
6. **DNS configuration** verification

## 🔗 Important URLs

- **FastPanel Admin**: https://your-vps-ip:8888
- **Main Site**: https://mechamap.com
- **Admin Panel**: https://mechamap.com/admin
- **Realtime Server**: https://realtime.mechamap.com
- **Health Checks**:
  - Laravel: https://mechamap.com/api/health
  - Realtime: https://realtime.mechamap.com/api/health
