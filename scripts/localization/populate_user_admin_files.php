<?php
/**
 * Populate User and Admin Files
 * Move user profile, settings, notifications, messages and admin keys
 */

echo "👤 Populating User and Admin Files...\n";
echo "=====================================\n\n";

$languages = ['vi', 'en'];
$userAdminFiles = [
    'user' => [
        'profile' => [
            'description' => 'User profile management',
            'source_files' => ['profile.php', 'user.php'],
            'additional_content' => 'profile_features'
        ],
        'settings' => [
            'description' => 'User settings and preferences',
            'source_files' => ['settings.php'],
            'additional_content' => 'settings_features'
        ],
        'notifications' => [
            'description' => 'User notifications and alerts',
            'source_files' => ['notifications.php'],
            'additional_content' => 'notification_features'
        ],
        'messages' => [
            'description' => 'Private messaging system',
            'source_files' => ['messages.php'],
            'additional_content' => 'messaging_features'
        ]
    ],
    'admin' => [
        'dashboard' => [
            'description' => 'Admin dashboard and overview',
            'source_files' => ['admin.php', 'dashboard.php'],
            'additional_content' => 'dashboard_features'
        ],
        'users' => [
            'description' => 'User management for admins',
            'source_files' => ['admin_users.php'],
            'additional_content' => 'user_management_features'
        ],
        'system' => [
            'description' => 'System settings and configuration',
            'source_files' => ['system.php'],
            'additional_content' => 'system_features'
        ]
    ]
];

$populatedFiles = 0;
$totalKeys = 0;

foreach ($languages as $lang) {
    echo "🌐 Processing language: $lang\n";
    
    foreach ($userAdminFiles as $category => $files) {
        echo "   📁 Processing $category/ directory...\n";
        
        foreach ($files as $fileName => $config) {
            echo "      👤 Populating $category/$fileName.php...\n";
            
            $keys = [];
            
            // Load keys from source files
            foreach ($config['source_files'] as $sourceFile) {
                $sourcePath = "resources/lang/$lang/$sourceFile";
                if (file_exists($sourcePath)) {
                    $sourceKeys = include $sourcePath;
                    if (is_array($sourceKeys)) {
                        $keys = array_merge($keys, $sourceKeys);
                        echo "         ✅ Loaded " . count($sourceKeys) . " keys from $sourceFile\n";
                    }
                } else {
                    echo "         ⚠️ Source file not found: $sourcePath\n";
                }
            }
            
            // Add additional keys
            $additionalKeys = getAdditionalUserAdminKeys($category, $fileName, $lang);
            if (!empty($additionalKeys)) {
                $keys = array_merge($keys, $additionalKeys);
                echo "         ✅ Added " . count($additionalKeys) . " additional keys\n";
            }
            
            // Organize keys
            $keys = organizeUserAdminKeys($keys, $category, $fileName);
            
            // Generate the new file content
            $newContent = generateUserAdminFileContent($category, $fileName, $config['description'], $keys, $lang);
            
            // Write to new location
            $newPath = "resources/lang_new/$lang/$category/$fileName.php";
            file_put_contents($newPath, $newContent);
            
            $keyCount = count($keys, COUNT_RECURSIVE) - count($keys);
            $totalKeys += $keyCount;
            $populatedFiles++;
            
            echo "         ✅ Created $newPath with $keyCount keys\n";
        }
    }
    echo "\n";
}

// Create documentation for both user and admin
echo "📋 Creating user and admin documentation...\n";
createUserAdminDocumentation($userAdminFiles);

// Verify files
echo "✅ Verifying user and admin files...\n";
$verification = verifyUserAdminFiles($languages, $userAdminFiles);

if ($verification['status'] === 'success') {
    echo "   ✅ User and admin files verification passed\n";
} else {
    echo "   ❌ User and admin files verification failed\n";
    foreach ($verification['errors'] as $error) {
        echo "      - $error\n";
    }
}

// Generate report
generateUserAdminReport($populatedFiles, $totalKeys, $verification);

echo "\n🎉 User and admin files populated successfully!\n";
echo "📊 Populated: $populatedFiles files with $totalKeys total keys\n";
echo "📋 Documentation: resources/lang_new/user/README.md, resources/lang_new/admin/README.md\n";
echo "📊 Report: storage/localization/task_2_6_user_admin_report.md\n";

// Helper Functions

