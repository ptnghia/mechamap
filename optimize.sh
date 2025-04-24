#!/bin/bash

# Optimization script for Mechamap Laravel application
echo "Starting optimization process..."

# Clear all caches first
echo "Clearing all caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear

# Optimize Composer's autoloader
echo "Optimizing Composer autoloader..."
composer dump-autoload --optimize

# Cache configuration
echo "Caching configuration..."
php artisan config:cache

# Cache routes
echo "Caching routes..."
php artisan route:cache

# Cache views
echo "Caching views..."
php artisan view:cache

# Optimize the application
echo "Running final optimization..."
php artisan optimize

# Generate sitemap
echo "Generating sitemap..."
php artisan sitemap:generate

echo "Optimization completed successfully!"
