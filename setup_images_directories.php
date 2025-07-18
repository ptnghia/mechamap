<?php
/**
 * Setup Images Directories and Permissions for MechaMap
 * Creates all required image directories with proper permissions
 */

echo "📁 Setting up MechaMap images directories...\n\n";

// Check if we're in Laravel project
if (!file_exists('artisan')) {
    echo "❌ Error: Please run this script from Laravel project root directory\n";
    exit(1);
}

// Define all image directories used by MechaMap
$imageDirectories = [
    'public/images',
    'public/images/users',
    'public/images/users/avatars',
    'public/images/threads',
    'public/images/threads/attachments',
    'public/images/showcases',
    'public/images/showcases/gallery',
    'public/images/products',
    'public/images/products/gallery',
    'public/images/categories',
    'public/images/forums',
    'public/images/banners',
    'public/images/logos',
    'public/images/temp', // Temporary uploads
];

$created = 0;
$existing = 0;
$errors = 0;

echo "🔧 Creating image directories...\n";

foreach ($imageDirectories as $dir) {
    if (!is_dir($dir)) {
        if (mkdir($dir, 0775, true)) {
            echo "✅ Created: $dir\n";
            $created++;
        } else {
            echo "❌ Failed to create: $dir\n";
            $errors++;
        }
    } else {
        echo "ℹ️  Already exists: $dir\n";
        $existing++;
    }
}

echo "\n🔒 Setting directory permissions...\n";

$permissionErrors = 0;

foreach ($imageDirectories as $dir) {
    if (is_dir($dir)) {
        // Set directory permissions to 775 (rwxrwxr-x)
        if (chmod($dir, 0775)) {
            echo "✅ Permissions set for: $dir\n";
        } else {
            echo "❌ Failed to set permissions for: $dir\n";
            $permissionErrors++;
        }
    }
}

// Create .gitkeep files to ensure directories are tracked in git
echo "\n📝 Creating .gitkeep files...\n";

$gitkeepDirs = [
    'public/images/users/avatars',
    'public/images/threads/attachments',
    'public/images/showcases/gallery',
    'public/images/products/gallery',
    'public/images/temp'
];

foreach ($gitkeepDirs as $dir) {
    if (is_dir($dir)) {
        $gitkeepFile = $dir . '/.gitkeep';
        if (!file_exists($gitkeepFile)) {
            if (file_put_contents($gitkeepFile, '')) {
                echo "✅ Created .gitkeep in: $dir\n";
            } else {
                echo "❌ Failed to create .gitkeep in: $dir\n";
            }
        } else {
            echo "ℹ️  .gitkeep already exists in: $dir\n";
        }
    }
}

// Create index.html files for security (prevent directory listing)
echo "\n🛡️  Creating security index.html files...\n";

$indexContent = '<!DOCTYPE html>
<html>
<head>
    <title>403 Forbidden</title>
</head>
<body>
    <h1>Directory access is forbidden.</h1>
</body>
</html>';

$securityDirs = [
    'public/images',
    'public/images/users',
    'public/images/threads',
    'public/images/showcases',
    'public/images/products',
    'public/images/temp'
];

foreach ($securityDirs as $dir) {
    if (is_dir($dir)) {
        $indexFile = $dir . '/index.html';
        if (!file_exists($indexFile)) {
            if (file_put_contents($indexFile, $indexContent)) {
                echo "✅ Created security index.html in: $dir\n";
            } else {
                echo "❌ Failed to create index.html in: $dir\n";
            }
        } else {
            echo "ℹ️  Security index.html already exists in: $dir\n";
        }
    }
}

// Summary
echo "\n" . str_repeat("=", 60) . "\n";
echo "📊 SETUP SUMMARY:\n";
echo str_repeat("=", 60) . "\n";
echo "Directories created: $created\n";
echo "Directories existing: $existing\n";
echo "Creation errors: $errors\n";
echo "Permission errors: $permissionErrors\n";

if ($errors === 0 && $permissionErrors === 0) {
    echo "\n✅ All image directories setup successfully!\n";
} else {
    echo "\n⚠️  Some issues occurred. Please check the output above.\n";
}

echo "\n📋 NEXT STEPS:\n";
echo "1. Verify web server has write access to these directories\n";
echo "2. For production, ensure proper ownership:\n";
echo "   sudo chown -R www-data:www-data public/images/\n";
echo "3. Test file upload functionality\n";

echo "\n🔧 MANUAL COMMANDS (if needed):\n";
echo "# Set ownership (production)\n";
echo "sudo chown -R www-data:www-data public/images/\n\n";
echo "# Set permissions (production)\n";
echo "sudo chmod -R 775 public/images/\n\n";
echo "# Set permissions (shared hosting)\n";
echo "chmod -R 755 public/images/\n\n";

echo "📁 Image directories are ready for MechaMap!\n";

// Display directory structure
echo "\n🌳 DIRECTORY STRUCTURE:\n";
echo "public/images/\n";
foreach ($imageDirectories as $dir) {
    if (strpos($dir, 'public/images/') === 0 && $dir !== 'public/images') {
        $relativePath = str_replace('public/images/', '', $dir);
        $depth = substr_count($relativePath, '/');
        $indent = str_repeat('  ', $depth + 1);
        $folderName = basename($relativePath);
        echo "$indent├── $folderName/\n";
    }
}

echo "\n🎉 Setup completed!\n";