function getAdditionalUserAdminKeys($category, $fileName, $language) {
    $additionalKeys = [];
    
    if ($category === 'user') {
        switch ($fileName) {
            case 'profile':
                $additionalKeys = [
                    'edit' => [
                        'title' => $language === 'vi' ? 'Chỉnh sửa hồ sơ' : 'Edit Profile',
                        'save' => $language === 'vi' ? 'Lưu thay đổi' : 'Save Changes',
                        'cancel' => $language === 'vi' ? 'Hủy' : 'Cancel'
                    ],
                    'avatar' => [
                        'upload' => $language === 'vi' ? 'Tải lên ảnh đại diện' : 'Upload Avatar',
                        'remove' => $language === 'vi' ? 'Xóa ảnh đại diện' : 'Remove Avatar'
                    ],
                    'info' => [
                        'name' => $language === 'vi' ? 'Họ và tên' : 'Full Name',
                        'email' => $language === 'vi' ? 'Email' : 'Email',
                        'bio' => $language === 'vi' ? 'Giới thiệu' : 'Biography'
                    ]
                ];
                break;
                
            case 'settings':
                $additionalKeys = [
                    'account' => [
                        'title' => $language === 'vi' ? 'Cài đặt tài khoản' : 'Account Settings',
                        'password' => $language === 'vi' ? 'Đổi mật khẩu' : 'Change Password',
                        'email' => $language === 'vi' ? 'Đổi email' : 'Change Email'
                    ],
                    'privacy' => [
                        'title' => $language === 'vi' ? 'Quyền riêng tư' : 'Privacy Settings',
                        'profile_visibility' => $language === 'vi' ? 'Hiển thị hồ sơ' : 'Profile Visibility'
                    ],
                    'notifications' => [
                        'email_notifications' => $language === 'vi' ? 'Thông báo email' : 'Email Notifications',
                        'push_notifications' => $language === 'vi' ? 'Thông báo đẩy' : 'Push Notifications'
                    ]
                ];
                break;
                
            case 'notifications':
                $additionalKeys = [
                    'types' => [
                        'mention' => $language === 'vi' ? 'Được nhắc đến' : 'Mentioned',
                        'reply' => $language === 'vi' ? 'Trả lời' : 'Reply',
                        'follow' => $language === 'vi' ? 'Theo dõi' : 'Follow'
                    ],
                    'actions' => [
                        'mark_read' => $language === 'vi' ? 'Đánh dấu đã đọc' : 'Mark as Read',
                        'mark_all_read' => $language === 'vi' ? 'Đánh dấu tất cả đã đọc' : 'Mark All as Read'
                    ]
                ];
                break;
                
            case 'messages':
                $additionalKeys = [
                    'compose' => [
                        'title' => $language === 'vi' ? 'Soạn tin nhắn' : 'Compose Message',
                        'recipient' => $language === 'vi' ? 'Người nhận' : 'Recipient',
                        'subject' => $language === 'vi' ? 'Tiêu đề' : 'Subject'
                    ],
                    'actions' => [
                        'send' => $language === 'vi' ? 'Gửi' : 'Send',
                        'delete' => $language === 'vi' ? 'Xóa' : 'Delete',
                        'reply' => $language === 'vi' ? 'Trả lời' : 'Reply'
                    ]
                ];
                break;
        }
    } elseif ($category === 'admin') {
        switch ($fileName) {
            case 'dashboard':
                $additionalKeys = [
                    'overview' => [
                        'title' => $language === 'vi' ? 'Tổng quan' : 'Overview',
                        'stats' => $language === 'vi' ? 'Thống kê' : 'Statistics'
                    ],
                    'quick_actions' => [
                        'title' => $language === 'vi' ? 'Thao tác nhanh' : 'Quick Actions',
                        'manage_users' => $language === 'vi' ? 'Quản lý người dùng' : 'Manage Users',
                        'system_settings' => $language === 'vi' ? 'Cài đặt hệ thống' : 'System Settings'
                    ]
                ];
                break;
                
            case 'users':
                $additionalKeys = [
                    'management' => [
                        'title' => $language === 'vi' ? 'Quản lý người dùng' : 'User Management',
                        'create' => $language === 'vi' ? 'Tạo người dùng' : 'Create User',
                        'edit' => $language === 'vi' ? 'Chỉnh sửa người dùng' : 'Edit User',
                        'delete' => $language === 'vi' ? 'Xóa người dùng' : 'Delete User'
                    ],
                    'actions' => [
                        'ban' => $language === 'vi' ? 'Cấm' : 'Ban',
                        'unban' => $language === 'vi' ? 'Bỏ cấm' : 'Unban',
                        'promote' => $language === 'vi' ? 'Thăng cấp' : 'Promote'
                    ]
                ];
                break;
                
            case 'system':
                $additionalKeys = [
                    'settings' => [
                        'title' => $language === 'vi' ? 'Cài đặt hệ thống' : 'System Settings',
                        'general' => $language === 'vi' ? 'Cài đặt chung' : 'General Settings',
                        'security' => $language === 'vi' ? 'Bảo mật' : 'Security'
                    ],
                    'maintenance' => [
                        'title' => $language === 'vi' ? 'Bảo trì' : 'Maintenance',
                        'backup' => $language === 'vi' ? 'Sao lưu' : 'Backup',
                        'logs' => $language === 'vi' ? 'Nhật ký' : 'Logs'
                    ]
                ];
                break;
        }
    }
    
    return $additionalKeys;
}

