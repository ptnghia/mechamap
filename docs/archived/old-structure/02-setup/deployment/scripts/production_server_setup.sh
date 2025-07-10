#!/bin/bash

# MECHAMAP PRODUCTION SERVER SETUP SCRIPT
# Run this script on Ubuntu 22.04 LTS server
# Usage: bash production_server_setup.sh

set -e  # Exit on any error

echo "🚀 Starting MechaMap Production Server Setup..."
echo "=================================================="

# Update system
echo "📦 Updating system packages..."
sudo apt update && sudo apt upgrade -y

# Install basic tools
echo "🔧 Installing basic tools..."
sudo apt install -y curl wget git unzip software-properties-common ufw fail2ban

# Configure firewall
echo "🔒 Configuring firewall..."
sudo ufw --force enable
sudo ufw allow 22     # SSH
sudo ufw allow 80     # HTTP
sudo ufw allow 443    # HTTPS
sudo ufw allow 3306   # MySQL (localhost only later)

# Install Nginx
echo "🌐 Installing Nginx..."
sudo apt install -y nginx
sudo systemctl enable nginx
sudo systemctl start nginx

# Install PHP 8.2
echo "🐘 Installing PHP 8.2..."
sudo add-apt-repository -y ppa:ondrej/php
sudo apt update
sudo apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-xml php8.2-curl \
                    php8.2-gd php8.2-mbstring php8.2-zip php8.2-bcmath \
                    php8.2-intl php8.2-redis php8.2-imagick

# Configure PHP for production
echo "⚙️ Configuring PHP for production..."
sudo cp /etc/php/8.2/fpm/php.ini /etc/php/8.2/fpm/php.ini.backup

# Update PHP settings
sudo sed -i 's/memory_limit = .*/memory_limit = 256M/' /etc/php/8.2/fpm/php.ini
sudo sed -i 's/upload_max_filesize = .*/upload_max_filesize = 50M/' /etc/php/8.2/fpm/php.ini
sudo sed -i 's/post_max_size = .*/post_max_size = 50M/' /etc/php/8.2/fpm/php.ini
sudo sed -i 's/max_execution_time = .*/max_execution_time = 300/' /etc/php/8.2/fpm/php.ini

# Install MySQL 8.0
echo "🗄️ Installing MySQL 8.0..."
sudo apt install -y mysql-server

# Install Redis
echo "📊 Installing Redis..."
sudo apt install -y redis-server
sudo systemctl enable redis-server
sudo systemctl start redis-server

# Install Composer
echo "🎼 Installing Composer..."
cd /tmp
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer

# Install Node.js 18 LTS
echo "📦 Installing Node.js..."
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Install Certbot for SSL
echo "🔐 Installing Certbot for SSL..."
sudo apt install -y certbot python3-certbot-nginx

# Configure fail2ban
echo "🛡️ Configuring fail2ban..."
sudo systemctl enable fail2ban
sudo systemctl start fail2ban

# Create web directory
echo "📁 Creating web directory..."
sudo mkdir -p /var/www/mechamap
sudo chown -R $USER:$USER /var/www/mechamap

# Create Laravel directories with proper permissions
sudo mkdir -p /var/www/mechamap/storage/logs
sudo mkdir -p /var/www/mechamap/storage/app/protected
sudo mkdir -p /var/www/mechamap/storage/framework/{cache,sessions,views}
sudo mkdir -p /var/www/mechamap/bootstrap/cache

echo "✅ Server setup completed!"
echo "=================================================="
echo "Next steps:"
echo "1. Configure your domain DNS to point to this server"
echo "2. Run the application deployment script"
echo "3. Configure SSL certificates with: sudo certbot --nginx -d yourdomain.com"
echo ""
echo "Server Details:"
echo "- Nginx: http://$(curl -s ifconfig.me)"
echo "- PHP Version: $(php -v | head -n1)"
echo "- MySQL Version: $(mysql --version)"
echo "- Redis: $(redis-server --version)"
echo ""
echo "🚀 Ready for MechaMap deployment!"
