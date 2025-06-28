# 🚀 MECHAMAP PRODUCTION DEPLOYMENT PLAN

**Decision**: ✅ **Option B - Proceed to Production Deployment**  
**Timeline**: 5-7 days to go-live  
**Start Date**: June 12, 2025  
**Target Go-Live**: June 17-19, 2025

---

## 📋 PRODUCTION DEPLOYMENT ROADMAP

### **DAY 1-2: INFRASTRUCTURE SETUP** ⭐ **CRITICAL**

#### **Day 1 (Today - June 12, 2025): Server Provisioning**

##### **Step 1: Choose Hosting Provider** (30 minutes)
**Recommended Options**:
1. **DigitalOcean** (Recommended for simplicity)
   - $20/month Droplet (4GB RAM, 2 vCPUs, 80GB SSD)
   - Easy setup, good documentation
   - Pre-configured LAMP stacks available

2. **AWS EC2** (Enterprise option)
   - t3.medium instance ($30/month)
   - More complex but highly scalable
   - Better for future scaling

3. **Linode** (Alternative)
   - $24/month Dedicated CPU
   - Good performance for Laravel apps

**Immediate Action**: Choose provider and create account

##### **Step 2: Domain Configuration** (1 hour)
```bash
# Register production domain
Domain: mechamap.com (or mechamap.vn for Vietnam market)
Nameservers: Point to hosting provider
DNS Records:
  - A record: @ -> Server IP
  - A record: www -> Server IP
  - CNAME: api -> mechamap.com
```

##### **Step 3: Server Initial Setup** (2 hours)
```bash
# Ubuntu 22.04 LTS Server Setup
# Initial server access and security
sudo apt update && sudo apt upgrade -y
sudo ufw enable
sudo ufw allow 22   # SSH
sudo ufw allow 80   # HTTP
sudo ufw allow 443  # HTTPS
```

#### **Day 2 (June 13, 2025): LAMP Stack Installation**

##### **Step 1: Install Web Server Stack** (2 hours)
```bash
# Install Nginx
sudo apt install nginx -y
sudo systemctl enable nginx
sudo systemctl start nginx

# Install PHP 8.2
sudo apt install software-properties-common -y
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install php8.2 php8.2-fpm php8.2-mysql php8.2-xml php8.2-curl php8.2-gd php8.2-mbstring php8.2-zip php8.2-bcmath php8.2-intl -y

# Install MySQL 8.0
sudo apt install mysql-server -y
sudo mysql_secure_installation

# Install Redis
sudo apt install redis-server -y
sudo systemctl enable redis-server
```

##### **Step 2: Configure Production Environment** (3 hours)
```bash
# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js (for asset compilation)
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install nodejs -y

# Install Git
sudo apt install git -y

# Configure PHP-FPM for production
sudo nano /etc/php/8.2/fpm/php.ini
# Set production values:
# memory_limit = 256M
# upload_max_filesize = 50M
# post_max_size = 50M
# max_execution_time = 300
```

---

### **DAY 3-4: APPLICATION DEPLOYMENT** 🔧

#### **Day 3 (June 14, 2025): Code Deployment**

##### **Step 1: Clone Repository** (1 hour)
```bash
# Create web directory
sudo mkdir -p /var/www/mechamap
sudo chown -R $USER:$USER /var/www/mechamap

# Clone production code
cd /var/www/mechamap
git clone https://github.com/your-repo/mechamap-backend.git .

# Set proper permissions
sudo chown -R www-data:www-data /var/www/mechamap
sudo chmod -R 755 /var/www/mechamap
sudo chmod -R 775 /var/www/mechamap/storage
sudo chmod -R 775 /var/www/mechamap/bootstrap/cache
```

##### **Step 2: Install Dependencies** (1 hour)
```bash
# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Install and build frontend assets (if needed)
npm install
npm run build

# Clear and cache Laravel configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

##### **Step 3: Environment Configuration** (2 hours)
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure production .env file
nano .env
```

#### **Day 4 (June 15, 2025): Database & Configuration**

##### **Step 1: Database Setup** (2 hours)
```bash
# Create production database
sudo mysql -u root -p
CREATE DATABASE mechamap_production;
CREATE USER 'mechamap_user'@'localhost' IDENTIFIED BY 'secure_password_here';
GRANT ALL PRIVILEGES ON mechamap_production.* TO 'mechamap_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Run migrations
php artisan migrate --force

# Seed initial data
php artisan db:seed --class=ProductCategorySeeder
php artisan db:seed --class=UserSeeder
```

##### **Step 2: SSL Certificate Setup** (1 hour)
```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx -y

# Get SSL certificate
sudo certbot --nginx -d mechamap.com -d www.mechamap.com
```

##### **Step 3: Nginx Configuration** (2 hours)
```bash
# Create Nginx virtual host
sudo nano /etc/nginx/sites-available/mechamap.com
```

---

### **DAY 5-7: SECURITY & GO-LIVE** 🔒

#### **Day 5 (June 16, 2025): Security Hardening**