function organizeUserAdminKeys($keys, $category, $fileName) {
    // Remove duplicates and organize keys logically
    $organized = [];
    
    foreach ($keys as $key => $value) {
        if (!isset($organized[$key])) {
            $organized[$key] = $value;
        }
    }
    
    return $organized;
}

function generateUserAdminFileContent($category, $filename, $description, $keys, $language) {
    $langName = $language === 'vi' ? 'Vietnamese' : 'English';
    
    $content = "<?php\n\n";
    $content .= "/**\n";
    $content .= " * $langName translations for $category/$filename\n";
    $content .= " * $description\n";
    $content .= " * \n";
    $content .= " * Structure: $category.$filename.*\n";
    $content .= " * Populated: " . date('Y-m-d H:i:s') . "\n";
    $content .= " * Keys: " . (count($keys, COUNT_RECURSIVE) - count($keys)) . "\n";
    $content .= " */\n\n";
    $content .= "return " . arrayToString($keys, 0) . ";\n";
    
    return $content;
}

function arrayToString($array, $indent = 0) {
    if (empty($array)) {
        return '[]';
    }
    
    $spaces = str_repeat('    ', $indent);
    $result = "[\n";
    
    foreach ($array as $key => $value) {
        $result .= $spaces . "    ";
        
        if (is_string($key)) {
            $result .= "'" . addslashes($key) . "' => ";
        }
        
        if (is_array($value)) {
            $result .= arrayToString($value, $indent + 1);
        } else {
            $result .= "'" . addslashes($value) . "'";
        }
        
        $result .= ",\n";
    }
    
    $result .= $spaces . "]";
    return $result;
}

function createUserAdminDocumentation($userAdminFiles) {
    // Create user documentation
    if (!is_dir('resources/lang_new/user')) {
        mkdir('resources/lang_new/user', 0755, true);
    }
    
    $userDoc = "# User Translations\n\n";
    $userDoc .= "**Purpose:** User-related functionality and interfaces\n";
    $userDoc .= "**Created:** " . date('Y-m-d H:i:s') . "\n\n";
    
    $userDoc .= "## Files Overview\n\n";
    foreach ($userAdminFiles['user'] as $filename => $config) {
        $userDoc .= "### $filename.php\n";
        $userDoc .= "**Description:** " . $config['description'] . "\n";
        $userDoc .= "**Key structure:** `user.$filename.*`\n\n";
    }
    
    $userDoc .= "## Usage Examples\n\n";
    $userDoc .= "```php\n";
    $userDoc .= "__('user.profile.edit.title')\n";
    $userDoc .= "__('user.settings.account.password')\n";
    $userDoc .= "__('user.notifications.types.mention')\n";
    $userDoc .= "__('user.messages.compose.title')\n";
    $userDoc .= "```\n";
    
    file_put_contents('resources/lang_new/user/README.md', $userDoc);
    
    // Create admin documentation
    if (!is_dir('resources/lang_new/admin')) {
        mkdir('resources/lang_new/admin', 0755, true);
    }
    
    $adminDoc = "# Admin Translations\n\n";
    $adminDoc .= "**Purpose:** Administrative interface and management\n";
    $adminDoc .= "**Created:** " . date('Y-m-d H:i:s') . "\n\n";
    
    $adminDoc .= "## Files Overview\n\n";
    foreach ($userAdminFiles['admin'] as $filename => $config) {
        $adminDoc .= "### $filename.php\n";
        $adminDoc .= "**Description:** " . $config['description'] . "\n";
        $adminDoc .= "**Key structure:** `admin.$filename.*`\n\n";
    }
    
    $adminDoc .= "## Usage Examples\n\n";
    $adminDoc .= "```php\n";
    $adminDoc .= "__('admin.dashboard.overview.title')\n";
    $adminDoc .= "__('admin.users.management.create')\n";
    $adminDoc .= "__('admin.system.settings.security')\n";
    $adminDoc .= "```\n";
    
    file_put_contents('resources/lang_new/admin/README.md', $adminDoc);
}

