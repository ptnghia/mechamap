<?php
/**
 * Server Requirements Checker for Mechamap
 * 
 * This script checks if the server meets the requirements for running Mechamap.
 */

echo "Checking server requirements for Mechamap...\n\n";

// Check PHP version
$requiredPhpVersion = '8.2.0';
$currentPhpVersion = PHP_VERSION;
$phpVersionCheck = version_compare($currentPhpVersion, $requiredPhpVersion, '>=');

echo "PHP Version: " . $currentPhpVersion . " (Required: >= " . $requiredPhpVersion . ") ";
echo $phpVersionCheck ? "[OK]" : "[FAILED]";
echo "\n";

// Check PHP extensions
$requiredExtensions = [
    'BCMath',
    'Ctype',
    'Fileinfo',
    'JSON',
    'Mbstring',
    'OpenSSL',
    'PDO',
    'Tokenizer',
    'XML',
    'cURL',
    'GD',
    'Zip'
];

echo "\nChecking PHP Extensions:\n";
$extensionsOk = true;

foreach ($requiredExtensions as $extension) {
    $loaded = extension_loaded(strtolower($extension));
    echo "- " . $extension . ": " . ($loaded ? "[OK]" : "[FAILED]");
    echo "\n";
    
    if (!$loaded) {
        $extensionsOk = false;
    }
}

// Check if the storage and bootstrap/cache directories are writable
echo "\nChecking Directory Permissions:\n";

$directories = [
    'storage' => is_writable(__DIR__ . '/storage'),
    'storage/app' => is_writable(__DIR__ . '/storage/app'),
    'storage/framework' => is_writable(__DIR__ . '/storage/framework'),
    'storage/logs' => is_writable(__DIR__ . '/storage/logs'),
    'bootstrap/cache' => is_writable(__DIR__ . '/bootstrap/cache')
];

$permissionsOk = true;

foreach ($directories as $directory => $isWritable) {
    echo "- " . $directory . ": " . ($isWritable ? "[OK]" : "[FAILED]");
    echo "\n";
    
    if (!$isWritable) {
        $permissionsOk = false;
    }
}

// Check for Composer
echo "\nChecking for Composer: ";
$composerExists = file_exists(__DIR__ . '/vendor/autoload.php');
echo $composerExists ? "[OK]" : "[FAILED]";
echo "\n";

// Check for .env file
echo "Checking for .env file: ";
$envExists = file_exists(__DIR__ . '/.env');
echo $envExists ? "[OK]" : "[FAILED]";
echo "\n";

// Check for Node.js and npm
echo "\nChecking for Node.js and npm:\n";
$nodeVersion = shell_exec('node -v 2>/dev/null');
$npmVersion = shell_exec('npm -v 2>/dev/null');

echo "- Node.js: " . ($nodeVersion ? trim($nodeVersion) . " [OK]" : "[FAILED]");
echo "\n";
echo "- npm: " . ($npmVersion ? trim($npmVersion) . " [OK]" : "[FAILED]");
echo "\n";

// Summary
echo "\n=== Summary ===\n";
echo "PHP Version: " . ($phpVersionCheck ? "OK" : "FAILED") . "\n";
echo "PHP Extensions: " . ($extensionsOk ? "OK" : "FAILED") . "\n";
echo "Directory Permissions: " . ($permissionsOk ? "OK" : "FAILED") . "\n";
echo "Composer: " . ($composerExists ? "OK" : "FAILED") . "\n";
echo ".env File: " . ($envExists ? "OK" : "FAILED") . "\n";
echo "Node.js: " . ($nodeVersion ? "OK" : "FAILED") . "\n";
echo "npm: " . ($npmVersion ? "OK" : "FAILED") . "\n";

// Final verdict
$allOk = $phpVersionCheck && $extensionsOk && $permissionsOk && $composerExists && $envExists;

echo "\nOverall Status: " . ($allOk ? "READY FOR DEPLOYMENT" : "NOT READY FOR DEPLOYMENT") . "\n";

if (!$allOk) {
    echo "\nPlease fix the issues above before deploying the application.\n";
    exit(1);
}

echo "\nYour server meets all the requirements for running Mechamap!\n";
exit(0);
