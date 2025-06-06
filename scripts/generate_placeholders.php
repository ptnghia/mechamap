<?php

// Script để tạo placeholder images cơ bản
$placeholdersDir = __DIR__ . '/../public/images/placeholders';

if (!file_exists($placeholdersDir)) {
    mkdir($placeholdersDir, 0755, true);
}

// Các size phổ biến cần tạo
$sizes = [
    ['width' => 50, 'height' => 50],
    ['width' => 64, 'height' => 64],
    ['width' => 150, 'height' => 150],
    ['width' => 300, 'height' => 200],
    ['width' => 300, 'height' => 300],
    ['width' => 800, 'height' => 600],
];

foreach ($sizes as $size) {
    $width = $size['width'];
    $height = $size['height'];
    $filename = "{$width}x{$height}.png";
    $filepath = $placeholdersDir . '/' . $filename;

    // Tạo image bằng GD
    if (extension_loaded('gd')) {
        $image = imagecreate($width, $height);

        // Màu nền
        $bgColor = imagecolorallocate($image, 204, 204, 204); // #cccccc
        $textColor = imagecolorallocate($image, 102, 102, 102); // #666666

        // Fill background
        imagefill($image, 0, 0, $bgColor);

        // Thêm text
        $text = "{$width}×{$height}";
        $fontSize = max(10, min($width, $height) / 10); // Dynamic font size

        // Calculate text position (center)
        $textBox = imagettfbbox($fontSize, 0, null, $text);
        if ($textBox) {
            $textWidth = $textBox[4] - $textBox[0];
            $textHeight = $textBox[1] - $textBox[7];
            $x = ($width - $textWidth) / 2;
            $y = ($height - $textHeight) / 2 + $textHeight;
        } else {
            // Fallback nếu không có font
            $x = $width / 2 - strlen($text) * 3;
            $y = $height / 2;
        }

        // Thêm text vào image
        if (function_exists('imagettftext')) {
            // Sử dụng built-in font nếu có
            imagestring($image, 3, $x, $y - 10, $text, $textColor);
        } else {
            imagestring($image, 3, $x, $y - 10, $text, $textColor);
        }

        // Lưu file
        imagepng($image, $filepath);
        imagedestroy($image);

        echo "✅ Created: {$filename}\n";
    } else {
        echo "❌ GD extension not loaded, cannot create: {$filename}\n";
    }
}

echo "\n🎉 Placeholder images generation completed!\n";
echo "📁 Location: public/images/placeholders/\n";

// List generated files
$files = scandir($placeholdersDir);
$files = array_filter($files, function ($file) {
    return pathinfo($file, PATHINFO_EXTENSION) === 'png';
});

echo "\n📄 Generated files:\n";
foreach ($files as $file) {
    $fileSize = filesize($placeholdersDir . '/' . $file);
    echo "   - {$file} (" . number_format($fileSize / 1024, 2) . " KB)\n";
}
