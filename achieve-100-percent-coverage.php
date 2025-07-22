<?php

/**
 * ACHIEVE 100% COVERAGE
 * Comprehensive script để đạt 100% translation coverage
 */

echo "=== ACHIEVING 100% TRANSLATION COVERAGE ===\n\n";

// PHASE 1: Thêm missing keys vào translation files
$newTranslationKeys = [
    // Conversations keys (2 keys) - thêm vào conversations.php
    'conversations_keys' => [
        'today' => ['vi' => 'Hôm nay', 'en' => 'Today'],
        'yesterday' => ['vi' => 'Hôm qua', 'en' => 'Yesterday'],
    ],
    
    // Navigation keys (1 key) - thêm vào navigation.php
    'navigation_keys' => [
        'dashboard' => ['vi' => 'Bảng điều khiển', 'en' => 'Dashboard'],
    ],
    
    // Forum keys (1 key) - thêm vào forum.php
    'forum_keys' => [
        'threads.locked' => ['vi' => 'Đã khóa', 'en' => 'Locked'],
    ],
    
    // FAQ keys (3 keys) - thêm vào faq.php
    'faq_keys' => [
        'categories' => ['vi' => 'Danh mục', 'en' => 'Categories'],
        'still_have_questions' => ['vi' => 'Vẫn còn câu hỏi?', 'en' => 'Still have questions?'],
        'contact_support' => ['vi' => 'Liên hệ hỗ trợ', 'en' => 'Contact Support'],
    ],
    
    // Showcase keys (3 keys) - thêm vào showcase.php
    'showcase_keys' => [
        'features.cad' => ['vi' => 'CAD', 'en' => 'CAD'],
        'features.download' => ['vi' => 'Tải xuống', 'en' => 'Download'],
        'ratings' => ['vi' => 'Đánh giá', 'en' => 'Ratings'],
    ],
    
    // Feature marketplace keys (3 keys) - thêm vào features.php
    'features_marketplace_keys' => [
        'marketplace.actions.reload' => ['vi' => 'Tải lại', 'en' => 'Reload'],
        'marketplace.actions.details' => ['vi' => 'Chi tiết', 'en' => 'Details'],
        'marketplace.time.just_now' => ['vi' => 'Vừa xong', 'en' => 'Just now'],
    ],
    
    // User profile keys (3 keys) - thêm vào user.php
    'user_keys' => [
        'profile.actions.search' => ['vi' => 'Tìm kiếm hồ sơ', 'en' => 'Search profiles'],
        'profile.labels.role' => ['vi' => 'Vai trò', 'en' => 'Role'],
        'profile.labels.all_roles' => ['vi' => 'Tất cả vai trò', 'en' => 'All roles'],
    ],
    
    // Auth registration keys (2 keys) - thêm vào auth.php
    'auth_registration_keys' => [
        'register.already_have_account' => ['vi' => 'Đã có tài khoản?', 'en' => 'Already have an account?'],
        'register.login_now' => ['vi' => 'Đăng nhập ngay', 'en' => 'Login now'],
    ],
    
    // Common marketplace actions (2 keys) - thêm vào common.php
    'common_marketplace_keys' => [
        'marketplace_actions.added_to_wishlist' => ['vi' => 'Đã thêm vào danh sách yêu thích', 'en' => 'Added to wishlist'],
        'marketplace_actions.added_to_cart' => ['vi' => 'Đã thêm vào giỏ hàng', 'en' => 'Added to cart'],
    ],
];

