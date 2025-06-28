# Server Requirements for Mechamap

This document outlines the server requirements and configuration needed to deploy the Mechamap application to a production environment.

## PHP Requirements

- PHP >= 8.2
- PHP Extensions:
  - BCMath
  - Ctype
  - Fileinfo
  - JSON
  - Mbstring
  - OpenSSL
  - PDO
  - Tokenizer
  - XML
  - cURL
  - GD (for image processing)
  - Zip

## Database Requirements

- MySQL >= 8.0 or MariaDB >= 10.3
- A dedicated database user with appropriate permissions

## Web Server Requirements

### Apache

- Apache >= 2.4
- mod_rewrite enabled
- AllowOverride All (to enable .htaccess files)

### Nginx (Alternative)

- Nginx >= 1.18
- Proper server block configuration for Laravel

## File System Requirements

- Minimum 1GB of disk space (recommended: 5GB+)
- Proper permissions:
  - `/storage` and `/bootstrap/cache` directories should be writable by the web server
  - All other files should be readable by the web server

## Additional Requirements

- Composer (for dependency management)
- Node.js >= 16.0 and NPM >= 8.0 (for asset compilation)
- Git (for version control and deployment)

## Recommended Server Specifications

- CPU: 2+ cores
- RAM: 2GB+ (4GB+ recommended)
- Disk: SSD storage for better performance

## SSL Certificate

- A valid SSL certificate is recommended for secure HTTPS connections
- Let's Encrypt can be used for free SSL certificates

## Cron Jobs

Set up a cron job to run Laravel's scheduler:

```
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

## Email Configuration

- SMTP server access for sending emails
- Valid email credentials configured in the .env file
