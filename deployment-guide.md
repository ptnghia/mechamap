# Mechamap Deployment Guide

This guide provides step-by-step instructions for deploying the Mechamap application to a production hosting environment.

## Pre-deployment Checklist

- [ ] Verify server meets all requirements in `server-requirements.md`
- [ ] Obtain database credentials for production
- [ ] Configure domain DNS settings
- [ ] Obtain SSL certificate (recommended)
- [ ] Set up email service for production
- [ ] Configure social login credentials for production URLs

## Deployment Steps

### 1. Clone the Repository

```bash
git clone https://github.com/ptnghia/mechamap.git
cd mechamap
```

### 2. Set Up Environment File

```bash
cp .env.production .env
```

Edit the `.env` file to update:
- Database credentials
- App URL
- Mail settings
- Social login credentials
- Any other environment-specific settings

### 3. Install Dependencies

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
```

### 4. Set Application Key

```bash
php artisan key:generate
```

### 5. Run Database Migrations

```bash
php artisan migrate --force
```

### 6. Seed the Database (if needed)

```bash
php artisan db:seed
```

### 7. Create Storage Link

```bash
php artisan storage:link
```

### 8. Optimize the Application

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### 9. Set Proper Permissions

```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

Replace `www-data` with your web server user (e.g., `apache`, `nginx`, etc.)

### 10. Set Up Cron Job

Add this cron job to run Laravel's scheduler:

```
* * * * * cd /path/to/mechamap && php artisan schedule:run >> /dev/null 2>&1
```

### 11. Configure Web Server

#### Apache

Ensure your virtual host configuration points to the `public` directory and that `.htaccess` files are enabled.

Example virtual host configuration:

```apache
<VirtualHost *:80>
    ServerName mechamap.com
    ServerAlias www.mechamap.com
    DocumentRoot /path/to/mechamap/public
    
    <Directory /path/to/mechamap/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/mechamap-error.log
    CustomLog ${APACHE_LOG_DIR}/mechamap-access.log combined
</VirtualHost>
```

#### Nginx

Example server block configuration:

```nginx
server {
    listen 80;
    server_name mechamap.com www.mechamap.com;
    root /path/to/mechamap/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 12. Set Up SSL (Recommended)

Use Let's Encrypt or your hosting provider's SSL tools to set up HTTPS.

### 13. Test the Application

- Visit your domain to ensure the application is working correctly
- Test user registration and login
- Test social login functionality
- Verify email sending works
- Check admin panel access

## Post-Deployment Tasks

- [ ] Set up monitoring for the application
- [ ] Configure backup strategy for database and files
- [ ] Set up error logging and notification
- [ ] Test all critical functionality in the production environment

## Troubleshooting

### Common Issues

1. **500 Server Error**
   - Check storage and bootstrap/cache permissions
   - Check Laravel logs at `storage/logs/laravel.log`
   - Verify .env file is properly configured

2. **Database Connection Issues**
   - Verify database credentials in .env
   - Check if database server is accessible from web server

3. **Asset Loading Issues**
   - Run `npm run build` again
   - Clear browser cache
   - Check for JavaScript console errors

4. **Email Sending Issues**
   - Verify mail configuration in .env
   - Test mail sending with `php artisan tinker` and Mail facade

## Rollback Procedure

If deployment fails, follow these steps to rollback:

1. Restore the database from backup
2. Revert to the previous code version:
   ```bash
   git reset --hard HEAD~1
   ```
3. Restore the previous .env file
4. Clear all caches:
   ```bash
   php artisan optimize:clear
   ```
