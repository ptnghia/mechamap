@echo off
REM Setup Script for MechaMap Virtual Host
echo 🚀 Setting up MechaMap Virtual Host Configuration
echo ================================================

echo.
echo 📝 Step 1: Adding mechamap.test to Windows hosts file...
echo.

REM Check if running as administrator
net session >nul 2>&1
if %errorLevel% == 0 (
    echo ✅ Running as Administrator
) else (
    echo ❌ Please run this script as Administrator
    echo Right-click and select "Run as administrator"
    pause
    exit /b 1
)

REM Backup hosts file
if not exist "C:\Windows\System32\drivers\etc\hosts.backup" (
    copy "C:\Windows\System32\drivers\etc\hosts" "C:\Windows\System32\drivers\etc\hosts.backup"
    echo ✅ Created backup of hosts file
)

REM Check if mechamap.test already exists
findstr /C:"mechamap.test" "C:\Windows\System32\drivers\etc\hosts" >nul
if %errorLevel% == 0 (
    echo ⚠️  mechamap.test already exists in hosts file
) else (
    echo 127.0.0.1    mechamap.test >> "C:\Windows\System32\drivers\etc\hosts"
    echo ✅ Added mechamap.test to hosts file
)

echo.
echo 📝 Step 2: Apache Virtual Host Configuration
echo.

set APACHE_CONF="d:\xampp\apache\conf\httpd.conf"
set VHOST_CONF="d:\xampp\apache\conf\extra\httpd-vhosts.conf"

if exist %APACHE_CONF% (
    echo ✅ Found Apache configuration at %APACHE_CONF%
) else (
    echo ❌ Apache configuration not found at expected location
    echo Please check your XAMPP installation
    pause
    exit /b 1
)

echo.
echo 📋 Manual Apache Configuration Steps:
echo =====================================
echo 1. Open %VHOST_CONF%
echo 2. Add the content from apache_vhost_mechamap.conf
echo 3. Ensure this line is uncommented in %APACHE_CONF%:
echo    Include conf/extra/httpd-vhosts.conf
echo 4. Restart Apache

echo.
echo 📝 Step 3: Verification
echo.
echo After completing Apache configuration:
echo 1. Restart Apache from XAMPP Control Panel
echo 2. Open browser and go to: http://mechamap.test
echo 3. You should see the Laravel application

echo.
echo 🎯 Testing Commands:
echo ==================
echo curl -I http://mechamap.test
echo curl http://mechamap.test/api/v1/products

echo.
echo ✅ Setup completed! Please restart Apache and test the configuration.
pause