##### **Step 1: Server Security** (3 hours)
```bash
# Configure fail2ban
sudo apt install fail2ban -y
sudo systemctl enable fail2ban

# Configure SSH security
sudo nano /etc/ssh/sshd_config
# Disable root login, change default port

# Set up automated backups
sudo crontab -e
# Add backup schedule
```

##### **Step 2: Application Security** (2 hours)
```bash
# Configure Laravel security
# Set secure session and cookie settings in .env
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=strict

# Configure CORS for production
php artisan config:cache
```

#### **Day 6 (June 17, 2025): Payment Gateway Configuration**

##### **Step 1: Live Payment Setup** (2 hours)
```bash
# Configure production Stripe keys
STRIPE_PUBLIC_KEY=pk_live_...
STRIPE_SECRET_KEY=sk_live_...
STRIPE_WEBHOOK_SECRET=whsec_...

# Configure production VNPay
VNPAY_MERCHANT_ID=your_live_merchant_id
VNPAY_HASH_SECRET=your_live_hash_secret
VNPAY_URL=https://pay.vnpay.vn/vpcpay.html
```

##### **Step 2: Test Payment Integration** (3 hours)
```bash
# Test Stripe live payments
# Test VNPay live payments
# Verify webhook endpoints
# Test complete purchase flow
```

#### **Day 7 (June 18, 2025): GO-LIVE** 🎉

##### **Step 1: Final Testing** (2 hours)
```bash
# Run comprehensive test suite
php artisan test

# Test all API endpoints
# Verify secure download system
# Test payment processing
# Check SSL certificates
```

##### **Step 2: Monitoring Setup** (2 hours)
```bash
# Install monitoring tools
# Configure error logging
# Set up uptime monitoring
# Configure email alerts
```

##### **Step 3: GO LIVE** (1 hour)
```bash
# Switch DNS to production server
# Monitor system performance
# Announce launch
# 🚀 MECHAMAP IS LIVE! 🚀
```

---

## 📊 DEPLOYMENT CHECKLIST

### **Infrastructure Checklist**
- [ ] Server provisioned and accessible
- [ ] Domain configured and pointing to server
- [ ] SSL certificates installed and working
- [ ] Firewall configured properly
- [ ] LAMP stack installed and configured

### **Application Checklist**
- [ ] Code deployed and permissions set
- [ ] Dependencies installed (Composer, NPM)
- [ ] Environment file configured
- [ ] Database created and migrated
- [ ] Laravel caches generated

### **Security Checklist**
- [ ] SSH hardened (key-based auth, custom port)
- [ ] Fail2ban configured
- [ ] Database secured (non-root user)
- [ ] Application security headers configured
- [ ] Backups automated

### **Payment Checklist**
- [ ] Stripe live keys configured
- [ ] VNPay live credentials set
- [ ] Webhook endpoints working
- [ ] Test transactions completed
- [ ] Payment confirmation flow working

### **Final Testing Checklist**
- [ ] All API endpoints responding
- [ ] User registration and login working
- [ ] Product listing and details working
- [ ] Shopping cart functionality working
- [ ] Order creation working
- [ ] Payment processing working
- [ ] Secure downloads working
- [ ] SSL certificates valid

---

## 🚨 EMERGENCY CONTACTS & ROLLBACK PLAN

### **Emergency Rollback Procedure**
If critical issues arise during deployment:

1. **Database Rollback**
```bash
# Restore from backup
mysql -u mechamap_user -p mechamap_production < backup_pre_migration.sql
```

2. **Code Rollback**
```bash
# Revert to previous working commit
git reset --hard [previous_commit_hash]
composer install
php artisan migrate:rollback
```

3. **DNS Rollback**
```bash
# Point domain back to development server
# Update A records in DNS management
```

### **Support Contacts**
- **Hosting Provider Support**: [Provider contact info]
- **Domain Registrar**: [Registrar contact info]
- **Payment Gateway Support**: 
  - Stripe: support@stripe.com
  - VNPay: support@vnpay.vn

---

## 📈 SUCCESS METRICS

### **Launch Day Metrics to Monitor**
- Server response time < 200ms
- SSL certificate grade A+
- Payment success rate > 95%
- Download success rate > 99%
- Zero critical errors in logs

### **Week 1 Business Metrics**
- User registrations
- Product views
- Purchase conversions
- Revenue generated
- Customer feedback

---

## 🎯 IMMEDIATE NEXT STEPS

### **TODAY (June 12, 2025) - START NOW**

**Priority 1**: Choose hosting provider and create account (30 min)
**Priority 2**: Register production domain (30 min)  
**Priority 3**: Provision server and start initial setup (2 hours)

**By end of today**: Server accessible via SSH with basic security configured

**Tomorrow**: Complete LAMP stack installation and basic Laravel deployment

---

**Status**: ✅ **PRODUCTION DEPLOYMENT PLAN READY**  
**Next Action**: Choose hosting provider and start server setup  
**Go-Live Target**: June 17-19, 2025 🚀

*Let's make MechaMap live and start generating revenue!*
