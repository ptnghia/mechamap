<?php

/**
 * Auto-generated Fix Script for Problematic __() Calls
 */

// Bootstrap Laravel
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔧 Fixing Problematic __() Calls\n";
echo "==============================\n\n";

// Fix file: resources/views\admin\pages\create.blade.php
echo "📄 Fixing resources/views\admin\pages\create.blade.php...\n";
$content = file_get_contents('resources/views\admin\pages\create.blade.php');
$changed = false;

if ($changed) {
    file_put_contents('resources/views\admin\pages\create.blade.php', $content);
    echo "✅ Updated: resources/views\admin\pages\create.blade.php\n";
}

// Fix file: resources/views\admin\pages\edit.blade.php
echo "📄 Fixing resources/views\admin\pages\edit.blade.php...\n";
$content = file_get_contents('resources/views\admin\pages\edit.blade.php');
$changed = false;

if ($changed) {
    file_put_contents('resources/views\admin\pages\edit.blade.php', $content);
    echo "✅ Updated: resources/views\admin\pages\edit.blade.php\n";
}

// Fix file: resources/views\admin\pages\show.blade.php
echo "📄 Fixing resources/views\admin\pages\show.blade.php...\n";
$content = file_get_contents('resources/views\admin\pages\show.blade.php');
$changed = false;

if ($changed) {
    file_put_contents('resources/views\admin\pages\show.blade.php', $content);
    echo "✅ Updated: resources/views\admin\pages\show.blade.php\n";
}

// Fix file: resources/views\components\header.blade.php
echo "📄 Fixing resources/views\components\header.blade.php...\n";
$content = file_get_contents('resources/views\components\header.blade.php');
$changed = false;

// Fix: auth.logout
if (strpos($content, '__(\'auth.logout\')') !== false) {
    $content = str_replace('__(\'auth.logout\')', 't_auth(\'logout\')', $content);
    $changed = true;
    echo "   ✅ Fixed: auth.logout → t_auth('logout')\n";
}

if ($changed) {
    file_put_contents('resources/views\components\header.blade.php', $content);
    echo "✅ Updated: resources/views\components\header.blade.php\n";
}

// Fix file: resources/views\components\menu\admin-menu.blade.php
echo "📄 Fixing resources/views\components\menu\admin-menu.blade.php...\n";
$content = file_get_contents('resources/views\components\menu\admin-menu.blade.php');
$changed = false;

// Fix: auth.logout
if (strpos($content, '__(\'auth.logout\')') !== false) {
    $content = str_replace('__(\'auth.logout\')', 't_auth(\'logout\')', $content);
    $changed = true;
    echo "   ✅ Fixed: auth.logout → t_auth('logout')\n";
}

if ($changed) {
    file_put_contents('resources/views\components\menu\admin-menu.blade.php', $content);
    echo "✅ Updated: resources/views\components\menu\admin-menu.blade.php\n";
}

// Fix file: resources/views\components\menu\business-menu.blade.php
echo "📄 Fixing resources/views\components\menu\business-menu.blade.php...\n";
$content = file_get_contents('resources/views\components\menu\business-menu.blade.php');
$changed = false;

// Fix: auth.logout
if (strpos($content, '__(\'auth.logout\')') !== false) {
    $content = str_replace('__(\'auth.logout\')', 't_auth(\'logout\')', $content);
    $changed = true;
    echo "   ✅ Fixed: auth.logout → t_auth('logout')\n";
}

if ($changed) {
    file_put_contents('resources/views\components\menu\business-menu.blade.php', $content);
    echo "✅ Updated: resources/views\components\menu\business-menu.blade.php\n";
}

// Fix file: resources/views\components\menu\guest-menu.blade.php
echo "📄 Fixing resources/views\components\menu\guest-menu.blade.php...\n";
$content = file_get_contents('resources/views\components\menu\guest-menu.blade.php');
$changed = false;

// Fix: auth.login
if (strpos($content, '__(\'auth.login\')') !== false) {
    $content = str_replace('__(\'auth.login\')', 't_auth(\'login\')', $content);
    $changed = true;
    echo "   ✅ Fixed: auth.login → t_auth('login')\n";
}

// Fix: auth.register
if (strpos($content, '__(\'auth.register\')') !== false) {
    $content = str_replace('__(\'auth.register\')', 't_auth(\'register\')', $content);
    $changed = true;
    echo "   ✅ Fixed: auth.register → t_auth('register')\n";
}

if ($changed) {
    file_put_contents('resources/views\components\menu\guest-menu.blade.php', $content);
    echo "✅ Updated: resources/views\components\menu\guest-menu.blade.php\n";
}

// Fix file: resources/views\components\menu\member-menu.blade.php
echo "📄 Fixing resources/views\components\menu\member-menu.blade.php...\n";
$content = file_get_contents('resources/views\components\menu\member-menu.blade.php');
$changed = false;

// Fix: auth.logout
if (strpos($content, '__(\'auth.logout\')') !== false) {
    $content = str_replace('__(\'auth.logout\')', 't_auth(\'logout\')', $content);
    $changed = true;
    echo "   ✅ Fixed: auth.logout → t_auth('logout')\n";
}

if ($changed) {
    file_put_contents('resources/views\components\menu\member-menu.blade.php', $content);
    echo "✅ Updated: resources/views\components\menu\member-menu.blade.php\n";
}

// Fix file: resources/views\gallery\index.blade.php
echo "📄 Fixing resources/views\gallery\index.blade.php...\n";
$content = file_get_contents('resources/views\gallery\index.blade.php');
$changed = false;

if ($changed) {
    file_put_contents('resources/views\gallery\index.blade.php', $content);
    echo "✅ Updated: resources/views\gallery\index.blade.php\n";
}

// Fix file: resources/views\search\index.blade.php
echo "📄 Fixing resources/views\search\index.blade.php...\n";
$content = file_get_contents('resources/views\search\index.blade.php');
$changed = false;

if ($changed) {
    file_put_contents('resources/views\search\index.blade.php', $content);
    echo "✅ Updated: resources/views\search\index.blade.php\n";
}

echo "✅ Fix script completed!\n";
