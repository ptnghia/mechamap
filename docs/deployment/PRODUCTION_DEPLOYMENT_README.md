# MechaMap Production Deployment Guide (FastPanel)

## 🚀 Overview

This guide covers the complete production deployment of MechaMap system on a VPS with FastPanel:
- **Main Application**: https://mechamap.com (Laravel via FastPanel)
- **Realtime Server**: https://realtime.mechamap.com (Node.js → FastPanel Reverse Proxy → 127.0.0.1:3000)

## 📋 Prerequisites

### Server Requirements
- **VPS** with FastPanel installed and configured
- **OS**: Ubuntu 20.04+ (FastPanel supported)
- **FastPanel**: Latest version with PHP 8.2+ support
- **Node.js**: 18.0+ (for realtime server)
- **MySQL**: Available via FastPanel
- **SSL**: Let's Encrypt via FastPanel

### Domain Setup
- DNS records pointing to your VPS IP:
  - `mechamap.com` → VPS IP
  - `realtime.mechamap.com` → VPS IP
- FastPanel access for domain and SSL management

## 🔧 Deployment Steps

### 1. FastPanel Server Preparation

```bash
# FastPanel should already be installed on your VPS
# Verify FastPanel is running
sudo systemctl status fastpanel

# Install Node.js 18+ (if not available via FastPanel)
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Install PM2 globally for realtime server
sudo npm install -g pm2

# Composer should be available via FastPanel
# If not, install manually:
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### 2. Database Setup via FastPanel

1. **Login to FastPanel** → Databases
2. **Create Database**:
   - Database Name: `mechamap_db`
   - Username: `mechamap_user`
   - Password: `YPF1Dt5JyTgxJ95R`
   - Character Set: `utf8mb4`
   - Collation: `utf8mb4_unicode_ci`

**Alternative: Manual Setup**
```bash
# If you prefer command line (FastPanel manages MySQL)
sudo mysql -u root -p
```

```sql
CREATE DATABASE mechamap_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'mechamap_user'@'localhost' IDENTIFIED BY 'YPF1Dt5JyTgxJ95R';
GRANT ALL PRIVILEGES ON mechamap_db.* TO 'mechamap_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 3. Application Deployment

```bash
# Clone repository
cd /var/www
sudo git clone https://github.com/your-repo/mechamap.git
sudo chown -R www-data:www-data mechamap
cd mechamap

# Run Laravel deployment script
sudo chmod +x deploy_production.sh
sudo ./deploy_production.sh
```

### 4. Realtime Server Deployment

```bash
# Run realtime server deployment script
sudo chmod +x realtime_deploy_production.sh
sudo ./realtime_deploy_production.sh

# Setup PM2 auto-start (run the command shown by PM2)
sudo env PATH=$PATH:/usr/bin /usr/lib/node_modules/pm2/bin/pm2 startup systemd -u www-data --hp /var/www
```

### 5. FastPanel Website Configuration

#### 5.1 Main Laravel Application
1. **FastPanel** → Websites → Add Website
2. **Domain**: `mechamap.com`
3. **Document Root**: `/var/www/mechamap/public`
4. **PHP Version**: 8.2
5. **Enable SSL**: Yes (Let's Encrypt)

#### 5.2 Realtime Server Reverse Proxy
1. **FastPanel** → Websites → Add Website
2. **Domain**: `realtime.mechamap.com`
3. **Type**: Reverse Proxy
4. **Target URL**: `http://127.0.0.1:3000`
5. **Enable SSL**: Yes (Let's Encrypt)
6. **Enable WebSocket**: Yes

### 6. SSL Certificate Setup (Automatic via FastPanel)

SSL certificates are automatically managed by FastPanel:
- **Let's Encrypt** integration
- **Auto-renewal** enabled
- **Force HTTPS** configured
- **HSTS headers** added automatically

## 🔐 Security Configuration

### Firewall Setup
```bash
sudo ufw enable
sudo ufw allow ssh
sudo ufw allow 'Nginx Full'
sudo ufw deny 3000  # Block direct access to Node.js
```

### File Permissions
```bash
sudo chown -R www-data:www-data /var/www/mechamap
sudo chmod -R 755 /var/www/mechamap/storage
sudo chmod -R 755 /var/www/mechamap/bootstrap/cache
```

## 📊 Monitoring & Maintenance

### Health Checks
- **Laravel**: https://mechamap.com/api/health
- **Realtime**: https://realtime.mechamap.com/api/health

### Log Locations
- **Laravel**: `/var/www/mechamap/storage/logs/`
- **Nginx**: `/var/log/nginx/`
- **Realtime**: `/var/www/mechamap/realtime-server/logs/`

### PM2 Management
```bash
# View status
pm2 status

# View logs
pm2 logs mechamap-realtime

# Restart service
pm2 restart mechamap-realtime

# Monitor
pm2 monit
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

### Database Backups
```bash
# Create backup script
sudo crontab -e
# Add: 0 2 * * * mysqldump -u mechamap_user -p'YPF1Dt5JyTgxJ95R' mechamap_db > /backup/mechamap_$(date +\%Y\%m\%d).sql
```

## 🚨 Troubleshooting

### Common Issues

1. **WebSocket Connection Failed**
   - Check PM2 status: `pm2 status`
   - Check Nginx proxy configuration
   - Verify SSL certificates

2. **Database Connection Error**
   - Check MySQL service: `sudo systemctl status mysql`
   - Verify database credentials in `.env`
   - Check firewall rules

3. **File Permission Issues**
   - Reset permissions: `sudo chown -R www-data:www-data /var/www/mechamap`
   - Check storage directory: `sudo chmod -R 755 storage`

### Log Analysis
```bash
# Laravel logs
tail -f /var/www/mechamap/storage/logs/laravel.log

# Nginx logs
tail -f /var/log/nginx/mechamap_error.log

# Realtime server logs
pm2 logs mechamap-realtime --lines 50
```

## 📞 Support

For technical support or deployment issues:
- Check logs first
- Review configuration files
- Verify all services are running
- Contact development team with specific error messages

## 🔗 Important URLs

- **Main Site**: https://mechamap.com
- **Admin Panel**: https://mechamap.com/admin
- **Realtime Server**: https://realtime.mechamap.com
- **Health Check**: https://mechamap.com/api/health
- **Metrics**: https://realtime.mechamap.com/api/monitoring/metrics
