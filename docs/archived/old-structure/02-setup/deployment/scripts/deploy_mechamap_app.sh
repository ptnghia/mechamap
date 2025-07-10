#!/bin/bash

# MECHAMAP APPLICATION DEPLOYMENT SCRIPT
# Run this after server setup is complete
# Usage: bash deploy_mechamap_app.sh

set -e  # Exit on any error

echo "🚀 Deploying MechaMap Application..."
echo "===================================="

# Configuration
DOMAIN="mechamap.com"
APP_DIR="/var/www/mechamap"
DB_NAME="mechamap_production"
DB_USER="mechamap_user"

# Get database password
read -s -p "Enter MySQL root password: " MYSQL_ROOT_PASS
echo
read -s -p "Enter new database user password: " DB_PASS
echo

# Clone repository (replace with your actual repository)
echo "📦 Cloning MechaMap repository..."
cd $APP_DIR
git init
# Note: Add your repository URL here
# git remote add origin https://github.com/your-username/mechamap-backend.git
# git pull origin main

# For now, copy from development
echo "📋 Please copy your development files to $APP_DIR"
echo "Press any key when files are copied..."
read -n 1

# Install PHP dependencies
echo "🎼 Installing Composer dependencies..."
composer install --optimize-autoloader --no-dev

# Install and build assets
echo "📦 Installing and building assets..."
npm install --production
npm run build

# Set up environment file
echo "⚙️ Setting up environment..."
cp .env.example .env

# Generate application key
php artisan key:generate --force

# Configure database
echo "🗄️ Setting up database..."
mysql -u root -p"$MYSQL_ROOT_PASS" <<EOF
CREATE DATABASE IF NOT EXISTS $DB_NAME;
CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';
GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'localhost';
FLUSH PRIVILEGES;
EOF

# Update .env file with database credentials
sed -i "s/DB_DATABASE=.*/DB_DATABASE=$DB_NAME/" .env
sed -i "s/DB_USERNAME=.*/DB_USERNAME=$DB_USER/" .env
sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=$DB_PASS/" .env

# Update .env for production
sed -i "s/APP_ENV=.*/APP_ENV=production/" .env
sed -i "s/APP_DEBUG=.*/APP_DEBUG=false/" .env
sed -i "s/APP_URL=.*/APP_URL=https:\/\/$DOMAIN/" .env

# Run migrations
echo "🔄 Running database migrations..."
php artisan migrate --force

# Seed essential data
echo "🌱 Seeding database..."
php artisan db:seed --class=ProductCategorySeeder --force
php artisan db:seed --class=UserSeeder --force

# Set up storage link
echo "🔗 Creating storage symlink..."
php artisan storage:link

# Set proper permissions
echo "🔒 Setting file permissions..."
sudo chown -R www-data:www-data $APP_DIR
sudo chmod -R 755 $APP_DIR
sudo chmod -R 775 $APP_DIR/storage
sudo chmod -R 775 $APP_DIR/bootstrap/cache

# Cache configurations for production
echo "🚀 Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create Nginx configuration
echo "🌐 Creating Nginx configuration..."
sudo tee /etc/nginx/sites-available/$DOMAIN > /dev/null <<EOF
server {
    listen 80;
    server_name $DOMAIN www.$DOMAIN;
    root $APP_DIR/public;
    index index.php index.html index.htm;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript application/javascript application/json;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Handle large file downloads
    location ~ ^/api/v1/downloads/ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;

        # Increase timeouts for large files
        fastcgi_read_timeout 300;
        proxy_read_timeout 300;
        client_max_body_size 100M;
    }
}
EOF

# Enable site
sudo ln -sf /etc/nginx/sites-available/$DOMAIN /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default

# Test and reload Nginx
sudo nginx -t && sudo systemctl reload nginx

# Restart services
echo "🔄 Restarting services..."
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx

echo "✅ Application deployment completed!"
echo "===================================="
echo "Next steps:"
echo "1. Configure SSL: sudo certbot --nginx -d $DOMAIN -d www.$DOMAIN"
echo "2. Test the application: http://$DOMAIN"
echo "3. Configure payment gateways in .env"
echo "4. Run final tests"
echo ""
echo "Application Details:"
echo "- Domain: $DOMAIN"
echo "- App Directory: $APP_DIR"
echo "- Database: $DB_NAME"
echo "- Database User: $DB_USER"
echo ""
echo "🎉 MechaMap is ready for SSL and final configuration!"