// PHASE 2: Blade files cần sửa hardcoded strings
$bladeFileFixes = [
    'resources/views/conversations/show.blade.php' => [
        "{{ __('Today') }}" => "{{ __('conversations.today') }}",
        "{{ __('Yesterday') }}" => "{{ __('conversations.yesterday') }}",
    ],
    
    'resources/views/following/participated.blade.php' => [
        "{{ __('Following') }}" => "{{ __('following.following') }}",
        "{{ __('Followers') }}" => "{{ __('following.followers') }}",
        "{{ __('Followed Threads') }}" => "{{ __('following.followed_threads') }}",
        "{{ __('Participated Discussions') }}" => "{{ __('following.participated_discussions') }}",
        "{{ __('Follow') }}" => "{{ __('following.follow') }}",
        "{{ __('Unfollow') }}" => "{{ __('following.unfollow') }}",
        "{{ __('Join the conversation by commenting on threads.') }}" => "{{ __('following.join_conversation_help') }}",
    ],
    
    'resources/views/profile/partials/update-password-form.blade.php' => [
        "{{ __('Update Password') }}" => "{{ __('profile.update_password') }}",
        "{{ __('Ensure your account is using a long, random password to stay secure.') }}" => "{{ __('profile.password_security_message') }}",
        "{{ __('Current Password') }}" => "{{ __('profile.current_password') }}",
        "{{ __('New Password') }}" => "{{ __('profile.new_password') }}",
        "{{ __('Confirm Password') }}" => "{{ __('auth.confirm_password') }}",
    ],
    
    'resources/views/gallery/create.blade.php' => [
        "{{ __('Select File') }}" => "{{ __('gallery.select_file') }}",
        "{{ __('Title') }}" => "{{ __('gallery.title') }}",
        "{{ __('Give your media a descriptive title (optional).') }}" => "{{ __('gallery.title_description') }}",
        "{{ __('Description') }}" => "{{ __('gallery.description') }}",
        "{{ __('Add a description for your media (optional).') }}" => "{{ __('gallery.description_help') }}",
        "{{ __('Upload Media') }}" => "{{ __('gallery.upload_media') }}",
    ],
    
    'resources/views/gallery/index.blade.php' => [
        "{{ __('Search gallery...') }}" => "{{ __('gallery.search_gallery') }}",
        "{{ __('Uploaded by') }}" => "{{ __('gallery.uploaded_by') }}",
        "{{ __('By') }}" => "{{ __('gallery.by') }}",
        "{{ __('No media items found.') }}" => "{{ __('gallery.no_media_found') }}",
        "{{ __('Upload Media') }}" => "{{ __('gallery.upload_media') }}",
    ],
    
    'resources/views/profile/partials/delete-user-form.blade.php' => [
        "{{ __('Delete Account') }}" => "{{ __('profile.delete_account') }}",
        "{{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}" => "{{ __('profile.delete_warning') }}",
        "{{ __('Are you sure you want to delete your account?') }}" => "{{ __('profile.delete_confirmation') }}",
        "{{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}" => "{{ __('profile.delete_password_confirmation') }}",
        "{{ __('Password') }}" => "{{ __('auth.password') }}",
    ],
    
    'resources/views/profile/partials/profile-posts-section.blade.php' => [
        "{{ __('Profile Posts') }}" => "{{ __('profile.posts') }}",
        "{{ __('Write something on') }}" => "{{ __('profile.write_something_on') }}",
        "{{ __('profile') }}" => "{{ __('profile.profile') }}",
        "{{ __('Post') }}" => "{{ __('profile.post') }}",
        "{{ __('No profile posts yet.') }}" => "{{ __('profile.no_posts_yet') }}",
    ],
];

// Function to add keys to translation files
function addKeysToFile($filePath, $keys, $lang) {
    if (!file_exists($filePath)) {
        echo "❌ File not found: $filePath\n";
        return false;
    }
    
    $content = file_get_contents($filePath);
    if ($content === false) {
        echo "❌ Failed to read $filePath\n";
        return false;
    }
    
    // Find the last closing bracket or parenthesis
    $lastBracketPos = strrpos($content, '];');
    if ($lastBracketPos === false) {
        $lastBracketPos = strrpos($content, ');');
    }
    if ($lastBracketPos === false) {
        echo "❌ Could not find closing bracket in $filePath\n";
        return false;
    }
    
    // Build new keys string
    $newKeysString = '';
    $addedCount = 0;
    
    foreach ($keys as $key => $translations) {
        if (isset($translations[$lang])) {
            $value = $translations[$lang];
            // Escape single quotes in the value
            $value = str_replace("'", "\\'", $value);
            $newKeysString .= "  '$key' => '$value',\n";
            $addedCount++;
        }
    }
    
    if (empty($newKeysString)) {
        echo "ℹ️  No keys to add for $lang\n";
        return true;
    }
    
    // Insert new keys before the closing bracket
    $beforeClosing = substr($content, 0, $lastBracketPos);
    $afterClosing = substr($content, $lastBracketPos);
    
    $newContent = $beforeClosing . $newKeysString . $afterClosing;
    
    // Write back to file
    if (file_put_contents($filePath, $newContent)) {
        echo "✅ Added $addedCount keys to $filePath\n";
        return true;
    } else {
        echo "❌ Failed to write $filePath\n";
        return false;
    }
}

