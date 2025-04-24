#!/bin/bash

# Deployment script for Mechamap Laravel application
echo "Starting deployment process..."

# Check server requirements
echo "Checking server requirements..."
php check-requirements.php
if [ $? -ne 0 ]; then
    echo "Server requirements check failed. Please fix the issues before deploying."
    exit 1
fi

# Pull the latest changes from the repository
echo "Pulling latest changes from repository..."
git pull origin main

# Install PHP dependencies
echo "Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader

# Install Node.js dependencies and build assets
echo "Installing Node.js dependencies and building assets..."
npm ci
npm run build

# Set up environment file if not exists
if [ ! -f .env ]; then
    echo "Creating .env file from .env.production..."
    cp .env.production .env
    php artisan key:generate
fi

# Clear and cache configuration
echo "Optimizing application..."
php artisan config:clear
php artisan config:cache
php artisan route:clear
php artisan route:cache
php artisan view:clear
php artisan view:cache
php artisan optimize

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force

# Create storage link if not exists
if [ ! -d public/storage ]; then
    echo "Creating storage link..."
    php artisan storage:link
fi

# Set proper permissions
echo "Setting proper permissions..."
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Generate sitemap
echo "Generating sitemap..."
php artisan sitemap:generate

# Set up cron job for Laravel scheduler if not already set up
if ! crontab -l | grep -q "artisan schedule:run"; then
    echo "Setting up cron job for Laravel scheduler..."
    (crontab -l 2>/dev/null; echo "* * * * * cd $(pwd) && php artisan schedule:run >> /dev/null 2>&1") | crontab -
fi

echo "Deployment completed successfully!"
