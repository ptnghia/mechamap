# MechaMap Deployment Documentation

## 📋 Overview

This directory contains all deployment-related documentation for MechaMap production environments.

## 📁 File Structure

### FastPanel Deployment (Recommended)
- **`fastpanel_configuration.md`** - Complete FastPanel setup guide
- **`PRODUCTION_DEPLOYMENT_README.md`** - Complete production deployment guide
- **`SECURITY_CONFIGURATION_UPDATE.md`** - Security configuration documentation

### General Production Deployment
- **`production-deployment.md`** - General production deployment instructions
- **`production-no-redis.md`** - Production deployment without Redis
- **`ubuntu-22-04-fix.md`** - Ubuntu 22.04 specific fixes

## 🚀 Quick Start

### For FastPanel VPS Deployment
1. Read `fastpanel_configuration.md` for complete setup
2. Follow `PRODUCTION_DEPLOYMENT_README.md` for deployment checklist
3. Use deployment scripts in project root:
   - `./deploy_production.sh`
   - `./realtime_deploy_production.sh`
   - `./fastpanel_health_check.sh`

### For General VPS Deployment
1. Read `PRODUCTION_DEPLOYMENT_README.md`
2. Follow `production-deployment.md` for step-by-step instructions
3. Use appropriate deployment scripts

## 🔧 Deployment Scripts (Located in Project Root)

- **`deploy_production.sh`** - Laravel application deployment
- **`realtime_deploy_production.sh`** - Realtime server deployment with PM2
- **`fastpanel_health_check.sh`** - Health check for FastPanel environment
- **`nginx_mechamap.conf`** - Nginx configuration reference

## 🏗️ Architecture

### FastPanel Architecture
```
Internet → FastPanel (SSL + Reverse Proxy) → Laravel + Node.js (Local)
```

### Standard VPS Architecture
```
Internet → Nginx (SSL + Reverse Proxy) → Laravel + Node.js
```

## 📊 Production Domains

| Service | Domain | Purpose |
|---------|--------|---------|
| **Main Application** | https://mechamap.com | Laravel web application |
| **Realtime Server** | https://realtime.mechamap.com | WebSocket server |
| **Admin Panel** | https://mechamap.com/admin | Administrative interface |

## 🔒 Security Features

- SSL/HTTPS enforcement
- Reverse proxy configuration
- Firewall protection
- Rate limiting
- Security headers

## 📈 Monitoring

- Health check endpoints
- System resource monitoring
- Application logs
- SSL certificate monitoring

## 📞 Support

For deployment issues:
1. Check the appropriate deployment guide
2. Run health check scripts
3. Review logs and error messages
4. Contact development team with specific details

## 🔗 Related Documentation

- **Main README**: `../../README.md`
- **Developer Guides**: `../developer-guides/`
- **API Documentation**: `../api/`
- **Troubleshooting**: `../troubleshooting/`