// Function to fix blade files
function fixBladeFile($filePath, $replacements) {
    if (!file_exists($filePath)) {
        echo "❌ File not found: $filePath\n";
        return false;
    }
    
    $content = file_get_contents($filePath);
    if ($content === false) {
        echo "❌ Failed to read $filePath\n";
        return false;
    }
    
    $originalContent = $content;
    $replacementCount = 0;
    
    foreach ($replacements as $search => $replace) {
        $newContent = str_replace($search, $replace, $content);
        if ($newContent !== $content) {
            $count = substr_count($content, $search);
            $replacementCount += $count;
            $content = $newContent;
            echo "  ✅ Replaced '$search' ($count times)\n";
        }
    }
    
    if ($content !== $originalContent) {
        if (file_put_contents($filePath, $content)) {
            echo "  💾 Saved $filePath with $replacementCount replacements\n";
            return true;
        } else {
            echo "  ❌ Failed to save $filePath\n";
            return false;
        }
    } else {
        echo "  ℹ️  No changes needed for $filePath\n";
        return true;
    }
}

echo "🎯 TARGET: 100% TRANSLATION COVERAGE\n";
echo "📊 Current: 96.6% coverage (63 keys missing)\n\n";

echo "=== PHASE 1: ADDING MISSING TRANSLATION KEYS ===\n\n";

// Map categories to files
$categoryFileMap = [
    'conversations_keys' => 'conversations',
    'navigation_keys' => 'navigation',
    'forum_keys' => 'forum',
    'faq_keys' => 'faq',
    'showcase_keys' => 'showcase',
    'features_marketplace_keys' => 'features',
    'user_keys' => 'user',
    'auth_registration_keys' => 'auth',
    'common_marketplace_keys' => 'common',
];

$totalKeysAdded = 0;

foreach ($newTranslationKeys as $category => $keys) {
    echo "📁 Processing category: $category\n";
    
    $file = $categoryFileMap[$category];
    
    // Add to Vietnamese file
    $viFile = __DIR__ . "/resources/lang/vi/$file.php";
    if (addKeysToFile($viFile, $keys, 'vi')) {
        $totalKeysAdded += count($keys);
    }
    
    // Add to English file
    $enFile = __DIR__ . "/resources/lang/en/$file.php";
    addKeysToFile($enFile, $keys, 'en');
    
    echo "\n";
}

echo "=== PHASE 2: FIXING BLADE FILES ===\n\n";

$totalBladeReplacements = 0;

foreach ($bladeFileFixes as $filePath => $replacements) {
    echo "📄 Processing: $filePath\n";
    $fullPath = __DIR__ . '/' . $filePath;
    
    if (fixBladeFile($fullPath, $replacements)) {
        $totalBladeReplacements += count($replacements);
    }
    
    echo "\n";
}

echo "=== FINAL SUMMARY ===\n";
echo "Translation keys added: $totalKeysAdded\n";
echo "Blade files processed: " . count($bladeFileFixes) . "\n";
echo "Total replacements: $totalBladeReplacements\n";
echo "Target: 100% coverage\n";

echo "\n🎯 100% COVERAGE SCRIPT COMPLETED at " . date('Y-m-d H:i:s') . "\n";
echo "\nNext: Run scan to verify 100% coverage achieved!\n";
?>