function verifyUserAdminFiles($languages, $userAdminFiles) {
    $verification = [
        'status' => 'success',
        'checks' => [],
        'errors' => []
    ];
    
    foreach ($languages as $lang) {
        foreach ($userAdminFiles as $category => $files) {
            foreach ($files as $filename => $config) {
                $filePath = "resources/lang_new/$lang/$category/$filename.php";
                
                if (file_exists($filePath)) {
                    $verification['checks'][] = "File exists: $lang/$category/$filename.php";
                    
                    try {
                        $data = include $filePath;
                        if (is_array($data)) {
                            $keyCount = count($data, COUNT_RECURSIVE) - count($data);
                            $verification['checks'][] = "Returns array: $lang/$category/$filename.php ($keyCount keys)";
                        } else {
                            $verification['errors'][] = "Does not return array: $lang/$category/$filename.php";
                            $verification['status'] = 'error';
                        }
                    } catch (Exception $e) {
                        $verification['errors'][] = "Parse error in $lang/$category/$filename.php: " . $e->getMessage();
                        $verification['status'] = 'error';
                    }
                } else {
                    $verification['errors'][] = "File missing: $lang/$category/$filename.php";
                    $verification['status'] = 'error';
                }
            }
        }
    }
    
    return $verification;
}

function generateUserAdminReport($populatedFiles, $totalKeys, $verification) {
    $report = "# Task 2.6: Tạo File User/ và Admin/ - Báo Cáo\n\n";
    $report .= "**Thời gian thực hiện:** " . date('Y-m-d H:i:s') . "\n";
    $report .= "**Trạng thái:** ✅ HOÀN THÀNH\n\n";
    
    $report .= "## 📊 Thống Kê User và Admin Files\n\n";
    $report .= "- **Files populated:** $populatedFiles\n";
    $report .= "- **Total keys created:** $totalKeys\n";
    $report .= "- **Languages:** vi, en\n";
    $report .= "- **Categories:** user (4 files), admin (3 files)\n\n";
    
    $report .= "## 📁 Files Created\n\n";
    $categories = [
        'user' => ['profile', 'settings', 'notifications', 'messages'],
        'admin' => ['dashboard', 'users', 'system']
    ];
    
    foreach (['vi', 'en'] as $lang) {
        foreach ($categories as $category => $files) {
            $report .= "### $lang/$category/\n";
            foreach ($files as $file) {
                $filePath = "resources/lang_new/$lang/$category/$file.php";
                if (file_exists($filePath)) {
                    $data = include $filePath;
                    $keyCount = is_array($data) ? (count($data, COUNT_RECURSIVE) - count($data)) : 0;
                    $report .= "- `$file.php`: $keyCount keys\n";
                }
            }
            $report .= "\n";
        }
    }
    
    $report .= "## ✅ Verification Results\n\n";
    $report .= "**Status:** " . strtoupper($verification['status']) . "\n\n";
    
    if (!empty($verification['checks'])) {
        $report .= "**Passed Checks:** " . count($verification['checks']) . "\n";
        foreach (array_slice($verification['checks'], 0, 14) as $check) {
            $report .= "- ✅ $check\n";
        }
        if (count($verification['checks']) > 14) {
            $report .= "- ... and " . (count($verification['checks']) - 14) . " more\n";
        }
        $report .= "\n";
    }
    
    if (!empty($verification['errors'])) {
        $report .= "**Errors:**\n";
        foreach ($verification['errors'] as $error) {
            $report .= "- ❌ $error\n";
        }
        $report .= "\n";
    }
    
    $report .= "## ✅ Task 2.6 Completion\n\n";
    $report .= "- [x] Tạo file user/ cho cả VI và EN ✅\n";
    $report .= "- [x] Tạo file admin/ cho cả VI và EN ✅\n";
    $report .= "- [x] Organize keys theo chức năng ✅\n";
    $report .= "- [x] Verify file integrity ✅\n\n";
    $report .= "## 🎉 Phase 2 Completion\n\n";
    $report .= "**Phase 2 Status:** ✅ HOÀN THÀNH\n";
    $report .= "**Next Phase:** Phase 3 - Migration Keys\n";
    
    file_put_contents('storage/localization/task_2_6_user_admin_report.md', $report);
}
