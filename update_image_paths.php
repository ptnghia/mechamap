<?php

// Kết nối đến database
$host = 'localhost';
$dbname = 'lara_mechamap_new';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Kết nối database thành công!\n";
} catch (PDOException $e) {
    die("Lỗi kết nối database: " . $e->getMessage());
}

// Lấy tất cả các settings có đường dẫn hình ảnh
$stmt = $pdo->prepare("SELECT id, `key`, value FROM settings WHERE `key` IN ('site_logo', 'site_favicon', 'site_banner') AND value LIKE '/storage/settings/%'");
$stmt->execute();
$settings = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Tìm thấy " . count($settings) . " settings cần cập nhật.\n";

// Cập nhật đường dẫn
foreach ($settings as $setting) {
    $oldPath = $setting['value'];
    $newPath = str_replace('/storage/settings/', '/images/settings/', $oldPath);

    $updateStmt = $pdo->prepare("UPDATE settings SET value = :newPath WHERE id = :id");
    $updateStmt->execute([
        'newPath' => $newPath,
        'id' => $setting['id']
    ]);

    echo "Đã cập nhật {$setting['key']} từ {$oldPath} thành {$newPath}\n";
}

echo "Hoàn tất cập nhật đường dẫn hình ảnh!\n";
